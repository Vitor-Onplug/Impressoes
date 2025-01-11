<?php
if (!defined('ABSPATH')) exit;

// Verificar se o ID da pessoa é válido
if (empty(chk_array($parametros, 1))) {
	echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/pessoas/">';
	echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/pessoas";</script>';
	exit;
}

if (chk_array($this->parametros, 1)) {
	$hash = chk_array($this->parametros, 1);
	$id = decryptHash($hash);
}

// Buscar os dados da pessoa
$modelo->getPessoa($id);

if ($modelo->form_msg && preg_match('/(inexistente|encontrado)/simx', $modelo->form_msg)) {
	echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/pessoas">';
	echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/pessoas";</script>';
	exit;
}

// Validar e processar o formulário de usuário
$modeloUsuarios->validarFormUsuarios();
$registro = $modeloUsuarios->getUsuario(null, $id);

$idEmpresa = $_SESSION['userdata']['idEmpresa'];
$filtros = array('status' => 'T', 'idEmpresa' => $idEmpresa);

// Buscar permissões e empresas disponíveis
$permissoes = $modeloPermissoes->getPermissoes($filtros);
$empresas = $modeloEmpresa->getEmpresas(); // Implementar método no modelo para buscar empresas

if ($registro != null) {
	if (chk_array($this->parametros, 2) == 'bloquear') {
		$modeloUsuarios->bloquearUsuario();
	}

	if (chk_array($this->parametros, 2) == 'desbloquear') {
		$modeloUsuarios->desbloquearUsuario();
	}

	$modeloUsuarios->removerUsuario($parametros);

	$dataHoraEdicao = explode(" ", chk_array($registro, 'dataEdicao'));

	$ultimaAtividade = implode("/", array_reverse(explode("-", $dataHoraEdicao[0]))) . " às " . $dataHoraEdicao[1];
}

?>
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1>Pessoa</h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>/pessoas">Pessoas</a></li>
						<li class="breadcrumb-item active"><a href="<?php echo HOME_URI; ?>/pessoas/index/usuarios/<?php echo chk_array($parametros, 1); ?>">Usuário</a></li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<?php require_once ABSPATH . '/views/pessoas/mini-perfil.inc.view.php'; ?>

	<div class="container-fluid">
		<div class="row">
			<div class="col-md-6">
				<section class="content">
					<div class="card card-secondary">
						<div class="card-header">
							<h3 class="card-title">Usuário</h3>
						</div>
						<form role="form" action="" method="POST">
							<div class="card-body">
								<?php
								echo $modeloUsuarios->form_msg;
								?>

								<div class="form-group">
									<label for="permissao">Permissão</label>
									<select class="form-control" name="permissao" id="permissao" required>
										<option value="">Escolha</option>
										<?php foreach ($permissoes as $dadosPermissoes): ?>
											<option <?php if (htmlentities(chk_array($registro, 'idPermissao')) == $dadosPermissoes['id']) {
														echo 'selected';
													} ?> value="<?php echo $dadosPermissoes['id']; ?>"><?php echo $dadosPermissoes['permissao']; ?></option>
										<?php endforeach; ?>
									</select>
								</div>

								<div class="form-group">
									<label for="empresa">Empresa</label>
									<select class="form-control" name="idEmpresa" id="empresa" required>
										<option value="">Selecione uma empresa</option>
										<?php foreach ($empresas as $empresa): ?>
											<option
												value="<?php echo $empresa['id']; ?>"
												<?php echo (isset($registro['idEmpresa']) && $registro['idEmpresa'] == $empresa['id']) ? 'selected' : ''; ?>>
												<?php echo $empresa['razaoSocial']; ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>


								<div class="form-group">
									<label for="senha">Senha</label>
									<input type="password" class="form-control" id="senha" name="senha" placeholder="" value="" required maxlength="255">
								</div>

								<div class="form-group">
									<label for="repitaSenha">Repita a Senha</label>
									<input type="password" class="form-control" id="repitaSenha" name="repitaSenha" placeholder="" value="" required maxlength="255">
								</div>
							</div>

							<div class="card-footer">
								<button type="submit" class="btn btn-primary">Salvar</button>
							</div>
						</form>
					</div>
				</section>
			</div>

			<div class="col-md-6">
				<section class="content">
					<div class="card card-secondary">
						<div class="card-header">
							<h3 class="card-title">Informações</h3>
						</div>
						<div class="card-body">
							<?php
							if ($registro == null) {
								echo $this->Messages->info('Essa pessoa não possuí usuário cadastrado.');
							} else {
							?>
								<?php if ($registro['status'] == 'T') { ?>
									<a href="<?php echo HOME_URI; ?>/pessoas/index/usuarios/<?php echo chk_array($parametros, 1); ?>/bloquear" class="btn btn-lg btn-block btn-success"><i class="fa fa-unlock"></i> Bloquear </a>
								<?php } else { ?>
									<a href="<?php echo HOME_URI; ?>/pessoas/index/usuarios/<?php echo chk_array($parametros, 1); ?>/desbloquear" class="btn btn-lg btn-block btn-warning"><i class="fa fa-lock"></i> Desbloquear </a>
								<?php } ?>
								<div class="clearfix"><br></div>
								<a href="<?php echo HOME_URI; ?>/pessoas/index/usuarios/<?php echo chk_array($parametros, 1); ?>/remover" class="btn btn-lg btn-block btn-danger"><i class="fa fa-times"></i> Remover </a>
							<?php } ?>
						</div>
						<?php if ($registro != null) { ?>
							<div class="card-footer">
								<ul class="nav nav-stacked">
									<li>Última Atividade <span class="pull-right"><?php echo $ultimaAtividade; ?></span></li>
								</ul>
							</div>
						<?php } ?>
					</div>
				</section>
			</div>
		</div>
	</div>
</div>