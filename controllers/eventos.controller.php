<?php
class EventosController extends MainController
{
	public $login_required = true;

	public function index()
	{
		$this->title = SYS_NAME . ' - Eventos';

		if (!$this->logged_in) {
			$this->logout();

			$this->goto_login();

			return;
		}

		$parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();

		$modelo = $this->load_model('eventos/eventos');
		$modeloUsuarios = $this->load_model('usuarios/usuarios');

		$modeloSetores = $this->load_model('eventos/setores');
		$modeloTerminais = $this->load_model('eventos/terminais');
		$modeloLeitores = $this->load_model('eventos/leitores');
		$modeloLotes = $this->load_model('eventos/lotes');

		$modeloCategorias = $this->load_model('eventos/categorias');

		//$modeloLocais = $this->load_model('eventos/locais');
		//$modeloContratacoes = $this->load_model('eventos/contratacoes');
		$modeloParceiros = $this->load_model('parceiros/parceiros');

		$parametro = chk_array($this->parametros, 0) ?? '';

		// Primeiro, verificamos se o parâmetro é relacionado a "setor" ou "terminal"
		if (preg_match('/setor(es)?/i', $parametro)) {
			if (chk_array($this->parametros, 0) == 'adicionarSetor') {
				$this->title = SYS_NAME . ' - Adicionar Setor';
				$conteudo = ABSPATH . '/views/eventos/setores/adicionar.view.php';
			} elseif (chk_array($this->parametros, 0) == 'editarSetor') {
				$this->title = SYS_NAME . ' - Editar Setor';
				$conteudo = ABSPATH . '/views/eventos/setores/editar.view.php';
			} else {
				$this->title = SYS_NAME . ' - Setores do Evento';
				$conteudo = ABSPATH . '/views/eventos/setores/setores.view.php';
			}
		} elseif (preg_match('/terminal(es)?/i', $parametro) || preg_match('/terminais/i', $parametro)) {
			if (chk_array($this->parametros, 0) == 'adicionarTerminal') {
				$this->title = SYS_NAME . ' - Adicionar Terminal';
				$conteudo = ABSPATH . '/views/eventos/terminais/adicionar.view.php';
			} elseif (chk_array($this->parametros, 0) == 'editarTerminal') {
				$this->title = SYS_NAME . ' - Editar Terminal';
				$conteudo = ABSPATH . '/views/eventos/terminais/editar.view.php';
			} else {
				$this->title = SYS_NAME . ' - Terminais do Evento';
				$conteudo = ABSPATH . '/views/eventos/terminais/terminais.view.php';
			}
		} elseif (preg_match('/leitor(es)?/i', $parametro)) {
			if(chk_array($this->parametros, 0) == 'adicionarLeitor'){
				$this->title = SYS_NAME . ' - Adicionar Leitor';
				$conteudo = ABSPATH . '/views/eventos/leitores/adicionar.view.php';
			}elseif(chk_array($this->parametros, 0) == 'editarLeitor'){
				$this->title = SYS_NAME . ' - Editar Leitor';
				$conteudo = ABSPATH . '/views/eventos/leitores/editar.view.php';
			}else{
				$this->title = SYS_NAME . ' - Leitores do Evento';
				$conteudo = ABSPATH . '/views/eventos/leitores/leitores.view.php';
			}
		} elseif (preg_match('/lote(es)?/i', $parametro)) {
			if(chk_array($this->parametros, 0) == 'adicionarLote'){
				$this->title = SYS_NAME . ' - Adicionar Lote';
				$conteudo = ABSPATH . '/views/eventos/lotes/adicionar.view.php';
			}elseif(chk_array($this->parametros, 0) == 'editarLote'){
				$this->title = SYS_NAME . ' - Editar Lote';
				$conteudo = ABSPATH . '/views/eventos/lotes/editar.view.php';
			}elseif(chk_array($this->parametros, 0) == 'relacoesLote'){
				$this->title = SYS_NAME . ' - Relações do Lotes';
				$conteudo = ABSPATH . '/views/eventos/lotes/relacoes.view.php';
			}
			else{
				$this->title = SYS_NAME . ' - Lotes do Evento';
				$conteudo = ABSPATH . '/views/eventos/lotes/lotes.view.php';
			}
		}
		else {
			// Agora aplicamos o switch para os outros casos
			switch ($parametro) {
				case 'adicionar':
					$this->title = SYS_NAME . ' - Adicionar Evento';
					$conteudo = ABSPATH . '/views/eventos/form.view.php';
					break;
				case 'editar':
					if (!$this->check_permissions('evento-editar', $this->userdata['modulo'])) {
						require_once ABSPATH . '/views/permission.view.php';
						return;
					}
					$this->title = SYS_NAME . ' - Editar Evento';
					$conteudo = ABSPATH . '/views/eventos/form.view.php';
					break;
				case 'perfil':
					$this->title = SYS_NAME . ' - Perfil do Evento';
					$conteudo = ABSPATH . '/views/eventos/perfil.view.php';
					break;
				case 'setEvento':
					$idPost = isset($_POST['idEvento']) ? $_POST['idEvento'] : 0;
					$hash = encryptId($idPost);
					if ($_SESSION['idEventoHash'] != $hash) {
						$_SESSION['idEventoHash'] = $hash;
					}
					$conteudo = ABSPATH . '/views/dashboard/dashboard.view.php';
					break;
				default:
					// Caso padrão para eventos
					$conteudo = ABSPATH . '/views/eventos/eventos.view.php';
					break;
			}
		}

		require ABSPATH . '/views/painel/painel.view.php';
	}

	public function json()
	{
		if (!$this->logged_in) {
			$this->logout();

			$this->goto_login();

			return;
		}

		$parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();

		if (chk_array($this->parametros, 0) == 'terceirizados') {
			$modelo = $this->load_model('eventos/contratacoes');

			require ABSPATH . '/views/eventos/terceirizados.json.view.php';
		}
	}

	public function upload()
	{
		$parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();

		$modelo = $this->load_model('eventos/upload');

		require ABSPATH . '/views/eventos/upload.view.php';
	}
}
