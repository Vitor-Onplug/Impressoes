<?php
if(!defined('ABSPATH')) exit;


?>

			<section class="content">
				<div class="card card-secondary">
					<div class="card-header">
						<h3 class="card-title">Permissão</h3>
					</div>
					<form role="form" action="" method="POST" enctype="multipart/form-data">
						<div class="card-body">
							<?php 
							echo $modelo->form_msg;
							?>
							
							<div class="row">									
								<div class="col-md-6">
									<div class="form-group">
										<label for="permissao">Permissão</label>
										<input type="text" class="form-control" id="permissao" name="permissao" placeholder="" value="<?php echo htmlentities(chk_array($modelo->form_data, 'permissao')); ?>" required maxlength="255">
									</div>
								</div>
								
								<div class="col-md-6">
									<div class="form-group">
										<label for="modulos">Modulos</label>
										<input type="text" class="form-control" id="modulos" name="modulos" placeholder="" value="<?php echo htmlentities(chk_array($modelo->form_data, 'modulos')); ?>" required maxlength="255">
									</div>
								</div>
								

							</div>

						</div>

						<div class="card-footer">
							<button type="submit" class="btn btn-primary">Salvar</button>
						</div>
					</form>
				</div>
			</section>