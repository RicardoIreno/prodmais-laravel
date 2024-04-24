<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Work;
use App\Models\Projeto;
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

    public function artigos(array $artigos, array $attributes, Request $request)
    {
        //echo "<pre>" . print_r($attributes, true) . "</pre>";
        //echo "<pre>" . print_r($artigos, true) . "</pre>";
        foreach ($artigos['ARTIGO-PUBLICADO'] as $artigo) {
            $authorLattesIds = [];
            $authorLattesIds[] = $attributes['NUMERO-IDENTIFICADOR'];
            $work = new Work;
            $work->fill([
                'authorLattesIds' => $authorLattesIds,
                'datePublished' => $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['ANO-DO-ARTIGO'],
                'doi' => $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['DOI'],
                'inLanguage' => $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['IDIOMA'],
                'name' => $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['TITULO-DO-ARTIGO'],
                'type' => 'Artigo publicado',
            ]);

            $isPartOf['name'] = $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['TITULO-DO-PERIODICO-OU-REVISTA'];
            $isPartOf['issn'] = $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['ISSN'];
            $isPartOf['volumeNumber'] = $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['VOLUME'];
            $isPartOf['issueNumber'] = $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['SERIE'];
            $isPartOf['pageStart'] = $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['PAGINA-INICIAL'];
            $isPartOf['pageEnd'] = $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['PAGINA-FINAL'];
            $isPartOf['city'] = $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['LOCAL-DE-PUBLICACAO'];

            $work->fill([
                'isPartOf' => $isPartOf,
            ]);

            $work->fill([
                'isPartOfName' => $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['TITULO-DO-PERIODICO-OU-REVISTA'],
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

            if (isset($request->instituicao)) {
                $instituicoes[] = $request->instituicao;
                $work->fill([
                    'instituicao' => $instituicoes
                ]);
                unset($instituicoes);
            }

            if (isset($request->ppg_nome)) {
                $ppgs[] = $request->ppg_nome;
                $work->fill([
                    'ppg_nome' => $ppgs
                ]);
                unset($ppgs);
            }

            if (isset($request->genero)) {
                $generos[] = $request->genero;
                $work->fill([
                    'genero' => $generos
                ]);
                unset($generos);
            }

            if (!empty($artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['DOI'])) {
                $sha256 = hash('sha256', $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['DOI']);
            } else {
                $sha256_array[] = $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['TITULO-DO-ARTIGO'];
                $sha256_array[] = $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['ANO-DO-ARTIGO'];
                $sha256_array[] = $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['TITULO-DO-PERIODICO-OU-REVISTA'];
                $sha256_array[] = $artigo['DADOS-BASICOS-DO-ARTIGO']['@attributes']['HOME-PAGE-DO-TRABALHO'];
                $sha256_array[] = $artigo['DETALHAMENTO-DO-ARTIGO']['@attributes']['PAGINA-INICIAL'];
                $sha256 = hash('sha256', '' . implode("", $sha256_array) . '');
            }

            $work->fill([
                'sha256' => $sha256,
            ]);

            $existingWork = Work::where('sha256', $sha256)->first();

            if ($existingWork) {
                $existingWork->authorLattesIds = array_merge($existingWork->authorLattesIds, [$attributes['NUMERO-IDENTIFICADOR']]);
                $existingWork->authorLattesIds = array_unique($existingWork->authorLattesIds);
                $existingWork->save();
                WorkController::indexRelations($existingWork->id);
            } else {
                try {
                    $work->save();
                    WorkController::indexRelations($work->id);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
    }

    public function trabalhosEmEventos(array $trabalhosEmEventos, array $attributes, Request $request)
    {
        //echo "<pre>" . print_r($attributes, true) . "</pre>";
        //echo "<pre>" . print_r($trabalhosEmEventos, true) . "</pre>";
        foreach ($trabalhosEmEventos['TRABALHO-EM-EVENTOS'] as $trabalhoEmEventos) {
            $authorLattesIds = [];
            $authorLattesIds[] = $attributes['NUMERO-IDENTIFICADOR'];
            $work = new Work;
            $work->fill([
                'authorLattesIds' => $authorLattesIds,
                'country' =>  $trabalhoEmEventos['DADOS-BASICOS-DO-TRABALHO']['@attributes']['PAIS-DO-EVENTO'],
                'datePublished' => $trabalhoEmEventos['DADOS-BASICOS-DO-TRABALHO']['@attributes']['ANO-DO-TRABALHO'],
                'doi' => $trabalhoEmEventos['DADOS-BASICOS-DO-TRABALHO']['@attributes']['DOI'],
                'educationEvent' => $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["NOME-DO-EVENTO"],
                'inLanguage' => $trabalhoEmEventos['DADOS-BASICOS-DO-TRABALHO']['@attributes']['IDIOMA'],
                'name' => $trabalhoEmEventos['DADOS-BASICOS-DO-TRABALHO']['@attributes']['TITULO-DO-TRABALHO'],
                'type' => 'Trabalhos em eventos'
            ]);

            $educationEvent['name'] = $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["NOME-DO-EVENTO"];
            $educationEvent['city'] = $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["CIDADE-DO-EVENTO"];
            $educationEvent['year'] = $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["ANO-DE-REALIZACAO"];
            $educationEvent['classification'] = $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["CLASSIFICACAO-DO-EVENTO"];
            $educationEvent['isPartOf'] = $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["TITULO-DOS-ANAIS-OU-PROCEEDINGS"];
            $educationEvent['volumeNumber'] = $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["VOLUME"];
            $educationEvent['issueNumber'] = $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["FASCICULO"];
            $educationEvent['pageStart'] = $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["PAGINA-INICIAL"];
            $educationEvent['pageEnd'] = $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["PAGINA-INICIAL"];
            $educationEvent['isbn'] = $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["ISBN"];
            $educationEvent['publisher']['name'] = $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["NOME-DA-EDITORA"];
            $educationEvent['publisher']['city'] = $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["CIDADE-DA-EDITORA"];

            $work->fill([
                'educationEvent' => $educationEvent,
            ]);

            $work->fill([
                'isPartOfName' => $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["TITULO-DOS-ANAIS-OU-PROCEEDINGS"],
                'educationEventName' => $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["NOME-DO-EVENTO"]
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

            if (isset($request->instituicao)) {
                $instituicoes[] = $request->instituicao;
                $work->fill([
                    'instituicao' => $instituicoes
                ]);
                unset($instituicoes);
            }

            if (isset($request->ppg_nome)) {
                $ppgs[] = $request->ppg_nome;
                $work->fill([
                    'ppg_nome' => $ppgs
                ]);
                unset($ppgs);
            }

            if (isset($request->genero)) {
                $generos[] = $request->genero;
                $work->fill([
                    'genero' => $generos
                ]);
                unset($generos);
            }

            if (!empty($trabalhoEmEventos['DADOS-BASICOS-DO-TRABALHO']['@attributes']['DOI'])) {
                $sha256 = hash('sha256', $trabalhoEmEventos['DADOS-BASICOS-DO-TRABALHO']['@attributes']['DOI']);
            } else {
                $sha256_array[] = $trabalhoEmEventos['DADOS-BASICOS-DO-TRABALHO']['@attributes']['TITULO-DO-TRABALHO'];
                $sha256_array[] = $trabalhoEmEventos['DADOS-BASICOS-DO-TRABALHO']['@attributes']['ANO-DO-TRABALHO'];
                $sha256_array[] = $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["NOME-DO-EVENTO"];
                $sha256_array[] = $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["PAGINA-INICIAL"];
                $sha256_array[] = $trabalhoEmEventos['DETALHAMENTO-DO-TRABALHO']['@attributes']["PAGINA-INICIAL"];
                $sha256 = hash('sha256', '' . implode("", $sha256_array) . '');
            }

            $work->fill([
                'sha256' => $sha256,
            ]);

            $existingWork = Work::where('sha256', $sha256)->first();

            if ($existingWork) {
                $existingWork->authorLattesIds = array_merge($existingWork->authorLattesIds, [$attributes['NUMERO-IDENTIFICADOR']]);
                $existingWork->authorLattesIds = array_unique($existingWork->authorLattesIds);
                $existingWork->save();
                WorkController::indexRelations($existingWork->id);
            } else {
                try {
                    $work->save();
                    WorkController::indexRelations($work->id);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
    }

    public function livros(array $livros, array $attributes, Request $request)
    {
        foreach ($livros as $livro_array) {
            if (isset($livro_array['DADOS-BASICOS-DO-LIVRO'])) {
                $this->processLivro($livro_array, $attributes, $request);
            } else {
                foreach ($livro_array as $livro) {
                    $this->processLivro($livro, $attributes, $request);
                }
            }
        }
    }

    function processLivro(array $livro, array $attributes, Request $request)
    {
        $authorLattesIds = [];
        $authorLattesIds[] = $attributes['NUMERO-IDENTIFICADOR'];
        $work = new Work;
        $work->fill([
            'authorLattesIds' => $authorLattesIds,
            'name' => $livro['DADOS-BASICOS-DO-LIVRO']['@attributes']['TITULO-DO-LIVRO'],
            'bookEdition' => $livro['DETALHAMENTO-DO-LIVRO']['@attributes']['NUMERO-DA-EDICAO-REVISAO'],
            'country' => $livro['DADOS-BASICOS-DO-LIVRO']['@attributes']['PAIS-DE-PUBLICACAO'],
            'datePublished' => $livro['DADOS-BASICOS-DO-LIVRO']['@attributes']['ANO'],
            'doi' => $livro['DADOS-BASICOS-DO-LIVRO']['@attributes']['DOI'],
            'inLanguage' => $livro['DADOS-BASICOS-DO-LIVRO']['@attributes']['IDIOMA'],
            'isbn' => $livro['DETALHAMENTO-DO-LIVRO']['@attributes']['ISBN'],
            'type' => 'Livro publicado ou organizado',
            'numberOfPages' => $livro['DETALHAMENTO-DO-LIVRO']['@attributes']['NUMERO-DE-PAGINAS']
        ]);

        $publisher['name'] = $livro['DETALHAMENTO-DO-LIVRO']['@attributes']['NOME-DA-EDITORA'];
        $publisher['city'] = $livro['DETALHAMENTO-DO-LIVRO']['@attributes']['CIDADE-DA-EDITORA'];

        $work->fill([
            'publisher' => $publisher,
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
            $work->fill([
                'author' => $aut_array,
                'author_array' => $aut_name_array,
            ]);
            unset($aut_array);
            unset($aut_name_array);
        }

        if (isset($livro['PALAVRAS-CHAVE'])) {
            $about_array = LattesController::processaPalavrasChaveLattes($livro['PALAVRAS-CHAVE']);
            $work->fill([
                'about' => $about_array,
            ]);
        }

        if (isset($livro['DADOS-BASICOS-DO-LIVRO']['@attributes']['HOME-PAGE-DO-TRABALHO'])) {
            $url = LattesController::processaURL($livro['DADOS-BASICOS-DO-LIVRO']['@attributes']['HOME-PAGE-DO-TRABALHO']);
            $work->fill([
                'url' => $url,
            ]);
        }

        if (isset($request->instituicao)) {
            $instituicoes[] = $request->instituicao;
            $work->fill([
                'instituicao' => $instituicoes
            ]);
            unset($instituicoes);
        }

        if (isset($request->ppg_nome)) {
            $ppgs[] = $request->ppg_nome;
            $work->fill([
                'ppg_nome' => $ppgs
            ]);
            unset($ppgs);
        }

        if (isset($request->genero)) {
            $generos[] = $request->genero;
            $work->fill([
                'genero' => $generos
            ]);
            unset($generos);
        }

        if (!empty($livro['DADOS-BASICOS-DO-LIVRO']['@attributes']['DOI'])) {
            $sha256 = hash('sha256', $livro['DADOS-BASICOS-DO-LIVRO']['@attributes']['DOI']);
        } else {
            $sha256_array[] = $livro['DADOS-BASICOS-DO-LIVRO']['@attributes']['TITULO-DO-LIVRO'];
            $sha256_array[] = $livro['DADOS-BASICOS-DO-LIVRO']['@attributes']['ANO'];
            $sha256_array[] = $livro['DETALHAMENTO-DO-LIVRO']['@attributes']['ISBN'];
            $sha256_array[] = $livro['DETALHAMENTO-DO-LIVRO']['@attributes']['NOME-DA-EDITORA'];
            $sha256 = hash('sha256', '' . implode("", $sha256_array) . '');
        }

        $work->fill([
            'sha256' => $sha256,
        ]);

        $existingWork = Work::where('sha256', $sha256)->first();

        if ($existingWork) {
            $existingWork->authorLattesIds = array_merge($existingWork->authorLattesIds, [$attributes['NUMERO-IDENTIFICADOR']]);
            $existingWork->authorLattesIds = array_unique($existingWork->authorLattesIds);
            $existingWork->save();
            WorkController::indexRelations($existingWork->id);
        } else {
            try {
                $work->save();
                WorkController::indexRelations($work->id);
                unset($authorLattesIds);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    public function capitulos(array $capitulos, array $attributes, Request $request)
    {
        foreach ($capitulos as $capitulo_array) {
            if (isset($capitulo_array['DADOS-BASICOS-DO-CAPITULO'])) {
                $this->processCapitulo($capitulo_array, $attributes, $request);
            } else {
                foreach ($capitulo_array as $capitulo) {
                    $this->processCapitulo($capitulo, $attributes, $request);
                }
            }
        }
    }

    function processCapitulo(array $capitulo, array $attributes, Request $request)
    {
        $authorLattesIds = [];
        $authorLattesIds[] = $attributes['NUMERO-IDENTIFICADOR'];
        $work = new Work;
        $work->fill([
            'authorLattesIds' => $authorLattesIds,
            'name' => $capitulo['DADOS-BASICOS-DO-CAPITULO']['@attributes']['TITULO-DO-CAPITULO-DO-LIVRO'],
            'country' => $capitulo['DADOS-BASICOS-DO-CAPITULO']['@attributes']['PAIS-DE-PUBLICACAO'],
            'datePublished' => $capitulo['DADOS-BASICOS-DO-CAPITULO']['@attributes']['ANO'],
            'doi' => $capitulo['DADOS-BASICOS-DO-CAPITULO']['@attributes']['DOI'],
            'inLanguage' => $capitulo['DADOS-BASICOS-DO-CAPITULO']['@attributes']['IDIOMA'],
            'type' => 'Capítulo de livro publicado',
        ]);

        $isPartOfBook['name'] = $capitulo['DETALHAMENTO-DO-CAPITULO']['@attributes']['TITULO-DO-LIVRO'];
        $isPartOfBook['org'] = $capitulo['DETALHAMENTO-DO-CAPITULO']['@attributes']['ORGANIZADORES'];
        $isPartOfBook['isbn'] = $capitulo['DETALHAMENTO-DO-CAPITULO']['@attributes']['ISBN'];
        $isPartOfBook['pageStart'] = $capitulo['DETALHAMENTO-DO-CAPITULO']['@attributes']['PAGINA-INICIAL'];
        $isPartOfBook['pageEnd'] = $capitulo['DETALHAMENTO-DO-CAPITULO']['@attributes']['PAGINA-FINAL'];
        $isPartOfBook['bookEdition'] = $capitulo['DETALHAMENTO-DO-CAPITULO']['@attributes']['NUMERO-DA-EDICAO-REVISAO'];
        $isPartOfBook['publisher']['name'] = $capitulo['DETALHAMENTO-DO-CAPITULO']['@attributes']['NOME-DA-EDITORA'];
        $isPartOfBook['publisher']['city'] = $capitulo['DETALHAMENTO-DO-CAPITULO']['@attributes']['CIDADE-DA-EDITORA'];

        $work->fill([
            'isPartOf' => $isPartOfBook,
        ]);

        $work->fill([
            'isPartOfName' => $capitulo['DETALHAMENTO-DO-CAPITULO']['@attributes']['TITULO-DO-LIVRO'],
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
            $work->fill([
                'author' => $aut_array,
                'author_array' => $aut_name_array,
            ]);
            unset($aut_array);
            unset($aut_name_array);
        }

        if (isset($capitulo['PALAVRAS-CHAVE'])) {
            $about_array = LattesController::processaPalavrasChaveLattes($capitulo['PALAVRAS-CHAVE']);
            $work->fill([
                'about' => $about_array,
            ]);
        }

        if (isset($capitulo['DADOS-BASICOS-DO-CAPITULO']['@attributes']['HOME-PAGE-DO-TRABALHO'])) {
            $url = LattesController::processaURL($capitulo['DADOS-BASICOS-DO-CAPITULO']['@attributes']['HOME-PAGE-DO-TRABALHO']);
            $work->fill([
                'url' => $url,
            ]);
        }

        if (isset($request->instituicao)) {
            $instituicoes[] = $request->instituicao;
            $work->fill([
                'instituicao' => $instituicoes
            ]);
            unset($instituicoes);
        }

        if (isset($request->ppg_nome)) {
            $ppgs[] = $request->ppg_nome;
            $work->fill([
                'ppg_nome' => $ppgs
            ]);
            unset($ppgs);
        }

        if (isset($request->genero)) {
            $generos[] = $request->genero;
            $work->fill([
                'genero' => $generos
            ]);
            unset($generos);
        }

        if (!empty($capitulo['DADOS-BASICOS-DO-CAPITULO']['@attributes']['DOI'])) {
            $sha256 = hash('sha256', $capitulo['DADOS-BASICOS-DO-CAPITULO']['@attributes']['DOI']);
        } else {
            $sha256_array[] = $capitulo['DADOS-BASICOS-DO-CAPITULO']['@attributes']['TITULO-DO-CAPITULO-DO-LIVRO'];
            $sha256_array[] = $capitulo['DADOS-BASICOS-DO-CAPITULO']['@attributes']['ANO'];
            $sha256_array[] = $capitulo['DETALHAMENTO-DO-CAPITULO']['@attributes']['ISBN'];
            $sha256_array[] = $capitulo['DETALHAMENTO-DO-CAPITULO']['@attributes']['PAGINA-INICIAL'];
            $sha256 = hash('sha256', '' . implode("", $sha256_array) . '');
        }

        $work->fill([
            'sha256' => $sha256,
        ]);

        $existingWork = Work::where('sha256', $sha256)->first();

        if ($existingWork) {
            $existingWork->authorLattesIds = array_merge($existingWork->authorLattesIds, [$attributes['NUMERO-IDENTIFICADOR']]);
            $existingWork->authorLattesIds = array_unique($existingWork->authorLattesIds);
            $existingWork->save();
            WorkController::indexRelations($existingWork->id);
        } else {
            try {
                $work->save();
                WorkController::indexRelations($work->id);
                unset($authorLattesIds);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    public function jornais(array $jornais, array $attributes, Request $request)
    {
        foreach ($jornais as $jornal_array) {
            if (isset($jornal_array['DADOS-BASICOS-DO-TEXTO'])) {
                $this->processJornal($jornal_array, $attributes, $request);
            } else {
                foreach ($jornal_array as $jornal) {
                    $this->processJornal($jornal, $attributes, $request);
                }
            }
        }
    }

    function processJornal(array $jornal, array $attributes, Request $request)
    {
        $authorLattesIds = [];
        $authorLattesIds[] = $attributes['NUMERO-IDENTIFICADOR'];
        $work = new Work;
        $work->fill([
            'authorLattesIds' => $authorLattesIds,
            'name' => $jornal['DADOS-BASICOS-DO-TEXTO']['@attributes']['TITULO-DO-TEXTO'],
            'country' => $jornal['DADOS-BASICOS-DO-TEXTO']['@attributes']['PAIS-DE-PUBLICACAO'],
            'datePublished' => $jornal['DADOS-BASICOS-DO-TEXTO']['@attributes']['ANO-DO-TEXTO'],
            'doi' => $jornal['DADOS-BASICOS-DO-TEXTO']['@attributes']['DOI'],
            'inLanguage' => $jornal['DADOS-BASICOS-DO-TEXTO']['@attributes']['IDIOMA'],
            'type' => 'Textos em jornais de notícias/revistas',
        ]);

        $isPartOfJournal['name'] = $jornal['DETALHAMENTO-DO-TEXTO']['@attributes']['TITULO-DO-JORNAL-OU-REVISTA'];
        $isPartOfJournal['issn'] = $jornal['DETALHAMENTO-DO-TEXTO']['@attributes']['ISSN'];
        $isPartOfJournal['volumeNumber'] = $jornal['DETALHAMENTO-DO-TEXTO']['@attributes']['VOLUME'];
        $isPartOfJournal['datePublished'] = $jornal['DETALHAMENTO-DO-TEXTO']['@attributes']['DATA-DE-PUBLICACAO'];
        $isPartOfJournal['pageStart'] = $jornal['DETALHAMENTO-DO-TEXTO']['@attributes']['PAGINA-INICIAL'];
        $isPartOfJournal['pageEnd'] = $jornal['DETALHAMENTO-DO-TEXTO']['@attributes']['PAGINA-FINAL'];
        $isPartOfJournal['city'] = $jornal['DETALHAMENTO-DO-TEXTO']['@attributes']['LOCAL-DE-PUBLICACAO'];


        $work->fill([
            'isPartOf' => $isPartOfJournal,
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
            $work->fill([
                'author' => $aut_array,
                'author_array' => $aut_name_array,
            ]);
            unset($aut_array);
            unset($aut_name_array);
        }

        if (isset($jornal['PALAVRAS-CHAVE'])) {
            $about_array = LattesController::processaPalavrasChaveLattes($jornal['PALAVRAS-CHAVE']);
            $work->fill([
                'about' => $about_array,
            ]);
        }

        if (isset($jornal['DADOS-BASICOS-DO-TEXTO']['@attributes']['HOME-PAGE-DO-TRABALHO'])) {
            $url = LattesController::processaURL($jornal['DADOS-BASICOS-DO-TEXTO']['@attributes']['HOME-PAGE-DO-TRABALHO']);
            $work->fill([
                'url' => $url,
            ]);
        }

        if (isset($request->instituicao)) {
            $instituicoes[] = $request->instituicao;
            $work->fill([
                'instituicao' => $instituicoes
            ]);
            unset($instituicoes);
        }

        if (isset($request->ppg_nome)) {
            $ppgs[] = $request->ppg_nome;
            $work->fill([
                'ppg_nome' => $ppgs
            ]);
            unset($ppgs);
        }

        if (isset($request->genero)) {
            $generos[] = $request->genero;
            $work->fill([
                'genero' => $generos
            ]);
            unset($generos);
        }

        if (!empty($jornal['DADOS-BASICOS-DO-TEXTO']['@attributes']['DOI'])) {
            $sha256 = hash('sha256', $jornal['DADOS-BASICOS-DO-TEXTO']['@attributes']['DOI']);
        } else {
            $sha256_array[] = $jornal['DADOS-BASICOS-DO-TEXTO']['@attributes']['TITULO-DO-TEXTO'];
            $sha256_array[] = $jornal['DADOS-BASICOS-DO-TEXTO']['@attributes']['ANO-DO-TEXTO'];
            $sha256_array[] = $jornal['DETALHAMENTO-DO-TEXTO']['@attributes']['TITULO-DO-JORNAL-OU-REVISTA'];
            $sha256_array[] = $jornal['DETALHAMENTO-DO-TEXTO']['@attributes']['VOLUME'];
            $sha256 = hash('sha256', '' . implode("", $sha256_array) . '');
        }

        $work->fill([
            'sha256' => $sha256,
        ]);

        $existingWork = Work::where('sha256', $sha256)->first();

        if ($existingWork) {
            $existingWork->authorLattesIds = array_merge($existingWork->authorLattesIds, [$attributes['NUMERO-IDENTIFICADOR']]);
            $existingWork->authorLattesIds = array_unique($existingWork->authorLattesIds);
            $existingWork->save();
            WorkController::indexRelations($existingWork->id);
        } else {
            try {
                $work->save();
                WorkController::indexRelations($work->id);
                unset($authorLattesIds);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    public function demais(array $demais, array $attributes, Request $request)
    {
        foreach ($demais as $demais_array) {
            if (isset($demais_array['DADOS-BASICOS-DE-OUTRA-PRODUCAO'])) {
                $this->processDemais($demais_array, $attributes, $request);
            } else {
                foreach ($demais_array as $outra) {
                    $this->processDemais($outra, $attributes, $request);
                }
            }
        }
    }

    function processDemais(array $outra, array $attributes, Request $request)
    {
        $authorLattesIds = [];
        $authorLattesIds[] = $attributes['NUMERO-IDENTIFICADOR'];
        $work = new Work;
        if (isset($outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO'])) {
            $work->fill([
                'authorLattesIds' => $authorLattesIds,
                'name' => $outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['TITULO'],
                'datePublished' => $outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['ANO'],
                'doi' => $outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['DOI'],
                'inLanguage' => $outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['IDIOMA'],
                'type' => 'Demais tipos - ' . $outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['NATUREZA'],
            ]);
            if (isset($outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['HOME-PAGE-DO-TRABALHO'])) {
                $url = LattesController::processaURL($outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['HOME-PAGE-DO-TRABALHO']);
                $work->fill([
                    'url' => $url,
                ]);
            }

            if (!empty($outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['DOI'])) {
                $sha256 = hash('sha256', $outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['DOI']);
            } else {
                $sha256_array[] = $outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['TITULO'];
                $sha256_array[] = $outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['ANO'];
                $sha256_array[] = $outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['NATUREZA'];
                $sha256_array[] = $outra['DADOS-BASICOS-DO-PREFACIO-POSFACIO']['@attributes']['HOME-PAGE-DO-TRABALHO'];
                $sha256 = hash('sha256', '' . implode("", $sha256_array) . '');
            }

            if (isset($request->instituicao)) {
                $instituicoes[] = $request->instituicao;
                $work->fill([
                    'instituicao' => $instituicoes
                ]);
                unset($instituicoes);
            }

            if (isset($request->ppg_nome)) {
                $ppgs[] = $request->ppg_nome;
                $work->fill([
                    'ppg_nome' => $ppgs
                ]);
                unset($ppgs);
            }

            if (isset($request->genero)) {
                $generos[] = $request->genero;
                $work->fill([
                    'genero' => $generos
                ]);
                unset($generos);
            }

            $work->fill([
                'sha256' => $sha256,
            ]);

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
                $work->fill([
                    'author' => $aut_array,
                    'author_array' => $aut_name_array,
                ]);
                unset($aut_array);
                unset($aut_name_array);
            }

            if (isset($outra['PALAVRAS-CHAVE'])) {
                $about_array = LattesController::processaPalavrasChaveLattes($outra['PALAVRAS-CHAVE']);
                $work->fill([
                    'about' => $about_array,
                ]);
            }

            $existingWork = Work::where('sha256', $sha256)->first();

            if ($existingWork) {
                $existingWork->authorLattesIds = array_merge($existingWork->authorLattesIds, [$attributes['NUMERO-IDENTIFICADOR']]);
                $existingWork->authorLattesIds = array_unique($existingWork->authorLattesIds);
                $existingWork->save();
                WorkController::indexRelations($existingWork->id);
            } else {
                try {
                    $work->save();
                    WorkController::indexRelations($work->id);
                    unset($authorLattesIds);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
        if (isset($outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO'])) {
            $work->fill([
                'authorLattesIds' => $authorLattesIds,
                'name' => $outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['TITULO'],
                'datePublished' => $outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['ANO'],
                'doi' => $outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['DOI'],
                'inLanguage' => $outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['IDIOMA'],
                'type' => 'Demais tipos - ' . $outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['NATUREZA'],
            ]);
            if (isset($outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['HOME-PAGE-DO-TRABALHO'])) {
                $url = LattesController::processaURL($outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['HOME-PAGE-DO-TRABALHO']);
                $work->fill([
                    'url' => $url,
                ]);
            }

            if (isset($request->instituicao)) {
                $instituicoes[] = $request->instituicao;
                $work->fill([
                    'instituicao' => $instituicoes
                ]);
                unset($instituicoes);
            }

            if (isset($request->ppg_nome)) {
                $ppgs[] = $request->ppg_nome;
                $work->fill([
                    'ppg_nome' => $ppgs
                ]);
                unset($ppgs);
            }

            if (isset($request->genero)) {
                $generos[] = $request->genero;
                $work->fill([
                    'genero' => $generos
                ]);
                unset($generos);
            }

            if (!empty($outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['DOI'])) {
                $sha256 = hash('sha256', $outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['DOI']);
            } else {
                $sha256_array[] = $outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['TITULO'];
                $sha256_array[] = $outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['ANO'];
                $sha256_array[] = $outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['NATUREZA'];
                $sha256_array[] = $outra['DADOS-BASICOS-DE-OUTRA-PRODUCAO']['@attributes']['HOME-PAGE-DO-TRABALHO'];
                $sha256 = hash('sha256', '' . implode("", $sha256_array) . '');
            }

            $work->fill([
                'sha256' => $sha256,
            ]);

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
                $work->fill([
                    'author' => $aut_array,
                    'author_array' => $aut_name_array,
                ]);
                unset($aut_array);
                unset($aut_name_array);
            }

            if (isset($outra['PALAVRAS-CHAVE'])) {
                $about_array = LattesController::processaPalavrasChaveLattes($outra['PALAVRAS-CHAVE']);
                $work->fill([
                    'about' => $about_array,
                ]);
            }

            $existingWork = Work::where('sha256', $sha256)->first();

            if ($existingWork) {
                $existingWork->authorLattesIds = array_merge($existingWork->authorLattesIds, [$attributes['NUMERO-IDENTIFICADOR']]);
                $existingWork->authorLattesIds = array_unique($existingWork->authorLattesIds);
                $existingWork->save();
                WorkController::indexRelations($existingWork->id);
            } else {
                try {
                    $work->save();
                    WorkController::indexRelations($work->id);
                    unset($authorLattesIds);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
    }

    public function artigosAceitos(array $artigosAceitos, array $attributes, Request $request)
    {
        foreach ($artigosAceitos as $artigosAceitos_array) {
            if (isset($artigosAceitos_array['DADOS-BASICOS-DO-ARTIGO'])) {
                $this->processArtigosAceitos($artigosAceitos_array, $attributes, $request);
            } else {
                foreach ($artigosAceitos_array as $artigoAceito) {
                    $this->processArtigosAceitos($artigoAceito, $attributes, $request);
                }
            }
        }
    }

    function processArtigosAceitos(array $artigoAceito, array $attributes, Request $request)
    {
        $authorLattesIds = [];
        $authorLattesIds[] = $attributes['NUMERO-IDENTIFICADOR'];
        $work = new Work;
        $work->fill([
            'authorLattesIds' => $authorLattesIds,
            'name' => $artigoAceito['DADOS-BASICOS-DO-ARTIGO']['@attributes']['TITULO-DO-ARTIGO'],
            'datePublished' => $artigoAceito['DADOS-BASICOS-DO-ARTIGO']['@attributes']['ANO-DO-ARTIGO'],
            'doi' => $artigoAceito['DADOS-BASICOS-DO-ARTIGO']['@attributes']['DOI'],
            'inLanguage' => $artigoAceito['DADOS-BASICOS-DO-ARTIGO']['@attributes']['IDIOMA'],
            'type' => 'Artigos aceitos para publicação',
        ]);

        $isPartOf['name'] = $artigoAceito['DETALHAMENTO-DO-ARTIGO']['@attributes']['TITULO-DO-PERIODICO-OU-REVISTA'];
        $isPartOf['issn'] = $artigoAceito['DETALHAMENTO-DO-ARTIGO']['@attributes']['ISSN'];
        $isPartOf['volumeNumber'] = $artigoAceito['DETALHAMENTO-DO-ARTIGO']['@attributes']['VOLUME'];
        $isPartOf['issueNumber'] = $artigoAceito['DETALHAMENTO-DO-ARTIGO']['@attributes']['SERIE'];
        $isPartOf['pageStart'] = $artigoAceito['DETALHAMENTO-DO-ARTIGO']['@attributes']['PAGINA-INICIAL'];
        $isPartOf['pageEnd'] = $artigoAceito['DETALHAMENTO-DO-ARTIGO']['@attributes']['PAGINA-FINAL'];
        $isPartOf['city'] = $artigoAceito['DETALHAMENTO-DO-ARTIGO']['@attributes']['LOCAL-DE-PUBLICACAO'];

        $work->fill([
            'isPartOf' => $isPartOf,
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
            $work->fill([
                'author' => $aut_array,
                'author_array' => $aut_name_array,
            ]);
            unset($aut_array);
            unset($aut_name_array);
        }

        if (isset($artigoAceito['PALAVRAS-CHAVE'])) {
            $about_array = LattesController::processaPalavrasChaveLattes($artigoAceito['PALAVRAS-CHAVE']);
            $work->fill([
                'about' => $about_array,
            ]);
        }

        if (isset($artigoAceito['DADOS-BASICOS-DO-ARTIGO']['@attributes']['HOME-PAGE-DO-TRABALHO'])) {
            $url = LattesController::processaURL($artigoAceito['DADOS-BASICOS-DO-ARTIGO']['@attributes']['HOME-PAGE-DO-TRABALHO']);
            $work->fill([
                'url' => $url,
            ]);
        }

        if (isset($request->instituicao)) {
            $instituicoes[] = $request->instituicao;
            $work->fill([
                'instituicao' => $instituicoes
            ]);
            unset($instituicoes);
        }

        if (isset($request->ppg_nome)) {
            $ppgs[] = $request->ppg_nome;
            $work->fill([
                'ppg_nome' => $ppgs
            ]);
            unset($ppgs);
        }

        if (isset($request->genero)) {
            $generos[] = $request->genero;
            $work->fill([
                'genero' => $generos
            ]);
            unset($generos);
        }

        if (!empty($artigoAceito['DADOS-BASICOS-DO-ARTIGO']['@attributes']['DOI'])) {
            $sha256 = hash('sha256', $artigoAceito['DADOS-BASICOS-DO-ARTIGO']['@attributes']['DOI']);
        } else {
            $sha256_array[] = $artigoAceito['DADOS-BASICOS-DO-ARTIGO']['@attributes']['TITULO-DO-ARTIGO'];
            $sha256_array[] = $artigoAceito['DADOS-BASICOS-DO-ARTIGO']['@attributes']['ANO-DO-ARTIGO'];
            $sha256_array[] = $artigoAceito['DADOS-BASICOS-DO-ARTIGO']['@attributes']['HOME-PAGE-DO-TRABALHO'];
            $sha256 = hash('sha256', '' . implode("", $sha256_array) . '');
        }

        $work->fill([
            'sha256' => $sha256,
        ]);

        $existingWork = Work::where('sha256', $sha256)->first();

        if ($existingWork) {
            $existingWork->authorLattesIds = array_merge($existingWork->authorLattesIds, [$attributes['NUMERO-IDENTIFICADOR']]);
            $existingWork->authorLattesIds = array_unique($existingWork->authorLattesIds);
            $existingWork->save();
            WorkController::indexRelations($existingWork->id);
        } else {
            try {
                $work->save();
                WorkController::indexRelations($work->id);
                unset($authorLattesIds);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    public function processProjetos(array $atuacoes, Person $person)
    {
        foreach ($atuacoes as $atuacao_profissional) {
            foreach ($atuacao_profissional as $atuacao_profissional1) {
                //dd($atuacao_profissional1);
                if (isset($atuacao_profissional1['ATIVIDADES-DE-PARTICIPACAO-EM-PROJETO']['PARTICIPACAO-EM-PROJETO'])) {
                    foreach ($atuacao_profissional1['ATIVIDADES-DE-PARTICIPACAO-EM-PROJETO']['PARTICIPACAO-EM-PROJETO'] as $projeto_de_pesquisa) {
                        if (isset($projeto_de_pesquisa['PROJETO-DE-PESQUISA'])) {
                            //dd($projeto_de_pesquisa);
                            $project = new Projeto;
                            $project->fill([
                                'name' => $projeto_de_pesquisa['PROJETO-DE-PESQUISA']['@attributes']['NOME-DO-PROJETO'],
                            ]);
                            try {
                                $project->save();
                                $project->authors()->attach($person);
                            } catch (\Exception $e) {
                                echo $e->getMessage();
                            }
                        }
                    }
                }
            }
        }
    }

    public function createPerson(array $lattes, Request $request)
    {
        //echo "<pre>" . print_r($attributes, true) . "</pre>";
        //echo "<pre>" . print_r($curriculo, true) . "</pre>";
        //echo "<pre>" . print_r($dados_complementares, true) . "</pre>";
        //echo "<pre>" . print_r($outra_producao, true) . "</pre>";
        //echo "<pre>" . print_r($request, true) . "</pre>";

        $person = Person::find($lattes['@attributes']['NUMERO-IDENTIFICADOR']);

        if ($person) {
            if (isset($request->instituicao)) {

                $person->instituicao = array_merge($person->instituicao, [$request->instituicao]);
                $person->instituicao = array_unique($person->instituicao);
            }

            if (isset($request->ppg_nome)) {
                $person->ppg_nome = array_merge($person->ppg_nome, [$request->ppg_nome]);
                $person->ppg_nome = array_unique($person->ppg_nome);
            }

            $person->save();
            exit;
        } else {
            $person = new Person();

            $person->fill([
                'id' => (string)$lattes['@attributes']['NUMERO-IDENTIFICADOR'],
                'lattesID16' => (string)$lattes['@attributes']['NUMERO-IDENTIFICADOR'],
                'lattesDataAtualizacao' => $lattes['@attributes']['DATA-ATUALIZACAO'],
                'resumoCVpt' => $lattes['DADOS-GERAIS']['RESUMO-CV']['@attributes']['TEXTO-RESUMO-CV-RH'],
                'resumoCVen' => $lattes['DADOS-GERAIS']['RESUMO-CV']['@attributes']['TEXTO-RESUMO-CV-RH-EN'],
                'name' => $lattes['DADOS-GERAIS']['@attributes']['NOME-COMPLETO'],
                'nacionalidade' => $lattes['DADOS-GERAIS']['@attributes']['PAIS-DE-NACIONALIDADE'],
                'nomeCitacoesBibliograficas' => $lattes['DADOS-GERAIS']['@attributes']['NOME-EM-CITACOES-BIBLIOGRAFICAS'],
                'orcid' => $lattes['DADOS-GERAIS']['@attributes']['ORCID-ID'],
                'formacao' => $lattes['DADOS-GERAIS']['FORMACAO-ACADEMICA-TITULACAO']
            ]);

            $lattesID10 = $this->lattesID10($lattes['@attributes']['NUMERO-IDENTIFICADOR']);

            if (!empty($lattesID10)) {
                $person->fill([
                    'lattesID10' => $lattesID10
                ]);
            }

            if (isset($lattes['DADOS-GERAIS']['IDIOMAS'])) {
                $person->fill([
                    'idiomas' => $lattes['DADOS-GERAIS']['IDIOMAS'],
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

            if (isset($request->genero)) {
                $person->fill([
                    'genero' => $request->genero
                ]);
            }

            if (isset($request->email)) {
                $person->fill([
                    'email' => $request->email
                ]);
            }

            if (isset($request->unidade)) {
                $person->fill([
                    'unidade' => $request->unidade
                ]);
            }

            try {
                $person->save();
                if (isset($lattes['DADOS-GERAIS']['ATUACOES-PROFISSIONAIS'])) {
                    $this->processProjetos($lattes['DADOS-GERAIS']['ATUACOES-PROFISSIONAIS'], $person);
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
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
                    $this->artigos($lattes['PRODUCAO-BIBLIOGRAFICA']['ARTIGOS-PUBLICADOS'], $lattes['@attributes'], $request);
                }
                if (isset($lattes['PRODUCAO-BIBLIOGRAFICA']['TRABALHOS-EM-EVENTOS'])) {
                    $this->trabalhosEmEventos($lattes['PRODUCAO-BIBLIOGRAFICA']['TRABALHOS-EM-EVENTOS'], $lattes['@attributes'], $request);
                }
                if (isset($lattes['PRODUCAO-BIBLIOGRAFICA']['LIVROS-E-CAPITULOS']['LIVROS-PUBLICADOS-OU-ORGANIZADOS'])) {
                    $this->livros($lattes['PRODUCAO-BIBLIOGRAFICA']['LIVROS-E-CAPITULOS']['LIVROS-PUBLICADOS-OU-ORGANIZADOS'], $lattes['@attributes'], $request);
                }
                if (isset($lattes['PRODUCAO-BIBLIOGRAFICA']['LIVROS-E-CAPITULOS']['CAPITULOS-DE-LIVROS-PUBLICADOS'])) {
                    $this->capitulos($lattes['PRODUCAO-BIBLIOGRAFICA']['LIVROS-E-CAPITULOS']['CAPITULOS-DE-LIVROS-PUBLICADOS'], $lattes['@attributes'], $request);
                }
                if (isset($lattes['PRODUCAO-BIBLIOGRAFICA']['TEXTOS-EM-JORNAIS-OU-REVISTAS'])) {
                    $this->jornais($lattes['PRODUCAO-BIBLIOGRAFICA']['TEXTOS-EM-JORNAIS-OU-REVISTAS'], $lattes['@attributes'], $request);
                }
                if (isset($lattes['PRODUCAO-BIBLIOGRAFICA']['DEMAIS-TIPOS-DE-PRODUCAO-BIBLIOGRAFICA'])) {
                    $this->demais($lattes['PRODUCAO-BIBLIOGRAFICA']['DEMAIS-TIPOS-DE-PRODUCAO-BIBLIOGRAFICA'], $lattes['@attributes'], $request);
                }
                if (isset($lattes['PRODUCAO-BIBLIOGRAFICA']['ARTIGOS-ACEITOS-PARA-PUBLICACAO'])) {
                    $this->artigosAceitos($lattes['PRODUCAO-BIBLIOGRAFICA']['ARTIGOS-ACEITOS-PARA-PUBLICACAO'], $lattes['@attributes'], $request);
                }
                //return redirect('/person' . '/' . $lattes['@attributes']['NUMERO-IDENTIFICADOR']);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        } else {
            echo 'Não foi enviado um arquivo XML do Lattes válido.';
        }
    }
}
