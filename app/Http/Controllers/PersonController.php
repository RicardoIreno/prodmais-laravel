<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Person::query();
        if ($request->name) {
            $query->where('name', 'iLIKE', '%' . $request->name . '%');
        }
        if ($request->genero) {
            $query->where('genero', $request->genero);
        }
        if ($request->instituicao) {
            $query->whereJsonContains('instituicao', $request->instituicao);
        }
        if ($request->ppg_nome) {
            $query->whereJsonContains('ppg_nome', $request->ppg_nome);
        }
        $people = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('people', compact('people', 'request'));
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
    public function store(StorePersonRequest $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        Person::create($request->all())->id;

        return redirect()->route('welcome')
            ->with('success', 'Person created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Person $person)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Person $person)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonRequest $request, Person $person)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Person $person)
    {
        //
    }
}