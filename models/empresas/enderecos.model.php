<?php 
class EnderecosModel extends MainModel {
	public $form_data;
	public $form_msg;
	public $db;

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

	private $ClasseEndereco;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
		
		$this->ClasseEndereco = new Endereco($this->db);
	}

	public function validarFormEndereco(){ 
		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}
		
		$this->form_data = array();
		
		$this->idEmpresa = !empty(chk_array($this->parametros, 1)) ? (int) chk_array($this->parametros, 1) : null;
		$this->titulo = isset($_POST["titulo"]) ? $_POST["titulo"] : null;
		$this->cep = isset($_POST["cep"]) ? $_POST["cep"] : null;
		$this->logradouro = isset($_POST["logradouro"]) ? $_POST["logradouro"] : null;
		$this->numero = isset($_POST["numero"]) ? $_POST["numero"] : null;
		$this->complemento = isset($_POST["complemento"]) ? $_POST["complemento"] : null;
		$this->zona = isset($_POST["zona"]) ? $_POST["zona"] : null;
		$this->estado = isset($_POST["estado"]) ? $_POST["estado"] : null;
		$this->bairro = isset($_POST["bairro"]) ? $_POST["bairro"] : null;
		$this->cidade = isset($_POST["cidade"]) ? $_POST["cidade"] : null;
		$this->estado = isset($_POST["estado"]) ? $_POST["estado"] : null;
		$this->latitude = isset($_POST["latitude"]) ? $_POST["latitude"] : null;
		$this->longitude = isset($_POST["longitude"]) ? $_POST["longitude"] : null;
		
		$validaEndereco = $this->ClasseEndereco->validarEndereco(0, $this->idEmpresa, $this->titulo, $this->cep, $this->logradouro, $this->numero, $this->complemento, $this->zona, $this->bairro, $this->cidade, $this->estado, $this->latitude, $this->longitude);
		
		$this->form_data['idEmpresa'] = trim($this->idEmpresa);
		$this->form_data['titulo'] = trim($this->titulo);
		$this->form_data['cep'] = trim($this->cep);
		$this->form_data['logradouro'] = trim($this->logradouro);
		$this->form_data['numero'] = trim($this->numero);
		$this->form_data['complemento'] = trim($this->complemento);
		$this->form_data['zona'] = trim($this->zona);
		$this->form_data['bairro'] = trim($this->bairro);
		$this->form_data['cidade'] = trim($this->cidade);
		$this->form_data['estado'] = trim($this->estado);
		$this->form_data['latitude'] = trim($this->latitude);
		$this->form_data['longitude'] = trim($this->longitude);
		
		if($validaEndereco != 1){
			$this->erro .= $validaEndereco;
		}
	
		if(!empty($this->erro)){
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
			
			return;
		}
		
		if(empty($this->form_data)){
			return;
		}
		
		if(chk_array($this->parametros, 3) == 'editar'){
			$this->editarEndereco();
		}else{
			$this->adicionarEnderecoEmpresa();
		}
		
		return;
	}
	
	private function editarEndereco(){
		$id = null;
		
		if(is_numeric(chk_array($this->parametros, 4))){
			$id = chk_array($this->parametros, 4);
		}

		$editaEndereco = $this->ClasseEndereco->editarEndereco($id, chk_array($this->form_data, 'titulo'), chk_array($this->form_data, 'cep'), chk_array($this->form_data, 'logradouro'), chk_array($this->form_data, 'numero'), chk_array($this->form_data, 'complemento'), chk_array($this->form_data, 'zona'), chk_array($this->form_data, 'bairro'), chk_array($this->form_data, 'cidade'), chk_array($this->form_data, 'estado'), chk_array($this->form_data, 'latitude'), chk_array($this->form_data, 'longitude'));
			
		if(!$editaEndereco){
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		}else{
			$this->form_msg = $this->controller->Messages->success('Registro editado com sucesso. Aguarde, você será redirecionado...');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/empresas/index/editar/' . chk_array($this->parametros, 1) .'/enderecos/">';
		}
		
		return;
	}
	
	private function adicionarEnderecoEmpresa(){
		$insereEndereco = $this->ClasseEndereco->adicionarEnderecoEmpresa(chk_array($this->form_data, 'idEmpresa'), chk_array($this->form_data, 'titulo'), chk_array($this->form_data, 'cep'), chk_array($this->form_data, 'logradouro'), chk_array($this->form_data, 'numero'), chk_array($this->form_data, 'complemento'), chk_array($this->form_data, 'zona'), chk_array($this->form_data, 'bairro'), chk_array($this->form_data, 'cidade'), chk_array($this->form_data, 'estado'), chk_array($this->form_data, 'latitude'), chk_array($this->form_data, 'longitude'));
		
		if(!$insereEndereco){
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		}else{
			$this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso.');
			
			$this->form_data = null;
		}
		
		return;
	}
	
	public function getEndereco($id = false){
		if(empty($id)){
			return;
		}
		
		$registro = $this->ClasseEndereco->getEndereco($id);
		
		if(empty($registro)){
			$this->form_msg = $this->controller->Messages->error('Registro inexistente.');

			return;
		}
		
		foreach($registro as $key => $value){
			$this->form_data[$key] = $value;
		}
		
		return;
	}

	public function getEnderecosEmpresa($idEmpresa = null){
		return $this->ClasseEndereco->getEnderecosEmpresa($idEmpresa);
	}
	
	public function removerEndereco($parametros = array()){
		$id = null;
		
		if(chk_array($parametros, 3) == 'remover'){
			$this->form_msg = $this->controller->Messages->questionYesNo('Tem certeza que deseja apagar este endereço?', 'Sim', 'Não', $_SERVER['REQUEST_URI'] . '/confirma',  HOME_URI . '/empresas/index/editar/'. chk_array($parametros, 1) . '/enderecos/');
			
			if(is_numeric(chk_array($parametros, 4)) && chk_array($parametros, 5) == 'confirma'){
				$id = (int) chk_array($parametros, 4);
			}
		}
		
		if($id > 0){
			$this->ClasseEndereco->removerEndereco($id);

			$this->form_msg .= '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/empresas/index/editar/'. chk_array($parametros, 1) . '/enderecos/">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/empresas/index/editar/'. chk_array($parametros, 1) . '/enderecos/";</script>';
			return;
		}
	}
}
?>