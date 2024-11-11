<?php 
class UtilsModel extends MainModel {
	public $form_data;
	public $form_msg;
	public $db;
	
	public function __construct($db = false, $controller = null){
		$this->db = $db;
		
		$this->controller = $controller;
		
		$this->parametros = $this->controller->parametros;

		$this->userdata = $this->controller->userdata;
	}

	public function buscaCep(){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep=" . $_REQUEST['cep'] );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$resultado = curl_exec($ch);
		
		curl_close($ch);
		
		
		return $resultado;
	}
	
	public function thumbnail($image = null, $width = null, $height = null){
		if(empty($image) && empty($width) && empty($height)){
			return;
		}

		if(preg_match('%^(http|ftp)%simx', $image)){
			return;
		}
		
		$newImage = NULL;
		$imageTrueColor = NULL;

		if(preg_match("/\.(jpg|jpeg|gif|png){1}$/i", $image)){
			if(preg_match("/\.(jpg|jpeg){1}$/i", $image)){
				header("Content-type: image/jpeg");
				
				$fileImage = ImageCreateFromJPEG($image);
			}elseif(preg_match("/\.(gif){1}$/i", $image)){
				header("Content-type: image/gif");
				
				$fileImage = ImageCreateFromGIF($image);
			}elseif(preg_match("/\.(png){1}$/i", $image)){
				header("Content-type: image/png");
				
				$fileImage = ImageCreateFromPNG($image);
			}
			
			$w = imagesx($fileImage);
			$h = imagesy($fileImage);

			$w1 = $w / $width;
			
			if ($height == 0){
				$h1 = $w1;
				$height = $h / $w1;
			}else{
				$h1 = $h / $height;
			}

			$min = min($w1, $h1);  
		  
			$xt = $min * $width;
			$x1 = ($w - $xt) / 2;
			$x2 = $w - $x1;	  

			$yt = $min * $height;
			$y1 = ($h - $yt) / 2;
			$y2 = $h - $y1;	  

			$x1 = (int) $x1;
			$x2 = (int) $x2;
			$y1 = (int) $y1;
			$y2 = (int) $y2;				
			
			$imageTrueColor = ImageCreateTrueColor($width, $height); 

			$imageColor  = ImageColorAllocate($imageTrueColor, 255, 255, 255); 
			 
			for($i = 0; $i <= $height; $i++){
				ImageLine($imageTrueColor, 0, $i, $width, $i, $imageColor);
			}
			  
			ImageCopyResampled($imageTrueColor, $fileImage, 0, 0, $x1, $y1, $width, $height, $x2-$x1, $y2-$y1);	
			ImageJPEG($imageTrueColor, $newImage, 100 );
		}else{
			return;
		}
	}
	
}