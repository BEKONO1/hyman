<!DOCTYPE html>
{{-- <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ session()->has('dir') ? session()->get('dir') : 'ltr' , }}"> --}}
<html lang="en" id="html-root" onload="pageLoad()">
<head>
    @yield('before_head')
    @include('landing-page.partials._head') 
      @include('landing-page.partials._currencyscripts') 

    @yield('after_head')
</head>
<style>
  [data-bs-theme="dark"] {
      .logo-normal {
          .darkmode-logo {
              display: none;
          }

          .light-logo {
              display: block;
          }
      }
  }

  [data-bs-theme="light"] {
      .logo-normal {
          .darkmode-logo {
              display: block;
          }

          .light-logo {
              display: none;
          }
      }
  }


  .text-primary {
      --bs-text-opacity: 1;
      color: rgba(var(--bs-primary-rgb), var(--bs-text-opacity)) !important;
  }

  .dark .text-primary {
      color: white !important;
  }

  .input-group-text {
      display: flex;
      align-items: center;
      padding: 1.1rem 1rem;
      font-size: 1rem;
      font-weight: 400;
      line-height: 1.75;
      color: var(--bs-body-color);
      text-align: center;
      white-space: nowrap;
      background-color: transparent;
      border: var(--bs-border-width) solid transparent;
      border-radius: var(--bs-border-radius-lg);
  }

  p {
      color: rgb(255, 255, 255) !important;
  }

  .dark p {
      color: rgb(0, 0, 0) !important;
  }

  .select2-container .select2-selection--single {
      height: 54px !important;
      padding: 0.8rem 1rem !important;
      border: var(--bs-border-width) solid transparent !important;
      border-radius: var(--bs-border-radius-lg) !important;
      background-color: #171928 !important;
  }

  .dark .select2-container .select2-selection--single {
      height: 54px !important;
      padding: 0.8rem 1rem !important;
      border: var(--bs-border-width) solid transparent !important;
      border-radius: var(--bs-border-radius-lg) !important;
      background-color: #fff !important;
  }

  .dark .select2-selection--multiple {
      background-color: #fff !important;
      border: 1px solid #aaa;
      border-radius: 4px;
      cursor: text;
      padding-bottom: 5px;
      padding-right: 5px;
      position: relative;
  }

  .select2-selection--multiple {
      background-color: #171928 !important;
      border: 1px solid #171928 !important;
      border-radius: 4px;
      cursor: text;
      padding-bottom: 8px;
      padding-right: 5px;
      position: relative;
  }

  .dark .select2-container--default .select2-selection--multiple .select2-selection__choice {
      background-color: #e4e4e4 !important;
      border: 1px solid #aaa;
      border-radius: 4px;
      cursor: default;
      float: left;
      margin-right: 5px;
      margin-top: 5px;
      padding: 0 5px;
  }

  .select2-container--default .select2-selection--multiple .select2-selection__choice {
      background-color: #171928 !important;
      border: 1px solid #aaa;
      border-radius: 4px;
      cursor: default;
      float: left;
      margin-right: 5px;
      margin-top: 5px;
      padding: 0 5px;
  }
</style>
<script>
  (function() {
      const savedTheme = localStorage.getItem('data-bs-theme') || 'light';
      document.documentElement.setAttribute('data-bs-theme', savedTheme);
      if (savedTheme === 'dark') {
          document.body.classList.add('dark');
      }
  })();
</script>
<body class="body-bg">


    <span class="screen-darken"></span>

    <div id="loading">
        @include('landing-page.partials.loading')
    </div>


    <main class="main-content" id="landing-app">
       
        @yield('content')
    </main>

  

  @yield('before_script')
    @include('landing-page.partials._scripts')
    @yield('after_script')

    <script>
      $('.select2').select2();
  </script>

  <script>
      function pageLoad() {
          var html = localStorage.getItem('data-bs-theme');
          if (html == null) {
              html = 'light';
          }
          if (html == 'light') {
              jQuery('body').addClass('dark');
              $('.darkmode-logo').removeClass('d-none')
              $('.light-logo').addClass('d-none')
          } else {
              jQuery('body').removeClass('dark');
              $('.darkmode-logo').addClass('d-none')
              $('.light-logo').removeClass('d-none')
          }
      }
      pageLoad();
  </script>
</body>
</html>
