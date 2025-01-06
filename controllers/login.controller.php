<?php
class LoginController extends MainController
{
	public function index()
	{
		if ($this->logged_in) {

			if (!isset($_SESSION['idParceiroHash'])) {
				$this->goto_page('/parceiros');
			} else {
				$this->goto_page('/');
			}
		}

		$parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();

		if (chk_array($this->parametros, 0) == 'esqueci-minha-senha') {
			$modeloUsuarios = $this->load_model('usuarios/usuarios');

			$this->title = SYS_NAME . ' - Esqueci Minha Senha';

			require ABSPATH . '/views/login/esqueci-minha-senha.view.php';
		} else {
			$this->title = SYS_NAME . ' - Autenticação';

			require ABSPATH . '/views/login/login.view.php';
		}
	}
}
