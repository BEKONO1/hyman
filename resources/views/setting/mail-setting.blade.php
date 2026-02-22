{{ html()->form('POST', route('envSetting'))->attribute('data-toggle', 'validator')->open() }}

{{ html()->hidden('id', null)->class('form-control') }}
{{ html()->hidden('page', $page)->class('form-control') }}
{{ html()->hidden('type', 'mail')->class('form-control') }}

    
    <div class="col-md-12 mt-20">
        <div class="row">
            @foreach(config('constant.MAIL_SETTING') as $key => $value)
                <div class="col-md-6">
                    <div class="form-group">
                            <label class="form-control-label text-capitalize">
                                {{ strtolower(str_replace('_', ' ', $key)) }}
                            </label>
                            @php
                                $encryptedMailPassword = auth()->user()->hasRole('admin') ? base64_encode(openssl_encrypt($value, 'aes-256-cbc', 'your_encryption_key', 0, openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc')))) : '';
                            @endphp
                            @if(auth()->user()->hasRole('admin'))
                                <input type="{{ $key == 'MAIL_PASSWORD' ? 'password' : 'text' }}"
                                    value="{{ $key == 'MAIL_PASSWORD' ? $encryptedMailPassword : $value }}"
                                    name="ENV[{{ $key }}]"
                                    class="form-control"
                                    placeholder="{{ config('constant.MAIL_PLACEHOLDER.'.$key) }}">
                            @else
                                <input type="{{ $key == 'MAIL_PASSWORD' ? 'password' : 'text' }}"
                                    value="{{ $key == 'MAIL_PASSWORD' ? $encryptedMailPassword : '' }}"
                                    name="ENV[{{ $key }}]"
                                    class="form-control"
                                    placeholder="{{ config('constant.MAIL_PLACEHOLDER.'.$key) }}">
                            @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

{{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end') }}
{{ html()->form()->close() }}