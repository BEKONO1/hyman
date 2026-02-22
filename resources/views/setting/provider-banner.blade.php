
{{ html()->form('POST', route('promotionConfig'))
    ->attribute('enctype', 'multipart/form-data')
    ->attribute('data-toggle', 'validator')
    ->id('myForm')
    ->open() }}

{{ html()->hidden('id', $promotionconfig->id ?? null)->class('form-control')->placeholder('id') }}
{{ html()->hidden('type')->value($page)->class('form-control')->placeholder('id') }}


<div class="form-group">
    <div class="form-control d-flex align-items-center justify-content-between">
        <label for="enable_provider_banner" class="mb-0">{{ __('messages.provider_promotional_banner') }}</label>
        <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
            <input type="checkbox" class="custom-control-input" name="promotion_enable" id="provider_banner"
                   {{ !empty($promotionconfig->promotion_enable) ? 'checked' : '' }}>
            <label class="custom-control-label" for="provider_banner"></label>
        </div>
    </div>
</div>

<div class="form-padding-box mb-3 d-none" id="promotion_banner">
    <div class="row">
        <div class="form-group col-sm-6 mb-0" >
            <div class="d-flex align-items-center justify-content-between">
                {{ html()->label(trans('messages.promotion_price') . '<span class="text-danger">*</span>' .' ' .'('. str_replace('0.00', '', getPriceFormat(0)).')' , 'provider_banner')->class('form-control-label') }}
                <i class="fas fa-info-circle ml-1" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="{{ trans('messages.set_daily_charges') }}"></i>
            </div>          
            {{ html()->number('promotion_price')
                ->class('form-control')
                ->id('provider_promotion_banner')
                ->value(!empty($promotionconfig->promotion_price) ? $promotionconfig->promotion_price : '')
                ->attribute('min', 1)
                ->attribute('step', '1')
                ->placeholder(__('messages.example'))
            }}
            <span class="help-block with-errors text-danger"></span>
        </div>
    </div>
</div>


{{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end') }}
{{ html()->form()->close() }}




<script>
    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();

        const PromotionalBanner = document.getElementById('provider_promotion_banner');

        let enablePromotionPayment = $("input[name='promotion_enable']").prop('checked');
        PromotionalBannerSetting(enablePromotionPayment);

        $('#provider_banner').change(function() {
            let value = $(this).prop('checked');
            console.log(value);
            PromotionalBannerSetting(value);
        });


        function PromotionalBannerSetting(value) {
    if (value) {
        $('#promotion_banner').removeClass('d-none');
        PromotionalBanner.setAttribute('required', 'required');
    } else {
        $('#promotion_banner').addClass('d-none');
        PromotionalBanner.removeAttribute('required');
    }
}

    });

</script>