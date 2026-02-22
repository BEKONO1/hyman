
<?php
    $auth_user= authSession();
?>
{{ html()->form('DELETE', route('provideraddress.destroy', $provideraddress->id))->attribute('data--submit', 'providertype' . $provideraddress->id)->open()}}
<div class="d-flex justify-content-end align-items-center">
    @if($auth_user->can('provideraddress delete'))
    <a class="me-3 text-danger" href="{{ route('provideraddress.destroy', $provideraddress->id) }}" data--submit="providertype{{$provideraddress->id}}" 
        data--confirmation='true' 
        data--ajax="true"
        data-datatable="reload"
        data-title="{{ __('messages.delete_form_title',['form'=>  __('messages.provider_address') ]) }}"
        title="{{ __('messages.delete_form_title',['form'=>  __('messages.address') ]) }}"
        data-message='{{ __("messages.delete_msg") }}'>
        <i class="far fa-trash-alt"></i>
    </a>
    @endif
</div>
{{ html()->form()->close() }}