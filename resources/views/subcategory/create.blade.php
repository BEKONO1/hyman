<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                            @if ($auth_user->can('subcategory list'))
                                <a href="{{ route('subcategory.index') }}" class=" float-end btn btn-sm btn-primary"><i
                                        class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('subcategory.store'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('subcategory')->open() }}
                        {{ html()->hidden('id', $subcategory->id ?? null) }}

                        @include('partials._language_toggale')
                        @foreach ($language_array as $language)
                            <div id="form-language-{{ $language['id'] }}" class="language-form"
                                style="display: {{ $language['id'] == app()->getLocale() ? 'block' : 'none' }};">
                                <div class="row">
                                    @foreach (['name' => __('messages.name'), 'description' => __('messages.description')] as $field => $label)
                                        <div class="form-group col-md-{{ $field === 'name' ? '4' : '12' }}">
                                            {{ html()->label($label . ($field === 'name' ? ' <span class="text-danger">*</span>' : ''), $field)->class('form-control-label language-label') }}

                                            @php
                                                if ($language['id'] === 'en') {
                                                    // Use the English value for 'en' language
                                                    $value = $subcategory ? $subcategory->$field : null;
                                                } else {
                                                    // Use the translation value for other languages, or set null if not available
                                                    $value =
                                                        $subcategory && $subcategory->translate($field, $language['id'])
                                                            ? $subcategory->translate($field, $language['id'])
                                                            : null;
                                                }
                                                // Set the input name
                                                $name =
                                                    $language['id'] == 'en'
                                                        ? $field
                                                        : "translations[{$language['id']}][$field]";
                                            @endphp

                                            @if ($field === 'name')
                                                {{ html()->text($name, $value)->placeholder($label)->class('form-control')->attribute('title', 'Please enter alphabetic characters and spaces only')->attribute('data-required', 'true') }}
                                            @elseif($field === 'description')
                                                {{ html()->textarea($name, $value)->class('form-control textarea description-field')->attribute('maxlength', 250)->rows(3)->placeholder($label)->attribute('data-lang', $language['id']) }}

                                                <small class="text-muted">
                                                    <span class="char-count"
                                                        id="char-count-{{ $language['id'] }}">{{ strlen($value ?? '') }}</span>/250
                                                </small>
                                            @endif


                                            <small class="help-block with-errors text-danger"></small>
                                        </div>
                                    @endforeach

                                    <!-- Category Selection -->
                                    <div class="form-group col-md-4">
                                        {{ html()->label(__('messages.select_name', ['select' => __('messages.category')]) . ' <span class="text-danger">*</span>', 'category_id')->class('form-control-label') }}
                                        <select name="category_id" id="category_id_{{ $language['id'] }}"
                                            class="form-select select2js-category" data-select2-type="category"
                                            data-selected-id="{{ $subcategory->category_id ?? '' }}"
                                            data-language-id="{{ $language['id'] }}"
                                            data-ajax--url="{{ route('ajax-list', ['type' => 'category', 'language_id' => $language['id']]) }}"
                                            data-placeholder="{{ __('messages.select_name', ['select' => __('messages.category')]) }}">
                                        </select>
                                        <small class="help-block with-errors text-danger"></small>
                                    </div>
                                </div>
                            </div>
                        @endforeach


                        <div class="form-group col-md-4">
                            {{ html()->label(trans('messages.status') . ' <span class="text-danger">*</span>', 'status')->class('form-control-label') }}
                            {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')], $subcategory->status)->class('form-select select2js')->required() }}
                        </div>

                        <div class="form-group col-md-4">
                            <label class="form-control-label" for="subcategory_image">{{ __('messages.image') }} <span
                                    class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" name="subcategory_image" class="custom-file-input"
                                    onchange="previewImage(event)" accept="image/*" required>
                                @if ($subcategory && getMediaFileExit($subcategory, 'subcategory_image'))
                                    <label
                                        class="custom-file-label upload-label">{{ $subcategory->getFirstMedia('subcategory_image')->file_name }}</label>
                                @else
                                    <label
                                        class="custom-file-label upload-label">{{ __('messages.choose_file', ['file' => __('messages.image')]) }}</label>
                                @endif
                            </div>
                            <small id="subcategory_image_error" class="text-danger"></small> <!-- Error message container -->
                            <small class="text-muted d-block mt-1">{{ __('messages.only_jpg_png_jpeg_allowed') }}</small> <!-- Note for allowed image types -->
                        </div>

                        <div class="col-md-2 mb-2">
                            <div class="image-preview-container">
                                <img id="subcategory_image_preview"
                                    src="{{ getMediaFileExit($subcategory, 'subcategory_image') ? getSingleMedia($subcategory, 'subcategory_image') : '' }}"
                                    alt="Image preview" class="attachment-image mt-1"
                                    style="width: 150px; {{ getMediaFileExit($subcategory, 'subcategory_image') ? '' : 'display: none;' }}">
                                <a class="text-danger remove-file" id="removeButton"
                                    onclick="removeImage(event, '{{ route('remove.file', ['id' => $subcategory->id, 'type' => 'subcategory_image']) }}')"
                                    style="{{ getMediaFileExit($subcategory, 'subcategory_image') ? 'display: inline;' : 'display: none;' }}">
                                    <i class="ri-close-circle-line"></i>
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <div class="custom-control custom-switch">
                                    {{ html()->checkbox('is_featured', $subcategory->is_featured)->class('custom-control-input')->id('is_featured') }}
                                    <label class="custom-control-label" for="is_featured">{{ __('messages.set_as_featured') }}</label>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="custom-control custom-switch">
                                    {{ html()->checkbox('seo_enabled', $subcategory->seo_enabled)->class('custom-control-input')->id('seo_enabled') }}
                                    <label class="custom-control-label" for="seo_enabled">{{ __('messages.set_seo') }}</label>
                                </div>
                            </div>
                        </div>
                    
                        <div class="row mt-4" id="seo_fields_section">
                            <div class="col-12">
                                <h5 class="fw-bold mb-3">{{ __('messages.seo_fields') }}</h5>
                            </div>
                            <!-- First Row: SEO Image (left) and Meta Title (right) -->
                            <div class="row">
                                <div class="form-group col-md-6 mb-3">
                                    {{ html()->label(__('messages.seo_image'), 'seo_image')->class('form-control-label') }}
                                    <div class="custom-file">
                                    @php
                                        $seoImageUrl = (isset($subcategory->id) && getMediaFileExit($subcategory, 'seo_image')) ? $subcategory->getFirstMediaUrl('seo_image') : '';
                                        $seoImageHas = !empty($seoImageUrl) ? '1' : '0';
                                    @endphp
                                    <input type="file" name="seo_image" class="custom-file-input" id="seo_image"
                                        accept=".jpg,.jpeg,.png"
                                        onchange="previewSeoImage(event)"
                                        data-has-image="{{ $seoImageHas }}">
                                        <label class="custom-file-label upload-label">{{ __('messages.choose_file', ['file' => __('messages.seo_image')]) }}</label>
                                    </div>
                                    <small id="seo_image_error" class="text-danger"></small> <!-- Error message container -->
                                    <small class="text-muted d-block mt-1">{{ __('messages.only_jpg_png_jpeg_allowed') }}</small> <!-- Note for allowed image types -->
                                    <!-- @php
                                        $seoImageUrl = ($subcategory && getMediaFileExit($subcategory, 'seo_image')) ? $subcategory->getFirstMediaUrl('seo_image') : null;
                                    @endphp -->
                                   
                                    <img id="seo_image_preview" src="{{ $seoImageUrl }}" alt="SEO Image Preview" style="max-width: 100px; margin-top: 10px; @if(empty($seoImageUrl)) display: none; @endif" />
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        {{ html()->label(__('messages.meta_title'), 'meta_title')->class('form-control-label') }}
                                        <span class="text-muted" style="font-size: 12px;">
                                            <span id="meta-title-count">{{ strlen($subcategory->meta_title ?? '') }}</span>/100
                                        </span>
                                    </div>
                                    @php
                                        $metaTitle = isset($subcategory->id) ? $subcategory->meta_title : '';
                                    @endphp
                                    {{ html()->text('meta_title', $metaTitle ?? '')
                                        ->class('form-control')
                                        ->placeholder(__('messages.enter_meta_title'))
                                        ->attribute('maxlength', 100)
                                        ->attribute('id', 'meta_title') }}
                                </div>
                            </div>
                            <!-- Second Row: Meta Keywords (half width, with symmetry) -->
                            <div class="row">
                                <div class="form-group col-md-6 mb-3">
                                    {{ html()->label(__('messages.meta_keywords'), 'meta_keywords')->class('form-control-label') }}
                                    @php
                                        $metaKeywordsValue = isset($subcategory->id) ? (is_array($subcategory->meta_keywords) ? implode(',', $subcategory->meta_keywords) : $subcategory->meta_keywords) : '';
                                    @endphp
                                    <input id="meta_keywords" name="meta_keywords" value="{{ $metaKeywordsValue }}" placeholder="{{ __('messages.type_a_keyword_and_press_enter') }}" class="w-100" />
                                    <br />
                                    <small class="text-muted">{{ __('messages.type_a_keyword_and_press_enter') }}</small>
                                </div>
                                <div class="col-md-6 d-none"></div>
                            </div>
                            <!-- Third Row: Meta Description (full width) -->
                            <div class="form-group col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    {{ html()->label(__('messages.meta_description'), 'meta_description')->class('form-control-label') }}
                                    <span class="text-muted" style="font-size: 12px;">
                                        <span id="meta-desc-count">{{ strlen($subcategory->meta_description ?? '') }}</span>/200
                                    </span>
                                </div>
                                @php
                                    $metaDescription = isset($subcategory->id) ? $subcategory->meta_description : '';
                                @endphp
                                {{ html()->textarea('meta_description', $metaDescription ?? '')
                                    ->class('form-control flex-grow-1')
                                    ->style('min-height: 120px; resize: vertical;')
                                    ->rows(4)
                                    ->placeholder(__('messages.enter_meta_description'))
                                    ->attribute('maxlength', 200)
                                    ->attribute('id', 'meta_description') }}
                            </div>
                        </div>
                        <script>
                        function slugify(text) {
                            return text.toString().toLowerCase()
                                .replace(/\s+/g, '-')           // Replace spaces with -
                                .replace(/[^a-z0-9\-]/g, '')   // Remove all non-alphanumeric chars except -
                                .replace(/\-+/g, '-')           // Replace multiple - with single -
                                .replace(/^-+/, '')             // Trim - from start of text
                                .replace(/-+$/, '');            // Trim - from end of text
                        }
                        document.addEventListener('DOMContentLoaded', function() {
                            var nameInput = document.querySelector('input[name="name"]');
                            var slugInput = document.querySelector('input[name="slug"]');
                            if (nameInput && slugInput) {
                                nameInput.addEventListener('input', function() {
                                    slugInput.value = slugify(this.value);
                                });
                            }
                            var seoImageInput = document.getElementById('seo_image');
                            if (seoImageInput) {
                                seoImageInput.addEventListener('change', previewSeoImage);
                            }
                        });
                        function previewSeoImage(event) {
                            const preview = document.getElementById('seo_image_preview');
                            const fileLabel = document.querySelector('#seo_image').nextElementSibling;
                            if (event.target.files && event.target.files[0]) {
                                preview.src = URL.createObjectURL(event.target.files[0]);
                                preview.style.display = 'block';
                                fileLabel.textContent = event.target.files[0].name;
                            }
                        }
                        </script>
                    
                        {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary float-end')->id('saveButton') }}
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('bottom_script')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                const textareas = document.querySelectorAll('.description-field');

                textareas.forEach(textarea => {
                    const langId = textarea.getAttribute('data-lang');
                    const counter = document.getElementById('char-count-' + langId);

                    const updateCounter = () => {
                        counter.textContent = textarea.value.length;
                    };

                    // Update on load
                    updateCounter();

                    // Update on input
                    textarea.addEventListener('input', updateCounter);
                });
            });

            function previewImage(event) {
                const preview = document.getElementById('subcategory_image_preview');
                const fileLabel = document.querySelector('.custom-file-label');
                const saveButton = document.getElementById('saveButton');
                const removeButton = document.getElementById('removeButton');

                preview.src = URL.createObjectURL(event.target.files[0]);
                preview.style.display = 'block'; // Show the image
                fileLabel.textContent = event.target.files[0].name; // Update label with the file name

                // Show the remove button and enable the save button
                removeButton.style.display = 'inline';
                saveButton.disabled = false;
            }

            function removeImage(event, removeUrl) {
                event.preventDefault(); // Prevent default link behavior

                // SweetAlert confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to remove the subcategory image?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const preview = document.getElementById('subcategory_image_preview');
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
                                document.querySelector('input[name="subcategory_image"]').value =
                                ''; // Clear the file input
                                fileLabel.textContent =
                                    '{{ __('messages.choose_file', ['file' => __('messages.image')]) }}'; // Reset the label text
                                saveButton.disabled = true; // Disable the save button
                                removeButton.style.display = 'none'; // Hide the remove button

                                // Optionally show a success message
                                Swal.fire(
                                    'Deleted!',
                                    'Your subcategory image has been removed.',
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
                const preview = document.getElementById('subcategory_image_preview');
                const fileLabel = document.querySelector('.custom-file-label');
                const saveButton = document.getElementById('saveButton');
                const removeButton = document.getElementById('removeButton');

                // Check if the image exists before removing
                if (preview.src) {
                    preview.src = '';
                    preview.style.display = 'none';
                    document.querySelector('input[name="subcategory_image"]').value = ''; // Clear the file input
                    fileLabel.textContent =
                    '{{ __('messages.choose_file', ['file' => __('messages.image')]) }}'; // Reset the label text
                    saveButton.disabled = true; // Disable the save button
                    removeButton.style.display = 'none'; // Hide the remove button
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                checkImage();
            });

            function checkImage() {
                var id = @json($subcategory->id);
                var route = "{{ route('check-image', ':id') }}";
                route = route.replace(':id', id);
                var type = 'subcategory';

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
                            $('input[name="subcategory_image"]').attr('required', 'required');
                            document.getElementById('saveButton').disabled = true; // Disable button initially
                        } else {
                            $('input[name="subcategory_image"]').removeAttr('required');
                            document.getElementById('saveButton').disabled = false; // Enable if there's an image
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
        
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
                        // The Blade template will have already populated the fields with $subcategory values
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
        </script>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                var metaDesc = document.getElementById('meta_description');
                var metaDescCount = document.getElementById('meta-desc-count');
                if (metaDesc && metaDescCount) {
                    function updateMetaDescCount() {
                        metaDescCount.textContent = metaDesc.value.length;
                    }
                    metaDesc.addEventListener('input', updateMetaDescCount);
                    updateMetaDescCount(); // Initial count
                }
            });
        </script>
        <script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    // 10MB in bytes
    const MAX_SIZE = 10 * 1024 * 1024;

    // Subcategory Image validation (10MB limit)
    const subcategoryImageInput = document.querySelector('input[name="subcategory_image"]');
    if (subcategoryImageInput) {
        subcategoryImageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            const errorBlock = document.getElementById('subcategory_image_error');
            if (file) {
                if (file.size > MAX_SIZE) {
                    event.target.value = '';
                    if (errorBlock) {
                        errorBlock.textContent = '{{ __("messages.image_size_must_be_less_than_10mb") }}';
                    }
                    var preview = document.getElementById('subcategory_image_preview');
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
                        errorBlock.textContent = '{{ __("messages.image_size_must_be_less_than_10mb") }}';
                    }
                    var preview = document.getElementById('seo_image_preview');
                    if (preview) preview.style.display = 'none';
                    seoImageInput.setAttribute('data-has-image', '0');
                } else {
                    if (errorBlock) errorBlock.textContent = '';
                }
            } else {
                seoImageInput.setAttribute('data-has-image', '1');
                if (errorBlock) errorBlock.textContent = '';
            }
        });
    }
});

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

    document.addEventListener('DOMContentLoaded', function() {
        var seoEnabledSwitch = document.getElementById('seo_enabled');
        var seoFieldsSection = document.getElementById('seo_fields_section');

        function toggleSeoFields() {
            if (seoEnabledSwitch && seoFieldsSection) {
                if (seoEnabledSwitch.checked) {
                    seoFieldsSection.style.display = 'block';
                } else {
                    seoFieldsSection.style.display = 'none';
                }
            }
        }

        // Initial state
        toggleSeoFields();

        // Add event listener
        if (seoEnabledSwitch) {
            seoEnabledSwitch.addEventListener('change', toggleSeoFields);
        }
    });
    </script>
    @endsection
</x-master-layout>


