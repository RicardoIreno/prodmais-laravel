@extends('layouts.layout')

@section('title', 'Prodmais')

@section('content')
<main class="container">
    <h1>Prodmais</h1>


    <form method="get" action="{{url('works')}}">
        <div class="mb-3">
            <label for="search" class="form-label">Expressão de busca</label>
            <input type="text" class="form-control" id="search" aria-describedby="searchHelp">
            <div id="searchHelp" class="form-text"></div>
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>

</main>
@stop