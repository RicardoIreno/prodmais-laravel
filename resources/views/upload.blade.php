<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prodmais - Upload de XML do Lattes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>

<body>
    <main class="container">
        <h1>Prodmais - Upload de XML do Lattes</h1>
        <form action="{{url('lattes')}}" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                @csrf
                <label for="formLattesXML" class="form-label">Upload do arquivo curriculo.xml do Lattes</label>
                <input class="form-control" type="file" id="formLattesXML" name='file'>
            </div>
            <button type="submit" class="btn btn-primary">Enviar XML</button>
        </form>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

</body>

</html>