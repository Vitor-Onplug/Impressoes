<?php
class UsuariosModel extends MainModel
{
	public $form_data;
	public $form_msg;
	public $db;

	private $idPessoa;
	private $permissao;
	private $senhaAtual;
	private $senha;
	private $email;
	private $idEmpresa;

	private $ClassePessoa;
	private $ClasseEmail;

	private $erro;

	public function __construct($db = false, $controller = null)
	{
		$this->db = $db;

		$this->controller = $controller;

		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;

		$this->ClassePessoa = new Pessoa($this->db);
		$this->ClasseEmail = new Email($this->db);
	}

	public function validarFormUsuarios()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			return;
		}

		$this->form_data = array();

		$this->idPessoa = !empty(chk_array($this->parametros, 1)) ? (int) chk_array($this->parametros, 1) : null;
		$this->permissao = isset($_POST["permissao"]) ? $_POST["permissao"] : null;
		$this->senha = isset($_POST["senha"]) ? $_POST["senha"] : null;
		$this->idEmpresa = isset($_POST["idEmpresa"]) ? $_POST["idEmpresa"] : null;

		$emails = $this->ClasseEmail->getEmails($this->idPessoa);

		if (count($emails) < 1) {
			$this->erro .= '<br>Não é possível realizar a recuperação de senha, o usuário não possui e-mails cadastrados. Clique <a href="' . HOME_URI . '/pessoas/index/editar/' . chk_array($this->parametros, 1) . '/emails/">aqui</a> para cadastrar.';
		}
		if (empty($this->permissao)) {
			$this->erro .= "<br>Selecione a permissão.";
		}
		if (empty($this->senha)) {
			$this->erro .= "<br>Preencha a senha.";
		}
		if (!empty($this->senha)) {
			if (strlen($this->senha) > 255) {
				$this->erro .= "<br>A senha não pode ultrapassar o limite de 255 caracteres.";
			}
			if ($this->senha != $_POST['repitaSenha']) {
				$this->erro .= "<br>As senhas estão diferentes.";
			}

			if (strlen($_POST['repitaSenha']) > 255) {
				$this->erro .= "<br>O campo de confirmação de senha não pode ultrapassar o limite de 255 caracteres.";
			}

			if (!senhaValida($this->senha)) {
				$this->erro .= "<br>A senha deve ter no mínimo 8 caracteres, incluindo pelo menos uma letra maiúscula, uma letra minúscula e um número.";
			}
		} else {
			$this->senha = null;
		}

		$this->form_data['idPessoa'] = $this->idPessoa;
		$this->form_data['permissao'] = $this->permissao;
		$this->form_data['senha'] = $this->senha;
		$this->form_data['idEmpresa'] = $this->idEmpresa;

		if (!empty($this->erro)) {
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);

			return;
		}

		if (empty($this->form_data)) {
			return;
		}

		if (!empty($this->senha)) {
			$pwHash = new PasswordHash(8, false);

			$this->form_data['senha'] = $pwHash->HashPassword($this->senha);
		}


		$query = $this->db->query('SELECT * FROM `vwUsuarios` WHERE `idPessoa` = ?', array($this->idPessoa));

		$registro = $query->fetch();

		if ($registro != false) {
			$this->editarUsuario();
		} else {
			$this->adicionarUsuario();
		}

		return;
	}

	private function editarUsuario()
	{
		$idPessoa = null;

		if (is_numeric(chk_array($this->parametros, 1))) {
			$idPessoa = chk_array($this->parametros, 1);
		}

		if (!empty(chk_array($this->form_data, 'permissao'))) {
			$query = $this->db->update('tblUsuario', 'idPessoa', $idPessoa, array('idPermissao' => chk_array($this->form_data, 'permissao'), 'senha' => chk_array($this->form_data, 'senha'), 'idEmpresa' => chk_array($this->form_data, 'idEmpresa')));
		} else {
			$query = $this->db->update('tblUsuario', 'idPessoa', $idPessoa, array('senha' => chk_array($this->form_data, 'senha')));
		}

		if (!$query) {
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		} else {
			$this->form_msg = $this->controller->Messages->success('Registro editado com sucesso. Aguarde, você será redirecionado...');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/pessoas/index/usuarios/' . chk_array($this->parametros, 1) . '">';
		}

		return;
	}

	private function adicionarUsuario()
	{
		$query = $this->db->insert('tblUsuario', array('idPessoa' => chk_array($this->form_data, 'idPessoa'), 'idPermissao' => chk_array($this->form_data, 'permissao'), 'senha' => chk_array($this->form_data, 'senha'), 'status' => 'T', 'idEmpresa' => chk_array($this->form_data, 'idEmpresa')));

		if (!$query) {
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		} else {
			$this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso. Aguarde, você será redirecionado...');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/pessoas/index/usuarios/' . chk_array($this->parametros, 1) . '">';

			$this->form_data = null;
		}

		return;
	}

	public function getUsuario($idUsuario = null, $idPessoa = null, $token = null)
	{
		if ($idPessoa > 0) {
			$query = $this->db->query('SELECT * FROM `vwUsuarios` WHERE `idPessoa` = ?', array($idPessoa));
		} elseif ($idUsuario > 0) {
			$query = $this->db->query('SELECT * FROM `vwUsuarios` WHERE `idUsuario` = ?', array($idUsuario));
		} elseif (!empty($token)) {
			$query = $this->db->query('SELECT * FROM `vwUsuarios` WHERE `token` = ?', array($token));
		} else {
			return;
		}

		if (!$query) {
			return false;
		}

		$registro = $query->fetch();

		return $registro;
	}

	public function getUsuarios()
	{
		$query = $this->db->query('SELECT * FROM `vwUsuarios` GROUP BY `idPessoa`');

		if (!$query) {
			return array();
		}

		return $query->fetchAll();
	}

	public function validarFormTrocarMinhaSenha()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			return;
		}

		$this->form_data = array();

		$this->senhaAtual = isset($_POST["senhaAtual"]) ? $_POST["senhaAtual"] : null;
		$this->senha = isset($_POST["senha"]) ? $_POST["senha"] : null;

		if (empty($this->senhaAtual)) {
			$this->erro .= "<br>Preencha a senha atual.";
		}
		if (!empty($this->senhaAtual)) {
			if ($this->senhaAtual != $this->userdata["senha"]) {
				$this->erro .= "<br>A senha atual é inválida.";
			}
		}
		if (empty($this->senha)) {
			$this->erro .= "<br>Preencha a senha.";
		}
		if (!empty($this->senha)) {
			if (strlen($this->senha) > 255) {
				$this->erro .= "<br>A senha não pode ultrapassar o limite de 255 caracteres.";
			}
			if ($this->senha != $_POST['repitaSenha']) {
				$this->erro .= "<br>As senhas estão diferentes.";
			}

			if (strlen($_POST['repitaSenha']) > 255) {
				$this->erro .= "<br>O campo de confirmação de senha não pode ultrapassar o limite de 255 caracteres.";
			}

			if (!senhaValida($this->senha)) {
				$this->erro .= "<br>A senha deve ter no mínimo 8 caracteres, incluindo pelo menos uma letra maiúscula, uma letra minúscula e um número.";
			}
		} else {
			$this->senha = null;
		}

		$this->form_data['senha'] = $this->senha;

		if (!empty($this->erro)) {
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);

			return;
		}

		if (empty($this->form_data)) {
			return;
		}

		if (!empty($this->senha)) {
			$pwHash = new PasswordHash(8, false);

			$this->form_data['senha'] = $pwHash->HashPassword($this->senha);
		}

		$this->parametros[1] = chk_array($this->userdata, 'idUsuario');

		$this->trocarSenha($this->userdata["id"]);

		return;
	}

	public function validarFormRecuperarSenha()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			return;
		}

		$this->form_data = array();

		$this->email = isset($_POST["email"]) ? $_POST["email"] : null;

		if (empty($this->email)) {
			$this->erro .= "<br>Preencha o e-mail.";
		}
		if (!empty($this->email)) {
			$query = $this->db->query('SELECT * FROM `vwUsuarios` WHERE `email` = ?', array($this->email));

			$registro = $query->fetch();
			if (empty($registro)) {
				$this->erro .= "<br>E-mail não encontrado.";
			}
		}

		$this->form_data['idPessoa'] = $registro['idPessoa'];
		$this->form_data['nome'] = $registro['nome'];
		$this->form_data['sobrenome'] = $registro['sobrenome'];
		$this->form_data['apelido'] = $registro['apelido'];
		$this->form_data['email'] = $this->email;

		if (!empty($this->erro)) {
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);

			return;
		}

		if (empty($this->form_data)) {
			return;
		}

		$this->recuperarSenha();

		return;
	}

	private function recuperarSenha()
	{
		$token = sha1(uniqid(time()));

		$queryUsuario = $this->db->update('tblUsuario', 'idPessoa', chk_array($this->form_data, 'idPessoa'), array('token' => $token));

		$assunto = SYS_NAME . ' - Esqueci a Senha';

		$mensagem = "<h1>Redefinição de Senha</h1><br>
					Olá " . chk_array($this->form_data, 'nome') . " " . chk_array($this->form_data, 'sobrenome') . ",
					<br>
					Caso você tenha solicitado a recuperação da sua senha visite o endereço 
					<br>
					<a href='" . HOME_URI . "/login/index/esqueci-minha-senha/" . $token . "'>" . HOME_URI . "/login/index/esqueci-minha-senha/" . $token . "</a>
					<br><br>
					Se você não fez nenhuma solicitação de recuperação, favor descosiderar esse email.
					<br>
					Para acessar a sua conta visite <a href='" . HOME_URI . "'>" . HOME_URI . "</a>
					<br><br>
					-----------------------------------------------
					<br>
					Esta é uma mensagem automática, por favor não responda.
					<br>
					Mensagem enviada através do site " . SYS_NAME . " - " . HOME_URI . "
					<br><br>
					<strong>Data/Hora:</strong> " . date('d/m/Y - H:i:s') . "<br>
					<strong>IP:</strong> " . $_SERVER['REMOTE_ADDR'];

		$this->ClasseMailer = new Mailer(MAILER_FromName, MAILER_Host, MAILER_Username, MAILER_Password, MAILER_SMTPAuth, MAILER_SMTPSecure, MAILER_Port);

		$emails = $this->ClasseEmail->getEmails(chk_array($this->form_data, 'idPessoa'));

		foreach ($emails as $dadosEmails) {
			$enviaEmail = $this->ClasseMailer->send($dadosEmails['email'], $assunto, $mensagem);
		}

		if (!$queryUsuario && !$enviaEmail) {
			$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
		} else {
			$this->form_msg = $this->controller->Messages->success('Solicitação efetuada com sucesso, você receberá um email com as instruções.');
		}

		return;
	}

	public function validarFormTrocarSenha($idPessoa = null)
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			return;
		}

		if (!is_numeric($idPessoa) && $idPessoa < 1) {
			return;
		}

		$this->form_data = array();

		$this->senha = isset($_POST["senha"]) ? $_POST["senha"] : null;

		if (empty($this->senha)) {
			$this->erro .= "<br>Preencha a senha.";
		}
		if (!empty($this->senha)) {
			if ($this->senha != $_POST['repitaSenha']) {
				$this->erro .= "<br>As senhas estão diferentes.";
			}
			if (!senhaValida($this->senha)) {
				$this->erro .= "<br>A senha deve ter no mínimo 8 caracteres, incluindo pelo menos uma letra maiúscula, uma letra minúscula e um número.";
			}
		}

		$this->form_data['senha'] = $this->senha;

		if (!empty($this->erro)) {
			$this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);

			return;
		}

		if (empty($this->form_data)) {
			return;
		}

		$pwHash = new PasswordHash(8, false);

		$this->form_data['senha'] = $pwHash->HashPassword($this->senha);

		$this->trocarSenha($idPessoa);

		return;
	}

	private function trocarSenha($idPessoa = null)
	{
		if (is_numeric($idPessoa) && $idPessoa > 0) {
			$idPessoa = (int) $idPessoa;

			$queryUsuario = $this->db->update('tblUsuario', 'idPessoa', $idPessoa, array('token' => null, 'senha' => chk_array($this->form_data, 'senha')));

			$assunto = SYS_NAME . ' - Senha Alterada';


			$registro = $this->ClassePessoa->getPessoa($idPessoa);

			$mensagem = "<h1>Redefinição de Senha</h1><br>
						Olá " . chk_array($registro, 'nome') . " " . chk_array($registro, 'sobrenome') . ",
						<br>
						A sua senha de acesso ao sistema foi alterada, caso você não tenha realizado está operação, entre em contato com o administrador do sistema e informe o ocorrido.
						<br>
						Se você realizou a troca da senha, favor descosiderar esse email, pois é apenas informativo.
						<br>
						Para acessar a sua conta visite <a href='" . HOME_URI . "'>" . HOME_URI . "</a>
						<br><br>
						-----------------------------------------------
						<br>
						Esta é uma mensagem automática, por favor não responda.
						<br>
						Mensagem enviada através do site " . SYS_NAME . " - " . HOME_URI . "
						<br><br>
						<strong>Data/Hora:</strong> " . date('d/m/Y - H:i:s') . "<br>
						<strong>IP:</strong> " . $_SERVER['REMOTE_ADDR'];

			$this->ClasseMailer = new Mailer(MAILER_FromName, MAILER_Host, MAILER_Username, MAILER_Password, MAILER_SMTPAuth, MAILER_SMTPSecure, MAILER_Port);

			$emails = $this->ClasseEmail->getEmails($idPessoa);

			foreach ($emails as $dadosEmails) {
				$enviaEmail = $this->ClasseMailer->send($dadosEmails['email'], $assunto, $mensagem);
			}

			if (!$queryUsuario) {
				$this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
			} else {
				$this->form_msg = $this->controller->Messages->success('Senha alterada com sucesso.');

				$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/login">';
			}
		}

		return;
	}

	public function bloquearUsuario($origem = null)
	{
		$id = null;


		if (is_numeric(chk_array($this->parametros, 1))) {
			$id = chk_array($this->parametros, 1);
		}

		if (!empty($id)) {
			$id = (int) $id;

			if ($origem == 'usuario') {
				$dado = 'id';
			} else {
				$dado = 'idPessoa';
			}

			$query = $this->db->update('tblUsuario', $dado, $id, array('status' => 'F'));

			$this->form_msg = $this->controller->Messages->success('Registro bloqueado com sucesso. Aguarde, você será redirecionado...');

			if ($origem == 'usuario') 
				$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/usuarios' . '">';
			 else 
				$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/pessoas/index/usuarios/' . chk_array($this->parametros, 1) . '">';
			


			return;
		}
	}

	public function desbloquearUsuario($origem = null)
	{
		$id = null;

		if (is_numeric(chk_array($this->parametros, 1))) {
			$id = chk_array($this->parametros, 1);
		}

		if (!empty($id)) {
			$id = (int) $id;

			if ($origem == 'usuario') 
				$dado = 'id';
			 else 
				$dado = 'idPessoa';
				

			$query = $this->db->update('tblUsuario', $dado, $id, array('status' => 'T'));

			$this->form_msg = $this->controller->Messages->success('Registro desbloqueado com sucesso. Aguarde, você será redirecionado...');

			if ($origem == 'usuario') 
				$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/usuarios' . '">';
			 else 
				$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/pessoas/index/usuarios/' . chk_array($this->parametros, 1) . '">';
			

			return;
		}
	}

	public function removerUsuario($parametros = array())
	{
		$id = null;

		if (chk_array($parametros, 2) == 'remover') {
			$this->form_msg = $this->controller->Messages->questionYesNo('Tem certeza que deseja apagar este usuário?', 'Sim', 'Não', $_SERVER['REQUEST_URI'] . '/confirma', HOME_URI . '/pessoas/index/usuarios/' . chk_array($this->parametros, 1));

			if (is_numeric(chk_array($parametros, 1)) && chk_array($parametros, 3) == 'confirma') {
				$id = chk_array($parametros, 1);
			}
		}

		if (!empty($id)) {
			$id = (int) $id;

			$query = $this->db->delete('tblUsuario', 'idPessoa', $id);

			$this->form_msg .= '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/pessoas/index/usuarios/' . chk_array($this->parametros, 1) . '">';
			$this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/pessoas/index/usuarios/' . chk_array($this->parametros, 1) . '";</script>';
			return;
		}
	}
}
