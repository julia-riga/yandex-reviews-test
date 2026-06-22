<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'author',
        'date',
        'text',
        'rating',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
