<?php
$auth_user= authSession();
?>
{{-- {{ $earningData->id}} --}}
{{ html()->form('DELETE', route('payment.destroy', $payment->id))->attribute('data--submit', 'payment'.$payment->id)->open() }}
<div class="d-flex justify-content-end align-items-center">
@if(auth()->user()->hasAnyRole(['admin']))
    <a class="me-3" href="{{ route('payment.destroy', $payment->id) }}" data--submit="payment{{$payment->id}}" 
        data--confirmation='true' 
        data--ajax="true"
        data-datatable="reload"
        data-title="{{ __('messages.delete_form_title',['form'=>  __('messages.payment') ]) }}"
        title="{{ __('messages.delete_form_title',['form'=>  __('messages.payment') ]) }}"
        data-message='{{ __("messages.delete_msg") }}'>
        <i class="far fa-trash-alt text-danger"></i>
    </a>
@endif
</div>
{{ html()->form()->close() }}