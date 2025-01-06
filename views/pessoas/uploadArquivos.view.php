<?php
if (!defined('ABSPATH')) exit;

require_once ABSPATH . '/views/pessoas/mini-perfil.inc.view.php';

$hash = chk_array($parametros, 1);
$id = decryptHash($hash);
?>

<section class="content">
    <div class="card">
        <div class="card-header bg-secondary">
            <h3 class="card-title">Gerenciador de Arquivos</h3>
        </div>
        <div class="card-body">
            <form id="fileupload" action="<?php echo HOME_URI; ?>/upload/pessoas/diversos/<?php echo $hash; ?>" method="POST" enctype="multipart/form-data">
                <!-- Botões de Controle -->
                <div class="btn-group mb-4">
                    <span class="btn btn-success rounded-pill fileinput-button me-2">
                        <i class="fas fa-plus-circle me-1"></i>
                        <span>Adicionar arquivos</span>
                        <input type="file" name="midia" multiple>
                    </span>
                    <button type="button" class="btn btn-primary rounded-pill start me-2"> <!-- Mudei de submit para button -->
                        <i class="fas fa-cloud-upload-alt me-1"></i>
                        <span>Iniciar upload</span>
                    </button>
                    <button type="reset" class="btn btn-warning rounded-pill cancel me-2">
                        <i class="fas fa-ban me-1"></i>
                        <span>Cancelar</span>
                    </button>
                    <button type="button" class="btn btn-danger rounded-pill delete">
                        <i class="fas fa-trash-alt me-1"></i>
                        <span>Excluir selecionados</span>
                    </button>
                    <div class="ms-3 d-flex align-items-center">
                        <input type="checkbox" class="toggle form-check-input" id="selectAll">
                        <label for="selectAll" class="ms-2">Selecionar todos</label>
                    </div>
                </div>

                <!-- Barra de Progresso Global -->
                <div class="progress mb-4" style="height: 25px; display: none;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                        role="progressbar" style="width: 0%;">
                        <span class="progress-text">0%</span>
                    </div>
                </div>

                <!-- Lista de Arquivos -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">Preview</th>
                                <th>Nome do Arquivo</th>
                                <th width="120">Tamanho</th>
                                <th width="200">Progresso</th>
                                <th width="120">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="files"></tbody>
                    </table>
                </div>

                <!-- Listagem de Diretórios -->
                <div class="directory-list mt-4">
                    <h5 class="mb-3">Diretórios</h5>
                    <div class="list-group" id="directoryList">
                        <!-- Será preenchido via AJAX -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Template de Upload -->
<script id="template-upload" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name mb-1">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size mb-0">{%=o.formatFileSize(file.size)%}</p>
        </td>
        <td>
            <div class="progress" style="height: 20px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" 
                     role="progressbar" style="width:0%">
                    <span class="progress-text">0%</span>
                </div>
            </div>
        </td>
        <td>
            <div class="btn-group">
                {% if (!i && !o.options.autoUpload) { %}
                    <button class="btn btn-sm btn-primary rounded-pill start">
                        <i class="fas fa-upload"></i>
                    </button>
                {% } %}
                <button class="btn btn-sm btn-warning rounded-pill cancel ms-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </td>
    </tr>
{% } %}
</script>

<!-- Template de Download -->
<script id="template-download" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" class="preview-link">
                        <img src="{%=file.thumbnailUrl%}" class="img-thumbnail">
                    </a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name mb-1">
                {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" class="text-decoration-none">
                        {%=file.name%}
                    </a>
                {% } else { %}
                    <span>{%=file.name%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
                <div><span class="badge bg-danger">Erro</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td></td>
        <td>
            <div class="btn-group">
                {% if (file.deleteUrl) { %}
                    <button class="btn btn-sm btn-danger rounded-pill delete" 
                            data-type="{%=file.deleteType%}" 
                            data-url="{%=file.deleteUrl%}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    <input type="checkbox" name="delete" value="1" class="toggle ms-2">
                {% } %}
            </div>
        </td>
    </tr>
{% } %}
</script>

<script>
    $(function() {
        'use strict';

        // Função para carregar diretórios
        var homeUri = "<?php echo HOME_URI; ?>";

        function loadDirectories() {
            $.get(homeUri + '/upload/listDirectories/' + $('#fileupload').data('hash'), function(data) {
                var html = '';
                data.directories.forEach(function(dir) {
                    html += `
                <a href="#" class="list-group-item list-group-item-action directory-item" 
                   data-path="${dir.path}">
                    <i class="fas fa-folder me-2"></i>
                    ${dir.name}
                    <small class="text-muted">(${dir.files} arquivos)</small>
                </a>`;
                });
                $('#directoryList').html(html);
            });
        }

        // Configuração do jQuery File Upload
        $('#fileupload').fileupload({
            url: $('#fileupload').attr('action'),
            dataType: 'json',
            autoUpload: false,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png|pdf|doc|docx|xls|xlsx)$/i,
            maxFileSize: 10000000, // 10MB
            paramName: 'midia',
            formData: []
        });

        // Manipuladores de eventos
        $('#fileupload')
            .on('fileuploadadd', function(e, data) {
                $('.progress').show();
                console.log('Arquivo adicionado:', data.files);
            })
            .on('fileuploadsubmit', function(e, data) {
                console.log('Enviando:', data);
                return true;
            })
            .on('fileuploadprogressall', function(e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('.progress-bar')
                    .css('width', progress + '%')
                    .find('.progress-text')
                    .text(progress + '%');
            })
            .on('fileuploaddone', function(e, data) {
                console.log('Upload concluído:', data.result);
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'Arquivo(s) enviado(s) com sucesso!',
                    icon: 'success'
                });
            })
            .on('fileuploadfail', function(e, data) {
                console.error('Erro no upload:', data);
                Swal.fire({
                    title: 'Erro!',
                    text: 'Erro ao enviar arquivo(s)',
                    icon: 'error'
                });
            });

        // Botão de envio
        $('.start').on('click', function(e) {
            e.preventDefault();
            console.log('Botão de envio clicado');
            var $this = $(this);
            var data = $('#fileupload').data('blueimp-fileupload') || $('#fileupload').data('fileupload');
            if (data && data.files.length > 0) {
                data.process().done(function() {
                    data.submit();
                });
            }
        });

        // Botão de cancelar
        $('.cancel').on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            var data = $('#fileupload').data('blueimp-fileupload') || $('#fileupload').data('fileupload');
            if (data && data.files.length > 0) {
                data.abort();
                $('.progress').hide();
                $('.progress-bar').css('width', '0%');
            }
        });

        // Eventos de diretório
        $(document).on('click', '.directory-item', function(e) {
            e.preventDefault();
            var path = $(this).data('path');
            // Implementar navegação de diretório
        });

        // Carregar diretórios inicialmente
        loadDirectories();
    });
</script>

<style>
    .fileinput-button {
        position: relative;
        overflow: hidden;
    }

    .fileinput-button input {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        opacity: 0;
        font-size: 200px;
        direction: ltr;
        cursor: pointer;
    }

    .preview img {
        max-width: 50px;
        max-height: 50px;
        border-radius: 4px;
    }

    .template-download td,
    .template-upload td {
        vertical-align: middle;
    }

    .progress {
        border-radius: 20px;
        overflow: hidden;
    }

    .directory-item {
        transition: all 0.2s ease;
    }

    .directory-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }

    .btn-group .btn {
        margin-right: 5px;
    }
</style>