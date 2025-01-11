<?php
if (!defined('ABSPATH')) exit;

if (chk_array($this->parametros, 0) == 'bloquear') {
    $modelo->bloquearPermissao();
}

if (chk_array($this->parametros, 0) == 'desbloquear') {
    $modelo->desbloquearPermissao();
}

$status = isset($_REQUEST["status"]) ? $_REQUEST["status"] : 'T';
$q = isset($_REQUEST["q"]) ? $_REQUEST["q"] : null;

$idEmpresa = $_SESSION['userdata']['idEmpresa'];

$filtros = array('status' => $status, 'q' => $q, 'idEmpresa' => $idEmpresa);


$permissoes = $modelo->getPermissoes($filtros);
?>


<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Permissões</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="<?php echo HOME_URI; ?>/permissoes">Permissões</a></li>
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
                                <label for="q">Busca por texto</label>
                                <input type="text" class="form-control" placeholder="Digite aqui..." name="q" id="q" value="<?php echo $q; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Buscar</button>
                    <a href="<?php echo HOME_URI; ?>/permissoes" class="btn btn-primary">Limpar</a>
                </div>
            </form>
        </div>
    </section>

    <section class="content">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Permissões</h3>
            </div>
            <div class="card-body">
                <?php
                echo $modelo->form_msg;
                ?>

                <table id="table" class="table table-hover table-bordered table-striped dataTable">
                    <thead>
                        <tr>
                            <th style="width: 250px;">Permissão</th>
                            <th>Módulos</th>
                            <th class="sorter-false">Opções</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $exibeSuper = $this->check_permissions('SUPERADMIN', $this->userdata['modulo']);
                        foreach ($permissoes as $dados): 
                            if (!$exibeSuper && $dados['id'] == 1) continue; 
                        ?>
                            <tr>
                                <td>
                                    <a href="<?php echo HOME_URI; ?>/configuracoes/permissoes/editar/<?php echo encryptId($dados['id']); ?>"><?php echo $dados['permissao']; ?></a>
                                </td>
                                <!-- Exibo os modulos da permissao que estao serializados em PHP -->
                                <td>

                                    <a href="<?php echo HOME_URI; ?>/configuracoes/permissoes/editar/<?php echo encryptId($dados['id']); ?>" class="permission-list">
                                        <?php
                                        $modulos = explode(',', $dados['modulo']);
                                        $textoModulos = [];
                                        

                                        foreach ($modulos as $moduloId) {

                                            if (isset($permissions[$moduloId])) {
                                                $textoModulos[] = $permissions[$moduloId];
                                            }
                                        }
                                        echo implode(', ', $textoModulos);
                                        ?>
                                    </a>
                                <td>
                                    <a href="<?php echo HOME_URI; ?>/configuracoes/permissoes/editar/<?php echo encryptId($dados['id']); ?>" class="icon-tab" title="Editar"><i class="far fa-edit"></i></a>&nbsp;

                                    <?php if ($dados['status'] == 'T') { ?>
                                        <a href="<?php echo HOME_URI; ?>/configuracoes/permissoes/bloquear/<?php echo encryptId($dados['id']); ?>" class="icon-tab" title="Bloquear"><i class="fas fa-unlock text-green"></i></a>&nbsp;
                                    <?php } else { ?>
                                        <a href="<?php echo HOME_URI; ?>/configuracoes/permissoes/desbloquear/<?php echo encryptId($dados['id']); ?>" class="icon-tab" title="Desbloquear"><i class="fas fa-lock text-red"></i></a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="<?php echo HOME_URI; ?>/configuracoes/permissoes/adicionar"><button type="button" class="btn btn-danger btn-lg">Adicionar Permissão</button></a>
            </div>
        </div>
    </section>
</div>

<style>
.permission-list {
    display: block;
    max-width: 400px; /* ou o tamanho que preferir */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>