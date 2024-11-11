<?php 
class FinanceiroModel extends MainModel {
	public $form_data;
	public $form_msg;
	public $db;
	
	private $idCadastro;
	private $idPessoa;
	private $descricao;
	private $data;
	private $tipo;
	private $valor;
	private $quantidade;
	private $observacao;
	private $midia;

	private $erro;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
	}

	public function validarFormOperacao(){
		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}
		
		if(!is_numeric(chk_array($this->parametros, 1))){
			return;
		}
		
		$this->form_data = array();
		
		if($this->userdata['idPermissao'] < 3){
			$this->idCadastro = isset($_POST["idCadastro"]) ? $_POST["idCadastro"] : null;
		}else{
			$this->idCadastro = chk_array($this->userdata, 'id');
		}
		
		$this->idPessoa = isset($_POST["idPessoa"]) ? $_POST["idPessoa"] : null;
		$this->descricao = isset($_POST["descricao"]) ? $_POST["descricao"] : null;
		$this->data = isset($_POST["data"]) ? $_POST["data"] : null;
		$this->tipo = isset($_POST["tipo"]) ? $_POST["tipo"] : null;
		$this->valor = isset($_POST["valor"]) ? $_POST["valor"] : null;
		$this->quantidade = isset($_POST["quantidade"]) ? $_POST["quantidade"] : null;
		$this->observacao = isset($_POST["observacao"]) ? $_POST["observacao"] : null;
		$this->midia = isset($_POST['diretorioMidia']) ? $_POST['diretorioMidia'] : null;
		
		if(empty($this->data)){ $this->erro .= "<br>Selecione a data de pagamento."; }
		if(!empty($this->data)){
			if(!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $this->data)){ $this->erro .= "<br>Data do pagamento inválida."; }
		}
		if(empty($this->descricao)){ $this->erro .= "<br>Preencha a descrição."; }
		if(empty($this->valor)){ $this->erro .= "<br >Preencha o valor do pagamento."; }
		if(!empty($this->valor)){
			if(!preg_match('/^(\d{1,3}){1}(\.\d{3})*(\,\d{2})?$/', $this->valor)){ $this->erro .= "<br >Valor do pagamento: formato inválido."; }
		}
		if(empty($this->quantidade)){ $this->erro .= "<br>Preencha a quantidade."; }
		if(!empty($this->quantidade)){ 
			if(!is_numeric($this->quantidade)){ $this->erro .= "<br>Quantidade: formato inválido."; }
			if($this->quantidade < 1){ $this->erro .= "<br>A quantidade deve ser ao menos 1."; }
		}
		
		if(empty($this->idCadastro)){ $this->erro .= "<br>Selecione o responsável."; }
		if(empty($this->idPessoa)){ $this->erro .= "<br>Selecione a pessoa vinculada."; }
		if(!empty($this->idCadastro)){
			if($this->idCadastro != $this->userdata['id']){
				$query = $this->db->query('SELECT * FROM `vwEventosLideres` WHERE `idEvento` = ? AND `idLider` = ?', array(chk_array($this->parametros, 1), $this->idCadastro));
			
				$registro = $query->fetch();
				
				if(($this->getTotalOperacaoLider(chk_array($this->parametros, 1), 'Débito', $registro['idCadastro']) + str_replace(',', '.', str_replace('.', '', $this->valor))) > $registro['verba']){
					$this->erro .= "<br>Você não tem verba suficiente para realizar essa transação.";
				}
			}else{
				$query = $this->db->query('SELECT * FROM `vwEventos` WHERE `id` = ?', array(chk_array($this->parametros, 1)));
			
				$registro = $query->fetch();
				
				if(($this->getTotalOperacao(chk_array($this->parametros, 1), 'Débito') + str_replace(',', '.', str_replace('.', '', $this->valor))) > $registro['verba']){
					$this->erro .= "<br>O evento não tem verba suficiente para realizar essa transação.";
				}
			}
		}
	
		$this->form_data['idCadastro'] = $this->idCadastro;
		$this->form_data['idPessoa'] = $this->idPessoa;
		$this->form_data['descricao'] = $this->descricao;
		$this->form_data['data'] = $this->data;
		$this->form_data['tipo'] = $this->tipo;
		$this->form_data['valor'] = $this->valor;
		$this->form_data['quantidade'] = $this->quantidade;
		$this->form_data['observacao'] = $this->observacao;
		$this->form_data['midia'] = $this->midia;
		
		if(!empty($this->erro)){
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
			
			return;
		}
		
		if(empty($this->form_data)){
			return;
		}

		$this->form_data['data'] = implode("-", array_reverse(explode("/", $this->data)));
		$this->form_data['valor'] = str_replace(',', '.', str_replace('.', '', $this->valor));
		
		if(chk_array($this->parametros, 2) == 'editar'){
			$this->editarOperacao();
			
			return;
		}else{
			$this->adicionarOperacao();
			
			return;
		}
	}
	
	private function editarOperacao(){
		$id = null;
		
		if(is_numeric(chk_array($this->parametros, 3))){
			$id = chk_array($this->parametros, 3);
		}

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblEventoFinanceiro', 'id', $id, array('idPessoa' => chk_array($this->form_data, 'idPessoa'), 'idCadastro' => chk_array($this->form_data, 'idCadastro'), 'descricao' => chk_array($this->form_data, 'descricao'), 'data' => chk_array($this->form_data, 'data'), 'tipo' => chk_array($this->form_data, 'tipo'), 'valor' => chk_array($this->form_data, 'valor'), 'quantidade' => chk_array($this->form_data, 'quantidade'), 'observacao' => chk_array($this->form_data, 'observacao')));
			
			if(!$query){
				$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
				return;
			}else{
				$this->form_msg = $this->controller->Messages->success('Registro editado com sucesso.');
				$this->form_msg .= '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/eventos/index/perfil/' . chk_array($this->parametros, 1) . '">';
				$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/eventos/index/perfil/' . chk_array($this->parametros, 1) . '";</script>';

				return;
			}
		}
	}
	
	private function adicionarOperacao(){
		$idEvento = chk_array($this->parametros, 1);
		
		$query = $this->db->insert('tblEventoFinanceiro', array('idEvento' => $idEvento, 'idPessoa' => chk_array($this->form_data, 'idPessoa'), 'idCadastro' => chk_array($this->form_data, 'idCadastro'), 'descricao' => chk_array($this->form_data, 'descricao'), 'data' => chk_array($this->form_data, 'data'), 'tipo' => chk_array($this->form_data, 'tipo'), 'valor' => chk_array($this->form_data, 'valor'), 'quantidade' => chk_array($this->form_data, 'quantidade'), 'observacao' => chk_array($this->form_data, 'observacao')));
		
		$idOperacao = $this->db->lastInsertId();
		
		$destinoMidia = "midia/financeiro/" .  date("Y") . "/" . date("m") . "/" . date("d") . "/" . $idOperacao;
		rcopy(chk_array($this->form_data, 'midia'), $destinoMidia);
		rrmdir(chk_array($this->form_data, 'midia'));
		
		if(!$query){
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
			return;
		}else{
			$this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/eventos/index/perfil/' . $idEvento . '">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/eventos/index/perfil/' . $idEvento . '";</script>';
			
			$this->form_data = null;

			return;
		}
	}
	
	public function getOperacao($id = false, $idEvento = null){
		if(empty($id) && empty($idEvento)){
			return;
		}
		
		$query = $this->db->query('SELECT * FROM `vwEventosFinanceiro` WHERE `id` = ? AND `idEvento` = ?', array($id, $idEvento));
		
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
	
	public function removerOperacao($parametros = array()){
		$id = null;
		
		if(chk_array($parametros, 2) == 'remover'){
			$this->form_msg = $this->controller->Messages->questionYesNo('Tem certeza que deseja apagar esta operação?', 'Sim', 'Não', $_SERVER['REQUEST_URI'] . '/confirma', HOME_URI . '/eventos/index/perfil/' . chk_array($parametros, 1));
			
			if(is_numeric(chk_array($parametros, 3)) && chk_array($parametros, 4) == 'confirma'){
				$id = chk_array($parametros, 3);
			}
		}
		
		if(!empty($id)){
			$id = (int) $id;
			
			$query = $this->db->delete('tblEventoFinanceiro', 'id', $id);

			$this->form_msg .= '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/eventos/index/perfil/' . chk_array($parametros, 1) . '">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/eventos/index/perfil/' . chk_array($parametros, 1) . '";</script>';
			return;
		}
	}
	
	public function getOperacoes($idEvento = null){
		if($idEvento > 0){
			$query = $this->db->query('SELECT * FROM `vwEventosFinanceiro` WHERE `idEvento` = ? ORDER BY `data` DESC', array($idEvento));
		}else{
			return;
		}
		
		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}
	
	public function getComprovante($id = null, $idEvento = null){
		if(is_numeric($id) > 0){
			$query = $this->db->query('SELECT * FROM `vwEventosFinanceiro` WHERE `id` = ? AND `idEvento` = ?', array($id, $idEvento));
		}else{
			return;
		}
		
		if(!$query){
			return;
		}
		
		$registro = $query->fetch();
		
		$dataHoraCriacao = explode(" ", $registro["dataCriacao"]);
		$dataCriacao = explode("-", $dataHoraCriacao[0]);
		
		$diretorio = "midia/financeiro/" . $dataCriacao[0] . "/" . $dataCriacao[1] . "/" . $dataCriacao[2] . "/" . $registro["id"];
		
		if(file_exists($diretorio)){
			$lerDiretorio = opendir($diretorio);
			$arquivos = array();
			while ($arquivos[] = readdir($lerDiretorio));
			closedir($lerDiretorio);
			foreach ($arquivos as $arquivo){
				if(preg_match("/\.(jpeg|jpg|gif|png|pdf|doc|docx|xls|xlsx|csv|ppt|pptx){1}$/i", strtolower($arquivo))) {
						return $diretorio . "/". $arquivo;
				}
			}
		}
		
		return "views/standards/images/no-img.jpg";
	}
	
	public function getTotalOperacao($idEvento = null, $operacao = null){
		if(empty($idEvento) && empty($operacao)){
			return;
		}
		
		$query = $this->db->query("SELECT  SUM(`valor` * `quantidade`) AS `total` FROM `vwEventosFinanceiro` WHERE `idEvento` = ? AND `tipo`= ?", array($idEvento, $operacao));
		
		if(!$query){
			$this->form_msg = $this->controller->Messages->error('Registro não encontrado.');

			return;
		}
		
		$registro = $query->fetch();
		
		return $registro['total'];
	}
	
	public function getTotalOperacaoLider($idEvento = null, $operacao = null, $lider = null){
		if(empty($idEvento) && empty($operacao) && empty($lider)){
			return;
		}
		
		$query = $this->db->query("SELECT  SUM(`valor` * `quantidade`) AS `total` FROM `vwEventosFinanceiro` WHERE `idEvento` = ? AND `tipo`= ? AND `idCadastro`= ?", array($idEvento, $operacao, $lider));
		
		if(!$query){
			return;
		}
		
		$registro = $query->fetch();
		
		return $registro['total'];
	}
}