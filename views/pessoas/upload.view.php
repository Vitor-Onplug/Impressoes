<?php 
if(!defined('ABSPATH')) exit; 

if(chk_array($parametros, 1) == "avatar"){
	return $modelo->avatar();
}

if(chk_array($parametros, 1) == "diversos"){
    return $modelo->diversos();
}

if(chk_array($parametros, 1) == "documentos"){
	header('content-type: application/json; charset=utf-8');
	header("access-control-allow-origin: *");
	
	$modeloUsuarios->getUsuario(chk_array($parametros, 2));
	
	$dataHoraCriacao = explode(" ", $modeloUsuarios->form_data['dataCriacao']);
	$dataCriacao = explode("-", $dataHoraCriacao[0]);
	
	$script = $_SERVER['REQUEST_URI'];
	$destino = "midia/documentos/" . $dataCriacao[0] . "/" . $dataCriacao[1] . "/" . $dataCriacao[2] . "/" . $modeloUsuarios->form_data['idPessoa'] . "/";
	$url = HOME_URI . "/" . "midia/documentos/" . $dataCriacao[0] . "/" . $dataCriacao[1] . "/" . $dataCriacao[2] . "/" . $modeloUsuarios->form_data['idPessoa'] . "/";
	
	$resposta = $modelo->documentos(array('script_url' =>  $script, 'upload_dir' => $destino, 'upload_url' => $url));
	
	print_r($resposta, true);
}
?>