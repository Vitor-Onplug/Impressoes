<?php
class UploadModel extends MainModel
{
    public $form_data;
    public $form_msg;
    public $db;

    private $erro;

    private $ClasseUploadFile;

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;

        $this->controller = $controller;

        $this->parametros = $this->controller->parametros;

        $this->userdata = $this->controller->userdata;
    }

    public function avatar($tipo = null, $diretorio = null)
    {
        if (!$tipo || !$diretorio) {
            return json_encode(['error' => 'Parâmetros inválidos']);
        }

        // Validar e criar diretório se não existir
        if (!file_exists($diretorio)) {
            if (!mkdir($diretorio, 0777, true)) {
                return json_encode(['error' => 'Erro ao criar diretório']);
            }
        }

        // Validar se é uma imagem permitida
        if (!$this->validarArquivoImagem()) {
            return json_encode(['error' => 'Arquivo não é uma imagem válida']);
        }

        if (!isset($_FILES['midia'])) {
            return json_encode(['error' => 'Nenhum arquivo enviado']);
        }

        $file = $_FILES['midia'];

        // Configurar parâmetros para o UploadImage
        $_REQUEST['main_path'] = $diretorio;
        $_REQUEST['thumbnail_path'] = $diretorio . '/thumb';
        $_REQUEST['resize_to'] = 800;
        $_REQUEST['thumbnail_size'] = 150;
        $_GET['filename'] = $file['name']; // Nome do arquivo original

        // Criar o conteúdo no input
        if (!@file_put_contents('php://input', file_get_contents($file['tmp_name']))) {
            return json_encode(['error' => 'Erro ao processar arquivo']);
        }

        // Instanciar classe de upload
        $uploadImage = new UploadImage();

        // Verificar se o arquivo foi criado
        $fullPath = $diretorio . '/' . $uploadImage->getFilename();
        if (!file_exists($fullPath)) {
            return json_encode(['error' => 'Erro ao salvar arquivo']);
        }

        return json_encode([
            'success' => true,
            'filename' => $uploadImage->getFilename(),
            'path' => $fullPath,
            'thumb' => $diretorio . '/thumb/' . $uploadImage->getFilename()
        ]);
    }

    // Adicionar este método getter na classe UploadImage
    public function getFilename()
    {
        return $this->filename;
    }

    private function validarArquivoImagem()
    {
        if (!isset($_FILES["midia"])) {
            return false;
        }

        $allowedTypes = ['image/jpeg', 'image/png'];

        if (!in_array($_FILES["midia"]["type"], $allowedTypes)) {
            return false;
        }

        return true;
    }

    public function diversos($tipo = null, $diretorio = null)
    {
        if (!$tipo || !$diretorio) {
            return json_encode(['error' => 'Parâmetros inválidos']);
        }

        // Validar e criar diretório se não existir
        if (!file_exists($diretorio)) {
            if (!mkdir($diretorio, 0777, true)) {
                return json_encode(['error' => 'Erro ao criar diretório']);
            }
        }

        // Instanciar classe de upload
        $this->ClasseUploadFile = new UploadFile($tipo, $diretorio, 'upload');

        // Validar tipos de arquivos permitidos
        if (!$this->validarArquivos()) {
            return json_encode(['error' => 'Tipo de arquivo não permitido']);
        }

        // Realizar upload
        $resultado = $this->ClasseUploadFile->load();

        // Registrar upload no banco de dados
        // if ($resultado) {
        //     $this->registrarUpload(json_decode($resultado, true));
        // }

        return $resultado;
    }

    private function validarArquivos()
    {
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];

        if (!isset($_FILES["midia"])) {
            return false;
        }

        if (is_array($_FILES["midia"]["name"])) {
            foreach ($_FILES["midia"]["name"] as $filename) {
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                if (!in_array($ext, $extensoesPermitidas)) {
                    return false;
                }
            }
        } else {
            $ext = strtolower(pathinfo($_FILES["midia"]["name"], PATHINFO_EXTENSION));
            if (!in_array($ext, $extensoesPermitidas)) {
                return false;
            }
        }

        return true;
    }

    private function registrarUpload($arquivos)
    {
        if (!is_array($arquivos)) {
            return false;
        }

        foreach ($arquivos as $arquivo) {
            $dados = [
                'nome_arquivo' => $arquivo,
                'tipo' => $this->ClasseUploadFile->getType(),
                'data_upload' => date('Y-m-d H:i:s'),
                'usuario_id' => $this->userdata['user_id']
            ];

            // Inserir no banco de dados
            $this->db->insert('uploads', $dados);
        }
    }
}
