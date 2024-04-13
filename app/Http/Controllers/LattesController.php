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

    // public function processaPalavrasChaveLattes($palavras_chave)
    // {
    //     $array_result = [];
    //     foreach (range(1, 6) as $number) {
    //         if (!empty($palavras_chave['@attributes']["PALAVRA-CHAVE-$number"])) {
    //             $array_result[$number] = $palavras_chave['@attributes']["PALAVRA-CHAVE-$number"];
    //         }
    //     }
    //     return $array_result;
    // }
    // public function processaAutores($autores)
    // {
    //     $array_autores = [];
    //     $i = 0;
    //     foreach ($autores as $autor) {
    //         $array_autores[$i]['NOME-COMPLETO-DO-AUTOR'] = $autor['@attributes']['NOME-COMPLETO-DO-AUTOR'];
    //         $array_autores[$i]['NOME-PARA-CITACAO'] = $autor['@attributes']['NOME-PARA-CITACAO'];
    //         $array_autores[$i]['NRO-ID-CNPQ'] = $autor['@attributes']['NRO-ID-CNPQ'];
    //         $i++;
    //     }
    //     return $array_autores;
    // }

    public function artigos(array $artigos, array $attributes)
    {
        //echo "<pre>" . print_r($attributes, true) . "</pre>";
        echo "<pre>" . print_r($artigos, true) . "</pre>";
        foreach ($artigos['ARTIGO-PUBLICADO'] as $artigo) {
            $work = new Work;
            $work->fill([
                'datePublished' => $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['ANO-DO-ARTIGO'],
                'inLanguage' => $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['IDIOMA'],
                'isPartOf' => $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['TITULO-DO-PERIODICO-OU-REVISTA'],
                'issn' => $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['ISSN'],
                'issueNumber' => $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['SERIE'],
                'name' => $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['TITULO-DO-ARTIGO'],
                'pageEnd' => $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['PAGINA-FINAL'],
                'pageStart' => $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['PAGINA-INICIAL'],
                'type' => 'Artigo publicado',
                'url' => $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['HOME-PAGE-DO-TRABALHO'],
                'volumeNumber' => $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['VOLUME'],

                //'about' => $artigo['PALAVRAS-CHAVE'],
            ]);

            if (isset($artigo['AUTORES'])) {
                $work->fill([
                    'author' => $artigo['AUTORES'],
                ]);
            }

            if (isset($artigo['PALAVRAS-CHAVE'])) {
                $work->fill([
                    'about' => $artigo['PALAVRAS-CHAVE'],
                ]);
            }

            try {
                $work->save();
                unset($array_result_pc);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    public function createPerson(array $curriculo, array $dados_complementares, array $outra_producao, array $attributes)
    {
        //echo "<pre>" . print_r($attributes, true) . "</pre>";
        //echo "<pre>" . print_r($curriculo, true) . "</pre>";
        //echo "<pre>" . print_r($dados_complementares, true) . "</pre>";
        //echo "<pre>" . print_r($outra_producao, true) . "</pre>";

        $record_person['id'] = $attributes['NUMERO-IDENTIFICADOR'];
        $record_person['lattesDataAtualizacao'] = $attributes['DATA-ATUALIZACAO'];
        //$record_person['lattesID10'] = $this->lattesID10($attributes['NUMERO-IDENTIFICADOR']);
        $record_person['resumoCVpt'] = $curriculo['RESUMO-CV']['@attributes']['TEXTO-RESUMO-CV-RH'];
        $record_person['resumoCVen'] = $curriculo['RESUMO-CV']['@attributes']['TEXTO-RESUMO-CV-RH-EN'];
        $record_person['name'] = $curriculo['@attributes']['NOME-COMPLETO'];
        $record_person['nacionalidade'] = $curriculo['@attributes']['PAIS-DE-NACIONALIDADE'];
        $record_person['nomeCitacoesBibliograficas'] = $curriculo['@attributes']['NOME-EM-CITACOES-BIBLIOGRAFICAS'];
        $record_person['orcid'] = $curriculo['@attributes']['ORCID-ID'];
        $record_person['idiomas'] = $curriculo['IDIOMAS'];
        $record_person['formacao'] = $curriculo['FORMACAO-ACADEMICA-TITULACAO'];

        if (isset($curriculo['ATUACOES-PROFISSIONAIS'])) {
            $record_person['atuacao'] = $curriculo['ATUACOES-PROFISSIONAIS'];
        }
        if (isset($dados_complementares['ORIENTACOES-EM-ANDAMENTO'])) {
            $record_person['orientacoesEmAndamento'] = $dados_complementares['ORIENTACOES-EM-ANDAMENTO'];
        }
        if (isset($outra_producao['ORIENTACOES-CONCLUIDAS'])) {
            $record_person['orientacoesConcluidas'] = $outra_producao['ORIENTACOES-CONCLUIDAS'];
        }
        try {
            //$person = new Person($record_person);
            //$person->save();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
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
                //echo "<pre>" . print_r($lattes['OUTRA-PRODUCAO'], true) . "</pre>";
                $this->createPerson($lattes['DADOS-GERAIS'], $lattes['DADOS-COMPLEMENTARES'], $lattes['OUTRA-PRODUCAO'], $lattes['@attributes']);
                $this->producaoBibliografica($lattes['PRODUCAO-BIBLIOGRAFICA'], $lattes['@attributes']);
                //echo "<pre>" . print_r($lattes['@attributes'], true) . "</pre>";
            } catch (\Exception $e) {
                //echo 'Erro ao processar o arquivo do Lattes';
            }
        } else {
            //echo 'Não foi enviado um arquivo XML do Lattes válido.';
        }
    }
}