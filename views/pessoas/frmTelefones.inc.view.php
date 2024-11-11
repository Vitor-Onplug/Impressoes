<?php
if(!defined('ABSPATH')) exit;

$modeloTelefones->validarFormTelefone();

$telefones = $modeloTelefones->getTelefones(chk_array($parametros, 1));

if(chk_array($parametros, 3) == 'editar' || chk_array($parametros, 3) == 'remover' ){
	if(empty(chk_array($parametros, 4)) || !is_numeric(chk_array($parametros, 4))){
		echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/pessoas/index/editar/' . chk_array($this->parametros, 1) . '/telefones">';
		echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/pessoas/index/editar/' . chk_array($this->parametros, 1) . '/telefones";</script>';
		
		exit;
	}
	
	$modeloTelefones->getTelefone(chk_array($parametros, 4));

	if(preg_match('/(inexistente)/simx', $modeloTelefones->form_msg)){
		echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/pessoas">';
		echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/pessoas";</script>';
		
		exit;
	}
}

$modeloTelefones->removerTelefone($parametros);
?>

				<?php require_once ABSPATH . '/views/pessoas/mini-perfil.inc.view.php'; ?>
				
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-6">
							<section class="content">
								<div class="card card-secondary">
									<div class="card-header">
										<h3 class="card-title">Telefone</h3>
									</div>
									<form role="form" action="" method="POST" enctype="multipart/form-data">
										<div class="card-body">
											<?php 
											echo $modeloTelefones->form_msg;
											?>
											
											<div class="row">
												<div class="col-md-6">
													<label for="tipo">Tipo</label>
													<select class="form-control" name="tipo" id="tipo" required>
														<option value="">Escolha</option>
														<option <?php if(htmlentities(chk_array($modeloTelefones->form_data, 'tipo')) == 'Fixo'){ echo'selected'; } ?> value="Fixo">Fixo</option>
														<option <?php if(htmlentities(chk_array($modeloTelefones->form_data, 'tipo')) == 'Celular'){ echo'selected'; } ?> value="Celular">Celular</option>
													</select>
												</div>
												
												<div class="col-md-6">
													<div class="form-group">
														<label for="telefone">Telefone</label>
														<input type="text" class="form-control <?php if(chk_array($modeloTelefones->form_data, 'tipo') == 'Fixo'){echo 'telefone'; }elseif(chk_array($modeloTelefones->form_data, 'tipo') == 'Celular'){echo 'celular'; } ?>" id="telefone" name="telefone" placeholder="" value="<?php echo htmlentities(chk_array($modeloTelefones->form_data, 'telefone')); ?>" required maxlength="50">
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
										<h3 class="card-title">Lista de Telefones</h3>
									</div>
									<div class="card-body">
										<?php
										if(count($telefones) < 1){
											echo'<div class="alert alert-info">Nenhum telefone encontrado.</div>';
										}else{
										?>
										<table id="table" class="table table-hover table-bordered table-striped dataTable">
											<thead>
												<tr>
													<th>Telefone</th>
													<th>Tipo</th>
													<th class="sorter-false">Opções</th>

												</tr>
											</thead>
											
											<tbody>
												<?php foreach($telefones AS $dados): ?>
												<tr>
													<td><?php echo $dados['telefone']; ?></td>
													<td><?php echo $dados['tipo']; ?></td>
													<td>
														<a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>/telefones/editar/<?php echo $dados['id']; ?>" class="icon-tab" title="Editar"><i class="far fa-edit fa-lg"></i></a>
														&nbsp;
														<a href="<?php echo HOME_URI;?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>/telefones/remover/<?php echo $dados['id']; ?>" class="icon-tab" title="Remover"><i class="fas fa-times fa-lg text-red"></i></a>
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

				<script>
				$(document).ready(function(){
					$('#tipo').on('change', function() {
						$('#telefone').removeClass('telefone');
						$('#telefone').removeClass('celular');
						
						if(this.value == 'Celular'){
							$("#telefone").inputmask("(99) 99999-9999");
						}
						
						if(this.value == 'Fixo'){
							$("#telefone").inputmask("(99) 9999-9999");
						}
						
						$("#telefone").val("");
					});
				});
				</script>