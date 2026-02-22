<x-master-layout>
    <div class="container-fluid">
    @include('partials._provider')
        <div class="row">
        <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                            @if($auth_user->can('providerdocument list'))
                                <a href="{{ route('providerdocument.show',['providerdocument' => $providerdata->id]) }}" class=" float-end btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('providerdocument.store'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('provider_document')->open() }}
                            {{ html()->hidden('id',$provider_document->id ?? null) }}
                            <div class="row">
                                @if(auth()->user()->hasAnyRole(['admin','demo_admin']))
                                <div class="form-group col-md-4">
                                    {{ html()->label(__('messages.select_name', ['select' => __('messages.providers')]) . ' <span class="text-danger">*</span>', 'provider_id')
                                        ->class('form-control-label')
                                    }}
                                    <br />
                                    {{ html()->select('provider_id', [$providerdata->id => $providerdata->display_name], $providerdata->id)
                                        ->class('select2js form-group providers')
                                        ->required()
                                        ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.providers')]))
                                    }}
                                </div>
                                @endif

                                @php
                                    $is_required = optional($provider_document->document)->is_required == 1 ? '*' : '';
                                @endphp

                               <div class="form-group col-md-4">
                                {{ html()->label(__('messages.select_name', ['select' => __('messages.document')]) . ' <span class="text-danger">* </span>', 'document_id')
                                    ->class('form-control-label')
                                }}
                                <br />
                                {{ html()->select(
                                        'document_id',
                                        [optional($provider_document->document)->id => optional($provider_document->document)->name],
                                        optional($provider_document->document)->id
                                    )
                                    ->class('select2js form-group document_id')
                                    ->id('document_id')
                                    ->required()
                                    ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.document')]))
                                    ->attribute('data-ajax--url', route('ajax-list', ['type' => 'documents']))
                                    ->attributeIf(optional($provider_document->document)->id, 'disabled', 'disabled')  {{-- 👈 Conditional disable --}}
                                }}
                            
                                @if(auth()->user()->can('document add'))
                                    <a href="{{ route('document.create') }}">
                                        <i class="fa fa-plus-circle mt-2"></i>
                                        {{ trans('messages.add_form_title', ['form' => trans('messages.document')]) }}
                                    </a>
                                @endif
                            </div>


                                @if(auth()->user()->hasAnyRole(['admin','demo_admin']))
                                <div class="form-group col-md-4">
                                    {{ html()->label(trans('messages.is_verify') . ' <span class="text-danger">*</span>', 'is_verified')
                                        ->class('form-control-label')
                                    }}
                                    {{ html()->select(
                                        'is_verified',
                                        ['1' => __('messages.verified'), '0' => __('messages.not_verified')],
                                        old('is_verified', isset($provider_document->is_verified) ? (string)$provider_document->is_verified : '0')
                                    )
                                        ->id('is_verified')
                                        ->class('form-select select2js')
                                        ->required()
                                    }}
                                </div>
                                @endif

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('messages.upload_document') . ' <span class="text-danger">*</span>', 'provider_document')
                                        ->class('form-control-label')
                                    }}
                                    <div class="custom-file">
                                        <input type="file" id="provider_document" name="provider_document" class="custom-file-input" @if(!$provider_document || !getMediaFileExit($provider_document, 'provider_document')) required @endif>
                                        @if($provider_document && getMediaFileExit($provider_document, 'provider_document'))
                                        <label class="custom-file-label upload-label">{{ $provider_document->getFirstMedia('provider_document')->file_name }}</label>
                                        @else
                                        <label class="custom-file-label upload-label">{{ __('messages.choose_file', ['file' =>  __('messages.document') ]) }}</label>
                                        @endif
                                        <span id="provider_document_error" class="text-danger d-none" >{{ __('messages.document_required') }}</span>
                                    </div>
                                </div>
{{-- 
                                @if(getMediaFileExit($provider_document, 'provider_document'))
                                    <div class="col-md-2 mb-2 position-relative">
                                        @php
                                            $file_extention = config('constant.IMAGE_EXTENTIONS');
                                            $image = getSingleMedia($provider_document,'provider_document');
                                            $extention = in_array(strtolower(imageExtention($image)), $file_extention);
                                        @endphp
                                        @if($extention)
                                            <img id="provider_document_preview" src="{{ $image }}" alt="" class="attachment-image mt-1">
                                        

                                        @if( isset($provider_document) && $provider_document->is_verified != 1)
                                        <a class="text-danger remove-file" href="{{ route('remove.file', ['id' => $provider_document->id, 'type' => 'provider_document']) }}"
                                            data--submit="confirm_form"
                                            data--confirmation='true'
                                            data--ajax="true"
                                            title='{{ __("messages.remove_file_title", ["name" => __("messages.image") ]) }}'
                                            data-title='{{ __("messages.remove_file_title", ["name" => __("messages.image") ]) }}'
                                            data-message='{{ __("messages.remove_file_msg") }}'>
                                            <i class="ri-close-circle-line"></i>
                                        </a>
                                        @endif
                                        @endif
                                        <a href="{{ $image }}" class="d-block mt-2" download target="_blank"><i class="fas fa-download "></i> {{ __('messages.download') }}</a>
                                    </div>
                                @endif --}}

                                @if(getMediaFileExit($provider_document, 'provider_document'))
    <div class="col-md-2 mb-2 position-relative preview-wrapper">
        @php
            $file_extention = config('constant.IMAGE_EXTENTIONS');
            $image = getSingleMedia($provider_document,'provider_document');
            $extention = in_array(strtolower(imageExtention($image)), $file_extention);
        @endphp

        @if($extention)
            <img id="provider_document_preview" src="{{ $image }}" alt="" class="attachment-image mt-1">

            @if( isset($provider_document) && $provider_document->is_verified != 1)
                <a class="text-danger remove-preview" href="javascript:void(0);"
                   title='{{ __("messages.remove_file_title", ["name" => __("messages.image") ]) }}'>
                   <i class="ri-close-circle-line"></i>
                </a>
            @endif
        @endif

        <a href="{{ $image }}" class="d-block mt-2" download target="_blank">
            <i class="fas fa-download "></i> {{ __('messages.download') }}
        </a>
    </div>
