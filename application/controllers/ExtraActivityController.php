<?php
 
class ExtraActivityController extends Zend_Controller_Action
{
	protected $_apikey='fbd49c72b9c1b00d8015e235ab354478';
	protected $_publickey='
-----BEGIN PUBLIC KEY----- 
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA1eBYZjpnNPkZXvVsF/UU 
UAH8GoYkSO/8acbaf5JvjkN8aff0nzKD/8q46W/Lgt167nBFiVpXZ9O3ynRZ6G2S 
gxXcxZyYdjqX5XHs7u1J+JNDwN92SLWbD4Z+N+Zai5SCNaU9V3A8WNPm/B+3byoV 
Ex354Cgh1+akTf7oJWad75nUZRTHWUwZ+WTIdg66/cuVLK4fV5WlGrpdFZLcfv8b 
sFfYmnKkTB9GJ6Zri+cjnp6NBm4gqPzA59mxYxrMgKXxPGWosDkE0WbR8a3ynyei 
M/iqHGl3h765f2buMoXbaRAnYqAk6W3XF5QtMIs2o97oi7HMM3/gVeKxZZQtGySr 
7QIDAQAB 
-----END PUBLIC KEY----- ';
	
    public function pemiraAction(){
    	//
    	if (PHP_VERSION_ID < 50600) {
    		iconv_set_encoding('input_encoding', 'UTF-8');
    		iconv_set_encoding('output_encoding', 'UTF-8');
    		iconv_set_encoding('internal_encoding', 'UTF-8');
    	} else {
    		ini_set('default_charset', 'UTF-8');
    	}
    	
    	$auth = Zend_Auth::getInstance();
    	$dbActCalend=new App_Model_General_DbTable_ActivityCalendar();
    	$dbSms=new App_Model_Smsgateway_DbTable_SmsGateways();
    	$active='';
    	$key='';
    	//echo $active;exit;
    	$row=$dbActCalend->getActiveEvent(46);
    	if ($row) {
    		$active="1";
    	} 
    	//echo $active;exit;
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		$registration_id = $auth->getIdentity()->registration_id;    
    	 	$this->view->IdStudentRegistration = $registration_id;
    	 	$this->view->msg='';
    	 	//To get Student Academic Info        
         	$studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
         	$student = $studentRegDB->getStudentInfo($registration_id);
    	  	$nim=$student['registrationId'];
    	  	$hp=$student['appl_phone_hp'];
    	  	$message='';
    	  	 
    	  		//generate token;
    	  		$token=md5($hp.date('his'));
    	  		//generate PIN
    	  		$dbconf=new App_Model_Record_DbTable_ConfirmationPamira();
    	  		$pin=$dbconf->genRandomNumber();
    	  		//ecrypt PIN
    	  		
//     	  		$Crypt=new Zend_Crypt_Rsa();
//     	  		$key=new Zend_Crypt_Rsa_Key_Public($this->_publickey);
//     	  		$encryptedpin=$Crypt->encrypt($pin,$key );
    	  		$res = openssl_public_encrypt($pin,$encrypted_otp,$this->_publickey,OPENSSL_PKCS1_PADDING);
    	  		if($res){
	    	   		//send to pamira
    	  			$enkripsi_otp = strtr(base64_encode($encrypted_otp), '+/=', '._-');
    	  			$data="nim=".$nim."&enkripsi_otp=".$enkripsi_otp."&tokenlink=".$token;
    	  			$data = $data.'&apikey='.$this->_apikey;
	    	  		//$send=$this->sendToPamira($this->dataEncrypt($nim, $token, $encrypted_otp));
	    	  		$send=$this->sendToPamira($data);
	    	  			
	    	  		$send=json_decode($send);
	    	  		//echo var_dump($send);exit;
					if ($send->code=='1') {
						$message="OTP=".$pin.' http://pemira.trisakti.ac.id/pemilihan/'.$token;
						//echo $message;
						$hp='081298204995';
						$iduser=$auth->getIdentity()->appl_id;
						$status='';//$dbSms->sendMessage($message, $hp, "0",$iduser,$registration_id);
						if ($status!='Success Send') $this->view->msg="Pengiriman OTP Gagal, Silahkan cek nomor HP anda dan  coba kembali beberapa saat";
						else {
							$dbconf->addData(array('IdStudentRegistration'=>$registration_id,'dt_entry'=>date('Y-m-d H:i:s'),'id_user'=>$iduser,'encrypted_confirm'=>$enkripsi_otp,'token'=>$token));
							$this->view->msg="Kode OTP dan tautan ke Pemira sudah dikirim ke HP anda menggunakan SMS";
						}  
					
					} else $this->view->msg="Gagal mengirim data ke Pemira, silahkan coba lagi";
					//exit;
    	  		} 
    	  	 
    	}
    	
    	$this->view->active=$active;
    }
    
    function curlapi($url, $data){
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	$output = curl_exec($ch);
    	curl_close($ch);
    	return $output;
    }
    
     
    
    function sendToPamira($data) {
	    
	    $send = $this->curlapi("http://pemira.trisakti.ac.id/apps/webservice/otpsistem",$data);
	    //print(json_encode($send));
	    return $send;
	}
    
}