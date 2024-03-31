<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Work;
use Illuminate\Support\Facades\DB;

class LattesController extends Controller
{

    public function artigos(array $artigos, array $attributes)
    {
        // echo "<pre>" . print_r($attributes, true) . "</pre>";
        echo "<pre>" . print_r($artigos, true) . "</pre>";
        foreach ($artigos['ARTIGO-PUBLICADO'] as $artigo) {
            echo "<pre>" . print_r($artigo, true) . "</pre>";
            $createArtigo =  Work::updateOrCreate(
                [
                    'name' => $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['TITULO-DO-ARTIGO'],
                    'datePublished' => $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['ANO-DO-ARTIGO'],
                    'doi' => $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['DOI']
                ]
            );
        }
    }

    public function createPerson(array $curriculo, array $attributes)
    {
        //echo "<pre>" . print_r($attributes, true) . "</pre>";
        //echo "<pre>" . print_r($curriculo, true) . "</pre>";
        //dd($curriculo['@attributes']['NOME-COMPLETO']);

        $person = Person::updateOrCreate(
            ['id' =>  $attributes['NUMERO-IDENTIFICADOR']],
            ['name' => $curriculo['@attributes']['NOME-COMPLETO']]
        );
    }

    public function producaoBibliografica(array $producaoBibliografica, array $attributes)
    {
        //echo "<pre>" . print_r($attributes, true) . "</pre>";
        //echo "<pre>" . print_r($producaoBibliografica, true) . "</pre>";

        if (isset($producaoBibliografica['ARTIGOS-PUBLICADOS'])) {
            //echo "<pre>" . print_r($lattesPB['ARTIGOS-PUBLICADOS'], true) . "</pre>";
            $this->artigos($producaoBibliografica['ARTIGOS-PUBLICADOS'], $attributes);
        }
    }

    public function processXML(Request $request)
    {
        if ($request->file) {
            try {
                $lattesXML = simplexml_load_file($request->file);
                $lattes = json_decode(json_encode($lattesXML), true);
                //$lattes = get_object_vars($lattesXML);
                //echo "<pre>" . print_r($lattes['PRODUCAO-BIBLIOGRAFICA'], true) . "</pre>";
                $this->createPerson($lattes['DADOS-GERAIS'], $lattes['@attributes']);
                $this->producaoBibliografica($lattes['PRODUCAO-BIBLIOGRAFICA'], $lattes['@attributes']);
                //echo "<pre>" . print_r($lattes['@attributes'], true) . "</pre>";
            } catch (\Exception $e) {
                echo 'O conteúdo recebido não é um XML do Lattes válido.';
            }
        } else {
            //echo 'Não foi enviado um arquivo XML do Lattes válido.';
        }
    }
}