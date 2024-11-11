<?php 
if(!defined('ABSPATH')) exit; 

$modelo->getEvento(chk_array($parametros, 1));

$modeloEventoContratacoesFaltas->validarFormFaltas();
$modeloEventoContratacoesFaltas->removerFalta($parametros);
?>
        <div class="content-wrapper">
            <section class="content-header">
                <h1>Controle de Faltas<small><?php echo htmlentities(chk_array($modelo->form_data, 'evento')); ?></small></h1>
                
				<ol class="breadcrumb">
                    <li><a href="<?php echo HOME_URI; ?>"><i class="fa fa-dashboard"></i> Painel</a></li>
                    <li><a href="<?php echo HOME_URI; ?>/eventos">Eventos</a></li>
                    <li><a href="<?php echo HOME_URI; ?>/eventos/index/perfil/<?php echo chk_array($parametros, 1); ?>"><?php echo htmlentities(chk_array($modelo->form_data, 'evento')); ?></a></li>
                    <li class="active"><a href="<?php echo HOME_URI; ?>/eventos/index/faltas/<?php echo chk_array($parametros, 1); ?>">Faltas</a></li>
                </ol>
            </section>

            <section class="content">
				<div class="row">
					<div class="col-md-12">
						<div class="info-perfil">
							<span class="info-perfil-icon bg-aqua"><img class="" src="<?php echo HOME_URI;?>/utils/thumbnail/?width=445&height=250&imagem=<?php echo $modelo->getAvatar(chk_array($parametros, 1)); ?>" alt="<?php echo htmlentities(chk_array($modelo->form_data, 'evento')); ?>"></span>

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
                    <div class="col-md-4">
                       <?php
					   $contratados = $modeloEventoContratacoes->getContratacoes(null, null, chk_array($parametros, 1));
					   ?>
                        <div class="box box-primary">
                            <div class="box-header with-border">
								<i class="fa fa-user-times"></i>
                                <h3 class="box-title">Adicionar uma Falta</h3>
                            </div>

                            <form role="form" action="" method="POST" enctype="multipart/form-data">
                                <div class="box-body">
									<?php 
									echo $modeloEventoContratacoesFaltas->form_msg;
									?>
									<div class="form-group">
										<label for="idEventoFaseContratado">Contratado</label>
										<select class="form-control select2" name="idEventoFaseContratado" id="idEventoFaseContratado">
											<option value="">Escolha</option>
											<?php
											foreach($contratados AS $dadosContratados):
												echo"<option value='" . $dadosContratados['id'] . "' ";
												if(htmlentities(chk_array($modeloEventoContratacoesFaltas->form_data, 'idEventoFaseContratado')) == $dadosContratados['id']){ echo'selected'; }
												echo">" . $dadosContratados['nome'] . " " . $dadosContratados['sobrenome'] . " (" . $dadosContratados['funcao'] . ")</option>";
											endforeach;
											?>
										</select>
									</div>
									
									<div class="form-group">
										<label for="falta">Dia da falta</label>
										<div class="input-group date">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control pull-right datepicker" id="falta" name="falta" placeholder="" value="<?php echo htmlentities(chk_array($modeloEventoContratacoesFaltas->form_data, 'falta')); ?>">
										</div>
									</div>

                                </div>
								
								<div class="box-footer">
									<button type="submit" class="btn btn-primary">Salvar</button>
								</div>
                            </form>
                        </div>
					</div>
					
                    <div class="col-md-8">
                        <div class="box box-danger">
                            <div class="box-header with-border">
								<i class="fa fa-list"></i>
                                <h3 class="box-title">Lista de Contratados</h3>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table no-margin">
                                        <thead>
                                            <tr>
                                                <th>Contratado</th>
                                                <th>Função</th>
												<th>Fase</th>
												<th>Faltas</th>
                                            </tr>
                                        </thead>
										
                                        <tbody>

											<?php 
											foreach($contratados AS $dadosContratados): 
												$faltas = $modeloEventoContratacoesFaltas->getFaltas(null, $dadosContratados["id"]);
											?>
											<tr>
												<td><a href="<?php echo HOME_URI; ?>/usuarios/index/perfil/<?php echo $dadosContratados["idUsuario"]; ?>"><?php echo $dadosContratados["nome"]; ?> <?php echo $dadosContratados["sobrenome"]; ?></a></td>
												<td><?php echo $dadosContratados["funcao"]; ?></td>
												<td><span class="label" style="background-color: <?php echo $dadosContratados["cor"]; ?>; color: #fff !important;"><?php echo $dadosContratados["fase"]; ?></span></td>
												<td><?php echo $quantidadeFaltas = count($faltas); ?></td>
											</tr>
											<?php endforeach; ?>

										</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="box box-danger">
                            <div class="box-header with-border">
								<i class="fa fa-list"></i>
                                <h3 class="box-title">Lista de Faltas</h3>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
								<?php
								$contratados = $modeloEventoContratacoesFaltas->getFaltas(chk_array($parametros, 1));
								?>
                                <div class="table-responsive">
                                    <table class="table no-margin">
                                        <thead>
                                            <tr>
                                                <th>Contratado</th>
                                                <th>Função</th>
												<th>Fase</th>
												<th>Dia</th>
												<th>Opções</th>
                                            </tr>
                                        </thead>
										
                                        <tbody>
										
											<?php 
											foreach($contratados AS $dadosContratados): 
											?>
											<tr>
												<td><a href="<?php echo HOME_URI; ?>/usuarios/index/perfil/<?php echo $dadosContratados["idUsuario"]; ?>"><?php echo $dadosContratados["nome"]; ?> <?php echo $dadosContratados["sobrenome"]; ?></a></td>
												<td><?php echo $dadosContratados["funcao"]; ?></td>
												<td><span class="label" style="background-color: <?php echo $dadosContratados["cor"]; ?>; color: #fff !important;"><?php echo $dadosContratados["fase"]; ?></span></td>
												<td><?php echo implode("/", array_reverse(explode("-", $dadosContratados["falta"]))); ?></td>
												<td><a href="<?php echo HOME_URI; ?>/eventos/index/faltas/<?php echo $dadosContratados['idEvento']; ?>/remover/<?php echo $dadosContratados['id']; ?>" class="icon-tab" title="Remover"><i class="fa fa-minus-circle fa-lg"></i></a></td>
											</tr>
											<?php endforeach; ?>
											
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
					</div>
                </div>
            </section>
        </div>