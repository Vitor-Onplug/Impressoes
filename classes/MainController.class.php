<?php
class MainController extends UserLogin {
	public $db;
	public $phpass;
	public $title;
	public $login_required = false;
	public $permission_required = 'any';
	public $parametros = array();

	public $Messages;
	
	public function __construct($parametros = array()){
		$this->db = new tix1DB();

		$this->phpass = new PasswordHash(8, false);
		
		$this->parametros = $parametros;
		
		$this->Messages = new Messages();
		
		$this->check_userlogin();
	}

	public function load_model($model_name = false){
		if(!$model_name) return;
		
		$model_name = strtolower($model_name);
		
		$model_path = ABSPATH . '/models/' . $model_name . '.model.php';
		
		if(file_exists($model_path)){
			require_once $model_path;

			$model_name = explode('/', $model_name);
			
			$model_name = end($model_name) . "model";
			
			$model_name = preg_replace('/[^a-zA-Z0-9]/is', '', $model_name);
			
			if(class_exists($model_name)){
				return new $model_name($this->db, $this);
			}
			
			return;
		}
	}
}