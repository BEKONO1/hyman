
<?php
    $auth_user= authSession();
?>
{{ html()->form('DELETE', route('handymantype.destroy', $handymantype->id))->attribute('data--submit', 'handymantype'.$handymantype->id)->open() }}
<div class="d-flex justify-content-end align-items-center ms-2">
    @if(!$handymantype->trashed())
        @if(auth()->user()->hasRole(['provider','admin']) )
            <a class="me-3 text-danger" href="{{ route('handymantype.destroy', $handymantype->id) }}" data--submit="handymantype{{$handymantype->id}}" 
                data--confirmation='true' 
                data--ajax="true"
                data-datatable="reload"
                data-title="{{ __('messages.delete_form_title',['form'=>  __('messages.handymantype') ]) }}"
                title="{{ __('messages.delete_form_title',['form'=>  __('messages.handymantype') ]) }}"
                data-message='{{ __("messages.delete_msg") }}'>
                <i class="far fa-trash-alt"></i>
            </a>
        @endif
    @endif
    @if(auth()->user()->hasAnyRole(['provider','admin']) && $handymantype->trashed())
        <a class="me-2" href="{{ route('handymantype.action',['id' => $handymantype->id, 'type' => 'restore']) }}"
            title="{{ __('messages.restore_form_title',['form' => __('messages.handymantype') ]) }}"
            data--submit="confirm_form"
            data--confirmation='true'
            data--ajax='true'
            data-title="{{ __('messages.restore_form_title',['form'=>  __('messages.handymantype') ]) }}"
            data-message='{{ __("messages.restore_msg") }}'
            data-datatable="reload">
            <i class="fas fa-redo text-secondary"></i>
        </a>
        <a href="{{ route('handymantype.action',['id' => $handymantype->id, 'type' => 'forcedelete']) }}"
            title="{{ __('messages.forcedelete_form_title',['form' => __('messages.handymantype') ]) }}"
            data--submit="confirm_form"
            data--confirmation='true'
            data--ajax='true'
            data-title="{{ __('messages.forcedelete_form_title',['form'=>  __('messages.handymantype') ]) }}"
            data-message='{{ __("messages.forcedelete_msg") }}'
            data-datatable="reload"
            class="me-2">
            <i class="far fa-trash-alt text-danger"></i>
        </a>
    @endif
</div>
{{ html()->form()->close() }}