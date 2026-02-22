<x-master-layout>
    <div class="container-fluid">
        <div class="row">
        <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                            @if($auth_user->can('servicefaq list'))
                                <a href="{{ route('servicefaq.index',['id' => $service_id]) }}" class=" float-end btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('servicefaq.store'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('servicefaq')->open()}}
                        {{ html()->hidden('id',$servicefaq->id ?? null) }}
                        {{ html()->hidden('service_id',$service_id) }}
                        <div class="row">
                            <div class="form-group col-md-4">
                                {{ html()->label(trans('messages.title') . ' <span class="text-danger">*</span>', 'title')->class('form-control-label') }}
                                {{ html()->text('title', $servicefaq->title)->placeholder(trans('messages.title'))->class('form-control')->required()}}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                            <div class="form-group col-md-4">
                                {{ html()->label(trans('messages.status') . ' <span class="text-danger">*</span>', 'status')->class('form-control-label') }}
                                {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')], $servicefaq->status)->id('role')->class('form-select select2js')->required()}}
                            </div>
                            <div class="form-group col-md-4">
                                {{ html()->label(trans('messages.description'), 'description')->class('form-control-label') }}
                                {{ html()->textarea('description', $servicefaq->description)->class('form-control textarea')->rows(3)->placeholder(__('messages.description'))}}
                        </div>
                        </div>                   
                        {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary float-end') }}
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>