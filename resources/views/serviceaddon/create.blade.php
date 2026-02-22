<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>

                            <a href="{{ route('serviceaddon.index') }}" class=" float-end btn btn-sm btn-primary"><i
                                    class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                   
                        {{ html()->form('POST', route('serviceaddon.store'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('serviceaddon')->open() }}
                        {{ html()->hidden('id', $serviceaddon->id ?? null) }}
                        @include('partials._language_toggale')
                        @foreach($language_array as $language)
                            <div id="form-language-{{ $language['id'] }}" class="language-form" style="display: {{ $language['id'] == app()->getLocale() ? 'block' : 'none' }};">
                                <div class="row">
                                @foreach(['name' => __('messages.name')] as $field => $label)
                                <div class="form-group col-md-{{ $field === 'name' ? '4' : '12' }}">
                                    {{ html()->label($label . ($field === 'name' ? ' <span class="text-danger">*</span>' : ''), $field)->class('form-control-label language-label') }}

                                    @php
                                        $value = $language['id'] == 'en' 
                                            ? $serviceaddon ? $serviceaddon->translate($field, 'en') : '' 
                                            : ($serviceaddon ? $serviceaddon->translate($field, $language['id']) : '');
                                        $name = $language['id'] == 'en' ? $field : "translations[{$language['id']}][$field]";
                                    @endphp

                                    @if($field === 'name')
                                        {{ html()->text($name, $value)
                                            ->placeholder($label)
                                            ->class('form-control')
                                            ->attribute('title', 'Please enter alphabetic characters and spaces only')
                                            ->attribute('data-required', 'true') }}
                                
                                    @endif

                                    <small class="help-block with-errors text-danger"></small>
                                </div>
                                @endforeach
                                @php
                                    if ($auth_user->user_type == 'admin' || $auth_user->user_type == 'demo_admin') {
                                        $route = route('ajax-list', ['type' => 'service-list', 'language_id' => $language['id']]);
                                    } else {
                                        $route = route('ajax-list', [
                                            'type' => 'service-list',
                                            'provider_id' => $auth_user->id, 
                                            'language_id' => $language['id']
                                        ]);
                                    }
                                @endphp
                                <!-- service Selection -->
                                <div class="form-group col-md-4">
                                    {{ html()->label(__('messages.select_name', ['select' => __('messages.service')]) . ' <span class="text-danger">*</span>', 'service_id')->class('form-control-label') }}
                                    <select name="service_id"
                                            id="service_id_{{ $language['id'] }}"
                                            class="form-select select2js-service"
                                            data-select2-type="service"
                                            data-selected-id="{{ optional($serviceaddon->service)->id ?? '' }}"
                                            data-language-id="{{ $language['id'] }}"
                                            data-ajax--url="{{ $route }}"
                                            data-placeholder="{{ __('messages.select_name', ['select' => __('messages.service')]) }}" >
                                    </select>
                                    <small class="help-block with-errors text-danger"></small>
                                </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="row">
                            <!-- <div class="form-group col-md-4">
                                {{ html()->label(__('messages.name') . ' <span class="text-danger">*</span>', 'name')->class('form-control-label') }}
                                {{ html()->text('name', $serviceaddon->name)->placeholder(__('messages.name'))->class('form-control')->required()->attribute('title', 'Please enter alphabetic characters and spaces only') }}
                                <small class="help-block with-errors text-danger"></small>
                            </div> -->

                            <!-- <div class="form-group col-md-4">
                                {{ html()->label(__('messages.select_name', ['select' => __('messages.service')]) . ' <span class="text-danger">*</span>', 'name')->class('form-control-label') }}
                                <br />
                                @php
                                    if ($auth_user->user_type == 'admin' || $auth_user->user_type == 'demo_admin') {
                                        $route = route('ajax-list', ['type' => 'service-list']);
                                    } else {
                                        $route = route('ajax-list', [
                                            'type' => 'service-list',
                                            'provider_id' => $auth_user->id,
                                        ]);
                                    }

                                @endphp
                                {{ html()->select(
                                        'service_id',
                                        [optional($serviceaddon->service)->id => optional($serviceaddon->service)->name],
                                        optional($serviceaddon->service)->id,
                                    )->class('select2js form-group service')->id('service_id')->required()->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.service')]))->attribute('data-ajax--url', $route) }}

                            </div> -->
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.price') . ' <span class="text-danger">*</span>', 'price')->class('form-control-label') }}
                                {{ html()->text('price', $serviceaddon->price)->placeholder(__('messages.price'))->class('form-control')->required()->attribute('pattern', '^\\d+(\\.\\d{1,2})?$') }}
                                <small class="help-block with-errors text-danger"></small>
                            </div>

                            <div class="form-group col-md-4">
                                {{ html()->label(trans('messages.status') . ' <span class="text-danger">*</span>', 'status')->class('form-control-label') }}
                                {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')], $serviceaddon->status)->class('form-select select2js')->required() }}
                            </div>

                            <div class="form-group col-md-4">
                                <label class="form-control-label" for="serviceaddon_image">{{ __('messages.image') }}
                                    <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" name="serviceaddon_image" class="custom-file-input"
                                        onchange="previewImage(event)" accept="image/*" required>
                                    @if ($serviceaddon && getMediaFileExit($serviceaddon, 'serviceaddon_image'))
                                        <label
                                            class="custom-file-label upload-label">{{ $serviceaddon->getFirstMedia('serviceaddon_image')->file_name }}</label>
                                    @else
                                        <label
                                            class="custom-file-label upload-label">{{ __('messages.choose_file', ['file' => __('messages.image')]) }}</label>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-2 mb-2">
                                <div class="image-preview-container">
                                    <img id="serviceaddon_image_preview"
                                        src="{{ getMediaFileExit($serviceaddon, 'serviceaddon_image') ? getSingleMedia($serviceaddon, 'serviceaddon_image') : '' }}"
                                        alt="Image preview" class="attachment-image mt-1"
                                        style="width: 150px; {{ getMediaFileExit($serviceaddon, 'serviceaddon_image') ? '' : 'display: none;' }}">
                                    <a class="text-danger remove-file" id="removeButton"
                                        onclick="removeImage(event, '{{ route('remove.file', ['id' => $serviceaddon->id, 'type' => 'serviceaddon_image']) }}')"
                                        style="{{ getMediaFileExit($serviceaddon, 'serviceaddon_image') ? 'display: inline;' : 'display: none;' }}">
                                        <i class="ri-close-circle-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary float-end')->id('saveButton') }}
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

          <script type="text/javascript">
            function previewImage(event) {
                const preview = document.getElementById('serviceaddon_image_preview');
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
                    text: "Do you want to remove the serviceaddon image?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const preview = document.getElementById('serviceaddon_image_preview');
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
                                document.querySelector('input[name="serviceaddon_image"]').value =
                                ''; // Clear the file input
                                fileLabel.textContent =
                                    '{{ __('messages.choose_file', ['file' => __('messages.image')]) }}'; // Reset the label text
                                saveButton.disabled = true; // Disable the save button
                                removeButton.style.display = 'none'; // Hide the remove button

                                // Optionally show a success message
                                Swal.fire(
                                    'Deleted!',
                                    'Your serviceaddon image has been removed.',
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
                const preview = document.getElementById('serviceaddon_image_preview');
                const fileLabel = document.querySelector('.custom-file-label');
                const saveButton = document.getElementById('saveButton');
                const removeButton = document.getElementById('removeButton');

                // Check if the image exists before removing
                if (preview.src) {
                    preview.src = '';
                    preview.style.display = 'none';
                    document.querySelector('input[name="serviceaddon_image"]').value = ''; // Clear the file input
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
                var id = @json($serviceaddon->id);
                var route = "{{ route('check-image', ':id') }}";
                route = route.replace(':id', id);
                var type = 'serviceaddon';

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
                            $('input[name="serviceaddon_image"]').attr('required', 'required');
                            document.getElementById('saveButton').disabled = true; // Disable button initially
                        } else {
                            $('input[name="serviceaddon_image"]').removeAttr('required');
                            document.getElementById('saveButton').disabled = false; // Enable if there's an image
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
        </script>

</x-master-layout>
