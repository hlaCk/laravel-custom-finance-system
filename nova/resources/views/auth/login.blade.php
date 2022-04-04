@extends('nova::auth.layout', ['isLogin' => true])

@section('content')

    <form class="log-in-form" method="POST" action="{{ route('nova.login') }}">
        {{ csrf_field() }}

        <div class="right">
            <div class="logo"><img src="/images/logo.png" /><br /><button type="button" name="button" class="button">Ticketing System</button></div>
        </div>

        <div class="left">
            <div class="title">
                <span>{{ __('labels.system.welcome_to') }}</span>
                <h1>{{ __('labels.system.name') }}</h1>
                <span>{{ __('labels.system.type') }} </span>
                <br />
                <div class="input-icons">
                    @if ($errors->any())
                    <p class="text-center font-semibold text-danger my-3">
                        @if ($errors->has('email'))
                            {{ $errors->first('email') }}
                        @else
                            {{ $errors->first('password') }}
                        @endif
                    </p>
                    @endif
                    <i class="fa fa-user icon"></i>
                    {{-- <div class="{{ $errors->has('email') ? ' has-error' : '' }}"> --}}
                    <input class="input-field" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                    {{-- </div> --}}
                    <br><br>
                    <i class="fa fas fa-lock icon"></i>
                    {{-- <div class="{{ $errors->has('password') ? ' has-error' : '' }}"> --}}
                    <input class="input-field" id="password" type="password" name="password" required>
                    {{-- </div> --}}
                    <br><br>
                    <input class="" type="checkbox" id="box1" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="box1">{{ __('labels.remember_me') }}</label>
                </div>
                <br><br>
                <button type="submit" name="button" class="button">{{ __('labels.login') }}</button>
                <br><br>
                @if (\Laravel\Nova\Nova::resetsPasswords())
                    <div class="ml-auto">
                        <a class="text-primary dim font-bold no-underline" href="{{ route('nova.password.request') }}">
                            {{ __('labels.forgot_your_password') }}
                        </a>
                    </div>
                @endif
                <span>{{ __("labels.dont_have_an_account") }}<a href="{{ __('labels.system.copyright_url') }}">{{ __('labels.sign_up_now') }}</a></span>
            </div>
        </div>
    </form>

@endsection
