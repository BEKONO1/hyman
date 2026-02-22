<x-master-layout>
    {{ html()->form('DELETE', route('provider.destroy', $providerdata->id))->attribute('data--submit', 'provider'. $providerdata->id)->open() }}
    <main class="main-area">
        <div class="main-content">
            <div class="container-fluid">
                @include('partials._provider')
                <div class="card mb-30">
                    <div class="card-body p-30">
                        <div class="col-lg-12">
                            <div class="card overview-detail mb-0">
                                <div class="card-body">
                                    <div class="row">
                                        
                                        <div class="form-group col-md-4">
                                            {{ html()->label(__('messages.type') . ' <span class="text-danger">*</span>', 'type')->class('form-control-label') }}
                                            <input type="text" class="form-control" placeholder="{{optional(optional($providerdata)->providertype)['type'] }}" readonly>
                                        </div>
                                        <div class="form-group col-md-4">
                                            {{ html()->label(__('messages.commission') . ' <span class="text-danger">*</span>', 'commission')->class('form-control-label') }}
                                            <input type="text" class="form-control" placeholder="{{optional(optional($providerdata)->providertype)['commission']}}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </main>
    {{ html()->form()->close() }}

    <style>
        .form-control::placeholder {
            color: var(--bs-body-color);
        }
    </style>
   
</x-master-layout>