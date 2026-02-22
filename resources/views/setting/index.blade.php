<x-master-layout>
    @php
    $user = auth()->user();
    $isAdminOrProvider = $user && ($user->hasRole('admin') || $user->hasRole('provider'));
    @endphp
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? __('messages.list') }}</h5>
                            <h5 class="fw-bold">v{{ config('app.version') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body setting-pills">
                        <div class="row">
                            <div class="col-sm-12 col-lg-12">
                                <ul class="nav flex-column nav-pills nav-fill tabslink" id="tabs-text" role="tablist">
                                    @if(in_array( $page,['profile_form','password_form','time_slot']))
                                        <li class="nav-item">
                                            <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=profile_form" data-target=".paste_here" class="nav-link {{$page=='profile_form'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.profile')}} </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=password_form" data-target=".paste_here" class="nav-link {{$page=='password_form'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.change_password') }} </a>
                                        </li>

                                        @role('provider')
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=time_slot" data-target=".paste_here" class="nav-link {{$page=='time_slot'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.slot') }} </a>
                                            </li>
                                        @endrole
                                    @else
                                        @hasanyrole('admin|demo_admin')
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=general-setting" data-target=".paste_here" class="nav-link {{$page=='general-setting'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.general_settings') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=theme-setup" data-target=".paste_here" class="nav-link {{$page=='theme-setup'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.theme_setup') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=site-setup" data-target=".paste_here" class="nav-link {{$page=='site-setup'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.site_setup') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=service-configurations" data-target=".paste_here" class="nav-link {{$page=='service-configurations'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.service_configurations') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=other-setting" data-target=".paste_here" class="nav-link {{$page=='other-setting'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.app_configurations') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=notification-setting" data-target=".paste_here" class="nav-link {{$page=='notification-setting'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.notification_configurations') }}</a>
                                            </li>

                                            <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=social-media" data-target=".paste_here" class="nav-link {{$page=='social-media'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.social_media') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=cookie-setup" data-target=".paste_here" class="nav-link {{$page=='cookie-setup'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.cookie_setup') }}</a>
                                            </li>
                                            <!-- <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=sliders" data-target=".paste_here" class="nav-link {{$page=='sliders'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.sliders') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=taxes" data-target=".paste_here" class="nav-link {{$page=='taxes'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.taxes') }}</a>
                                            </li> -->
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=role-permission-setup" data-target=".paste_here" class="nav-link {{$page=='role-permission-setup'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.role_permission_setup') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=language-setting" data-target=".paste_here" class="nav-link {{$page=='language-setting'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.language_settings') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=payment-setting" data-target=".paste_here" class="nav-link {{$page=='payment-setting'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.payment_configuration') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=mail-setting" data-target=".paste_here" class="nav-link {{$page=='mail-setting'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.mail_settings') }}</a>
                                            </li>

                                            <!-- <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=push-notification-setting" data-target=".paste_here" class="nav-link {{$page=='push-notification-setting'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.pushnotification_settings') }}</a>
                                            </li> -->
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=earning-setting" data-target=".paste_here" class="nav-link {{$page=='earning-setting'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.earning_setting') }}</a>
                                            </li>

                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=provider-banner" data-target=".paste_here" class="nav-link {{$page=='provider-banner'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.provider_promotional_banner') }}</a>
                                            </li>
                                              <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=seo-setting" data-target=".paste_here" class="nav-link {{$page=='seo-setting'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.seo_settings') }}</a>
                                            </li>
                                            {{-- @if(!$isAdminOrProvider)
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_page') }}?page=provider-zip" data-target=".paste_here" class="nav-link {{$page=='provider-zip'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.zip_code') }}</a>
                                            </li>
                                            @endif                                                          --}}
                                        @endhasanyrole
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-lg-12">
                                <div class="tab-content" id="pills-tabContent-1">
                                    <div class="tab-pane active p-1" >
                                        @if(session('message'))
                                        <div class="alert alert-success">
                                                {{ session('message') }}
                                        </div>
                                        @endif
                                        <div class="paste_here"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@section('bottom_script')
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">

<script>
    $(document).ready(function() {
        const loadPageContent = () => {

            var $this = $('.nav-item').find('a.active');
            var loadurl = '{{ route('layout_page') }}?page={{ $page }}';
            var targ = $this.attr('data-target');
            console.log('Loading content from URL:', targ);
            

            $.post(loadurl, {
                '_token': $('meta[name="csrf-token"]').attr('content')
            }, function(data) {
                console.log('Content loaded successfully:', 'hello');
                $(targ).html(data);
                
                initializeTagify();
            });

            $this.tab('show');
            return false;
        };

         const initializeTagify = () => {
            document.querySelectorAll('.meta-keywords-input').forEach(function(input) {
                new Tagify(input, {
                    delimiters: ",",
                    dropdown: { enabled: 0 }
                });

                // Prevent Enter from submitting the form
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') e.preventDefault();
                });
            });
        };
        

        loadPageContent();
    });
</script>
@endsection

</x-master-layout>
