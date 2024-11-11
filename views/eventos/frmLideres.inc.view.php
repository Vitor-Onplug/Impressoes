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
                                </ul>
                            </div>
                        </div>
					</div>
					
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
								<i class="fa fa-user-circle-o"></i>
                                <h3 class="box-title">Liderança</h3>
                            </div>

                            <form role="form" action="" method="POST" enctype="multipart/form-data">
                                <div class="box-body">
									<?php 
									echo $modeloEventoLideres->form_msg;
									?>		
									<div class="row">
										<?php
										$liderancas = $modeloEventoLideres->getLiderancas(); 
										?>
										<div class="col-md-4">
											<div class="form-group">
												<div class="form-group">
													<label for="idLideranca">Liderança</label>
													<select class="form-control select2" name="idLideranca" id="idLideranca">
														<option value="">Escolha uma opção</option>
														<?php
														foreach($liderancas AS $dadosLiderancas):
															echo"<option value='" . $dadosLiderancas['id'] . "' ";
															if(htmlentities(chk_array($modeloEventoLideres->form_data, 'idLideranca')) == $dadosLiderancas['id']){ echo'selected'; }
															echo">" . $dadosLiderancas['lideranca'] . "</option>";
														endforeach;
														?>
													</select>
												</div>
											</div>
										</div>
										
										<?php
										$lideres = $modeloUsuarios->getUsuarios(array('hierarquia' => 3)); 
										?>
										<div class="col-md-4">
											<div class="form-group">
												<div class="form-group">
													<label for="idLider">Gerente</label>
													<select class="form-control select2" name="idLider" id="idLider">
														<option value="">Escolha uma opção</option>
														<?php
														foreach($lideres AS $dadosLideres):
															echo"<option value='" . $dadosLideres['idPessoa'] . "' ";
															if(htmlentities(chk_array($modeloEventoLideres->form_data, 'idLider')) == $dadosLideres['idPessoa']){ echo'selected'; }
															echo">" . $dadosLideres['nome'] . " " . $dadosLideres['sobrenome'] . "</option>";
														endforeach;
														?>
													</select>
												</div>
											</div>
										</div>
										
										<div class="col-md-4">
											<div class="form-group">
												<label for="verba">Dinheiro Adiantado</label>
												<input type="text" class="form-control money" id="verba" name="verba" placeholder="" value="<?php echo htmlentities(chk_array($modeloEventoLideres->form_data, 'verba')); ?>">
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
						$lideresEvento = $modeloEventoLideres->getLideres(chk_array($parametros, 1)); 
						if(count($lideresEvento) > 0){
						?>
						<div class="box box-primary">
                            <div class="box-header with-border">
								<i class="fa fa-user-circle-o"></i>
                                <h3 class="box-title">Lideranças</h3>
                            </div>

                            <div class="table-responsive">
								<table class="table no-margin">
									<thead>
										<tr>
											<th>Gerente</th>
											<th>Liderança</th>
											<th>Dinheiro Adiantado</th>
											<th>Opções</th>
										</tr>
									</thead>
									
									<tbody>
										<?php foreach($lideresEvento AS $dadosLideresEventos): ?>
                                            <tr>
                                                <td><?php echo $dadosLideresEventos['nome']; ?> <?php echo $dadosLideresEventos['sobrenome']; ?></td>
                                                <td><?php echo $dadosLideresEventos['lideranca']; ?></td>
                                                <td><?php echo number_format($dadosLideresEventos['verba'], 2, ',', '.'); ?></td>
                                                <td>
													<a href="<?php echo HOME_URI; ?>/eventos/index/editar/<?php echo $dadosLideresEventos['idEvento']; ?>/lideres/editar/<?php echo $dadosLideresEventos['id']; ?>" class="icon-tab" title="Editar"><i class="fa fa-pencil-square-o  fa-lg"></i></a>
													&nbsp; &nbsp; &nbsp;
													<a href="<?php echo HOME_URI; ?>/eventos/index/editar/<?php echo $dadosLideresEventos['idEvento']; ?>/lideres/remover/<?php echo $dadosLideresEventos['id']; ?>" class="icon-tab" title="Remover"><i class="fa fa-minus-circle fa-lg"></i></a>
												</td>
                                            </tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
							
                        </div>
						
						<?php } ?>
					</div>

                </div>