@if(isset($query->id))

<a href="{{ route('wallet.show', ['wallet' => $query->id]) }}">
  <div class="d-flex gap-3 align-items-center">
    <div class="text-start">        
    <h6 class="m-0">
      @if(isset($query->wallet->amount))
          {{ getPriceFormat($query->wallet->amount) }}
      @else
          -
      @endif
    </h6>
    </div>
  </div>
</a>
@else

<div class="align-items-center">
    <h6 class="text-center">{{ '-' }} </h6>
</div>
@endif