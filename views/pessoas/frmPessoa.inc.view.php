<?php
if (!defined('ABSPATH')) exit;

$modelo->validarFormPessoa();
?>
<?php require_once ABSPATH . '/views/pessoas/mini-perfil.inc.view.php'; ?>

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
								<option <?php if (htmlentities(chk_array($modelo->form_data, 'genero')) == 'Masculino') {
											echo 'selected';
										} ?> value="Masculino">Masculino</option>
								<option <?php if (htmlentities(chk_array($modelo->form_data, 'genero')) == 'Feminino') {
											echo 'selected';
										} ?> value="Feminino">Feminino</option>
								<option <?php if (htmlentities(chk_array($modelo->form_data, 'genero')) == 'Outros') {
											echo 'selected';
										} ?> value="Outros">Outros</option>
							</select>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label for="dataNascimento">Data de Nascimento</label>
							<input type="text" class="form-control datetimepicker datetimepicker-input" data-toggle="datetimepicker" data-target="#dataNascimento" id="dataNascimento" name="dataNascimento" placeholder="" value="<?php if (!empty(chk_array($modelo->form_data, 'dataNascimento'))) {
																																																										echo implode("/", array_reverse(explode("-", chk_array($modelo->form_data, 'dataNascimento'))));
																																																									} ?>">
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="observacoes">Observações</label>
					<textarea class="form-control" rows="3" placeholder="" name="observacoes" id="observacoes"><?php echo htmlentities(chk_array($modelo->form_data, 'observacoes') ?? ''); ?></textarea>
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
$(function() {
    'use strict';

	// Verificação se é o próprio usuário
    const isOwnProfile = '<?php echo $_SESSION['userdata']['id']; ?>' === '<?php echo $modelo->form_data['id']; ?>';

    $('#fileupload').fileupload({
        url: '<?php echo HOME_URI; ?>/upload/pessoas/avatar/<?php echo encryptId($modelo->form_data['id']); ?>',
        dataType: 'json',
        autoUpload: true,
        paramName: 'midia',
        maxFileSize: 5000000, // 5MB
        disableImageLoad: true, // Desabilita o carregamento de metadados da imagem
        disableImagePreview: false, // Mantém o preview
        disableImageMetadata: true, // Desabilita metadados
        disableImageValidation: false, // Mantém validação básica
        processQueue: [
            {
                action: 'validate',
                // Validação básica
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                maxFileSize: 5000000 // 5MB
            }
        ],
        
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

                     // Se for o próprio usuário, recarrega a página após 1.5 segundos
					 if (isOwnProfile) {
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        // Esconde os alertas após 5 segundos apenas se não for recarregar
                        setTimeout(function() {
                            $('.alert').fadeOut('slow');
                        }, 5000);
                    }
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
        
        if(confirm('Tem certeza que deseja remover esta imagem?')) {
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