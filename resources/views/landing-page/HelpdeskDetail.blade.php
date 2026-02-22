@extends('landing-page.layouts.default')


@section('content')
<div class="section-padding booking">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="justify-content-start mb-5">
                    <a href="{{ route('helpdesk.list') }}" class="btn btn-md btn-primary mx-2"><i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex mt-3">
                    <strong class="mx-2 text-primary">#{{$findHelpdesk->id ?? '-'}}</strong>
                    <p class="pl-2">{{ !empty($findHelpdesk->created_at) ?  date("{$date_time['date_format']} {$date_time['time_format']}", strtotime($findHelpdesk->created_at->setTimezone(new \DateTimeZone($date_time['time_zone'] ?? 'UTC')) )) : '-'}}</p>
                </div>
                <!-- <p >{{ !empty($findHelpdesk->updated_at) ? date("{$date_time['date_format']} {$date_time['time_format']}", strtotime($findHelpdesk->updated_at)) : '-'}}</p> -->
                <h5 class="text-capitalize">{{ $findHelpdesk->subject ?? ''}}</h5>
                <p >{{ $findHelpdesk->description ?? ''}}</p>
            </div>
        </div>
            @if(optional($findHelpdesk->helpdeskactivity)->isNotEmpty() && count($findHelpdesk->helpdeskactivity) <= 1 && $findHelpdesk->status == 0)
            <form method="POST" enctype="multipart/form-data" >
                @csrf
                <input type="hidden" name="helpdesk_id" class="helpdesk_id" value="{{ $findHelpdesk->id }}" data-helpdesk-id="{{ $findHelpdesk->id }}">
                @if(!empty(auth()->user()))
                    <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
                @endif
                <div>
                    <div class="row">
                        <!-- File input for attachments -->
                        <div class="form-group col-md-12">
                            <label class="form-control-label" for="helpdesk_activity_attachment">{{ __('messages.image') }}</label>
                            <div class="custom-file">
                                <input type="file" name="helpdesk_activity_attachment[]" class="form-control"
                                    data-file-error="{{ __('messages.files_not_allowed') }}" accept="image/*">
                                <!-- <label class="custom-file-label upload-label">{{ __('messages.choose_file', ['file' => __('messages.attachments')]) }}</label> -->
                            </div>
                        </div>

                        <!-- Description text area -->
                        <div class="form-group col-md-12">

                            {{ html()->label(trans('messages.description'). ' <span class="text-danger">*</span>', 'description')->class('form-control-label') }}
                            {{ html()->textarea('description', null)->class('form-control textarea')->required()->rows(3)->placeholder(__('messages.description')) }}
                            <span class="text-danger description-error" style="display: none;">{{ __('messages.description_required') }}</span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <!-- Link to mark as closed -->
                        <!-- <a href="{{ route('helpdesk.closed', ['id' => $findHelpdesk->id]) }}" class="btn btn-md btn-secondary mx-4">{{ __('messages.marked_closed') }}< -->
                        <!-- Submit button with id -->
                        <button type="button" class="btn btn-md btn-primary p-3 closeReply mx-2">{{ __('messages.marked_closed') }}</button>
                        <button type="button" class="btn btn-md btn-primary p-3 saveReply">{{__('messages.reply')}}</button>
                    </div>
                </div>
            </form>
            @elseif(count($findHelpdesk->helpdeskactivity) > 0)

            <div class="mt-2 pt-1">
                    <div class="row">
                       
                        <div class="col-12 mt-2">
                            <div class="p-5 bg-primary-subtle rounded-3">
                                <div class="service-accordian helpdesk-accordion accordion" id="service-accordion">
                                    @foreach($helpdeskData['activity'] as $index => $activity)
                                    @php
                                        $formattedDate = date("{$date_time['date_format']} {$date_time['time_format']}", strtotime($activity['updated_at']));
                                        $messageKey = $activity['activity_type'] !== 'closed_helpdesk' ? ($index === 0 ? 'messages.created_by_helpdesk' : 'messages.replied_by_helpdesk') : 'messages.closed_by_helpdesk';
                                    @endphp

                                    <div class="accordion-item border-0">
                                        <div class="d-flex flex-sm-row flex-column gap-4">
                                            <div class="flex-shrink-0">
                                                <img src="{{ $activity['sender_image'] }}" class="avatar-70 object-cover rounded-circle" alt="comment-user" />
                                            </div>
                                            <div class="flex-grow-1">
                                            <div class="accrodion-title collapsed p-0" 
                                                @if($activity['activity_type'] !== 'closed_helpdesk') 
                                                    data-bs-toggle="collapse" 
                                                @endif
                                                data-bs-target="#q-{{$activity['id']}}" 
                                                aria-expanded="false" 
                                                aria-controls="q-{{$activity['id']}}">
                                                    <div class="d-flex justify-content-between gap-2">
                                                        <h6 class="title text-body m-0">{{ __($messageKey, [
                                                            'name' => $activity['sender_name'],
                                                            'date' => $formattedDate
                                                        ])}}</h6>
                                                        @if($activity['activity_type'] !== 'closed_helpdesk')
                                                        <div class="flex-shrink-0">
                                                            <span class="icon-accrodion icon-inactive">
                                                                <h6 class="mb-1 text-primary toggle-message" style="cursor: pointer;"><span class="message">{{__('messages.show_message')}}</span> <i class="fa fa-angle-down"></i></h6>
                                                            </span>
                                                            <span class="icon-accrodion icon-active">
                                                                <h6 class="mb-1 text-primary toggle-message" style="cursor: pointer;"><span class="message">{{__('messages.show_message')}}</span> <i class="fa fa-angle-up"></i></h6>
                                                            </span>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div id="q-{{$activity['id']}}" class="accordion-collapse collapse mt-3" >
                                                    <div class="d-flex gap-2 p-3 border bg-body rounded-2">
                                                        @php
                                                            $attachmentUrls = $index === 0 ? getAttachments($findHelpdesk->getMedia('helpdesk_attachment')) : $activity['attachments'];
                                                            $firstAttachmentUrl = $attachmentUrls[0] ?? null; // Use a default image if empty
                                                        @endphp
                                                        @if($firstAttachmentUrl !== null)
                                                        <img src="{{ $firstAttachmentUrl }}" alt="avatar" class="avatar avatar-40">
                                                        @endif
                                                        <div class="accordion-body pb-0">{{ $activity['messages'] }}</div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @if($findHelpdesk->status == 0)
                                <div class="modal-footer mt-2 px-0">
                                    <!-- <button type="button" class="btn btn-md btn-primary mx-2 closeReply">{{ __('messages.marked_closed') }}</button> -->
                                    <button class="btn btn-md btn-primary" id="replyButton">{{ __('messages.reply') }}</button>
                                </div>
                                @endif
                                <div class="p-3 col-12 ">
                                    <h6 class="mb-3" id="replytitle" style="display: none;">Reply to this message</h6>
                                    <form method="POST" enctype="multipart/form-data" id="replyForm" style="display: none;">
                                    
                                    @csrf
                                    <input type="hidden" name="helpdesk_id" class="helpdesk_id" value="{{ $findHelpdesk->id }}" data-helpdesk-id="{{ $findHelpdesk->id }}">
                                    @if(!empty(auth()->user()))
                                        <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
                                    @endif
                                    <div>
                                        <div class="row">
                                            <!-- File input for attachments -->
                                            <div class="form-group col-md-12">
                                                <label class="form-control-label form-label" for="helpdesk_activity_attachment">{{ __('messages.image') }}</label>
                                                <div class="custom-file">
                                                    <input type="file" name="helpdesk_activity_attachment[]" class="form-control"
                                                        data-file-error="{{ __('messages.files_not_allowed') }}" accept="image/*">
                                                    <!-- <label class="custom-file-label upload-label">{{ __('messages.choose_file', ['file' => __('messages.attachments')]) }}</label> -->
                                                </div>
                                            </div>

                                            <!-- Description text area -->
                                            <div class="form-group col-md-12">
                                                {{ html()->label(trans('messages.description'). ' <span class="text-danger">*</span>', 'description')->class('form-control-label form-label') }}
                                                {{ html()->textarea('description', null)->class('form-control textarea')->required()->rows(3)->placeholder(__('messages.description')) }}
                                                <span class="text-danger description-error" style="display: none;">{{ __('messages.description_required') }}</span>
                                            </div>
                                        </div>

                                        <div class="modal-footer gap-2 px-0">
                                            <!-- Link to mark as closed -->
                                            <!-- <a href="{{ route('helpdesk.closed', ['id' => $findHelpdesk->id]) }}" class="btn btn-md btn-secondary mx-4">{{ __('messages.marked_closed') }}< -->
                                            <!-- Submit button with id -->
                                            <button type="button" class="btn btn-md btn-primary p-3 closeReply">{{ __('messages.marked_closed') }}</button>
                                            <button type="button" class="btn btn-md btn-primary p-3 saveReply">{{__('messages.reply')}}</button>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                               
                            </div>
                            <p class="text-danger mt-2">* You can mark this as closed if you are satisfied with our answer.</p>
                        </div>
                    </div>
                </div>
            @endif
    </div>
