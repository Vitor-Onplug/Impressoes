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
}
