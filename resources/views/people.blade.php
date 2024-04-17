@extends('layouts.layout')

@section('title', 'Prodmais - Pesquisadores')

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
            $request->has('instituicao')||
            $request->has('ppg_nome')
            )
            <div class="alert alert-light" role="alert">
                <a href="people" class="btn btn-warning btn-sm">Limpar
                    busca</a><br /><br /><br />
                Filtros ativos: <br />
                @foreach ($request->all() as $key => $work)
                @if ($key != 'page')
                <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
                    @php
                    if ($key == 'name') {
                    $key_name = 'Pesquisador';
                    }
                    if ($key == 'type') {
                    $key_name = 'Tipo';
                    }
                    if ($key == 'instituicao') {
                    $key_name = 'Instituição';
                    }
                    if ($key == 'ppg_nome') {
                    $key_name = 'Programa de Pós-Graduação';
                    }
                    @endphp
                    <a type="button" class="btn btn-outline-warning mb-1" href="people?{{ http_build_query(array_diff_key($request->all(), [$key => $work])) }}">
                        {{ $key_name }}: {{ $work }} (X)
                    </a>
                </div>
                @endif
                @endforeach
            </div>
            @endif
            <div class="c-filterlist__content">
                <div class="accordion" id="facets">
                    <x-facetArray field="instituicao" fieldName="Instituição" :request="$request" type="Person" />
                    <x-facetArray field="ppg_nome" fieldName="Programa de Pós-Graduação" :request="$request" type="Person" />
                </div>
            </div>
        </details>
    </nav>

    <main class="p-result-main">
        <div class="col col-lg-12">
            <h3 class="mt-2">
                Resultado da busca <span class="badge text-bg-light fw-lighter">Exibindo
                    {{ $people->firstItem() }}
                    a {{ $people->lastItem() }} de {{ $people->total() }} registros</span>
            </h3>
            <form action="people" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Pesquisar no título" name="name">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </div>
            </form>
            <div class="d-flex mt-3 mb-3">
                <div class="mx-auto">
                    {{ $people->links() }}
                </div>
            </div>

            <div class="row">
                @if ($people->count() == 0)
                <div class="alert alert-warning" role="alert">
                    Nenhum registro encontrado
                </div>
                @endif
                <ul class='c-authors-list'>
                    @foreach($people as $key => $value)
                    <li class='c-card-author t t-b t-md'>
                        <a href="person/{{ $value->id }}">{{ $value->name }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class=" d-flex mt-3 mb-3">
                <div class="mx-auto">
                    {{ $people->links() }}
                </div>
            </div>

    </main>
</div>
@stop