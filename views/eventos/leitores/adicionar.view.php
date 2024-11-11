<?php
if (!defined('ABSPATH')) exit;

$hash = $_SESSION['idEventoHash'];
$idEvento = decryptHash($hash);

// Se for post, adiciona o leitor
if (isset($_POST['adicionarLeitor'])) {
    $modeloLeitores->validarFormLeitores();
}

// Carrega os terminais disponíveis
$terminais = $modeloTerminais->getTerminais($idEvento);
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Adicionar Leitor Facial</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>/leitores">Leitores Faciais</a></li>
                        <li class="breadcrumb-item active">Adicionar Leitor</li>
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
                <h3 class="card-title">Adicionar Leitor Facial</h3>
            </div>
            <form role="form" action="" method="POST">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nomeLeitor">Nome do Leitor</label>
                                <input type="text" class="form-control" name="nomeLeitor" id="nomeLeitor" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numeroLeitor">Número do Leitor</label>
                                <input type="text" class="form-control" name="numeroLeitor" id="numeroLeitor">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="idTerminal">Terminal </label>
                                <select class="form-control select2" name="idTerminal" id="idTerminal" required>
                                    <option value="">Selecione um terminal</option>
                                    <?php foreach ($terminais as $terminal): ?>
                                        <option value="<?php echo $terminal['id']; ?>">
                                            <?php echo $terminal['nomeTerminal']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ip">IP do Leitor</label>
                                <input type="text" class="form-control" name="ip" id="ip" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="usuario">Usuário do Leitor</label>
                                <input type="text" class="form-control" name="usuario" id="usuario">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="senha">Senha do Leitor</label>
                                <input type="password" class="form-control" name="senha" id="senha">
                            </div>
                        </div>
                    </div>


                </div>

                <div class="card-footer">
                    <button type="submit" name="adicionarLeitor" class="btn btn-primary">Adicionar Leitor</button>
                    <a href="<?php echo HOME_URI; ?>/eventos/index/leitores" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </section>
</div>

<!-- Script para inicializar o Select2 -->
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Selecione uma opção',
            allowClear: true
        });
    });
</script>