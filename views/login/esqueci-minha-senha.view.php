<?php 
if(!defined('ABSPATH')) exit; 

if(empty(chk_array($parametros, 1))){
	$modeloUsuarios->validarFormRecuperarSenha();
}else{
	$registro = $modeloUsuarios->getUsuario(null, null, chk_array($parametros, 1));
	$modeloUsuarios->validarFormTrocarSenha($registro['idPessoa']);
	
	if($registro == false){
		echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/login/index/esqueci-minha-senha">';
		echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/login/index/esqueci-minha-senha";</script>';
		
		exit;
	}
}
?>
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
				<?php if(!isset($registro)){ ?>
					<p class="login-box-msg">Enviaremos um e-mail para redefinir a sua senha</p>

					<?php echo $modeloUsuarios->form_msg; ?>

					<form action="" method="post">
						<div class="input-group mb-3">
							<input type="email" class="form-control" placeholder="E-mail" name="email" id="email" required autocomplete="off">
							<div class="input-group-append">
								<div class="input-group-text">
									<span class="fas fa-envelope"></span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12">
								<button type="submit" class="btn btn-primary btn-block">Solicitar nova senha</button>
							</div>
						</div>
					</form>
				<?php }else{ ?>
					<p class="login-box-msg">Olá <?php echo htmlentities($registro["nome"]); ?>, defina a sua nova senha.</p>
					<?php echo $modeloUsuarios->form_msg; ?>
					<form action="" method="post">
						<div class="input-group mb-3">
							<input type="password" class="form-control" placeholder="Senha" name="senha" id="senha" required>
							<div class="input-group-append">
								<div class="input-group-text">
									<span class="fas fa-lock"></span>
								</div>
							</div>
						</div>
						
						<div class="input-group mb-3">
							<input type="password" class="form-control" placeholder="Repita a Senha" name="repitaSenha" id="repitaSenha" required>
							<div class="input-group-append">
								<div class="input-group-text">
									<span class="fas fa-lock"></span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12">
								<button type="submit" class="btn btn-primary btn-block">Trocar Senha</button>
							</div>
						</div>
					</form>
			<?php } ?>

				<p class="mt-3 mb-1">
					<a href="./">Entrar no Sistema</a>
				</p>
			</div>
		</div>
	</div>
	
	<script src="<?php echo HOME_URI;?>/views/standards/adminlte/plugins/jquery/jquery.min.js"></script>
	<script src="<?php echo HOME_URI;?>/views/standards/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="<?php echo HOME_URI;?>/views/standards/adminlte/js/adminlte.min.js"></script>
	
	<script>
	$(document).ready(function(){
		 <?php if(empty(chk_array($parametros, 1))){ ?>
		 $("#email").focus();
		 <?php }else{ ?>
		 $("#senha").focus();
		 <?php } ?>
	});
	</script>
</body>

</html>