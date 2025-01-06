<?php 
class Pessoa {
	public $db;

	private $id;
	private $nome;
	private $sobrenome;
	private $apelido;
	private $genero;
	private $dataNascimento;
	private $observacoes;
	private $avatar;
	private $idEmpresa;
	
	private $erro;
	
	public function __construct($db = false){
		$this->db = $db;
	}

	public function validarPessoa($nome = null, $sobrenome = null, $apelido = null, $genero = null, $dataNascimento = null, $observacoes = null, $idEmpresa = null){
		$this->nome = $nome;
		$this->sobrenome = $sobrenome;
		$this->apelido = $apelido;
		$this->genero = $genero;
		$this->dataNascimento = $dataNascimento;
		$this->observacoes = $observacoes;
		$this->idEmpresa = $idEmpresa;
		
		if(empty($this->nome)){ $this->erro .= "<br>Preencha o nome."; }
		if(!empty($this->nome)){
				if(strlen($this->nome) > 255){ $this->erro .= "<br>O nome não pode ultrapassar o limite de 255 caracteres."; }
		}
		if(empty($this->sobrenome)){ $this->erro .= "<br>Preencha o sobrenome."; }
		if(!empty($this->sobrenome)){
				if(strlen($this->sobrenome) > 255){ $this->erro .= "<br>O sobrenome não pode ultrapassar o limite de 255 caracteres."; }
		}
		if(empty($this->apelido)){ $this->erro .= "<br>Preencha o nome social ou tratamento."; }
		if(!empty($this->apelido)){
				if(strlen($this->apelido) > 255){ $this->erro .= "<br>O nome social ou tratamento não pode ultrapassar o limite de 255 caracteres."; }
		}
		if(empty($this->genero)){ $this->erro .= "<br>Selecione o gênero."; }
		if(empty($this->dataNascimento)){ $this->erro .= "<br>Selecione a data da nascimento."; }
		if(!empty($this->dataNascimento)){
			if(!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $this->dataNascimento)){ $this->erro .= "<br>Data de nascimento inválida."; }
		}
		
		if(!empty($this->erro)){
			return $this->erro;
		}
		
