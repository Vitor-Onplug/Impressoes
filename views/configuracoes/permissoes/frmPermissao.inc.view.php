<?php
if (!defined('ABSPATH')) exit;

// Recupera o ID da permissão
$hash = chk_array($this->parametros, 1);
$idPermissao = $hash ? decryptHash($hash) : null;

// Se for POST, valida o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelo->validarFormPermissao();
}

// Carrega os dados da permissão se for edição
$permissao = $idPermissao ? $modelo->getPermissao($idPermissao) : null;
?>

<div class="content-wrapper">
    <!-- Cabeçalho de conteúdo -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?php echo $idPermissao ? 'Editar Permissão' : 'Cadastrar Permissão'; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>/configuracoes/permissoes">Permissões</a></li>
                        <li class="breadcrumb-item active"><?php echo $idPermissao ? 'Editar Permissão' : 'Nova Permissão'; ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <?php
    // Exibe mensagens de validação, se houver
    echo $modelo->form_msg;
    ?>

    <!-- Formulário de Cadastro/Edição de Permissão -->
    <section class="content">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title"><?php echo $idPermissao ? 'Editar Permissão' : 'Nova Permissão'; ?></h3>
            </div>
            <form role="form" action="" method="POST">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="permissao">Nome da Permissão</label>
                                <input type="text" class="form-control" id="permissao" name="permissao" 
                                       value="<?php echo htmlentities($permissao['permissao'] ?? ''); ?>" required>
                            </div>
                        </div>

						<input type="hidden" name="idEmpresa" value="<?php echo $_SESSION['userdata']['idEmpresa']; ?>">
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Módulos do Sistema</label>
                                <div class="permission-container">
                                    <?php
                                    $modulosSelecionados = [];
                                    if (isset($permissao['modulo'])) {
										
                                        $modulosSelecionados = explode(',', $permissao['modulo']);
                                    }

									$exibeSuper = $this->check_permissions('SUPERADMIN', $this->userdata['modulo']);
                                    
                                    foreach ($permissions as $id => $nome): 
										if (!$exibeSuper && $id == 1) continue; 
                                        $checked = in_array($id, $modulosSelecionados) ? 'checked' : '';
                                    ?>
                                        <div class="custom-control custom-checkbox permission-item">
                                            <input type="checkbox" 
                                                   class="custom-control-input" 
                                                   id="modulo_<?php echo $id; ?>" 
                                                   name="modulos[]" 
                                                   value="<?php echo $id; ?>"
                                                   <?php echo $checked; ?>>
                                            <label class="custom-control-label" for="modulo_<?php echo $id; ?>">
                                                <?php echo $nome; ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <small class="text-muted mt-2 d-block">
                                    <span id="selectedCount">0</span> módulos selecionados
                                </small>
                            </div>
                        </div>
                    </div>

                    <?php if ($idPermissao): ?>
                        <input type="hidden" name="id" value="<?php echo encryptId($idPermissao); ?>">
                    <?php endif; ?>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $idPermissao ? 'Salvar Alterações' : 'Cadastrar Permissão'; ?>
                    </button>
                    <a href="<?php echo HOME_URI; ?>/configuracoes/permissoes" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </section>
</div>

<style>
.permission-container {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 1rem;
    background: #fff;
}

.permission-item {
    margin-bottom: 0.5rem;
    padding: 0.5rem;
    border-radius: 0.25rem;
    transition: background-color 0.2s;
}

.permission-item:hover {
    background-color: #f8f9fa;
}
</style>

<script>
$(document).ready(function() {
    function updateSelectedCount() {
        const count = $('input[name="modulos[]"]:checked').length;
        $('#selectedCount').text(count);
    }

    updateSelectedCount();

    $('input[name="modulos[]"]').on('change', function() {
        updateSelectedCount();
    });
    
    $('form').on('submit', function(e) {
        e.preventDefault();
        const selectedModules = [];
        $('input[name="modulos[]"]:checked').each(function() {
            selectedModules.push($(this).val());
        });
        
        $('<input>').attr({
            type: 'hidden',
            name: 'modulos',
            value: selectedModules.join(',')
        }).appendTo(this);
        
        this.submit();
    });
});
</script>