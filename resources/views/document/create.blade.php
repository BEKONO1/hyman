<x-master-layout>
    <div class="container-fluid">
        <div class="row justify-content-center">
        <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                            @if($auth_user->can('document list'))
                                <a href="{{ route('document.index') }}" class=" float-end btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('document.store'))->attribute('data-toggle', 'validator')->id('documents')->open() }}
                        {{ html()->hidden('id',$documentdata->id ?? null) }}
                        <div class="row">
                            <div class="form-group col-md-12">
                                {{ html()->label(trans('messages.name') . ' <span class="text-danger">*</span>', 'name')->class('form-control-label') }}
                                {{ html()->text('name',$documentdata->name)->placeholder(trans('messages.name'))->class('form-control')->required()}}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                    
                            <div class="form-group col-md-12">
                                {{ html()->label(trans('messages.status') . ' <span class="text-danger">*</span>', 'status')->class('form-control-label') }}
                                {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')], $documentdata->status)->id('status')->class('form-select select2js')->required()}}
                            </div>
                    
                            <div class="form-group col-md-12">
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    {{ html()->checkbox('is_required', $documentdata->is_required)->class('custom-control-input')->id('is_required')}}                                    
                                    <label class="custom-control-label" for="is_required">{{ __('messages.is_required') }}</label>
                                    </label>
                            </div>
                            </div>
                        </div>
                        {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary float-end')}}
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>