<x-master-layout>
    <div class="container-fluid">
        <div class="row">
        <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? __('messages.list') }}</h5>
                                <a href="{{ route('tax.index') }}" class=" float-end btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('tax.store'))->attribute('data-toggle', 'validator')->id('tax')->open()}}
                        {{ html()->hidden('id', $taxdata->id ?? null) }}
                        
                        <div class="row">
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.title') . ' <span class="text-danger">*</span>', 'title')->class('form-control-label') }}
                                {{ html()->text('title', $taxdata->title)->placeholder(__('messages.title'))->class('form-control')->required()}}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                            
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.value') . ' <span class="text-danger">*</span>', 'value')->class('form-control-label') }}
                                {{ html()->number('value', $taxdata->value)->attributes(['min' => 0,'step' => 'any'])->placeholder(__('messages.value'))->class('form-control')->required()}}
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.select_name', ['select' => __('messages.type')]) . ' <span class="text-danger">*</span>', 'type')->class('form-control-label') }}
                                {{ html()->select('type', ['percent' => __('messages.percent'), 'fixed' => __('messages.fixed')], $taxdata->type)
                                    ->class('form-select select2js')
                                    ->id('type')
                                    ->required()
                                }}
                            </div>
                            
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.status') . ' <span class="text-danger">*</span>', 'status')->class('form-control-label') }}
                                {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')], $taxdata->status)
                                    ->class('form-select select2js')
                                    ->id('status')
                                    ->required()
                                }}
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
        const typeSelect = document.getElementById('type');
        const valueInput = document.getElementById('value');
        const valueError = document.getElementById('value-error');

        function setMinMax() {
                const type = typeSelect.value;

                if (type === 'percent') {
                    valueInput.min = 1;
                    valueInput.max = 100;
                } else if (type === 'fixed') {
                    valueInput.removeAttribute('min');
                    valueInput.removeAttribute('max');
                }
            }

        // Initialize min/max based on the current selection
        $(document).on('change', '#type', function() {
        setMinMax();
        });
        // Listen for changes in the type dropdown
        typeSelect.addEventListener('change', setMinMax);

        // Also validate on input change for the value field
        valueInput.addEventListener('input', setMinMax);
    });
</script>
</x-master-layout>