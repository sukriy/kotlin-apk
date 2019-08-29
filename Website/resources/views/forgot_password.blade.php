@extends('template.auth_template')

@section('content')
	<div class="wrap-login100">
		<div class="login100-pic js-tilt" data-tilt>
			<img src="/template/images/img-01.png" alt="IMG">
		</div>

		<form class="login100-form validate-form" id='form_input'>
			<span class="login100-form-title">
				Forgot Password
			</span>

			<div class="wrap-input100 validate-input">
				<input class="input100" type="text" required name="email" placeholder="Email">
				<span class="focus-input100"></span>
				<span class="symbol-input100">
					<i class="fa fa-user-circle-o" aria-hidden="true"></i>
				</span>
			</div>

			<div class="container-login100-form-btn">
				<button class="login100-form-btn" type='submit'>
					Login
				</button>
			</div>

			<div class="text-center p-t-136">
				<a class="txt2" href="/login">
					Have Account? Login
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
		$('.js-tilt').tilt({
			scale: 1.1
		})
		$('#form_input').submit(function(e){
			e.preventDefault();
			$('button[type=submit]').prop('disabled', true);
			console.log('run');

			var email = $('input[name=email]').val();

			if(email != ''){
				axios.post('/forgot_password_process', {
					email: email,
				})
				.then(function (response) {
					$('button[type=submit]').prop('disabled', false);
					console.log('response');
					console.log(response.data);
					if(response.data.success){
						alert('check your mailbox')
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
					console.log('error');
					console.log(error);
					$('button[type=submit]').prop('disabled', false);
					alert(error);
				});
			}else{
				alert('Harap isi semua kolom');
			}
		});
	</script>
@endsection
