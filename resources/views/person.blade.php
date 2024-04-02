@extends('layouts.layout')

@section('title', 'Prodmais - Pesquisadores')

@section('content')

<div class="container mt-3">

    <h1>{{ $id->name }}</h1>
    <ul>
        <li>ORCID: <a href="{{ $id->orcid }}">{{ $id->orcid }}</a></li>
        <li>Data de atualização do Lattes: {{ $id->lattesDataAtualizacao }}</li>
        <li>Resumo: {{ $id->resumoCVpt }}</li>
        <li>Resumo em inglês: {{ $id->resumoCVen }}</li>
    </ul>

</div>

@stop