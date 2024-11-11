<?php
if(!defined('ABSPATH')) exit;

$modelo->validarFormPessoa();
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
										<label for="nome">Nome</label>
										<input type="text" class="form-control" id="nome" name="nome" placeholder="" value="<?php echo htmlentities(chk_array($modelo->form_data, 'nome')); ?>" required maxlength="255">
									</div>
								</div>
								
								<div class="col-md-6">
									<div class="form-group">
										<label for="sobrenome">Sobrenome</label>
										<input type="text" class="form-control" id="sobrenome" name="sobrenome" placeholder="" value="<?php echo htmlentities(chk_array($modelo->form_data, 'sobrenome')); ?>" required maxlength="255">
									</div>
								</div>
								
								<div class="col-md-4">
									<div class="form-group">
										<label for="apelido">Nome Social/Tratamento</label>
										<input type="text" class="form-control" id="apelido" name="apelido" placeholder="" value="<?php echo htmlentities(chk_array($modelo->form_data, 'apelido')); ?>" required maxlength="255">
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="genero">Genêro</label>
										<select class="form-control" name="genero" id="genero" required>
											<option value="">Escolha</option>
											<option <?php if(htmlentities(chk_array($modelo->form_data, 'genero')) == 'Masculino'){ echo'selected'; } ?> value="Masculino">Masculino</option>
											<option <?php if(htmlentities(chk_array($modelo->form_data, 'genero')) == 'Feminino'){ echo'selected'; } ?> value="Feminino">Feminino</option>
											<option <?php if(htmlentities(chk_array($modelo->form_data, 'genero')) == 'Outros'){ echo'selected'; } ?> value="Outros">Outros</option>
										</select>
									</div>
								</div>
															
								<div class="col-md-4">
									<div class="form-group">
										<label for="dataNascimento">Data de Nascimento</label>
										<input type="text" class="form-control datetimepicker datetimepicker-input" data-toggle="datetimepicker" data-target="#dataNascimento" id="dataNascimento" name="dataNascimento" placeholder="" value="<?php if(!empty(chk_array($modelo->form_data, 'dataNascimento'))){ echo implode("/", array_reverse(explode("-", chk_array($modelo->form_data, 'dataNascimento')))); } ?>">
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="observacoes">Observações</label>
								<textarea class="form-control" rows="3" placeholder="" name="observacoes" id="observacoes"><?php echo htmlentities(chk_array($modelo->form_data, 'observacoes') ?? ''); ?></textarea>
							</div>
						</div>

						<div class="card-footer">
							<button type="submit" class="btn btn-primary">Salvar</button>
						</div>
					</form>
				</div>
			</section>