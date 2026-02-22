<x-master-layout>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-block card-stretch">
                <div class="card-body p-0">
                    <div class="d-flex justify-content-between align-items-center p-3">
                    <h5 class="fw-bold">{{ $pageTitle ?? __('messages.list') }}</h5>
                                <a href="{{ route('wallet.index') }}" class=" float-end btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                            @if($auth_user->can('providertype list'))
                            @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    {{ html()->form('POST', route('wallet.store'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('wallet')->open() }}
                    {{ html()->hidden('id',$wallet->id ?? null) }}
                    <div class="row">
                        <div class="form-group col-md-4">
                            {{ html()->label(__('messages.title') . ' <span class="text-danger">*</span>', 'title')
                                ->class('form-control-label') 
                            }}
                            {{ html()->text('title', $wallet->title ?? old('title'))
                                ->placeholder(__('messages.title'))
                                ->class('form-control')
                                ->required() 
                            }}
                            <small class="help-block with-errors text-danger"></small>
                        </div>
                        <div class="form-group col-md-4">
                            {{ html()->label(__('messages.select_name', ['select' => __('messages.user')]) . ' <span class="text-danger">*</span>','name')->class('form-control-label') }}
                            <br />
                            {{ html()->select('user_id',[optional($wallet->providers)->id => optional($wallet->providers)->display_name],optional($wallet->providers)->id)
                                ->class('select2js form-group')
                                ->id('user_id')
                                ->required()
                                ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.provider')]))
                                ->attribute('data-ajax--url', route('ajax-list', ['type' => 'provider-user'])) 
                            }}
                        </div>
                
                        <div class="form-group col-md-4">
                            {{ html()->label(__('messages.amount') . ' <span class="text-danger">*</span>', 'amount')
                                ->class('form-control-label') 
                            }}
                            {{ html()->number('amount', $wallet->amount)
                                ->placeholder(__('messages.amount'))
                                ->class('form-control')
                                ->required()
                                ->attributes(['min' => 0, 'step' => 'any']) 
                            }}
                        </div>
                        <div class="form-group col-md-4">
                            {{ html()->label(__('messages.status') . ' <span class="text-danger">*</span>', 'status')
                                ->class('form-control-label') 
                            }}
                            {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')],$wallet->status ??  old('status'))
                                ->class('form-select select2js')
                                ->required() }}
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
