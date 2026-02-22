{{ html()->form('POST', route('themesetup'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open()}}

{{ html()->hidden('id', $themesetup->id ?? null )->attribute('placeholder', 'id')->class('form-control') }}
{{ html()->hidden('page', $page)->attribute('placeholder', 'id')->class('form-control') }}
<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label for="avatar" class="col-sm-3 form-control-label">{{ __('messages.logo') }}</label>
            <div class="col-sm-12">

                <div class="row">
                    <div class="col-sm-4">
                    <img src="{{ getSingleMedia($themesetup,'logo') }}" width="100"  id="logo_preview" alt="logo" class="image logo logo_preview">
                            @if($themesetup && getMediaFileExit($themesetup, 'logo'))
                                <a class="text-danger remove-file" href="{{ route('remove.file', ['id' => $themesetup->id, 'type' => 'logo']) }}"
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
                            {{ html()->file('logo')
                            ->class('custom-file-input custom-file-input-sm detail')
                            ->id('logo')
                            ->attribute('lang', 'en')
                            ->attribute('accept', 'image/*')
                            ->attribute('onchange', 'preview()')
                        }}                                @if($themesetup && getMediaFileExit($themesetup, 'logo'))
                                    <label class="custom-file-label upload-label">{{ $themesetup->getFirstMedia('logo')->file_name }}</label>
                                @else
                                    <label class="custom-file-label upload-label">{{ ('logo.png') }}</label>
                                @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="form-group">
            <label for="avatar" class="col-sm-6 form-control-label">{{ __('messages.favicon') }}</label>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-4">
                        <img src="{{ getSingleMedia($themesetup,'favicon') }}" height="30"  id="favicon_preview" alt="favicon" class="image favicon favicon_preview">
                        @if($themesetup && getMediaFileExit($themesetup, 'favicon'))
                            <a class="text-danger remove-file" href="{{ route('remove.file', ['id' => $themesetup->id, 'type' => 'favicon']) }}"
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
                            {{ html()->file('favicon')
                            ->class('custom-file-input custom-file-input-sm detail')
                            ->id('favicon')
                            ->attribute('lang', 'en')
                            ->attribute('accept', 'image/*')
                            ->attribute('onchange', 'preview()')
                        }}
                        @if($themesetup && getMediaFileExit($themesetup, 'favicon'))
                                <label class="custom-file-label upload-label">{{ $themesetup->getFirstMedia('favicon')->file_name }}</label>
                            @else
                                <label class="custom-file-label upload-label">{{ ('favicon.png') }}</label>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="avatar" class="col-sm-3 form-control-label">{{ __('messages.footer_logo') }}</label>
            <div class="col-sm-12">

                <div class="row">
                    <div class="col-sm-4">
                        <img src="{{ getSingleMedia($themesetup,'footer_logo') }}" width="100"  id="footer_logo_preview" alt="footer_logo" class="image footer_logo footer_logo_preview">
                        @if($themesetup && getMediaFileExit($themesetup, 'footer_logo'))
                            <a class="text-danger remove-file" href="{{ route('remove.file', ['id' => $themesetup->id, 'type' => 'footer_logo']) }}"
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
                            {{ html()->file('footer_logo')
                            ->class('custom-file-input custom-file-input-sm detail')
                            ->id('footer_logo')
                            ->attribute('lang', 'en')
                            ->attribute('accept', 'image/*')
                            ->attribute('onchange', 'preview()')
                        }}
                        @if($themesetup && getMediaFileExit($themesetup, 'footer_logo'))
                                <label class="custom-file-label upload-label">{{ $themesetup->getFirstMedia('footer_logo')->file_name }}</label>
                            @else
                                <label class="custom-file-label upload-label">{{ ('logo.png') }}</label>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="form-group">
            <label for="avatar" class="col-sm-6 form-control-label">{{ __('messages.loader') }}</label>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-4">
                        <img src="{{ getSingleMedia($themesetup,'loader') }}" style="height: 80px; width: auto;" id="loader_preview" alt="loader" class="image loader loader_preview">
                        @if($themesetup && getMediaFileExit($themesetup, 'loader'))
                            <a class="text-danger remove-file" href="{{ route('remove.file', ['id' => $themesetup->id, 'type' => 'loader']) }}"
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
                            {{ html()->file('loader')
                            ->class('custom-file-input custom-file-input-sm detail')
                            ->id('loader')
                            ->attribute('lang', 'en')
                            ->attribute('accept', 'image/*')
                            ->attribute('onchange', 'preview()')
                        }}
                        @if($themesetup && getMediaFileExit($themesetup, 'loader'))
                                <label class="custom-file-label upload-label">{{ $themesetup->getFirstMedia('loader')->file_name }}</label>
                            @else
                                <label class="custom-file-label upload-label">{{ ('loader.gif') }}</label>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>

    <!-- color setting section -->
    <div class="col-lg-12 mt-4">
        <h4>{{ __('messages.color_settings') }}</h4>
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="form-control-label">{{ __('messages.primary_color') }}</label>
                    <div class="d-flex align-items-center">
                        <input type="color" name="primary_color" class="form-control"
                            value="{{ isset($themesetup) ? json_decode($themesetup->value)->primary_color ?? '#000000' : '#000000' }}" style="padding:0.3rem">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <div class="col-md-offset-3 col-sm-12 ">
                {{ html()->submit(__('messages.save'))
                    ->class('btn btn-md btn-primary float-md-end submit_section1')
                    ->attribute('onclick', 'saveThemeColors(event)') }}
            </div>
        </div>
     </div>
</div>
{{ html()->form()->close() }}
<script>
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
            validateImageDimensions(input, className).then(isValid => {
                if (isValid) {
                    reader.onload = function(e){
                        $(document).find('img.'+className).attr('src', e.target.result);
                        $(document).find("label."+className).text((input.files[0].name));
                    }
                    
                    reader.readAsDataURL(input.files[0]);
                }
            });
        }
    }
    $(document).ready(function (){
        $('.select2js').select2();
        $(document).on('change','#logo',function(){
            readURL(this,'logo');
        });
        $(document).on('change','#favicon',function(){
            readURL(this,'favicon');
        });
        $(document).on('change','#footer_logo',function(){
            readURL(this,'footer_logo');
        });
        $(document).on('change','#loader',function(){
            readURL(this,'loader');
        });

        // Apply saved colors from localStorage
        applySavedColors();

        // If there's a primary color value in the form, update localStorage
        const primaryColorInput = document.querySelector('input[name="primary_color"]');
        if (primaryColorInput && primaryColorInput.value) {
            localStorage.setItem('primaryColor', primaryColorInput.value);
            updateThemeColors();
        }
    })
    function preview() {
    var input = event.target;
    var previewImage;
    if (input.name === 'logo') {
        previewImage = logo;
    } else if (input.name === 'favicon') {
        previewImage = favicon;
    } else if (input.name === 'footer_logo') {
        previewImage = footer_logo;
    } else if (input.name === 'loader') {
        previewImage = loader;
    }
    previewImage.src = URL.createObjectURL(input.files[0]);
    var fileName = input.files[0].name;
    var label = $(input).closest('.custom-file').find('.custom-file-label');
    label.text(fileName);
}

