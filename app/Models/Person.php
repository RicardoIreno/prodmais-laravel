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
        'instituicao' => 'array',
        'orientacoesConcluidas' => 'array',
        'orientacoesEmAndamento' => 'array',
        'ppg_nome' => 'array'
    ];

    protected $fillable = [
        'atuacao',
        'curso_nome',
        'departamento',
        'email',
        'formacao',
        'genero',
        'id',
        'idiomas',
        'instituicao',
        'lattesDataAtualizacao',
        'lattesID10',
        'nacionalidade',
        'name',
        'nomeCitacoesBibliograficas',
        'orcid',
        'orientacoesConcluidas',
        'orientacoesEmAndamento',
        'ppg_nome',
        'resumoCVpt',
        'resumoCVen',
        'tipvin',
        'unidade'
    ];

    public function works()
    {
        return $this->belongsToMany(Work::class, 'person_work');
    }
}