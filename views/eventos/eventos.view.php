<?php
if (!defined('ABSPATH')) exit;

if ($this->check_permissions('evento-remover', $this->userdata['modulo'])) {
	$modelo->removerEvento($parametros);
}

if (chk_array($this->parametros, 0) == 'bloquear') {
	$modelo->bloquearEvento();
}

if (chk_array($this->parametros, 0) == 'desbloquear') {
	$modelo->desbloquearEvento();
}


$categorias = $modeloCategorias->getCategorias();
$parceiros = $modeloParceiros->getparceiros();

$dataInicioFim = isset($_REQUEST["dataInicioFim"]) ? $_REQUEST["dataInicioFim"] : null;
$categoria = isset($_REQUEST["categoria"]) ? $_REQUEST["categoria"] : null;
$parceiro = isset($_REQUEST["parceiro"]) ? $_REQUEST["parceiro"] : null;
$responsavel = isset($_REQUEST["responsavel"]) ? $_REQUEST["responsavel"] : null;
$q = isset($_REQUEST["q"]) ? $_REQUEST["q"] : null;

if ($this->userdata['idPermissao'] == 3) {
	$responsavel = $this->userdata['id'];
}

$filtros = array('dataInicioFim' => $dataInicioFim, 'categoria' => $categoria, 'parceiro' => $parceiro, 'responsavel' => $responsavel, 'q' => $q);

$eventos = $modelo->getEventos($filtros);

