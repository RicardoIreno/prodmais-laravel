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
        'genero' => 'array',
        'isbn' => 'array',
        'isPartOf' => 'array',
        'instituicao' => 'array',
        'ppg_nome' => 'array',
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
        'educationEventName',
        'genero',
        'inLanguage',
        'instituicao',
        'isbn',
        'isPartOf',
        'isPartOfName',
        'issn',
        'issueNumber',
        'name',
        'numberOfPages',
        'pageEnd',
        'pageStart',
        'ppg_nome',
        'publisher',
        'qualis',
        'sha256',
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