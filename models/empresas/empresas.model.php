<?php
class EmpresasModel extends MainModel
{
	public $form_data;
	public $form_msg;
	public $db;

	private $id;
	private $razaoSocial;
	private $nomeFantasia;
	private $observacoes;
	private $avatar;
	private $token;
	private $accessToken;
	private $idUsuarioCriacao;

	private $erro;

	private $ClasseEmpresa;

	public function __construct($db = false, $controller = null)
	{
		$this->db = $db;

		$this->controller = $controller;

		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;

		$this->ClasseEmpresa = new Empresa($this->db);
	}

	public function validarFormEmpresa()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			return;
		}

		$this->form_data = array();

		$this->razaoSocial = isset($_POST["razaoSocial"]) ? $_POST["razaoSocial"] : null;
		$this->nomeFantasia = isset($_POST["nomeFantasia"]) ? $_POST["nomeFantasia"] : null;
		$this->observacoes = isset($_POST["observacoes"]) ? $_POST["observacoes"] : null;
		$this->avatar = isset($_POST["diretorioAvatar"]) ? $_POST["diretorioAvatar"] : null;
		$this->token = isset($_POST["token"]) ? $_POST["token"] : null;
		$this->accessToken = isset($_POST["accessToken"]) ? $_POST["accessToken"] : null;
		$this->idUsuarioCriacao = isset($_POST["idUsuarioCriacao"]) ? $_POST["idUsuarioCriacao"] : null;

		$validaEmpresa = $this->ClasseEmpresa->validarEmpresa($this->razaoSocial, $this->nomeFantasia, $this->observacoes, $this->idUsuarioCriacao);

		$this->form_data['razaoSocial'] = trim($this->razaoSocial);
		$this->form_data['nomeFantasia'] = trim($this->nomeFantasia);
		$this->form_data['observacoes'] = trim($this->observacoes);
		$this->form_data['avatar'] = trim($this->avatar);
		$this->form_data['token'] = trim($this->token);
		$this->form_data['accessToken'] = trim($this->accessToken);
		$this->form_data['idUsuarioCriacao'] = trim($this->idUsuarioCriacao);

		if ($validaEmpresa != 1) {
			$this->erro .= $validaEmpresa;
		}

		if (!empty($this->erro)) {
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
			return;
		}

		if (empty($this->form_data)) {
			return;
		}

		if (chk_array($this->parametros, 0) == 'editar') {
			$this->editarEmpresa();
			return;
		} else {
			$this->adicionarEmpresa();
			return;
		}
	}

	private function editarEmpresa()
	{
		if (chk_array($this->parametros, 1)) {
			$hash = chk_array($this->parametros, 1);
			$this->id = decryptHash($hash);
		}

		$editaEmpresa = $this->ClasseEmpresa->editarEmpresa($this->id, chk_array($this->form_data, 'razaoSocial'), chk_array($this->form_data, 'nomeFantasia'), chk_array($this->form_data, 'observacoes'));

		if (!$editaEmpresa) {
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		} else {

			$this->db->query('DELETE FROM `tblTokens` WHERE idEmpresa = ? ',  array($this->id));

			$this->db->insert(
				'tblTokens',
				array(
					'idEmpresa' => $this->id,
					'token' => chk_array($this->form_data, 'token'),
					'type' => 'CADASTRO'
				)
			);

			$this->db->insert(
				'tblTokens',
				array(
					'idEmpresa' => $this->id,
					'token' => chk_array($this->form_data, 'accessToken'),
					'type' => 'ACESSO'
				)
			);

			$this->form_msg = $this->controller->Messages->success('Registro editado com sucesso. Aguarde, você será redirecionado...');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/empresas/index/editar/' . chk_array($this->parametros, 1) . '">';
		}

		return;
	}

	private function adicionarEmpresa()
	{
		$insereEmpresa = $this->ClasseEmpresa->adicionarEmpresa(chk_array($this->form_data, 'razaoSocial'), chk_array($this->form_data, 'nomeFantasia'), chk_array($this->form_data, 'observacoes'), chk_array($this->form_data, 'idUsuarioCriacao'));
		$this->id = $this->db->lastInsertId();

		$destinoAvatar = "midia/avatar/" .  date("Y") . "/" . date("m") . "/" . date("d") . "/" . $this->id;
		rcopy(chk_array($this->form_data, 'avatar'), $destinoAvatar);
		rrmdir(chk_array($this->form_data, 'avatar'));

		if (!$insereEmpresa) {
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		} else {

			$this->db->insert(
				'tblTokens',
				array(
					'idEmpresa' => $this->id,
					'token' => chk_array($this->form_data, 'token'),
					'type' => 'CADASTRO'
				)
			);

			$this->db->insert(
				'tblTokens',
				array(
					'idEmpresa' => $this->id,
					'token' => chk_array($this->form_data, 'accessToken'),
					'type' => 'ACESSO'
				)
			);


			$this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso. Aguarde, você será redirecionado...');
			$hash = encryptId($this->id);
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/empresas/index/editar/' . $hash . '">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/empresas/index/editar/' . $hash . '";</script>';

			$this->form_data = null;
		}

		return;
	}

	public function getEmpresa($id = false)
	{
		if (empty($id)) {
			return;
		}

		$registro = $this->ClasseEmpresa->getEmpresa($id);

		if (empty($registro)) {
			$this->form_msg = $this->controller->Messages->error('Registro inexistente.');
			return;
		}

		foreach ($registro as $key => $value) {
			$this->form_data[$key] = $value;
		}

		return;
	}

	public function getEmpresas($filtros = null)
	{
		return $this->ClasseEmpresa->getEmpresas($filtros);
	}

	public function getFuncionariosEmpresa($idEmpresa)
	{
		$query = $this->db->query('SELECT tblPessoa.*, tblUsuario.idPermissao, tblUsuario.idEmpresa
		FROM tblPessoa 
		INNER JOIN tblUsuario ON tblPessoa.id = tblUsuario.idPessoa
		WHERE tblPessoa.status = "T" AND idEmpresa = ? ', array($idEmpresa));

		if (!$query) {
			return false;
		}

		return $query->fetchAll();
	}

	public function getAvatar($id = null, $tn = false)
	{
		try {
			// Validar ID
			if (!is_numeric($id) || $id <= 0) {
				return "midia/empresas/noPictureProfile.png";
			}

			// Como já estamos no modelo pessoa, podemos usar direto
			$this->getEmpresa($id);

			if (!isset($this->form_data['razaoSocial'])) {
				return "midia/empresas/noPictureProfile.png";
			}

			$diretorio = UP_ABSPATH . '/empresas/' . $id . '-' . $this->form_data['razaoSocial'] . '/imagens/avatar';

			if (!is_dir($diretorio)) {
				return "midia/empresas/noPictureProfile.png";
			}

			// Lista todos os arquivos do diretório
			$arquivos = scandir($diretorio);
			$arquivos = array_diff($arquivos, array('.', '..'));

			// Filtrar apenas imagens
			$imagens = array_filter($arquivos, function ($arquivo) {
				return preg_match("/\.(jpg|jpeg|gif|png)$/i", strtolower($arquivo));
			});

			if (empty($imagens)) {
				return "midia/empresas/noPictureProfile.png";
			}

			// Ordena os arquivos pelo nome em ordem decrescente (baseado no timestamp no nome)
			usort($imagens, function ($a, $b) {
				return strcmp($b, $a); // Ordenação reversa para pegar o mais recente
			});

			// Pega a imagem mais recente
			$imagemRecente = array_shift($imagens);

			// Retorna o caminho conforme solicitado (normal ou thumbnail)
			if ($tn) {
				$caminhoThumb = $diretorio . '/thumb/' . $imagemRecente;
				if (file_exists($caminhoThumb)) {
					return str_replace(ABSPATH, '', $caminhoThumb);
				}
			}

			$responseImage = str_replace(ABSPATH, '', $diretorio . '/' . $imagemRecente);

			if(!file_exists(ABSPATH . $responseImage)){
				return "midia/empresas/noPictureProfile.png";
			}

			return $responseImage;
		} catch (Exception $e) {
			return "midia/empresas/noPictureProfile.png";
		}
	}

	public function bloquearEmpresa()
	{
		$id = null;

		if (chk_array($this->parametros, 1)) {
			$hash = chk_array($this->parametros, 1);
			$id = decryptHash($hash);
		}

		if (!empty($id)) {
			$id = (int) $id;

			$query = $this->db->update('tblEmpresa', 'id', $id, array('status' => 'F'));

			$this->form_msg = $this->controller->Messages->success('Registro bloqueado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/empresas/">';

			return;
		}
	}

	public function desbloquearEmpresa()
	{
		$id = null;

		if (chk_array($this->parametros, 1)) {
			$hash = chk_array($this->parametros, 1);
			$id = decryptHash($hash);
		}

		if (!empty($id)) {
			$id = (int) $id;

			$query = $this->db->update('tblEmpresa', 'id', $id, array('status' => 'T'));

			$this->form_msg = $this->controller->Messages->success('Registro desbloqueado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/empresas/">';

			return;
		}
	}

	public function buscarEmpresas($termo = null)
	{
		if (strlen($termo) >= 3) {
			$query = $this->db->query('
            SELECT 
                `tblEmpresa`.`id`,
                `tblEmpresa`.`razaoSocial`,
				`tblDocumento`.`documento` as documento
            FROM `tblEmpresa`
			LEFT JOIN `tblDocumento` ON `tblDocumento`.`idEmpresa` = `tblEmpresa`.`id`
            WHERE (`tblEmpresa`.`razaoSocial` LIKE ? OR `tblEmpresa`.`nomeFantasia` LIKE ? OR `tblDocumento`.`documento` LIKE ?)
            AND `tblEmpresa`.`status` = 1
            ORDER BY `tblEmpresa`.`razaoSocial`
            LIMIT 30
        ', array('%' . $termo . '%', '%' . $termo . '%', '%' . $termo . '%'));

			if (!$query) {
				return array('items' => array(), 'total_count' => 0);
			}

			$resultados = $query->fetchAll();

			// Formata os resultados para o select2
			$items = array_map(function ($empresa) {
				return array(
					'id' => $empresa['id'],
					'text' => $empresa['razaoSocial'], // Necessário para o select2
					'razaoSocial' => $empresa['razaoSocial'],
					'documento' => $empresa['documento']
				);
			}, $resultados);

			return array(
				'items' => $items,
				'total_count' => count($items)
			);
		}

		return array('items' => array(), 'total_count' => 0);
	}

	public function validarTokenEmpresa($token, $tipo)
	{
		$query = $this->db->query('SELECT `idEmpresa` 
                                    FROM `tblTokens` 
                                    WHERE `token` = ?
									AND `type` = ?
									', array($token, $tipo));
		$registro = $query->fetch();
		// Verifica se há registros retornados
		if ($query->rowCount() > 0) {
			return $registro['idEmpresa']; // Token válido
		}

		return false; // Token inválido
	}

}
