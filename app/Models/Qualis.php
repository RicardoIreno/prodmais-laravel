<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualis extends Model
{
    use HasFactory;

    protected $fillable = [
        'issn',
        'titulo',
        'estrato',
        'area',
        'ano'
    ];
}