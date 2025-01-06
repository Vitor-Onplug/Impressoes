<?php
class UploadImage {
	private $thumb;
	private $main_path;
	private $thumbnail_path;
	private $delete;
	private $rotate;
	private $degree_lvl;
	private $filename;
	private $cyr;
	private $lat;
	private $bytes;
	private $imgsize;
	private $resize_to;
	private $thumbnail_size;
	private $watermark;	
	private $orakuploader_crop_to_width;	
	private $orakuploader_crop_to_height;	
	private $orakuploader_crop_thumb_to_width;	
	private $orakuploader_crop_thumb_to_height;	
	
	public function __construct(){
		$this->main_path = isset($_REQUEST['main_path']) ? $_REQUEST['main_path'] : null; 
		$this->thumbnail_path = isset($_REQUEST['thumbnail_path']) ? $_REQUEST['thumbnail_path'] : null; 
		$this->delete = isset($_GET['delete']) ? $_GET['delete'] : null;
		$this->rotate = isset($_GET['rotate']) ? $_GET['rotate'] : null;
		$this->degree_lvl = isset($_GET['degree_lvl']) ? $_GET['degree_lvl'] : null;
		$this->filename = isset($_GET['filename']) ? $_GET['filename'] : null;
		$this->resize_to = isset($_REQUEST["resize_to"]) ? $_REQUEST["resize_to"] : null;
		$this->thumbnail_size = isset($_REQUEST["thumbnail_size"]) ? $_REQUEST["thumbnail_size"] : null;
		$this->watermark = isset($_REQUEST["watermark"]) ? $_REQUEST["watermark"] : null;
		$this->orakuploader_crop_to_width = isset($_REQUEST['orakuploader_crop_to_width']) ? $_REQUEST['orakuploader_crop_to_width'] : null;
		$this->orakuploader_crop_to_height = isset($_REQUEST["orakuploader_crop_to_height"]) ? $_REQUEST["orakuploader_crop_to_height"] : null;
		$this->orakuploader_crop_thumb_to_width = isset($_REQUEST["orakuploader_crop_thumb_to_width"]) ? $_REQUEST["orakuploader_crop_thumb_to_width"] : null;
		$this->orakuploader_crop_thumb_to_height = isset($_REQUEST["orakuploader_crop_thumb_to_height"]) ? $_REQUEST["orakuploader_crop_thumb_to_height"] : null;
		
		if(!isset($this->main_path) && !isset($this->thumbnail_path)){
			return;
		}
		
		@mkdir($this->main_path, 0777, true);
		@mkdir($this->thumbnail_path, 0777, true);

		if(isset($this->delete)){
			
			unlink($this->main_path . "/" . $this->delete);
			unlink($this->thumbnail_path . "/" . $this->delete);
			
			if(file_exists($this->main_path . "/cache/" . $this->delete)){
				unlink($this->main_path . "/cache/" . $this->delete);
			}
			
			if(file_exists($this->thumbnail_path . "/cache/" . $this->delete)){
				unlink($this->thumbnail_path . "/cache/" . $this->delete);
			}
			
			return;
			
		}elseif(isset($this->rotate)){
			rotateImage($this->rotate, $this->main_path, $this->degree_lvl);
			rotateImage($this->rotate, $this->thumbnail_path, $this->degree_lvl);
			
			echo $this->rotate;
			
			return;
		}

		$info = pathinfo($this->filename);
		$this->filename = $this->filename . '_' . date('Y-m-d_H-i-s') . '.' . $info['extension'];
		$this->filename = preg_replace("#\\s+#", "_", $this->filename);

		$this->cyr = array(
		'ж',  'ч',  'щ',   'ш',  'ю',  'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ь', 'я',
		'Ж',  'Ч',  'Щ',   'Ш',  'Ю',  'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ь', 'Я');
		
		$this->lat = array(
		"l", "s",
		'zh', 'ch', 'sht', 'sh', 'yu', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'y', 'x', 'q',
		'Zh', 'Ch', 'Sht', 'Sh', 'Yu', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c', 'Y', 'X', 'Q');

		$this->filename = str_replace($this->cyr, $this->lat, $this->filename);
		$this->filename = $this->normalizeChars($this->filename);

		$this->bytes = file_put_contents(
			$this->main_path . '/' . ($this->filename),
			file_get_contents('php://input')
		);

		$this->imgsize = @getimagesize($this->main_path .'/'. $this->filename);

		if(!isset($this->imgsize) || !isset($this->imgsize['mime']) || !in_array($this->imgsize['mime'], array('image/jpeg', 'image/png'))){
			unlink($this->main_path .'/'. ($this->filename));
			return;
		}

		if($this->resize_to > 0)
		{
			$width = $this->imgsize[0];
			$height = $this->imgsize[1];
			if($width > $this->resize_to) createThumbnail($this->main_path, $this->filename, $this->main_path, $this->resize_to, 100);
		}

		if($this->bytes > 8) {
			if((int)$this->thumbnail_size > 0){
				$this->createThumbnail($this->main_path, $this->filename, $this->thumbnail_path, $this->thumbnail_size, 100);
			}
		}else{
			return;
		}
		
		if(isset($this->watermark) && $this->watermark != ''){
			addWatermark($this->watermark, $this->main_path, $this->filename);
			addWatermark($this->watermark, $this->thumbnail_path, $this->filename);
		}
		
		$crop_to_width = isset($this->orakuploader_crop_to_width) ? (int) $this->orakuploader_crop_to_width : 0;
		$crop_to_height = isset($this->orakuploader_crop_to_height) ? (int) $this->orakuploader_crop_to_height : 0;

		$crop_thumb_to_width = isset($this->orakuploader_crop_thumb_to_width) ? (int) $this->orakuploader_crop_thumb_to_width : 0;
		$crop_thumb_to_height = isset($this->orakuploader_crop_thumb_to_height) ? (int) $this->orakuploader_crop_thumb_to_height : 0;

		if($crop_thumb_to_width > 0 && $crop_thumb_to_height > 0){
			crop($crop_thumb_to_width, $crop_thumb_to_height, $this->main_path . '/' . $this->filename, $this->thumbnail_path .'/'. $this->filename);
		}

		if($crop_to_width > 0 && $crop_to_height > 0){
			crop($crop_to_width, $crop_to_height, $this->main_path .'/'. $this->filename, $this->main_path .'/'. $this->filename);
		}
		
		echo $this->filename;
	}
	
	function createThumbnail($imageDirectory, $imageName, $thumbDirectory, $thumbWidth, $quality = 100){
		$image_extension = @end(explode(".", $imageName));
		
		switch($image_extension){
			case "jpg": 
				@$srcImg = imagecreatefromjpeg("$imageDirectory/$imageName");
				break;
			case "jpeg":
				@$srcImg = imagecreatefromjpeg("$imageDirectory/$imageName");
				break;
			case "png":
				$srcImg = imagecreatefrompng("$imageDirectory/$imageName");
				break;
		}
		
		if(!$srcImg){
			return;
		}
		
		$origWidth = imagesx($srcImg);
		$origHeight = imagesy($srcImg);
		$ratio = $origHeight/ $origWidth;
		$thumbHeight = $thumbWidth * $ratio;

		$thumbImg = imagecreatetruecolor($thumbWidth, $thumbHeight);
		
		if($image_extension == 'png'){
			$background = imagecolorallocate($thumbImg, 0, 0, 0);
			imagecolortransparent($thumbImg, $background);
			imagealphablending($thumbImg, false);
			imagesavealpha($thumbImg, true);
		}
		
		imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $origWidth, $origHeight);

		switch($image_extension){
			case "jpg": 
				imagejpeg($thumbImg, "$thumbDirectory/$imageName", $quality);
				break;
			case "jpeg":
				imagejpeg($thumbImg, "$thumbDirectory/$imageName", $quality);
				break;
			case "png":
				imagepng($thumbImg, "$thumbDirectory/$imageName");
				break;
		}

	}

