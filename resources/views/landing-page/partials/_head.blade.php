@php
    $metaTags = getMetaTagsForPage();
    // dd($metaTags);
@endphp
<title>{{ $metaTags['meta_title'] }}</title>
<meta name="title" content="{{ $metaTags['meta_title'] }}">
<meta name="description" content="{{ $metaTags['meta_description'] }}">
<meta name="keywords" content="{{ $metaTags['meta_keywords'] }}">
<meta name="image" content="{{ $metaTags['og_image'] }}">
@if(!empty($metaTags['og_image']))
    <meta property="og:image" content="{{ $metaTags['og_image'] }}">
@endif
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
@php
    $seo = App\Models\Setting::where('type','general-setting')->where('key', 'general-setting')->first();
    $seo_value = $seo ? json_decode($seo->value, true) : null;

    $seo_name = $seo_value['site_name'] ?? '';
    @endphp

<title>{{$seo_name ?? 'Handyman Service'}}</title>
@if(!empty($metaTags['google_site_verification']))
    <meta name="google-site-verification" content="{{ $metaTags['google_site_verification'] }}" />
@endif
@if(!empty($metaTags['global_canonical_url']))
    <link rel="canonical" href="{{ $metaTags['global_canonical_url'] }}" />
@endif
<link rel="shortcut icon" class="favicon_preview" href="{{ getSingleMedia(imageSession('get'),'favicon',null) }}" />
<link rel="stylesheet" href="{{ asset('css/landing-page.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/landing-page-rtl.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/landing-page.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/landing-page-custom.min.css') }}">

<link rel="stylesheet" href="{{ asset('vendor/@fortawesome/fontawesome-free/css/all.min.css')}}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<meta name="assert_url" content="{{ URL::to('') }}" />

<meta name="baseUrl" content="{{env('APP_URL')}}" />
<link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>

<script>
    // Set primary color immediately on page load
    const savedPrimaryColor = localStorage.getItem('primaryColor');
    if (savedPrimaryColor) {
        const root = document.documentElement;

        // Convert HEX to RGB for primary-rgb
        const hex = savedPrimaryColor.replace('#', '');
        const r = parseInt(hex.substring(0, 2), 16);
        const g = parseInt(hex.substring(2, 4), 16);
        const b = parseInt(hex.substring(4, 6), 16);

        // Set CSS variables for primary color
        root.style.setProperty('--bs-primary', savedPrimaryColor);
        root.style.setProperty('--bs-primary-rgb', `${r}, ${g}, ${b}`);
        root.style.setProperty('--bs-primary-bg-subtle', `rgba(${r}, ${g}, ${b}, 0.09)`);
        root.style.setProperty('--bs-primary-border-subtle', `rgba(${r}, ${g}, ${b}, 0.09)`);
        root.style.setProperty('--bs-primary-hover-bg', `rgba(${r}, ${g}, ${b}, 0.75)`);
        root.style.setProperty('--bs-primary-hover-border', `rgba(${r}, ${g}, ${b}, 0.75)`);
        root.style.setProperty('--bs-primary-active-bg', `rgba(${r}, ${g}, ${b}, 0.75)`);
        root.style.setProperty('--bs-primary-active-border', `rgba(${r}, ${g}, ${b}, 0.75)`);
    }
</script>



@php
        $currentLang = app()->getLocale();
        $langFolderPath = resource_path("lang/$currentLang");
        $filePaths = \File::files($langFolderPath);
        $sitesetup = App\Models\Setting::where('type','site-setup')->where('key', 'site-setup')->first();
        $date_time = $sitesetup ? json_decode($sitesetup->value, true) : null;

        $dateformate = $date_time ? $date_time['date_format'] : 'Y-m-d';
        $serviceconfig = App\Models\Setting::getValueByKey('service-configurations', 'service-configurations');
    @endphp

    @foreach ($filePaths as $filePath)
        @php
            $fileName = pathinfo($filePath, PATHINFO_FILENAME);
        @endphp
        <script>
            window.localMessagesUpdate = {
                ...window.localMessagesUpdate,
                "{{ $fileName }}": @json(require($filePath))
            };

            window.dateformate = @json($dateformate);
            window.cancellationCharge = @json($serviceconfig);
        </script>
    @endforeach
    <script>
        window.cancellationCharge = @json($serviceconfig);
        window.currentLang = @json($currentLang);
    </script>
<script>
    const savedPrimaryColordata = localStorage.getItem('primaryColor');
    // Assign the plain string value directly
    window.currentcolor = savedPrimaryColordata || null;
</script>
<script>
    const currencyFormat = (amount) => {
      const DEFAULT_CURRENCY = JSON.parse(@json(json_encode(Currency::getDefaultCurrency(true))))
       const noOfDecimal = 2
       const currencyPosition = DEFAULT_CURRENCY.defaultPosition
       const currencySymbol = DEFAULT_CURRENCY.defaultCurrency.symbol
      return formatCurrency(amount, noOfDecimal, currencyPosition, currencySymbol)
    }
    window.currencyFormat = currencyFormat
    window.defaultCurrencySymbol = @json(Currency::defaultSymbol())

  </script>

@if(empty($metaTags['meta_description']))
    <div class="global-notice" style="background:#ffeeba;color:#856404;padding:10px;text-align:center;">
        {{ __('Welcome to our service! For more information, visit our About Us page.') }}
    </div>
@endif



