<?php 
class CargosModel extends MainModel {
	public $form_data;
	public $form_msg;
	public $db;
	
	private $idEventoFase;
	private $idFuncao;
	private $idEventoLideranca;
	private $quantidade;
	private $valor;

	private $erro;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
	}

	public function validarFormCargos(){
		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}
		
		$this->form_data = array();
		
		$this->idEventoFase = isset($_POST["idEventoFase"]) ? $_POST["idEventoFase"] : null;
		$this->idFuncao = isset($_POST["idFuncao"]) ? $_POST["idFuncao"] : null;
		$this->idEventoLideranca = isset($_POST["idEventoFase"]) ? $_POST["idEventoLideranca"] : null;
		$this->quantidade = isset($_POST["quantidade"]) ? $_POST["quantidade"] : null;
		
		if(empty($this->idFuncao)){ $this->erro .= "<br>Selecione a função."; }
		if(empty($this->idEventoLideranca)){ $this->erro .= "<br>Selecione a lideranca."; }
		if($this->quantidade < 1){ $this->erro .= "<br>Preencha a quantidade."; }
				
		$query = $this->db->query('SELECT * FROM `tblEventoFaseFuncao` WHERE `idEventoFase` = ? AND `idFuncao` = ? AND `idEventoLideranca` = ?', array($this->idEventoFase, $this->idFuncao, $this->idEventoLideranca));
		
		$registro = $query->fetch();
		if(chk_array($this->parametros, 5) == 'editar'){
			if($registro && $registro['id'] != chk_array($this->parametros, 6)){
				$this->erro .= "<br>Função já cadastrada.";
			}
		}else{
			if(!empty($registro)){
				$this->erro .= "<br>Função já cadastrada.";
			}
		}
	
		$this->form_data['idEventoFase'] = $this->idEventoFase;
		$this->form_data['idFuncao'] = $this->idFuncao;
		$this->form_data['idEventoLideranca'] = $this->idEventoLideranca;
		$this->form_data['quantidade'] = $this->quantidade;
		
		$this->form_data['formulario'] = $this->idEventoFase;
		
		if(!empty($this->erro)){
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
			
			return;
		}
		
		if(empty($this->form_data)){
			return;
		}

		$query = $this->db->query('SELECT * FROM `tblFuncao` WHERE `id` = ?', array($this->idFuncao));
		
		$registro = $query->fetch();
	
		$this->form_data['valor'] = $registro['valor'];
		
		if(chk_array($this->parametros, 5) == 'editar'){
			$this->editarCargo();
			
			return;
		}else{
			$this->adicionarCargo();
			
			return;
		}
	}
	
	private function editarCargo(){
		$id = null;
		
		if(is_numeric(chk_array($this->parametros, 6))){
			$id = chk_array($this->parametros, 6);
		}

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblEventoFaseFuncao', 'id', $id, array('idEventoFase' => chk_array($this->form_data, 'idEventoFase'), 'idFuncao' => chk_array($this->form_data, 'idFuncao'), 'idEventoLideranca' => chk_array($this->form_data, 'idEventoLideranca'), 'quantidade' => chk_array($this->form_data, 'quantidade'), 'valor' => chk_array($this->form_data, 'valor')));
			
			if(!$query){
				$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
				return;
			}else{
				$this->form_msg = $this->controller->Messages->success('Registro editado com sucesso.');

				return;
			}
		}
	}
	
	private function adicionarCargo(){
		$query = $this->db->insert('tblEventoFaseFuncao', array('idEventoFase' => chk_array($this->form_data, 'idEventoFase'), 'idFuncao' => chk_array($this->form_data, 'idFuncao'), 'idEventoLideranca' => chk_array($this->form_data, 'idEventoLideranca'), 'quantidade' => chk_array($this->form_data, 'quantidade'), 'valor' => chk_array($this->form_data, 'valor')));

		if(!$query){
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
			return;
		}else{
			$this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso.');
			
			$formulario = $this->form_data['idEventoFase'];
			
			$this->form_data = null;
			
			$this->form_data['formulario'] = $formulario;

			return;
		}
	}
	
	public function getCargo($id = false){
		$s_id = false;
		
		if(!empty($id)){
			$s_id = (int) $id;
		}
		
		if(empty($s_id)){
			return;
		}
		
		$query = $this->db->query('SELECT * FROM `vwEventosFasesFuncoes` WHERE `id` = ?', array($s_id));
		
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

	public function removerCargo($parametros = array()){
		$id = null;
		
		if(chk_array($parametros, 5) == 'remover'){
			$this->form_data['formulario'] = chk_array($parametros, 4);
			
			$this->form_msg = $this->controller->Messages->questionYesNo('Tem certeza que deseja apagar esta função?', 'Sim', 'Não', $_SERVER['REQUEST_URI'] . '/confirma', HOME_URI . '/eventos/index/editar/' . chk_array($parametros, 1) . '/fases');
			
			if(is_numeric(chk_array($parametros, 6)) && chk_array($parametros, 7) == 'confirma'){
				$id = chk_array($parametros, 6);
			}
		}
		
		if(!empty($id)){
			$id = (int) $id;
			
			$query = $this->db->delete('tblEventoFaseFuncao', 'id', $id);

			$this->form_msg .= '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/eventos/index/editar/' . chk_array($parametros, 1) . '/fases/">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/eventos/index/editar/' . chk_array($parametros, 1) . '/fases/";</script>';
			return;
		}
	}

	public function getCargos($idEventoFase){
		if($idEventoFase > 0){
			$query = $this->db->query('SELECT * FROM `vwEventosFasesFuncoes` WHERE `idEventoFase` = ? ORDER BY `dataCriacao` ASC', array($idEventoFase));
		}else{
			return;
		}
		
		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}
}