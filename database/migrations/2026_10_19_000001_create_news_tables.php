<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->boolean('is_admin')->default(false));
        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->string('name_en');
            $t->string('name_bn');
            $t->string('slug')->unique();
            $t->timestamps();
        });
        Schema::create('sources', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->text('feed_url');
            $t->foreignId('category_id')->constrained();
            $t->text('keywords')->nullable();
            $t->boolean('enabled')->default(false);
            $t->boolean('auto_publish')->default(false);
            $t->boolean('image_rights_confirmed')->default(false);
            $t->unsignedInteger('interval_minutes')->default(30);
            $t->unsignedInteger('max_posts')->default(3);
            $t->timestamp('last_checked_at')->nullable();
            $t->text('last_error')->nullable();
            $t->timestamps();
        });
        Schema::create('posts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('category_id')->constrained();
            $t->foreignId('source_id')->nullable()->constrained()->nullOnDelete();
            $t->string('slug')->unique();
            $t->string('title_en');
            $t->string('title_bn');
            $t->text('excerpt_en')->nullable();
            $t->text('excerpt_bn')->nullable();
            $t->longText('body_en');
            $t->longText('body_bn');
            $t->text('image_url')->nullable();
            $t->string('image_path')->nullable();
            $t->string('image_credit')->nullable();
            $t->text('source_url')->nullable();
            $t->string('source_name')->nullable();
            $t->string('import_key', 64)->nullable()->unique();
            $t->string('headline_key', 64)->nullable()->index();
            $t->string('seo_title_en')->nullable();
            $t->string('seo_title_bn')->nullable();
            $t->text('meta_description_en')->nullable();
            $t->text('meta_description_bn')->nullable();
            $t->string('status')->default('draft')->index();
            $t->timestamp('published_at')->nullable()->index();
            $t->boolean('featured')->default(false);
            $t->boolean('is_demo')->default(false);
            $t->timestamps();
        });
        Schema::create('import_runs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('source_id')->nullable()->constrained()->nullOnDelete();
            $t->string('source_name');
            $t->string('status');
            $t->unsignedInteger('imported')->default(0);
            $t->unsignedInteger('skipped')->default(0);
            $t->text('message')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_runs');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('sources');
        Schema::dropIfExists('categories');
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('is_admin'));
    }
};
