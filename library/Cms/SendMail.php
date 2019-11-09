<?php

class Cms_SendMail {
	private $lobjCommon;
	private $lobjTransport;
	private $lstrdefaultmail;
	public function __construct() {
		$this->fnsetObj();
	}

	private function fnsetObj() {
		$this->lobjCommon = new App_Model_Common();
		$idUniversity = 1;
		$larrinitConfigFetchAllData = $this->lobjCommon->fnGetInitialConfigDetails($idUniversity);
		if(count($larrinitConfigFetchAllData)!=0) {
			$strsmtpServer = $larrinitConfigFetchAllData['SMTPServer'];
			$strusername = $larrinitConfigFetchAllData['SMTPUsername'];
			$strpassword = $larrinitConfigFetchAllData['SMTPPassword'];
			$strsslValue = $larrinitConfigFetchAllData['SSL'];
			$this->_lstrdefaultmail = $larrinitConfigFetchAllData['DefaultEmail'];
			//$strfromEmail = $stremailTemplateFrom;
			$strsmtpPort = $larrinitConfigFetchAllData['SMTPPort'];
			$config = array('auth' => 'login','username' => $strusername,'password' => $strpassword,'ssl'=> 'tls','port' => '587');
			if(!isset($strsmtpServer)) {
				echo '<script language="javascript">alert("Unable to send mail \n Check  STMP Settings")</script>';
				//echo "<script>parent.refreshpage();</script>";
			} else {
				$this->lobjTransport = new Zend_Mail_Transport_Smtp($strsmtpServer,$config);
			}
		}
	}

	public function fnSendMail($to,$subject,$message,$toname="",$cc=array(),$bcc=array()) {
		try{
			//Intialize Zend Mailing Object
			$lobjMail = new Zend_Mail();
			$lobjMail->setFrom($this->_lstrdefaultmail,"University Admin");
			$lobjMail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
			$lobjMail->addHeader('MIME-Version', '1.0');
			$lobjMail->setSubject($subject);
			$lobjMail->addTo($to,$toname);
			foreach($cc as $email => $name) {
				$lobjMail->addCc($email,$name);
			}
			foreach($bcc as $email) {
				$lobjMail->addBcc($email);
			}
			$lobjMail->setBodyHtml($message);

			try {
				$lobjMail->send($this->lobjTransport);
			} catch (Exception $e) {
				echo '<script language="javascript">alert("'.$e->getMessage().'")</script>';
			}
		}catch(Exception $e){

			echo '<script language="javascript">alert("'.$e->getMessage().'")</script>';
		}
	}
}