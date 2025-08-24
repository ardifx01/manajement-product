<?= $this->extend($config->viewLayout) ?>
<?= $this->section('main') ?>

<div class="auth-wrapper">
	<div class="container-fluid h-100">
		<div class="row flex-row h-100 bg-white">
			<div class="col-xl-8 col-lg-6 col-md-5 p-0 d-md-block d-lg-block d-sm-none d-none">
				<div class="lavalite-bg" style="background-image: url('<?= base_url('theme/img/auth/login-bg.jpg') ?>')">
					<div class="lavalite-overlay"></div>
				</div>
			</div>
			<div class="col-xl-4 col-lg-6 col-md-7 my-auto p-0">
				<div class="authentication-form mx-auto">
					<div class="logo-centered">
						<a href="<?= base_url('/') ?>"><img src="<?= base_url('theme/src/img/logo.svg') ?>" alt=""></a>
					</div>
					<h3>Sign In</h3>
					<?= view('App\Views\Auth\_message_block') ?>

					<form action="<?= url_to('login') ?>" method="post">
						<?= csrf_field() ?>

						<?php if ($config->validFields === ['email']): ?>
							<div class="form-group">
								<input type="email" class="form-control <?php if (session('errors.login')) : ?>is-invalid<?php endif ?>"
									name="login" placeholder="<?= lang('Auth.email') ?>" ><i class="ik ik-user"></i>
								<div class="invalid-feedback">
									<?= session('errors.login') ?>
								</div>
							</div>
						<?php else: ?>
							<div class="form-group">
								<input type="text" class="form-control <?php if (session('errors.login')) : ?>is-invalid<?php endif ?>"
									name="login" placeholder="<?= lang('Auth.emailOrUsername') ?>"><i class="ik ik-user"></i>
								<div class="invalid-feedback">
									<?= session('errors.login') ?>
								</div>
							</div>
						<?php endif; ?>
						<div class="form-group">
							<input type="password" name="password" class="form-control <?php if (session('errors.password')) : ?>is-invalid<?php endif ?> " placeholder="<?= lang('Auth.password') ?>" required="" >
							<i class="ik ik-lock"></i>
							<div class="invalid-feedback">
								<?= session('errors.password') ?>
							</div>
						</div>

						<div class="row">
							<div class="col text-left">
								<?php if ($config->allowRemembering): ?>
									<div class="form-check">
										<label class="form-check-label">
											<input type="checkbox" name="remember" class="form-check-input" <?php if (old('remember')) : ?> checked <?php endif ?>>
											<?= lang('Auth.rememberMe') ?>
										</label>
									</div>
								<?php endif; ?>
							</div>
							<div class="col text-right">
								<a href="<?= url_to('forgot') ?>">Forgot Password ?</a>
							</div>
						</div>
						<div class="sign-btn text-center">
							<button class="btn btn-theme">Sign In</button>
						</div>
					</form>
					<div class="register">
						<p>Don't have an account? <a href="<?= url_to('register') ?>">Create account</a></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?= $this->endSection() ?>