<?php
if (!defined('ABSPATH')) exit;

// Recupera o hash do lote, se disponível (edição)
$hash = chk_array($parametros, 1);
$idLote = !empty($hash) ? decryptHash($hash) : null;
$hashEvento = $_SESSION['idEventoHash'];
$idEvento = decryptHash($hashEvento);

// Se for post, valida o formulário
if (isset($_POST['salvar_lote'])) {
    $modeloLotes->validarFormRelacoes();
}

// Carrega os dados do lote, setores e períodos, se houver (edição)
$lote = $idLote ? $modeloLotes->getLote($idLote) : [];
$setores = $modeloSetores->getSetores($idEvento); // Setores disponíveis no evento
$setoresSelecionados = $idLote ? $modeloLotes->getSetoresLote($idLote) : []; // Setores permitidos no lote
$periodos = $idLote ? $modeloLotes->getPeriodosLote($idLote) : [];

?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Relações do Lote de Credenciais: <?php echo htmlentities(chk_array($lote, 'nomeLote')); ?> </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>/eventos/index/lotes">Lotes de Credenciais</a></li>
                        <li class="breadcrumb-item active"> Relações do Lote de Credenciais</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <?php echo $modeloLotes->form_msg; ?>

    <section class="content">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Períodos de Acesso</h3>
            </div>
            <form role="form" action="" method="POST">
                <div class="card-body">

                    <!-- Períodos de Acesso -->
                    <div class="row">
                        <div class="col-md-12">
                            <div id="periodos">
                                <?php if (!empty($periodos)): ?>
                                    <?php foreach ($periodos as $index => $periodo): ?>
                                        <div class="row periodo-item mb-2">
                                            <div class="col-md-5">
                                                <input type="datetime-local" class="form-control" name="periodos[<?php echo $index; ?>][dataInicio]" value="<?php echo date('Y-m-d\TH:i:s', strtotime($periodo['dataInicio'])); ?>" step="1" required>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="datetime-local" class="form-control" name="periodos[<?php echo $index; ?>][dataTermino]" value="<?php echo date('Y-m-d\TH:i:s', strtotime($periodo['dataInicio'])); ?>" step="1" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger" onclick="removerPeriodo(this)">Remover</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Caso não tenha períodos, inicia com um campo em branco -->
                                    <div class="row periodo-item mb-2">
                                        <div class="col-md-5">
                                            <input type="datetime-local" class="form-control" name="periodos[0][dataInicio]" step="1" required>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="datetime-local" class="form-control" name="periodos[0][dataTermino]" step="1" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger" onclick="removerPeriodo(this)">Remover</button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn btn-success mt-2" onclick="adicionarPeriodo()">Adicionar Período</button>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" name="salvar_lote" class="btn btn-primary">Salvar</button>
                    <a href="<?php echo HOME_URI; ?>/eventos/index/lotes" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>

        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Setores Permitidos</h3>
            </div>
            <form role="form" action="" method="POST">
                <div class="card-body">
                    <!-- Setores Permitidos com Ativar/Desativar -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="row" id="setores">
                                <?php foreach ($setores as $setor): ?>
                                    <div class="col-md-4">
                                        <div class="form-group row align-items-center border p-2 ml-1 mr-1" style="border-radius: 5px; background-color: #f8f9fa; margin-bottom: 10px;">
                                            <label class="col-md-7" style="font-size: 14px; font-weight: bold;"><?php echo $setor['nomeSetor']; ?></label>
                                            <div class="col-md-5 text-right">
                                                <input type="hidden" name="setores[<?php echo $setor['id']; ?>][id]" value="<?php echo $setor['id']; ?>">
                                                <input type="hidden" name="setores[<?php echo $setor['id']; ?>][status]" value="F">
                                                <input type="checkbox" name="setores[<?php echo $setor['id']; ?>][status]" value="T"
                                                    <?php echo in_array($setor['id'], array_column($setoresSelecionados, 'idSetor')) && $setoresSelecionados[array_search($setor['id'], array_column($setoresSelecionados, 'idSetor'))]['status'] == 'T' ? 'checked' : ''; ?>>
                                                <span style="font-size: 12px;">Ativo</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" name="salvar_lote" class="btn btn-primary">Salvar</button>
                    <a href="<?php echo HOME_URI; ?>/eventos/index/lotes" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </section>
</div>

<!-- Scripts para adicionar/remover períodos -->
<script>
    function adicionarPeriodo() {
        const periodos = document.getElementById('periodos');
        const index = periodos.children.length;
        const row = document.createElement('div');
        row.className = 'row periodo-item';
        row.innerHTML = `
            <div class="col-md-5 mb-2">
                <input type="datetime-local" class="form-control" name="periodos[${index}][dataInicio]" step="1" required>
            </div>
            <div class="col-md-5 mb-2">
                <input type="datetime-local" class="form-control" name="periodos[${index}][dataTermino]" step="1" required>
            </div>
            <div class="col-md-2 mb-2">
                <button type="button" class="btn btn-danger" onclick="removerPeriodo(this)">Remover</button>
            </div>
        `;
        periodos.appendChild(row);
    }

    function removerPeriodo(button) {
        button.closest('.periodo-item').remove();
    }

    $(document).ready(function() {
        // Inicializa Select2
        $('.select2').select2({
            placeholder: 'Selecione os setores permitidos',
            allowClear: true
        });
    });
</script>