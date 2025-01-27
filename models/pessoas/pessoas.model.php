<?php
class PessoasModel extends MainModel
{
	public $form_data;
	public $form_msg;
	public $db;

	private $id;
	private $nome;
	private $sobrenome;
	private $apelido;
	private $genero;
	private $dataNascimento;
	private $observacoes;
	private $avatar;


	private $erro;

	private $ClassePessoa;

	public function __construct($db = false, $controller = null)
	{
		$this->db = $db;

		$this->controller = $controller;

		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;

		$this->ClassePessoa = new Pessoa($this->db);
	}

	public function validarFormPessoa()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			return;
		}

		$this->form_data = array();

		$this->nome = isset($_POST["nome"]) ? $_POST["nome"] : null;
		$this->sobrenome = isset($_POST["sobrenome"]) ? $_POST["sobrenome"] : null;
		$this->apelido = isset($_POST["apelido"]) ? $_POST["apelido"] : null;
		$this->genero = isset($_POST["genero"]) ? $_POST["genero"] : null;
		$this->dataNascimento = isset($_POST["dataNascimento"]) ? $_POST["dataNascimento"] : null;
		$this->avatar = isset($_POST["diretorioAvatar"]) ? $_POST["diretorioAvatar"] : null;
		$this->observacoes = isset($_POST["observacoes"]) ? $_POST["observacoes"] : null;


		$validaPessoa = $this->ClassePessoa->validarPessoa($this->nome, $this->sobrenome, $this->apelido, $this->genero, $this->dataNascimento, $this->observacoes);

		$this->form_data['nome'] = trim($this->nome);
		$this->form_data['sobrenome'] = trim($this->sobrenome);
		$this->form_data['apelido'] = trim($this->apelido);
		$this->form_data['genero'] = trim($this->genero);
		$this->form_data['dataNascimento'] = trim($this->dataNascimento);
		$this->form_data['observacoes'] = trim($this->observacoes);
		$this->form_data['avatar'] = trim($this->avatar);

		if ($validaPessoa != 1) {
			$this->erro .= $validaPessoa;
		}

		if (!empty($this->erro)) {
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);

			return;
		}

		if (empty($this->form_data)) {
			return;
		}

		if (chk_array($this->parametros, 0) == 'editar') {
			$this->editarPessoa();

			return;
		} else {
			$this->adicionarPessoa();

			return;
		}
	}

	private function editarPessoa()
	{

		$hash = chk_array($this->parametros, 1);
		$this->id = decryptHash($hash);

		$editaPessoa = $this->ClassePessoa->editarPessoa($this->id, chk_array($this->form_data, 'nome'), chk_array($this->form_data, 'sobrenome'), chk_array($this->form_data, 'apelido'), chk_array($this->form_data, 'genero'), chk_array($this->form_data, 'dataNascimento'), chk_array($this->form_data, 'observacoes'));

		if (!$editaPessoa) {
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		} else {
			$this->form_msg = $this->controller->Messages->success('Registro editado com sucesso. Aguarde, você será redirecionado...');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/pessoas/index/editar/' . chk_array($this->parametros, 1) . '">';
		}

		return;
	}

	private function adicionarPessoa()
	{
		$inserePessoa = $this->ClassePessoa->adicionarPessoa(chk_array($this->form_data, 'nome'), chk_array($this->form_data, 'sobrenome'), chk_array($this->form_data, 'apelido'), chk_array($this->form_data, 'genero'), chk_array($this->form_data, 'dataNascimento'), chk_array($this->form_data, 'observacoes'));
		$this->id = $this->db->lastInsertId();

		$destinoAvatar = "midia/avatar/" .  date("Y") . "/" . date("m") . "/" . date("d") . "/" . $this->id;
		rcopy(chk_array($this->form_data, 'avatar'), $destinoAvatar);
		rrmdir(chk_array($this->form_data, 'avatar'));

		if (!$inserePessoa) {
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		} else {
			$this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso. Aguarde, você será redirecionado...');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/pessoas/index/editar/' . $this->id . '">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/pessoas/index/editar/' . $this->id . '";</script>';

			$this->form_data = null;
		}

		return;
	}

	public function getPessoa($id = false)
	{
		if (empty($id)) {
			return;
		}

		$registro = $this->ClassePessoa->getPessoa($id);

		if (empty($registro)) {
			$this->form_msg = $this->controller->Messages->error('Registro inexistente.');

			return;
		}

		foreach ($registro as $key => $value) {
			$this->form_data[$key] = $value;
		}

		return;
	}

	public function getPessoas($filtros = null)
	{
		return $this->ClassePessoa->getPessoas($filtros);
	}

	public function getAvatar($id = null, $tn = false)
	{
		try {
			// Validar ID
			if (!is_numeric($id) || $id <= 0) {
				return "midia/pessoas/noPictureProfile.png";
			}

			// Como já estamos no modelo pessoa, podemos usar direto
			$this->getPessoa($id);

			if (!isset($this->form_data['nome'])) {
				return "midia/pessoas/noPictureProfile.png";
			}

			$diretorio = UP_ABSPATH . '/pessoas/' . $id . '-' . $this->form_data['nome'] . '/imagens/avatar';

			if (!is_dir($diretorio)) {
				return "midia/pessoas/noPictureProfile.png";
			}

			// Lista todos os arquivos do diretório
			$arquivos = scandir($diretorio);
			$arquivos = array_diff($arquivos, array('.', '..'));

			// Filtrar apenas imagens
			$imagens = array_filter($arquivos, function ($arquivo) {
				return preg_match("/\.(jpg|jpeg|gif|png)$/i", strtolower($arquivo));
			});

			if (empty($imagens)) {
				return "midia/pessoas/noPictureProfile.png";
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
				return "midia/pessoas/noPictureProfile.png";
			}

			return $responseImage;
		} catch (Exception $e) {
			return "midia/pessoas/noPictureProfile.png";
		}
	}


	public function bloquearPessoa()
	{
		$id = null;

		if (chk_array($this->parametros, 1)) {
			$hash = chk_array($this->parametros, 1);
			$id = decryptHash($hash);
		}


		if (!empty($id)) {
			$id = (int) $id;

			$query = $this->db->update('tblPessoa', 'id', $id, array('status' => 'F'));

			$this->form_msg = $this->controller->Messages->success('Registro bloqueado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/pessoas/">';

			return;
		}
	}

	public function desbloquearPessoa()
	{
		$id = null;

		if (chk_array($this->parametros, 1)) {
			$hash = chk_array($this->parametros, 1);
			$id = decryptHash($hash);
		}

		if (!empty($id)) {
			$id = (int) $id;

			$query = $this->db->update('tblPessoa', 'id', $id, array('status' => 'T'));

			$this->form_msg = $this->controller->Messages->success('Registro desbloqueado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/pessoas/">';

			return;
		}
	}
}
