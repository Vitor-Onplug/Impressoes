<?php
if (!defined('ABSPATH')) exit;

$modelo->validarFormEmpresa();

?>

<section class="content">
	<div class="card card-secondary">
		<div class="card-header">
			<h3 class="card-title">Identificação</h3>
		</div>
		<form role="form" action="" method="POST" enctype="multipart/form-data">
			<div class="card-body">
				<?php
				echo $modelo->form_msg;
				?>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="razaoSocial">Razao Social</label>
							<input type="text" class="form-control" id="razaoSocial" name="razaoSocial" placeholder="" value="<?php echo htmlentities(chk_array($modelo->form_data, 'razaoSocial')); ?>" required maxlength="255">
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label for="nomeFantasia">Nome Fantasia</label>
							<input type="text" class="form-control" id="nomeFantasia" name="nomeFantasia" placeholder="" value="<?php echo htmlentities(chk_array($modelo->form_data, 'nomeFantasia')); ?>" required maxlength="255">
						</div>
					</div>

					<div class="col-md-12">
						<div class="form-group">
							<label for="token">Token</label>
							<div class="input-group">
								<input type="text" class="form-control" id="token" name="token" placeholder="" value="<?php echo htmlentities(chk_array($modelo->form_data, 'token')); ?>" required maxlength="255">
								<div class="input-group-append">
									<button type="button" class="btn btn-secondary" onclick="generateToken()">Gerar Token</button>
								</div>
							</div>
						</div>
					</div>

				</div>

				<div class="form-group">
					<label for="observacoes">Observações</label>
					<textarea class="form-control" rows="3" placeholder="" name="observacoes" id="observacoes"><?php echo htmlentities(chk_array($modelo->form_data, 'observacoes')); ?></textarea>
				</div>
			</div>

			<div class="card-footer">
				<button type="submit" class="btn btn-primary">Salvar</button>
			</div>
		</form>
	</div>
</section>

<script>
	function generateToken() {
		const length = 100;
		const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		let token = '';
		for (let i = 0; i < length; i++) {
			token += characters.charAt(Math.floor(Math.random() * characters.length));
		}
		document.getElementById('token').value = token;
	}
</script>