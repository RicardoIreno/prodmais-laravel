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
        'isbn' => 'array'
    ];

    protected $fillable = [
        'about',
        'author',
        'datePublished',
        'doi',
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