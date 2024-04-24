<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;

class ExportersController extends Controller
{

    static function generateBibtex($work)
    {
        //dd($work);
        $record = [];

        if (!empty($work->name)) {
            $recordContent[] = 'title   = {' . $work->name . '}';
        }

        if (!empty($work->author_array)) {
            $authorsArray = [];
            foreach ($work->author_array as $author) {
                $authorsArray[] = $author;
            }
            $recordContent[] = 'author = {' . implode(" and ", $authorsArray) . '}';
        }

        if (!empty($work->datePublished)) {
            $recordContent[] = 'year = {' . $work->datePublished . '}';
        }

        if (!empty($work->doi)) {
            $recordContent[] = 'doi = {' . $work->doi . '}';
        }

        if (!empty($work->publisher->name)) {
            $recordContent[] = 'publisher = {' . $work->publisher->name . '}';
        }

        if (!empty($cursor["_source"]["releasedEvent"])) {
            $recordContent[] = 'booktitle   = {' . $cursor["_source"]["releasedEvent"] . '}';
        }

        if ($work->type = "Capítulo de livro publicado") {
            $recordContent[] = 'booktitle   = {' . $work->isPartOfName . '}';
        } else {
            if (!empty($work->isPartOfName)) {
                $recordContent[] = 'journal   = {' . $work->isPartOfName . '}';
            }
        }

        $sha256 = hash('sha256', '' . implode("", $recordContent) . '');

        switch ($work->type) {
            case "Artigo publicado":
                $record[] = '@article{article' . substr($sha256, 0, 8) . ',';
                $record[] = implode(",\n", $recordContent);
                $record[] = '}';
                break;
            case "Livro publicado ou organizado":
                $record[] = '@book{book' . substr($sha256, 0, 8) . ',';
                $record[] = implode(",\n", $recordContent);
                $record[] = '}';
                break;
            case "Capítulo de livro publicado":
                $record[] = '@inbook{inbook' . substr($sha256, 0, 8) . ',';
                $record[] = implode(",\n", $recordContent);
                $record[] = '}';
                break;
            case "Trabalhos em eventos":
                $record[] = '@inproceedings{inproceedings' . substr($sha256, 0, 8) . ',';
                $record[] = implode(",\n", $recordContent);
                $record[] = '}';
                break;
            case "TRABALHO DE EVENTO-RESUMO":
                $record[] = '@inproceedings{inproceedings' . substr($sha256, 0, 8) . ',';
                $record[] = implode(",\n", $recordContent);
                $record[] = '}';
                break;
            case "TESE":
                $record[] = '@mastersthesis{mastersthesis' . substr($sha256, 0, 8) . ',';
                $recordContent[] = 'school = {Universidade de São Paulo}';
                $record[] = implode(",\n", $recordContent);
                $record[] = '}';
                break;
            default:
                $record[] = '@misc{misc' . substr($sha256, 0, 8) . ',';
                $record[] = implode(",\n", $recordContent);
                $record[] = '}';
        }
        $record_blob = implode("\n", $record);
        return $record_blob;
    }
    public function bibtex($id)
    {
        $person = Person::find($id)->load(['works' => function ($query) {
            $query->orderBy('datePublished', 'desc');
        }]);
        foreach ($person->works as $work) {
            $bibtex_array[] = $this->generateBibtex($work);
        }
        $filename = "$id.bib";
        $record_blob = implode("\n", $bibtex_array);

        // Cria uma resposta que força o download do arquivo .bib
        return response($record_blob)
            ->header('Content-Type', 'application/x-bibtex')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
