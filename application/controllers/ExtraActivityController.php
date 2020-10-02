<?php
 
class ExtraActivityController extends Zend_Controller_Action
{
	protected $_apikey='UfIzmscPiNCZxJxNYaJY2+evRf4d7C+caJmCAOKrfcU=';
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
    	  		if(!$res){
    	  			echo "Enkripsi gagal !";
    	  			exit;
    	  		}
    	  		echo $encrypted_otp;exit;
    	  		//send to pamira
    	  		$send=$this->sendToPamira($this->dataEncrypt($nim, $token, $encryptedpin));
				echo var_dump($send);exit;
				if ($send) {
					$status=$dbSms->sendMessage($message, $hp, "0");
					if ($status!='Success Send') $this->view->msg="Pengiriman OTP Gagal, Silahkan coba kembali beberapa saat";
					else
						$dbconf->addData(array('IdStudentRegistration'=>$registration_id,'dt_entry'=>date('Y-m-d H:i:s'),'id_user'=>$auth->getIdentity()->id,'encrypted_confirm'=>$encryptedpin,'token'=>$token));
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
    
    function dataEncrypt($nim,$token,$pin) {
    	$encypteddata='{"NIM":'.$nim.';"TOKEN:"'.$token.';"OTP":'.$pin.'}';
    	$Crypt=new Zend_Crypt_Rsa();
    	$key=new Zend_Crypt_Rsa_Key_Public($this->_publickey);
    	$encypteddata=$Crypt->encrypt($encypteddata, $key);
    	$data = array('data'=>$encypteddata,
    			'apikey' => $this->_apikey //<=untuk mendapatkan apikey silakan login ke pemira
    	);
    	return $data;
    }
    
    function sendToPamira($data) {
	    
	    $send = $this->curlapi("http://pemira.trisakti.ac.id/apps/webservice/otpsistem",json_encode($data));
	    //print(json_encode($send));
	    return $send;
	}
    
}