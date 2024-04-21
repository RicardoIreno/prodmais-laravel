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
        'authorLattesIds' => 'array',
        'educationEvent' => 'array',
        'isbn' => 'array',
        'isPartOf' => 'array',
        'publisher' => 'array'
    ];

    protected $fillable = [
        'about',
        'author',
        'author_array',
        'authorLattesIds',
        'bookEdition',
        'country',
        'datePublished',
        'doi',
        'educationEvent',
        'inLanguage',
        'isbn',
        'isPartOf',
        'issn',
        'issueNumber',
        'name',
        'numberOfPages',
        'pageEnd',
        'pageStart',
        'publisher',
        'type',
        'url',
        'volumeNumber'
    ];

    protected $with = ['authors'];

    public function authors()
    {
        return $this->belongsToMany(Person::class, 'person_work')->withTimestamps();
    }
}