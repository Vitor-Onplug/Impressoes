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
    $idParceiroUsuario = $modelo->getIdParceiroUsuario($_SESSION['userdata']['idUsuario']);
}

if (isset($_POST['idEmpresas'])) {
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
                        <div class="col-md-6">
                            <!-- Nome do Parceiro -->
                            <div class="form-group">
                                <label for="nome">Nome Parceria</label>
                                <input type="text" class="form-control" id="nomeParceiro" name="nomeParceiro" placeholder="Digite o nome"
                                    value="<?php echo htmlspecialchars(chk_array($parceiro, 'nomeParceiro') ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    required maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Tipo de Parceiro -->
                            <!-- Tipo de Parceiro -->
                            <div class="form-group">
                                <label for="tipo">Tipo</label>
                                <select class="form-control" id="tipo" name="tipo"
                                    <?php echo chk_array($this->parametros, 0) == 'editar' ? 'disabled' : ''; ?>
                                    required>
                                    <option value="">Selecione um tipo</option>
                                    <?php
                                    $tipos = $modelo->getTiposParceiros();
                                    $tipoSelecionado = $parceiro['tipo'] ?? null;

                                    foreach ($tipos as $tipo) {
                                        $selected = $tipo == $tipoSelecionado ? 'selected' : '';
                                        echo "<option value='$tipo' $selected>" . ucfirst(strtolower($tipo)) . "</option>";
                                    }
                                    ?>
                                </select>
                                <?php if (chk_array($this->parametros, 0) == 'editar'): ?>
                                    <!-- Campo oculto para enviar o tipo no modo de edição -->
                                    <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($tipoSelecionado, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php endif; ?>
                            </div>

                        </div>

                        <div class="col-md-12">
                            <!-- Empresas Associadas -->
                            <div class="form-group">
                                <label for="idEmpresas">Empresas</label>
                                <select class="form-control select2" id="idEmpresas" name="idEmpresas[]" multiple required>
                                    <?php
                                    $empresas = $modeloEmpresa->getEmpresas();
                                    $empresasSelecionadas = $modelo->getEmpresasDoParceiro($parceiro['id'] ?? null);


                                    foreach ($empresas as $empresa) {
                                        $selected = in_array($empresa['id'], $empresasSelecionadas) ? 'selected' : '';
                                        echo "<option value='{$empresa['id']}' $selected>{$empresa['razaoSocial']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Campo oculto para o ID do parceiro -->
                            <input type="hidden" name="idParceiro"
                                value="<?php
                                        if (chk_array($this->parametros, 0) === 'editar') {
                                            // No modo de edição, use o ID do parceiro salvo
                                            echo htmlspecialchars($parceiro['idParceiro'] ?? '', ENT_QUOTES, 'UTF-8');
                                        } else {
                                            // No modo de criação, use o ID do parceiro associado ao usuário
                                            echo htmlspecialchars($idParceiroUsuario['idParceiro'] ?? '', ENT_QUOTES, 'UTF-8');
                                        }
                                        ?>">


                            <!-- Token -->
                            <div class="form-group">
                                <label for="token">Token</label>
                                <input type="text" class="form-control" id="token" name="token" placeholder="Digite o token"
                                    value="<?php echo htmlspecialchars(chk_array($parceiro, 'token') ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    required maxlength="255">
                            </div>

                            <!-- Observações -->
                            <div class="form-group">
                                <label for="observacoes">Observações</label>
                                <textarea class="form-control" rows="3" placeholder="Adicione observações" name="observacoes" id="observacoes"><?php echo htmlspecialchars(chk_array($parceiro, 'observacoes') ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
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