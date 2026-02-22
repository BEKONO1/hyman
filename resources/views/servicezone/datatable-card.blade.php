@php
    $isTrashed = !empty($data->deleted_at);
@endphp

<a href="{{ route('service.list', ['zone_id'=>$data->id]) }}" >
    <div class="card text-center bg-light circle-clip-effect rounded-3">
        <div class="card-body category-card">
            <div class="img-bg d-inline-block rounded-3">
                <img src="{{ getSingleMedia($data, 'zone_image', null) ?? asset('images/default-zone.png') }}" alt="zone" class="img-fluid avatar-70">
            </div>
            <h5 class="categories-name text-capitalize mt-4 mb-2 line-count-1">{{ $data->name }}</h5>
            <p class="categories-desc mb-0 text-capitalize line-count-2">{{ $data->description ?? 'No description' }}</p>
        </div>
    </div>
</a>
