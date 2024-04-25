@extends('layouts.layout')

@section('title', 'Consulta QUALIS')

@section('content')

<main class="p-about">
    <div class="c-wrapper-container">
        <div class="row">
            <div class="col col-lg-12">
                <h2 class="mt-2 mb-2">Pesquisa Qualis Periódicos - <a
                        href="https://sucupira.capes.gov.br/sucupira/public/consultas/coleta/veiculoPublicacaoQualis/listaConsultaGeralPeriodicos.jsf"
                        target="_blank">Fonte</a> - <a class="btn btn-warning" href="qualis">Limpar filtros</a>
                </h2>
                <form action="qualis" method="get">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Pesquisar por ISSN" name="issn"
                            value="{{ request()->issn }}">
                        <button class="btn btn-primary" type="submit">Pesquisar</button>
                    </div>
                </form>
                <form action="qualis" method="get">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Pesquisar por parte do título"
                            name="titulo" value="{{ request()->titulo }}">
                        <button class="btn btn-primary" type="submit">Pesquisar</button>
                    </div>
                </form>
                <div class="d-flex mt-3 mb-3">
                    <div class="mx-auto">
                        {!! $qualis->withQueryString()->links() !!}
                    </div>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">ISSN</th>
                            <th scope="col">Título <a
                                    href="{{ $qualis->appends(['orderByTitulo' => 'asc'])->url(1) }}">&uarr;</a><a
                                    href="{{ $qualis->appends(['orderByTitulo' => 'desc'])->url(1) }}">&darr;</a></th>
                            <th scope="col">Área de avaliação</th>
                            <th scope="col">Estrato <a
                                    href="{{ $qualis->appends(['orderByEstrato' => 'asc'])->url(1) }}">&uarr;</a><a
                                    href="{{ $qualis->appends(['orderByEstrato' => 'desc'])->url(1) }}">&darr;</a></th>
                            <th scope="col">Ano</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($qualis as $q)
                        <tr>
                            <th scope="row">{{ $q->issn }}</th>
                            <td>{{ $q->titulo }}</td>
                            <td><a href="qualis?area={{ $q->area }}">{{ $q->area }}</a></td>
                            <td>{{ $q->estrato }}</td>
                            <td>{{ $q->ano }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex mt-3 mb-3">
                    <div class="mx-auto">
                        {!! $qualis->withQueryString()->links() !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

@endsection