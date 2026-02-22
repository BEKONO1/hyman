<x-guest-layout>
    <section class="login-content">
       <div class="container h-100">
          <div class="row align-items-center justify-content-center h-100">
             <div class="col-md-5">
                <div class="card p-3">
                   <div class="card-body">
                      <div class="auth-logo text-center mb-3">
                         <a href="{{ route('frontend.index') }}">
                            <img src="{{ getSingleMedia(imageSession('get'), 'logo', null) }}" class="img-fluid rounded-normal" alt="logo">
                         </a>
                      </div>

                      {{-- ✅ Show success message --}}
                      @if (session('success'))
                         <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                         </div>
                      @endif

                      {{-- ✅ Optional: Show session status like email verified, password reset --}}
                      <x-auth-session-status class="mb-4" :status="session('status')" />

                      {{-- ✅ Show form errors --}}
                      <x-auth-validation-errors class="mb-4" :errors="$errors" />

                      <h3 class="mb-3 fw-bold text-center">{{ __('auth.sign_in') }}</h3>
                      <p class="text-center text-secondary mb-4">{{ __('auth.login_continue') }}</p>

                      <form method="POST" action="{{ route('login') }}" data-bs-toggle="validator">
                         {{ csrf_field() }}
                         <div class="row">
                            <div class="col-lg-12">
                               <div class="form-group">
                                  <label class="text-secondary">
                                     {{ __('auth.email') }} <span class="text-danger">*</span>
                                  </label>
                                  <input id="email" name="email" value="{{ request('email') }}"
                                         class="form-control" type="email"
                                         placeholder="{{ __('auth.enter_name', ['name' => __('auth.email')]) }}"
                                         required autofocus>
                                  <small class="help-block with-errors text-danger"></small>
                               </div>
                            </div>

                            <div class="col-lg-12 mt-2">
                               <div class="form-group">
                                  <div class="d-flex justify-content-between align-items-center">
                                     <label class="text-secondary">
                                        {{ __('auth.login_password') }} <span class="text-danger">*</span>
                                     </label>
                                  </div>
                                  <input class="form-control" type="password" value="{{ request('password') }}"
                                         placeholder="{{ __('auth.enter_name', ['name' => __('auth.login_password')]) }}"
                                         name="password" required autocomplete="current-password">
                                  <small class="help-block with-errors text-danger"></small>
                               </div>
                            </div>

                            <div class="col-lg-12 mb-2">
                               <div class="d-flex justify-content-end align-items-center">
                                  <a href="{{ route('auth.recover-password') }}"
                                     class="btn-link p-0 text-capitalize">
                                     <i>{{ __('auth.forgot_password') }}</i>
                                  </a>
                               </div>
                            </div>
                         </div>


                            @if(getSettingValue('demo_login') == 1)
                            <div class="text-center mb-3">
                                <button type="button" class="btn btn btn-outline-primary btn-sm mx-1 demo-login" data-email="demo@admin.com" data-password="12345678">Demo Admin</button>
                                <button type="button" class="btn btn btn-outline-primary btn-sm mx-1 demo-login" data-email="demo@provider.com" data-password="12345678"> Provider</button>
                                <button type="button" class="btn btn btn-outline-primary btn-sm mx-1 demo-login" data-email="demo@handyman.com" data-password="12345678"> Handyman</button>
                            </div>
                            @endif
                         <button type="submit" class="btn btn-primary btn-block mt-2 w-100">
                            {{ __('auth.login') }}
                         </button>

                         <div class="text-center my-4 text-signup">
                            <label class="m-0 text-capitalize">
                               {{ __('auth.dont_have_account') }}
                            </label>
                            <a href="{{ route('auth.register') }}"
                               class="ms-1 btn-link align-baseline text-capitalize">
                               {{ __('auth.signup') }}
                            </a>
                         </div>
                      </form>
                   </div>
                </div>
             </div>
          </div>
       </div>
    </section>

    <script>
    document.querySelectorAll('.demo-login').forEach(button => {
       button.addEventListener('click', function () {
          document.getElementById('email').value = this.getAttribute('data-email');
          document.querySelector('input[name="password"]').value = this.getAttribute('data-password');
       });
    });
 </script>
 </x-guest-layout>
