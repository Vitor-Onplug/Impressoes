<?php if (!defined('ABSPATH')) exit; 

if(chk_array($this->parametros, 0) == 'editar'){
	
	$parametros[1] = chk_array($parametros, 1);
	
	$modelo->getParceiros(chk_array($parametros, 1));
	
	if(preg_match('/(inexistente|encontrado)/simx', $modelo->form_msg)){
		echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/parceiros">';
		echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/parceiros";</script>';
		
		exit;
	}
	
	if(!empty(chk_array($parametros, 1)) && !is_numeric(chk_array($parametros, 1))){
		echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/parceiros">';
		echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/parceiros";</script>';
		
		exit;
	}
}
if(isset($_POST['idEmpresa'])){
    $modelo->validarParceiro();
}

?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Adicionar Parceiro</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>/parceiros">Parceiros</a></li>
                        <li class="breadcrumb-item active">Adicionar Parceiro</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Selecionar Empresa</h3>
            </div>
            <form role="form" action="" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                             <?php 
							echo $modelo->form_msg;
							?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="idEmpresa">Empresa</label>
                                <select class="form-control" id="idEmpresa" name="idEmpresa" required>
                                    <option value="">Selecione uma empresa</option>
                                    <?php 
                                    // Obter todas as empresas para exibir na lista de seleção
                                    $empresas = $modeloEmpresa->getEmpresas();
                                    foreach ($empresas as $empresa): 
                                        echo"<option value='" . $empresa['id'] . "' ";
                                        if(chk_array($this->parametros, 0) == 'editar')
										if($empresa == $parceiro['idEmpresa']){ echo'selected'; }
										echo">" . $empresa['razaoSocial'] . "</option>";
                                     endforeach; ?>


                                </select>
                            </div>

                            <div class="form-group">
								<label for="observacoes">Observações</label>
								<textarea class="form-control" rows="3" placeholder="" name="observacoes" id="observacoes"><?php echo htmlentities(chk_array($modelo->form_data, 'observacoes')); ?></textarea>
							</div>
                                
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Adicionar Parceiro</button>
                    <a href="<?php echo HOME_URI; ?>/parceiros" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </section>
</div>
