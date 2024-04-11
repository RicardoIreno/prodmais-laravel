<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Work;
use Illuminate\Support\Facades\DB;

class LattesController extends Controller
{

    function lattesID10($lattesID16)
    {
        $url = 'https://lattes.cnpq.br/' . $lattesID16 . '';

        $headers = @get_headers($url);

        $lattesID10 = "";
        foreach ($headers as $h) {
            if (substr($h, 0, 87) == 'Location: http://buscatextual.cnpq.br/buscatextual/visualizacv.do?metodo=apresentar&id=') {
                $lattesID10 = trim(substr($h, 87));
                break;
            }
        }
        return $lattesID10;
    }

    public function processaPalavrasChaveLattes($palavras_chave)
    {
        foreach (range(1, 6) as $number) {
            if (!empty($palavras_chave['@attributes']["PALAVRA-CHAVE-$number"])) {
                $array_result[$number] = $palavras_chave['@attributes']["PALAVRA-CHAVE-$number"];
            }
        }
        if (isset($array_result)) {
            return $array_result;
        }
        unset($array_result);
    }
    public function processaAutores($autores)
    {
        $i = 0;
        foreach ($autores as $autor) {
            $array_autores[$i]['NOME-COMPLETO-DO-AUTOR'] = $autor['@attributes']['NOME-COMPLETO-DO-AUTOR'];
            $array_autores[$i]['NOME-PARA-CITACAO'] = $autor['@attributes']['NOME-PARA-CITACAO'];
            $array_autores[$i]['NRO-ID-CNPQ'] = $autor['@attributes']['NRO-ID-CNPQ'];
            $i++;
        }
        return $array_autores;
        unset($array_autores);
    }

    public function artigos(array $artigos, array $attributes)
    {
        // echo "<pre>" . print_r($attributes, true) . "</pre>";
        // echo "<pre>" . print_r($artigos, true) . "</pre>";
        foreach ($artigos['ARTIGO-PUBLICADO'] as $artigo) {
            // echo "<pre>" . print_r($artigo, true) . "</pre>";
            if (isset($artigo['PALAVRAS-CHAVE'])) {
                $array_result_pc = $this->processaPalavrasChaveLattes($artigo['PALAVRAS-CHAVE']);
            }
            if (isset($artigo['AUTORES'])) {
                $array_autores = $this->processaAutores($artigo['AUTORES']);
            }
            $record['about'] = $array_result_pc;
            $record['author'] = $array_autores;
            $record['datePublished'] = $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['ANO-DO-ARTIGO'];
            $record['doi'] = $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['DOI'];
            $record['inLanguage'] = $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['IDIOMA'];
            $record['isPartOf'] = $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['TITULO-DO-PERIODICO-OU-REVISTA'];
            $record['issn'] = $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['ISSN'];
            $record['issueNumber'] = $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['SERIE'];
            $record['name'] = $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['TITULO-DO-ARTIGO'];
            $record['pageEnd'] = $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['PAGINA-FINAL'];
            $record['pageStart'] = $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['PAGINA-INICIAL'];
            $record['type'] = 'Artigo publicado';
            $record['url'] = $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['HOME-PAGE-DO-TRABALHO'];
            $record['volumeNumber'] = $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['VOLUME'];
            //echo "<pre>" . print_r($record, true) . "</pre>";
            $work = new Work($record);
            $work->save();
            unset($array_result_pc);
        }
    }

    public function createPerson(array $curriculo, array $attributes)
    {
        echo "<pre>" . print_r($attributes, true) . "</pre>";
        echo "<pre>" . print_r($curriculo, true) . "</pre>";

        $record_person['id'] = $attributes['NUMERO-IDENTIFICADOR'];
        $record_person['lattesDataAtualizacao'] = $attributes['DATA-ATUALIZACAO'];
        $record_person['lattesID10'] = $this->lattesID10($attributes['NUMERO-IDENTIFICADOR']);
        $record_person['resumoCVpt'] = $curriculo['RESUMO-CV']['@attributes']['TEXTO-RESUMO-CV-RH'];
        $record_person['resumoCVen'] = $curriculo['RESUMO-CV']['@attributes']['TEXTO-RESUMO-CV-RH-EN'];
        $record_person['name'] = $curriculo['@attributes']['NOME-COMPLETO'];
        $record_person['nacionalidade'] = $curriculo['@attributes']['PAIS-DE-NACIONALIDADE'];
        $record_person['orcid'] = $curriculo['@attributes']['ORCID-ID'];
        $record_person['idiomas'] = $curriculo['IDIOMAS'];
        $record_person['formacao'] = $curriculo['FORMACAO-ACADEMICA-TITULACAO'];
        $person = new Person($record_person);
        $person->save();
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
