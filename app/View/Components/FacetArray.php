<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Http\Request;
use App\Models\Work;
use Illuminate\Support\Facades\DB;

class FacetArray extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public Request $request,
        public string $field,
        public string $fieldName
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        // $facets = Work::select(DB::raw("jsonb_array_elements_text(about) as subject"))
        //     ->groupBy('subject')
        //     ->selectRaw('count(*) as count')
        //     ->get();
        $facets = Work::select(DB::raw("jsonb_array_elements_text(about) as subject"))
            ->groupBy('subject')
            ->selectRaw('count(*) as count')
            ->orderBy('count', 'desc')
            ->get();
        return view('components.facetArray', compact('facets'));
    }
}