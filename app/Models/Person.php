<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $casts = [
        'idiomas' => 'array',
    ];

    protected $fillable = [
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
