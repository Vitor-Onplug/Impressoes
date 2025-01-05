<?php
spl_autoload_register(function($class_name){
    $file = ABSPATH . '/classes/' . $class_name . '.class.php';

    if (!file_exists($file)){
        require_once ABSPATH . '/views/404.view.php';
        return;
    }

    require_once $file;
});

function chk_array ($array, $key) {
    if( isset($array[$key]) && ! empty( $array[$key])){
        return $array[$key];
    }

    return null;
}


function encryptId($id, $dataCriacao = "") {
    $secretKey = HASH_KEY;  // Chave secreta para criptografia
    $cipherMethod = 'AES-256-CBC';       // Método de criptografia
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipherMethod));  // Gera um IV aleatório
    
    // Concatena o ID e a data de criação
    $plaintext = $id . '|' . $dataCriacao;
    
    // Criptografa o texto
    $encrypted = openssl_encrypt($plaintext, $cipherMethod, $secretKey, 0, $iv);
    
    // Codifica o IV e o texto criptografado em base64
    $base64Encrypted = base64_encode($iv . '::' . $encrypted);
    
    // Substitui os caracteres +, /, e = por caracteres seguros para URL
    return strtr($base64Encrypted, '+/=', '-_,');
}


function decryptHash($encryptedData) {
    $secretKey = HASH_KEY;  // A mesma chave secreta usada na criptografia
    $cipherMethod = 'AES-256-CBC';       // O mesmo método de criptografia

	// Reverte a substituição de caracteres para recuperar a string base64 original
    $base64Encrypted = strtr($encryptedData, '-_,', '+/=');

    // Decodifica o texto base64
    $data = base64_decode($base64Encrypted);
	if($data === false) 
	{	return false;	}
    
    // Separa o IV e o texto criptografado
    list($iv, $encryptedText) = explode('::', $data, 2);
    
    // Descriptografa o texto
    $decrypted = openssl_decrypt($encryptedText, $cipherMethod, $secretKey, 0, $iv);
    
    // Separar o ID da data (retorna o ID)
    list($id, $dataCriacao) = explode('|', $decrypted);
    
    // Retorna o ID
    return $id;
}

function RemoveAcentos($msg){  
    $a = array( 
            '/[ÂÀÁÄÃ]/'=>'A', 
            '/[âãàáä]/'=>'a', 
            '/[ÊÈÉË]/'=>'E', 
            '/[êèéë]/'=>'e', 
            '/[ÎÍÌÏ]/'=>'I', 
            '/[îíìï]/'=>'i', 
            '/[ÔÕÒÓÖ]/'=>'O', 
            '/[ôõòóö]/'=>'o', 
            '/[ÛÙÚÜ]/'=>'U', 
            '/[ûúùü]/'=>'u', 
            '/ç/'=>'c', 
            '/Ç/'=> 'C', 
            '/ñ/'=>'n', 
            '/Ñ/'=> 'N'
    );  
    
    return preg_replace(array_keys($a), array_values($a), $msg);  
}

function _vcpfj($candidato){
    $l = strlen($candidato = str_replace(array(".","-","/"),"",$candidato));
    if ((!is_numeric($candidato)) || (!in_array($l,array(11,14))) || (count(count_chars($candidato,1))==1)) {
        return false;
    }
    $cpfj = str_split(substr($candidato,0,$l-2));
    $k = 9;
	$s = null;
    for ($j=0;$j<2;$j++) {
        for ($i=(count($cpfj));$i>0;$i--) {
            $s += $cpfj[$i-1] * $k;
            $k--;
            $l==14&&$k<2?$k=9:1;
        }
        $cpfj[] = $s%11==10?0:$s%11;
        $s = 0;
        $k = 9;
    }    
    return $candidato==join($cpfj);
}

function _otimizaBusca($keyword){
	$keyword = trim($keyword);
	$keyword = str_replace(' ', '%', $keyword);
	return $keyword;
}

function createdir($directory){
	if(!is_dir($directory)){
		mkdir($directory, 0777, true);
		chmod($directory, 0777);
		
		return true;
	}elseif(is_dir($directory)){
		return true;
	}else{
		return false;
	}
}

function rrmdir($dir){
	if (is_dir($dir)){
		$files = scandir($dir);
		foreach ($files as $file){
			if ($file != "." && $file != "..") rrmdir("$dir/$file");
		}
		rmdir($dir);
	}elseif(file_exists($dir)){
		unlink($dir);
	}
}
  
