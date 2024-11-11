<?php 
class FaltasModel extends MainModel {
	public $form_data;
	public $form_msg;
	public $db;
	
	private $idEventoFaseContratado;
	private $falta;

	private $erro;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
	}

	public function validarFormFaltas(){
		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}
		
		$this->form_data = array();
		
		$this->idEventoFaseContratado = isset($_POST["idEventoFaseContratado"]) ? $_POST["idEventoFaseContratado"] : null;
		$this->falta = isset($_POST["falta"]) ? $_POST["falta"] : null;
		
		if(empty($this->idEventoFaseContratado)){ $this->erro .= "<br>Selecione a pessoa."; }
		if(empty($this->falta)){ $this->erro .= "<br>Selecione a data da falta."; }
		if(!empty($this->falta)){
			if(!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $this->falta)){ $this->erro .= "<br>Data da falta inválida."; }
			if(!empty($this->idEventoFaseContratado)){
				$query = $this->db->query('SELECT * FROM `vwEventosFasesContratados` WHERE `id` = ?', array($this->idEventoFaseContratado));
				$registro = $query->fetch();	
				
				if(diff_datas(implode("/", array_reverse(explode("-", $registro['dataInicio']))), $this->falta) < 0){
					$this->erro .= "<br>A data da falta deve estar entre " . implode("/", array_reverse(explode("-", $registro["dataInicio"]))) . " e " . implode("/", array_reverse(explode("-", $registro["dataFim"]))) . ".";
				}
				
				if(diff_datas(implode("/", array_reverse(explode("-", $registro['dataFim']))), $this->falta) > 0){
					$this->erro .= "<br>A data da falta deve estar entre " . implode("/", array_reverse(explode("-", $registro["dataInicio"]))) . " e " . implode("/", array_reverse(explode("-", $registro["dataFim"]))) . ".";
				}
			}
		}
		
		if(!empty($this->idEventoFaseContratado)){		
			$query = $this->db->query('SELECT * FROM `tblEventoFaseContratadoFalta` WHERE `idEventoFaseContratado` = ? AND `falta` = ?', array($this->idEventoFaseContratado, implode("-", array_reverse(explode("/", $this->falta)))));
			
			$registro = $query->fetch();
			if(!empty($registro)){
				$this->erro .= "<br>Falta já cadastrada.";
			}
		}
	
		$this->form_data['idEventoFaseContratado'] = $this->idEventoFaseContratado;
		$this->form_data['falta'] = $this->falta;
		
		if(!empty($this->erro)){
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
			
			return;
		}
		
		if(empty($this->form_data)){
			return;
		}
		
		$this->form_data['falta'] = implode("-", array_reverse(explode("/", $this->falta)));
		$this->adicionarFalta();
	}
	
	private function adicionarFalta(){
		$query = $this->db->insert('tblEventoFaseContratadoFalta', array('idEventoFaseContratado' => chk_array($this->form_data, 'idEventoFaseContratado'), 'falta' => chk_array($this->form_data, 'falta')));

		if(!$query){
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
			return;
		}else{
			$this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso.');
			
			$this->form_data = null;

			return;
		}
	}
	
	public function getFalta($id = false){
		$s_id = false;
		
		if(!empty($id)){
			$s_id = (int) $id;
		}
		
		if(empty($s_id)){
			return;
		}
		
		$query = $this->db->query('SELECT * FROM `vwEventosFasesContradosFaltas` WHERE `id` = ?', array($s_id));
		
		if(!$query){
			$this->form_msg = $this->controller->Messages->error('Registro não encontrado.');

			return;
		}
		
		$registro = $query->fetch();
		
		if(empty($registro)){
			$this->form_msg = $this->controller->Messages->error('Registro inexistente.');

			return;
		}
		
		foreach($registro as $key => $value){
			$this->form_data[$key] = $value;
		}
		
		return;
	}

	public function removerFalta($parametros = array()){
		$id = null;
		
		if(chk_array($parametros, 2) == 'remover'){
			$this->form_msg = $this->controller->Messages->questionYesNo('Tem certeza que deseja apagar esta falta?', 'Sim', 'Não', $_SERVER['REQUEST_URI'] . '/confirma', HOME_URI . '/eventos/index/faltas/' . chk_array($parametros, 1));
			
			if(is_numeric(chk_array($parametros, 3)) && chk_array($parametros, 4) == 'confirma'){
				$id = chk_array($parametros, 3);
			}
		}
		
		if(!empty($id)){
			$id = (int) $id;
			
			$query = $this->db->delete('tblEventoFaseContratadoFalta', 'id', $id);

			$this->form_msg .= '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/eventos/index/faltas/' . chk_array($parametros, 1) . '">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/eventos/index/faltas/' . chk_array($parametros, 1) . '";</script>';
			return;
		}
	}
	
	public function getFaltas($idEvento = null, $idEventoFaseContratado = null){
		if($idEvento > 0){
			$query = $this->db->query('SELECT * FROM `vwEventosFasesContradosFaltas` WHERE `idEvento` = ? ORDER BY `falta` ASC', array($idEvento));
		}elseif($idEventoFaseContratado > 0){
			$query = $this->db->query('SELECT * FROM `vwEventosFasesContradosFaltas` WHERE `idEventoFaseContratado` = ? ORDER BY `falta` ASC', array($idEventoFaseContratado));
		}else{
			$query = $this->db->query('SELECT * FROM `vwEventosFasesContradosFaltas` ORDER BY `falta` ASC');
		}
		
		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}
}