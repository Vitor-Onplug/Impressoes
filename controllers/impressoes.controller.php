<?php
class ImpressoesController extends MainController
{
    public $login_required = true;

    public function index()
    {
        $this->title = SYS_NAME . ' - Impressoes';

        if (!$this->logged_in) {
            $this->logout();

            $this->goto_login();

            return;
        }

        $parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();

        $modelo = $this->load_model('impressoes/impressoes');


        if (chk_array($this->parametros, 0) == '/api/cadastrarImpressao') {

            // Exemplo de dados recebidos via POST
            $dadosRecebidos = $_POST['dadosImpressao'] ?? []; // Supondo que os dados venham como array

            if (!empty($dadosRecebidos)) {
                $modelo->salvarDadosImpressao($dadosRecebidos);
                echo json_encode(['success' => true, 'message' => 'Dados salvos com sucesso.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Nenhum dado recebido.']);
            }
        } else {
            $conteudo = ABSPATH . '/views/impressoes/impressoes.view.php';

            require ABSPATH . '/views/painel/painel.view.php';
        }
    }

}
