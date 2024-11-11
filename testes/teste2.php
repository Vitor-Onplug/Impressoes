<?php
// Defina as configurações do MinIO e o objeto a ser acessado
$bucketName = 'credeapp';
$objectName = 'teste.jpg';
$minioEndpoint = 'http://127.0.0.1:9000'; // URL do MinIO

$accessKey = 'I08awmT9UiIhDBfXK7QD';
$secretKey = 'eWy3i1VN10l7MD1JxbY6aJIgYuw8DH8faKWHPfL4';

// Região genérica para MinIO
$region = 'us-east-1';

// Caminho completo do recurso no MinIO
$resource = "/{$bucketName}/{$objectName}";

// Data e hora para a requisição (em formato ISO8601 para `x-amz-date`)
$amzDate = gmdate('Ymd\THis\Z');
$dateStamp = gmdate('Ymd'); // Data no formato Ymd para a assinatura

// Cabeçalhos para a requisição
$host = parse_url($minioEndpoint, PHP_URL_HOST);
$headers = [
    'Host' => $host,
    'x-amz-date' => $amzDate,
    'x-amz-content-sha256' => hash('sha256', ''), // SHA-256 do corpo vazio (necessário para GET)
];

// String canônica para a assinatura
$canonicalHeaders = '';
foreach ($headers as $key => $value) {
    $canonicalHeaders .= strtolower($key) . ':' . $value . "\n";
}
$signedHeaders = implode(';', array_map('strtolower', array_keys($headers)));

// Criação do `CanonicalRequest`
$canonicalRequest = "GET\n" . $resource . "\n" . "\n" . $canonicalHeaders . "\n" . $signedHeaders . "\n" . hash('sha256', '');

// Debug: Imprimir a String Canônica
echo "\n\n--- Debug: Canonical Request ---\n";
echo $canonicalRequest . "\n\n";

// String para a assinatura (String to Sign)
$algorithm = 'AWS4-HMAC-SHA256';
$credentialScope = $dateStamp . '/' . $region . '/s3/aws4_request';
$stringToSign = $algorithm . "\n" . $amzDate . "\n" . $credentialScope . "\n" . hash('sha256', $canonicalRequest);

// Debug: Imprimir a String to Sign
echo "--- Debug: String to Sign ---\n";
echo $stringToSign . "\n\n";

// Geração da chave de assinatura (Signing Key)
$signingKey = getSignatureKey($secretKey, $dateStamp, $region, 's3');
$signature = hash_hmac('sha256', $stringToSign, $signingKey);

// Debug: Imprimir a Chave de Assinatura e a Assinatura Final
echo "--- Debug: Signing Key e Signature ---\n";
echo "Signing Key: " . bin2hex($signingKey) . "\n";
echo "Signature: " . $signature . "\n\n";

// Cabeçalho de autorização (Authorization)
$authorizationHeader = $algorithm . ' Credential=' . $accessKey . '/' . $credentialScope . ', SignedHeaders=' . $signedHeaders . ', Signature=' . $signature;

// Inclui o cabeçalho de autorização na lista de cabeçalhos
$headers['Authorization'] = $authorizationHeader;

// Prepara os cabeçalhos para a requisição cURL
$curlHeaders = [];
foreach ($headers as $key => $value) {
    $curlHeaders[] = $key . ': ' . $value;
}

// URL completa para a requisição
$url = $minioEndpoint . $resource;

// Inicializa o cURL
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $curlHeaders);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Retornar como string
curl_setopt($curl, CURLOPT_HEADER, true); // Incluir o cabeçalho na resposta para verificar
curl_setopt($curl, CURLOPT_VERBOSE, true); // Ativar o modo verbose para depuração

// Executa a requisição cURL
$response = curl_exec($curl);

// Verifica se houve erro
if (curl_errno($curl)) {
    echo 'Erro na requisição: ' . curl_error($curl);
} else {
    // Verifica o código de resposta HTTP
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ($httpCode == 200) {
        // Exibe a imagem se a resposta for bem-sucedida
        header('Content-Type: image/jpeg');
        echo $response;
    } else {
        echo 'Falha ao acessar o bucket. Código HTTP: ' . $httpCode . "\n";
        echo 'Resposta completa: ' . $response;

        // Debug: Exibir a String Canônica e a String para Assinatura
        echo "\n\n--- Debug ---\n";
        echo "CanonicalRequest:\n" . $canonicalRequest . "\n\n";
        echo "StringToSign:\n" . $stringToSign . "\n\n";
        echo "Authorization Header:\n" . $authorizationHeader . "\n";
    }
}

// Fecha a conexão cURL
curl_close($curl);

// Função para gerar a chave de assinatura (Signing Key)
function getSignatureKey($key, $dateStamp, $regionName, $serviceName) {
    $kDate = hash_hmac('sha256', $dateStamp, 'AWS4' . $key, true);
    $kRegion = hash_hmac('sha256', $regionName, $kDate, true);
    $kService = hash_hmac('sha256', $serviceName, $kRegion, true);
    $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);
    return $kSigning;
}
?>
