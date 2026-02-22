{{ html()->form('POST', route('sitesetup'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}

{{ html()->hidden('id',$site->id ?? null)->attribute('placeholder', 'id')->class('form-control') }}
{{ html()->hidden('page', $page)->attribute('placeholder', 'id')->class('form-control') }}

<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label for="date_format" class="col-sm-6 form-control-label">{{ __('messages.date_format') }}</label>
            <div class="col-sm-12">
                <select class="form-select select2js date_format" name="date_format" id="date_format">
                @foreach(dateFormatList() as $formatCode => $format)
                @if(isset($site->date_format) && $site->date_format == $formatCode)
                    <option value="{{ $formatCode }}" selected="">{{ $format }}</option>
                @else
                        <option value="{{ $formatCode }}">{{ $format }}</option>

                @endif
                @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="time_format" class="col-sm-6 form-control-label">{{ __('messages.time_formate') }}</label>
            <div class="col-sm-12">
                <select class="form-select select2js time_format" name="time_format" id="time_format">

                @foreach(timeFormatList() as $timeFormat)
                @if(isset($site->time_format) && $site->time_format == $timeFormat['format'])
                    <option value="{{ $timeFormat['format'] }}" selected="">{{ $timeFormat['time'] }}</option>
                @else
                    <option value="{{ $timeFormat['format'] }}">{{ $timeFormat['time'] }}</option>
                @endif
                @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="time_zone" class="col-sm-6 form-control-label">{{ __('messages.timezone') }}</label>
            <div class="col-sm-12">
            <select class="form-select select2js" name="time_zone" id="time_zone"
                    data-ajax--url="{{ route('ajax-list', ['type' => 'time_zone']) }}"
                    data-ajax--cache="true">
                @if(isset($site->time_zone))
                    <option value="{{ $site->time_zone }}" selected="">{{ timeZoneList()[$site->time_zone] }}</option>
                @else
                    {{-- Display the first option when $site->time_zone is not set --}}
                    @foreach(timeZoneList() as $timeZoneId => $timeZoneName)
                        <option value="{{ $timeZoneId }}">{{ $timeZoneName }}</option>
                        {{-- Break after the first iteration to display only the first option --}}
                        @break
                    @endforeach
                @endif
            </select>
            </div>
        </div>



        @php
            $languages = [];
            $languagesname = [];
            if ($site) {
                $decodedData = json_decode($site->value, true);
                $languages = isset($decodedData['language_option']) ? $decodedData['language_option'] : [];
            }
        @endphp
        <div class="form-group">
            <label for="language_option" class="col-sm-12 form-control-label">{{ __('messages.default_language') }}</label>
            <div class="col-sm-12">
                <select class="form-select select2js language_option" name="language_option[]" id="language_option" multiple required>
                    @foreach(languagesArray() as $language)
                        @if(in_array($language['id'], $languages))
                            <option value="{{ $language['id'] }}" selected>{{ $language['title'] }}</option>
                        @else
                            <option value="{{ $language['id'] }}">{{ $language['title'] }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>


        <div class="form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.default_currency') }}</label>
            <div class="col-sm-12">
                {{ html()->select('default_currency',[],  old('default_currency'))
                    ->class('select2js form-group country')
                    ->id('default_currency')
                    ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.currency')]))
                }}
            </div>
        </div>

        <div class = "form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.currency_position') }}</label>
            <div class="col-sm-12">
                {{ html()->select('currency_position', ['left' => __('messages.left'), 'right' => __('messages.right')], isset($site->currency_position) ? $site->currency_position : 'left')->class('form-select select2js')}}            
                </div>
        </div>
        @hasanyrole('admin')
        <div class="form-group">
            {{ html()->label(trans('messages.google_map_keys'),'google_map_keys')->class('col-sm-6 form-control-label') }}
            <div class="col-sm-12">
                {{ html()->text('google_map_keys',$site->google_map_keys )->id('google_map_keys')->class('form-control')->placeholder(trans('messages.google_map_keys'))}}
            </div>
            <small class="help-block with-errors text-danger"></small>
        </div>
        @endhasanyrole
        <div class="form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.latitude') }}</label>
            <div class="col-sm-12">
                {{ html()->text('latitude',$site->latitude)->class('form-control')->placeholder(trans('messages.latitude'))->id('latitude')}}
            </div>
        </div>

    </div>
    <div class="col-lg-6">

        <div class="form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.longitude') }}</label>
            <div class="col-sm-12">
                {{ html()->text('longitude', $site->longitude)->class('form-control')->placeholder(trans('messages.longitude'))->id('longitude')}}
            </div>
        </div>

        <div class = "form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.distance_type') }}</label>
            <div class="col-sm-12">
                {{ html()->select('distance_type', ['km' => __('messages.km'), 'mile' => __('messages.mile')],  isset($site->distance_type) ? $site->distance_type : 'km')->class('form-select select2js')}}
            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.radious') }}</label>
            <div class="col-sm-12">
                {{ html()->number('radious',$site->radious)->class('form-control')->placeholder('50')->id('radious')}}
            </div>
        </div>
    <div class="form-group">
    <label for="digitafter_decimal_point" class="col-sm-6 form-control-label">{{ __('messages.digitafter_decimal_point') }}</label>
    <div class="col-sm-12">
        {{ html()->number('digitafter_decimal_point', $site->digitafter_decimal_point)
            ->class('form-control')
            ->placeholder('2')
            ->id('digitafter_decimal_point')
            ->attribute('min', 2)
            ->attribute('max', 6)
            ->attribute('step', 1)
            ->required()
        }}
        <small id="digitafter_decimal_point_error" class="text-danger d-block mt-1"></small>
    </div>
</div>

        <div class="form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.copyright_text') }}</label>
            <div class="col-sm-12">
                {{ html()->text('site_copyright',  $site->site_copyright)->class('form-control')->placeholder(__('messages.copyright_text'))}}
            </div>
        </div>

        <div class="form-group col-md-12 d-flex justify-content-between">
            <label for="android_app_links" class="mb-0">{{ __('messages.android_app_links') }}</label>
            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                <input type="checkbox" class="custom-control-input" name="android_app_links" id="android_app_links" {{ !empty($site->android_app_links) ? 'checked' : '' }}>
                <label class="custom-control-label" for="android_app_links"></label>
            </div>
        </div>


        <div class="form-padding-box mb-3" id='android_app'>
            <div class="row">
                <div class="form-group col-sm-6 mb-0">
                    {{ html()->label(trans('messages.playstore_url'), 'playstore_url')->class('form-control-label') }}
                    {{ html()->text('playstore_url',$site->playstore_url)->class('form-control')->placeholder(trans('messages.playstore_url'))->id('playstore_url')}}
                    <small class="help-block with-errors text-danger"></small>
                </div>
                <div class="form-group col-sm-6 mt-sm-0 mt-3 mb-0">
                    {{ html()->label(trans('messages.provider_playstore_url'), 'provider_playstore_url')->class('form-control-label') }}
                    {{ html()->text('provider_playstore_url', $site->provider_playstore_url)->class('form-control')->placeholder(trans('messages.provider_playstore_url'))->id('provider_playstore_url')}}
                    <small class="help-block with-errors text-danger"></small>
                </div>
            </div>
        </div>

        <div class="form-group col-md-12 d-flex justify-content-between">
            <label for="ios_app_links" class="mb-0">{{ __('messages.ios_app_links') }}</label>
            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                <input type="checkbox" class="custom-control-input" name="ios_app_links" id="ios_app_links" {{ !empty($site->ios_app_links) ? 'checked' : '' }}>
                <label class="custom-control-label" for="ios_app_links"></label>
            </div>
        </div>


        <div class="form-padding-box mb-3" id='ios_app'>
            <div class="row">
                <div class="form-group col-sm-6 mb-0">
                    {{ html()->label(trans('messages.appstore_url'), 'appstore_url')->class('form-control-label') }}
                    {{ html()->text('appstore_url', $site->appstore_url)->class('form-control')->placeholder(trans('messages.appstore_url'))->id('appstore_url')}}
                    <small class="help-block with-errors text-danger"></small>
                </div>
                <div class="form-group col-sm-6 mt-sm-0 mt-3 mb-0">
                    {{ html()->label(trans('messages.provider_appstore_url'), 'provider_appstore_url')->class('form-control-label') }}
                    {{ html()->text('provider_appstore_url', $site->provider_appstore_url)->class('form-control')->placeholder(trans('messages.provider_appstore_url'))->id('provider_appstore_url')}}
                    <small class="help-block with-errors text-danger"></small>
                </div>
            </div>
        </div>




    </div>
     <div class="col-lg-12">
        <div class="form-group">
            <div class="col-md-offset-3 col-sm-12 ">
                {{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end submit_section1') }}
            </div>
        </div>
    </div>
</div>
{{ html()->form()->close() }}
<script>
    $(document).ready(function () {

        // 🔢 Validate 'digitafter_decimal_point' input: allow only 2–6
        $('#digitafter_decimal_point').on('input', function () {
            let val = parseInt(this.value);
            let errorBox = $('#digitafter_decimal_point_error');

            if (isNaN(val) || val < 2 || val > 6) {
                errorBox.text('Please enter a value between 2 and 6');
                this.value = ''; // Clear the input if invalid
            } else {
                errorBox.text('');
            }
        });

        // 🌍 Load currencies dynamically
        loadCurrency();

        // 🧠 Initialize Select2
        $('.select2js').select2();

        // 🌐 Prevent default language from being selected as option
        $('.default_language').on('change', function () {
            var selectedId = $(this).val();

            $('.language_option option:disabled').prop('selected', true);
            $('.language_option option').prop('disabled', false);

            $('.language_option option').each(function () {
                var $option = $(this);
                if (selectedId == $option.val()) {
                    $option.prop('disabled', true).prop('selected', false);
                }
            });

            $('.language_option').select2("destroy").select2();
        });

        // 🔒 Ensure English language is always selected in language_option
        const englishLangId = 'en';
        const selectElement = $('#language_option');

        selectElement.on('change', function () {
            let selectedOptions = $(this).val();

            if (!selectedOptions.includes(englishLangId)) {
                selectedOptions.push(englishLangId);
                $(this).val(selectedOptions).trigger('change');
            }
        });

        selectElement.select2();

        // 📱 Android App toggle
        let androidAppLinks = $("input[name='android_app_links']").prop('checked');
        androidAppLinksToggle(androidAppLinks);

        $('#android_app_links').change(function () {
            androidAppLinksToggle($(this).prop('checked'));
        });

        function androidAppLinksToggle(enabled) {
            if (enabled) {
                $('#android_app').removeClass('d-none');
                $("#provider_playstore_url, #playstore_url").prop("required", true);
            } else {
                $('#android_app').addClass('d-none');
                $("#provider_playstore_url, #playstore_url").prop("required", false);
            }
        }

        // 🍎 iOS App toggle
        let iosAppLinks = $("input[name='ios_app_links']").prop('checked');
        iosAppLinksToggle(iosAppLinks);

        $('#ios_app_links').change(function () {
            iosAppLinksToggle($(this).prop('checked'));
        });

        function iosAppLinksToggle(enabled) {
            if (enabled) {
                $('#ios_app').removeClass('d-none');
                $("#provider_appstore_url, #appstore_url").prop("required", true);
            } else {
                $('#ios_app').addClass('d-none');
                $("#provider_appstore_url, #appstore_url").prop("required", false);
            }
        }
    });

    // 📦 AJAX load currency list and set default
    function loadCurrency() {
        var currency = "{{ isset($site->default_currency) ? $site->default_currency : '' }}";
        var currency_route = "{{ route('ajax-list', ['type' => 'currency']) }}".replace('amp;', '');

        $.ajax({
            url: currency_route,
            success: function (result) {
                $('#default_currency').select2({
                    width: '100%',
                    placeholder: "{{ trans('messages.select_name', ['select' => trans('messages.currency')]) }}",
                    data: result.results
                });

                if (currency) {
                    $("#default_currency").val(currency).trigger('change');
                }
            }
        });
    }
</script>
