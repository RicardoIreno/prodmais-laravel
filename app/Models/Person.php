<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $casts = [
        'atuacao' => 'array',
        'formacao' => 'array',
        'idiomas' => 'array',
    ];

    protected $fillable = [
        'atuacao',
        'formacao',
        'id',
        'idiomas',
        'lattesDataAtualizacao',
        'lattesID10',
        'nacionalidade',
        'name',
        'nomeCitacoesBibliograficas',
        'orcid',
        'resumoCVpt',
        'resumoCVen'
    ];
}