@extends('layouts.auth')

@section('content')
<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">
            Login to your account
        </h2>
        <form action="{{ route('login') }}" method="POST" autocomplete="off">
            @csrf

            <x-input name="email" :value="old('email')" placeholder="your@email.com" required="true"/>

            <x-input type="password" name="password" placeholder="Your password" required="true"/>

            <div class="mb-2">
                <label for="remember" class="form-check">
                    <input type="checkbox" id="remember" name="remember" class="form-check-input"/>
                    <span class="form-check-label">Remember me on this device</span>
                </label>
            </div>

            <div class="form-footer">
                <x-button type="submit" class="w-100">
                    {{ __('Sign in') }}
                </x-button>
            </div>
        </form>
    </div>
</div>

<div class="text-center mt-3" style="color: white;">
    <p>Don't have an account yet?
        <a href="{{ route('register') }}" style="color: white; text-decoration: underline;">
            Sign up
        </a>
    </p>

    <p class="mt-2">
        <a href="{{ route('password.request') }}" style="color: white; text-decoration: underline;">
            I forgot my password
        </a>
    </p>
</div>

@endsection
