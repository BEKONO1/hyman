<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                    <div class="card card-block card-stretch">
                        <div class="card-body p-0">
                            <div class="d-flex justify-content-between align-items-center p-3">
                                <h5 class="fw-bold">{{ $pageTitle ?? __('messages.list') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    {{ html()->form('POST', route('refund-cancellation-policy-save'))->attribute('data-toggle', 'validator')->open() }}
                    {{ html()->hidden('id',$setting_data->id ?? null) }}
                    @include('partials._language_toggale')
                        @foreach($language_array as $language)
                        <div id="form-language-{{ $language['id'] }}" class="language-form" style="display: {{ $language['id'] == app()->getLocale() ? 'block' : 'none' }};">
                        
                            <div class="row">
                            @foreach(['value' => __('messages.refund_cancellation_policy')] as $field => $label)
                                <div class="form-group col-md-{{ $field === 'value' ? '12' : '12' }}">
                                    {{ html()->label($label . ($field === 'value' ? ' <span class="text-danger">*</span>' : ''), $field)->class('form-control-label language-label') }}

                                    @php
                                        $value = $language['id'] == 'en' 
                                            ? $refund_cancellation_policy ? $refund_cancellation_policy : ''  
                                            : ($setting_data ? $setting_data->translate($field, $language['id']) : '');
                                            
                                        $name = $language['id'] == 'en' ? $field : "translations[{$language['id']}][$field]";
                                    @endphp

                                    @if($field === 'value')
                                        
                                        {{ html()->textarea($name, $value)
                                            ->class('form-control tinymce-refund_cancellation_policy')
                                            ->rows(3)
                                            ->placeholder($label) }}
                                    @endif

                                    <small class="help-block with-errors text-danger"></small>
                                </div>
                                @endforeach
                        <!-- <div class="form-group col-md-12">
                            {{ html()->label(__('messages.refund_cancellation_policy'), 'refund_cancellation_policy')->class('form-control-label') }}
                            {{ html()->textarea('value',$setting_data->value)->class('form-control tinymce-refund_cancellation_policy')->placeholder(__('messages.refund_cancellation_policy')) }}
                         </div> -->
                    </div>
                    </div>
                    @endforeach
                    <div class="form-group col-md-4">
                        {{ html()->label(__('messages.status'), 'status')->class('form-control-label') }}
                        <div class="form-control d-flex align-items-center justify-content-between">
                            <label for="status" class="mb-0">{{ __('messages.status') }}</label>
                            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                                <input type="checkbox" class="custom-control-input" name="status" id="status" value="1" {{ ($status ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="status"></label>
                            </div>
                        </div>
                    </div>
                    @if(auth()->user()->hasRole(['admin', 'demo_admin']))
                    {{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-end') }}
                    @endif
                    {{ html()->form()->close() }}
                </div>
            </div>
        </div>
    </div>
    @section('bottom_script')
        <script>
            (function($) {
                $(document).ready(function(){
                    tinymceEditor('.tinymce-refund_cancellation_policy',' ',function (ed) {
    
                    }, 450)
                
                });
    
            })(jQuery);
        </script>
    @endsection
    </x-master-layout>