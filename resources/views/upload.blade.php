@extends('layouts.layout')

@section('title', 'Prodmais - Upload de XML do Lattes')

@section('content')
<main class="container">
    <h2>Prodmais - Upload de XML do Lattes</h2>
    <form action="{{url('lattes')}}" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            @csrf
            <label for="formLattesXML" class="form-label">Upload do arquivo curriculo.xml do Lattes</label>
            <input class="form-control" type="file" id="formLattesXML" name='file'>
        </div>
        <button type="submit" class="btn btn-primary">Enviar XML</button>
    </form>
</main>
@stop