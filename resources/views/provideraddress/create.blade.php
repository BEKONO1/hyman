<x-master-layout>
    <div class="container-fluid">
    @include('partials._provider')
        <div class="row">
        <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? __('messages.list') }}</h5>
                            @if($auth_user->can('provideraddress list'))
                                <a href="{{ route('provideraddress.show',['provideraddress' => $providerdata->id]) }}" class=" float-end btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('provideraddress.store'))->attribute('data-toggle', 'validator')->id('provideraddress')->open()}}
                        {{ html()->hidden('id', $provideraddress->id ?? null) }}
                        <div class="row">
                                @if(auth()->user()->hasAnyRole(['admin','demo_admin']))
                                <div class="form-group col-md-4">
                                    {{ html()->label(__('messages.select_name', ['select' => __('messages.providers')]) . ' <span class="text-danger">*</span>', 'provider_id')->class('form-control-label') }}
                                    <br />
                                    {{ html()->select('provider_id', [$providerdata->id => $providerdata->display_name], $providerdata->id)
                                        ->class('select2js form-group providers')
                                        ->required()
                                        ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.providers')]))
                                        
                                        }}
                                </div>
                            @endif
                              
                    
                            <div id="latFields" class="form-group col-md-4">
                                {{ html()->label(__('messages.latitude'), 'latitude')->class('form-control-label') }}
                                {{ html()->number('latitude',$provideraddress->latitude )->id('latitude')->placeholder('00.0000')->class('form-control')->attribute('step', 'any')}}
                            </div>
                    
                            <div id="lngFields" class="form-group col-md-4">
                                {{ html()->label(__('messages.longitude'), 'longitude')->class('form-control-label') }}
                                {{ html()->number('longitude', $provideraddress->longitude )->id('longitude')->placeholder('00.0000')->class('form-control')->attribute('step', 'any')}}
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.status') . ' <span class="text-danger">*</span>', 'status')->class('form-control-label') }}
                                {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')], $provideraddress->status )->class('form-select select2js')->required()}}
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.address') . ' <span class="text-danger">*</span>', 'address')->class('form-control-label') }}
                                {{ html()->textarea('address', $provideraddress->address)->id('address-input')->class('form-control textarea')->rows(3)->required()->placeholder(__('messages.address'))}}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                        </div>
                    
                        {{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-end') }}
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>
<script>
    $('#address-input').on('input', function() {
        var address = $(this).val();

        if (address) {
            $.ajax({
                url: '{{ route("getLatLong") }}',
                method: 'POST',
                data: { address: address },
                dataType: 'json',
                success: function(response) {
                    var latitude = response.latitude;
                    var longitude = response.longitude;
                    if (latitude != null && longitude != null) {
                       
                        $('#latitude').val(latitude);
                        $('#longitude').val(longitude);
                    }
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        } 
    });

</script>
