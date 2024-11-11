<?php
class UtilsController extends MainController {
	public $login_required = true;

    public function index(){
		return;
    }
	
	public function cep(){
		if (!$this->logged_in){
			$this->logout();
			
			$this->goto_login();
			
			return;
		}
		
		$parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();
	
        $modelo = $this->load_model('utils/utils');
		
		require ABSPATH . '/views/utils/cep.view.php';
	}
	
	public function thumbnail(){
		if (!$this->logged_in){
			$this->logout();
			
			$this->goto_login();
			
			return;
		}
		
		$parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();
	
        $modelo = $this->load_model('utils/utils');
		
		require ABSPATH . '/views/utils/thumbnail.view.php';
	}
}