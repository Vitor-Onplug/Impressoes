<?php
if(!defined('ABSPATH')) exit;

$modeloEnderecos->validarFormEndereco();

if (chk_array($this->parametros, 1)) {
	$hash = chk_array($this->parametros, 1);
	$id = decryptHash($hash);
}

$enderecos = $modeloEnderecos->getEnderecos($id);

if(chk_array($parametros, 3) == 'editar' || chk_array($parametros, 3) == 'remover' ){
	if(empty(chk_array($parametros, 4)) || !is_numeric(chk_array($parametros, 4))){
		echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/pessoas/index/editar/' . chk_array($this->parametros, 1) . '/enderecos">';
		echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/pessoas/index/editar/' . chk_array($this->parametros, 1) . '/enderecos";</script>';
		
		exit;
	}
	
	$modeloEnderecos->getEndereco(chk_array($parametros, 4));
	
	if(preg_match('/(inexistente)/simx', $modeloEnderecos->form_msg)){
		echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/pessoas">';
		echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/pessoas";</script>';
		
		exit;
	}
}

$modeloEnderecos->removerEndereco($parametros);
?>
				<?php require_once ABSPATH . '/views/pessoas/mini-perfil.inc.view.php'; ?>
				
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-6">
							<section class="content">
								<div class="card card-secondary">
									<div class="card-header">
										<h3 class="card-title">Endereço</h3>
									</div>
									<form role="form" action="" method="POST" enctype="multipart/form-data">
										<input type="hidden" id="latitude" name="latitude" value="<?php echo htmlentities(chk_array($modeloEnderecos->form_data, 'latitude')); ?>">
										<input type="hidden" id="longitude" name="longitude" value="<?php echo htmlentities(chk_array($modeloEnderecos->form_data, 'longitude')); ?>">
										<input type="hidden" id="status-mapa" name="status-mapa" value="true">
									
										<div class="card-body">
											<?php 
											echo $modeloEnderecos->form_msg;
											?>
											
											<div class="form-group">
												<label for="titulo">Título</label>
												<input type="text" class="form-control" id="titulo" name="titulo" placeholder="" value="<?php echo htmlentities(chk_array($modeloEnderecos->form_data, 'titulo')); ?>" required maxlength="255">
											</div>
											
											<div class="row">
												<div class="col-md-3">
													<label for="cep">CEP</label>
													<input type="text" class="form-control cep" id="cep" name="cep" placeholder="" value="<?php echo htmlentities(chk_array($modeloEnderecos->form_data, 'cep')); ?>" required maxlength="20">
												</div>
												
												<div class="col-md-6">
													<div class="form-group">
														<label for="logradouro">Logradouro</label>
														<input type="text" class="form-control" id="logradouro" name="logradouro" placeholder="" value="<?php echo htmlentities(chk_array($modeloEnderecos->form_data, 'logradouro')); ?>" required maxlength="255">
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label for="numero">Número</label>
														<input type="text" class="form-control" id="numero" name="numero" placeholder="" value="<?php echo htmlentities(chk_array($modeloEnderecos->form_data, 'numero')); ?>" onBlur="carregarEnderecoMapa($('#logradouro').val() + ', '  + $(this).val() + ' - ' + $('#bairro').val() + ' - ' + $('#cidade').val() + '/' + $('#estado').val() + ' - ' + $('.cep').val())" required maxlength="50">
													</div>
												</div>
											</div>
											
											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label for="complemento">Complemento</label>
														<input type="text" class="form-control" id="complemento" name="complemento" placeholder="" value="<?php echo htmlentities(chk_array($modeloEnderecos->form_data, 'complemento')); ?>" maxlength="255">
													</div>
												</div>
												
												<div class="col-md-6">
													<div class="form-group">
														<label for="zona">Zona</label>
														<input type="text" class="form-control" id="zona" name="zona" placeholder="" value="<?php echo htmlentities(chk_array($modeloEnderecos->form_data, 'zona')); ?>" maxlength="255">
													</div>
												</div>
											
												<div class="col-md-4">
													<label for="bairro">Bairro</label>
													<input type="text" class="form-control" id="bairro" name="bairro" placeholder="" value="<?php echo htmlentities(chk_array($modeloEnderecos->form_data, 'bairro')); ?>" required maxlength="255">
												</div>
												
												<div class="col-md-4">
													<div class="form-group">
														<label for="cidade">Cidade</label>
														<input type="text" class="form-control" id="cidade" name="cidade" placeholder="" value="<?php echo htmlentities(chk_array($modeloEnderecos->form_data, 'cidade')); ?>" required maxlength="255">
													</div>
												</div>
												
												<div class="col-md-4">
													<div class="form-group">
														<label for="estado">Estado</label>
														<select class="form-control" name="estado" id="estado" required>
															<option value="">UF</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'AC'){ echo"selected='selected'"; } ?> value="AC">AC</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'AL'){ echo"selected='selected'"; } ?> value="AL">AL</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'AP'){ echo"selected='selected'"; } ?> value="AP">AP</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'AM'){ echo"selected='selected'"; } ?> value="AM">AM</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'BA'){ echo"selected='selected'"; } ?> value="BA">BA</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'CE'){ echo"selected='selected'"; } ?> value="CE">CE</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'DF'){ echo"selected='selected'"; } ?> value="DF">DF</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'ES'){ echo"selected='selected'"; } ?> value="ES">ES</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'GO'){ echo"selected='selected'"; } ?> value="GO">GO</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'MA'){ echo"selected='selected'"; } ?> value="MA">MA</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'MG'){ echo"selected='selected'"; } ?> value="MG">MG</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'MT'){ echo"selected='selected'"; } ?> value="MT">MT</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'MS'){ echo"selected='selected'"; } ?> value="MS">MS</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'PA'){ echo"selected='selected'"; } ?> value="PA">PA</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'PB'){ echo"selected='selected'"; } ?> value="PB">PB</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'PR'){ echo"selected='selected'"; } ?> value="PR">PR</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'PE'){ echo"selected='selected'"; } ?> value="PE">PE</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'PI'){ echo"selected='selected'"; } ?> value="PI">PI</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'RJ'){ echo"selected='selected'"; } ?> value="RJ">RJ</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'RN'){ echo"selected='selected'"; } ?> value="RN">RN</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'RS'){ echo"selected='selected'"; } ?> value="RS">RS</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'RO'){ echo"selected='selected'"; } ?> value="RO">RO</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'SRR'){ echo"selected='selected'"; } ?> value="RR">RR</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'SC'){ echo"selected='selected'"; } ?> value="SC">SC</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'SP'){ echo"selected='selected'"; } ?> value="SP">SP</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'SE'){ echo"selected='selected'"; } ?> value="SE">SE</option>
															<option <?php if(htmlentities(chk_array($modeloEnderecos->form_data, 'estado')) == 'TO'){ echo"selected='selected'"; } ?> value="TO">TO</option>
														</select>
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
							
							<section class="content">
								<div class="card card-secondary">
									<div class="card-header">
										<h3 class="card-title">Mapa</h3>
									</div>
								
									<div class="card-body">
										<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=AIzaSyDqITRk0Rt9D7RsFR3spL9r_HiEupKEcY4&amp;"></script>
										<script type="text/javascript" src="<?php echo HOME_URI;?>/views/standards/javascripts/mapa.js"></script>									
										
										<div id="mapa" style="width: 100%; height: 400px;"></div>
									</div>

								</div>
							</section>
						</div>
						
						<div class="col-md-6">
							<section class="content">
								<div class="card card-secondary">
									<div class="card-header">
										<h3 class="card-title">Lista de Endereços</h3>
									</div>
									<div class="card-body">
										<?php
										if(count($enderecos) < 1){
											echo'<div class="alert alert-info">Nenhum endereço encontrado.</div>';
										}else{
										?>
										<table id="table" class="table table-hover table-bordered table-striped dataTable">
											<thead>
												<tr>
													<th>Título</th>
													<th class="sorter-false">Opções</th>

												</tr>
											</thead>
											
											<tbody>
												<?php foreach($enderecos AS $dados): ?>
												<tr>
													<td><?php echo $dados['titulo']; ?></td>
													<td>
														<a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>/enderecos/editar/<?php echo $dados['id']; ?>" class="icon-tab" title="Editar"><i class="far fa-edit fa-lg"></i></a>
														&nbsp;
														<a href="<?php echo HOME_URI;?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>/enderecos/remover/<?php echo $dados['id']; ?>" class="icon-tab" title="Remover"><i class="fas fa-times fa-lg text-red"></i></a>
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