{{ html()->form('POST', route('socialMedia'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}

{{ html()->hidden('id', $socialmedia->id ?? null )->attribute('placeholder', 'id')->class('form-control') }}
{{ html()->hidden('page', $page)->attribute('placeholder', 'id')->class('form-control') }}
<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            {{ html()->label(__('messages.facebook_url'))->class('col-sm-6 form-control-label')->for('facebook_url') }}
            <div class="col-sm-12">
                {{ html()->text('facebook_url', $socialmedia->facebook_url)->class('form-control')->placeholder(__('messages.facebook_url_placeholder'))}}
            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.twitter_url') }}</label>
            <div class="col-sm-12">
                {{ html()->text('twitter_url', $socialmedia->twitter_url)->class('form-control')->placeholder(__('messages.twitter_url_placeholder'))}}
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.linkedin_url') }}</label>
            <div class="col-sm-12">
                {{ html()->text('linkedin_url', $socialmedia->linkedin_url)->class('form-control')->placeholder(__('messages.linkedin_url_placeholder'))}}
            </div>
        </div>
    </div>
    <div class="col-lg-6">

        <div class="form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.instagram_url') }}</label>
            <div class="col-sm-12">
                {{ html()->text('instagram_url', $socialmedia->instagram_url)->class('form-control')->placeholder(__('messages.instagram_url_placeholder'))}}
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.youtube_url') }}</label>
            <div class="col-sm-12">
                {{ html()->text('youtube_url', $socialmedia->youtube_url)->class('form-control')->placeholder(__('messages.youtube_url_placeholder'))}}
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
<script>
    
</script>
