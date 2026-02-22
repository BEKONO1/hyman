{{ html()->form('POST', route('paymentsettingsUpdates'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}
{{ html()->hidden('id',$payment_data->id ?? null )->attribute('placeholder', 'id')->class('form-control') }}
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
        {{ html()->label(trans('messages.gateway_name').' <span class="text-danger">*</span>', 'title', ['class' => 'form-control-label']) }}
        {{ html()->text('title',old('title'))
            ->id('title')
            ->placeholder(trans('messages.title'))
            ->class('form-control')
        }}
        <small class="help-block with-errors text-danger"></small>
    </div>
    
    <div class="form-group col-md-12">
        {{ html()->label(trans('messages.razorx_url').' <span class="text-danger">*</span>', 'razorx_url', ['class' => 'form-control-label']) }}
        {{ html()->text('razorx_url',old('razorx_url'))
            ->id('razorx_url')
            ->placeholder(trans('messages.razorx_url'))
            ->class('form-control')
        }}
        <small class="help-block with-errors text-danger"></small>
    </div>
    
    <div class="form-group col-md-12">
        {{ html()->label(trans('messages.razorx_account').' <span class="text-danger">*</span>', 'razorx_account', ['class' => 'form-control-label']) }}
        {{ html()->text('razorx_account',old('razorx_account'))
            ->id('razorx_account')
            ->placeholder(trans('messages.razorx_account'))
            ->class('form-control')
        }}
        <small class="help-block with-errors text-danger"></small>
    </div>
    
    <div class="form-group col-md-12">
        {{ html()->label(trans('messages.razorx_key').' <span class="text-danger">*</span>', 'razorx_key', ['class' => 'form-control-label']) }}
        {{ html()->text('razorx_key',old('razorx_key'))
            ->id('razorx_key')
            ->placeholder(trans('messages.razorx_key'))
            ->class('form-control')
        }}
        <small class="help-block with-errors text-danger"></small>
    </div>
    
    <div class="form-group col-md-12">
        {{ html()->label(trans('messages.razorx_secret').' <span class="text-danger">*</span>', 'razorx_secret', ['class' => 'form-control-label']) }}
        {{ html()->text('razorx_secret',old('razorx_secret'))
            ->id('razorx_secret')
            ->placeholder(trans('messages.razorx_secret'))
            ->class('form-control')
            ->required()
        }}
        <small class="help-block with-errors text-danger"></small>
    </div>
</div>
{{ html()->submit(__('messages.save'))->class("btn btn-md btn-primary float-md-end") }}
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
        $('#razorx_url').prop('required', true);
        $('#razorx_account').prop('required', true);
        $('#razorx_key').prop('required', true);
        $('#razorx_secret').prop('required', true);
    }else{
      
        $('#enable_razorpay_payment').addClass('d-none');
        $('#title').prop('required', false);
        $('#razorx_url').prop('required', false);
        $('#razorx_account').prop('required', false);
        $('#razorx_key').prop('required', false);
        $('#razorx_secret').prop('required', false);
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
            var razorx_url=razorx_key=razorx_secret=razorx_account=title = '';

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
                    var razorx_url = obj.razorx_url;
                    var razorx_account = obj.razorx_account;
                    var razorx_key = obj.razorx_key;
                    var razorx_secret = obj.razorx_secret;
                }

                $('#razorx_key').val(razorx_key)
                $('#razorx_account').val(razorx_account)
                $('#razorx_key').val(razorx_key)
                $('#razorx_secret').val(razorx_secret)
                $('#razorx_url').val(razorx_url)
                $('#title').val(title)
            
            }
        },
        error: function(error) {
         console.log(error);
        }
    });
}
</script>