<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LattesController extends Controller
{
    public function processXML(Request $request)
    {
        dd($request->all());
        if ($request->file) {
            $lattes = simplexml_load_file($request->file);
            // Verifica se o conteúdo é um XML válido
            if ($lattes) {
                // Sucesso, o XML foi recebido e é válido
                echo 'XML recebido com sucesso!';
                // Aqui você pode manipular o objeto $xml conforme necessário
            } else {
                return Response::json(array('O conteúdo recebido não é um XML válido.' => 'O conteúdo recebido não é um XML válido.'), 204);
                // Falha, o conteúdo recebido não é um XML válido
                echo 'O conteúdo recebido não é um XML válido.';
            }
            var_dump($lattes);
        } else {
            return Response::json(array('O conteúdo recebido não é um XML válido.' => 'O conteúdo recebido não é um XML válido.'), 204);
        }
    }
}