
<?php
    $auth_user= authSession();
?>
{{ html()->form('DELETE', route('serviceaddon.destroy', $serviceaddon->id))->attribute('data--submit', 'serviceaddon'.$serviceaddon->id)->open() }}
@if($auth_user->can('service add on delete'))
<div class="d-flex justify-content-end align-items-center ms-2">
        <a class="me-2" href="{{ route('serviceaddon.destroy', $serviceaddon->id) }}" data--submit="serviceaddon{{$serviceaddon->id}}"
            data--confirmation='true'
            data--ajax="true"
            data-datatable="reload"
            data-title="{{ __('messages.delete_form_title',['form'=>  __('messages.service_addon') ]) }}"
            title="{{ __('messages.delete_form_title',['form'=>  __('messages.service_addon') ]) }}"
            data-message='{{ __("messages.delete_msg") }}'>
            <i class="far fa-trash-alt text-danger"></i>
        </a>
    </div>
@endif
{{ html()->form()->close()}}