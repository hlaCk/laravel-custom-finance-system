<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full font-sans antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1280">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ \Laravel\Nova\Nova::name() }}</title>



    <!-- Fonts -->
    <link rel="stylesheet" href="{{  mix('/css/Nunito.css') }}">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('app.css', 'vendor/nova') }}">

    @if (in_array(app()->getLocale(), config('nova-rtl-theme.locales', [])))
        <link rel="stylesheet" href="{{ asset('/css/custom-rtl.css') }}">
    @else
        <link rel="stylesheet" href="{{  mix('/css/custom-ltr.css') }}">
    @endif

    <!-- Tool Styles -->
    @foreach(\Laravel\Nova\Nova::availableStyles(request()) as $name => $path)
        @if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://']))
            <link rel="stylesheet" href="{!! $path !!}">
        @else
            <link rel="stylesheet" href="/nova-api/styles/{{ $name }}">
        @endif
    @endforeach

    <!-- Custom Meta Data -->
    @include('nova::partials.meta')

    <!-- Theme Styles -->
    @foreach(\Laravel\Nova\Nova::themeStyles() as $publicPath)
        <link rel="stylesheet" href="{{ $publicPath }}">
    @endforeach

    @stack('css')
</head>
<body class="min-w-site bg-40 text-90 font-medium min-h-full">
    <div id="nova">
        <div v-cloak class="flex min-h-screen">
            <!-- Sidebar -->
            <div class="flex-none pt-header min-h-screen w-sidebar bg-grad-sidebar side_navigation_menu_container">
                <a href="{{ \Laravel\Nova\Nova::path() }}">
                    <div class="menu-logo-conntainer absolute pin-t pin-l pin-r bg-logo flex items-center w-sidebar h-header px-6 text-white">
                       @include('nova::partials.logo')
                    </div>
                </a>

                @foreach (\Laravel\Nova\Nova::availableTools(request()) as $tool)
                    {!! $tool->renderNavigation() !!}
                @endforeach
            </div>

            <!-- Content -->
            <div class="content">
                <div class="flex items-center relative shadow h-header bg-white z-20 px-view page-header">

                    <a v-if="@json(\Laravel\Nova\Nova::name() !== null)" href="{{ \Illuminate\Support\Facades\Config::get('nova.url') }}" class="no-underline font-bold text-90 mr-6 app_name">
                        {{ __('labels.system.name') }}
                        <!-- {{ \Laravel\Nova\Nova::name() }} -->
                    </a>


                    @if (count(\Laravel\Nova\Nova::globallySearchableResources(request())) > 0)
                        <global-search dusk="global-search-component"></global-search>
                    @endif

                    <locale-switcher></locale-switcher>

                    <dropdown class="ml-auto h-9 flex items-center dropdown-right">
                        @include('nova::partials.user')
                    </dropdown>

                </div>

                <nova-quick-search dusk="nova-quick-search-component"></nova-quick-search>

                <div data-testid="content" class="px-view py-view mx-auto">
                    @yield('content')

                    @include('nova::partials.footer')
                </div>
            </div>
        </div>
    </div>

    <script>
        window.config = @json(\Laravel\Nova\Nova::jsonVariables(request()));
    </script>

    <!-- Scripts -->
    <script src="{{ mix('manifest.js', 'vendor/nova') }}"></script>
    <script src="{{ mix('vendor.js', 'vendor/nova') }}"></script>
    <script src="{{ mix('app.js', 'vendor/nova') }}"></script>

    <!-- Build Nova Instance -->
    <script>
        window.Nova = new CreateNova(config)
    </script>

    <!-- Tool Scripts -->
    @foreach (\Laravel\Nova\Nova::availableScripts(request()) as $name => $path)
        @if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://']))
            <script src="{!! $path !!}"></script>
        @else
            <script src="/nova-api/scripts/{{ $name }}"></script>
        @endif
    @endforeach

    <!-- Start Nova -->
    <script>
        Nova.liftOff()
    </script>

    @stack('js')

</body>
</html>
