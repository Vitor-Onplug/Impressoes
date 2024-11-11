<?php

// Função para gerar a chave de assinatura (como exemplo)
function getSignatureKey($key, $date, $region, $service) {
    $kDate = hash_hmac('sha256', $date, 'AWS4' . $key, true);
    $kRegion = hash_hmac('sha256', $region, $kDate, true);
    $kService = hash_hmac('sha256', $service, $kRegion, true);
    $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);
    return $kSigning;
}

// Função para gerar um nome de arquivo único baseado no tipo e na entidade
function gerarNomeArquivo($tipo, $id, $nomeEntidade, $isEmpresa = false, $log = '') {
    $prefixo = '';

    // Definir prefixo baseado no tipo
    switch ($tipo) {
        case 'foto':
            $prefixo = 'PHO';
            break;
        case 'documento':
            $prefixo = 'DOC';
            break;
        case 'diverso':
            $prefixo = 'DIV';
            break;
        default:
            $prefixo = 'ARQ'; // Caso não seja um dos tipos esperados
    }

    // Definir o tipo de entidade
    $entidadeTipo = $isEmpresa ? 'EMP' : 'PES';

    // Definir se o arquivo é um log (opcional)
    switch ($log) {
        case 'log':
            $sufixo = '_LOG';
            break;
        default:
            $sufixo = '';
    }

    // Limpar o nome da entidade removendo caracteres especiais
    $nomeEntidade = preg_replace('/[^a-zA-Z0-9]/', '', $nomeEntidade);

    // Gerar timestamp legível
    $timestamp = date('Ymd_His'); // Exemplo: 20230924_143210

    // Combinar as partes para criar o nome do arquivo
    $fileName = "{$prefixo}_{$entidadeTipo}_{$id}_{$timestamp}{$sufixo}";

    return $fileName;
}

// Função genérica para salvar arquivos no MinIO
function salvarArquivo($fileContents, $bucketName, $folderPath, $fileName) {
    $minioHost = MINIOHOST;
    $accessKey = MINIOACCESSKEY;
    $secretKey = MINIOSECRETKEY;
    $bucketName = MINIOBUCKET;

    foreach ($_FILES['files']['tmp_name'] as $index => $tmpFilePath) {
        if ($_FILES['files']['error'][$index] === UPLOAD_ERR_OK) {
            $fileContents = file_get_contents($tmpFilePath);
            $fileName = $_FILES['files']['name'][$index];

            // Gerar data e horário
            $region = 'us-east-1';
            $service = 's3';
            $algorithm = 'AWS4-HMAC-SHA256';
            $amzDate = gmdate('Ymd\THis\Z');
            $shortDate = gmdate('Ymd');
            $payloadHash = hash('sha256', $fileContents);

            // URI canônico
            $canonicalUri = "/$bucketName/$fileName";
            $canonicalHeaders = "host:$minioHost\nx-amz-content-sha256:$payloadHash\nx-amz-date:$amzDate\n";
            $signedHeaders = "host;x-amz-content-sha256;x-amz-date";
            $canonicalRequest = "PUT\n$canonicalUri\n\n$canonicalHeaders\n$signedHeaders\n$payloadHash";
            $hashedCanonicalRequest = hash('sha256', $canonicalRequest);

            // Criar a string a ser assinada
            $credentialScope = "$shortDate/$region/$service/aws4_request";
            $stringToSign = "$algorithm\n$amzDate\n$credentialScope\n$hashedCanonicalRequest";

            // Função para gerar a assinatura
            function getSignatureKey($key, $date, $region, $service) {
                $kDate = hash_hmac('sha256', $date, 'AWS4' . $key, true);
                $kRegion = hash_hmac('sha256', $region, $kDate, true);
                $kService = hash_hmac('sha256', $service, $kRegion, true);
                $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);
                return $kSigning;
            }

            // Gerar a assinatura
            $signingKey = getSignatureKey($secretKey, $shortDate, $region, $service);
            $signature = hash_hmac('sha256', $stringToSign, $signingKey);

            // Cabeçalho de autorização final
            $authorizationHeader = "$algorithm Credential=$accessKey/$credentialScope, SignedHeaders=$signedHeaders, Signature=$signature";

            // Cabeçalhos
            $headers = [
                "Host: $minioHost",
                "x-amz-content-sha256: $payloadHash",
                "x-amz-date: $amzDate",
                "Authorization: $authorizationHeader",
                'Content-Type: ' . $_FILES['files']['type'][$index], // Usar o tipo MIME correto
            ];

            // Inicializar cURL para envio do arquivo
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "http://$minioHost/$bucketName/$fileName",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => $fileContents,
            ]);

            // Executar e verificar o resultado
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($httpCode == 200) {
                echo "Upload de $fileName bem-sucedido!\n";
            } else {
                echo "Erro no upload de $fileName: $response\n";
            }

            curl_close($curl);
        }
    }
}

// Função para salvar arquivos diversos de pessoa
function salvarArquivoDiversoPessoa($fileContents, $fileName, $bucketName, $idPessoa, $nomePessoa) {
    // Criar caminho da pasta para salvar o arquivo
    $folderPath = "pessoas/$nomePessoa/diversos";
    return salvarArquivo($fileContents, $bucketName, $folderPath, $fileName);
}
