@extends('layouts.layout')

@section('title', 'Prodmais - Produção Intelectual')

@section('content')
<main class="container">
    <h2>Prodmais - Produção Intelectual</h2>
    <div class="row">
        <div class="col col-lg-12">
            <h3 class="mt-2">
                Resultado da busca <span class="badge text-bg-light fw-lighter">Exibindo
                    {{ $works->firstItem() }}
                    a {{ $works->lastItem() }} de {{ $works->total() }} registros</span>
            </h3>
            <form action="works" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Pesquisar no título" name="name">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </div>
            </form>
            <div class="d-flex mt-3 mb-3">
                <div class="mx-auto">
                    {{ $works->links() }}
                </div>
            </div>
        </div>
        <div class="col col-lg-4">
            <h3>Refinar resultados <a href="works" class="btn btn-warning">Limpar busca</a> </h3>
            @if (
            $request->has('name')||
            $request->has('type')||
            $request->has('datePublished')||
            $request->has('author')||
            $request->has('about')||
            $request->has('isPartOf_name')||
            $request->has('releasedEvent')||
            $request->has('inLanguage')||
            $request->has('issn')||
            $request->has('sourceOrganization')||
            $request->has('publisher')
            )
            <div class="alert alert-light" role="alert">
                Filtros ativos: <br>
                @foreach ($request->all() as $key => $value)
                @if ($key != 'page')
                <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
                    @php
                    if ($key == 'author') {
                    $key_name = 'Autor';
                    }
                    if ($key == 'name') {
                    $key_name = 'Título';
                    }
                    if ($key == 'about') {
                    $key_name = 'Assunto';
                    }
                    if ($key == 'type') {
                    $key_name = 'Tipo';
                    }
                    if ($key == 'datePublished') {
                    $key_name = 'Ano de publicação';
                    }
                    if ($key == 'isPartOf_name') {
                    $key_name = 'Publicação';
                    }
                    if ($key == 'releasedEvent') {
                    $key_name = 'Nome do evento';
                    }
                    if ($key == 'inLanguage') {
                    $key_name = 'Idioma';
                    }
                    if ($key == 'issn') {
                    $key_name = 'ISSN';
                    }
                    if ($key == 'publisher') {
                    $key_name = 'Editora';
                    }
                    if ($key == 'sourceOrganization') {
                    $key_name = 'Instituição';
                    }
                    @endphp
                    <a type="button" class="btn btn-outline-warning mb-1"
                        href="works?{{ http_build_query(array_diff_key($request->all(), [$key => $value])) }}">
                        {{ $key_name }}: {{ $value }} (X)
                    </a>
                </div>
                @endif
                @endforeach
            </div>
            @endif


            <div class="accordion" id="facets">
                <x-facet field="datePublished" fieldName="Ano de publicação" :request="$request" />
            </div>

        </div>
        <div class="col col-lg-8">
            <h3>Resultados</h3>
            @if ($works->count() == 0)
            <div class="alert alert-warning" role="alert">
                Nenhum registro encontrado
            </div>
            @endif
            @foreach($works as $key => $value)
            <p>{{ $value->name }}</p>
            <ul>

                <li>Ano de publicação: {{ $value->datePublished }}</li>
                <li>DOI: <a href="https://doi.org/{{ $value->doi }}" target="_blank"
                        rel="nofollow">{{ $value->doi }}</a>
                </li>
            </ul>
            @endforeach
            <div class=" d-flex mt-3 mb-3">
                <div class="mx-auto">
                    {{ $works->links() }}
                </div>
            </div>
        </div>
    </div>
</main>
@stop