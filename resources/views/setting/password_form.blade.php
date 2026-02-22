{{ html()->form('POST', route('changePassword'))
    ->attributes(['data-toggle' => 'validator', 'id' => 'user-password'])
    ->open() }}

<div class="row">
    <div class="col-md-3 d-md-block d-none"></div>
    <div class="col-md-6">
        {{ html()->hidden('id', $user_data->id ?? null)->class('form-control')->placeholder('id') }}
        <div class="form-group has-feedback">
            {{ html()->label(
                trans('messages.old_password') . ' <span class="text-danger">*</span>',
                'old_password'
            )->class('form-control-label col-md-12') }}
            <div class="col-md-12">
                {{ html()->password('old')
                    ->class('form-control')
                    ->id('old_password')
                    ->placeholder(trans('messages.old_password'))
                    ->required() }}
                    </div>
            </div>
        <div class="form-group has-feedback">
            
            {{ html()->label(trans('messages.new_password') . ' <span class="text-danger">*</span>','password')->class('form-control-label col-md-12') }}
            <div class="col-md-12">
                {{ html()->password('password')->class('form-control')->id('password')->placeholder(trans('messages.new_password'))->required() }}
                </div>
            </div>
        <div class="form-group has-feedback">
            {{ html()->label(trans('messages.confirm_new_password') . ' <span class="text-danger">*</span>','password-confirm')->class('form-control-label col-md-12') }}
            <div class="col-md-12">
                {{ html()->password('password_confirmation')->class('form-control')->id('password-confirm')->placeholder(trans('messages.confirm_new_password'))->required() }}
                </div>
            </div>
            <div class="form-group ">
            <div class="col-md-12">
                {{ html()->submit(trans('messages.save'))
                    ->class('btn btn-md btn-primary float-md-end mt-15')
                    ->id('submit') }}
                </div>       
            </div>
        </div>
        <div class="col-md-3 d-md-block d-none"></div>
    </div>
{{ html()->form()->close() }}