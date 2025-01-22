<?php
header('Content-Type: application/json');

class ApiinternaController extends MainController
{
    public $login_required = true;
    private $modeloEmpresas;
    private $modeloParceiros;

    public function __construct()
    {
        // if (!$this->logged_in) {
        //     echo json_encode(array('status' => 'error', 'message' => 'Usuário não autenticado'));
        //     return;
        // }
        $this->db = new tix1DB();
        $this->modeloParceiros = $this->load_model('parceiros/parceiros');
        $this->modeloEmpresas = $this->load_model('empresas/empresas');
        $parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();
    }

    public function buscarEmpresas()
    {
        $parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();
        // Pega o termo da requisição POST
        $termo = $_GET['term'];
        $termo = filter_var($termo, FILTER_DEFAULT);
        $termo = strip_tags(trim($termo));

        // Busca as empresas usando o termo
        $empresas = $this->modeloEmpresas->buscarEmpresas($termo);

        // Retorna o resultado em JSON
        header('Content-Type: application/json');
        echo json_encode($empresas);
    }
}
