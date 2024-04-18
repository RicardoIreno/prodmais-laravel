<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Http\Request;
use App\Models\Work;
use Illuminate\Support\Facades\DB;

class Facet extends Component
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
        $query = Work::select($this->field . ' as field', DB::raw('count(*) as count'));
        if ($this->request->name) {
            $query->where('name', 'like', '%' . $this->request->name . '%');
        }
        if ($this->request->type) {
            $query->where('type', $this->request->type);
        }
        if ($this->request->search) {
            $query->where('name', 'iLIKE', '%' . $this->request->search . '%')->orWhere('author', 'iLIKE', '%' . $this->request->search . '%');
        }
        if ($this->request->datePublished) {
            $query->where('datePublished', $this->request->datePublished);
        }
        if ($this->request->inLanguage) {
            $query->where('inLanguage', 'like', '%' .  $this->request->inLanguage . '%');
        }
        if ($this->request->issn) {
            $query->where('issn', $this->request->issn);
        }
        if ($this->request->isbn) {
            $query->where('isbn', $this->request->isbn);
        }
        if ($this->request->doi) {
            $query->where('doi', $this->request->doi);
        }
        if ($this->request->inSupportOf) {
            $query->where('inSupportOf', $this->request->inSupportOf);
        }
        if ($this->request->sourceOrganization) {
            $query->where('sourceOrganization', $this->request->sourceOrganization);
        }
        if ($this->request->about) {
            $query->whereJsonContains('about', $this->request->about);
        }
        if ($this->request->author_array) {
            $query->whereJsonContains('author_array', $this->request->author_array);
        }
        if ($this->request->releasedEvent) {
            $query->where('releasedEvent', 'like', '%' . $this->request->releasedEvent . '%');
        }
        if ($this->request->isPartOf_name) {
            $query->where('isPartOf_name', 'like', '%' . $this->request->isPartOf_name . '%');
        }
        if ($this->field == 'datePublished') {
            $query->groupBy($this->field)->orderByDesc('field')->limit(50);
        } else {
            $query->groupBy($this->field)->orderByDesc('count')->orderByDesc($this->field)->limit(50);
        }

        $facets = $query->get();
        return view('components.facet', compact('facets'));
    }
}
