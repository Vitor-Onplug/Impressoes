<?php
if (!defined('ABSPATH')) exit;

$hash = $_SESSION['idEventoHash'];
$idEvento = decryptHash($hash);

if (chk_array($this->parametros, 0) == 'bloquearTerminal') {
    $modeloTerminais->bloquearTerminal();
}

if (chk_array($this->parametros, 0) == 'desbloquearTerminal') {
    $modeloTerminais->desbloquearTerminal();
}

$status = isset($_REQUEST["status"]) ? $_REQUEST["status"] : null;
$q = isset($_REQUEST["q"]) ? $_REQUEST["q"] : null;

$filtros = array('status' => $status, 'q' => $q);

$terminais = $modeloTerminais->getTerminais($idEvento);
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Gerenciamento de Terminais</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="<?php echo HOME_URI; ?>/eventos/index/terminais">Terminais</a></li>
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
                    <a href="<?php echo HOME_URI; ?>/eventos/index/terminais" class="btn btn-primary">Limpar</a>
                </div>
            </form>
        </div>
    </section>

    <section class="content">

        <div class="row">
            <div class="col-md-12">
            <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Terminais</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        echo $modeloTerminais->form_msg;
                        ?>
                        <div class="table-responsive">
                            <table id="table" class="table table-hover table-bordered table-striped dataTable">
                                <thead>
                                    <tr>
                                        <th>Terminal</th>
                                        <th>Numero</th>
                                        <th>Setor</th>
                                        <th>Tipo</th>
                                        <th>IP</th>
                                        <th>Usuário</th>
                                        <th>Senha</th>
                                        <th>Opções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($terminais as $terminal): ?>
                                        <tr>
                                            <td><?php echo $terminal['nomeTerminal']; ?></td>
                                            <td><?php echo $terminal['numeroTerminal']; ?></td>
                                            <td><?php echo $terminal['nomeSetor']; ?></td>
                                            <th><?php echo $terminal['tipo']; ?></th>
                                            <td><?php echo $terminal['ip']; ?></td>
                                            <td><?php echo $terminal['usuario']; ?></td>
                                            <td><?php echo $terminal['senha']; ?></td>
                                            <td>
                                                <a href="<?php echo HOME_URI; ?>/eventos/index/editarTerminal/<?php echo encryptId($terminal['id']); ?>" class="icon-tab" title="Editar"><i class="far fa-edit"></i></a>&nbsp;

                                                <?php if ($terminal['status'] == 'T') { ?>
                                                    <a href="<?php echo HOME_URI; ?>/eventos/index/bloquearTerminal/<?php echo encryptId($terminal['id']); ?>" class="icon-tab" title="Bloquear"><i class="fas fa-unlock text-green"></i></a>&nbsp;
                                                <?php } else { ?>
                                                    <a href="<?php echo HOME_URI; ?>/eventos/index/desbloquearTerminal/<?php echo encryptId($terminal['id']); ?>" class="icon-tab" title="Desbloquear"><i class="fas fa-lock text-red"></i></a>
                                                <?php } ?>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Terminal</th>
                                        <th>Numero</th>
                                        <th>Setor</th>
                                        <th>Tipo</th>
                                        <th>IP</th>
                                        <th>Usuário</th>
                                        <th>Senha</th>
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
                <a href="<?php echo HOME_URI; ?>/eventos/index/adicionarTerminal/<?php echo encryptId($idEvento); ?>"><button type="button" class="btn btn-block btn-danger">Adicionar Terminal</button></a>
            </div>
        </div>
    </section>
</div>