function updateThemeColors() {
    const root = document.documentElement;

    // Get primary color value - first try from input, then fallback to localStorage
    const primaryColorInput = document.querySelector('input[name="primary_color"]');
    const primaryColor = primaryColorInput ? primaryColorInput.value : localStorage.getItem('primaryColor');

    if (!primaryColor) return; // Exit if no color is found

    // Convert HEX to RGB
    const rgbPrimaryColor = hexToRgb(primaryColor);

    // Update CSS variables
    root.style.setProperty('--bs-primary', primaryColor);
    root.style.setProperty('--bs-primary-rgb', rgbPrimaryColor);

    const primaryBgSubtle = hexToRgbaWithAlpha(primaryColor, 0.09); // Adding 0.09 opacity
    root.style.setProperty('--bs-primary-bg-subtle', primaryBgSubtle);

    // Set border subtle color to be the same as background subtle
    root.style.setProperty('--bs-primary-border-subtle', 'var(--bs-primary-bg-subtle)');

    // Optionally set hover and active colors
    const primaryHover = hexToRgbaWithAlpha(primaryColor, 0.75);
    root.style.setProperty('--bs-primary-hover-bg', primaryHover);
    root.style.setProperty('--bs-primary-hover-border', primaryHover);

    const primaryActive = hexToRgbaWithAlpha(primaryColor, 0.75);
    root.style.setProperty('--bs-primary-active-bg', primaryActive);
    root.style.setProperty('--bs-primary-active-border', primaryActive);

    // Update Bootstrap elements with new hover/active states
    updateColorClasses(primaryColor, rgbPrimaryColor, primaryBgSubtle, primaryHover, primaryActive);
}

