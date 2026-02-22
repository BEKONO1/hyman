
<?php
    $auth_user= authSession();
?>
{{ html()->form('DELETE', route('providertype.destroy', $providertype->id))->attribute('data--submit', 'providertype' . $providertype->id)->open() }}
<div class="d-flex justify-content-end align-items-center ms-2">
    @if(!$providertype->trashed())

    @if($auth_user->can('providertype delete'))
        <a class="me-3 text-danger" href="{{ route('providertype.destroy', $providertype->id) }}" data--submit="providertype{{$providertype->id}}" 
            data--confirmation='true'
            data--ajax="true"
            data-datatable="reload"
            data-title="{{ __('messages.delete_form_title',['form'=>  __('messages.providertype') ]) }}"
            title="{{ __('messages.delete_form_title',['form'=>  __('messages.providertype') ]) }}"
            data-message='{{ __("messages.delete_msg") }}'>
            <i class="far fa-trash-alt"></i>
        </a>
    @endif

    @endif
    @if(auth()->user()->hasAnyRole(['admin']) && $providertype->trashed())
        <a class="me-2" href="{{ route('providertype.action',['id' => $providertype->id, 'type' => 'restore']) }}"
            title="{{ __('messages.restore_form_title',['form' => __('messages.providertype') ]) }}"
            data--submit="confirm_form"
            data--confirmation='true'
            data--ajax='true'
            data-title="{{ __('messages.restore_form_title',['form'=>  __('messages.providertype') ]) }}"
            data-message='{{ __("messages.restore_msg") }}'
            data-datatable="reload">
            <i class="fas fa-redo text-secondary"></i>
        </a>
        <a href="{{ route('providertype.action',['id' => $providertype->id, 'type' => 'forcedelete']) }}"
            title="{{ __('messages.forcedelete_form_title',['form' => __('messages.providertype') ]) }}"
            data--submit="confirm_form"
            data--confirmation='true'
            data--ajax='true'
            data-title="{{ __('messages.forcedelete_form_title',['form'=>  __('messages.providertype') ]) }}"
            data-message='{{ __("messages.forcedelete_msg") }}'
            data-datatable="reload"
            class="me-2">
            <i class="far fa-trash-alt text-danger"></i>
        </a>
    @endif
</div>
{{ html()->form()->close() }}