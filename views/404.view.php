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
		<div class="login-logo">
			<a href="./"><img src="<?php echo HOME_URI;?>/views/standards/images/logo-login.png" class="img-responsive" alt="<?php echo SYS_NAME; ?>"></a>
		</div>
		<div class="card">
			<div class="card-body login-card-body">
				<div class="alert alert-warning alert-dismissible">
					<h4><i class="icon fa fa-ban"></i>ERRO 404</h4>
					Página não encontrada!
				</div>

				<p class="mt-3 mb-1">
					<a href="./" class="btn btn-primary btn-block">Voltar</a>
				</p>
			</div>
		</div>
	</div>
	
	<script src="<?php echo HOME_URI;?>/views/standards/adminlte/plugins/jquery/jquery.min.js"></script>
	<script src="<?php echo HOME_URI;?>/views/standards/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="<?php echo HOME_URI;?>/views/standards/adminlte/js/adminlte.min.js"></script>
</body>

</html>