<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{$pageTitle}}</h5>
                            <a href="{{ route('provider.index') }}   " class=" float-end btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('provider.changepassword'))->attribute('data-toggle', 'validator')->id('provider')->open() }}
                        <div class="row">
                            <div class="col-md-3 d-md-block d-none"></div>
                            <div class="col-md-6">
                                {{ html()->hidden('id', $providerdata->id ?? null)->class('form-control')->placeholder('id') }}
                                <div class="form-group has-feedback">
                                    {{ html()->label(__('messages.old_password') . ' <span class="text-danger">*</span>', 'old_password')->class('form-control-label col-md-12') }}
                                    <div class="col-md-12">
                                        {{ html()->password('old')->class('form-control')->id('old_password')->placeholder(__('messages.old_password'))->required() }}
                                    </div>
                                </div>
                                <div class="form-group has-feedback">

                                    {{ html()->label(__('messages.new_password') . ' <span class="text-danger">*</span>', 'password')->class('form-control-label col-md-12') }}
                                    <div class="col-md-12">
                                        {{ html()->password('password')->class('form-control')->id('password')->placeholder(__('messages.new_password'))->required() }}
                                    </div>
                                </div>
                                <div class="form-group has-feedback">
                                    {{ html()->label(__('messages.confirm_new_password') . ' <span class="text-danger">*</span>', 'password-confirm')->class('form-control-label col-md-12') }}
                                <div class="col-md-12">
                                        {{ html()->password('password_confirmation')->class('form-control')->id('password-confirm')->placeholder(__('messages.confirm_new_password'))->required() }}
                                </div>
                                </div>
                                    <div class="form-group ">
                                    <div class="col-md-12">
                                            {{ html()->submit(__('messages.save'))->id('submit')->class('btn btn-md btn-primary float-md-right mt-15') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 d-md-block d-none"></div>
                            </div>
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>