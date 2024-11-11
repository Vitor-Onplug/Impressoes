<?php 
if(!defined('ABSPATH')) exit; 

if(chk_array($parametros, 1) == "avatar"){
	return $modelo->avatar();
}

if(chk_array($parametros, 1) == "comprovante"){
	header('content-type: application/json; charset=utf-8');
	header("access-control-allow-origin: *");
	
	$modeloEventoContratacoes->getContratacao(chk_array($parametros, 2));
	
	$dataHoraCriacao = explode(" ", $modeloEventoContratacoes->form_data['dataCriacao']);
	$dataCriacao = explode("-", $dataHoraCriacao[0]);
	
	$destino = "midia/comprovantes/" . $dataCriacao[0] . "/" . $dataCriacao[1] . "/" . $dataCriacao[2] . "/" . $modeloEventoContratacoes->form_data['id'] . "/";
	
	$opcao = isset($_REQUEST["opcao"]) ? $_REQUEST["opcao"] : null;
	
	$resposta = $modelo->comprovante($destino, $opcao);
	
	print_r($resposta);
	
}

if(chk_array($parametros, 1) == "financeiro"){
	header('content-type: application/json; charset=utf-8');
	header("access-control-allow-origin: *");
	
	$destino = $_SESSION['comprovanteFinanceiro'];
	
	$opcao = isset($_REQUEST["opcao"]) ? $_REQUEST["opcao"] : null;
	
	$resposta = $modelo->comprovante($destino, $opcao);
	
	print_r($resposta);
	
}
?>