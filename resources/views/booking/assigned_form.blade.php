<!-- Modal -->

<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ $pageTitle }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            </button>
        </div>

       {{ html()->form('POST', route('booking.assigned'))->attribute('data-toggle', 'validator')->open() }}
        <div class="modal-body">
        {{ html()->hidden('id',$bookingdata->id ?? null) }}

            <div class="row">
                
                <div class="col-md-12 form-group ">
                {{ html()->label(__('messages.select_name', ['select' => __('messages.handyman')]) . ' <span class="text-danger">*</span>', 'handyman_id')->class('form-control-label')}}
                    
                    <br />
                    @php
                        if($bookingdata->booking_address_id != null)
                        {
                            $route = route('ajax-list', ['type' => 'handyman', 'provider_id' => $bookingdata->provider_id, 'booking_id' => $bookingdata->id ]);
                        } else {
                            $route = route('ajax-list', ['type' => 'handyman', 'provider_id' => $bookingdata->provider_id ]);
                        }
                        $assigned_handyman = $bookingdata->handymanAdded->mapWithKeys(function ($item) {
                            return [$item->handyman_id => optional($item->handyman)->display_name];
                        });
                    @endphp
                    {{ html()->select('handyman_id[]', [$bookingdata->handymanAdded->pluck('handyman_id')], $assigned_handyman)
                                        ->class('select2js form-group')
                                        ->id('handyman_id')
                                        ->required()
                                        ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.handyman')]))
                                        ->attribute('data-ajax--url', $route)
                                    }}
                  
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-md btn-secondary" data-bs-dismiss="modal">{{ trans('messages.close') }}</button>
            <button type="submit" class="btn btn-md btn-primary" id="btn_submit" data-form="ajax" >{{ trans('messages.save') }}</button>
        </div>
        {{ html()->form()->close() }}
  
    </div>
</div>
<script>
    $('#handyman_id').select2({
        width: '100%',
        placeholder: "{{ __('messages.select_name',['select' => __('messages.handyman')]) }}",
    });
</script>