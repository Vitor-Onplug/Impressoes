<?php 
if(!defined('ABSPATH')) exit; 


if (chk_array($this->parametros, 0) == 'bloquear') {
    $modelo->bloquearUsuario('usuario');
}

if (chk_array($this->parametros, 0) == 'desbloquear') {
    $modelo->desbloquearUsuario('usuario');
}

$status = isset($_REQUEST["status"]) ? $_REQUEST["status"] : null;
$q = isset($_REQUEST["q"]) ? $_REQUEST["q"] : null;

$filtros = array('status' => $status, 'q' => $q);

$usuarios = $modelo->getUsuarios($filtros);
?>

<div class="content-wrapper">
			<section class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1>Usuários</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
								<li class="breadcrumb-item active"><a href="<?php echo HOME_URI; ?>/usuarios">Usuários</a></li>
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
							<a href="<?php echo HOME_URI; ?>/usuarios" class="btn btn-primary">Limpar</a>
						</div>
					</form>
				</div>
			</section>
			
			<section class="content">
				<div class="card card-secondary">
					<div class="card-header">
						<h3 class="card-title">Usuários Cadastradas</h3>
					</div>
					<div class="card-body">
						<?php 
						echo $modelo->form_msg;
						?>
						
						<table id="table" class="table table-hover table-bordered table-striped dataTable">
							<thead>
								<tr>
									<th style="width: 250px;">Nome</th>
                                    <th>Email Principal</th>
									<th class="sorter-false">Opções</th>
								</tr>
							</thead>

							<tbody>
								<?php foreach($usuarios AS $dados): ?>
								<tr>
									<td>
										<a href="<?php echo HOME_URI;?>/pessoas/index/usuarios/<?php echo $dados['idPessoa']; ?>"><?php echo $dados['nome']; ?> <?php echo $dados['sobrenome']; ?></a>
									</td>	
                                    <td>
                                        <a href="<?php echo HOME_URI;?>/pessoas/index/editar/<?php echo $dados['idPessoa'];?>/emails"> <?php echo $dados['email']; ?></a>
                                    </td>									
									<td>
										<a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo $dados['idPessoa']; ?>" class="icon-tab" title="Editar Pessoa"><i class="far fa-edit"></i></a>&nbsp;
										
										<a href="<?php echo HOME_URI; ?>/pessoas/index/usuarios/<?php echo $dados['idPessoa']; ?>" class="icon-tab" title="Editar Usuário"><i class="fa-solid fa-user-pen"></i></a>&nbsp;
										
										<?php if($dados['status'] == 'T'){ ?>
										<a href="<?php echo HOME_URI;?>/usuarios/index/bloquear/<?php echo $dados['idUsuario']; ?>" class="icon-tab" title="Bloquear"><i class="fas fa-unlock text-green"></i></a>&nbsp;
										<?php }else{ ?>
										<a href="<?php echo HOME_URI;?>/usuarios/index/desbloquear/<?php echo $dados['idUsuario']; ?>" class="icon-tab" title="Desbloquear"><i class="fas fa-lock text-red"></i></a>
										<?php } ?>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<div class="card-footer">
						<a href="<?php echo HOME_URI;?>/usuarios/index/adicionar"><button type="button" class="btn btn-danger btn-lg">Adicionar Usuário</button></a>
					</div>
				</div>
			</section>
		</div>