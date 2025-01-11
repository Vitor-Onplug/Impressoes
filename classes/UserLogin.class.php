<?php
class UserLogin
{
	public $logged_in;
	public $userdata;
	public $login_error;

	public function check_userlogin()
	{
		if (isset($_SESSION['userdata']) && !empty($_SESSION['userdata']) && is_array($_SESSION['userdata']) && !isset($_POST['userdata'])) {
			$userdata = $_SESSION['userdata'];
			$userdata['post'] = false;
		}

		if (isset($_POST['userdata']) && !empty($_POST['userdata']) && is_array($_POST['userdata'])) {
			$userdata = $_POST['userdata'];
			$userdata['post'] = true;
		}

		if (!isset($userdata) || !is_array($userdata)) {
			$this->logout();

			return;
		}

		if ($userdata['post'] === true) {
			$post = true;
		} else {
			$post = false;
		}

		unset($userdata['post']);

		if (empty($userdata)) {
			$this->logged_in = false;
			$this->login_error = null;

			$this->logout();

			return;
		}

		extract($userdata);

		if ((!isset($email) || !isset($senha)) && (!isset($userdata['email']) || !isset($userdata['senha']))) {
			$this->logged_in = false;
			$this->login_error = null;

			$this->logout();

			return;
		}

		$query = $this->db->query('SELECT * FROM `vwLogin` WHERE email = ? LIMIT 1', array($email));

		if (!$query) {
			$this->logged_in = false;
			$this->login_error = $this->Messages->error('Erro interno.');

			$this->logout();

			return;
		}

		$fetch = $query->fetch(PDO::FETCH_ASSOC);

		$user_id = (int) $fetch['id'];

		if (empty($user_id)) {
			$this->logged_in = false;
			$this->login_error = $this->Messages->error('Usuário não encontrado.');

			$this->logout();

			return;
		}

		$user_status = $fetch['status'];

		if ($user_status == 'F') {
			$this->logged_in = false;
			$this->login_error = $this->Messages->error('Usuário bloqueado.');

			$this->logout();

			return;
		}

		if ($this->phpass->CheckPassword($senha, $fetch['senha'])) {
			if (session_id() != $fetch['sessionID'] && !$post) {
				$this->logged_in = false;
				$this->login_error = $this->Messages->error('Sessão inválida.');

				$this->logout();

				return;
			}

			if ($post) {
				session_regenerate_id();
				$session_id = session_id();

				// Atribui os dados do usuário à variável de sessão
				$_SESSION['userdata'] = $fetch;

				$_SESSION['userdata']['senha'] = $senha;

				$_SESSION['userdata']['sessionID'] = $session_id;

				$query = $this->db->query('UPDATE `tblUsuario` SET `sessionID` = ?, `token`= null WHERE `idPessoa` = ?',	array($session_id, $user_id));
			}


			//$_SESSION['userdata']['modulo'] = unserialize($fetch['modulo']);

			$this->logged_in = true;

			$this->userdata = $_SESSION['userdata'];

			if (isset($_SESSION['goto_url'])) {
				$goto_url = urldecode($_SESSION['goto_url']);

				unset($_SESSION['goto_url']);

				echo '<meta http-equiv="refresh" content="0; url=' . $goto_url . '">';
				echo '<script type="text/javascript">window.location.href = "' . $goto_url . '";</script>';
			}

			return;
		} else {
			$this->logged_in = false;

			$this->login_error = $this->Messages->error('Senha inválida.');

			$this->logout();

			return;
		}
	}

	protected function logout($redirect = false)
	{
		$_SESSION['userdata'] = array();

		unset($_SESSION['userdata']);

		// Limpa a sessão
		$_SESSION = array();

		session_regenerate_id();

		if ($redirect === true) {
			$this->goto_login();
		}
	}

	protected function goto_login()
	{
		if (defined('HOME_URI')) {
			$login_uri  = HOME_URI . '/login/';

			$_SESSION['goto_url'] = urlencode($_SERVER['REQUEST_URI']);

			echo '<meta http-equiv="refresh" content="0; url=' . $login_uri . '">';
			echo '<script type="text/javascript">window.location.href = "' . $login_uri . '";</script>';
		}

		return;
	}

	final protected function goto_page($page_uri = null)
	{
		if (isset($_GET['url']) && !empty($_GET['url']) && !$page_uri) {
			$page_uri  = urldecode($_GET['url']);
		}

		if ($page_uri) {
			echo '<meta http-equiv="refresh" content="0; url=' . $page_uri . '">';
			echo '<script type="text/javascript">window.location.href = "' . $page_uri . '";</script>';

			return;
		}
	}

	final public function check_permissions($required = 'SUPERADMIN', $user_permissions = '')
	{
		// Se as permissões do usuário vierem como string, converte para array
		if (is_string($user_permissions)) {
			$user_permissions = explode(',', $user_permissions);
		}

		// Se não tiver permissões, retorna false
		if (empty($user_permissions)) {
			return false;
		}

		// Array global de permissões
		global $permissions;

		// Se o usuário tem permissão de SUPERADMIN, permite tudo
		if (in_array(1, array_map('trim', $user_permissions))) {
			return true;
		}

		// Procura o ID da permissão requerida
		$required_id = array_search($required, $permissions);

		// Se não encontrou a permissão no array
		if ($required_id === false) {
			return false;
		}

		// Verifica se o usuário tem a permissão requerida
		if (in_array($required_id, array_map('trim', $user_permissions))) {
			return true;
		}

		return false;
	}

	// final public function check_permissions($required = 'any', $user_permissions = array('any')){
	// 	if(!is_array($user_permissions)){
	// 		return;
	// 	}

	// 	if(!in_array($required, $user_permissions)){
	// 		return false;
	// 	}else{
	// 		return true;
	// 	}
	// }
}
