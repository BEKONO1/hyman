<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? __('messages.list') }}</h5>
                            <a href="{{ route('notification-templates.index') }}" class="float-end btn btn-sm btn-primary"><i
                                    class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                {{ html()->form('PATCH', route('notification-templates.update', $data->id))->attribute('button-loader', 'true') ->open() }}
                                {{ html()->hidden('id',$data->id ??  null) }}
                                {{ html()->hidden('type', $data->type ?? null) }}
                                {{ html()->hidden('defaultNotificationTemplateMap[template_id]', $data->id ?? null) }}
                                <div class="row">
                                    <div class="form-group col-md-3">
                                            <label>{{ (__('Type')) }} : <span class="text-danger">*</span></label>
                                            <select name="type" class="select2js form-control" id="type" data-ajax--url="{{ route('notificationtemplates.ajax-list',['type' => 'constants_key','data_type' => 'notification_type']) }}" data-ajax--cache="true" required disabled>
                                                @if(isset($data->type))
                                                <option value="{{ $data->type }}" selected>{{ $data->constant->name ?? '' }}</option>
                                                @endif
                                            </select>
                                    </div>
                            
                                    <div class="form-group col-md-3">
                                        <label>{{ __('To') }} :</label><br>
                                         <select name="to[]" id="toSelect" class="select2js form-control" data-ajax--url="{{ route('notificationtemplates.ajax-list',['type' => 'constants_key','data_type' => 'notification_to']) }}" data-ajax--cache="true" multiple>
                                             @if(isset($data) && $data->to != null)
                                             @foreach(json_decode($data->to) as $to)
                                             <option value="{{$to}}" selected="">{{$to}}</option>
                                             @endforeach
                                             @endif
                                         </select>
                                    </div>
                                    <div class="form-group col-md-3">

                                        @php
                                            $toValues = json_decode($data->to, true) ?? [];
                                        @endphp
                                        {{ html()->label(__('messages.user_type') . ': <span class="text-danger">*</span>', 'user_type')->class('form-control-label') }}
                                        {{ html()->select('defaultNotificationTemplateMap[user_type]', $toValues, null)->class('form-select select2js')->id('userTypeSelect')->required() }}
                                    </div>
                                    <div class="form-group col-md-3">
                                        {{ html()->label(trans('messages.status') . ':', 'status')->class('form-control-label') }}
                                        {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')],$data->status )->id('role')->class('form-select select2js')->required() }}
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                                <label>{{ (__('Parameters')) }} :</label><br>
                                            <div class="main_form">
                                                @if(isset($buttonTypes))
                                                    @include('notificationtemplates.perameters-buttons',['buttonTypes' => $buttonTypes])
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                            
                                    <div class="col-md-12 mt-5">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <h4>{{ __('messages.notification_template') }}</h4>
                                                </div>
                            
                                                <div class="form-group">
                                                    <label class="float-start">{{ __('messages.subject') }} :</label>
                                                        <input type="text" name="defaultNotificationTemplateMap[subject]" value="" class="form-control">
                                                        <input type="hidden" name="defaultNotificationTemplateMap[status]" value="1" class="form-control">
                                                </div>
                            
                                                <div class="text-left">
                                                    <label>{{ (__('messages.template')) }} :</label>
                                                    {{ html()->hidden('defaultNotificationTemplateMap[language]', 'en') }}
                                                </div>
                                                <div class="form-group">
                                                    {{ html()->textarea('defaultNotificationTemplateMap[template_detail]')->class('form-control textarea tinymce-template')->id('notification_mytextarea') }}
                                                </div>
                            
                                            </div>
                            
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">

                                                    <h4>{{ __('messages.mail_template') }}</h4>

                                                </div>
                            
                                                <div class="form-group">
                                                    <label class="float-start">{{ (__('messages.subject')) }} :</label>
                                                    {{ html()->text('defaultNotificationTemplateMap[mail_subject]',$data->defaultNotificationTemplateMap['mail_subject'] ?? '')->class('form-control') }}
                                                    {{ html()->hidden('defaultNotificationTemplateMap[status]', 1)->class('form-control') }}
                                                </div>
                            
                                                <div class="text-left">
                                                    <label>{{ (__('messages.template')) }} :</label>
                                                    {{ html()->hidden('defaultNotificationTemplateMap[language]', 'en') }}
                                                </div>
                            
                                                <div class="form-group">
                                                    {{ html()->textarea('defaultNotificationTemplateMap[mail_template_detail]')->class('form-control textarea tinymce-template')->id('mail_mytextarea') }}
                                                </div>
                            
                                            </div>

                                            <!-- whatsapp and sms -->

                                            <div class="col-md-6">
                                                <div class="form-group mb-3">

                                                    <h4>{{ __('messages.sms_template') }}</h4>

                                                </div>
                            
                                                <div class="form-group">
                                                    <label class="float-start">{{ (__('messages.subject')) }} :</label>
                                                    {{ html()->text('defaultNotificationTemplateMap[sms_subject]',$data->defaultNotificationTemplateMap['sms_subject'] ?? '')->class('form-control') }}
                                                    {{ html()->hidden('defaultNotificationTemplateMap[status]', 1)->class('form-control') }}
                                                </div>
                            
                                                <div class="text-left">
                                                    <label>{{ (__('messages.template')) }} :</label>
                                                    {{ html()->hidden('defaultNotificationTemplateMap[language]', 'en') }}
                                                </div>
                            
                                                <div class="form-group">
                                                    {{ html()->textarea('defaultNotificationTemplateMap[sms_template_detail]')->class('form-control textarea tinymce-template')->id('sms_mytextarea') }}
                                                </div>
                            
                                            </div>
                                             
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">

                                                    <h4>{{ __('messages.whatsapp_template') }}</h4>

                                                </div>
                            
                                                <div class="form-group">
                                                    <label class="float-start">{{ (__('messages.subject')) }} :</label>
                                                    {{ html()->text('defaultNotificationTemplateMap[whatsapp_subject]',$data->defaultNotificationTemplateMap['whatsapp_subject'] ?? '')->class('form-control') }}
                                                    {{ html()->hidden('defaultNotificationTemplateMap[status]', 1)->class('form-control') }}
                                                </div>
                            
                                                <div class="text-left">
                                                    <label>{{ (__('messages.template')) }} :</label>
                                                    {{ html()->hidden('defaultNotificationTemplateMap[language]', 'en') }}
                                                </div>
                            
                                                <div class="form-group">
                                                    {{ html()->textarea('defaultNotificationTemplateMap[whatsapp_template_detail]')->class('form-control textarea tinymce-template')->id('whatsapp_mytextarea') }}
                                                </div>
                            
                                            </div>
                                             
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="row">
                                    <div class="col-md-12 pt-2 text-right">
                                    <button type="submit" class="btn btn-primary"> {{ (__('save'))}}<i class="md md-lock-open"></i></button>
                                </div>
                            </div>
                            

                                {{ html()->form()->close() }}
                        </div>

                        </div>
                    </div>
                </div>
            </div>


</div>
</div>
@section('bottom_script')
<script type="text/javascript">
    (function($) {
        $(document).ready(function() {
            tinymceEditor('.tinymce-template', ' ', function(ed) {

            }, 450)

        });

    })(jQuery);

    $(document).ready(function() {
        $('.select2-tag').select2({
            tags: true,
            createTag: function(params) {
                if (params.term.length > 2) {
                    return {
                        id: params.term,
                        text: params.term,
                        newTag: true
                    }
                }
                return null;
            }
        });
    });

    function onChangeType(url, render) {
        var dropdown = document.getElementById("type");
        var selectedValue = dropdown.value;
        var url = "{{ route('notificationtemplates.notification-buttons',['type' => 'buttonTypes']) }}";
        $.get(url, function(data) {
            var html = data;
            if (render !== undefined && render !== '' && render !== null) {
                $('.' + render).html(html);
            } else {
                $(".main_form").html(html);
                $("#formModal").modal("show");
            }
        });
    }

    $(document).ready(function() {
        $('.select2js').select2();

        $('select[name="defaultNotificationTemplateMap[user_type]"]').off('change').on('change', function() {
            var userType = $(this).val();
            var type = $('select[name="type"]').val();
            $.ajax({
                url: "{{ route('notificationtemplates.fetchnotification_data') }}",
                method: "GET",
                data: {
                    user_type: userType,
                    type: type
                },
                success: function(response) {

                    if (response.success) {
                        var data = response.data
                        var notification_template_data = response.notification_template_data

                        if(data){

                            $("input[name='defaultNotificationTemplateMap[subject]']").val(data.subject);
                            $("textarea[name='defaultNotificationTemplateMap[template_detail]']").val(data.template_detail);
                            tinymce.get('notification_mytextarea').setContent(data.template_detail);

                        }else{
                            
                            $("input[name='defaultNotificationTemplateMap[subject]']").val('');
                            $("textarea[name='defaultNotificationTemplateMap[template_detail]']").val('');
                            tinymce.get('notification_mytextarea').setContent('');

                        }

                        if(data){

                            $("input[name='defaultNotificationTemplateMap[mail_subject]']").val(data.mail_subject);
                            $("textarea[name='defaultNotificationTemplateMap[mail_template_detail]']").val(data.mail_template_detail);
                            tinymce.get('mail_mytextarea').setContent(data.mail_template_detail);

                        }else{

                            $("input[name='defaultNotificationTemplateMap[mail_subject]']").val('');
                            $("textarea[name='defaultNotificationTemplateMap[mail_template_detail]']").val('');
                            tinymce.get('mail_mytextarea').setContent('');    
                        }

                        if(data){

                            $("input[name='defaultNotificationTemplateMap[sms_subject]']").val(data.sms_subject);
                            $("textarea[name='defaultNotificationTemplateMap[sms_template_detail]']").val(data.sms_template_detail);
                            tinymce.get('sms_mytextarea').setContent(data.sms_template_detail);

                        }else{

                            $("input[name='defaultNotificationTemplateMap[sms_subject]']").val('');
                            $("textarea[name='defaultNotificationTemplateMap[sms_template_detail]']").val('');
                            tinymce.get('sms_mytextarea').setContent('');    
                        }

                        if(data){

                            $("input[name='defaultNotificationTemplateMap[whatsapp_subject]']").val(data.whatsapp_subject);
                            $("textarea[name='defaultNotificationTemplateMap[whatsapp_template_detail]']").val(data.whatsapp_template_detail);
                            tinymce.get('whatsapp_mytextarea').setContent(data.whatsapp_template_detail);

                        }else{

                            $("input[name='defaultNotificationTemplateMap[whatsapp_subject]']").val('');
                            $("textarea[name='defaultNotificationTemplateMap[whatsapp_template_detail]']").val('');
                            tinymce.get('whatsapp_mytextarea').setContent('');    
                        }


                    } else {
                        $("input[name='defaultNotificationTemplateMap[subject]']").val('');
                        $("textarea[name='defaultNotificationTemplateMap[template_detail]']").val('');
                        tinymce.get('notification_mytextarea').setContent('');
                        $("input[name='defaultNotificationTemplateMap[mail_subject]']").val('');
                        $("textarea[name='defaultNotificationTemplateMap[mail_template_detail]']").val('');
                        tinymce.get('mail_mytextarea').setContent('');
                        $("input[name='defaultNotificationTemplateMap[sms_subject]']").val('');
                        $("textarea[name='defaultNotificationTemplateMap[sms_template_detail]']").val('');
                        tinymce.get('sms_mytextarea').setContent('');
                        $("input[name='defaultNotificationTemplateMap[whatsapp_subject]']").val('');
                        $("textarea[name='defaultNotificationTemplateMap[whatsapp_template_detail]']").val('');
                        tinymce.get('whatsapp_mytextarea').setContent('');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });
    });
    $(document).ready(function() {
        var toSelect = $('#toSelect');
        var userTypeSelect = $('#userTypeSelect');

        function updateUserTypeOptions(selectedValues) {
            userTypeSelect.empty();

            if (selectedValues) {
                selectedValues.forEach(function(value) {
                    userTypeSelect.append(new Option(value, value));
                });
            }
            userTypeSelect.trigger('change');
        }

        var initialSelectedValues = toSelect.val();
        updateUserTypeOptions(initialSelectedValues);

        toSelect.on('change', function() {
            var selectedValues = $(this).val();
            updateUserTypeOptions(selectedValues);
        });

        toSelect.select2();
        userTypeSelect.select2();
    });
    $(document).on('click', '#variable_button', function() {
     
            const textarea = $(document).find('.tab-pane.active');
            const textareaID = textarea.find('textarea').attr('id');
            tinyMCE.activeEditor.selection.setContent($(this).attr('data-value'));
        });
   </script>
  @endsection
</x-master-layout>