		return true;
	}
	
	public function editarPessoa($id = null, $nome = null, $sobrenome = null, $apelido = null, $genero = null, $dataNascimento = null, $observacoes = null, $idEmpresa = null){
		if(empty($id) && empty($nome) && empty($sobrenome) && empty($apelido) && empty($genero)){
			return false;
		}
		
		$this->id = $id;
		$this->nome = $nome;
		$this->sobrenome = $sobrenome;
		$this->apelido = $apelido;
		$this->genero = $genero;
		$this->dataNascimento = implode("-", array_reverse(explode("/", $this->dataNascimento)));
		$this->observacoes = $observacoes;
		$this->idEmpresa = $idEmpresa;


		$query = $this->db->update('tblPessoa', 'id', $this->id, array('nome' => $this->nome, 'sobrenome' => $this->sobrenome, 'apelido' => $this->apelido, 'genero' => $this->genero, 'dataNascimento' => $this->dataNascimento, 'observacoes' => $this->observacoes));
			
		if(!$query){
			return false;
		}
		
		return $query;
	}
	
	public function adicionarPessoa($nome = null, $sobrenome = null, $apelido = null, $genero = null, $dataNascimento = null, $observacoes = null, $idEmpresa = null){
		if(empty($nome) && empty($sobrenome) && empty($apelido) && empty($genero) && empty($dataNascimento)){
			return false;
		}
		
		$this->nome = $nome;
		$this->sobrenome = $sobrenome;
		$this->apelido = $apelido;
		$this->genero = $genero;
		$this->dataNascimento = implode("-", array_reverse(explode("/", $this->dataNascimento)));
		$this->observacoes = $observacoes;
		$this->idEmpresa = $idEmpresa;
		
		$query = $this->db->insert('tblPessoa', array('nome' => $this->nome, 'sobrenome' => $this->sobrenome, 'apelido' => $this->apelido, 'genero' => $this->genero, 'dataNascimento' => $this->dataNascimento, 'observacoes' => $this->observacoes));
		
		if(!$query){
			return false;
		}
		
		return $query;
	}
	
	public function getPessoa($id = null){
		if(empty($id)){
			return;
		}
		
		$id = (int) $id;
		
		$query = $this->db->query('SELECT * FROM `tblPessoa` WHERE `id` = ?', array($id));
		
		if(!$query){
			return false;
		}
		
		return $query->fetch();
	}

	public function getPessoas($filtros = null){
		$where = null;
		$limit = null;
		
		if(!empty($filtros["genero"])){
			if(!empty($where)){
				$where .= " AND ";
			}else{
				$where = " WHERE ";
			}
			
			$where .= "(`tblPessoa`.`genero` = '" . $filtros['genero'] . "')";
		}
		
		if(!empty($filtros["status"])){
			if(!empty($where)){
				$where .= " AND ";
			}else{
				$where = " WHERE ";
			}
			
			$where .= "(`tblPessoa`.`status` = '" . $filtros['status'] . "')";
		}
		
		if(!empty($filtros["q"])){
			if(!empty($where)){
				$where .= " AND ";
			}else{
				$where = " WHERE ";
			}
			
			$where .= "((`tblPessoa`.`nome` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`tblPessoa`.`sobrenome` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR ";
			$where .= "(`tblPessoa`.`apelido` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`tblPessoa`.`genero` LIKE '%" . _otimizaBusca($filtros['q']) . "%')  OR (`tblPessoa`.`dataNascimento` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`tblPessoa`.`observacoes` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR ";
			$where .= "(`tblDocumento`.`tipo` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`tblDocumento`.`titulo` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`tblDocumento`.`documento` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR ";
			$where .= "(`tblEndereco`.`titulo` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`tblEndereco`.`cep` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`tblEndereco`.`logradouro` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR ";
			$where .= "(`tblEndereco`.`numero` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`tblEndereco`.`bairro` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`tblEndereco`.`cidade` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR ";
			$where .= "(`tblEndereco`.`estado` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`tblEndereco`.`complemento` LIKE '%" . _otimizaBusca($filtros['q']) . "%')  OR (`tblEndereco`.`zona` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`tblEndereco`.`latitude` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`tblEndereco`.`longitude` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR ";
			$where .= "(`tblEmail`.`email` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`tblTelefone`.`telefone` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`tblTelefone`.`tipo` LIKE '%" . _otimizaBusca($filtros['q']) . "%'))";
		}
		
		if(!empty($filtros["limite"])){
			$limit = "LIMIT " . $filtros["limite"];
		}
		
		if(!empty($filtros["ordena"]) && !empty($filtros["ordem"])){
			$orderby = "ORDER BY " . $filtros["ordena"] . " " .  $filtros["ordem"];
		}else{
			$oderby = "ORDER BY `tblPessoa`.`nome`, `tblPessoa`.`sobrenome`";
		}
		
		$sql = "SELECT `tblPessoa`.*, `tblEmail`.`email`, `tblTelefone`.`telefone`, `tblEmpresa`.`razaoSocial` as empresaNome, `tblEmpresa`.`id` as empresaId 
		 FROM `tblPessoa` ";
		$sql .= "LEFT JOIN `tblDocumento` ON (`tblDocumento`.`idPessoa` = `tblPessoa`.`id`) ";
		$sql .= "LEFT JOIN `tblEmail` ON (`tblEmail`.`idPessoa` = `tblPessoa`.`id`) ";
		$sql .= "LEFT JOIN `tblEndereco` ON (`tblEndereco`.`idPessoa` = `tblPessoa`.`id`) ";
		$sql .= "LEFT JOIN `tblTelefone` ON (`tblTelefone`.`idPessoa` = `tblPessoa`.`id`) ";
		$sql .= "LEFT JOIN `tblUsuario` ON (`tblUsuario`.`idPessoa` = `tblPessoa`.`id`) ";
		$sql .= "LEFT JOIN `tblEmpresa` ON (`tblEmpresa`.`id` = `tblUsuario`.`idEmpresa`) ";
		$sql .= "$where GROUP BY `tblPessoa`.`id` $oderby $limit";
		
		$query = $this->db->query($sql);

		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}
}
?>