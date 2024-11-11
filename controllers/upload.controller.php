<<?php
class UploadController extends MainController
{

    public $login_required = true;

    public function index()
    {
        $this->title = SYS_NAME . ' - Upload';

        if (!$this->logged_in) {
            $this->logout();

            $this->goto_login();

            return;
        }

        $parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();

        $uploadModelPessoa = $this->load_model('pessoas/upload');
        $uploadModelEmpresa = $this->load_model('empresas/upload');

    }
}
