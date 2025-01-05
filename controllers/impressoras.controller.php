<?php
class ImpressorasController extends MainController
{
    public $login_required = true;

    public function index()
    {
        $this->title = SYS_NAME . ' - Impressoras';

        if (!$this->logged_in) {
            $this->logout();

            $this->goto_login();

            return;
        }

        $parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();

        $modelo = $this->load_model('impressoras/impressoras');

        if (chk_array($this->parametros, 0) == 'adicionar') {
            $this->title = SYS_NAME . ' - Adicionar Impressora';

            $conteudo = ABSPATH . '/views/impressoras/adicionar_editar.view.php';

            require ABSPATH . '/views/painel/painel.view.php';
        } elseif (chk_array($this->parametros, 0) == 'editar') {
            $this->title = SYS_NAME . ' - Editar Impressora';

            $conteudo = ABSPATH . '/views/impressoras/adicionar_editar.view.php';

            require ABSPATH . '/views/painel/painel.view.php';
        } 
        
        elseif (chk_array($this->parametros, 0) == 'adicionarMarca') {
            $modelo->adicionarMarca();
        } 
        elseif (chk_array($this->parametros, 0) == 'adicionarDepartamento') {
            $modelo->adicionarDepartamento();
        } 
        elseif (chk_array($this->parametros, 0) == 'getMarcas') {
            $response = $modelo->getMarcas();
            echo json_encode($response);
        }
        elseif (chk_array($this->parametros, 0) == 'getDepartamentos') {
            $response = $modelo->getDepartamentos();
            echo json_encode($response);
        }
        
        else {
            $conteudo = ABSPATH . '/views/impressoras/impressoras.view.php';

            require ABSPATH . '/views/painel/painel.view.php';
        }
    }
}
