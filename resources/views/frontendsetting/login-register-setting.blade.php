{{ html()->form('POST', route('login_register_page_settings'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('frontend_setting')->open() }}

{{ html()->hidden('id',$landing_page_data ?? null)->placeholder('id')->class('form-control') }}
{{ html()->hidden('type', $tabpage)->placeholder('id')->class('form-control') }}

<div class="form-group">
    <div class="d-flex align-items-center justify-content-between form-control">
                <label class="mb-0" for="enable_login_register">{{__('messages.enable_login_register')}}</label>
        <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <input type="checkbox" class="custom-control-input login_register" name="status" id="login_register" data-type="login_register"  {{!empty($landing_page_data) && $landing_page_data->status == 1 ? 'checked' : ''}}>
            <label class="custom-control-label" for="login_register"></label>
        </div>
    </div>
</div>
        <div class="row" id='enable_login_register'>
    <div class="form-group col-md-6">
        {{ html()->label(trans('messages.title') . ' <span class="text-danger">*</span>', 'title')->class('form-control-label')->attribute('for', 'title') }}
        {{ html()->text('title',$landing_page_data->title )->id('title')->placeholder(trans('messages.title'))->class('form-control') }}
        <small class="help-block with-errors text-danger"></small>
    </div>

    <div class="form-group col-md-6">
                <label for="avatar" class="col-sm-6 form-control-label">{{ __('messages.image') }}</label>
        <div class="col-sm-12">
            <div class="row">
            <div class="col-sm-4">
    <div class="image-preview-wrapper">
    <img src="{{ getSingleMedia($landing_page_data,'login_register_image') }}" 
         width="100"  
         id="login_register_image_preview" 
         alt="login_register_image" 
         class="image login_register_image login_register_image_preview">
    @if($landing_page_data && getMediaFileExit($landing_page_data, 'login_register_image'))
    <a class="text-danger remove-file remove-btn"
   href="{{ route('remove.file', ['id' => $landing_page_data->id, 'type' => 'login_register_image']) }}"
   data--submit="confirm_form"
   data--confirmation="true"
   data--ajax="true"
   title='{{ __("messages.remove_file_title" , ["name" =>  __("messages.image") ]) }}'
   data-title='{{ __("messages.remove_file_title" , ["name" =>  __("messages.image") ]) }}'
   data-message='{{ __("messages.remove_file_msg") }}'>
    <i class="ri-close-circle-line"></i>
</a>

    @endif
</div>
</div>
<style>
    .image-preview-wrapper {
    position: relative;
    display: inline-block;
    padding-top: 10px; /* Add padding to prevent icon overlap if needed */
}

.image-preview-wrapper .remove-btn {
    position: absolute;
    top: -10px;     /* Move it up outside the image */
    right: -10px;   /* Move it to the right outside the image */
    background: #ccd3da;
    border-radius: 50%;
    padding: 4px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    z-index: 10;
}

.image-preview-wrapper img {
    display: block;
    max-width: 100px;
}

</style>

                <div class="col-sm-8 mt-sm-0 mt-2">
                    <div class="custom-file col-md-12">
                        {{ html()->file('login_register_image')->class('custom-file-input custom-file-input-sm detail')->id('login_register_image')->attribute('lang', 'en')->attribute('accept', 'image/*')->attribute('onchange', 'preview()') }}
                        @if($landing_page_data && getMediaFileExit($landing_page_data, 'login_register_image'))
                            <label class="custom-file-label upload-label">{{ $landing_page_data->getFirstMedia('login_register_image')->file_name }}</label>
                        @else
                                    <label class="custom-file-label upload-label">{{ __('messages.choose_file',['file' =>  __('messages.attachments') ]) }}</label>
                        @endif
                    </div>
                    <img id="login_register_image" src="" width="150px" />
                </div>
            </div>

        </div>
    </div>

    <div class="form-group col-md-12">
        {{ html()->label(__('messages.description'), 'description')->class('form-control-label') }}
        {{ html()->textarea('description',$landing_page_data->description )->class('form-control textarea')->rows(2)->placeholder(__('messages.description')) }}
    </div>
            
           
</div>

{{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end submit_section1') }}
{{ html()->form()->close() }}


<script>
    var enable_login_register = $("input[name='status']").prop('checked');
    checkSection3(enable_login_register);

    $('#login_register').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkSection3(value);
        
    });

    function checkSection3(value) {
        if (value == true) {
            $('#enable_login_register').removeClass('d-none');
            $('#title').prop('required', true);
        } else {
            $('#enable_login_register').addClass('d-none');
            $('#title').prop('required', false);
        }
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
        $(document).on('change','#login_register_image',function(){
            readURL(this,'login_register_image');
        });
    })
    function preview() {
        var input = event.target;
        login_register_image.src = URL.createObjectURL(input.files[0]);
        var fileName = input.files[0].name;
        var label = $(input).closest('.custom-file').find('.custom-file-label');
        label.text(fileName);
    }
</script>
<script>
   $(document).on('click', '.remove-file', function (e) {
    e.preventDefault();

    var url = $(this).attr('href');
    var id = getUrlParameter(url, 'id');
    var type = getUrlParameter(url, 'type');

    $.ajax({
        url: url,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            id: id,
            type: type
        },
        success: function (response) {
            Snackbar.show({ 
                text: 'File removed successfully.', 
                pos: 'bottom-right', 
                backgroundColor: '#28a745', 
                actionTextColor: '#fff' 
            });

            // Clear image preview and remove the icon
            $('.login_register_image_preview').attr('src', '');
            $('.remove-file').remove();
        },
        error: function (xhr) {
            Snackbar.show({ 
                text: 'Failed to remove file.', 
                pos: 'bottom-right', 
                backgroundColor: '#d32f2f', 
                actionTextColor: '#fff' 
            });
        }
    });
});

// Helper to get query string values
function getUrlParameter(url, name) {
    let results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(url);
    return results ? decodeURIComponent(results[1]) : null;
}


</script>
