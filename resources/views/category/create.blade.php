<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                            @if ($auth_user->can('category list'))
                                <a href="{{ route('category.index') }}" class=" float-end btn btn-sm btn-primary"><i
                                        class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('category.store'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('category-form')->open() }}
                        {{ html()->hidden('id', $categorydata->id ?? null) }}

                        @include('partials._language_toggale')

                        <!-- Loop through all languages -->
                        @foreach ($language_array as $language)
                            <div id="form-language-{{ $language['id'] }}" class="language-form"
                                style="display: {{ $language['id'] == app()->getLocale() ? 'block' : 'none' }};">
                                <div class="row">
                                    @foreach (['name' => __('messages.name'), 'description' => __('messages.description')] as $field => $label)
                                        <div class="form-group col-md-{{ $field === 'name' ? '4' : '12' }}">
                                            {{ html()->label($label . ($field === 'name' ? ' <span class="text-danger">*</span>' : ''), $field)->class('form-control-label language-label') }}

                                            @php
                                                $value =
                                                    $language['id'] == 'en'
                                                        ? ($categorydata
                                                            ? $categorydata->$field
                                                            : '')
                                                        : ($categorydata
                                                            ? $categorydata->translate($field, $language['id'])
                                                            : '');
                                                $name =
                                                    $language['id'] == 'en'
                                                        ? $field
                                                        : "translations[{$language['id']}][$field]";
                                            @endphp

                                            @if ($field === 'name')
                                                {{ html()->text($name, $value)->placeholder($label)->class('form-control')->attribute('title', 'Please enter alphabetic characters and spaces only')->attribute('data-required', 'true') }}
                                            @else
                                                {{ html()->textarea($name, $value)->class('form-control textarea description-textarea')->rows(3)->placeholder($label)->attribute('maxlength', 250)->attribute('data-lang', $language['id']) }}

                                                <small class="text-muted d-block">
                                                    <span id="desc-count-{{ $language['id'] }}">0</span>/250
                                                </small>
                                            @endif

                                            <small class="help-block with-errors text-danger"></small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <!-- Image Field -->
                        <div class="form-group col-md-4">
                            <label class="form-control-label" for="category_image">{{ __('messages.image') }} <span
                                    class="text-danger">*</span></label>
                            <div class="custom-file">

                                <input type="file" name="category_image" class="custom-file-input"
                                    id="category_image" accept=".jpg,.jpeg,.png"
                                    onchange="previewImage(event)"
                                    {{ is_null($categorydata->id) ? 'required' : '' }}>

                                @if ($categorydata && getMediaFileExit($categorydata, 'category_image'))
                                    <label
                                        class="custom-file-label upload-label">{{ $categorydata->getFirstMedia('category_image')->file_name }}</label>
                                @else
                                    <label
                                        class="custom-file-label upload-label">{{ __('messages.choose_file', ['file' => __('messages.image')]) }}</label>
                                @endif
                            </div>
                            <small class="help-block with-errors text-danger" id="category_image_error"></small>
                            <small class="text-muted d-block mt-1">{{ __('messages.only_jpg_png_jpeg_allowed') }}</small> <!-- Note for allowed image types -->
                        </div>

                        <img id="category_image_preview" src="" width="150px" />

                        <!-- Status Field -->
                        <div class="form-group col-md-4">
                            {{ html()->label(trans('messages.status') . ' <span class="text-danger">*</span>', 'status')->class('form-control-label') }}
                            {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')], $categorydata->status)->id('role')->class('form-select select2js')->required() }}
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <div class="custom-control custom-switch">
                                    {{ html()->checkbox('is_featured', $categorydata->is_featured)->class('custom-control-input')->id('is_featured') }}
                                    <label class="custom-control-label" for="is_featured">{{ __('messages.set_as_featured') }}</label>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="custom-control custom-switch">
                                    {{ html()->checkbox('seo_enabled', $categorydata->seo_enabled)->class('custom-control-input')->id('seo_enabled') }}
                                    <label class="custom-control-label" for="seo_enabled">{{ __('messages.set_seo') }}</label>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Fields Section for this language -->
                        <div class="row mt-4" id="seo_fields_section">
                            <div class="col-12">
                                <h5 class="fw-bold mb-3">{{ __('messages.seo_fields') }}</h5>
                            </div>
                            <!-- First Row: SEO Image (left) and Meta Title (right) -->
                            <div class="row">
                                <div class="form-group col-md-6 mb-3">
                                    {{ html()->label(__('messages.seo_image'), 'seo_image')->class('form-control-label language-label') }}
                                    <div class="custom-file">
                                    @php
                                        $seoImageUrl = (isset($categorydata->id) && getMediaFileExit($categorydata, 'seo_image')) ? $categorydata->getFirstMediaUrl('seo_image') : '';
                                        $seoImageHas = !empty($seoImageUrl) ? '1' : '0';
                                    @endphp
                                    <input type="file" name="seo_image" class="custom-file-input" id="seo_image"
                                        accept=".jpg,.jpeg,.png"
                                        onchange="previewSeoImage(event)"
                                        data-has-image="{{ $seoImageHas }}">
                                        <label class="custom-file-label upload-label" for="seo_image">
                                            {{ __('messages.choose_file', ['file' => __('messages.image')]) }}
                                        </label>
                                    </div>
                                    <small class="help-block with-errors text-danger" id="seo_image_error"></small>
                                    <small class="text-muted d-block mt-1">{{ __('messages.only_jpg_png_jpeg_allowed') }}</small> <!-- Note for allowed image types -->
                                    <!-- @php
                                        $seoImageUrl = (isset($categorydata->id) && getMediaFileExit($categorydata, 'seo_image')) ? $categorydata->getFirstMediaUrl('seo_image') : '';
                                    @endphp -->
                                    <img id="seo_image_preview" src="{{ $seoImageUrl }}" alt="SEO Image Preview" style="max-width: 100px; margin-top: 10px; @if(empty($seoImageUrl)) display: none; @endif" />
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        {{ html()->label(__('messages.meta_title'), 'meta_title')->class('form-control-label language-label') }}
                                        <span class="text-muted" style="font-size: 12px;">
                                            <span id="meta-title-count">{{ strlen($categorydata->meta_title ?? '') }}</span>/100
                                        </span>
                                    </div>
                                    @php
                                        $metaTitleValue = isset($categorydata->id) ? $categorydata->meta_title : '';
                                    @endphp
                                    {{ html()->text('meta_title', $metaTitleValue)
                                        ->placeholder(__('messages.enter_meta_title'))
                                        ->class('form-control')
                                        ->attribute('maxlength', 100)
                                        ->attribute('id', 'meta_title') }}
                                    <small class="help-block with-errors text-danger"></small>
                                </div>
                            </div>
                            <!-- Second Row: Meta Keywords (full width, original markup) -->
                             <div class="row">
                            <div class="form-group col-md-6 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    {{ html()->label(__('messages.meta_keywords'), 'meta_keywords')->class('form-control-label language-label') }}
                                </div>
                                @php
                                    $metaKeywordsValue = isset($categorydata->id) ? (is_array($categorydata->meta_keywords) ? implode(',', $categorydata->meta_keywords) : $categorydata->meta_keywords) : '';
                                @endphp
                                <input id="meta_keywords" class="w-100" name="meta_keywords" value="{{ $metaKeywordsValue }}" placeholder="{{ __('messages.type_and_press_enter') }}" />
                                <br />
                                <small class="text-muted">{{ __('messages.type_and_press_enter') }}</small>
                            </div>
                            <div class="col-md-6 d-none"></div>
                            </div>
                            <!-- Third Row: Meta Description (full width) -->
                            <div class="form-group col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    {{ html()->label(__('messages.meta_description'), 'meta_description')->class('form-control-label language-label') }}
                                    <span class="text-muted" style="font-size: 12px;">
                                        <span id="meta-desc-count">{{ strlen($categorydata->meta_description ?? '') }}</span>/200
                                    </span>
                                </div>
                                @php
                                    $metaDescValue = isset($categorydata->id) ? $categorydata->meta_description : '';
                                @endphp
                                {{ html()->textarea('meta_description', $metaDescValue)
                                    ->placeholder(__('messages.enter_meta_description'))
                                    ->class('form-control flex-grow-1')
                                    ->style('min-height: 120px; resize: vertical;')
                                    ->rows(4)
                                    ->attribute('maxlength', 200)
                                    ->attribute('id', 'meta_description') }}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                        </div>

                        {{ html()->submit(trans('messages.save'))
                        ->class('btn btn-md btn-primary float-end')
                        ->attribute('onclick', 'return checkData()')
                        ->id('saveButton') }}
                     {{ html()->form()->close() }}

                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('bottom_script')
    
        <script type="text/javascript">
            function previewSeoImage(event) {
                const preview = document.getElementById('seo_image_preview');
                const file = event.target.files[0];
                const seoImageInput = document.getElementById('seo_image');

                if (preview && file) {
                    preview.src = URL.createObjectURL(file);
                    preview.style.display = 'block';
                    // Update the data-has-image attribute to indicate a new file is selected
                    seoImageInput.setAttribute('data-has-image', '1');
                } else if (preview && !file) {
                    // If no file is selected, hide the preview
                    preview.style.display = 'none';
                    seoImageInput.setAttribute('data-has-image', '0');
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Initialize description character counters
                document.querySelectorAll('.description-textarea').forEach(function(textarea) {
                    const lang = textarea.dataset.lang;
                    const counter = document.getElementById('desc-count-' + lang);

                    function updateCount() {
                        const currentLength = textarea.value.length;
                        counter.textContent = currentLength;
                    }

                    textarea.addEventListener('input', updateCount);

                    // Initial count on page load
                    updateCount();
                });
            });

            function previewImage(event) {
                const preview = document.getElementById('category_image_preview');
                const fileLabel = document.querySelector('.custom-file-label');
                const saveButton = document.getElementById('saveButton');
                const removeButton = document.getElementById('removeButton');

                preview.src = URL.createObjectURL(event.target.files[0]);
                preview.style.display = 'block'; // Show the image
                fileLabel.textContent = event.target.files[0].name; // Update label with the file name

                // Show the remove button and enable the save button
                $('#removeButton').removeClass('d-none');
                saveButton.disabled = false;
            }

            function removeImage(event, removeUrl) {
                event.preventDefault(); // Prevent default link behavior

                // SweetAlert confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to remove the category image?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const preview = document.getElementById('category_image_preview');
                        const fileLabel = document.querySelector('.custom-file-label');
                        const saveButton = document.getElementById('saveButton');
                        const removeButton = document.getElementById('removeButton');

                        // AJAX request to remove the media file
                        $.ajax({
                            url: removeUrl,
                            type: 'POST',
                            success: function(result) {
                                // Handle success
                                preview.src = '';
                                preview.style.display = 'none';
                                document.querySelector('input[name="category_image"]').value =
                                ''; // Clear the file input
                                fileLabel.textContent =
                                    '{{ __('messages.choose_file', ['file' => __('messages.image')]) }}'; 
                                saveButton.disabled = true; // Disable the save button
                                $('#removeButton').addClass('d-none'); 

                                // Optionally show a success message
                                Swal.fire(
                                    'Deleted!',
                                    'Your category image has been removed.',
                                    'success'
                                );
                            },
                            error: function(xhr, status, error) {
                                console.error('Error removing media file:', error);
                            }
                        });
                    }
                });
            }

            function removeLocalImage() {
                const preview = document.getElementById('category_image_preview');
                const fileLabel = document.querySelector('.custom-file-label');
                const saveButton = document.getElementById('saveButton');
                const removeButton = document.getElementById('removeButton');

                // Check if the image exists before removing
                if (preview.src) {
                    preview.src = '';
                    preview.style.display = 'none';
                    document.querySelector('input[name="category_image"]').value = ''; // Clear the file input
                    fileLabel.textContent =
                    '{{ __('messages.choose_file', ['file' => __('messages.image')]) }}'; // Reset the label text
                    saveButton.disabled = true; // Disable the save button
                    $('#removeButton').addClass('d-none'); // Hide the remove button
                }
            }

            function removeImage(event, removeUrl) {
                event.preventDefault(); // Prevent default link behavior

                // SweetAlert confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to remove the category image?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const preview = document.getElementById('category_image_preview');
                        const fileLabel = document.querySelector('.custom-file-label');
                        const saveButton = document.getElementById('saveButton');
                        const removeButton = document.querySelector('.remove-button'); // Get the remove button

                        // AJAX request to remove the media file
                        $.ajax({
                            url: removeUrl,
                            type: 'POST',
                            success: function(result) {
                                // Handle success, e.g., show a success message
                                preview.src = '';
                                preview.style.display = 'none';
                                document.querySelector('input[name="category_image"]').value =
                                ''; // Clear the file input
                                fileLabel.textContent =
                                    '{{ __('messages.choose_file', ['file' => __('messages.image')]) }}'; // Reset the label text
                                saveButton.disabled = true; // Disable the save button
                                // removeButton.style.display = 'none'; // Hide the remove button
                                $('#removeButton').addClass('d-none');
                                // Optionally show a success message
                                Swal.fire(
                                    'Deleted!',
                                    'Your category image has been removed.',
                                    'success'
                                );
                            },
                            error: function(xhr, status, error) {
                                console.error('Error removing media file:', error);
                            }
                        });
                    }
                });
            }





            function removeLocalImage(event) {
                const preview = document.getElementById('category_image_preview');
                const fileLabel = document.querySelector('.custom-file-label');
                const saveButton = document.getElementById('saveButton');
                const removeButton = document.querySelector('.remove-button'); // Get the remove button

                preview.src = '';
                preview.style.display = 'none';
                document.querySelector('input[name="category_image"]').value = ''; // Clear the file input
                fileLabel.textContent =
                '{{ __('messages.choose_file', ['file' => __('messages.image')]) }}'; // Reset the label text

                // Disable save button if image is required and not present
                saveButton.disabled = true;

                // Hide the remove button
                $('#removeButton').addClass('d-none');
            }



            document.addEventListener('DOMContentLoaded', function() {
                checkImage();
            });

            function checkImage() {
                var id = @json($categorydata->id);
                var route = "{{ route('check-image', ':id') }}";
                route = route.replace(':id', id);
                var type = 'category';

                $.ajax({
                    url: route,
                    type: 'GET',
                    data: {
                        type: type,
                    },
                    success: function(result) {
                        var attachments = result.results;
                        var attachmentsCount = Object.keys(attachments).length;
                        if (attachmentsCount == 0) {
                            $('input[name="category_image"]').attr('required', 'required');
                            document.getElementById('saveButton').disabled = true; // Disable button initially
                        } else {
                            $('input[name="category_image"]').removeAttr('required');
                            document.getElementById('saveButton').disabled = false; // Enable if there's an image
                            $('#removeButton').removeClass('d-none');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
        </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var input = document.querySelector('input[name=meta_keywords]');
        if (input) {
            new Tagify(input, {
                delimiters: ",",
                whitelist: [],
                dropdown: { enabled: 0 },
                originalInputValueFormat: valuesArr => JSON.stringify(valuesArr.map(item => item.value))
            });
        }

        // SEO Enable/Disable Switch functionality
        var seoEnabledSwitch = document.getElementById('seo_enabled');
        var seoFieldsSection = document.getElementById('seo_fields_section');
        var metaTitle = document.getElementById('meta_title');
        var metaTitleCount = document.getElementById('meta-title-count');
        var metaDesc = document.getElementById('meta_description');
        var metaDescCount = document.getElementById('meta-desc-count');
        var metaKeywords = document.getElementById('meta_keywords');
        var seoImage = document.querySelector('input[name="seo_image"]');

        function toggleSeoFields() {
            if (seoEnabledSwitch.checked) {
                seoFieldsSection.style.display = 'block';
                // Do not restore old data, keep fields as is (empty if just toggled on)
            } else {
                seoFieldsSection.style.display = 'none';
                // Clear SEO fields when disabling
                if (metaTitle) {
                    metaTitle.value = '';
                    if (metaTitleCount) metaTitleCount.textContent = '0';
                }
                if (metaDesc) {
                    metaDesc.value = '';
                    if (metaDescCount) metaDescCount.textContent = '0';
                }
                if (metaKeywords) {
                    metaKeywords.value = '';
                    if (metaKeywords.tagify) metaKeywords.tagify.removeAllTags();
                }
                if (seoImage) {
                    seoImage.value = '';
                    var seoImagePreview = document.getElementById('seo_image_preview');
                    if (seoImagePreview) {
                        seoImagePreview.src = '';
                        seoImagePreview.style.display = 'none';
                    }
                }
            }
        }

        // Initial state: show/hide and populate fields based on backend data
        if (seoEnabledSwitch) {
            if (seoEnabledSwitch.checked) {
                seoFieldsSection.style.display = 'block';
                // The Blade template will have already populated the fields with $categorydata values
            } else {
                seoFieldsSection.style.display = 'none';
                // Clear fields (in case of browser autofill)
                if (metaTitle) metaTitle.value = '';
                if (metaDesc) metaDesc.value = '';
                if (metaKeywords) {
                    metaKeywords.value = '';
                    if (metaKeywords.tagify) metaKeywords.tagify.removeAllTags();
                }
                if (seoImage) {
                    seoImage.value = '';
                    var seoImagePreview = document.getElementById('seo_image_preview');
                    if (seoImagePreview) {
                        seoImagePreview.src = '';
                        seoImagePreview.style.display = 'none';
                    }
                }
                if (metaTitleCount) metaTitleCount.textContent = '0';
                if (metaDescCount) metaDescCount.textContent = '0';
            }
            // Add event listener
            seoEnabledSwitch.addEventListener('change', toggleSeoFields);
        }
    });

document.addEventListener('DOMContentLoaded', function() {
    // SEO Image validation
    const seoImageInput = document.querySelector('input[name="seo_image"]');
    const seoImageError = document.getElementById('seo_image_error');
    if (seoImageInput) {
        seoImageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) {
                    event.target.value = '';
                    seoImageError.textContent = 'Only JPG, JPEG, and PNG files are allowed.';
                    document.getElementById('seo_image_preview').style.display = 'none'; // Hide preview on error
                    seoImageInput.setAttribute('data-has-image', '0');
                } else {
                    seoImageError.textContent = '';
                }
            } else {
                seoImageInput.setAttribute('data-has-image', seoImageInput.value ? '1' : '0');
                seoImageError.textContent = '';
            }
        });
    }
    // Category Image validation
    const categoryImageInput = document.querySelector('input[name="category_image"]');
    const categoryImageError = document.getElementById('category_image_error');
    if (categoryImageInput) {
        categoryImageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) {
                    event.target.value = '';
                    categoryImageError.textContent = 'Only JPG, JPEG, and PNG files are allowed.';
                    document.getElementById('category_image_preview').style.display = 'none'; // Hide preview on error
                } else {
                    categoryImageError.textContent = '';
                }
            } else {
                categoryImageError.textContent = '';
            }
        });
    }
    // Prevent form submit if file type error exists
    const form = document.getElementById('category-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (categoryImageError.textContent || seoImageError.textContent) {
                e.preventDefault();
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // 10MB in bytes
    const MAX_SIZE = 10 * 1024 * 1024;

    // Category Image validation (10MB limit)
    const categoryImageInput = document.querySelector('input[name="category_image"]');
    if (categoryImageInput) {
        categoryImageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            const errorBlock = document.getElementById('category_image_error');
            if (file) {
                if (file.size > MAX_SIZE) {
                    event.target.value = '';
                    if (errorBlock) {
                        errorBlock.textContent = '{{ __("messages.image_size_must_be_less_than_10mb") }}';
                    } else {
                        alert('{{ __("messages.image_size_must_be_less_than_10mb") }}');
                    }
                    var preview = document.getElementById('category_image_preview');
                    if (preview) preview.style.display = 'none';
                } else {
                    if (errorBlock) errorBlock.textContent = '';
                }
            } else {
                if (errorBlock) errorBlock.textContent = '';
            }
        });
    }

    // SEO Image validation (10MB limit)
    const seoImageInput = document.querySelector('input[name="seo_image"]');
    if (seoImageInput) {
        seoImageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            const errorBlock = document.getElementById('seo_image_error');
            if (file) {
                if (file.size > MAX_SIZE) {
                    event.target.value = '';
                    if (errorBlock) {
                        errorBlock.textContent = 'Image size must be less than 10MB.';
                    } else {
                        alert('Image size must be less than 10MB.');
                    }
                    var preview = document.getElementById('seo_image_preview');
                    if (preview) preview.style.display = 'none';
                    seoImageInput.setAttribute('data-has-image', '0');
                } else {
                    if (errorBlock) errorBlock.textContent = '';
                    seoImageInput.setAttribute('data-has-image', '1');
                }
            } else {
                seoImageInput.setAttribute('data-has-image', '1');
                if (errorBlock) errorBlock.textContent = '';
                
            }
        });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Meta Title
    var metaTitle = document.getElementById('meta_title');
    var metaTitleCount = document.getElementById('meta-title-count');
    if (metaTitle && metaTitleCount) {
        function updateMetaTitleCount() {
            metaTitleCount.textContent = metaTitle.value.length;
        }
        metaTitle.addEventListener('input', updateMetaTitleCount);
        updateMetaTitleCount();
    }
    // Meta Description
    var metaDesc = document.getElementById('meta_description');
    var metaDescCount = document.getElementById('meta-desc-count');
    if (metaDesc && metaDescCount) {
        function updateMetaDescCount() {
            metaDescCount.textContent = metaDesc.value.length;
        }
        metaDesc.addEventListener('input', updateMetaDescCount);
        updateMetaDescCount();
    }

    // SEO Save Button Enable/Disable Logic
    var seoEnabledSwitch = document.getElementById('seo_enabled');
    var saveButton = document.getElementById('saveButton');
    var metaKeywords = document.getElementById('meta_keywords');
    var seoImage = document.querySelector('input[name="seo_image"]');

    function checkSeoFields() {
        if (seoEnabledSwitch && seoEnabledSwitch.checked) {
            var titleFilled = metaTitle && metaTitle.value.trim().length > 0;
            var descFilled = metaDesc && metaDesc.value.trim().length > 0;
            var keywordsFilled = metaKeywords && metaKeywords.value.trim().length > 0;
            var imageFilled = seoImage && (seoImage.files && seoImage.files.length > 0 || seoImage.getAttribute('data-has-image') === '1');
            if (titleFilled && descFilled && keywordsFilled && imageFilled) {
                saveButton.disabled = false;
            } else {
                saveButton.disabled = true;
            }
        } else {
            saveButton.disabled = false;
        }
    }

    if (seoEnabledSwitch) {
        seoEnabledSwitch.addEventListener('change', checkSeoFields);
    }
    if (metaTitle) metaTitle.addEventListener('input', checkSeoFields);
    if (metaDesc) metaDesc.addEventListener('input', checkSeoFields);
    if (metaKeywords) metaKeywords.addEventListener('input', checkSeoFields);
    if (seoImage) seoImage.addEventListener('change', checkSeoFields);
    checkSeoFields();
});
</script>
    @endsection
</x-master-layout>
