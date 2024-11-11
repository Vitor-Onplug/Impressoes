<?php 
class TerminaisModel extends MainModel {
    public $form_data;
    public $form_msg;
    public $db;

    private $nomeTerminal;
    private $idSetor;
    private $numeroTerminal;
    private $tipo;
    private $ip;
    private $usuario;
    private $senha;

    private $erro;

    public function __construct($db = false, $controller = null){
        $this->db = $db;
        $this->controller = $controller;
        $this->parametros = $this->controller->parametros;
        $this->userdata = $this->controller->userdata;
    }

    public function validarFormTerminais(){
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            return;
        }

        $this->form_data = array();
        $this->nomeTerminal = isset($_POST["nomeTerminal"]) ? $_POST["nomeTerminal"] : null;
        $this->numeroTerminal = isset($_POST["numeroTerminal"]) ? $_POST["numeroTerminal"] : null;
        $this->tipo = isset($_POST["tipo"]) ? $_POST["tipo"] : null;
        $this->idSetor = isset($_POST["idSetor"]) ? $_POST["idSetor"] : null;
        $this->ip = isset($_POST["ip"]) ? $_POST["ip"] : null;
        $this->usuario = isset($_POST["usuario"]) ? $_POST["usuario"] : null;
        $this->senha = isset($_POST["senha"]) ? $_POST["senha"] : null;

        if(empty($this->nomeTerminal)){ $this->erro .= "<br>Preencha o nome do terminal."; }
        if(empty($this->idSetor)){ $this->erro .= "<br>Selecione o evento."; }
        if(empty($this->numeroTerminal)){ $this->erro .= "<br>Preencha o número do terminal."; }
        if(empty($this->tipo)){ $this->erro .= "<br>Selecione o tipo do terminal."; }
        if(empty($this->ip)){ $this->erro .= "<br>Preencha o IP do terminal."; }
        if(empty($this->usuario)){ $this->erro .= "<br>Preencha o usuário do terminal."; }
        if(empty($this->senha)){ $this->erro .= "<br>Preencha a senha do terminal."; }

        // Se houver erros, exibe a mensagem de erro
        if(!empty($this->erro)){
            $this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
            return;
        }

        // Se não houver erros, prepara os dados do formulário
        $this->form_data['nomeTerminal'] = $this->nomeTerminal;
        $this->form_data['numeroTerminal'] = $this->numeroTerminal;
        $this->form_data['tipo'] = $this->tipo;
        $this->form_data['idSetor'] = $this->idSetor;
        $this->form_data['ip'] = $this->ip;
        $this->form_data['usuario'] = $this->usuario;
        $this->form_data['senha'] = $this->senha;

