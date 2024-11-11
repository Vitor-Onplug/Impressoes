<?php
class LeitoresModel extends MainModel
{
    public $form_data;
    public $form_msg;
    public $db;

    private $nomeLeitor;
    private $idTerminal;
    private $numeroLeitor;
    private $ip;
    private $usuario;
    private $senha;

    private $erro;

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        $this->controller = $controller;
        $this->parametros = $this->controller->parametros;
        $this->userdata = $this->controller->userdata;
    }

    public function validarFormLeitores()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            return;
        }

        $this->form_data = array();
        $this->nomeLeitor = isset($_POST["nomeLeitor"]) ? $_POST["nomeLeitor"] : null;
        $this->numeroLeitor = isset($_POST["numeroLeitor"]) ? $_POST["numeroLeitor"] : null;
        $this->idTerminal = isset($_POST["idTerminal"]) ? $_POST["idTerminal"] : null;
        $this->ip = isset($_POST["ip"]) ? $_POST["ip"] : null;
        $this->usuario = isset($_POST["usuario"]) ? $_POST["usuario"] : null;
        $this->senha = isset($_POST["senha"]) ? $_POST["senha"] : null;

        if (empty($this->nomeLeitor)) {
            $this->erro .= "<br>Preencha o nome do leitor.";
        }
        if (empty($this->ip)) {
            $this->erro .= "<br>Preencha o IP do leitor.";
        }

        if (!empty($this->erro)) {
            $this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
            return;
        }

        // Prepara os dados do formulário
        $this->form_data['nomeLeitor'] = $this->nomeLeitor;
        $this->form_data['numeroLeitor'] = $this->numeroLeitor;
        $this->form_data['idTerminal'] = $this->idTerminal;
        $this->form_data['ip'] = $this->ip;
        $this->form_data['usuario'] = $this->usuario;
        $this->form_data['senha'] = $this->senha;

        // Verifica se está editando ou adicionando um leitor
        if (chk_array($this->parametros, 0) == 'editarLeitor') {
            $this->editarLeitor();
        } else {
            $this->adicionarLeitor();
        }
    }

    private function editarLeitor()
    {
        $id = null;

        if (chk_array($this->parametros, 1)) {
            $hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
        }

        if (!empty($id)) {
            $query = $this->db->update('tblEventoLeitor', 'id', $id, array(
                'nomeLeitor' => chk_array($this->form_data, 'nomeLeitor'),
                'numeroLeitor' => chk_array($this->form_data, 'numeroLeitor'),
                'idTerminal' => chk_array($this->form_data, 'idTerminal'),
                'ip' => chk_array($this->form_data, 'ip'),
                'usuario' => chk_array($this->form_data, 'usuario'),
                'senha' => chk_array($this->form_data, 'senha')
            ));

            if (!$query) {
                $this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
            } else {
                $this->form_msg = $this->controller->Messages->success('Registro editado com sucesso.');
                $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos/index/leitores/">';
            }
        }
    }

    public function adicionarLeitor()
    {
        $query = $this->db->insert('tblEventoLeitor', array(
            'nomeLeitor' => chk_array($this->form_data, 'nomeLeitor'),
            'numeroLeitor' => chk_array($this->form_data, 'numeroLeitor'),
            'idTerminal' => chk_array($this->form_data, 'idTerminal'),
            'ip' => chk_array($this->form_data, 'ip'),
            'usuario' => chk_array($this->form_data, 'usuario'),
            'senha' => chk_array($this->form_data, 'senha')
        ));

        if (!$query) {
            $this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
        } else {
            $this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos/index/leitores/">';
        }
    }

    public function getLeitores($idEvento = false)
    {
        $s_id = false;

        if(!empty($idEvento)){
            $s_id = (int) $idEvento;
        }

        if(empty($s_id)){
            return;
        }

        $query = $this->db->query('SELECT `tblEventoLeitor`.*, `tblEventoSetor`.`nomeSetor`, `tblEventoTerminal`.`nomeTerminal`
        FROM `tblEventoLeitor`
        INNER JOIN `tblEventoTerminal` ON `tblEventoLeitor`.`idTerminal` = `tblEventoTerminal`.`id`
        INNER JOIN `tblEventoSetor` ON `tblEventoTerminal`.`idSetor` = `tblEventoSetor`.`id` 
        INNER JOIN `tblEvento` ON `tblEventoSetor`.`idEvento` = `tblEvento`.`id` 
        WHERE `tblEventoSetor`.`idEvento` = ?', array($s_id));

        if (!$query) {
            return array();
        }

        return $query->fetchAll();
    }

    public function getLeitor($id = false)
    {
        if (empty($id)) {
            return array();
        }

        $query = $this->db->query('SELECT * FROM `tblEventoLeitor` WHERE `id` = ?', array($id));

        if (!$query) {
            $this->form_msg = $this->controller->Messages->error('Registro não encontrado.');
            return;
        }

        $registro = $query->fetch();

        if (empty($registro)) {
            $this->form_msg = $this->controller->Messages->error('Registro inexistente.');
            return;
        }

        foreach ($registro as $key => $value) {
            $this->form_data[$key] = $value;
        }

        return;
    }

    public function bloquearLeitor()
    {
        $id = null;

        if (chk_array($this->parametros, 1)) {
            $hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
        }

        if (!empty($id)) {
            $id = (int) $id;

            $query = $this->db->update('tblEventoLeitor', 'id', $id, array('status' => 'F'));

            $this->form_msg = $this->controller->Messages->success('Leitor bloqueado com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos/index/leitores/">';

            return;
        }
    }

    public function desbloquearLeitor()
    {
        $id = null;

        if (chk_array($this->parametros, 1)) {
            $hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
        }

        if (!empty($id)) {
            $id = (int) $id;

            $query = $this->db->update('tblEventoLeitor', 'id', $id, array('status' => 'T'));

            $this->form_msg = $this->controller->Messages->success('Leitor desbloqueado com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos/index/leitores/">';

            return;
        }
    }

    public function getQuantidadeLeitores($idEvento = false)
    {
        $s_id = false;

        if(!empty($idEvento)){
            $s_id = (int) $idEvento;
        }

        if(empty($s_id)){
            return;
        }

        $query = $this->db->query('SELECT COUNT(*) AS quantidade FROM `tblEventoLeitor`
        INNER JOIN `tblEventoTerminal` ON `tblEventoLeitor`.`idTerminal` = `tblEventoTerminal`.`id`
        INNER JOIN `tblEventoSetor` ON `tblEventoTerminal`.`idSetor` = `tblEventoSetor`.`id`
        INNER JOIN `tblEvento` ON `tblEventoSetor`.`idEvento` = `tblEvento`.`id`
        WHERE `idEvento` = ?', array($s_id));

        if (!$query) {
            return array();
        }

        $quantidade = $query->fetch();

        return $quantidade['quantidade'];
    }
}
