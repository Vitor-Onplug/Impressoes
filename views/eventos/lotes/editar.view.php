<?php
if (!defined('ABSPATH')) exit;

// Recupera o hash do lote (edição)
$hash = chk_array($parametros, 1);
$idLote = decryptHash($hash);
$hashEvento = $_SESSION['idEventoHash'];
$idEvento = decryptHash($hashEvento);

// Se for post, atualiza o lote
if (isset($_POST['editarLote'])) {
    $modeloLotes->validarFormLotes();
}

// Carrega os tipos de credenciais e códigos, e os dados do lote
$tiposCredenciais = $modeloLotes->getTiposCredencial();
$tiposCodigos = $modeloLotes->getTiposCodigo();
$lote = $modeloLotes->getLote($idLote);

?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Editar Lote de Credenciais</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Editar Lote</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <?php echo $modeloLotes->form_msg; ?>

    <section class="content">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Editar Lote de Credenciais</h3>
            </div>
            <form role="form" action="" method="POST">
                <div class="card-body">
                    <div class="row">
                        <!-- Nome do Lote -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nomeLote">Nome do Lote</label>
                                <input type="text" class="form-control" name="nomeLote" id="nomeLote" value="<?php echo htmlentities($lote['nomeLote']); ?>" required>
                            </div>
                        </div>
                        
                        <!-- Tipo de Código e Tipo de Credencial -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tipoCodigo">Tipo de Código</label>
                                <select class="form-control" name="tipoCodigo" id="tipoCodigo" required>
                                    <option value="">Selecione um tipo de código</option>
                                    <?php foreach ($tiposCodigos as $tipoCodigo) : ?>
                                        <option value="<?php echo $tipoCodigo['id']; ?>" <?php echo $lote['idTipoCodigo'] == $tipoCodigo['id'] ? 'selected' : ''; ?>>
                                            <?php echo $tipoCodigo['tipoCodigo']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tipoCredencial">Tipo de Credencial</label>
                                <select class="form-control" name="tipoCredencial" id="tipoCredencial" required>
                                    <option value="">Selecione um tipo de credencial</option>
                                    <?php foreach ($tiposCredenciais as $tipoCredencial) : ?>
                                        <option value="<?php echo $tipoCredencial['id']; ?>" <?php echo $lote['idTipoCredencial'] == $tipoCredencial['id'] ? 'selected' : ''; ?>>
                                            <?php echo $tipoCredencial['tipoCredencial']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="idEvento" value="<?php echo $idEvento; ?>">

                    <div class="row">
                        <!-- Checkboxes -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permiteAcessoFacial" id="permiteAcessoFacial" value="1" <?php echo $lote['permiteAcessoFacial'] == 1 ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="permiteAcessoFacial">Permite Acesso Facial</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permiteImpressao" id="permiteImpressao" value="1" <?php echo $lote['permiteImpressao'] == 1 ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="permiteImpressao">Permite Impressão</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="temAutonumeracao" id="temAutonumeracao" value="1" <?php echo $lote['temAutonumeracao'] == 1 ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="temAutonumeracao">Tem Autonumeração</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="card-footer">
                    <button type="submit" name="editarLote" class="btn btn-primary">Salvar Alterações</button>
                    <a href="<?php echo HOME_URI; ?>/eventos/index/lotes" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </section>
</div>

<!-- Script para inicializar o Select2 -->
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Selecione um setor',
            allowClear: true
        });
    });
</script>
