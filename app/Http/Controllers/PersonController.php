<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /**
     * Generate tag cloud.
     */

    public static function personTagCloud(string $id)
    {
        $queryAbout = DB::table('works')->select(DB::raw("jsonb_array_elements_text(about) as about"));
        $queryAbout->whereRaw('("authorLattesIds")::jsonb @> \'["' . $id . '"]\'');
        $queryAbout->selectRaw('count(*) as count');
        $queryAbout->groupBy('about');
        $queryAbout->orderBy('count', 'desc');
        $aboutData = $queryAbout->limit(100)->get();

        $aboutData = json_encode($aboutData);
        $aboutData = json_decode($aboutData);
        shuffle($aboutData);

        $buf = [];
        foreach ($aboutData as $t) {
            $buf[] = '<li><a class="tag" data-weight="' . $t->count . '">' . $t->about . '</a> </li>';
        }

        echo ("<ul class='tag-cloud' role='navigation' aria-label='Tags mais usadas'>");
        echo (implode("", $buf));
        echo "</ul>";
    }
}
