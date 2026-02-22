
<?php
    $auth_user= authSession();
?>
{{ html()->form('DELETE', route('plans.destroy', $plan->id))->attribute('data--submit', 'plan'.$plan->id)->open() }}
<div class="d-flex justify-content-end align-items-center">
    @if(auth()->user()->hasAnyRole(['admin']))
        <a class="me-3" href="{{ route('plans.destroy', $plan->id) }}" data--submit="plan{{$plan->id}}" 
            data--confirmation='true' 
            data--ajax="true"
            data-datatable="reload"
            data-title="{{ __('messages.delete_form_title',['form'=>  __('messages.plan') ]) }}"
            title="{{ __('messages.delete_form_title',['form'=>  __('messages.plan') ]) }}"
            data-message='{{ __("messages.delete_msg") }}'>
            <i class="far fa-trash-alt text-danger"></i>
        </a>
    @endif
</div>
{{ html()->form()->close() }}