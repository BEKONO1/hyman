
<?php
    $auth_user= authSession();
?>
{{ html()->form('DELETE', route('servicezone.destroy', $data->id))->attribute('data--submit', 'servicezone'.$data->id)->open() }}
<div class="d-flex justify-content-end align-items-center">
    @if(!$data->trashed())
   
        @if($auth_user->can('service delete'))
        <a class="me-2" href="{{ route('servicezone.destroy', $data->id) }}" data--submit="servicezone{{$data->id}}"
            data--confirmation='true' 
            data--ajax="true"
            data-datatable="reload"
            data-title="{{ __('messages.delete_form_title',['form'=>  __('messages.servicezone') ]) }}"
            title="{{ __('messages.delete_form_title',['form'=>  __('messages.servicezone') ]) }}"
            data-message='{{ __("messages.delete_msg") }}'>
            <i class="far fa-trash-alt text-danger"></i>
        </a>
        @endif
      
    @endif
    @if(auth()->user()->hasAnyRole(['admin','provider']) && $data->trashed())
        @if($data->trashed())
            <a href="{{ route('servicezone.action', ['id' => $data->id, 'type' => 'restore']) }}"
                title="{{ __('messages.restore_form_title', ['form' => __('messages.servicezone')]) }}"
            data--submit="confirm_form"
                data--confirmation="true"
                data--ajax="true"
                data-title="{{ __('messages.restore_form_title', ['form' => __('messages.servicezone')]) }}"
                data-message="{{ __('messages.restore_msg') }}"
            data-datatable="reload"
                class="me-2">
            <i class="fas fa-redo text-primary"></i>
        </a>
        @endif
        <a href="{{ route('servicezone.action',['id' => $data->id, 'type' => 'forcedelete']) }}"
            title="{{ __('messages.forcedelete_form_title',['form' => __('messages.servicezone') ]) }}"
            data--submit="confirm_form"
            data--confirmation='true'
            data--ajax='true'
            data-title="{{ __('messages.forcedelete_form_title',['form'=>  __('messages.servicezone') ]) }}"
            data-message='{{ __("messages.forcedelete_msg") }}'
            data-datatable="reload"
            class="me-2">
            <i class="far fa-trash-alt text-danger"></i>
        </a>
    @endif
</div>
{{ html()->form()->close()}}





<!-- 
<form method="POST" action="{{ route('servicezone.destroy', $data->id) }}" class="d-inline">
    @csrf
    @method('DELETE')
    <div class="d-flex justify-content-end align-items-center">
@if(!$data->trashed() && auth()->user()->can('service zone delete'))
    <a href="{{ route('servicezone.destroy', $data->id) }}"
       class="btn btn-link p-0 me-3 delete-btn"
       data-id="{{ $data->id }}">
        <i class="far fa-trash-alt text-danger"></i>
    </a>
@endif
@if(auth()->check() && auth()->user()->hasAnyRole(['admin']) && $data->trashed())
    <a href="{{ route('servicezone.action',['id' => $data->id, 'type' => 'restore']) }}"
        title="{{ __('messages.restore_form_title',['form' => __('messages.servicezone')]) }}"
        class="me-2 restore-btn"
        data-id="{{ $data->id }}">
        <i class="fas fa-redo text-secondary"></i>
    </a>
    <a href="{{ route('servicezone.action',['id' => $data->id, 'type' => 'forcedelete']) }}"
        title="{{ __('messages.forcedelete_form_title',['form' => __('messages.servicezone')]) }}"
        class="me-2 force-delete-btn"
        data-id="{{ $data->id }}">
        <i class="far fa-trash-alt text-danger"></i>
    </a>
@endif
</div>
</form>

 -->
