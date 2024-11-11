<?php 
if(!defined('ABSPATH')) exit; 

if(chk_array($parametros, 0) == "avatar"){
	header('content-type: application/json; charset=utf-8');
	header("access-control-allow-origin: *");
	
	$destino = $_SESSION['naspPessoaAvatar'];
	
	$opcao = isset($_REQUEST["opcao"]) ? $_REQUEST["opcao"] : null;
	
	$resposta = $modelo->avatar($destino, array('option' => $opcao));
	
	print_r($resposta);
	
}
?>