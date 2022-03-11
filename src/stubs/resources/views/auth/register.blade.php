<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo width="82" />
            </a>
        </x-slot>

        <div class="card-body">
            <!-- Status Alerts -->
            <x-status-alerts class="mb-3" :errors="$errors" />

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="row">
                    <div class="col-md-6">
                        <!-- First Name -->
                        <div class="mb-3">
                            <x-label for="first_name" :value="__('First Name')" />

                            <x-input id="first_name" type="text" name="first_name" :value="old('first_name')" required autofocus />
                        </div>
                    </div> <!-- /.col -->
                    <div class="col-md-6">
                        <!-- Last Name -->
                        <div class="mb-3">
                            <x-label for="last_name" :value="__('Last Name')" />

                            <x-input id="last_name" type="text" name="last_name" :value="old('last_name')" required />
                        </div>
                    </div> <!-- /.col -->
                </div> <!-- /.form-row -->

                <!-- Email Address -->
                <div class="mb-3">
                    <x-label for="email" :value="__('Email')" />

                    <x-input id="email" type="email" name="email" :value="old('email')" required />
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <x-label for="password" :value="__('Password')" />

                    <x-input id="password" type="password"
                                    name="password"
                                    required autocomplete="new-password" />
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                    <x-label for="password_confirmation" :value="__('Confirm Password')" />

                    <x-input id="password_confirmation" type="password"
                                    name="password_confirmation" required />
                </div>

                <div class="mb-0">
                    <div class="d-flex justify-content-end align-items-baseline">
                        <a class="text-muted me-3 text-decoration-none" href="{{ route('login') }}">
                            {{ __('Already registered?') }}
                        </a>

                        <x-button>
                            {{ __('Register') }}
                        </x-button>
                    </div>
                </div>
            </form>
        </div>
    </x-auth-card>
</x-guest-layout>
