<?php
class Permissao
{
    public $db;

	private $id;

    private $erro;

    public function __construct($db = false){
		$this->db = $db;
	}

    
}