<?php
$auth_user = authSession();
?>
{{ html()->form('DELETE', route('promotional-banner.destroy', $banner->id))->attribute('data--submit', 'promotional-banner' . $banner->id)->open() }}
<div class="d-flex justify-content-end align-items-center">
    @if (!$banner->trashed())
        @if ($auth_user->can('promotional-banner delete') && !$banner->trashed())
            <a class="me-3" href="{{ route('promotional-banner.destroy', $banner->id) }}"
                data--submit="promotional-banner {{ $banner->id }}" data--confirmation='true' data--ajax="true"
                data-datatable="reload"
                data-title="{{ __('messages.delete_form_title', ['form' => __('messages.promotional-banner')]) }}"
                title="{{ __('messages.delete_form_title', ['form' => __('messages.promotional-banner')]) }}"
                data-message='{{ __('messages.delete_msg') }}'>
                <i class="far fa-trash-alt text-danger"></i>
            </a>
        @endif
    @endif
   @if (auth()->user()->hasAnyRole(['admin', 'demo_admin']) && $banner->trashed())
        <a class="me-2" href="{{ route('promotional-banner.action', ['id' => $banner->id, 'type' => 'restore']) }}"
            title="{{ __('messages.restore_form_title', ['form' => __('messages.booking')]) }}"
            data--submit="confirm_form" data--confirmation='true' data--ajax='true'
            data-title="{{ __('messages.restore_form_title', ['form' => __('messages.promotional-banner')]) }}"
            data-message='{{ __('messages.restore_msg') }}' data-datatable="reload">
            <i class="fas fa-redo text-secondary"></i>
        </a>
        <a href="{{ route('promotional-banner.action', ['id' => $banner->id, 'type' => 'forcedelete']) }}"
            title="{{ __('messages.forcedelete_form_title', ['form' => __('messages.promotional-banner')]) }}"
            data--submit="confirm_form" data--confirmation='true' data--ajax='true'
            data-title="{{ __('messages.forcedelete_form_title', ['form' => __('messages.promotional-banner')]) }}"
            data-message='{{ __('messages.forcedelete_msg') }}' data-datatable="reload" class="me-2">
            <i class="far fa-trash-alt text-danger"></i>
        </a>
    @endif
    @if (auth()->user()->hasAnyRole(['admin', 'demo_admin']))
        @if ($banner->status === 'pending')
            @if ($banner->payment_status === 'paid')
                <a href="javascript:void(0)" class="btn btn-outline-success btn-sm p-1 me-2 approve-banner"
                    data-id="{{ $banner->id }}" title="Approve">
                    <i class="fas fa-check fa-2xs"></i>
                </a>
            @else
                <a href="javascript:void(0)" class="btn btn-outline-success btn-sm p-1 me-2"
                    onclick="Swal.fire({
                   title: '{{ __('messages.payment_pending') }}',
                   text: '{{ __('messages.complete_payment_first') }}',
                   icon: 'warning',
                   confirmButtonText: 'OK'
               })"
                    title="{{ __('messages.payment_pending') }}">
                    <i class="fas fa-check fa-2xs"></i>
                </a>
            @endif
            <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm p-1 me-2 reject-banner"
                data-id="{{ $banner->id }}" title="Reject">
                <i class="fas fa-times fa-2xs"></i>
            </a>
        @endif
    @endif
    @if (auth()->user()->hasAnyRole(['provider', 'admin', 'demo_admin']))
        @if ($banner->status !== 'pending')
            <a class="me-2" href="{{ route('promotional-banner.destroy', $banner->id) }}"
                data--submit="service{{ $banner->id }}" data--confirmation='true' data--ajax="true"
                data-datatable="reload"
                data-title="{{ __('messages.delete_form_title', ['form' => __('messages.provider_promotional_banner')]) }}"
                title="{{ __('messages.delete_form_title', ['form' => __('messages.provider_promotional_banner')]) }}"
                data-message='{{ __('messages.delete_msg') }}'>
                <i class="far fa-trash-alt text-danger"></i>
            </a>


            {{-- <a
               class="btn btn-sm btn-danger delete-promotional-banner"
               data-id="{{ $banner->id }}"
               data--submit="confirm_form"
               data--confirmation='true'
               data--ajax='true'
               data-title="{{ __('messages.delete_form_title',['form' => __('messages.promotional-banner')]) }}"
               data-message='{{ __("messages.delete_msg") }}'>
                <i class="far fa-trash-alt"></i>
            </a> --}}
        @endif
    @endif
    <a href="{{ route('promotional-banner.show', $banner->id) }}" title="{{ __('messages.view_details') }}">
        <i class="far fa-eye"></i>
    </a>
</div>
{{ html()->form()->close() }}
