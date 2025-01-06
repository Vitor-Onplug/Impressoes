<?php
if(!defined('ABSPATH')) exit;

$modeloDocumentos->validarFormDocumento();

if (chk_array($this->parametros, 1)) {
	$hash = chk_array($this->parametros, 1);
	$id = decryptHash($hash);
}

$documentos = $modeloDocumentos->getDocumentos($id);

if(chk_array($parametros, 3) == 'editar' || chk_array($parametros, 3) == 'remover' ){
	if(empty(chk_array($parametros, 4)) || !is_numeric(chk_array($parametros, 4))){
		echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/pessoas/index/editar/' . chk_array($this->parametros, 1) . '/documentos">';
		echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/pessoas/index/editar/' . chk_array($this->parametros, 1) . '/documentos";</script>';
		
		exit;
	}
	
	$modeloDocumentos->getDocumento(chk_array($parametros, 4));

	if(preg_match('/(inexistente)/simx', $modeloDocumentos->form_msg)){
		echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/pessoas">';
		echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/pessoas";</script>';
		
		exit;
	}
}

$modeloDocumentos->removerDocumento($parametros);
?>
				<?php require_once ABSPATH . '/views/pessoas/mini-perfil.inc.view.php'; ?>
				
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-6">
							<section class="content">
								<div class="card card-secondary">
									<div class="card-header">
										<h3 class="card-title">Documento</h3>
									</div>
									<form role="form" action="" method="POST" enctype="multipart/form-data">
										<div class="card-body">
											<?php 
											echo $modeloDocumentos->form_msg;
											?>
											
											<div class="row">
												<div class="col-md-6">
													<label for="tipo">Tipo</label>
													<select class="form-control" name="tipo" id="tipo" required>
														<option value="">Escolha</option>
														<option <?php if(htmlentities(chk_array($modeloDocumentos->form_data, 'tipo')) == 'RG'){ echo'selected'; } ?> value="RG">RG</option>
														<option <?php if(htmlentities(chk_array($modeloDocumentos->form_data, 'tipo')) == 'CPF'){ echo'selected'; } ?> value="CPF">CPF</option>
														<option <?php if(htmlentities(chk_array($modeloDocumentos->form_data, 'tipo')) == 'CNH'){ echo'selected'; } ?> value="CNH">CNH</option>
														<option <?php if(htmlentities(chk_array($modeloDocumentos->form_data, 'tipo')) == 'Passaporte'){ echo'selected'; } ?> value="Passaporte">Passaporte</option>
														<option <?php if(htmlentities(chk_array($modeloDocumentos->form_data, 'tipo')) == 'Outros'){ echo'selected'; } ?> value="Outros">Outros</option>
													</select>
												</div>
												
												<div class="col-md-6">
													<div class="form-group">
														<label for="titulo">Título</label>
														<input type="text" class="form-control " id="titulo" name="titulo" placeholder="título" value="<?php echo htmlentities(chk_array($modeloDocumentos->form_data, 'titulo')); ?>" required maxlength="255">
													</div>
												</div>
												
												<div class="col-md-12">
													<div class="form-group">
														<label for="documento">Documento</label>
														<input type="number" class="form-control " id="documento" name="documento" placeholder="documento" value="<?php echo htmlentities(chk_array($modeloDocumentos->form_data, 'documento')); ?>" required maxlength="255">
													</div>
												</div>

												<div class="col-md-12">
													<div class="form-group">
														<label for="detalhes">Detalhes</label>
														<input type="text" class="form-control" id="detalhes" name="detalhes" value="<?php echo htmlentities(chk_array($modeloDocumentos->form_data, 'detalhes')); ?>">
													</div>
												</div>

												<div class="col-md-12">
													<div class="form-group">
														<label for="arquivo">Arquivo</label>
														<input type="file" class="form-control" id="arquivo" name="arquivo" accept=".pdf">
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
										<h3 class="card-title">Lista de Documentos</h3>
									</div>
									<div class="card-body">
										<?php
										if(count($documentos) < 1){
											echo'<div class="alert alert-info">Nenhum documento encontrado.</div>';
										}else{
										?>
										<table id="table" class="table table-hover table-bordered table-striped dataTable">
											<thead>
												<tr>
													<th>Tipo</th>
													<th>Título</th>
													<th>Documento</th>
													<th>Detalhes</th>
													<th class="sorter-false">Opções</th>

												</tr>
											</thead>
											
											<tbody>
												<?php foreach($documentos AS $dados): ?>
												<tr>
													<td><?php echo $dados['tipo']; ?></td>
													<td><?php echo $dados['titulo']; ?></td>
													<td><?php echo $dados['documento']; ?></td>
													<td><?php echo $dados['detalhes']; ?></td>
													<td>
														<a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>/documentos/editar/<?php echo $dados['id']; ?>" class="icon-tab" title="Editar"><i class="far fa-edit fa-lg"></i></a>
														&nbsp;
														<a href="<?php echo HOME_URI;?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>/documentos/remover/<?php echo $dados['id']; ?>" class="icon-tab" title="Remover"><i class="fas fa-times fa-lg text-red"></i></a>
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
						$('#documento').removeClass('cpf');
						
						if(this.value == 'CPF'){
							$("#documento").inputmask("999.999.999-99");
						}
						
						$("#documento").val("");
					});
				});
				</script>