function rcopy($src, $dst) {
	if(file_exists($dst)){
		rrmdir($dst);
	}
	if(is_dir($src)){
		createdir($dst);
		$files = scandir($src);
		foreach($files as $file){
			if($file != "." && $file != ".."){
				rcopy("$src/$file", "$dst/$file");
			}
		}
	}elseif(file_exists($src)){
		copy($src, $dst);
	}
}

function diaAbreviado($data) {
	$ano =  substr($data, 0, 4);
	$mes =  substr($data, 5, -3);
	$dia =  substr($data, 8, 9);
	$diaAbreviado = date("w", mktime(0,0,0,$mes,$dia,$ano) );
	switch($diaAbreviado) {
		case"0": $diaAbreviado = "Dom";
		break;
		case"1": $diaAbreviado = "Seg"; 
		break;
		case"2": $diaAbreviado = "Ter";   
		break;
		case"3": $diaAbreviado = "Qua";  
		break;
		case"4": $diaAbreviado = "Qui";  
		break;
		case"5": $diaAbreviado = "Sex";   
		break;
		case"6": $diaAbreviado = "Sáb";  
		break;
	}
	return $diaAbreviado;
}

function mesAbreviado($mes) {
	switch($mes) {
		case"01": $mesAbreviado = "Jan";
		break;
		case"02": $mesAbreviado = "Fev"; 
		break;
		case"03": $mesAbreviado = "Mar";   
		break;
		case"04": $mesAbreviado = "Abr";  
		break;
		case"05": $mesAbreviado = "Mai";  
		break;
		case"06": $mesAbreviado = "Jun";   
		break;
		case"07": $mesAbreviado = "Jul";  
		break;
		case"08": $mesAbreviado = "Ago";  
		break;
		case"09": $mesAbreviado = "Set";  
		break;
		case"10": $mesAbreviado = "Out";  
		break;
		case"11": $mesAbreviado = "Nov";  
		break;
		case"12": $mesAbreviado = "Dez";  
		break;
	}
	return "$mesAbreviado";
}

function diff_datas($inicio, $final){
	$inicio = explode("/", $inicio);
	$final = explode("/", $final);
	$timeInicio = mktime(0, 0, 0, $inicio[1], $inicio[0], $inicio[2]);
	$timeFinal = mktime(0, 0, 0, $final[1], $final[0], $final[2]);

	$intervalo = $timeFinal - $timeInicio;
	$intervalo = (int)floor($intervalo / (60 * 60 * 24));

	return $intervalo;
}function _geraURL($str, $replace=array(), $delimiter='-') {
    setlocale(LC_ALL, 'en_US.UTF8');
    if( !empty($replace) ) {
        $str = str_replace((array)$replace, ' ', $str);
    }
 
    $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
    $clean = strtolower(trim($clean, '-'));
    $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
 
    return $clean;
}

function _gera_senha($tamanho, $maiuscula, $minuscula, $numeros, $codigos) {
    $maius = "ABCDEFGHIJKLMNOPQRSTUWXYZ";
    $minus = "abcdefghijklmnopqrstuwxyz";
    $numer = "0123456789";
    $codig = '!@#$%&*()-+.,;?{[}]^><:|';

    $base  = '';
    $base .= ($maiuscula) ? $maius : '';
    $base .= ($minuscula) ? $minus : '';
    $base .= ($numeros) ? $numer : '';
    $base .= ($codigos) ? $codig : '';

    srand((float) microtime() * 10000000);

    $senha = '';

    for ($i = 0; $i < $tamanho; $i++) {
            $senha .= substr($base, rand(0, strlen($base)-1), 1);
    }

    return $senha;
}

function limitarTexto($texto, $limite){
	$contador = strlen($texto);
	if ($contador >= $limite){      
		$texto = substr($texto, 0, strrpos(substr($texto, 0, $limite), ' ')) . '...';
		return $texto;
	}else{
		return $texto;
	}
}

function senhaValida($senha) {
    return preg_match('/[a-z]/', $senha) && preg_match('/[A-Z]/', $senha) && preg_match('/[0-9]/', $senha) && preg_match('/^[\w$@]{8,}$/', $senha);
}

function htmltransform($string = null){
	if(empty($string)){
		return null;
	}else{
		return htmlentities($string);
	}
}

?>