	function normalizeChars($s = null){
		if(empty($s)){
			return;
		}
		
		$replace = array(
			'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'Ae', 'Å'=>'A', 'Æ'=>'A', 'Ă'=>'A', 'Ą' => 'A', 'ą' => 'a',
			'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'ae', 'å'=>'a', 'ă'=>'a', 'æ'=>'ae',
			'þ'=>'b', 'Þ'=>'B',
			'Ç'=>'C', 'ç'=>'c', 'Ć' => 'C', 'ć' => 'c',
			'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ę' => 'E', 'ę' => 'e',
			'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 
			'Ğ'=>'G', 'ğ'=>'g',
			'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'İ'=>'I', 'ı'=>'i', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
			'Ł' => 'L', 'ł' => 'l',
			'Ñ'=>'N', 'Ń' => 'N', 'ń' => 'n',
			'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'Oe', 'Ø'=>'O', 'ö'=>'oe', 'ø'=>'o',
			'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
			'Š'=>'S', 'š'=>'s', 'Ş'=>'S', 'ș'=>'s', 'Ș'=>'S', 'ş'=>'s', 'ß'=>'ss', 'Ś' => 'S', 'ś' => 's',
			'ț'=>'t', 'Ț'=>'T',
			'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'Ue',
			'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'ue', 
			'Ý'=>'Y',
			'ý'=>'y', 'ý'=>'y', 'ÿ'=>'y',
			'Ž'=>'Z', 'ž'=>'z', 'Ż' => 'Z', 'ż' => 'z', 'Ź' => 'Z', 'ź' => 'z'
		);
		
		return strtr($s, $replace);
	}