        // Verifica se está editando ou adicionando um terminal
        if(chk_array($this->parametros, 0) == 'editarTerminal'){
            $this->editarTerminal();
            return;
        }else{
            $this->adicionarTerminal();
            return;
        }
    }

    private function editarTerminal(){
        $id = null;

        if(chk_array($this->parametros, 1)){
            $hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
        }

        if(!empty($id)){
            $id = (int) $id;

            $query = $this->db->update('tblEventoTerminal', 'id', $id, array(
                'nomeTerminal' => chk_array($this->form_data, 'nomeTerminal'),
                'numeroTerminal' => chk_array($this->form_data, 'numeroTerminal'),
                'tipo' => chk_array($this->form_data, 'tipo'),
                'idSetor' => chk_array($this->form_data, 'idSetor'),
                'ip' => chk_array($this->form_data, 'ip'),
                'usuario' => chk_array($this->form_data, 'usuario'),
                'senha' => chk_array($this->form_data, 'senha')
            ));

            if(!$query){
                $this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
                $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos/index/terminais/' . encryptId($id) . '">';
                return;
            }else{
                $this->form_msg = $this->controller->Messages->success('Registro editado com sucesso.');
                $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos/index/terminais/">';
                return;
            }
        }
    }

    public function adicionarTerminal(){
        $query = $this->db->insert('tblEventoTerminal', array(
            'nomeTerminal' => chk_array($this->form_data, 'nomeTerminal'),
            'numeroTerminal' => chk_array($this->form_data, 'numeroTerminal'),
            'tipo' => chk_array($this->form_data, 'tipo'),
            'idSetor' => chk_array($this->form_data, 'idSetor'),
            'ip' => chk_array($this->form_data, 'ip'),
            'usuario' => chk_array($this->form_data, 'usuario'),
            'senha' => chk_array($this->form_data, 'senha')
        ));

        if(!$query){
            $this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
            return;
        }else{
            $this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/eventos/index/terminais">';
            $this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/eventos/index/terminais";</script>';
            return;
        }
    }

    public function getTerminais($idEvento = false){
        $s_id = false;

        if(!empty($idEvento)){
            $s_id = (int) $idEvento;
        }

        if(empty($s_id)){
            return;
        }

        $query = $this->db->query('SELECT `tblEventoTerminal`.*, `tblEvento`.`evento`, `tblEventoSetor`.`nomeSetor` FROM `tblEventoTerminal` 
        INNER JOIN `tblEventoSetor` ON `tblEventoTerminal`.`idSetor` = `tblEventoSetor`.`id` 
        INNER JOIN `tblEvento` ON `tblEventoSetor`.`idEvento` = `tblEvento`.`id` 
        WHERE `tblEventoSetor`.`idEvento` = ?', array($s_id));

        if(!$query){
            return array();
        }

        return $query->fetchAll();
    }

    public function getTerminal($id = false){
        $s_id = false;

        if(!empty($id)){
            $s_id = (int) $id;
        }

        if(empty($s_id)){
            return;
        }

        $query = $this->db->query('SELECT * FROM `tblEventoTerminal` WHERE `id` = ?', array($s_id));

        if(!$query){
            $this->form_msg = $this->controller->Messages->error('Registro não encontrado.');
            return;
        }

        $registro = $query->fetch();

        if(empty($registro)){
            $this->form_msg = $this->controller->Messages->error('Registro inexistente.');
            return;
        }

        foreach($registro as $key => $value){
            $this->form_data[$key] = $value;
        }

        return;
    }

    public function getTiposTerminal() {
        $query = $this->db->query("SHOW COLUMNS FROM `tblEventoTerminal` LIKE 'tipo'");
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $enum = str_replace("'", "", substr($row['Type'], 5, (strlen($row['Type']) - 6))); // Retira os parênteses
        $enum_values = explode(',', $enum); // Separa as opções
        return $enum_values;
    }
    

    public function bloquearTerminal(){
        $id = null;
        
        if(chk_array($this->parametros, 1)){
            $hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
        }

        if(!empty($id)){
            $id = (int) $id;

            $query = $this->db->update('tblEventoTerminal', 'id', $id, array('status' => 'F'));

            $this->form_msg = $this->controller->Messages->success('Terminal bloqueado com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos/index/terminais/">';
        
            return;
        }
    }

    public function desbloquearTerminal(){
        $id = null;
        
        if(chk_array($this->parametros, 1)){
            $hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
        }

        if(!empty($id)){
            $id = (int) $id;

            $query = $this->db->update('tblEventoTerminal', 'id', $id, array('status' => 'T'));

            $this->form_msg = $this->controller->Messages->success('Terminal desbloqueado com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos/index/terminais/">';

            return;
        }
    }

    public function getQuantidadeTerminais($idEvento = false){
        $s_id = false;

        if(!empty($idEvento)){
            $s_id = (int) $idEvento;
        }

        if(empty($s_id)){
            return;
        }

        $query = $this->db->query('SELECT COUNT(*) AS quantidade FROM `tblEventoTerminal` 
        INNER JOIN `tblEventoSetor` ON `tblEventoTerminal`.`idSetor` = `tblEventoSetor`.`id` 
        INNER JOIN `tblEvento` ON `tblEventoSetor`.`idEvento` = `tblEvento`.`id` 
        WHERE `tblEventoSetor`.`idEvento` = ?', array($s_id));

        if(!$query){
            return array();
        }

        $quantidade = $query->fetch();

        return $quantidade['quantidade'];
    }
}
?>
