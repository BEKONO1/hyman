@if(isset($query->id))
  @if(isset($query->wallet->amount))
  <a href="{{ route('wallet.show', ['wallet' => $query->id]) }}">
    <div class="d-flex gap-3 align-items-center">
      <div class="text-start">        
      <h6 class="m-0">
        
            {{ getPriceFormat($query->wallet->amount) }}
        
      </h6>
      </div>
    </div>
  </a>
  @else
    <div class="d-flex gap-3 align-items-center">
      <div class="text-start">        
      <h6 class="m-0">
        -
      </h6>
      </div>
    </div>
  @endif
@else

<div class="align-items-center">
    <h6 class="text-center">{{ '-' }} </h6>
</div>
@endif