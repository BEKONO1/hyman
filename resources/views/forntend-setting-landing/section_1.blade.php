{{ html()->form('POST', route('landing_page_settings_updates'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}



{{ html()->hidden('id',$landing_page->id)->placeholder('id')->class('form-control') }}
{{ html()->hidden('type', $tabpage)->placeholder('id')->class('form-control') }}

        <div class="form-group">
            <div class="form-control d-flex align-items-center justify-content-between">
                <label class="mb-0" for="enable_section_1">{{__('messages.home_slidar')}}</label>
                <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <input type="checkbox" class="custom-control-input section_1" name="status" id="section_1" data-type="section_1"  {{!empty($landing_page) && $landing_page->status == 1 ? 'checked' : ''}}>
                    <label class="custom-control-label" for="section_1"></label>
                </div>
            </div>
        </div>
        <div class="row" id='enable_section_1'>
            <div class="col-md-12">
                <div class="form-group">
                    {{ html()->label(trans('messages.title') . ' <span class="text-danger">*</span>', 'title')->class('form-control-label') }}
                    {{ html()->text('title', old('title'))->id('title')->placeholder(trans('messages.title'))->class('form-control') }}
                    <small class="help-block with-errors text-danger"></small>
                </div>
            </div>
            <div class="form-group col-md-12">
                {{ html()->label(trans('messages.description'), 'description')->class('form-control-label') }}
                {{ html()->textarea('description')->id('description')->placeholder(trans('messages.description'))->class('form-control textarea')->rows(2) }}
                <small class="help-block with-errors text-danger"></small>
            </div>
            <div class="form-group">
                <div class="form-control d-flex align-items-center justify-content-between">
                    <label for="enable_current_location" class="mb-0">{{__('messages.enable_current_location')}}</label>
                    <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="current_location" id="current_location">
                        <label class="custom-control-label" for="current_location"></label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="form-control d-flex align-items-center justify-content-between">
                    <label for="enable_search" class="mb-0">{{__('messages.enable_search')}}</label>
                    <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="enable_search" id="enable_search">
                        <label class="custom-control-label" for="enable_search"></label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="form-control d-flex align-items-center justify-content-between">
                    <label for="enable_popular_services" class="mb-0">{{__('messages.enable_popular_category')}}</label>
                    <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="enable_popular_services" id="enable_popular_services">
                        <label class="custom-control-label" for="enable_popular_services"></label>
                    </div>
                </div>
            </div>
            <div class="form-group" id='enable_select_category'>
                {{ html()->label(__('messages.select_name', ['select' => __('messages.category')]) . ' <span class="text-danger">*</span>', 'name')->class('form-control-label') }}
                <br />
                {{ html()->select('category_id[]',[],old('category_id'))
                    ->class('select2js form-control category_id')
                    ->id('category_id')
                    ->multiple()
                    ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.category')]))
                    ->attribute('data-ajax--url', route('ajax-list', ['type' => 'category']))
                }}
            </div>
            <div class="form-group">
                <div class="form-control d-flex align-items-center justify-content-between">
                    <label for="enable_popular_provider" class="mb-0">{{__('messages.enable_popular_provider')}}</label>
                    <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="enable_popular_provider" id="enable_popular_provider">
                        <label class="custom-control-label" for="enable_popular_provider"></label>
                    </div>
                </div>
            </div>
            <div class="form-group" id='enable_select_provider'>
                {{ html()->label(__('messages.select_name', ['select' => __('messages.provider')]) . ' <span class="text-danger">*</span>', 'name')->class('form-control-label') }}
                <br />
                {{ html()->select('provider_id[]', [],null)
                    ->class('select2js form-control provider_id')
                    ->id('provider_id')
                    ->multiple()
                    ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.provider')]))
                    ->attribute('data-ajax--url', route('ajax-list', ['type' => 'provider']))
                }}
                        </div>
                    </div>
                {{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end submit_section1') }}
                {{ html()->form()->close() }}

<script>
    var enable_section_1 = $("input[name='status']").prop('checked');
    checkSection1(enable_section_1);

    $('#section_1').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkSection1(value);
    });

    function checkSection1(value) {
        if (value == true) {
            $('#enable_section_1').removeClass('d-none');
            $('#title').prop('required', true);
        } else {
            $('#enable_section_1').addClass('d-none');
            $('#title').prop('required', false);
        }
    }

    ///// open select popular category ///////////

    var enable_popular_services = $("input[name='enable_popular_services']").prop('checked');
    checkEnableService(enable_popular_services);

    $('#enable_popular_services').change(function() {
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

     var enable_popular_provider = $("input[name='enable_popular_provider']").prop('checked');
    checkEnableProvider(enable_popular_provider);

    $('#enable_popular_provider').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkEnableProvider(value);
    });

    function checkEnableProvider(value) {
        if (value == true) {
            $('#enable_select_provider').removeClass('d-none');
            $('#provider_id').prop('required', true);
        } else {
            $('#enable_select_provider').addClass('d-none');
            $('#provider_id').prop('required', false);
        }
    }

    $(document).ready(function() {
        $('.select2js').select2();

        $('#category_id').on('change', function() {
            var selectedOptions = $(this).val();

            if (selectedOptions && selectedOptions.length > 4) {
                // Remove the last selected item
                selectedOptions.pop();
                $(this).val(selectedOptions).trigger('change.select2');
                
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'bottom',
                    iconColor: '#fff',
                    customClass: {
                        popup: 'colored-toast'
                    },
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: false
                });
                Toast.fire({
                    icon: 'error',
                    title: 'Maximum of 4 Category allowed to select. Please deselect one to add another.'
                });
            }
        });

        $('#provider_id').on('change', function() {
            var selectedOptions = $(this).val();

            if (selectedOptions && selectedOptions.length > 4) {
                // Remove the last selected item
                selectedOptions.pop();
                $(this).val(selectedOptions).trigger('change.select2');
                
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'bottom',
                    iconColor: '#fff',
                    customClass: {
                        popup: 'colored-toast'
                    },
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: false
                });
                Toast.fire({
                    icon: 'error',
                    title: 'Maximum of 4 Provider allowed to select. Please deselect one to add another.'
                });
            }
        });
    });

    // Add styles for toast
    var style = document.createElement('style');
    style.textContent = `
        .colored-toast.swal2-icon-error {
            background-color: #dc3545 !important;
        }
        .colored-toast {
            color: white !important;
        }
        .colored-toast .swal2-title {
            color: white !important;
            font-size: 15px !important;
            font-weight: 500 !important;
            padding: 5px 15px !important;
        }
        .colored-toast .swal2-icon {
            margin: 5px !important;
            width: 20px !important;
            height: 20px !important;
        }
        .colored-toast .swal2-icon.swal2-error [class^=swal2-x-mark-line] {
            background-color: #fff !important;
            height: 0.15em !important;
        }
    `;
    document.head.appendChild(style);

    var get_value = $('input[name="status"]:checked').data("type");
    getConfig(get_value)
    $('.section_1').change(function(){
        value = $(this).prop('checked') == true ? true : false;
        type = $(this).data("type");
        getConfig(type)

    });

    var categorySelect = $('#category_id');
    var providerSelect = $('#provider_id');
    function getConfig(type) {
        var _token = $('meta[name="csrf-token"]').attr('content');
        var page = "{{$tabpage}}";
        var getDataRoute = "{{ route('getLandingLayoutPageConfig') }}";
        $.ajax({
            url: getDataRoute,
            type: "POST",
            data: {
                type: type,
                page: page,
                _token: _token
            },
            success: function (response) {
                var obj = '';
                var section_1 = title = description = current_location = enable_search = enable_post_job = enable_popular_services = category_ids = enable_popular_provider = provider_id = '';

                if (response) {
                    if (response.data.key == 'section_1') {
                        obj = JSON.parse(response.data.value);
                    }
                    if (obj !== null) {
                        var title = obj.title;
                        var description = obj.description;
                        var current_location = obj.current_location;
                        var enable_search = obj.enable_search;
                        var enable_post_job = obj.enable_post_job;
                        var enable_popular_services = obj.enable_popular_services;
                        var category_ids = obj.category_id;
                        var enable_popular_provider = obj.enable_popular_provider;
                        var provider_ids = obj.provider_id;
                    }
                    $('#title').val(title)
                    $('#description').val(description)
                    $('#current_location').prop('checked', current_location == 'on');
                    $('#enable_search').prop('checked', enable_search == 'on');
                    $('#enable_post_job').prop('checked', enable_post_job == 'on');
                    var enable_popular_services = obj.enable_popular_services;
                    $('#enable_popular_services').prop('checked', enable_popular_services == 'on');
                    $('#enable_popular_services').change();
                    $('#enable_popular_provider').prop('checked',enable_popular_provider == 'on');
                    $('#enable_popular_provider').change();
                    if (category_ids && category_ids.length > 0) {
                            loadCategory(category_ids);
                        }
                    if (provider_ids && provider_ids.length > 0) {
                            loadProvider(provider_ids);
                        }
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    function loadCategory(category_ids) {
        var categorySelect = $('#category_id');
        var category_route = "{{ route('ajax-list', ['type' => 'category']) }}";
        if (category_ids && category_ids.length > 0) {
            $.ajax({
                url: category_route,
                data: { ids: category_ids },
                success: function (result) {
                    categorySelect.select2({
                        width: '100%',
                        placeholder: "{{ trans('messages.select_name',['select' => trans('messages.category')]) }}",
                        data: result.results
                    });
                    categorySelect.val(category_ids).trigger('change');
                }
            });
        } else {
            categorySelect.empty().trigger('change');
        }
    }

    function loadProvider(provider_ids) {
        var providerSelect = $('#provider_id');
        var provider_route = "{{ route('ajax-list', ['type' => 'provider']) }}";
            $.ajax({
                url: provider_route,
                data: { ids: provider_ids },
                success: function (result) {
                    providerSelect.select2({
                        width: '100%',
                        placeholder: "{{ trans('messages.select_name',['select' => trans('messages.provider')]) }}",
                        data: result.results
                    });
                    providerSelect.val(provider_ids).trigger('change');
                }
            });
        
    }



</script>
