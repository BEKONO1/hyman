
{{ html()->form('POST', route('saveLangContent'))->attribute('data-toggle', 'validator')->id('flatArray')->open() }}
<input type="hidden" value="{{$filename}}" name="filename"/>
<input type="hidden" value="{{$requestLang}}" name="requestLang"/>
<div class="table-responsive mb-0">
    <table class="table lang_table table-sm table-fixed">
        <thead>
            <tr class="text-secondary">
            <th scope="col">{{__('messages.key')}}</th>
            <th scope="col">{{__('messages.value')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($flatArray as $key => $val)
               <tr>
                   <td>{{$key}}</td>
                   <td><input type = "text" class ="form-control" name ="{{$key}}" value="{{$val}}" /></td>
               </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end submit_section1') }}
{{ html()->form()->close() }}
