@extends('nova::auth.layout', ['isLogin' => true])

@push('js')
    @include('key_shift_space')
@endpush

@section('content')

<div class="container">
    <div class="right">
        <div class="logo">
            <img src="{{ (currentLocale() && currentLocale() != '') ? '/images/logo-'.currentLocale().'.png' : '/images/logo-en.png'}}"/>
{{--            <br/>--}}
{{--            @if ($system_type = __("labels.system.type"))--}}
{{--                <button type="button" name="button" class="button">{{$system_type}}</button>--}}
{{--            @endif--}}
            @if ($right_panel = __('labels.login_page.panels.right'))
                <button type="button" name="right_panel_button" class="pin-x pin-b rounded-br-full fixed bg-primary text-50 text-xs p-1 text-left">{{$right_panel}}</button>
            @endif
        </div>
    </div><!-- end right -->
    <div class="left">
        <!-- Start title-->
        <div class="title">
            <span>{{ __('labels.login_page.title.0') }}</span>
            <h1>{{ __('labels.login_page.title.1') }}</h1>
            <span>{{ __('labels.login_page.title.2') }}</span>
            <br/>
            <form class="log-in-form" method="POST" action="{{ route('nova.login') }}">
                {{ csrf_field() }}
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
                    {{-- <div class="mb-6 {{ $errors->has('email') ? ' has-error' : '' }}"> --}}
                    <input class="input-field" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                    <br><br>
                    <i class="fa fas fa-lock icon"></i>
                    <input class="input-field" id="password" type="password" name="password" required>
                    <br><br>
                    <input class="" type="checkbox" id="box1" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="box1">{{ __('labels.remember_me') }}</label>
                    </div>
                    <br><br>
                    <button type="submit" name="button" class="button">{{ __('labels.login') }}</button>
                    @if (\Laravel\Nova\Nova::resetsPasswords())
                        <div class="ml-auto">
                            <a class="text-primary dim font-bold no-underline" href="{{ route('nova.password.request') }}">
                                {{ __('labels.forgot_your_password') }}
                            </a>
                        </div>
                    @endif

                    @if(Route::has("register"))
                        <span>{{__("labels.dont_have_an_account")}} <a href="{{route('register')}}">{{__('labels.sign_up_now')}}</a></span>
                    @endif
                    <br><br>


                </div><!-- end title -->
            </form>
        </div><!-- end left -->
    </div>
</div>
@endsection
