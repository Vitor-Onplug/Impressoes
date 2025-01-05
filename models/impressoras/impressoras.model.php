<?php 
class ImpressorasModel extends MainModel {
    public $form_data;
    public $form_msg;
    public $db;

    private $id;
    private $nome;
    private $idMarca;
    private $modelo;
    private $ip;
    private $descricao;
    private $status;
    private $idDepartamento;
    
    private $erro;

    public function __construct($db = false, $controller = null){
        $this->db = $db;
        $this->controller = $controller;
        $this->parametros = $this->controller->parametros;
        $this->userdata = $this->controller->userdata;
    }

    // Função para validar e salvar uma impressora
    public function validarFormImpressora() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            return;
        }

        $this->form_data = array();

        $this->id = isset($_POST["idImpressora"]) ? $_POST["idImpressora"] : null;
        $this->nome = isset($_POST["nome"]) ? trim($_POST["nome"]) : null;
        $this->idMarca = isset($_POST["idMarca"]) ? (int)$_POST["idMarca"] : null;
        $this->idDepartamento = isset($_POST["idDepartamento"]) ? (int)$_POST["idDepartamento"] : null;
        $this->modelo = isset($_POST["modelo"]) ? trim($_POST["modelo"]) : null;
        $this->ip = isset($_POST["ip"]) ? trim($_POST["ip"]) : null;
        $this->descricao = isset($_POST["descricao"]) ? trim($_POST["descricao"]) : null;

        // Validações
        if (empty($this->nome)) {
            $this->erro .= "<br>Preencha o nome da impressora.";
        }
        if (strlen($this->nome) > 100) {
            $this->erro .= "<br>O nome da impressora não pode ultrapassar 100 caracteres.";
        }
        if (empty($this->idMarca)) {
            $this->erro .= "<br>Selecione uma marca.";
        }
        if (empty($this->idDepartamento)) {
            $this->erro .= "<br>Selecione um departamento.";
        }
        if (empty($this->ip)) {
            $this->erro .= "<br>Preencha o IP da impressora.";
        }

        // Verifica se há erros
        if (!empty($this->erro)) {
            $this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
            return;
        }

        $this->form_data = array(
            'nome' => $this->nome,
            'idMarca' => $this->idMarca,
            'idDepartamento' => $this->idDepartamento,
            'modelo' => $this->modelo,
            'ip' => $this->ip,
            'descricao' => $this->descricao
        );

        if (chk_array($this->parametros, 0) == 'editar') {
            $this->editarImpressora();
        } else {
            $this->adicionarImpressora();
        }
    }

    private function adicionarImpressora() {
        $query = $this->db->insert('tblImpressora', $this->form_data);

        if ($query) {
            $this->form_msg = $this->controller->Messages->success('Impressora adicionada com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/impressoras/">';
            $this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/impressoras/";</script>';
        } else {
            $this->form_msg = $this->controller->Messages->error('Erro ao adicionar impressora.');
        }
    }

    private function editarImpressora() {
        $id = decryptHash($this->id);
        
        if (empty($id)) {
            return;
        }

        $query = $this->db->update('tblImpressora', 'id', $id, $this->form_data);

        if ($query) {
            $this->form_msg = $this->controller->Messages->success('Impressora atualizada com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/impressoras/">';
            $this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/impressoras/";</script>';
        } else {
            $this->form_msg = $this->controller->Messages->error('Erro ao atualizar impressora.');
        }
    }

    public function getImpressora($id = null){
        if (empty($id)) {
            return;
        }

        $query = $this->db->query('SELECT * FROM `tblImpressora` WHERE `id` = ?', array($id));

        if (!$query) {
            return false;
        }

        return $query->fetch();
    }

    public function getImpressoras($filtros = null) {
        $where = " 1=1 ";
        $orderby = "";

        if (!empty($filtros["q"])) {
            $where = " (`tblImpressora`.`nome` LIKE '%" . _otimizaBusca($filtros['q']) . "%')";
        }

        if (!empty($filtros["status"])) {
            $where .= " AND (`tblImpressora`.`status` = '" . $filtros['status'] . "')";
        }

        $orderby = "ORDER BY `tblImpressora`.`nome`";

        $sql = "SELECT `tblImpressora`.*, `tblMarcaImpressora`.`nome` AS marcaNome, `tblDepartamento`.`nome` AS departamentoNome
                FROM `tblImpressora`
                LEFT JOIN `tblMarcaImpressora` ON `tblImpressora`.`idMarca` = `tblMarcaImpressora`.`id`
                LEFT JOIN `tblDepartamento` ON `tblImpressora`.`idDepartamento` = `tblDepartamento`.`id`
                WHERE $where $orderby";

        $query = $this->db->query($sql);

        if (!$query) {
            return array();
        }

        return $query->fetchAll();
    }

    // Funções para gerenciar marcas
    public function adicionarMarca() {

        $nome = isset($_POST["novaMarca"]) ? trim($_POST["novaMarca"]) : null;

        if (empty($nome)) {
            return false;
        }

        $query = $this->db->insert('tblMarcaImpressora', array('nome' => $nome));

        if ($query) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function getMarcas() {
        $query = $this->db->query('SELECT * FROM `tblMarcaImpressora` WHERE `status` = "T" ORDER BY `nome`');

        if (!$query) {
            return array();
        }

        return $query->fetchAll();
    }

    // Funções para gerenciar departamentos
    public function adicionarDepartamento() {

        $nome = isset($_POST["novoDepartamento"]) ? trim($_POST["novoDepartamento"]) : null;

        if (empty($nome)) {
            return false;
        }

        $query = $this->db->insert('tblDepartamento', array('nome' => $nome));

        if ($query) {
           echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function getDepartamentos() {
        $query = $this->db->query('SELECT * FROM `tblDepartamento` WHERE `status` = "T" ORDER BY `nome`');

        if (!$query) {
            return array();
        }

        return $query->fetchAll();
    }
    
	public function bloquearImpressora(){
		$id = null;
		
        if (chk_array($this->parametros, 1)) {
            $hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
        }

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblImpressora', 'id', $id, array('status' => 'F'));
			
			$this->form_msg = $this->controller->Messages->success('Registro bloqueado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/pessoas/">';
		
			return;
		}
	}
	
	public function desbloquearImpressora(){
		$id = null;

        if (chk_array($this->parametros, 1)) {
            $hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
        }
		
		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblImpressora', 'id', $id, array('status' => 'T'));
			
			$this->form_msg = $this->controller->Messages->success('Registro desbloqueado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/pessoas/">';
		
			return;
		}
	}

}