?>
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1>Eventos</h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
						<li class="breadcrumb-item active"><a href="<?php echo HOME_URI; ?>/eventos">Eventos</a></li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		<?php if ($this->userdata['idPermissao'] < 4) { ?>
			<div class="row">
				<div class="col-md-12">
					<div class="box box-danger collapsed-box">
						<div class="box-header with-border" data-widget="collapse">
							<h3 class="box-title">Busca com filtros <i class="fa fa-filter fa-sm"></i></h3>
						</div>

						<form role="form" action="" method="POST" enctype="multipart/form-data">
							<div class="box-body">
								<div class="row">

									<div class="col-md-3">
										<label for="dataInicio">Data</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fa fa-calendar"></i></span>
											</div>
											<input type="text" class="form-control" id="dataInicio" name="dataInicio">
										</div>
									</div>



									<div class="col-md-3">
										<div class="form-group">
											<label for="categoria">Categoria</label>

											<select class="form-control select2" name="categoria" id="categoria">
												<option value="">Escolha uma opção</option>
												<?php
												foreach ($categorias as $dadosCategorias):
													echo "<option value='" . $dadosCategorias['id'] . "' ";
													if ($categoria == $dadosCategorias['id']) {
														echo 'selected';
													}
													echo ">" . $dadosCategorias['categoria'] . "</option>";
												endforeach;
												?>
											</select>
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label for="cliente">Cliente</label>
											<select class="form-control select2" name="cliente" id="cliente">
												<option value="">Escolha</option>
												<?php

												foreach ($parceiros as $dadosParceiros):
													echo "<option value='" . $dadosParceiros['idEmpresa'] . "' ";
													if ($parceiro == $dadosParceiros['idEmpresa']) {
														echo 'selected';
													}
													echo ">" . $dadosParceiros['razaoSocial'] . "</option>";
												endforeach;
												?>
											</select>
										</div>
									</div>

								</div>



								<div class="col-md-6">
									<div class="form-group">
										<label for="q">Busca por texto</label>
										<input type="text" class="form-control" placeholder="Digite aqui..." name="q" id="q" value="<?php echo $q; ?>">
									</div>
								</div>

							</div>

							<div class="box-footer">
								<button type="submit" class="btn btn-primary">Buscar</button>
								<a href="<?php echo HOME_URI; ?>/eventos" class="btn btn-primary">Limpar</a>
							</div>
						</form>

					</div>
				</div>
			</div>
		<?php } ?>
		<br>
		<div class="row">
			<div class="col-md-12">
				<div class="card card-secondary">
					<div class="card-header">
						<h3 class="card-title">Eventos</h3>
					</div>

					<div class="card-body">

						<?php
						echo $modelo->form_msg;
						?>
						<div class="table-responsive">
							<table id="table" class="table table-hover table-bordered table-striped dataTable">
								<thead>
									<tr>
										<th>Evento</th>
										<?php if ($this->userdata['idPermissao'] < 4) { ?>
											<th>Cliente</th>
										<?php } ?>
										<th>Periodo</th>
										<th>Data Cadastro</th>
										<th>Categoria</th>
										<th>Opções</th>
									</tr>
								</thead>

								<tbody>
									<?php
									foreach ($eventos as $dados):
										$dados['id'] = $dados['idEvento'];

									?>
										<tr>
											<td><a href="<?php echo HOME_URI; ?>/eventos/index/perfil/<?php echo encryptId($dados['id']); ?>"><?php echo $dados['evento']; ?></a></td>

											<?php if ($this->userdata['idPermissao'] < 4) { ?>
												<td><a href="<?php echo HOME_URI; ?>/empresas/index/perfil/<?php echo $dados['idCliente']; ?>"><?php echo $dados['responsavel']; ?></a></td>
											<?php } ?>

											<td>
												<a href="<?php echo HOME_URI; ?>/eventos/index/perfil/<?php echo encryptId($dados['id']); ?>">
													<?php
													$dataInicial = explode("-", $dados['dataInicio']);
													$dataFinal = explode("-", $dados['dataFim']);

													if ($dados['dataInicio'] == $dados['dataFim']) {
														echo $dataInicial[2] . " " . mesAbreviado($dataInicial[1]) . "/" . $dataInicial[0];
													} else {
														echo $dataInicial[2] . " " . mesAbreviado($dataInicial[1]) . "/" . $dataInicial[0] . " - " . $dataFinal[2] . " " . mesAbreviado($dataFinal[1]) . "/" . $dataFinal[0];
													}
													?>
												</a>
											</td>

											<td><a href="<?php echo HOME_URI; ?>/eventos/index/perfil/<?php echo encryptId($dados['id']); ?>"><?php echo date('d/m/Y H:i:s', strtotime($dados['dataCriacao'])); ?></a></td>

											<td><?php echo $dados['categoriaEvento']; ?></td>

											<td>

												<?php if ($dados['status'] == 'T') { ?>
													<a href="<?php echo HOME_URI; ?>/eventos/index/bloquear/<?php echo encryptId($dados['id']); ?>" class="icon-tab" title="Bloquear"><i class="fas fa-unlock text-green"></i></a>&nbsp;
												<?php } else { ?>
													<a href="<?php echo HOME_URI; ?>/eventos/index/desbloquear/<?php echo encryptId($dados['id']); ?>" class="icon-tab" title="Desbloquear"><i class="fas fa-lock text-red"></i></a>
												<?php } ?>

											</td>

										</tr>
									<?php endforeach; ?>
								</tbody>
								<tfoot>
									<tr>
										<th>Evento</th>
										<?php if ($this->userdata['idPermissao'] < 4) { ?>
											<th>Cliente</th>
										<?php } ?>
										<th>Periodo</th>
										<th>Data Cadastro</th>
										<th>Categoria</th>
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
				<a href="<?php echo HOME_URI; ?>/eventos/index/adicionar"><button type="button" class="btn btn-block btn-danger">Adicionar Evento</button></a>
			</div>
		</div>
	</section>
</div>

<script>
	$(document).ready(function() {
		$('#dataInicio').datepicker({
			autoclose: true,
			format: 'dd/mm/yyyy',
			todayHighlight: true,
			templates: {
				leftArrow: '<i class="fa fa-chevron-left"></i>',
				rightArrow: '<i class="fa fa-chevron-right"></i>'
			}
		});

		$('#cliente').select2({
			theme: "classic"
		});

		$('#categoria').select2({
			theme: "classic"
		});
	});;
</script>