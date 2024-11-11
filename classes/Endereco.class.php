<?php 
class Endereco {
	public $db;

	private $idPessoa;
	private $idEmpresa;
	private $titulo;
	private $cep;
	private $logradouro;
	private $numero;
	private $complemento;
	private $zona;
	private $bairro;
	private $cidade;
	private $estado;
	private $latitude;
	private $longitude;

	private $erro;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
	}

	public function validarEndereco($idPessoa = null, $idEmpresa = null, $titulo = null, $cep = null, $logradouro = null, $numero = null, $complemento = null, $zona = null, $bairro = null, $cidade = null, $estado = null, $latitude = null, $longitude = null){ 
		$this->idPessoa = $idPessoa;
		$this->idEmpresa = $idEmpresa;
		$this->titulo = $titulo;
		$this->cep = $cep;
		$this->logradouro = $logradouro;
		$this->numero = $numero;
		$this->complemento = $complemento;
		$this->zona = $zona;
		$this->bairro = $bairro;
		$this->cidade = $cidade;
		$this->estado = $estado;
		$this->latitude = $latitude;
		$this->longitude = $longitude;
		
		if(empty($this->idPessoa) && empty($this->idEmpresa)){ $this->erro .= "<br>Pessoa ou Empresa inválida."; }
		if(empty($this->titulo)){ $this->erro .= "<br>Preencha o título do endereço."; }
		if(strlen($this->titulo) > 255){ $this->erro .= "<br>O título não pode ultrapassar o limite de 255 caracteres."; }
		if(empty($this->cep)){ $this->erro .= "<br>Preencha o CEP."; }
		if(!empty($this->cep)){
		if(strlen($this->cep) > 20){ $this->erro .= "<br>O cep não pode ultrapassar o limite de 20 caracteres."; }
			if(!preg_match('/^[0-9]{5}-[0-9]{3}$/', $this->cep)){ $this->erro .= "<br>CEP inválido."; }
		}
		if(empty($this->logradouro)){ $this->erro .= "<br>Preencha o logradouro."; }
		if(strlen($this->logradouro) > 255){ $this->erro .= "<br>O logradouro não pode ultrapassar o limite de 255 caracteres."; }
		if(empty($this->numero)){ $this->erro .= "<br>Preencha o número."; }
		if(strlen($this->numero) > 50){ $this->erro .= "<br>O número não pode ultrapassar o limite de 50 caracteres."; }
		if(strlen($this->complemento) > 255){ $this->erro .= "<br>O complemento não pode ultrapassar o limite de 255 caracteres."; }
		if(empty($this->bairro)){ $this->erro .= "<br>Preencha o bairro."; }
		if(strlen($this->bairro) > 255){ $this->erro .= "<br>O bairro não pode ultrapassar o limite de 255 caracteres."; }
		if(empty($this->cidade)){ $this->erro .= "<br>Preencha a cidade."; }
		if(strlen($this->cidade) > 255){ $this->erro .= "<br>O cidade não pode ultrapassar o limite de 255 caracteres."; }
		if(empty($this->estado)){ $this->erro .= "<br>Selecione o estado."; }
		if(empty($this->latitude) && empty($this->longitude)){ $this->erro .= "<br />Marque um ponto no mapa."; }
		
		if(!empty($this->erro)){
			return $this->erro;
		}

		return true;
	}
	
	public function editarEndereco($id = null, $titulo = null, $cep = null, $logradouro = null, $numero = null, $complemento = null, $zona = zona, $bairro = null, $cidade = null, $estado = null, $latitude = null, $longitude = null){ 
		if(empty($id) && empty($titulo) && empty($cep) && empty($logradouro) && empty($numero) && empty($bairro) && empty($cidade) && empty($estado) && empty($latitude) && empty($longitude)){
			return false;
		}
		
		$this->titulo = $titulo;
		$this->cep = $cep;
		$this->logradouro = $logradouro;
		$this->numero = $numero;
		$this->complemento = $complemento;
		$this->zona = $zona;
		$this->bairro = $bairro;
		$this->cidade = $cidade;
		$this->estado = $estado;
		$this->latitude = $latitude;
		$this->longitude = $longitude;

		$query = $this->db->update('tblEndereco', 'id', $id, array('titulo' => $this->titulo, 'cep' => $this->cep, 'logradouro' => $this->logradouro, 'numero' => $this->numero, 'complemento' => $this->complemento, 'zona' => $this->zona, 'bairro' => $this->bairro, 'cidade' => $this->cidade, 'estado' => $this->estado, 'latitude' => $this->latitude, 'longitude' => $this->longitude));
			
		if(!$query){
			return false;
		}
		
		return $query;
	}
	
	public function adicionarEndereco($idPessoa = null, $titulo = null, $cep = null, $logradouro = null, $numero = null, $complemento = null, $zona = null, $bairro = null, $cidade = null, $estado = null, $latitude = null, $longitude = null){ 
		if(empty($idPessoa) && empty($titulo) && empty($cep) && empty($logradouro) && empty($numero) && empty($bairro) && empty($cidade) && empty($estado) && empty($latitude) && empty($longitude)){
			return false;
		}
		
		$this->idPessoa = $idPessoa;
		$this->titulo = $titulo;
		$this->cep = $cep;
		$this->logradouro = $logradouro;
		$this->numero = $numero;
		$this->complemento = $complemento;
		$this->zona = $zona;
		$this->bairro = $bairro;
		$this->cidade = $cidade;
		$this->estado = $estado;
		$this->latitude = $latitude;
		$this->longitude = $longitude;
		
		$query = $this->db->insert('tblEndereco', array('idPessoa' => $this->idPessoa, 'titulo' => $this->titulo, 'cep' => $this->cep, 'logradouro' => $this->logradouro, 'numero' => $this->numero, 'complemento' => $this->complemento, 'zona' => $this->zona, 'bairro' => $this->bairro, 'cidade' => $this->cidade, 'estado' => $this->estado, 'latitude' => $this->latitude, 'longitude' => $this->longitude));
		
		if(!$query){
			return false;
		}
		
		return $query;
	}
	
	public function getEndereco($id = null){
		if(empty($id)){
			return;
		}
		
		$id = (int) $id;
		
		$query = $this->db->query('SELECT * FROM `tblEndereco` WHERE `id` = ?', array($id));
		
		if(!$query){
			return false;
		}
		
		return $query->fetch();		
	}

	public function getEnderecos($idPessoa = null){
		if($idPessoa > 0){
			$query = $this->db->query('SELECT * FROM `tblEndereco` WHERE `idPessoa` = ?', array($idPessoa));
		}else{
			return array();
		}

		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}
	
	public function removerEndereco($id = null){
		if(empty($id)){
			return false;
		}
			
		$query = $this->db->delete('tblEndereco', 'id', $id);
		
		return $query;
	}

	// Parte da interação com os endereços usando idEmpresa
	public function getEnderecosEmpresa($idEmpresa = null){
		if($idEmpresa > 0){
			$query = $this->db->query('SELECT * FROM `tblEndereco` WHERE `idEmpresa` = ?', array($idEmpresa));
		}else{
			return array();
		}

		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}

	public function adicionarEnderecoEmpresa($idEmpresa = null, $titulo = null, $cep = null, $logradouro = null, $numero = null, $complemento = null, $zona = null, $bairro = null, $cidade = null, $estado = null, $latitude = null, $longitude = null){ 
		if(empty($idEmpresa) && empty($titulo) && empty($cep) && empty($logradouro) && empty($numero) && empty($bairro) && empty($cidade) && empty($estado) && empty($latitude) && empty($longitude)){
			return false;
		}
		
		$this->idEmpresa = $idEmpresa;
		$this->titulo = $titulo;
		$this->cep = $cep;
		$this->logradouro = $logradouro;
		$this->numero = $numero;
		$this->complemento = $complemento;
		$this->zona = $zona;
		$this->bairro = $bairro;
		$this->cidade = $cidade;
		$this->estado = $estado;
		$this->latitude = $latitude;
		$this->longitude = $longitude;
		
		$query = $this->db->insert('tblEndereco', array('idEmpresa' => $this->idEmpresa, 'titulo' => $this->titulo, 'cep' => $this->cep, 'logradouro' => $this->logradouro, 'numero' => $this->numero, 'complemento' => $this->complemento, 'zona' => $this->zona, 'bairro' => $this->bairro, 'cidade' => $this->cidade, 'estado' => $this->estado, 'latitude' => $this->latitude, 'longitude' => $this->longitude));
		
		if(!$query){
			return false;
		}
		
		return $query;
	}

}
?>