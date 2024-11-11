<?php
if (!defined('ABSPATH')) exit;

$hash = $_SESSION['idEventoHash'];
$idEvento = decryptHash($hash);

if (chk_array($this->parametros, 0) == 'bloquearSetor') {
    $modeloSetores->bloquearSetor();
}

if (chk_array($this->parametros, 0) == 'desbloquearSetor') {
    $modeloSetores->desbloquearSetor();
}

$status = isset($_REQUEST["status"]) ? $_REQUEST["status"] : null;
$q = isset($_REQUEST["q"]) ? $_REQUEST["q"] : null;

$filtros = array('status' => $status, 'q' => $q);

$setores = $modeloSetores->getSetores($idEvento); // Carrega setores do evento em questão
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Gerenciamento de Setores</h1>
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


    <section class="content">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Pesquisa</h3>
            </div>
            <form role="form" action="" method="GET" enctype="multipart/form-data">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="q">Busca por texto</label> *Desenvolvimento*
                                <input type="text" class="form-control" placeholder="Digite aqui..." name="q" id="q" value="<?php echo $q; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Buscar</button>
                    <a href="<?php echo HOME_URI; ?>/eventos/index/setores" class="btn btn-primary">Limpar</a>
                </div>
            </form>
        </div>
    </section>

    <section class="content">
        <!-- Listagem de setores -->
        <div class="row">
            <div class="col-md-12">

                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Setores</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        echo $modeloSetores->form_msg;
                        ?>
                        <div class="table-responsive">
                            <table id="table" class="table table-hover table-bordered table-striped dataTable">
                                <thead>
                                    <tr>
                                        <th>Setor</th>
                                        <th>Evento</th>
                                        <th>Opções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($setores as $setor): ?>
                                        <tr>
                                            <td><?php echo $setor['nomeSetor']; ?></td>
                                            <td><?php echo $setor['evento']; ?></td>
                                            <td>
                                                <a href="<?php echo HOME_URI; ?>/eventos/index/editarSetor/<?php echo encryptId($setor['id']); ?>" class="icon-tab" title="Editar"><i class="far fa-edit"></i></a>&nbsp;

                                                <?php if ($setor['status'] == 'T') { ?>
                                                    <a href="<?php echo HOME_URI; ?>/eventos/index/bloquearSetor/<?php echo encryptId($setor['id']); ?>" class="icon-tab" title="Bloquear"><i class="fas fa-unlock text-green"></i></a>&nbsp;
                                                <?php } else { ?>
                                                    <a href="<?php echo HOME_URI; ?>/eventos/index/desbloquearSetor/<?php echo encryptId($setor['id']); ?>" class="icon-tab" title="Desbloquear"><i class="fas fa-lock text-red"></i></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Setor</th>
                                        <th>Evento</th>
                                        <th>Opções</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <a href="<?php echo HOME_URI; ?>/eventos/index/adicionarSetor/<?php echo encryptId($idEvento); ?>"><button type="button" class="btn btn-block btn-danger">Adicionar Setor</button></a>
            </div>
        </div>
    </section>
</div>