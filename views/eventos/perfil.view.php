<?php 
if(!defined('ABSPATH')) exit; 

$hash = chk_array($parametros, 1);

if (!empty($hash)) {
	if($_SESSION['idEventoHash'] != $hash){
		$_SESSION['idEventoHash'] = $hash;
		//tira o hash da url

		 // Redireciona para a URL sem o hash (remove o hash da URL)
		 $urlSemHash = HOME_URI . '/eventos/index/' . implode('/', array_slice($parametros, 0, 1)); // Remove o hash
		 header("Location: $urlSemHash");
		 exit;
	}
}else{
	$hash = $_SESSION['idEventoHash'];
}

$idEvento = decryptHash($_SESSION['idEventoHash']);
$evento = $modelo->getEvento($idEvento);

// Carrega as contagens
$quantidadeLeitores = $modeloLeitores->getQuantidadeLeitores($idEvento);
$quantidadeTerminais = $modeloTerminais->getQuantidadeTerminais($idEvento);
$quantidadeSetores = $modeloSetores->getQuantidadeSetores($idEvento);
$quantidadeCredenciais = 0; //$modeloCredenciais->getQuantidadeCredenciais($idEvento);
$quantidadeIngressos = 0; //$modeloIngressos->getQuantidadeIngressos($idEvento);

// Carrega os últimos 10 registros de entrada
$ultimosRegistros = [];// $modeloRegistros->getUltimosRegistrosEntrada($idEvento);

?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard do Evento: <?php echo htmlentities(chk_array($modelo->form_data, 'evento')); ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Evento</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Quantidade de Setores -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $quantidadeSetores; ?></h3>
                            <p>Setores</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <a href="<?php echo HOME_URI; ?>/eventos/index/setores/<?php echo encryptId($idEvento); ?>" class="small-box-footer">
                            Ver Setores <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Quantidade de Terminais -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo $quantidadeTerminais; ?></h3>
                            <p>Terminais</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-desktop"></i>
                        </div>
                        <a href="<?php echo HOME_URI; ?>/eventos/index/terminais/<?php echo encryptId($idEvento); ?>" class="small-box-footer">
                            Ver Terminais <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Quantidade de Leitores Faciais -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo $quantidadeLeitores; ?></h3>
                            <p>Leitores Faciais</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-camera"></i>
                        </div>
                        <a href="<?php echo HOME_URI; ?>/eventos/index/leitores/<?php echo encryptId($idEvento); ?>" class="small-box-footer">
                            Ver Leitores <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Quantidade de Credenciais -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?php echo $quantidadeCredenciais; ?></h3>
                            <p>Credenciais Emitidas</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-id-badge"></i>
                        </div>
                        <a href="<?php echo HOME_URI; ?>/eventos/index/credenciais/<?php echo encryptId($idEvento); ?>" class="small-box-footer">
                            Ver Credenciais <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Quantidade de Ingressos -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?php echo $quantidadeIngressos; ?></h3>
                            <p>Ingressos Usados</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <a href="<?php echo HOME_URI; ?>/eventos/index/ingressos/<?php echo encryptId($idEvento); ?>" class="small-box-footer">
                            Ver Ingressos <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Últimos 10 Registros de Entrada -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Últimos 10 Registros de Entrada</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Data e Hora</th>
                                        <th>Credencial</th>
                                        <th>Terminal</th>
                                        <th>Leitor Facial</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ultimosRegistros as $registro): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y H:i:s', strtotime($registro['dataHora'])); ?></td>
                                            <td><?php echo htmlentities($registro['credencial']); ?></td>
                                            <td><?php echo htmlentities($registro['terminal']); ?></td>
                                            <td><?php echo htmlentities($registro['leitorFacial']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
