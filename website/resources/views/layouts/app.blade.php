<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="http://bg.ac.rs/favicon.ico" type="image/x-icon" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{url('/css/app.css')}}" rel="stylesheet">
    <link href="{{url('/css/style.css')}}" rel="stylesheet">
    <script src="https://use.fontawesome.com/b56297b782.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div id="app">
        <div id = "header">
			<div class="container" id="logo">
				<div id="logo"><img src="{{url('/images/logo.jpg')}}"/></div>
			</div>
        </div>
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                        <!-- Authentication Links -->
                    @if (!Auth::guest())
                    <ul class="nav navbar-nav">
                        <li><a href="{{ url('/humidity') }}">Humidity</a></li>
                        <li><a href="{{ url('/top5visibility') }}">Top 5 balkan</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown navbar-right ">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ url('/logout') }}"
                                        onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    @endif
                </div>
            </div>
        </nav>
        <div id="background-container">
            @yield('content')
        </div>

        <footer class="footer">
            <div class="container text-center">
                <span class="text-muted">Made with <span style="color: #ff0000;">❤</span> by Team Schnitzel (Danny, Jouke, Joost, Matthijs, Bart, Eran). This website is for educational purposes</span>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="{{url('/js/app.js')}}"></script>
</body>
</html>
