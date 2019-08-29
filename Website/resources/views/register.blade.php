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
				Member Register
			</span>
			<div class="wrap-input100">
				<input class="input100" type="text" placeholder="Nama Lengkap" name='namalengkap'
					data-validation="required length" data-validation-length="6-190"
				>
			</div>
			<div class="wrap-input100">
				<input class="input100" type="text" placeholder="Username" name='username'
					data-validation="required length" data-validation-length="6-190"
				>
			</div>
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
			<div class="wrap-input100">
				<input class="input100" type="text" placeholder="Gaji" name='gaji'
					data-validation="required number"
				>
			</div>
			<div class="wrap-input100">
				<input class="input100" type="text" placeholder="Email" name='email'
					data-validation="required email"
				>
			</div>
			<div class="wrap-input100">
				<label>Jenis Kelamin</label><br>
				<label class="form-radio-label">
					<input class="form-radio-input" type="radio" name="jenis_kelamin" value="1">
					<span class="form-radio-sign">Laki-laki</span>
				</label>
				<label class="form-radio-label ml-3">
					<input class="form-radio-input" type="radio" name="jenis_kelamin" value="0" >
					<span class="form-radio-sign">Perempuan</span>
				</label><br>
				<span class="help-block form-error" id='error_jenis_kelamin'></span>
			</div>
			<div class="wrap-input100">
				<label for="level">Level</label>
				<select class="input100" name='jabatan' data-validation="required">
					<option>Staff</option>
					<option>SPV</option>
					<option>Manager</option>
				</select>
			</div>
			<div class="wrap-input100">
				<input class="input100" type="text" placeholder="Alamat" name='alamat'
					data-validation="required length" data-validation-length="max190"
				>
			</div>
			<div class="wrap-input100">
				<input class="input100" type="text" placeholder="Nomor Telepon" name='telepon'
					data-validation="required length number" data-validation-length="6-190"
				>
			</div>
			<div class="wrap-input100" style="z-index:999999">
				<input class="input100" type="text" placeholder="Tanggl Join" name='tgl_join' autocomplete='off'
					data-validation="required date" data-validation-format="dd-mm-yyyy"
				>
			</div>
			<div class="wrap-input100">
				<input class="input100" type="file" style="padding-top:10px" name='gambar' accept="image/*" ref='gambar' onChange="showPreview(this)"
					data-validation="mime size" data-validation-allowing="jpg, png, gif" data-validation-max-size="2M"
				>
			</div>
			<div class="container-login100-form-btn">
				<button type='submit' class="login100-form-btn">
					Register
				</button>
			</div>
			<div class="text-center p-t-136">
				<a class="txt2" href="/forgot_password">
					Forgort Password
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
		function showPreview(objFileInput) {
			if (objFileInput.files[0]) {
				var fileReader = new FileReader();
				fileReader.onload = function (e) {
					$('#img_change').attr('src', e.target.result);
				}
				fileReader.readAsDataURL(objFileInput.files[0]);
			}
		}
		$(document).ready(function(){
		    $('input[name=tgl_join]').datepicker({
				dateFormat: "dd-mm-yy"
			});
			$.validate({
				modules : 'security|file'
			});
			$('.js-tilt').tilt({
				scale: 1.1
			})
			$.validate();
			$('#form_input').submit(function(e){
				e.preventDefault();
				console.log('check');
				console.log('jenis_kelamin', $("input[name=jenis_kelamin]:checked").val());
				if($("input[name=jenis_kelamin]:checked").val() == undefined){
					$('#error_jenis_kelamin').text('This is a required field');
				}else{
					$('#error_jenis_kelamin').text('');
				}

				if(
					$('input[name=namalengkap]').val() != '' &&	$('input[name=username]').val() != '' && $('input[name=password]').val() != '' &&
					$('input[name=password_confirmation]').val() != '' && $('input[name=email]').val() != '' &&	$('input[name=alamat]').val() != '' &&
					$('input[name=telepon]').val() != '' &&	$('input[name=tgl_join]').val() != '' && $('input[name=jabatan]').val() != '' &&
					$("input[name=jenis_kelamin]:checked").val() != undefined
				){
					$('button[type=submit]').prop('disabled', true);
					console.log('run');

					var tgl_join;
					if($('input[name=tgl_join]').val()){
						var d = $('input[name=tgl_join]').val().split("-");
	                    d = d[2]+'-'+d[1]+'-'+d[0];
	                    d = new Date(d);
						tgl_join = d.getFullYear()+'-'+("0" + (d.getMonth() + 1)).slice(-2)+'-'+("0" + d.getDate()).slice(-2)
					}

					let formData = new FormData();
					formData.append('username', $('input[name=username]').val());
					formData.append('namalengkap', $('input[name=namalengkap]').val());
					formData.append('password', $('input[name=password]').val());
					formData.append('password_confirmation', $('input[name=password_confirmation]').val());
					formData.append('gaji', $('input[name=gaji]').val());
					formData.append('email', $('input[name=email]').val());
					formData.append('jenis_kelamin', $('input[name=jenis_kelamin]').val());
					formData.append('jabatan', $('select[name=jabatan]').val());
					formData.append('alamat', $('input[name=alamat]').val());
					formData.append('telepon', $('input[name=telepon]').val());
					formData.append('tgl_join', tgl_join);
					if($('input[name=gambar]')[0].files[0]){
						formData.append('gambar', $('input[name=gambar]')[0].files[0]);
					}
					formData.append('device', 1);
					console.log(formData);

					axios.post(
						'/register',
						formData,
						{
							headers: {
								'Content-Type': 'multipart/form-data'
							}
						}
					).then(function(response){
						if(response.data.success){
							location.reload();
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
