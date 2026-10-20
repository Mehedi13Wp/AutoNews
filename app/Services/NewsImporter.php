<?php

namespace App\Services;

use App\Models\ImportRun;
use App\Models\Post;
use App\Models\Source;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;

class NewsImporter
{
    public static function fingerprintUrl(string $url): string
    {
        $parts = parse_url($url);
        parse_str($parts['query'] ?? '', $query);
        foreach (array_keys($query) as $key) {
            if (str_starts_with(strtolower($key), 'utm_') || in_array(strtolower($key), ['fbclid', 'gclid'])) {
                unset($query[$key]);
            }
        }
        ksort($query);
        $canonical = strtolower($parts['scheme'] ?? 'https').'://'.strtolower($parts['host'] ?? '').rtrim($parts['path'] ?? '', '/');
        if ($query) {
            $canonical .= '?'.http_build_query($query);
        }

        return hash('sha256', $canonical);
    }

    public function run(Source $source): void
    {
        $lock = Cache::lock('import-source-'.$source->id, 1150);
        if (! $lock->get()) {
            return;
        }
        $run = ImportRun::create(['source_id' => $source->id, 'source_name' => $source->name, 'status' => 'running']);
        $imported = 0;
        $skipped = 0;
        $errors = [];
        try {
            $items = app(FeedParser::class)->parse(app(PublicFeedClient::class)->get($source->feed_url));
            foreach ($items as $item) {
                if ($imported >= $source->max_posts) {
                    break;
                }
                $key = self::fingerprintUrl($item['url']);
                $headline = hash('sha256', mb_strtolower(preg_replace('/[^\pL\pN]/u', '', $item['title'])));
                if (Post::where('import_key', $key)->orWhere('headline_key', $headline)->exists()) {
                    $skipped++;

                    continue;
                }
                $words = array_filter(array_map('trim', explode(',', $source->keywords ?? '')));
                if ($words && ! collect($words)->contains(fn ($word) => mb_stripos($item['title'].' '.$item['body'], $word) !== false)) {
                    $skipped++;

                    continue;
                }
                if ($item['date']) {
                    try {
                        if (Carbon::parse($item['date'])->lt(now()->subDays(7))) {
                            $skipped++;

                            continue;
                        }
                    } catch (Throwable) { /* Some publishers use nonstandard dates. */
                    }
                }
                try {
                    $data = app(ArticleWriter::class)->write($item);
                    Post::create([...$data, 'category_id' => $source->category_id, 'source_id' => $source->id, 'slug' => Str::limit(Str::slug($data['title_en']), 140, '').'-'.Str::lower(Str::random(8)),
                        'source_url' => $item['url'], 'source_name' => $source->name, 'import_key' => $key, 'headline_key' => $headline,
                        'image_url' => $source->image_rights_confirmed ? $item['image'] : null, 'image_credit' => $source->image_rights_confirmed ? $source->name : null,
                        'status' => $source->auto_publish ? 'published' : 'draft', 'published_at' => $source->auto_publish ? now() : null]);
                    $imported++;
                } catch (Throwable $e) {
                    $skipped++;
                    $errors[] = 'An article could not be generated. Check feed text and AI credentials/quota.';
                }
            }
            $error = $errors ? implode(' ', array_unique($errors)) : null;
            $source->update(['last_checked_at' => now(), 'last_error' => $error]);
            $run->update(['status' => $errors ? 'partial' : 'completed', 'imported' => $imported, 'skipped' => $skipped, 'message' => $error ?: 'Feed checked. '.$imported.' new article(s).']);
        } catch (Throwable $e) {
            $message = $e instanceof RequestException ? 'Feed request failed (HTTP '.$e->response->status().').' : ($e instanceof ConnectionException ? 'Could not connect to feed. Check network access and URL.' : 'Feed import failed. Check that the URL is a public, valid RSS/Atom feed.');
            $source->update(['last_checked_at' => now(), 'last_error' => $message]);
            $run->update(['status' => 'failed', 'imported' => $imported, 'skipped' => $skipped, 'message' => $message]);
        } finally {
            $lock->release();
        }
    }
}
