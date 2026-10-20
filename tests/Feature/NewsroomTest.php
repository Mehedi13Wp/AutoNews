<?php

namespace Tests\Feature;

use App\Jobs\ImportSource;
use App\Livewire\DashboardStats;
use App\Models\Category;
use App\Models\ImportRun;
use App\Models\Post;
use App\Models\Source;
use App\Models\User;
use App\Services\ArticleWriter;
use App\Services\FeedParser;
use App\Services\NewsImporter;
use App\Services\PublicFeedClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class NewsroomTest extends TestCase
{
    use RefreshDatabase;

    private function category(): Category
    {
        return Category::firstOrCreate(['slug' => 'politics'], ['name_en' => 'Politics', 'name_bn' => 'রাজনীতি']);
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function article(array $data = []): Post
    {
        return Post::create([...['category_id' => $this->category()->id, 'title_en' => 'Public title', 'title_bn' => 'বাংলা শিরোনাম', 'slug' => 'public-title', 'body_en' => 'English story', 'body_bn' => 'বাংলা সংবাদ', 'status' => 'published', 'published_at' => now()->subMinute()], ...$data]);
    }

    public function test_language_switch_and_publication_visibility(): void
    {
        $post = $this->article();
        $this->article(['slug' => 'hidden-story', 'title_en' => 'Hidden draft', 'title_bn' => 'গোপন খসড়া', 'status' => 'draft']);
        $this->article(['slug' => 'future-story', 'title_en' => 'Future article', 'title_bn' => 'ভবিষ্যৎ সংবাদ', 'published_at' => now()->addDay()]);
        $this->get('/')->assertOk()->assertSee('বাংলা শিরোনাম')->assertDontSee('গোপন খসড়া')->assertDontSee('ভবিষ্যৎ সংবাদ');
        $this->post('/language/en')->assertRedirect();
        $this->get('/')->assertSee('Public title')->assertDontSee('বাংলা শিরোনাম');
        $this->get('/news/hidden-story')->assertNotFound();
        $this->get('/news/future-story')->assertNotFound();
        $this->get('/sitemap.xml')->assertOk()->assertSee('public-title')->assertDontSee('hidden-story');
        $this->get('/category/politics')->assertOk()->assertSee('Public title');
        $this->get('/?q=doesnotexist')->assertOk()->assertDontSee('Public title');
    }

    public function test_story_library_filters_by_category_and_status(): void
    {
        $this->actingAs($this->admin());
        $first = $this->article();
        $category = Category::create(['slug' => 'technology', 'name_en' => 'Technology', 'name_bn' => 'প্রযুক্তি']);
        $draft = $this->article(['slug' => 'tech-draft', 'category_id' => $category->id, 'status' => 'draft']);
        $this->get('/admin/posts?category='.$category->id.'&status=draft')
            ->assertOk()->assertViewHas('posts', fn ($posts) => $posts->total() === 1 && $posts->first()->id === $draft->id);
        $this->get('/admin/posts?category='.$category->id.'&status=published')
            ->assertOk()->assertViewHas('posts', fn ($posts) => $posts->total() === 0);
        $this->get('/admin')->assertOk()->assertViewHas('setupDone', 1)
            ->assertViewHas('activity', fn ($days) => $days->count() === 7 && $days->sum('count') === 1);
    }

    public function test_admin_access_login_and_logout(): void
    {
        $user = $this->admin();
        $user->password = 'testing-password';
        $user->save();
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->post('/admin/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors();
        $this->post('/admin/login', ['email' => $user->email, 'password' => 'testing-password'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
        foreach (['/admin', '/admin/posts', '/admin/posts/create', '/admin/categories', '/admin/sources'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
        $nonadmin = User::factory()->create();
        $this->actingAs($nonadmin)->get('/admin')->assertForbidden();
        $this->assertTrue(Hash::check('testing-password', $user->password));
    }

    public function test_post_creation_image_upload_edit_schedule_and_delete(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());
        $category = $this->category();
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9Zl1sAAAAASUVORK5CYII=');
        $data = ['title_en' => 'My first story', 'title_bn' => 'আমার প্রথম সংবাদ', 'body_en' => 'Original report', 'body_bn' => 'মৌলিক প্রতিবেদন', 'category_id' => $category->id, 'status' => 'draft'];
        $this->post('/admin/posts', [...$data, 'image' => UploadedFile::fake()->createWithContent('cover.png', $png)])->assertRedirect('/admin/posts');
        $post = Post::firstOrFail();
        Storage::disk('public')->assertExists($post->image_path);
        $this->get('/admin/posts/'.$post->id.'/edit')->assertOk();
        $this->get('/news/'.$post->slug)->assertNotFound();
        $this->put('/admin/posts/'.$post->id, [...$data, 'status' => 'published', 'image_url' => 'https://example.com/photo.jpg', 'remove_image' => 1])->assertRedirect('/admin/posts');
        $post->refresh();
        $this->assertNull($post->image_path);
        $this->assertEquals('https://example.com/photo.jpg', $post->image());
        $this->get('/news/'.$post->slug)->assertOk()->assertSee('আমার প্রথম সংবাদ');
        $this->put('/admin/posts/'.$post->id, [...$data, 'status' => 'scheduled', 'published_at' => now()->addHour()->format('Y-m-d H:i:s')])->assertRedirect('/admin/posts');
        $this->get('/news/'.$post->slug)->assertNotFound();
        $this->travel(2)->hours();
        $this->artisan('news:publish')->assertSuccessful();
        $this->assertEquals('published', $post->fresh()->status);
        $this->delete('/admin/posts/'.$post->id)->assertRedirect();
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_validation_prevents_unsafe_input_and_category_deletion(): void
    {
        $this->actingAs($this->admin());
        $post = $this->article();
        $this->delete('/admin/categories/'.$post->category_id)->assertSessionHasErrors('category');
        $this->post('/admin/posts', ['title_en' => 'Only one field'])->assertSessionHasErrors(['title_bn', 'category_id', 'body_en']);
        $this->post('/admin/sources', ['name' => 'Unsafe', 'feed_url' => 'http://127.0.0.1/feed', 'category_id' => $post->category_id, 'interval_minutes' => 30, 'max_posts' => 3])->assertSessionHasErrors('feed_url');
        $this->post('/admin/categories', ['name_en' => 'Business', 'name_bn' => 'ব্যবসা', 'slug' => 'business'])->assertRedirect();
        $this->assertDatabaseHas('categories', ['slug' => 'business']);
    }

    public function test_rss_atom_parsing_and_entity_rejection(): void
    {
        $parser = app(FeedParser::class);
        $items = $parser->parse('<rss version="2.0"><channel><item><title>Test</title><link>https://example.com/story</link><description><![CDATA[<p>Original text</p>]]></description><enclosure url="https://example.com/image.jpg" type="image/jpeg"/></item></channel></rss>');
        $this->assertSame('Original text', $items[0]['body']);
        $this->assertSame('https://example.com/image.jpg', $items[0]['image']);
        $atom = $parser->parse('<feed xmlns="http://www.w3.org/2005/Atom"><entry><title>Atom</title><link href="https://example.com/atom"/><content>Story text</content></entry></feed>');
        $this->assertSame('https://example.com/atom', $atom[0]['url']);
        $this->expectException(\RuntimeException::class);
        $parser->parse('<!DOCTYPE rss [<!ENTITY x SYSTEM "file:///etc/passwd">]><rss/>');
    }

    public function test_url_fingerprints_preserve_article_ids_and_ignore_tracking(): void
    {
        $this->assertSame(NewsImporter::fingerprintUrl('https://example.com/?p=1'), NewsImporter::fingerprintUrl('https://example.com/?utm_source=test&p=1#top'));
        $this->assertNotSame(NewsImporter::fingerprintUrl('https://example.com/?p=1'), NewsImporter::fingerprintUrl('https://example.com/?p=2'));
    }

    public function test_private_network_feeds_are_rejected(): void
    {
        Http::preventStrayRequests();
        $this->expectException(\RuntimeException::class);
        app(PublicFeedClient::class)->get('https://127.0.0.1/feed');
    }

    public function test_import_generates_drafts_and_deduplicates(): void
    {
        $category = $this->category();
        $source = Source::create(['name' => 'Example', 'feed_url' => 'https://example.com/feed', 'category_id' => $category->id, 'keywords' => 'politics', 'max_posts' => 3]);
        $xml = '<rss><channel><item><title>Politics today</title><link>https://example.com/story</link><description>A source report about politics.</description></item><item><title>Sports today</title><link>https://example.com/sports</link><description>Sport report.</description></item></channel></rss>';
        $this->mock(PublicFeedClient::class, fn ($m) => $m->shouldReceive('get')->andReturn($xml));
        $this->mock(ArticleWriter::class, fn ($m) => $m->shouldReceive('write')->once()->andReturn(['title_en' => 'New original title', 'title_bn' => 'নতুন শিরোনাম', 'body_en' => 'Original English report', 'body_bn' => 'নতুন বাংলা প্রতিবেদন']));
        app(NewsImporter::class)->run($source);
        app(NewsImporter::class)->run($source->fresh());
        $this->assertDatabaseCount('posts', 1);
        $this->assertSame('draft', Post::first()->status);
        $this->assertNull(Post::first()->image_url);
        $this->assertDatabaseCount('import_runs', 2);
        $this->assertSame(1, ImportRun::first()->imported);
    }

    public function test_auto_publish_and_feed_failure_are_reported(): void
    {
        $category = $this->category();
        $source = Source::create(['name' => 'Licensed source', 'feed_url' => 'https://example.com/feed', 'category_id' => $category->id, 'max_posts' => 1, 'auto_publish' => true, 'image_rights_confirmed' => true]);
        $xml = '<rss><channel><item><title>Original news</title><link>https://example.com/new</link><description>A licensed report.</description><enclosure url="https://example.com/photo.jpg" type="image/jpeg"/></item></channel></rss>';
        $this->mock(PublicFeedClient::class, fn ($m) => $m->shouldReceive('get')->once()->andReturn($xml));
        $this->mock(ArticleWriter::class, fn ($m) => $m->shouldReceive('write')->once()->andReturn(['title_en' => 'Original title', 'title_bn' => 'শিরোনাম', 'body_en' => 'Report', 'body_bn' => 'সংবাদ']));
        app(NewsImporter::class)->run($source);
        $this->assertSame('published', Post::first()->status);
        $this->assertSame('https://example.com/photo.jpg', Post::first()->image_url);
        $this->mock(PublicFeedClient::class, fn ($m) => $m->shouldReceive('get')->once()->andThrow(new \RuntimeException('invalid feed')));
        app(NewsImporter::class)->run($source->fresh());
        $this->assertSame('failed', ImportRun::latest('id')->first()->status);
        $this->assertNotNull($source->fresh()->last_error);
    }

    public function test_live_dashboard_requires_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        Livewire::test(DashboardStats::class)->assertSee('মোট সংবাদ')->assertSee('0');
        $this->actingAs(User::factory()->create());
        Livewire::test(DashboardStats::class)->assertForbidden();
    }

    public function test_ai_request_validates_bilingual_output(): void
    {
        config(['news.ai_key' => 'testing-only']);
        $output = ['title_en' => 'New title', 'title_bn' => 'শিরোনাম', 'excerpt_en' => 'Summary', 'excerpt_bn' => 'সারাংশ', 'body_en' => 'Article', 'body_bn' => 'প্রতিবেদন', 'seo_title_en' => 'SEO title', 'seo_title_bn' => 'এসইও', 'meta_description_en' => 'Description', 'meta_description_bn' => 'বিবরণ'];
        Http::fake(['api.openai.com/*' => Http::response(['choices' => [['message' => ['content' => json_encode($output)]]]])]);
        $actual = app(ArticleWriter::class)->write(['title' => 'Original', 'body' => str_repeat('Verified fact. ', 20), 'url' => 'https://example.com/story']);
        $this->assertSame('শিরোনাম', $actual['title_bn']);
        Http::assertSent(fn ($r) => $r->url() === 'https://api.openai.com/v1/chat/completions' && $r['response_format']['type'] === 'json_object');
    }

    public function test_source_queue_and_scheduler(): void
    {
        Queue::fake();
        $this->actingAs($this->admin());
        $category = $this->category();
        $data = ['name' => 'Example', 'feed_url' => 'https://example.com/rss', 'category_id' => $category->id, 'interval_minutes' => 30, 'max_posts' => 3, 'enabled' => 1];
        $this->post('/admin/sources', $data)->assertRedirect();
        $source = Source::firstOrFail();
        config(['news.ai_key' => null]);
        $this->post('/admin/sources/'.$source->id.'/run')->assertSessionHasErrors('ai');
        config(['news.ai_key' => 'test-only']);
        $this->post('/admin/sources/'.$source->id.'/run')->assertRedirect();
        Queue::assertPushed(ImportSource::class);
        $source->last_checked_at = now();
        $source->save();
        Queue::fake();
        $this->artisan('news:collect')->assertSuccessful();
        Queue::assertNothingPushed();
        $this->travel(31)->minutes();
        $this->artisan('news:collect')->assertSuccessful();
        Queue::assertPushed(ImportSource::class);
    }
}
