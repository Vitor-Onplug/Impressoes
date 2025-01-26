<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="card card-widget widget-user">
				<div class="widget-user-header bg-secondary ">
					<?php
					if (chk_array($this->parametros, 1)) {
						$hash = chk_array($this->parametros, 1);
						$id = decryptHash($hash);
					}
					$dirAvatar = $modelo->getAvatar($id, false);
					?>
					<!-- Imagem -->
					<h3 class="widget-user-username">
						<img
							src="<?php echo HOME_URI . '/' . $dirAvatar ?>"
							class="img-circle elevation-2"
							alt="User Image"
							style="width: 90px; height: 90px; object-fit: cover;">
					</h3>
					<!-- Nome -->
					<h3 class="widget-user-username"><?php echo htmlentities(chk_array($modelo->form_data, 'razaoSocial')); ?></h3>
					<h5 class="widget-user-desc"><?php echo htmlentities(chk_array($modelo->form_data, 'nomeFantasia')); ?></h5>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	.widget-user-header {
		min-height: 180px;
		/* Increase height */
	}
</style>