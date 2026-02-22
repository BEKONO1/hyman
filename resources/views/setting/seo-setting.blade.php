{{ html()->form('POST', route('seosetting'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}

{{ html()->hidden('id',$seosetting->id ?? null)->class('form-control')->placeholder('id') }}
{{ html()->hidden('page')->value($page)->class('form-control')->placeholder('id') }}

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{__('messages.seo_settings')}}</h4>
            </div>
            <div class="card-body">
                <!-- First Row: SEO Image, Meta Title, Meta Keywords -->
                <div class="row">
                    <div class="form-group col-md-4 mb-3">
                        <label class="form-control-label language-label">
                            {{ __('messages.seo_image') }} <span class="text-danger">*</span>
                        </label>
                        <div class="custom-file">
                            <input type="file" name="seo_image" class="custom-file-input" accept="image/*" id="seo_image" onchange="previewSeoImage(event)">
                            <label class="custom-file-label upload-label" for="seo_image">{{ __('messages.choose_file', ['file' => __('messages.seo_image')]) }}</label>
                        </div>
                        <small class="help-block with-errors text-danger"></small>
                        @if ($errors->has('seo_image'))
                            <span class="text-danger">{{ $errors->first('seo_image') }}</span>
                        @endif
                        @php
                            $seoImageUrl = isset($seosetting) && $seosetting->getFirstMediaUrl('seo_image') ? $seosetting->getFirstMediaUrl('seo_image') : null;
                        @endphp
                        <img id="seo_image_preview" src="{{ $seoImageUrl }}" alt="{{ __('messages.seo_image') }}" style="max-width: 100px; margin-top: 10px; @if(empty($seoImageUrl)) display: none; @endif" />
                    </div>
                    <div class="form-group col-md-4 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-control-label language-label">
                                {{ __('messages.meta_title') }} <span class="text-danger">*</span>
                            </label>
                            <span class="text-muted" style="font-size: 12px;">
                                <span id="meta-title-count">{{ strlen($seosetting->meta_title ?? '') }}</span>/100
                            </span>
                        </div>
                        {{ html()->text('meta_title', $seosetting->meta_title ?? '')
                            ->placeholder(__('messages.enter_meta_title'))
                            ->class('form-control')
                            ->attribute('maxlength', 100)
                            ->attribute('id', 'meta_title') }}
                        <small class="help-block with-errors text-danger"></small>
                        @if ($errors->has('meta_title'))
                            <span class="text-danger">{{ $errors->first('meta_title') }}</span>
                        @endif
                    </div>
                    <div class="form-group col-md-4 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-control-label language-label">
                                {{ __('messages.meta_keywords') }} <span class="text-danger">*</span>
                            </label>
                        </div>
                        @php
                            $metaKeywordsRaw = $seosetting->meta_keywords ?? '';
                            if (is_array($metaKeywordsRaw)) {
                                $metaKeywordsValue = implode(',', $metaKeywordsRaw);
                            } elseif (is_string($metaKeywordsRaw) && str_starts_with($metaKeywordsRaw, '[')) {
                                $decoded = json_decode($metaKeywordsRaw, true);
                                $metaKeywordsValue = is_array($decoded) ? implode(',', $decoded) : $metaKeywordsRaw;
                            } else {
                                $metaKeywordsValue = $metaKeywordsRaw;
                            }
                        @endphp
                        <input class="meta-keywords-input" name="meta_keywords" value="{{ $metaKeywordsValue }}" placeholder="{{ __('messages.type_and_press_enter') }}" />
                        <small class="text-muted">{{ __('messages.type_and_press_enter') }}</small>
                        @if ($errors->has('meta_keywords'))
                            <span class="text-danger">{{ $errors->first('meta_keywords') }}</span>
                        @endif
                    </div>
                </div>
                <!-- Second Row: Global Canonical URL, Google Site Verification -->
                <div class="row">
                    <div class="form-group col-md-6 mb-3">
                        <label class="form-control-label">
                            {{ __('messages.global_canonical_url') }} <span class="text-danger">*</span>
                        </label>
                        {{ html()->text('global_canonical_url', $seosetting->global_canonical_url ?? '')
                            ->class('form-control')
                            ->placeholder(__('messages.global_canonical_url'))
                            ->id('global_canonical_url') }}
                        @if ($errors->has('global_canonical_url'))
                            <span class="text-danger">{{ $errors->first('global_canonical_url') }}</span>
                        @endif
                    </div>
                    <div class="form-group col-md-6 mb-3">
                        <label class="form-control-label">
                            {{ __('messages.google_site_verification') }} <span class="text-danger">*</span>
                        </label>
                        {{ html()->text('google_site_verification', $seosetting->google_site_verification ?? '')
                            ->class('form-control')
                            ->placeholder(__('messages.google_site_verification'))
                            ->id('google_site_verification') }}
                        @if ($errors->has('google_site_verification'))
                            <span class="text-danger">{{ $errors->first('google_site_verification') }}</span>
                        @endif
                    </div>
                </div>
                <!-- Third Row: Meta Description (full width) -->
                <div class="row">
                    <div class="form-group col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-control-label language-label">
                                {{ __('messages.meta_description') }}
                            </label>
                        </div>
                        {{ html()->textarea('meta_description', $seosetting->meta_description ?? '')
                            ->placeholder(__('messages.enter_meta_description'))
                            ->class('form-control flex-grow-1')
                            ->style('min-height: 120px; resize: vertical;')
                            ->rows(4)
                            ->attribute('maxlength', 200)
                            ->attribute('id', 'meta_description') }}
                        <small class="text-muted d-block">
                            <span id="meta-desc-count">0</span>/200 words
                        </small>
                        @if ($errors->has('meta_description'))
                            <span class="text-danger">{{ $errors->first('meta_description') }}</span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="col-md-offset-3 col-sm-12 ">
                                  {{ html()->submit(trans('messages.save'))
                                    ->class('btn btn-md btn-primary float-end')
                                    ->attribute('onclick', 'return checkData()')
                                    ->id('saveButton') }}
            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{ html()->form()->close() }}
<script>
// SEO Image Preview Function
function previewSeoImage(event) {
    const preview = document.getElementById('seo_image_preview');
    const file = event.target.files[0];
    const fileLabel = event.target.nextElementSibling; // Get the label element

    if (preview && file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
        fileLabel.textContent = file.name; // Update label with file name
    } else if (preview && !file) {
        preview.style.display = 'none';
        fileLabel.textContent = '{{ __('messages.choose_file', ['file' => __('messages.seo_image')]) }}';
    }
}

$(document).ready(function() {
    // Meta Description word count
// Meta Description character count
var $metaDesc = $('#meta_description');
var $metaDescCount = $('#meta-desc-count');
if ($metaDesc.length && $metaDescCount.length) {
    function updateMetaDescCount() {
        $metaDescCount.text($metaDesc.val().length);
    }
    $metaDesc.on('input', updateMetaDescCount);
    updateMetaDescCount();
}

    // Meta Title character count
    var $metaTitle = $('#meta_title');
    var $metaTitleCount = $('#meta-title-count');
    if ($metaTitle.length && $metaTitleCount.length) {
        function updateMetaTitleCount() {
            $metaTitleCount.text($metaTitle.val().length);
        }
        $metaTitle.on('input', updateMetaTitleCount);
        updateMetaTitleCount();
    }
});
</script>



