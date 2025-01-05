<?php if (!defined('ABSPATH')) exit;
$hash = $_SESSION['idParceiroHash'];
$idParceiro = decryptHash($hash);
$relatorio = $modelo->getRelatorioPorUsuario($idParceiro);
?>

<div class="content-wrapper">
    <!-- Cabeçalho de conteúdo -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Relatório de Impressões por Usuário</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Início</a></li>
                        <li class="breadcrumb-item ">Relatórios</li>
                        <li class="breadcrumb-item active">Impressões por Usuário</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Conteúdo principal -->
    <section class="content">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Impressões por Usuário</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Tabela com barra de rolagem -->
                    <div class="col-md-6" style="overflow-x: auto; max-height: 400px;">
                        <table id="table-relatorio-usuario" class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Usuário</th>
                                    <th>Soma de Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($relatorio as $linha): ?>
                                    <tr>
                                        <td><?php echo htmlentities($linha['usuario']); ?></td>
                                        <td><?php echo htmlentities($linha['total']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Gráfico ao lado direito -->
                    <div class="col-md-6">
                        <canvas id="chart-relatorio-usuario" style="width:100%; max-height:400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function () {
        // Dados do gráfico
        const usuarios = <?php echo json_encode(array_column($relatorio, 'usuario')); ?>;
        const totais = <?php echo json_encode(array_column($relatorio, 'total')); ?>;

        // Configuração do gráfico
        const ctx = document.getElementById('chart-relatorio-usuario').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: usuarios,
                datasets: [{
                    label: 'Total de Impressões',
                    data: totais,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
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
                            text: 'Usuários'
                        },
                        ticks: {
                            autoSkip: false,
                            maxRotation: 90,
                            minRotation: 45
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
