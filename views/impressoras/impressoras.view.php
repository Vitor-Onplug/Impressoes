<?php
if (!defined('ABSPATH')) exit;

$hash = $_SESSION['idParceiroHash'];
$idParceiro = decryptHash($hash);

// Ações para bloquear e desbloquear uma impressora
if (chk_array($this->parametros, 0) == 'bloquear') {
    $modelo->bloquearImpressora();
}

if (chk_array($this->parametros, 0) == 'desbloquear') {
    $modelo->desbloquearImpressora();
}
var_dump($_SESSION);
// Filtros para pesquisa
$status = isset($_REQUEST["status"]) ? $_REQUEST["status"] : null;
$q = isset($_REQUEST["q"]) ? $_REQUEST["q"] : null;

// Define os filtros para a busca de impressoras
$filtros = array('status' => $status, 'q' => $q, 'idParceiro' => $idParceiro);

// Obtém a lista de impressoras com base nos filtros aplicados
$impressoras = $modelo->getImpressoras($filtros);
?>
<div class="content-wrapper">
    <!-- Cabeçalho de conteúdo -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Impressoras</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="<?php echo HOME_URI; ?>/impressoras">Impressoras</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Formulário de pesquisa de impressoras -->
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
                                <label for="q">Busca por texto</label>
                                <input type="text" class="form-control" placeholder="Digite o nome ou descrição da impressora..." name="q" id="q" value="<?php echo $q; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Buscar</button>
                    <a href="<?php echo HOME_URI; ?>/impressoras" class="btn btn-primary">Limpar</a>
                </div>
            </form>
        </div>
    </section>

    <!-- Listagem de impressoras cadastradas -->
    <section class="content">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Impressoras Cadastradas</h3>
            </div>
            <div class="card-body">
                <?php
                echo $modelo->form_msg; // Mensagens do formulário, se houver
                ?>

                <table id="table" class="table table-hover table-bordered table-striped dataTable">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Departamento</th>
                            <th class="sorter-false">Opções</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($impressoras as $dados): ?>
                            <tr>
                                <td>
                                    <a><?php echo $dados['nome']; ?></a>
                                </td>
                                <td>
                                    <a><?php echo $dados['marcaNome'] ?? 'N/A'; ?></a>
                                </td>
                                <td>
                                    <a><?php echo $dados['modelo'] ?? 'N/A'; ?></a>
                                </td>
                                <td>
                                    <a><?php echo $dados['departamentoNome'] ?? 'N/A'; ?></a>
                                </td>
                                <td>
                                    <a href="<?php echo HOME_URI; ?>/impressoras/index/editar/<?php echo encryptId($dados['id']); ?>" class="icon-tab" title="Editar"><i class="far fa-edit"></i></a>&nbsp;

                                    <?php if ($dados['status'] == 'T') { ?>
                                        <a href="<?php echo HOME_URI; ?>/impressoras/index/bloquear/<?php echo encryptId($dados['id']); ?>" class="icon-tab" title="Bloquear"><i class="fas fa-unlock text-green"></i></a>&nbsp;
                                    <?php } else { ?>
                                        <a href="<?php echo HOME_URI; ?>/impressoras/index/desbloquear/<?php echo encryptId($dados['id']); ?>" class="icon-tab" title="Desbloquear"><i class="fas fa-lock text-red"></i></a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="<?php echo HOME_URI; ?>/impressoras/index/adicionar"><button type="button" class="btn btn-danger btn-lg">Adicionar Impressora</button></a>
            </div>
        </div>
    </section>
</div>
