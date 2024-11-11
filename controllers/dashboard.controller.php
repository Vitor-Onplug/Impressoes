<?php
class DashBoardController extends MainController {
	public $login_required = true;
	public $permission_required = 'dashboard';

    public function index() {
		$this->title = SYS_NAME . ' - Dashboard';
		
		if (!$this->logged_in){
			$this->logout();
			
			$this->goto_login();
			
			return;
		}
		
		if(!$this->check_permissions($this->permission_required, $this->userdata['modulo'])){
			$this->goto_page(HOME_URI . '/eventos');
			
			return;
		}
		
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
		
		$modelo = $this->load_model('dashboard/dashboard');
		
		$modeloUsuarios = $this->load_model('usuarios/usuarios');
		
		$conteudo = ABSPATH . '/views/dashboard/dashboard.view.php';
	
        require ABSPATH . '/views/painel/painel.view.php';
    }
	
}