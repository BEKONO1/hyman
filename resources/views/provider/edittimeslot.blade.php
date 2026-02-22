<x-master-layout>
    <div class="container-fluid">
        @include('partials._provider')
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $providerdata->first_name . ' ' . $providerdata->last_name }} {{ $pageTitle }}</h5>
                            <a href="{{ route('provider.time-slot',['id' => $provider_id]) }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('providerslot.store'))->attribute('data-toggle', 'validator')->id('provider-form')->open() }}
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="id" id="provider-id" value="{{ $provider_id }}">

                                <!-- Day Tabs -->
                                <div class="form-group">
                                    {{ html()->label(__('messages.day').' <span class="text-danger">*</span>', 'Day')->class('form-control-label col-md-12') }}
                                    <div class="col-md-12">
                                        <ul class="nav nav-tabs pay-tabs nav-fill gap-3 tabslink" id="tab-text" role="tablist">
                                            @foreach ($slotsArray['days'] as $day)
                                                @if (isset($day))
                                                    <li class="nav-item m-0">
                                                        <a href="#{{ $day }}" class="nav-link day-link @if(strtolower($day) === strtolower($activeDay)) active @endif"
                                                           data-day="{{ $day }}" data-bs-toggle="tab">{{ ucfirst($day) }}</a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <!-- Time Slots -->
                                <div class="form-group">
                                    {{ html()->label(__('messages.time').' <span class="text-danger">*</span>', 'Time')->class('form-control-label col-md-12') }}
                                    <div class="tab-content" id="pills-tabContent-1">
                                        @foreach ($slotsArray['days'] as $day)
                                            @if (isset($day))
                                                <div class="tab-pane fade @if(strtolower($day) === strtolower($activeDay)) show active @endif day-slot" id="{{ $day }}">
                                                    <ul class="nav nav-tabs pay-tabs nav-fill tabslink gap-3 provider-slot">
                                                        @for ($hour = 0; $hour < 24; $hour++)
                                                            @php
                                                                $slotTime = sprintf('%02d:00', $hour);
                                                                $isActive = in_array($slotTime, $activeSlots[$day] ?? []);
                                                            @endphp
                                                            <li class="nav-item m-0">
                                                                <a href="javascript:void(0)"
                                                                   class="nav-link time-link slot-link @if ($isActive) active @endif"
                                                                   data-day="{{ $day }}"
                                                                   data-slot="{{ $slotTime }}">
                                                                    {{ $slotTime }}
                                                                </a>
                                                            </li>
                                                        @endfor
                                                    </ul>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                {{ html()->submit(__('messages.submit'))->class('btn btn-md btn-primary') }}
                            </div>
                        </div>
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        showActiveDaySlots();

        $('.day-link').on('click', function (e) {
            e.preventDefault();
            $('.day-link').removeClass('active');
            $(this).addClass('active');

            var selectedDay = $(this).data('day');
            $('.day-slot').removeClass('show active');
            $('#' + selectedDay).addClass('show active');
        });

        $('.time-link').on('click', function () {
            $(this).toggleClass('active');
        });

        $('#provider-form').on('submit', function (e) {
            e.preventDefault();

            let selectedSlotsByDay = {};

            $('.slot-link.active').each(function () {
                const day = $(this).data('day');
                const slot = $(this).data('slot');
                if (!selectedSlotsByDay[day]) {
                    selectedSlotsByDay[day] = [];
                }
                selectedSlotsByDay[day].push(slot);
            });

            let selectedSlots = Object.keys(selectedSlotsByDay).map(day => ({
                day: day,
                time: selectedSlotsByDay[day]
            }));

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '{{ route("providerslot.store") }}',
                data: {
                    provider_id: $('#provider-id').val(),
                    slots: selectedSlots
                },
                success: function (response) {

                      showMessage(response.message);
                    //  alert(response.message || 'Slots saved successfully!');
                },
                error: function (error) {
                    alert('An error occurred. Please try again.');
                    console.error(error);
                }
            });
        });


        function showActiveDaySlots() {
            $('.day-slot').removeClass('show active');
            const activeDayLink = $('.day-link.active');
            if (activeDayLink.length) {
                const day = activeDayLink.data('day');
                $('#' + day).addClass('show active');
            }
        }
    });

        function showMessage(message) {
          Snackbar.show({
              text: message,
              pos: 'bottom-center'
          });
      }
</script>
