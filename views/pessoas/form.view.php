<?php 
if(!defined('ABSPATH')) exit;

if(chk_array($this->parametros, 0) == 'editar'){
	if(empty(chk_array($parametros, 1))){
		$parametros[1] = chk_array($this->userdata, 'id');
	}else{
		$parametros[1] = chk_array($parametros, 1);
	}
	
	$modelo->getPessoa(chk_array($parametros, 1));
	
	if(preg_match('/(inexistente|encontrado)/simx', $modelo->form_msg)){
		echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/pessoas">';
		echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/pessoas";</script>';
		
		exit;
	}
	
	if(!empty(chk_array($parametros, 1)) && !is_numeric(chk_array($parametros, 1))){
		echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/pessoas">';
		echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/pessoas";</script>';
		
		exit;
	}
}

?>
		<div class="content-wrapper">
			<section class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1>Editar Pessoa</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
								<li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>/pessoas">Pessoas</a></li>
								<?php if(chk_array($this->parametros, 0) == 'editar'){ ?>
								<li class="breadcrumb-item active"><a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>">Editar</a></li>
								<?php 
								}
								if(chk_array($this->parametros, 0) == 'adicionar'){  
								?>
								<li class="breadcrumb-item active"><a href="<?php echo HOME_URI; ?>/pessoas/index/adicionar">Adicionar</a></li>
								<?php } ?>
							</ol>
						</div>
					</div>
				</div>
			</section>
			
			<section class="content">
				<div class="row">
                    <div class="col-lg-2 col-xs-2">
                        <div class="small-box <?php if(chk_array($this->parametros, 0) == 'adicionar'){ echo"bg-red"; }elseif(chk_array($this->parametros, 0) == 'editar' && (chk_array($this->parametros, 2) != 'documentos' && chk_array($this->parametros, 2) != 'emails' && chk_array($this->parametros, 2) != 'enderecos' && chk_array($this->parametros, 2) != 'telefones' && chk_array($this->parametros, 2) != 'arquivos')){ echo"bg-red"; }else{ echo"bg-gray"; } ?>">
                            <div class="inner">
                                <h3>01</h3>

                                <p>Pessoa</p>
                            </div>
                            <div class="icon">
                                <i class="far fa-user-circle"></i>
                            </div>
							<?php if(chk_array($this->parametros, 0) == 'editar'){ ?>
                            <a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>" class="small-box-footer">Mostrar <i class="fas fa-arrow-circle-right"></i></a>
							<?php } ?>
                        </div>
                    </div>

					<div class="col-lg-2 col-xs-2">
                        <div class="small-box <?php if(chk_array($this->parametros, 0) == 'adicionar'){ echo"bg-gray"; }elseif(chk_array($this->parametros, 0) == 'editar' && chk_array($this->parametros, 2) == 'documentos'){ echo"bg-red"; }else{ echo"bg-gray"; } ?>">
                            <div class="inner">
                                <h3>02</h3>

                                <p>Documentos</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-id-card"></i>
                            </div>
							<?php if(chk_array($this->parametros, 0) == 'editar'){ ?>
                            <a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>/documentos" class="small-box-footer">Mostrar <i class="fas fa-arrow-circle-right"></i></a>
							<?php } ?>
                        </div>
                    </div>

                    <div class="col-lg-2 col-xs-2">
                        <div class="small-box <?php if(chk_array($this->parametros, 0) == 'adicionar'){ echo"bg-gray"; }elseif(chk_array($this->parametros, 0) == 'editar' && chk_array($this->parametros, 2) == 'emails'){ echo"bg-red"; }else{ echo"bg-gray"; } ?>">
                            <div class="inner">
                                <h3>03</h3>

                                <p>E-mails</p>
                            </div>
                            <div class="icon">
                                <i class="far fa-envelope"></i>
                            </div>
							<?php if(chk_array($this->parametros, 0) == 'editar'){ ?>
                            <a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>/emails" class="small-box-footer">Mostrar <i class="fas fa-arrow-circle-right"></i></a>
							<?php } ?>
                        </div>
                    </div>
					
					<div class="col-lg-2 col-xs-2">
                        <div class="small-box <?php if(chk_array($this->parametros, 0) == 'adicionar'){ echo"bg-gray"; }elseif(chk_array($this->parametros, 0) == 'editar' && chk_array($this->parametros, 2) == 'enderecos'){ echo"bg-red"; }else{ echo"bg-gray"; } ?>">
                            <div class="inner">
                                <h3>04</h3>

                                <p>Endereços</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-home"></i>
                            </div>
							<?php if(chk_array($this->parametros, 0) == 'editar'){ ?>
                            <a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>/enderecos" class="small-box-footer">Mostrar <i class="fas fa-arrow-circle-right"></i></a>
							<?php } ?>
                        </div>
                    </div>
					
					<div class="col-lg-2 col-xs-2">
                        <div class="small-box <?php if(chk_array($this->parametros, 0) == 'adicionar'){ echo"bg-gray"; }elseif(chk_array($this->parametros, 0) == 'editar' && chk_array($this->parametros, 2) == 'telefones'){ echo"bg-red"; }else{ echo"bg-gray"; } ?>">
                            <div class="inner">
                                <h3>05</h3>

                                <p>Telefones</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-phone"></i>
                            </div>
							<?php if(chk_array($this->parametros, 0) == 'editar'){ ?>
                            <a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>/telefones" class="small-box-footer">Mostrar <i class="fas fa-arrow-circle-right"></i></a>
							<?php } ?>
                        </div>
                    </div>
					
					<div class="col-lg-2 col-xs-2">
                        <div class="small-box <?php if(chk_array($this->parametros, 0) == 'adicionar'){ echo"bg-gray"; }elseif(chk_array($this->parametros, 0) == 'editar' && chk_array($this->parametros, 2) == 'arquivos'){ echo"bg-red"; }else{ echo"bg-gray"; } ?>">
                            <div class="inner">
                                <h3>06</h3>

                                <p>Arquivos</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-archive"></i>
                            </div>
							<?php if(chk_array($this->parametros, 0) == 'editar'){ ?>
                            <a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>/arquivos" class="small-box-footer">Mostrar <i class="fas fa-arrow-circle-right"></i></a>
							<?php } ?>
                        </div>
                    </div>
                </div>
            </section>
			
			<?php
			
			if(chk_array($this->parametros, 0) == 'adicionar'){ 
				require ABSPATH . '/views/pessoas/frmPessoa.inc.view.php';
			}elseif(chk_array($this->parametros, 0) == 'editar'){
				if(chk_array($this->parametros, 2) == 'documentos'){
					require ABSPATH . '/views/pessoas/frmDocumentos.inc.view.php';
				}elseif(chk_array($this->parametros, 2) == 'enderecos'){
					require ABSPATH . '/views/pessoas/frmEnderecos.inc.view.php';
				}elseif(chk_array($this->parametros, 2) == 'emails'){
					require ABSPATH . '/views/pessoas/frmEmails.inc.view.php';
				}elseif(chk_array($this->parametros, 2) == 'telefones'){
					require ABSPATH . '/views/pessoas/frmTelefones.inc.view.php';
				}elseif(chk_array($this->parametros, 2) == 'arquivos'){
					require ABSPATH . '/views/pessoas/uploadArquivos.view.php';
				}
				else{
					require ABSPATH . '/views/pessoas/frmPessoa.inc.view.php';
				}
			}
			?>
		</div>