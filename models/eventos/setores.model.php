<?php 
class SetoresModel extends MainModel {
    public $form_data;
    public $form_msg;
    public $db;

    private $nomeSetor;
    private $idEvento;

    private $erro;

    public function __construct($db = false, $controller = null){
        $this->db = $db;
        $this->controller = $controller;
        $this->parametros = $this->controller->parametros;
        $this->userdata = $this->controller->userdata;
    }

    public function validarFormSetores(){
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            return;
        }

        $this->form_data = array();
        $this->nomeSetor = isset($_POST["nomeSetor"]) ? $_POST["nomeSetor"] : null;
        $this->idEvento = isset($_POST["idEvento"]) ? $_POST["idEvento"] : null;

        if(empty($this->nomeSetor)){ $this->erro .= "<br>Preencha o nome do setor."; }
        if(empty($this->idEvento)){ $this->erro .= "<br>Selecione o evento."; }

        // Se houver erros, exibe a mensagem de erro
        if(!empty($this->erro)){
            $this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
            return;
        }

        // Se não houver erros, prepara os dados do formulário
        $this->form_data['nomeSetor'] = $this->nomeSetor;
        $this->form_data['idEvento'] = $this->idEvento;

        // Verifica se está editando ou adicionando um setor
        if(chk_array($this->parametros, 0) == 'editarSetor'){
            $this->editarSetor();
            return;
        }else{
            $this->adicionarSetor();
            return;
        }
    }

    private function editarSetor(){
        $id = null;

        if(chk_array($this->parametros, 1)){
            $hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
        }

        if(!empty($id)){
            $id = (int) $id;

            $query = $this->db->update('tblEventoSetor', 'id', $id, array(
                'nomeSetor' => chk_array($this->form_data, 'nomeSetor'),
                'idEvento' => chk_array($this->form_data, 'idEvento')
            ));

            if(!$query){
                $this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
                $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos/index/setores/' . encryptId($id) . '">';
                return;
            }else{
                $this->form_msg = $this->controller->Messages->success('Registro editado com sucesso.');
                $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos/index/setores/">';
                return;
            }
        }
    }

    public function adicionarSetor(){
        $query = $this->db->insert('tblEventoSetor', array(
            'nomeSetor' => chk_array($this->form_data, 'nomeSetor'),
            'idEvento' => chk_array($this->form_data, 'idEvento')
        ));

        if(!$query){
            $this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
            return;
        }else{
            $this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/eventos/index/setores">';
            $this->form_msg .= '<script type="text/javascript">window.location.href = "' . HOME_URI . '/eventos/index/setores";</script>';
            return;
        }
    }

    public function getSetores($idEvento = false){
        $s_id = false;

        if(!empty($idEvento)){
            $s_id = (int) $idEvento;
        }

        if(empty($s_id)){
            return;
        }

        $query = $this->db->query('SELECT `tblEventoSetor`.*, `tblEvento`.`evento` FROM `tblEventoSetor` INNER JOIN `tblEvento` ON `tblEventoSetor`.`idEvento` = `tblEvento`.`id` WHERE `tblEventoSetor`.`idEvento` = ?', array($s_id));

        if(!$query){
            return array();
        }

        return $query->fetchAll();
    }

    public function getSetor($id = false){
        $s_id = false;

        if(!empty($id)){
            $s_id = (int) $id;
        }

        if(empty($s_id)){
            return;
        }

        $query = $this->db->query('SELECT * FROM `tblEventoSetor` WHERE `id` = ?', array($s_id));

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

    public function bloquearSetor(){
		$id = null;
		
		if(chk_array($this->parametros, 1)){
			$hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
		}

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblEventoSetor', 'id', $id, array('status' => 'F'));
			
			$this->form_msg = $this->controller->Messages->success('Registro bloqueado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos/index/setores/">';
		
			return;
		}
	}
	
	public function desbloquearSetor(){
		$id = null;
        
		if(chk_array($this->parametros, 1)){
			$hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
		}

		if(!empty($id)){
			$id = (int) $id;
		
			$query = $this->db->update('tblEventoSetor', 'id', $id, array('status' => 'T'));
			
			$this->form_msg = $this->controller->Messages->success('Registro desbloqueado com sucesso.');
			$this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos/index/setores/">';
		
			return;
		}
	}

    public function getQuantidadeSetores($idEvento = false){
        $s_id = false;

        if(!empty($idEvento)){
            $s_id = (int) $idEvento;
        }

        if(empty($s_id)){
            return;
        }

        $query = $this->db->query('SELECT COUNT(*) AS quantidade FROM `tblEventoSetor` WHERE `idEvento` = ?', array($s_id));

        if(!$query){
            return 0;
        }

        $quantidade = $query->fetch();

        return $quantidade['quantidade'];
    }
}
?>
