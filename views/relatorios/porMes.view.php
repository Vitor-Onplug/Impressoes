<?php if (!defined('ABSPATH')) exit;

$mesSelecionado = isset($_POST['mes']) ? $_POST['mes'] : date('Y-m');
$relatorio = $modelo->getRelatorioPorDia($_SESSION['userdata']['id'], $mesSelecionado);
$meses = [
    'January' => 'Janeiro',
    'February' => 'Fevereiro',
    'March' => 'Março',
    'April' => 'Abril',
    'May' => 'Maio',
    'June' => 'Junho',
    'July' => 'Julho',
    'August' => 'Agosto',
    'September' => 'Setembro',
    'October' => 'Outubro',
    'November' => 'Novembro',
    'December' => 'Dezembro'
];
?>

<div class="content-wrapper">
    <!-- Cabeçalho de conteúdo -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Relatório de Impressões por Mês</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Início</a></li>
                        <li class="breadcrumb-item ">Relatórios</li>
                        <li class="breadcrumb-item active">Impressões por Mês</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Filtro por Mês -->
    <section class="content">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Selecione o Mês</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="mes">Mês</label>
                        <select class="form-control" id="mes" name="mes" onchange="this.form.submit()">
                            <?php for ($i = 0; $i < 12; $i++):
                                $mes = date('Y-m', strtotime("-$i months"));
                                $mesIngles = date('F', strtotime("-$i months"));
                                $mesTraduzido = $meses[$mesIngles] ?? $mesIngles;
                                $ano = date('Y', strtotime("-$i months"));
                            ?>
                                <option value="<?php echo $mes; ?>" <?php echo ($mes == $mesSelecionado) ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($mesTraduzido) . " de $ano"; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Conteúdo principal -->
    <section class="content">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Impressões por Dia</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Tabela -->
                    <div class="col-md-6" style="overflow-x: auto; max-height: 400px;">
                        <table id="table-relatorio-dia" class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Soma de Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($relatorio as $linha): ?>
                                    <tr>
                                        <td><?php echo htmlentities($linha['dia']); ?></td>
                                        <td><?php echo htmlentities($linha['total']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Gráfico -->
                    <div class="col-md-6">
                        <canvas id="chart-relatorio-dia" style="width:100%; max-height:400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        // Dados do gráfico
        const dias = <?php echo json_encode(array_column($relatorio, 'dia')); ?>;
        const totais = <?php echo json_encode(array_column($relatorio, 'total')); ?>;

        // Configuração do gráfico
        const ctx = document.getElementById('chart-relatorio-dia').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dias,
                datasets: [{
                    label: 'Total de Impressões',
                    data: totais,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Dias'
                        },
                        ticks: {
                            autoSkip: false
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Total de Impressões'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>