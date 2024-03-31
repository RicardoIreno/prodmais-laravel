@extends('layouts.layout')

@section('title', 'Prodmais - Produção Intelectual')

@section('content')
<main class="container">
    <h1>Prodmais - Produção Intelectual</h1>
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
            @endforeach
            <div class="d-flex mt-3 mb-3">
                <div class="mx-auto">
                    {{ $works->links() }}
                </div>
            </div>
        </div>
    </div>
</main>
@stop