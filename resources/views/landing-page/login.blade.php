@extends('landing-page.layouts.headerremove')


@section('content')
<div>
    <div class="container-fluid px-lg-0 py-lg-0 pb-5">
        <div class="row min-vh-100 g-lg-0">
            <div class="col-xl-8 col-lg-7 mh-100">
                <div class="py-5 d-flex flex-column justify-content-center h-100">
                    <div class="row justify-content-center">
                        <div class="col-xl-8 col-lg-10">
                            <div class="text-center">

                            @if ($sectionData && isset($sectionData['login_register']) && $sectionData['login_register'] == 1)
                                <div class="mb-5">
                                    <h3 class="text-capitalize mb-3">
                                    {{ $sectionData['title'] }}
                                    </h3>
                                    <p class="m-0">
                                    {{$sectionData['description']}}

                                    </p>
                                </div>
                                @php
                                    $loginregisterimage = Spatie\MediaLibrary\MediaCollections\Models\Media::where('collection_name', 'login_register_image')->first();
                                @endphp
                                @if($loginregisterimage)
                                    <img src="{{ url('storage/' . $loginregisterimage->id . '/' . $loginregisterimage->file_name) }}" alt="video-popup" class="img-fluid w-100 rounded">
                                @else
                                    <img src="{{asset('landing-images/general/login.webp ')}}" class="img-fluid w-75" alt="log-in"/>
                                @endif
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4 col-lg-5 mh-100">
                <div class="py-5 px-3 bg-light d-flex flex-column justify-content-center h-100">
                    <div class="row justify-content-center">
                        <div class="col-xl-8 col-lg-10">
                            <div class="authontication-forms">
                                <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3 mb-5">
                                    <h4 class="text-capitalize">{{__('auth.sign_in')}}</h4>
                                    <a href="{{ route('login') }}" class=" float-end me-1 btn btn-sm btn-primary">{{__('landingpage.admin_login')}}</a>
                                </div>
                                <div class="iq-login-form ">
                                    <div class="alert alert-danger d-none" role="alert"  id="error">

                                    </div>
                                     {{-- ✅ Show success message --}}
                     @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                           {{ session('success') }}
                           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                     @endif
                                    <form id="loginForm" data-toggle="validator" method="post">
                                        {{csrf_field()}}
                                        <div class="form-group icon-right mb-5 custom-form-field">
                                            <label>{{__('landingpage.email')}} <span class="text-danger">*</span></label>
                                            <input type="email" id="email" name="email" class="form-control" placeholder="{{__('placeholder.email')}}"
                                                aria-label="Username" aria-describedby="basic-addon1" required autofocus>
                                            <small class="help-block with-errors text-danger"></small>
                                        </div>


                                        <div class="form-group icon-right mb-5 custom-form-field">
                                        <label>{{__('landingpage.your')}} {{__('auth.login_password')}} <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" id="password" name="password" class="form-control" placeholder="{{__('placeholder.login_password')}}"
                                                        required> 
                                                <span class="input-group-text" id="togglePassword">
                                                    <i class="fa fa-eye-slash" aria-hidden="true" onclick="togglePassword()"></i>
                                                </span>
                                            </div>
                                            <small class="help-block with-errors text-danger"></small>
                                        </div>


                                        <input type="hidden" name="login" value="user_login">

                                         <div class="d-flex flex-sm-row justify-content-between align-items-center mb-4">
                                            <p class="login-remember m-0"><label class="m-0"><input name="rememberme"
                                                        type="checkbox" id="rememberme" value="forever"> {{__('landingpage.remember_me')}}</label>
                                            </p>
                                            <a href="{{route('user.forgot_password')}}" class="btn-link p-0 text-capitalize"><i>{{__('auth.forgot_password')}}</i></a>
                                         </div>

                                        <div class="login-submit">
                                            <button type="submit"  class="btn btn-primary w-100 text-capitalize">{{__('auth.login')}}</button>
                                        </div>
                                    </form>


                                  <div class="text-center my-4 text-signup">
                                        <label class="m-0 text-capitalize"> {{__('auth.dont_have_account')}}</label>
                                        <a href="{{route('user.register')}}" class="ms-1 btn-link align-baseline text-capitalize">{{__('auth.signup')}}</a>
                                    </div>

                                    <div class="text-center">
                                        <a href="{{route('auth.register')}}" class="btn-link p-0 text-capitalize">{{__('landingpage.handyman_provider_register')}}</a>
                                    </div>
                                    <div class="mt-5">
                                        {{-- <h6 class="mb-3 text-capitalize text-center">{{__('landingpage.demo_accounts')}}</h6> --}}
                                        {{--  <div class="px-5 py-3 bg-primary">
                                            <ul class="iq-social-list-text d-flex align-items-center justify-content-center flex-wrap m-0 list-inline">
                                            <li class="me-3 pe-3">
                                                    <a href="{{ route('auth.login') }}" class="text-capitalize text-white">{{__('landingpage.admin_login')}}</a>
                                                </li>
                                                <li class="me-3 pe-3">
                                                    <a href="{{ route('auth.login', ['email' => 'demo@provider.com', 'password' => '12345678']) }}" class="text-capitalize text-white">{{__('messages.provider')}}</a>
                                                </li>
                                                <li class="me-3 pe-3">
                                                    <a href="{{ route('auth.login', ['email' => 'demo@handyman.com', 'password' => '12345678']) }}" class="text-capitalize text-white">{{__('messages.handyman')}}</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('auth.login', ['email' => 'demo@user.com', 'password' => '12345678']) }}" class="text-capitalize text-white">{{__('landingpage.user')}}</a>
                                                </li> 
                                            </ul>
                                        </div>--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
        // CSRF Token and Base URL
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const baseUrl = $('meta[name="baseUrl"]').attr('content');
        const successMessage = "Login successful!";

        // Login form submission
        $('#loginForm').submit(function (e) {
            e.preventDefault(); // Prevent default form submission
            const formData = $(this).serialize();

            $.ajax({
                method: 'POST',
                url: `${baseUrl}/api/login`,
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                dataType: 'json',
                success: function (response) {
                    if (response.data) {
                        const urlParams = new URLSearchParams(window.location.search);
                        const serviceId = urlParams.get('service_id');
                        const Favservice = urlParams.get('favorite_service');

                        if (serviceId) {
                            window.location.href = `${baseUrl}/book-service?id=${serviceId}`;
                        } else if (Favservice) {
                            window.location.href = `${baseUrl}/service-detail/${Favservice}`;
                        } else {
                            // Store success message in localStorage and redirect to homepage
                            localStorage.setItem('login_success_message', successMessage);
                            window.location.href = baseUrl + '/';
                        }
                    }
                },
                error: function (xhr) {
                    // Display error message
                    const errorMessage = xhr.responseJSON?.message || 'An error occurred. Please try again.';
                    $('#error').removeClass('d-none').text(errorMessage);
                }
            });
        });

        // Show success message if available
        const storedMessage = localStorage.getItem('login_success_message');
        if (storedMessage) {
            showMessage(storedMessage);
            localStorage.removeItem('login_success_message'); // Remove after showing
        }

        // Function to display alert messages
        function showMessage(message) {
            const alertDiv = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
            $('body').prepend(alertDiv);
        }

        // Autofocus on email field
        setTimeout(() => {
            $('#email').focus();
        }, 100);

        // Toggle password visibility
        $('#togglePassword').click(function () {
            const passwordInput = $('#password');
            const icon = $(this).find('i');
            const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
            passwordInput.attr('type', type);
            icon.toggleClass('fa-eye-slash fa-eye');
        });
    });
</script>
