{{ html()->form('POST', route('paymentsettingsUpdates'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}
{{ html()->hidden('id',$payment_data->id ?? null)->attribute('placeholder', 'id')->class('form-control') }}
{{ html()->hidden('type', $tabpage)->attribute('placeholder', 'id')->class('form-control') }}
    <div class="form-group col-md-12">
        <div class="form-control d-flex align-items-center justify-content-between">
            <label for="enable_midtrans" class="mb-0">{{__('messages.payment_on',['gateway'=>__('messages.midtrans')])}}</label>
            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inlineh">
                <input type="checkbox" class="custom-control-input" name="status" id="enable_midtrans" {{!empty($payment_data->status) ? 'checked' : '' }}>
                <label class="custom-control-label" for="enable_midtrans"></label>
            </div>
        </div>
    </div>
</div>
<div class="row" id='enable_midtrans_payment'>
    <div class="form-group col-md-12">
        <label class="form-control-label">{{__('messages.payment_option',['gateway'=>__('messages.midtrans')])}}</label><br/>
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
        {{ html()->text('title', old('title'))
            ->id('title')
            ->placeholder(trans('messages.title'))
            ->class('form-control')
        }}
        <small class="help-block with-errors text-danger"></small>
    </div>
    <div class="form-group col-md-12">
        {{ html()->label(trans('messages.client_id').' <span class="text-danger">*</span>', 'client_id', ['class' => 'form-control-label']) }}
        {{ html()->text('client_id', old('client_id'))
            ->placeholder(trans('messages.client_id'))
            ->class('form-control')
        }}
        <small class="help-block with-errors text-danger"></small>
    </div>
</div>
{{ html()->submit(__('messages.save'))->class("btn btn-md btn-primary float-md-end") }}
{{ html()->form()->close() }}
<script>
var enable_midtrans = $("input[name='status']").prop('checked');
checkPaymentTabOption(enable_midtrans);

$('#enable_midtrans').change(function(){
    value = $(this).prop('checked') == true ? true : false;
    checkPaymentTabOption(value);
});
function checkPaymentTabOption(value){
    if(value == true){
        $('#enable_midtrans_payment').removeClass('d-none');
        $('#title').prop('required', true);
        $('#client_id').prop('required', true);
    }else{
        $('#enable_midtrans_payment').addClass('d-none');
        $('#title').prop('required', false);
        $('#client_id').prop('required', false);
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
            var client_id = title = '';

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
                    var client_id = obj.client_id;
                }
                $('#title').val(title)
                $('#client_id').val(client_id)
            
            }
        },
        error: function(error) {
         console.log(error);
        }
    });
}
</script>