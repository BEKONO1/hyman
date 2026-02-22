
<?php
    $auth_user= authSession();
?>
{{ html()->form('DELETE', route('handymanpayout.destroy', $handymanpayout->id))->attribute('data--submit', 'handymanpayout'.$handymanpayout->id)->open() }}
<div class="d-flex justify-content-end align-items-center">

    <a class="me-3" href="{{ route('handymanpayout.destroy', $handymanpayout->id) }}" data--submit="handymanpayout{{$handymanpayout->id}}" 
        data--confirmation='true' 
        data--ajax="true"
        data-datatable="reload"
        data-title="{{ __('messages.delete_form_title',['form'=>  __('messages.handyman_payout') ]) }}"
        title="{{ __('messages.delete_form_title',['form'=>  __('messages.handyman_payout') ]) }}"
        data-message='{{ __("messages.delete_msg") }}'>
        <i class="far fa-trash-alt text-danger"></i>
    </a>
</div>
{{ html()->form()->close() }}