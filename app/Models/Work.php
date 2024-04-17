<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    protected $casts = [
        'about' => 'array',
        'author' => 'array',
        'author_array' => 'array',
        'isbn' => 'array'
    ];

    protected $fillable = [
        'about',
        'author',
        'author_array',
        'datePublished',
        'doi',
        'educationEvent',
        'inLanguage',
        'isbn',
        'isPartOf',
        'issn',
        'issueNumber',
        'name',
        'pageEnd',
        'pageStart',
        'type',
        'url',
        'volumeNumber'
    ];
}