				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="card card-widget widget-user">
								<div class="widget-user-header bg-secondary">
									<!-- Imagem -->
									<h3 class="widget-user-username"><img src="<?php echo HOME_URI . '/views/_uploads/' . htmlentities(chk_array($modelo->form_data, 'imagem')); ?>" class="img-circle" alt="User Image"></h3>
									 <!-- Nome -->
									<h3 class="widget-user-username"><?php echo htmlentities(chk_array($modelo->form_data, 'nome')); ?> <?php echo htmlentities(chk_array($modelo->form_data, 'sobrenome')); ?></h3>
									<h5 class="widget-user-desc"><?php echo htmlentities(chk_array($modelo->form_data, 'apelido')); ?></h5>
								</div>
							</div>
						</div>						
					</div>
				</div>