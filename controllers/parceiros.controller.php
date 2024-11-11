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

		if(chk_array($this->parametros, 0) == 'adicionar'){
			$this->title = SYS_NAME . ' - Adicionar Parceiro';
			
			$conteudo = ABSPATH . '/views/parceiros/frmParceiro.inc.view.php';
		}elseif(chk_array($this->parametros, 0) == 'editar'){
			$this->title = SYS_NAME . ' - Editar Parceiro';
			
			$conteudo = ABSPATH . '/views/parceiros/frmParceiro.inc.view.php';
		}else{
			$conteudo = ABSPATH . '/views/parceiros/parceiros.view.php';
		}
		
		require ABSPATH . '/views/painel/painel.view.php';

    }
}
