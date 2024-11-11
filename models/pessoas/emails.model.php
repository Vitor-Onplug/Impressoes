<?php 
class EmailsModel extends MainModel {
	public $form_data;
	public $form_msg;
	public $db;

	private $idPessoa;
	private $email;
	
	private $erro;

	private $ClasseEmail;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
		
		$this->ClasseEmail = new Email($this->db);
	}

	public function validarFormEmail(){ 
		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}
		
		$this->form_data = array();
		
		$this->idPessoa = !empty(chk_array($this->parametros, 1)) ? (int) chk_array($this->parametros, 1) : null;
		$this->email = isset($_POST["email"]) ? $_POST["email"] : null;
		
		if(chk_array($this->parametros, 3) == 'editar'){
			$validaEmail = $this->ClasseEmail->validarEmail($this->idPessoa, 0, $this->email, true);
		}else{
			$validaEmail = $this->ClasseEmail->validarEmail($this->idPessoa, 0, $this->email);
		}
		
		$this->form_data['idPessoa'] = trim($this->idPessoa);
		$this->form_data['email'] = trim($this->email);
		
		if($validaEmail != 1){
			$this->erro .= $validaEmail;
		}

		if(!empty($this->erro)){
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
			
			return;
		}
		
		if(empty($this->form_data)){
			return;
		}
		
		if(chk_array($this->parametros, 3) == 'editar'){
			$this->editarEmail();
		}else{
			$this->adicionarEmail();

		}
		
		return;
	}
	
	private function editarEmail(){
		$id = null;
		
		if(is_numeric(chk_array($this->parametros, 4))){
			$id = chk_array($this->parametros, 4);
		}

		$editaEmail = $this->ClasseEmail->editarEmail($id, chk_array($this->form_data, 'email'));
			
		if(!$editaEmail){
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		}else{
			$this->form_msg = $this->controller->Messages->success('Registro editado com sucesso. Aguarde, você será redirecionado...');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/pessoas/index/editar/' . chk_array($this->parametros, 1) .'/emails/">';
		}
		
		return;
	}
	
	private function adicionarEmail(){
		$insereEmail = $this->ClasseEmail->adicionarEmail(chk_array($this->form_data, 'idPessoa'), chk_array($this->form_data, 'email'));
		
		if(!$insereEmail){
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		}else{
			$this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso.');
			
			$this->form_data = null;
		}
		
		return;
	}
	
	public function getEmail($id = null){
		if(empty($id)){
			return;
		}
		
		$registro = $this->ClasseEmail->getEmail($id);
		
		if(empty($registro)){
			$this->form_msg = $this->controller->Messages->error('Registro inexistente.');

			return;
		}
		
		foreach($registro as $key => $value){
			$this->form_data[$key] = $value;
		}
		
		return;
	}

	public function getEmails($idPessoa = null){
		return $this->ClasseEmail->getEmails($idPessoa);
	}
	
	public function removerEmail($parametros = array()){
		$id = null;
		
		if(chk_array($parametros, 3) == 'remover'){
			$this->form_msg = $this->controller->Messages->questionYesNo('Tem certeza que deseja apagar este e-mail?', 'Sim', 'Não', $_SERVER['REQUEST_URI'] . '/confirma', HOME_URI . '/pessoas/index/editar/'. chk_array($parametros, 1) . '/emails/');
			
			if(is_numeric(chk_array($parametros, 4)) && chk_array($parametros, 5) == 'confirma'){
				$id = (int) chk_array($parametros, 4);
			}
		}
		
		if($id > 0){
			$this->ClasseEmail->removerEmail($id);

			$this->form_msg .= '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/pessoas/index/editar/'. chk_array($parametros, 1) . '/emails/">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/pessoas/index/editar/'. chk_array($parametros, 1) . '/emails/";</script>';
			return;
		}
	}
}
?>