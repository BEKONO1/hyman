
<?php
    $auth_user= authSession();
?>
    {{ html()->form('DELETE', route('servicefaq.destroy', $servicefaq->id))->attribute('data--submit', 'servicefaq'.$servicefaq->id)->open() }}
<div class="d-flex justify-content-start align-items-center ml-0">
        @if($auth_user->can('servicefaq edit'))
        <a class="me-2" href="{{ route('servicefaq.create',['id' => $servicefaq->id]) }}" title="{{ __('messages.update_form_title',['form' => __('messages.servicefaq') ]) }}"><i class="fas fa-pen text-secondary"></i></a>
        @endif  

        @if($auth_user->can('servicefaq delete'))
        <a class="me-2" href="javascript:void(0)" data--submit="servicefaq{{$servicefaq->id}}" 
            data--confirmation='true' data-title="{{ __('messages.delete_form_title',['form'=>  __('messages.servicefaq') ]) }}"
            title="{{ __('messages.delete_form_title',['form'=>  __('messages.servicefaq') ]) }}"
            data-message='{{ __("messages.delete_msg") }}'>
            <i class="far fa-trash-alt text-danger"></i>
        </a>
        @endif
</div>
{{ html()->form()->close()}}