<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NBTP') }} | {{ (isset($title))? $title:'Welcome' }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Gauge Meter -->
    <!-- <link href="{{ asset('css/gaugemeter.css') }}" rel="stylesheet"> -->
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'NBTP') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        @auth
                            <li class="nav-item {{ ($title == 'Dashboard')? 'active':'' }}">
                                <a class="nav-link" href="{{ route('dashboard') }}">
                                    {{ __('Dashboard') }}
                                </a>
                            </li>
                            <li class="nav-item {{ ($title == 'Collections')? 'active':'' }}">
                                <a class="nav-link" href="{{ route('collections') }}">
                                    {{ __('Collections') }}
                                </a>
                            </li>
                            <li class="nav-item {{ ($title == 'Transfers')? 'active':'' }}">
                                <a class="nav-link" href="{{ route('transfers') }}">
                                    {{ __('Transfers') }}
                                </a>
                            </li>
                            <li class="nav-item {{ ($title == 'Distributions')? 'active':'' }}">
                                <a class="nav-link" href="{{ route('distributions') }}">
                                    {{ __('Distributions') }}
                                </a>
                            </li>
                            <li class="nav-item {{ ($title == 'Zones')? 'active':'' }}">
                                <a class="nav-link" href="{{ route('zones') }}">
                                    {{ __('Zones') }}
                                </a>
                            </li>
                            <li class="nav-item {{ ($title == 'Regions')? 'active':'' }}">
                                <a class="nav-link" href="{{ route('regions') }}">
                                    {{ __('Regions') }}
                                </a>
                            </li>
                            <li class="nav-item {{ ($title == 'Districts')? 'active':'' }}">
                                <a class="nav-link" href="{{ route('districts') }}">
                                    {{ __('Districts') }}
                                </a>
                            </li>
                            <li class="nav-item {{ ($title == 'Centers')? 'active':'' }}">
                                <a class="nav-link" href="{{ route('centers') }}">
                                    {{ __('Centers') }}
                                </a>
                            </li>
                            <li class="nav-item {{ ($title == 'Groups')? 'active':'' }}">
                                <a class="nav-link" href="{{ route('groups') }}">
                                    {{ __('Groups') }}
                                </a>
                            </li>
                            <li class="nav-item {{ ($title == 'Users')? 'active':'' }}">
                                <a class="nav-link" href="{{ route('users') }}">
                                    {{ __('Users') }}
                                </a>
                            </li>
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <div class="photo float-left" style="width: 30px; height: 30px; border-radius: 20px; overflow: hidden;">
                                        <img src="{{ Storage::url(Auth::user()->avatar) }}" style="min-height: 100%; width: 100%; height: auto;" />
                                    </div>
                                    <div class="info d-inline-block pl-1 py-1" style="height: 30px;">
                                        <span>{{ Auth::user()->firstname }}</span>
                                        <span class="caret"></span>
                                    </div>
                                </a>

                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="/settings/info">
                                        {{ __('Settings') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <!-- Map all the locations in a javascript array and cascade it for cascaded select form fields -->
    @include('layouts.location_cascade')
</body>
</html>