@endif

                            </div>

                            @if( isset($provider_document) && $provider_document->is_verified != 1)
                            {{ html()->submit(trans('messages.save'))
                                ->class('btn btn-md btn-primary float-end')
                                ->id('saveBtn')
                                ->attribute('disabled', true)
                            }}
                            @endif
                        {{ html()->form()->close() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
    @section('bottom_script')
        <script type="text/javascript">
            (function($) {
                "use strict";
                    $(document).ready(function(){
                        $(document).on('change' , '#document_id' , function (){
                            var data = $('#document_id').select2('data')[0];

                            if(data.is_required == 1)
                            {
                                $('#document_required').text('*');
                                $('#provider_document').attr('required');
                            } else {
                                $('#document_required').text('');
                                $('#provider_document').attr('required', false);
                            }
                            toggleSaveButton();
                        })
                        $(document).on('change', 'input[name="provider_document"]', function () {
                            toggleSaveButton();
                        });

                        window.hasOldFile = Boolean(@json(getMediaFileExit($provider_document ?? null, 'provider_document')));
                        toggleSaveButton();
                        function toggleSaveButton() {
                            var docSelected = $('#document_id').val() && $('#document_id').val() !== '';
                            var fileInput = $('input[name="provider_document"]').get(0);
                            var fileUploaded = fileInput && fileInput.files && fileInput.files.length > 0;
                            var hasOldFile = typeof window.hasOldFile !== 'undefined' ? window.hasOldFile : @json(getMediaFileExit($provider_document ?? null, 'provider_document'));
                            var isValid = docSelected && (fileUploaded || hasOldFile);
                            $('#saveBtn').prop('disabled', !isValid);
                        }
                    })
            })(jQuery);

    document.addEventListener('DOMContentLoaded', function() {
    checkImage();
});
function checkImage() {
    var id = @json($providerdata->id );
    var route = "{{ route('check-image', ':id') }}";
    route = route.replace(':id', id);
    var type = 'provider_document';

    $.ajax({
        url: route,
        type: 'GET',
        data: {
            type: type,
        },
        success: function(result) {
            var attachments = result.results;

            if (attachments && attachments.length === 0) {
                $('input[name="provider_document"]').attr('required', 'required');
            } else {
                $('input[name="provider_document"]').removeAttr('required');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

function checkProviderDocumentImage() {
    var img = document.getElementById('provider_document_preview');
    var saveBtn = document.getElementById('saveBtn');
    var errorMsg = document.getElementById('provider_document_error');
    if (img && img.src === '{{ asset('images/default.png') }}') {
        if (saveBtn) saveBtn.disabled = true;
        if (errorMsg) {
            errorMsg.classList.remove('d-none');
            errorMsg.classList.add('d-block');
        }
    } else {
        if (saveBtn) saveBtn.disabled = false;
        if (errorMsg) {
            errorMsg.classList.remove('d-block');
            errorMsg.classList.add('d-none');
        }
    }
}

checkProviderDocumentImage();

$('input[name="provider_document"]').on('change', function(e) {
    var file = this.files[0];
    if (file) {
        var reader = new FileReader();
        var ext = file.name.split('.').pop().toLowerCase();
        if(ext === 'pdf') {
            $('#provider_document_preview').addClass('d-none');
            $('.remove-file').addClass('d-none');
        } else {
            $('#provider_document_preview').removeClass('d-none');
            $('.remove-file').removeClass('d-none');
        }
        reader.onload = function(e) {
            $('#provider_document_preview').attr('src', e.target.result);
            checkProviderDocumentImage();
        }
        reader.readAsDataURL(file);
    } else {
        $('#provider_document_preview').removeClass('d-none');
        $('.remove-file').removeClass('d-none');
        checkProviderDocumentImage();
    }
});

$('#provider_document_preview').on('load', function() {
    checkProviderDocumentImage();
});


document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('provider_document');
    const previewWrapper = document.querySelector('.preview-wrapper');
    const saveBtn = document.getElementById('saveBtn');

    function toggleSaveButton(enable) {
        if (saveBtn) {
            saveBtn.disabled = !enable;
        }
    }

    if (fileInput) {
        fileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    let previewImage = document.getElementById('provider_document_preview');

                    if (!previewImage) {
                        previewImage = document.createElement('img');
                        previewImage.id = 'provider_document_preview';
                        previewImage.className = 'attachment-image mt-1';
                        previewWrapper.prepend(previewImage);
                    }

                    previewImage.src = e.target.result;
                    previewImage.classList.remove('d-none');
                    previewWrapper.classList.remove('d-none');

                    toggleSaveButton(true);
                };
                reader.readAsDataURL(file);
            } else {
                toggleSaveButton(false);
            }
        });
    }

    document.querySelector('.remove-preview')?.addEventListener('click', function () {
        const previewImage = document.getElementById('provider_document_preview');
        if (previewImage) {
            previewImage.src = '';
            previewImage.classList.add('d-none');
        }
        fileInput.value = '';
        previewWrapper.classList.add('d-none');
        toggleSaveButton(false);
    });

    // Initial state based on existing preview
    const existingPreview = document.getElementById('provider_document_preview');
    if (!existingPreview || existingPreview.src === '') {
        toggleSaveButton(false);
    } else {
        toggleSaveButton(true);
    }
});

    

        </script>
    @endsection
</x-master-layout>