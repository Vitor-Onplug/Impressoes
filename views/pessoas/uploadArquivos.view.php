<?php
if (!defined('ABSPATH')) exit;
?>

<?php require_once ABSPATH . '/views/pessoas/mini-perfil.inc.view.php'; ?>

<link rel="stylesheet" href="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/css/blueimp-gallery.min.css">
<link rel="stylesheet" href="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/css/jquery.fileupload.css">
<link rel="stylesheet" href="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/css/jquery.fileupload-ui.css">
<noscript>
    <link rel="stylesheet" href="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/css/jquery.fileupload-noscript.css">
</noscript>
<noscript>
    <link rel="stylesheet" href="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/css/jquery.fileupload-ui-noscript.css">
</noscript>

<section class="content">
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Arquivos</h3>
        </div>
        <form id="fileupload" role="form" action="<?php echo HOME_URI; ?>/pessoas/upload/index/diversos/<?php echo chk_array($parametros, 1); ?>" method="POST" enctype="multipart/form-data">
            <div class="card-body">
                <?php echo $modelo->form_msg; ?>
                
                <noscript>
                    <input type="hidden" name="redirect" value="<?php echo HOME_URI; ?>">
                </noscript>
                
                <div class="row fileupload-buttonbar">
                    <div class="col-lg-7">
                        <span class="btn btn-success fileinput-button">
                            <i class="glyphicon glyphicon-plus"></i>
                            <span>Adicionar...</span>
                            <input type="file" name="files[]" multiple>
                        </span>
                        <button type="submit" class="btn btn-primary start">
                            <i class="glyphicon glyphicon-upload"></i>
                            <span>Enviar</span>
                        </button>
                        <button type="reset" class="btn btn-warning cancel">
                            <i class="glyphicon glyphicon-ban-circle"></i>
                            <span>Cancelar</span>
                        </button>
                        <button type="button" class="btn btn-danger delete">
                            <i class="glyphicon glyphicon-trash"></i>
                            <span>Remover</span>
                        </button>
                        <input type="checkbox" class="toggle">
                        <span class="fileupload-process"></span>
                    </div>

                    <div class="col-lg-5 fileupload-progress fade">
                        <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                        </div>
                        <div class="progress-extended">&nbsp;</div>
                    </div>
                </div>

                <table role="presentation" class="table table-striped">
                    <tbody class="files"></tbody>
                </table>

                <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
                    <div class="slides"></div>
                    <h3 class="title"></h3>
                    <a class="prev">‹</a>
                    <a class="next">›</a>
                    <a class="close">×</a>
                    <a class="play-pause"></a>
                    <ol class="indicator"></ol>
                </div>

                <script id="template-upload" type="text/x-tmpl">
                    {% for (var i=0, file; file=o.files[i]; i++) { %}
                        <tr class="template-upload fade">
                            <td>
                                <span class="preview"></span>
                            </td>
                            <td>
                                <p class="name">{%=file.name%}</p>
                                <strong class="error text-danger"></strong>
                            </td>
                            <td>
                                <p class="size">Carregando...</p>
                                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                                </div>
                            </td>
                            <td>
                                {% if (!i && !o.options.autoUpload) { %}
                                    <button class="btn btn-primary start" disabled>
                                        <i class="glyphicon glyphicon-upload"></i>
                                        <span>Enviar</span>
                                    </button>
                                {% } %}
                                {% if (!i) { %}
                                    <button class="btn btn-warning cancel">
                                        <i class="glyphicon glyphicon-ban-circle"></i>
                                        <span>Cancelar</span>
                                    </button>
                                {% } %}
                            </td>
                        </tr>
                    {% } %}
                </script>

                <script id="template-download" type="text/x-tmpl">
                    {% for (var i=0, file; file=o.files[i]; i++) { %}
                        <tr class="template-download fade">
                            <td>
                                <span class="preview">
                                    {% if (file.thumbnailUrl) { %}
                                        <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery>
                                            <img src="{%=file.thumbnailUrl%}">
                                        </a>
                                    {% } %}
                                </span>
                            </td>
                            <td>
                                <p class="name">
                                    {% if (file.url) { %}
                                        <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                                    {% } else { %}
                                        <span>{%=file.name%}</span>
                                    {% } %}
                                </p>
                                {% if (file.error) { %}
                                    <div><span class="label label-danger">Erro</span> {%=file.error%}</div>
                                {% } %}
                            </td>
                            <td>
                                <span class="size">{%=o.formatFileSize(file.size)%}</span>
                            </td>
                            <td>
                                {% if (file.deleteUrl) { %}
                                    <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                                        <i class="glyphicon glyphicon-trash"></i>
                                        <span>Remover</span>
                                    </button>
                                    <input type="checkbox" name="delete" value="1" class="toggle">
                                {% } else { %}
                                    <button class="btn btn-warning cancel">
                                        <i class="glyphicon glyphicon-ban-circle"></i>
                                        <span>Cancelar</span>
                                    </button>
                                {% } %}
                            </td>
                        </tr>
                    {% } %}
                </script>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</section>

<script>
	$('#fileupload').on('submit', function(e){
		e.preventDefault();
		
	});
</script>

<script src="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/js/vendor/jquery.ui.widget.js"></script>
<script src="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/js/tmpl.min.js"></script>
<script src="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/js/load-image.all.min.js"></script>
<script src="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/js/canvas-to-blob.min.js"></script>
<script src="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/js/jquery.blueimp-gallery.min.js"></script>
<script src="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/js/jquery.iframe-transport.js"></script>
<script src="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/js/jquery.fileupload.js"></script>
<script src="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/js/jquery.fileupload-process.js"></script>
<script src="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/js/jquery.fileupload-image.js"></script>
<script src="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/js/jquery.fileupload-audio.js"></script>
<script src="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/js/jquery.fileupload-video.js"></script>
<script src="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/js/jquery.fileupload-validate.js"></script>
<script src="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/js/jquery.fileupload-ui.js"></script>
<script src="<?php echo HOME_URI; ?>/views/standards/plugins/fileupload/js/main.js"></script>
