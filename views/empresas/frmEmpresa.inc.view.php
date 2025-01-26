<?php
if (!defined('ABSPATH')) exit;

$modelo->validarFormEmpresa();

$hash = chk_array($parametros, 1);

?>
<?php require_once ABSPATH . '/views/empresas/mini-perfil.inc.view.php'; ?>
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
							<label for="token">Token de Cadastro</label>
							<div class="input-group">
								<input type="text" class="form-control" id="token" name="token" placeholder="" value="<?php echo htmlentities(chk_array($modelo->form_data, 'token')); ?>" required maxlength="255">
								<div class="input-group-append">
									<button type="button" class="btn btn-secondary" onclick="generateToken('token')">Gerar Token</button>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-12">
						<div class="form-group">
							<label for="accessToken">Token de Acesso</label>
							<div class="input-group">
								<input type="text" class="form-control" id="accessToken" name="accessToken" placeholder="" value="<?php echo htmlentities(chk_array($modelo->form_data, 'accessToken')); ?>" required maxlength="255">
								<div class="input-group-append">
									<button type="button" class="btn btn-secondary" onclick="generateToken('accessToken')">Gerar Token</button>
								</div>
							</div>
						</div>
					</div>

					<!-- id do Usuario hidden -->
					<input type="hidden" name="idUsuarioCriacao" value="<?php echo $_SESSION['userdata']['id']; ?>">

				</div>

				<div class="form-group">
					<label for="observacoes">Observações</label>
					<textarea class="form-control" rows="3" placeholder="" name="observacoes" id="observacoes"><?php echo htmlentities(chk_array($modelo->form_data, 'observacoes')); ?></textarea>
				</div>

				<div class="form-group">
					<label for="avatar">Foto/avatar</label>
					<div id="upload-container">
						<span class="btn btn-success fileinput-button">
							<i class="fas fa-plus"></i>
							<span>Selecionar foto...</span>
							<input id="fileupload" type="file" name="midia" accept="image/*">
						</span>
					</div>
					<div id="preview" class="mt-3"></div>
					<p class="help-block">Formatos aceitos: JPG, PNG e GIF</p>
				</div>

			</div>

			<div class="card-footer">
				<button type="submit" class="btn btn-primary">Salvar</button>
			</div>
		</form>
	</div>
</section>

<script>
	function generateToken(id) {
		const length = 100;
		const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		let token = '';
		for (let i = 0; i < length; i++) {
			token += characters.charAt(Math.floor(Math.random() * characters.length));
		}
		document.getElementById(id).value = token;
	}
</script>

<script>
	$(function() {
		'use strict';

		$('#fileupload').fileupload({
			url: '<?php echo HOME_URI; ?>/upload/empresas/avatar/<?php echo $hash; ?>',
			dataType: 'json',
			autoUpload: true,
			paramName: 'midia',
			maxFileSize: 5000000, // 5MB
			disableImageLoad: true, // Desabilita o carregamento de metadados da imagem
			disableImagePreview: false, // Mantém o preview
			disableImageMetadata: true, // Desabilita metadados
			disableImageValidation: false, // Mantém validação básica
			processQueue: [{
				action: 'validate',
				// Validação básica
				acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
				maxFileSize: 5000000 // 5MB
			}],

			// Quando inicia o upload
			send: function(e, data) {
				$('#preview').html(`
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" 
                         style="width: 0%">
                        0%
                    </div>
                </div>
            `);
			},

			// Durante o upload
			progress: function(e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				$('.progress-bar').css('width', progress + '%')
					.text(progress + '%');
			},

			// Upload concluído
			done: function(e, data) {
				if (data.result && data.result.midia && data.result.midia[0]) {
					var file = data.result.midia[0];

					// Se temos uma URL, o upload foi bem sucedido
					if (file.url) {
						var html = `
                        <div class="alert alert-success alert-dismissible fade show mb-2">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="fas fa-check-circle"></i> Upload realizado com sucesso!
                        </div>
                        <div class="uploaded-file">
                            <img src="${file.url}" alt="Preview" class="img-thumbnail mb-2" style="max-width: 200px">
                            <div>
                                <button type="button" class="btn btn-danger btn-sm delete-file" 
                                        data-filename="${file.name}" data-url="${file.deleteUrl}">
                                    <i class="fas fa-trash"></i> Remover
                                </button>
                            </div>
                        </div>
                    `;

						$('#preview').html(html);

						// Se houve erro no resize
						if (file.error) {
							$('#preview').append(`
                            <div class="alert alert-warning mt-2">
                                <i class="fas fa-exclamation-triangle"></i> 
                                A imagem foi enviada com sucesso, mas houve um problema com a miniatura.
                            </div>
                        `);
						}

						// Esconde os alertas após 5 segundos apenas se não for recarregar
						setTimeout(function() {
                            window.location.reload();
                        }, 1500);

					}
				}
			},

			// Erro no upload
			fail: function(e, data) {
				$('#preview').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i> Erro no upload. Por favor, tente novamente.
                </div>
            `);
			}
		});

		// Deletar arquivo
		$(document).on('click', '.delete-file', function(e) {
			e.preventDefault();
			var $this = $(this);
			var url = $this.data('url');

			if (confirm('Tem certeza que deseja remover esta imagem?')) {
				$.ajax({
					url: url,
					type: 'DELETE',
					success: function() {
						$('#preview').empty().html(`
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Imagem removida com sucesso!
                        </div>
                    `);
						setTimeout(function() {
							$('.alert').fadeOut('slow');
						}, 3000);
					},
					error: function() {
						$('#preview').append(`
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i> Erro ao remover imagem.
                        </div>
                    `);
					}
				});
			}
		});
	});
</script>