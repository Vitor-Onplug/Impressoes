<?php
class DatabaseHandler
{
    protected $pdo;
    protected $log;

    public function __construct($host, $dbname, $user, $pass, $log)
    {
        try {
            $this->log = $log;
            $dsn = "mysql:host=$host;dbname=$dbname";
            $this->pdo = new PDO($dsn, $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            $this->log->write('Erro ao conectar ao MySQL: ' . $e->getMessage());
            die();
        }
    }

    public function checkTicketExists($ticketId)
    {
        try {
            $query = "SELECT * FROM tblTickets WHERE ticket_id = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$ticketId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->log->write('Erro ao buscar ticket no MySQL: ' . $e->getMessage());
            return false;
        }
    }

    public function insertTicket($ticketId, $eventId, $ticketHash, $ticketSection, $date)
    {
        try {
            $query = "INSERT INTO tblTickets (ticket_id, event_id, ticket_hash, ticket_section, date) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$ticketId, $eventId, $ticketHash, $ticketSection, $date]);
        } catch (PDOException $e) {
            $this->log->write('Erro ao inserir ticket no MySQL: ' . $e->getMessage());
        }
    }

    public function getNewAccess()
    {
        try {
            $query = "SELECT tblAcessos.dataCriacao as dataAcesso, tblTickets.ticket_id, tblTickets.event_id, tblAcessos.id as idAcesso
            FROM tblAcessos 
            INNER JOIN tblTickets ON tblAcessos.id = tblTickets.idAcesso 
            WHERE tblTickets.idAcesso = 0 AND tblAcessos.status = 'T'
            LIMIT 1000
            ORDER BY tblAcessos.dataCriacao ASC
            ";
            $stmt = $this->pdo->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->log->write('Erro ao buscar acessos não processados no MySQL: ' . $e->getMessage());
            return [];
        }
    }

    public function markAccessProcessed($accessId, $ticketId)
    {
        try {
            $query = "UPDATE tblTickets SET idAcesso = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$accessId, $ticketId]);
        } catch (PDOException $e) {
            $this->log->write('Erro ao marcar ticket como processado no MySQL: ' . $e->getMessage());
        }
    }
}

class APIClient
{
    private $baseUrl;
    private $token;
    private $log;

    public function __construct($baseUrl, $token, $log)
    {
        $this->baseUrl = $baseUrl;
        $this->token = $token;
        $this->log = $log;
    }

    public function getNotifications($eventId = null)
    {
        $url = $this->baseUrl . '/v1/notifications?eventId=' . $eventId;
      
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token
        ]);

        $response = curl_exec($ch);
        
        if (curl_errno($ch) || !$response) {
            $this->log->write('Erro ao buscar notificações da API: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);
       
        $decodedResponse = json_decode($response, true);

        if (is_null($decodedResponse)) {
           $this->log->write('Erro ao decodificar a resposta JSON da API.');
            return null;
        }

        return $decodedResponse['result'];
    }

    public function getEvents()
    {
        $url = $this->baseUrl . '/v1/events';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->log->write('Erro ao buscar eventos da API: ' . curl_error($ch));
            curl_close($ch);
            return;
        }

        curl_close($ch);
        return json_decode($response, true);
    }


