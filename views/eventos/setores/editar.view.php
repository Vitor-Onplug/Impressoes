<?php
if (!defined('ABSPATH')) exit;

$hash = chk_array($parametros, 1);
$idSetor = decryptHash($hash);
$hashEvento = $_SESSION['idEventoHash'];
$idEvento = decryptHash($hashEvento);

$setor = $modeloSetores->getSetor($idSetor);

if (isset($_POST['editar_setor'])) {
    $modeloSetores->validarFormSetores();
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
                        <li class="breadcrumb-item active"><a href="<?php echo HOME_URI; ?>/eventos/index/setores">Setores</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <?php
    echo $modeloSetores->form_msg;
    ?>

    <section class="content">

        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Editar Setor: <?php echo htmlentities(chk_array($modeloSetores->form_data, 'nomeSetor')); ?></h3>
            </div>

            <form role="form" action="" method="POST">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="nomeSetor">Nome do Setor</label>
                                <input type="text" class="form-control" name="nomeSetor" id="nomeSetor" value="<?php echo htmlentities(chk_array($modeloSetores->form_data, 'nomeSetor')); ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="idEvento" value="<?php echo $idEvento; ?>">

                <div class="card-footer">
                    <button type="submit" name="editar_setor" class="btn btn-primary">Salvar Alterações</button>
                    <a href="<?php echo HOME_URI; ?>/eventos/index/setores/<?php echo encryptId($idEvento); ?>" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>


    </section>
</div>