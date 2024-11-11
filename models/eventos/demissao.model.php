<?php 
class DemissaoModel extends MainModel {
	public $form_data;
	public $form_msg;
	public $db;
	
	private $dataDispensado;
	private $observacaoDispensado;

	private $erro;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
	}

	public function validarFormDemissao(){
		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}
		
		$id = null;
		
		if(!is_numeric(chk_array($this->parametros, 1))){
			return;
		}
		
		$this->form_data = array();
		
		$this->dataDispensado = isset($_POST["dataDispensado"]) ? $_POST["dataDispensado"] : null;
		$this->observacaoDispensado = isset($_POST["observacaoDispensado"]) ? $_POST["observacaoDispensado"] : null;
		
		if(empty($this->dataDispensado)){ $this->erro .= "<br>Selecione a data da demissão."; }
		if(!empty($this->dataDispensado)){
			if(!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $this->dataDispensado)){ $this->erro .= "<br>Data da demissão inválida."; }
			if(chk_array($this->parametros, 1) > 0){
				$query = $this->db->query('SELECT * FROM `vwEventosFasesContratados` WHERE `id` = ?', array(chk_array($this->parametros, 1)));
				$registro = $query->fetch();	
				
				if(diff_datas(implode("/", array_reverse(explode("-", $registro['dataInicio']))), $this->dataDispensado) < 0){
					$this->erro .= "<br>A data da demissão deve estar entre " . implode("/", array_reverse(explode("-", $registro["dataInicio"]))) . " e " . implode("/", array_reverse(explode("-", $registro["dataFim"]))) . ".";
				}
				
				if(diff_datas(implode("/", array_reverse(explode("-", $registro['dataFim']))), $this->dataDispensado) > 0){
					$this->erro .= "<br>A data da demissão deve estar entre " . implode("/", array_reverse(explode("-", $registro["dataInicio"]))) . " e " . implode("/", array_reverse(explode("-", $registro["dataFim"]))) . ".";
				}
			}
		}
	
		$this->form_data['dataDispensado'] = $this->dataDispensado;
		$this->form_data['observacaoDispensado'] = $this->observacaoDispensado;
		
		if(!empty($this->erro)){
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
			
			return;
		}
		
		if(empty($this->form_data)){
			return;
		}
		
		$this->form_data['dataDispensado'] = implode("-", array_reverse(explode("/", $this->dataDispensado)));
		$this->editarDemissao();
	}
	
	private function editarDemissao(){
		$id = null;
		
		if(is_numeric(chk_array($this->parametros, 1))){
			$id = chk_array($this->parametros, 1);
		}

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblEventoFaseContratado', 'id', $id, array('dispensado' => 'T', 'dataDispensado' => chk_array($this->form_data, 'dataDispensado'), 'observacaoDispensado' => chk_array($this->form_data, 'observacaoDispensado')));
			
			if(!$query){
				$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
				return;
			}else{
				$this->form_msg = $this->controller->Messages->success('Registro editado com sucesso.');

				$this->form_data = null;

				return;
			}
		}
	}
	
	private function removerDemissao(){
		$id = null;
		
		if(!is_numeric(chk_array($parametros, 1)) ){
			return;
		}else{
			$id = (int) chk_array($parametros, 1);
		}

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblEventoFaseContratado', 'id', $id, array('dispensado' => 'F', 'dataDispensado' => null, 'observacaoDispensado' => null));
			
			if(!$query){
				$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
				return;
			}else{
				$this->form_msg = $this->controller->Messages->success('Registro editado com sucesso.');

				return;
			}
		}
	}
}