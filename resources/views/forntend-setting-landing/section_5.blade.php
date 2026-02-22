
{{ html()->form('POST', route('landing_page_settings_updates'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('frontend_setting')->open() }}
{{ html()->hidden('id',$landing_page->id)->class('form-control')->placeholder('id') }}
{{ html()->hidden('type', $tabpage)->class('form-control')->placeholder('id') }}

<div class="form-group">
    <div class="form-control d-flex align-items-center justify-content-between">
                    <label for="enable_section_5" class="mb-0">{{__('messages.join_us')}}</label>
        <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                        <input type="checkbox" class="custom-control-input section_5" name="status" id="section_5" data-type="section_5"  {{!empty($landing_page) && $landing_page->status == 1 ? 'checked' : ''}}>
            <label class="custom-control-label" for="section_5"></label>
        </div>
    </div>
</div>
<div class="row" id='enable_section_5'>
    <div class="form-group col-md-6">
        {{ html()->label(trans('messages.title') . ' <span class="text-danger">*</span>', 'title')->class('form-control-label') }}
        {{ html()->text('title', old('title'))->id('title')->class('form-control')->placeholder(trans('messages.title')) }}
        <small class="help-block with-errors text-danger"></small>
    </div>
    <div class="form-group col-md-6">
        {{ html()->label(__('messages.email'), 'email')->class('form-control-label') }}
        {{ html()->email('email', old('email'))->class('form-control')->placeholder(__('messages.email'))->attribute('pattern', '[^@]+@[^@]+\.[a-zA-Z]{2,}')->attribute('title', 'Please enter a valid email address') }}
        <small class="help-block with-errors text-danger"></small>
    </div>
    <div class="form-group col-md-6">
        {{ html()->label(__('messages.contact_number'), 'contact_number')->class('form-control-label') }}
        {{ html()->text('contact_number', old('contact_number'))->class('form-control contact_number')->placeholder(__('messages.contact_number')) }}
        <small class="help-block with-errors text-danger" id="contact_number_err"></small>
    </div>
    <div class="form-group col-md-6">
                <label class="form-control-label" for="section5_attachment">{{ __('messages.image') }}</label>
        <div class="custom-file">
            <input type="file" name="section5_attachment[]" class="custom-file-input" data-file-error="{{ __('messages.files_not_allowed') }}" multiple onchange="preview()">
            @if($landing_page && getMediaFileExit($landing_page, 'section5_attachment'))
                <label class="custom-file-label upload-label">{{ $landing_page->getFirstMedia('section5_attachment')->file_name }}</label>
            @else
                        <label class="custom-file-label upload-label">{{ __('messages.choose_file',['file' =>  __('messages.attachments') ]) }}</label>
            @endif
        </div>
        <img id="frontend_image_preview" src="" width="150px" />
    </div>
    <div class="row section5_attachment_div">
        <div class="col-md-12">
            @if($landing_page && getMediaFileExit($landing_page, 'section5_attachment'))
                @php
                        $attchments = $landing_page->getMedia('section5_attachment');
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
                                <div class="col-md-2 pe-10 text-center galary file-gallary-{{$landing_page->id}} position-relative"
                                    data-gallery=".file-gallary-{{$landing_page->id}}"
                                    id="section5_attachment_preview_{{$attchment->id}}">
                                    @if($extention)
                                        <a id="attachment_files" href="{{ $attchment->getFullUrl() }}" class="list-group-item-action attachment-list" target="_blank">
                                            <img src="{{ $attchment->getFullUrl() }}" class="attachment-image" alt="">
                                    </a>
                                @else
                                        <a id="attachment_files" class="video list-group-item-action attachment-list" href="{{ $attchment->getFullUrl() }}">
                                        <img src="{{ asset('images/file.png') }}" class="attachment-file">
                                    </a>
                                @endif
                                <a class="text-danger remove-file" href="{{ route('remove.file', ['id' => $attchment->id, 'type' => 'section5_attachment']) }}"
                                    data--submit="confirm_form" data--confirmation="true"
                                    data--ajax="true" data-toggle="tooltip"
                                        title='{{ __("messages.remove_file_title" , ["name" =>  __("messages.attachments") ] ) }}'
                                        data-title='{{ __("messages.remove_file_title" , ["name" =>  __("messages.attachments") ] ) }}'
                                        data-message='{{ __("messages.remove_file_msg") }}'>
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
    <div class="form-group col-md-12">
        {{ html()->label(__('messages.description'), 'description')->class('form-control-label') }}
        {{ html()->textarea('description',null)->class('form-control textarea')->rows(2)->placeholder(__('messages.description')) }}
    </div>
</div>




{{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end submit_section1') }}
{{ html()->form()->close() }}

<script>
    var enable_section_5 = $("input[name='status']").prop('checked');
    checkSection5(enable_section_5);

    $('#section_5').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkSection5(value);
        
    });

    function checkSection5(value) {
        if (value == true) {
            $('#enable_section_5').removeClass('d-none');
            $('#title').prop('required', true);
        } else {
            $('#enable_section_5').addClass('d-none');
            $('#title').prop('required', false);
        }
    }


    

    var get_value = $('input[name="status"]:checked').data("type");
    getConfig(get_value)
    $('.section_5').change(function(){
        value = $(this).prop('checked') == true ? true : false;
        type = $(this).data("type");
        getConfig(type)

    });

    function getConfig(type) {
        var _token = $('meta[name="csrf-token"]').attr('content');
        var page = "{{$tabpage}}";
        var getDataRoute = "{{ route('getLandingLayoutPageConfig') }}";
        $.ajax({
            url: getDataRoute,
            type: "POST",
            data: {
                type: type,
                page: page,
                _token: _token
            },
            success: function (response) {
                var obj = '';
                var section_5 = title = description = email = contact_number = '';

                if (response) {
                    if (response.data.key == 'section_5') {
                        obj = JSON.parse(response.data.value);
                    }
                    if (obj !== null) {
                        var title = obj.title;
                        var email = obj.email;
                        var contact_number = obj.contact_number;
                        var description = obj.description;
                    }
                    $('#title').val(title);
                    $('#email').val(email);
                    $('#contact_number').val(contact_number);
                    $('#description').val(description);
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    function preview() {
            frontend_image_preview.src = URL.createObjectURL(event.target.files[0]);
            var fileName = event.target.files[0].name;
            var label = $(event.target).closest('.custom-file').find('.custom-file-label');
            label.text(fileName);
        }

</script>
