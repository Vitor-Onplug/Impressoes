<?php
class MarcasModel extends MainModel {
    public $form_data;
    public $form_msg;
    public $db;

    private $id;
    private $nome;
    private $idEmpresa;
    private $erro;

    public function __construct($db = false, $controller = null) {
        $this->db = $db;
        $this->controller = $controller;
        $this->parametros = $this->controller->parametros;
        $this->userdata = $this->controller->userdata;
    }

    public function validarFormMarca() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            return;
        }

        $this->nome = isset($_POST['nome']) ? trim($_POST['nome']) : null;
        $this->idEmpresa = isset($_POST['idEmpresa']) ? (int)$_POST['idEmpresa'] : null;

        if (empty($this->nome)) {
            $this->erro = '<br>Preencha o nome da marca.';
        }
        if (strlen($this->nome) > 100) {
            $this->erro .= '<br>O nome da marca não pode ultrapassar 100 caracteres.';
        }

        if (!empty($this->erro)) {
            $this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
            return;
        }

        $this->form_data['nome'] = $this->nome;

        if (chk_array($this->parametros, 1) == 'editar') {
            $this->editarMarca();
        } else {
            $this->form_data['idEmpresa'] = $this->idEmpresa;
            $this->adicionarMarca();
        }
    }

    private function adicionarMarca() {
        $query = $this->db->insert('tblMarcaImpressora', $this->form_data);

        if ($query) {
            $this->form_msg = $this->controller->Messages->success('Marca adicionada com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/impressoras/index/marcas/">';
            $this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/impressoras/index/marcas/";</script>';
        } else {
            $this->form_msg = $this->controller->Messages->error('Erro ao adicionar marca.');
        }
    }

    private function editarMarca() {
        $id = decryptHash(chk_array($this->parametros, 2));

        if (empty($id)) {
            return;
        }

        $query = $this->db->update('tblMarcaImpressora', 'id', $id, $this->form_data);

        if ($query) {
            $this->form_msg = $this->controller->Messages->success('Marca atualizada com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/impressoras/index/marcas/">';
            $this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/impressoras/index/marcas/";</script>';
        } else {
            $this->form_msg = $this->controller->Messages->error('Erro ao atualizar marca.');
        }
    }

    public function getMarcas($idEmpresa = null) {
        $idEmpresa = (int)$idEmpresa;

        if (empty($idEmpresa)) {
            return;
        }

        $query = $this->db->query('SELECT * FROM tblMarcaImpressora WHERE idEmpresa = ? AND status = "T" 
        ORDER BY nome', array($idEmpresa));

        return $query->fetchAll();
    }

    public function getMarca($id) {
        $query = $this->db->query('SELECT * FROM tblMarcaImpressora WHERE id = ?', array($id));

        if (!$query) {
            return [];
        }

        return $query->fetch();
    }

}


