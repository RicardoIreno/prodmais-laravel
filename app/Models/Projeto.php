<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projeto extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'name',
        'projectYearEnd',
        'projectYearStart',
        'situacao'
    ];

    protected $with = ['authors'];

    public function authors()
    {
        return $this->belongsToMany(Person::class, 'person_projeto')->withTimestamps();
    }
}
