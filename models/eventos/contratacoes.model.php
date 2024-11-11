<?php 
class ContratacoesModel extends MainModel {
	public $form_data;
	public $form_msg;
	public $db;
	
	private $idEventoFase;
	private $idEventoFaseFuncao;
	private $idPessoa;

	private $erro;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
		
		//$this->ClasseNotificacao = new Notificacao($this->db);
		$this->ClasseEmail = new Email($this->db);
		$this->ClasseMailer = new Mailer(SYS_NAME, MAILER_Host, MAILER_Username, MAILER_Password);
	}

	public function validarFormContratacoes(){
		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}
		
		$this->form_data = array();
		
		$this->idEventoFase = isset($_POST["idEventoFase"]) ? $_POST["idEventoFase"] : null;
		$this->idEventoFaseFuncao = isset($_POST["idEventoFaseFuncao"]) ? $_POST["idEventoFaseFuncao"] : null;
		$this->idPessoa = isset($_POST["idPessoa"]) ? $_POST["idPessoa"] : null;
		
		if(empty($this->idEventoFaseFuncao)){ $this->erro .= "<br>Selecione a função."; }
		if(empty($this->idPessoa)){ $this->erro .= "<br>Selecione o terceirizado."; }
				
		$query = $this->db->query('SELECT * FROM `tblEventoFaseContratado` WHERE `idEventoFaseFuncao` = ? AND `idPessoa` = ?', array($this->idEventoFaseFuncao, $this->idPessoa));
		
		$registro = $query->fetch();
		if(chk_array($this->parametros, 5) == 'editar'){
			if($registro && $registro['id'] != chk_array($this->parametros, 6)){
				$this->erro .= "<br>Contratação já cadastrada.";
			}
		}else{
			if(!empty($registro)){
				$this->erro .= "<br>Contratação já cadastrada.";
			}
		}
	
		$this->form_data['idEventoFase'] = $this->idEventoFase;
		$this->form_data['idEventoFaseFuncao'] = $this->idEventoFaseFuncao;
		$this->form_data['idPessoa'] = $this->idPessoa;
		
		$this->form_data['formulario'] = $this->idEventoFase;
		
		if(!empty($this->erro)){
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
			
			return;
		}
		
		if(empty($this->form_data)){
			return;
		}
		
		$this->adicionarContratacao();
	}
	
	private function adicionarContratacao(){
		$query = $this->db->insert('tblEventoFaseContratado', array('idEventoFaseFuncao' => chk_array($this->form_data, 'idEventoFaseFuncao'), 'idPessoa' => chk_array($this->form_data, 'idPessoa')));

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
	
	public function getContratacao($id = false){
		$s_id = false;
		
		if(!empty($id)){
			$s_id = (int) $id;
		}
		
		if(empty($s_id)){
			return;
		}
		
		$query = $this->db->query('SELECT * FROM `vwEventosFasesContratados` WHERE `id` = ?', array($s_id));
		
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

	public function removerContratacao($parametros = array()){
		$id = null;
		
		if(chk_array($parametros, 5) == 'remover'){
			$this->form_data['formulario'] = chk_array($parametros, 4);
			
			$this->form_msg = $this->controller->Messages->questionYesNo('Tem certeza que deseja apagar esta contratação?', 'Sim', 'Não', $_SERVER['REQUEST_URI'] . '/confirma', HOME_URI . '/eventos/index/contratacoes/' . chk_array($parametros, 1));

			if(is_numeric(chk_array($parametros, 6)) && chk_array($parametros, 7) == 'confirma'){
				$id = chk_array($parametros, 6);
			}
		}
		
		if(!empty($id)){
			$id = (int) $id;
			
			$query = $this->db->delete('tblEventoFaseContratado', 'id', $id);

			$this->form_msg .= '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/eventos/index/contratacoes/' . chk_array($parametros, 1) . '">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/eventos/index/contratacoes/' . chk_array($parametros, 1) . '";</script>';
			return;
		}
	}

	public function getContratacoes($idEventoFase = null, $idFuncao = null, $idEvento = null){
		if($idEventoFase > 0){
			$query = $this->db->query('SELECT * FROM `vwEventosFasesContratados` WHERE `idEventoFase` = ? ORDER BY `dataCriacao` ASC', array($idEventoFase));
			
			if($idFuncao > 0){
				$query = $this->db->query('SELECT * FROM `vwEventosFasesContratados` WHERE `idEventoFase` = ? AND `idFuncao` = ? ORDER BY `dataCriacao` ASC', array($idEventoFase, $idFuncao));
			}
		}elseif($idEvento > 0){
			$query = $this->db->query('SELECT * FROM `vwEventosFasesContratados` WHERE `idEvento` = ? ORDER BY `dataCriacao` ASC', array($idEvento));
		}else{
			return;
		}
		
		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}
	
	public function getTerceirizados($idFuncao = null){
		if($idFuncao > 0){
			$query = $this->db->query("SELECT * FROM `vwUsuariosFuncoes` WHERE `idFuncao` = ? AND `idPermissao`='4' ORDER BY `nome`, `sobrenome`", array($idFuncao));
		}else{
			return;
		}
		
		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}
	
	public function notificaContratados($idEvento = null){
		if(empty($idEvento)){
			return;
		}
		
		$contratados = $this->getContratacoes(null, null, $idEvento);
		
		if(count($contratados) < 1){
			return;
		}
		
		$queryEvento = $this->db->query('SELECT * FROM `vwEventos` WHERE `id` = ?', array($idEvento));
		
		$evento = $queryEvento->fetch();
		
		foreach($contratados AS $contratado){
			if($contratado['notificado'] == 'F'){
				$token = sha1(uniqid(time()));
				
				$query = $this->db->update('tblEventoFaseContratado', 'id', $contratado['id'], array('tokenAprovacao' => $token, 'notificado' => 'T'));
				
				$assunto = SYS_NAME . ' - Novo Trabalho';
				
				$urlAceitar = HOME_URI . '/trabalho/index/contratado/aceitar/' . $token;
				$urlRecusar = HOME_URI . '/trabalho/index/contratado/recusar/' . $token;
			
				$mensagem = file_get_contents(HOME_URI . '/email-html/index-contratado.html');
				$mensagem = str_replace("{{apelido}}", $contratado['apelido'], $mensagem);
				$mensagem = str_replace("{{cargo}}", $contratado['funcao'] . ' - R$ ' . number_format($contratado['valor'], 2, ',', '.') . '/dia', $mensagem);
				$mensagem = str_replace("{{evento}}", $evento['evento'], $mensagem);
				$mensagem = str_replace("{{inicio}}", implode("/", array_reverse(explode("-", $contratado['dataInicio']))), $mensagem);
				$mensagem = str_replace("{{fim}}", implode("/", array_reverse(explode("-", $contratado['dataFim']))), $mensagem);
				$mensagem = str_replace("{{local}}", $evento['titulo'], $mensagem);
				$mensagem = str_replace("{{cidade}}", $evento['cidade'] . '/' . $evento['estado'], $mensagem);
				$mensagem = str_replace("{{lider}}", $contratado['apelidoLider'], $mensagem);
				$mensagem = str_replace("{{url-aceitar}}", $urlAceitar, $mensagem);
				$mensagem = str_replace("{{url-recusar}}", $urlRecusar, $mensagem);
				
				$this->ClasseNotificacao->adicionarNotificacao('Você foi selecionado para um trabalho, confira seu email para aceitar ou recusar!', $contratado['idPessoa']);
				$enviaEmail = $this->ClasseMailer->send(chk_array($this->ClasseEmail->getEmail($contratado['idPessoa']), 'email'), $assunto, $mensagem);
			}
		}
		
		$this->form_msg = $this->controller->Messages->success('Notificações enviadas com sucesso.');
		
		return;
	}
}