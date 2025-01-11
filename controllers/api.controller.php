<?php
class ApiController extends MainController
{
    public $login_required = true;

    public function impressao()
    {
        $parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();

        $modelo = $this->load_model('impressoes/impressoes');
        $modeloParceiros = $this->load_model('parceiros/parceiros');

        if (chk_array($this->parametros, 0) == 'cadastro') {
            // Valida o método POST e o token
            try {
                header('Content-Type: application/json; charset=utf-8');

                $tokenRecebido = $_POST['token'] ?? '';

                if (empty($tokenRecebido)) {
                    echo json_encode(['success' => false, 'message' => 'Token não informado.']);
                    return;
                }



                $tokenValido = $modeloParceiros->validarTokenParceiro($tokenRecebido);

                if (!isset($tokenValido['id'])) {
                    echo json_encode(['success' => false, 'message' => 'Token inválido.']);
                    exit;
                }

                if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                    $arquivo = $_FILES['file']['tmp_name'];
                    $dadosRecebidos = [];

                    if (($handle = fopen($arquivo, 'r')) !== false) {
                        while (($linha = fgetcsv($handle, 1000, ',')) !== false) {

                            // Corrige a codificação para UTF-8
                            foreach ($linha as &$coluna) {
                                $coluna = mb_convert_encoding($coluna, 'UTF-8', 'ISO-8859-1'); // De ISO-8859-1 para UTF-8
                            }

                            // Verifica se a primeira coluna não é uma data válida (ignora cabeçalho ou textos)
                            if (!isset($linha[0]) || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $linha[0])) {
                                continue; // Ignora cabeçalhos ou linhas inválidas
                            }

                            $dadosRecebidos[] = [
                                "Data/Hora" => $linha[0],
                                "Usuario" => $linha[1],
                                "Paginas" => intval($linha[2]),
                                "Copias" => intval($linha[3]),
                                "Total" => intval($linha[2]) * intval($linha[3]),
                                "Impressora" => $linha[4],
                                "Documento" => $linha[5],
                                "Cliente" => $linha[6],
                                "Papel" => $linha[7],
                                "Linguagem" => $linha[8],
                                "Duplex" => $linha[11],
                                "Monocromatico" => $linha[12],
                                "Tamanho" => $linha[13],
                                "idParceiro" => $tokenValido['id']
                            ];
                        }
                        fclose($handle);

                        //echo json_encode([$dadosRecebidos]);
                        // var_dump($dadosRecebidos);
                        // exit;
                        // Envia os dados para o modelo
                        $response = $modelo->salvarDadosImpressao($dadosRecebidos);
                        echo json_encode($response);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Erro ao abrir o arquivo CSV.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Nenhum arquivo enviado ou erro no upload.']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Erro ao processar o arquivo CSV. ' . $e->getMessage()]);
            }
        } else if (chk_array($this->parametros, 0) == 'exportar') {
            $response = $modelo->exportarImpressao();
            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'message' => 'Rota não encontrada.']);
        }
    }

    public function getDadosImpressao()
    {
        try {
            header('Content-Type: application/json; charset=utf-8');

            // Verificar se é uma requisição POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
                return;
            }
            // Pegar dados do JSON
            $jsonData = file_get_contents('php://input');
            $dados = json_decode($jsonData, true);

            if ($dados === null) {
                echo json_encode(['success' => false, 'message' => 'Dados JSON inválidos']);
                return;
            }

            // Validar token
            $tokenRecebido = $dados['token'] ?? '';

            if (empty($tokenRecebido)) {
                echo json_encode(['success' => false, 'message' => 'Token não informado.']);
                return;
            }

            $modeloParceiros = $this->load_model('parceiros/parceiros');
            $tokenValido = $modeloParceiros->validarTokenParceiro($tokenRecebido);

            if ($tokenValido === false) {
                echo json_encode(['success' => false, 'message' => 'Token inválido.']);
                return;
            }

            // Pegar datas do intervalo
            $dataInicio = $dados['data_inicio'] ?? '';
            $dataFim = $dados['data_fim'] ?? '';

            // Validar datas
            if (empty($dataInicio) || empty($dataFim)) {
                echo json_encode(['success' => false, 'message' => 'Período não informado.']);
                return;
            }

            $dataInicio = DateTime::createFromFormat('d-m-Y', $dataInicio);
            $dataFim = DateTime::createFromFormat('d-m-Y', $dataFim);
            // Formatar datas
            $dataInicio = $dataInicio->format('Y-m-d 00:00:00');
            $dataFim = $dataFim->format('Y-m-d 23:59:59');

            var_dump($dataFim);

            // Consultar dados
            $query = $this->db->query(
                "SELECT 
                dataCadastro as 'Data/Hora',
                nomeUsuario as Usuario,
                paginas as Paginas,
                copias as Copias,
                qtdFolhas as Total,
                nomeImpressora as Impressora,
                nomeArquivo as Documento,
                cliente as Cliente,
                papel as Papel,
                linguagem as Linguagem,
                duplex as Duplex,
                monocromatico as Monocromatico,
                tamanhoKb as Tamanho
            FROM tblImpressoes 
            WHERE idParceiro = ? 
            AND dataCadastro BETWEEN ? AND ?
            ORDER BY dataCadastro ASC",
                [$tokenValido['idParceiro'], $dataInicio, $dataFim]
            );

            if (!$query) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erro ao consultar dados: ' . $this->db->error()
                ]);
                return;
            }

            $dados = $query->fetchAll();
            $totalRegistros = count($dados);

            // Retornar resultado
            echo json_encode([
                'success' => true,
                'message' => 'Dados recuperados com sucesso',
                'countRegistro' => $totalRegistros,
                'data' => $dados,
               
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao recuperar dados: ' . $e->getMessage()
            ]);
        }
    }
}
