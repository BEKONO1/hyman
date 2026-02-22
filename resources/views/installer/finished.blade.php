@extends('installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.final.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-flag-checkered fa-fw" aria-hidden="true"></i>
    {{ trans('installer_messages.final.title') }}
@endsection

@section('container')
    <div class="buttons">
        <a href="{{ route('frontend.index') }}" class="button" id="exit-button" onclick="disableButton()">
            {{ trans('installer_messages.final.exit') }}
        </a>
    </div>

    <div class="buttons">
        <a href="{{ route('login') }}" class="button" id="exit-button" onclick="disableButton()">
            {{ trans('installer_messages.final.admin_panel') }}
        </a>
    </div>

    <script>
        function disableButton() {
            // Disable the button once clicked
            document.getElementById('exit-button').classList.add('disabled');
            document.getElementById('exit-button').innerText = 'Loading...';
        }
    </script>

    <style>
        .disabled {
            pointer-events: none;
            opacity: 0.6;
        }
    </style>
@endsection
