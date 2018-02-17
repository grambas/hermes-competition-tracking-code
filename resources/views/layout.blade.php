<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hermes Competition project by Mindaugas Milius</title>

     <!--  CSS -->
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">

    </head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">Home</a>
        <a class="navbar-brand" href="{{ route('track') }}">Track Package</a>
        <a class="navbar-brand" href="{{ route('simulation') }}">Simulation</a>
        <div class=" float-right"><span class="red"> Debug: </span><input id="debug" class="btn-danger" checked data-toggle="toggle" type="checkbox"></div>
      </div>
</nav>

<div class="container">
    @yield('content')
</div>

<div class="container">
    <pre id="logger">
        <h1>console.log</h1>
    </pre>
</div>



    <!--  JS -->


    <!--  Jquery -->
    <script type="text/javascript" charset="UTF-8" src="{{ URL::asset('js/jquery.min.js') }}"></script>

    <!--  Google Maps Api -->
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB22CeN3MfdvEvsYelWXZXq4iTyLZSjerM"></script>
    <!--  bootstrap html framework  -->
    <script type="text/javascript" charset="UTF-8" src="{{ URL::asset('js/tether.min.js') }}"></script>
    <script type="text/javascript" charset="UTF-8" src="{{ URL::asset('js/bootstrap.min.js') }}"></script>

    <!--  Main JS -->
    <script src="{{ URL::asset('js/main.js') }}"></script>

</body>
</html>