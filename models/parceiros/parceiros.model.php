<?php
class ParceirosModel extends MainModel
{
    public $form_data;
    public $form_msg;
    public $db;

    private $id;
    private $idEmpresa;
    private $observacoes;
    private $token;
    private $idParceiro; // Refencia um id na propria tabela tblParceiro gerando um relacionamento entre parceiros e subparceiros

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

        $this->idEmpresa = isset($_POST["idEmpresa"]) ? $_POST["idEmpresa"] : null;
        $this->observacoes = isset($_POST["observacoes"]) ? $_POST["observacoes"] : null;
        $this->token = isset($_POST["token"]) ? $_POST["token"] : null;

        if (empty($this->idEmpresa)) {
            $this->erro .= "<br>Selecione uma empresa para o parceiro.";
        }

        if (!empty($this->erro)) {
            $this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
            return;
        }

        $this->form_data['idEmpresa'] = trim($this->idEmpresa);
        $this->form_data['observacoes'] = trim($this->observacoes);
        $this->form_data['token'] = trim($this->token);

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
            $query = $this->db->query('SELECT * FROM `tblParceiro` WHERE `id` = ?', array($idParceiro));
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

    public function getParceiros($filtros = null)
    {

        $where = null;
        $limit = null;

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

        $sql = "SELECT `tblParceiro`.*, `tblEmpresa`.`razaoSocial` FROM `tblParceiro` 
        INNER JOIN `tblEmpresa` ON `tblParceiro`.`idEmpresa` = `tblEmpresa`.`id` 
        
         $where $orderby $limit";

        $query = $this->db->query($sql);

        if (!$query) {
            return array();
        }

        return $query->fetchAll();
    }

    public function adicionarParceiro()
    {

        if (empty(chk_array($this->form_data, 'idEmpresa'))) {
            $this->form_msg = '<p class="form_error">Selecione uma empresa para o parceiro.</p>';
            return;
        }

        $query = $this->db->insert('tblParceiro', array(
            'idEmpresa' => chk_array($this->form_data, 'idEmpresa'),
            'observacoes' => chk_array($this->form_data, 'observacoes'),
            'token' => chk_array($this->form_data, 'token'),
            'dataCriacao' => date('Y-m-d H:i:s')
        ));
        $this->id = $this->db->lastInsertId();

        if ($query) {
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
        } else {
            $id = isset($_POST['idParceiro']) ? decryptHash($_POST['idParceiro']) : null;
        }

        if (empty($id)) {
            $this->form_msg = $this->controller->Messages->error('ID do parceiro inválido.');
            return;
        }

        // Valide os dados do formulário
        $this->form_data = array(
            'idEmpresa' => isset($_POST['idEmpresa']) ? trim($_POST['idEmpresa']) : null,
            'observacoes' => isset($_POST['observacoes']) ? trim($_POST['observacoes']) : null,
            'token' => isset($_POST['token']) ? trim($_POST['token']) : null,
            'dataEdicao' => date('Y-m-d H:i:s')
        );

        if (empty($this->form_data['idEmpresa'])) {
            $this->erro .= "<br>Selecione uma empresa.";
        }

        if (!empty($this->erro)) {
            $this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
            return;
        }

        // Atualize o registro no banco de dados
        $query = $this->db->update('tblParceiro', 'id', $id, $this->form_data);

        if ($query) {
            $this->form_msg = $this->controller->Messages->success('Parceiro atualizado com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/parceiros/">';
            $this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/parceiros/";</script>';
        } else {
            $this->form_msg = $this->controller->Messages->error('Erro ao atualizar o parceiro.');
        }
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


            $idEmpresa = $this->db->query('SELECT `idEmpresa` FROM `tblParceiro` WHERE `id` = ?', array($id));
            $idEmpresa = $idEmpresa->fetch();
            $idEmpresa = $idEmpresa['idEmpresa'];


            // Desbloquear a empresa vinculada ao parceiro
            $query2 = $this->db->update('tblEmpresa', 'id', $idEmpresa, array('status' => 'T'));

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
