<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once ABSPATH . '/classes/PHPMailer/src/Exception.php';
require_once ABSPATH . '/classes/PHPMailer/src/PHPMailer.php';
require_once ABSPATH . '/classes/PHPMailer/src/SMTP.php';

class Mailer {
	private $mailer;
	private $FromName;
	private $Host;
	private $SMTPAuth;
	private $Username;
	private $Password;
	private $SMTPSecure;
	private $Port;
	
	public function __construct($FromName = null, $Host = null, $Username = null, $Password = null, $SMTPAuth = true, $SMTPSecure = 'tls', $Port = '587'){
		if(empty($FromName) && empty($Host) && empty($Username) && empty($Password)){
			return;
		}
		
		$this->FromName = $FromName;
		$this->Host = $Host;
		$this->SMTPAuth = $SMTPAuth;
		$this->Username = $Username;
		$this->Password = $Password;
		$this->SMTPSecure = $SMTPSecure;
		$this->Port = $Port;
		
		$this->mailer = new PHPMailer();
	}
	
	
	public function send($Recipient = null, $Subject = null, $Body = null){
		if(empty($Recipient) && empty($Subject) && empty($Body)){
			return;
		}
		
		$this->mailer->isSMTP();
		$this->mailer->Host = $this->Host;
		$this->mailer->SMTPAuth = $this->SMTPAuth;
		$this->mailer->Username = $this->Username;
		$this->mailer->Password = $this->Password;
		$this->mailer->SMTPSecure = $this->SMTPSecure;
		$this->mailer->Port = $this->Port;

		$this->mailer->From = $this->Username;
		$this->mailer->FromName = $this->FromName;
		$this->mailer->addAddress($Recipient);

		$this->mailer->isHTML(true);
		$this->mailer->CharSet = 'UTF-8';
		$this->mailer->Subject = $Subject;
		$this->mailer->Body = $Body;

		if($this->mailer->send()){
			$this->mailer->ClearAllRecipients();
			$this->mailer->ClearAttachments();
			
			return true;
		}else{
			return $this->mailer->ErrorInfo;
		}
	}
}