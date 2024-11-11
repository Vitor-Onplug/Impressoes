<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'crede');
define('DB_PASS', 'crede@123');
define('DB_NAME', 'test');

define('WEB_SERVICE_Q2_URL', 'https://ticketnotificationapi.stg.q2ingressos.com.br');
define('WEB_SERVICE_Q2_USER', 'catresp');
define('WEB_SERVICE_Q2_PASS', '112233');

define('SLEEP_TIME', 5);
define('LOG_FILE', 'logQ2.log');

define('EVENT_ID', 1);

require_once __DIR__ . '/webServiceQ2.php';

// Chamada do serviço
$syncService = new SyncServiceQ2(DB_HOST, DB_NAME, DB_USER, DB_PASS, WEB_SERVICE_Q2_USER, WEB_SERVICE_Q2_PASS, WEB_SERVICE_Q2_URL, SLEEP_TIME, LOG_FILE);
$syncService->getEvents();

if (EVENT_ID) {
    $syncService->sync(EVENT_ID);
}

/*
    Esse código está fazendo a chamada da API de notificações da Q2 e salvando os tickets no banco de dados caso eles não existam.
    Depois, ele verifica se existem novos acessos no banco de dados e confirma a entrada deles na API.
    Todo o código foi feito para rodar em um loop infinito, verificando a cada 5 segundos se existem novas notificações e acessos.
    Todos os métodos de comunicação com a API e o banco de dados estão encapsulados em classes separadas para facilitar a manutenção e a leitura do código.

    Todos os dados sensíveis, como credenciais de acesso ao banco de dados e à API, podem ser passados como parâmetros para o construtor da classe SyncServiceQ2, 
    depois temos que ver se faz sentido passar esses dados como parâmetros ou se seria melhor mantê-los em um arquivo de configuração externo ou um front-end para salvar essas informações no banco de dados.

    Precisa rodar o seguinte script SQL no MySQL:

    CREATE TABLE tblTickets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ticket_id VARCHAR(255) NOT NULL,
            event_id INT NOT NULL,
            idAcesso INT DEFAULT 0,
            ticket_hash VARCHAR(255) NOT NULL,
            ticket_section VARCHAR(255) NOT NULL,
            date DATETIME NOT NULL
        );

*/


