<?php
if (!defined('ABSPATH')) exit;

// Filtros para pesquisa
$status = isset($_REQUEST["status"]) ? $_REQUEST["status"] : null;
$q = isset($_REQUEST["q"]) ? $_REQUEST["q"] : null;

// Define os filtros para a busca de impressões
$filtros = array('status' => $status, 'q' => $q);

// Obtém a lista de impressões com base nos filtros aplicados
$impressao = $modelo->getImpressoes($filtros, $_SESSION['userdata']['id']);

?>

<div class="content-wrapper">
    <!-- Cabeçalho de conteúdo -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Listagem de Impressões</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Início</a></li>
                        <li class="breadcrumb-item active">Impressões</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Formulário de pesquisa de impressoras -->
    <section class="content">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Pesquisa</h3>
            </div>
            <form role="form" action="" method="GET" enctype="multipart/form-data">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="q">Busca por Impressões</label>
                                <input type="text" class="form-control" placeholder="Pesquisa ..." name="q" id="q" value="<?php echo $q; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Buscar</button>
                    <a href="<?php echo HOME_URI; ?>/impressoes" class="btn btn-primary">Limpar</a>
                </div>
            </form>
        </div>
    </section>



    <!-- Tabela de Impressões -->
    <section class="content">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Impressões Registradas</h3>
            </div>
            <div class="card-body">
                <table id="table-impressao" class="table table-hover table-bordered table-striped dataTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th> <!-- Checkbox para selecionar todas as linhas -->
                            <th>Data e Hora</th>
                            <th>Usuário</th>
                            <th>Páginas</th>
                            <th>Cópias</th>
                            <th>Total Folhas</th>
                            <th>Impressora</th>
                            <th>Arquivo</th>
                            <th>Cliente</th>
                            <th>Papel</th>
                            <th>Duplex</th>
                            <th>Monocromático</th>
                            <th>Tamanho (KB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($impressao as $item): ?>
                            <tr>
                                <td><input type="checkbox" class="row-checkbox"></td>
                                <td><?php echo htmlentities($item['dataCadastro']); ?></td>
                                <td class="text-break"><?php echo htmlentities($item['nomeUsuario']); ?></td>
                                <td><?php echo htmlentities($item['paginas']); ?></td>
                                <td><?php echo htmlentities($item['copias']); ?></td>
                                <td><?php echo htmlentities($item['qtdFolhas']); ?></td>
                                <td class="text-break"><?php echo htmlentities($item['nomeImpressora']); ?></td>
                                <td class="text-break"><?php echo htmlentities($item['nomeArquivo']); ?></td>
                                <td class="text-break"><?php echo htmlentities($item['cliente']); ?></td>
                                <td><?php echo htmlentities($item['papel']); ?></td>
                                <td><?php echo htmlentities($item['duplex']); ?></td>
                                <td><?php echo htmlentities($item['monocromatico']); ?></td>
                                <td><?php echo htmlentities($item['tamanhoKb']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <button class="btn btn-success" id="btn-exportar">Exportar para CSV</button>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        document.getElementById('btn-exportar').addEventListener('click', function() {
            window.location.href = "<?php echo HOME_URI; ?>/api/impressao/exportar";
        });

    });
</script>