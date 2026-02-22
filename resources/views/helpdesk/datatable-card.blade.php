<div class="helpdesk-card position-relative mb-5">
        <div class="d-flex mt-3">
        <strong class="font-size-14 mx-2 text-primary">#{{$data->id ?? '-'}}</strong>
        <p class="font-size-14 pl-2">
    @if($data->created_at)
        {{ $data->created_at->setTimezone(new \DateTimeZone($datetime->time_zone ?? 'UTC'))->format("$datetime->date_format $datetime->time_format") }}
    @else
        -
    @endif
</p>
        </div>
        <h6 class="">
            {{ $data->subject ?? '-'}}
        </h6>
            <div class="d-flex mt-3">
                <p class="font-size-14 line-count-2">{{ $data->description ?? '-' }}</p>
            </div>
            @if($data->status == '0')
            <div class="booking-date bg-success text-white font-size-14 d-flex align-items-center gap-1 position-absolute top-0">
                <span class="text-capitalize font-size-14 fw-500">{{__('messages.open')}}</span>
            </div>
            @elseif($data->status == '1')
            <div class="booking-date bg-danger text-white font-size-14 d-flex align-items-center gap-1 position-absolute top-0">
                <span class="text-capitalize font-size-14 fw-500">{{__('messages.closed')}}</span>
            </div>
            @endif
            <hr class="dropdown-divider mt-0 mb-2">
            @if($data->status == '1')
                <div class="col-lg-12 mt-5 mt-lg-0 d-flex ">
                    <span class="text-capitalize text-success font-size-14 fw-500">{{__('messages.closed_on')}} : </span>
                    <span class="text-end font-size-14 mx-2">{{ date("$datetime->date_format $datetime->time_format", strtotime( $data->updated_at->setTimezone(new \DateTimeZone($datetime->time_zone ?? 'UTC'))))}}</span>
                </div>
            @endif
        <a href="{{ route('helpdesk.detail', $data->id) }}">{{__('messages.view_detail')}}</a>
</div>
