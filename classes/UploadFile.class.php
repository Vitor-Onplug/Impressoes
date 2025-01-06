<?php
class UploadFile
{
	private $type = null;
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

	private $maxFileSize = 5242880; // 5MB

	public function __construct($tipo = null, $outputDir = null, $option = null)
	{
		$this->type = $tipo;
		$this->outputDir = $outputDir;
		$this->option = $option;
	}

	public function load()
	{
		if ($this->option == "upload") {
			return $this->upload();
		} elseif ($this->option == "delete") {
			return $this->delete();
		} elseif ($this->option == "download") {
			return $this->download();
		} else {
			return $this->loadFiles();
		}
	}

	private function upload()
	{
		if (!isset($_FILES["midia"])) {
			return json_encode(['error' => 'Nenhum arquivo enviado']);
		}

		$this->ret = array();

		if (!is_dir($this->outputDir)) {
			if (!@mkdir($this->outputDir, 0777, true)) {
				return json_encode(['error' => 'Erro ao criar diretório']);
			}
		}

		if (!is_array($_FILES["midia"]["name"])) {
			$this->processaSingleUpload();
		} else {
			$this->processaMultiUpload();
		}

		return json_encode($this->ret);
	}

	private function processaSingleUpload()
	{
		if ($_FILES["midia"]["size"] > $this->maxFileSize) {
			$this->ret['error'] = 'Arquivo muito grande';
			return;
		}

		$this->fileName = $this->sanitizeFileName($_FILES["midia"]["name"]);
		$destino = $this->outputDir . "/" . $this->fileName;

		if (move_uploaded_file($_FILES["midia"]["tmp_name"], $destino)) {
			$this->ret[] = $this->fileName;
		}
	}

	private function processaMultiUpload()
	{
		$this->fileCount = count($_FILES["midia"]["name"]);

		for ($i = 0; $i < $this->fileCount; $i++) {
			if ($_FILES["midia"]["size"][$i] > $this->maxFileSize) {
				continue;
			}

			$this->fileName = $this->sanitizeFileName($_FILES["midia"]["name"][$i]);
			$destino = $this->outputDir . "/" . $this->fileName;

			if (move_uploaded_file($_FILES["midia"]["tmp_name"][$i], $destino)) {
				$this->ret[] = $this->fileName;
			}
		}
	}

	private function sanitizeFileName($filename)
	{
		// Remove caracteres especiais e acentos
		$filename = preg_replace('/[áàãâä]/ui', 'a', $filename);
		$filename = preg_replace('/[éèêë]/ui', 'e', $filename);
		$filename = preg_replace('/[íìîï]/ui', 'i', $filename);
		$filename = preg_replace('/[óòõôö]/ui', 'o', $filename);
		$filename = preg_replace('/[úùûü]/ui', 'u', $filename);
		$filename = preg_replace('/[ç]/ui', 'c', $filename);

		// Remove outros caracteres especiais
		$filename = preg_replace('/[^a-zA-Z0-9\s._-]/', '', $filename);

		// Substitui espaços por underline
		$filename = str_replace(' ', '_', $filename);

		// Adiciona timestamp para evitar sobrescrita
		$info = pathinfo($filename);
		return $info['filename'] . '_' . date('Y-m-d_H-i-s') . '.' . $info['extension'];
	}

	private function loadFiles()
	{
		if (is_dir($this->outputDir)) {
			$this->files = scandir($this->outputDir);
		} else {
			return null;
		}

		$this->ret = array();
		foreach ($this->files as $this->file) {
			if ($this->file == "." || $this->file == "..") {
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

	private function download()
	{
		if (isset($_GET['filename'])) {
			$this->fileName = $_GET['filename'];
			$this->fileName = str_replace("..", ".", $this->fileName);

			$this->file = $this->outputDir . "/" . $this->fileName;
			$this->file = str_replace("..", "", $this->file);

			if (file_exists($this->file)) {
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

	private function delete()
	{
		if (isset($_POST['name'])) {
			$this->fileName = $_POST['name'];
			$this->fileName = str_replace("..", ".", $this->fileName);
			$this->filePath = $this->outputDir . "/" . $this->fileName;
			if (file_exists($this->filePath)) {
				unlink($this->filePath);
			}
		}

		return null;
	}
}
