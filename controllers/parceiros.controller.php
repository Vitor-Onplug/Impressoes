<?php
class ParceirosController extends MainController
{

    public $login_required = true;

    public function index()
    {
        $this->title = SYS_NAME . ' - Parceiros';

        if (!$this->logged_in) {
            $this->logout();

            $this->goto_login();

            return;
        }

        $parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();

        $modelo = $this->load_model('parceiros/parceiros');
        $modeloEmpresa = $this->load_model('empresas/empresas');

        if (chk_array($this->parametros, 0) == 'adicionar') {
            $this->title = SYS_NAME . ' - Adicionar Parceiro';

            $conteudo = ABSPATH . '/views/parceiros/frmParceiro.inc.view.php';
        } elseif (chk_array($this->parametros, 0) == 'editar') {
            $this->title = SYS_NAME . ' - Editar Parceiro';

            $conteudo = ABSPATH . '/views/parceiros/frmParceiro.inc.view.php';
        } elseif (chk_array($this->parametros, 0) == 'ver') {
            $this->title = SYS_NAME . ' - Visualizar Parceiro';

            $conteudo = ABSPATH . '/views/parceiros/frmParceiro.inc.view.php';
        } elseif (chk_array($this->parametros, 0) == 'setParceiro') {
            $idPost = isset($_POST['idParceiro']) ? $_POST['idParceiro'] : 0;
            $hash = encryptId($idPost);
            if ($idPost == 0) {
                $hash = 0;
            }
            if ($_SESSION['idParceiroHash'] != $hash) {
                $_SESSION['idParceiroHash'] = $hash;
            }

            $this->goto_page('/');
            return;
        } else {
            $conteudo = ABSPATH . '/views/parceiros/parceiros.view.php';
        }

        require ABSPATH . '/views/painel/painel.view.php';
    }
}
