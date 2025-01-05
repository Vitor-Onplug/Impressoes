<?php
if (!defined('ABSPATH')) exit;

$status = isset($_REQUEST["status"]) ? $_REQUEST["status"] : null;
$q = isset($_REQUEST["q"]) ? $_REQUEST["q"] : null;

$filtros = array('status' => $status, 'q' => $q);

if (chk_array($this->parametros, 0) == 'bloquear') {
    $modelo->bloquearParceiro();
}

if (chk_array($this->parametros, 0) == 'desbloquear') {
    $modelo->desbloquearParceiro();
}

$parceiros = $modelo->getParceiros($filtros);

?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Parceiros</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="<?php echo HOME_URI; ?>/parceiros">Parceiros</a></li>
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
                    <a href="<?php echo HOME_URI; ?>/parceiros" class="btn btn-primary">Limpar</a>
                </div>
            </form>
        </div>
    </section>

    <section class="content">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Parceiros</h3>
            </div>
            <div class="card-body">
                <?php
                    echo $modelo->form_msg;
                ?>

                <table id="table" class="table table-hover table-bordered table-striped dataTable">
                    <thead>
                        <tr>
                            <th style="width: 250px;">Empresa</th>
                            <th style="width: 150px;">Parceiro Desde</th>
                            <th class="sorter-false">Opções</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($parceiros as $dados): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo HOME_URI; ?>/empresas/index/perfil/<?php echo encryptId($dados['idEmpresa']); ?>"><?php echo $dados['razaoSocial']; ?></a>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($dados['dataCriacao'])); ?></td>
                                <td>
										<a href="<?php echo HOME_URI;?>/empresas/index/perfil/<?php echo encryptId($dados['idEmpresa']); ?>" class="icon-tab" title="Perfil"><i class="fas fa-user "></i></a>&nbsp;
										<a href="<?php echo HOME_URI; ?>/parceiros/index/editar/<?php echo encryptId($dados['id']); ?>" class="icon-tab" title="Editar"><i class="far fa-edit"></i></a>&nbsp;
										
										<?php if($dados['status'] == 'T'){ ?>
										<a href="<?php echo HOME_URI;?>/parceiros/index/bloquear/<?php echo encryptId($dados['id']); ?>" class="icon-tab" title="Bloquear"><i class="fas fa-unlock text-green"></i></a>&nbsp;
										<?php }else{ ?>
										<a href="<?php echo HOME_URI;?>/parceiros/index/desbloquear/<?php echo encryptId($dados['id']); ?>" class="icon-tab" title="Desbloquear"><i class="fas fa-lock text-red"></i></a>
										<?php } ?>
									</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="<?php echo HOME_URI; ?>/parceiros/index/adicionar"><button type="button" class="btn btn-danger btn-lg">Adicionar Parceiro</button></a>
            </div>
        </div>
    </section>
</div>