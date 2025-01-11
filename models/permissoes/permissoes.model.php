<?php
class PermissoesModel extends MainModel
{
	public $form_data;
	public $form_msg;
	public $db;

	private $id;
	private $permissao;
	private $modulo;
	private $idEmpresa;

	private $erro;

	public function __construct($db = false, $controller = null)
	{
		$this->db = $db;

		$this->controller = $controller;

		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
	}

	public function validarFormPermissao()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			return;
		}

		$this->form_data = array();

		// Pega os dados do POST
		$this->id = isset($_POST["id"]) ? $_POST["id"] : null;
		$this->permissao = isset($_POST["permissao"]) ? trim($_POST["permissao"]) : null;
		$this->modulo = isset($_POST["modulos"]) ? $_POST["modulos"] : null;
		$this->idEmpresa = isset($_POST["idEmpresa"]) ? $_POST["idEmpresa"] : null;

		// Validações
		if (empty($this->permissao)) {
			$this->erro .= "<br>Preencha o nome da permissão.";
		}
		if (strlen($this->permissao) > 255) {
			$this->erro .= "<br>O nome da permissão não pode ultrapassar 255 caracteres.";
		}
		if (empty($this->modulo)) {
			$this->erro .= "<br>Selecione pelo menos um módulo.";
		}

		// Verifica se há erros
		if (!empty($this->erro)) {
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
			return;
		}

		// Prepara os dados para inserção/atualização
		$this->form_data = array(
			'permissao' => $this->permissao,
			'modulo' => $this->modulo,
			'idEmpresa' => $this->idEmpresa,
			'status' => 'T' // Por padrão ativa
		);

		// Verifica se é edição ou adição
		if (chk_array($this->parametros, 0) == 'editar') {
			$this->editarPermissao();
		} else {
			$this->adicionarPermissao();
		}
	}

	private function adicionarPermissao()
	{
		// Verifica se já existe uma permissão com o mesmo nome
		$query = $this->db->query(
			"SELECT id FROM tblPermissao WHERE permissao = ?",
			array($this->permissao)
		);

		if ($query->fetch()) {
			$this->form_msg = $this->controller->Messages->error('Já existe uma permissão com este nome.');
			return;
		}

		// Insere a nova permissão
		$query = $this->db->insert('tblPermissao', $this->form_data);

		if ($query) {
			$this->form_msg = $this->controller->Messages->success('Permissão adicionada com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/configuracoes/permissoes">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/configuracoes/permissoes";</script>';

			$this->form_data = null;
		} else {
			$this->form_msg = $this->controller->Messages->error('Erro ao adicionar permissão.');
		}
	}

	private function editarPermissao()
	{
		$id = decryptHash($this->id);

		if (empty($id)) {
			return;
		}

		// Verifica se já existe outra permissão com o mesmo nome
		$query = $this->db->query(
			"SELECT id FROM tblPermissao WHERE permissao = ? AND id != ?",
			array($this->permissao, $id)
		);

		if ($query->fetch()) {
			$this->form_msg = $this->controller->Messages->error('Já existe outra permissão com este nome.');
			return;
		}

		// Atualiza a permissão
		$query = $this->db->update('tblPermissao', 'id', $id, $this->form_data);

		if ($query) {
			$this->form_msg = $this->controller->Messages->success('Permissão atualizada com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/configuracoes/permissoes">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/configuracoes/permissoes";</script>';

			$this->form_data = null;
		} else {
			$this->form_msg = $this->controller->Messages->error('Erro ao atualizar permissão.');
		}
	}

	public function getPermissao($idPermissao = null)
	{
		if (is_numeric($idPermissao) > 0) {
			$query = $this->db->query('SELECT * FROM `tblPermissao` WHERE `id` = ?', array($idPermissao));
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

	public function getPermissoes($filtros)
	{

		$idEmpresa = $filtros['idEmpresa'];

		$status = $filtros['status'];

		$query = $this->db->query("SELECT * FROM `tblPermissao` 
		WHERE `status`= ?
		AND `idEmpresa` = ?
		ORDER BY `permissao`", array($status, $idEmpresa));

		if (!$query) {
			return array();
		}

		return $query->fetchAll();
	}

	public function getAllPermissoes()
	{
		$query = $this->db->query("SELECT * FROM `tblPermissao` ORDER BY `permissao`");

		if (!$query) {
			return array();
		}

		return $query->fetchAll();
	}

	public function bloquearPermissao()
	{
		$id = null;

		if (is_numeric(chk_array($this->parametros, 1))) {
			$id = chk_array($this->parametros, 1);
		}

		if (!empty($id)) {
			$id = (int) $id;

			$query = $this->db->update('tblPermissao', 'id', $id, array('status' => 'F'));

			$this->form_msg = $this->controller->Messages->success('Registro bloqueado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/permissoes/">';

			return;
		}
	}

	public function desbloquearPermissao()
	{
		$id = null;

		if (is_numeric(chk_array($this->parametros, 1))) {
			$id = chk_array($this->parametros, 1);
		}

		if (!empty($id)) {
			$id = (int) $id;

			$query = $this->db->update('tblPermissao', 'id', $id, array('status' => 'T'));

			$this->form_msg = $this->controller->Messages->success('Registro desbloqueado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/permissoes/">';

			return;
		}
	}
}
