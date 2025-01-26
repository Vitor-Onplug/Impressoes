<?php
if (!defined('ABSPATH')) exit;

$modeloMarcas->validarFormMarca();

if (chk_array($this->parametros, 1) == 'editar') {
    if (chk_array($this->parametros, 2)) {
        $hash = chk_array($this->parametros, 2);
        $id = decryptHash($hash);
    }
}
var_dump($id);
$marca = "";
if ($id) {
    $marca = $modeloMarcas->getMarca($id);
}

$idEmpresa = $_SESSION['userdata']['idEmpresa'];

$marcas = $modeloMarcas->getMarcas($idEmpresa);

?>
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <section class="content">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Marca</h3>
                        </div>
                        <form role="form" action="" method="POST">
                            <div class="card-body">
                                <?php echo $modeloMarcas->form_msg; ?>
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome da marca" value="<?php echo htmlentities(chk_array($marca, 'nome')); ?>" required maxlength="255">
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
                            <h3 class="card-title">Lista de Marcas</h3>
                        </div>
                        <div class="card-body">
                            <?php if (count($marcas) < 1) { ?>
                                <div class="alert alert-info">Nenhuma marca encontrada.</div>
                            <?php } else { ?>
                                <table class="table table-hover table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th class="sorter-false">Opções</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($marcas as $marca): ?>
                                            <tr>
                                                <td><?php echo $marca['nome']; ?></td>
                                                <td>
                                                    <a href="<?php echo HOME_URI; ?>/impressoras/index/marcas/editar/<?php echo encryptId($marca['id']); ?>" class="icon-tab" title="Editar"><i class="far fa-edit fa-lg"></i></a>
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