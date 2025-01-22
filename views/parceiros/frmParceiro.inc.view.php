<?php if (!defined('ABSPATH')) exit;

if (chk_array($this->parametros, 0) == 'editar' || chk_array($this->parametros, 0) == 'ver') {
    if (chk_array($this->parametros, 1)) {
        $hash = chk_array($this->parametros, 1);
        $id = decryptHash($hash);
    }

    $parceiro = $modelo->getParceiro($id);

    if (!empty($modelo->form_msg) && preg_match('/(inexistente|encontrado)/simx', $modelo->form_msg)) {
        echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/parceiros">';
        echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/parceiros";</script>';
        exit;
    }
} else {
    $parceiro = null;
}


$idParceiroUsuario = $modelo->getIdParceiroUsuario($_SESSION['userdata']['idUsuario']);
if (isset($_POST['idEmpresas'])) {
    $modelo->validarParceiro();
}

$edicao = true;
if (chk_array($this->parametros, 0) == 'ver') {
    $edicao = false;
}

$empresas = $modeloEmpresa->getEmpresas();
$empresasSelecionadas = $modelo->getEmpresasDoParceiro($parceiro['id'] ?? null);

?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?php echo chk_array($this->parametros, 0) == 'editar' ? 'Editar Parceiro' : 'Adicionar Parceiro'; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>/parceiros">Parceiros</a></li>
                        <li class="breadcrumb-item active"><?php echo chk_array($this->parametros, 0) == 'editar' ? 'Editar Parceiro' : 'Adicionar Parceiro'; ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title"><?php echo chk_array($this->parametros, 0) == 'editar' ? 'Editar Parceiro' : 'Adicionar Parceiro'; ?></h3>
            </div>
            <form role="form" action="" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                    <?php echo $modelo->form_msg; ?>
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Nome do Parceiro -->
                            <div class="form-group">
                                <label for="nomeParceiro">Nome Parceria</label>
                                <input type="text" class="form-control" id="nomeParceiro" name="nomeParceiro" placeholder="Digite o nome"
                                    value="<?php echo htmlspecialchars(chk_array($parceiro, 'nomeParceiro') ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    required maxlength="255"
                                    <?php echo !$edicao ? 'disabled' : ''; ?>>
                            </div>
                        </div>
                        <div class="col-md-3">

                            <?php
                            $disabled = 'disabled'; // Por padrão, o campo é desabilitado.
                            if ((chk_array($this->parametros, 0) === 'adicionar') || (chk_array($this->parametros, 0) === 'editar' && $parceiro['idParceiro'] === $idParceiroUsuario['idParceiro'])) {
                                $disabled = ''; // Campo habilitado se estiver editando e os IDs forem iguais.
                            }
                            ?>

                            <!-- Tipo de Parceiro -->
                            <div class="form-group">
                                <label for="tipo">Tipo</label>
                                <select class="form-control" id="tipo" name="tipo"
                                    <?php echo $disabled; ?>


                                    required>
                                    <option value="">Selecione um tipo</option>
                                    <?php
                                    $tipos = $modelo->getTiposParceiros();
                                    $tipoSelecionado = $parceiro['tipo'] ?? null;

                                    foreach ($tipos as $tipo) {
                                        $selected = $tipo == $tipoSelecionado ? 'selected' : '';
                                        echo "<option value='$tipo' $selected>" . ucfirst(strtolower($tipo)) . "</option>";
                                    }
                                    ?>
                                </select>
                                <?php if (chk_array($this->parametros, 0) == 'editar'): ?>
                                    <!-- Campo oculto para enviar o tipo no modo de edição -->
                                    <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($tipoSelecionado, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php endif; ?>
                            </div>

                        </div>

                        <div class="col-md-3">
                            <!-- Qtd de Revendas -->
                            <div class="form-group">
                                <label for="qtdRevenda">Qtd. Revendas</label>
                                <input type="number" class="form-control" id="qtdRevenda" name="qtdRevenda" placeholder="Digite a quantidade"
                                    value="<?php echo htmlspecialchars(chk_array($parceiro, 'qtdRevenda') ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    min="0" max="9999">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <!-- Empresas Associadas -->

                            <!-- Empresas Associadas -->
                            <div class="form-group">
                                <label for="idEmpresas">Empresas</label>
                                <select class="form-control select2" id="idEmpresas" name="idEmpresas[]" multiple required
                                    data-minimum-input-length="3"
                                    data-ajax--url="<?php echo HOME_URI; ?>/apiinterna/buscarEmpresas"
                                    data-ajax--delay="300"
                                    data-ajax--cache="true"

                                    <?php echo !$edicao ? 'disabled' : ''; ?>>
                                    <?php
                                    // Load only pre-selected companies initially
                                    if (!empty($empresasSelecionadas)) {
                                        foreach ($empresas as $empresa) {
                                            if (in_array($empresa['id'], $empresasSelecionadas)) {
                                                echo "<option value='{$empresa['id']}' selected>{$empresa['razaoSocial']}</option>";
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Campo oculto para o ID do parceiro -->
                            <input type="hidden" name="idParceiro"
                                value="<?php
                                        if (chk_array($this->parametros, 0) === 'editar') {
                                            // No modo de edição, use o ID do parceiro salvo
                                            echo htmlspecialchars($parceiro['idParceiro'] ?? '', ENT_QUOTES, 'UTF-8');
                                        } else {
                                            // No modo de criação, use o ID do parceiro associado ao usuário
                                            echo htmlspecialchars($idParceiroUsuario['idParceiro'] ?? '', ENT_QUOTES, 'UTF-8');
                                        }
                                        ?>">


                            <!-- Token -->
                            <!-- <div class="form-group">
                                <label for="token">Token</label>
                                <input type="text" class="form-control" id="token" name="token" placeholder="Digite o token"
                                    value="<?php echo htmlspecialchars(chk_array($parceiro, 'token') ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    required maxlength="255"
                                    <?php echo !$edicao ? 'disabled' : ''; ?>>
                            </div> -->

                            <!-- Observações -->
                            <div class="form-group">
                                <label for="observacoes">Observações</label>
                                <textarea class="form-control" rows="3" placeholder="Adicione observações" name="observacoes" id="observacoes"
                                    <?php echo !$edicao ? 'disabled' : ''; ?>><?php echo htmlspecialchars(chk_array($parceiro, 'observacoes') ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success"><?php echo chk_array($this->parametros, 0) == 'editar' ? 'Salvar Alterações' : 'Adicionar Parceiro'; ?></button>
                    <a href="<?php echo HOME_URI; ?>/parceiros" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
    // Se o tipo de parceiro for 'Revenda', exibir o campo de quantidade de revendas
    $(document).ready(function() {
        if ($(this).val() === 'REVENDA') {
            $('#qtdRevenda').parent().show();
        } else {
            $('#qtdRevenda').parent().hide();
        }
        $('#tipo').change(function() {
            if ($(this).val() === 'REVENDA') {
                $('#qtdRevenda').parent().show();
            } else {
                $('#qtdRevenda').parent().hide();
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Inicializa o Select2 com configuração refinada
        $('#idEmpresas').select2({
            minimumInputLength: 3, // Mínimo de caracteres para iniciar a busca
            allowClear: true, // Permitir limpar a seleção
            ajax: {
                url: '<?php echo HOME_URI; ?>/apiinterna/buscarEmpresas', // URL da API
                type: 'GET',
                dataType: 'json',
                delay: 300, // Atraso antes de enviar a requisição
                data: function(params) {
                    return {
                        term: params.term // Termo de busca enviado para a API
                    };
                },
                processResults: function(data) {
                    console.log("Dados retornados pela API:", data); // Log para depuração
                    if (data && data.items) {
                        return {
                            results: data.items.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.razaoSocial // Exibe a razão social
                                };
                            })
                        };
                    }
                    return {
                        results: []
                    }; // Caso não haja resultados
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Erro na requisição AJAX:", textStatus, errorThrown);
                }
            },
            language: {
                inputTooShort: function() {
                    return "Digite pelo menos 3 caracteres para buscar...";
                },
                noResults: function() {
                    return "Nenhuma empresa encontrada.";
                },
                searching: function() {
                    return "Buscando...";
                }
            }
        });

        // Verifica se o Select2 está funcionando corretamente
        $('#idEmpresas').on('select2:select', function(e) {
            console.log("Empresa selecionada:", e.params.data);
        });

        // Formatação dos resultados exibidos no dropdown
        function formatCompany(company) {
            if (!company.id) {
                return company.text; // Placeholder
            }
            return $(
                `<div class="d-flex flex-column">
                <div class="font-weight-bold">${company.text}</div>
            </div>`
            );
        }

        // Formatação do item selecionado
        function formatCompanySelection(company) {
            return company.text || company.id;
        }
    });
</script>