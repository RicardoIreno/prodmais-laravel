@extends('layouts.layout')

@section('title', 'Prodmais - Produção Intelectual')

@section('content')
<main class="container">
    <h1>Prodmais - Produção Intelectual</h1>
    <div class="row">
        <div class="col col-lg-4">
            <h3>Refinar resultados <a href="works" class="btn btn-warning">Limpar busca</a> </h3>
        </div>
        <div class="col col-lg-8">
            <h3>Resultados</h3>
            @foreach($works as $key => $value)
            <p>{{ $value->name }}</p>
            @endforeach
        </div>
    </div>
</main>
@stop