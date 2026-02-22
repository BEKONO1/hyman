<div class="row">
    <div class="col-md-4">
        <div class="row">
            {{ html()->hidden('id',$data->id ??  null) }}
            {{ html()->hidden('type', $data->type ?? null) }}
            {{ html()->hidden('defaultNotificationTemplateMap[template_id]', $data->id ?? null) }}
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ (__('Type')) }} : <span class="text-danger">*</span></label>

                    <select name="type" class="select2js form-control" id="type" data-ajax--url="{{ route('mailtemplates.ajax-list',['type' => 'constants_key','data_type' => 'notification_type']) }}" data-ajax--cache="true" required disabled>
                        @if(isset($data->type))
                        <option value="{{ $data->type }}" selected>{{ $data->constant->name ?? '' }}</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ (__('Parameters')) }} :</label><br>
                    <div class="main_form">
                        @if(isset($buttonTypes))
                        @include('mailtemplates.perameters-buttons',['buttonTypes' => $buttonTypes])
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label" for="status">{{ __('messages.status') }} :</label>
                        <div class="custom-control custom-switch">
                            {{ html()->checkbox('status', $data->status, null)
                            ->class('custom-control-input')
                            ->id('status') 
                            }}                            
                        <label class="custom-control-label" for="status"></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ __('To') }} :</label><br>
                    <select name="to[]" id="toSelect" class="select2js form-control" data-ajax--url="{{ route('notificationtemplates.ajax-list',['type' => 'constants_key','data_type' => 'notification_to']) }}" data-ajax--cache="true" multiple>
                        @if(isset($data) && $data->to != null)
                        @foreach(json_decode($data->to) as $to)
                        <option value="{{$to}}" selected="">{{$to}}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ (__('BCC')) }} :</label><br>
                    <select class="form-select select2-tag" name="bcc[]" multiple="">
                        @if(isset($data))
                        @if($data->bcc != null)
                        @foreach(json_decode($data->bcc) as $bcc)
                        <option value="{{$bcc}}" selected="">{{$bcc}}</option>
                        @endforeach
                        @endif
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ (__('CC')) }} :</label><br>
                    <select class="form-select select2-tag" name="cc[]" multiple="">
                        @if(isset($data))
                        @if($data->cc != null)
                        @foreach(json_decode($data->cc) as $cc)
                        <option value="{{$cc}}" selected="">{{$cc}}</option>
                        @endforeach
                        @endif
                        @endif
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8 ">
        <div class="row ps-3">
            <div class="col-md-12">
                @php
                $toValues = json_decode($data->to, true) ?? [];
                @endphp
               
<div class="form-group">
    {{ html()->label(__('messages.user_type') . ' <span class="text-danger">*</span>', 'user_type')
        ->class('form-control-label')
    }}
    {{ html()->select('defaultMailTemplateMap[user_type]', array_combine($toValues, $toValues), $defaultMailTemplateMap['user_type'] ?? null)
        ->class('form-select select2js')
        ->required() 
    }}
</div>

<div class="form-group">
    <label class="float-start">{{ (__('messages.subject')) }} :</label>
    {{ html()->text('defaultMailTemplateMap[subject]', null)->class('form-control') }}

    {{ html()->hidden('defaultMailTemplateMap[status]', 1)->class('form-control')  }}
</div>
<div class="text-left">
    <label>{{ (__('messages.template')) }} :</label>
    {{ html()->hidden('defaultMailTemplateMap[language]', 'en') }}
</div>
<div class="form-group">
    {{ html()->textarea('defaultMailTemplateMap[template_detail]', null)
        ->class('form-control textarea tinymce-template')
        ->id('mytextarea') 
    }}
</div>
<div class="form-group">
    <label class="float-start">{{ (__('messages.notification_body')) }} :</label>
    {{ html()->text('defaultMailTemplateMap[notification_message]', null)
        ->class('form-control notification_message')
        ->id('en-notification_message') 
    }}
</div>
<div class="form-group">
    <label class="float-start">{{ (__('messages.notification_link')) }} :</label>
    {{ html()->text('defaultMailTemplateMap[notification_link]', null)
        ->class('form-control notification_link')
        ->id('en-notification_link') 
    }}
</div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 pt-2">
            <button type="submit" class="btn btn-primary"><i class="far fa-save"></i> {{ (__('save'))}}<i class="md md-lock-open"></i></button>
            <button onclick="window.history.back();" class="btn btn-outline-primary"><i class="fa-solid fa-angles-left"></i> {{ (__('close'))}}<i class="md md-lock-open"></i></button>
        </div>
    </div>
</div>
<script>
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
        var url = "{{ route('mailtemplates.notification-buttons',['type' => 'buttonTypes']) }}";
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

        $('select[name="defaultMailTemplateMap[user_type]"]').off('change').on('change', function() {
            var userType = $(this).val();
            var type = $('select[name="type"]').val();

            $.ajax({
                url: "{{ route('mailtemplates.fetch_data') }}",
                method: "GET",
                data: {
                    user_type: userType,
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        $("input[name='defaultMailTemplateMap[subject]']").val(data.subject);
                        $("textarea[name='defaultMailTemplateMap[template_detail]']").val(data.template_detail);
                        tinymce.get('mytextarea').setContent(data.template_detail);
                        $("input[name='defaultMailTemplateMap[notification_message]']").val(data.notification_message);
                        $("input[name='defaultMailTemplateMap[notification_link]']").val(data.notification_link);
                        console.log('change');
                    } else {
                        $("input[name='defaultMailTemplateMap[subject]']").val('');
                        $("textarea[name='defaultMailTemplateMap[template_detail]']").val('');
                        tinymce.get('mytextarea').setContent('');
                        $("input[name='defaultMailTemplateMap[notification_message]']").val('');
                        $("input[name='defaultMailTemplateMap[notification_link]']").val('');
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
</script>