<?php
class DashboardModel extends MainModel
{
	public $form_data;
	public $form_msg;
	public $db;

	private $erro;

	public function __construct($db = false, $controller = null)
	{
		$this->db = $db;

		$this->controller = $controller;

		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
	}

	public function getDadosDashboard($idParceiro = 0)
	{
		// Total de Impressões
		$queryTotalImpressao = $this->db->query("SELECT COUNT(*) as total FROM tblImpressoes WHERE idParceiro = ?", array($idParceiro));
		$totalImpressao = $queryTotalImpressao->fetch()['total'];

		// Total de Usuários
		$queryTotalUsuarios = $this->db->query("SELECT COUNT(DISTINCT nomeUsuario) as total FROM tblImpressoes WHERE idParceiro = ?", array($idParceiro));
		$totalUsuarios = $queryTotalUsuarios->fetch()['total'];

		// Total de Impressoras
		$queryTotalImpressoras = $this->db->query("SELECT COUNT(DISTINCT nomeImpressora) as total FROM tblImpressoes WHERE idParceiro = ?", array($idParceiro));
		$totalImpressoras = $queryTotalImpressoras->fetch()['total'];

		// Impressões no Mês Atual
		$queryImpressaoMes = $this->db->query("
        SELECT COUNT(*) as total FROM tblImpressoes 
        WHERE MONTH(dataCadastro) = MONTH(CURRENT_DATE()) 
        AND YEAR(dataCadastro) = YEAR(CURRENT_DATE())
		AND idParceiro = ?", array($idParceiro));

		$impressaoMes = $queryImpressaoMes->fetch()['total'];

		$where = " WHERE tblImpressoes.idParceiro = " . $idParceiro . " ";

		// Impressões por Usuário
		$queryUsuarios = $this->db->query("
        SELECT nomeUsuario as usuario, COUNT(*) as total 
        FROM tblImpressoes 
		$where
        GROUP BY nomeUsuario 
        ORDER BY total DESC
        LIMIT 10
    ");
		$usuarios = $queryUsuarios->fetchAll(PDO::FETCH_ASSOC);

		// Impressões por Impressora
		$queryImpressoras = $this->db->query("
        SELECT nomeImpressora as impressora, COUNT(*) as total 
        FROM tblImpressoes 
		$where
        GROUP BY nomeImpressora 
        ORDER BY total DESC
        LIMIT 10
    ");
		$impressoras = $queryImpressoras->fetchAll(PDO::FETCH_ASSOC);

		return [
			'totalImpressoes' => $totalImpressao,
			'totalUsuarios' => $totalUsuarios,
			'totalImpressoras' => $totalImpressoras,
			'impressaoMes' => $impressaoMes,
			'usuarios' => [
				'labels' => array_column($usuarios, 'usuario'),
				'data' => array_column($usuarios, 'total')
			],
			'impressoras' => [
				'labels' => array_column($impressoras, 'impressora'),
				'data' => array_column($impressoras, 'total')
			]
		];
	}
}
