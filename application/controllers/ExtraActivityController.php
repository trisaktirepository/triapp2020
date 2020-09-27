<?php

class StudentPortalController extends Zend_Controller_Action
{
	protected $_apikey='UfIzmscPiNCZxJxNYaJY2+evRf4d7C+caJmCAOKrfcU=';
	
    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function pamiraAction(){
    	//
    	$auth = Zend_Auth::getInstance();
    	$dbActCalend=new App_Model_General_DbTable_ActivityCalendar();
    	$dbSms=new App_Model_Smsgateway_DbTable_SmsGateways();
    	$active='';
    	$key='';
    	$row=$dbActCalend->getActiveEvent(46);
    	if ($row) {
    		$active="1";
    	} 
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
    	  	$status=$dbSms->sendMessage($message, $hp, "0");
    	  	if ($status!='Success Send') $this->view->msg="Pengiriman OTP Gagal, Silahkan coba kembali beberapa saat";
    	  	else {
    	  		//generate token;
    	  		$token=md5($hp.date('his'));
    	  		//generate PIN
    	  		$dbconf=new App_Model_Record_DbTable_ConfirmationPamira();
    	  		$rand=$dbconf->genRandomNumber();
    	  		//encrypt data
    	  		
    	  		//send to pamira
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
    	$encypteddata='';
    	$data = array('data'=>$encypteddata,
    			'apikey' => $this->_apikey //<=untuk mendapatkan apikey silakan login ke pemira
    	);
    	return $data;
    }
    
    function sendToPamira($data) {
	    
	    $send = $this->curlapi("http://pemira.trisakti.ac.id/apps/webservice/otpsistem",json_encode($data));
	    print(json_encode($send));
	    
	}
    
}