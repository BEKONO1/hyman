
<?php
$auth_user= authSession();
?>
{{ html()->form('DELETE', route('post-job-request.destroy', $post_job->id))->attribute('data--submit', 'post-job-request'.$post_job->id)->open() }}

<div class="d-flex justify-content-end align-items-center">

<a class="me-2" href="javascript:void(0)" data--submit="post-job-request{{$post_job->id}}" 
    data--confirmation='true' data-title="{{ __('messages.delete_form_title',['form'=>  __('postJob') ]) }}"
    title="{{ __('messages.delete_form_title',['form'=>  __('postJob') ]) }}"
    data-message='{{ __("messages.delete_msg") }}'>
    <i class="far fa-trash-alt text-danger"></i>
</a>
<a class="" href="{{ route('post-job-request.show', $post_job->id) }}" title="{{ __('messages.view_form_title',['form'=>  __('messages.postjob') ]) }}"><i class="far fa-eye text-secondary me-2"></i></a>

</div>
{{ html()->form()->close() }}