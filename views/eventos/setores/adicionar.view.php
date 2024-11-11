<?php
if (!defined('ABSPATH')) exit;

$hash = $_SESSION['idEventoHash'];
$idEvento = decryptHash($hash);

if (isset($_POST['adicionar_setor'])) {
    $modeloSetores->validarFormSetores();
}

$evento = $modelo->getEvento($idEvento);
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Adicionar Setor ao Evento: <?php echo htmlentities(chk_array($modelo->form_data, 'evento')); ?></h1>
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
                <h3 class="card-title">Adicionar Setor</h3>
            </div>

            <form role="form" action="" method="POST">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="nomeSetor">Nome do Setor</label>
                                <input type="text" class="form-control" name="nomeSetor" id="nomeSetor" required>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="idEvento" value="<?php echo $idEvento; ?>">

                <div class="card-footer">
                    <button type="submit" name="adicionar_setor" class="btn btn-primary">Adicionar Setor</button>
                    <a href="<?php echo HOME_URI; ?>/setores/index/<?php echo encryptId($idEvento); ?>" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>

    </section>
</div>