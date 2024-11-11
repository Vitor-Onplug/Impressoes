<?php
if (!defined('ABSPATH')) exit;

// Recupera o hash do leitor e do evento
$hash = chk_array($parametros, 1);
$idLeitor = decryptHash($hash);
$hashEvento = $_SESSION['idEventoHash'];
$idEvento = decryptHash($hashEvento);

// Carrega os dados do leitor e dos terminais
$leitor = $modeloLeitores->getLeitor($idLeitor);
$terminais = $modeloTerminais->getTerminais($idEvento);

// Se for post, valida o formulário
if (isset($_POST['editar_leitor'])) {
    $modeloLeitores->validarFormLeitores();
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
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>/leitores">Leitores Faciais</a></li>
                        <li class="breadcrumb-item active">Editar Leitor</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <?php
    echo $modeloLeitores->form_msg;
    ?>

    <section class="content">


        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Editar Leitor: <?php echo htmlentities(chk_array($modeloLeitores->form_data, 'nomeLeitor')); ?></h3>
            </div>

            <form role="form" action="" method="POST">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nomeLeitor">Nome do Leitor</label>
                                <input type="text" class="form-control" name="nomeLeitor" id="nomeLeitor" value="<?php echo htmlentities(chk_array($modeloLeitores->form_data, 'nomeLeitor')); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numeroLeitor">Número do Leitor</label>
                                <input type="text" class="form-control" name="numeroLeitor" id="numeroLeitor" value="<?php echo htmlentities(chk_array($modeloLeitores->form_data, 'numeroLeitor')); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="idTerminal">Terminal</label>
                                <select class="form-control select2" name="idTerminal" id="idTerminal" required>
                                    <option value="">Selecione um terminal</option>
                                    <?php foreach ($terminais as $terminal): ?>
                                        <option value="<?php echo $terminal['id']; ?>"
                                            <?php echo (chk_array($modeloLeitores->form_data, 'idTerminal') == $terminal['id']) ? 'selected' : ''; ?>>
                                            <?php echo $terminal['nomeTerminal']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ip">IP do Leitor</label>
                                <input type="text" class="form-control" name="ip" id="ip" value="<?php echo htmlentities(chk_array($modeloLeitores->form_data, 'ip')); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="usuario">Usuário do Leitor</label>
                                <input type="text" class="form-control" name="usuario" id="usuario" value="<?php echo htmlentities(chk_array($modeloLeitores->form_data, 'usuario')); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="senha">Senha do Leitor</label>
                                <input type="password" class="form-control" name="senha" id="senha" value="<?php echo htmlentities(chk_array($modeloLeitores->form_data, 'senha')); ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="idEvento" value="<?php echo $idEvento; ?>">

                <div class="card-footer">
                    <button type="submit" name="editar_leitor" class="btn btn-primary">Salvar Alterações</button>
                    <a href="<?php echo HOME_URI; ?>/eventos/index/leitores/<?php echo encryptId($idEvento); ?>" class="btn btn-default">Cancelar</a>
                </div>
            </form>

        </div>
    </section>
</div>

<!-- Script para inicializar o Select2 -->
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Selecione um terminal',
            allowClear: true
        });
    });
</script>