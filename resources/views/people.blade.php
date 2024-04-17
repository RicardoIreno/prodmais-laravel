@extends('layouts.layout')

@section('title', 'Prodmais - Pesquisadores')

@section('content')
<div class="p-result-container">

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