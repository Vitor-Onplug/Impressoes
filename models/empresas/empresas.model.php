<?php 
class EmpresasModel extends MainModel {
    public $form_data;
	public $form_msg;
	public $db;

    private $id;
	private $razaoSocial;
	private $nomeFantasia;
	private $observacoes;
	private $avatar;
	
	private $erro;

	private $ClasseEmpresa;

    public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
		
		$this->ClasseEmpresa = new Empresa($this->db);
	}

    public function validarFormEmpresa(){ 
		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}
		
		$this->form_data = array();
		
		$this->razaoSocial = isset($_POST["razaoSocial"]) ? $_POST["razaoSocial"] : null;
		$this->nomeFantasia = isset($_POST["nomeFantasia"]) ? $_POST["nomeFantasia"] : null;
		$this->observacoes = isset($_POST["observacoes"]) ? $_POST["observacoes"] : null;
		$this->avatar = isset($_POST["diretorioAvatar"]) ? $_POST["diretorioAvatar"] : null;
		
		$validaEmpresa = $this->ClasseEmpresa->validarEmpresa($this->razaoSocial, $this->nomeFantasia, $this->observacoes);
		
		$this->form_data['razaoSocial'] = trim($this->razaoSocial);
		$this->form_data['nomeFantasia'] = trim($this->nomeFantasia);
		$this->form_data['observacoes'] = trim($this->observacoes);
		$this->form_data['avatar'] = trim($this->avatar);
		
		if($validaEmpresa != 1){
			$this->erro .= $validaEmpresa;
		}
	
		if(!empty($this->erro)){
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
			return;
		}
		
		if(empty($this->form_data)){
			return;
		}
		
		if(chk_array($this->parametros, 0) == 'editar'){
			$this->editarEmpresa();
			return;
		}else{
			$this->adicionarEmpresa();
			return;
		}
	}
	
	private function editarEmpresa(){
		if (chk_array($this->parametros, 1)) {
            $hash = chk_array($this->parametros, 1);
            $this->id = decryptHash($hash);
        }

		$editaEmpresa = $this->ClasseEmpresa->editarEmpresa($this->id, chk_array($this->form_data, 'razaoSocial'), chk_array($this->form_data, 'nomeFantasia'), chk_array($this->form_data, 'observacoes'));
			
		if(!$editaEmpresa){
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		}else{
			$this->form_msg = $this->controller->Messages->success('Registro editado com sucesso. Aguarde, você será redirecionado...');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/empresas/index/editar/' . chk_array($this->parametros, 1) .'">';
		}
		
		return;
	}
	
	private function adicionarEmpresa(){
		$insereEmpresa = $this->ClasseEmpresa->adicionarEmpresa(chk_array($this->form_data, 'razaoSocial'), chk_array($this->form_data, 'nomeFantasia'), chk_array($this->form_data, 'observacoes'));
		$this->id = $this->db->lastInsertId();
		
		$destinoAvatar = "midia/avatar/" .  date("Y") . "/" . date("m") . "/" . date("d") . "/" . $this->id;
		rcopy(chk_array($this->form_data, 'avatar'), $destinoAvatar);
		rrmdir(chk_array($this->form_data, 'avatar'));
		
		if(!$insereEmpresa){
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		}else{
			$this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso. Aguarde, você será redirecionado...');
			$hash = encryptId($this->id);
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/empresas/index/editar/'. $hash . '">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/empresas/index/editar/'. $hash . '";</script>';
			
			$this->form_data = null;
		}
		
		return;
	}
	
	public function getEmpresa($id = false){
		if(empty($id)){
			return;
		}
		
		$registro = $this->ClasseEmpresa->getEmpresa($id);
		
		if(empty($registro)){
			$this->form_msg = $this->controller->Messages->error('Registro inexistente.');
			return;
		}
		
		foreach($registro as $key => $value){
			$this->form_data[$key] = $value;
		}
		
		return;
	}

	public function getEmpresas($filtros = null){
		return $this->ClasseEmpresa->getEmpresas($filtros);
	}
	
	public function getAvatar($id = null, $tn = false){
		if(is_numeric($id) && $id > 0){
			$registro = $this->ClasseEmpresa->getEmpresa($id);
		}else{
			return;
		}
		
		$dataHoraCriacao = explode(" ", $registro["dataCriacao"]);
		$dataCriacao = explode("-", $dataHoraCriacao[0]);
		
		$diretorio = "midia/avatar/" . $dataCriacao[0] . "/" . $dataCriacao[1] . "/" . $dataCriacao[2] . "/" . $registro["id"];
		
		if(file_exists($diretorio)){
			$lerDiretorio = opendir($diretorio);
			$imagens = array();
			while($imagens[] = readdir($lerDiretorio));
			closedir($lerDiretorio);
			foreach($imagens as $imagem) {
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
	
	public function bloquearEmpresa(){
		$id = null;
		
		if (chk_array($this->parametros, 1)) {
            $hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
        }

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblEmpresa', 'id', $id, array('status' => 'F'));

			$this->form_msg = $this->controller->Messages->success('Registro bloqueado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/empresas/">';
		
			return;
		}
	}
	
	public function desbloquearEmpresa(){
		$id = null;
		
		if (chk_array($this->parametros, 1)) {
            $hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
        }

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblEmpresa', 'id', $id, array('status' => 'T'));
			
			$this->form_msg = $this->controller->Messages->success('Registro desbloqueado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/empresas/">';
		
			return;
		}
	}
}
?>
