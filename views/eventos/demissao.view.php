<?php 
if(!defined('ABSPATH')) exit; 

$modeloEventoContratacoesDemissao->validarFormDemissao();

$modeloEventoContratacoes->getContratacao(chk_array($parametros, 1));

$modelo->getEvento(chk_array($modeloEventoContratacoes->form_data, 'idEvento'));

$modeloUsuarios->getUsuario(chk_array($modeloEventoContratacoes->form_data, 'idUsuario'));
$dataHoraCriacaoUsuario = explode(" ", chk_array($modeloUsuarios->form_data, 'dataCriacao'));
$dataCriacaoUsuario = explode("-", $dataHoraCriacaoUsuario[0]);

$faltas = $modeloEventoContratacoesFaltas->getFaltas(null, chk_array($parametros, 1));
$quantidadeFaltas = count($faltas);
?>
        <div class="content-wrapper">
            <section class="content-header">
                <h1>Pagamento<small><?php echo htmlentities(chk_array($modelo->form_data, 'evento')); ?></small></h1>
                
				<ol class="breadcrumb">
                    <li><a href="<?php echo HOME_URI; ?>"><i class="fa fa-dashboard"></i> Painel</a></li>
                    <li><a href="<?php echo HOME_URI; ?>/eventos">Eventos</a></li>
                    <li><a href="<?php echo HOME_URI; ?>/eventos/index/perfil/<?php echo chk_array($modelo->form_data, 'id'); ?>"><?php echo htmlentities(chk_array($modelo->form_data, 'evento')); ?></a></li>
                    <li class="active"><a href="<?php echo HOME_URI; ?>/eventos/index/pagamento/<?php echo chk_array($parametros, 1); ?>">Pagamento</a></li>
                </ol>
            </section>

            <section class="content">
				<div class="row">
					<div class="col-md-12">
						<div class="info-perfil">
							<span class="info-perfil-icon bg-aqua"><img class="" src="<?php echo HOME_URI;?>/utils/thumbnail/?width=445&height=250&imagem=<?php echo $modelo->getAvatar(chk_array($modelo->form_data, 'id')); ?>" alt="<?php echo htmlentities(chk_array($modelo->form_data, 'evento')); ?>"></span>

							<div class="info-perfil-content">
								<span class="info-perfil-text"><?php echo htmlentities(chk_array($modelo->form_data, 'dataInicioFim')); ?></span>
								<span class="info-perfil-number"><?php echo htmlentities(chk_array($modelo->form_data, 'evento')); ?></span>
								<span class="info-perfil-text"><?php echo htmlentities(chk_array($modelo->form_data, 'categoria')); ?></span>
								<span class="info-perfil-text"><a href="<?php echo HOME_URI; ?>/clientes/index/perfil/<?php echo htmlentities(chk_array($modelo->form_data, 'idCliente')); ?>"><?php echo htmlentities(chk_array($modelo->form_data, 'nomeFantasia')); ?></a></a></span>
							</div>
						</div>
					</div>
				</div>
		
                <div class="row">
                    <div class="col-md-7">
						<div class="row">
							<div class="col-md-12">
								<div class="callout" style="background-color: <?php echo chk_array($modeloEventoContratacoes->form_data, 'cor'); ?>; color: #fff !important;">
									<h4 class="mb-x0"><i class="fa fa-check-circle-o"></i> <?php echo chk_array($modeloEventoContratacoes->form_data, 'fase'); ?> - <?php echo implode("/", array_reverse(explode("-", chk_array($modeloEventoContratacoes->form_data, 'dataInicio')))); ?> - <?php echo implode("/", array_reverse(explode("-", chk_array($modeloEventoContratacoes->form_data, 'dataFim')))); ?></h4>
								</div>
							</div>
						</div>
						
						
                        <div class="box box-danger">
                            <div class="box-header with-border">
								<i class="fa fa-user-circle-o"></i>
                                <h3 class="box-title"><?php echo htmlentities(chk_array($modeloUsuarios->form_data, 'nome')); ?> <?php echo htmlentities(chk_array($modeloUsuarios->form_data, 'sobrenome')); ?></h3>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table no-margin">
                                        <thead>
                                            <tr>
                                                <th>Período</th>
                                                <th>Função</th>
												<th>Diária</th>
												<th>Dias</th>
												<th>Valor Diárias</th>
												<th>Faltas</th>
												<th>Valor Faltas</th>
												<th>Total</th>
                                            </tr>
                                        </thead>
										
                                        <tbody>
										
                                            <tr>
                                                <td><?php echo implode("/", array_reverse(explode("-", chk_array($modeloEventoContratacoes->form_data, 'dataInicio')))); ?> - <?php echo implode("/", array_reverse(explode("-", chk_array($modeloEventoContratacoes->form_data, 'dataFim')))); ?></td>
												<td><?php echo chk_array($modeloEventoContratacoes->form_data, 'funcao'); ?></td>
												<td>R$ <?php echo number_format(chk_array($modeloEventoContratacoes->form_data, 'valor'), 2, ',', '.'); ?></td>
												<td><?php echo $quantidadeDias = diff_datas(implode("/", array_reverse(explode("-", chk_array($modeloEventoContratacoes->form_data, 'dataInicio')))), implode("/", array_reverse(explode("-", chk_array($modeloEventoContratacoes->form_data, 'dataFim'))))) + 1; ?></td>
												<td>R$ <?php echo number_format((chk_array($modeloEventoContratacoes->form_data, 'valor') * $quantidadeDias), 2, ',', '.'); ?></td>
												<td><?php echo $quantidadeFaltas; ?></td>
												<td>R$ <?php echo number_format((chk_array($modeloEventoContratacoes->form_data, 'valor') * $quantidadeFaltas), 2, ',', '.'); ?></td>
												<td>R$ <?php echo number_format((chk_array($modeloEventoContratacoes->form_data, 'valor') * ($quantidadeDias - $quantidadeFaltas)), 2, ',', '.'); ?></td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
						
						<?php
						if($quantidadeFaltas > 0){
						?>
                        <div class="box box-danger">
                            <div class="box-header with-border">
								<i class="fa fa-calendar-times-o"></i>
                                <h3 class="box-title">Faltas</h3>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>

                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table no-margin">
                                        <thead>
                                            <tr>
                                                <th>Dia da falta</th>
                                            </tr>
                                        </thead>
										
                                        <tbody>
											
											<?php 
											foreach($faltas AS $dadosContratados): 
											?>
											<tr>
                                                <td><?php echo implode("/", array_reverse(explode("-", $dadosContratados["falta"]))); ?></td>
                                            </tr>
											<?php endforeach; ?>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
						<?php
						}
						?>
						
                        <div class="box box-primary">
                            <div class="box-header with-border">
							<i class="fa fa-user-times"></i>
                                <h3 class="box-title">Demissão</h3>
                            </div>

                            <form role="form" action="" method="POST" enctype="multipart/form-data">
								<div class="box-body">
									<?php 
									echo $modeloEventoContratacoesDemissao->form_msg;
									?>
									
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label for="dataDispensado">Dia da Demissão</label>
												<div class="input-group date">
													<div class="input-group-addon">
														<i class="fa fa-calendar"></i>
													</div>
													<input type="text" class="form-control pull-right datepicker" id="dataDispensado" name="dataDispensado" placeholder="" value="<?php if(!empty(chk_array($modeloEventoContratacoesDemissao->form_data, 'dataDispensado'))){ echo htmlentities(chk_array($modeloEventoContratacoesDemissao->form_data, 'dataDispensado')); }else{ echo implode("/", array_reverse(explode("-", chk_array($modeloEventoContratacoes->form_data, 'dataDispensado')))); } ?>">
												</div>
											</div>
										</div>
										
										
										<div class="col-md-8">
											<div class="form-group">
												<label for="observacaoDispensado">Justificativa</label>
												<textarea class="textarea" name="observacaoDispensado" id="observacaoDispensado" placeholder="" style="width: 100%; height: 75px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php if(!empty(chk_array($modeloEventoContratacoesDemissao->form_data, 'observacaoDispensado'))){ echo htmlentities(chk_array($modeloEventoContratacoesDemissao->form_data, 'dataDispensado')); }else{ echo chk_array($modeloEventoContratacoes->form_data, 'observacaoDispensado'); } ?></textarea>
											</div>
										</div>

									</div>
                                </div> 
								
								<div class="box-footer">
									<button type="submit" class="btn btn-primary">Demitir</button>
                                </div>
                            </form>
                        </div>
					</div>

                    <div class="col-md-5">
						<div class="box box-widget widget-user">
                            <div class="widget-user-header bg-red-active">
                                <h3 class="widget-user-username"><?php echo htmlentities(chk_array($modeloUsuarios->form_data, 'nome')); ?></h3>
                                <h5 class="widget-user-desc"><?php echo htmlentities(chk_array($modeloUsuarios->form_data, 'permissao')); ?></h5>
                            </div>
							
                            <div class="widget-user-image">
                                <img class="img-circle" src="<?php echo HOME_URI;?>/utils/thumbnail/?width=128&height=128&imagem=<?php echo $modeloUsuarios->getAvatar(chk_array($modeloUsuarios->form_data, 'id')); ?>" alt="<?php echo htmlentities(chk_array($modeloUsuarios->form_data, 'nome')); ?>">
                            </div>
							
                            <div class="box-footer">
                                <div class="row">
                                    
									<div class="col-sm-4 border-right">
                                        <div class="description-block">
                                            <h5 class="description-header">05 EVENTOS</h5>
                                            <span class="description-text">DESDE <?php echo mesAbreviado($dataCriacaoUsuario[1]) . '/' . $dataCriacaoUsuario[0];	?></span>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                    </div>
									
                                    <div class="col-sm-4 border-left">
                                        <div class="description-block">
                                            <h5 class="description-header">
												<?php
												$pontuacao = $modeloUsuarios->getPontuacao(chk_array($modeloUsuarios->form_data, 'idPessoa'));
												
												if($pontuacao["media"] < 0.5){
													echo'<i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>';
												}elseif($pontuacao["media"] < 1){
													echo'<i class="fa fa-star-half-o"></i><i class="fa fa-star-o"></i><iclass="fa fa-star-o"></i><iclass="fa fa-star-o"></i><iclass="fa fa-star-o"></i>';
												}elseif($pontuacao["media"] < 1.5){
													echo'<i class="fa fa-star"></i><i class="fa fa-star-o"></i><iclass="fa fa-star-o"></i><iclass="fa fa-star-o"></i><iclass="fa fa-star-o"></i>';
												}elseif($pontuacao["media"] < 2){
													echo'<i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><iclass="fa fa-star-o"></i><iclass="fa fa-star-o"></i><iclass="fa fa-star-o"></i>';
												}elseif($pontuacao["media"] < 2.5){
													echo'<i class="fa fa-star"></i><i class="fa fa-star"></i><iclass="fa fa-star-o"></i><iclass="fa fa-star-o"></i><iclass="fa fa-star-o"></i>';
												}elseif($pontuacao["media"] < 3){
													echo'<i class="fa fa-star"></i><i class="fa fa-star"></i><iclass="fa fa-star-half-o"></i><iclass="fa fa-star-o"></i><iclass="fa fa-star-o"></i>';
												}elseif($pontuacao["media"] < 3.5){
													echo'<i class="fa fa-star"></i><i class="fa fa-star"></i><iclass="fa fa-star"></i><iclass="fa fa-star-o"></i><iclass="fa fa-star-o"></i>';
												}elseif($pontuacao["media"] < 4){
													echo'<i class="fa fa-star"></i><iclass="fa fa-star"></i><iclass="fa fa-star"></i><iclass="fa fa-star-half-o"></i><iclass="fa fa-star-o"></i>';
												}elseif($pontuacao["media"] < 4.5){
													echo'<i class="fa fa-star"></i><i class="fa fa-star"></i><iclass="fa fa-star"></i><iclass="fa fa-star"></i><iclass="fa fa-star-o"></i>';
												}elseif($pontuacao["media"] < 5){
													echo'<i class="fa fa-star"></i><i class="fa fa-star"></i><iclass="fa fa-star"></i><iclass="fa fa-star"></i><iclass="fa fa-star-half-o"></i>';
												}else{
													echo'<i class="fa fa-star"></i><i class="fa fa-star"></i><iclass="fa fa-star"></i><iclass="fa fa-star"></i><iclass="fa fa-star"></i>';
												}
												?>
												&nbsp;&nbsp;
												<strong><?php echo $pontuacao["media"]; ?></strong>
											</h5>
                                            <span class="description-text">PONTUAÇÃO</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
												
					    <div class="box box-danger">
                            <div class="box-header with-border">
								<i class="fa fa-address-card-o"></i>
                                <h3 class="box-title">Informações</h3>
                            </div>

                            <div class="box-body pt-x0 pb-x0">
                                
								<div class="box-footer no-padding bt-none">
									<ul class="nav nav-stacked">
										<li><a href="#">Nome <span class="pull-right"><?php echo htmlentities(chk_array($modeloUsuarios->form_data, 'nome')); ?> <?php echo htmlentities(chk_array($modeloUsuarios->form_data, 'sobrenome')); ?></span></a></li>
										
										<li><a href="#">CPF / RG <span class="pull-right"><?php echo htmlentities(chk_array($modeloUsuarios->form_data, 'cpf')); ?> / <?php echo htmlentities(chk_array($modeloUsuarios->form_data, 'rg')); ?></span></a></li>
										
										<li><a href="#">Nome da mãe <span class="pull-right"><?php echo htmlentities(chk_array($modeloUsuarios->form_data, 'nomeMae')); ?></span></a></li>
										
										<li><a href="#">Email/Usuário<span class="pull-right"><?php echo htmlentities(chk_array($modeloUsuarios->form_data, 'email')); ?></span></a></li>
										
										<li><a href="#">Celular / Nextel<span class="pull-right"><?php echo htmlentities(chk_array($modeloUsuarios->form_data, 'celular')); ?> <?php if(!empty(chk_array($modeloUsuarios->form_data, 'nextel'))){ echo '/ ' . htmlentities(chk_array($modeloUsuarios->form_data, 'nextel')); } ?></span></a></li>

										<?php
										$funcoes = $modeloUsuarios->getUsuarioFuncoes(chk_array($modeloUsuarios->form_data, 'idPessoa'));
										if(count($funcoes) > 0){
										?>
										<li>
											<a href="#">Funções
												<span class="pull-right">
													<?php 
													$contadorFuncoes = 0;
													foreach($funcoes AS $dadosFuncoes):
														if($contadorFuncoes > 0){ echo " - "; }
														
														echo $dadosFuncoes['funcao'];
														
														$contadorFuncoes++;
													endforeach;
													?>
												</span>
											</a>
										</li>
										<?php } ?>
										
										<li>
											<a href="#">Idiomas
												<span class="pull-right">
													<?php 
													$contadorIdiomas = 0;
													foreach($modeloUsuarios->form_data['idiomas'] AS $idIdioma):
														if($contadorIdiomas > 0){ echo " - "; }

														echo chk_array(chk_array($modeloIdiomas->getIdioma($idIdioma), 0), 'idioma');
														
														$contadorIdiomas++;
													endforeach;
													?>
												</span>
											</a>
										</li>
										
										<li><a href="#">Endereço <span class="pull-right"><?php echo htmlentities(chk_array($modeloUsuarios->form_data, 'logradouro')); ?>, <?php echo htmlentities(chk_array($modeloUsuarios->form_data, 'numero')); ?><?php if(!empty(chk_array($modeloUsuarios->form_data, 'complemento'))){ echo ' - ' . chk_array($modeloUsuarios->form_data, 'complemento'); } ?><?php if(!empty(chk_array($modeloUsuarios->form_data, 'zona'))){ echo ' - Zona ' . chk_array($modeloUsuarios->form_data, 'zona'); } ?> - <?php echo htmlentities(chk_array($modeloUsuarios->form_data, 'bairro')); ?></span></a></li>
										
										<li><a href="#">CEP <span class="pull-right"><?php echo htmlentities(chk_array($modeloUsuarios->form_data, 'cep')); ?></span></a></li>
										
										<li><a href="#">Cidade - UF <span class="pull-right"><?php echo htmlentities(chk_array($modeloUsuarios->form_data, 'cidade')); ?> - <?php echo htmlentities(chk_array($modeloUsuarios->form_data, 'estado')); ?></span></a></li>
									</ul>
								</div>
							
                            </div>

                        </div>
					</div>
                </div>
            </section>
        </div>
