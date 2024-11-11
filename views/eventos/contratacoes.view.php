<?php 
if(!defined('ABSPATH')) exit; 

$modelo->getEvento(chk_array($parametros, 1));

if(!empty($_POST['notificar']) && $_POST['notificar'] == 'true'){
	$modeloEventoContratacoes->notificaContratados(chk_array($parametros, 1));
}

if(!empty($_POST['idEventoFase']) && empty($_POST['notificar'])){
	$modeloEventoContratacoes->validarFormContratacoes();
}

$modeloEventoContratacoes->removerContratacao($parametros);
?>
        <div class="content-wrapper">
			<section class="content-header">
                <h1>Eventos<small><?php echo htmlentities(chk_array($modelo->form_data, 'evento')); ?></small></h1>
                
				<ol class="breadcrumb">
                    <li><a href="<?php echo HOME_URI; ?>"><i class="fa fa-dashboard"></i> Painel</a></li>
                    <li><a href="<?php echo HOME_URI; ?>/eventos">Eventos</a></li>
					<li><a href="<?php echo HOME_URI; ?>/eventos/index/perfil/<?php echo chk_array($parametros, 1); ?>"><?php echo htmlentities(chk_array($modelo->form_data, 'evento')); ?></a></li>
                    <li class="active"><a href="<?php echo HOME_URI; ?>/eventos/index/contratacoes/<?php echo chk_array($parametros, 1); ?>">Contratações</a></li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-md-6">
                        <div class="box box-primary">
							<?php
							if(empty($modeloEventoContratacoes->form_data['formulario'])){
								echo $modeloEventoContratacoes->form_msg;
							}
							?>
							<form role="form" action="" method="POST">
								<input type="hidden" name="notificar" id="notificar" value="true">
								<div class="box-footer">
									<button type="submit" class="btn btn-primary btn-block">ENVIAR NOTIFICAÇÕES PARA TODOS</button>
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
									
									<div class="pull-right box-tools">&nbsp;</div>
								</div>

								<form role="form" action="" name="funcaoFaseContratacao<?php echo $contadorFases; ?>" id="funcaoFaseContratacao<?php echo $contadorFases; ?>" method="POST" enctype="multipart/form-data">
									<input type="hidden" name="idEventoFase" id="idEventoFase" value="<?php echo $dadosFasesEventos['id']; ?>">
									
									<div class="box-body">
										<?php
										if(isset($modeloEventoContratacoes->form_data['formulario']) && $modeloEventoContratacoes->form_data['formulario'] == $dadosFasesEventos['id']){
											echo $modeloEventoContratacoes->form_msg;
										}
										?>
									
										<div class="row">
											<?php
											$funcoes = $modeloEventoCargos->getCargos($dadosFasesEventos['id']);  
											?>
											<div class="col-md-6">
												<div class="form-group">
													<div class="form-group">
														<label for="idEventoFaseFuncao<?php echo $dadosFasesEventos['id']; ?>">Mão de obra / Função</label>
														<select class="form-control select2 selectTerceirizado" name="idEventoFaseFuncao" id="idEventoFaseFuncao<?php echo $dadosFasesEventos['id']; ?>">
															<option value="" idFuncao="">Escolha uma opção</option>
															<?php
															foreach($funcoes AS $dadosFuncoes):
																$contratados = $modeloEventoContratacoes->getContratacoes($dadosFasesEventos['id'], $dadosFuncoes['idFuncao']);
																$contratacoesRestantes = $dadosFuncoes['quantidade'] - count($contratados);
																if($contratacoesRestantes > 0){
																	echo"<option value='" . $dadosFuncoes['id'] . "' ";
																	if(htmlentities(chk_array($modeloEventoCargos->form_data, 'idFuncao')) == $dadosFuncoes['id'] && chk_array($modeloEventoContratacoes->form_data, 'idEventoFaseFuncao') == $dadosFasesEventos['id']){ echo'selected'; }
																	echo" idFuncao='" . $dadosFuncoes['idFuncao'] . "' selectPessoa='#sPessoa" . $dadosFasesEventos['id'] . "'>(" . $contratacoesRestantes . ") " . $dadosFuncoes['funcao'] . " - R$ " . number_format($dadosFuncoes['valor'], 2, ',', '.') . "/dia</option>";
																}
															endforeach;
															?>
														</select>
													</div>
												</div>
											</div>
											
											<div class="col-md-6">
												<div class="form-group">
													<div class="form-group">
														<label for="sPessoa<?php echo $dadosFasesEventos['id']; ?>">Terceirizado</label>
														<select class="form-control select2" name="idPessoa" id="sPessoa<?php echo $dadosFasesEventos['id']; ?>">
														</select>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="box-footer">
										<button type="submit" class="btn btn-primary">Salvar</button>
									</div>
								</form>
								
								<?php 
								$cargosEvento = $modeloEventoContratacoes->getContratacoes($dadosFasesEventos['id']); 
								if(count($cargosEvento) > 0){
								?>
									<div class="table-responsive">
										<table class="table no-margin">
											<thead>
												<tr>
													<th>Mão de obra / Função</th>
													<th>Terceirizado</th>
													<th>Status</th>
													<th>Opções</th>
												</tr>
											</thead>
											
											<tbody>
												<?php foreach($cargosEvento AS $dadosCargosEvento): ?>
												<tr>
													<td><?php echo $dadosCargosEvento['funcao']; ?> - R$ <?php echo number_format($dadosCargosEvento['valor'], 2, ',', '.'); ?>/dia</td>
													<td><?php echo $dadosCargosEvento['nome']; ?> <?php echo $dadosCargosEvento['sobrenome']; ?></td>
													<td>
													<?php 
													if(empty($dadosCargosEvento['observacaoAprovacao'])){
														if(empty($dadosCargosEvento['tokenAprovacao'])){
															echo'Não Notificado';
														}else{
															echo'Pendente';
														}
													}else{
														if($dadosCargosEvento['observacaoAprovacao'] == 'Aprovado'){
															echo'<span class="text-green">Aprovado</span>';
														}
														
														if($dadosCargosEvento['observacaoAprovacao'] == 'Recusado'){
															echo'<span class="text-red">Recusado</span>';
														}
													}
													?>
													</td>
													<td>
														<a href="<?php echo HOME_URI; ?>/eventos/index/contratacoes/<?php echo $dadosFasesEventos['idEvento']; ?>/fases/editar/<?php echo $dadosFasesEventos['id']; ?>/remover/<?php echo $dadosCargosEvento['id']; ?>" class="icon-tab" title="Remover"><i class="fa fa-minus-circle fa-lg"></i></a>
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
					
                     <div class="col-md-6">
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
									<li><a href="#">Parceiro <span class="pull-right"><?php echo htmlentities(chk_array($modelo->form_data, 'nomeFantasia')); ?></span></a></li>
									
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
                </div>
            </section>
        </div>
		
		<script>
		function select2Pessoa(){
			select2ajax('.selectTerceirizado', null, 'eventos/json/terceirizados', 'idFuncao', 'selectPessoa');
		};
		
		jQuery(document).ready(function ($) { setTimeout('select2Pessoa()', 1000); });
		</script>