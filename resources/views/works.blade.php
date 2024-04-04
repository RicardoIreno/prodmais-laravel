@extends('layouts.layout')

@section('title', 'Prodmais - Produção Intelectual')

@section('content')
<div class="p-result-container">

    <nav class="p-result-nav">
        <details id="filterlist" class="c-filterlist" onload="resizeMenu" open="">
            <summary class="c-filterlist__header">
                <h3 class="c-filterlist__title">Refinar resultados <a href="works" class="btn btn-warning btn-sm">Limpar
                        busca</a></h3>
            </summary>
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

            <div class="c-filterlist__content">
                <div class="accordion" id="facets">
                    <x-facet field="datePublished" fieldName="Ano de publicação" :request="$request" />
                    <x-facet field="inLanguage" fieldName="Idioma" :request="$request" />
                </div>

            </div>
        </details>
    </nav>

    <main class="p-result-main">
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
        <div class="row">

            <h3>Resultados</h3>
            @if ($works->count() == 0)
            <div class="alert alert-warning" role="alert">
                Nenhum registro encontrado
            </div>
            @endif
            <ul>
                @foreach($works as $key => $value)

                <li class='s-list-2'>
                    <div class='s-list-bullet'>
                        <i class='i i-articlePublished s-list-ico' title='articlePublished'></i>
                    </div>

                    <div class='s-list-content'>
                        <p class='t t-b t-md'>{{ $value->name }} ({{ $value->datePublished }})</p>
                        <p class='t t-b t-md'><i>Tipo</i></p>
                        <p class='t-gray'>
                            <b class='t-subItem'>Autores: </b>
                        <ul>
                            @forelse ($value->author as $author)
                            <li class="list-group-item"><a
                                    href="https://lattes.cnpq.br/{{ $author['NRO-ID-CNPQ'] }}">{{ $author['NOME-COMPLETO-DO-AUTOR'] }}</a>
                            </li>
                            @empty
                            <p>Sem autores</p>
                            @endforelse
                        </ul>
                        </p>

                        <p class='d-linewrap t-gray'>
                        </p>
                        <p class='mt-3'>
                            DOI: <a href="https://doi.org/{{ $value->doi }}" target="_blank"
                                rel="nofollow">{{ $value->doi }}</a>
                        </p>

                        <p class='t t-light'>
                            Fonte:
                        </p>

                        <p class='t-gray'>
                            <b class='t-subItem'>Assuntos: </b>
                        <ul>
                            @forelse ($value->about as $about)
                            <li class="list-group-item">{{ $about }} </li>
                            @empty
                            <p>Sem assuntos</p>
                            @endforelse
                        </ul>
                        </p>
                    </div>
                </li>

                <!-- <li>Ano de publicação: {{ $value->datePublished }}</li>
                <li>DOI: <a href="https://doi.org/{{ $value->doi }}" target="_blank"
                        rel="nofollow">{{ $value->doi }}</a>
                </li>
                <li>URL: <a href="{{ $value->url }}" target="_blank" rel="nofollow">{{ $value->url }}</a>
                </li>
                <li>Idioma: {{ $value->inLanguage }}</li>
                <li>É parte de: {{ $value->isPartOf }}</li>
                <li>ISSN: {{ $value->issn }}</li>
                <li>Fascículo: {{ $value->issueNumber }}</li>
                <li>Volume: {{ $value->volumeNumber }}</li>
                <li>Página inicial: {{ $value->pageStart }}</li>
                <li>Página final: {{ $value->pageEnd }}</li>
                @foreach($value->about as $about)
                <li>Assuntos: {{ $about }}</li>
                @endforeach
                @foreach($value->author as $author)
                <li>Autores: <a href="https://lattes.cnpq.br/{{ $author['NRO-ID-CNPQ'] }}">
                        {{ $author['NOME-COMPLETO-DO-AUTOR'] }}</a>
                </li>
                @endforeach -->
            </ul>
            @endforeach
            <div class=" d-flex mt-3 mb-3">
                <div class="mx-auto">
                    {{ $works->links() }}
                </div>
            </div>
        </div>
    </main>
</div>
@stop