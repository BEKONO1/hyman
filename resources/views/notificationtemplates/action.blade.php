<?php
$auth_user= authSession();
?>
{{ html()->form('DELETE', route('notification-templates.destroy', $data->id))->attribute('data--submit', 'notificationtemplates'.$data->id)->open() }}
<div class="d-flex justify-content-end align-items-center">
    
<a class="me-2" href="{{ route('notification-templates.edit', ['notification_template' => $data->id]) }}" title="{{ __('messages.update_form_title',['form' => __('messages.notification_templates') ]) }}"><i class="fas fa-pen text-secondary"></i></a>

@if(auth()->user()->hasAnyRole(['admin']))
    <a class="me-3" href="{{ route('notification-templates.destroy', $data->id) }}" data--submit="notificationtemplates{{$data->id}}" 
        data--confirmation='true' 
        data--ajax="true"
        data-datatable="reload"
        data-title="{{ __('messages.delete_form_title',['form'=>  __('messages.notification') ]) }}"
        title="{{ __('messages.delete_form_title',['form'=>  __('messages.notification') ]) }}"
        data-message='{{ __("messages.delete_msg") }}'>
        <i class="far fa-trash-alt text-danger"></i>
    </a>
@endif
</div>
{{ html()->form()->close() }}