
<?php
    $auth_user= authSession();
?>

{{ html()->form('DELETE', route('bank.destroy', $bank->id))->attribute('data--submit', 'bank'.$bank->id)->open()}}
<div class="d-flex justify-content-end align-items-center">
    @if(!$bank->trashed())
        @if($auth_user->can('bank edit'))
        <a class="me-2" href="{{ route('bank.edit',['id' => $bank->id]) }}" title="{{ __('messages.update_form_title',['form' => __('messages.bank') ]) }}"><i class="fas fa-pen text-secondary"></i></a>
        @endif

        @if($auth_user->can('bank delete'))
        <a class="me-2" href="javascript:void(0)" data--submit="bank{{$bank->id}}" 
            data--confirmation='true' data-title="{{ __('messages.delete_form_title',['form'=>  __('messages.bank') ]) }}"
            title="{{ __('messages.delete_form_title',['form'=>  __('messages.bank') ]) }}"
            data-message='{{ __("messages.delete_msg") }}'>
            <i class="far fa-trash-alt text-danger"></i>
        </a>
        @endif
    @endif
    @if(auth()->user()->hasAnyRole(['admin']) && $bank->trashed())
        <a href="{{ route('bank.action',['id' => $bank->id, 'type' => 'restore']) }}"
            title="{{ __('messages.restore_form_title',['form' => __('messages.bank') ]) }}"
            data--submit="confirm_form"
            data--confirmation='true'
            data--ajax='true'
            data-title="{{ __('messages.restore_form_title',['form'=>  __('messages.bank') ]) }}"
            data-message='{{ __("messages.restore_msg") }}'
            data-datatable="reload"
            class="me-2">
            <i class="fas fa-redo text-secondary"></i>
        </a>
        <a href="{{ route('bank.action',['id' => $bank->id, 'type' => 'forcedelete']) }}"
            title="{{ __('messages.forcedelete_form_title',['form' => __('messages.bank') ]) }}"
            data--submit="confirm_form"
            data--confirmation='true'
            data--ajax='true'
            data-title="{{ __('messages.forcedelete_form_title',['form'=>  __('messages.bank') ]) }}"
            data-message='{{ __("messages.forcedelete_msg") }}'
            data-datatable="reload"
            class="me-2">
            <i class="far fa-trash-alt text-danger"></i>
        </a>
    @endif
</div>
{{ html()->form()->close() }}