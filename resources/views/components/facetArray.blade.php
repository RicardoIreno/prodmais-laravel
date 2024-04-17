@if ((count($facets) == 1) && $facets[0]->field == '')

@else
<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button collapsed" style="width:90%" type="button" data-bs-toggle="collapse"
            data-bs-target="#{{ hash('crc32', $fieldName) }}" aria-expanded="true"
            aria-controls="{{ hash('crc32', $fieldName) }}">
            {{ $fieldName }}
        </button>
    </h2>
    <div id="{{ hash('crc32', $fieldName) }}" class="accordion-collapse collapse"
        data-bs-parent="#{{ hash('crc32', $fieldName) }}">
        <div class="accordion-body">
            <ul class="c-filterdrop__content" name="bloc1">
                @foreach ($facets as $facet)
                @php
                $searchArray = [];
                $searchArray[] = $field . '=' . $facet->field;
                @endphp
                @foreach ($request->all() as $k => $v)
                @php
                if ($k != $field) {
                $searchArray[] = $k . '=' . $v;
                }
                @endphp
                @endforeach
                @php
                if($type == "Work"){
                $page = "works";
                }
                if($type == "Person"){
                $page = "people";
                }
                @endphp
                <li class="c-filterdrop__item">
                    <a href="{{ $page }}?{{ implode('&', $searchArray)}}">
                        {{ $facet->field }} <span class="c-filterdrop__count">{{ $facet->count }}</span>
                    </a>
                </li>
                @endforeach

            </ul>
        </div>
    </div>
</div>

@endif