<?php
if (!defined('ABSPATH')) exit;

// Recupera o ID da impressora
$hash = chk_array($this->parametros, 1);
$idImpressora = $hash ? decryptHash($hash) : null;

// Se for POST, valida o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelo->validarFormImpressora();
}

// Carrega os dados da impressora se for edição
$impressora = $idImpressora ? $modelo->getImpressora($idImpressora) : null;

// Carrega as listas de marcas e departamentos
$marcas = $modelo->getMarcas();
$departamentos = $modelo->getDepartamentos();
?>

<div class="content-wrapper">
    <!-- Cabeçalho de conteúdo -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?php echo $idImpressora ? 'Editar Impressora' : 'Cadastrar Impressora'; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>/impressoras">Impressoras</a></li>
                        <li class="breadcrumb-item active"><?php echo $idImpressora ? 'Editar Impressora' : 'Cadastrar Impressora'; ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <?php
    // Exibe mensagens de validação, se houver
    echo $modelo->form_msg;
    ?>

    <!-- Formulário de Cadastro/Edição de Impressora -->
    <section class="content">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title"><?php echo $idImpressora ? 'Editar Impressora' : 'Cadastrar Impressora'; ?></h3>
            </div>
            <form role="form" action="" method="POST">
                <div class="card-body">
                    <div class="row">
                        <!-- Nome da Impressora -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nome">Nome da Impressora</label>
                                <input type="text" class="form-control" name="nome" id="nome" value="<?php echo htmlentities($impressora['nome'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <!-- IP da Impressora -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ip">Endereço IP</label>
                                <input type="text" class="form-control" name="ip" id="ip" value="<?php echo htmlentities($impressora['ip'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Marca da Impressora -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="idMarca">Marca</label>
                                <div class="input-group">
                                    <select class="form-control" name="idMarca" id="idMarca" required>
                                        <option value="">Selecione uma marca</option>
                                        <?php foreach ($marcas as $marca): ?>
                                            <option value="<?php echo $marca['id']; ?>" <?php echo isset($impressora['idMarca']) && $impressora['idMarca'] == $marca['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlentities($marca['nome']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-secondary" onclick="abrirModalMarca()">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Departamento da Impressora -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="idDepartamento">Departamento</label>
                                <div class="input-group">
                                    <select class="form-control" name="idDepartamento" id="idDepartamento" required>
                                        <option value="">Selecione um departamento</option>
                                        <?php foreach ($departamentos as $departamento): ?>
                                            <option value="<?php echo $departamento['id']; ?>" <?php echo isset($impressora['idDepartamento']) && $impressora['idDepartamento'] == $departamento['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlentities($departamento['nome']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-secondary" onclick="abrirModalDepartamento()">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- idEmpresa hidden -->
                    <input type="hidden" name="idEmpresa" value="<?php echo $_SESSION['userdata']['idEmpresa']; ?>">

                    <!-- Modelo e Descrição -->
                    <div class="form-group">
                        <label for="modelo">Modelo</label>
                        <input type="text" class="form-control" name="modelo" id="modelo" value="<?php echo htmlentities($impressora['modelo'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="descricao">Descrição</label>
                        <textarea class="form-control" name="descricao" id="descricao" rows="4"><?php echo htmlentities($impressora['descricao'] ?? ''); ?></textarea>
                    </div>

                    <!-- ID oculto para edição -->
                    <?php if ($idImpressora): ?>
                        <input type="hidden" name="idImpressora" value="<?php echo encryptId($idImpressora); ?>">
                    <?php endif; ?>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><?php echo $idImpressora ? 'Salvar Alterações' : 'Cadastrar Impressora'; ?></button>
                    <a href="<?php echo HOME_URI; ?>/impressoras" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </section>
</div>

<!-- Modal para adicionar nova marca -->
<div class="modal fade" id="modalMarca" tabindex="-1" role="dialog" aria-labelledby="modalMarcaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalMarcaLabel">Adicionar Marca</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="novaMarca">Nome da Marca</label>
                    <input type="text" class="form-control" name="novaMarca" id="novaMarca" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnAdicionarMarca">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para adicionar novo departamento -->
<div class="modal fade" id="modalDepartamento" tabindex="-1" role="dialog" aria-labelledby="modalDepartamentoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDepartamentoLabel">Adicionar Departamento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="novoDepartamento">Nome do Departamento</label>
                    <input type="text" class="form-control" name="novoDepartamento" id="novoDepartamento" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnAdicionarDepartamento">Salvar</button>
            </div>
        </div>
    </div>
</div>


<script>
        function abrirModalMarca() {
            $('#modalMarca').modal('show');
        }

        function abrirModalDepartamento() {
            $('#modalDepartamento').modal('show');
        }

    $(document).ready(function() {

        // Adicionar Marca
        $('#btnAdicionarMarca').click(function() {
            const novaMarca = $('#novaMarca').val().trim();

            if (!novaMarca) {
                toastr.error('Por favor, insira o nome da marca.');
                return;
            }

            $.ajax({
                url: '<?php echo HOME_URI; ?>/impressoras/index/adicionarMarca',
                method: 'POST',
                data: {
                    novaMarca
                },
                success: function(data) {
                    let response = JSON.parse(data);

                    if (response.success) {
                        toastr.success('Marca adicionada com sucesso!');
                        $('#novaMarca').val('');
                        $('#modalMarca').modal('hide');
                        atualizarMarcas();
                    } else {
                        toastr.error(response.message || 'Erro ao adicionar marca.');
                    }
                },
                error: function() {
                    toastr.error('Erro ao processar a solicitação.');
                }
            });
        });

        // Adicionar Departamento
        $('#btnAdicionarDepartamento').click(function() {
            const novoDepartamento = $('#novoDepartamento').val().trim();

            if (!novoDepartamento) {
                toastr.error('Por favor, insira o nome do departamento.');
                return;
            }

            $.ajax({
                url: '<?php echo HOME_URI; ?>/impressoras/index/adicionarDepartamento',
                method: 'POST',
                data: {
                    novoDepartamento
                },
                success: function(data) {
                    let response = JSON.parse(data);
                    if (response.success) {
                        toastr.success('Departamento adicionado com sucesso!');
                        $('#novoDepartamento').val('');
                        $('#modalDepartamento').modal('hide');
                        atualizarDepartamentos();
                    } else {
                        toastr.error(response.message || 'Erro ao adicionar departamento.');
                    }
                },
                error: function() {
                    toastr.error('Erro ao processar a solicitação.');
                }
            });
        });

        // Atualizar lista de Marcas
        function atualizarMarcas() {
            $.ajax({
                url: '<?php echo HOME_URI; ?>/impressoras/index/getMarcas',
                method: 'GET',
                success: function(data) {
                    let response = JSON.parse(data);
                    const selectMarca = $('#idMarca');
                    selectMarca.empty();
                    selectMarca.append('<option value="">Selecione uma marca</option>');

                    response.forEach(marca => {
                        selectMarca.append(`<option value="${marca.id}">${marca.nome}</option>`);
                    });
                },
                error: function() {
                    toastr.error('Erro ao atualizar lista de marcas.');
                }
            });
        }

        // Atualizar lista de Departamentos
        function atualizarDepartamentos() {
            $.ajax({
                url: '<?php echo HOME_URI; ?>/impressoras/index/getDepartamentos',
                method: 'GET',
                success: function(data) {
                    let response = JSON.parse(data);
                    const selectDepartamento = $('#idDepartamento');
                    selectDepartamento.empty();
                    selectDepartamento.append('<option value="">Selecione um departamento</option>');

                    response.forEach(departamento => {
                        selectDepartamento.append(`<option value="${departamento.id}">${departamento.nome}</option>`);
                    });
                },
                error: function() {
                    toastr.error('Erro ao atualizar lista de departamentos.');
                }
            });
        }
    });
</script>