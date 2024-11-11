<?php 
if(!defined('ABSPATH')) exit; 

if(chk_array($this->parametros, 0) == 'adicionar' || (chk_array($this->parametros, 0) == 'editar' && (chk_array($this->parametros, 2) != 'lideres' && chk_array($this->parametros, 2) != 'fases'))){
	$modelo->validarFormEventos();
	
	$categorias = $modeloCategorias->getCategorias(); 
	$clientes = $modeloClientes->getClientes(); 
	$locais = $modeloLocais->getLocais(); 
}

if(chk_array($this->parametros, 0) == 'editar'){
	$modelo->getEvento(chk_array($parametros, 1));
	
	if(chk_array($this->parametros, 2) == 'lideres'){
		$modeloEventoLideres->validarFormLideres();
		$modeloEventoLideres->getLider(chk_array($parametros, 4));
		$modeloEventoLideres->removerLider($parametros);		
	}elseif(chk_array($this->parametros, 2) == 'fases'){
		if(!empty($_POST['notificar']) && $_POST['notificar'] == 'true'){
			$modeloEventoLideres->notificaLideres(chk_array($parametros, 1));
		}
		
		if(empty($_POST['idEventoFase']) && empty($_POST['notificar'])){
			$modeloEventoFases->validarFormFases();
		}
		
		$modeloEventoFases->getFase(chk_array($parametros, 4));
		$modeloEventoFases->removerFase($parametros);	
		
		if(!empty($_POST['idEventoFase'])){
			$modeloEventoCargos->validarFormCargos();
		}
		
		$modeloEventoCargos->getCargo(chk_array($parametros, 6));
		$modeloEventoCargos->removerCargo($parametros);	
	}
}

$dataHoraCriacao = explode(" ", chk_array($modelo->form_data, 'dataCriacao'));
$dataCriacao = explode("-", $dataHoraCriacao[0]);

if(chk_array($this->parametros, 0) == 'adicionar'){
	if(empty(chk_array($modelo->form_data, 'midia'))){
		$diretorioTemporario = md5(uniqid(time()));
		$modelo->form_data['midia'] = "temp/eventos/" . $diretorioTemporario;
	}
}else{
	$modelo->form_data['midia'] = "midia/eventos/" . $dataCriacao[0] . "/" . $dataCriacao[1] . "/" . $dataCriacao[2] . "/" . chk_array($parametros, 1);
}

$totalArquivosMidia = @scandir($modelo->form_data['midia']);
$totalArquivosMidia = count($totalArquivosMidia) - 3;
?>
        <div class="content-wrapper">
            <section class="content-header">
                <h1><?php if(chk_array($this->parametros, 0) == 'editar'){ echo'Editar'; }else{ echo'Adicionar'; } ?> Evento<small>&nbsp;</small></h1>
                
				<ol class="breadcrumb">
                    <li><a href="<?php echo HOME_URI; ?>"><i class="fa fa-dashboard"></i> Painel</a></li>
                    <li><a href="<?php echo HOME_URI; ?>/eventos">Eventos</a></li>
					<?php if(chk_array($this->parametros, 0) == 'editar'){  ?>
                    <li class="active"><a href="<?php echo HOME_URI; ?>/eventos/index/editar/<?php echo chk_array($parametros, 1); ?>">Editar</a></li>
					<?php 
					}
					if(chk_array($this->parametros, 0) == 'adicionar'){  
					?>
					<li class="active"><a href="<?php echo HOME_URI; ?>/eventos/index/adicionar">Adicionar</a></li>
					<?php } ?>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-lg-4 col-xs-6">
                        <div class="small-box <?php if(chk_array($this->parametros, 0) == 'adicionar'){ echo"bg-red"; }elseif(chk_array($this->parametros, 0) == 'editar' && (chk_array($this->parametros, 2) != 'lideres' && chk_array($this->parametros, 2) != 'fases')){ echo"bg-red"; }else{ echo"bg-gray"; } ?>">
                            <div class="inner">
                                <h3>01</h3>

                                <p>Evento</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-calendar-plus-o"></i>
                            </div>
							<?php if(chk_array($this->parametros, 0) == 'editar'){ ?>
                            <a href="<?php echo HOME_URI; ?>/eventos/index/editar/<?php echo chk_array($parametros, 1); ?>" class="small-box-footer">Mostrar <i class="fa fa-arrow-circle-right"></i></a>
							<?php } ?>
                        </div>
                    </div>

                    <div class="col-lg-4 col-xs-6">
                        <div class="small-box <?php if(chk_array($this->parametros, 0) == 'adicionar'){ echo"bg-gray"; }elseif(chk_array($this->parametros, 0) == 'editar' && chk_array($this->parametros, 2) == 'lideres'){ echo"bg-red"; }else{ echo"bg-gray"; } ?>">
                            <div class="inner">
                                <h3>02</h3>

                                <p>Líderes</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-user-circle-o"></i>
                            </div>
							<?php if(chk_array($this->parametros, 0) == 'editar'){ ?>
                            <a href="<?php echo HOME_URI; ?>/eventos/index/editar/<?php echo chk_array($parametros, 1); ?>/lideres" class="small-box-footer">Mostrar <i class="fa fa-arrow-circle-right"></i></a>
							<?php } ?>
                        </div>
                    </div>
					
                    <div class="col-lg-4 col-xs-6">
                        <div class="small-box <?php if(chk_array($this->parametros, 0) == 'adicionar'){ echo"bg-gray"; }elseif(chk_array($this->parametros, 0) == 'editar' && chk_array($this->parametros, 2) == 'fases'){ echo"bg-red"; }else{ echo"bg-gray"; } ?>">
                            <div class="inner">
                                <h3>03</h3>

                                <p>Fases e Mão de Obra</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-users"></i>
                            </div>
							<?php if(chk_array($this->parametros, 0) == 'editar'){ ?>
                            <a href="<?php echo HOME_URI; ?>/eventos/index/editar/<?php echo chk_array($parametros, 1); ?>/fases" class="small-box-footer">Mostrar <i class="fa fa-arrow-circle-right"></i></a>
							<?php } ?>
                        </div>
                    </div>
                </div>
				
				<?php
				if(chk_array($this->parametros, 0) == 'adicionar'){ 
					require ABSPATH . '/views/eventos/frmEvento.inc.view.php';
				}elseif(chk_array($this->parametros, 0) == 'editar'){
					if(chk_array($this->parametros, 2) == 'lideres'){
						require ABSPATH . '/views/eventos/frmLideres.inc.view.php';
					}elseif(chk_array($this->parametros, 2) == 'fases'){
						require ABSPATH . '/views/eventos/frmFases.inc.view.php';
					}else{
						require ABSPATH . '/views/eventos/frmEvento.inc.view.php';
					}
				}
				?>
            </section>
        </div>