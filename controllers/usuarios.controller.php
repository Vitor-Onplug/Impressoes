<?php
class UsuariosController extends MainController
{

    public $login_required = true;

    public function index()
    {
        $this->title = SYS_NAME . ' - Usuários';

        if (!$this->logged_in) {
            $this->logout();

            $this->goto_login();

            return;
        }

        $parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();
		
		$modelo = $this->load_model('usuarios/usuarios');

        if(chk_array($this->parametros, 0) == 'adicionar'){
            $this->title = SYS_NAME . ' - Adicionar Usuário';
            
            $conteudo = ABSPATH . '/views/usuarios/form.view.php';
        }
        else
            $conteudo = ABSPATH . '/views/usuarios/usuarios.view.php';

        require ABSPATH . '/views/painel/painel.view.php';

    }
}
?>