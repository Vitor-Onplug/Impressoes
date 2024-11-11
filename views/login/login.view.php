<?php if ( ! defined('ABSPATH')) exit; ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo SYS_NAME; ?></title>
	
	<base href="<?php echo HOME_URI;?>/">
	
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<link rel="stylesheet" href="<?php echo HOME_URI;?>/views/standards/adminlte/plugins/fontawesome-free/css/all.min.css">
	<link rel="stylesheet" href="<?php echo HOME_URI;?>/views/standards/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo HOME_URI;?>/views/standards/adminlte/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">
	<div class="login-box">
		<div class="card card-outline card-primary">
			<div class="card-header text-center">
				<a href="./"><img src="<?php echo HOME_URI;?>/views/standards/images/logo-login.png" class="img-responsive" alt="<?php echo SYS_NAME; ?>"></a>
			</div>
			<div class="card-body">
				<p class="login-box-msg">Bem-vindo ao <?php echo SYS_NAME; ?></p>
				
				<?php
				if(!empty($this->login_error)){
					echo $this->login_error;
				}
				?>

				<form action="" method="post">
					<div class="input-group mb-3">
						<input type="email" class="form-control" placeholder="E-mail" name="userdata[email]" required autocomplete="off">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-envelope"></span>
							</div>
						</div>
					</div>
					<div class="input-group mb-3">
						<input type="password" class="form-control" placeholder="Senha" name="userdata[senha]" required autocomplete="off">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-lock"></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-8">
							<a href="<?php echo HOME_URI;?>/login/index/esqueci-minha-senha">Esqueci minha senha</a>
						</div>
						<div class="col-4">
							<button type="submit" class="btn btn-primary btn-block">Entrar</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<script src="<?php echo HOME_URI;?>/views/standards/adminlte/plugins/jquery/jquery.min.js"></script>
	<script src="<?php echo HOME_URI;?>/views/standards/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="<?php echo HOME_URI;?>/views/standards/adminlte/js/adminlte.min.js"></script>
</body>

</html>