<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Koperasi</title>
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
	<link rel="manifest" href="/manifest.json">
	<link rel="stylesheet" href="home/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="home/css/ready.css">
	<link rel="stylesheet" href="home/css/demo.css">
	<style>
		.temp-tgl{
			background-color: white;
		}
	</style>
</head>
<body>
	<div class="wrapper" id = "app">
        @component('template.home_template_header')
        @endcomponent

        @component('template.home_template_menu')
        @endcomponent
			<div class="main-panel">
				<div class="content">
					@yield('content')
				</div>
                @component('template.home_template_footer')
                @endcomponent
			</div>
		</div>
	</div>
</div>
</body>
<script src="https://www.gstatic.com/firebasejs/6.0.2/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/6.0.2/firebase-messaging.js"></script>
<script type = "text/javascript" src = "https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<!-- <script type = "text/javascript" src = "https://cdn.jsdelivr.net/npm/vue/dist/vue.min.js"></script> -->
<script type = "text/javascript" src = "https://unpkg.com/vue-router/dist/vue-router.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vee-validate@latest/dist/vee-validate.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://unpkg.com/vuejs-datepicker"></script>

<script src="home/js/core/jquery.3.2.1.min.js"></script>
<script src="home/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
<script src="home/js/core/popper.min.js"></script>
<script src="home/js/core/bootstrap.min.js"></script>
<script src="home/js/plugin/chartist/chartist.min.js"></script>
<script src="home/js/plugin/chartist/plugin/chartist-plugin-tooltip.min.js"></script>
<script src="home/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
<script src="home/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js"></script>
<script src="home/js/plugin/jquery-mapael/jquery.mapael.min.js"></script>
<script src="home/js/plugin/jquery-mapael/maps/world_countries.min.js"></script>
<script src="home/js/plugin/chart-circle/circles.min.js"></script>
<script src="home/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script src="home/js/ready.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@yield('script')
<script>
	var token = null;

	var firebaseConfig = {
		apiKey: "AIzaSyAIqZ1HsGZqllJTa36hHGFXGGOFfLc5Pec",
		authDomain: "prokop-ff2bd.firebaseapp.com",
		databaseURL: "https://prokop-ff2bd.firebaseio.com",
		projectId: "prokop-ff2bd",
		storageBucket: "prokop-ff2bd.appspot.com",
		messagingSenderId: "8827050897",
		appId: "1:8827050897:web:9ec015bf319f65ea"
	};
	firebase.initializeApp(firebaseConfig);
	const messaging = firebase.messaging();
	messaging.usePublicVapidKey('BLH6aOM_bNnn5HxhYA6yG1YYfS1DHdPyRNKQ6k0CiYOEGUjKOrdjzL_JMz5oz_DLlX5NHVrMwUochP8ZH3W9xYU');

	messaging.requestPermission().then(function() {
		messaging.getToken().then(function(currentToken) {
			// console.log(currentToken);
			token = currentToken;

			var vm = this;
            axios.post('/api/updateToken',{
				fcm_token: token,
                api_token: "{{ Session::get('api_token') }}",
            })
            .then(function (response) {
                console.log(response);
            })
            .catch(function (error) {
                console.log(error);
            });

		}).catch(function(err) {
			console.log(err);
		});
	}).catch(function(err) {
		console.log('Unable to get permission to notify.', err);
	});

	messaging.onMessage(function(payload) {
		console.log('Message received. ', payload);
		const Toast = Swal.mixin({
		  toast: true,
		  position: 'top-end',
		  showConfirmButton: false,
		  timer: 3000
		});

		Toast.fire({
		  type: 'info',
		  text: payload.notification.body,
		  title: payload.notification.title
		})
    });

	$('#displayNotif').on('click', function(){
		var placementFrom = $('#notify_placement_from option:selected').val();
		var placementAlign = $('#notify_placement_align option:selected').val();
		var state = $('#notify_state option:selected').val();
		var style = $('#notify_style option:selected').val();
		var content = {};

		content.message = 'Turning standard Bootstrap alerts into "notify" like notifications';
		content.title = 'Bootstrap notify';
		if (style == "withicon") {
			content.icon = 'la la-bell';
		} else {
			content.icon = 'none';
		}
		content.url = 'index.html';
		content.target = '_blank';

		$.notify(content,{
			type: state,
			placement: {
				from: placementFrom,
				align: placementAlign
			},
			time: 1000,
		});
	});
</script>
</html>
