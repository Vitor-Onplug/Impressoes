                <div class="row">

                    <div class="col-md-12">
                        <div class="box box-widget widget-user-3">
                            <div class="widget-user-header bg-red">
                                <div class="widget-user-image">
                                    <img class="img-thumbnail" src="<?php echo HOME_URI;?>/utils/thumbnail/?width=445&height=250&imagem=<?php echo $modelo->getAvatar(chk_array($parametros, 1)); ?>" alt="<?php echo htmlentities(chk_array($modelo->form_data, 'evento')); ?>">
                                </div>
                                <h3 class="widget-user-username"><?php echo htmlentities(chk_array($modelo->form_data, 'evento')); ?></h3>
                                <h5 class="widget-user-desc"><?php echo htmlentities(chk_array($modelo->form_data, 'categoria')); ?></h5>
                            </div>
							
                            <div class="box-footer no-padding">
                                <ul class="nav nav-stacked">
									<li><a href="#">Cliente <span class="pull-right"><?php echo htmlentities(chk_array($modelo->form_data, 'nomeFantasia')); ?></span></a></li>
									
									<li><a href="#">Data início/fim <span class="pull-right"><?php echo htmlentities(chk_array($modelo->form_data, 'dataInicioFim')); ?></span></a></li>
									
                                    <li><a href="#">Verba Total <span class="pull-right">R$ <?php echo number_format(chk_array($modelo->form_data, 'verba'), 2, ',', '.'); ?></span></a></li>
									
									<li><a href="#">Local <span class="pull-right"><?php echo htmlentities(chk_array($modelo->form_data, 'tituloCurto')); ?></span></a></li>
									
									<li><a href="#">Endereço <span class="pull-right"><?php echo htmlentities(chk_array($modelo->form_data, 'logradouro')); ?>, <?php echo htmlentities(chk_array($modelo->form_data, 'numero')); ?><?php if(!empty(chk_array($modelo->form_data, 'complemento'))){ echo ' - ' . chk_array($modelo->form_data, 'complemento'); } ?> - <?php echo htmlentities(chk_array($modelo->form_data, 'bairro')); ?></span></a></li>
										
									<li><a href="#">CEP <span class="pull-right"><?php echo htmlentities(chk_array($modelo->form_data, 'cep')); ?></span></a></li>
										
									<li><a href="#">Cidade - UF <span class="pull-right"><?php echo htmlentities(chk_array($modelo->form_data, 'cidade')); ?> - <?php echo htmlentities(chk_array($modelo->form_data, 'estado')); ?></span></a></li>
									
									<?php
									$lideresEvento = $modeloEventoLideres->getLideres(chk_array($parametros, 1)); 
									if(count($lideresEvento) > 0){
									?>
										<?php foreach($lideresEvento AS $dadosLideresEventos): ?>
											<li><a href="#"><?php echo $dadosLideresEventos['lideranca']; ?><span class="pull-right"><?php echo $dadosLideresEventos['nome']; ?> <?php echo $dadosLideresEventos['sobrenome']; ?> / R$ <?php echo number_format($dadosLideresEventos['verba'], 2, ',', '.'); ?></span></a></li>
										<?php endforeach; ?>
									<?php } ?>
                                </ul>
                            </div>
                        </div>
						
					</div>
					
					<div class="col-md-12">
						<div class="box box-primary">
							<?php 
							echo $modeloEventoLideres->form_msg;
							?>
							<form role="form" action="" method="POST">
								<input type="hidden" name="notificar" id="notificar" value="true">
								<div class="box-footer">
									<button type="submit" class="btn btn-primary btn-block">ENVIAR NOTIFICAÇÕES PARA TODOS</button>
								</div>
							</form>
						</div>
					</div>
					
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
							<i class="fa fa-clock-o"></i>
                                <h3 class="box-title">Fase</h3>
                            </div>

                            <form role="form" action="" name="fase" id="fase" method="POST" enctype="multipart/form-data">
								<div class="box-body">
									<?php 
									echo $modeloEventoFases->form_msg;
									?>

									<div class="row">
										<?php
										$fases = $modeloFases->getFases(); 
										?>
										<div class="col-md-8">
											<div class="form-group">
												<div class="form-group">
													<label for="idFase">Nome da fase</label>
													<select class="form-control select2" name="idFase" id="idFase">
														<option value="">Escolha uma opção</option>
														<?php
														foreach($fases AS $dadosFases):
															echo"<option value='" . $dadosFases['id'] . "' ";
															if(htmlentities(chk_array($modeloEventoFases->form_data, 'idFase')) == $dadosFases['id']){ echo'selected'; }
															echo">" . $dadosFases['fase'] . "</option>";
														endforeach;
														?>
													</select>
												</div>
											</div>
										</div>
										
										<div class="col-md-4">
											<label for="dataInicioFim">Data início/fim:</label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control pull-right" name="dataInicioFim" id="dataInicioFim" value="<?php echo chk_array($modeloEventoFases->form_data, 'dataInicioFim'); ?>">
											</div>
										</div>
									</div>

								</div>
								
								<div class="box-footer">
									<button type="submit" class="btn btn-primary">Salvar</button>
                                </div>
                            </form>
                        </div>
						
						<?php 
						$fasesEvento = $modeloEventoFases->getFases(chk_array($parametros, 1)); 
						if(count($fasesEvento) > 0){
							$contadorFases = 1;
						?>
							<?php foreach($fasesEvento AS $dadosFasesEventos): ?>
							<div class="box box-primary">
								<div class="box-header with-border">
									<i class="fa fa-clock-o"></i>
									<h3 class="box-title">
										Fase #<?php echo $contadorFases;?>
										
										<small><?php echo $dadosFasesEventos['fase']; ?> - <?php echo implode("/", array_reverse(explode("-", $dadosFasesEventos['dataInicio']))) . " - " . implode("/", array_reverse(explode("-", $dadosFasesEventos['dataFim']))); ?></small>
									</h3>
									
									<div class="pull-right box-tools">
										<a href="<?php echo HOME_URI; ?>/eventos/index/editar/<?php echo $dadosFasesEventos['idEvento']; ?>/fases/editar/<?php echo $dadosFasesEventos['id']; ?>" class="icon-tab" title="Editar"><i class="fa fa-pencil-square-o  fa-lg"></i></a>
										<a href="<?php echo HOME_URI; ?>/eventos/index/editar/<?php echo $dadosFasesEventos['idEvento']; ?>/fases/remover/<?php echo $dadosFasesEventos['id']; ?>" class="icon-tab" title="Remover"><i class="fa fa-minus-circle fa-lg"></i></a>
									</div>
								</div>

								<form role="form" action="" name="funcaoFase<?php echo $contadorFases; ?>" id="funcaoFase<?php echo $contadorFases; ?>" method="POST" enctype="multipart/form-data">
									<input type="hidden" name="idEventoFase" id="idEventoFase" value="<?php echo $dadosFasesEventos['id']; ?>">
									
									<div class="box-body">
										<?php
										if(isset($modeloEventoCargos->form_data['formulario']) && $modeloEventoCargos->form_data['formulario'] == $dadosFasesEventos['id']){
											echo $modeloEventoCargos->form_msg;
										}
										?>
									
										<div class="row">
											<?php
											$funcoes = $modeloFuncoes->getFuncoes(); 
											?>
											<div class="col-md-5">
												<div class="form-group">
													<div class="form-group">
														<label for="idFuncao">Mão de obra / Função</label>
														<select class="form-control select2" name="idFuncao" id="idFuncao">
															<option value="">Escolha uma opção</option>
															<?php
															foreach($funcoes AS $dadosFuncoes):
																echo"<option value='" . $dadosFuncoes['id'] . "' ";
																if(htmlentities(chk_array($modeloEventoCargos->form_data, 'idFuncao')) == $dadosFuncoes['id'] && chk_array($modeloEventoCargos->form_data, 'idEventoFase') == $dadosFasesEventos['id']){ echo'selected'; }
																echo">" . $dadosFuncoes['funcao'] . " - R$ " . number_format($dadosFuncoes['valor'], 2, ',', '.') . "/dia</option>";
															endforeach;
															?>
														</select>
													</div>
												</div>
											</div>
											
											<?php
											$liderancas = $modeloEventoLideres->getLideres(chk_array($parametros, 1)); 
											?>
											<div class="col-md-5">
												<div class="form-group">
													<div class="form-group">
														<label for="idEventoLideranca">Liderança</label>
														<select class="form-control select2" name="idEventoLideranca" id="idEventoLideranca">
															<option value="">Escolha uma opção</option>
															<?php
															foreach($liderancas AS $dadosLiderancas):
																echo"<option value='" . $dadosLiderancas['id'] . "' ";
																if(htmlentities(chk_array($modeloEventoCargos->form_data, 'idEventoLideranca')) == $dadosLiderancas['id'] && chk_array($modeloEventoCargos->form_data, 'idEventoFase') == $dadosFasesEventos['id']){ echo'selected'; }
																echo">" . $dadosLiderancas['lideranca'] . "</option>";
															endforeach;
															?>
														</select>
													</div>
												</div>
											</div>
											
											<div class="col-md-2">
												<div class="form-group">
													<label for="quantidade">Quantidade</label>
													<input type="number" class="form-control" name="quantidade" id="quantidade" placeholder="" value="<?php if(!empty(chk_array($modeloEventoCargos->form_data, 'quantidade')) && chk_array($modeloEventoCargos->form_data, 'idEventoFase') == $dadosFasesEventos['id']){ echo htmlentities(chk_array($modeloEventoCargos->form_data, 'quantidade')); } ?>" step="1" min="1">
												</div>
											</div>
										</div>
									</div>
									
									<div class="box-footer">
										<button type="submit" class="btn btn-primary">Salvar</button>
									</div>
								</form>
								
								<?php 
								$cargosEvento = $modeloEventoCargos->getCargos($dadosFasesEventos['id']); 
								if(count($cargosEvento) > 0){
								?>
									<div class="table-responsive">
										<table class="table no-margin">
											<thead>
												<tr>
													<th>Mão de obra / Função</th>
													<th>Liderança</th>
													<th>Qtd</th>
													<th>Opções</th>
												</tr>
											</thead>
											
											<tbody>
												<?php foreach($cargosEvento AS $dadosCargosEvento): ?>
												<tr>
													<td><?php echo $dadosCargosEvento['funcao']; ?></td>
													<td><?php echo $dadosCargosEvento['lideranca']; ?></td>
													<td><?php echo $dadosCargosEvento['quantidade']; ?></td>
													<td>
														<a href="<?php echo HOME_URI; ?>/eventos/index/editar/<?php echo $dadosFasesEventos['idEvento']; ?>/fases/editar/<?php echo $dadosFasesEventos['id']; ?>/editar/<?php echo $dadosCargosEvento['id']; ?>" class="icon-tab" title="Editar"><i class="fa fa-pencil-square-o  fa-lg"></i></a>
														&nbsp; &nbsp; &nbsp;
														<a href="<?php echo HOME_URI; ?>/eventos/index/editar/<?php echo $dadosFasesEventos['idEvento']; ?>/fases/editar/<?php echo $dadosFasesEventos['id']; ?>/remover/<?php echo $dadosCargosEvento['id']; ?>" class="icon-tab" title="Remover"><i class="fa fa-minus-circle fa-lg"></i></a>
													</td>
												</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
								<?php } ?>
							</div>
						<?php
								$contadorFases++;
							endforeach; 
						}
						?>
					</div>
	
                </div>

				<script>
				$(document).ready(function(){
					setDateRangePicker("#dataInicioFim", "<?php echo implode("/", array_reverse(explode("-", chk_array($modelo->form_data, 'dataInicio')))); ?>", "<?php echo implode("/", array_reverse(explode("-", chk_array($modelo->form_data, 'dataFim')))); ?>");
				});
				</script>