<?php
class PessoasController extends MainController {
	public $login_required = true;

    public function index(){
		$this->title = SYS_NAME . ' - Pessoas';
		
		if (!$this->logged_in){
			$this->logout();
			
			$this->goto_login();
			
			return;
		}
	
		$parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();
		
		$modelo = $this->load_model('pessoas/pessoas');
		
		$modeloDocumentos = $this->load_model('pessoas/documentos');
		$modeloTelefones = $this->load_model('pessoas/telefones');
		$modeloEmails = $this->load_model('pessoas/emails');
		$modeloEnderecos = $this->load_model('pessoas/enderecos');
		$modeloEmpresa = $this->load_model('empresas/empresas');
		
		$modeloUsuarios = $this->load_model('usuarios/usuarios');
		$modeloPermissoes = $this->load_model('permissoes/permissoes');
		
		if(chk_array($this->parametros, 0) == 'adicionar'){
			$this->title = SYS_NAME . ' - Adicionar Pessoa';
			
			$conteudo = ABSPATH . '/views/pessoas/form.view.php';
		}elseif(chk_array($this->parametros, 0) == 'editar'){
			$this->title = SYS_NAME . ' - Editar Pessoa';
			
			$conteudo = ABSPATH . '/views/pessoas/form.view.php';
		}elseif(chk_array($this->parametros, 0) == 'perfil'){
			$this->title = SYS_NAME . ' - Perfil da Pessoa';
			
			$conteudo = ABSPATH . '/views/pessoas/perfil.view.php';
		}elseif(chk_array($this->parametros, 0) == 'trocar-minha-senha'){
			$this->title = SYS_NAME . ' - Trocar Minha Senha';
			
			$conteudo = ABSPATH . '/views/usuarios/trocar-minha-senha.view.php';
		}elseif(chk_array($this->parametros, 0) == 'usuarios'){
			$this->title = SYS_NAME . ' - Usuários';
		
			$conteudo = ABSPATH . '/views/usuarios/usuario.view.php';
		}else{
			$conteudo = ABSPATH . '/views/pessoas/pessoas.view.php';
		}
		
		require ABSPATH . '/views/painel/painel.view.php';
    }
	
	public function upload(){
		$parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();
	
        $modelo = $this->load_model('pessoas/upload');
		$modeloPessoas  = $this->load_model('pessoas/pessoas');
		
		require ABSPATH . '/views/pessoas/upload.view.php';
	}
}