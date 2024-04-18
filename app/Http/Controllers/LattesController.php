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
        $array_result = [];
        foreach (range(1, 6) as $number) {
            if (!empty($palavras_chave['@attributes']["PALAVRA-CHAVE-$number"])) {
                $array_result[] = $palavras_chave['@attributes']["PALAVRA-CHAVE-$number"];
            }
        }
        return $array_result;
    }

    public function processaURL($url)
    {
        $url_array = explode('[', $url);
        if (isset($url_array[1])) {
            $url_response = str_replace(']', '', $url_array[1]);
            return $url_response;
        } else {
            return "";
        }
    }

    public function artigos(array $artigos, array $attributes)
    {
        //echo "<pre>" . print_r($attributes, true) . "</pre>";
        //echo "<pre>" . print_r($artigos, true) . "</pre>";
        foreach ($artigos['ARTIGO-PUBLICADO'] as $artigo) {
            $work = new Work;
            $work->fill([
                'datePublished' => $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['ANO-DO-ARTIGO'],
                'doi' => $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['DOI'],
                'inLanguage' => $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['IDIOMA'],
                'isPartOf' => $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['TITULO-DO-PERIODICO-OU-REVISTA'],
                'issn' => $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['ISSN'],
                'issueNumber' => $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['SERIE'],
                'name' => $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['TITULO-DO-ARTIGO'],
                'pageEnd' => $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['PAGINA-FINAL'],
                'pageStart' => $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['PAGINA-INICIAL'],
                'type' => 'Artigo publicado',
                'volumeNumber' => $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['VOLUME'],
            ]);

            if (isset($artigo['AUTORES'])) {
                foreach ($artigo['AUTORES'] as $autores) {
                    if (isset($autores['@attributes'])) {
                        $aut_array[] = $autores['@attributes'];
                        $aut_name_array[] = $autores['@attributes']['NOME-COMPLETO-DO-AUTOR'];
                    } else {
                        $aut_array[] = $autores;
                        $aut_name_array[] = $autores['NOME-COMPLETO-DO-AUTOR'];
                    }
                }
                $work->fill([
                    'author' => $aut_array,
                    'author_array' => $aut_name_array,
                ]);
                unset($aut_array);
                unset($aut_name_array);
            }

            if (isset($artigo['PALAVRAS-CHAVE'])) {
                $about_array = $this->processaPalavrasChaveLattes($artigo['PALAVRAS-CHAVE']);
                $work->fill([
                    'about' => $about_array,
                ]);
            }

            if (isset($artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['HOME-PAGE-DO-TRABALHO'])) {
                $url = $this->processaURL($artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['HOME-PAGE-DO-TRABALHO']);
                $work->fill([
                    'url' => $url,
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

    public function trabalhosEmEventos(array $trabalhosEmEventos, array $attributes)
    {
        //echo "<pre>" . print_r($attributes, true) . "</pre>";
        //echo "<pre>" . print_r($trabalhoEmEventoss, true) . "</pre>";
        foreach ($trabalhosEmEventos['TRABALHO-EM-EVENTOS'] as $trabalhoEmEventos) {
            $work = new Work;
            $work->fill([
                'datePublished' => $trabalhoEmEventos['DADOS-BASICOS-DO-TRABALHO']['@attributes']['ANO-DO-TRABALHO'],
                'doi' => $trabalhoEmEventos['DADOS-BASICOS-DO-TRABALHO']['@attributes']['DOI'],
                'educationEvent' => $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["NOME-DO-EVENTO"],
                'inLanguage' => $trabalhoEmEventos['DADOS-BASICOS-DO-TRABALHO']['@attributes']['IDIOMA'],
                'isPartOf' => $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']['TITULO-DOS-ANAIS-OU-PROCEEDINGS'],
                'name' => $trabalhoEmEventos['DADOS-BASICOS-DO-TRABALHO']['@attributes']['TITULO-DO-TRABALHO'],
                'pageEnd' => $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']['PAGINA-FINAL'],
                'pageStart' => $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']['PAGINA-INICIAL'],
                'type' => 'Trabalhos em eventos'
            ]);

            if (isset($trabalhoEmEventos['AUTORES'])) {
                foreach ($trabalhoEmEventos['AUTORES'] as $autores) {
                    if (isset($autores['@attributes'])) {
                        $aut_array[] = $autores['@attributes'];
                        $aut_name_array[] = $autores['@attributes']['NOME-COMPLETO-DO-AUTOR'];
                    } else {
                        $aut_array[] = $autores;
                        $aut_name_array[] = $autores['NOME-COMPLETO-DO-AUTOR'];
                    }
                }
                $work->fill([
                    'author' => $aut_array,
                    'author_array' => $aut_name_array,
                ]);
                unset($aut_array);
                unset($aut_name_array);
            }

            if (isset($trabalhoEmEventos['PALAVRAS-CHAVE'])) {
                $about_array = $this->processaPalavrasChaveLattes($trabalhoEmEventos['PALAVRAS-CHAVE']);
                $work->fill([
                    'about' => $about_array,
                ]);
            }

            if (isset($trabalhoEmEventos['DADOS-BASICOS-DO-TRABALHO']['@attributes']['HOME-PAGE-DO-TRABALHO'])) {
                $url = $this->processaURL($trabalhoEmEventos['DADOS-BASICOS-DO-TRABALHO']['@attributes']['HOME-PAGE-DO-TRABALHO']);
                $work->fill([
                    'url' => $url,
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

    public function livros(array $livros, array $attributes)
    {
        foreach ($livros as $livro_array) {
            if (isset($livro_array['DADOS-BASICOS-DO-LIVRO'])) {
                $this->processLivro($livro_array);
            } else {
                foreach ($livro_array as $livro) {
                    $this->processLivro($livro);
                }
            }
        }
    }

    function processLivro(array $livro)
    {
        $book = new Work;
        $book->fill([
            'name' => $livro['DADOS-BASICOS-DO-LIVRO']['@attributes']['TITULO-DO-LIVRO'],
            'datePublished' => $livro['DADOS-BASICOS-DO-LIVRO']['@attributes']['ANO'],
            'doi' => $livro['DADOS-BASICOS-DO-LIVRO']['@attributes']['DOI'],
            'inLanguage' => $livro['DADOS-BASICOS-DO-LIVRO']['@attributes']['IDIOMA'],
            'isbn' => $livro['DETALHAMENTO-DO-LIVRO']['@attributes']['ISBN'],
            'type' => 'Livro publicado ou organizado',
            'url' => $livro['DADOS-BASICOS-DO-LIVRO']['@attributes']['HOME-PAGE-DO-TRABALHO'],
        ]);

        if (isset($livro['AUTORES'])) {
            foreach ($livro['AUTORES'] as $autores) {
                if (isset($autores['@attributes'])) {
                    $aut_array[] = $autores['@attributes'];
                    $aut_name_array[] = $autores['@attributes']['NOME-COMPLETO-DO-AUTOR'];
                } else {
                    $aut_array[] = $autores;
                    $aut_name_array[] = $autores['NOME-COMPLETO-DO-AUTOR'];
                }
            }
            $book->fill([
                'author' => $aut_array,
                'author_array' => $aut_name_array,
            ]);
            unset($aut_array);
            unset($aut_name_array);
        }

        if (isset($livro['PALAVRAS-CHAVE'])) {
            $about_array = LattesController::processaPalavrasChaveLattes($livro['PALAVRAS-CHAVE']);
            $book->fill([
                'about' => $about_array,
            ]);
        }

        if (isset($livro['DADOS-BASICOS-DO-LIVRO']['@attributes']['HOME-PAGE-DO-TRABALHO'])) {
            $url = LattesController::processaURL($livro['DADOS-BASICOS-DO-LIVRO']['@attributes']['HOME-PAGE-DO-TRABALHO']);
            $book->fill([
                'url' => $url,
            ]);
        }

        try {
            $book->save();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function createPerson(array $lattes, Request $request)
    {
        //echo "<pre>" . print_r($attributes, true) . "</pre>";
        //echo "<pre>" . print_r($curriculo, true) . "</pre>";
        //echo "<pre>" . print_r($dados_complementares, true) . "</pre>";
        //echo "<pre>" . print_r($outra_producao, true) . "</pre>";
        //echo "<pre>" . print_r($request, true) . "</pre>";

        $person = new Person();

        $person->fill([
            'id' => $lattes['@attributes']['NUMERO-IDENTIFICADOR'],
            'lattesDataAtualizacao' => $lattes['@attributes']['DATA-ATUALIZACAO'],
            'resumoCVpt' => $lattes['DADOS-GERAIS']['RESUMO-CV']['@attributes']['TEXTO-RESUMO-CV-RH'],
            'resumoCVen' => $lattes['DADOS-GERAIS']['RESUMO-CV']['@attributes']['TEXTO-RESUMO-CV-RH-EN'],
            'name' => $lattes['DADOS-GERAIS']['@attributes']['NOME-COMPLETO'],
            'nacionalidade' => $lattes['DADOS-GERAIS']['@attributes']['PAIS-DE-NACIONALIDADE'],
            'nomeCitacoesBibliograficas' => $lattes['DADOS-GERAIS']['@attributes']['NOME-EM-CITACOES-BIBLIOGRAFICAS'],
            'orcid' => $lattes['DADOS-GERAIS']['@attributes']['ORCID-ID'],
            'idiomas' => $lattes['DADOS-GERAIS']['IDIOMAS'],
            'formacao' => $lattes['DADOS-GERAIS']['FORMACAO-ACADEMICA-TITULACAO']
        ]);

        $lattesID10 = $this->lattesID10($lattes['@attributes']['NUMERO-IDENTIFICADOR']);

        if (!empty($lattesID10)) {
            $person->fill([
                'lattesID10' => $lattesID10
            ]);
        }

        if (isset($lattes['DADOS-GERAIS']['ATUACOES-PROFISSIONAIS'])) {
            $person->fill([
                'atuacao' => $lattes['DADOS-GERAIS']['ATUACOES-PROFISSIONAIS']
            ]);
        }
        if (isset($lattes['DADOS-COMPLEMENTARES']['ORIENTACOES-EM-ANDAMENTO'])) {
            $person->fill([
                'orientacoesEmAndamento' => $lattes['DADOS-COMPLEMENTARES']['ORIENTACOES-EM-ANDAMENTO']
            ]);
        }
        if (isset($lattes['OUTRA-PRODUCAO']['ORIENTACOES-CONCLUIDAS'])) {
            $person->fill([
                'orientacoesConcluidas' => $lattes['OUTRA-PRODUCAO']['ORIENTACOES-CONCLUIDAS']
            ]);
        }

        if (isset($request->instituicao)) {
            $instituicoes[] = $request->instituicao;
            $person->fill([
                'instituicao' => $instituicoes
            ]);
            unset($instituicoes);
        }

        if (isset($request->ppg_nome)) {
            $ppgs[] = $request->ppg_nome;
            $person->fill([
                'ppg_nome' => $ppgs
            ]);
            unset($ppgs);
        }


        try {
            $person->save();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function processXML(Request $request)
    {
        if ($request->file) {
            try {
                $lattesXML = simplexml_load_file($request->file);
                $lattes = json_decode(json_encode($lattesXML), true);
                $this->createPerson($lattes, $request);
                if (isset($lattes['PRODUCAO-BIBLIOGRAFICA']['ARTIGOS-PUBLICADOS'])) {
                    $this->artigos($lattes['PRODUCAO-BIBLIOGRAFICA']['ARTIGOS-PUBLICADOS'], $lattes['@attributes']);
                }
                if (isset($lattes['PRODUCAO-BIBLIOGRAFICA']['TRABALHOS-EM-EVENTOS'])) {
                    $this->trabalhosEmEventos($lattes['PRODUCAO-BIBLIOGRAFICA']['TRABALHOS-EM-EVENTOS'], $lattes['@attributes']);
                }
                if (isset($lattes['PRODUCAO-BIBLIOGRAFICA']['LIVROS-E-CAPITULOS']['LIVROS-PUBLICADOS-OU-ORGANIZADOS'])) {
                    $this->livros($lattes['PRODUCAO-BIBLIOGRAFICA']['LIVROS-E-CAPITULOS']['LIVROS-PUBLICADOS-OU-ORGANIZADOS'], $lattes['@attributes']);
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        } else {
            echo 'Não foi enviado um arquivo XML do Lattes válido.';
        }
    }
}
