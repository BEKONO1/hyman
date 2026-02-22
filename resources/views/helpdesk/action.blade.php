
<?php
    $auth_user= authSession();
?>


<a class="me-2" href="{{ route('helpdesk.show',$row->id) }}" title="{{ __('messages.update_form_title',['form' => __('messages.helpdesk') ]) }}"><i class="fas fa-eye text-secondary"></i></a>
@if($row->status == 0)
<a class="me-2" href="{{ route('helpdesk.closed',$row->id) }}" title="{{ __('messages.marked_closed') }}"><i class="fas fa-check text-success"></i></a>
@endif