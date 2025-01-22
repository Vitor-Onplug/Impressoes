<?php if (!defined('ABSPATH')) exit;

$idParceiro = 0;
if (isset($_SESSION['idParceiroHash'])) {
    $hash = $_SESSION['idParceiroHash'];
    $idParceiro = decryptHash($hash);
}else{
    $idEmpresa = $_SESSION['userdata']['idEmpresa'];
    $idParceiro = $modeloParceiros->getParceiroEmpresa($idEmpresa);
}

// Primeiro buscamos apenas os anos disponíveis
$anos = $modelo->getAnosDisponiveis($idParceiro);

// Se não houver anos disponíveis ou id do parceiro, exibe mensagem ou dados vazios
if (empty($anos) || $idParceiro === 0) {
    $dadosDashboard = [
        'paginasUsuario' => ['labels' => [], 'data' => []],
        'paginasMes' => ['labels' => [], 'data' => []],
        'paginasImpressora' => ['labels' => [], 'data' => []],
        'grayscale' => [0, 0],
        'duplex' => [0, 0],
        'documentos' => [],
        'anos' => [],
        'anoSelecionado' => date('Y')
    ];
} else {
    // Depois definimos o ano selecionado
    $anoSelecionado = isset($_GET['ano']) ? (int)$_GET['ano'] : $anos[0];

    // Por fim buscamos os dados com o ano já definido
    $dadosDashboard = $modelo->getDadosDashboardParceiro($idParceiro, $anoSelecionado);
    $dadosDashboard['anos'] = $anos;
    $dadosDashboard['anoSelecionado'] = $anoSelecionado;
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-sm-6">
                    <h1>GESTÃO DE IMPRESSÕES</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-right">
                        <select id="selectAno" class="form-control" onchange="atualizarAno(this.value)">
                            <?php foreach ($dadosDashboard['anos'] as $ano): ?>
                                <option value="<?php echo $ano; ?>" <?php echo $ano == $anoSelecionado ? 'selected' : ''; ?>>
                                    <?php echo $ano; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php
            echo $modelo->form_msg;
            ?>
            <!-- Primeira linha de gráficos -->
            <div class="row mb-4"> <!-- Adicionei margin-bottom entre as linhas -->
                <!-- Gráfico: Páginas por Usuário -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h3 class="card-title">PÁGINAS POR USUÁRIO</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="graficoPaginasUsuario"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfico: Páginas por Mês -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h3 class="card-title">PÁGINAS POR MÊS</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="graficoPaginasMes"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfico: Páginas por Impressora -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h3 class="card-title">PÁGINAS POR IMPRESSORAS</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="graficoPaginasImpressora"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Segunda linha de gráficos -->
            <div class="row">
                <!-- Gráfico: Impressões por Grayscale -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h3 class="card-title">IMPRESSÕES POR GRAYSCALE</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="graficoGrayscale"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfico: Impressões por Duplex -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h3 class="card-title">IMPRESSÕES POR DUPLEX</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="graficoDuplex"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        // Gráfico de Páginas por Usuário
        const ctxPaginasUsuario = document.getElementById('graficoPaginasUsuario').getContext('2d');
        new Chart(ctxPaginasUsuario, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($dadosDashboard['paginasUsuario']['labels']); ?>,
                datasets: [{
                    label: 'Páginas',
                    data: <?php echo json_encode($dadosDashboard['paginasUsuario']['data']); ?>,
                    backgroundColor: '#00264D'
                }]
            },
            options: {
                indexAxis: 'y',
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                if (typeof value === 'string' && value.length > 15) {
                                    return value.substr(0, 15) + '...';
                                }
                                return value;
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de Páginas por Mês
        const ctxPaginasMes = document.getElementById('graficoPaginasMes').getContext('2d');
        new Chart(ctxPaginasMes, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($dadosDashboard['paginasMes']['labels']); ?>,
                datasets: [{
                    label: 'Páginas',
                    data: <?php echo json_encode($dadosDashboard['paginasMes']['data']); ?>,
                    backgroundColor: '#00264D'
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Gráfico de Páginas por Impressora
        const ctxPaginasImpressora = document.getElementById('graficoPaginasImpressora').getContext('2d');
        new Chart(ctxPaginasImpressora, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($dadosDashboard['paginasImpressora']['labels']); ?>,
                datasets: [{
                    data: <?php echo json_encode($dadosDashboard['paginasImpressora']['data']); ?>,
                    backgroundColor: ['#0088FE', '#00C49F', '#FFBB28', '#FF8042']
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gráfico de Impressões por Grayscale
        const ctxGrayscale = document.getElementById('graficoGrayscale').getContext('2d');
        new Chart(ctxGrayscale, {
            type: 'doughnut',
            data: {
                labels: ['GRAYSCALE', 'NOT GRAYSCALE'],
                datasets: [{
                    data: <?php echo json_encode($dadosDashboard['grayscale']); ?>,
                    backgroundColor: ['#0088FE', '#00C49F']
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gráfico de Impressões por Duplex
        const ctxDuplex = document.getElementById('graficoDuplex').getContext('2d');
        new Chart(ctxDuplex, {
            type: 'doughnut',
            data: {
                labels: ['DUPLEX', 'NOT DUPLEX'],
                datasets: [{
                    data: <?php echo json_encode($dadosDashboard['duplex']); ?>,
                    backgroundColor: ['#0088FE', '#00C49F']
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Handler para mudança de ano
        $('#selectAno').on('change', function() {
            let ano = $(this).val();
            // Aqui você pode implementar a lógica para atualizar os dados
            // Por exemplo, fazer uma chamada AJAX ou recarregar a página
            window.location.href = window.location.pathname + '?ano=' + ano;
        });

        // ... resto do código dos gráficos ...
    });
</script>
<style>
    /* Estilo adicional para o select */
    #selectAno {
        width: 120px;
        display: inline-block;
    }

    .content-wrapper {
        padding: 20px;
    }

    .card {
        box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        margin-bottom: 1.5rem;
    }

    .card-header {
        padding: 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, .125);
    }

    .card-title {
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
    }

    .card-body {
        padding: 1.25rem;
        height: 300px;
    }

    canvas {
        width: 100% !important;
        height: 100% !important;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>