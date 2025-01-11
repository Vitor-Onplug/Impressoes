<?php
class ConfiguracoesController extends MainController
{
    public $login_required = true;


    public function index()
    {
        $this->title = SYS_NAME . ' - Configurações';

        if (!$this->logged_in) {
            $this->logout();

            $this->goto_login();

            return;
        }

        $parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();
    }

    public function permissoes()
    {
        $this->title = SYS_NAME . ' - Permissões';

        if (!$this->logged_in) {
            $this->logout();

            $this->goto_login();

            return;
        }

        $parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();

        $modelo = $this->load_model('permissoes/permissoes');

        global $permissions;

        if (!$this->check_permissions('CONFIGURACOES', $this->userdata['modulo'])) {
            require_once ABSPATH . '/views/permission.view.php';

            return;
        }

        if (chk_array($this->parametros, 0) == 'adicionar') {
            $this->title = SYS_NAME . ' - Adicionar Permissão';

            $conteudo = ABSPATH . '/views/configuracoes/permissoes/frmPermissao.inc.view.php';
        } elseif (chk_array($this->parametros, 0) == 'editar') {
            $this->title = SYS_NAME . ' - Editar Permissão';

            $conteudo = ABSPATH . '/views/configuracoes/permissoes/frmPermissao.inc.view.php';
        } else {
            $conteudo = ABSPATH . '/views/configuracoes/permissoes/permissoes.view.php';
        }

        require ABSPATH . '/views/painel/painel.view.php';
    }
}