	function rotateImage($image_name, $path, $degree_lvl){
		if($degree_lvl == 4){
			unlink($path ."/". $image_name);
			rename($path ."/cache/" . $image_name, $path ."/". $image_name);
			return $image_name;
		}
		
		if(!file_exists($path . "/cache/" . $image_name)) {
			@mkdir($path . "/cache", 0777);
			copy($path . "/" . $image_name, $path . "/cache/" . $image_name);
			unlink($path . "/" . $image_name);
		}
			
		$image_extension = @end(explode(".", $image_name));
		
		switch($image_extension){
			case "jpg": 
				@$image = imagecreatefromjpeg($path . "/cache/" . $image_name);
				break;
			case "jpeg":
				@$image = imagecreatefromjpeg($path . "/cache/" . $image_name);
				break;
			case "png":
				$image = imagecreatefrompng($path . "/cache/" . $image_name);
				break;
		}
		
		$transColor = imagecolorallocatealpha($image, 255, 255, 255, 270);
		$rotated_image = imagerotate($image, -90*$degree_lvl, $transColor);
		
		
		switch($image_extension){
			case "jpg": 
				header('Content-type: image/jpeg');
				imagejpeg($rotated_image, "$path/$image_name", 100);
				break;
			case "jpeg":
				header('Content-type: image/jpeg');
				imagejpeg($rotated_image, "$path/$image_name", 100);
				break;
			case "png":
				header('Content-type: image/png');
				imagepng($rotated_image, "$path/$image_name");
				break;
		}
		return $image_name;
	}


	function addWatermark($watermark, $imageDirectory, $imageName, $x = 0, $y = 0){
		if(file_exists($watermark))
		{
			$marge_right  = 0;
			$marge_bottom = 0;	

			$stamp = imagecreatefrompng($watermark);

			$image_extension = @end(explode(".", $imageName));
			switch($image_extension) 
			{
				case "jpg": 
					$im = imagecreatefromjpeg("$imageDirectory/$imageName");
					break;
				case "jpeg":
					$im = imagecreatefromjpeg("$imageDirectory/$imageName");
					break;
				case "png":
					$im = imagecreatefrompng("$imageDirectory/$imageName");
					break;
			}

			$imageSize = getimagesize("$imageDirectory/$imageName");
			$watermark_o_width = imagesx($stamp);
			$watermark_o_height = imagesy($stamp);

			$newWatermarkWidth = $imageSize[0];
			$newWatermarkHeight = $watermark_o_height * $newWatermarkWidth / $watermark_o_width;

			
			if((int)$x <= 0)
				$x = $imageSize[0]/2 - $newWatermarkWidth/2;
			if((int)$y <= 0)
				$y = $imageSize[1]/2 - $newWatermarkHeight/2;
			
			imagecopyresized($im, $stamp, $x, $y, 0, 0, $newWatermarkWidth, $newWatermarkHeight, imagesx($stamp), imagesy($stamp));

			switch($image_extension) 
			{
				case "jpg": 
					header('Content-type: image/jpeg');
					imagejpeg($im, "$imageDirectory/$imageName", 100);
					break;
				case "jpeg":
					header('Content-type: image/jpeg');
					imagejpeg($im, "$imageDirectory/$imageName", 100);
					break;
				case "png":
					header('Content-type: image/png');
					imagepng($im, "$imageDirectory/$imageName");
					break;
			}
		}
	}

	function crop($max_width, $max_height, $source_file, $dst_dir){
		$imgsize = getimagesize($source_file);
		$width = $imgsize[0];
		$height = $imgsize[1];
		$mime = $imgsize['mime'];
	 
		switch($mime){
			case 'image/png':
				$image_create = "imagecreatefrompng";
				$image = "imagepng";
				break;
	 
			case 'image/jpeg':
				$image_create = "imagecreatefromjpeg";
				$image = "imagejpeg";
				break;
	 
			default:
				return false;
				break;
		}
		 
		$dst_img = imagecreatetruecolor($max_width, $max_height);

		if($mime == 'image/png')
		{
			$background = imagecolorallocate($dst_img, 0, 0, 0);
			imagecolortransparent($dst_img, $background);
			imagealphablending($dst_img, false);
			imagesavealpha($dst_img, true);
		}	
		
		$src_img = $image_create($source_file);
		 
		$width_new = $height * $max_width / $max_height;
		$height_new = $width * $max_height / $max_width;
		
		if($width_new > $width){
			$h_point = (($height - $height_new) / 2);
			imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
		} else{
			$w_point = (($width - $width_new) / 2);
			imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
		}
		
		if($mime == 'image/jpeg')
			$image($dst_img, $dst_dir, 100);
		else
			$image($dst_img, $dst_dir);
	 
		if($dst_img)imagedestroy($dst_img);
		if($src_img)imagedestroy($src_img);
	}
}