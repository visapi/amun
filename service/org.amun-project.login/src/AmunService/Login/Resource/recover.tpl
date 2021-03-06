
<div class="row">

	<form method="POST">

	<div class="col-md-8">

		<h3>Recover by Email</h3>

		<p>If you have provided an email address to your account you can recover
		your password by providing your Identity and Email address. We will
		send you an email with a link where you can reset your password.</p>

	</div>

	<div class="col-md-4">

		<h3>Recover</h3>

		<?php if(isset($error)): ?>
		<div class="alert alert-danger">
			<img src="<?php echo $base; ?>/img/icons/login/exclamation.png" />
			<?php echo $error; ?>
		</div>
		<?php endif; ?>

		<p>
			<label for="email">Email</label>
			<input type="email" name="email" id="email" maxlength="255" required="required" class="form-control" />
		</p>

		<p>
			<label for="captcha">Captcha:</label><br />
			<img src="<?php echo $captcha; ?>" alt="Captcha" class="form-captcha" id="amun-service-login-register-form-captcha" /><br />
			<input type="text" name="captcha" id="captcha" value="" maxlength="64" required="required" class="form-control" />
		</p>

		<p>
			<input class="btn btn-primary" type="submit" id="recover" name="recover" value="Recover" />
		</p>

	</div>

	</form>

</div>
