<?php 
class Email {
	public $db;

	private $idPessoa;
	private $email;
	private $idEmpresa;
	
	private $erro;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
	}

	public function validarEmail($idPessoa = null, $idEmpresa = null, $email = null, $editar = false){ 
		$this->idPessoa = $idPessoa;
		$this->idEmpresa = $idEmpresa;
		$this->email = $email;
		
		if(empty($this->idPessoa) && empty($this->idEmpresa)){ $this->erro .= "<br>Pessoa ou Empresa inválida."; }
		if(empty($this->email)){ $this->erro .= "<br>Preencha o email."; }
			if(strlen($this->email) > 255){ $this->erro .= "<br>O email não pode ultrapassar o limite de 255 caracteres."; }
		if(!empty($this->email)){
			if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
				$this->erro .= "<br>Preencha o email corretamente."; 
			}else{ 
				list($user, $domain) = explode("@", $this->email, 2);     
				if(!checkdnsrr($domain, "MX")){ 
					$this->erro .= "<br>Email não existe ou com problemas no DNS."; 
				}
			}

			$registro = array();

			if($this->idPessoa > 0 && $this->idPessoa != null){
				$query = $this->db->query('SELECT * FROM `tblEmail` WHERE `email` = ? AND `idPessoa` > 0', array($this->email));
				$registro = $query->fetch();
			}
						
			if ($this->idEmpresa > 0 && $this->idEmpresa != null){
				$query = $this->db->query('SELECT * FROM `tblEmail` WHERE `email` = ? AND `idEmpresa` = ?', array($this->email, $this->idEmpresa));
				$registro = $query->fetch();
			}
			
			if(!empty($registro)){
				$this->erro .= "<br>Email já cadastrado.";
			}
		}
		
		if(!empty($this->erro)){
			return $this->erro;
		}

		return true;
	}
	
	public function editarEmail($id = null, $email = null){
		if(empty($id) && empty($email)){
			return false;
		}
		
		$this->email = $email;

		$query = $this->db->update('tblEmail', 'id', $id, array('email' => $this->email));
			
		if(!$query){
			return false;
		}
		
		return $query;
	}
	
	public function adicionarEmail($idPessoa = null, $email = null){
		if(empty($idPessoa) && empty($email)){
			return false;
		}
		
		$this->idPessoa = $idPessoa;
		$this->email = $email;
		
		$query = $this->db->insert('tblEmail', array('idPessoa' => $this->idPessoa, 'email' => $this->email));
		
		if(!$query){
			return false;
		}
		
		return $query;
	}
	
	public function getEmail($id = null){
		if(empty($id)){
			return;
		}
		
		$id = (int) $id;
		
		$query = $this->db->query('SELECT * FROM `tblEmail` WHERE `id` = ?', array($id));
		
		if(!$query){
			return false;
		}
		
		return $query->fetch();		
	}

	public function getEmails($idPessoa = null){
		if($idPessoa > 0){
			$query = $this->db->query('SELECT * FROM `tblEmail` WHERE `idPessoa` = ?', array($idPessoa));
		}else{
			return array();
		}

		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}
	
	public function removerEmail($id = null){
		if(empty($id)){
			return false;
		}
			
		$query = $this->db->delete('tblEmail', 'id', $id);
		
		return $query;
	}

	//Parte da interação com os emails usando idEmpresa
	public function getEmailsEmpresa($idEmpresa = null){
		if($idEmpresa > 0){
			$query = $this->db->query('SELECT * FROM `tblEmail` WHERE `idEmpresa` = ?', array($idEmpresa));
		}else{
			return array();
		}

		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}

	public function adicionarEmailEmpresa($idEmpresa = null, $email = null){
		if(empty($idEmpresa) && empty($email)){
			return false;
		}
		
		$this->idEmpresa = $idEmpresa;
		$this->email = $email;
		
		$query = $this->db->insert('tblEmail', array('idEmpresa' => $this->idEmpresa, 'email' => $this->email));
		
		if(!$query){
			return false;
		}
		
		return $query;
	}
}
?>