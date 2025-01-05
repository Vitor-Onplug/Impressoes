<?php if (!defined('ABSPATH')) exit;

if (chk_array($this->parametros, 0) == 'editar') {
    if (chk_array($this->parametros, 1)) {
        $hash = chk_array($this->parametros, 1);
        $id = decryptHash($hash);
    }

    $parceiro = $modelo->getParceiro($id);

    if (!empty($modelo->form_msg) && preg_match('/(inexistente|encontrado)/simx', $modelo->form_msg)) {
        echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/parceiros">';
        echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/parceiros";</script>';
        exit;
    }
} else {
    $parceiro = null;
}

if (isset($_POST['idEmpresa'])) {
    $modelo->validarParceiro();
}

?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?php echo chk_array($this->parametros, 0) == 'editar' ? 'Editar Parceiro' : 'Adicionar Parceiro'; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>/parceiros">Parceiros</a></li>
                        <li class="breadcrumb-item active"><?php echo chk_array($this->parametros, 0) == 'editar' ? 'Editar Parceiro' : 'Adicionar Parceiro'; ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title"><?php echo chk_array($this->parametros, 0) == 'editar' ? 'Editar Parceiro' : 'Adicionar Parceiro'; ?></h3>
            </div>
            <form role="form" action="" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                    <?php echo $modelo->form_msg; ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="idEmpresa">Empresa</label>
                                <select class="form-control" id="idEmpresa" name="idEmpresa" required>
                                    <option value="">Selecione uma empresa</option>
                                    <?php
                                    $empresasComParceiros = $modeloEmpresa->getEmpresasParceiros();

                                    foreach ($empresasComParceiros as $empresa) {
                                        if (chk_array($this->parametros, 0) == 'editar') {
                                            // Mostrar a empresa correspondente ao parceiro em edição
                                            $selected = $empresa['id'] == $parceiro['idEmpresa'] ? 'selected' : '';
                                            echo "<option value='" . $empresa['id'] . "' $selected>" . $empresa['razaoSocial'] . "</option>";
                                        } else {
                                            // Ocultar empresas que já possuem parceiros
                                            if (!isset($empresa['idParceiro']) || $empresa['idParceiro'] == null) {
                                                echo "<option value='" . $empresa['id'] . "'>" . $empresa['razaoSocial'] . "</option>";
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="token">Token</label>
                                <input type="text" class="form-control" id="token" name="token" placeholder=""
                                       value="<?php echo htmlentities(chk_array($parceiro, 'token') ?? ''); ?>"
                                       required maxlength="255">
                            </div>

                            <div class="form-group">
                                <label for="observacoes">Observações</label>
                                <textarea class="form-control" rows="3" placeholder="" name="observacoes" id="observacoes"><?php echo htmlentities($parceiro['observacoes'] ?? ''); ?></textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success"><?php echo chk_array($this->parametros, 0) == 'editar' ? 'Salvar Alterações' : 'Adicionar Parceiro'; ?></button>
                    <a href="<?php echo HOME_URI; ?>/parceiros" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </section>
</div>