    public function confirmEntrance($ticketId, $eventId, $date)
    {
        $url = $this->baseUrl . '/v1/entrance';
        $data = json_encode([
            'ticketId' => $ticketId,
            'eventId' => $eventId,
            'date' => $date
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->log->write('Erro ao confirmar entrada na API: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }
        
        curl_close($ch);
        return json_decode($response, true);
    }
}

class Log
{
    private $logFile;

    public function __construct($logFile)
    {
        $this->logFile = $logFile;

        // Checar se o arquivo de log existe, se não existir, criar
        if (!file_exists($this->logFile)) {
            file_put_contents($this->logFile, '');
        }

        // Se o arquivo de log existir, verificar se é possível escrever nele
        if (!is_writable($this->logFile)) {
            echo 'Erro: Não é possível escrever no arquivo de log.' . PHP_EOL;
            return;
        }

        // Se o arquivo de log existir, verificar seu tamanho e recomeçar se for muito grande
        if (filesize($this->logFile) > 1000000) {
            file_put_contents($this->logFile, '');
        }
    }

    public function write($message)
    {
        $date = date('Y-m-d H:i:s');
        $logMessage = "[$date] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
}

class SyncServiceQ2
{
    private $hostDB;
    private $dbnameDB;
    private $userDB;
    private $passDB;

    private $usernameAPI;
    private $passwordAPI;
    private $baseUrlAPI;
    private $tokenAPI;

    private $eventId;

    private $logFile;
    private $sleepTime;

    private $dbHandler;
    private $apiClient;
    private $log;

    public function __construct($hostDB, $dbnameDB, $userDB, $passDB, $usernameAPI, $passwordAPI, $baseUrlAPI, $sleepTime, $logFile)
    {
        $this->hostDB = $hostDB;
        $this->dbnameDB = $dbnameDB;
        $this->userDB = $userDB;
        $this->passDB = $passDB;

        $this->usernameAPI = $usernameAPI;
        $this->passwordAPI = $passwordAPI;
        $this->baseUrlAPI = $baseUrlAPI;

        $this->sleepTime = $sleepTime;
        $this->logFile = $logFile;

        $this->log = new Log($this->logFile);
        $this->apiClient = new APIClient($this->baseUrlAPI, $this->tokenAPI, $this->log);
        $this->dbHandler = new DatabaseHandler($this->hostDB, $this->dbnameDB, $this->userDB, $this->passDB, $this->log);
        
    }

    private function log($message)
    {
        $date = date('Y-m-d H:i:s');
        $logMessage = "[$date] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }

    public function getEvents()
    {
        $token = $this->login($this->usernameAPI, $this->passwordAPI);
        
        if (!$token) {
            $this->log->write('Erro ao fazer login na API.');
            return;
        }

        $this->tokenAPI = $token;

        $events = $this->apiClient->getEvents();

        if ($events === null) {
            $this->log->write('Erro ao buscar eventos da API.');
            return;
        }
        else{
            $this->log->write('Eventos encontrados: ' . $events);
            return;
        }
    }

    public function sync($eventId)
    {
        if(!$eventId){
            $this->log->write('Erro: Evento não informado.');
            return;
        }

        $this->eventId = $eventId;
        
        while (true) {

            $notifications = $this->apiClient->getNotifications($this->eventId);

            if ($notifications === null) {
                $this->log('Erro ao buscar notificações da API. Notificações: ' . $notifications);
                return;
            }

            //$this->log->write('Pagina Atual: ' . $notifications['currentPage']);
            //$this->log->write('Tamaho da Pagina Atual: ' . $notifications['currentPageSize']);
            //$this->log->write('Total de Paginas: ' . $notifications['totalPages']);
            //$this->log->write('Total de Registros: ' . $notifications['totalNotifications']);

            $tickets = $notifications['notifications'];

            foreach ($tickets as $ticket) {
                if (!($this->dbHandler->checkTicketExists($ticket['ticketId']))) {
                    $this->dbHandler->insertTicket($ticket['ticketId'], $ticket['eventId'], $ticket['ticketHash'], $ticket['ticketSection'], $ticket['date']);
                }else{
                    //$this->log->write('Ticket ' . $ticket['ticketId'] . ' já existe no banco de dados.');
                }
            }

            $accesses = $this->dbHandler->getNewAccess();

            foreach ($accesses as $access) {
                $this->apiClient->confirmEntrance($access['ticket_id'], $access['event_id'], $access['dataAcesso']);
                $this->dbHandler->markAccessProcessed($access['idAcesso'], $access['ticket_id']);
            }

            $this->log->write('Sincronização realizada com sucesso. Data/Hora: ' . date('Y-m-d H:i:s') . PHP_EOL);
            sleep($this->sleepTime);
        }
    }

    private function login($username, $password)
    {
        $url = $this->baseUrlAPI . '/v1/login';
        $data = json_encode(['username' => $username, 'password' => $password]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch) || !$response) {
            $this->log->write('Erro ao fazer login na API: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        $responseDecoded = json_decode($response, true);
        if (!isset($responseDecoded['result'])) {
            $this->log->write('Erro ao decodificar a resposta JSON do login.');
            return null;
        }

        return $responseDecoded['result'];
    }
}


