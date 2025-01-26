<?php
class EmpresasController extends MainController
{

	public $login_required = true;

	public function index()
	{
		$this->title = SYS_NAME . ' - Empresas';

		if (!$this->logged_in) {
			$this->logout();

			$this->goto_login();

			return;
		}

		$parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();

		$modelo = $this->load_model('empresas/empresas');

		$modeloDocumentos = $this->load_model('empresas/documentos');
		$modeloTelefones = $this->load_model('empresas/telefones');
		$modeloEmails = $this->load_model('empresas/emails');
		$modeloEnderecos = $this->load_model('empresas/enderecos');
		$modeloDepartamentos = $this->load_model('empresas/departamentos');

		// $modeloUsuarios = $this->load_model('empresas/usuarios');
		// $modeloPermissoes = $this->load_model('empresas/permissoes');

		if (chk_array($this->parametros, 0) == 'adicionar') {
			$this->title = SYS_NAME . ' - Adicionar Empresa';

			$conteudo = ABSPATH . '/views/empresas/form.view.php';
		} elseif (chk_array($this->parametros, 0) == 'editar') {
			$this->title = SYS_NAME . ' - Editar Empresa';

			$conteudo = ABSPATH . '/views/empresas/form.view.php';
		} elseif (chk_array($this->parametros, 0) == 'perfil') {
			$this->title = SYS_NAME . ' - Perfil da Empresa';

			$conteudo = ABSPATH . '/views/empresas/perfil.view.php';
		} elseif (chk_array($this->parametros, 0) == 'departamentos') {

			$this->title = SYS_NAME . ' - Departamentos da Empresa';

			$conteudo = ABSPATH . '/views/empresas/departamentos.view.php';
		} else {
			$conteudo = ABSPATH . '/views/empresas/empresas.view.php';
		}

		require ABSPATH . '/views/painel/painel.view.php';
	}
}
