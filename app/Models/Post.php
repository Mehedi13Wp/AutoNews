<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'featured' => 'boolean', 'is_demo' => 'boolean'];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function scopeVisible(Builder $q): void
    {
        $q->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function text(string $field): string
    {
        return $this->{$field.'_'.app()->getLocale()} ?? '';
    }

    public function image(): string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : ($this->image_url ?: asset('images/placeholder.svg'));
    }
}
