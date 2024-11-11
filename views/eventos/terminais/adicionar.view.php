<?php
if (!defined('ABSPATH')) exit;

$hash = $_SESSION['idEventoHash'];
$idEvento = decryptHash($hash);

// Se for post, adiciona o terminal
if (isset($_POST['adicionarTerminal'])) {
    $modeloTerminais->validarFormTerminais();
}

// Carrega os setores disponíveis para o evento
$setores = $modeloSetores->getSetores($idEvento); 
$tiposTerminal = $modeloTerminais->getTiposTerminal();
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Adicionar Terminal</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>/eventos/index/terminais">Terminais</a></li>
                        <li class="breadcrumb-item active">Adicionar Terminal</li>
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
                <h3 class="card-title">Adicionar Terminal</h3>
            </div>
            <form role="form" action="" method="POST">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nomeTerminal">Nome do Terminal</label>
                                <input type="text" class="form-control" name="nomeTerminal" id="nomeTerminal" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numeroTerminal">Número do Terminal</label>
                                <input type="text" class="form-control" name="numeroTerminal" id="numeroTerminal" required>
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
                                        <option value="<?php echo $setor['id']; ?>">
                                            <?php echo $setor['nomeSetor']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                         <!-- Campo de seleção do tipo de terminal (pré-seleciona o valor do banco de dados) -->
                         <div class="col-md-3">
                           <!-- Campo de seleção do tipo de terminal -->
                            <div class="form-group">
                                <label for="tipo">Tipo de Terminal</label>
                                <select class="form-control" name="tipo" id="tipo" required>
                                    <option value="">Selecione o tipo</option>
                                    <?php foreach ($tiposTerminal as $tipo) : ?>
                                        <option value="<?php echo $tipo; ?>"><?php echo ucfirst($tipo); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ip">IP do Terminal</label>
                                <input type="text" class="form-control" name="ip" id="ip" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="usuario">Usuário do Terminal</label>
                                <input type="text" class="form-control" name="usuario" id="usuario" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="senha">Senha do Terminal</label>
                                <input type="password" class="form-control" name="senha" id="senha" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" name="adicionarTerminal" class="btn btn-primary">Adicionar Terminal</button>
                    <a href="<?php echo HOME_URI; ?>/eventos/index/terminais" class="btn btn-default">Cancelar</a>
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
