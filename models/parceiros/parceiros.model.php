<?php
class ParceirosModel extends MainModel
{
    public $form_data;
    public $form_msg;
    public $db;

    private $id;
    private $idEmpresas;
    private $observacoes;
    private $token;
    private $idParceiro; // Refencia um id na propria tabela tblParceiro gerando um relacionamento entre parceiros e subparceiros
    private $tipo; // REVENDA, FINAL
    private $nomeParceiro;

    private $erro;

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;

        $this->controller = $controller;

        $this->parametros = $this->controller->parametros;

        $this->userdata = $this->controller->userdata;
    }

    public function validarParceiro()
    {
        $this->form_data = array();

        $this->idEmpresas = isset($_POST["idEmpresas"]) ? $_POST["idEmpresas"] : null;
        $this->observacoes = isset($_POST["observacoes"]) ? $_POST["observacoes"] : null;
        $this->token = isset($_POST["token"]) ? $_POST["token"] : null;
        $this->idParceiro = isset($_POST["idParceiro"]) ? $_POST["idParceiro"] : null;
        $this->tipo = isset($_POST["tipo"]) ? $_POST["tipo"] : null;
        $this->nomeParceiro = isset($_POST["nomeParceiro"]) ? $_POST["nomeParceiro"] : null;

        if (empty($this->idEmpresas) || !is_array($this->idEmpresas)) {
            $this->erro .= "<br>Selecione pelo menos uma empresa válida para o parceiro.";
        } else {
            // Verificar se os IDs das empresas existem no banco
            foreach ($this->idEmpresas as $idEmpresa) {
                $empresaExistente = $this->db->query('SELECT id FROM tblEmpresa WHERE id = ?', array($idEmpresa));
                if ($empresaExistente->rowCount() === 0) {
                    $this->erro .= "<br>A empresa com ID $idEmpresa não é válida.";
                }
            }
        }


        if (!empty($this->erro)) {
            $this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
            return;
        }

        $this->form_data['observacoes'] = trim($this->observacoes);
        $this->form_data['token'] = trim($this->token);
        $this->form_data['idEmpresas'] = $this->idEmpresas;
        $this->form_data['tipo'] = $this->tipo;
        $this->form_data['idParceiro'] = $this->idParceiro;
        $this->form_data['nomeParceiro'] = $this->nomeParceiro;

        if (empty($this->form_data)) {
            return;
        }

        if (chk_array($this->parametros, 0) == 'editar') {
            $this->editarParceiro();
            return;
        } else {
            $this->adicionarParceiro();
            return;
        }
    }

    public function getParceiro($idParceiro = null)
    {
        if (is_numeric($idParceiro) > 0) {
            $query = $this->db->query('SELECT `tblParceiro`.*, `parceiroPai`.`nomeParceiro` AS `nome_parceiro_pai`, 
            GROUP_CONCAT(`tblEmpresa`.`razaoSocial` SEPARATOR ", ") AS `empresas`
            FROM `tblParceiro` 
            LEFT JOIN `tblParceiro` AS `parceiroPai` ON `tblParceiro`.`idParceiro` = `parceiroPai`.`id`
            LEFT JOIN `relParceiroEmpresa` ON `tblParceiro`.`id` = `relParceiroEmpresa`.`idParceiro`
            LEFT JOIN `tblEmpresa` ON `relParceiroEmpresa`.`idEmpresa` = `tblEmpresa`.`id`
            WHERE `tblParceiro`.`id` = ?', array($idParceiro));
        } else {
            return;
        }

        if (!$query) {
            return 'Registro não encontrado.';
        }

        $registro = $query->fetch();

        if (empty($registro)) {
            return 'Registro inexistente.';
        }

        return $registro;
    }

    public function getIdParceiroUsuario($idUsuario)
    {
        $query = $this->db->query('SELECT r.`idParceiro`
                                    FROM `relParceiroEmpresa` r
                                    JOIN `tblUsuario` u ON r.`idEmpresa` = u.`idEmpresa`
                                    WHERE u.`id` = ?', array($idUsuario));

        if (!$query) {
            return 'Registro não encontrado.';
        }

        $registro = $query->fetch();

        if (empty($registro)) {
            return 'Registro inexistente.';
        }

        return $registro;
    }

    public function getParceiros($filtros = null)
    {

        $where = null;
        $limit = null;
        $groupby = null;

        if (!empty($filtros["q"])) {
            if (!empty($where)) {
                $where .= " AND ";
            } else {
                $where = " WHERE ";
            }

            $where .= "(`tblEmpresa`.`razaoSocial` LIKE '%" . _otimizaBusca($filtros['q']) . "%' OR `tblEmpresa`.`razaoSocial` LIKE '%" . _otimizaBusca($filtros['q']) . "%') ";
        }

        if (!empty($filtros["limite"])) {
            $limit = "LIMIT " . $filtros["limite"];
        }

        if (!empty($filtros["ordena"]) && !empty($filtros["ordem"])) {
            $orderby = "ORDER BY " . $filtros["ordena"] . " " .  $filtros["ordem"];
        } else {
            $orderby = "ORDER BY `tblEmpresa`.`razaoSocial`, `tblParceiro`.`id`";
        }

        if (!empty($filtros["status"])) {
            if (!empty($where)) {
                $where .= " AND ";
            } else {
                $where = " WHERE ";
            }

            $where .= "(`tblParceiro`.`status` = '" . $filtros['status'] . "')";
        }

        $groupby = "GROUP BY `tblParceiro`.`id`";

        $sql = "SELECT `tblParceiro`.*, 
                   `parceiroPai`.`nomeParceiro` AS `nome_parceiro_pai`, 
                   GROUP_CONCAT(`tblEmpresa`.`razaoSocial` SEPARATOR ', ') AS `empresas`
            FROM `tblParceiro` 
            LEFT JOIN `tblParceiro` AS `parceiroPai` ON `tblParceiro`.`idParceiro` = `parceiroPai`.`id`
            LEFT JOIN `relParceiroEmpresa` ON `tblParceiro`.`id` = `relParceiroEmpresa`.`idParceiro`
            LEFT JOIN `tblEmpresa` ON `relParceiroEmpresa`.`idEmpresa` = `tblEmpresa`.`id`
        
        $where $groupby $orderby $limit";

        $query = $this->db->query($sql);

        if (!$query) {
            return array();
        }

        return $query->fetchAll();
    }

    public function adicionarParceiro()
    {

        if (empty(chk_array($this->form_data, 'idEmpresas'))) {
            $this->form_msg = '<p class="form_error">Selecione uma empresa para o parceiro.</p>';
            return;
        }

        // Verificar se o token já existe
        $token = chk_array($this->form_data, 'token');
        $query = $this->db->query('SELECT COUNT(*) as total FROM tblParceiro WHERE token = ?', [$token]);
        $result = $query->fetch();

        if ($result['total'] > 0) {
            $this->form_msg = '<p class="form_error">O token informado já está em uso. Por favor, escolha outro.</p>';
            return;
        }

        $query = $this->db->insert('tblParceiro', array(
            'nomeParceiro' => chk_array($this->form_data, 'nomeParceiro'),
            'idParceiro' => chk_array($this->form_data, 'idParceiro'),
            'tipo' => chk_array($this->form_data, 'tipo'),
            'observacoes' => chk_array($this->form_data, 'observacoes'),
            'token' => chk_array($this->form_data, 'token')
        ));

        $this->id = $this->db->lastInsertId();

        if ($query) {

            // Associar o parceiro às empresas na tabela relParceiroEmpresa
            if (!empty($this->form_data['idEmpresas'])) {
                $empresas = $this->form_data['idEmpresas'];
                foreach ($empresas as $idEmpresa) {
                    $this->db->insert('relParceiroEmpresa', array(
                        'idEmpresa' => $idEmpresa,
                        'idParceiro' => $this->id
                    ));
                }
            }

            $this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso. Aguarde, você será redirecionado...');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/parceiros' . '">';
            $this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/parceiros' . '";</script>';

            $this->form_data = null;
            return;
        } else {
            $this->form_msg = '<p class="form_error">Erro ao adicionar parceiro.</p>';
            return;
        }
    }

    public function editarParceiro()
    {
        // Obtenha o ID do parceiro do parâmetro ou do POST
        if (chk_array($this->parametros, 1)) {
            $hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
        }

        if (empty($id)) {
            $this->form_msg = $this->controller->Messages->error('ID do parceiro inválido.');
            return;
        }


        // Valide os dados do formulário
        $this->form_data = array(
            'nomeParceiro' => isset($_POST['nomeParceiro']) ? trim($_POST['nomeParceiro']) : null,
            'idParceiro' => isset($_POST['idParceiro']) ? trim($_POST['idParceiro']) : null,
            'tipo' => isset($_POST['tipo']) ? trim($_POST['tipo']) : null,
            'observacoes' => isset($_POST['observacoes']) ? trim($_POST['observacoes']) : null,
            'token' => isset($_POST['token']) ? trim($_POST['token']) : null,
            'dataEdicao' => date('Y-m-d H:i:s')
        );

        // Verificar se o token já existe
        $token = chk_array($this->form_data, 'token');
        $query = $this->db->query('SELECT COUNT(*) as total FROM tblParceiro WHERE token = ?', [$token]);
        $result = $query->fetch();

        if ($result['total'] > 0) {
            $this->form_msg = '<p class="form_error">O token informado já está em uso. Por favor, escolha outro.</p>';
            return;
        }

        // Atualize o registro no banco de dados
        $query = $this->db->update('tblParceiro', 'id', $id, $this->form_data);

        if ($query) {

            $this->form_data['idEmpresas'] = isset($_POST['idEmpresas']) ? $_POST['idEmpresas'] : null;

            // Atualizar associações na tabela relParceiroEmpresa
            if (!empty($this->form_data['idEmpresas'])) {
                // Remove associações antigas
                $this->db->delete('relParceiroEmpresa', 'idParceiro', $id);

                // Insere novas associações
                foreach ($this->form_data['idEmpresas'] as $idEmpresa) {
                    $this->db->insert('relParceiroEmpresa', array(
                        'idEmpresa' => $idEmpresa,
                        'idParceiro' => $id
                    ));
                }
            }

            $this->form_msg = $this->controller->Messages->success('Parceiro atualizado com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/parceiros/">';
            $this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/parceiros/";</script>';
        } else {
            $this->form_msg = $this->controller->Messages->error('Erro ao atualizar o parceiro.');
        }
    }

    public function getTiposParceiros()
    {
        $query = $this->db->query("SHOW COLUMNS FROM `tblParceiro` LIKE 'tipo'");
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $enum = str_replace("'", "", substr($row['Type'], 5, (strlen($row['Type']) - 6))); // Retira os parênteses
        $enum_values = explode(',', $enum); // Separa as opções
        return $enum_values;
    }

    public function getEmpresasDoParceiro($idParceiro)
    {
        if (empty($idParceiro)) {
            return [];
        }

        $query = $this->db->query("SELECT idEmpresa FROM relParceiroEmpresa WHERE idParceiro = ?", [$idParceiro]);

        return array_column($query->fetchAll(), 'idEmpresa');
    }


    public function desbloquearParceiro()
    {
        $id = null;

        if (chk_array($this->parametros, 1)) {
            $hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
        }

        if (!empty($id)) {
            $id = (int) $id;

            $query = $this->db->update('tblParceiro', 'id', $id, array('status' => 'T'));

            $this->form_msg = $this->controller->Messages->success('Registro desbloqueado com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/parceiros/">';

            return;
        }
    }

    public function bloquearParceiro()
    {
        $id = null;

        if (chk_array($this->parametros, 1)) {
            $hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
        }

        if (!empty($id)) {
            $id = (int) $id;

            $query = $this->db->update('tblParceiro', 'id', $id, array('status' => 'F'));

            $this->form_msg = $this->controller->Messages->success('Registro bloqueado com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/parceiros/">';

            return;
        }
    }

    public function validarTokenParceiro($token)
    {
        $query = $this->db->query('SELECT `id` FROM `tblParceiro` WHERE `token` = ?', array($token));
        $registro = $query->fetch();
        // Verifica se há registros retornados
        if ($query->rowCount() > 0) {
            return $registro;
        }

        return false; // Token inválido
    }
}
