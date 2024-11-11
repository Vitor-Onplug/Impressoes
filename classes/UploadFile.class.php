<?php
class UploadFile {
	private $option = null;
	private $outputDir = null;
	private $ret = null;
	private $error = null;
	private $fileCount = null;
	private $fileName = null;
	private $file = null;
	private $files = null;
	private $filePath = null;
	private $details = null;
	
	public function __construct($outputDir = null, $option = null){
		$this->outputDir = $outputDir;
		$this->option = $option;
	}
	
	public function load(){
		if($this->option == "upload"){
			return $this->upload();
		}elseif($this->option == "delete"){
			return $this->delete();
		}elseif($this->option == "download"){
			return $this->download();
		}else{
			return $this->loadFiles();
		}
	}
	
	private function upload(){
		if(isset($_FILES["midia"])){
			$this->ret = array();
			
			$error = $_FILES["midia"]["error"];
			
			@mkdir($this->outputDir, 0777, true);

			if(!is_array($_FILES["midia"]["name"])){
				$this->fileName = $_FILES["midia"]["name"];
				move_uploaded_file($_FILES["midia"]["tmp_name"], $this->outputDir . "/" . $this->fileName);
				
				$this->ret[] = $this->fileName;
			}else{
			  $this->fileCount = count($_FILES["midia"]["name"]);
			  for($i = 0; $i < $this->fileCount; $i++){
				$this->fileName = $_FILES["midia"]["name"][$i];
				
				move_uploaded_file($_FILES["midia"]["tmp_name"][$i], $this->outputDir . "/" . $this->fileName);
				
				$this->ret[] = $this->fileName;
			  }
			}
			
			return json_encode($this->ret);
		 }else{
			 return null;
		 }
	}
	
	private function loadFiles(){
		if(is_dir($this->outputDir)){
			$this->files = scandir($this->outputDir);
		}else{
			return null;
		}
		
		$this->ret = array();
		foreach($this->files as $this->file){
			if($this->file == "." || $this->file == ".."){
				continue;
			}
			
			$this->filePath = $this->outputDir . "/" . $this->file;
			$this->details = array();
			$this->details['name'] = $this->file;
			$this->details['path'] = $this->filePath;
			$this->details['size'] = filesize($this->filePath);
			
			$this->ret[] = $this->details;

		}

		return json_encode($this->ret);
	}
	
	private function download(){
		if(isset($_GET['filename'])){
			$this->fileName = $_GET['filename'];
			$this->fileName = str_replace("..", ".", $this->fileName);
			
			$this->file = $this->outputDir . "/" . $this->fileName;
			$this->file = str_replace("..", "", $this->file);
			
			if(file_exists($this->file)){
				$this->fileName = str_replace(" ", "", $this->fileName);
				
				header('Content-Description: File Transfer');
				header('Content-Disposition: attachment; filename=' . $this->fileName);
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($this->file));
				
				ob_clean();
				flush();
				
				readfile($this->file);

			}
		}
		
		return null;
	}
	
	private function delete(){
		if(isset($_POST['name'])){
			$this->fileName = $_POST['name'];
			$this->fileName = str_replace("..", ".", $this->fileName);
			$this->filePath = $this->outputDir . "/" . $this->fileName;
			if(file_exists($this->filePath)){
				unlink($this->filePath);
			}
		}
		
		return null;
	}
}