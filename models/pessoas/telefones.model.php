<?php 
class TelefonesModel extends MainModel {
	public $form_data;
	public $form_msg;
	public $db;

	private $idPessoa;
	private $telefone;
	private $tipo;
	
	private $erro;

	private $ClasseTelefone;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
		
		$this->ClasseTelefone = new Telefone($this->db);
	}

	public function validarFormTelefone(){ 
		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}
		
		$this->form_data = array();
		
		$this->idPessoa = !empty(chk_array($this->parametros, 1)) ? chk_array($this->parametros, 1) : null;
		$this->telefone = isset($_POST["telefone"]) ? $_POST["telefone"] : null;
		$this->tipo = isset($_POST["tipo"]) ? $_POST["tipo"] : null;
		
		$validaTelefone = $this->ClasseTelefone->validarTelefone($this->idPessoa, 0, $this->telefone, $this->tipo);

		$this->form_data['idPessoa'] = trim($this->idPessoa);
		$this->form_data['telefone'] = trim($this->telefone);
		$this->form_data['tipo'] = trim($this->tipo);
		
		if($validaTelefone != 1){
			$this->erro .= $validaTelefone;
		}

		if(!empty($this->erro)){
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
			
			return;
		}
		
		if(empty($this->form_data)){
			return;
		}
		
		if(chk_array($this->parametros, 3) == 'editar'){
			$this->editarTelefone();
		}else{
			$this->adicionarTelefone();

		}
		
		return;
	}
	
	private function editarTelefone(){
		$id = null;
		
		if(is_numeric(chk_array($this->parametros, 4))){
			$id = chk_array($this->parametros, 4);
		}

		$editaTelefone = $this->ClasseTelefone->editarTelefone($id, chk_array($this->form_data, 'telefone'), chk_array($this->form_data, 'tipo'));
			
		if(!$editaTelefone){
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		}else{
			$this->form_msg = $this->controller->Messages->success('Registro editado com sucesso. Aguarde, você será redirecionado...');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/pessoas/index/editar/' . chk_array($this->parametros, 1) .'/telefones/">';
		}
		
		return;
	}
	
	private function adicionarTelefone(){
		$insereTelefone = $this->ClasseTelefone->adicionarTelefone(chk_array($this->form_data, 'idPessoa'), chk_array($this->form_data, 'telefone'), chk_array($this->form_data, 'tipo'));
		
		if(!$insereTelefone){
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		}else{
			$this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso.');
			
			$this->form_data = null;
		}
		
		return;
	}
	
	public function getTelefone($id = null){
		if(empty($id)){
			return;
		}
		
		$registro = $this->ClasseTelefone->getTelefone($id);
		
		if(empty($registro)){
			$this->form_msg = $this->controller->Messages->error('Registro inexistente.');

			return;
		}
		
		foreach($registro as $key => $value){
			$this->form_data[$key] = $value;
		}
		
		return;
	}

	public function getTelefones($idPessoa = null){
		return $this->ClasseTelefone->getTelefones($idPessoa);
	}
	
	public function removerTelefone($parametros = array()){
		$id = null;
		
		if(chk_array($parametros, 3) == 'remover'){
			$this->form_msg = $this->controller->Messages->questionYesNo('Tem certeza que deseja apagar este telefone?', 'Sim', 'Não', $_SERVER['REQUEST_URI'] . '/confirma', HOME_URI . '/pessoas/index/editar/'. chk_array($parametros, 1) . '/telefones/');
			
			if(is_numeric(chk_array($parametros, 4)) && chk_array($parametros, 5) == 'confirma'){
				$id = (int) chk_array($parametros, 4);
			}
		}
		
		if($id > 0){
			$this->ClasseTelefone->removerTelefone($id);

			$this->form_msg .= '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/pessoas/index/editar/'. chk_array($parametros, 1) . '/telefones/">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/pessoas/index/editar/'. chk_array($parametros, 1) . '/telefones/";</script>';
			return;
		}
	}
}
?>