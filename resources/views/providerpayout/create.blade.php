<x-master-layout>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-block card-stretch">
                <div class="card-body p-0">
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                        <a href="{{ route('earning') }}" class=" float-end btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    {{ html()->form('POST', route('providerpayout.store'))->attributes(['enctype' => 'multipart/form-data', 'data-toggle' => 'validator', 'id' => 'providerpayout'])->open() }}       
                    {{ html()->hidden('provider_id',$payoutdata->provider_id ?? null) }}
                    {{ html()->hidden('redirect_type', $redirect_type) }}
                
                    <div class="row">
                        <div class="form-group col-md-4" id="payment_method_id">
                            {{ html()->label(trans('messages.method') . ' <span class="text-danger">*</span>', 'method')->class('form-control-label') }}
                            {{ html()->select('payment_method', ['bank' => __('messages.bank'),'cash' => __('messages.cash'),'wallet' => __('messages.wallet'),], old('method'))->attributes(['id' => 'method', 'class' => 'form-select select2js', 'required']) }}
                        </div>
                
                        <div class="form-group col-md-4" id="select_bank">
                            {{ html()->label(__('messages.select_bank', ['select' => __('messages.select_bank')]) . ' <span class="text-danger">*</span>', 'bank')->class('form-control-label') }}
                            <a href="{{ route('bank.create', ['user_id' => $payoutdata->provider_id]) }}" class="me-1 btn-link btn-link-hover"><i class="fa fa-plus-circle"></i> {{ trans('messages.add_form_title', ['form' => trans('messages.bank')]) }}</a>
                            <br />
                            {{ html()->select('bank', [])
                                ->attributes(['class' => 'select2js form-group col-md-12 bank','id' => 'bank', 'data-placeholder' => __('messages.select_bank', ['select' => __('messages.')])]) }}
                        </div>
                        <div class="form-group col-md-4">
                            {{ html()->label(__('messages.amount'), 'amount')->class('form-control-label') }}
                            
                            {{-- Display the formatted amount in a readonly input field --}}
                            {{ html()->text('formatted_amount', getPriceFormat($payoutdata->amount ?? 0))->attributes([
                                'class' => 'form-control',
                                'readonly' => true,
                                'placeholder' => __('messages.amount'),
                            ]) }}
                
                            {{-- Use a hidden field to store the raw amount value for submission --}}
                            {{ html()->hidden('amount', old('amount') ?? $payoutdata->amount)->attributes(['id' => 'raw_amount']) }}
                        </div>
                        <div class="form-group col-md-12"  id='payment_gateway'>
                            <label class="form-control-label">{{__('messages.payment_gateway',['gateway'=>__('messages.payment_gateway')])}}</label><br/>
                            <div class="form-check-inline">
                                      <label class="form-check-label">
                                          <input type="radio" class="form-check-input is_test" value="razorpayx" name="payment_gateway" data-type="razorpayx" {{ old('payment_gateway') == 'razorpayx' || !old('payment_gateway') ? 'checked' : '' }}>{{__('messages.razorx')}}
                                      </label>
                            </div>
                            <div class="form-check-inline">
                                      <label class="form-check-label">
                                          <input type="radio" class="form-check-input is_test" value="stripe" name="payment_gateway" data-type="stripe" {{ old('payment_gateway') == 'stripe' ? 'checked' : '' }} >{{__('messages.stripe')}}
                                      </label>
                            </div>
                
                            <small class="help-block with-errors text-danger"></small>
                        </div>

                      
                          
                            <div class="form-group col-md-12">
                                {{ html()->label(__('messages.description'), 'description')->class('form-control-label') }}
                                {{ html()->textarea('description', null)->attributes(['class' => 'form-control textarea', 'rows' => 3, 'placeholder' => __('messages.description')]) }}
                            </div>
                            
                        </div>
                    {{ html()->submit(trans('messages.save'))->attributes(['class' => 'btn btn-md btn-primary float-end', 'id' => 'saveButton']) }}
                    {{ html()->form()->close() }}
                </div>                
            </div>
        </div>
    </div>
</div>
@section('bottom_script')
<script type="text/javascript">
            (function($) {
                "use strict";
                $(document).ready(function(){
                    var payment_method =  "{{ isset($provider_payouts->payment_method) ? $provider_payouts->payment_method : 0 }}";
                       
                    var provider_id = $('input[name="provider_id"]').val();
       
                    bankdetails(provider_id , bank);

                    $(document).on('change', '#method', function() {
                        var payment_method = $(this).val();

                        if (payment_method == 'bank') {
                            $("#select_bank").removeClass("d-none"); 
                            $("#bank").attr("required", true);      
                            $("#payment_gateway").removeClass("d-none");
                            bankdetails(provider_id, bank);
                        } else {
                            $('#select_bank').addClass("d-none");  
                            $("#bank").attr("required", false);      
                            $('#payment_gateway').addClass("d-none");
                        }
                    });
                   
                })
                function bankdetails(provider_id , bank ="" ){
                    var bank_route = "{{ route('ajax-list', [ 'type' => 'bank','provider_id' =>'']) }}"+provider_id;
                    bank_route = bank_route.replace('amp;','');

                    $.ajax({
                        url: bank_route,
                        success: function(result){
                          
                            $('#bank').select2({
                                width: '100%',
                                placeholder: "{{ trans('messages.bank_name',['select' => trans('messages.bank_name')]) }}",
                                data: result.results
                            });
                            if(bank != null){
                                $("#bank_details").val(bank).trigger('change');
                            }
                        }
                    });
                }
        
               
            })(jQuery);


            window.onload = function() {
    if (window.history && window.history.pushState) {
        window.history.pushState('', null, '');
        window.onpopstate = function() {
            window.history.pushState('', null, '');
        };
    }
};

    $(document).ready(function() {
        $('#providerpayout').on('submit', function() {
            $('#saveButton').attr('disabled', true); 
        });
    });
</script>
@endsection
</x-master-layout>
