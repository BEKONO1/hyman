<x-master-layout>
    <div class="container-fluid">
    @include('partials._provider')
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{$providerdata->first_name .' '. $providerdata->last_name}} {{$pageTitle}}</h5>
                          
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', '#')->attribute('data-toggle', 'validator')->id('provider')->open() }}
                        <div class="row">
                            <div class="col-md-12">
                                {{ html()->hidden('id', $provider_id)->class('form-control')->attribute('placeholder', 'id') }}
                                <div class="form-group has-feedback">
                                    <a class="me-2 float-end btn btn-sm btn-primary" href="{{ route('provider.edit-time-slot',['id' => $provider_id]) }}" title="{{ __('messages.slot') }}">{{ __('messages.update') }}</a>
                                    {{ html()->label(__('messages.day') . ' <span class="text-danger">*</span>', 'Day')->class('form-control-label col-md-12') }}
                    
                                    <div class="col-md-12">
                                        <ul class="nav nav-tabs pay-tabs nav-fill gap-3 tabslink mt-3" id="tab-text" role="tablist">
                                            @foreach ($slotsArray as $slotDay)
                                                @if (isset($slotDay['day']))
                                                   
                                                    <li class="nav-item m-0">
                                                        <a href="#" class="nav-link day-link" data-day="{{ $slotDay['day'] }}" data-bs-toggle="tab" rel="tooltip">{{ ucfirst($slotDay['day']) }}</a>
                                                    </li>
                                                    
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>

                                </div>
                    
                                <div class="form-group has-feedback">
                                    <div class="col-md-12">

                                        {{ html()->label(__('messages.time') . ' <span class="text-danger">*</span>', 'Time')->class('form-control-label col-md-12') }}
                                        <div class="tab-content" id="pills-tabContent-1">
                                            @foreach ($slotsArray as $slotDay)
                                                @if (isset($slotDay['day']) && isset($slotDay['slot']))
                                                    <div class="tab-pane p-1 day-slot @if(strtolower($slotDay['day']) === strtolower($activeDay)) active @endif" id="{{ $slotDay['day'] }}">
                                                                {{-- <!-- <h3>{{ $slotDay['day'] }}</h3> --> --}}
                                                        @if($slotDay['slot'])
                                                                    <ul  class="nav nav-tabs pay-tabs nav-fill tabslink gap-3 provider-slot">
                                                                @foreach ($slotDay['slot'] as $slot)
                                                                    @php
                                                                        $slot = sprintf('%02d:00', $slot)
                                                                    @endphp
                                                                    <li class="nav-item m-0">
                                                                        <a href="javascript:void(0)" class="nav-link" data-bs-toggle="tab" rel="tooltip">{{ $slot }}</a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <div>
                                                                <span>No time slots selected for {{ $slotDay['day'] }} day.</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    </div>
                                   
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
        setActiveDay('mon');
        $('.day-link').on('click', function (e) {
            e.preventDefault();
            var selectedDay = $(this).data('day');
            setActiveDay(selectedDay);
            showActiveDaySlots();
        });
       
        function setActiveDay(day) {
            $('.day-slot').removeClass('active');
            $('.day-link').removeClass('active');
            $('.day-link[data-day="' + day + '"]').addClass('active');
            $('.day-slot#' + day).addClass('active');
            activeDay = day;
        }

        function showActiveDaySlots() {
            $('.day-slot').hide();
            $('.day-slot.active').show();
        }
    });
</script>
