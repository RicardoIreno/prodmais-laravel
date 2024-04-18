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

    public function capitulos(array $capitulos, array $attributes)
    {
        foreach ($capitulos as $capitulo_array) {
            if (isset($capitulo_array['DADOS-BASICOS-DO-CAPITULO'])) {
                $this->processCapitulo($capitulo_array);
            } else {
                foreach ($capitulo_array as $capitulo) {
                    $this->processCapitulo($capitulo);
                }
            }
        }
    }

    function processCapitulo(array $capitulo)
    {
        $chapter = new Work;
        $chapter->fill([
            'name' => $capitulo['DADOS-BASICOS-DO-CAPITULO']['@attributes']['TITULO-DO-CAPITULO-DO-LIVRO'],
            'datePublished' => $capitulo['DADOS-BASICOS-DO-CAPITULO']['@attributes']['ANO'],
            'doi' => $capitulo['DADOS-BASICOS-DO-CAPITULO']['@attributes']['DOI'],
            'inLanguage' => $capitulo['DADOS-BASICOS-DO-CAPITULO']['@attributes']['IDIOMA'],
            'isbn' => $capitulo['DETALHAMENTO-DO-CAPITULO']['@attributes']['ISBN'],
            'type' => 'Capítulo de livro publicado',
        ]);

        if (isset($capitulo['AUTORES'])) {
            foreach ($capitulo['AUTORES'] as $autores) {
                if (isset($autores['@attributes'])) {
                    $aut_array[] = $autores['@attributes'];
                    $aut_name_array[] = $autores['@attributes']['NOME-COMPLETO-DO-AUTOR'];
                } else {
                    $aut_array[] = $autores;
                    $aut_name_array[] = $autores['NOME-COMPLETO-DO-AUTOR'];
                }
            }
            $chapter->fill([
                'author' => $aut_array,
                'author_array' => $aut_name_array,
            ]);
            unset($aut_array);
            unset($aut_name_array);
        }

        if (isset($capitulo['PALAVRAS-CHAVE'])) {
            $about_array = LattesController::processaPalavrasChaveLattes($capitulo['PALAVRAS-CHAVE']);
            $chapter->fill([
                'about' => $about_array,
            ]);
        }

        if (isset($capitulo['DADOS-BASICOS-DO-CAPITULO']['@attributes']['HOME-PAGE-DO-TRABALHO'])) {
            $url = LattesController::processaURL($capitulo['DADOS-BASICOS-DO-CAPITULO']['@attributes']['HOME-PAGE-DO-TRABALHO']);
            $chapter->fill([
                'url' => $url,
            ]);
        }

        try {
            $chapter->save();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function jornais(array $jornais, array $attributes)
    {
        foreach ($jornais as $jornal_array) {
            if (isset($jornal_array['DADOS-BASICOS-DO-TEXTO'])) {
                $this->processJornal($jornal_array);
            } else {
                foreach ($jornal_array as $jornal) {
                    $this->processJornal($jornal);
                }
            }
        }
    }

    function processJornal(array $jornal)
    {
        $journal = new Work;
        $journal->fill([
            'name' => $jornal['DADOS-BASICOS-DO-TEXTO']['@attributes']['TITULO-DO-TEXTO'],
            'datePublished' => $jornal['DADOS-BASICOS-DO-TEXTO']['@attributes']['ANO-DO-TEXTO'],
            'doi' => $jornal['DADOS-BASICOS-DO-TEXTO']['@attributes']['DOI'],
            'inLanguage' => $jornal['DADOS-BASICOS-DO-TEXTO']['@attributes']['IDIOMA'],
            'type' => 'Textos em jornais de notícias/revistas',
        ]);

        if (isset($jornal['AUTORES'])) {
            foreach ($jornal['AUTORES'] as $autores) {
                if (isset($autores['@attributes'])) {
                    $aut_array[] = $autores['@attributes'];
                    $aut_name_array[] = $autores['@attributes']['NOME-COMPLETO-DO-AUTOR'];
                } else {
                    $aut_array[] = $autores;
                    $aut_name_array[] = $autores['NOME-COMPLETO-DO-AUTOR'];
                }
            }
            $journal->fill([
                'author' => $aut_array,
                'author_array' => $aut_name_array,
            ]);
            unset($aut_array);
            unset($aut_name_array);
        }

        if (isset($jornal['PALAVRAS-CHAVE'])) {
            $about_array = LattesController::processaPalavrasChaveLattes($jornal['PALAVRAS-CHAVE']);
            $journal->fill([
                'about' => $about_array,
            ]);
        }

        if (isset($jornal['DADOS-BASICOS-DO-TEXTO']['@attributes']['HOME-PAGE-DO-TRABALHO'])) {
            $url = LattesController::processaURL($jornal['DADOS-BASICOS-DO-TEXTO']['@attributes']['HOME-PAGE-DO-TRABALHO']);
            $journal->fill([
                'url' => $url,
            ]);
        }

        try {
            $journal->save();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function demais(array $demais, array $attributes)
    {
        foreach ($demais as $demais_array) {
            if (isset($demais_array['DADOS-BASICOS-DE-OUTRA-PRODUCAO'])) {
                $this->processDemais($demais_array);
            } else {
                foreach ($demais_array as $outra) {
                    $this->processDemais($outra);
                }
            }
        }
    }

    function processDemais(array $outra)
    {
        $other = new Work;
        if (isset($outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO'])) {
            $other->fill([
                'name' => $outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['TITULO'],
                'datePublished' => $outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['ANO'],
                'doi' => $outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['DOI'],
                'inLanguage' => $outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['IDIOMA'],
                'type' => 'Demais tipos - ' . $outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['NATUREZA'],
            ]);
            if (isset($outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['HOME-PAGE-DO-TRABALHO'])) {
                $url = LattesController::processaURL($outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['HOME-PAGE-DO-TRABALHO']);
                $other->fill([
                    'url' => $url,
                ]);
            }
        } else {
            $other->fill([
                'name' => $outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['TITULO'],
                'datePublished' => $outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['ANO'],
                'doi' => $outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['DOI'],
                'inLanguage' => $outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['IDIOMA'],
                'type' => 'Demais tipos - ' . $outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['NATUREZA'],
            ]);
            if (isset($outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['HOME-PAGE-DO-TRABALHO'])) {
                $url = LattesController::processaURL($outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['HOME-PAGE-DO-TRABALHO']);
                $other->fill([
                    'url' => $url,
                ]);
            }
        }


        if (isset($outra['AUTORES'])) {
            foreach ($outra['AUTORES'] as $autores) {
                if (isset($autores['@attributes'])) {
                    $aut_array[] = $autores['@attributes'];
                    $aut_name_array[] = $autores['@attributes']['NOME-COMPLETO-DO-AUTOR'];
                } else {
                    $aut_array[] = $autores;
                    $aut_name_array[] = $autores['NOME-COMPLETO-DO-AUTOR'];
                }
            }
            $other->fill([
                'author' => $aut_array,
                'author_array' => $aut_name_array,
            ]);
            unset($aut_array);
            unset($aut_name_array);
        }

        if (isset($outra['PALAVRAS-CHAVE'])) {
            $about_array = LattesController::processaPalavrasChaveLattes($outra['PALAVRAS-CHAVE']);
            $other->fill([
                'about' => $about_array,
            ]);
        }


        try {
            $other->save();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function artigosAceitos(array $artigosAceitos, array $attributes)
    {
        foreach ($artigosAceitos as $artigosAceitos_array) {
            if (isset($artigosAceitos_array['DADOS-BASICOS-DO-ARTIGO'])) {
                $this->processArtigosAceitos($artigosAceitos_array);
            } else {
                foreach ($artigosAceitos_array as $artigoAceito) {
                    $this->processArtigosAceitos($artigoAceito);
                }
            }
        }
    }

    function processArtigosAceitos(array $artigoAceito)
    {
        $articleAcepted = new Work;
        $articleAcepted->fill([
            'name' => $artigoAceito['DADOS-BASICOS-DO-ARTIGO']['@attributes']['TITULO-DO-ARTIGO'],
            'datePublished' => $artigoAceito['DADOS-BASICOS-DO-ARTIGO']['@attributes']['ANO-DO-ARTIGO'],
            'doi' => $artigoAceito['DADOS-BASICOS-DO-ARTIGO']['@attributes']['DOI'],
            'inLanguage' => $artigoAceito['DADOS-BASICOS-DO-ARTIGO']['@attributes']['IDIOMA'],
            'type' => 'Artigos aceitos para publicação',
        ]);

        if (isset($artigoAceito['AUTORES'])) {
            foreach ($artigoAceito['AUTORES'] as $autores) {
                if (isset($autores['@attributes'])) {
                    $aut_array[] = $autores['@attributes'];
                    $aut_name_array[] = $autores['@attributes']['NOME-COMPLETO-DO-AUTOR'];
                } else {
                    $aut_array[] = $autores;
                    $aut_name_array[] = $autores['NOME-COMPLETO-DO-AUTOR'];
                }
            }
            $articleAcepted->fill([
                'author' => $aut_array,
                'author_array' => $aut_name_array,
            ]);
            unset($aut_array);
            unset($aut_name_array);
        }

        if (isset($artigoAceito['PALAVRAS-CHAVE'])) {
            $about_array = LattesController::processaPalavrasChaveLattes($artigoAceito['PALAVRAS-CHAVE']);
            $articleAcepted->fill([
                'about' => $about_array,
            ]);
        }

        if (isset($artigoAceito['DADOS-BASICOS-DO-ARTIGO']['@attributes']['HOME-PAGE-DO-TRABALHO'])) {
            $url = LattesController::processaURL($artigoAceito['DADOS-BASICOS-DO-ARTIGO']['@attributes']['HOME-PAGE-DO-TRABALHO']);
            $articleAcepted->fill([
                'url' => $url,
            ]);
        }

        try {
            $articleAcepted->save();
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
                if (isset($lattes['PRODUCAO-BIBLIOGRAFICA']['LIVROS-E-CAPITULOS']['CAPITULOS-DE-LIVROS-PUBLICADOS'])) {
                    $this->capitulos($lattes['PRODUCAO-BIBLIOGRAFICA']['LIVROS-E-CAPITULOS']['CAPITULOS-DE-LIVROS-PUBLICADOS'], $lattes['@attributes']);
                }
                if (isset($lattes['PRODUCAO-BIBLIOGRAFICA']['TEXTOS-EM-JORNAIS-OU-REVISTAS'])) {
                    $this->jornais($lattes['PRODUCAO-BIBLIOGRAFICA']['TEXTOS-EM-JORNAIS-OU-REVISTAS'], $lattes['@attributes']);
                }
                if (isset($lattes['PRODUCAO-BIBLIOGRAFICA']['DEMAIS-TIPOS-DE-PRODUCAO-BIBLIOGRAFICA'])) {
                    $this->demais($lattes['PRODUCAO-BIBLIOGRAFICA']['DEMAIS-TIPOS-DE-PRODUCAO-BIBLIOGRAFICA'], $lattes['@attributes']);
                }
                if (isset($lattes['PRODUCAO-BIBLIOGRAFICA']['ARTIGOS-ACEITOS-PARA-PUBLICACAO'])) {
                    $this->artigosAceitos($lattes['PRODUCAO-BIBLIOGRAFICA']['ARTIGOS-ACEITOS-PARA-PUBLICACAO'], $lattes['@attributes']);
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        } else {
            echo 'Não foi enviado um arquivo XML do Lattes válido.';
        }
    }
}
