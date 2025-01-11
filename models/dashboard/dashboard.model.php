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

	public function getDadosDashboardParceiro($idParceiro = 0, $ano = 0)
	{
		$where = " WHERE 1=1 ";

		if ($idParceiro > 0) {
			$where .= " AND idParceiro = " . $idParceiro . " ";
		}



		// Primeiro busca os anos disponíveis
		$queryAnos = $this->db->query("
            SELECT DISTINCT YEAR(dataCadastro) as ano
            FROM tblImpressoes
            $where
            ORDER BY ano DESC
        ");
		$anos = $queryAnos->fetchAll(PDO::FETCH_COLUMN);

		// Pega o primeiro ano da lista (mais recente)
		$anoSelecionado = $anos[0];

		if ($ano > 0) {
			$anoSelecionado = $ano;
		}

		$where .= " AND YEAR(dataCadastro) = " . $anoSelecionado;

		// Páginas por Usuário
		$queryPaginasUsuario = $this->db->query("
            SELECT 
                nomeUsuario as usuario,
                SUM(paginas) as total
            FROM tblImpressoes
            $where
            GROUP BY nomeUsuario
            ORDER BY total DESC
            LIMIT 15
        ");
		$paginasUsuario = $queryPaginasUsuario->fetchAll(PDO::FETCH_ASSOC);

		// Páginas por Mês (últimos 12 meses)
		$queryPaginasMes = $this->db->query("
            SELECT 
                DATE_FORMAT(dataCadastro, '%b') as mes,
                SUM(paginas) as total
            FROM tblImpressoes
            $where
            AND dataCadastro >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY MONTH(dataCadastro), YEAR(dataCadastro)
            ORDER BY YEAR(dataCadastro) DESC, MONTH(dataCadastro) DESC
            LIMIT 12
        ");
		$paginasMes = $queryPaginasMes->fetchAll(PDO::FETCH_ASSOC);

		// Páginas por Impressora
		$queryPaginasImpressora = $this->db->query("
            SELECT 
                nomeImpressora as impressora,
                SUM(paginas) as total,
                COUNT(*) as documentos
            FROM tblImpressoes
            $where
            GROUP BY nomeImpressora
            ORDER BY total DESC
        ");
		$paginasImpressora = $queryPaginasImpressora->fetchAll(PDO::FETCH_ASSOC);

		// Grayscale vs Colorido
		$queryGrayscale = $this->db->query("
            SELECT 
                monocromatico,
                COUNT(*) as total,
                SUM(paginas) as paginas
            FROM tblImpressoes
            $where
            GROUP BY monocromatico
        ");
		$grayscale = $queryGrayscale->fetchAll(PDO::FETCH_ASSOC);

		// Duplex vs Simplex
		$queryDuplex = $this->db->query("
            SELECT 
                duplex,
                COUNT(*) as total,
                SUM(paginas) as paginas
            FROM tblImpressoes
            $where
            GROUP BY duplex
        ");
		$duplex = $queryDuplex->fetchAll(PDO::FETCH_ASSOC);

		// Documentos Recentes
		$queryDocumentos = $this->db->query("
            SELECT 
                DATE_FORMAT(dataCadastro, '%d/%m/%Y %H:%i:%s') as data,
                nomeUsuario as usuario,
                nomeArquivo as documento,
                nomeImpressora as impressora,
                paginas,
                duplex,
                monocromatico,
                custoTotal
            FROM tblImpressoes
            $where
            ORDER BY dataCadastro DESC
            LIMIT 50
        ");
		$documentos = $queryDocumentos->fetchAll(PDO::FETCH_ASSOC);

		// Calcula percentuais para gráficos com validação
		$totalPaginasImpressora = array_sum(array_column($paginasImpressora, 'total'));
		foreach ($paginasImpressora as &$impressora) {
			$impressora['percentual'] = $totalPaginasImpressora > 0 ?
				round(($impressora['total'] / $totalPaginasImpressora) * 100, 2) : 0;
		}

		// Processa dados de grayscale com validação
		$grayscaleData = [
			'GRAYSCALE' => array_sum(array_column(array_filter($grayscale, function ($item) {
				return $item['monocromatico'] == 'GRAYSCALE';
			}), 'paginas')),
			'NOT GRAYSCALE' => array_sum(array_column(array_filter($grayscale, function ($item) {
				return $item['monocromatico'] == 'NOT GRAYSCALE';
			}), 'paginas'))
		];

		$totalGrayscale = $grayscaleData['GRAYSCALE'] + $grayscaleData['NOT GRAYSCALE'];

		if ($totalGrayscale > 0) {
			$grayscalePercentual = [
				round(($grayscaleData['GRAYSCALE'] / $totalGrayscale) * 100, 2),
				round(($grayscaleData['NOT GRAYSCALE'] / $totalGrayscale) * 100, 2)
			];
		} else {
			$grayscalePercentual = [0, 0];
		}

		// Processa dados de duplex com validação
		$duplexData = [
			'DUPLEX' => array_sum(array_column(array_filter($duplex, function ($item) {
				return $item['duplex'] == 'DUPLEX';
			}), 'paginas')),
			'NOT DUPLEX' => array_sum(array_column(array_filter($duplex, function ($item) {
				return $item['duplex'] == 'NOT DUPLEX';
			}), 'paginas'))
		];

		$totalDuplex = $duplexData['DUPLEX'] + $duplexData['NOT DUPLEX'];

		if ($totalDuplex > 0) {
			$duplexPercentual = [
				round(($duplexData['DUPLEX'] / $totalDuplex) * 100, 2),
				round(($duplexData['NOT DUPLEX'] / $totalDuplex) * 100, 2)
			];
		} else {
			$duplexPercentual = [0, 0];
		}

		// Se não tiver dados para o ano, retorna arrays vazios
		if (empty($paginasUsuario)) {
			return [
				'paginasUsuario' => [
					'labels' => [],
					'data' => []
				],
				'paginasMes' => [
					'labels' => [],
					'data' => []
				],
				'paginasImpressora' => [
					'labels' => [],
					'data' => []
				],
				'grayscale' => [0, 0],
				'duplex' => [0, 0],
				'documentos' => [],
				'anos' => $anos,
				'anoSelecionado' => $anoSelecionado
			];
		}

		return [
			'paginasUsuario' => [
				'labels' => array_column($paginasUsuario, 'usuario'),
				'data' => array_column($paginasUsuario, 'total')
			],
			'paginasMes' => [
				'labels' => array_reverse(array_column($paginasMes, 'mes')),
				'data' => array_reverse(array_column($paginasMes, 'total'))
			],
			'paginasImpressora' => [
				'labels' => array_column($paginasImpressora, 'impressora'),
				'data' => array_column($paginasImpressora, 'percentual')
			],
			'grayscale' => $grayscalePercentual,
			'duplex' => $duplexPercentual,
			'documentos' => $documentos,
			'anos' => $anos,
			'anoSelecionado' => $anoSelecionado
		];
	}

	public function getAnosDisponiveis($idParceiro = 0)
	{
		$where = " WHERE 1=1 ";
		if ($idParceiro > 0) {
			$where .= " AND idParceiro = " . $idParceiro . " ";
		}

		$queryAnos = $this->db->query("
        SELECT DISTINCT YEAR(dataCadastro) as ano
        FROM tblImpressoes
        $where
        ORDER BY ano DESC
    ");
		return $queryAnos->fetchAll(PDO::FETCH_COLUMN);
	}

	public function getDadosDashboard($idEmpresa = 0)
	{
		if ($idEmpresa == 0) {
			return [
				'totalImpressoes' => 0,
				'totalUsuarios' => 0,
				'totalImpressoras' => 0,
				'impressaoMes' => 0,
				'usuarios' => [
					'labels' => 'nomeUsuario',
					'data' => 'total'
				],
				'impressoras' => [
					'labels' => 'nomeImpressora',
					'data' => 0
				]
			];
		}
		// Primeiro buscamos todos os parceiros relacionados à empresa (incluindo subparceiros)
		$parceirosQuery = "
        WITH RECURSIVE SubParceiros AS (
            -- Seleciona os parceiros diretos da empresa
            SELECT p.id, p.idParceiro
            FROM tblParceiro p
            INNER JOIN relParceiroEmpresa rpe ON p.id = rpe.idParceiro
            WHERE rpe.idEmpresa = ?
            
            UNION ALL
            
            -- Seleciona os subparceiros recursivamente
            SELECT p.id, p.idParceiro
            FROM tblParceiro p
            INNER JOIN SubParceiros sp ON p.idParceiro = sp.id
        )
        SELECT id FROM SubParceiros";

		$parceirosStmt = $this->db->query($parceirosQuery, array($idEmpresa));
		$parceiros = $parceirosStmt->fetchAll(PDO::FETCH_COLUMN);

		// Adiciona o id dos parceiros ao array para usar no IN da query
		$parceirosIds = implode(',', array_filter($parceiros));

		if (empty($parceirosIds)) {
			return [
				'totalImpressoes' => 0,
				'totalUsuarios' => 0,
				'totalImpressoras' => 0,
				'impressaoMes' => 0,
				'usuarios' => [
					'labels' => [],
					'data' => []
				],
				'impressoras' => [
					'labels' => [],
					'data' => []
				]
			];
		}

		// Total de Impressões
		$queryTotalImpressao = $this->db->query("
        SELECT COUNT(*) as total 
        FROM tblImpressoes 
        WHERE idParceiro IN ($parceirosIds)");
		$totalImpressao = $queryTotalImpressao->fetch()['total'];

		// Total de Usuários Ativos
		$queryTotalUsuarios = $this->db->query("
        SELECT COUNT(DISTINCT nomeUsuario) as total 
        FROM tblImpressoes 
        WHERE idParceiro IN ($parceirosIds)");
		$totalUsuarios = $queryTotalUsuarios->fetch()['total'];

		// Total de Impressoras
		$queryTotalImpressoras = $this->db->query("
        SELECT COUNT(DISTINCT nomeImpressora) as total 
        FROM tblImpressoes 
        WHERE idParceiro IN ($parceirosIds)");
		$totalImpressoras = $queryTotalImpressoras->fetch()['total'];

		// Impressões no Mês Atual
		$queryImpressaoMes = $this->db->query("
        SELECT COUNT(*) as total 
        FROM tblImpressoes 
        WHERE idParceiro IN ($parceirosIds)
        AND MONTH(dataCadastro) = MONTH(CURRENT_DATE()) 
        AND YEAR(dataCadastro) = YEAR(CURRENT_DATE())");
		$impressaoMes = $queryImpressaoMes->fetch()['total'];

		// Impressões por Usuário
		$queryUsuarios = $this->db->query("
        SELECT 
            nomeUsuario, 
            COUNT(*) as total 
        FROM tblImpressoes 
        WHERE idParceiro IN ($parceirosIds)
        GROUP BY nomeUsuario 
        ORDER BY total DESC 
        LIMIT 10");
		$usuarios = $queryUsuarios->fetchAll(PDO::FETCH_ASSOC);

		// Impressões por Impressora
		$queryImpressoras = $this->db->query("
        SELECT 
            nomeImpressora, 
            COUNT(*) as total 
        FROM tblImpressoes 
        WHERE idParceiro IN ($parceirosIds)
        GROUP BY nomeImpressora 
        ORDER BY total DESC");
		$impressoras = $queryImpressoras->fetchAll(PDO::FETCH_ASSOC);

		// Calcula percentuais para os gráficos
		$totalImpressoras = array_sum(array_column($impressoras, 'total'));
		$impressorasData = [];
		foreach ($impressoras as $imp) {
			$impressorasData[] = round(($imp['total'] / $totalImpressoras) * 100, 2);
		}

		return [
			'totalImpressoes' => $totalImpressao,
			'totalUsuarios' => $totalUsuarios,
			'totalImpressoras' => $totalImpressoras,
			'impressaoMes' => $impressaoMes,
			'usuarios' => [
				'labels' => array_column($usuarios, 'nomeUsuario'),
				'data' => array_column($usuarios, 'total')
			],
			'impressoras' => [
				'labels' => array_column($impressoras, 'nomeImpressora'),
				'data' => $impressorasData
			]
		];
	}
}
