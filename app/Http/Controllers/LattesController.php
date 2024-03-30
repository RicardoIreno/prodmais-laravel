<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LattesController extends Controller
{
    public function processXML(Request $request)
    {
        //dd($request->all());
        if ($request->file) {
            try {
                $lattes = simplexml_load_file($request->file);
                echo 'XML do Lattes recebido com sucesso!';
                var_dump($lattes);
            } catch (\Exception $e) {
                echo 'O conteúdo recebido não é um XML do Lattes válido.';
            }
        } else {
            echo 'Não foi enviado um arquivo XML do Lattes válido.';
        }
    }
}