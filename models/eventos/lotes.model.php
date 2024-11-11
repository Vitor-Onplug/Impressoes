<?php
class LotesModel extends MainModel
{
    public $form_data;
    public $form_msg;
    public $db;

    private $id;
    private $nomeLote;
    private $idEvento;
    private $idTipoCredencial;
    private $idTipoCodigo;
    private $permiteAcessoFacial;
    private $permiteImpressao;
    private $temAutonumeracao;

    private $periodos;
    private $setores;

    private $erro;

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        $this->controller = $controller;
        $this->parametros = $this->controller->parametros;
        $this->userdata = $this->controller->userdata;
    }

    public function validarFormLotes()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            return;
        }

        $this->form_data = array();
        $this->nomeLote = isset($_POST["nomeLote"]) ? $_POST["nomeLote"] : null;
        $this->idEvento = isset($_POST["idEvento"]) ? $_POST["idEvento"] : null;
        $this->idTipoCredencial = isset($_POST["tipoCredencial"]) ? $_POST["tipoCredencial"] : null;
        $this->idTipoCodigo = isset($_POST["tipoCodigo"]) ? $_POST["tipoCodigo"] : null;
        $this->permiteAcessoFacial = isset($_POST["permiteAcessoFacial"]) ? $_POST["permiteAcessoFacial"] : null;
        $this->permiteImpressao = isset($_POST["permiteImpressao"]) ? $_POST["permiteImpressao"] : null;
        $this->temAutonumeracao = isset($_POST["temAutonumeracao"]) ? $_POST["temAutonumeracao"] : null;
        $this->periodos = isset($_POST["periodos"]) ? $_POST["periodos"] : [];

        if (empty($this->nomeLote)) {
            $this->erro .= "<br>Preencha o nome do lote.";
        }
        if (empty($this->idEvento)) {
            $this->erro .= "<br>Selecione o evento.";
        }
        if (empty($this->idTipoCredencial)) {
            $this->erro .= "<br>Selecione o tipo de credencial.";
        }
        if (empty($this->idTipoCodigo)) {
            $this->erro .= "<br>Selecione o tipo de código.";
        }

        if (!empty($this->erro)) {
            $this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
            return;
        }

        $this->form_data['nomeLote'] = $this->nomeLote;
        $this->form_data['idTipoCredencial'] = $this->idTipoCredencial;
        $this->form_data['idTipoCodigo'] = $this->idTipoCodigo;
        $this->form_data['permiteAcessoFacial'] = $this->permiteAcessoFacial;
        $this->form_data['permiteImpressao'] = $this->permiteImpressao;
        $this->form_data['temAutonumeracao'] = $this->temAutonumeracao;
        $this->form_data['idEvento'] = $this->idEvento;

        if (chk_array($this->parametros, 0) == 'editarLote') {
            $this->editarLote();
            return;
        } else {
            $this->adicionarLote();
            return;
        }
    }

    private function editarLote()
    {
        $id = null;

        if (chk_array($this->parametros, 1)) {
            $hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
        }

        if (!empty($id)) {
            $id = (int) $id;

            // Atualiza os dados principais do lote
            $query = $this->db->update('tblcredenciallote', 'id', $id, array(
                'nomeLote' => chk_array($this->form_data, 'nomeLote'),
                'idEvento' => chk_array($this->form_data, 'idEvento'),
                'idTipoCredencial' => chk_array($this->form_data, 'idTipoCredencial'),
                'idTipoCodigo' => chk_array($this->form_data, 'idTipoCodigo'),
                'permiteAcessoFacial' => chk_array($this->form_data, 'permiteAcessoFacial'),
                'permiteImpressao' => chk_array($this->form_data, 'permiteImpressao'),
                'temAutonumeracao' => chk_array($this->form_data, 'temAutonumeracao')
            ));

            if (!$query) {
                $this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
                return;
            }

            $this->form_msg = $this->controller->Messages->success('Registro editado com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos/index/lotes/">';
            return;
        }
    }

    public function adicionarLote()
    {
        $query = $this->db->insert('tblcredenciallote', array(
            'nomeLote' => chk_array($this->form_data, 'nomeLote'),
            'idTipoCredencial' => chk_array($this->form_data, 'idTipoCredencial'),
            'idTipoCodigo' => chk_array($this->form_data, 'idTipoCodigo'),
            'permiteAcessoFacial' => chk_array($this->form_data, 'permiteAcessoFacial'),
            'permiteImpressao' => chk_array($this->form_data, 'permiteImpressao'),
            'temAutonumeracao' => chk_array($this->form_data, 'temAutonumeracao'),
            'idEvento' => chk_array($this->form_data, 'idEvento')
        ));

        if (!$query) {
            $this->form_msg = $this->controller->Messages->error('Erro interno: Os dados não foram enviados.');
            return;
        }

        $idLote = $this->db->lastInsertId();

        $this->form_msg = $this->controller->Messages->success('Registro cadastrado com sucesso.');
        $this->form_data .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos/index/lotes/">';
        return;
    }

    private function inserirPeriodos($idLote)
    {
        foreach ($this->periodos as $periodo) {
            $this->db->insert('tblcredencialloteperiodo', array(
                'idLote' => $idLote,
                'dataInicio' => $periodo['dataInicio'],
                'dataTermino' => $periodo['dataTermino']
            ));
        }
    }

    public function getLote($id)
    {
        return $this->db->query('SELECT * FROM `tblCredencialLote` WHERE `id` = ?', array($id))->fetch();
    }

    public function getPeriodos($idLote)
    {
        return $this->db->query('SELECT * FROM `tblcredencialloteperiodo` WHERE `idLote` = ?', array($idLote))->fetchAll();
    }

    public function getTiposCredencial()
    {
        return $this->db->query('SELECT * FROM `tblTipocredencial` WHERE `status` = "T"')->fetchAll();
    }

    public function getTiposCodigo()
    {
        return $this->db->query('SELECT * FROM `tblTipoCodigoCredencial` WHERE `status` = "T"')->fetchAll();
    }

    public function getLotes($idEvento)
    {
        return $this->db->query('SELECT `tblcredenciallote`.*, `tblTipocredencial`.`tipoCredencial`, `tblTipoCodigoCredencial`.`tipoCodigo`
            FROM `tblcredenciallote`
            INNER JOIN `tblevento` ON `tblCredencialLote`.`idEvento` = `tblevento`.`id`
            INNER JOIN `tblTipocredencial` ON `tblcredenciallote`.`idTipoCredencial` = `tblTipocredencial`.`id`
            INNER JOIN `tblTipoCodigoCredencial` ON `tblcredenciallote`.`idTipoCodigo` = `tblTipoCodigoCredencial`.`id`
            WHERE `tblcredenciallote`.`idEvento` = ?', array($idEvento))->fetchAll();
    }

    public function bloquearLote()
    {
        $id = null;

        if (chk_array($this->parametros, 1)) {
            $hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
        }

        if (!empty($id)) {
            $id = (int) $id;

            $query = $this->db->update('tblCredencialLote', 'id', $id, array('status' => 'F'));

            $this->form_msg = $this->controller->Messages->success('Registro bloqueado com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos/index/lotes/">';

            return;
        }
    }

    public function desbloquearLote()
    {
        $id = null;

        if (chk_array($this->parametros, 1)) {
            $hash = chk_array($this->parametros, 1);
            $id = decryptHash($hash);
        }

        if (!empty($id)) {
            $id = (int) $id;

            $query = $this->db->update('tblCredencialLote', 'id', $id, array('status' => 'T'));

            $this->form_msg = $this->controller->Messages->success('Registro desbloqueado com sucesso.');
            $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos/index/lotes/">';

            return;
        }
    }

    public function getPeriodosLote($idLote)
    {
        return $this->db->query('SELECT * FROM `tblCredencialLotePeriodo` WHERE `idLote` = ?', array($idLote))->fetchAll();
    }

    public function getSetoresLote($idLote)
    {
        return $this->db->query('SELECT * FROM `relCredencialSetor` WHERE `idLote` = ?', array($idLote))->fetchAll();
    }

    public function validarFormRelacoes()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            return;
        }

        $this->form_data = array();
        $this->periodos = isset($_POST["periodos"]) ? $_POST["periodos"] : [];
        $this->setores = isset($_POST["setores"]) ? $_POST["setores"] : [];

        if ((empty($this->periodos) && empty($this->setores))) {
            $this->erro .= "<br>Selecione ao menos um período ou setor.";
        }

        if (!empty($this->erro)) {
            $this->form_msg = $this->controller->Messages->error('<strong>Os seguintes erros foram encontrados:</strong>' . $this->erro);
            return;
        }

        $this->atualizarPeriodos();
        $this->atualizarSetores();

        $this->form_msg = $this->controller->Messages->success('Relações atualizadas com sucesso.');
        $this->form_msg .= '<meta http-equiv="refresh" content="2; url=' . HOME_URI . '/eventos/index/lotes/">';
        return;
    }

    private function atualizarPeriodos()
    {
        $idLote = null;

        if (chk_array($this->parametros, 1)) {
            $hash = chk_array($this->parametros, 1);
            $idLote = decryptHash($hash);
        }

        if (!empty($idLote)) {
            $idLote = (int) $idLote;

            // Deleta os períodos antigos
            $this->db->delete('tblCredencialLotePeriodo', 'idLote', $idLote);

            // Insere os novos períodos
            foreach ($this->periodos as $periodo) {
                $this->db->insert('tblCredencialLotePeriodo', array(
                    'idLote' => $idLote,
                    'dataInicio' => $periodo['dataInicio'],
                    'dataTermino' => $periodo['dataTermino']
                ));
            }
        }
    }

    private function atualizarSetores()
    {
        $idLote = null;

        if (chk_array($this->parametros, 1)) {
            $hash = chk_array($this->parametros, 1);
            $idLote = decryptHash($hash);
        }

        if(empty($idLote)) {
            return;
        }

        // Percorre os setores enviados e insere ou atualiza
        foreach ($_POST['setores'] as $setorData) {
            $idSetor = $setorData['id'];
            $status = isset($setorData['status']) && $setorData['status'] === 'T' ? 'T' : 'F'; // Define status com base no checkbox
    
            // Verifica se o setor já existe na relação do lote
            $query = $this->db->query('SELECT * FROM `relCredencialSetor` WHERE `idLote` = ? AND `idSetor` = ?', array($idLote, $idSetor));
    
            if ($query->rowCount() == 0) {
                // Insere o setor se não existir
                $this->db->insert('relCredencialSetor', array(
                    'idLote' => $idLote,
                    'idSetor' => $idSetor,
                    'status' => $status
                ));
            } else {
                // Atualiza o status do setor existente
                $this->db->update('relCredencialSetor', 'idSetor', $idSetor, array(
                    'status' => $status
                ));
            }
        }
    }    
}
