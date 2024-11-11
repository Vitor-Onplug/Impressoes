<?php 
class Documento {
	public $db;

	private $idPessoa;
	private $idEmpresa;
	private $tipo;
	private $titulo;
	private $documento;	
	private $detalhes;
	
	private $erro;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
	}

	public function validarDocumento($idPessoa = null, $idEmpresa = null, $tipo = null, $titulo = null, $documento = null, $editar = false, $detalhes =null){ 
		$this->idPessoa = $idPessoa;
		$this->idEmpresa = $idEmpresa;
		$this->tipo = $tipo;
		$this->titulo = $titulo;
		$this->documento = $documento;	
		$this->detalhes = $detalhes;
		
		if(empty($this->idPessoa) && empty($this->idEmpresa)){ $this->erro .= "<br>Pessoa ou Empresa inválida."; }
		if(empty($this->tipo)){ $this->erro .= "<br>Selecione o tipo de documento."; }
		if(empty($this->titulo)){ $this->erro .= "<br>Preencha o título do documento."; }
		if(strlen($this->titulo) > 255){ $this->erro .= "<br>O título não pode ultrapassar o limite de 255 caracteres."; }
		if(empty($this->documento)){ $this->erro .= "<br>Preencha o documento."; }
		if(strlen($this->documento) > 255){ $this->erro .= "<br>O documento não pode ultrapassar o limite de 255 caracteres."; }
		if(!empty($this->documento)){
			
			if($this->tipo == "CPF"){
				if(!_vcpfj($this->documento)){ $this->erro .= "<br>CPF inválido."; }
			}
			else if($this->tipo == "CNPJ"){
				if(!_vcpfj($this->documento)){ $this->erro .= "<br>CNPJ inválido."; }
			}
			else if($this->tipo == "Passaporte"){
				if(!validarPassaporte($this->documento) < 6){ $this->erro .= "<br>Passaporte inválido."; }
			}
			else if($this->tipo == "CNH"){
				if(!validarCNH($this->documento)){ $this->erro .= "<br>CNH inválida."; }
			}
			
			$registro = array();

			if($this->idPessoa > 0 && $this->idPessoa != null){
				$query = $this->db->query('SELECT * FROM `tblDocumento` WHERE `documento` = ? AND `idPessoa` > 0', array($this->documento));
				$registro = $query->fetch();
			}
						
			if ($this->idEmpresa > 0 && $this->idEmpresa != null){
				$query = $this->db->query('SELECT * FROM `tblDocumento` WHERE `documento` = ? AND `idEmpresa` > 0', array($this->documento));
				$registro = $query->fetch();
			}
						
			$registro = $query->fetch();
			
			if(!empty($registro)){
				$this->erro .= "<br>Documento já cadastrado.";
			}
		}
		
		if(!empty($this->erro)){
			return $this->erro;
		}

		return true;
	}
	
	public function editarDocumento($id = null, $tipo = null, $titulo = null, $documento = null, $detalhes = null){
		if(empty($id) && empty($tipo) && empty($titulo) && empty($documento)){
			return false;
		}
		
		$this->tipo = $tipo;
		$this->titulo = $titulo;
		$this->documento = $documento;
		$this->detalhes = $detalhes;

		$query = $this->db->update('tblDocumento', 'id', $id, array('tipo' => $this->tipo, 'titulo' => $this->titulo, 'documento' => $this->documento, 'detalhes' => $this->detalhes));
			
		if(!$query){
			return false;
		}
		
		return $query;
	}
	
	public function adicionarDocumento($idPessoa = null, $tipo = null, $titulo = null, $documento = null, $detalhes = null){
		if(empty($idPessoa) && empty($tipo) && empty($titulo) && empty($documento)){
			return false;
		}
		
		$this->idPessoa = $idPessoa;
		$this->tipo = $tipo;
		$this->titulo = $titulo;
		$this->documento = $documento;
		$this->detalhes = $detalhes;
		
		$query = $this->db->insert('tblDocumento', array('idPessoa' => $this->idPessoa, 'tipo' => $this->tipo, 'titulo' => $this->titulo, 'documento' => $this->documento, 'detalhes' => $this->detalhes));
		
		if(!$query){
			return false;
		}
		
		return $query;
	}
	
	public function getDocumento($id = null){
		if(empty($id)){
			return;
		}
		
		$id = (int) $id;
		
		$query = $this->db->query('SELECT * FROM `tblDocumento` WHERE `id` = ?', array($id));
		
		if(!$query){
			return false;
		}
		
		return $query->fetch();		
	}

	public function getDocumentos($idPessoa = null){
		if($idPessoa > 0){
			$query = $this->db->query('SELECT * FROM `tblDocumento` WHERE `idPessoa` = ?', array($idPessoa));
		}else{
			return array();
		}

		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}
	
	public function removerDocumento($id = null){
		if(empty($id)){
			return false;
		}
			
		$query = $this->db->delete('tblDocumento', 'id', $id);
		
		return $query;
	}

	//Parte de interagir usando o idEmpresa
	public function adicionarDocumentoEmpresa($idEmpresa = null, $tipo = null, $titulo = null, $documento = null, $detalhes = null){
		if(empty($idEmpresa) && empty($tipo) && empty($titulo) && empty($documento)){
			return false;
		}
		
		$this->idEmpresa = $idEmpresa;
		$this->tipo = $tipo;
		$this->titulo = $titulo;
		$this->documento = $documento;
		$this->detalhes = $detalhes;
		
		$query = $this->db->insert('tblDocumento', array('idEmpresa' => $this->idEmpresa, 'tipo' => $this->tipo, 'titulo' => $this->titulo, 'documento' => $this->documento, 'detalhes' => $this->detalhes));
		
		if(!$query){
			return false;
		}
		
		return $query;
	}

	public function getDocumentosEmpresa($idEmpresa = null){
		if($idEmpresa > 0){
			$query = $this->db->query('SELECT * FROM `tblDocumento` WHERE `idEmpresa` = ?', array($idEmpresa));
		}else{
			return array();
		}

		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}
}
?>