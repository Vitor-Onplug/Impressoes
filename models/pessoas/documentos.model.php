<?php 
class DocumentosModel extends MainModel {
	public $form_data;
	public $form_msg;
	public $db;

	private $idPessoa;
	private $tipo;
	private $titulo;
	private $documento;
	private $detalhes;
	private $erro;

	private $ClasseDocumento;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
		
		$this->ClasseDocumento = new Documento($this->db);
	}

	public function validarFormDocumento(){ 
		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}
		
		$this->form_data = array();
		
		$this->idPessoa = !empty(chk_array($this->parametros, 1)) ? (int) chk_array($this->parametros, 1) : null;
		$this->tipo = isset($_POST["tipo"]) ? $_POST["tipo"] : null;
		$this->titulo = isset($_POST["titulo"]) ? $_POST["titulo"] : null;
		$this->documento = isset($_POST["documento"]) ? $_POST["documento"] : null;
		$this->detalhes = isset($_POST["detalhes"]) ? $_POST["detalhes"] : null;

		if(chk_array($this->parametros, 3) == 'editar'){
			$validaDocumento = $this->ClasseDocumento->validarDocumento($this->idPessoa, 0, $this->tipo, $this->titulo, $this->documento, true, $this->detalhes);
		}else{
			$validaDocumento = $this->ClasseDocumento->validarDocumento($this->idPessoa, 0, $this->tipo, $this->titulo, $this->documento, false, $this->detalhes);
		}
		
		$this->form_data['idPessoa'] = trim($this->idPessoa);
		$this->form_data['tipo'] = trim($this->tipo);
		$this->form_data['titulo'] = trim($this->titulo);
		$this->form_data['documento'] = trim($this->documento);
		$this->form_data['detalhes'] = trim($this->detalhes);
		
		if($validaDocumento != 1){
			$this->erro .= $validaDocumento;
		}
	
		if(!empty($this->erro)){
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
			
			return;
		}
		
		if(empty($this->form_data)){
			return;
		}
		
		if(chk_array($this->parametros, 3) == 'editar'){
			$this->editarDocumento();
		}else{
			$this->adicionarDocumento();
		}
		
		return;
	}
	
	private function editarDocumento(){
		$id = null;
		
		if(is_numeric(chk_array($this->parametros, 4))){
			$id = chk_array($this->parametros, 4);
		}

		$editaDocumento = $this->ClasseDocumento->editarDocumento($id, chk_array($this->form_data, 'tipo'), chk_array($this->form_data, 'titulo'), chk_array($this->form_data, 'documento'), chk_array($this->form_data, 'detalhes'));
			
		if(!$editaDocumento){
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		}else{
			$this->form_msg = $this->controller->Messages->success('Registro editado com sucesso. Aguarde, você será redirecionado...');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/pessoas/index/editar/' . chk_array($this->parametros, 1) .'/documentos/">';
		}
		
		return;
	}
	
	private function adicionarDocumento(){
		$insereDocumento = $this->ClasseDocumento->adicionarDocumento(chk_array($this->form_data, 'idPessoa'), chk_array($this->form_data, 'tipo'), chk_array($this->form_data, 'titulo'), chk_array($this->form_data, 'documento'), chk_array($this->form_data, 'detalhes'));
		
		if(!$insereDocumento){
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		}else{
			$this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso.');
			
			$this->form_data = null;
		}
		
		return;
	}
	
	public function getDocumento($id = false){
		if(empty($id)){
			return;
		}
		
		$registro = $this->ClasseDocumento->getDocumento($id);
		
		if(empty($registro)){
			$this->form_msg = $this->controller->Messages->error('Registro inexistente.');

			return;
		}
		
		foreach($registro as $key => $value){
			$this->form_data[$key] = $value;
		}
		
		return;
	}

	public function getDocumentos($idPessoa = null){
		return $this->ClasseDocumento->getDocumentos($idPessoa);
	}
	
	public function removerDocumento($parametros = array()){
		$id = null;
		
		if(chk_array($parametros, 3) == 'remover'){
			$this->form_msg = $this->controller->Messages->questionYesNo('Tem certeza que deseja apagar este endereço?', 'Sim', 'Não', $_SERVER['REQUEST_URI'] . '/confirma',  HOME_URI . '/pessoas/index/editar/'. chk_array($parametros, 1) . '/documentos/');
			
			if(is_numeric(chk_array($parametros, 4)) && chk_array($parametros, 5) == 'confirma'){
				$id = (int) chk_array($parametros, 4);
			}
		}
		
		if($id > 0){
			$this->ClasseDocumento->removerDocumento($id);

			$this->form_msg .= '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/pessoas/index/editar/'. chk_array($parametros, 1) . '/documentos/">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/pessoas/index/editar/'. chk_array($parametros, 1) . '/documentos/";</script>';
			return;
		}
	}
}
?>