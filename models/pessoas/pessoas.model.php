<?php 
class PessoasModel extends MainModel {
	public $form_data;
	public $form_msg;
	public $db;

	private $id;
	private $nome;
	private $sobrenome;
	private $apelido;
	private $genero;
	private $dataNascimento;
	private $observacoes;
	private $avatar;
	
	private $erro;

	private $ClassePessoa;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
		
		$this->ClassePessoa = new Pessoa($this->db);
	}

	public function validarFormPessoa(){ 
		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}
		
		$this->form_data = array();
		
		$this->nome = isset($_POST["nome"]) ? $_POST["nome"] : null;
		$this->sobrenome = isset($_POST["sobrenome"]) ? $_POST["sobrenome"] : null;
		$this->apelido = isset($_POST["apelido"]) ? $_POST["apelido"] : null;
		$this->genero = isset($_POST["genero"]) ? $_POST["genero"] : null;
		$this->dataNascimento = isset($_POST["dataNascimento"]) ? $_POST["dataNascimento"] : null;
		$this->avatar = isset($_POST["diretorioAvatar"]) ? $_POST["diretorioAvatar"] : null;
		
		$validaPessoa = $this->ClassePessoa->validarPessoa($this->nome, $this->sobrenome, $this->apelido, $this->genero, $this->dataNascimento, $this->observacoes);
		
		$this->form_data['nome'] = trim($this->nome);
		$this->form_data['sobrenome'] = trim($this->sobrenome);
		$this->form_data['apelido'] = trim($this->apelido);
		$this->form_data['genero'] = trim($this->genero);
		$this->form_data['dataNascimento'] = trim($this->dataNascimento);
		$this->form_data['observacoes'] = trim($this->observacoes);
		$this->form_data['avatar'] = trim($this->avatar);
		
		if($validaPessoa != 1){
			$this->erro .= $validaPessoa;
		}
	
		if(!empty($this->erro)){
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
			
			return;
		}
		
		if(empty($this->form_data)){
			return;
		}
		
		if(chk_array($this->parametros, 0) == 'editar'){
			$this->editarPessoa();
			
			return;
		}else{
			$this->adicionarPessoa();
			
			return;
		}
	}
	
	private function editarPessoa(){
		if(is_numeric(chk_array($this->parametros, 1))){
			$this->id = chk_array($this->parametros, 1);
		}

		$editaPessoa = $this->ClassePessoa->editarPessoa($this->id, chk_array($this->form_data, 'nome'), chk_array($this->form_data, 'sobrenome'), chk_array($this->form_data, 'apelido'), chk_array($this->form_data, 'genero'), chk_array($this->form_data, 'dataNascimento'), chk_array($this->form_data, 'observacoes'));
			
		if(!$editaPessoa){
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		}else{
			$this->form_msg = $this->controller->Messages->success('Registro editado com sucesso. Aguarde, você será redirecionado...');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/pessoas/index/editar/' . chk_array($this->parametros, 1) .'">';
		}
		
		return;
	}
	
	private function adicionarPessoa(){
		$inserePessoa = $this->ClassePessoa->adicionarPessoa(chk_array($this->form_data, 'nome'), chk_array($this->form_data, 'sobrenome'), chk_array($this->form_data, 'apelido'), chk_array($this->form_data, 'genero'), chk_array($this->form_data, 'dataNascimento'), chk_array($this->form_data, 'observacoes'));
		$this->id = $this->db->lastInsertId();
		
		$destinoAvatar = "midia/avatar/" .  date("Y") . "/" . date("m") . "/" . date("d") . "/" . $this->id;
		rcopy(chk_array($this->form_data, 'avatar'), $destinoAvatar);
		rrmdir(chk_array($this->form_data, 'avatar'));
		
		if(!$inserePessoa){
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		}else{
			$this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso. Aguarde, você será redirecionado...');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/pessoas/index/editar/'. $this->id . '">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/pessoas/index/editar/'. $this->id . '";</script>';
			
			$this->form_data = null;
		}
		
		return;
	}
	
	public function getPessoa($id = false){
		if(empty($id)){
			return;
		}
		
		$registro = $this->ClassePessoa->getPessoa($id);
		
		if(empty($registro)){
			$this->form_msg = $this->controller->Messages->error('Registro inexistente.');

			return;
		}
		
		foreach($registro as $key => $value){
			$this->form_data[$key] = $value;
		}
		
		return;
	}

	public function getPessoas($filtros = null){
		return $this->ClassePessoa->getPessoas($filtros);
	}
	
	public function getAvatar($id = null, $tn = false){
		if(is_numeric($id) && $id > 0){
			$registro = $this->ClassePessoa->getPessoa($id);
		}else{
			return;
		}
		
		$dataHoraCriacao = explode(" ", $registro["dataCriacao"]);
		$dataCriacao = explode("-", $dataHoraCriacao[0]);
		
		$diretorio = "midia/avatar/" . $dataCriacao[0] . "/" . $dataCriacao[1] . "/" . $dataCriacao[2] . "/" . $registro["id"];
		
		if(file_exists($diretorio)){
			$lerDiretorio = opendir($diretorio);
			$imagens = array();
			while($imagens[] = readdir($lerDiretorio));
			closedir($lerDiretorio);
			foreach($imagens as $imagem) {
				if(preg_match("/\.(jpg|jpeg|gif|png){1}$/i", strtolower($imagem))) {
					if($tn){
						return $diretorio . "/tn/". $imagem;
					}else{
						return $diretorio . "/". $imagem;
					}
				}
			}
		}
		
		return "views/standards/images/no-img.jpg";
	}
	
	public function bloquearPessoa(){
		$id = null;
		
		if(is_numeric(chk_array($this->parametros, 1))){
			$id = chk_array($this->parametros, 1);
		}

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblPessoa', 'id', $id, array('status' => 'F'));
			
			$this->form_msg = $this->controller->Messages->success('Registro bloqueado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/pessoas/">';
		
			return;
		}
	}
	
	public function desbloquearPessoa(){
		$id = null;
		
		if(is_numeric(chk_array($this->parametros, 1))){
			$id = chk_array($this->parametros, 1);
		}

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblPessoa', 'id', $id, array('status' => 'T'));
			
			$this->form_msg = $this->controller->Messages->success('Registro desbloqueado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/pessoas/">';
		
			return;
		}
	}
}
?>