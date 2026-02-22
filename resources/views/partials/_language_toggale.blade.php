<div class="form-group col-md-12">
    <div class="d-flex flex-wrap gap-3">
        @foreach($language_array as $index => $lang)
            @php
                $flag_path = file_exists(public_path('/images/flags/' . $lang['id'] . '.png'))
                    ? asset('/images/flags/' . $lang['id'] . '.png')
                    : asset('/images/language.png');
            @endphp
            <button type="button"
                    class="btn language-btn {{ $lang['id'] ===  app()->getLocale() ?? 'en' ? 'btn-primary' : 'btn-outline-secondary' }} d-flex align-items-center" style="gap: 10px; padding: 10px 15px;"
                    onclick="toggleLanguageForm('{{ $lang['id'] }}')">
                <img src="{{ $flag_path }}" alt="flag-{{ $lang['id'] }}"  style="width: 20px; height: auto;">
                {{ strtoupper($lang['title']) }}
            </button>
        @endforeach
    </div>
</div>