<?php 
class UploadModel extends MainModel {
	public $form_data;
	public $form_msg;
	public $db;

	private $erro;

	private $ClasseUploadFile;

	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
	}
	
	public function avatar($diretorio = null, $opcoes = null){
		$this->ClasseUploadFile = new UploadFile($diretorio, $opcoes);
		
		return $this->ClasseUploadFile->load();
	}

	public function diversos($diretorio = null, $opcoes = null){
		$this->ClasseUploadFile = new UploadFile($diretorio, $opcoes);
		
		return $this->ClasseUploadFile->load();
	}
}