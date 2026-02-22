<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                            @if($auth_user->can('slider list'))
                            <a href="{{ route('slider.index') }}" class=" float-end btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('slider.store'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('slider')->open()}}
                        {{ html()->hidden('id',$sliderdata->id ?? null) }}
                        <div class="row">
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.title') . ' <span class="text-danger">*</span>', 'title')->class('form-control-label') }}
                                {{ html()->text('title',$sliderdata->title)->placeholder(__('messages.title'))->class('form-control')->required()}}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.select_name', ['select' => __('messages.service')]) . ' <span class="text-danger">*</span>', 'type_id')->class('form-control-label') }}
                                <br />
                                {{ html()->select('type_id', [optional($sliderdata->service)->id => optional($sliderdata->service)->name], optional($sliderdata->service)->id)
                                    ->class('select2js form-group service')
                                    ->required()
                                    ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.service')]))
                                    ->attribute('data-ajax--url', route('ajax-list', ['type' => 'service-list']))}}
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(trans('messages.status') . ' <span class="text-danger">*</span>', 'status')->class('form-control-label') }}
                                {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')], $sliderdata->status)->class('form-select select2js')->required()}}
                            </div>
                    
                            <div class="form-group col-md-4">
                                <label class="form-control-label" for="slider_image">{{ __('messages.image') }} <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" name="slider_image" class="custom-file-input" onchange="previewImage(event)" accept="image/*" required>
                                    @if($sliderdata && getMediaFileExit($sliderdata, 'slider_image'))
                                        <label class="custom-file-label upload-label">{{ $sliderdata->getFirstMedia('slider_image')->file_name }}</label>
                                    @else
                                        <label class="custom-file-label upload-label">{{ __('messages.choose_file', ['file' => __('messages.image')]) }}</label>
                                    @endif
                                </div>
                            </div>
                    
                            <div class="col-md-2 mb-2">
                                <div class="image-preview-container position-relative">
                                    <img id="slider_image_preview" src="{{ getMediaFileExit($sliderdata, 'slider_image') ? getSingleMedia($sliderdata, 'slider_image') : '' }}" alt="Image preview" class="attachment-image mt-1" style="width: 150px; {{ getMediaFileExit($sliderdata, 'slider_image') ? '' : 'display: none;' }}">
                                    <a class="text-danger remove-file" id="removeButton" onclick="removeImage(event, '{{ route('remove.file', ['id' => $sliderdata->id, 'type' => 'slider_image']) }}')" style="{{ getMediaFileExit($sliderdata, 'slider_image') ? 'display: inline;' : 'display: none;' }}">
                                        <i class="ri-close-circle-line"></i>
                                    </a>
                                </div>
                            </div>
                    
                            <div class="form-group col-md-12">
                                {{ html()->label(trans('messages.description'), 'description')->class('form-control-label') }}
                                {{ html()->textarea('description', $sliderdata->description)->class('form-control textarea')->rows(3)->placeholder(__('messages.description'))}}
                            </div>
                        </div>
                    
                        {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary float-end')->id('saveButton')}}
                        {{ html()->form()->close() }}
                    </div>                    
                </div>
            </div>
        </div>
    </div>

    @section('bottom_script')


    <script type="text/javascript">
        function previewImage(event) {
            const preview = document.getElementById('slider_image_preview');
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
                text: "Do you want to remove the slider image?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    const preview = document.getElementById('slider_image_preview');
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
                            document.querySelector('input[name="slider_image"]').value = ''; // Clear the file input
                            fileLabel.textContent = '{{ __('messages.choose_file', ['file' => __('messages.image')]) }}'; // Reset the label text
                            saveButton.disabled = true; // Disable the save button
                            removeButton.style.display = 'none'; // Hide the remove button

                            // Optionally show a success message
                            Swal.fire(
                                'Deleted!',
                                'Your slider image has been removed.',
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

        document.addEventListener('DOMContentLoaded', function() {
            checkImage();
        });

        function checkImage() {
            var id = @json($sliderdata->id);
            var route = "{{ route('check-image', ':id') }}";
            route = route.replace(':id', id);
            var type = 'slider';

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
                        $('input[name="slider_image"]').attr('required', 'required');
                        document.getElementById('saveButton').disabled = true; // Disable button initially
                    } else {
                        $('input[name="slider_image"]').removeAttr('required');
                        document.getElementById('saveButton').disabled = false; // Enable if there's an image
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
    </script>
    @endsection
</x-master-layout>