<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Koperasi</title>
    <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
	<link rel="stylesheet" type="text/css" href="/template/vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/template/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="/template/vendor/animate/animate.css">
	<link rel="stylesheet" type="text/css" href="/template/vendor/css-hamburgers/hamburgers.min.css">
	<link rel="stylesheet" type="text/css" href="/template/vendor/select2/select2.min.css">
	<link rel="stylesheet" type="text/css" href="/template/css/util.css">
	<link rel="stylesheet" type="text/css" href="/template/css/main.css">
	@yield('style')
</head>
<body>
    <div class="limiter">
		<div class="container-login100">
			@yield('content')
		</div>
	</div>
</body>
	<script src="/template/vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="/template/vendor/bootstrap/js/popper.js"></script>
    <script src="/template/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="/template/vendor/select2/select2.min.js"></script>
    <script src="/template/vendor/tilt/tilt.jquery.min.js"></script>
	<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
	@yield('script')
</html>
