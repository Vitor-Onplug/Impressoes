<?php 
class EventosModel extends MainModel {
	public $form_data;
	public $form_msg;
	public $db;
	
	private $evento;
	private $idCategoria;
	private $cliente;
	private $dataInicioFim;
	private $dataInicio;
	private $dataFim;
	private $valorContrato;
	private $imposto;
	private $verba;
	private $escritorio;
	private $midia;
	private $local;

	private $erro;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
	}

	public function validarFormEventos(){
		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}
		
		$this->form_data = array();
		
		$this->evento = isset($_POST["evento"]) ? $_POST["evento"] : null;
		$this->idCategoria = isset($_POST["idCategoria"]) ? $_POST["idCategoria"] : null;
		$this->cliente = isset($_POST["cliente"]) ? $_POST["cliente"] : null;
		$this->dataInicioFim = isset($_POST["dataInicioFim"]) ? $_POST["dataInicioFim"] : null;
		$this->valorContrato = isset($_POST["valorContrato"]) ? $_POST["valorContrato"] : null;
		$this->imposto = isset($_POST["imposto"]) ? $_POST["imposto"] : null;
		$this->verba = isset($_POST["verba"]) ? $_POST["verba"] : null;
		$this->verba = isset($_POST["verba"]) ? $_POST["verba"] : null;
		$this->escritorio = isset($_POST["escritorio"]) ? $_POST["escritorio"] : null;
		$this->midia = isset($_POST['diretorioMidia']) ? $_POST['diretorioMidia'] : null;
		$this->local = isset($_POST['local']) ? $_POST['local'] : null;
		
		if(empty($this->evento)){ $this->erro .= "<br>Preencha o nome do evento."; }
		if(empty($this->idCategoria)){ $this->erro .= "<br>Selecione a categoria."; }
		if(empty($this->cliente)){ $this->erro .= "<br>Selecione o cliente."; }
		if(empty($this->dataInicioFim)){ $this->erro .= "<br>Preencha a data de início e fim do evento."; }
		if(!empty($this->dataInicioFim)){
			if(!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}([^\b])[\-]([^\b])\d{1,2}\/\d{1,2}\/\d{4}$/', $this->dataInicioFim)){ 
				$erro .= "<br>Intervalo de datas de início e fim inválido."; 
			}else{
				$datas = explode(" - ", $this->dataInicioFim);
				$this->dataInicio = $datas[0];
				$this->dataFim = $datas[1];
				
				if(!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $this->dataInicio)){ $this->erro .= "<br>Data de início do evento inválida."; }
				if(!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $this->dataFim)){ $this->erro .= "<br>Data de término do evento inválida."; }
				
				if(diff_datas(implode("/", array_reverse(explode("-", $this->dataInicio))), implode("/", array_reverse(explode("-", $this->dataFim)))) < 0){
					$this->erro .= "<br>Data de término deve ser maior que a data de início.";
				}
			}
		}
		if(!empty($this->valorContrato)){
			if(!preg_match('/^(\d{1,3}){1}(\.\d{3})*(\,\d{2})?$/', $this->valorContrato)){ $this->erro .= "<br >Valor do Contrato: formato inválido."; }
		}
		if(!empty($this->imposto)){
			if(!preg_match('/(^(100(?:,0{1,2})?))|(?!^0*$)(?!^0*,0*$)^\d{1,2}(,\d{1,2})?$/', $this->imposto)){ $this->erro .= "<br >Imposto: formato inválido."; }
		}
		if(empty($this->verba)){ $this->erro .= "<br >Preencha a verba do evento."; }
		if(!empty($this->verba)){
			if(!preg_match('/^(\d{1,3}){1}(\.\d{3})*(\,\d{2})?$/', $this->verba)){ $this->erro .= "<br >Verba do evento: formato inválido."; }
		}
		if(empty($this->escritorio)){ $this->erro .= "<br >Preencha o gasto do escritório."; }
		if(!empty($this->escritorio)){
			if(!preg_match('/^(\d{1,3}){1}(\.\d{3})*(\,\d{2})?$/', $this->escritorio)){ $this->erro .= "<br >Gasto do escritório: formato inválido."; }
		}
		if(empty($this->local)){ $this->erro .= "<br>Selecione o local do evento."; }

		$this->form_data['evento'] = $this->evento;
		$this->form_data['idCategoria'] = $this->idCategoria;
		$this->form_data['cliente'] = $this->cliente;
		$this->form_data['dataInicioFim'] = $this->dataInicioFim;
		$this->form_data['dataInicio'] = implode("-", array_reverse(explode("/", $this->dataInicio)));
		$this->form_data['dataFim'] = implode("-", array_reverse(explode("/", $this->dataFim)));
		$this->form_data['valorContrato'] = str_replace(',', '.', str_replace('.', '', $this->valorContrato));
		$this->form_data['imposto'] = str_replace(',', '.', $this->imposto);
		$this->form_data['verba'] = str_replace(',', '.', str_replace('.', '', $this->verba));
		$this->form_data['escritorio'] = str_replace(',', '.', str_replace('.', '', $this->escritorio));
		$this->form_data['midia'] = $this->midia;
		$this->form_data['local'] = $this->local;
	
		if(!empty($this->erro)){
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
			
			return;
		}
		
		if(empty($this->form_data)){
			return;
		}
		
		if(chk_array($this->parametros, 0) == 'editar'){
			$this->editarEvento();
			
			return;
		}else{
			$this->adicionarEvento();
			
			return;
		}
	}
	
	private function editarEvento(){
		$id = null;
		
		if(is_numeric(chk_array($this->parametros, 1))){
			$id = chk_array($this->parametros, 1);
		}

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblEvento', 'id', $id, array('idCategoria' => chk_array($this->form_data, 'idCategoria'), 'idCliente' => chk_array($this->form_data, 'cliente'), 'idLocal' => chk_array($this->form_data, 'local'), 'evento' => chk_array($this->form_data, 'evento'), 'dataInicio' => chk_array($this->form_data, 'dataInicio'), 'dataFim' => chk_array($this->form_data, 'dataFim'), 'valorContrato' => chk_array($this->form_data, 'valorContrato'), 'imposto' => chk_array($this->form_data, 'imposto'), 'verba' => chk_array($this->form_data, 'verba'), 'verbaEscritorio' => chk_array($this->form_data, 'escritorio')));
			
			if(!$query){
				$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
				return;
			}else{
				$this->form_msg = $this->controller->Messages->success('Registro editado com sucesso.');

				return;
			}
		}
	}
	
	private function adicionarEvento(){
		$query = $this->db->insert('tblEvento', array('idCategoria' => chk_array($this->form_data, 'idCategoria'), 'idCliente' => chk_array($this->form_data, 'cliente'), 'idLocal' => chk_array($this->form_data, 'local'), 'evento' => chk_array($this->form_data, 'evento'), 'dataInicio' => chk_array($this->form_data, 'dataInicio'), 'dataFim' => chk_array($this->form_data, 'dataFim'), 'valorContrato' => chk_array($this->form_data, 'valorContrato'), 'imposto' => chk_array($this->form_data, 'imposto'), 'verba' => chk_array($this->form_data, 'verba'), 'verbaEscritorio' => chk_array($this->form_data, 'escritorio')));
		
		$idEvento = $this->db->lastInsertId();
		
		$destinoMidia = "midia/eventos/" .  date("Y") . "/" . date("m") . "/" . date("d") . "/" . $idEvento;
		rcopy(chk_array($this->form_data, 'midia'), $destinoMidia);
		rrmdir(chk_array($this->form_data, 'midia'));
		
		if(!$query){
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
			return;
		}else{
			$this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/eventos/index/editar/' . $idEvento . '/lideres">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/eventos/index/editar/' . $idEvento . '/lideres";</script>';
			
			$this->form_data = null;

			return;
		}
	}
	
	public function getEvento($id = false){
		$s_id = false;
		
		if(!empty($id)){
			$s_id = (int) $id;
		}
		
		if(empty($s_id)){
			return;
		}
		
		$query = $this->db->query('SELECT * FROM `vwEventos` WHERE `idEvento` = ?', array($s_id));
		
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

	public function removerEvento($parametros = array()){
		$id = null;
		
		if(chk_array($parametros, 0) == 'remover'){
			$this->form_msg = $this->controller->Messages->questionYesNo('Tem certeza que deseja apagar este evento?', 'Sim', 'Não', $_SERVER['REQUEST_URI'] . '/confirma', HOME_URI . '/eventos');
			
			if(is_numeric(chk_array($parametros, 1)) && chk_array($parametros, 2) == 'confirma'){
				$id = chk_array($parametros, 1);
			}
		}
		
		if(!empty($id)){
			$id = (int) $id;
			
			$query = $this->db->delete('tblEvento', 'id', $id);

			$this->form_msg .= '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/eventos/">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/eventos/";</script>';
			return;
		}
	}

	public function getEventos($filtros = null){
		$where = null;
		
		if(!empty($filtros["dataInicioFim"])){
			if(!empty($where)){
				$where .= " AND ";
			}else{
				$where = " WHERE ";
			}
			
			$datas = explode(" - ", $filtros["dataInicioFim"]);
			$dataInicio = implode("-", array_reverse(explode("/", $datas[0])));
			$dataFim = implode("-", array_reverse(explode("/", $datas[1])));
			
			if($dataInicio == $dataFim){
				$where .= "DATE(`vwEventos`.`dataInicio`) >= DATE('" . $dataInicio . "')";
			}else{
				$where .= "(DATE(`vwEventos`.`dataInicio`) >= DATE('" . $dataInicio . "') AND DATE(`vwEventos`.`dataInicio`) <= DATE('" . $dataFim . "'))";
			}
		}
		
		if(!empty($filtros["categoria"])){
			if(!empty($where)){
				$where .= " AND ";
			}else{
				$where = " WHERE ";
			}
			
			$where .= "(`vwEventos`.`idCategoria` = '" . $filtros['categoria'] . "')";
		}
		
		if(!empty($filtros["cliente"])){
			if(!empty($where)){
				$where .= " AND ";
			}else{
				$where = " WHERE ";
			}
			
			$where .= "(`vwEventos`.`idCliente` = '" . $filtros['cliente'] . "')";
		}
		
		if(!empty($filtros["local"])){
			if(!empty($where)){
				$where .= " AND ";
			}else{
				$where = " WHERE ";
			}
			
		$where .= "(`vwEventos`.`idLocal` = '" . $filtros['local'] . "')";
		}
		
		// if(!empty($filtros["fase"])){
		// 	if(!empty($where)){
		// 		$where .= " AND ";
		// 	}else{
		// 		$where = " WHERE ";
		// 	}
			
		// 	$where .= "(`vwEventosFases`.`idFase` = '" . $filtros['fase'] . "')";
		// }
		
		if(!empty($filtros["q"])){
			if(!empty($where)){
				$where .= " AND ";
			}else{
				$where = " WHERE ";
			}
			
			$where .= "((`vwEventos`.`evento` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`vwEventos`.`titulo` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`vwEventos`.`tituloCurto` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR ";
			$where .= "(`vwEventos`.`cep` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`vwEventos`.`logradouro` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`vwEventos`.`complemento` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR ";
			$where .= "(`vwEventos`.`zona` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`vwEventos`.`bairro` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`vwEventos`.`cidade` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR ";
			$where .= "(`vwEventos`.`estado` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`vwEventos`.`categoria` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`vwEventos`.`nomeFantasia` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR ";
			$where .= "(`vwEventos`.`razaoSocial` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`vwEventos`.`nome` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`vwEventos`.`sobrenome` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR ";
			$where .= "(`vwEventos`.`email` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`vwEventos`.`nome` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR (`vwEventos`.`sobrenome` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR ";
		}
		
		$sql = "SELECT `vwEventos`.* FROM `vwEventos`";
		$sql .= " $where ";
		$sql .= "GROUP BY `vwEventos`.`idEvento` ORDER BY `vwEventos`.`dataInicio` DESC";
			
		$query = $this->db->query($sql);
		
		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}
	
	public function getAvatar($id = null, $tn = false){
		if(is_numeric($id) > 0){
			$query = $this->db->query('SELECT * FROM `vwEventos` WHERE `idEvento` = ?', array($id));
		}else{
			return;
		}
		
		if(!$query){
			return;
		}
		
		$registro = $query->fetch();
		
		$dataHoraCriacao = explode(" ", $registro["dataCriacao"]);
		$dataCriacao = explode("-", $dataHoraCriacao[0]);
		
		$diretorio = "midia/eventos/" . $dataCriacao[0] . "/" . $dataCriacao[1] . "/" . $dataCriacao[2] . "/" . $registro["idEvento"];
		
		if(file_exists($diretorio)){
			$lerDiretorio = opendir($diretorio);
			$imagens = array();
			while ($imagens[] = readdir($lerDiretorio));
			closedir($lerDiretorio);
			foreach ($imagens as $imagem) {
				if(preg_match("/\.(jpg|jpeg|gif|png){1}$/i", strtolower($imagem))) {
					if($tn){
						return $diretorio . "/tn/". $imagem;
					}else{
						return $diretorio . "/". $imagem;
					}
				}
			}
		}
		
		return "views/standards/images/no-img.jpg";
	}

	public function bloquearEvento(){
		$id = null;
		
		if(chk_array($this->parametros, 1)){
			$hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
		}

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblevento', 'id', $id, array('status' => 'F'));
			
			$this->form_msg = $this->controller->Messages->success('Evento bloqueado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos">';
		
			return;
		}
	}
	
	public function desbloquearEvento(){
		$id = null;
        
		if(chk_array($this->parametros, 1)){
			$hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
		}

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblevento', 'id', $id, array('status' => 'T'));
			
			$this->form_msg = $this->controller->Messages->success('Evento desbloqueado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos">';
		
			return;
		}
	}
}