<?php 
class ParceirosModel extends MainModel {
    public $form_data;
	public $form_msg;
	public $db;

    private $id;
    private $idEmpresa;
    private $observacoes;
	
	private $erro;

    public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
	}

    public function validarParceiro(){ 
        $this->form_data = array();
        
        $this->idEmpresa = isset($_POST["idEmpresa"]) ? $_POST["idEmpresa"] : null;
        $this->observacoes = isset($_POST["observacoes"]) ? $_POST["observacoes"] : null;
        
        if(empty($this->idEmpresa)){ $this->erro .= "<br>Selecione uma empresa para o parceiro."; }
        
        if(!empty($this->erro)){
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
			return;
		}

        $this->form_data['idEmpresa'] = trim($this->idEmpresa);
        $this->form_data['observacoes'] = trim($this->observacoes);
		
		if(empty($this->form_data)){
			return;
		}
		
		if(chk_array($this->parametros, 0) == 'editar'){
			$this->editarParceiro();
			return;
		}else{
			$this->adicionarParceiro();
			return;
		}
    }

    public function getParceiro($idParceiro = null){
        if(is_numeric($idParceiro) > 0){
            $query = $this->db->query('SELECT * FROM `tblParceiro` WHERE `id` = ?', array($idParceiro));
        }else{
            return;
        }
        
        if(!$query){
            return 'Registro não encontrado.';
        }
        
        $registro = $query->fetch();
        
        if(empty($registro)){
            return 'Registro inexistente.';
        }
        
        return $registro;
    }

    public function getParceiros($filtros = null){

        $where = null;
        $limit = null;
        
        if(!empty($filtros["q"])){
            if(!empty($where)){
                $where .= " AND ";
            }else{
                $where = " WHERE ";
            }
            
			$where .= "(`tblEmpresa`.`razaoSocial` LIKE '%" . _otimizaBusca($filtros['q']) . "%' OR `tblEmpresa`.`razaoSocial` LIKE '%" . _otimizaBusca($filtros['q']) . "%') ";
        }
        
        if(!empty($filtros["limite"])){
            $limit = "LIMIT " . $filtros["limite"];
        }
        
        if(!empty($filtros["ordena"]) && !empty($filtros["ordem"])){
            $orderby = "ORDER BY " . $filtros["ordena"] . " " .  $filtros["ordem"];
        }else{
            $orderby = "ORDER BY `tblEmpresa`.`razaoSocial`, `tblParceiro`.`id`";
        }
        
        $sql = "SELECT `tblParceiro`.*, `tblEmpresa`.`razaoSocial` FROM `tblParceiro` 
        INNER JOIN `tblEmpresa` ON `tblParceiro`.`idEmpresa` = `tblEmpresa`.`id` 
        
         $where $orderby $limit";
        
        $query = $this->db->query($sql);

        if(!$query){
            return array();
        }
        
        return $query->fetchAll();
    }

    public function adicionarParceiro()
    {
 
        if (empty(chk_array($this->form_data, 'idEmpresa'))) {
            $this->form_msg = '<p class="form_error">Selecione uma empresa para o parceiro.</p>';
            return;
        }

        $query = $this->db->insert('tblParceiro', array(
            'idEmpresa' => chk_array($this->form_data, 'idEmpresa'),
            'observacoes' => chk_array($this->form_data, 'observacoes'),
            'dataCriacao' => date('Y-m-d H:i:s')
        ));
        $this->id = $this->db->lastInsertId();

        if ($query) {
            $this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso. Aguarde, você será redirecionado...');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/parceiros' . '">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/parceiros'. '";</script>';
			
			$this->form_data = null;
            return;
        } else {
            $this->form_msg = '<p class="form_error">Erro ao adicionar parceiro.</p>';
            return;
        }
    }

    public function editarParceiro()
    {
       
    }

    public function desbloquearParceiro()
    {

    }

    public function bloquearParceiro()
    {

    }
}