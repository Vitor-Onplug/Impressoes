<?php
if (!defined('ABSPATH')) exit;

$modeloDepartamentos->validarFormDepartamento();

if (chk_array($this->parametros, 1) == 'editar') {
    if (chk_array($this->parametros, 2)) {
        $hash = chk_array($this->parametros, 2);
        $id = decryptHash($hash);
    }
}

$departamento = "";
if ($id) {
    $departamento = $modeloDepartamentos->getDepartamento($id);
}

$idEmpresa = $_SESSION['userdata']['idEmpresa'];

$departamentos = $modeloDepartamentos->getDepartamentos($idEmpresa);

?>
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <section class="content">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Departamento</h3>
                        </div>
                        <form role="form" action="" method="POST">
                            <div class="card-body">
                                <?php echo $modeloDepartamentos->form_msg; ?>
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome do departamento" value="<?php echo htmlentities(chk_array($departamento, 'nome')); ?>" required maxlength="255">
                                </div>

                                <input type="hidden" name="idEmpresa" value="<?php echo $idEmpresa; ?>">
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Salvar</button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>

            <div class="col-md-6">
                <section class="content">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Lista de Departamentos</h3>
                        </div>
                        <div class="card-body">
                            <?php if (count($departamentos) < 1) { ?>
                                <div class="alert alert-info">Nenhum departamento encontrado.</div>
                            <?php } else { ?>
                                <table class="table table-hover table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th class="sorter-false">Opções</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($departamentos as $dep): ?>
                                            <tr>
                                                <td><?php echo $dep['nome']; ?></td>
                                                <td>
                                                    <a href="<?php echo HOME_URI; ?>/empresas/index/departamentos/editar/<?php echo encryptId($dep['id']); ?>" class="icon-tab" title="Editar"><i class="far fa-edit fa-lg"></i></a>
                                                    &nbsp;
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php } ?>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>