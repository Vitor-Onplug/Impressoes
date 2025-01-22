<?php
if (!defined('ABSPATH')) exit;

// Verifica se o ID da empresa foi passado.
if (chk_array($this->parametros, 1)) {
    $hash = chk_array($this->parametros, 1);
    $idEmpresa = decryptHash($hash);
} else {
    echo '<div class="alert alert-danger">ID da empresa não fornecido.</div>';
    exit;
}

// Busca os funcionários relacionados à empresa.
$funcionarios = $modelo->getFuncionariosEmpresa($idEmpresa);

if (chk_array($parametros, 3) == 'editar' || chk_array($parametros, 3) == 'remover') {
    if (empty(chk_array($parametros, 4)) || !is_numeric(chk_array($parametros, 4))) {
        echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/empresas/index/editar/' . chk_array($this->parametros, 1) . '/funcionarios">';
        echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/empresas/index/editar/' . chk_array($this->parametros, 1) . '/funcionarios";</script>';
        exit;
    }

    $modelo->getFuncionario(chk_array($parametros, 4));

    if (preg_match('/(inexistente)/simx', $modeloFuncionarios->form_msg)) {
        echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/empresas">';
        echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/empresas";</script>';
        exit;
    }
}


?>

<?php require_once ABSPATH . '/views/empresas/mini-perfil.inc.view.php'; ?>

<div class="container-fluid">
    <div class="row">
        <!-- Cadastro de Funcionário -->
        <!-- <div class="col-md-6">
            <section class="content">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Cadastro de Funcionário</h3>
                    </div>
                    <form role="form" action="" method="POST" enctype="multipart/form-data">
                        <div class="card-body">
                            <?php 
                            echo $modelo->form_msg;
                            ?>

                            <div class="form-group">
                                <label for="nome">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome do funcionário" value="<?php echo htmlentities(chk_array($modelo->form_data, 'nome')); ?>" required maxlength="255">
                            </div>

                            <div class="form-group">
                                <label for="genero">Gênero</label>
                                <select class="form-control" id="genero" name="genero">
                                    <option value="Masculino" <?php echo chk_array($modelo->form_data, 'genero') === 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                                    <option value="Feminino" <?php echo chk_array($modelo->form_data, 'genero') === 'Feminino' ? 'selected' : ''; ?>>Feminino</option>
                                    <option value="Outro" <?php echo chk_array($modelo->form_data, 'genero') === 'Outro' ? 'selected' : ''; ?>>Outro</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="dataNascimento">Data de Nascimento</label>
                                <input type="date" class="form-control" id="dataNascimento" name="dataNascimento" value="<?php echo htmlentities(chk_array($modelo->form_data, 'dataNascimento')); ?>" required>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>
            </section>
        </div> -->

        <!-- Lista de Funcionários -->
        <div class="col-md-12">
            <section class="content">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Funcionários</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($funcionarios)): ?>
                            <div class="alert alert-info">Nenhum funcionário encontrado.</div>
                        <?php else: ?>
                            <table class="table table-hover table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Sobrenome</th>
                                        <th>Gênero</th>
                                        <th>Data de Nascimento</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($funcionarios as $funcionario): ?>
                                        <tr>
                                            <td><?php echo htmlentities($funcionario['nome']); ?></td>
                                            <td><?php echo htmlentities($funcionario['sobrenome']); ?></td>
                                            <td><?php echo htmlentities($funcionario['genero']); ?></td>
                                            <td><?php echo htmlentities($funcionario['dataNascimento']); ?></td>
                                            <td>
                                                <a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo encryptId($funcionario['id']); ?>" class="icon-tab" title="Editar"><i class="far fa-edit fa-lg"></i></a>
                                                &nbsp;
                                                <!-- <a href="<?php echo HOME_URI; ?>/pessoas/index/remover/<?php echo encryptId($funcionario['id']); ?>" class="icon-tab" title="Remover"><i class="fas fa-times fa-lg text-red"></i></a> -->
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>