
<?php
    $auth_user= authSession();
?>
{{ html()->form('DELETE', route('booking-rating.destroy', $bookingrating->id))->attribute('data--submit', 'bookingrating' . $bookingrating->id)->open() }}
@if(auth()->user()->hasAnyRole(['admin']))
<div class="d-flex justify-content-end align-items-center">
        <a class="me-2" href="{{ route('booking-rating.destroy', $bookingrating->id) }}" data--submit="bookingrating{{$bookingrating->id}}" 
            data--confirmation='true' 
            data--ajax="true"
            data-datatable="reload"
            data-title="{{ __('messages.delete_form_title',['form'=>  __('messages.rating') ]) }}"
            title="{{ __('messages.delete_form_title',['form'=>  __('messages.rating') ]) }}"
            data-message='{{ __("messages.delete_msg") }}'>
            <i class="far fa-trash-alt text-danger"></i>
        </a>
</div>
@endif
{{ html()->form()->close() }}