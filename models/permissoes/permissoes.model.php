<?php 
class PermissoesModel extends MainModel {
	public $form_data;
	public $form_msg;
	public $db;

	private $erro;

	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
	}
	
	public function getPermissao($idPermissao = null){
		if(is_numeric($idPermissao) > 0){
			$query = $this->db->query('SELECT * FROM `tblPermissao` WHERE `id` = ?', array($idPermissao));
		}else{
			return;
		}
		
		if(!$query){
			return 'Registro não encontrado.';
		}
		
		$registro = $query->fetch();
		
		if(empty($registro)){
			return 'Registro inexistente.';
		}
		
		return $registro;
	}

	public function getPermissoes(){
		$query = $this->db->query("SELECT * FROM `tblPermissao` WHERE `status`='T' ORDER BY `permissao`");
		
		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}

	public function getAllPermissoes(){
		$query = $this->db->query("SELECT * FROM `tblPermissao` ORDER BY `permissao`");
		
		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}

	public function bloquearPermissao(){
		$id = null;
		
		if(is_numeric(chk_array($this->parametros, 1))){
			$id = chk_array($this->parametros, 1);
		}

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblPermissao', 'id', $id, array('status' => 'F'));
			
			$this->form_msg = $this->controller->Messages->success('Registro bloqueado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/permissoes/">';
		
			return;
		}
	}

	public function desbloquearPermissao(){
		$id = null;
		
		if(is_numeric(chk_array($this->parametros, 1))){
			$id = chk_array($this->parametros, 1);
		}

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblPermissao', 'id', $id, array('status' => 'T'));
			
			$this->form_msg = $this->controller->Messages->success('Registro desbloqueado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/permissoes/">';
		
			return;
		}
	}


}