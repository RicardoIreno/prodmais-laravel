<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\LattesController;
use App\Http\Controllers\ExportersController;
use App\Models\Person;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/upload', function () {
    return view('upload');
});

Route::resource('people', PersonController::class);
Route::resource('works', WorkController::class);

Route::post('/lattes', [LattesController::class, 'processXML']);

Route::get('/person/{id}', function (Person $id) {
    $id->load(['works' => function ($query) {
        $query->orderBy('datePublished', 'desc');
    }])->load(['projetos' => function ($query) {
        $query->orderBy('name', 'desc');
    }]);
    return view('person', compact('id'));
});

Route::get('graficos', [WorkController::class, 'graficos'])->name('graficos');

Route::get('/sobre', function () {
    return view('sobre');
});

Route::get('/exporters/bibtex/{id}', [ExportersController::class, 'bibtex']);
