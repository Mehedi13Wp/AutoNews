<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Category::firstOrCreate(['slug' => 'politics'], ['name_en' => 'Politics', 'name_bn' => 'রাজনীতি']);
        $category = Category::firstOrCreate(['slug' => 'bangladesh'], ['name_en' => 'Bangladesh', 'name_bn' => 'বাংলাদেশ']);
        Post::firstOrCreate(['slug' => 'demo-story-1'], ['category_id' => $category->id, 'published_at' => now()->subHours(1), 'featured' => true, 'is_demo' => true, 'title_en' => 'A new morning by the river: seeing Dhaka from a different perspective', 'title_bn' => 'নদীর শহরে নতুন সকাল: ঢাকাকে দেখার অন্য এক দৃষ্টিভঙ্গি', 'excerpt_en' => 'Exploring the people, waterways and everyday rhythms that give a city its character.', 'excerpt_bn' => 'নদী, মানুষ আর শহরের গল্পে নতুন করে চেনা আমাদের চারপাশ।', 'body_en' => 'Exploring the people, waterways and everyday rhythms that give a city its character.

This is an original demonstration article created to preview the AutoNews design and bilingual publishing experience. It does not describe a verified news event.

Use the dashboard to replace this example with your own reporting, upload a cover image, choose a category and publish in both Bangla and English.', 'body_bn' => 'নদী, মানুষ আর শহরের গল্পে নতুন করে চেনা আমাদের চারপাশ।

এটি AutoNews-এর নকশা ও দুই ভাষার প্রকাশনা পরীক্ষা করার জন্য তৈরি মৌলিক ডেমো লেখা। এটি কোনো যাচাইকৃত বাস্তব ঘটনার সংবাদ নয়।

ড্যাশবোর্ড থেকে এই নমুনা সরিয়ে আপনার নিজের সংবাদ লিখুন। ছবি যুক্ত করুন, ক্যাটাগরি বেছে নিন এবং বাংলা ও ইংরেজিতে প্রকাশ করুন।', 'image_url' => '/images/river.svg', 'image_credit' => 'AutoNews · Original editorial illustration', 'status' => 'published', 'seo_title_en' => 'A new morning by the river: seeing Dhaka from a different perspective', 'seo_title_bn' => 'নদীর শহরে নতুন সকাল: ঢাকাকে দেখার অন্য এক দৃষ্টিভঙ্গি', 'meta_description_en' => 'Exploring the people, waterways and everyday rhythms that give a city its character.', 'meta_description_bn' => 'নদী, মানুষ আর শহরের গল্পে নতুন করে চেনা আমাদের চারপাশ।']);
        $category = Category::firstOrCreate(['slug' => 'technology'], ['name_en' => 'Technology', 'name_bn' => 'প্রযুক্তি']);
        Post::firstOrCreate(['slug' => 'demo-story-2'], ['category_id' => $category->id, 'published_at' => now()->subHours(2), 'featured' => false, 'is_demo' => true, 'title_en' => 'How artificial intelligence is reshaping the way we work', 'title_bn' => 'কৃত্রিম বুদ্ধিমত্তার সঙ্গে বদলে যাচ্ছে আগামী দিনের কাজ', 'excerpt_en' => 'Curiosity, new skills and thoughtful choices are shaping our relationship with technology.', 'excerpt_bn' => 'প্রযুক্তির নতুন সম্ভাবনা বুঝতে প্রয়োজন কৌতূহল, দক্ষতা ও দায়িত্বশীল ব্যবহার।', 'body_en' => 'Curiosity, new skills and thoughtful choices are shaping our relationship with technology.

This is an original demonstration article created to preview the AutoNews design and bilingual publishing experience. It does not describe a verified news event.

Use the dashboard to replace this example with your own reporting, upload a cover image, choose a category and publish in both Bangla and English.', 'body_bn' => 'প্রযুক্তির নতুন সম্ভাবনা বুঝতে প্রয়োজন কৌতূহল, দক্ষতা ও দায়িত্বশীল ব্যবহার।

এটি AutoNews-এর নকশা ও দুই ভাষার প্রকাশনা পরীক্ষা করার জন্য তৈরি মৌলিক ডেমো লেখা। এটি কোনো যাচাইকৃত বাস্তব ঘটনার সংবাদ নয়।

ড্যাশবোর্ড থেকে এই নমুনা সরিয়ে আপনার নিজের সংবাদ লিখুন। ছবি যুক্ত করুন, ক্যাটাগরি বেছে নিন এবং বাংলা ও ইংরেজিতে প্রকাশ করুন।', 'image_url' => '/images/technology.svg', 'image_credit' => 'AutoNews · Original editorial illustration', 'status' => 'published', 'seo_title_en' => 'How artificial intelligence is reshaping the way we work', 'seo_title_bn' => 'কৃত্রিম বুদ্ধিমত্তার সঙ্গে বদলে যাচ্ছে আগামী দিনের কাজ', 'meta_description_en' => 'Curiosity, new skills and thoughtful choices are shaping our relationship with technology.', 'meta_description_bn' => 'প্রযুক্তির নতুন সম্ভাবনা বুঝতে প্রয়োজন কৌতূহল, দক্ষতা ও দায়িত্বশীল ব্যবহার।']);
        $category = Category::firstOrCreate(['slug' => 'sports'], ['name_en' => 'Sports', 'name_bn' => 'খেলাধুলা']);
        Post::firstOrCreate(['slug' => 'demo-story-3'], ['category_id' => $category->id, 'published_at' => now()->subHours(3), 'featured' => false, 'is_demo' => true, 'title_en' => 'Beyond the boundary: the game that brings people together', 'title_bn' => 'মাঠের বাইরেও ক্রিকেট: যে খেলা মানুষকে একসঙ্গে আনে', 'excerpt_en' => 'From neighbourhood pitches to the big stage, a shared love of the game connects us.', 'excerpt_bn' => 'পাড়া থেকে বড় মাঠ—ক্রিকেটের প্রতি ভালোবাসার পরিচিত গল্প।', 'body_en' => 'From neighbourhood pitches to the big stage, a shared love of the game connects us.

This is an original demonstration article created to preview the AutoNews design and bilingual publishing experience. It does not describe a verified news event.

Use the dashboard to replace this example with your own reporting, upload a cover image, choose a category and publish in both Bangla and English.', 'body_bn' => 'পাড়া থেকে বড় মাঠ—ক্রিকেটের প্রতি ভালোবাসার পরিচিত গল্প।

এটি AutoNews-এর নকশা ও দুই ভাষার প্রকাশনা পরীক্ষা করার জন্য তৈরি মৌলিক ডেমো লেখা। এটি কোনো যাচাইকৃত বাস্তব ঘটনার সংবাদ নয়।

ড্যাশবোর্ড থেকে এই নমুনা সরিয়ে আপনার নিজের সংবাদ লিখুন। ছবি যুক্ত করুন, ক্যাটাগরি বেছে নিন এবং বাংলা ও ইংরেজিতে প্রকাশ করুন।', 'image_url' => '/images/sports.svg', 'image_credit' => 'AutoNews · Original editorial illustration', 'status' => 'published', 'seo_title_en' => 'Beyond the boundary: the game that brings people together', 'seo_title_bn' => 'মাঠের বাইরেও ক্রিকেট: যে খেলা মানুষকে একসঙ্গে আনে', 'meta_description_en' => 'From neighbourhood pitches to the big stage, a shared love of the game connects us.', 'meta_description_bn' => 'পাড়া থেকে বড় মাঠ—ক্রিকেটের প্রতি ভালোবাসার পরিচিত গল্প।']);
        $category = Category::firstOrCreate(['slug' => 'entertainment'], ['name_en' => 'Entertainment', 'name_bn' => 'বিনোদন']);
        Post::firstOrCreate(['slug' => 'demo-story-4'], ['category_id' => $category->id, 'published_at' => now()->subHours(4), 'featured' => false, 'is_demo' => true, 'title_en' => 'A new screen for storytelling: the creative possibilities of Bangla cinema', 'title_bn' => 'গল্পের নতুন পর্দা: বাংলা সিনেমার সৃজনশীল সম্ভাবনা', 'excerpt_en' => 'New voices and fresh ideas open up more ways for stories to find their audience.', 'excerpt_bn' => 'নতুন নির্মাতা, ভিন্ন ভাবনা এবং দর্শকের সঙ্গে নতুন সংযোগ।', 'body_en' => 'New voices and fresh ideas open up more ways for stories to find their audience.

This is an original demonstration article created to preview the AutoNews design and bilingual publishing experience. It does not describe a verified news event.

Use the dashboard to replace this example with your own reporting, upload a cover image, choose a category and publish in both Bangla and English.', 'body_bn' => 'নতুন নির্মাতা, ভিন্ন ভাবনা এবং দর্শকের সঙ্গে নতুন সংযোগ।

এটি AutoNews-এর নকশা ও দুই ভাষার প্রকাশনা পরীক্ষা করার জন্য তৈরি মৌলিক ডেমো লেখা। এটি কোনো যাচাইকৃত বাস্তব ঘটনার সংবাদ নয়।

ড্যাশবোর্ড থেকে এই নমুনা সরিয়ে আপনার নিজের সংবাদ লিখুন। ছবি যুক্ত করুন, ক্যাটাগরি বেছে নিন এবং বাংলা ও ইংরেজিতে প্রকাশ করুন।', 'image_url' => '/images/entertainment.svg', 'image_credit' => 'AutoNews · Original editorial illustration', 'status' => 'published', 'seo_title_en' => 'A new screen for storytelling: the creative possibilities of Bangla cinema', 'seo_title_bn' => 'গল্পের নতুন পর্দা: বাংলা সিনেমার সৃজনশীল সম্ভাবনা', 'meta_description_en' => 'New voices and fresh ideas open up more ways for stories to find their audience.', 'meta_description_bn' => 'নতুন নির্মাতা, ভিন্ন ভাবনা এবং দর্শকের সঙ্গে নতুন সংযোগ।']);
        $category = Category::firstOrCreate(['slug' => 'world'], ['name_en' => 'World', 'name_bn' => 'বিশ্ব']);
        Post::firstOrCreate(['slug' => 'demo-story-5'], ['category_id' => $category->id, 'published_at' => now()->subHours(5), 'featured' => false, 'is_demo' => true, 'title_en' => 'Rethinking our cities: designing urban life around people', 'title_bn' => 'টেকসই নগরের ভাবনা: মানুষের জন্য কেমন শহর চাই', 'excerpt_en' => 'Green spaces and better connections help us imagine more welcoming places to live.', 'excerpt_bn' => 'সবুজ জায়গা, সহজ যাতায়াত ও মানুষের প্রয়োজন নিয়ে নগর পরিকল্পনার আলোচনা।', 'body_en' => 'Green spaces and better connections help us imagine more welcoming places to live.

This is an original demonstration article created to preview the AutoNews design and bilingual publishing experience. It does not describe a verified news event.

Use the dashboard to replace this example with your own reporting, upload a cover image, choose a category and publish in both Bangla and English.', 'body_bn' => 'সবুজ জায়গা, সহজ যাতায়াত ও মানুষের প্রয়োজন নিয়ে নগর পরিকল্পনার আলোচনা।

এটি AutoNews-এর নকশা ও দুই ভাষার প্রকাশনা পরীক্ষা করার জন্য তৈরি মৌলিক ডেমো লেখা। এটি কোনো যাচাইকৃত বাস্তব ঘটনার সংবাদ নয়।

ড্যাশবোর্ড থেকে এই নমুনা সরিয়ে আপনার নিজের সংবাদ লিখুন। ছবি যুক্ত করুন, ক্যাটাগরি বেছে নিন এবং বাংলা ও ইংরেজিতে প্রকাশ করুন।', 'image_url' => '/images/city.svg', 'image_credit' => 'AutoNews · Original editorial illustration', 'status' => 'published', 'seo_title_en' => 'Rethinking our cities: designing urban life around people', 'seo_title_bn' => 'টেকসই নগরের ভাবনা: মানুষের জন্য কেমন শহর চাই', 'meta_description_en' => 'Green spaces and better connections help us imagine more welcoming places to live.', 'meta_description_bn' => 'সবুজ জায়গা, সহজ যাতায়াত ও মানুষের প্রয়োজন নিয়ে নগর পরিকল্পনার আলোচনা।']);
        $category = Category::firstOrCreate(['slug' => 'economy'], ['name_en' => 'Economy', 'name_bn' => 'অর্থনীতি']);
        Post::firstOrCreate(['slug' => 'demo-story-6'], ['category_id' => $category->id, 'published_at' => now()->subHours(6), 'featured' => false, 'is_demo' => true, 'title_en' => 'Small ventures, bigger dreams: the spirit of entrepreneurship', 'title_bn' => 'ছোট উদ্যোগ, বড় স্বপ্ন: উদ্যোক্তাদের পথচলার গল্প', 'excerpt_en' => 'How practical ideas and persistence can turn a small beginning into a meaningful venture.', 'excerpt_bn' => 'একটি ভাবনা থেকে উদ্যোগ—সৃজনশীলতার সঙ্গে এগিয়ে যাওয়ার সম্ভাবনা।', 'body_en' => 'How practical ideas and persistence can turn a small beginning into a meaningful venture.

This is an original demonstration article created to preview the AutoNews design and bilingual publishing experience. It does not describe a verified news event.

Use the dashboard to replace this example with your own reporting, upload a cover image, choose a category and publish in both Bangla and English.', 'body_bn' => 'একটি ভাবনা থেকে উদ্যোগ—সৃজনশীলতার সঙ্গে এগিয়ে যাওয়ার সম্ভাবনা।

এটি AutoNews-এর নকশা ও দুই ভাষার প্রকাশনা পরীক্ষা করার জন্য তৈরি মৌলিক ডেমো লেখা। এটি কোনো যাচাইকৃত বাস্তব ঘটনার সংবাদ নয়।

ড্যাশবোর্ড থেকে এই নমুনা সরিয়ে আপনার নিজের সংবাদ লিখুন। ছবি যুক্ত করুন, ক্যাটাগরি বেছে নিন এবং বাংলা ও ইংরেজিতে প্রকাশ করুন।', 'image_url' => '/images/economy.svg', 'image_credit' => 'AutoNews · Original editorial illustration', 'status' => 'published', 'seo_title_en' => 'Small ventures, bigger dreams: the spirit of entrepreneurship', 'seo_title_bn' => 'ছোট উদ্যোগ, বড় স্বপ্ন: উদ্যোক্তাদের পথচলার গল্প', 'meta_description_en' => 'How practical ideas and persistence can turn a small beginning into a meaningful venture.', 'meta_description_bn' => 'একটি ভাবনা থেকে উদ্যোগ—সৃজনশীলতার সঙ্গে এগিয়ে যাওয়ার সম্ভাবনা।']);
        $category = Category::firstOrCreate(['slug' => 'lifestyle'], ['name_en' => 'Lifestyle', 'name_bn' => 'জীবনযাপন']);
        Post::firstOrCreate(['slug' => 'demo-story-7'], ['category_id' => $category->id, 'published_at' => now()->subHours(7), 'featured' => false, 'is_demo' => true, 'title_en' => 'Closer to nature: finding a little green in our everyday lives', 'title_bn' => 'প্রকৃতির কাছে ফিরে: ব্যস্ততার মাঝে একটু সবুজের খোঁজ', 'excerpt_en' => 'Simple ways to make space for quieter moments and connect with the world outdoors.', 'excerpt_bn' => 'ব্যস্ত জীবনেও প্রকৃতির সঙ্গে সময় কাটানোর সহজ কিছু ভাবনা।', 'body_en' => 'Simple ways to make space for quieter moments and connect with the world outdoors.

This is an original demonstration article created to preview the AutoNews design and bilingual publishing experience. It does not describe a verified news event.

Use the dashboard to replace this example with your own reporting, upload a cover image, choose a category and publish in both Bangla and English.', 'body_bn' => 'ব্যস্ত জীবনেও প্রকৃতির সঙ্গে সময় কাটানোর সহজ কিছু ভাবনা।

এটি AutoNews-এর নকশা ও দুই ভাষার প্রকাশনা পরীক্ষা করার জন্য তৈরি মৌলিক ডেমো লেখা। এটি কোনো যাচাইকৃত বাস্তব ঘটনার সংবাদ নয়।

ড্যাশবোর্ড থেকে এই নমুনা সরিয়ে আপনার নিজের সংবাদ লিখুন। ছবি যুক্ত করুন, ক্যাটাগরি বেছে নিন এবং বাংলা ও ইংরেজিতে প্রকাশ করুন।', 'image_url' => '/images/travel.svg', 'image_credit' => 'AutoNews · Original editorial illustration', 'status' => 'published', 'seo_title_en' => 'Closer to nature: finding a little green in our everyday lives', 'seo_title_bn' => 'প্রকৃতির কাছে ফিরে: ব্যস্ততার মাঝে একটু সবুজের খোঁজ', 'meta_description_en' => 'Simple ways to make space for quieter moments and connect with the world outdoors.', 'meta_description_bn' => 'ব্যস্ত জীবনেও প্রকৃতির সঙ্গে সময় কাটানোর সহজ কিছু ভাবনা।']);
        $category = Category::firstOrCreate(['slug' => 'bangladesh'], ['name_en' => 'Bangladesh', 'name_bn' => 'বাংলাদেশ']);
        Post::firstOrCreate(['slug' => 'demo-story-8'], ['category_id' => $category->id, 'published_at' => now()->subHours(8), 'featured' => false, 'is_demo' => true, 'title_en' => 'Learning beyond the page: a world of everyday discovery', 'title_bn' => 'শেখার নতুন পথ: বইয়ের বাইরেও জানার অসীম জগৎ', 'excerpt_en' => 'Everyday curiosity and a willingness to explore can make learning a lifelong habit.', 'excerpt_bn' => 'কৌতূহল আর অনুশীলনে প্রতিদিন নতুন কিছু শেখার সুযোগ।', 'body_en' => 'Everyday curiosity and a willingness to explore can make learning a lifelong habit.

This is an original demonstration article created to preview the AutoNews design and bilingual publishing experience. It does not describe a verified news event.

Use the dashboard to replace this example with your own reporting, upload a cover image, choose a category and publish in both Bangla and English.', 'body_bn' => 'কৌতূহল আর অনুশীলনে প্রতিদিন নতুন কিছু শেখার সুযোগ।

এটি AutoNews-এর নকশা ও দুই ভাষার প্রকাশনা পরীক্ষা করার জন্য তৈরি মৌলিক ডেমো লেখা। এটি কোনো যাচাইকৃত বাস্তব ঘটনার সংবাদ নয়।

ড্যাশবোর্ড থেকে এই নমুনা সরিয়ে আপনার নিজের সংবাদ লিখুন। ছবি যুক্ত করুন, ক্যাটাগরি বেছে নিন এবং বাংলা ও ইংরেজিতে প্রকাশ করুন।', 'image_url' => '/images/education.svg', 'image_credit' => 'AutoNews · Original editorial illustration', 'status' => 'published', 'seo_title_en' => 'Learning beyond the page: a world of everyday discovery', 'seo_title_bn' => 'শেখার নতুন পথ: বইয়ের বাইরেও জানার অসীম জগৎ', 'meta_description_en' => 'Everyday curiosity and a willingness to explore can make learning a lifelong habit.', 'meta_description_bn' => 'কৌতূহল আর অনুশীলনে প্রতিদিন নতুন কিছু শেখার সুযোগ।']);
        $category = Category::firstOrCreate(['slug' => 'technology'], ['name_en' => 'Technology', 'name_bn' => 'প্রযুক্তি']);
        Post::firstOrCreate(['slug' => 'demo-story-9'], ['category_id' => $category->id, 'published_at' => now()->subHours(9), 'featured' => false, 'is_demo' => true, 'title_en' => 'Everyday habits for a more secure digital life', 'title_bn' => 'ডিজিটাল জীবনে নিজের তথ্য সুরক্ষিত রাখার অভ্যাস', 'excerpt_en' => 'Small, practical steps can help us make more informed choices about our digital lives.', 'excerpt_bn' => 'অনলাইনে সচেতন ব্যবহার শুরু হোক ছোট ছোট পদক্ষেপ দিয়ে।', 'body_en' => 'Small, practical steps can help us make more informed choices about our digital lives.

This is an original demonstration article created to preview the AutoNews design and bilingual publishing experience. It does not describe a verified news event.

Use the dashboard to replace this example with your own reporting, upload a cover image, choose a category and publish in both Bangla and English.', 'body_bn' => 'অনলাইনে সচেতন ব্যবহার শুরু হোক ছোট ছোট পদক্ষেপ দিয়ে।

এটি AutoNews-এর নকশা ও দুই ভাষার প্রকাশনা পরীক্ষা করার জন্য তৈরি মৌলিক ডেমো লেখা। এটি কোনো যাচাইকৃত বাস্তব ঘটনার সংবাদ নয়।

ড্যাশবোর্ড থেকে এই নমুনা সরিয়ে আপনার নিজের সংবাদ লিখুন। ছবি যুক্ত করুন, ক্যাটাগরি বেছে নিন এবং বাংলা ও ইংরেজিতে প্রকাশ করুন।', 'image_url' => '/images/city.svg', 'image_credit' => 'AutoNews · Original editorial illustration', 'status' => 'published', 'seo_title_en' => 'Everyday habits for a more secure digital life', 'seo_title_bn' => 'ডিজিটাল জীবনে নিজের তথ্য সুরক্ষিত রাখার অভ্যাস', 'meta_description_en' => 'Small, practical steps can help us make more informed choices about our digital lives.', 'meta_description_bn' => 'অনলাইনে সচেতন ব্যবহার শুরু হোক ছোট ছোট পদক্ষেপ দিয়ে।']);
    }
}
