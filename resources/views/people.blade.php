@extends('layouts.layout')

@section('title', 'Prodmais - Pesquisadores')

@section('content')
<main class="container">
    <h1>Prodmais - Pesquisadores</h1>
    <div class="row">
        <div class="col col-lg-4">
            <h3>Refinar resultados <a href="people" class="btn btn-warning">Limpar busca</a> </h3>
        </div>
        <div class="col col-lg-8">
            <h3>Pesquisadores</h3>
            @foreach($people as $key => $value)
            <p>{{ $value->name }}</p>
            @endforeach
        </div>
    </div>
</main>
@stop