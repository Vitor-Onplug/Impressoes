<?php
if(!defined('ABSPATH')) exit;

$modeloEmails->validarFormEmail();

$emails = $modeloEmails->getEmailsEmpresa(chk_array($parametros, 1));

if(chk_array($parametros, 3) == 'editar' || chk_array($parametros, 3) == 'remover' ){
	if(empty(chk_array($parametros, 4)) || !is_numeric(chk_array($parametros, 4))){
		echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/empresas/index/editar/' . chk_array($this->parametros, 1) . '/emails">';
		echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/empresas/index/editar/' . chk_array($this->parametros, 1) . '/emails";</script>';
		
		exit;
	}
	
	$modeloEmails->getEmail(chk_array($parametros, 4));
	
	if(preg_match('/(inexistente)/simx', $modeloEmails->form_msg)){
		echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/empresas">';
		echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/empresas";</script>';
		
		exit;
	}
}

$modeloEmails->removerEmail($parametros);
?>
				<?php require_once ABSPATH . '/views/empresas/mini-perfil.inc.view.php'; ?>
				
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-6">
							<section class="content">
								<div class="card card-secondary">
									<div class="card-header">
										<h3 class="card-title">E-mail</h3>
									</div>
									<form role="form" action="" method="POST" enctype="multipart/form-data">
										<div class="card-body">
											<?php 
											echo $modeloEmails->form_msg;
											?>
											
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="email">E-mail</label>
														<input type="text" class="form-control " id="email" name="email" placeholder="e-mail" value="<?php echo htmlentities(chk_array($modeloEmails->form_data, 'email')); ?>" required maxlength="255">
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
						
						<div class="col-md-6">
							<section class="content">
								<div class="card card-secondary">
									<div class="card-header">
										<h3 class="card-title">Lista de E-mails</h3>
									</div>
									<div class="card-body">
										<?php
										if(count($emails) < 1){
											echo'<div class="alert alert-info">Nenhum e-mail encontrado.</div>';
										}else{
										?>
										<table id="table" class="table table-hover table-bordered table-striped dataTable">
											<thead>
												<tr>
													<th>E-mail</th>
													<th class="sorter-false">Opções</th>

												</tr>
											</thead>
											
											<tbody>
												<?php foreach($emails AS $dados): ?>
												<tr>
													<td><?php echo $dados['email']; ?></td>
													<td>
														<a href="<?php echo HOME_URI; ?>/empresas/index/editar/<?php echo chk_array($parametros, 1); ?>/emails/editar/<?php echo $dados['id']; ?>" class="icon-tab" title="Editar"><i class="far fa-edit fa-lg"></i></a>
														&nbsp;
														<a href="<?php echo HOME_URI;?>/empresas/index/editar/<?php echo chk_array($parametros, 1); ?>/emails/remover/<?php echo $dados['id']; ?>" class="icon-tab" title="Remover"><i class="fas fa-times fa-lg text-red"></i></a>
													</td>
												</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
										<?php } ?>
									</div>
								</div>
							</section>
						</div>
					</div>
				</div>