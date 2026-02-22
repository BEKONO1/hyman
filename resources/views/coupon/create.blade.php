<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? __('messages.list') }}</h5>
                            @if($auth_user->can('coupon list'))
                            <a href="{{ route('coupon.index') }}" class=" float-end btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('coupon.store'))->attribute('data-toggle', 'validator')->id('coupon')->open()}}
                        {{ html()->hidden('id',$coupondata->id ?? null) }}
                        <div class="row">
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.code') . ' <span class="text-danger">*</span>', 'code')->class('form-control-label') }}
                                {{-- @if($coupondata->id == null) --}}
                                    {{ html()->text('code', old('code', $coupondata->code))->placeholder(__('messages.code'))->class('form-control')->required()}}
                                {{-- @else
                                    <p>{{ $coupondata->code }}</p>
                                @endif --}}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.discount_type') . ' <span class="text-danger">*</span>', 'discount_type')->class('form-control-label') }}
                                {{ html()->select('discount_type', ['fixed' => __('messages.fixed'), 'percentage' => __('messages.percentage')], old('status'))->id('discount_type')->class('form-select select2js')->required()}}
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.discount') . ' <span class="text-danger">*</span>', 'discount')->class('form-control-label') }}
                                {{ html()->number('discount', $coupondata->discount)->attribute('min', 0)->attribute('step', 'any')->placeholder(__('messages.discount'))->class('form-control')->required()}}
                            </div>
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.expire_date') . ' <span class="text-danger">*</span>', 'expire_date')->class('form-control-label') }}
                                {{ html()->text('expire_date',$coupondata->expire_date)->placeholder(__('messages.expire_date'))->class('form-control datetimepicker')->required()}}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.select_name', ['select' => __('messages.service')]) . ' <span class="text-danger">*</span>', 'name')->class('form-control-label') }}
                                <br />
                                @php
                                    $assigned_service = $coupondata->serviceAdded->mapWithKeys(function ($item) {
                                        return [$item->service_id => optional($item->service)->name];
                                    });
                                @endphp
                                {{ html()->select('service_id[]', $assigned_service, $coupondata->serviceAdded->pluck('service_id'))
                                    ->class('select2js form-group service')
                                    ->required()
                                    ->multiple()
                                    ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.service')]))
                                    ->attribute('data-ajax--url', route('ajax-list', ['type' => 'service-list']))
                                }}
                                
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.status') . ' <span class="text-danger">*</span>', 'status')->class('form-control-label') }}
                                {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')],$coupondata->expire_date)->id('role')->class('form-select select2js')->required()}}
                            </div>

                        </div>
                    
                        {{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-end') }}
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('discount_type');
        const valueInput = document.getElementById('discount');
        const valueError = document.getElementById('value-error');

        function setMinMax() {
                const type = typeSelect.value;

                if (type === 'percentage') {
                    valueInput.min = 1;
                    valueInput.max = 100;
                } else if (type === 'fixed') {
                    valueInput.removeAttribute('min');
                    valueInput.removeAttribute('max');
                }
            }

        // Initialize min/max based on the current selection
        $(document).on('change', '#discount_type', function() {
        setMinMax();
        });
        // Listen for changes in the type dropdown
        typeSelect.addEventListener('change', setMinMax);

        // Also validate on input change for the value field
        valueInput.addEventListener('input', setMinMax);
    });
</script>
</x-master-layout>