<x-master-layout>
    <div class="container-fluid">
        <div class="row">
        <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                            @if($auth_user->can('providerdocument list'))
                                <a href="{{ route('providerdocument.index') }}" class=" float-end btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('ratingreview.store'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('rating_review')->open() }}
                        {{ html()->hidden('id',$rating_review->id ?? null) }}
                        <div class="row">
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.select_name', ['select' => __('messages.customer')]) . ' <span class="text-danger">*</span>', 'name')
                                    ->class('form-control-label') 
                                }}
                                <br />
                                {{ html()->select('customer_id', [optional($rating_review->customer)->id => optional($rating_review->customer)->display_name], optional($rating_review->customer)->id)
                                    ->class('select2js form-group customer')
                                    ->required()
                                    ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.customer')]))
                                    ->attribute('data-ajax--url', route('ajax-list', ['type' => 'user']))
                                }}
                            </div>
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.select_name', ['select' => __('messages.service')]) . ' <span class="text-danger">*</span>', 'service_id')
                                    ->class('form-control-label') 
                                }}
                                <br />
                                {{ html()->select('service_id', [optional($rating_review->service)->id => optional($rating_review->service)->name], optional($rating_review->service)->id)
                                    ->class('select2js form-group service')
                                    ->required()
                                    ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.service')]))
                                    ->attribute('data-ajax--url', route('ajax-list', ['type' => 'service']))
                                }}
                            </div>
                            <div class="form-group col-md-4">
                                {{ html()->label(trans('messages.rating') . ' <span class="text-danger">*</span>', 'rating')
                                    ->class('form-control-label') 
                                }}
                                {{ html()->select('rating', ['1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5], $rating_review->rating ?? old('rating'))
                                    ->class('form-select select2js')
                                    ->id('rating')
                                    ->required() 
                                }}
                            </div>
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.review') . ' <span class="text-danger">*</span>', 'review')
                                    ->class('form-control-label') 
                                }}
                                {{ html()->textarea('review', $rating_review->review)
                                    ->class('form-control textarea')
                                    ->rows(3)
                                    ->placeholder(__('messages.description')) 
                                }}
                                <small class="help-block with-errors text-danger"></small>
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