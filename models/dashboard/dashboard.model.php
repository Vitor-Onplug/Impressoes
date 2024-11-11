<?php 
class DashboardModel extends MainModel {
	public $form_data;
	public $form_msg;
	public $db;
	
	private $erro;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
	}

	public function getContratados($limite = null){
		if($limite > 0){
			$query = $this->db->query("SELECT * FROM `vwUsuarios` WHERE `hierarquia`='4' ORDER BY RAND() LIMIT $limite");
		}else{
			$query = $this->db->query("SELECT * FROM `vwUsuarios` WHERE `hierarquia`='4' ORDER BY RAND()");
		}
		
		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}
	
	public function getLideres($limite = null){
		if($limite > 0){
			$query = $this->db->query("SELECT * FROM `vwUsuarios` WHERE `hierarquia`='3' ORDER BY RAND() LIMIT $limite");
		}else{
			$query = $this->db->query("SELECT * FROM `vwUsuarios` WHERE `hierarquia`='3' ORDER BY RAND()");
		}
		
		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}
	
	public function getEventos($limite = null){
		if($limite > 0){
			$query = $this->db->query("SELECT * FROM `vwEventos` ORDER BY `dataInicio` DESC LIMIT $limite");
		}else{
			$query = $this->db->query("SELECT * FROM `vwEventos` ORDER BY `dataInicio` DESC");
		}
		
		if(!$query){
			return array();
		}
		
		return $query->fetchAll();
	}
}