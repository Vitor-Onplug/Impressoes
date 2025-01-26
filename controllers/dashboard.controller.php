<?php
class DashBoardController extends MainController
{
	public $login_required = true;
	public $permission_required = 'dashboard';

	public function index()
	{
		$this->title = SYS_NAME . ' - Dashboard';

		if (!$this->logged_in) {
			$this->logout();

			$this->goto_login();

			return;
		}

		$parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();

		$modelo = $this->load_model('dashboard/dashboard');
		$modeloParceiros = $this->load_model('parceiros/parceiros');

		$modeloUsuarios = $this->load_model('usuarios/usuarios');

		if (
			(!isset($_SESSION['idParceiroHash']) ||
			$_SESSION['idParceiroHash'] == 0) &&
			$this->check_permissions('ADMINISTRADOR', $this->userdata['modulo'])) {
			$conteudo = ABSPATH . '/views/dashboard/dashboard.view.php';
		} else {
			$conteudo = ABSPATH . '/views/dashboard/dashboardParceiro.view.php';
		}

		require ABSPATH . '/views/painel/painel.view.php';
	}
}
