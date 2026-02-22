<x-master-layout>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-block card-stretch">
                <div class="card-body p-0">
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                        <a href="{{ route('handymanEarning') }}" class=" float-end btn btn-sm btn-primary"><i
                                    class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    {{ html()->form('POST', route('handymanpayout.store'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('handymanpayout')->open() }}
                
                    {{ html()->hidden('handyman_id',$payoutdata->handyman_id ?? null) }}
                
                    <div class="row">
                        <div class="form-group col-md-3">
                            {{ html()->label(trans('messages.method') . ' <span class="text-danger">*</span>', 'method')->class('form-control-label')}}
                            {{ html()->select('payment_method', ['cash' => __('messages.cash'),'wallet' => __('messages.wallet')],old('method'))->class('form-select select2js')->id('method')->required()}}
                        </div>
                        <div class="form-group col-md-3">
                            {{ html()->label(__('messages.amount'), 'amount')->class('form-control-label') }}
                            
                            {{-- Display the formatted amount in a readonly input field --}}
                            {{ html()->text('formatted_amount', getPriceFormat($payoutdata->amount ?? 0))
                                ->class('form-control')
                                ->attribute('readonly', true)
                                ->placeholder(__('messages.amount')) 
                            }}
                            
                            {{-- Use a hidden field to store the raw amount value for submission --}}
                            {{ html()->hidden('amount', old('amount') ?? $payoutdata->amount)->id('raw_amount') }}
                        </div>
                
                        <div class="form-group col-md-12">
                            {{ html()->label(__('messages.description'), 'description')->class('form-control-label') }}
                            {{ html()->textarea('description', null)->class('form-control textarea')->rows(3)->placeholder(__('messages.description')) }}
                        </div>
                
                    </div>
                
                    {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary float-end')->id('saveButton') }}                
                    {{ html()->form()->close() }}
                </div>                
            </div>
        </div>
    </div>
</div>

@section('bottom_script')
<script type="text/javascript">
    $(document).ready(function() {
        $('#handymanpayout').on('submit', function() {
            $('#saveButton').attr('disabled', true); 
        });
    });
</script>
@endsection

</x-master-layout>
