<?php 
if(!defined('ABSPATH')) exit; 

$parametros[1] = chk_array($this->userdata, 'id');

if(!empty(chk_array($parametros, 1)) && !is_numeric(chk_array($parametros, 1))){
	echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/pessoas">';
	echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/pessoas";</script>';
	
	exit;
}

$modelo->getPessoa(chk_array($parametros, 1));
$modeloUsuarios->validarFormTrocarMinhaSenha();
?>
		<div class="content-wrapper">
			<section class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1>Pessoa</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
								<li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>/pessoas">Pessoas</a></li>
								<li class="breadcrumb-item active"><a href="<?php echo HOME_URI; ?>/pessoas/index/trocar-minha-senha">Trocar Minha Senha</a></li>
							</ol>
						</div>
					</div>
				</div>
			</section>
			
			<?php require_once ABSPATH . '/views/pessoas/mini-perfil.inc.view.php'; ?>
			
			<div class="container-fluid">
				<section class="content">
					<div class="card card-secondary">
						<div class="card-header">
							<h3 class="card-title">Senha</h3>
						</div>
						<form role="form" action="" method="POST" enctype="multipart/form-data">
							<div class="card-body">
								<?php 
								echo $modeloUsuarios->form_msg;
								?>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label for="senhaAtual">Senha Atual</label>
											<input type="password" class="form-control" id="senhaAtual" name="senhaAtual" placeholder="" value="" required maxlength="255">
										</div>

										<div class="form-group">
											<label for="senha">Senha</label>
											<input type="password" class="form-control" id="senha" name="senha" placeholder="" value="" required maxlength="255">
										</div>
										
										 <div class="form-group">
											<label for="repitaSenha">Repita a Senha</label>
											<input type="password" class="form-control" id="repitaSenha" name="repitaSenha" placeholder="" value="" required maxlength="255">
										</div>
									</div>
								</div>
							</div>

							<div class="card-footer">
								<button type="submit" class="btn btn-primary">Salvar</button>
							</div>
						</form>
					</div>
				</section>
			</div>
		</div>