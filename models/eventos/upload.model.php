<?php 
class UploadModel extends MainModel {
	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
	}
	
	public function avatar(){
		return $this->ClasseUpload = new UploadImage();
	}
	
	public function comprovante($diretorio = null, $opcoes = null){
		$this->ClasseUploadFile = new UploadFile($diretorio, $opcoes);
		
		return $this->ClasseUploadFile->load();
	}
	
}