@extends('template.auth_template')

@section('content')
	<div class="wrap-login100">
		<div class="login100-pic js-tilt" data-tilt>
			<img src="/template/images/img-01.png" alt="IMG">
		</div>

		<form class="login100-form validate-form" id='form_input'>
			<span class="login100-form-title">
				Member Login
			</span>

			<div class="wrap-input100 validate-input">
				<input class="input100" type="text" required name="username" placeholder="Username">
				<span class="focus-input100"></span>
				<span class="symbol-input100">
					<i class="fa fa-user-circle-o" aria-hidden="true"></i>
				</span>
			</div>

			<div class="wrap-input100 validate-input">
				<input class="input100" type="password" required name="password" placeholder="Password">
				<span class="focus-input100"></span>
				<span class="symbol-input100">
					<i class="fa fa-lock" aria-hidden="true"></i>
				</span>
			</div>

			<div class="container-login100-form-btn">
				<button class="login100-form-btn" type='submit'>
					Login
				</button>
			</div>

			<div class="text-center p-t-136">
				<a class="txt2" href="/forgot_password">
					Forgort Password
					<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
				</a>
				<br>
				<a class="txt2" href="/register">
					Create your Account
					<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
				</a>
			</div>
		</form>
	</div>
@endsection

@section('script')
	<script type = "text/javascript">
		$(document).ready(function(){

			$('.js-tilt').tilt({
				scale: 1.1
			})
			$('#form_input').submit(function(e){
				e.preventDefault();
				$('button[type=submit]').prop('disabled', true);
				console.log('run');

				var username = $('input[name=username]').val();
				var password = $('input[name=password]').val();
				if(username != '' && password != ''){
					axios.post('/login', {
						username: username,
						password: password,
						device: 1,
					})
					.then(function (response) {
						if(response.data.success){
							location.reload();
						}else{
							$('button[type=submit]').prop('disabled', false);
							var val = response.data.message;
							if (typeof val == 'object'){
								for (var key in val) {
									alert(val[key]);
								}
							}else{
								alert(val);
							}
						}
					})
					.catch(function (error) {
						$('button[type=submit]').prop('disabled', false);
						alert(error);
					});
				}else{
					alert('Harap isi semua kolom');
				}
			});
		});
	</script>
@endsection
