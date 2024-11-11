<?php
class ErrorController extends MainController {
    public function index() {
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
		
		$this->title = SYS_NAME . " - Ooops, página não encontrada!";
		
		require ABSPATH . '/views/404.view.php';
    }
	
}