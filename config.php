<?php
define('ABSPATH', dirname( __FILE__ ));

define('UP_ABSPATH', ABSPATH . '/midia');

define('SYS_NAME', 'Print Manager');
define('SYS_VERSION', '1.0');
define('SYS_COPYRIGHT', 'TIX1');
define('SYS_COPYRIGHT_URL', '#');
define('SYS_DATE', '06/04/2016');
define('SYS_YEAR', '2024');

define('SYS_LAT', '-23.503764');
define('SYS_LNG', '-46.642383');

date_default_timezone_set('America/Sao_Paulo');

define('MAILER_FromName', 'Print Manager');
define('MAILER_Host', 'localhost');
define('MAILER_SMTPAuth', 'true');
define('MAILER_Username', 'no-reply@crede.app.br');
define('MAILER_Password', 'Kn@*daM[=e%b');
define('MAILER_SMTPSecure', 'tls');
define('MAILER_Port', '587');

define('HOME_URI', 'https://impressoes.teste.me');

define('HOSTNAME', 'localhost');
define('DB_NAME', 'printManager');
define('DB_USER', 'crede');
define('DB_PASSWORD', 'crede@123');
define('DB_CHARSET', 'utf8');

define('HASH_KEY', 'crede@123');

define('DEBUG', true);

require_once ABSPATH . '/loader.php';
?>