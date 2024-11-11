<?php
if (!defined('ABSPATH')) exit;

$hash = $_SESSION['idEventoHash'];
$idEvento = decryptHash($hash);

if (chk_array($this->parametros, 0) == 'bloquearLeitor') {
    $modeloLeitores->bloquearLeitor();
}

if (chk_array($this->parametros, 0) == 'desbloquearLeitor') {
    $modeloLeitores->desbloquearLeitor();
}

// Filtrar por status ou busca de texto
$status = isset($_REQUEST["status"]) ? $_REQUEST["status"] : null;
$q = isset($_REQUEST["q"]) ? $_REQUEST["q"] : null;

$filtros = array('status' => $status, 'q' => $q);

// Obter os leitores faciais do evento (filtrados por setor)
$leitores = $modeloLeitores->getLeitores($idEvento);
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Gerenciamento de Leitores Faciais</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="<?php echo HOME_URI; ?>/eventos/index/leitores">Leitores Faciais</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Filtro de Busca</h3>
            </div>
            <form role="form" action="" method="GET" enctype="multipart/form-data">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="q">Busca por Texto</label> *Desenvolvimento*
                                <input type="text" class="form-control" placeholder="Digite aqui..." name="q" id="q" value="<?php echo $q; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Buscar</button>
                    <a href="<?php echo HOME_URI; ?>/eventos/index/leitores" class="btn btn-primary">Limpar</a>
                </div>
            </form>
        </div>

        <?php
        $modeloLeitores->form_msg;
        ?>

        <div class="row">
            <div class="col-md-12">

                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Leitores Faciais</h3>
                    </div>
                    <div class="card-body">
                        <?php echo $modeloLeitores->form_msg; ?>
                        <div class="table-responsive">
                            <table id="table" class="table table-hover table-bordered table-striped dataTable">
                                <thead>
                                    <tr>
                                        <th>Nome do Leitor</th>
                                        <th>Número</th>
                                        <th>Setor</th>
                                        <th>Terminal</th>
                                        <th>IP</th>
                                        <th>Usuário</th>
                                        <th>Senha</th>
                                        <th>Opções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($leitores as $leitor): ?>
                                        <tr>
                                            <td><?php echo $leitor['nomeLeitor']; ?></td>
                                            <td><?php echo $leitor['numeroLeitor']; ?></td>
                                            <td><?php echo $leitor['nomeSetor']; ?></td> <!-- ou nome do setor -->
                                            <td><?php echo $leitor['nomeTerminal']; ?></td>
                                            <td><?php echo $leitor['ip']; ?></td>
                                            <td><?php echo $leitor['usuario']; ?></td>
                                            <td><?php echo $leitor['senha']; ?></td>
                                            <td>
                                                <a href="<?php echo HOME_URI; ?>/eventos/index/editarLeitor/<?php echo encryptId($leitor['id']); ?>" class="icon-tab" title="Editar"><i class="far fa-edit"></i></a>&nbsp;

                                                <?php if ($leitor['status'] == 'T') { ?>
                                                    <a href="<?php echo HOME_URI; ?>/eventos/index/bloquearLeitor/<?php echo encryptId($leitor['id']); ?>" class="icon-tab" title="Bloquear"><i class="fas fa-unlock text-green"></i></a>&nbsp;
                                                <?php } else { ?>
                                                    <a href="<?php echo HOME_URI; ?>/eventos/index/desbloquearLeitor/<?php echo encryptId($leitor['id']); ?>" class="icon-tab" title="Desbloquear"><i class="fas fa-lock text-red"></i></a>
                                                <?php } ?>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Nome do Leitor</th>
                                        <th>Número</th>
                                        <th>Setor</th>
                                        <th>Terminal</th>
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
                <a href="<?php echo HOME_URI; ?>/eventos/index/adicionarLeitor/<?php echo encryptId($idEvento); ?>"><button type="button" class="btn btn-block btn-danger">Adicionar Leitor</button></a>
            </div>
        </div>
    </section>
</div>