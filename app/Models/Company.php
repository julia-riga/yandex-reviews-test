<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'yandex_id',
        'name',
        'rating',
        'rating_count',
        'review_count',
        'last_parsed_at',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}