<x-guest-layout>
    <section class="login-content">
        <div class="container h-100">
            <div class="row align-items-center justify-content-center h-100">
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="auth-logo">
                                <img src="{{ getSingleMedia(imageSession('get'),'logo',null) }}" class="img-fluid rounded-normal" alt="logo">
                            </div>
                            <h2 class="mb-2 text-center">{{ __('auth.reset_password') }}</h2>
                            <p>{{ __('messages.reset_password_info') }}</p>
                            <!-- Validation Errors -->
                            <x-auth-validation-errors class="mb-4" :errors="$errors" />
                            <form action="{{ route('password.update') }}" method="POST" data-bs-toggle="validator">
                                @csrf
                                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="email" class="form-control-label">
                                                {{ __('messages.email') }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                                class="form-control" placeholder="{{ __('messages.email') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="password" class="form-control-label">
                                                {{ __('messages.new_password') }} <span class="text-danger">*</span>
                                            </label>

                                                <input type="password" name="password" id="password"
                                                    class="form-control"
                                                    placeholder="{{ __('messages.password') }}"
                                                    aria-label="Password" aria-describedby="togglePasswordIcon"
                                                    required>
                                            
                                            <small id="password-error" class="help-block text-danger" style="display: none;"></small>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="password_confirmation" class="form-control-label">
                                                {{ __('messages.new_confirm_password') }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="password" name="password_confirmation" id="password_confirmation"
                                                class="form-control" placeholder="{{ __('messages.password_confirmation') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    {{ __('Reset Password') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const errorMsg = document.getElementById('password-error');

            passwordInput.addEventListener('input', function() {
                const password = passwordInput.value.trim();
                if (password.length < 8 || password.length > 14) {
                    errorMsg.textContent = 'Password Length Should be 8 to 14 Characters long.';
                    errorMsg.style.display = 'block';
                } else {
                    errorMsg.textContent = '';
                    errorMsg.style.display = 'none';
                }
            });
        });
    </script>

</x-guest-layout>