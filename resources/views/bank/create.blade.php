<x-master-layout>   
    <div class="container-fluid">
    @if($user_type=='provider')
    @include('partials._provider')
    @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                            @if($auth_user->can('bank list'))
                            <a href="{{ route('bank.list',['user_id'=>$providerdata->id]) }}" class=" float-end btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('bank.store'))->id('bank')->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}
                        {{ html()->hidden('id',$bankdata->id ?? null) }}
                        {{ html()->hidden('type', $user_type) }}
                        <div class="row">
                            @if(auth()->user()->hasAnyRole(['admin','demo_admin']))
                                <div class="form-group col-md-4 d-none">
                                    {{ html()->label(__('messages.select_name', ['select' => __('messages.user')]) . ' <span class="text-danger">*</span>', 'name')->class('form-control-label')}}
                                    <br />
                                    {{ html()->select('provider_id', [$providerdata->id => $providerdata->display_name], $providerdata->id)
                                        ->class('select2js form-group')
                                        ->id('provider_id')
                                        ->required()
                                        ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.user')]))
                                        ->attribute('readonly', 'readonly')
                                    }}
                                </div>
                            @endif
                            <div class="form-group col-md-4">
                                {{ html()->label(trans('messages.bank_name') . ' <span class="text-danger">*</span>', 'bank_name')->class('form-control-label')}}
                                {{ html()->text('bank_name', $bankdata->bank_name)->placeholder(trans('messages.bank_name'))->class('form-control')->required()}}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                            <div class="form-group col-md-4">
                                {{ html()->label(trans('messages.branch_name') . ' <span class="text-danger">*</span>', 'branch_name')->class('form-control-label')}}
                                {{ html()->text('branch_name', $bankdata->branch_name)->placeholder(trans('messages.branch_name'))->class('form-control')->required()}}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                            <div class="form-group col-md-4">
                                {{ html()->label(trans('messages.account_no') . ' <span class="text-danger">*</span>', 'account_no')->class('form-control-label')}}
                                {{ html()->text('account_no', $bankdata->account_no)->placeholder(trans('messages.account_no'))->class('form-control')->required()}}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(trans('messages.ifsc_no'), 'ifsc_no')->class('form-control-label')}}
                                {{ html()->text('ifsc_no',$bankdata->ifsc_no)->placeholder(trans('messages.ifsc_no'))->class('form-control')}}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(trans('messages.status') . ' <span class="text-danger">*</span>', 'status')->class('form-control-label')}}
                                {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')],$bankdata->status)->class('form-select select2js')->required()}}
                            </div>
                            {{-- <div class="form-group col-md-4">
                                <label class="form-control-label" for="bank_attachment">{{ __('messages.image') }} <span class="text-danger">*</span> </label>
                                <div class="custom-file">
                                    <input type="file" name="bank_attachment[]" class="custom-file-input" data-file-error="{{ __('messages.files_not_allowed') }}" multiple>
                                    <label class="custom-file-label upload-label">{{ __('messages.choose_file',['file' =>  __('messages.attachments') ]) }}</label>
                                </div>
                            </div> --}}
                            <div class="row bank_attachment_div">
                                <div class="col-md-12">
                                    @if(getMediaFileExit($bankdata, 'bank_attachment'))
                                        @php
                                            $attchments = $bankdata->getMedia('bank_attachment');
                                            $file_extention = config('constant.IMAGE_EXTENTIONS');
                                        @endphp
                                        <div class="border-start">
                                            <p class="ms-2"><b>{{ __('messages.attached_files') }}</b></p>
                                            <div class="ms-2 my-3">
                                                <div class="row">
                                                @foreach($attchments as $attchment )
                                                <?php
                                                            $extention = in_array(strtolower(imageExtention($attchment->getFullUrl())), $file_extention);
                                                ?>
                    
                                                        <div class="col-md-2 pe-10 text-center galary file-gallary-{{$bankdata->id}} position-relative" data-gallery=".file-gallary-{{$bankdata->id}}" id="bank_attachment_preview_{{$attchment->id}}">
                                                            @if($extention)
                                                                <a id="attachment_files" href="{{ $attchment->getFullUrl() }}" class="list-group-item-action  attachment-list" target="_blank">
                                                                    <img src="{{ $attchment->getFullUrl() }}" class="attachment-image img-fluid" alt="">
                                                                </a>
                                                            @else
                                                                <a id="attachment_files" class="video list-group-item-action attachment-list " href="{{ $attchment->getFullUrl() }}">
                                                                    <img src="{{ asset('images/file.png') }}" class="attachment-file img-fluid">
                                                                </a>
                                                            @endif
                                                            <a class="text-danger remove-file" href="{{ route('remove.file', ['id' => $attchment->id, 'type' => 'bank_attachment']) }}" data--submit="confirm_form" data--confirmation='true' data--ajax="true" data-toggle="tooltip" title='{{ __("messages.remove_file_title" , ["name" =>  __("messages.attachments") ] ) }}' data-title='{{ __("messages.remove_file_title" , ["name" =>  __("messages.attachments") ] ) }}' data-message='{{ __("messages.remove_file_msg") }}'>
                                                                <i class="ri-close-circle-line"></i>
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary float-end') }}
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function preview() {
            bank_image_preview.src = URL.createObjectURL(event.target.files[0]);
        }
        // $(document).on('keyup', '.mobile_no', function() {
        //     var contactNumberInput = document.getElementById('mobile_no');
        //     var inputValue = contactNumberInput.value;
        //     inputValue = inputValue.replace(/[^0-9+\- ]/g, '');
        //     if (inputValue.length > 15) {
        //         inputValue = inputValue.substring(0, 15);
        //         $('#mobile_no_err').text('Contact number should not exceed 15 characters');
        //     } else {
        //             $('#mobile_no_err').text('');
        //     }
        //     contactNumberInput.value = inputValue;
        //     if (inputValue.match(/^[0-9+\- ]+$/)) {
        //         $('#mobile_no_err').text('');
        //     } else {
        //         $('#mobile_no_err').text('Please enter a valid mobile number');
        //     }
        // });
//         document.addEventListener('DOMContentLoaded', function() { 
//     checkImage();
// });
// function checkImage() { 
//     var id = @json($bankdata->id); 
//     var route = "{{ route('check-image', ':id') }}";
//     route = route.replace(':id', id);  
//     var type = 'bank_attachment';

//     $.ajax({
//         url: route,
//         type: 'GET',   
//         data: {
//             type: type,   
//         }, 
//         success: function(result) {  
//             var attachments = result.results;  

//             if (attachments.length === 0) { 
//                 $('input[name="bank_attachment[]"]').attr('required', 'required');
//             } else { 
//                 $('input[name="bank_attachment[]"]').removeAttr('required');
//             }         
//         },
//         error: function(xhr, status, error) {
//             console.error('Error:', error);  
//         }
//     });
// }
    </script>
</x-master-layout>