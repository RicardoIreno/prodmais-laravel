<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkRequest;
use App\Http\Requests\UpdateWorkRequest;
use App\Models\Work;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Work::query();
        if ($request->datePublished) {
            $query->where('datePublished', $request->datePublished);
        }
        if ($request->inLanguage) {
            $query->where('inLanguage', $request->inLanguage);
        }
        if ($request->name) {
            $query->where('name', 'iLIKE', '%' . $request->name . '%');
        }
        if ($request->search) {
            $query->where('name', 'iLIKE', '%' . $request->search . '%');
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->isPartOfName) {
            $query->where('isPartOfName', $request->isPartOfName);
        }
        if ($request->educationEventName) {
            $query->where('educationEventName', $request->educationEventName);
        }
        if ($request->qualis) {
            $query->where('qualis', $request->qualis);
        }
        if ($request->about) {
            $query->whereJsonContains('about', $request->about);
        }
        if ($request->genero) {
            $query->whereJsonContains('genero', $request->genero);
        }
        if ($request->instituicao) {
            $query->whereJsonContains('instituicao', $request->instituicao);
        }
        if ($request->ppg_nome) {
            $query->whereJsonContains('ppg_nome', $request->ppg_nome);
        }
        if ($request->author_array) {
            $query->whereJsonContains('author_array', $request->author_array);
        }

        $works = $query->orderByDesc('datePublished')->paginate(10)->withQueryString();

        return view('works', compact('works', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Work $work)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Work $work)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkRequest $request, Work $work)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Work $work)
    {
        //
    }

    public function graficos(Request $request)
    {
        // Gerar gráfico de datas
        $datePublishedData = DB::table('works')->select('datePublished as year', \DB::raw('COUNT(*) as total'));
        if ($request->datePublished) {
            $datePublishedData = $datePublishedData->where('datePublished', $request->datePublished);
        }
        if ($request->type) {
            $datePublishedData = $datePublishedData->where('type', $request->type);
        }
        $datePublishedData = $datePublishedData->groupBy('datePublished')->get();


        // Gerar gráfico de tipos
        $typeData = DB::table('works')->select('type', \DB::raw('COUNT(*) as total'));
        if ($request->datePublished) {
            $typeData = $typeData->where('datePublished', $request->datePublished);
        }
        if ($request->type) {
            $typeData = $typeData->where('type', $request->type);
        }
        $typeData = $typeData->groupBy('type')->orderBy('total', 'desc')->get();

        // Gerar gráfico de tag cloud

        $queryAbout = DB::table('works')->select(DB::raw("jsonb_array_elements_text(about) as about"));
        if ($request->type) {
            $queryAbout->where('type', $request->type);
        }
        if ($request->datePublished) {
            $queryAbout->where('datePublished', $request->datePublished);
        }
        if ($request->about) {
            $queryAbout->whereJsonContains('about', $request->about);
        }
        if ($request->instituicao) {
            $queryAbout->whereJsonContains('instituicao', $request->instituicao);
        }
        if ($request->ppg_nome) {
            $queryAbout->whereJsonContains('ppg_nome', $request->ppg_nome);
        }

        $queryAbout->selectRaw('count(*) as count');
        $queryAbout->groupBy('about');
        $queryAbout->orderBy('count', 'desc');

        $aboutData = $queryAbout->limit(200)->get();
        //$aboutData = json_encode($aboutData);


        return view('graficos', array('aboutData' => $aboutData, 'datePublishedData' => $datePublishedData, 'typeData' => $typeData, 'request' => $request));
    }


    public static function indexRelations($id)
    {
        $record = Work::find($id);
        $record->authors()->detach();

        foreach ($record->authorLattesIds as $authorLattesId) {
            $person = Person::find($authorLattesId);
            $record->authors()->attach($person);
        }
    }
}