{{ html()->form('POST', route('notificationtemplates.settings.update'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}

{{ html()->hidden('id',null)->class('form-control')->placeholder('id') }}
{{ html()->hidden('page', $page)->class('form-control')->placeholder('id') }}

    <div class="col-md-12 mt-10 w-100">
        <div class="table-responsive notification-setting-table">
            <table class="table table-condensed">
                <thead>
                <tr>
                
                    <th>{{__('messages.template')}}</th>
                    @foreach($notificationChannels as $key => $value)
                        <th>{{ $value }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                    @foreach($data as $templateData)
                        <tr>
                            
                            <td>{{ $templateData['template'] }}</td>
                            <td>
                                <input type="hidden" class="form-check-input " name="template_channels[{{ $templateData['id'] }}][IS_MAIL]" value="0">
                                <input type="checkbox" class="form-check-input " name="template_channels[{{ $templateData['id'] }}][IS_MAIL]" value="1" {{ $templateData['channels']['IS_MAIL'] ? 'checked' : '' }}>
                            </td>
                            <td>
                                <input type="hidden" class="form-check-input " name="template_channels[{{ $templateData['id'] }}][PUSH_NOTIFICATION]" value="0">
                                <input type="checkbox" class="form-check-input " name="template_channels[{{ $templateData['id'] }}][PUSH_NOTIFICATION]" value="1" {{ $templateData['channels']['PUSH_NOTIFICATION'] ? 'checked' : '' }}>
                            </td>
                            <td>
                                <input type="hidden" class="form-check-input " name="template_channels[{{ $templateData['id'] }}][IS_WHATSAPP]" value="0">
                                <input type="checkbox" class="form-check-input " name="template_channels[{{ $templateData['id'] }}][IS_WHATSAPP]" value="1" {{ $templateData['channels']['IS_WHATSAPP'] ? 'checked' : '' }}>
                            </td>
                            <td>
                                <input type="hidden" class="form-check-input " name="template_channels[{{ $templateData['id'] }}][IS_SMS]" value="0">
                                <input type="checkbox" class="form-check-input " name="template_channels[{{ $templateData['id'] }}][IS_SMS]" value="1" {{ $templateData['channels']['IS_SMS'] ? 'checked' : '' }}>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
{{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end submit_section1') }}
{{ html()->form()->close() }}
