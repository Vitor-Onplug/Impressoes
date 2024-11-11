<?php 
class CategoriasModel extends MainModel {
    public $form_data;
	public $form_msg;
	public $db;

    private $categoria;
	
    private $erro;

    public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
	}

    public function getCategorias($filtros = null){

        $where = null;

        if(!empty($filtros["q"])){
			if(!empty($where)){
				$where .= " AND ";
			}else{
				$where = " WHERE ";
			}
			
			$where .= "((`tblEventoCategoria`.`categoria` LIKE '%" . _otimizaBusca($filtros['q']) . "%') OR ";
		}
		
		$sql = "SELECT `tblEventoCategoria`.* FROM `tblEventoCategoria`";
		$sql .= " $where ";
			
		$query = $this->db->query($sql);
		
		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
    
    }

}