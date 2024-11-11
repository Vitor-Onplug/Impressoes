<?php 
class PassosModel extends MainModel {
	public $form_data;
	public $form_msg;
	public $db;
	
	private $idFase;
	private $dataInicioFim;
	private $dataInicio;
	private $dataFim;

	private $erro;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
	}

	public function validarFormFases(){
		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}
		
		$this->form_data = array();
		
		$this->idFase = isset($_POST["idFase"]) ? $_POST["idFase"] : null;
		$this->dataInicioFim = isset($_POST["dataInicioFim"]) ? $_POST["dataInicioFim"] : null;
		
		if(empty($this->idFase)){ $this->erro .= "<br>Selecione a fase."; }
		
		if(empty($this->dataInicioFim)){ $this->erro .= "<br>Preencha a data de início e fim da fase."; }
		if(!empty($this->dataInicioFim)){
			if(!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}([^\b])[\-]([^\b])\d{1,2}\/\d{1,2}\/\d{4}$/', $this->dataInicioFim)){ 
				$erro .= "<br>Intervalo de datas de início e fim inválido."; 
			}else{
				$datas = explode(" - ", $this->dataInicioFim);
				$this->dataInicio = $datas[0];
				$this->dataFim = $datas[1];
				
				if(!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $this->dataInicio)){ $this->erro .= "<br>Data de início da fase inválida."; }
				if(!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $this->dataFim)){ $this->erro .= "<br>Data de término da fase inválida."; }
				
				if(diff_datas($this->dataInicio, $this->dataFim) < 0){
					$this->erro .= "<br>Data de término deve ser maior que a data de início.";
				}
				
				$query = $this->db->query('SELECT * FROM `vwEventos` WHERE `id` = ?', array(chk_array($this->parametros, 1)));
				$registro = $query->fetch();	
				
				if(diff_datas(implode("/", array_reverse(explode("-", $registro['dataInicio']))), $this->dataInicio) < 0){
					$this->erro .= "<br>A data de início deve ser maior ou igual a " . implode("/", array_reverse(explode("-", $registro["dataInicio"]))) . ".";
				}
				
				if(diff_datas(implode("/", array_reverse(explode("-", $registro['dataFim']))), $this->dataFim) > 0){
					$this->erro .= "<br>A data de término deve ser menor ou igual a " . implode("/", array_reverse(explode("-", $registro["dataFim"]))) . ".";
				}
			}
		}
		
		$query = $this->db->query('SELECT * FROM `vwEventosFases` WHERE `idEvento` = ? AND `idFase` = ?', array(chk_array($this->parametros, 1), $this->idFase));
		$registro = $query->fetch();		
		if(chk_array($this->parametros, 3) == 'editar'){
			if($registro && $registro['id'] != chk_array($this->parametros, 4)){
				$this->erro .= "<br>Fase já cadastrada.";
			}
		}else{
			if(!empty($registro)){
				$this->erro .= "<br>Fase já cadastrada.";
			}
		}

		$this->form_data['idFase'] = $this->idFase;
		$this->form_data['dataInicioFim'] = $this->dataInicioFim;
		$this->form_data['dataInicio'] = implode("-", array_reverse(explode("/", $this->dataInicio)));
		$this->form_data['dataFim'] = implode("-", array_reverse(explode("/", $this->dataFim)));
	
		if(!empty($this->erro)){
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
			
			return;
		}
		
		if(empty($this->form_data)){
			return;
		}
		
		if(chk_array($this->parametros, 3) == 'editar'){
			$this->editarFase();
			
			return;
		}else{
			$this->adicionarFase();
			
			return;
		}
	}
	
	private function editarFase(){
		$id = null;
		
		if(is_numeric(chk_array($this->parametros, 4))){
			$id = chk_array($this->parametros, 4);
		}

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblEventoFase', 'id', $id, array('idEvento' => chk_array($this->parametros, 1), 'idFase' => chk_array($this->form_data, 'idFase'), 'dataInicio' => chk_array($this->form_data, 'dataInicio'), 'dataFim' => chk_array($this->form_data, 'dataFim')));
			
			if(!$query){
				$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
				return;
			}else{
				$this->form_msg = $this->controller->Messages->success('Registro editado com sucesso.');

				return;
			}
		}
	}
	
	private function adicionarFase(){
		$query = $this->db->insert('tblEventoFase', array('idEvento' => chk_array($this->parametros, 1), 'idFase' => chk_array($this->form_data, 'idFase'), 'dataInicio' => chk_array($this->form_data, 'dataInicio'), 'dataFim' => chk_array($this->form_data, 'dataFim')));

		if(!$query){
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
			return;
		}else{
			$this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso.');
			
			$this->form_data = null;

			return;
		}
	}
	
	public function getFase($id = false){
		$s_id = false;
		
		if(!empty($id)){
			$s_id = (int) $id;
		}
		
		if(empty($s_id)){
			return;
		}
		
		$query = $this->db->query('SELECT * FROM `vwEventosFases` WHERE `id` = ?', array($s_id));
		
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
		
		$this->form_data['dataInicioFim'] = implode("/", array_reverse(explode("-", $this->form_data['dataInicio']))) . " - " . implode("/", array_reverse(explode("-", $this->form_data['dataFim'])));
		
		return;
	}

	public function removerFase($parametros = array()){
		$id = null;
		
		if(chk_array($parametros, 3) == 'remover'){
			$this->form_msg = $this->controller->Messages->questionYesNo('Tem certeza que deseja apagar esta fase?', 'Sim', 'Não', $_SERVER['REQUEST_URI'] . '/confirma', HOME_URI . '/eventos/index/editar/' . chk_array($parametros, 1) . '/fases');
			
			if(is_numeric(chk_array($parametros, 4)) && chk_array($parametros, 5) == 'confirma'){
				$id = chk_array($parametros, 4);
			}
		}
		
		if(!empty($id)){
			$id = (int) $id;
			
			$query = $this->db->delete('tblEventoFase', 'id', $id);

			$this->form_msg .= '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/eventos/index/editar/' . chk_array($parametros, 1) . '/fases/">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/eventos/index/editar/' . chk_array($parametros, 1) . '/fases/";</script>';
			return;
		}
	}

	public function getFases($idEvento = null){
		if($idEvento > 0){
			$query = $this->db->query('SELECT * FROM `vwEventosFases` WHERE `idEvento` = ? ORDER BY `dataCriacao` ASC', array($idEvento));
		}else{
			$query = $this->db->query('SELECT * FROM `vwEventosFases` ORDER BY `dataCriacao` ASC');
		}
		
		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}
}