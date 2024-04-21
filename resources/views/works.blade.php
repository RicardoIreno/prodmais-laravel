@extends('layouts.layout')

@section('title', 'Prodmais - Produção Intelectual')

@section('content')
<div class="p-result-container">

    <nav class="p-result-nav">
        <details id="filterlist" class="c-filterlist" onload="resizeMenu" open="">
            <summary class="c-filterlist__header">
                <h3 class="c-filterlist__title">Refinar resultados</h3>
            </summary>
            @if (
            $request->has('name')||
            $request->has('type')||
            $request->has('datePublished')||
            $request->has('author')||
            $request->has('author_array')||
            $request->has('about')||
            $request->has('isPartOf')||
            $request->has('educationEvent')||
            $request->has('releasedEvent')||
            $request->has('inLanguage')||
            $request->has('issn')||
            $request->has('sourceOrganization')||
            $request->has('publisher')
            )
            <div class="alert alert-light" role="alert">
                <a href="works" class="btn btn-warning btn-sm">Limpar
                    busca</a><br /><br /><br />
                Filtros ativos: <br />
                @foreach ($request->all() as $key => $work)
                @if ($key != 'page')
                <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
                    @php
                    if ($key == 'author') {
                    $key_name = 'Autor';
                    }
                    if ($key == 'author_array') {
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
                    if ($key == 'isPartOf') {
                    $key_name = 'Publicação';
                    }
                    if ($key == 'educationEvent') {
                    $key_name = 'Nome do evento';
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
                        href="works?{{ http_build_query(array_diff_key($request->all(), [$key => $work])) }}">
                        {{ $key_name }}: {{ $work }} (X)
                    </a>
                </div>
                @endif
                @endforeach
            </div>
            @endif
            <div class="c-filterlist__content">
                <div class="accordion" id="facets">
                    <x-facet field="type" fieldName="Tipo de publicação" type="Work" :request="$request" />
                    <x-facetArray field="author_array" fieldName="Autores" :request="$request" type="Work" />
                    <x-facet field="datePublished" fieldName="Ano de publicação" type="Work" :request="$request" />
                    <x-facet field="inLanguage" fieldName="Idioma" type="Work" :request="$request" />
                    <x-facetArray field="about" fieldName="Assuntos" :request="$request" type="Work" />
                    <x-facet field="isPartOf" fieldName="É parte de" type="Work" :request="$request" />
                    <x-facet field="educationEvent" fieldName="Nome do evento" type="Work" :request="$request" />
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
                    <input type="text" class="form-control" placeholder="Buscar no campo título" name="name">
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
            @if ($works->count() == 0)
            <div class="alert alert-warning" role="alert">
                Nenhum registro encontrado
            </div>
            @endif
            <ul>
                @foreach($works as $work)

                <li class='s-list-2'>

                    <div class='s-list-bullet'>
                        <i>{{ $work->type }}</i><i class='i i-articlePublished s-list-ico' title='articlePublished'></i>
                    </div>

                    <div class='s-list-content'>
                        <p class='t t-b t-md'>{{ $work->name }} ({{ $work->datePublished }})</p>

                        @if(is_array($work->author) && count($work->author) > 0)
                        <p class='t-gray mb-2 mt-2'><b class='t-subItem'>Autores: </b>
                            {!! implode(', ', array_map(function($author) {
                            return e($author['NOME-COMPLETO-DO-AUTOR']) . (!empty($author['NRO-ID-CNPQ']) ?
                            '<a href="https://lattes.cnpq.br/' . e($author['NRO-ID-CNPQ']) . '" target="_blank"
                                rel="external"><img class="c-socialiconalt"
                                    src="' . e(url('/')) . '/images/logos/logo_lattes.svg" alt="Lattes"
                                    title="Lattes" /></a>' : '');
                            }, $work->author)) !!}
                        </p>
                        @endif


                        @if(!empty($work->doi) or !empty($work->url))
                        <p>Acesso ao texto completo:
                            @if(!empty($work->doi))

                            <a class="t t-a d-icon-text" href="https://doi.org/{{ $work->doi }}" target="blank">
                                <img class="i-doi" src="{{ e(url('/')) }}/images/logos/doi.svg" title="doi" alt="doi">
                            </a>

                            @endif

                            @if(!empty($work->url))

                            <a href="{{ $work->url }}" target="_blank" rel="nofollow">{{ $work->url }}</a>

                            @endif
                        </p>
                        @endif

                        <!-- @if(!empty($work->inLanguage))
                        <p>
                            Idioma: {{ $work->inLanguage }}
                        </p>
                        @endif -->

                        @if(!empty($work->educationEvent))
                        <ul>
                            <li>Nome do evento: {{ $work->educationEvent['name'] }}</li>
                            <li>Cidade do evento: {{ $work->educationEvent['city'] }}</li>
                            <li>Ano: {{ $work->educationEvent['year'] }}</li>
                            <li>Classificação do evento: {{ $work->educationEvent['classification'] }}</li>
                            <li>Título da publicação: {{ $work->educationEvent['isPartOf'] }}</li>
                            <li>Volume: {{ $work->educationEvent['volumeNumber'] }}</li>
                            <li>Fascículo: {{ $work->educationEvent['issueNumber'] }}</li>
                            <li>Paginação:
                                {{ $work->educationEvent['pageStart'] }}-{{ $work->educationEvent['pageEnd'] }}
                            </li>
                            <li>ISBN: {{ $work->educationEvent['isbn'] }}</li>
                            <li>
                                Editora / Cidade: {{ $work->educationEvent['publisher']['name'] }} /
                                {{ $work->educationEvent['publisher']['city'] }}
                            </li>
                        </ul>
                        @endif


                        @if(!empty($work->isPartOf))
                        <p class='t t-light'>
                        <ul>
                            <li>Publicação: {{ $work->isPartOf['name'] }}</li>
                            @if (isset($work->isPartOf['issn']))
                            <li>ISSN: {{ $work->isPartOf['issn'] }}</li>
                            @endif
                            @if (isset($work->isPartOf['isbn']))
                            <li>ISBN: {{ $work->isPartOf['isbn'] }}</li>
                            @endif
                            @if (isset($work->isPartOf['volumeNumber']))
                            <li>Volume: {{ $work->isPartOf['volumeNumber'] }}</li>
                            @endif
                            @if (isset($work->isPartOf['issueNumber']))
                            <li>Fascículo: {{ $work->isPartOf['issueNumber'] }}</li>
                            @endif
                            <li>
                                Paginação:
                                {{ $work->isPartOf['pageStart'] }}-{{ $work->isPartOf['pageEnd'] }}
                            </li>
                            @if (isset($work->isPartOf['bookEdition']))
                            <li>Edição: {{ $work->isPartOf['bookEdition'] }}</li>
                            @endif
                            @if (isset($work->isPartOf['publisher']['name']))
                            <li>Editora: {{ $work->isPartOf['publisher']['name'] }}</li>
                            @endif
                            @if (isset($work->isPartOf['publisher']['city']))
                            <li>Cidade da editora: {{ $work->isPartOf['publisher']['city'] }}</li>
                            @endif
                        </ul>
                        </p>
                        @endif


                        @if(!empty($work->isbn))
                        <p class='t t-light'>
                        <ul>
                            <li>ISBN: {{ $work->isbn }}</li>
                            <li>Edição: {{ $work->bookEdition }}</li>
                            <li>País: {{ $work->country }}</li>
                            <li>Número de páginas: {{ $work->numberOfPages }}</li>
                            <li>Editora: {{ $work->publisher['name'] }}</li>
                            <li>Cidade da editora: {{ $work->publisher['city'] }}</li>
                        </ul>
                        </p>
                        @endif


                        @if(is_array($work->about))
                        <p class='d-linewrap t-gray'>
                            Assuntos: {{ implode(", ", $work->about) }}
                        </p>
                        @endif

                    </div>
                </li>
                @endforeach
            </ul>

            <div class=" d-flex mt-3 mb-3">
                <div class="mx-auto">
                    {{ $works->links() }}
                </div>
            </div>
        </div>
    </main>
</div>
@stop