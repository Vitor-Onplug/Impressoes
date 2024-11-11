<?php 
if(!defined('ABSPATH')) exit; 

if(!empty($_GET['imagem']) && !empty($_GET['width']) && !empty($_GET['height'])){
	$modelo->thumbnail($_GET['imagem'], $_GET['width'], $_GET['height']);
}
?>