</div>
@endsection
@section('after_script')
<script>
    $(document).ready(function() {

        const baseUrl = document.querySelector('meta[name="baseUrl"]').getAttribute('content');
        function setLoading(button, isLoading) {
            if (isLoading) {
                // Show loader text and disable button
                button.prop('disabled', true);
                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
            } else {
                // Restore button text and enable button
                button.prop('disabled', false);
                button.html(button.data('original-text'));
            }
        }
        $('.saveReply').off('click').on('click', function() {
            const form = $(this).closest('form');
            const description = form.find('textarea[name="description"]').val().trim();

            // Clear previous error message
            $('.description-error').hide();

            if (!description) {
                // Show error if description is empty
                $('.description-error').show();
                return;
            }
            const button = $(this);
            button.data('original-text', button.html()); // Save original button text
            setLoading(button, true);

            const formData = new FormData();

            // Append the description text from the textarea
            // const description = form.find('textarea[name="description"]').val();
            formData.append('description', description);

            // Append the helpdesk_id and user_id
            const helpdesk_id = form.find('.helpdesk_id').val();
            const user_id = $('#user_id').val();
            formData.append('helpdesk_id', helpdesk_id);
            formData.append('user_id', user_id);

            // Get files from the file input
            const fileInput = form.find('input[name="helpdesk_activity_attachment[]"]')[0];
            const files = fileInput.files;
            for (let i = 0; i < files.length; i++) {
                formData.append(`helpdesk_activity_attachment_${i}`, files[i]); // Dynamic key for each attachment
            }
            formData.append('attachment_count', files.length); // Add the count of attachments

            formData.append('_token', '{{ csrf_token() }}');  // CSRF token

            $.ajax({
                url: baseUrl + '/api/helpdesk-activity-save/' + helpdesk_id,
                type: 'POST',
                data: formData,
                contentType: false,  // Required for file uploads
                processData: false,  // Required for FormData
                success: function(response) {
                    Swal.fire({
                        title: 'Done',
                        text: response.message,
                        icon: 'success',
                        iconColor: '#5F60B9'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            setLoading(button, false);
                            window.location.reload();
                        }
                    })
                },
                error: function(error) {
                    setLoading(button, false);
                    console.error('Error:', error);
                }
            });
        });


        $('.closeReply').off('click').on('click', function() {
            const form = $(this).closest('form');
            const button = $(this);
            button.data('original-text', button.html()); // Save original button text
            setLoading(button, true);
            const userId = $('#user_id').val();
            const helpdesk_id = form.find('.helpdesk_id').data('helpdesk-id');
            $.ajax({
                url: baseUrl + '/api/helpdesk-closed/' + helpdesk_id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    userId: userId,
                    helpdesk_id : helpdesk_id,
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Done',
                        text: response.message,
                        icon: 'success',
                        iconColor: '#5F60B9'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            setLoading(button, false);
                            window.location.href = `${baseUrl}/helpdesk-list`;
                        }
                    })
                },
                error: function(error) {
                    setLoading(button, false);
                    console.error('Error', error);
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
    const replyButton = document.getElementById('replyButton');

    // Check if the button exists to avoid errors if the element is not rendered
    if (replyButton) {
        replyButton.addEventListener('click', function() {
            const replyForm = document.getElementById('replyForm');
            const replytitle = document.getElementById('replytitle');
            if (replyForm.style.display === 'none' || replyForm.style.display === '') {
                replyForm.style.display = 'block';
                replytitle.style.display = 'block';
                replyButton.innerText = 'Close';  // Change button text to "Close"
            } else {
                replyForm.style.display = 'none';
                replytitle.style.display = 'none';
                replyButton.innerText = 'Reply';  // Change button text back to "Reply"
            }
        });
    }
});
</script>

@endsection
