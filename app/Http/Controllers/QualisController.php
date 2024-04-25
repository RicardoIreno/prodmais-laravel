<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQualisRequest;
use App\Http\Requests\UpdateQualisRequest;
use App\Models\Qualis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QualisController extends Controller
{
    public function consultarQualis(Request $request)
    {
        $issn = $request->input('issn');
        $titulo = $request->input('titulo');
        $area = $request->input('area');

        // Certifique-se de que a tabela onde o Qualis está armazenado seja substituída pelo nome correto
        $query = DB::table('qualis');

        if ($issn) {
            $query->where('issn', $issn);
        }

        if ($titulo) {
            $query->where('titulo', 'ilike', '%' . $titulo . '%');
        }

        if ($area) {
            $query->where('area', $area);
        }

        $qualis = $query->get();

        if ($qualis) {
            // Resultado encontrado no Qualis, faça o que for necessário aqui
            return response()->json(['data' => $qualis], 200);
        } else {
            // Resultado não encontrado no Qualis
            return response()->json(['message' => 'Nenhum resultado encontrado no Qualis'], 404);
        }
    }

    public function index(Request $request)
    {
        $query = Qualis::query();
        if ($request->issn) {
            $query->where('issn', $request->issn);
        }
        if ($request->titulo) {
            $query->where('titulo', 'iLIKE', '%' . $request->titulo . '%');
        }
        if ($request->area) {
            $query->where('area', $request->area);
        }
        if ($request->orderByEstrato) {
            $query->orderBy('estrato', $request->orderByEstrato);
        }
        if ($request->orderByTitulo) {
            $query->orderBy('titulo', $request->orderByTitulo);
        }
        $qualis = $query->paginate(15);

        return view('qualis', compact('qualis'));
    }

    public static function queryQualis($issn)
    {
        $query = DB::table('qualis');
        $query->where('issn', $issn);
        $qualis = $query->first();

        return $qualis;
    }
}