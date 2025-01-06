<?php
class UploadModel extends MainModel
{
    public $form_data;
    public $form_msg;
    public $db;
    private $uploadHandler;
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

    // Garantir que os diretórios existam
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0777, true);
    }
    if (!is_dir($diretorio . '/thumb')) {
        mkdir($diretorio . '/thumb', 0777, true);
    }

    $options = [
        'upload_dir' => $diretorio . '/',
        'upload_url' => str_replace(ABSPATH, HOME_URI, $diretorio) . '/',
        'param_name' => 'midia',
        'image_versions' => [
            // Versão original
            '' => [
                'auto_orient' => true,
                'max_width' => 1920,
                'max_height' => 1080,
                'jpeg_quality' => 95
            ],
            // Versão thumbnail
            'thumbnail' => [
                'crop' => false,
                'max_width' => 150,
                'max_height' => 150,
                'jpeg_quality' => 80,
                'upload_dir' => $diretorio . '/thumb/',
                'upload_url' => str_replace(ABSPATH, HOME_URI, $diretorio) . '/thumb/'
            ]
        ],
        'accept_file_types' => '/\.(gif|jpe?g|png)$/i',
        'max_file_size' => 5 * 1024 * 1024, // 5MB
        'image_library' => 0, // 0 = GD, 1 = Imagick
        'image_file_types' => '/\.(gif|jpe?g|png)$/i',
        'print_response' => true,
        'mkdir_mode' => 0777,
        'orient_image' => true
    ];

    try {
        // Verificar se os diretórios têm permissão de escrita
        if (!is_writable($diretorio)) {
            error_log("Diretório não tem permissão de escrita: " . $diretorio);

        }

        if (!is_writable($diretorio . '/thumb')) {
            error_log("Diretório thumb não tem permissão de escrita: " . $diretorio . '/thumb');

        }

        // Verificar extensões do PHP necessárias
        if (!extension_loaded('gd')) {
            error_log("Extensão GD não está carregada");

        }

        $this->uploadHandler = new UploadHandler($options);
    } catch (Exception $e) {
        error_log("Erro no upload: " . $e->getMessage());
        return json_encode(['error' => $e->getMessage()]);
    }
}
    public function diversos($tipo = null, $diretorio = null)
    {
        if (!$tipo || !$diretorio) {
            return json_encode(['error' => 'Parâmetros inválidos']);
        }

        $options = [
            'upload_dir' => $diretorio . '/',
            'upload_url' => HOME_URI . str_replace(ABSPATH, '', $diretorio) . '/',
            'accept_file_types' => '/\.(gif|jpe?g|png|pdf|doc|docx|xls|xlsx)$/i',
            'max_file_size' => 10 * 1024 * 1024, // 10MB
            'param_name' => 'midia',
            'image_versions' => [] // Sem versões de imagem para diversos
        ];

        $this->uploadHandler = new UploadHandler($options);
        return;
    }
}
