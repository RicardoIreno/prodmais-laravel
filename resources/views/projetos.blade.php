@extends('layouts.layout')

@section('title', 'Prodmais - Projetos de pesquisa')

@section('content')
<div class="p-result-container">

    <nav class="p-result-nav">
        <details id="filterlist" class="c-filterlist" onload="resizeMenu" open="">
            <summary class="c-filterlist__header">
                <h3 class="c-filterlist__title">Refinar resultados</h3>
            </summary>
            @if (
            $request->has('name')||
            $request->has('genero')||
            $request->has('type')||
            $request->has('instituicao')||
            $request->has('situacao')||
            $request->has('unidade')||
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
                    $key_name = 'Projetos';
                    }
                    if ($key == 'genero') {
                    $key_name = 'Gênero';
                    }
                    if ($key == 'type') {
                    $key_name = 'Tipo';
                    }
                    if ($key == 'instituicao') {
                    $key_name = 'Instituição';
                    }
                    if ($key == 'situacao') {
                    $key_name = 'Situação';
                    }
                    if ($key == 'unidade') {
                    $key_name = 'Unidade';
                    }
                    if ($key == 'ppg_nome') {
                    $key_name = 'Programa de Pós-Graduação';
                    }
                    @endphp
                    <a type="button" class="btn btn-outline-warning mb-1" href="projetos?{{ http_build_query(array_diff_key($request->all(), [$key => $work])) }}">
                        {{ $key_name }}: {{ $work }} (X)
                    </a>
                </div>
                @endif
                @endforeach
            </div>
            @endif
            <div class="c-filterlist__content">
                <div class="accordion" id="facets">
                    <x-facet field="instituicao" fieldName="Instituição" type="Projeto" :request="$request" />
                    <x-facet field="situacao" fieldName="Situação" type="Projeto" :request="$request" />
                </div>
            </div>
        </details>
    </nav>

    <main class="p-result-main">
        <div class="col col-lg-12">
            <h3 class="mt-2">
                Resultado da busca <span class="badge text-bg-light fw-lighter">Exibindo
                    {{ $projeto->firstItem() }}
                    a {{ $projeto->lastItem() }} de {{ $projeto->total() }} registros</span>
            </h3>
            <form action="projetos" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Pesquisar no título" name="name">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </div>
            </form>
            <div class="d-flex mt-3 mb-3">
                <div class="mx-auto">
                    {{ $projeto->links() }}
                </div>
            </div>

            <div class="row">
                @if ($projeto->count() == 0)
                <div class="alert alert-warning" role="alert">
                    Nenhum registro encontrado
                </div>
                @endif
                <ul class='c-authors-list'>
                    @foreach($projeto as $key => $value)

                    <li class='s-list-2'>

                        <div class='s-list-bullet'>
                            <i class='i i-research s-list-ico' title='projeto de pesquisa'></i>
                        </div>

                        <div class='s-list-content'>
                            <p class='t t-b t-md'>{{ $value->name }}</p>

                            <div class="row">

                                <div class="col">

                                    <p class='d-linewrap t-gray mt-2 mb-2'>
                                        {{ $value->instituicao }}
                                    </p>

                                    <p class='d-linewrap t-gray mt-2'>
                                        Integrantes: {{ implode(", ", $value->integrantes) }}
                                    </p>

                                    @if(isset($value->description))
                                    <p class='d-linewrap t-gray mt-2'>
                                        Descrição: {{ $value->description }}
                                    </p>
                                    @endif

                                    <p class='t t-gray'>Ano de início: {{ $value->projectYearStart }}</p>
                                    <p class='t t-gray'>Ano de término: {{ $value->projectYearEnd }}</p>
                                    <p class='t t-gray'>Situação: {{ $value->situacao }}</p>

                                </div>
                            </div>

                        </div>
                    </li>



                    @endforeach
                </ul>
            </div>
            <div class=" d-flex mt-3 mb-3">
                <div class="mx-auto">
                    {{ $projeto->links() }}
                </div>
            </div>

    </main>
</div>
@stop