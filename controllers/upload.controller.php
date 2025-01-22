<?php
    class UploadController extends MainController
    {

        private $modeloUpload;
        private $modeloEmpresas;
        private $modeloPessoas;
        private $idElemento = null; // ID do elemento que será feito o upload
        public $db;

        // Caminho para a pasta de uploads:
        // define('UP_ABSPATH', ABSPATH . '/midia');

        public function __construct()
        {
            $parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();

            $hash = chk_array($parametros, 1);
            $this->idElemento = decryptHash($hash);

            if ($this->idElemento == null) {
                return json_encode(['error' => 'ID inválido']);
            }

            $this->db = new tix1DB();

            $this->modeloUpload = $this->load_model('utils/upload');
            $this->modeloPessoas = $this->load_model('pessoas/pessoas');
            $this->modeloEmpresas = $this->load_model('empresas/empresas');
        }

        // Método para gereciar o upload para pessoas
        public function pessoas()
        {
            $parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();
            $this->modeloPessoas->getPessoa($this->idElemento);
            $pessoa = json_decode(json_encode($this->modeloPessoas), true);
            $nomePessoa = $pessoa['form_data']['nome'];

            $baseDir = UP_ABSPATH . '/pessoas/' . $this->idElemento . '-' . $nomePessoa;

            if (chk_array($parametros, 0) == 'avatar') {
                $uploadDir = $baseDir . '/imagens/avatar';
        
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $this->modeloUpload->avatar('pessoa', $uploadDir);
            } else {
                if (!is_dir($baseDir)) {
                    mkdir($baseDir, 0777, true);
                }
                $this->modeloUpload->diversos('pessoa', $baseDir);
            }
        }

        // Método para gereciar o upload para empresas
        public function empresas()
        {
            $parametros = (func_num_args() >= 1) ? func_get_arg(0) : array();
            $this->modeloEmpresas->getEmpresa($this->idElemento);
            $empresa = json_decode(json_encode($this->modeloEmpresas), true);
            $nomeEmpresa = $empresa['form_data']['razaoSocial'];

            $baseDir = UP_ABSPATH . '/empresas/' . $this->idElemento . '-' . $nomeEmpresa;

            if (chk_array($parametros, 0) == 'avatar') {
                if (!is_dir($baseDir . '/imagens/avatar')) {
                    mkdir($baseDir . '/imagens/avatar', 0777, true);
                    mkdir($baseDir . '/imagens/avatar/thumb', 0777, true);
                }
                $this->modeloUpload->avatar('empresa', $baseDir . '/imagens/avatar');
            } else {
                if (!is_dir($baseDir)) {
                    mkdir($baseDir, 0777, true);
                }
                $this->modeloUpload->diversos('empresa', $baseDir);
            }
        }
    }
