<?php
class RelatoriosController extends MainController
{
    public $login_required = true;

    public function index()
    {
        $this->title = SYS_NAME . ' - Relatórios';

        if (!$this->logged_in) {
            $this->logout();
            $this->goto_login();
            return;
        }

        $parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();

        $modelo = $this->load_model('relatorios/relatorios');

        // Rota para relatório por usuário
        if (chk_array($this->parametros, 0) == 'porUsuario') {
            $this->title = SYS_NAME . ' - Relatório por Usuário';

            $conteudo = ABSPATH . '/views/relatorios/porUsuario.view.php';

            require ABSPATH . '/views/painel/painel.view.php';
        } 
        // Rota para relatório por impressora
        elseif (chk_array($this->parametros, 0) == 'porImpressora') {
            $this->title = SYS_NAME . ' - Relatório por Impressora';

            $conteudo = ABSPATH . '/views/relatorios/porImpressora.view.php';

            require ABSPATH . '/views/painel/painel.view.php';
        } 
        // Rota para relatório por estação
        elseif (chk_array($this->parametros, 0) == 'porEstacao') {
            $this->title = SYS_NAME . ' - Relatório por Estação';

            $conteudo = ABSPATH . '/views/relatorios/porEstacao.view.php';

            require ABSPATH . '/views/painel/painel.view.php';
        } 
        // Rota para relatório por dia
        elseif (chk_array($this->parametros, 0) == 'porMes') {
            $this->title = SYS_NAME . ' - Relatório por Mês';

            $conteudo = ABSPATH . '/views/relatorios/porMes.view.php';

            require ABSPATH . '/views/painel/painel.view.php';
        } 
        // Rota para exportação de relatórios (caso necessário)
        elseif (chk_array($this->parametros, 0) == 'exportar') {
            $response = $modelo->exportarRelatorios();
            echo json_encode($response);
        } 
        // Rota padrão (exibe uma visão geral dos relatórios)
        else {
            $conteudo = ABSPATH . '/views/relatorios/relatorios.view.php';

            require ABSPATH . '/views/painel/painel.view.php';
        }
    }
}
