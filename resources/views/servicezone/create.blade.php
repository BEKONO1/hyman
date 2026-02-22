<x-master-layout>

    <head>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    </head>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{__('messages.service_zone_configuration')}}</h5>
                            <a href="{{route('servicezone.index')}}" class="float-end btn btn-sm btn-primary">
                                <i class="fa fa-angle-double-left"></i> {{__('messages.back')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="fw-bold mb-3">{{__('messages.guidelines_to_create_zone')}}</h5>

                <p><strong class="text-primary">{{__('messages.Step_1')}}</strong> {{__('messages.create_zone_by_clicking_on_the_map_and_connect_the_dots_together')}}</p>
                <p><strong class="text-primary">{{__('messages.Step_2')}}</strong> {{__('messages.use_this')}}
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.6875 6.25008C14.3228 6.24955 13.9638 6.3409 13.6438 6.5157C13.5281 6.15744 13.3219 5.83509 13.0452 5.57984C12.7685 5.32458 12.4306 5.14506 12.0642 5.05864C11.6978 4.97223 11.3153 4.98183 10.9536 5.08654C10.592 5.19124 10.2636 5.3875 10 5.65633C9.69586 5.34592 9.30616 5.13313 8.88059 5.0451C8.45502 4.95708 8.01288 4.9978 7.61056 5.16209C7.20823 5.32637 6.86395 5.60676 6.62163 5.96751C6.37931 6.32825 6.24994 6.753 6.25 7.18758V8.75008H5.3125C4.73234 8.75008 4.17594 8.98054 3.7657 9.39078C3.35547 9.80102 3.125 10.3574 3.125 10.9376V11.8751C3.125 13.6984 3.84933 15.4471 5.13864 16.7364C6.42795 18.0257 8.17664 18.7501 10 18.7501C11.8234 18.7501 13.572 18.0257 14.8614 16.7364C16.1507 15.4471 16.875 13.6984 16.875 11.8751V8.43758C16.875 7.85741 16.6445 7.30102 16.2343 6.89078C15.8241 6.48054 15.2677 6.25008 14.6875 6.25008ZM15.625 11.8751C15.625 13.3669 15.0324 14.7977 13.9775 15.8526C12.9226 16.9074 11.4918 17.5001 10 17.5001C8.50816 17.5001 7.07742 16.9074 6.02252 15.8526C4.96763 14.7977 4.375 13.3669 4.375 11.8751V10.9376C4.375 10.6889 4.47377 10.4505 4.64959 10.2747C4.8254 10.0988 5.06386 10.0001 5.3125 10.0001H6.25V11.8751C6.25 12.0408 6.31585 12.1998 6.43306 12.317C6.55027 12.4342 6.70924 12.5001 6.875 12.5001C7.04076 12.5001 7.19973 12.4342 7.31694 12.317C7.43415 12.1998 7.5 12.0408 7.5 11.8751V7.18758C7.5 6.93894 7.59877 6.70048 7.77459 6.52466C7.9504 6.34885 8.18886 6.25008 8.4375 6.25008C8.68614 6.25008 8.9246 6.34885 9.10041 6.52466C9.27623 6.70048 9.375 6.93894 9.375 7.18758V9.37508C9.375 9.54084 9.44085 9.69981 9.55806 9.81702C9.67527 9.93423 9.83424 10.0001 10 10.0001C10.1658 10.0001 10.3247 9.93423 10.4419 9.81702C10.5592 9.69981 10.625 9.54084 10.625 9.37508V7.18758C10.625 6.93894 10.7238 6.70048 10.8996 6.52466C11.0754 6.34885 11.3139 6.25008 11.5625 6.25008C11.8111 6.25008 12.0496 6.34885 12.2254 6.52466C12.4012 6.70048 12.5 6.93894 12.5 7.18758V9.37508C12.5 9.54084 12.5658 9.69981 12.6831 9.81702C12.8003 9.93423 12.9592 10.0001 13.125 10.0001C13.2908 10.0001 13.4497 9.93423 13.5669 9.81702C13.6842 9.69981 13.75 9.54084 13.75 9.37508V8.43758C13.75 8.18894 13.8488 7.95048 14.0246 7.77466C14.2004 7.59885 14.4389 7.50008 14.6875 7.50008C14.9361 7.50008 15.1746 7.59885 15.3504 7.77466C15.5262 7.95048 15.625 8.18894 15.625 8.43758V11.8751Z" fill="#6C757D"/>
                    </svg>
                    {{__('messages.to_drag_the_map_and_find_the_proper_area')}}
                </p>
                <p><strong class="text-primary">{{__('messages.Step_3')}}</strong> Click this icon
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.0187 3.85621C17.7865 3.624 17.5109 3.4398 17.2075 3.31413C16.9042 3.18845 16.579 3.12377 16.2507 3.12377C15.9223 3.12377 15.5972 3.18845 15.2939 3.31413C14.9905 3.4398 14.7149 3.624 14.4827 3.85621C14.3254 4.01347 14.1898 4.19109 14.0796 4.38433L11.8749 3.78277C11.8817 3.28539 11.74 2.79729 11.4679 2.3809C11.1958 1.96451 10.8056 1.63876 10.3474 1.44532C9.88909 1.25188 9.38354 1.19954 8.89536 1.29501C8.40719 1.39047 7.95859 1.62939 7.60694 1.98121C7.23148 2.35756 6.9855 2.84361 6.9046 3.36904C6.8237 3.89446 6.91208 4.432 7.15694 4.90386L4.50538 7.29058C4.0243 6.97261 3.44821 6.83044 2.87444 6.88809C2.30066 6.94574 1.76437 7.19967 1.35616 7.60699C0.89973 8.05996 0.635395 8.67139 0.618094 9.3142C0.600792 9.95702 0.831853 10.5818 1.26326 11.0586C1.69466 11.5355 2.29323 11.8278 2.93456 11.8748C3.57589 11.9218 4.21066 11.7198 4.70694 11.3109L10.1757 15.3234C10.0007 15.7617 9.95328 16.2405 10.039 16.7046C10.1247 17.1686 10.34 17.599 10.66 17.9458C10.98 18.2927 11.3917 18.5418 11.8474 18.6646C12.303 18.7873 12.7842 18.7785 13.2351 18.6392C13.686 18.5 14.0883 18.236 14.3954 17.8777C14.7026 17.5194 14.902 17.0815 14.9708 16.6146C15.0395 16.1477 14.9747 15.6709 14.7838 15.2393C14.5929 14.8078 14.2838 14.439 13.8921 14.1757L16.0319 8.11402C16.1038 8.12027 16.1757 8.12339 16.2476 8.12339C16.7419 8.12332 17.2252 7.97668 17.6362 7.70202C18.0472 7.42736 18.3676 7.03701 18.5568 6.58031C18.746 6.1236 18.7956 5.62105 18.6993 5.13618C18.6029 4.6513 18.365 4.20588 18.0155 3.85621H18.0187ZM8.48741 2.8648C8.66228 2.69012 8.88501 2.57121 9.12745 2.52309C9.36989 2.47497 9.62115 2.49981 9.84948 2.59446C10.0778 2.68911 10.2729 2.84932 10.4102 3.05486C10.5475 3.26039 10.6208 3.50201 10.6208 3.74917C10.6208 3.99634 10.5475 4.23796 10.4102 4.44349C10.2729 4.64902 10.0778 4.80924 9.84948 4.90389C9.62115 4.99854 9.36989 5.02338 9.12745 4.97526C8.88501 4.92714 8.66228 4.80823 8.48741 4.63355C8.3714 4.51725 8.27944 4.37923 8.2168 4.22737C8.15417 4.07551 8.12207 3.9128 8.12236 3.74853C8.12265 3.58426 8.15532 3.42166 8.21849 3.27002C8.28167 3.11838 8.37411 2.98069 8.49054 2.8648H8.48741ZM2.23741 10.2593C2.12119 10.1432 2.02899 10.0054 1.96609 9.85363C1.90318 9.70188 1.8708 9.53922 1.8708 9.37496C1.8708 9.21069 1.90318 9.04803 1.96609 8.89628C2.02899 8.74453 2.12119 8.60667 2.23741 8.49058C2.41228 8.3159 2.63501 8.19699 2.87745 8.14887C3.11989 8.10075 3.37115 8.12559 3.59948 8.22024C3.8278 8.31489 4.02295 8.47511 4.16024 8.68064C4.29753 8.88617 4.3708 9.12779 4.3708 9.37496C4.3708 9.62212 4.29753 9.86374 4.16024 10.0693C4.02295 10.2748 3.8278 10.435 3.59948 10.5297C3.37115 10.6243 3.11989 10.6492 2.87745 10.601C2.63501 10.5529 2.41228 10.434 2.23741 10.2593ZM13.3819 17.1343C13.1475 17.3688 12.8295 17.5005 12.498 17.5005C12.1664 17.5005 11.8484 17.3688 11.614 17.1343C11.3795 16.8999 11.2478 16.5819 11.2478 16.2503C11.2478 15.9188 11.3795 15.6008 11.614 15.3664C11.7301 15.2503 11.8679 15.1582 12.0195 15.0954C12.1712 15.0325 12.3338 15.0002 12.498 15.0002C12.6621 15.0002 12.8247 15.0325C12.9764 15.0954C13.128 15.1582C13.2659 15.2503C13.3819 15.3664C13.498 15.4824C13.5901 15.6203C13.6529 15.7719C13.7158 15.9236C13.7481 16.0862C13.7481 16.2503C13.7481 16.4145C13.7158 16.5771C13.6529 16.7288C13.5901 16.8804C13.498 17.0182C13.3819 17.1343ZM12.714 13.7609C12.0659 13.7044 11.4213 13.9029 10.9171 14.314L5.44835 10.3015C5.58088 9.96719 5.64011 9.60832 5.62205 9.24914C5.60399 8.88996 5.50906 8.53883 5.34366 8.21949L7.996 5.83277C8.28135 6.02189 8.60249 6.1504 8.93954 6.21032C9.27658 6.27024 9.62234 6.26031 9.95539 6.18113C10.2884 6.10195 10.6017 5.95522 10.8757 5.75002C11.1497 5.54483 11.3786 5.28554 11.5483 4.98824L13.7499 5.59058C13.7439 6.00617 13.8417 6.41668 14.0343 6.78499C14.2269 7.15329 14.5083 7.46777 14.853 7.69996L12.714 13.7609ZM17.1327 6.50777C17.0184 6.63129 16.8804 6.73045 16.7268 6.79933C16.5733 6.86821 16.4074 6.90537 16.2392 6.9086C16.0709 6.91183 15.9038 6.88105 15.7477 6.81811C15.5916 6.75517 15.4499 6.66137 15.331 6.54232C15.212 6.42328 15.1183 6.28144 15.0555 6.12532C14.9927 5.9692 14.9621 5.802 14.9655 5.63376C14.9689 5.46551 15.0062 5.29968 15.0752 5.1462C15.1442 4.99272 15.2435 4.85476 15.3671 4.74058C15.6015 4.50613 15.9195 4.37442 16.2511 4.37442C16.5826 4.37442 16.9006 4.50613C17.1351 4.74058C17.3695 4.97503 17.5012 5.29301 17.5012 5.62457C17.5012 5.95612 17.3695 6.2741 17.1351 6.50855L17.1327 6.50777Z" fill="#6C757D"/>
                    </svg>
                    {{__('messages.to_start_pinning_points_on_the_map_and_connect_them_to_draw_a_zone_Minimum_3_points_required')}} <br>
                    <span class="text-muted"></span>
                </p>
            </div>
        </div>


        <!-- Full Width Card -->
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <h2 class="mb-4">{{ $pageTitle }}</h2>

                    <form method="POST" action="{{ route('servicezone.store') }}" id="servicezone" data-toggle="validator">
                        @csrf
                        @if(isset($servicezone->id))
                            <input type="hidden" name="id" value="{{ $servicezone->id }}">
                        @endif

                        <div class="form-group mb-3">
                            <label for="zone_name">{{__('messages.zone_name')}} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="zone_name" class="form-control" placeholder="{{__('messages.enter_zone_name')}}" value="{{ $servicezone->name ?? old('name') }}" required>
                            <small class="help-block with-errors text-danger"></small>
                        </div>

                        <div class="form-group mb-3">
                            <label>{{__('messages.draw_zone_on_map')}} <span class="text-danger">*</span></label>
                            <div id="map" style="height: 500px; width: 100%;"></div>
                            <input type="hidden" name="coordinates" id="coordinates" value="{{ isset($servicezone->coordinates) ? json_encode($servicezone->coordinates) : (old('coordinates') ? old('coordinates') : '[]') }}" required>
                            <small class="help-block with-errors text-danger"></small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="status">{{__('messages.status')}}</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1" {{ (isset($servicezone) && $servicezone->status == 1) ? 'selected' : '' }}>{{__('messages.active')}}</option>
                                <option value="0" {{ (isset($servicezone) && $servicezone->status == 0) ? 'selected' : '' }}>{{__('messages.inactive')}}</option>
                            </select>
                        </div>

                        @if(auth()->user()->can('service zone add'))
                            <button type="submit" class="btn btn-md btn-primary float-end">{{__('messages.save')}}</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    @section('bottom_script')
    <script>
        let map;
        let drawingManager;
        let selectedShape;
        let existingCoordinates = {!! isset($servicezone->coordinates) ? json_encode($servicezone->coordinates) : 'null' !!};

        // Form submission handling
        $('#serviceZoneForm').on('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            Swal.fire({
                title: '{{ __("messages.please_wait") }}',
                text: '{{ __("messages.saving_data") }}',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("messages.success") }}',
                            text: response.message || '{{ __("messages.servicezone_saved") }}',
                            showConfirmButton: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '{{ route("servicezone.index") }}';
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("messages.error") }}',
                            text: response.message || '{{ __("messages.something_went_wrong") }}',
                            showConfirmButton: true
                        });
                    }
                },
                error: function(xhr) {
                    let message = '{{ __("messages.something_went_wrong") }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("messages.error") }}',
                        text: message,
                        showConfirmButton: true
                    });
                }
            });
        });

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 20.5937, lng: 78.9629 },
                zoom: 5,
            });

            drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.POLYGON,
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: ['polygon']
                },
                polygonOptions: {
                    fillColor: '#555',
                    fillOpacity: 0.4,
                    strokeWeight: 2,
                    clickable: false,
                    editable: true,
                    zIndex: 1
                }
            });

            drawingManager.setMap(map);

            // If editing and coordinates exist, draw the existing polygon
            if (existingCoordinates && existingCoordinates.length > 0) {
                try {
                    console.log('Loading existing coordinates:', existingCoordinates);
                    
                    const polygon = new google.maps.Polygon({
                        paths: existingCoordinates,
                        strokeColor: '#555',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '#555',
                        fillOpacity: 0.4,
                        editable: true
                    });
                    polygon.setMap(map);
                    selectedShape = polygon;

                    // Add listeners for polygon edits
                    google.maps.event.addListener(polygon.getPath(), 'set_at', updateCoordinates);
                    google.maps.event.addListener(polygon.getPath(), 'insert_at', updateCoordinates);
                    google.maps.event.addListener(polygon.getPath(), 'remove_at', updateCoordinates);
                } catch (e) {
                    console.error('Error loading existing coordinates:', e);
                }
            }

            // Handle new polygon creation
            google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
                if (event.type === 'polygon') {
                    if (selectedShape) {
                        selectedShape.setMap(null);
                    }
                    selectedShape = event.overlay;
                    updateCoordinates();

                    // Add listeners for polygon edits
                    google.maps.event.addListener(selectedShape.getPath(), 'set_at', updateCoordinates);
                    google.maps.event.addListener(selectedShape.getPath(), 'insert_at', updateCoordinates);
                    google.maps.event.addListener(selectedShape.getPath(), 'remove_at', updateCoordinates);
                }
            });
        }

        function updateCoordinates() {
            if (selectedShape) {
                const coordinates = selectedShape.getPath().getArray().map(latlng => ({
                    lat: latlng.lat(),
                    lng: latlng.lng()
                }));
                document.getElementById('coordinates').value = JSON.stringify(coordinates);
            }
        }

        window.initMap = initMap;

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('servicezone').addEventListener('submit', function (e) {
                const coordInput = document.getElementById('coordinates');
                const coords = coordInput.value;

                try {
                    const parsedCoords = JSON.parse(coords);

                    if (!Array.isArray(parsedCoords) || parsedCoords.length < 3) {
                        e.preventDefault(); // Stop form submission
                        showCoordinateError("{{ __('messages.please_draw_zone') }}");
                    } else {
                        clearCoordinateError();
                    }

                } catch (err) {
                    e.preventDefault(); // Stop form submission
                    showCoordinateError("{{ __('messages.invalid_coordinates') }}");
                }
            });

            function showCoordinateError(message) {
                const errorBlock = document.querySelector('#coordinates ~ .help-block');
                if (errorBlock) {
                    errorBlock.textContent = message;
                }
            }

            function clearCoordinateError() {
                const errorBlock = document.querySelector('#coordinates ~ .help-block');
                if (errorBlock) {
                    errorBlock.textContent = '';
                }
            }
        });
    </script>

   <script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=drawing&callback=initMap">
</script>

    @endsection


</x-master-layout>