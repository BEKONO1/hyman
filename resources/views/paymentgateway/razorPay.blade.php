{{ html()->form('POST', route('paymentsettingsUpdates'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}
{{ html()->hidden('id', $payment_data->id ?? null )->attribute('placeholder', 'id')->class('form-control') }}
{{ html()->hidden('type', $tabpage)->attribute('placeholder', 'id')->class('form-control') }}
<div class="row">
    <div class="form-group col-md-12">
        <div class="form-control d-flex align-items-center justify-content-between">
            <label for="enable_razorpay" class="mb-0">{{__('messages.payment_on',['gateway'=>__('messages.razor')])}}</label>
            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                <input type="checkbox" class="custom-control-input" name="status" id="enable_razorpay" {{!empty($payment_data) && $payment_data->status == 1 ? 'checked' : ''}}>
                <label class="custom-control-label" for="enable_razorpay"></label>
            </div>
        </div>
    </div>
</div>
<div class="row"  id='enable_razorpay_payment'>
    <div class="form-group col-md-12">
        <label class="form-control-label">{{__('messages.payment_option',['gateway'=>__('messages.razorpay')])}}</label><br/>
        <div class="form-check-inline">
            <label class="form-check-label">
                <input type="radio" class="form-check-input is_test" value="on" name="is_test" data-type="is_test_mode"  {{!empty($payment_data) &&  $payment_data->is_test == 1 ? 'checked' :''}}>{{__('messages.is_test_mode')}}
            </label>
        </div>
        <div class="form-check-inline">
            <label class="form-check-label">
                <input type="radio" class="form-check-input is_test" value="off" name="is_test" data-type="is_live_mode" {{!empty($payment_data) &&  $payment_data->is_test == 0 ? 'checked' :''}}>{{__('messages.is_live_mode')}}
            </label>
        </div>
        <small class="help-block with-errors text-danger"></small>
    </div>
    <div class="form-group col-md-12">
            {{ html()->label(trans('messages.gateway_name').' <span class="text-danger">*</span>', 'title')->class('form-control-label') }}
            {{ html()->text('title',  old('title'))
                ->id('title')
                ->placeholder(trans('messages.title'))
                ->class('form-control') }}
            <small class="help-block with-errors text-danger"></small>
        </div>
     @php
    $isAdmin = auth()->user()->hasRole('admin'); // Adjust this condition as per your system
@endphp

<div class="form-group col-md-12">
    {{ html()->label(trans('messages.razor_url').' <span class="text-danger">*</span>', 'razor_url')->class('form-control-label') }}
    {{ html()->{$isAdmin ? 'text' : 'password'}('razor_url', old('razor_url'))
        ->id('razor_url')
        ->placeholder(trans('messages.razor_url'))
        ->class('form-control') }}
    <small class="help-block with-errors text-danger"></small>
</div>

<div class="form-group col-md-12">
    {{ html()->label(trans('messages.razor_key').' <span class="text-danger">*</span>', 'razor_key')->class('form-control-label') }}
    {{ html()->{$isAdmin ? 'text' : 'password'}('razor_key', old('razor_key'))
        ->id('razor_key')
        ->placeholder(trans('messages.razor_key'))
        ->class('form-control') }}
    <small class="help-block with-errors text-danger"></small>
</div>

<div class="form-group col-md-12">
    {{ html()->label(trans('messages.razor_secret').' <span class="text-danger">*</span>', 'razor_secret')->class('form-control-label') }}
    {{ html()->{$isAdmin ? 'text' : 'password'}('razor_secret', old('razor_secret'))
        ->id('razor_secret')
        ->placeholder(trans('messages.razor_secret'))
        ->class('form-control') }}
    <small class="help-block with-errors text-danger"></small>
</div>
       
</div>
{{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end') }}
{{ html()->form()->close() }}
<script>
var enable_razorpay = $("input[name='status']").prop('checked');
checkPaymentTabOption(enable_razorpay);


$('#enable_razorpay').change(function(){
    value = $(this).prop('checked') == true ? true : false;
    checkPaymentTabOption(value);
});
function checkPaymentTabOption(value){
    if(value == true){
        $('#enable_razorpay_payment').removeClass('d-none');
        $('#title').prop('required', true);
        $('#razor_url').prop('required', true);
        $('#razor_key').prop('required', true);
        $('#razor_secret').prop('required', true);
    }else{
        $('#enable_razorpay_payment').addClass('d-none');
        $('#title').prop('required', false);
        $('#razor_url').prop('required', false);
        $('#razor_key').prop('required', false);
        $('#razor_secret').prop('required', false);
    }
}
var get_value = $('input[name="is_test"]:checked').data("type");
getConfig(get_value)
$('.is_test').change(function(){
    value = $(this).prop('checked') == true ? true : false;
    type = $(this).data("type");
    getConfig(type)

});

function getConfig(type){
    var _token   = $('meta[name="csrf-token"]').attr('content');
    var baseUrl = $('meta[name="baseUrl"]').attr('content');
    var page =  "{{$tabpage}}";
    $.ajax({
        url: baseUrl+"/get_payment_config",
        type:"POST",
        data:{
          type:type,
          page:page,
          _token: _token
        },
        success:function(response){
            var obj = '';
            var razor_url=razor_key=razor_secret=title = '';

            if(response){
            
                if(response.data.type == 'is_test_mode'){
                    obj = JSON.parse(response.data.value);
                }else{
                    obj = JSON.parse(response.data.live_value);
                }

                if(response.data.title != ''){
                    title = response.data.title
                }
                
                if(obj !== null){
                    var razor_url = obj.razor_url;
                    var razor_key = obj.razor_key;
                    var razor_secret = obj.razor_secret;
                }

                $('#razor_url').val(razor_url)
                $('#razor_key').val(razor_key)
                $('#razor_secret').val(razor_secret)
                $('#title').val(title)
            
            }
        },
        error: function(error) {
         console.log(error);
        }
    });
}
</script>