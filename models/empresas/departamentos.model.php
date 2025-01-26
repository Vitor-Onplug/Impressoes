<?php
class DepartamentosModel extends MainModel {
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

    public function validarFormDepartamento() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            return;
        }

        $this->nome = isset($_POST['nome']) ? trim($_POST['nome']) : null;
        $this->idEmpresa = isset($_POST['idEmpresa']) ? (int)$_POST['idEmpresa'] : null;

        if (empty($this->nome)) {
            $this->erro = '<br>Preencha o nome do departamento.';
        }
        if (strlen($this->nome) > 100) {
            $this->erro .= '<br>O nome do departamento não pode ultrapassar 100 caracteres.';
        }

        if (!empty($this->erro)) {
            $this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
            return;
        }

        $this->form_data['nome'] = $this->nome;

        if (chk_array($this->parametros, 1) == 'editar') {
            $this->editarDepartamento();
        } else {
            $this->form_data['idEmpresa'] = $this->idEmpresa;
            $this->adicionarDepartamento();
        }
    }

    private function adicionarDepartamento() {
        $query = $this->db->insert('tblDepartamento', $this->form_data);

        if ($query) {
            $this->form_msg = $this->controller->Messages->success('Departamento adicionado com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/empresas/index/departamentos/">';
            $this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/empresas/index/departamentos/";</script>';
        } else {
            $this->form_msg = $this->controller->Messages->error('Erro ao adicionar departamento.');
        }
    }

    private function editarDepartamento() {
        $id = decryptHash(chk_array($this->parametros, 2));

        if (empty($id)) {
            return;
        }

        $query = $this->db->update('tblDepartamento', 'id', $id, $this->form_data);

        if ($query) {
            $this->form_msg = $this->controller->Messages->success('Departamento atualizado com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/empresas/index/departamentos/">';
            $this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/empresas/index/departamentos/";</script>';
        } else {
            $this->form_msg = $this->controller->Messages->error('Erro ao atualizar departamento.');
        }
    }

    public function getDepartamentos($idEmpresa) {
        $query = $this->db->query('SELECT * FROM tblDepartamento WHERE status = "T" AND idEmpresa = ?
        ORDER BY nome', array($idEmpresa));

        if (!$query) {
            return [];
        }

        return $query->fetchAll();
    }

    public function getDepartamento($id) {
        $query = $this->db->query('SELECT * FROM tblDepartamento WHERE id = ?', array($id));

        if (!$query) {
            return [];
        }

        return $query->fetch();
    }
}
