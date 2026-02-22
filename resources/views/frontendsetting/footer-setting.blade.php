{{ html()->form('POST', route('footer_page_settings'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('myForm')->open() }}

{{ html()->hidden('id', $landing_page_data ?? null)->placeholder('id')->class('form-control') }}
{{ html()->hidden('type', $page)->placeholder('id')->class('form-control') }}

<div class="form-group">
    <div class="form-control d-flex justify-content-between">
        <label class="mb-0" for="enable_footer_setting">{{ __('messages.enable_footer_setting') }}</label>
        <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
            <input type="checkbox" class="custom-control-input footer_setting" name="status" id="footer_setting"
                data-type="footer_setting"
                {{ !empty($landing_page_data) && $landing_page_data->status ? 'checked' : '' }}>
            <label class="custom-control-label" for="footer_setting"></label>
        </div>
    </div>
</div>

<div class="row" id='enable_footer_setting'>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body setting-pills">
                <div class="form-group">
                    <div class="form-control d-flex align-items-center justify-content-between">
                        <label class="mb-0"
                            for="enable_popular_category">{{ __('messages.enable_popular_category') }}</label>
                        <div
                            class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                            <input type="checkbox" class="custom-control-input" name="enable_popular_category"
                                id="enable_popular_category"
                                {{ !empty($landing_page_data->enable_popular_category) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="enable_popular_category"></label>
                        </div>
                    </div>
                </div>

                @php
                    $selectedCategoryIds = [];
                    $categoryNames = [];
                    if ($landing_page_data) {
                        $decodedData = json_decode($landing_page_data->value, true);
                        $selectedCategoryIds = isset($decodedData['category_id']) ? $decodedData['category_id'] : [];
                        if ($selectedCategoryIds) {
                            $categoryNames = \App\Models\Category::where('status', 1)
                                ->whereIn('id', $selectedCategoryIds)
                                ->pluck('name', 'id')
                                ->toArray();
                        }
                    }
                @endphp

                <div class="form-group" id="enable_select_category">
                    {{ html()->label(__('messages.select_name', ['select' => __('messages.category')]) . ' <span class="text-danger">*</span>', 'category_id[]')->class('form-control-label') }}
                    <br />
                    {{ html()->select('category_id[]', $categoryNames, old('category_id', $selectedCategoryIds))->class('select2js form-control category_id')->id('category_id')->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.category')]))->attribute('data-ajax--url', route('ajax-list', ['type' => 'category_footer']))->multiple() }}
                </div>
                <div class="form-group">
                    <div class="form-control d-flex align-items-center justify-content-between">
                        <label class="mb-0"
                            for="enable_popular_service">{{ __('messages.enable_popular_services') }}</label>
                        <div
                            class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                            <input type="checkbox" class="custom-control-input" name="enable_popular_service"
                                id="enable_popular_service"
                                {{ !empty($landing_page_data->enable_popular_service) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="enable_popular_service"></label>
                        </div>
                    </div>
                </div>
                @php
                    $selectedServiceIds = [];
                    $serviceNames = [];

                    if ($landing_page_data) {
                        $decodedData = json_decode($landing_page_data->value, true);
                        $selectedServiceIds = isset($decodedData['service_id']) ? $decodedData['service_id'] : [];
                        if ($selectedServiceIds) {
                            $serviceNames = \App\Models\Service::whereIn('id', $selectedServiceIds)
                                ->pluck('name', 'id')
                                ->toArray();
                        }
                    }
                @endphp
                <div class="form-group col-md-12" id='enable_select_service'>
                    {{ html()->label(__('messages.select_name', ['select' => __('messages.service')]) . ' <span class="text-danger">*</span>', 'service_id[]')->class('form-control-label') }}
                    <br />
                    {{ html()->select('service_id[]', $serviceNames, old('service_id', $selectedServiceIds))->class('select2js form-control service_id')->id('service_id')->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.service')]))->attribute('data-ajax--url', route('ajax-list', ['type' => 'service']))->multiple() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end') }}
{{ html()->form()->close() }}

<script>
    var enable_footer_setting = $("input[name='status']").prop('checked');
    checkSection1(enable_footer_setting);

    $('#footer_setting').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkSection1(value);
    });

    function checkSection1(value) {
        if (value == true) {
            $('#enable_footer_setting').removeClass('d-none');
        } else {
            $('#enable_footer_setting').addClass('d-none');
        }
    }

    var enable_popular_category = $("input[name='enable_popular_category']").prop('checked');
    checkEnableService(enable_popular_category);

    $('#enable_popular_category').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkEnableService(value);
    });

    function checkEnableService(value) {
        if (value == true) {
            $('#enable_select_category').removeClass('d-none');
            $('#category_id').prop('required', true);
        } else {
            $('#enable_select_category').addClass('d-none');
            $('#category_id').prop('required', false);
        }
    }


    ///// open select popular provider ///////////

    var enable_popular_service = $("input[name='enable_popular_service']").prop('checked');
    checkEnableProvider(enable_popular_service);

    $('#enable_popular_service').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkEnableProvider(value);
    });

    function checkEnableProvider(value) {
        if (value == true) {
            $('#enable_select_service').removeClass('d-none');
            $('#service_id').prop('required', true);
        } else {
            $('#enable_select_service').addClass('d-none');
            $('#service_id').prop('required', false);
        }
    }

    $(document).ready(function() {
        $('.select2js').select2();
        $('#category_id').on('change', function() {

            var selectedOptions = $(this).val();
            if (selectedOptions && selectedOptions.length > 6) {
                selectedOptions.pop();
                $(this).val(selectedOptions).trigger('change.select2');
            }
        });
        $('#service_id').on('change', function() {

            var selectedOptions = $(this).val();
            if (selectedOptions && selectedOptions.length > 6) {
                selectedOptions.pop();
                $(this).val(selectedOptions).trigger('change.select2');
            }
        });

    });
</script>
