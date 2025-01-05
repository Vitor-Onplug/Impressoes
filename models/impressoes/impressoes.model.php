<?php
class ImpressoesModel extends MainModel
{
    public $form_data;
    public $form_msg;
    public $db;

    private $id;

    private $erro;

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        $this->controller = $controller;
        $this->parametros = $this->controller->parametros;
        $this->userdata = $this->controller->userdata;
    }

    // function getImpressoes($filtros = null, $idUsuario = null)
    // {
    //     $where = "1=1";
    //     $orderby = "";

    //     if (!empty($filtros["q"])) {
    //         $where = " (`tblImpressoes`.`nomeUsuario` LIKE '%" . _otimizaBusca($filtros['q']) . "%')";
    //         $where .= " OR (`tblImpressoes`.`nomeImpressora` LIKE '%" . _otimizaBusca($filtros['q']) . "%')";
    //         $where .= " OR (`tblImpressoes`.`nomeArquivo` LIKE '%" . _otimizaBusca($filtros['q']) . "%')";
    //         $where .= " OR (`tblImpressoes`.`cliente` LIKE '%" . _otimizaBusca($filtros['q']) . "%')";

    //     }


    //     $orderby = "ORDER BY `tblImpressoes`.`dataCadastro` DESC";

    //     $sql = "SELECT `tblImpressoes`.*, `tblMarcaImpressora`.`nome` AS `marca`, `tblDepartamento`.`nome` AS `departamento`
    //             FROM `tblImpressoes`
    //             LEFT JOIN `tblImpressora` ON `tblImpressoes`.`idImpressora` = `tblImpressora`.`id`
    //             LEFT JOIN `tblMarcaImpressora` ON `tblImpressora`.`idMarca` = `tblMarcaImpressora`.`id`
    //             LEFT JOIN `tblDepartamento` ON `tblImpressora`.`idDepartamento` = `tblDepartamento`.`id`
    //             WHERE $where $orderby";

    //     $query = $this->db->query($sql);

    //     if (!$query) {
    //         return array();
    //     }

    //     return $query->fetchAll();
    // }

    function getImpressoes($filtros = null, $idUsuario = null)
    {
        $whereClauses = array();
        $params = array();
        $orderby = "ORDER BY `tblImpressoes`.`dataCadastro` DESC";

        // Base condition
        $whereClauses[] = "1=1";

        // Search Filters
        if (!empty($filtros["q"])) {
            $searchTerm = '%' . _otimizaBusca($filtros['q']) . '%';
            $whereClauses[] = "(`tblImpressoes`.`nomeUsuario` LIKE ? 
                            OR `tblImpressoes`.`nomeImpressora` LIKE ? 
                            OR `tblImpressoes`.`nomeArquivo` LIKE ? 
                            OR `tblImpressoes`.`cliente` LIKE ?)";
            $params = array_merge($params, array($searchTerm, $searchTerm, $searchTerm, $searchTerm));
        }

        // Get User Data
        if (!empty($idUsuario)) {
            $queryUser = $this->db->query("SELECT * FROM vwUsuarios WHERE idUsuario = ?", array($idUsuario));
            $userData = $queryUser->fetch();

            if ($userData) {
                if ($userData['idPermissao'] !== 1) {
                    $idEmpresa = $userData['idEmpresa'];
                    $queryParceiro = $this->db->query("SELECT id FROM tblParceiro WHERE idEmpresa = ?", array($idEmpresa));
                    $parceiros = $queryParceiro->fetchAll(PDO::FETCH_COLUMN, 0);

                    if (!empty($parceiros)) {
                        $placeholders = implode(',', array_fill(0, count($parceiros), '?'));
                        $whereClauses[] = "tblImpressoes.idParceiro IN ($placeholders)";
                        $params = array_merge($params, $parceiros);
                    } else {
                        return array(); // No parceiros associated
                    }
                }
            }
        }

        // Combine WHERE clauses
        $where = implode(' AND ', $whereClauses);

        // Complete SQL Query
        $sql = "SELECT `tblImpressoes`.*, `tblMarcaImpressora`.`nome` AS `marca`, `tblDepartamento`.`nome` AS `departamento`
            FROM `tblImpressoes`
            LEFT JOIN `tblImpressora` ON `tblImpressoes`.`idImpressora` = `tblImpressora`.`id`
            LEFT JOIN `tblMarcaImpressora` ON `tblImpressora`.`idMarca` = `tblMarcaImpressora`.`id`
            LEFT JOIN `tblDepartamento` ON `tblImpressora`.`idDepartamento` = `tblDepartamento`.`id`
            WHERE $where $orderby";


        // Prepare and execute the query
        $query = $this->db->query($sql);
        
        if (!$query) {
            return array();
        }

        return $query->fetchAll();
    }



    function getImpressao($id = null)
    {
        if (empty($id)) {
            return;
        }

        $query = $this->db->query('SELECT * FROM `tblImpressoes` WHERE `id` = ?', array($id));

        if (!$query) {
            return false;
        }

        return $query->fetch();
    }

    public function exportarImpressao()
    {
        // Obtém os dados das impressões

        $impressao = $this->getImpressoes(); // Modifique conforme sua lógica

        // Define o cabeçalho do CSV
        $csv = "Data e Hora,Usuário,Páginas,Cópias,Total Folhas,Impressora,Arquivo,Cliente,Papel,Duplex,Monocromático,Tamanho (KB)\n";

        // Adiciona os dados
        foreach ($impressao as $item) {
            $csv .= implode(",", [
                $item['dataCadastro'],
                $item['nomeUsuario'],
                $item['paginas'],
                $item['copias'],
                $item['qtdFolhas'],
                $item['nomeImpressora'],
                $item['nomeArquivo'],
                $item['cliente'],
                $item['papel'],
                $item['duplex'],
                $item['monocromatico'],
                $item['tamanhoKb']
            ]) . "\n";
        }

        // Define os headers para download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="impressao.csv"');
        echo $csv;
        exit;
    }


    function salvarDadosImpressao($dadosRecebidos)
    {
        $erros = [];
        foreach ($dadosRecebidos as $linha) {
            // Sanitização básica dos dados
            $dataHora = date('Y-m-d H:i:s', strtotime($linha['Data/Hora']));
            $nomeUsuario = trim($linha['Usuario']);
            $paginas = intval($linha['Paginas'] ?? 0);
            $copias = intval($linha['Copias'] ?? 0);
            $total = intval($linha['Total'] ?? 0);
            $nomeImpressora = trim($linha['Impressora']);
            $documento = trim($linha['Documento'] ?? '');
            $cliente = trim($linha['Cliente'] ?? '');
            $papel = trim($linha['Papel'] ?? '');
            $duplex = trim($linha['Duplex'] ?? '');
            $monocromatico = trim($linha['Monocromatico'] ?? '');
            $tamanho = trim($linha['Tamanho'] ?? '');
            $linguagem = trim($linha['Linguagem'] ?? '');
            $idParceiro = trim($linha['idParceiro'] ?? '');

            $descricao = $linha['Descricao'] ?? '';
            $conteudo = $linha['Conteudo'] ?? '';
            $custoPorFolha = $linha['CustoPorFolha'] ?? '';
            $custoTotal = $linha['CustoTotal'] ?? '';

            // Busca o ID da impressora
            $queryImpressora = $this->db->query("SELECT id FROM tblImpressora WHERE nome = ?", [$nomeImpressora]);
            if (!$queryImpressora) {
                $erros[] = "Erro na consulta da impressora: " . $this->db->error();
                $sucesso = false;
                continue;
            }

            $impressora = $queryImpressora->fetch();
            $idImpressora = $impressora['id'] ?? null;

            $usuarioLowerParts = explode(' ', $nomeUsuario);
            $primeiroNome = $usuarioLowerParts[0] ?? null;

            if ($primeiroNome) {
                $queryUsuario = $this->db->query("SELECT tblUsuario.id FROM tblUsuario 
                                              INNER JOIN tblPessoa ON tblUsuario.idPessoa = tblPessoa.id
                                              WHERE nome LIKE ?", ["%{$primeiroNome}%"]);

                if (!$queryUsuario) {
                    $erros[] = "Erro na consulta do usuário: " . $this->db->error();
                    $sucesso = false;
                    continue;
                }

                $usuario = $queryUsuario->fetch();
                $idUsuario = $usuario['id'] ?? null;
            }

            // Verifica se todos os campos obrigatórios estão preenchidos
            if (empty($dataHora) || empty($nomeUsuario) || empty($nomeImpressora)) {
                $erros[] = 'Dados obrigatórios não preenchidos na linha: ' . json_encode($linha);
                continue; // Pula a linha se os dados obrigatórios não estiverem presentes
            }


            // Verifica se a impressão já foi computada com nome da impressora e do usuário
            // $queryVerifica = $this->db->query(
            //     "SELECT id FROM tblImpressoes 
            //         WHERE dataCadastro = ? 
            //         AND nomeImpressora LIKE  ? 
            //         AND nomeArquivo LIKE  ? 
            //         AND nomeUsuario LIKE  ?",

            //     [$dataHora, "%{$nomeImpressora}%", "%{$documento}%", "%{$nomeUsuario}%"]
            // );

            $queryVerifica = $this->db->query(
                "SELECT id FROM tblImpressoes 
                 WHERE dataCadastro = ? 
                 AND nomeImpressora = ? 
                 AND nomeArquivo = ? 
                 AND nomeUsuario = ?",
                [$dataHora, $nomeImpressora, $documento, $nomeUsuario]
            );

            if ($queryVerifica && $queryVerifica->rowCount() > 0) {
                //$erros[] = "Impressão já computada: " . json_encode($linha);
                continue; // Pula a linha se a impressão já existir
            }

            // Insere os dados no banco
            $queryInsert = $this->db->insert('tblImpressoes', array(
                'dataCadastro' => $dataHora,
                'nomeUsuario' => $nomeUsuario,
                'nomeImpressora' => $nomeImpressora,
                'paginas' => $paginas,
                'copias' => $copias,
                'qtdFolhas' => $total,
                'idImpressora' => $idImpressora,
                'idUsuario' => $idUsuario,
                'nomeArquivo' => $documento,
                'cliente' => $cliente,
                'papel' => $papel,
                'duplex' => $duplex,
                'monocromatico' => $monocromatico,
                'tamanhoKb' => $tamanho,
                'descricao' => $descricao,
                'conteudo' => $conteudo,
                'custoPorFolha' => $custoPorFolha,
                'custoTotal' => $custoTotal,
                'linguagem' => $linguagem,
                'idParceiro' => $idParceiro
            ));
        }

        if (empty($erros)) {
            return ['success' => true, 'message' => 'Dados salvos com sucesso.'];
        } else {
            return ['success' => false, 'message' => $erros];
        }
    }
}
