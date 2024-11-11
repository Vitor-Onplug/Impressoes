<?php 
if(!defined('ABSPATH')) exit; 

if(chk_array($this->parametros, 0) == 'bloquear'){
	$modelo->bloquearPessoa();
}

if(chk_array($this->parametros, 0) == 'desbloquear'){
	$modelo->desbloquearPessoa();
}

$genero = isset($_REQUEST["genero"]) ? $_REQUEST["genero"] : null;
$status = isset($_REQUEST["status"]) ? $_REQUEST["status"] : null;
$q = isset($_REQUEST["q"]) ? $_REQUEST["q"] : null;

$filtros = array('genero' => $genero, 'status' => $status, 'q' => $q);

$pessoas = $modelo->getPessoas($filtros); 
?>
		<div class="content-wrapper">
			<section class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1>Pessoas</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
								<li class="breadcrumb-item active"><a href="<?php echo HOME_URI; ?>/pessoas">Pessoas</a></li>
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
							<a href="<?php echo HOME_URI; ?>/pessoas" class="btn btn-primary">Limpar</a>
						</div>
					</form>
				</div>
			</section>
			
			<section class="content">
				<div class="card card-secondary">
					<div class="card-header">
						<h3 class="card-title">Pessoas Cadastradas</h3>
					</div>
					<div class="card-body">
						<?php 
						echo $modelo->form_msg;
						?>
						
						<table id="table" class="table table-hover table-bordered table-striped dataTable">
							<thead>
								<tr>
									<th style="width: 250px;">Nome</th>
									<th>Nome Social</th>
									<th>Email</th>
									<th>Telefone</th>
									<th class="sorter-false">Opções</th>
								</tr>
							</thead>

							<tbody>
								<?php foreach($pessoas AS $dados): ?>
								<tr>
									<td>
										<a href="<?php echo HOME_URI;?>/pessoas/index/perfil/<?php echo $dados['id']; ?>"><?php echo $dados['nome']; ?> <?php echo $dados['sobrenome']; ?></a>
									</td>
									<td>
										<a href="<?php echo HOME_URI;?>/pessoas/index/perfil/<?php echo $dados['id']; ?>"><?php echo $dados['apelido']; ?></a>
									</td>			
									<td>
										<a href="<?php echo HOME_URI;?>/pessoas/index/perfil/<?php echo $dados['id']; ?>"><?php echo $dados['email']; ?></a>	
									</td>
									<td>
										<a href="<?php echo HOME_URI;?>/pessoas/index/perfil/<?php echo $dados['id']; ?>"><?php echo $dados['telefone']; ?></a>
									</td>								
									<td>
										<a href="<?php echo HOME_URI;?>/pessoas/index/perfil/<?php echo $dados['id']; ?>" class="icon-tab" title="Perfil"><i class="fas fa-user "></i></a>&nbsp;
										<a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo $dados['id']; ?>" class="icon-tab" title="Editar"><i class="far fa-edit"></i></a>&nbsp;
										
										<a href="<?php echo HOME_URI; ?>/pessoas/index/usuarios/<?php echo $dados['id']; ?>" class="icon-tab" title="Usuário"><i class="fa-solid fa-user-pen"></i></a>&nbsp;
										
										<?php if($dados['status'] == 'T'){ ?>
										<a href="<?php echo HOME_URI;?>/pessoas/index/bloquear/<?php echo $dados['id']; ?>" class="icon-tab" title="Bloquear"><i class="fas fa-unlock text-green"></i></a>&nbsp;
										<?php }else{ ?>
										<a href="<?php echo HOME_URI;?>/pessoas/index/desbloquear/<?php echo $dados['id']; ?>" class="icon-tab" title="Desbloquear"><i class="fas fa-lock text-red"></i></a>
										<?php } ?>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<div class="card-footer">
						<a href="<?php echo HOME_URI;?>/pessoas/index/adicionar"><button type="button" class="btn btn-danger btn-lg">Adicionar Pessoa</button></a>
					</div>
				</div>
			</section>
		</div>