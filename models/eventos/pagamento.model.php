<?php 
class PagamentoModel extends MainModel {
	public $form_data;
	public $form_msg;
	public $db;
	
	private $dataPagamento;
	private $observacaoPagamento;

	private $erro;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
		
		$this->ClasseMensagem = new Mensagem($this->db);
	}

	public function validarFormPagamento(){
		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}
		
		$id = null;
		
		if(!is_numeric(chk_array($this->parametros, 1))){
			return;
		}
		
		$this->form_data = array();
		
		$this->dataPagamento = isset($_POST["dataPagamento"]) ? $_POST["dataPagamento"] : null;
		$this->observacaoPagamento = isset($_POST["observacaoPagamento"]) ? $_POST["observacaoPagamento"] : null;
		
		if(empty($this->dataPagamento)){ $this->erro .= "<br>Selecione a data do pagamento."; }
		if(!empty($this->dataPagamento)){
			if(!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $this->dataPagamento)){ $this->erro .= "<br>Data do pagamento inválida."; }
		}
	
		$this->form_data['dataPagamento'] = $this->dataPagamento;
		$this->form_data['observacaoPagamento'] = $this->observacaoPagamento;
		
		if(!empty($this->erro)){
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
			
			return;
		}
		
		if(empty($this->form_data)){
			return;
		}
		
		$this->form_data['dataPagamento'] = implode("-", array_reverse(explode("/", $this->dataPagamento)));
		$this->editarPagamento();
	}
	
	private function editarPagamento(){
		$id = null;
		
		if(is_numeric(chk_array($this->parametros, 1))){
			$id = chk_array($this->parametros, 1);
		}

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblEventoFaseContratado', 'id', $id, array('pago' => 'T', 'dataPagamento' => chk_array($this->form_data, 'dataPagamento'), 'observacaoPagamento' => chk_array($this->form_data, 'observacaoPagamento')));
			
			if(!$query){
				$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
				return;
			}else{
				$query = $this->db->query('SELECT * FROM `vwEventosFasesContratados` WHERE `id` = ?', array($id));
		
				$registro = $query->fetch();
				
				$this->ClasseMensagem->enviarMensagem(chk_array($this->userdata, 'id'), $registro['idPessoa'], 'Seu pagamento do serviço realizado no evento <a href="' . HOME_URI . '/eventos/index/perfil/' . $registro["idEvento"]. '">' . $registro["evento"]. '</a> foi liberado.');
				
				$this->form_msg = $this->controller->Messages->success('Registro editado com sucesso.');

				$this->form_data = null;

				return;
			}
		}
	}
	
	private function removerPagamento(){
		$id = null;
		
		if(!is_numeric(chk_array($parametros, 1)) ){
			return;
		}else{
			$id = (int) chk_array($parametros, 1);
		}

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblEventoFaseContratado', 'id', $id, array('pago' => 'F', 'dataPagamento' => null, 'observacaoPagamento' => null));
			
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