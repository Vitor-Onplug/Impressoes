<?php
class RelatoriosModel extends MainModel
{
    public $form_data;
    public $form_msg;
    public $db;

    private $id;

    private $erro;

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        $this->controller = $controller;
        $this->parametros = $this->controller->parametros;
        $this->userdata = $this->controller->userdata;
    }

    public function getRelatorioPorUsuario()
    {
        // Query para obter o total de impressões por usuário
        $query = $this->db->query("
        SELECT nomeUsuario AS usuario, COUNT(DISTINCT id) AS total
        FROM tblImpressoes
        GROUP BY nomeUsuario
        ORDER BY total DESC;

    ");

        // Retorna os dados
        if ($query) {
            return $query->fetchAll();
        }

        return [];
    }

    public function getRelatorioPorImpressora()
    {
        // Query para obter o total de impressões por impressora
        $query = $this->db->query("
        SELECT nomeImpressora AS impressora, COUNT(DISTINCT id) AS total
        FROM tblImpressoes
        GROUP BY nomeImpressora
        ORDER BY total DESC
    ");

        // Retorna os dados
        if ($query) {
            return $query->fetchAll();
        }

        return [];
    }

    public function getRelatorioPorEstacao()
    {
        // Query para obter o total de impressões por estação (cliente)
        $query = $this->db->query("
        SELECT cliente AS estacao, COUNT(DISTINCT id) AS total
        FROM tblImpressoes
        GROUP BY cliente
        ORDER BY total DESC
    ");

        // Retorna os dados
        if ($query) {
            return $query->fetchAll();
        }

        return [];
    }
    
    public function getRelatorioPorDia($mes = null)
    {
        // Se não for passado um mês, pega o atual
        if (!$mes) {
            $mes = date('Y-m');
        }

        // Query para obter o total de impressões por dia
        $query = $this->db->query("
            SELECT DATE_FORMAT(dataCadastro, '%d/%b') AS dia, COUNT(id) AS total
            FROM tblImpressoes
            WHERE DATE_FORMAT(dataCadastro, '%Y-%m') = ?
            GROUP BY dia
            ORDER BY DATE(dataCadastro) ASC
        ", [$mes]);

        // Retorna os dados
        if ($query) {
            return $query->fetchAll();
        }

        return [];
    }
}
