<?php

function listMinioObjects($bucketName, $prefix = '', $recursive = true) {
    // Defina suas credenciais do MinIO
    $accessKey = 'I08awmT9UiIhDBfXK7QD';
    $secretKey = 'eWy3i1VN10l7MD1JxbY6aJIgYuw8DH8faKWHPfL4';

    // Endpoint e caminho do bucket
    $endpoint = 'http://localhost:9000';
    $region = 'us-east-1'; // Defina a região, pode ser genérico para MinIO

    // Cria o caminho do recurso
    $resource = "/{$bucketName}/";
    $queryString = $recursive ? 'list-type=2&prefix=' . urlencode($prefix) : 'list-type=2';
    $url = $endpoint . $resource . '?' . $queryString;

    // Define cabeçalhos e data
    $amzDate = gmdate('Ymd\THis\Z');
    $dateStamp = gmdate('Ymd');

    // Cabeçalho a ser enviado na requisição
    $headers = [
        'Host' => parse_url($endpoint, PHP_URL_HOST),
        'x-amz-content-sha256' => hash('sha256', ''),
        'x-amz-date' => $amzDate,
    ];

    // Cria a string canônica
    $canonicalHeaders = '';
    foreach ($headers as $key => $value) {
        $canonicalHeaders .= strtolower($key) . ':' . $value . "\n";
    }
    $signedHeaders = implode(';', array_map('strtolower', array_keys($headers)));

    $canonicalRequest = "GET\n" . $resource . "\n" . $queryString . "\n" . $canonicalHeaders . "\n" . $signedHeaders . "\n" . hash('sha256', '');

    // String para assinatura
    $algorithm = 'AWS4-HMAC-SHA256';
    $credentialScope = $dateStamp . '/' . $region . '/s3/aws4_request';
    $stringToSign = $algorithm . "\n" . $amzDate . "\n" . $credentialScope . "\n" . hash('sha256', $canonicalRequest);

    // Gera a assinatura
    $signingKey = getSignatureKey($secretKey, $dateStamp, $region, 's3');
    $signature = hash_hmac('sha256', $stringToSign, $signingKey);

    // Cria o cabeçalho de autorização
    $authorizationHeader = $algorithm . ' Credential=' . $accessKey . '/' . $credentialScope . ', SignedHeaders=' . $signedHeaders . ', Signature=' . $signature;
    $headers['Authorization'] = $authorizationHeader;

    // Prepara a requisição cURL
    $curl = curl_init($url);
    $curlHeaders = [];
    foreach ($headers as $key => $value) {
        $curlHeaders[] = $key . ': ' . $value;
    }

    curl_setopt($curl, CURLOPT_HTTPHEADER, $curlHeaders);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        echo 'Erro na requisição: ' . curl_error($curl);
    }
    curl_close($curl);

    // Exibe a resposta XML com os objetos do bucket
    echo "Resposta do MinIO:\n";
    echo $response;
}

// Função auxiliar para gerar a chave de assinatura
function getSignatureKey($key, $dateStamp, $regionName, $serviceName) {
    $kDate = hash_hmac('sha256', $dateStamp, 'AWS4' . $key, true);
    $kRegion = hash_hmac('sha256', $regionName, $kDate, true);
    $kService = hash_hmac('sha256', $serviceName, $kRegion, true);
    $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);
    return $kSigning;
}

// Testa a listagem dos objetos no bucket 'mybucket' com o prefixo 'myprefix'
listMinioObjects('credeapp', '');

?>
