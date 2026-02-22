<?php
$auth_user= authSession();
?>

{{ html()->form('DELETE', route('mail-templates.destroy', $data->id))->attribute('data--submit', 'mailtemplates' . $data->id)->open() }}
<div class="d-flex justify-content-end align-items-center">
    
<a class="me-2" href="{{ route('mail-templates.edit', ['mail_template' => $data->id]) }}" title="{{ __('messages.update_form_title',['form' => __('messages.mail_templates') ]) }}"><i class="fas fa-pen text-secondary"></i></a>

@if(auth()->user()->hasAnyRole(['admin']))
    <a class="me-3" href="{{ route('mail-templates.destroy', $data->id) }}" data--submit="mailtemplates{{$data->id}}" 
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
