<x-master-layout>
    
    <main class="main-area">
        <div class="main-content">
            <div class="container-fluid">
                <div class="page-title-wrap d-flex justify-content-between gap-3 flex-wrap mb-3 p-3">
                    <h2 class="page-title">{{ $pageTitle ?? '-' }}</h2>
                    <a href="{{ route('helpdesk.index') }}" class="float-end btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                </div>
                <div class="card">
                    <div class="card-body p-30">
                        <div class="helpdesk-details-overview row">
                            <div class="helpdesk-details-overview__statistics col-md-5">
                                <div class="statistics-card statistics-card__style2 statistics-card__order-overview">
                                    <table class="table mb-0">
                                        <tr>
                                            <td width="25%"><span>{{ __('messages.id') }} : </span></td>
                                            <td class=""><strong>#{{ !empty($helpdeskdata->id) ? $helpdeskdata->id : '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td width="25%"><span>{{ __('messages.datetime') }} : </span></td>
                                            <td class=""><strong>{{ !empty($helpdeskdata->updated_at) ? date("$datetime->date_format $datetime->time_format", strtotime($helpdeskdata->updated_at)) : '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td width="25%"><span>{{ __('messages.name') }} : </span></td>
                                            <td class=""><strong>{{ !empty($helpdeskdata->employee_id) ? optional($helpdeskdata->users)->first_name . ' ' . optional($helpdeskdata->users)->last_name . ' (' . ucfirst(optional($helpdeskdata->users)->user_type) . ')' : '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td width="25%"><span>{{ __('messages.mode') }} : </span></td>
                                            <td class="">
                                                <strong>
                                                    {{ !empty($helpdeskdata->mode) ? ucfirst($helpdeskdata->mode) : '-' }}
                                                    <span>
                                                        @if($helpdeskdata->mode === 'phone')
                                                            ({{ !empty($helpdeskdata->contact_number) ? $helpdeskdata->contact_number : optional($helpdeskdata->users)->contact_number }})
                                                        @elseif($helpdeskdata->mode === 'email')
                                                            ({{ !empty($helpdeskdata->email) ? $helpdeskdata->email : optional($helpdeskdata->users)->email }})
                                                        @endif
                                                    </span>
                                                </strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <h5 class="mb-2 mt-4">{{ __('messages.helpdesk') }} {{ __('messages.detail') }}</h5>
                                <div class="statistics-card statistics-card__style2 statistics-card__order-overview">
                                    <span class="helpdesk-status">
                                        @if($helpdeskdata->status == '0')
                                            <span class="badge text-white bg-success text-uppercase">Open</span>
                                        @else
                                            <span class="badge text-white bg-danger text-uppercase">Closed</span>
                                        @endif
                                    </span>
                                    <table class="table mb-0">
                                        <tr>
                                            <td width="25%"><span>{{ __('messages.subject') }} : </span></td>
                                            <td class=""><strong>{{ !empty($helpdeskdata->subject) ? $helpdeskdata->subject : '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td width="25%"><span>{{ __('messages.description') }} : </span></td>
                                            <td>
                                                <div class="d-flex gap-2 align-items-center">
                                                    @php
                                                        $attachmentUrls = getAttachments($helpdeskdata->getMedia('helpdesk_attachment'));
                                                        $firstAttachmentUrl = $attachmentUrls[0] ?? null; // Use a default image if empty
                                                    @endphp
                                                    @if($firstAttachmentUrl !== null)
                                                    <img src="{{ $firstAttachmentUrl }}" alt="avatar" class="avatar avatar-40">   
                                                    @endif             
                                                    <div class="text-start">
                                                        <span class="">{{ $helpdeskdata->description ?? '--' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>                                      
                                </div>
                            </div>
                            
                            <div class="rounded-2 helpdesk-note col-md-7">
                                <h5 class="mb-1 ">{{ __('messages.note') }}</h5>
                                <div class="statistics-card statistics-card__order-overview">
                                @if(optional($helpdeskdata->helpdeskactivity)->isNotEmpty() && count($helpdeskdata->helpdeskactivity) <= 1 && $helpdeskdata->status == 0)
                                {{ html()->form('POST', route('helpdesk.activity', $helpdeskdata->id))
                                    ->attribute('enctype', 'multipart/form-data')
                                    ->attribute('data-toggle', 'validator')
                                    ->id('helpdeskactivty-form')
                                    ->open()
                                }}
                                {{ html()->hidden('helpdesk_id', $helpdeskdata->id ?? null) }}
                                <!-- <form  action="{{ route('helpdesk.activity', $helpdeskdata->id) }}" method="POST" enctype="multipart/form-data" > -->
                                @csrf
                                <div>
                                <div class="row">
                                        <!-- File input for attachments -->
                                        <div class="form-group col-md-12">
                                            <label class="form-control-label" for="helpdesk_activity_attachment">{{ __('messages.image') }}</label>
                                            <div class="custom-file">
                                                <input type="file" name="helpdesk_activity_attachment[]" class="custom-file-input"
                                                    data-file-error="{{ __('messages.files_not_allowed') }}" accept="image/*">
                                                <label class="custom-file-label upload-label">{{ __('messages.choose_file', ['file' => __('messages.attachments')]) }}</label>
                                            </div>
                                        </div>

                                        <!-- Description text area -->
                                        <div class="form-group col-md-12">
                                            {{ html()->label(trans('messages.description'). ' <span class="text-danger">*</span>', 'description')->class('form-control-label') }}
                                            {{ html()->textarea('description', null)->class('form-control textarea')->required()->rows(3)->placeholder(__('messages.description')) }}
                        
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <!-- Link to mark as closed -->
                                        <a href="{{ route('helpdesk.closed', ['id' => $helpdeskdata->id]) }}" class="btn btn-md btn-secondary mx-4">{{ __('messages.marked_closed') }}</a>

                                        <!-- Submit button with id -->
                                        {{ html()->submit( __('messages.save'))->class('btn btn-md btn-primary float-end')->id('replyButton') }}
                                        
                                    </div>
                                    </div>
                                    {{ html()->form()->close() }}
                                    @elseif(count($helpdeskdata->helpdeskactivity) > 0)
                                        <div class="col-md-5 col-lg-12">
                                            <div class="activity-height">
                                                <ul class="iq-timeline provider-timeline">
                                                    <?php date_default_timezone_set($admin->time_zone ?? 'UTC'); ?>
                                                    @foreach($helpdeskdata->helpdeskactivity as $index => $activity)
                                                    <li>
                                                        <div class="timeline-dots">
                                                            <img src="{{ getSingleMedia(optional($activity->sender),'profile_image', null) }}" alt="avatar" class="avatar avatar-40 rounded-pill"> 
                                                        </div>
                                                        @if($activity->activity_type !== 'closed_helpdesk')
                                                        <div class="d-flex justify-content-between gap-2">                                    
                                                            <span class="mb-1">
                                                                {{ __($index === 0 ? 'messages.created_by_helpdesk' : 'messages.replied_by_helpdesk', [
                                                                    'name' => optional($activity->sender)->display_name,
                                                                    'date' => date("$datetime->date_format $datetime->time_format", strtotime($activity->updated_at))
                                                                ])}}
                                                            </span>
                                                            <h6 class="mb-1 text-primary toggle-message" id="open-message-{{ $index }}" style="cursor: pointer;">{{__('messages.show_message')}} <i class="fa fa-angle-down toggle-icon"></i></h6>
                                                        </div>
                                                        
                                                        <div class="d-flex gap-2 align-items-center message-content d-none" id="messages-{{ $index }}" >
                                                            @php
                                                                $attachmentUrls = $index === 0 ? getAttachments($helpdeskdata->getMedia('helpdesk_attachment')) : getAttachments($activity->getMedia('helpdesk_activity_attachment'));
                                                                $firstAttachmentUrl = $attachmentUrls[0] ?? null; // Use a default image if empty
                                                            @endphp
                                                            @if($firstAttachmentUrl !== null)
                                                                <img src="{{ $firstAttachmentUrl }}" alt="avatar" class="avatar avatar-40">
                                                            @endif
                                                                <div class="text-start" id="messages">
                                                                    <span>{{ $activity->messages ?? '--' }}</span>
                                                                </div>
                                                        </div>
                                                        @elseif($activity->activity_type == 'closed_helpdesk')
                                                        <div class="d-flex justify-content-between gap-2">
                                                        <!-- <h6 class="mb-1">{{str_replace("_"," ",ucfirst($activity->activity_type))}}</h6> -->
                                                        <span class="mb-1">
                                                            {{ __('messages.closed_by_helpdesk', [
                                                            'name' => optional($activity->sender)->display_name,
                                                            'date' => date("$datetime->date_format $datetime->time_format", strtotime($activity->updated_at))
                                                            ])}}
                                                        </span>
                                                        </div>
                                                        @endif
                                                    </li>
                                                    @endforeach
                                                    
                                                </ul>
                                                {{ html()->form('POST', route('helpdesk.activity', $helpdeskdata->id))
                                                    ->attribute('enctype', 'multipart/form-data')
                                                    ->attribute('data-toggle', 'validator')
                                                    ->id('replyForm')
                                                    ->style("display: none;")
                                                    ->open()
                                                }}
                                                {{ html()->hidden('helpdesk_id', $helpdeskdata->id ?? null) }}
                                                <!-- <form id="replyForm" action="{{ route('helpdesk.activity', $helpdeskdata->id) }}" method="POST" enctype="multipart/form-data" style="display: none;"> -->
                                                    @csrf
                                                    <div class="row mt-3">
                                                        <!-- File input for attachments -->
                                                        <div class="form-group col-md-12">
                                                            <label class="form-control-label" for="helpdesk_activity_attachment">{{ __('messages.image') }}</label>
                                                            <div class="custom-file">
                                                                <input type="file" name="helpdesk_activity_attachment[]" class="custom-file-input"
                                                                    data-file-error="{{ __('messages.files_not_allowed') }}" accept="image/*">
                                                                <label class="custom-file-label upload-label">{{ __('messages.choose_file', ['file' => __('messages.attachments')]) }}</label>
                                                            </div>
                                                        </div>

                                                        <!-- Description text area -->
                                                        <div class="form-group col-md-12">
                                                            {{ html()->label(trans('messages.description'). ' <span class="text-danger">*</span>', 'description')->class('form-control-label') }}
                                                            {{ html()->textarea('description', null)->class('form-control textarea')->required()->rows(3)->placeholder(__('messages.description')) }}
                        
                                                        </div>
                                                    </div>

                                                    <!-- Link to mark as closed and submit button -->
                                                    <div class="modal-footer">
                                                        <a href="{{ route('helpdesk.closed', ['id' => $helpdeskdata->id]) }}" class="btn btn-md btn-secondary mx-4">{{ __('messages.marked_closed') }}</a>
                                                        
                                                        {{ html()->submit( __('messages.reply'))->class('btn btn-md btn-primary') }}
                                    
                                                    </div>
                                                {{ html()->form()->close() }}
                                                @if($helpdeskdata->status == 0)
                                                <div class="modal-footer mt-2">
                                                    <button class="btn btn-md btn-primary" id="replyButton">{{ __('messages.reply') }}</button>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

</x-master-layout>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const replyButton = document.getElementById('replyButton');

    // Check if the button exists to avoid errors if the element is not rendered
    if (replyButton) {
        replyButton.addEventListener('click', function() {
            const replyForm = document.getElementById('replyForm');
            if (replyForm.style.display === 'none' || replyForm.style.display === '') {
                replyForm.style.display = 'block';
                replyButton.innerText = 'Close';  // Change button text to "Close"
            } else {
                replyForm.style.display = 'none';
                replyButton.innerText = 'Reply';  // Change button text back to "Reply"
            }
        });
    }

    document.querySelectorAll('.toggle-message').forEach(function (toggleButton) {
        toggleButton.addEventListener('click', function () {
            // Find the current index to toggle
            const index = this.id.replace('open-message-', '');
            const messageContent = document.getElementById(`messages-${index}`);
            const icon = this.querySelector('.toggle-icon');

            // Check if the messageContent element exists
            if (messageContent) {
                // Toggle the 'd-none' class to show/hide content
                messageContent.classList.toggle('d-none');
                
                // Update icon based on visibility
                if (messageContent.classList.contains('d-none')) {
                    icon.classList.remove('fa-angle-up');
                    icon.classList.add('fa-angle-down');
                } else {
                    icon.classList.remove('fa-angle-down');
                    icon.classList.add('fa-angle-up');
                }
            } else {
                console.warn(`Element with ID messages-${index} not found.`);
            }
        });
    });
});


</script>
