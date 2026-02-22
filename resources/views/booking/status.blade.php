@php
$extraValue = 0;
$sitesetup = App\Models\Setting::where('type','site-setup')->where('key', 'site-setup')->first();
$datetime = $sitesetup ? json_decode($sitesetup->value) : null;
$timezone = getTimeZone();
@endphp
<div class="row">
    <!-- Timeline Column -->
    <div class="col-md-6">
        <div class=" pb-3">
            <h2 class="modal-title" id="breakdownModalLabel">{{__('messages.booking_status')}}</h2>

            <div class="vertical-timeline mb-4">
                <!-- New Booking (Always show) -->
                <div class="timeline-item {{ $bookingdata->status == 'pending' ? 'active' : '' }}">
                    <div class="timeline-date">
                        {{ \Carbon\Carbon::parse($bookingdata->created_at)->timezone($timezone)->format($datetime->time_format) }}
                        <div class="date-details">
                            {{ \Carbon\Carbon::parse($bookingdata->created_at)->timezone($timezone)->format($datetime->date_format) }}
                        </div>
                    </div>
                    <div class="timeline-content">
                        <div class="point"></div>
                        <div class="timeline-info">
                            <p class="fs-4"><strong>{{__('messages.new_booking')}}</strong></p>
                            <div class="timeline-details">
                                <p class="mt-2">New Booking Added by {{ optional($bookingdata->customer)->display_name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-connector"></div>
                </div>

                @if($bookingdata->status == 'cancelled')
                <!-- Show only cancelled status after new booking if cancelled -->
                <div class="timeline-item active">
                    <div class="timeline-date">
                        {{ \Carbon\Carbon::parse($bookingdata->updated_at)->timezone($timezone)->format($datetime->time_format) }}
                        <div class="date-details">
                            {{ \Carbon\Carbon::parse($bookingdata->updated_at)->timezone($timezone)->format($datetime->date_format) }}
                        </div>
                    </div>
                    <div class="timeline-content">
                        <div class="point"></div>
                        <div class="timeline-info">
                            <p class="fs-4"><strong>Cancelled</strong></p>
                            <div class="timeline-details">
                                <p class="mt-2 text-danger">Booking has been cancelled</p>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                    <!-- Accepted (Show if not cancelled) -->
                    <div class="timeline-item {{ $bookingdata->status == 'accept' ? 'active' : '' }}">
                        <div class="timeline-date">
                            {{ \Carbon\Carbon::parse($bookingdata->updated_at)->timezone($timezone)->format($datetime->time_format) }}
                            <div class="date-details">
                                {{ \Carbon\Carbon::parse($bookingdata->updated_at)->timezone($timezone)->format($datetime->date_format) }}
                            </div>
                        </div>
                        <div class="timeline-content">
                            <div class="point"></div>
                            <div class="timeline-info">
                                <p class="fs-4"><strong>{{__('messages.accept_booking')}}</strong></p>
                                <div class="timeline-details">
                                    <p class="mt-2">Booking Accepted by {{ optional($bookingdata->provider)->display_name }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-connector"></div>
                    </div>

                    @if($bookingdata->handymanAdded->count() > 0)
                    <!-- Show assigned only if handyman is assigned -->
                    <div class="timeline-item {{ $bookingdata->status == 'assigned' ? 'active' : '' }}">
                        <div class="timeline-date">
                            {{ \Carbon\Carbon::parse($bookingdata->updated_at)->timezone($timezone)->format($datetime->time_format) }}
                            <div class="date-details">
                                {{ \Carbon\Carbon::parse($bookingdata->updated_at)->timezone($timezone)->format($datetime->date_format) }}
                            </div>
                        </div>
                        <div class="timeline-content">
                            <div class="point"></div>
                            <div class="timeline-info">
                                <p class="fs-4"><strong>{{__('messages.assigned_booking')}}</strong></p>
                                <div class="timeline-details">
                                    @foreach($bookingdata->handymanAdded as $handyman)
                                        <p class="mt-2">Service Assigned to {{ optional($handyman->handyman)->display_name }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="timeline-connector"></div>
                    </div>
                    @endif

                    @if($bookingdata->status == 'on_going')
                    <!-- Show on_going status after assigned -->
                    <div class="timeline-item active">
                        <div class="timeline-date">
                            {{ \Carbon\Carbon::parse($bookingdata->updated_at)->timezone($timezone)->format($datetime->time_format) }}
                            <div class="date-details">
                                {{ \Carbon\Carbon::parse($bookingdata->updated_at)->timezone($timezone)->format($datetime->date_format) }}
                            </div>
                        </div>
                        <div class="timeline-content">
                            <div class="point"></div>
                            <div class="timeline-info">
                                <p class="fs-4"><strong>{{__('messages.on_going')}}</strong></p>
                                <div class="timeline-details">
                                    <p class="mt-2 text-primary">Service is currently in progress</p>
                                    @if($bookingdata->handymanAdded->count() > 0)
                                        @foreach($bookingdata->handymanAdded as $handyman)
                                            <p class="mt-2">Being handled by {{ optional($handyman->handyman)->display_name }}</p>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="timeline-connector"></div>
                    </div>
                    @endif

                    <!-- Show completed status (always show but only update details when completed) -->
                    <div class="timeline-item {{ $bookingdata->status == 'completed' ? 'active' : '' }}">
                        <div class="timeline-date">
                            @if($bookingdata->status == 'completed')
                                {{ \Carbon\Carbon::parse($bookingdata->updated_at)->timezone($timezone)->format($datetime->time_format) }}
                                <div class="date-details">
                                    {{ \Carbon\Carbon::parse($bookingdata->updated_at)->timezone($timezone)->format($datetime->date_format) }}
                                </div>
                            @else
                                <span class="text-muted">--:--</span>
                                <div class="date-details">
                                    <span class="text-muted">----/--/--</span>
                                </div>
                            @endif
                        </div>
                        <div class="timeline-content">
                            <div class="point"></div>
                            <div class="timeline-info">
                                <p class="fs-4"><strong>{{__('messages.completed')}}</strong></p>
                                <div class="timeline-details">
                                    @if($bookingdata->status == 'completed')
                                        <p class="mt-2">Service Completed - Final Amount: {{ getPriceFormat($bookingdata->total_amount) }}</p>
                                    @else
                                        <p class="mt-2 text-muted">Pending completion</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Provider and Handyman Cards Column -->
    <div class="col-md-6">
        <!-- Booking Summary Card -->
        <div class="card mb-4">
            <div class="card-body bg-body">
                <ul class="list-unstyled">
                    <li class="d-flex justify-content-between mb-2 p-2">
                        <span class="text-muted">{{ __('messages.book_placed') }}:</span>
                        <span class="fw-medium">
                            {{ \Carbon\Carbon::parse($bookingdata->created_at)->format($datetime->date_format) }} /
                            {{ \Carbon\Carbon::parse($bookingdata->created_at)->format($datetime->time_format) }}
                        </span>
                    </li>
                    <li class="d-flex justify-content-between mb-2 p-2">
                        <span class="text-muted">{{__('messages.booking_status')}}:</span>
                        <strong><span class="text-primary">{{ isset($bookingdata->status) ? ucwords(str_replace('_', ' ', $bookingdata->status)) : __('messages.pending') }}</span></strong>
                    </li>                   
                    <li class="d-flex justify-content-between mb-2 p-2">
                        <span class="text-muted">{{ __('messages.payment_status') }}:</span>
                        @if(isset($payment) && $payment->payment_status)
                            @php
                                $statusClass = match($payment->payment_status) {
                                    'paid', 'advanced_paid' => 'text-success',
                                    'Advanced Refund' => 'text-warning',
                                    default => 'text-danger',
                                };
                            @endphp
                            <strong>
                                <span class="{{ $statusClass }}">
                                    {{ str_replace('_', ' ', ucfirst($payment->payment_status)) }}
                                </span>
                            </strong>
                        @else
                            <strong>
                                <span class="text-danger">{{ __('messages.pending') }}</span>
                            </strong>
                        @endif
                    </li>      
                    <li class="d-flex justify-content-between p-2">
                        <span class="text-muted">{{__('messages.booking_amount')}}:</span>
                        <span class="fw-medium">{{ getPriceFormat($bookingdata->total_amount) }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-md-12">
            <!-- Provider Information -->
                <div class="card mb-4">
                    <div class="card-body bg-body">
                            <div class="d-flex align-items-start gap-3">
                                <div class="flex-shrink-0">
                                    <img src="{{ getSingleMedia($bookingdata->provider,'profile_image', null) }}" 
                                        alt="Provider Profile" 
                                        class="rounded-circle"
                                        style="width: 60px; height: 60px; object-fit: cover;">
                                    @if(optional($bookingdata->provider)->profile_image)
                                        <img src="{{ asset('images/default-user.png') }}" 
                                            alt="Default Profile" 
                                            class="rounded-circle"
                                            style="width: 60px; height: 60px; object-fit: cover;">
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1 text-primary">{{__('messages.provider')}}</p>
                                    <h5 class="mb-2">{{optional($bookingdata->provider)->display_name ?? '-'}}</h5>
                                </div>
                            </div>
                        <ul class="list-unstyled mt-3">
                            <li class="d-flex align-items-center mb-2">
                                <i class="ri-phone-line me-2"></i>
                                <a href="tel:{{optional($bookingdata->provider)->contact_number}}" class="text-body">
                                    {{ optional($bookingdata->provider)->contact_number ?? '-' }}
                                </a>
                            </li>
                            <!-- <li class="d-flex align-items-center mb-2">
                                <i class="ri-mail-line me-2"></i>
                                <a href="mailto:{{optional($bookingdata->provider)->email}}" class="text-body">
                                    {{ optional($bookingdata->provider)->email ?? '-' }}
                                </a>
                            </li> -->
                            <li class="d-flex align-items-center">
                                <i class="ri-map-pin-line me-2"></i>
                                <span class="text-wrap">{{ optional($bookingdata->provider)->address ?? '-' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

            <!-- Handyman Information -->
                        @if(count($bookingdata->handymanAdded) > 0)
                            <div class="card mb-4">
                                <div class="card-body bg-body">
        
                                    @foreach($bookingdata->handymanAdded as $booking)
                                    <div class="d-flex align-items-start gap-4">
                                        <div class="flex-shrink-0">
                                            
                                                <img src="{{ getSingleMedia($booking->handyman,'profile_image', null) }}" 
                                                    alt="Handyman Profile" 
                                                    class="rounded-circle"
                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                                    @if(optional($booking->handyman)->profile_image)
                                                <img src="{{ asset('images/default-user.png') }}" 
                                                    alt="Default Profile" 
                                                    class="rounded-circle"
                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-1 text-primary">{{__('messages.handyman')}}</p>
                                            <h5 class="mb-2 ">{{optional($booking->handyman)->display_name ?? '-'}}</h5>
                                        </div>
                                    </div>
                                            <ul class="list-unstyled mt-3">
                                                <li class="d-flex align-items-center mb-2">
                                                    <i class="ri-phone-line me-2"></i>
                                                    <a href="tel:{{optional($booking->handyman)->contact_number}}" class="text-body">
                                                        {{ optional($booking->handyman)->contact_number ?? '-' }}
                                                    </a>
                                                </li>
                                                <li class="d-flex align-items-center">
                                                    <i class="ri-map-pin-line me-2"></i>
                                                    <span class="text-wrap">{{ optional($booking->handyman)->address ?? '-' }}</span>
                                                </li>
                                            </ul>
                                    @endforeach
                                </div>
                            </div>
                        @endif
        </div>
                    
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingId = '{{ $bookingdata->id }}';
    let currentStatus = '{{ $bookingdata->status }}';

    function updateTimelineStatus() {
        const timelineItems = document.querySelectorAll('.timeline-item');
        timelineItems.forEach(item => {
            if (item.classList.contains(currentStatus)) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }

    function updateBookingStatus(status) {
        const bookingStatusElement = document.querySelector('[data-booking-status]');
        if (bookingStatusElement) {
            bookingStatusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ');
        }
    }

    // Polling for status updates
    function pollForStatusUpdates() {
        setInterval(() => {
            fetch(`/api/booking/${bookingId}/status`)
                .then(response => response.json())
                .then(data => {
                    if (data.status !== currentStatus) {
                        currentStatus = data.status;
                        updateTimelineStatus();
                        updateBookingStatus(data.status);
                    }
                });
        }, 5000); // Poll every 5 seconds
    }

    // Initialize
    updateTimelineStatus();
    updateBookingStatus(currentStatus);
    pollForStatusUpdates();
});
</script>

<!-- Add required CSS for animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>

<style>
/* Enhance existing timeline styles */
.timeline-item {
    transition: all 0.3s ease-in-out;
}

.timeline-item .point {
    transition: background-color 0.3s ease-in-out;
}

.timeline-item.active .point {
    animation: pulse 2s infinite;
}

.timeline-item .timeline-connector {
    transition: border-color 0.3s ease-in-out;
}

/* Smooth color transitions */
.timeline-item:nth-child(1).active .point,
.timeline-item:nth-child(1).active .timeline-connector {
    transition: all 0.3s ease-in-out;
}

.timeline-item:nth-child(2).active .point,
.timeline-item:nth-child(2).active .timeline-connector {
    transition: all 0.3s ease-in-out;
}

.timeline-item:nth-child(3).active .point,
.timeline-item:nth-child(3).active .timeline-connector {
    transition: all 0.3s ease-in-out;
}

.timeline-item:nth-child(4).active .point {
    transition: all 0.3s ease-in-out;
}

/* Enhanced pulse animation */
@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.4);
    }
    70% {
        transform: scale(1.1);
        box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
    }
}

/* Base Timeline Structure */
.vertical-timeline {
    position: relative;
    padding: 20px 0;
    margin-left: 100px;
}

.timeline-item {
    position: relative;
    padding-bottom: 50px;
    margin-bottom: 15px;  
}

.timeline-date {
    position: absolute;
    left: -120px;
    width: 100px;
    text-align: right;
}

.timeline-content {
    display: flex;
    align-items: flex-start;
    padding-top: 10px;    
}


.timeline-connector {
    position: absolute;
    left: 9px;
    top: 25px;
    bottom: 0;
    width: 2px;
    border-left: 2px dashed #e0e0e0;
    height: calc(100% - 15px);
}


.point {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #e0e0e0;
    margin-right: 15px;
}


/* New Booking - Red */
.timeline-item:nth-child(1).active .point {
    background: #dc3545;
}
.timeline-item:nth-child(1).active .timeline-connector {
    border-left: 2px dashed #dc3545;
}

/* Accepted - Yellow */
.timeline-item:nth-child(2).active .point {
    background: #ffc107;
}
.timeline-item:nth-child(2).active .timeline-connector {
    border-left: 2px dashed #ffc107;
}

/* Assigned - Orange */
.timeline-item:nth-child(3).active .point {
    background: #fd7e14;
}
.timeline-item:nth-child(3).active .timeline-connector {
    border-left: 2px dashed #fd7e14;
}

/* Completed - Green */
.timeline-item:nth-child(4).active .point {
    background: #28a745;
}
.timeline-item:nth-child(4).active .timeline-connector {
    border-left: 2px dashed #28a745;
}

/* Remove last connector */
.timeline-item:last-child .timeline-connector {
    display: none;
}

/* Add style for on_going status */
.timeline-item.active[data-status="on_going"] .point {
    background: #0dcaf0; /* Using Bootstrap's info color */
    animation: pulse 2s infinite;
}

.timeline-item.active[data-status="on_going"] .timeline-connector {
    border-left: 2px dashed #0dcaf0;
}
</style>