

{{ html()->form('POST', route('otherSetting'))
    ->attribute('enctype', 'multipart/form-data')
    ->attribute('data-toggle', 'validator')
    ->id('myForm')
    ->open() }}

{{ html()->hidden('id', $othersetting->id ?? null )
    ->class('form-control')
    ->placeholder('id') }}

{{ html()->hidden('type', $page)
    ->class('form-control')
    ->placeholder('id') }}
    <div class="form-group">
        <div class="form-control d-flex align-items-center justify-content-between">
            <label for="social_login" class="mb-0">{{ __('messages.enable_social_login') }}</label>
            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                <input type="checkbox" class="custom-control-input" name="social_login" id="social_login" {{ !empty($othersetting->social_login) ? 'checked' : '' }}>
                <label class="custom-control-label" for="social_login"></label>
            </div>
        </div>
    </div>

    <div class="form-padding-box mb-3" id='social_login_data'>
        <div class="form-group">
            <div class="form-control d-flex align-items-center justify-content-between">
                <label for="google_login" class="mb-0">{{ __('messages.enable_google_login') }}</label>
                <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <input type="checkbox" class="custom-control-input" name="google_login" id="google_login" {{ !empty($othersetting->google_login) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="google_login"></label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-control d-flex align-items-center justify-content-between">
                <label for="apple_login" class="mb-0">{{ __('messages.enable_apple_login') }}</label>
                <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <input type="checkbox" class="custom-control-input" name="apple_login" id="apple_login" {{ !empty($othersetting->apple_login) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="apple_login"></label>
                </div>
            </div>
        </div>
        <div class="form-group mb-0">
            <div class="form-control d-flex align-items-center justify-content-between">
                <label for="otp_login" class="mb-0">{{ __('messages.enable_otp_login') }}</label>
                <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <input type="checkbox" class="custom-control-input" name="otp_login" id="otp_login" {{ !empty($othersetting->otp_login) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="otp_login"></label>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Demo Login Section -->
    <div class="" id='demo_login_section'>
        <div class="form-group">
            <div class="form-control d-flex align-items-center justify-content-between">
                <label for="demo_login" class="mb-0">{{ __('messages.demo_login') }}</label>
                <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <input type="checkbox" class="custom-control-input" name="demo_login" id="demo_login" {{ !empty($othersetting->demo_login) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="demo_login"></label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="form-control d-flex align-items-center justify-content-between">
            <label for="online_payment" class="mb-0">{{ __('messages.enable_online_payment') }}</label>
            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                <input type="checkbox" class="custom-control-input" name="online_payment" id="online_payment" {{ !empty($othersetting->online_payment) ? 'checked' : '' }}>
                <label class="custom-control-label" for="online_payment"></label>
            </div>
        </div>
    </div>


    <div class="form-group">
        <div class="form-control d-flex align-items-center justify-content-between">
            <label for="blog" class="mb-0">{{ __('messages.enable_blog') }}</label>
            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                <input type="checkbox" class="custom-control-input" name="blog" id="blog" {{ !empty($othersetting->blog) ? 'checked' : '' }}>
                <label class="custom-control-label" for="blog"></label>
            </div>
        </div>
    </div>


    <div class="form-group">
        <div class="form-control d-flex align-items-center justify-content-between">
            <label for="enable_chat_gpt" class="mb-0">{{ __('messages.enable_chat_gpt') }}</label>
            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                <input type="checkbox" class="custom-control-input" name="enable_chat_gpt" id="enable_chat_gpt" {{ !empty($othersetting->enable_chat_gpt) ? 'checked' : '' }}>
                <label class="custom-control-label" for="enable_chat_gpt"></label>
            </div>
        </div>
    </div>

<div class="form-padding-box mb-3" id='chat_gpt_key_section'>
    <div class="row">
        <div class="form-group col-md-12 mb-0">
            <div class="form-control d-flex align-items-center justify-content-between">
                <label for="test_without_key" class="mb-0">{{ __('messages.test_without_key') }}</label>
                <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <input type="checkbox" class="custom-control-input" name="test_without_key" id="test_without_key" {{ !empty($othersetting->test_without_key) ? 'checked' : 'checked' }}>
                    <label class="custom-control-label" for="test_without_key"></label>
                </div>
            </div>
        </div>
        <div class="form-group col-sm-6 mb-0" id='key'>

                {{ html()->label(__('messages.key') . ' ')
                ->class('form-control-label') }}

                {{ html()->text('chat_gpt_key', $othersetting->chat_gpt_key)
                ->class('form-control')
                ->id('chat_gpt_key')
                ->placeholder(__('messages.key')) }}
            <small class="help-block with-errors text-danger"></small>
        </div>

    </div>
</div>

@hasanyrole('admin')


    <div class="form-group">
        <div class="form-control d-flex align-items-center justify-content-between">
            <label for="maintenance_mode" class="mb-0">{{ __('messages.enable_maintenance_mode') }}</label>
            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                <input type="checkbox" class="custom-control-input" name="maintenance_mode" id="maintenance_mode" {{ !empty($othersetting->maintenance_mode) ? 'checked' : '' }}>
                <label class="custom-control-label" for="maintenance_mode"></label>
            </div>
        </div>
    </div>

@endhasanyrole


    <div class="form-group">
        <div class="form-control d-flex align-items-center justify-content-between">
            <label for="wallet">{{ __('messages.enable_user_wallet') }}</label>
            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                <input type="checkbox" class="custom-control-input" name="wallet" id="wallet" {{ !empty($othersetting->wallet) ? 'checked' : '' }}>
                <label class="custom-control-label" for="wallet"></label>
            </div>
        </div>
    </div>


    <div class="form-group">
        <div class="form-control d-flex align-items-center justify-content-between">
            <label for="force_update_user_app" class="mb-0">{{ __('messages.enable_user_app_force_update') }}</label>
            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                <input type="checkbox" class="custom-control-input" name="force_update_user_app" id="force_update_user_app" {{ !empty($othersetting->force_update_user_app) ? 'checked' : '' }}>
                <label class="custom-control-label" for="force_update_user_app"></label>
            </div>
        </div>
    </div>


<div class="form-padding-box mb-3" id='user_verson_code'>
    <div class="row">
        <div class="form-group col-sm-6 mb-0">
            {{ html()->label(__('messages.user_app_minimum_version') . ' ')
                ->class('form-control-label') }}

            {{ html()->number('user_app_minimum_version', $othersetting->provider_app_minimum_version)
                ->id('user_app_minimum_version')
                ->placeholder('1')
                ->class('form-control') }}
            <small class="help-block with-errors text-danger"></small>
        </div>
        <div class="form-group col-sm-6 mt-sm-0 mt-3 mb-0">
            {{ html()->label(__('messages.user_app_latest_version') . ' ')
                ->class('form-control-label') }}

            {{ html()->number('user_app_latest_version', $othersetting->provider_app_minimum_version)
                ->id('user_app_latest_version')
                ->placeholder('2')
                ->class('form-control') }}
            <small class="help-block with-errors text-danger"></small>
        </div>
    </div>
</div>

    <div class="form-group">
        <div class="form-control d-flex align-items-center justify-content-between">
            <label for="force_update_provider_app" class="mb-0">{{ __('messages.enable_provider_app_force_update') }}</label>
            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                <input type="checkbox" class="custom-control-input" name="force_update_provider_app" id="force_update_provider_app" {{ !empty($othersetting->force_update_provider_app) ? 'checked' : '' }}>
                <label class="custom-control-label" for="force_update_provider_app"></label>
            </div>
        </div>
    </div>

<div class="form-padding-box mb-3" id='provider_verson_code'>
    <div class="row">
        <div class="form-group col-sm-6 mb-0">
            {{ html()->label(trans('messages.provider_app_minimum_version') . ' ', 'provider_app_minimum_version', ['class' => 'form-control-label']) }}
            {{ html()->number('provider_app_minimum_version',  $othersetting->provider_app_minimum_version)
                ->class('form-control')
                ->id('provider_app_minimum_version')
                ->placeholder('1')
            }}
            <small class="help-block with-errors text-danger"></small>
        </div>
        <div class="form-group col-sm-6 mt-sm-0 mt-3 mb-0">
            {{ html()->label(trans('messages.provider_app_latest_version') . ' ', 'provider_app_latest_version', ['class' => 'form-control-label']) }}
            {{ html()->number('provider_app_latest_version',  $othersetting->provider_app_latest_version)
                ->class('form-control')
                ->id('provider_app_latest_version')
                ->placeholder('2')
            }}
            <small class="help-block with-errors text-danger"></small>
        </div>
    </div>
</div>

    <div class="form-group">
        <div class="form-control d-flex align-items-center justify-content-between">
            <label for="force_update_admin_app" class="mb-0">{{ __('messages.enable_admin_app_force_update') }}</label>
            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                <input type="checkbox" class="custom-control-input" name="force_update_admin_app" id="force_update_admin_app" {{ !empty($othersetting->force_update_admin_app) ? 'checked' : '' }}>
                <label class="custom-control-label" for="force_update_admin_app"></label>
            </div>
        </div>
    </div>

<div class="form-padding-box mb-3" id='admin_verson_code'>
    <div class="row">
        <div class="form-group col-sm-6 mb-0">
            {{ html()->label(trans('messages.admin_app_minimum_version') . ' ', 'admin_app_minimum_version', ['class' => 'form-control-label']) }}
            {{ html()->number('admin_app_minimum_version',  $othersetting->admin_app_minimum_version)
                ->class('form-control')
                ->id('admin_app_minimum_version')
                ->placeholder('1')
            }}
            <small class="help-block with-errors text-danger"></small>
        </div>
        <div class="form-group col-sm-6 mt-sm-0 mt-3 mb-0">
            {{ html()->label(trans('messages.admin_app_latest_version') . ' ', 'admin_app_latest_version', ['class' => 'form-control-label']) }}
            {{ html()->number('admin_app_latest_version',  $othersetting->admin_app_latest_version)
                ->class('form-control')
                ->id('admin_app_latest_version')
                ->placeholder('2')
            }}
            <small class="help-block with-errors text-danger"></small>
        </div>
    </div>
</div>


<div class="form-group">
    <div class="form-control d-flex align-items-center justify-content-between">
        <label for="is_in_app_purchase_enable" class="mb-0">{{ __('messages.enable_in_app_purchase') }}</label>
        <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
            <input type="checkbox" class="custom-control-input" name="is_in_app_purchase_enable" id="is_in_app_purchase_enable" {{ !empty($othersetting->is_in_app_purchase_enable) ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_in_app_purchase_enable"></label>
        </div>
    </div>
</div>

<div class="form-padding-box mb-3 d-none" id="in_app_purchase_fields">
    <div class="row">
        <div class="form-group col-sm-6 mb-0">
            {{ html()->label(trans('messages.entitlement_id') . ' ', 'entitlement_id', ['class' => 'form-control-label']) }}

            <a href="https://www.revenuecat.com/docs/getting-started/entitlements#creating-an-entitlement" target="_blank"
            class="ml-2" data-toggle="tooltip" data-placement="top" title="Learn more about creating entitlements">
                <i class="fas fa-info-circle"></i>
            </a>
            {{ html()->text('entitlement_id', $othersetting->entitlement_id ?? '')
                ->class('form-control')
                ->id('entitlement_id')
                ->placeholder('Enter Entitlement ID')
            }}
            <small class="help-block with-errors text-danger"></small>
        </div>
        <div class="form-group col-sm-6 mb-0">
            {{ html()->label(trans('messages.google_public_api_key') . ' ', 'google_public_api_key', ['class' => 'form-control-label']) }}
            <a href="https://www.revenuecat.com/docs/welcome/authentication#obtaining-api-keys" target="_blank"
            class="ml-2" data-toggle="tooltip" data-placement="top" title="Learn more about creating google public api key">
                <i class="fas fa-info-circle"></i>
            </a>
            {{ html()->text('google_public_api_key', $othersetting->google_public_api_key ?? '')
                ->class('form-control')
                ->id('google_public_api_key')
                ->placeholder('Enter Google API Key')
            }}
            <small class="help-block with-errors text-danger"></small>
        </div>
        <div class="form-group col-sm-6 mt-3 mb-0">
            {{ html()->label(trans('messages.apple_public_api_key') . ' ', 'apple_public_api_key', ['class' => 'form-control-label']) }}
            <a href="https://www.revenuecat.com/docs/welcome/authentication#obtaining-api-keys" target="_blank"
            class="ml-2" data-toggle="tooltip" data-placement="top" title="Learn more about creating apple public api key">
                <i class="fas fa-info-circle"></i>
            </a>
            {{ html()->text('apple_public_api_key', $othersetting->apple_public_api_key ?? '')
                ->class('form-control')
                ->id('apple_public_api_key')
                ->placeholder('Enter Apple API Key')
            }}
            <small class="help-block with-errors text-danger"></small>
        </div>
    </div>
</div>


    <div class="form-group">
        <div class="form-control d-flex align-items-center justify-content-between">
            <label for="firebase_notification" class="mb-0">{{ __('messages.firebase_notification') }}</label>
            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                <input type="checkbox" class="custom-control-input" name="firebase_notification" id="firebase_notification" {{ !empty($othersetting->firebase_notification) ? 'checked' : '' }}>
                <label class="custom-control-label" for="firebase_notification"></label>
            </div>
        </div>
    </div>

<div class="form-padding-box mb-3" id='firebase_notification_key'>
    <div class="row">
        <div class="form-group col-sm-6 mb-0">

            {{ html()->label(trans('messages.firebase_project_id') . ' ', 'project_id', ['class' => 'form-control-label']) }}
            {{ html()->text('project_id', $othersetting->project_id)
                ->class('form-control')
                ->id('project_id')
                ->placeholder(__('messages.firebase_project_id'))
            }}
            <small class="help-block with-errors text-danger"></small>
        </div>
        <div class="form-group col-sm-6 mb-0 ">
            <label for="json_file" class="form-control-label">

                {{ trans('messages.json_file') }} <span class="ml-3"><a class="text-primary" href="https://console.firebase.google.com/">Download JSON File</a></span>
            </label>
            {{-- {{ Form::label('json_file',trans('messages.json_file').' ',['class'=>'form-control-label'], false ) }} --}}
            <div class="custom-file">
                {{ html()->file('json_file')->class('custom-file-input')->id('json_file')->attribute('accept', '.json')->attribute('aria-describedby', 'additionalFileHelp')}}
                <label id="additionalFileHelp" class="custom-file-label upload-label border-0">Upload Firebase JSON files only Once.</label>
                <small class="help-block with-errors text-danger"></small>
            </div>
        </div>
    </div>
</div>


    <div class="form-group">
        <div class="form-control d-flex align-items-center justify-content-between">
            <label for="auto_assign_provider" class="mb-0">{{ __('messages.enable_auto_assign') }}</label>
            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                <input type="checkbox" class="custom-control-input" name="auto_assign_provider" id="auto_assign_provider" {{ !empty($othersetting->auto_assign_provider) ? 'checked' : '' }}>
                <label class="custom-control-label" for="auto_assign_provider"></label>
            </div>
        </div>
    </div>

<!-- Enable Chat Section -->
    <div class="" id='chat_section'>
        <div class="form-group">
            <div class="form-control d-flex align-items-center justify-content-between">
                <label for="enable_chat" class="mb-0">{{ __('messages.enable_chat') }}</label>
                <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <input type="checkbox" class="custom-control-input" name="enable_chat" id="enable_chat" {{ !empty($othersetting->enable_chat) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="enable_chat"></label>
                </div>
            </div>
        </div>
        <!-- <div class="form-group d-none" id="chat_api_key_section">
            {{ html()->label(__('messages.chat_api_key') . ' ')
                ->class('form-control-label') }}
            {{ html()->text('chat_api_key', $othersetting->chat_api_key ?? '')
                ->class('form-control')
                ->id('chat_api_key')
                ->placeholder(__('messages.chat_api_key')) }}
            <small class="help-block with-errors text-danger"></small>
        </div> -->
    </div>

    <!-- WhatsApp Notification -->
<div class="form-group col-md-12 d-flex justify-content-between">
    <label for="whatsapp_notification" class="mb-0">{{ __('messages.whatsapp_notificaion') }}</label>
    <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
        <input type="checkbox" class="custom-control-input" name="whatsapp_notification" id="whatsapp_notification" {{ !empty($othersetting->whatsapp_notification) && $othersetting->whatsapp_notification == 1 ? 'checked' : '' }}>
        <label class="custom-control-label" for="whatsapp_notification"></label>
    </div>
</div>
<div class="form-padding-box mb-3" class="{{ empty($othersetting->whatsapp_notification) || $othersetting->whatsapp_notification != 1 ? 'd-none' : '' }}" id="whatsapp-settings">
     <!-- WhatsApp Configuration -->
    <div class="row">
        <div class="form-group col-sm-6 mb-0">
            <label for="twilio_sid_whatsapp" class="col-sm-6 form-control-label">{{ __('messages.twilio_sid') }} (WhatsApp)</label>
            {{ html()->text('twilio_sid_whatsapp', $othersetting->twilio_sid_whatsapp ?? '')->class('form-control')->id('twilio_sid_whatsapp')->placeholder(__('messages.twilio_sid')) }}
        </div>


        <div class="form-group col-sm-6 mb-0">
            <label for="twilio_auth_token_whatsapp" class="col-sm-6 form-control-label">{{ __('messages.twilio_auth_token') }} (WhatsApp)</label>
            {{ html()->text('twilio_auth_token_whatsapp', $othersetting->twilio_auth_token_whatsapp ?? '')->class('form-control')->id('twilio_auth_token_whatsapp')->placeholder(__('messages.twilio_auth_token')) }}
        </div>

        <div class="form-group col-sm-6  mt-3 mb-0">
            <label for="twilio_whatsapp_number" class="col-sm-6 form-control-label">{{ __('messages.twilio_whatsapp_number') }} (WhatsApp)</label>
            {{ html()->number('twilio_whatsapp_number', $othersetting->twilio_whatsapp_number ?? '')->class('form-control')->id('twilio_whatsapp_number')->placeholder(__('messages.twilio_whatsapp_number')) }}
        </div>
    </div>
</div>

    <div class="form-group col-md-12 d-flex justify-content-between">
        <label for="sms_notification" class="mb-0">{{ __('messages.sms_notificaion') }}</label>
        <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
            <input type="checkbox" class="custom-control-input" name="sms_notification" id="sms_notification" {{ !empty($othersetting->sms_notification) && $othersetting->sms_notification == 1 ? 'checked' : '' }}>
            <label class="custom-control-label" for="sms_notification"></label>
        </div>
    </div>
    <div class="form-padding-box mb-3" id="sms-settings" class="{{ empty($othersetting->sms_notification) || $othersetting->sms_notification != 1 ? 'd-none' : '' }}">
        <!-- SMS Configuration -->
        <div class="row">
            <div class="form-group col-sm-6 mb-0">
               <label for="twilio_sid_sms" class="col-sm-6 form-control-label">{{ __('messages.twilio_sid') }} (SMS)</label>
                {{ html()->text('twilio_sid_sms', $othersetting->twilio_sid_sms ?? '')->class('form-control')->id('twilio_sid_sms')->placeholder(__('messages.twilio_sid')) }}
            </div>


            <div class="form-group col-sm-6 mb-0">
                <label for="twilio_auth_token_sms" class="col-sm-6 form-control-label">{{ __('messages.twilio_auth_token') }} (SMS)</label>
                {{ html()->text('twilio_auth_token_sms', $othersetting->twilio_auth_token_sms ?? '')->class('form-control')->id('twilio_auth_token_sms')->placeholder(__('messages.twilio_auth_token')) }}
            </div>


            <div class="form-group col-sm-6 mt-3 mb-0">
                <label for="twilio_phone_number_sms" class="col-sm-6 form-control-label">{{ __('messages.twilio_phone_number') }} (SMS)</label>
                {{ html()->number('twilio_phone_number_sms', $othersetting->twilio_phone_number_sms ?? '')->class('form-control')->id('twilio_phone_number_sms')->placeholder(__('messages.twilio_phone_number')) }}
            </div>
        </div>
    </div>
    </div>


<div class="row">
    <div class="form-group col-md-12 d-flex justify-content-between">
        <label for="dashboard_type" class="mb-0">{{ __('messages.set_dashboard') }}</label>
    </div>
</div>
<div class="form-padding-box mb-3">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5">
        <div class="text-center col">
            <input type="radio" class="btn-check" name="dashboard_type" id="dashboard" value="dashboard" {{ ($othersetting->dashboard_type ?? '') == 'dashboard' ? 'checked' : '' }}>
            <label class="btn btn-border d-block p-1" for="dashboard">
                <img class="img-fluid img-height" src="{{ asset('/images/dashboard/Dashboard.jpg') }}">
                <h5 class="py-2 mb-0">Default</h5>
            </label>
        </div>
        <div class="text-center col">
            <input type="radio" class="btn-check" name="dashboard_type" id="dashboard_1" value="dashboard_1" {{ ($othersetting->dashboard_type ?? '') == 'dashboard_1' ? 'checked' : '' }}>
            <label class="btn btn-border d-block p-1" for="dashboard_1">
                <img class="img-fluid img-height" src="{{ asset('/images/dashboard/Dashboard -1.jpg') }}">
                <h5 class="py-2 mb-0">Sleek Touch</h5>
            </label>
        </div>
        <div class="text-center col">
            <input type="radio" class="btn-check" name="dashboard_type" id="dashboard_2" value="dashboard_2" {{ ($othersetting->dashboard_type ?? '') == 'dashboard_2' ? 'checked' : '' }}>
            <label class="btn btn-border d-block p-1" for="dashboard_2">
                <img class="img-fluid img-height" src="{{ asset('/images/dashboard/Dashboard -2.jpg') }}">
                <h5 class="py-2 mb-0">Hasty Ease</h5>
            </label>
        </div>
        <div class="text-center col">
            <input type="radio" class="btn-check" name="dashboard_type" id="dashboard_3" value="dashboard_3" {{ ($othersetting->dashboard_type ?? '') == 'dashboard_3' ? 'checked' : '' }}>
            <label class="btn btn-border d-block p-1" for="dashboard_3">
                <img class="img-fluid img-height" src="{{ asset('/images/dashboard/Dashboard -3.jpg') }}">
                <h5 class="py-2 mb-0">Magic Touch</h5>
            </label>
        </div>
        <div class="text-center col">
            <input type="radio" class="btn-check" name="dashboard_type" id="dashboard_4" value="dashboard_4" {{ ($othersetting->dashboard_type ?? '') == 'dashboard_4' ? 'checked' : '' }}>
            <label class="btn btn-border d-block p-1" for="dashboard_4">
                <img class="img-fluid img-height" src="{{ asset('/images/dashboard/Dashboard -4.jpg') }}">
                <h5 class="py-2 mb-0">Whiz Fix</h5>
            </label>
        </div>
    </div>
</div>




{{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end submit_section1') }}
{{ html()->form()->close() }}

<script>
    var social_login = $("input[name='social_login']").prop('checked');

    checkOtherSettingOption(social_login);

    $('#social_login').change(function() {
        value = $(this).prop('checked');
        checkOtherSettingOption(value);
    });

    function checkOtherSettingOption(value) {
        if (value == true) {
            $('#social_login_data').removeClass('d-none');
        } else {
            $('#social_login_data').addClass('d-none');
        }
    }
    var enable_chat_gpt = $("input[name='enable_chat_gpt']").prop('checked');

    checkChatGPTSetting(enable_chat_gpt);
    $('#enable_chat_gpt').change(function() {
        value = $(this).prop('checked');
        checkChatGPTSetting(value);
    });

    function checkChatGPTSetting(value) {
        if (value == true) {
            $('#chat_gpt_key_section').removeClass('d-none');
        } else {
            $('#chat_gpt_key_section').addClass('d-none');
        }
    }

    var test_without_key = $("input[name='test_without_key']").prop('checked');

    testWithoutKey(test_without_key);
    $('#test_without_key').change(function() {
        value = $(this).prop('checked');
        testWithoutKey(value);
    });

    function testWithoutKey(value) {
        if (value == true) {
            $('#key').addClass('d-none');
            $("#chat_gpt_key").prop("required", false);
        } else {
            $('#key').removeClass('d-none');
            $("#chat_gpt_key").prop("required", true);
        }
    }


    var force_update_user_app = $("input[name='force_update_user_app']").prop('checked');

    checkForceUpdateSettingOption(force_update_user_app);

    $('#force_update_user_app').change(function() {
        value = $(this).prop('checked');
        checkForceUpdateSettingOption(value);
    });

    function checkForceUpdateSettingOption(value) {
        if (value == true) {
            $('#user_verson_code').removeClass('d-none');
            $("#user_app_latest_version").prop("required", true);
            $("#user_app_minimum_version").prop("required", true);
        } else {
            $('#user_verson_code').addClass('d-none');
            $("#user_app_latest_version").prop("required", false);
            $("#user_app_minimum_version").prop("required", false);
        }
    }

    var force_update_provider_app = $("input[name='force_update_provider_app']").prop('checked');

    checkProviderForceUpdateSetting(force_update_provider_app);

    $('#force_update_provider_app').change(function() {
        value = $(this).prop('checked');
        checkProviderForceUpdateSetting(value);
    });

    function checkProviderForceUpdateSetting(value) {
        if (value == true) {
            $('#provider_verson_code').removeClass('d-none');
            $("#provider_app_latest_version").prop("required", true);
            $("#provider_app_minimum_version").prop("required", true);
        } else {
            $('#provider_verson_code').addClass('d-none');
            $("#provider_app_latest_version").prop("required", false);
            $("#provider_app_minimum_version").prop("required", false);

        }
    }

    var force_update_admin_app = $("input[name='force_update_admin_app']").prop('checked');

    checkAdminForceUpdateSetting(force_update_admin_app);

    $('#force_update_admin_app').change(function() {
        value = $(this).prop('checked');
        checkAdminForceUpdateSetting(value);
    });

    function checkAdminForceUpdateSetting(value) {
        if (value == true) {
            $('#admin_verson_code').removeClass('d-none');
            $("#admin_app_latest_version").prop("required", true);
            $("#admin_app_minimum_version").prop("required", true);
        } else {
            $('#admin_verson_code').addClass('d-none');
            $("#admin_app_latest_version").prop("required", false);
            $("#admin_app_minimum_version").prop("required", false);
        }
    }

    var is_in_app_purchase_enable = $("input[name='is_in_app_purchase_enable']").prop('checked');

checkInAppPurchaseSetting(is_in_app_purchase_enable);

$('#is_in_app_purchase_enable').change(function() {
    var value = $(this).prop('checked');
    checkInAppPurchaseSetting(value);
});

function checkInAppPurchaseSetting(value) {
    if (value === true) {
        $('#in_app_purchase_fields').removeClass('d-none');
        $("#entitlement_id").prop("required", true);
        $("#google_public_api_key").prop("required", true);
        $("#apple_public_api_key").prop("required", true);
    } else {
        $('#in_app_purchase_fields').addClass('d-none');
        $("#entitlement_id").prop("required", false);
        $("#google_public_api_key").prop("required", false);
        $("#apple_public_api_key").prop("required", false);
    }
}

    var is_sms_notification_enabled = $("input[name='sms_notification']").prop('checked');
    toggleSmsSettings(is_sms_notification_enabled);

    $('#sms_notification').change(function () {
        var value = $(this).prop('checked');
        toggleSmsSettings(value);
    });

    function toggleSmsSettings(value) {
        if (value === true) {
            $('#sms-settings').removeClass('d-none');
            $("#twilio_sid_sms").prop("required", true);
            $("#twilio_auth_token_sms").prop("required", true);
            $("#twilio_phone_number_sms").prop("required", true);
        } else {
            $('#sms-settings').addClass('d-none');
            $("#twilio_sid_sms").prop("required", false);
            $("#twilio_auth_token_sms").prop("required", false);
            $("#twilio_phone_number_sms").prop("required", false);
        }
    }


    var is_whatsapp_notification_enabled = $("input[name='whatsapp_notification']").prop('checked');
    toggleWhatsAppSettings(is_whatsapp_notification_enabled);

    $('#whatsapp_notification').change(function () {
        var value = $(this).prop('checked');
        toggleWhatsAppSettings(value);
    });

    function toggleWhatsAppSettings(value) {
        if (value === true) {
            $('#whatsapp-settings').removeClass('d-none');
            $("#twilio_sid_whatsapp").prop("required", true);
            $("#twilio_auth_token_whatsapp").prop("required", true);
            $("#twilio_whatsapp_number").prop("required", true);
        } else {
            $('#whatsapp-settings').addClass('d-none');
            $("#twilio_sid_whatsapp").prop("required", false);
            $("#twilio_auth_token_whatsapp").prop("required", false);
            $("#twilio_whatsapp_number").prop("required", false);
        }
    }


    var firebase_notification = $("input[id='firebase_notification']").prop('checked');

    checkfirebaseNotificationSetting(firebase_notification);

    $('#firebase_notification').change(function() {
        value = $(this).prop('checked');
        checkfirebaseNotificationSetting(value);
    });

    function checkfirebaseNotificationSetting(value) {
        if (value == true) {
            $('#firebase_notification_key').removeClass('d-none');
            $("#project_id").prop("required", true);
        } else {
            $('#firebase_notification_key').addClass('d-none');
            $("#project_id").prop("required", false);

        }
    }
    $(document).ready(function () {
        // Function to handle visibility of fields
        function toggleNotificationFields() {
            var whatsappChecked = $('#whatsapp_notification').prop('checked');
            var smsChecked = $('#sms_notification').prop('checked');

            // Show/Hide Twilio settings based on any toggle
            if (whatsappChecked || smsChecked) {
                $('#twilio-settings').show();
            } else {
                $('#twilio-settings').hide();
            }

            // Show/Hide SMS settings
            if (smsChecked) {
                $('#sms-settings').show();
            } else {
                $('#sms-settings').hide();
            }

            // Show/Hide WhatsApp settings
            if (whatsappChecked) {
                $('#whatsapp-settings').show();
            } else {
                $('#whatsapp-settings').hide();
            }
        }

        // Initial setup on page load
        toggleNotificationFields();

        // Attach change event to toggle switches
        $('#whatsapp_notification, #sms_notification').change(function () {
            toggleNotificationFields();
        });
    });
    // var enable_chat = $("input[name='enable_chat']").prop('checked');
    // checkChatSetting(enable_chat);
    // $('#enable_chat').change(function() {
    //     value = $(this).prop('checked');
    //     checkChatSetting(value);
    // });

    // function checkChatSetting(value) {
    //     if (value == true) {
    //         $('#chat_api_key_section').removeClass('d-none');
    //     } else {
    //         $('#chat_api_key_section').addClass('d-none');
    //     }
    // }
</script>
