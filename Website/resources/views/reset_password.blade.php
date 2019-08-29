@extends('template.auth_template')

@section('style')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
	.form-error{
		color: red
	}
</style>
@endsection

@section('content')
	<div class="wrap-login100">
		<div class="login100-pic js-tilt" data-tilt>
			<img src="/template/images/img-01.png" alt="IMG" id='img_change'>
		</div>

		<form class="login100-form validate-form" id='form_input'>
			<span class="login100-form-title">
				Reset Password
			</span>
			<div class="wrap-input100">
				<input class="input100" type="password" placeholder="Password" name='password_confirmation'
					data-validation="required length" data-validation-length="6-190"
				>
			</div>
			<div class="wrap-input100">
				<input class="input100" type="password" placeholder="Confirm Password" name='password' data-validation="confirmation"
					data-validation="required length" data-validation-length="6-190"
				>
			</div>
			<div class="container-login100-form-btn">
				<button type='submit' class="login100-form-btn">
					Reset Password
				</button>
			</div>
			<div class="text-center p-t-136">
				<a class="txt2" href="/register">
					Don't Have Account? Register
					<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
				</a>
				<br>
				<a class="txt2" href="/login">
					Have Account? Login
					<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
				</a>
			</div>
		</form>
	</div>
@endsection

@section('script')
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
	<script type = "text/javascript">
		$(document).ready(function(){
			var token = '{{ $token }}';
			console.log(token);
			$.validate({
				modules : 'security'
			});
			$('.js-tilt').tilt({
				scale: 1.1
			})
			$.validate();
			$('#form_input').submit(function(e){
				e.preventDefault();
				if($('input[name=password]').val() != '' && $('input[name=password_confirmation]').val() != ''){
					if($('input[name=password]').val() != $('input[name=password_confirmation]').val()){
						alert('Password not match');
						return;
					}

					$('button[type=submit]').prop('disabled', true);
					console.log('run');

					let formData = new FormData();
					formData.append('password', $('input[name=password]').val());
					formData.append('password_confirmation', $('input[name=password_confirmation]').val());
					formData.append('token', token);

					axios.post('/api/reset_password',formData)
					.then(function(response){
						if(response.data.success){
							window.location.replace("/");
						}else{
							$('button[type=submit]').prop('disabled', false);
							var val = response.data.message;
							if (val.isArray){
								for (var key in val) {
									alert(val[key]);
								}
							}else{
								alert(val);
							}
						}
					}).catch(function(error){
						$('button[type=submit]').prop('disabled', false);
						alert(error);
					});
				}else{
					alert("Harap isi semua kolom");
				}
			});
		});
	</script>
@endsection
