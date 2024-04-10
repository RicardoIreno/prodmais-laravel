<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $casts = [
        'formacao' => 'array',
        'idiomas' => 'array',
    ];

    protected $fillable = [
        'formacao',
        'id',
        'idiomas',
        'lattesDataAtualizacao',
        'nacionalidade',
        'name',
        'orcid',
        'resumoCVpt',
        'resumoCVen'
    ];
}
