{{ html()->form('POST', route('saveEarningTypeSetting'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}
{{ html()->hidden('id', $earningsetting->id ?? null )->attribute('placeholder', 'id')->class('form-control') }}
{{ html()->hidden('page', $page)->attribute('placeholder', 'id')->class('form-control') }}

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            {{ html()->label(__('messages.earning_type_provider').' <span class="text-danger">*</span>', 'earning_type')->class('form-control-label') }}
            {{ html()->select('earning_type', ['commission' => __('messages.commission'),'subscription' => __('messages.subscription')])->class('form-select select2js')->required()->value($earningsetting->value ?? null) }}
        </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group">
            <div class="col-md-offset-3 col-sm-12 ">
                {{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end') }}
            </div>
        </div>
    </div>
</div>

<!-- Note Section -->

<div class="row mt-4">
    <div class="col-lg-12">
        <div>
            <strong>Note:</strong>
            <ul class="mb-0 mt-2">
                <li>
                    <strong>Commission:</strong> If you choose "Commission," the system will charge providers a percentage or a flat fee on each booking or transaction. <br>
                    &emsp;<i>Logic:</i> This means that every time a customer books a service, the platform takes a cut (based on the chosen commission percentage or flat fee). 
                    For example, if the commission is set at 20%, and a handyman completes a job for $100, the platform will take $20, and the provider will receive $80.
                </li>
                <br>
                <li>
                    <strong>Subscription:</strong> If you choose "Subscription," providers will pay a fixed fee periodically (e.g., monthly or yearly) to use the platform's services. <br>
                    &emsp;<i>Logic:</i> This model allows service providers to pay a fixed amount every month or year to stay active on the platform. 
                    For example, if a provider pays $50 per month as a subscription, regardless of how many jobs they complete, they will continue to have access to the platform.
                </li>
                <br>
                <li>
                    This setting determines the earning model for service providers on the platform. Choose the option that aligns with your business model. <br>
                    &emsp;<i>Logic:</i> The admin can choose the model that works best for the platform’s goals. 
                    For businesses that prefer steady income, subscription might be the best choice. 
                    For businesses that want to charge per transaction, commission is a good option.
                </li>
            </ul>
        </div>
    </div>
</div>


{{ html()->form()->close() }}
