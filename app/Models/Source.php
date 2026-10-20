<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['enabled' => 'boolean', 'auto_publish' => 'boolean', 'image_rights_confirmed' => 'boolean', 'last_checked_at' => 'datetime'];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
