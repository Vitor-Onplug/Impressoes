<?php if (!defined('ABSPATH')) exit;

$idEmpresa = $_SESSION['userdata']['idEmpresa'];
$dadosDashboard = $modelo->getDadosDashboard($idEmpresa);

// Se não houver dados, retorna tudo vazio e exibe mensagem de erro
if (!$dadosDashboard) {
    $dadosDashboard = [
        'totalImpressoes' => 0,
        'totalUsuarios' => 0,
        'totalImpressoras' => 0,
        'impressaoMes' => 0,
        'usuarios' => [
            'labels' => [],
            'data' => []
        ],
        'impressoras' => [
            'labels' => [],
            'data' => []
        ]
    ];
}
?>

<div class="content-wrapper">
    <!-- Cabeçalho de conteúdo -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Início</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Conteúdo principal -->
    <section class="content">
        <div class="container-fluid">
            <?php
            echo $modeloEmails->form_msg;
            ?>
            <div class="row">
                <!-- Card: Total de Impressões -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $dadosDashboard['totalImpressoes']; ?></h3>
                            <p>Total de Impressões</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-printer"></i>
                        </div>
                    </div>
                </div>

                <!-- Card: Usuários -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo $dadosDashboard['totalUsuarios']; ?></h3>
                            <p>Usuários Ativos</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person"></i>
                        </div>
                    </div>
                </div>

                <!-- Card: Impressoras -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo $dadosDashboard['totalImpressoras']; ?></h3>
                            <p>Impressoras Ativas</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-printer"></i>
                        </div>
                    </div>
                </div>

                <!-- Card: Impressões do Mês -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?php echo $dadosDashboard['impressaoMes']; ?></h3>
                            <p>Impressões neste Mês</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-calendar"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="row">
                <!-- Gráfico de Impressões por Usuário -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Impressões por Usuário</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="graficoUsuarios" style="height: 400px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Impressões por Impressora -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Impressões por Impressora</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="graficoImpressoras" style="height: 400px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        // Dados para o gráfico de Usuários
        const usuarios = <?php echo json_encode($dadosDashboard['usuarios']['labels']); ?>;
        const totalUsuarios = <?php echo json_encode($dadosDashboard['usuarios']['data']); ?>;

        const ctxUsuarios = document.getElementById('graficoUsuarios').getContext('2d');
        new Chart(ctxUsuarios, {
            type: 'bar',
            data: {
                labels: usuarios,
                datasets: [{
                    label: 'Impressões',
                    data: totalUsuarios,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Usuários'
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

        // Dados para o gráfico de Impressoras
        const impressoras = <?php echo json_encode($dadosDashboard['impressoras']['labels']); ?>;
        const totalImpressoras = <?php echo json_encode($dadosDashboard['impressoras']['data']); ?>;

        const ctxImpressoras = document.getElementById('graficoImpressoras').getContext('2d');
        new Chart(ctxImpressoras, {
            type: 'pie',
            data: {
                labels: impressoras,
                datasets: [{
                    data: totalImpressoras,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });
    });
</script>