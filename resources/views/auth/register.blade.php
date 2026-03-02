@extends('layouts.auth')

@section('content')
<form class="card card-md" action="{{ route('register') }}" method="POST" autocomplete="off">
    @csrf

    <div class="card-body">
        <h2 class="card-title text-center mb-4">Create new account</h2>

        <x-input name="name" :value="old('name')" placeholder="Your name" required="true"/>

        <x-input name="email" :value="old('email')" placeholder="your@email.com" required="true"/>

        <x-input name="username" :value="old('username')" placeholder="Your username" required="true"/>

        <x-input name="password" :value="old('password')" placeholder="Password" required="true"/>

        <x-input name="password_confirmation" :value="old('password_confirmation')" placeholder="Password confirmation" required="true" label="Password Confirmation"/>

        <div class="mb-3">
            <label class="form-check">
                <input type="checkbox" name="terms-of-service" id="terms-of-service"
                       class="form-check-input @error('terms-of-service') is-invalid @enderror"
                >
                <span class="form-check-label">
                    Agree the <a href="./terms-of-service.html" tabindex="-1">
                        terms and policy</a>.
                </span>
            </label>
        </div>

        <div class="form-footer">
            <x-button type="submit" class="w-100">
                {{ __('Create new account') }}
            </x-button>
        </div>
    </div>
</form>

<div class="text-center mt-3" style="color: white;">
    Already have an account?
    <a href="{{ route('login') }}" style="color: white; text-decoration: underline;">
        Sign in
    </a>
</div>
@endsection
