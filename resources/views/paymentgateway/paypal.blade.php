{{ html()->form('POST', route('paymentsettingsUpdates'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}
{{ html()->hidden('id',$payment_data->id ?? null )->attribute('placeholder', 'id')->class('form-control') }}
{{ html()->hidden('type', $tabpage)->attribute('placeholder', 'id')->class('form-control') }}
 <div class="row">
    <div class="form-group col-md-12" >
        <div class="form-control d-flex align-items-center justify-content-between">
            <label for="enable_paypal" class="mb-0">{{__('messages.payment_on',['gateway'=>__('messages.paypal')])}}</label>
            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inlineh">
                <input type="checkbox" class="custom-control-input" name="status" id="enable_paypal" {{!empty($payment_data) && $payment_data->status == 1 ? 'checked' : ''}}>
                <label class="custom-control-label" for="enable_paypal"></label>
            </div>
        </div>
    </div>
 </div>
 <div class="row" id='enable_paypal_payment'>
    <div class="form-group col-md-12">
        <label class="form-control-label">{{__('messages.payment_option',['gateway'=>__('messages.paypal')])}}</label><br/>
        <div class="form-check-inline">
            <label class="form-check-label">
                <input type="radio" class="form-check-input is_test" value="on" name="is_test" data-type="is_test_mode" {{!empty($payment_data) && $payment_data->is_test == 1 ? 'checked' :''}}>{{__('messages.is_test_mode')}}
            </label>
        </div>
        <div class="form-check-inline">
            <label class="form-check-label">
                <input type="radio" class="form-check-input is_test" value="off" name="is_test" data-type="is_live_mode" {{!empty($payment_data) && $payment_data->is_test == 0 ? 'checked' :''}}>{{__('messages.is_live_mode')}}
            </label>
        </div>
        <small class="help-block with-errors text-danger"></small>
    </div>
    <div class="form-group col-md-12">
        {{ html()->label(trans('messages.gateway_name').' <span class="text-danger">*</span>', 'title', ['class' => 'form-control-label']) }}
        {{ html()->text('title', old('title') )
            ->id('title')
            ->placeholder(trans('messages.title'))
            ->class('form-control')
        }}
        <small class="help-block with-errors text-danger"></small>
    </div>
    <div class="form-group col-md-12">
        {{ html()->label(trans('messages.paypal_client_id').' <span class="text-danger">*</span>', 'paypal_client_id', ['class' => 'form-control-label']) }}
        {{ html()->text('paypal_client_id', old('paypal_client_id') )
            ->id('paypal_client_id')
            ->placeholder(trans('messages.paypal_client_id'))
            ->class('form-control')
        }}
        <small class="help-block with-errors text-danger"></small>
    </div>
    <div class="form-group col-md-12">
        {{ html()->label(trans('messages.paypal_secret_key').' <span class="text-danger">*</span>', 'paypal_secret_key', ['class' => 'form-control-label']) }}
        {{ html()->text('paypal_secret_key', old('paypal_secret_key') )
            ->id('paypal_secret_key')
            ->placeholder(trans('messages.paypal_secret_key'))
            ->class('form-control')
        }}
        <small class="help-block with-errors text-danger"></small>
    </div>
    </div>
    {{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end') }}
    {{ html()->form()->close() }}
<script>
var enable_paypal = $("input[name='status']").prop('checked');
checkPaymentTabOption(enable_paypal);

$('#enable_paypal').change(function(){
    value = $(this).prop('checked') == true ? true : false;
    checkPaymentTabOption(value);
});
function checkPaymentTabOption(value){
    if(value == true){
        $('#enable_paypal_payment').removeClass('d-none');
        $('#title').prop('required', true);
        $('#paypal_client_id').prop('required', true);
        $('#paypal_secret_key').prop('required', true);
    }else{
        $('#enable_paypal_payment').addClass('d-none');
        $('#title').prop('required', false);
        $('#paypal_client_id').prop('required', false);
        $('#paypal_secret_key').prop('required', false);
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
            var paypal_client_id=paypal_secret_key=title = '';

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
                    var paypal_client_id = obj.paypal_client_id;
                    var paypal_secret_key = obj.paypal_secret_key;
                }

                $('#paypal_client_id').val(paypal_client_id)
                $('#paypal_secret_key').val(paypal_secret_key)
                $('#title').val(title)
            
            }
        },
        error: function(error) {
         console.log(error);
        }
    });
}

</script>