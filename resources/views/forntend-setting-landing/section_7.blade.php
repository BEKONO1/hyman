{{ html()->form('POST', route('landing_page_settings_updates'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('frontend_setting')->open() }}

{{ html()->hidden('id',$landing_page->id)->class('form-control')->placeholder('id') }}
{{ html()->hidden('type', $tabpage)->class('form-control')->placeholder('id') }}

<div class="row">
    <div class="form-group col-md-12">
        <div class="form-control d-flex align-items-center justify-content-between">
                    <label for="enable_section_7" class="mb-0">{{__('messages.how_it_works')}}</label>
            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                        <input type="checkbox" class="custom-control-input section_7" name="status" id="section_7" data-type="section_7"  {{!empty($landing_page) && $landing_page->status == 1 ? 'checked' : ''}}>
                <label class="custom-control-label" for="section_7"></label>
            </div>
        </div>
    </div>
</div>
<div class="row" id='enable_section_7'>
    <div class="form-group col-md-6">
        {{ html()->label(trans('messages.title') . ' <span class="text-danger">*</span>', 'title')->class('form-control-label') }}
        {{ html()->text('title',old('title'))->id('title')->class('form-control')->placeholder(trans('messages.title')) }}
        <small class="help-block with-errors text-danger"></small>
    </div>
    <div class="form-group col-md-6">
        {{ html()->label(trans('messages.url') . ' <span class="text-danger">*</span>', 'url')->class('form-control-label') }}
        {{ html()->text('url',old('url'))->id('url')->class('form-control')->placeholder(trans('messages.url')) }}
        <small class="help-block with-errors text-danger"></small>
    </div>
            

    <div class="form-group col-md-6">
                <label for="avatar" class="col-sm-6 form-control-label">{{ __('messages.image') }}</label>
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-4">
                            <img src="{{ getSingleMedia($landing_page,'vimage') }}" width="100"  id="vimage_preview" alt="vimage" class="image vimage vimage_preview">
                    @if($landing_page && getMediaFileExit($landing_page, 'vimage'))
                        <a class="text-danger remove-file" href="{{ route('remove.file', ['id' => $landing_page->id, 'type' => 'vimage']) }}"
                                    data--submit="confirm_form"
                                    data--confirmation='true'
                                    data--ajax="true"
                                    title='{{ __("messages.remove_file_title" , ["name" =>  __("messages.image") ]) }}'
                                    data-title='{{ __("messages.remove_file_title" , ["name" =>  __("messages.image") ]) }}'
                                    data-message='{{ __("messages.remove_file_msg") }}'>
                            <i class="ri-close-circle-line"></i>
                        </a>
                    @endif
                </div>
                <div class="col-sm-8 mt-sm-0 mt-2">
                    <div class="custom-file col-md-12">
                                {{ html()->file('vimage')->class("custom-file-input custom-file-input-sm detail")->id("vimage")->attribute('lang', 'en')->attribute('accept', 'image/*')->attribute('onchange', 'preview()') }}
                                @if($landing_page && getMediaFileExit($landing_page, 'vimage'))
                                <label class="custom-file-label upload-label">{{ $landing_page->getFirstMedia('vimage')->file_name }}</label>
                                @else
                                <label class="custom-file-label upload-label">{{ __('messages.choose_file',['file' =>  __('messages.attachments') ]) }}</label>
                                @endif
                            </div>
                            <img id="vimage" src="" width="150px" />
                        </div>
                    </div>

        </div>
    </div>

    <div class="form-group col-md-12">
        {{ html()->label(__('messages.description'), 'description')->class('form-control-label') }}
        {{ html()->textarea('description',null)->class("form-control textarea")->rows(2)->placeholder(__('messages.description')) }}
    </div>

    @if($landing_page && $landing_page->value != null)
        @php
            $landingPageValue = json_decode($landing_page->value, true);
        @endphp

        @foreach($landingPageValue['subtitle'] as $index => $subtitle)
                    <div class="form-section1 form-group col-md-12 ">
                @if(isset($landingPageValue['subtitle'][$index]) || ($landingPageValue['subdescription'][$index]))
                    <div class="row">
                        <div class="form-group col-md-12">
                            {{ html()->label(__('messages.subtitle'), 'subtitle')->class('form-control-label') }}
                            {{ html()->text("subtitle[$index]", is_array($subtitle) ? $subtitle[0] : $subtitle)->id("subtitle_$index")->class('form-control')->placeholder(trans('messages.subtitle'))->required() }}
                        </div>
                        <div class="form-group col-md-12">
                            {{ html()->label(__('messages.subdescription'), 'subdescription')->class('form-control-label') }}
                            {{ html()->textarea("subdescription[$index]", is_array($landingPageValue['subdescription'][$index]) ? $landingPageValue['subdescription'][$index][0] : $landingPageValue['subdescription'][$index])->id('subdescription_'.$index)->class('form-control textarea')->rows(2)->placeholder(trans('messages.subdescription'))->required() }}
                        </div>
                        <small class="help-block with-errors text-danger"></small>
                        <div class="form-group col-3 mb-0 align-self-center">
                                <button class="remove-section1 button-custom button-remove"  title="Remove" data--confirmation1='true'>
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

        @endforeach

    @endif

                <div class="form-section form-group col-md-12 d-none">
                </div>

    <div class="form-group col-md-12">
        <div class="form-group row">
            <div class="col-md-9 text-md-right pe-1">
                <button type="button" id="add-section" class="button-custom button-added">
                    <i class="fas fa-plus me-2"></i>Add More
                </button>
            </div>
            <div class="col-md-3"></div>
        </div>
    </div>

</div>
                     

        

{{ html()->submit(__('messages.save'))->class("btn btn-md btn-primary float-md-end submit_section1") }}
{{ html()->form()->close() }}

<script>
    var enable_section_7 = $("input[name='status']").prop('checked');
    checkSection3(enable_section_7);

    $('#section_7').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkSection3(value);
        
    });

    function checkSection3(value) {
        if (value == true) {
            $('#enable_section_7').removeClass('d-none');
            $('#title').prop('required', true);
            $('#url').prop('required', true);
        } else {
            $('#enable_section_7').addClass('d-none');
            $('#title').prop('required', false);
            $('#url').prop('required', false);
        }
    }

    $(document).ready(function () {
        var maxSections = 6; 

         //hide form section
        function hideFormSection(){
            if($(".form-section1").length >= maxSections){
                $('.form-section').hide();
            }else{
                $('.form-section').show();
            }
        }
        function addSectionBtn(){
            var totalSections = $(".form-section").length + $(".form-section1").length; 
            if( totalSections >= maxSections){
                $('#add-section').hide();
            }else{
                $('#add-section').show();
            }
        }
        hideFormSection();

        // Add Section
        $("#add-section").click(function () {
            var totalSections = $(".form-section").length + $(".form-section1").length; 
            if (totalSections < maxSections) {
                var newSection = `
                    <div class="form-section form-group col-md-12">
                    <div class="form-group col-md-12">
                        {{ html()->label(__('messages.subtitle'), 'subtitle')->class('form-control-label') }}
                        {{ html()->text('subtitle[]', '')->class('form-control')->placeholder(__('messages.subtitle'))->required() }}
                    </div>
                    <div class="form-group col-md-12">
                        {{ html()->label(__('messages.subdescription'), 'subdescription')->class('form-control-label') }}
                        {{ html()->textarea('subdescription[]', '')->class('form-control textarea')->rows(2)->placeholder(__('messages.subdescription'))->required() }}
                    </div>
                    <div class="col-md-6 text-md-left pe-1">
                        <button class="remove-section button-custom button-remove" title="Remove" data--confirmation1="true">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </div>
                </div>`; 
            $(".form-section:last").after(newSection);
                updateRemoveButtonVisibility();
            }
            else{
                $('#add-section').hide();
            }
            addSectionBtn();
            hideFormSection();
        });

        // Remove Section
        $(document).on('click', '.remove-section[data--confirmation1="true"]', function(e) {
            e.preventDefault();

            var confirmationMessage = $(this).data('message') || 'Are you sure you want to delete?';
            var _this = this;
            $.confirm({
                content: confirmationMessage,
                type: '',
                title: 'Remove Section',
                buttons: {
                    yes: {
                        action: function() {
                            if ($(".form-section").length > 1) {
                            $(_this).closest('.form-section').remove();
                            updateRemoveButtonVisibility();
                            hideFormSection();
                            addSectionBtn()
                            }
                            
                            var form = $(this).attr('data--submit');
                                if (form == 'confirm_form') {
                                    $('#confirm_form').attr('action', $(_this).attr('href'));
                                }
                                $('[data--submit="'+form+'"]').submit();
                                
                        }
                    },
                    no: {
                        action: function() {}
                    },
                },
                theme: 'material'
            });
        });


        // Remove Section1
        $(document).on('click', '.remove-section1[data--confirmation1="true"]', function(e) {
            e.preventDefault();

            var confirmationMessage = $(this).data('message') || 'Are you sure you want to delete?';
            var _this = this;

            $.confirm({
                content: confirmationMessage,
                type: '',
                title: 'Remove Section1',
                buttons: {
                    yes: {
                        action: function() {
                            $(_this).closest('.form-section1').remove();
                            var form = $(this).attr('data--submit');
                                if (form == 'confirm_form') {
                                    $('#confirm_form').attr('action', $(_this).attr('href'));
                                }
                                $('[data--submit="'+form+'"]').submit();
                        }
                    },
                    no: {
                        action: function() {}
                    },
                },
                theme: 'material'
            });
        });

       
       
        // Function to update Remove button visibility
        function updateRemoveButtonVisibility() {
            if ($(".form-section").length > 1) {
                $('.remove-section').show();
            } else {
                $('.remove-section').hide();
            }
        }

        // Initially hide Remove button if there's only one section
        updateRemoveButtonVisibility();
    });
    

    var get_value = $('input[name="status"]:checked').data("type");
    getConfig(get_value)
    $('.section_7').change(function(){
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
                var section_7 = title = description = url = subtitle= subdescription ='';

                if (response && response.data.value !== undefined) {
                    if (response.data.key == 'section_7') {
                        obj = JSON.parse(response.data.value);
                    }

                    if (obj !== null) {
                        var title = obj.title;
                        var description = obj.description;
                        var url = obj.url;
                        var subtitle = obj.subtitle;
                        var subdescription = obj.subdescription;
                    }
                    $('#title').val(title);
                    $('#description').val(description);
                    $('#url').val(url);
                    $('#subtitle').val(Array.isArray(subtitle) ? subtitle.join(', ') : subtitle);
                    $('#subdescription').val(Array.isArray(subdescription) ? subdescription.join(', ') : subdescription);
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    function getExtension(filename) {
            var parts = filename.split('.');
            return parts[parts.length - 1];
        }
        function isImage(filename) {
            var ext = getExtension(filename);
            switch (ext.toLowerCase()) {
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                case 'ico':
                    return true;
            }
            return false;
        }
    function readURL(input,className) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            var res = isImage(input.files[0].name);
            if(res == false){
                var msg = 'Image should be png/PNG, jpg/JPG & jpeg/JPG.';
                Snackbar.show({text: msg ,pos: 'bottom-right',backgroundColor:'#d32f2f',actionTextColor:'#fff'});
                $(input).val("");
                return false;
            }
            reader.onload = function(e){
                $(document).find('img.'+className).attr('src', e.target.result);
                $(document).find("label."+className).text((input.files[0].name));
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    $(document).ready(function (){
        $('.select2js').select2();
        $(document).on('change','#vimage',function(){
            readURL(this,'vimage');
        });
    })
    function preview() {
        var input = event.target;
        vimage.src = URL.createObjectURL(input.files[0]);
        var fileName = input.files[0].name;
        var label = $(input).closest('.custom-file').find('.custom-file-label');
        label.text(fileName);
    }
</script>