function applySavedColors() {
    const root = document.documentElement;
    const savedPrimaryColor = localStorage.getItem('primaryColor');

    if (savedPrimaryColor) {
        const rgbPrimaryColor = hexToRgb(savedPrimaryColor);

        root.style.setProperty('--bs-primary', savedPrimaryColor);
        root.style.setProperty('--bs-primary-rgb', rgbPrimaryColor);

        const primaryBgSubtle = hexToRgbaWithAlpha(savedPrimaryColor, 0.09);
        root.style.setProperty('--bs-primary-bg-subtle', primaryBgSubtle);
        root.style.setProperty('--bs-primary-border-subtle', 'var(--bs-primary-bg-subtle)');

        const primaryHover = hexToRgbaWithAlpha(savedPrimaryColor, 0.75);
        root.style.setProperty('--bs-primary-hover-bg', primaryHover);
        root.style.setProperty('--bs-primary-hover-border', primaryHover);

        const primaryActive = hexToRgbaWithAlpha(savedPrimaryColor, 0.75);
        root.style.setProperty('--bs-primary-active-bg', primaryActive);
        root.style.setProperty('--bs-primary-active-border', primaryActive);
    }
}

// Convert HEX color to RGB
function hexToRgb(hex) {
    hex = hex.replace(/^#/, '');
    const bigint = parseInt(hex, 16);
    const r = (bigint >> 16) & 255;
    const g = (bigint >> 8) & 255;
    const b = bigint & 255;
    return `${r}, ${g}, ${b}`;
}

// Convert HEX color to RGBA (with alpha)
function hexToRgbaWithAlpha(hex, alpha) {
    const color = hex.replace(/^#/, '');
    const r = parseInt(color.substring(0, 2), 16);
    const g = parseInt(color.substring(2, 4), 16);
    const b = parseInt(color.substring(4, 6), 16);
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

// Add event listeners for color changes
document.querySelectorAll('input[type="color"]').forEach(input => {
    input.addEventListener('input', updateThemeColors);
    input.addEventListener('change', updateThemeColors);
});

// Initialize colors on page load
document.addEventListener('DOMContentLoaded', () => {
    applySavedColors(); // Apply saved colors from localStorage
    updateThemeColors(); // Update Bootstrap classes
});

function saveThemeColors(event) {
    // Get the primary color value
    const primaryColor = document.querySelector('input[name="primary_color"]').value;

    // Store in localStorage
    localStorage.setItem('primaryColor', primaryColor);

    // Apply the colors immediately
    updateThemeColors();
}

function updateColorClasses(primaryColor, rgbPrimaryColor, primaryBgSubtle, primaryHover, primaryActive) {
    // Implementation depends on what you need to update with these colors
    // This is just a placeholder function to prevent errors
    // console.log('Updating color classes with new colors');
}

// Add this function after the existing script tag
function validateImageDimensions(input, className) {
    return new Promise((resolve, reject) => {
        if (input.files && input.files[0]) {
            const img = new Image();
            img.onload = function() {
                const width = this.width;
                const height = this.height;
                
                if (className === 'logo' && (width > 40 || height > 40)) {
                    Snackbar.show({
                        text: 'Logo image dimensions must be 40x40 pixels or smaller',
                        pos: 'bottom-right',
                        backgroundColor: '#d32f2f',
                        actionTextColor: '#fff'
                    });
                    $(input).val("");
                    resolve(false);
                } else {
                    resolve(true);
                }
            };
            img.src = URL.createObjectURL(input.files[0]);
        }
    });
}
</script>

