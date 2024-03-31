<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkRequest;
use App\Http\Requests\UpdateWorkRequest;
use App\Models\Work;
use Illuminate\Http\Request;

class WorkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!$request->per_page) {
            $request->per_page = 10;
        }
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
        $works = $query->orderByDesc('datePublished')->paginate($request->per_page)->withQueryString();

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
}