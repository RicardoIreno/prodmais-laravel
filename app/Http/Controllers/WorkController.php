<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkRequest;
use App\Http\Requests\UpdateWorkRequest;
use App\Models\Work;
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
            $query->where('name', 'iLIKE', '%' . $request->search . '%')->orWhere('author', 'iLIKE', '%' . $request->search . '%');
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->isPartOf) {
            $query->where('isPartOf', $request->isPartOf);
        }
        if ($request->educationEvent) {
            $query->where('educationEvent', $request->educationEvent);
        }
        if ($request->about) {
            $query->whereJsonContains('about', $request->about);
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
        $typeData = $typeData->groupBy('type')->get();


        return view('graficos', array('datePublishedData' => $datePublishedData, 'typeData' => $typeData, 'request' => $request));
    }
}
