@if(isset($query->providers))

@if($query->providers->user_type=='provider')
<a href="{{ route('provider_info', $query->providers->id) }}">
  @else

  <a href="{{ route('booking.index', ['customer_id' => $query->providers->id]) }}">

  @endif
  <div class="d-flex gap-3 align-items-center">
    <img src="{{ getSingleMedia(optional($query->providers),'profile_image', null) }}" alt="avatar" class="avatar img-fluid avatar-40 rounded-pill">
    <div class="text-start">
      <h6 class="m-0">{{ optional($query->providers)->display_name }} </h6>
      <span>{{ optional($query->providers)->email ?? '--' }}</span>
    </div>
  </div>
</a>
@else

<div class="align-items-center">
    <h6 class="text-center">{{ '-' }} </h6>
</div>
@endif



