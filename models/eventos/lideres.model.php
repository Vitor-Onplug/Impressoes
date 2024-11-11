<?php 
class LideresModel extends MainModel {
	public $form_data;
	public $form_msg;
	public $db;
	
	private $idLideranca;
	private $idLider;
	private $verba;

	private $erro;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
		
		$this->ClasseNotificacao = new Notificacao($this->db);
		$this->ClasseEmail = new Email($this->db);
		$this->ClasseMailer = new Mailer(SYS_NAME, MAILER_Host, MAILER_Username, MAILER_Password);
	}

	public function validarFormLideres(){
		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}
		
		$this->form_data = array();
		
		$this->idLideranca = isset($_POST["idLideranca"]) ? $_POST["idLideranca"] : null;
		$this->idLider = isset($_POST["idLider"]) ? $_POST["idLider"] : null;
		$this->verba = isset($_POST["verba"]) ? $_POST["verba"] : null;
		
		if(empty($this->idLideranca)){ $this->erro .= "<br>Selecione a liderança."; }
		if(empty($this->idLider)){ $this->erro .= "<br>Selecione o líder."; }
		if(empty($this->verba)){ $this->erro .= "<br >Preencha o valor do dinheiro adiantado."; }
		if(!empty($this->verba)){
			if(!preg_match('/^(\d{1,3}){1}(\.\d{3})*(\,\d{2})?$/', $this->verba)){ $this->erro .= "<br >Dinheiro Adiantado: formato inválido."; }
		}
		
		$query = $this->db->query('SELECT * FROM `tblEventoLideranca` WHERE `idEvento` = ? AND `idLider` = ? AND `idLideranca` = ?', array(chk_array($this->parametros, 1), $this->idLider, $this->idLideranca));
		
		$registro = $query->fetch();
		
		if(chk_array($this->parametros, 3) == 'editar'){
			if($registro && $registro['id'] != chk_array($this->parametros, 4)){
				$this->erro .= "<br>Liderança já cadastrada.";
			}
		}else{
			if(!empty($registro)){
				$this->erro .= "<br>Liderança já cadastrada.";
			}
		}

		$this->form_data['idLideranca'] = $this->idLideranca;
		$this->form_data['idLider'] = $this->idLider;
		$this->form_data['verba'] = str_replace(',', '.', str_replace('.', '', $this->verba));
	
		if(!empty($this->erro)){
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
			
			return;
		}
		
		if(empty($this->form_data)){
			return;
		}
		
		if(chk_array($this->parametros, 3) == 'editar'){
			$this->editarLider();
			
			return;
		}else{
			$this->adicionarLider();
			
			return;
		}
	}
	
	private function editarLider(){
		$id = null;
		
		if(is_numeric(chk_array($this->parametros, 4))){
			$id = chk_array($this->parametros, 4);
		}

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblEventoLideranca', 'id', $id, array('idEvento' => chk_array($this->parametros, 1), 'idLider' => chk_array($this->form_data, 'idLider'), 'idLideranca' => chk_array($this->form_data, 'idLideranca'), 'verba' => chk_array($this->form_data, 'verba')));
			
			if(!$query){
				$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
				return;
			}else{
				$this->form_msg = $this->controller->Messages->success('Registro editado com sucesso.');

				return;
			}
		}
	}
	
	private function adicionarLider(){
		$query = $this->db->insert('tblEventoLideranca', array('idEvento' => chk_array($this->parametros, 1), 'idLider' => chk_array($this->form_data, 'idLider'), 'idLideranca' => chk_array($this->form_data, 'idLideranca'), 'verba' => chk_array($this->form_data, 'verba')));

		if(!$query){
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
			return;
		}else{
			$this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso.');
			
			$this->form_data = null;

			return;
		}
	}
	
	public function getLider($id = null, $idEvento = null, $idLider = null){
		if($id > 0){
			$query = $this->db->query('SELECT * FROM `vwEventosLideres` WHERE `id` = ?', array($id));
		}elseif($idEvento > 0 && $idLider > 0){
			$query = $this->db->query('SELECT * FROM `vwEventosLideres` WHERE `idEvento` = ? AND `idLider` = ?', array($idEvento, $idLider));
		}else{
			return;
		}
		
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

	public function removerLider($parametros = array()){
		$id = null;
		
		if(chk_array($parametros, 3) == 'remover'){
			$this->form_msg = $this->controller->Messages->questionYesNo('Tem certeza que deseja apagar esta liderança?', 'Sim', 'Não', $_SERVER['REQUEST_URI'] . '/confirma', HOME_URI . '/eventos/index/editar/' . chk_array($parametros, 4) . '/lideres');
			
			if(is_numeric(chk_array($parametros, 4)) && chk_array($parametros, 5) == 'confirma'){
				$id = chk_array($parametros, 4);
			}
		}
		
		if(!empty($id)){
			$id = (int) $id;
			
			$query = $this->db->delete('tblEventoLideranca', 'id', $id);

			$this->form_msg .= '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/eventos/index/editar/' . chk_array($parametros, 1) . '/lideres/">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/eventos/index/editar/' . chk_array($parametros, 1) . '/lideres/";</script>';
			return;
		}
	}

	public function getLideres($idEvento = null){
		if($idEvento > 0){
			$query = $this->db->query('SELECT * FROM `vwEventosLideres` WHERE `idEvento` = ? ORDER BY `nome`, `sobrenome`', array($idEvento));
		}else{
			return;
		}
		
		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}
	
	public function getLiderancas(){
		$query = $this->db->query('SELECT * FROM `tblLideranca` ORDER BY `lideranca`');
		
		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}
	
	public function notificaLideres($idEvento = null){
		if(empty($idEvento)){
			return;
		}
		
		$lideres = $this->getLideres($idEvento);
		
		if(count($lideres) < 1){
			return;
		}
		
		$queryEvento = $this->db->query('SELECT * FROM `vwEventos` WHERE `id` = ?', array($idEvento));
		
		$evento = $queryEvento->fetch();
		
		foreach($lideres AS $lider){
			if($lider['notificado'] == 'F'){
				$token = sha1(uniqid(time()));
				
				$query = $this->db->update('tblEventoLideranca', 'id', $lider['id'], array('tokenAprovacao' => $token, 'notificado' => 'T'));
				
				$assunto = SYS_NAME . ' - Novo Trabalho';
				
				$urlAceitar = HOME_URI . '/trabalho/index/gerente/aceitar/' . $token;
				$urlRecusar = HOME_URI . '/trabalho/index/gerente/recusar/' . $token;
			
				$mensagem = file_get_contents(HOME_URI . '/email-html/index-gerente.html');
				$mensagem = str_replace("{{apelido}}", $lider['apelido'], $mensagem);
				$mensagem = str_replace("{{gerencia}}", $lider['lideranca'], $mensagem);
				$mensagem = str_replace("{{evento}}", $evento['evento'], $mensagem);
				$mensagem = str_replace("{{inicio}}", implode("/", array_reverse(explode("-", $evento['dataInicio']))), $mensagem);
				$mensagem = str_replace("{{fim}}", implode("/", array_reverse(explode("-", $evento['dataFim']))), $mensagem);
				$mensagem = str_replace("{{local}}", $evento['titulo'], $mensagem);
				$mensagem = str_replace("{{cidade}}", $evento['cidade'] . '/' . $evento['estado'], $mensagem);
				$mensagem = str_replace("{{url-aceitar}}", $urlAceitar, $mensagem);
				$mensagem = str_replace("{{url-recusar}}", $urlRecusar, $mensagem);
				
				$this->ClasseNotificacao->adicionarNotificacao('Você foi selecionado para um trabalho, confira seu email para aceitar ou recusar!', $lider['idLider']);
				$enviaEmail = $this->ClasseMailer->send(chk_array($this->ClasseEmail->getEmail($lider['idLider']), 'email'), $assunto, $mensagem);
			}
		}
		
		$this->form_msg = $this->controller->Messages->success('Notificações enviadas com sucesso.');
		
		return;
	}
}