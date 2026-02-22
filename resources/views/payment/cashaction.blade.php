<?php
$auth_user= authSession();
?>
{{ html()->form('DELETE', route('payment.destroy', $payment->id))->attribute('data--submit', 'payment'.$payment->id)->open() }}
    <div class="d-flex justify-content-end align-items-center">
        @php
            $payment_status_check =   App\Models\PaymentHistory::where('payment_id',$payment->id)->orderBy('datetime','desc')->first();
        @endphp


        @if($payment_status_check !== null && $payment_status_check->status == 'pending_by_admin') 
            <a class="btn-sm text-white btn btn-success me-2"  href="{{route('cash.approve',$payment->id)}}"><i class="fa fa-check"></i>Approve</a>
        @endif


        @if(auth()->user()->hasAnyRole(['admin']))
            <a class="ml-6" href="{{ route('payment.destroy', $payment->id) }}" data--submit="payment{{$payment->id}}" 
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