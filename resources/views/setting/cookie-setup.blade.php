{{ html()->form('POST', route('cookiesetup'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}

{{ html()->hidden('id', $cookiesetup->id ?? null )->class('form-control')->placeholder('id') }}
{{ html()->hidden('page', $page)->class('form-control')->placeholder('id') }}

<div class="row">
    <div class="col-lg-12"> 

        <div class="form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.title') }}</label>
            <div class="col-sm-12">
                {{ html()->text('title', $cookiesetup->title)->class('form-control')->placeholder(__('messages.title')) }}
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.description') }}</label>
            <div class="col-sm-12">
                {{ html()->textarea('description', $cookiesetup->description)->class('form-control textarea')->rows(3)->placeholder(__('messages.description')) }}
            </div>
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
{{ html()->form()->close() }}

