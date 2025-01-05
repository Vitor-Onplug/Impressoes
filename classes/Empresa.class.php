<?php
class Empresa
{
    public $db;

    private $id;
    private $razaoSocial;
    private $nomeFantasia;
    private $observacoes;
    private $avatar;

    private $erro;

    public function __construct($db = false)
    {
        $this->db = $db;
    }

    public function validarEmpresa($razaoSocial = null, $nomeFantasia = null, $observacoes = null)
    {
        $this->razaoSocial = $razaoSocial;
        $this->nomeFantasia = $nomeFantasia;
        $this->observacoes = $observacoes;

        if (empty($this->razaoSocial)) {
            $this->erro .= "<br>Preencha a razão social.";
        }
        if (!empty($this->razaoSocial)) {
            if (strlen($this->razaoSocial) > 255) {
                $this->erro .= "<br>A razão social não pode ultrapassar o limite de 255 caracteres.";
            }
        }
        if (empty($this->nomeFantasia)) {
            $this->erro .= "<br>Preencha o nome fantasia.";
        }
        if (!empty($this->nomeFantasia)) {
            if (strlen($this->nomeFantasia) > 255) {
                $this->erro .= "<br>O nome fantasia não pode ultrapassar o limite de 255 caracteres.";
            }
        }

        if (!empty($this->erro)) {
            return $this->erro;
        }

        return true;
    }

    public function adicionarEmpresa($razaoSocial = null, $nomeFantasia = null, $observacoes = null)
    {
        if (empty($razaoSocial) && empty($nomeFantasia)) {
            return false;
        }

        $this->razaoSocial = $razaoSocial;
        $this->nomeFantasia = $nomeFantasia;
        $this->observacoes = $observacoes;

        $query = $this->db->insert('tblEmpresa', array('razaoSocial' => $this->razaoSocial, 'nomeFantasia' => $this->nomeFantasia, 'observacoes' => $this->observacoes));

        if (!$query) {
            return false;
        }

        return $query;
    }

    public function editarEmpresa($id = null, $razaoSocial = null, $nomeFantasia = null, $observacoes = null)
    {
        if (empty($id) && empty($razaoSocial) && empty($nomeFantasia)) {
            return false;
        }

        $this->id = $id;
        $this->razaoSocial = $razaoSocial;
        $this->nomeFantasia = $nomeFantasia;
        $this->observacoes = $observacoes;

        $query = $this->db->update('tblEmpresa', 'id', $this->id, array('razaoSocial' => $this->razaoSocial, 'nomeFantasia' => $this->nomeFantasia, 'observacoes' => $this->observacoes));

        if (!$query) {
            return false;
        }

        return $query;
    }

    public function getEmpresa($id = null)
    {
        if (empty($id)) {
            return;
        }

        $id = (int) $id;

        $query = $this->db->query('SELECT * FROM `tblEmpresa` WHERE `id` = ?', array($id));

        if (!$query) {
            return false;
        }

        return $query->fetch();
    }

    public function getEmpresas($filtros = null)
    {
        $where = null;
        $limit = null;

        if (!empty($filtros["q"])) {
            if (!empty($where)) {
                $where .= " AND ";
            } else {
                $where = " WHERE ";
            }

            $where .= "(`tblEmpresa`.`razaoSocial` LIKE '%" . _otimizaBusca($filtros['q']) . "%' OR `tblEmpresa`.`nomeFantasia` LIKE '%" . _otimizaBusca($filtros['q']) . "%') ";
        }

        if (!empty($filtros["limite"])) {
            $limit = "LIMIT " . $filtros["limite"];
        }

        if (!empty($filtros["ordena"]) && !empty($filtros["ordem"])) {
            $orderby = "ORDER BY " . $filtros["ordena"] . " " .  $filtros["ordem"];
        } else {
            $orderby = "ORDER BY `tblEmpresa`.`razaoSocial`, `tblEmpresa`.`nomeFantasia`";
        }

        if (!empty($filtros["status"])) {
            if (!empty($where)) {
                $where .= " AND ";
            } else {
                $where = " WHERE ";
            }

            $where .= "(`tblEmpresa`.`status` = '" . $filtros['status'] . "')";
        }

        $sql = "SELECT `tblEmpresa`.* FROM `tblEmpresa` $where $orderby $limit";

        $query = $this->db->query($sql);

        if (!$query) {
            return array();
        }

        return $query->fetchAll();
    }
}
