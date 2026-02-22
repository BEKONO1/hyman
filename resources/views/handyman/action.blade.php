<?php
    $auth_user = authSession();
?>
{{ html()->form('DELETE', route('handyman.destroy', $handyman->id))->attribute('data--submit', 'handyman'.$handyman->id)->open() }}
<div class="d-flex justify-content-end align-items-center ms-2">

    @if(!$handyman->trashed())
        @can('handyman edit')
            <a class="me-2" href="{{ route('handyman.create', ['id' => $handyman->id]) }}" 
               title="{{ __('messages.update_form_title', ['form' => __('messages.provider')]) }}">
                <i class="fas fa-pen text-secondary"></i>
            </a>
        @endcan

        @can('handyman changePassword')
            <a class="me-2" href="{{ route('handyman.getchangepassword', ['id' => $handyman->id]) }}" 
               title="{{ __('messages.change_password', ['form' => __('messages.handyman')]) }}">
                <i class="fa fa-lock text-success"></i>
            </a>
        @endcan

        @can('handyman delete')
            <a class="me-3 text-danger" href="{{ route('handyman.destroy', $handyman->id) }}" 
               data--submit="handyman{{ $handyman->id }}" 
               data--confirmation="true"
               data--ajax="true"
               data-datatable="reload"
               data-title="{{ __('messages.delete_form_title', ['form' => __('messages.handyman')]) }}"
               title="{{ __('messages.delete_form_title', ['form' => __('messages.handyman')]) }}"
               data-message="{{ __('messages.delete_msg') }}">
                <i class="far fa-trash-alt"></i>
            </a>
        @endcan
    @endif

    @if(auth()->user()->hasAnyRole(['admin', 'provider']) && $handyman->trashed())
        @foreach (['restore' => 'redo', 'forcedelete' => 'trash-alt'] as $action => $icon)
            <a href="{{ route('handyman.action', ['id' => $handyman->id, 'type' => $action]) }}"
               title="{{ __('messages.' . $action . '_form_title', ['form' => __('messages.handyman')]) }}"
               data--submit="confirm_form"
               data--confirmation="true"
               data--ajax="true"
               data-title="{{ __('messages.' . $action . '_form_title', ['form' => __('messages.handyman')]) }}"
               data-message="{{ __('messages.' . $action . '_msg') }}"
               data-datatable="reload"
               class="me-2">
                <i class="fas fa-{{ $icon }} text-{{ $action == 'restore' ? 'secondary' : 'danger' }}"></i>
            </a>
        @endforeach
    @endif

</div>
{{ html()->form()->close() }}