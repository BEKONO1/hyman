
<?php
$language_option = sitesetupSession('get')->language_option ?? ["ar","nl","en","fr","de","hi","it"];
$language_array = languagesArray($language_option);
$files = ["auth", "messages", "pagination", "passwords","validation","landingpage"];
?>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="language_option" class="col-sm-12 form-control-label">{{ __('messages.language_option') }}</label>
            <div class="col-sm-12">
                <select class="form-select select2js opt-LANGUAGE" name="language_option[]" id='change_lang'>
                    @if(count($language_array) > 0)
                        @foreach( $language_array  as $lang )
                            <option value="{{$lang['id']}}" {{ config('app.locale') == $lang['id']  ? 'selected' : '' }} >{{$lang['title']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="selected_file" class="col-sm-12 form-control-label">{{ __('messages.select_file') }}</label>
            <div class="col-sm-12">
                <select class="form-select select2js opt-LANGUAGE" name="selected_file[]" id="selected_file">
                    @foreach( $files  as $value )
                        <option value="{{$value}}" >{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="lang-section"></div>
    </div>
</div>
<script>
    function getLangFile(lang='', file=''){
        var url = "{{ route('getLangFile') }}";
        $.ajax({
            type: 'post',
            url: url,
            data: {
                'lang': lang,
                'file': file
            },
            success: function(res){
                $('.lang-section').html(res);
            }
        });
    }

    function onloadLang(){
        // Retrieve stored language and file from local storage
        let selectedLang = localStorage.getItem('selectedLang') || $("#change_lang :selected").val();
        let selectedFile = localStorage.getItem('selectedFile') || $('#selected_file :selected').val();
        
        // Set the select values to the stored values
        $("#change_lang").val(selectedLang);
        $('#selected_file').val(selectedFile);
        
        // Load the language file
        getLangFile(selectedLang, selectedFile);
    }

    $(document).ready(function (){
        onloadLang();   
        
        $(document).on('change','#change_lang',function(){
            let selectedLang = $("#change_lang :selected").val();
            let selectedFile = $('#selected_file :selected').val();
            
            // Store the selected language in local storage
            localStorage.setItem('selectedLang', selectedLang);
            getLangFile(selectedLang, selectedFile);
        });  

        $(document).on('change','#selected_file',function(){
            let selectedLang = $("#change_lang :selected").val();
            let selectedFile = $('#selected_file :selected').val();
            
            // Store the selected file in local storage
            localStorage.setItem('selectedFile', selectedFile);
            getLangFile(selectedLang, selectedFile);
        });        
    });
</script>

