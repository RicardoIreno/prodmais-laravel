@extends('layouts.layout')

@section('title', 'Prodmais - Upload de XML do Lattes')

@section('content')
<main class="c-wrapper-container">
    <div class="c-wrapper-paper">
        <div class="c-wrapper-inner">
            <h1 class="t t-h1">Prodmais - Upload</h1>
            <div class="c-wrapper-inner">
                <h2 class="t t-h4">Inserir um XML do Lattes</h2>
                <form action="{{url('lattes')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">

                        <label for="formLattesXML" class="form-label">Upload do arquivo curriculo.xml do Lattes</label>

                    </div>
                    <div class="input-group">
                        <input class="form-control" type="file" id="formLattesXML" name='file'>
                        <input class="form-control" type="text" placeholder="Instituição" name="instituicao">
                        <input class="form-control" type="text" placeholder="Unidade" name="unidade">
                        <input class="form-control" type="text" placeholder="Departamento" name="departamento">
                    </div>
                    <div class="input-group">
                        <input class="form-control" type="text" placeholder="Nome do PPG" name="ppg_nome">
                        <input class="form-control" type="text" placeholder="Tipo de vínculo" name="tipvin">
                        <input class="form-control" type="text" placeholder="Genero" name="genero">
                        <input class="form-control" type="text" placeholder="Curso" name="curso_nome">
                        <input class="form-control" type="text" placeholder="E-mail" name="email">
                    </div>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Enviar XML</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@stop