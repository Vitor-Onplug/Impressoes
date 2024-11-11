<?php
if (!defined('ABSPATH')) exit;

$hash = $_SESSION['idEventoHash'];
$idEvento = decryptHash($hash);

if (chk_array($this->parametros, 0) == 'bloquearLote') {
    $modeloLotes->bloquearLote();
}

if (chk_array($this->parametros, 0) == 'desbloquearLote') {
    $modeloLotes->desbloquearLote();
}

$status = isset($_REQUEST["status"]) ? $_REQUEST["status"] : null;
$q = isset($_REQUEST["q"]) ? $_REQUEST["q"] : null;

$filtros = array('status' => $status, 'q' => $q);

$lotes = $modeloLotes->getLotes($idEvento);
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Gerenciamento de Lotes</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="<?php echo HOME_URI; ?>/eventos/index/lotes">Lotes de Credenciais</a></li>
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
                    <a href="<?php echo HOME_URI; ?>/eventos/index/lotes" class="btn btn-primary">Limpar</a>
                </div>
            </form>
        </div>
    </section>

    <section class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Lotes de Credenciais</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        echo $modeloLotes->form_msg;
                        ?>
                        <div class="table-responsive">
                            <table id="table" class="table table-hover table-bordered table-striped dataTable">
                                <thead>
                                    <tr>
                                        <th>Lote</th>
                                        <th>Tipo de Credencial</th>
                                        <th>Tipo de Código</th>
                                        <th>Permite Acesso Facial</th>
                                        <th>Permite Impressão</th>
                                        <th>Tem Autonumeração</th>
                                        <th>Opções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lotes as $lote): ?>
                                        <tr>
                                            <td><?php echo $lote['nomeLote']; ?></td>
                                            <td><?php echo $lote['tipoCredencial'] ?></td>
                                            <td><?php echo $lote['tipoCodigo']?></td>
                                            <td><?php echo $lote['permiteAcessoFacial'] == '1' ? 'Sim' : 'Não'; ?></td>
                                            <td><?php echo $lote['permiteImpressao'] == '1' ? 'Sim' : 'Não'; ?></td>
                                            <td><?php echo $lote['temAutonumeracao'] == '1' ? 'Sim' : 'Não'; ?></td>

                                            <td>
                                                <a href="<?php echo HOME_URI; ?>/eventos/index/editarLote/<?php echo encryptId($lote['id']); ?>" class="icon-tab" title="Editar"><i class="far fa-edit"></i></a>&nbsp;
                                                <a href="<?php echo HOME_URI; ?>/eventos/index/relacoesLote/<?php echo encryptId($lote['id']); ?>" class="icon-tab" title="Relações"><i class="fas fa-link"></i></a>&nbsp;

                                                <?php if ($lote['status'] == 'T') { ?>
                                                    <a href="<?php echo HOME_URI; ?>/eventos/index/bloquearLote/<?php echo encryptId($lote['id']); ?>" class="icon-tab" title="Bloquear"><i class="fas fa-unlock text-green"></i></a>&nbsp;
                                                <?php } else { ?>
                                                    <a href="<?php echo HOME_URI; ?>/eventos/index/desbloquearLote/<?php echo encryptId($lote['id']); ?>" class="icon-tab" title="Desbloquear"><i class="fas fa-lock text-red"></i></a>
                                                <?php } ?>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                    <th>Lote</th>
                                        <th>Tipo de Credencial</th>
                                        <th>Tipo de Código</th>
                                        <th>Permite Acesso Facial</th>
                                        <th>Permite Impressão</th>
                                        <th>Tem Autonumeração</th>
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
                <a href="<?php echo HOME_URI; ?>/eventos/index/adicionarLote/<?php echo encryptId($idEvento); ?>"><button type="button" class="btn btn-block btn-danger">Adicionar Lote</button></a>
            </div>
        </div>
    </section>
</div>