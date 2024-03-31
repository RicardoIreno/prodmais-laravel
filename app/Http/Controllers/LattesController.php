<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;
use Illuminate\Support\Facades\DB;

class LattesController extends Controller
{

    public function createPerson(Object $curriculo, array $attributes)
    {
        //echo "<pre>" . print_r($attributes, true) . "</pre>";
        //echo "<pre>" . print_r($curriculo, true) . "</pre>";
        $lattesCV = get_object_vars($curriculo);
        //dd($lattesCV['@attributes']['NOME-COMPLETO']);

        // $person = DB::table('people')->insert([
        //     'name' => $lattesCV['@attributes']['NOME-COMPLETO'],
        //     'id' => $attributes['NUMERO-IDENTIFICADOR']
        // ]);
        // dd($person);

        $person = Person::updateOrCreate(
            ['id' =>  $attributes['NUMERO-IDENTIFICADOR']],
            ['name' => $lattesCV['@attributes']['NOME-COMPLETO']]
        );
        //dd($person);
        dd($person);
    }

    public function processXML(Request $request)
    {
        if ($request->file) {
            try {
                $lattesXML = simplexml_load_file($request->file);
                $lattes = get_object_vars($lattesXML);
                //echo 'XML do Lattes recebido com sucesso!';
                $this->createPerson($lattes['DADOS-GERAIS'], $lattes['@attributes']);
                //echo "<pre>" . print_r($lattes['@attributes'], true) . "</pre>";
            } catch (\Exception $e) {
                //echo 'O conteúdo recebido não é um XML do Lattes válido.';
            }
        } else {
            //echo 'Não foi enviado um arquivo XML do Lattes válido.';
        }
    }
}