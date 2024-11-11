<?php 
class Telefone {
	public $db;

	private $idPessoa;
	private $idEmpresa;
	private $telefone;
	private $tipo;
	
	private $erro;
	
	public function __construct($db = false){
		$this->db = $db;
	}

	public function validarTelefone($idPessoa = null, $idEmpresa = null, $telefone = null, $tipo = null){ 
		$this->idPessoa = $idPessoa;
		$this->idEmpresa = $idEmpresa;
		$this->telefone = $telefone;
		$this->tipo = $tipo;
		
		if(empty($this->idPessoa) && empty($this->idEmpresa)){ $this->erro .= "<br>Pessoa ou Empresa inválida."; }
		if(empty($this->telefone)){ $this->erro .= "<br>Preencha o telefone."; }
		if(!empty($this->telefone)){
				if(strlen($this->telefone) > 50){ $this->erro .= "<br>O telefone não pode ultrapassar o limite de 50 caracteres."; }
		}
		if(empty($this->tipo)){ $this->erro .= "<br>Selecione o tipo de telefone."; }
		
		if(!empty($this->erro)){
			return $this->erro;
		}

		return true;
	}
	
	public function editarTelefone($id = null, $telefone = null, $tipo = null){
		if(empty($id) && empty($telefone) && empty($tipo)){
			return false;
		}
		
		$this->telefone = $telefone;
		$this->tipo = $tipo;

		$query = $this->db->update('tblTelefone', 'id', $id, array('telefone' => $this->telefone, 'tipo' => $this->tipo));
			
		if(!$query){
			return false;
		}
		
		return $query;
	}
	
	public function adicionarTelefone($idPessoa = null, $telefone = null, $tipo = null){
		if(empty($idPessoa) && empty($telefone) && empty($tipo)){
			return false;
		}
		
		$this->idPessoa = $idPessoa;
		$this->telefone = $telefone;
		$this->tipo = $tipo;
		
		$query = $this->db->insert('tblTelefone', array('idPessoa' => $this->idPessoa, 'telefone' => $this->telefone, 'tipo' => $this->tipo));
		
		if(!$query){
			return false;
		}
		
		return $query;
	}
	
	public function getTelefone($id = null){
		if(empty($id)){
			return;
		}
		
		$id = (int) $id;
		
		$query = $this->db->query('SELECT * FROM `tblTelefone` WHERE `id` = ?', array($id));
		
		if(!$query){
			return false;
		}
		
		return $query->fetch();		
	}

	public function getTelefones($idPessoa = null){
		if($idPessoa > 0){
			$query = $this->db->query('SELECT * FROM `tblTelefone` WHERE `idPessoa` = ?', array($idPessoa));
		}else{
			return array();
		}

		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}
	
	public function removerTelefone($id = null){
		if(empty($id)){
			return false;
		}
			
		$query = $this->db->delete('tblTelefone', 'id', $id);
		
		return $query;
	}

	//Parte da interação com os telefones usando idEmpresa
	public function getTelefonesEmpresa($idEmpresa = null){
		if($idEmpresa > 0){
			$query = $this->db->query('SELECT * FROM `tblTelefone` WHERE `idEmpresa` = ?', array($idEmpresa));
		}else{
			return array();
		}

		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}

	public function adicionarTelefoneEmpresa($idEmpresa = null, $telefone = null, $tipo = null){
		if(empty($idEmpresa) && empty($telefone) && empty($tipo)){
			return false;
		}
		
		$this->idEmpresa = $idEmpresa;
		$this->telefone = $telefone;
		$this->tipo = $tipo;
		
		$query = $this->db->insert('tblTelefone', array('idEmpresa' => $this->idEmpresa, 'telefone' => $this->telefone, 'tipo' => $this->tipo));
		
		if(!$query){
			return false;
		}
		
		return $query;
	}
}
?>