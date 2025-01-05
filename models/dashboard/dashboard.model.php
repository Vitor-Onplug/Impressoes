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

	public function getDadosDashboard($idUsuario = null)
	{
		$whereClauses = array();
		$params = array();
	
		// Base condition
		$whereClauses[] = "1=1";
	
		// Get User Data
		if (!empty($idUsuario)) {
			$queryUser = $this->db->query("SELECT * FROM vwUsuarios WHERE idUsuario = $idUsuario");
			$userData = $queryUser->fetch();
	
			if ($userData && $userData['idPermissao'] !== 1) {
				$idEmpresa = $userData['idEmpresa'];
	
				// Fetch parceiros related to the user's company
				$queryParceiro = $this->db->query("SELECT id FROM tblParceiro WHERE idEmpresa = $idEmpresa");
				$parceiros = $queryParceiro->fetchAll(PDO::FETCH_COLUMN, 0);
	
				if (!empty($parceiros)) {
					$placeholders = implode(',', $parceiros);
					$whereClauses[] = "tblImpressoes.idParceiro IN ($placeholders)";
				} else {
					return [
						'totalImpressoes' => 0,
						'totalUsuarios' => 0,
						'totalImpressoras' => 0,
						'impressaoMes' => 0,
						'usuarios' => ['labels' => [], 'data' => []],
						'impressoras' => ['labels' => [], 'data' => []],
					];
				}
			}
		}
	
		// Combine WHERE clauses
		$where = implode(' AND ', $whereClauses);
	
		// Total de Impressões
		$queryTotalImpressao = $this->db->query("SELECT COUNT(*) as total FROM tblImpressoes WHERE $where");
		$totalImpressao = $queryTotalImpressao->fetch()['total'];
	
		// Total de Usuários
		$queryTotalUsuarios = $this->db->query("SELECT COUNT(DISTINCT nomeUsuario) as total FROM tblImpressoes WHERE $where");
		$totalUsuarios = $queryTotalUsuarios->fetch()['total'];
	
		// Total de Impressoras
		$queryTotalImpressoras = $this->db->query("SELECT COUNT(DISTINCT nomeImpressora) as total FROM tblImpressoes WHERE $where");
		$totalImpressoras = $queryTotalImpressoras->fetch()['total'];
	
		// Impressões no Mês Atual
		$queryImpressaoMes = $this->db->query("
			SELECT COUNT(*) as total FROM tblImpressoes 
			WHERE MONTH(dataCadastro) = MONTH(CURRENT_DATE()) 
			AND YEAR(dataCadastro) = YEAR(CURRENT_DATE())
			AND $where
		");
		$impressaoMes = $queryImpressaoMes->fetch()['total'];
	
		// Impressões por Usuário
		$queryUsuarios = $this->db->query("
			SELECT nomeUsuario as usuario, COUNT(*) as total 
			FROM tblImpressoes 
			WHERE $where
			GROUP BY nomeUsuario 
			ORDER BY total DESC
			LIMIT 10
		");
		$usuarios = $queryUsuarios->fetchAll(PDO::FETCH_ASSOC);
	
		// Impressões por Impressora
		$queryImpressoras = $this->db->query("
			SELECT nomeImpressora as impressora, COUNT(*) as total 
			FROM tblImpressoes 
			WHERE $where
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
