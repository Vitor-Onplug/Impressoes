<?php
if (!defined('ABSPATH')) exit;

// Recupera o hash do terminal e o de evento
$hash = chk_array($parametros, 1);
$idTerminal = decryptHash($hash);
$hashEvento = $_SESSION['idEventoHash'];
$idEvento = decryptHash($hashEvento);

// Carrega os dados do terminal e setores
$terminal = $modeloTerminais->getTerminal($idTerminal);
$setores = $modeloSetores->getSetores($idEvento);
$tiposTerminal = $modeloTerminais->getTiposTerminal();

// Se for post, valida o formulário
if (isset($_POST['editar_terminal'])) {
    $modeloTerminais->validarFormTerminais();
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">

                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>/eventos/index/terminais">Terminais</a></li>
                        <li class="breadcrumb-item active">Editar Terminal</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <?php
    echo $modeloTerminais->form_msg;
    ?>

    <section class="content">

        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Editar Terminal: <?php echo htmlentities(chk_array($modeloTerminais->form_data, 'nomeTerminal')); ?></h3>
            </div>
            <form role="form" action="" method="POST">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nomeTerminal">Nome do Terminal</label>
                                <input type="text" class="form-control" name="nomeTerminal" id="nomeTerminal" value="<?php echo htmlentities(chk_array($modeloTerminais->form_data, 'nomeTerminal')); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numeroTerminal">Número do Terminal</label>
                                <input type="text" class="form-control" name="numeroTerminal" id="numeroTerminal" value="<?php echo htmlentities(chk_array($modeloTerminais->form_data, 'numeroTerminal')); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="idSetor">Setor</label>
                                <select class="form-control select2" name="idSetor" id="idSetor" required>
                                    <option value="">Selecione um setor</option>
                                    <?php foreach ($setores as $setor): ?>
                                        <option value="<?php echo $setor['id']; ?>"
                                            <?php echo (chk_array($modeloTerminais->form_data, 'idSetor') == $setor['id']) ? 'selected' : ''; ?>>
                                            <?php echo $setor['nomeSetor']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!-- Campo de seleção do tipo de terminal (pré-seleciona o valor do banco de dados) -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tipo">Tipo de Terminal</label>
                                <select class="form-control" name="tipo" id="tipo" required>
                                    <option value="">Selecione o tipo</option>
                                    <?php foreach ($tiposTerminal as $tipo) : ?>
                                        <option value="<?php echo $tipo; ?>" <?php echo chk_array($modeloTerminais->form_data, 'tipo') == $tipo ? 'selected' : ''; ?>>
                                            <?php echo ucfirst($tipo); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ip">IP do Terminal</label>
                                <input type="text" class="form-control" name="ip" id="ip" value="<?php echo htmlentities(chk_array($modeloTerminais->form_data, 'ip')); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="usuario">Usuário do Terminal</label>
                                <input type="text" class="form-control" name="usuario" id="usuario" value="<?php echo htmlentities(chk_array($modeloTerminais->form_data, 'usuario')); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="senha">Senha do Terminal</label>
                                <input type="password" class="form-control" name="senha" id="senha" value="<?php echo htmlentities(chk_array($modeloTerminais->form_data, 'senha')); ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="idEvento" value="<?php echo $idEvento; ?>">

                <div class="card-footer">
                    <button type="submit" name="editar_terminal" class="btn btn-primary">Salvar Alterações</button>
                    <a href="<?php echo HOME_URI; ?>/eventos/index/terminais/<?php echo encryptId($idEvento); ?>" class="btn btn-default">Cancelar</a>
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