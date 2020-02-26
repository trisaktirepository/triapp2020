<?php

class ApplicantPortalController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

	public function indexAction()
    {
    	
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;    

    	$msg = $this->_getParam('msg', null);
    	if($msg){
    		$this->view->noticeError = $msg;
    	}
    	
    	 //transaction data
        $transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
      
    	$transaction = $transactionDb->getApplicantTransaction($appl_id);
    	if (!$transaction){    		
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'new-application'),'default',true));
    	}
    	//echo 'close';exit;
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	
        $this->view->title = $this->view->translate("Home");
        
        
        $list = $transactionDb->getApplicantPaginateData($appl_id);
         
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($list));
		$paginator->setItemCountPerPage(50);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		$this->view->paginator = $paginator;
		
		//transaction programme
		$applicantProgramDb = new App_Model_Application_DbTable_ApplicantProgram();
		$idintake=0;
		$i=0;
		$txnProgram = array();
		
		foreach ($paginator as  $txn){
			$txnProgram[$i] = $applicantProgramDb->getApplicantProgramByID($txn['at_trans_id']);
			if ($txn['at_intake']>0) $idintake=$txn['at_intake'];
			$i++;
		}
			
		$this->view->txnProgram = $txnProgram;
		
		//get list reg schedule
		$scheduleDb = new App_Model_Registration_DbTable_RegDateSetup();
		$schedule = $scheduleDb->getData($idintake);
		 
		if(is_array($schedule)){
			foreach($schedule as $key=>$row){
				//count total
				$total_allocate = $transactionDb->getTotalAllocateSchedule($row['rds_id']);
				
				if($total_allocate >= $row['rds_capacity']) {
					unset($schedule[$key]);
					//$schedule[$key]["status"] = 1; //Not available
				}else{
					//$schedule[$key]["status"] = 2; //available
					$schedule[$key]["allocate"]  = $total_allocate;
					$schedule[$key]["available"] = abs($row['rds_capacity'])-abs($total_allocate);
				}
			}
		}	
		$this->view->schedule = $schedule;
		
    }
    
    public function newApplicationAction(){
    	//create new transaction record
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id; 
    	
		//get current intake
		$intakeDB = new App_Model_Record_DbTable_Intake();
		$intake = $intakeDB->getCurrentIntake();
		$IdIntake = $intake["IdIntake"];
    	
		if($IdIntake==""){
			$this->view->message="Harap maaf, Pendaftaran belum dibuka";
		}else{
			//echo $IdIntake;
		
		//insert applicant id into applicant_transaction
        $data_trans = array();
		$data_trans["at_appl_id"] = $appl_id;				
		$data_trans["at_status"]="APPLY";
		$data_trans["at_create_by"] = $appl_id;
		$data_trans["at_create_date"]=date("Y-m-d H:i:s");	
		//$data_trans["at_intake"]=$IdIntake;			
						
		$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
		$txnId = $transDB->addData($data_trans);
						
		$auth->getIdentity()->transaction_id = $txnId;
		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'biodata'),'default',true));
		}
	}
    
    public function continueApplicationAction(){
    	$txnId = $this->_getParam('id', 0);
    	
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id; 
    	
    	$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
    	
    	//check intake session
    	$this->checkIntakeSession($txnId);
    	
    	//check validation txn
    	if($txnId!=0 && $transactionDb->checkValidApplicant($txnId, $appl_id)){
    		$auth->getIdentity()->transaction_id = $txnId;
    		
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'biodata'),'default',true));
    	}else{
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
    	}
    }
    
    public function bankPinEntryAction(){
    	$txnId = $this->_getParam('id', 0);
    	
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id; 
    	
    	//check intake session
    	$this->checkIntakeSession($txnId);
    	
    	$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
    	
    	if($txnId!=0 && $transactionDb->checkValidApplicant($txnId, $appl_id)){
    		
    		$auth->getIdentity()->transaction_id = $txnId;
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'verification'),'default',true));
    		
    	}else{
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
    	}
    }
    
    
    
    public function getDocumentAction(){
    	$txnId = $this->_getParam('id', 0);
    	$type = $this->_getParam('typeId', 0);
    	
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id; 
    	
    	$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
    	
    	if($txnId!=0 && $transactionDb->checkValidApplicant($txnId, $appl_id)){
    		
    		if($type=="42"){
    			$this->genSurat($txnId);    			
    		}
    		  $this->view->file_path = $this->getDocumentPath($txnId,$type);
    		 
    	}else{
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
    	}
    }
    
    public function getDocumentPath($txnId, $typeId){
    	
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id; 
    	;
    	$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
    	$txnData = $transactionDb->getTransactionData($txnId);
    
    	if($txnId!=0 && $transactionDb->checkValidApplicant($txnId, $appl_id)){
    		
    		$applicantDocumentDb = new App_Model_Application_DbTable_ApplicantDocument();
    		
    		$docData = $applicantDocumentDb->getData($txnId,$typeId);
    		
    		if($typeId==63){ 
    			$docData=null;
    		}
    		//$docData=null;
    		//echo var_dump($docData);exit;
    		if($docData){
    			/* if($typeId==30){
    				//echo $txnId;exit;
    				$this->_redirect('/online-application/create-usm-card/id/'.$txnId);
    			} else  */
    			return DOCUMENT_PATH.DIRECTORY_SEPARATOR.$docData['ad_filepath'].DIRECTORY_SEPARATOR.$docData['ad_filename'];
    		
    		}else{
    			
    					//CASE regenerate
    					if($typeId==31){
    						 
    						$this->generatePssbConfimationLetterPdf($txnId);
				    		//Add Function Here
				    	}else
				    	if($typeId==32){
				    		//Add Function Here
				    	}else
				    	if($typeId==45){	
				    		//echo $txnData["at_appl_type"];exit;
				    			if($txnData["at_appl_type"]==1){
					    			$this->generateUsmOfferLetterPDF($txnId);
					    		}elseif($txnData["at_appl_type"]==2 || 
					    				$txnData["at_appl_type"]==3 || 
					    				$txnData["at_appl_type"]==4 || 
					    				$txnData["at_appl_type"]==5 ||
					    				$txnData["at_appl_type"]==6 ||
					    				$txnData["at_appl_type"]==7){
					    			$this->generateOfferLetterPDF($txnId);
					    		}
					    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'download','txnId'=>$txnId,'type'=>45),'default',true));
								exit;
				    	}else
				    	if($typeId==30){
				    		//Add Function Here
				    		$this->_redirect('/online-application/create-usm-card/id/'.$txnId);
				    		 
				    		
				    	} else if($typeId==50){
				    		//agreement letter
				    		$this->generateAgreementLetter($txnId);
				    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'download','txnId'=>$txnId,'type'=>50),'default',true));
				    	}else if($typeId==63){
				    		//Surat utk lulus tahap pertama
				    		$this->generateLulus1Letter($txnId);
					    				    		
				    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'download','txnId'=>$txnId,'type'=>63),'default',true));
				    	} else
				    	if($typeId==75){
				    		//Transfer
				    		$this->generateCredittransferConfimationLetterPdf($txnId);
				    	} else
				    	if($typeId==76){
				    		//Invitation
				    		$this->generateInvitationConfimationLetterPdf($txnId);
				    		
				    	}
		 	  }
    		
    	}else{
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
    	}
    }
    
    public function biodataAction(){
    $msg = $this->_getParam('msg', null);
		if($msg){
			$this->view->noticeError = $msg;	
		}
		
		//title
    	$this->view->title = $this->view->translate("Biodata");
    	
    	$auth = Zend_Auth::getInstance();    	 
        $language = $auth->getIdentity()->appl_prefer_lang;  
       
    	$appl_id = $auth->getIdentity()->appl_id;    	
    	$this->view->appl_id = $appl_id;
    	    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	
    	$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
    	$form = new App_Form_Biodata(array ('lang' => $locale));
    	$form->removeElement('appl_admission_type');
    	$form->removeElement('cancel');
    	    	
    	if ($this->getRequest()->isPost()) {    		
    		
    		$formData = $this->getRequest()->getPost();  
    		
    		//check nationality
			if($formData["appl_nationality"]==96){
				$form->country_origin->setRequired(false)->setValidators(array());
			}
			
    		if ($form->isValid($formData)) {
				
    			$info["appl_fname"]=strtoupper($formData["appl_fname"]);
				$info["appl_mname"]=strtoupper($formData["appl_mname"]);
				$info["appl_lname"]=strtoupper($formData["appl_lname"]);
				$info["appl_email"]=$formData["appl_email"];			
				$info["appl_dob"]=$formData["appl_dob"]['day']."-".$formData["appl_dob"]['month']."-".$formData["appl_dob"]['year'];
				$info["appl_birth_place"]=$formData["appl_birth_place"];
				$info["appl_gender"]=$formData["appl_gender"];
				if($formData["appl_nationality"]==0){
					$formData["appl_nationality"] = $formData["country_origin"];
				}
				$info["appl_nationality"]=$formData["appl_nationality"];
				//$info["appl_admission_type"]=$formData["appl_admission_type"];
				
				$info["appl_religion"]=$formData["appl_religion"];
				$info["appl_marital_status"]=$formData["appl_marital_status"];				
				$info["appl_jacket_size"]=$formData["appl_jacket_size"];
				$info["appl_parent_salary"]=$formData["appl_parent_salary"];

				if($formData["appl_marital_status"]!=""){
					$info["appl_no_of_child"]=isset($formData["appl_no_of_child"])?$formData["appl_no_of_child"]:0;	
				}
				
                if($formData["appl_email"] != $applicant["appl_email"])
                {
                    $verifyKey = '';
                    
                    $keys = array_merge(range(0, 9), range('a', 'z'));
                    
                    for ($i = 0; $i < 20; $i++) {
                        $verifyKey .= $keys[array_rand($keys)];
                    }
                    
                    $salt = 'trisakti';
                    $beforeHash = time().$verifyKey.$salt;
                    $verifyKey = md5($beforeHash);
                    $info["verifyKey"] = $verifyKey;
                    
                }			
				
				$appProfileDB->updateData($info, $formData["appl_id"]);
				
                if(isset($info['verifyKey']))
                {
                    $this->sendemailque($appl_id);
                    
                }
                
				$father["af_appl_id"]=$formData["appl_id"];
				$father["af_name"]=$formData["father_name"];
				$father["af_relation_type"]=$formData["relationtype"];
				$father["af_address_rt"]=$formData["appl_address_rt"];
				$father["af_address_rw"]=$formData["appl_address_rw"];
				$father["af_address1"]=$formData["appl_address1"];
				//$father["af_address2"]=$formData["appl_address2"];
				$father["af_province"]=$formData["appl_province"];
				$father["af_state"]=$formData["appl_state"];
				$father["af_city"]=$formData["appl_city"];
				$father["af_kelurahan"]=$formData["appl_kelurahan"];
				$father["af_kecamatan"]=$formData["appl_kecamatan"];
				$father["af_postcode"]=$formData["appl_postcode"];
				$father["af_phone"]=$formData["telephone"];
				$father["af_email"]=$formData["email"];
				$father["af_job"]=$formData["job"];
				$father["af_education_level"]=$formData["edulevel"];
				$father["af_family_condition"]=$formData["condition"];
				
				
				
				$mother["af_appl_id"]=$formData["appl_id"];
				$mother["af_name"]=$formData["mother_name"];
				$mother["af_relation_type"]=$formData["mrelationtype"];
				$mother["af_address_rt"]=$formData["mappl_address_rt"];
				$mother["af_address_rw"]=$formData["mappl_address_rw"];
				$mother["af_address1"]=$formData["mappl_address1"];
				//$mother["af_address2"]=$formData["mappl_address2"];
				$mother["af_province"]=$formData["mappl_province"];
				$mother["af_state"]=$formData["mappl_state"];
				$mother["af_city"]=$formData["mappl_city"];
				$mother["af_kelurahan"]=$formData["mappl_kelurahan"];
				$mother["af_kecamatan"]=$formData["mappl_kecamatan"];
				$mother["af_postcode"]=$formData["mappl_postcode"];
				$mother["af_phone"]=$formData["mtelephone"];
				$mother["af_email"]=$formData["memail"];
				$mother["af_job"]=$formData["mjob"];
				$mother["af_education_level"]=$formData["medulevel"];
				$mother["af_family_condition"]=$formData["mcondition"];
				
				
				$family = new App_Model_Application_DbTable_ApplicantFamily();
				
				$rsfather=$family->fetchdata($father["af_appl_id"],$father["af_relation_type"]);
				
				if (!$rsfather){				
					$family->addData($father);
				} else {
					$family->updateData($father,$rsfather["af_id"]);
				}
				
				$rsmother=$family->fetchdata($mother["af_appl_id"],$mother["af_relation_type"]);
				
				if (!$rsmother){				
					$family->addData($mother);
				} else {
					$family->updateData($mother,$rsmother["af_id"]);
				}
	
				
				$this->view->noticeSuccess = $this->view->translate("Data Successfuly Saved");
				
				$this->view->data = $formData;
				
				$form->populate($formData);
    			$this->view->form = $form;
    			
    		} else {
    			$this->view->data = $formData;
    			
    			$form->populate($formData);
				$this->view->form = $form;
					
    		}//end isvalid form
			
    	}else{
    		$family = new App_Model_Application_DbTable_ApplicantFamily();
    		
    		$rsfather=$family->fetchdata($appl_id,20);
    		
    		if ($rsfather){
    		$applicant["father_name"]=$rsfather["af_name"];
			$applicant["relationtype"]=$rsfather["af_relation_type"];
			$applicant["appl_address_rt"]=$rsfather["af_address_rt"];
			$applicant["appl_address_rw"]=$rsfather["af_address_rw"];
			$applicant["appl_address1"]=$rsfather["af_address1"];
			$applicant["appl_address2"]=$rsfather["af_address2"];
			$applicant["appl_province"]=$rsfather["af_province"];
			$applicant["appl_state"]=$rsfather["af_state"];
			$applicant["appl_city"]=$rsfather["af_city"];
			$applicant["appl_kelurahan"]=$rsfather["af_kelurahan"];
			$applicant["appl_kecamatan"]=$rsfather["af_kecamatan"];
			$applicant["appl_postcode"]=$rsfather["af_postcode"];
			$applicant["telephone"]=$rsfather["af_phone"];
			$applicant["email"]=$rsfather["af_email"];
			$applicant["job"]=$rsfather["af_job"];
			$applicant["edulevel"]=$rsfather["af_education_level"];
			$applicant["condition"]=$rsfather["af_family_condition"];
    		}
    		
			$rsmother=$family->fetchdata($appl_id,21);
			
			if ($rsmother){
			$applicant["mother_name"]=$rsmother["af_name"];
			$applicant["mrelationtype"]=$rsmother["af_relation_type"];
			$applicant["mappl_address_rt"]=$rsmother["af_address_rt"];
			$applicant["mappl_address_rw"]=$rsmother["af_address_rw"];
			$applicant["mappl_address1"]=$rsmother["af_address1"];
			$applicant["mappl_address2"]=$rsmother["af_address2"];
			$applicant["mappl_province"]=$rsfather["af_province"];
			$applicant["mappl_state"]=$rsfather["af_state"];
			$applicant["mappl_city"]=$rsmother["af_city"];
			$applicant["mappl_kelurahan"]=$rsmother["af_kelurahan"];
			$applicant["mappl_kecamatan"]=$rsmother["af_kecamatan"];
			$applicant["mappl_postcode"]=$rsmother["af_postcode"];
			$applicant["mtelephone"]=$rsmother["af_phone"];
			$applicant["memail"]=$rsmother["af_email"];
			$applicant["mjob"]=$rsmother["af_job"];
			$applicant["medulevel"]=$rsmother["af_education_level"];
			$applicant["mcondition"]=$rsfather["af_family_condition"];
			}
				
			
    			
    		/*if($applicant['appl_admission_type']==null){
    			$applicant['appl_admission_type'] = '2';
    		}*/
			$this->view->data = $applicant;
			
	    	$form->populate($applicant);
	    	$this->view->form = $form;
    	}
    }
    
    public function contactInfoAction(){
    	$msg = $this->_getParam('msg', null);
		if($msg){
			$this->view->noticeError = $msg;	
		}
			
		//title
    	$this->view->title = $this->view->translate("ContactInfo");
    	
    	//$appl_id = $this->_getParam('id', 0);
    	$auth = Zend_Auth::getInstance(); 
    	$appl_id = $auth->getIdentity()->appl_id;      	
    	$this->view->appl_id = $appl_id;
    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	
    	$form = new App_Form_ContactInfo();
    	$form->removeElement('cancel');
    	
    	if ($this->getRequest()->isPost()) {
	    		$formData = $this->getRequest()->getPost();
	    		
	    		if ($form->isValid($formData)) {
	    				
	    			 unset($formData['check_opt']);
	    			  unset($formData['check_opt_same']);
	    			 unset($formData['save']);
	    			
	    			$appProfileDB->updateData($formData, $formData["appl_id"]);
	    		
	    			$this->view->noticeSuccess = $this->view->translate("Data Successfuly Saved");
				
	    			$form->populate($formData);
					$this->view->form = $form;
					
	    		}else{
	    			$this->view->noticeError = $this->view->translate("Please Check Form");
	    			
					$form->populate($formData);
					$this->view->form = $form;
	    		}
    	}else{
	    	$form->populate($applicant);
	    	$this->view->form = $form;
    	}
    }

    public function changeDateAction(){
    	
   		$txnId = $this->_getParam('id', 0);
    	
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id; 
    	
    	$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
    	
    	if($txnId!=0 && $transactionDb->checkValidApplicant($txnId, $appl_id)){
    		
    		$auth->getIdentity()->transaction_id = $txnId;
    		
    	}else{
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
    	}
		
		$msg = $this->_getParam('msg', null);
		if($msg){
			$this->view->noticeError = $msg;	
		}
		
		//$this->_helper->layout->setLayout('application');
		
		//title
    	$this->view->title = $this->view->translate("Tukar Jadwal Ujian Saringan");
    	
    	//$appl_id = $this->_getParam('id', 0);    	
    	$this->view->appl_id = $appl_id;
    	
    	$transaction_id = $auth->getIdentity()->transaction_id; 
    	$this->view->transaction_id = $transaction_id;
    	
	
    	//get applicant profile
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	
    	//transaction data
    	$applicantTransactionDn = new App_Model_Application_DbTable_ApplicantTransaction();
    	$transaction = $applicantTransactionDn->getTransactionData($auth->getIdentity()->transaction_id);
    	
    	$dbPlacement=new App_Model_Application_DbTable_ApplicantPtest();
    	$test=$dbPlacement->getPtest($transaction_id);
    	$code=$test['apt_ptest_code'];
    	if($this->_getParam('from', null)!="verify"){
	    	if($transaction["at_status"]!= "PROCESS"){
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
	    	}
    	}
    	
    	if($this->_getParam('from', null)=="verify"){
    		$this->view->noticeMessage ="Tanggal USM yang anda pilih telah lewat, silahkan pilih tanggal baru.";
    	}
    	
    	$this->view->transaction = $transaction;    		
    	   	    	
    	//get available placement test (code)
    	$placementDB = new App_Model_Record_DbTable_PlacementHead();
    	$program = $placementDB->getPlacementTest();
    	$this->view->placement_code = $program["aph_placement_code"];
    	
    	$aph_fees_program  = $program["aph_fees_program"];
    	$aph_fees_location = $program["aph_fees_location"];
    	
    	$ptestinfo = new App_Model_Application_DbTable_ApplicantPtest();   
    	$applicant_placement_test_info = $ptestinfo->getScheduleInfo($transaction_id);
    	
    	$this->view->aps_id = $applicant_placement_test_info["aps_id"];
    		
    	$form = new App_Form_PlacementTest(array('aphplacementcode'=>$code,'aphfeesprogram'=>$program["aph_fees_program"],
    	'aphfeeslocation'=>$program["aph_fees_location"],'transactionid'=>$transaction_id ,'applid'=>$appl_id,'change'=>"1"));
    	
    	if ($this->getRequest()->isPost()) {    		
    
    		$formData = $this->getRequest()->getPost(); 
			$log = $ptestinfo->getPtest($transaction_id);
			$log["apt_log_date"]=date("Y-m-d H:i:s");
			$ptestlog = new App_Model_Application_DbTable_ApplicantPtestLog();
			$ptestlog->addData($log);
			
			//Utk skip checking form//
			$formData["apt_fee_amt"]="skipcheck"; 
			//		
    		if($form->isValid($formData)){  		    		
			
	    		//get billing runno bankID & applicant(payeeID)
	    		//$bankpinDB = new App_Model_Application_DbTable_ApplicantPinBank();
	    		//$bank_info = $bankpinDB->getData();
	    		   			
	    		$info["apt_at_trans_id"]=	$transaction_id;
				$info["apt_ptest_code"]	=	$formData["app_ptest_code"];
				$info["apt_aps_id"]		=	$formData["aps_id"]; //appl_placement_location
				//$info["apt_fee_amt"]	=	$formData["apt_fee_amt"];
				$info["apt_room_id"]    = "";
				$info["apt_sit_no"]    =  "";
				//print_r($info);
				//exit;
				$appptestDB = new App_Model_Application_DbTable_ApplicantPtest();

				if($applicant_placement_test_info){
					$appptestDB->updateData($info,$applicant_placement_test_info["apt_id"]);
				}  
				
				if($formData["from"]=="verify"){
					$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'bank-pin-entry','id'=>$transaction_id ,'from'=>'verify'),'default',true));
				}

				//Generate new seat
    			$appprogramDB = new App_Model_Application_DbTable_ApplicantProgram();
				$app_program = $appprogramDB->getPlacementProgram($transaction_id);
				    	
		    	$program_data["program_code1"]="0";
		    	$program_data["program_code2"]="0";
		    	
		    	$i=1;						    	
		    	foreach($app_program as $program){
		    		$program_data["program_code".$i] = $program["program_code"];		    								    	
		    		$i++;
		    	}					    					    	

				if($program_data["program_code2"]=="0"){
					$program_data["program_code2"] = $program_data["program_code1"];
				}
				$data = $appprogramDB->getProcedure($transaction_id,$program_data["program_code1"],$program_data["program_code2"],$info["apt_aps_id"]);
											
				if($data[0]["roomid"]==0){
					$error="Maaf tempat untuk USM telah penuh. Sila hubungi pihak manajemen universitas";
					$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'verification','msg'=>$error),'default',true));
					exit;
				}
				
				$this->generateKartuUSM($transaction_id);
				//exit;
				//to update appl_pin_to_bank to bankID has been used "P"
				//$pin["status"]="P"; //dah pakai
				//$bankpinDB->updateData($pin,$bank_info["billing_no"]);
				
				
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal','action'=>'index'),'default',true));
    		}else{
    			//$form->populate($formData);
    			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal','action'=>'change-date','id' => $transaction_id),'default',true));
    		}
			
    	}else{
    		
    		
	    	if($applicant_placement_test_info){
	    		$this->view->data = $applicant_placement_test_info;
	    		
	    		$form->populate($applicant_placement_test_info);
	    		
			    $form->addElement('submit', 'cancel', array(
		          'label'=>'Cancel',
		          'decorators'=>array('ViewHelper'),
		          'onClick'=>"window.location ='" . $this->view->url(array('module'=>'default', 'controller'=>'applicant-portal','action'=>'index','from'=>$this->_getParam('from', 0)),'default',true) . "'; return false;"
		        ));
		        
		        $form->addElement('hidden','from',array('value'=>$this->_getParam('from', 0)));
		        $form->removeElement('apt_fee_amt');
    		}
	    	$this->view->form = $form;
    	}
	}
	
   private function generateKartuUSM($txn_id){
   		$transaction_id = $txn_id;      	
    	$profileDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant = $profileDB->viewkartu($transaction_id);
		
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
		
 
		if($applicant){
			
			//print_r($applicant);exit;
			$billing_no=$applicant["billing_no"];
			$pin_no = $applicant["REGISTER_NO"];
			$applicantID = $applicant["at_pes_id"];
			
			$locadb=new App_Model_Application_DbTable_PlacementTestRoom();
			$room = $locadb->getdata($applicant["apt_room_id"]);			
			
			$location = $applicant["apt_id"];
			
			//--------get applicant program  -----------
	    	$appprogramDB = new App_Model_Application_DbTable_ApplicantProgram();
	    	$app_program = $appprogramDB->getPlacementProgram($transaction_id);
	    	
	    	$program_data["program_code1"]="0";
	    	$program_data["program_code2"]="0";
	    	$program_data["faculty_name2"]="";
	    	$program_data["program_name2"]="";
	    	
	    	$i=1;						    	
	    	foreach($app_program as $program){
	    		$program_data["program_name".$i] = $program["program_name"];
	    		$program_data["faculty_name".$i] = $program["faculty"];
	    		$program_data["program_code".$i] = $program["program_code"];
	    								    	
	    	$i++;
	    	}	
	    			
			$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
	    	$document = $documentDB->getData($transaction_id,30); //kartu
	    	//$document=false;
	    	//print_r($document);exit;
	    	if($document){
    		
    		
    		    //-------- get applicant photo --------
    		    $photo_name='';
    		   // $photoDB = new App_Model_Application_DbTable_ApplicantUploadFile();
    		   // $photo = $photoDB->getTxnFile($transaction_id,33); //PHoto
    		   
				$photoDB = new App_Model_Application_DbTable_UploadFile();
    		    $photo = $photoDB->getFile($transaction_id,33) ;   		    
    		    
	    	    if($photo["auf_file_name"]){
    		   	 	$photo_name =  $photo["auf_file_name"]; 
    		   	 	$photodetail = $photo["pathupload"]; 
    		   	 	
    		    }
    		 				    		   
    		   	
    		   	//------get program component info------
    		    $componentDB = new App_Model_Application_DbTable_ApplicantProgram();
    		   	$rs_component = $componentDB->getComponentSchedulebytype($transaction_id,1,$applicant["apt_aps_id"]);	

    			//$x=1;
    			$comp['comp_program1']="";
    			$comp['exam_date_time1']="";
    			$comp['comp_program2']="";
    			$comp['exam_date_time2']="";
    			$comp['location_venue_sitno1']="";
    			$comp['location_venue_sitno2']="";
    		
    			
    			foreach($rs_component as $component){
    				if ($locale=="en_US"){
						$comp['comp_program1'] .= $component["ac_comp_name"].", ";
					}else if($locale=="id_ID"){
						$comp['comp_program1'] .= $component["ac_comp_name_bahasa"].", ";
					}
					//$comp['comp_program1'] .= $component["ac_comp_name_bahasa"].", ";
					
					$comp['exam_date_time1']=date('M d Y',strtotime($component["aps_test_date"])).' '.$component["ac_start_time"];											
    				$comp['location_venue_sitno1'] = $room["av_room_name"]." - ".$applicant["apt_sit_no"];
					//$x++;
    			}
    			//exit;
    			$rs_component2 = $componentDB->getComponentSchedulebytype($transaction_id,2,$applicant["apt_aps_id"]);									    	
		    	if($rs_component2){
	    			foreach($rs_component2 as $component2){
	    				if ($locale=="en_US"){
							$comp['comp_program2'] .= $component2["ac_comp_name"].", ";
						}else if($locale=="id_ID"){
							$comp['comp_program2'] .= $component2["ac_comp_name_bahasa"].", ";
						}
						//$comp['comp_program2'] .= $component2["ac_comp_name"].", ";
						
						$comp['exam_date_time2']=date('M d Y',strtotime($component2["aps_test_date"])).' '.$component2["ac_start_time"];											
	    				$comp['location_venue_sitno2'] = $room["av_room_name"]." - ".$applicant["apt_sit_no"];
						//$x++;
	    			}	
		    	}	
		    	//print_r($comp);exit;		
				$fieldValues = array (
				     '$[pinnumber]' => $applicantID, 
				 	 '$[Applicantname]' => $applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
				     '$[Address1]' => $applicant["appl_address1"],
				     '$[Address2]' => $applicant["appl_address2"],
					 '$[Mobilenumber]' => $applicant["appl_phone_hp"],
					 '$[Facultyname1]' => $program_data["faculty_name1"], 
				     '$[Facultyname2]' => $program_data["faculty_name2"], 
					 '$[Programme1]' => $program_data["program_name1"],
					 '$[Programme2]' => $program_data["program_name2"],
					 '$[Comp_Program1]' => $comp["comp_program1"],
					 '$[Exam_Date_Time1]' => $comp["exam_date_time1"],
				     '$[location_venue_sitno1]' => $comp['location_venue_sitno1'],
					 '$[Comp_Program2]' => $comp["comp_program2"],
					 '$[Exam_Date_Time2]' => $comp["exam_date_time2"],
				     '$[location_venue_sitno2]' => $comp['location_venue_sitno2'],
				     '$[username]' => $applicant["appl_email"],
				     '$[password]' => $applicant["appl_password"],
					 '$[photodetail]' => $photodetail
				);

				//print_r($fieldValues);exit;
				$monthyearfolder=date("mY");

				// ------ create kartu peserta ujian in PDF	----					    	
		    	$filepath = DOCUMENT_PATH."/template/kartu.docx";   

		    	//create directory to locate file
				//$output_directory_path = DOCUMENT_PATH ."/".$transaction_id;
				
				//directory to locate file
				$app_directory_path = DOCUMENT_PATH."/applicant/".$monthyearfolder;	

				//create directory to locate file			
				/*if (!is_dir($app_directory_path)) {
				    mkdir($app_directory_path, 0775);
				}*/
				
				$output_directory_path = DOCUMENT_PATH."/".$document["ad_filepath"];
				//echo $output_directory_path;exit;
    			//create directory to locate file			
				/*if (!is_dir($output_directory_path)) {
				    mkdir($output_directory_path, 0775);
				}*/
				//print_r($fieldValues);exit; 
				//kalau file gambar duduk kat folder bulantahun lain
	    		if(!is_file($output_directory_path."/".$photo_name)){
					copy($photodetail,"$output_directory_path/".$photo_name);
				}	
								
				//$location_path
			    $location_path = $output_directory_path;
			    
				//filename
				$output_filename = $applicantID."_kartu.pdf";			    
	    	// ------- create PDF File section	--------   
				try{
					require_once 'dompdf_config.inc.php';
					
					$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
					$autoloader->pushAutoloader('DOMPDF_autoload');
					
					//template path	 
					$html_template_path = DOCUMENT_PATH."/template/kartu.html";
					
					$html = file_get_contents($html_template_path);
					
					//replace variable
					foreach ($fieldValues as $key=>$value){
						$html = str_replace($key,$value,$html);	
					}
						
					
					$dompdf = new DOMPDF();
					$dompdf->load_html($html);
					$dompdf->set_paper('a4', 'potrait');
					$dompdf->render();
					
					$dompdf = $dompdf->output();
					
					
					
					//to rename output file			
					$output_file_path = $output_directory_path."/".$output_filename;
			
					file_put_contents($output_file_path, $dompdf);
							
					// ------- End PDF File section	--------
					
					$status = true;
				
				}catch (Exception $e) {
					$status = false;	
				}			    
	    	}  
		}
   }

	private function genSurat($transaction_id){
//----------SURAT undangan orang tua section------
				$profileDB = new App_Model_Application_DbTable_ApplicantProfile();
				$applicant = $profileDB->viewkartu($transaction_id);
				$suratDB = new App_Model_Application_DbTable_ApplicantDocument();
	    		$surat = $suratDB->getData($transaction_id,42); //kartu
	    	   //print_r($surat); 	
	    				//get academic year
			    	    $ayearDb = new App_Model_Record_DbTable_AcademicYear();
						$academic_year = $ayearDb->getNextAcademicYearData();
	    			
	    			    //get schedule info
	    			    $scheduleDB = new App_Model_Application_DbTable_ApplicantPtest();
	    			    $schedule = $scheduleDB->getScheduleInfo($transaction_id);
	    			    
	    			    $city["CityName"]='';
	    			    $city["state_name"]='';
	    			    
	    			    if($schedule["al_city"]){
	    			    	$cityDb = new App_Model_General_DbTable_City();
	    			    	$city=$cityDb->getData($schedule["al_city"]);
	    			    }
	    			    
	    			    $location = $schedule["al_location_name"].",".
	    			    			$schedule["al_address1"].",".
	    			    			$schedule["al_address2"].",".
	    			    			$city["CityName"].",".
	    			    			$city["state_name"];
	    			    
						$fieldValues = array (
						     '$[ApplicantName]' => $applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
						     '$[ACADEMICYEAR]' => $academic_year["ay_code"],
						     '$[TestDate]' => date('j F Y',strtotime($schedule["aps_test_date"])),
						     '$[Time]' => date("g:i a.",strtotime($schedule["aps_start_time"])),
						     '$[Location]' => $location,
						     '$[verifydate]' => date("j F Y")
							
						);
						
						// ------ create kartu peserta ujian in PDF	----					    	
				    	$filepath_surat = DOCUMENT_PATH."/template/UndanganOT.html";   
		
				    					
						$output_directory_path = DOCUMENT_PATH."/".$surat["ad_filepath"];
												
						//filename
						$output_filename_surat = $surat["ad_filename"];
						
						//
						// ------- create PDF File section	--------
						try{
							require_once 'dompdf_config.inc.php';
								
							$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
							$autoloader->pushAutoloader('DOMPDF_autoload');
								
							//template path
							$html_template_path = $filepath_surat;
								
							$html = file_get_contents($html_template_path);
								
							//replace variable
							foreach ($fieldValues as $key=>$value){
								$html = str_replace($key,$value,$html);
							}
						
								
							$dompdf = new DOMPDF();
							$dompdf->load_html($html);
							$dompdf->set_paper('a4', 'potrait');
							$dompdf->render();
								
							$dompdf = $dompdf->output();
								
								
								
							//to rename output file
							$output_file_path = $output_directory_path."/".$output_filename;
								
							file_put_contents($output_file_path, $dompdf);
								
							// ------- End PDF File section	--------
								
							$status = true;
						
						}catch (Exception $e) {
							$status = false;
						}
						//print_r($fieldValuesSurat);
						//echo "$filepath_surat, $fieldValuesSurat, $output_directory_path_surat, $output_filename_surat)";					
				    	//$this->mailmerge($filepath_surat, $fieldValuesSurat, $output_directory_path_surat, $output_filename_surat);


				//-----------------end SURAT undangan orang tua section --------------------	
	}
	protected function mailmerge($filepath,$fieldValues,$output_directory_path,$output_filename,$photoFilename=''){
		
		    //create PDF File  
/*			print_r($fieldValues);
			echo $photoFilename."<br>";
			echo $fieldValues["image:photo"];
			exit; 		
    		*/
			$mailMerge = new Zend_Service_LiveDocx_MailMerge();
									
			$mailMerge->setUsername('yatie')
			          ->setPassword('al_hasib');	
			          
  
			/*if($photoFilename){				
				if (!$mailMerge->imageExists($fieldValues["image:photo"])) {
				    $mailMerge->uploadImage($photoFilename);
				}
			}*/
			//buat ni sebab kalo takde gambor error
			if($photoFilename){					
				$fieldValues["image:photo"]=$photoFilename;	
				if (!$mailMerge->imageExists($fieldValues["image:photo"])) {
					$directory_photo =  $output_directory_path."/".$photoFilename; //get directory DOCUMENT_PATH/$appl_id/$photo_name
				    $mailMerge->uploadImage($directory_photo);
				}
			}         
			          
			$mailMerge->setLocalTemplate($filepath);
		   
			$mailMerge->setFieldValues($fieldValues);
			//$mailMerge->assign('image:photo', $photoFilename);
			$mailMerge->createDocument();
			 
			$document = $mailMerge->retrieveDocument('pdf');
						
			//create directory to locate file			
			if (!is_dir($output_directory_path)) {
		    	mkdir($output_directory_path, 0775, true);
			}

			//to rename output file			
			$output_file_path = $output_directory_path."/".$output_filename;
			
			file_put_contents($output_file_path, $document);
			if($photoFilename){
				$mailMerge->deleteImage($output_directory_path."/".$photoFilename);
			}
	}
	
   public function genKartuAction(){
   	exit;
  	 $ptdb = new App_Model_Application_DbTable_ApplicantPtest();
  	 $row = $ptdb->listCandidate(11,230,10);
  	 foreach ($row as $info){
  	 	$gen = $this->generateKartuUSM($info["apt_at_trans_id"]);
  	 	echo $info["apt_at_trans_id"]."-";
  	 	if($gen) echo "OK";
  	 	else echo "Fail";
  	 	echo "<hr>";
  	 }
   }

   
	private function generateOfferLetterPdf($txnId){
	
		//get applicant info
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
    	
    	//$txnId=$this->_request->getParam('transaction_id');
    	$applicant = $applicantDB->getAllProfile($txnId);
    	//get transaction info
    	$applicantTxnDB = new App_Model_Application_DbTable_ApplicantTransaction();
		$txnData = $applicantTxnDB->getTransaction($txnId);
		
		if ($txnData['at_appl_type']=="1") {
			$typeselection="Ujian Saringan Masuk (USM)";
			$message="-";
		}else
		if ($txnData['at_appl_type']=="2") {
			$typeselection="Program Seleksi Siswa Berpotensi";
			$message="-";
		} else if ($txnData['at_appl_type']=="3") {
			$typeselection="Program Seleksi Transfer/Pindahan";
			$message="Pelajari terlebih dahulu hasil penyetaraan, sebelum melakukan pembayaran";
		} else if ($txnData['at_appl_type']=="4") {
			$typeselection="Program Seleksi Undangan";
			$message="-";
		} else if ($txnData['at_appl_type']=="5") {
			$typeselection="Program Seleksi Portofolio";
			$message="-";
		} else if ($txnData['at_appl_type']=="6") {
			$typeselection="Program Seleksi Beasiswa";
			$dbSetup=new App_Model_General_DbTable_Setup();
			$typebeasiswa=$dbSetup->getDataById($txnData['appl_sub_type']);
			$typeselection=$typeselection." ".$typebeasiswa['ssd_name_bahasa'];
			$message="Karena anda mendapatkan beasiswa sampai dengan lulus menjadi Sarjana dalam waktu 4 tahun, maka abaikan tagihan ini";
		} else if ($txnData['at_appl_type']=="7") {
			$typeselection="Program Seleksi Nilai UTBK";
			$message="-";
		}  
		//get assessment data
		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessment();
		$assessmentData = $assessmentDb->getData($txnId);
		
		//get transaction info
		$applicantTxnDB = new App_Model_Application_DbTable_ApplicantTransaction();
		$txnData = $applicantTxnDB->getTransaction($txnId);
		
		//getapplicantprogram
		$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
		$program = $appProgramDB->getProgramFaculty($txnId,$txnData['at_appl_type']);
    			
		//program data
		$programDb = new App_Model_General_DbTable_Program();
		$programData = $programDb->fngetProgramData($program[0]['program_id']);
		
		//award type
		$award = "";
		
		if($programData['Award'] == 36){
			$award = "D3";
		}else
		if($programData['Award'] == 363){
			$award = "D4";
		}else{
			$award = "S1";
		}
		
		
		$learning_duration = $award." = ".$programData['OptimalDuration']." Semester";
		
		
		//rank		
		if($assessmentData['aar_rating_rector']==1){
			$rank = "1 (Satu)";
			$biaya =$programData['Estimate_Fee_R1']!=null?$programData['Estimate_Fee_R1']:""; 
		}else
		if($assessmentData['aar_rating_rector']==2){
			$rank = "2 (Dua)"; 
			$biaya =$programData['Estimate_Fee_R2']!=null?$programData['Estimate_Fee_R2']:"";
		}else
		if($assessmentData['aar_rating_rector']==3){
			$rank = "3 (Tiga)"; 
			$biaya =$programData['Estimate_Fee_R3']!=null?$programData['Estimate_Fee_R3']:"";
		}
							
		
		//faculty data
		$collegeMasterDb = new App_Model_General_DbTable_Collegemaster();
		$facultyData = $collegeMasterDb->fngetCollegemasterData($program[0]['faculty_id']);
		
		//get applicant parents info
    	$familyDB =  new App_Model_Application_DbTable_ApplicantFamily();
    	$father = $familyDB->fetchdata($applicant["appl_id"],20); //father's    	
    	
    	//get next intake
    	$intakeDb = new App_Model_Record_DbTable_Intake();
    	$intakeData = $intakeDb->getData($txnData['at_intake']);
    	
    	//get fee structure
		$feeStructureDb = new Studentfinance_Model_DbTable_FeeStructure();
		
		//get fee structure
		if($applicant["appl_nationality"]==96){
			//local
			$feeStructureDb = new App_Model_Finance_DbTable_FeeStructure();
			$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData['IdIntake'],$program[0]["program_id"]);
			$biaya = number_format($biaya, 2, '.', ',');
		
		}else{
			//foreigner
			$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData['IdIntake'],$program[0]["program_id"],315);	
			$biaya = $biaya*2;
			$biaya = number_format($biaya, 2, '.', ',');
		}
		
		
		//fee structure plan
		$feeStructurePlanDb = new App_Model_Finance_DbTable_FeeStructurePlan();
		$paymentPlanData = $feeStructurePlanDb->getStructureData($feeStructureData['fs_id']);
		$feeStructureData['payment_plan'] = $paymentPlanData;
		global $feestructureplan;
		$feestructureplan=$paymentPlanData;
		//fee structure program
		$feeStructureProgramDb = new App_Model_Finance_DbTable_FeeStructureProgram();
		$feeStructureProgramData = $feeStructureProgramDb->getStructureData($feeStructureData['fs_id'],$program[0]["program_id"]);
		
		//fee structure plan detail
		$fspdDb = new App_Model_Finance_DbTable_FeeStructurePlanDetail();
		
		foreach ($feeStructureData['payment_plan'] as $key=>$plan){
			
			for($installment=1; $installment<=$plan['fsp_bil_installment']; $installment++){
				$feeStructureData['payment_plan'][$key]['plan_detail'][$installment] = $fspdDb->getPlanData($plan['fsp_structure_id'], $plan['fsp_id'], $installment, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);	
			}
		}
		
		
		/*
		 * paket A
		 */
		//$paket_a_plan = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[0]['fsp_id'], 1, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		
		
		/*
		 * paket B
		 */
		/*
		$paket_b_plan_cicilan1 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 1, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		$paket_b_plan_cicilan2 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 2, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		$paket_b_plan_cicilan3 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 3, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		$paket_b_plan_cicilan4 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 4, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		$paket_b_plan_cicilan5 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 5, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		$paket_b_plan_cicilan6 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 6, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
		*/
		
		/*echo "<pre>";
		print_r($paket_b_plan_cicilan1);
		echo "<pre>";
		exit;*/
		  
		//create image
		//$this->createImage();
		
		
    	
		//$nomor = '010/AK.4.02/PSSB-BAA/Usakti/WR.I/I-3/2012';
		$nomor=$assessmentData['asd_nomor'];
		
		$address = "";
		if( isset($applicant["appl_address1"]) && $applicant["appl_address1"]!=""){
			$address = $address . $applicant["appl_address1"]."<br />";
		}
		if( isset($applicant["appl_address2"]) && $applicant["appl_address2"]!=""){
			$address = $address . $applicant["appl_address2"]."<br />";
		}
		if( isset($applicant["CityName"]) && $applicant["CityName"]!=""){
			$address = $address . $applicant["CityName"]."<br />";
		}
		if( isset($applicant["appl_postcode"]) && $applicant["appl_postcode"]!=""){
			$address = $address . $applicant["appl_postcode"]."<br />";
		}
		if( isset($applicant["StateName"]) && $applicant["StateName"]!=""){
			$address = $address . $applicant["StateName"]."<br />";
		}
		
		$fieldValues = array(
				'$[NO_PES]'=>$txnData["at_pes_id"],
		        '$[NOMOR]'=>$nomor,
				'$[LAMPIRAN]'=>"-",
		        '$[TITLE_TEMPLATE]'=>$this->view->translate("Pemberitahuan diterima sebagai calon Mahasiswa di Universitas Trisakti"),
		        '$[APPLICANT_NAME]'=>$applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
				'$[PARENTNAME]'=>$father["af_name"],
		        '$[ADDRESS]' =>$address,
				'$ADDRESS1]'=>$applicant["appl_address1"],
				'$ADDRESS2]'=>$applicant["appl_address2"],
				'$[CITY]'=>$applicant["CityName"],
				'$[POSTCODE]'=>$applicant["appl_postcode"],
				'$[STATE]'=>$applicant["StateName"],				
		    	'$[ACADEMIC_YEAR]'=>$txnData['ay_code'],
				'$[PERIOD]'=>$txnData['ap_desc'],
				'$[FACULTY]'=>$program[0]["faculty2"],
				'$[FACULTY_NAME]'=>($facultyData['ArabicName']!=null?$facultyData['ArabicName']." ":"-"),
				'$[FACULTY_SHORTNAME]'=>($facultyData['ShortName']!=null?$facultyData['ShortName']." ":"-"),
				'$[FACULTY_ADDRESS1]'=>($facultyData['Add1']!=null?$facultyData['Add1']." ":"-"),
				'$[FACULTY_ADDRESS2]'=>($facultyData['Add2']!=null?$facultyData['Add2']." ":""),
				'$[FACULTY_ADDRESS]'=>($facultyData['Add1']!=null?$facultyData['Add1']." ":"").($facultyData['Add2']!=null?$facultyData['Add2']." ":""),
				'$[FACULTY_PHONE]'=>($facultyData['Phone1']!=null?$facultyData['Phone1']." ":"").($facultyData['Phone2']!=null?", ".$facultyData['Phone2']." ":""),
				'$[FACULTY_FAX]'=>($facultyData['Fax']!=null?$facultyData['Fax']." ":""),
				'$[PROGRAME]'=>$program[0]["program_name_indonesia"],
				'$[KELAS]'=>$program[0]["GroupName"],
				'$[RANK]' => $rank,
		        '$[PRINT_DATE]'=>date('j M Y'),
				'$[REGISTRATION_DATE_START]'=> date ( 'j F Y' , strtotime ( $assessmentData['aar_reg_start_date'] ) ),
				'$[REGISTRATION_DATE_END]'=> date ( 'j F Y' , strtotime ( $assessmentData['aar_reg_end_date'] ) ),
				//'$[PAKET_A_DATE_PAYMENT]'=> date ( 'j F Y' , strtotime ( $assessmentData['aar_payment_start_date'] ) ),
				//'$[PAKET_A_SP]' => number_format($paket_a_plan[0]['total_amount'], 2, '.', ','),
				//'$[PAKET_A_BPP_POKOK]' => number_format($paket_a_plan[1]['total_amount'], 2, '.', ','),
				//'$[PAKET_A_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				//'$[PAKET_A_BPP_SKS_VALUE]' => number_format($paket_a_plan[2]['fsi_amount'], 2, '.', ','),
				//'$[PAKET_A_BPP_SKS_AMOUNT]' => number_format($paket_a_plan[2]['total_amount'], 2, '.', ','),
				//'$[PAKET_A_PRAKTIKUM]' => number_format($paket_a_plan[3]['total_amount'], 2, '.', ','),
				//'$[PAKET_A_TOTAL]' => number_format($paket_a_plan[0]['total_amount'] + $paket_a_plan[1]['total_amount'] + $paket_a_plan[2]['total_amount'] + $paket_a_plan[3]['total_amount'], 2, '.', ',') ,

				//'$[PAKET_B_C1_DATE_PAYMENT]'=>date ( 'j F Y' , strtotime ( $assessmentData['aar_payment_start_date'] ) ),
				//'$[PAKET_B_C1_SP]' => number_format($paket_b_plan_cicilan1[0]['total_amount'], 2, '.', ','),
				//'$[PAKET_B_C1_BPP_POKOK]' => number_format($paket_b_plan_cicilan1[1]['total_amount'], 2, '.', ','),
				//'$[PAKET_B_C1_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				//'$[PAKET_B_C1_BPP_SKS_VALUE]' => number_format($paket_b_plan_cicilan1[2]['fsi_amount'], 2, '.', ','),
				//'$[PAKET_B_C1_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan1[2]['total_amount'], 2, '.', ','),
				//'$[PAKET_B_C1_PRAKTIKUM]' => number_format($paket_b_plan_cicilan1[3]['total_amount'], 2, '.', ','),
				//'$[PAKET_B_C1_TOTAL]' => number_format($paket_b_plan_cicilan1[0]['total_amount'] + $paket_b_plan_cicilan1[1]['total_amount'] + $paket_b_plan_cicilan1[2]['total_amount'] + $paket_b_plan_cicilan1[3]['total_amount'], 2, '.', ',') ,
		
				//'$[PAKET_B_C2_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+1 month' , strtotime ( $assessmentData['aar_reg_end_date'] ) ) ),
				//'$[PAKET_B_C2_SP]' => number_format($paket_b_plan_cicilan2[0]['total_amount'], 2, '.', ','),
				//'$[PAKET_B_C2_BPP_POKOK]' => number_format($paket_b_plan_cicilan2[1]['total_amount'], 2, '.', ','),
				//'$[PAKET_B_C2_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				//'$[PAKET_B_C2_BPP_SKS_VALUE]' => number_format($paket_b_plan_cicilan2[2]['fsi_amount'], 2, '.', ','),
				//'$[PAKET_B_C2_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan2[2]['total_amount'], 2, '.', ','),
				//'$[PAKET_B_C2_PRAKTIKUM]' => number_format($paket_b_plan_cicilan2[3]['total_amount'], 2, '.', ','),
				//'$[PAKET_B_C2_TOTAL]' => number_format($paket_b_plan_cicilan2[0]['total_amount'] + $paket_b_plan_cicilan2[1]['total_amount'] + $paket_b_plan_cicilan2[2]['total_amount'] + $paket_b_plan_cicilan2[3]['total_amount'], 2, '.', ',') ,
		
				/*'$[PAKET_B_C3_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+2 month' , strtotime ( $assessmentData['aar_reg_end_date'] ) ) ),
				'$[PAKET_B_C3_SP]' => number_format($paket_b_plan_cicilan3[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C3_BPP_POKOK]' => number_format($paket_b_plan_cicilan3[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C3_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C3_SKS_VALUE]' => number_format($paket_b_plan_cicilan3[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C3_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan3[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C3_PRAKTIKUM]' => number_format($paket_b_plan_cicilan3[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C3_TOTAL]' => number_format($paket_b_plan_cicilan3[0]['total_amount'] + $paket_b_plan_cicilan3[1]['total_amount'] + $paket_b_plan_cicilan3[2]['total_amount'] + $paket_b_plan_cicilan3[3]['total_amount'], 2, '.', ',') ,
		
				'$[PAKET_B_C4_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+3 month' , strtotime ( $assessmentData['aar_reg_end_date'] ) ) ),
				'$[PAKET_B_C4_SP]' => number_format($paket_b_plan_cicilan4[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C4_BPP_POKOK]' => number_format($paket_b_plan_cicilan4[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C4_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C4_SKS_VALUE]' => number_format($paket_b_plan_cicilan4[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C4_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan4[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C4_PRAKTIKUM]' => number_format($paket_b_plan_cicilan4[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C4_TOTAL]' => number_format($paket_b_plan_cicilan4[0]['total_amount'] + $paket_b_plan_cicilan4[1]['total_amount'] + $paket_b_plan_cicilan4[2]['total_amount'] + $paket_b_plan_cicilan4[3]['total_amount'], 2, '.', ',') ,
		
				'$[PAKET_B_C5_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+4 month' , strtotime ( $assessmentData['aar_reg_end_date'] ) ) ),
				'$[PAKET_B_C5_SP]' => number_format($paket_b_plan_cicilan5[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C5_BPP_POKOK]' => number_format($paket_b_plan_cicilan5[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C5_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C5_SKS_VALUE]' => number_format($paket_b_plan_cicilan5[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C5_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan5[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C5_PRAKTIKUM]' => number_format($paket_b_plan_cicilan5[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C5_TOTAL]' => number_format($paket_b_plan_cicilan5[0]['total_amount'] + $paket_b_plan_cicilan5[1]['total_amount'] + $paket_b_plan_cicilan5[2]['total_amount'] + $paket_b_plan_cicilan5[3]['total_amount'], 2, '.', ',') ,
		
				'$[PAKET_B_C6_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+5 month' , strtotime ( $assessmentData['aar_reg_end_date'] ) ) ),
				'$[PAKET_B_C6_SP]' => number_format($paket_b_plan_cicilan6[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C6_BPP_POKOK]' => number_format($paket_b_plan_cicilan6[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C6_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C6_SKS_VALUE]' => number_format($paket_b_plan_cicilan6[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C6_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan6[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C6_PRAKTIKUM]' => number_format($paket_b_plan_cicilan6[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C6_TOTAL]' => number_format($paket_b_plan_cicilan6[0]['total_amount'] + $paket_b_plan_cicilan6[1]['total_amount'] + $paket_b_plan_cicilan6[2]['total_amount'] + $paket_b_plan_cicilan6[3]['total_amount'], 2, '.', ',') ,
					
				'$[BALANCE_INSTALLMENT_PAKET_B]' => number_format( ( $paket_a_plan[0]['total_amount'] + $paket_a_plan[1]['total_amount'] + $paket_a_plan[2]['total_amount'] + $paket_a_plan[3]['total_amount'] ) - ( $paket_b_plan_cicilan1[0]['total_amount'] + $paket_b_plan_cicilan1[1]['total_amount'] + $paket_b_plan_cicilan1[2]['total_amount'] + $paket_b_plan_cicilan1[3]['total_amount'] ), 2, '.', ','),
				*/
				'$[MESSAGE]'=>$message,
				'$[SELECTION_TYPE]'=>$typeselection,
				'$[LEARNING_DURATION]' => $learning_duration,
				'$[ESTIMASI_BIAYA]' => $biaya,
				'$[RECTOR_DATE]' => date('j M Y',strtotime($assessmentData['asd_decree_date']))
		);
		
		require_once 'dompdf_config.inc.php';
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		$html_template_path = DOCUMENT_PATH."/template/OfferLetter2020.html";
		
		$html = file_get_contents($html_template_path);
		
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);	
		}
		
		//program data
		global $program;
		$program = $feeStructureProgramData;
		
		//registration date
		global $reg_date;
		$reg_date = array(
						'REGISTRATION_DATE_START'=> $assessmentData['aar_reg_start_date'],
						'REGISTRATION_DATE_END'=> $assessmentData['aar_reg_end_date']
					);

		//date payment
		foreach($feeStructureData['payment_plan'] as $key=>$plan){
			$start = $assessmentData['aar_reg_start_date'];
			$end = $assessmentData['aar_reg_end_date'];
			
			foreach ($plan['plan_detail'] as $key2=>$installment){
				$reg_date['date_payment'][$key][$key2]['start'] = $start;
				$reg_date['date_payment'][$key][$key2]['end'] = $end;

				$end = date ( 'F Y' , strtotime ( '+1 month' , strtotime ( $end) ) );
			}
			
			$end = $assessmentData['aar_reg_end_date'];
		}			
		
		
		//fee data
		global $fees;
		$fees = $feeStructureData['payment_plan'];
		
		//footer variable
		global $pes;
		$pes = $txnData["at_pes_id"];
		//echo $html;
		//exit;
		
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		@$dompdf->render();
		
		
		//$location_path
		$location_path = "applicant/".date("mY")."/".$txnId;
		
	
		//untuk stream ke student
		//$dompdf->stream($txnData["at_pes_id"]."_offer_letter.pdf");
			
		//untuk save ke db
		$pdf = @$dompdf->output();
		//exit;
		
		//output_directory_path
		$output_directory_path = DOCUMENT_PATH."/".$location_path;
		
		//create directory to locate file			
		if (!is_dir($output_directory_path)) {
	    	mkdir($output_directory_path, 0775);
		}
		
		//output filename 
		$output_filename = $txnData["at_pes_id"]."_offer_letter.pdf";
		
		//to rename output file			
		$output_file_path = $output_directory_path."/".$output_filename;
						
		file_put_contents($output_file_path, $pdf);
		
				
		//update file info
		$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
		$fileexist = $documentDB->getDataArray($txnId, 45);
		
		$doc["ad_filepath"]=$location_path;
		$doc["ad_filename"]=$output_filename;
		$doc["ad_appl_id"]=$txnId;
		$doc["ad_type"]=45;
		$doc["ad_createddt"]=date("Y-m-d");
		
		
		if($fileexist){
			$documentDB->updateDocument($doc,$txnId,45);
		}else{
			$documentDB->addData($doc);
		}
		

		//regenerate performa invoice
		//$proformaInvoiceDb = new Application_Model_DbTable_ProformaInvoice();
		//$proformaInvoiceDb->regenerateProformaInvoice($txnId);
				
		//exit();
		
	}
	
		
	private function generateUsmOfferLetterPDF($txnId){
		
		$offerleter = new icampus_Function_Application_Offerletter();
		
		$offerleter->generateUsmOfferLetter($txnId);	
		
	}
	
	
	private function generateUsmOfferLetterPDF_old($txnId){
		
		//get applicant info
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $applicantDB->getAllProfile($txnId);
    	//get transaction info
    	$applicantTxnDB = new App_Model_Application_DbTable_ApplicantTransaction();
		$txnData = $applicantTxnDB->getTransaction($txnId);
		
		//get assessment data
		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessmentUsm();
		$assessmentData = $assessmentDb->getData($txnId);				
		
		//getapplicantprogram
		$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
		$program = $appProgramDB->getUsmOfferProgram($txnId);     	
		
		//program data
		$programDb = new App_Model_General_DbTable_Program();
		$programData = $programDb->fngetProgramData($program['program_id']);
		
		//award type
		$award = "";
		
		if($programData['Award'] == 36){
			$award = "D3";
		}else
		if($programData['Award'] == 363){
			$award = "D4";
		}else{
			$award = "S1";
		}
		
		$learning_duration = $award." = ".$programData['OptimalDuration']." Semester";
		
		
		//rank
		if($assessmentData['aau_rector_ranking']==1){
			$rank = "1 (Satu)";
			$biaya =$programData['Estimate_Fee_R1']!=null?number_format($programData['Estimate_Fee_R1'], 2, '.', ','):""; 
		}else
		if($assessmentData['aau_rector_ranking']==2){
			$rank = "2 (Dua)"; 
			$biaya =$programData['Estimate_Fee_R2']!=null?number_format($programData['Estimate_Fee_R2'], 2, '.', ','):"";
		}else
		if($assessmentData['aau_rector_ranking']==3){
			$rank = "3 (Tiga)"; 
			$biaya =$programData['Estimate_Fee_R3']!=null?number_format($programData['Estimate_Fee_R3'], 2, '.', ','):"";
		}
		
		
		
		
		//faculty data
		$collegeMasterDb = new App_Model_General_DbTable_Collegemaster();
		$facultyData = $collegeMasterDb->fngetCollegemasterData($program['faculty_id']);
		
		//get applicant parents info
    	$familyDB =  new App_Model_Application_DbTable_ApplicantFamily();
    	$father = $familyDB->fetchdata($applicant["appl_id"],20); //father's    	
    	
    	//get next intake
    	$intakeDb = new App_Model_Record_DbTable_Intake();
    	$intakeData = $intakeDb->getData($txnData['at_intake']);
    	    	
		//get fee structure
		$feeStructureDb = new App_Model_Finance_DbTable_FeeStructure();
		$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData['IdIntake'],$program["program_id"],1);
		
		//fee structure plan
		$feeStructurePlanDb = new App_Model_Finance_DbTable_FeeStructurePlan();
		$paymentPlanData = $feeStructurePlanDb->getStructureData($feeStructureData['fs_id']);
		$feeStructureData['payment_plan'] = $paymentPlanData;
		
		//fee structure program
		$feeStructureProgramDb = new App_Model_Finance_DbTable_FeeStructureProgram();
		$feeStructureProgramData = $feeStructureProgramDb->getStructureData($feeStructureData['fs_id'],$program["program_id"]);
		
		
		
		//fee structure plan detail
		$fspdDb = new App_Model_Finance_DbTable_FeeStructurePlanDetail();
		
		foreach ($feeStructureData['payment_plan'] as $key=>$plan){
			
			for($installment=1; $installment<=$plan['fsp_bil_installment']; $installment++){
				$feeStructureData['payment_plan'][$key]['plan_detail'][$installment] = $fspdDb->getPlanData($plan['fsp_structure_id'], $plan['fsp_id'], $installment, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
			}
		}
		
		
		/*
		 * paket A
		 */
		//$paket_a_plan = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[0]['fsp_id'], 1, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		
		
		/*
		 * paket B
		 */
		//$paket_b_plan_cicilan1 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 1, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		//$paket_b_plan_cicilan2 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 2, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		//$paket_b_plan_cicilan3 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 3, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		//$paket_b_plan_cicilan4 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 4, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		//$paket_b_plan_cicilan5 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 5, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		//$paket_b_plan_cicilan6 = $fspdDb->getPlanData($feeStructureData['fs_id'], $paymentPlanData[1]['fsp_id'], 6, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aau_rector_ranking']);
		
		/*echo "<pre>";
		print_r($paket_b_plan_cicilan1);
		echo "<pre>";
		exit;*/
		  
		//create image
		//$this->createImage();
		
		
    	
		//$nomor = '010/AK.4.02/PSSB-BAA/Usakti/WR.I/I-3/2012';
		$nomor=$assessmentData['aaud_nomor'];
		
		$address = "";
		if( isset($applicant["appl_address1"]) && $applicant["appl_address1"]!=""){
			$address = $address . $applicant["appl_address1"]."<br />";
		}
		if( isset($applicant["appl_address2"]) && $applicant["appl_address2"]!=""){
			$address = $address . $applicant["appl_address2"]."<br />";
		}
		if( isset($applicant["CityName"]) && $applicant["CityName"]!=""){
			$address = $address . $applicant["CityName"]."<br />";
		}
		if( isset($applicant["appl_postcode"]) && $applicant["appl_postcode"]!=""){
			$address = $address . $applicant["appl_postcode"]."<br />";
		}
		if( isset($applicant["StateName"]) && $applicant["StateName"]!=""){
			$address = $address . $applicant["StateName"]."<br />";
		}
		
		$fieldValues = array(
				'$[NO_PES]'=>$txnData["at_pes_id"],
		        '$[NOMOR]'=>$nomor,
				'$[LAMPIRAN]'=>"-",
		        '$[TITLE_TEMPLATE]'=>$this->view->translate("Pemberitahuan diterima sebagai calon Mahasiswa di Universitas Trisakti"),
		        '$[APPLICANT_NAME]'=>$applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
				'$[PARENTNAME]'=>$father["af_name"],
		        '$[ADDRESS]' =>$address,
				'$[ADDRESS1]'=>$applicant["appl_address1"],
				'$[ADDRESS2]'=>$applicant["appl_address2"],
				'$[CITY]'=>$applicant["CityName"],
				'$[POSTCODE]'=>$applicant["appl_postcode"],
				'$[STATE]'=>$applicant["StateName"],				
		    	'$[ACADEMIC_YEAR]'=>$txnData['ay_code'],
				'$[PERIOD]'=>$txnData['ap_desc'],
				'$[FACULTY]'=>$program["faculty2"],
				'$[FACULTY_NAME]'=>($facultyData['ArabicName']!=null?$facultyData['ArabicName']." ":"-"),
				'$[FACULTY_SHORTNAME]'=>($facultyData['ShortName']!=null?$facultyData['ShortName']." ":"-"),
				'$[FACULTY_ADDRESS1]'=>($facultyData['Add1']!=null?$facultyData['Add1']." ":"-"),
				'$[FACULTY_ADDRESS2]'=>($facultyData['Add2']!=null?$facultyData['Add2']." ":""),
				'$[FACULTY_ADDRESS]'=>($facultyData['Add1']!=null?$facultyData['Add1']." ":"").($facultyData['Add2']!=null?$facultyData['Add2']." ":""),
				'$[FACULTY_PHONE]'=>($facultyData['Phone1']!=null?$facultyData['Phone1']." ":"").($facultyData['Phone2']!=null?", ".$facultyData['Phone2']." ":""),
				'$[FACULTY_FAX]'=>($facultyData['Fax']!=null?$facultyData['Fax']." ":""),
				'$[PROGRAME]'=>$program["program_name_indonesia"],
				'$[KELAS]'=>$program["GroupName"],
				'$[RANK]' => $rank,
		        '$[PRINT_DATE]'=>date('j M Y'),
				'$[REGISTRATION_DATE_START]'=> date ( 'j F Y' , strtotime ( $assessmentData['aaud_reg_start_date'] ) ),
				'$[REGISTRATION_DATE_END]'=> date ( 'j F Y' , strtotime ( $assessmentData['aaud_reg_end_date'] ) ),
				/*'$[PAKET_A_DATE_PAYMENT]'=> date ( 'j F Y' , strtotime ( $assessmentData['aaud_payment_start_date'] ) ),
				'$[PAKET_A_SP]' => number_format($paket_a_plan[0]['total_amount'], 2, '.', ','),
				'$[PAKET_A_BPP_POKOK]' => number_format($paket_a_plan[1]['total_amount'], 2, '.', ','),
				'$[PAKET_A_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_A_BPP_SKS_VALUE]' => number_format($paket_a_plan[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_A_BPP_SKS_AMOUNT]' => number_format($paket_a_plan[2]['total_amount'], 2, '.', ','),
				'$[PAKET_A_PRAKTIKUM]' => number_format($paket_a_plan[3]['total_amount'], 2, '.', ','),
				'$[PAKET_A_TOTAL]' => number_format($paket_a_plan[0]['total_amount'] + $paket_a_plan[1]['total_amount'] + $paket_a_plan[2]['total_amount'] + $paket_a_plan[3]['total_amount'], 2, '.', ',') ,

				'$[PAKET_B_C1_DATE_PAYMENT]'=>date ( 'j F Y' , strtotime ( $assessmentData['aaud_payment_start_date'] ) ),
				'$[PAKET_B_C1_SP]' => number_format($paket_b_plan_cicilan1[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C1_BPP_POKOK]' => number_format($paket_b_plan_cicilan1[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C1_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C1_BPP_SKS_VALUE]' => number_format($paket_b_plan_cicilan1[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C1_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan1[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C1_PRAKTIKUM]' => number_format($paket_b_plan_cicilan1[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C1_TOTAL]' => number_format($paket_b_plan_cicilan1[0]['total_amount'] + $paket_b_plan_cicilan1[1]['total_amount'] + $paket_b_plan_cicilan1[2]['total_amount'] + $paket_b_plan_cicilan1[3]['total_amount'], 2, '.', ',') ,
		
				'$[PAKET_B_C2_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+1 month' , strtotime ( $assessmentData['aaud_reg_start_date'] ) ) ),
				'$[PAKET_B_C2_SP]' => number_format($paket_b_plan_cicilan2[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C2_BPP_POKOK]' => number_format($paket_b_plan_cicilan2[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C2_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C2_BPP_SKS_VALUE]' => number_format($paket_b_plan_cicilan2[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C2_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan2[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C2_PRAKTIKUM]' => number_format($paket_b_plan_cicilan2[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C2_TOTAL]' => number_format($paket_b_plan_cicilan2[0]['total_amount'] + $paket_b_plan_cicilan2[1]['total_amount'] + $paket_b_plan_cicilan2[2]['total_amount'] + $paket_b_plan_cicilan2[3]['total_amount'], 2, '.', ',') ,
		
				'$[PAKET_B_C3_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+2 month' , strtotime ( $assessmentData['aaud_reg_end_date'] ) ) ),
				'$[PAKET_B_C3_SP]' => number_format($paket_b_plan_cicilan3[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C3_BPP_POKOK]' => number_format($paket_b_plan_cicilan3[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C3_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C3_SKS_VALUE]' => number_format($paket_b_plan_cicilan3[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C3_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan3[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C3_PRAKTIKUM]' => number_format($paket_b_plan_cicilan3[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C3_TOTAL]' => number_format($paket_b_plan_cicilan3[0]['total_amount'] + $paket_b_plan_cicilan3[1]['total_amount'] + $paket_b_plan_cicilan3[2]['total_amount'] + $paket_b_plan_cicilan3[3]['total_amount'], 2, '.', ',') ,
		
				'$[PAKET_B_C4_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+3 month' , strtotime ( $assessmentData['aaud_reg_end_date'] ) ) ),
				'$[PAKET_B_C4_SP]' => number_format($paket_b_plan_cicilan4[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C4_BPP_POKOK]' => number_format($paket_b_plan_cicilan4[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C4_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C4_SKS_VALUE]' => number_format($paket_b_plan_cicilan4[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C4_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan4[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C4_PRAKTIKUM]' => number_format($paket_b_plan_cicilan4[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C4_TOTAL]' => number_format($paket_b_plan_cicilan4[0]['total_amount'] + $paket_b_plan_cicilan4[1]['total_amount'] + $paket_b_plan_cicilan4[2]['total_amount'] + $paket_b_plan_cicilan4[3]['total_amount'], 2, '.', ',') ,
		
				'$[PAKET_B_C5_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+4 month' , strtotime ( $assessmentData['aaud_reg_end_date'] ) ) ),
				'$[PAKET_B_C5_SP]' => number_format($paket_b_plan_cicilan5[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C5_BPP_POKOK]' => number_format($paket_b_plan_cicilan5[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C5_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C5_SKS_VALUE]' => number_format($paket_b_plan_cicilan5[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C5_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan5[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C5_PRAKTIKUM]' => number_format($paket_b_plan_cicilan5[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C5_TOTAL]' => number_format($paket_b_plan_cicilan5[0]['total_amount'] + $paket_b_plan_cicilan5[1]['total_amount'] + $paket_b_plan_cicilan5[2]['total_amount'] + $paket_b_plan_cicilan5[3]['total_amount'], 2, '.', ',') ,
		
				'$[PAKET_B_C6_DATE_PAYMENT]'=>date ( 'F Y' , strtotime ( '+5 month' , strtotime ( $assessmentData['aaud_reg_end_date'] ) ) ),
				'$[PAKET_B_C6_SP]' => number_format($paket_b_plan_cicilan6[0]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C6_BPP_POKOK]' => number_format($paket_b_plan_cicilan6[1]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C6_BPP_SKS]' => $feeStructureProgramData['fsp_first_sem_sks'],
				'$[PAKET_B_C6_SKS_VALUE]' => number_format($paket_b_plan_cicilan6[2]['fsi_amount'], 2, '.', ','),
				'$[PAKET_B_C6_BPP_SKS_AMOUNT]' => number_format($paket_b_plan_cicilan6[2]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C6_PRAKTIKUM]' => number_format($paket_b_plan_cicilan6[3]['total_amount'], 2, '.', ','),
				'$[PAKET_B_C6_TOTAL]' => number_format($paket_b_plan_cicilan6[0]['total_amount'] + $paket_b_plan_cicilan6[1]['total_amount'] + $paket_b_plan_cicilan6[2]['total_amount'] + $paket_b_plan_cicilan6[3]['total_amount'], 2, '.', ',') ,
		
				'$[BALANCE_INSTALLMENT_PAKET_B]' => number_format( ( $paket_a_plan[0]['total_amount'] + $paket_a_plan[1]['total_amount'] + $paket_a_plan[2]['total_amount'] + $paket_a_plan[3]['total_amount'] ) - ( $paket_b_plan_cicilan1[0]['total_amount'] + $paket_b_plan_cicilan1[1]['total_amount'] + $paket_b_plan_cicilan1[2]['total_amount'] + $paket_b_plan_cicilan1[3]['total_amount'] ), 2, '.', ','),
				*/
				'$[LEARNING_DURATION]' => $learning_duration,
				'$[ESTIMASI_BIAYA]' => $biaya,
				'$[RECTOR_DATE]' => date('j M Y',strtotime($assessmentData['aaud_decree_date']))
		);
		
		require_once 'dompdf_config.inc.php';
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		$html_template_path = DOCUMENT_PATH."/template/OfferLetterUSM.html";
		
		$html = file_get_contents($html_template_path);
		
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);	
		}
		
		//program data
		global $program;
		$program = $feeStructureProgramData;
		
		//registration date
		global $reg_date;
		$reg_date = array(
						'REGISTRATION_DATE_START'=> $assessmentData['aaud_reg_start_date'],
						'REGISTRATION_DATE_END'=> $assessmentData['aaud_reg_end_date']
					);

		//date payment
		foreach($feeStructureData['payment_plan'] as $key=>$plan){
			$start = $assessmentData['aaud_reg_start_date'];
			$end = $assessmentData['aaud_reg_end_date'];
			
			foreach ($plan['plan_detail'] as $key2=>$installment){
				$reg_date['date_payment'][$key][$key2]['start'] = $start;
				$reg_date['date_payment'][$key][$key2]['end'] = $end;

				$end = date ( 'F Y' , strtotime ( '+1 month' , strtotime ( $end) ) );
			}
			
			$end = $assessmentData['aaud_reg_end_date'];
		}	
		
		
		//fee data
		global $fees;
		$fees = $feeStructureData['payment_plan'];
		
		//footer variable
		global $pes;
		$pes = $txnData["at_pes_id"];
		//echo $html;
		//exit;
		
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		$dompdf->render();
		
		//untuk stream ke student
		//$dompdf->stream($txnData["at_pes_id"]."_offer_letter.pdf");
		//untuk save ke db
		$pdf = $dompdf->output();
		//exit;
		
		//$location_path
		$location_path = "applicant/".date("mY")."/".$txnId;
		
		//output_directory_path
		$output_directory_path = DOCUMENT_PATH."/".$location_path;
		
		//create directory to locate file			
		if (!is_dir($output_directory_path)) {
	    	mkdir($output_directory_path, 0775, true);
		}
		
		//output filename 
		$output_filename = $txnData["at_pes_id"]."_offer_letter.pdf";
		
		//to rename output file			
		$output_file_path = $output_directory_path."/".$output_filename;

		file_put_contents($output_file_path, $pdf);
		
		//update file info
		/*$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
		$doc["ad_filepath"]=$location_path;
		$doc["ad_filename"]=$output_filename;
		$documentDB->updateDocument($doc,$txnId,45);*/
		
		//update file info
		$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
		$fileexist = $documentDB->getDataArray($txnId, 45);
		
		$doc["ad_filepath"]=$location_path;
		$doc["ad_filename"]=$output_filename;
		$doc["ad_appl_id"]=$txnId;
		$doc["ad_type"]=45;
		$doc["ad_createddt"]=date("Y-m-d");
		
		if($fileexist){
			$documentDB->updateDocument($doc,$txnId,45);
		}else{
			$documentDB->addData($doc);
		} 
		
		//regenerate performa invoice
		$proformaInvoiceDb = new Application_Model_DbTable_ProformaInvoice();
		$proformaInvoiceDb->regenerateProformaInvoice($txnId); 

	}
	
	public function accountAction(){
		$this->view->title = $this->view->translate("Account Statement");
		
		$auth = Zend_Auth::getInstance();    	        
    	$appl_id = $auth->getIdentity()->appl_id; 
    	
		$this->view->appl_id = $appl_id;
		$registration_id = $auth->getIdentity()->registration_id;
		$Dbinvoice=new Studentfinance_Model_DbTable_InvoiceMain();
		
		$studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
		if ($registration_id!='') {
			$student = $studentRegDB->getStudentInfo($registration_id);
			
			$activity=$Dbinvoice->isAnyOpenInvoice($registration_id);
			if ($activity!=0 && $student['IdProgram']!=60) $Dbinvoice->dispatcher($registration_id,$activity);
		}
    	//profile
    	$applicantProfileDb = new App_Model_Application_DbTable_ApplicantProfile();
    	$this->view->profile = $applicantProfileDb->getData($appl_id);
    	
    	//account
		$db = Zend_Db_Table::getDefaultAdapter();
		
    	$select_invoice = $db->select()
						->from(array('im'=>'invoice_main'),array(
							'record_date'=>'im.date_create',
							'description' => 'im.bill_description',
							'txn_type' => new Zend_Db_Expr ('"Invoice"'),
							'debit' =>'bill_amount',
							'credit' => new Zend_Db_Expr ('"0.00"'),
							'document' => 'bill_number',
							'invoice_no' => 'bill_number',
							'va' => 'va',
							'receipt_no' => new Zend_Db_Expr ('"-"')
						)
						)
						->where('im.appl_id = ?', $appl_id)
						->where("im.status != 'X'");
						
		$select_payment = $db->select()
						->from(
							array('pm'=>'payment_main'),array(
															'record_date'=>'pm.payment_date',
															'description' => 'pm.payment_description',
															'txn_type' => new Zend_Db_Expr ('"Payment"'),
															'debit' =>new Zend_Db_Expr ('"0.00"'),
															'credit' => 'amount',
															'document' => 'pbrd.bancs_journal_number',
															'invoice_no' => 'billing_no',
															'va' => 'va',
															'receipt_no' => 'pbrd.bancs_journal_number',
														)
						)
						->joinLeft(array('pbrd'=>'payment_bank_record_detail'),'pbrd.id = pm.transaction_reference', array())
						->where('pm.appl_id = ?', $appl_id);
										
		
		//credit note
		$select_creditnote = $db->select()
						->from(
							array('cn'=>'credit_note'),array(
															'record_date'=>'cn.cn_create_date',
															'description' => 'cn.cn_description',
															'txn_type' => new Zend_Db_Expr ('"Credit Note"'),
															'debit' =>new Zend_Db_Expr ('"0.00"'),
															'credit' => 'cn.cn_amount',
															'document' => new Zend_Db_Expr ('"null"'),
															'invoice_no' => 'cn.cn_billing_no',
															'va' => new Zend_Db_Expr ('"null"'),
															'receipt_no' => new Zend_Db_Expr ('"-"')
														)
						)
						->where('cn.appl_id = ?', $appl_id)
						->where('cn.cn_approver is not null')
						->where('cn.cn_approve_date is not null');
		
		//debit note
		$select_debitnote = $db->select()
		->from(
				array('dn'=>'debit_note'),array(
						'record_date'=>'dn.dn_create_date',
						'description' => 'dn.dn_description',
						'txn_type' => new Zend_Db_Expr ('"Debit Note"'),
						'debit' => 'dn.dn_amount',
						'credit' => new Zend_Db_Expr ('"0.00"'),
						'document' => new Zend_Db_Expr ('"null"'),
						'invoice_no' => 'dn.dn_billing_no',
						'va' => new Zend_Db_Expr ('"null"'),
						'receipt_no' => new Zend_Db_Expr ('"-"')
				)
		)
		->where('dn.appl_id = ?', $appl_id)
		->where('dn.dn_approver is not null')
		->where('dn.dn_approve_date is not null');

		//refund
		$select_refund = $db->select()
						->from(
							array('rfd'=>'refund'),array(
															'record_date'=>'rfd.rfd_approve_date',
															'description' => 'rfd.rfd_desc',
															'txn_type' => new Zend_Db_Expr ('"Refund"'),
															'debit' => 'rfd.rfd_amount',
															'credit' => new Zend_Db_Expr ('"0.00"'),
															'document' => new Zend_Db_Expr ('"null"'),
															'invoice_no' => new Zend_Db_Expr ('"-"'),
															'va' => new Zend_Db_Expr ('"null"'),
															'receipt_no' => new Zend_Db_Expr ('"-"')
														)
						)
						->where('rfd.rfd_appl_id  = ?', $appl_id)
						->where('rfd.rfd_approver_id is not null')
						->where('rfd.rfd_approve_date is not null');				
						
		$select = $db->select()
				    ->union(array($select_invoice, $select_payment, $select_creditnote, $select_debitnote, $select_refund),  Zend_Db_Select::SQL_UNION_ALL)
				    ->order("record_date");
		
   		//echo $select;
		//exit;				
		$row = $db->fetchAll($select);
		
		if(!$row){
			$row = null;
		}
//	echo var_dump($row);exit;
		$this->view->account = $row;

	}
	
	public function accountInvoiceAction(){
		if( $this->getRequest()->isXmlHttpRequest() ){
			$this->_helper->layout->disableLayout();
		}
		
		$appl_id = $this->_getParam('id', null);
		$this->view->appl_id = $appl_id;
		
		$db = Zend_Db_Table::getDefaultAdapter();
		//is student
		$select=$db->select()
				->from(array('ap'=>'applicant_profile'))
				->where('ap.appl_id=?',$appl_id)
				->where("ap.appl_role='1'");
		
		$student=false;
		if($db->fetchRow($select)) $student=true; 
		
		if ($student) {
			//get programid
			$select=$db->select()
			->from(array('sr'=>'tbl_studentregistration'))
			->where('sr.IdApplication=?',$appl_id);
			$row=$db->fetchRow($select);
			$programid=$row['IdProgram'];
		} 
		/*else {
			$select=$db->select()
			->from(array('sr'=>'applicant_program'),array())
			->join(array('tr'=>'applicant_transaction'),'sr.ap_at_trans_id=tr.at_trans_id',array())
			->join(array('pr'=>'tbl_program'),'pr.ProgramCode=sr.ap_prog_code',array('IdProgram'))
			->where('sr.ap_usm_status="1" and tr.at_appl_type="1"')
			->ORwhere(' tr.at_appl_type="2"')
			->where('tr.at_appl_id=?',$appl_id);
			$row=$db->fetchRow($select);
			$programid=$row['IdProgram'];
			echo $programid;exit;
		}*/
		
		
	
		
		$select = $db->select()
						->from(array('im'=>'invoice_main'),array(
							'id'=>'im.id',
							'IdStudentRegistration'=>'im.IdStudentRegistration',
							'record_date'=>'im.date_create',
							'description' => 'im.bill_description',
							'txn_type' => new Zend_Db_Expr ('"Invoice"'),
							'debit' =>'bill_amount',
							'credit' => new Zend_Db_Expr ('"0.00"'),
							'document' => 'bill_number',
							'bill_amount' => 'bill_amount',
							'bill_paid' => 'bill_paid',
							'bill_balance' => 'bill_balance',
							'cn_amount' => 'cn_amount',
							'dn_amount' => 'dn_amount',
							'va'=>'va',
'idactivity'=>'idactivity',
							'status' => 'status'
							)
						)
						->joinLeft(array('ppd'=>'payment_plan_detail'), 'ppd.ppd_invoice_id = im.id', array('ppd_id'))
						->where('im.appl_id = ?', $appl_id)
						->where("im.status != 'X'")
						->order("im.date_create")
						->order("im.bill_number");
						
		$row = $db->fetchAll($select);
		//if ($student) {
			foreach ($row as $key=>$r) {
				//get bank id of items
				$invoiceid=$r['id'];
				if ($student) {
					$select = $db->select()
							->from(array('fsi'=>'invoice_detail'),array())
							->join(array('fia'=>'fee_item_account'),'fsi.fi_id=fia.fiacc_fee_item',array())
							->join(array('bank'=>'tbl_bank'),'fia.fiacc_bank=bank.IdBank',array('IdBank','BankName'))
							->where('fia.fiacc_program_id=?',$programid)
							->where('fsi.invoice_main_id=?',$invoiceid);
				
					$bank=$db->fetchRow($select);
					if (!$bank) $bank=array('IdBank'=>'1','BankName'=>'BNI46');
				} else $bank=array('IdBank'=>'1','BankName'=>'BNI46');
				$row[$key]=array_merge($row[$key],$bank);
			}
		
		if(!$row){
			$row = null;
		}
		//echo var_dump($row);exit;
		$this->view->invoice = $row;
	}
	
	public function accountPaymentAction(){
		if( $this->getRequest()->isXmlHttpRequest() ){
			$this->_helper->layout->disableLayout();
		}
		
		$appl_id = $this->_getParam('id', null);
		$this->view->appl_id = $appl_id;
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
						->from(
							array('pm'=>'payment_main'),array(
															'record_date'=>'pm.payment_date',
															'description' => 'pm.payment_description',
															'txn_type' => new Zend_Db_Expr ('"Payment"'),
															'debit' =>new Zend_Db_Expr ('"0.00"'),
															'credit' => 'amount',
															'document' => 'pbrd.bancs_journal_number',
															'payment_mode' => 'payment_mode'
														)
						)
						->joinLeft(array('pbrd'=>'payment_bank_record_detail'),'pbrd.id = pm.transaction_reference', array())
						->where('pm.appl_id = ?', $appl_id);
						
		$row = $db->fetchAll($select);
		
		if(!$row){
			$row = null;
		}
		
		$this->view->payment = $row;
	}
	
	public function accountPrintBankSlipAction(){
		
		$invoice_id = $this->_getParam('invoice_id', null);
		$bank_id = $this->_getParam('bank_id', null);
		$bankslip = new icampus_Function_Application_BankSlip();
		//echo $bank_id;exit;
		$bankslip->printBankSlip($invoice_id,$bank_id);
		exit;
	}
	
	
	public function downloadAction(){
		
		$txnId = $this->_getParam('txnId', null);
		$type = $this->_getParam('type', null);
		
		$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id; 
    	
    	$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();    	
    	
		if($txnId!=0 && $transactionDb->checkValidApplicant($txnId, $appl_id)){
			    
	    		$applicantDocumentDb = new App_Model_Application_DbTable_ApplicantDocument();	    		
	    		$docData = $applicantDocumentDb->getData($txnId,$type);
	    		
				if($docData){
	    			$this->view->file_path =  DOCUMENT_PATH.DIRECTORY_SEPARATOR.$docData['ad_filepath'].DIRECTORY_SEPARATOR.$docData['ad_filename'];
	    		}
	    		
	    		
		} //end if txn
		
	}//end if function
	
	
	public function generateAgreementLetterOld($txnId){
		
		$agreementletter = new icampus_Function_Application_Agreementletter();
		
		$agreementletter->generateAgreementLetter($txnId);
		
	}
	
	private function checkIntakeSession($txnId){
		
		$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
		
		//txn data
		$txnData = $transactionDb->getTransactionData($txnId);
		//echo var_dump($txnData);exit;
		//Check Intake
		$intakeDB = new App_Model_Record_DbTable_Intake();
		if ($txnData['at_intake']=="" || $txnData['at_intake']==0) return;
		$intake = $intakeDB->getActiveIntake($txnData['at_intake']);
		
		if(!$intake){
			//redirect home
			$msg = $this->view->translate('Invalid intake');
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index','msg'=>$msg),'default',true));
		}
	}
	
	private function generateLulus1Letter($txnId){
	
		//get applicant info
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $applicantDB->getAllProfile($txnId);
    	
    	//get transaction info
    	$applicantTxnDB = new App_Model_Application_DbTable_ApplicantTransaction();
		$txnData = $applicantTxnDB->getTransaction($txnId);
		
		//get assessment data pssb
		if($txnData['at_appl_type']==2){
			$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessment();
			$assessmentData = $assessmentDb->getData($txnId);
			$nomor=$assessmentData['asd_nomor'];
            $selectionName = 'Program Seleksi Siswa Berpotensi';
		}
		//get assessment data USM
		if($txnData['at_appl_type']==1){
			$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessmentUsm();
			$assessmentData = $assessmentDb->getData($txnId);
			$nomor=$assessmentData['aaud_nomor'];
            $selectionName = 'Ujian Saringan Masuk';
		}		
		//getapplicantprogram
		$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
		$program = $appProgramDB->getProgramFaculty($txnId);
    			
		//program data
		$programDb = new App_Model_General_DbTable_Program();
		$programData = $programDb->fngetProgramData($program[0]['program_id']);
		
		
		//faculty data
		$collegeMasterDb = new App_Model_General_DbTable_Collegemaster();
		$facultyData = $collegeMasterDb->fngetCollegemasterData($program[0]['faculty_id']);
		
		//get applicant parents info
    	$familyDB =  new App_Model_Application_DbTable_ApplicantFamily();
    	$father = $familyDB->fetchdata($applicant["appl_id"],20); //father's    	
    	
    	//get next intake
    	$intakeDb = new App_Model_Record_DbTable_Intake();
    	$intakeData = $intakeDb->getData($txnData['at_intake']);
    	
    	
    	//get ptest schedule for TPA
    	$applicant_ptestDb = new App_Model_Application_DbTable_ApplicantPtest();
    	$ptest_data = $applicant_ptestDb->getTpaExamSchedule($txnData['at_trans_id']);
    	
    	

		
		$address = "";
		if( isset($applicant["appl_address1"]) && $applicant["appl_address1"]!=""){
			$address = $address . $applicant["appl_address1"]."<br />";
		}
		if( isset($applicant["appl_address2"]) && $applicant["appl_address2"]!=""){
			$address = $address . $applicant["appl_address2"]."<br />";
		}
		if( isset($applicant["CityName"]) && $applicant["CityName"]!=""){
			$address = $address . $applicant["CityName"]."<br />";
		}
		if( isset($applicant["appl_postcode"]) && $applicant["appl_postcode"]!=""){
			$address = $address . $applicant["appl_postcode"]."<br />";
		}
		if( isset($applicant["StateName"]) && $applicant["StateName"]!=""){
			$address = $address . $applicant["StateName"]."<br />";
		}
		
		$fieldValues = array(
				'$[NO_PES]'=>$txnData["at_pes_id"],
		        '$[NOMOR]'=>$nomor,
				'$[LAMPIRAN]'=>"-",
				'$[APPLICATIONTYPE]'=> $selectionName,
		        '$[TITLE_TEMPLATE]'=>$this->view->translate("Pemberitahuan diterima sebagai calon Mahasiswa di Universitas Trisakti"),
		        '$[APPLICANT_NAME]'=>$applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
				'$[PARENTNAME]'=>$father["af_name"],
		        '$[ADDRESS]' =>$address,			
		    	'$[ACADEMIC_YEAR]'=>$txnData['ay_code'],
				'$[PERIOD]'=>$txnData['ap_desc'],
				'$[FACULTY]'=>$program[0]["faculty2"],
				'$[FACULTY_NAME]'=>($facultyData['ArabicName']!=null?$facultyData['ArabicName']." ":"-"),
				'$[FACULTY_SHORTNAME]'=>($facultyData['ShortName']!=null?$facultyData['ShortName']." ":"-"),
				'$[FACULTY_ADDRESS1]'=>($facultyData['Add1']!=null?$facultyData['Add1']." ":"-"),
				'$[FACULTY_ADDRESS2]'=>($facultyData['Add2']!=null?$facultyData['Add2']." ":""),
				'$[FACULTY_ADDRESS]'=>($facultyData['Add1']!=null?$facultyData['Add1']." ":"").($facultyData['Add2']!=null?$facultyData['Add2']." ":""),
				'$[FACULTY_PHONE]'=>($facultyData['Phone1']!=null?$facultyData['Phone1']." ":"").($facultyData['Phone2']!=null?", ".$facultyData['Phone2']." ":""),
				'$[FACULTY_FAX]'=>($facultyData['Fax']!=null?$facultyData['Fax']." ":""),
				'$[PROGRAME]'=>$program[0]["program_name_indonesia"],
		        '$[PRINT_DATE]'=>date('j M Y'),
				'$[RECTOR_DATE]' => date('j M Y',strtotime($assessmentData['asd_decree_date'])),
				'$[TEST_DATE]' => date('j M Y',strtotime($ptest_data['aps_test_date'])),
				'$[TEST_TIME]' => $ptest_data['aps_start_time'],
				'$[TEST_PLACE]' => $ptest_data['av_room_name']
		);
		
		//data USM
		if($txnData['at_appl_type']==1){
			$fieldValues['$[RECTOR_DATE]']=date('j M Y',strtotime($assessmentData['aaud_decree_date']));
			
		}
		

		require_once 'dompdf_config.inc.php';
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		$html_template_path = DOCUMENT_PATH."/template/Lulus1Letter.html";
		
		$html = file_get_contents($html_template_path);
		
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);	
		}	
		
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		$dompdf->render();
		
		
		//$location_path
		$location_path = "applicant/".date("mY")."/".$txnId;
		
	
		//untuk stream ke student
		//$dompdf->stream($txnData["at_pes_id"]."_offer_letter.pdf");
			
		//untuk save ke db
		$pdf = $dompdf->output();
		//exit;
		
		//output_directory_path
		$output_directory_path = DOCUMENT_PATH."/".$location_path;
		
		//create directory to locate file			
		if (!is_dir($output_directory_path)) {
	    	mkdir($output_directory_path, 0775);
		}
		
		//output filename 
		$output_filename = $txnData["at_pes_id"]."_lulus1.pdf";
		
		//to rename output file			
		$output_file_path = $output_directory_path."/".$output_filename;
						
		file_put_contents($output_file_path, $pdf);
		
				
		//update file info
		$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
		$fileexist = $documentDB->getDataArray($txnId, 63);
		
		$doc["ad_filepath"]=$location_path;
		$doc["ad_filename"]=$output_filename;
		$doc["ad_appl_id"]=$txnId;
		$doc["ad_type"]=63;
		$doc["ad_createddt"]=date("Y-m-d");
		
		
		if($fileexist){
			$documentDB->updateDocument($doc,$txnId,63);
		}else{
			$documentDB->addData($doc);
		}	
	
	}
    
    public function sendemailque($appl_id)
    {
        $appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
        
        // $name = $applicant['appl_fname'].' ';
        
        // if($applicant['appl_mname'] != '')
        // {
            // $name .= $applicant['appl_mname'].' ';
        // }
        
        // if($applicant['appl_lname'] != '')
        // {
            // $name .= $applicant['appl_lname'].' ';
        // }
        
        // $name = rtrim($name,' ');
        
        //get Email Template based on preferred Language
        
        if($applicant['verifyKey'] != NULL)
        {
            $path = APP_HOSTNAME.'/authentication/verify/key/'.$applicant['verifyKey'];
            
            $verifyLink = "<a href=\"".$path."\">".$path."</a>";
            $templateDB = new App_Model_General_DbTable_EmailTemplate();
            $templateData = $templateDB->getData(9,$applicant["appl_prefer_lang"]);
            
            
            $templateMail = $templateData['body'];				
            //$templateMail = str_replace("[Candidate]",$applicant["appl_fname"],$templateMail);
            //$templateMail = str_replace("[EmailApplicant]",$applicant["appl_email"],$templateMail);
            $templateMail = str_replace("[Verify]",$verifyLink,$templateMail);				
            $templateMail = str_replace("[FIRST_NAME]",$applicant["appl_fname"],$templateMail);
            $templateMail = str_replace("[MIDDLE_NAME]",$applicant["appl_mname"],$templateMail);
            $templateMail = str_replace("[LAST_NAME]",$applicant["appl_lname"],$templateMail); 
            
            $data = array(
            'recepient_email' => $applicant['appl_email'],
            'subject'   => $templateData['subject'],
            'content'   => $templateMail,
            'attachment_path' => NULL,
            'attachment_filename' => NULL
            );
            
            $Email = new App_Model_System_DbTable_Email();
            $Email->addData($data);
            
            
        }
    }
    
    
    public function jadwalPendaftaranAction(){
    	
    	if ($this->getRequest()->isPost()) {    		
    		
    		$formData = $this->getRequest()->getPost();      		
			
			$rds_id = $formData["rds_id"];
			$txn_id = $formData["txn_id"];
			
	    	 //transaction data
	        $transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
	    	$transactionDb->updateData(array('rds_id'=>$rds_id),$txn_id);
	    	
	    	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
    	}
    }
    
    public function downloadBuktiReservasiAction(){
    	
    	$txn_id = $this->_getParam('txn_id', null);
    	
    	setlocale(LC_ALL, 'id_ID');
    	
    	//get applicant info
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $applicantDB->getAllProfile($txn_id);
    	
    	//get transaction info
    	$applicantTxnDB = new App_Model_Application_DbTable_ApplicantTransaction();
		$txnData = $applicantTxnDB->getTransaction($txn_id);
			
		//getapplicantprogram
		$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
		$program = $appProgramDB->getProgramOffered($txn_id,$txnData['at_appl_type']);
		
		//faculty data
		$collegedB = new GeneralSetup_Model_DbTable_Collegemaster();
        $facultyData = $collegedB->getFullInfoCollege($program['faculty_id']);
		//$facultyData = $collegeMasterDb->fngetCollegemasterData($program[0]['faculty_id']);
		
		$scheduleDb = new App_Model_Registration_DbTable_RegDateSetup();
		$schedule = $scheduleDb->getInfo($txn_id);
		
		$fieldValues = array(			
				'$[APPLICANT_NAME]'=>$applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
		        '$[NO_PES]'=>$txnData["at_pes_id"],
				'$[PROGRAMME]'=>$program["program_name_indonesia"],
		        '$[FACULTY]'=>($facultyData['ArabicName']!=null?$facultyData['ArabicName']." ":"-"),						
		    	'$[INTAKE]'=>$txnData["intake"],
				'$[REG_DATE]'=>strftime("%A, %d %B %Y", strtotime($schedule['rds_date'])),
				'$[REG_START_TIME]'=>date('H:i',strtotime($schedule['rds_start_time'])),
				'$[REG_END_TIME]'=>date('H:i',strtotime($schedule['rds_end_time'])),
		 	    '$[PROGRAM]'=>$program["program_name_indonesia"],
    	        '$[FACULTY_NAME]'=>'FAKULTAS '.$facultyData["ArabicName"],
				'$[ADDRESS]'=>ucwords(strtolower($facultyData["Add1"])).' '.ucwords(strtolower($facultyData["Add2"])).' '.ucwords(strtolower($facultyData["CityName"])).' '.ucwords(strtolower($facultyData["StateName"])),
				'$[PHONE]'=>$facultyData["Phone1"],
				'$[EMAIL]'=>$facultyData["Email"]
         
		);
    	
		require_once 'dompdf_config.inc.php';
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		$html_template_path = DOCUMENT_PATH."/template/BuktiReservasi.html";
		
		$html = file_get_contents($html_template_path);
		
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);	
		}
		
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		$dompdf->render();

		//output filename 
		$output_filename = "Bukti_Reservasi.pdf";
		
		//$dompdf = $dompdf->output();
		$dompdf->stream($output_filename);						
							
		//to rename output file						
	    $output_file_path = DOCUMENT_PATH."/student/".$output_filename;
		
		file_put_contents($output_file_path, $dompdf);
		
		$this->view->file_path = $output_file_path;
		
		
    }
    
    public function approveGradeAction()
    {
        $this->_helper->layout->disableLayout();
        
        $trans_id = $this->_getParam('trans_id',0);
        $this->view->title="Credit Transfer: Status Persetujuan";
        $CreditApply = new App_Model_Application_DbTable_CreditTransfer();
        $credit_apply = $CreditApply->getDataByTransaction($trans_id);
        
        $credit_transfer = null;
        $programId = $credit_apply['IdProgram'];
        $transactionid = $credit_apply['transaction_id'];
         
        $dbprofile=new App_Model_Application_DbTable_ApplicantProfile();
        $student=$dbprofile->getProfileByTransaction($transactionid);
        $this->view->student=$student;
        //exit;
        if ($student!=array())   {
        
        	$dbProgram=new GeneralSetup_Model_DbTable_Program();
        	$program=$dbProgram->fngetProgramData($programId);
        	$this->view->program=$program;
        	if ($program["Dept_Bahasa"]=='-') $jurusan=$program["ArabicName"];
        	else $jurusan=$program["Dept_Bahasa"].' / '.$program["ArabicName"];
        	if ($program["Departement"]=='-') $jurusanEng=$program["ProgramName"];
        	else $jurusanEng=$program["Departement"].' / '.$program["ProgramName"];
        	$this->view->jurusan=$jurusan;
        	$this->view->jurusanEng=$jurusanEng;
        	//get info college
        	$collegedB = new GeneralSetup_Model_DbTable_Collegemaster();
        	$college = $collegedB->getFullInfoCollege($program["IdCollege"]);
        
        	//get photo student
        	$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
        	$file = $uploadFileDb->getFile($transactionid,51);
        	$photo_url=null;
        	if(isset($file["pathupload"])){
        		if (file_exists($file["pathupload"])) {
        
        			$fnImage = new icampus_Function_General_Image();
        			$photo_url = $fnImage->getImagePath($file['pathupload'],100,123);
        			//$photo_url = str_replace("/var/www/html/triapp", "", $file["pathupload"]);
        
        			//$photo_url  = $photo_url;
        		}else{
        			$photo_url = "http://".ONNAPP_HOSTNAME."/images/no-photo.jpg";
        		}
        	}else{
        		$photo_url = "http://".ONNAPP_HOSTNAME."/images/no-photo.jpg";
        	}
        }
        $this->view->photo=$photo_url;
       
        
        $CreditTransfer = new App_Model_Application_DbTable_CreditTransferSubject();
        $credit_transfer = $CreditTransfer->getDataByApplyIdPdf($credit_apply['idApply']);
       
        $this->view->credit_transfer = $credit_transfer;
        
       
    }
    
    private function generatePssbConfimationLetterPdf($txnId){
    
    	$registry = Zend_Registry::getInstance();
    	$locale = $registry->get('Zend_Locale');
    
    	$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
    	$txnData = $transactionDb->getTransaction($txnId);
    
    	//get applicant profile
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $appProfileDB->getTransProfile($txnData['at_appl_id'],$txnId);
    
    	//get next academic year
    	$ayearDb = new App_Model_Record_DbTable_AcademicYear();
    	$academic_year = $ayearDb->getNextAcademicYearData();
    
    	//--------get applicant program  -----------
    	$appprogramDB = new App_Model_Application_DbTable_ApplicantProgram();
    	$app_program = $appprogramDB->getPlacementProgram($txnId);
    
    	$program_data["program_code1"]="0";
    	$program_data["program_code2"]="0";
    
    	$i=1;
    	foreach($app_program as $program){
    		$program_data["program_name".$i] = $program["program_name"];
    		$program_data["faculty_name".$i] = $program["faculty"];
    		$program_data["program_code".$i] = $program["program_code"];
    		 
    		$i++;
    	}
    	 
    	 
    	//pengumumam hasil seleksi
    	setlocale (LC_ALL, $locale);
    
    	//0=sunday onwards
    	$today = date("w");
    
    	if($today<=2){
    		$selection_date = strftime('%e %B %Y',  strtotime("this Saturday")).', '.strftime('%e %B %Y',  strtotime("second Saturday")).' atau '.strftime('%e %B %Y',  strtotime("third Saturday"));
    	}else{
    		$selection_date = strftime('%e %B %Y',  strtotime("second Saturday")).', '.strftime('%e %B %Y',  strtotime("third Saturday")).' atau '.strftime('%e %B %Y',  strtotime("fourth Saturday"));
    	}
    
    	if($applicant["appl_gender"]==1) $gender="LAKI-LAKI";
    	if($applicant["appl_gender"]==2) $gender="PEREMPUAN";
    
    	$fieldValues = array (
    			'$[Applicantname]' => $applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
    			'$[dob]' => $applicant["appl_dob"],
    			'$[Sex]' => $gender,
    			'$[Address]' => $applicant["appl_address1"].','.$applicant["appl_address2"],
    			'$[phone]' => $applicant["appl_phone_hp"],
    			'$[email]' => $applicant["appl_email"],
    			'$[Discipline]' => $applicant["discipline"] ,
    			'$[PROGRAM1]' => $program_data["program_name1"],
    			'$[submission_date]'=>date('j M Y'),
    			'$[ACADEMICYEAR]'=>$academic_year["ay_code"],
    			'$[registration_date]'=>date('j M Y'),
    			'$[withdrawal_date]'=>date('j M Y'),
    			'$[seleksi_date]'=>$selection_date
    			// 'registration_date'=>$registrasi["StartDate"].' s.d '.$registrasi["EndDate"],
    			// 'withdrawal_date'=>$withdrawal["StartDate"].' s.d '.$withdrawal["EndDate"]
    
    	);
    
    	$educationDB = new App_Model_Application_DbTable_ApplicantEducation();
    
    	$education = $educationDB->getEducationDetail($txnId);
    
    	if($education==null){
    		$education = $educationDB->getEducationDetailApplId($applicant['appl_id']);
    	}
    	 
    	global $matapelajaran;
    	$matapelajaran=$education;
    	 
    	//echo var_dump($matapelajaran);exit;
    	// ------- create PDF File section	--------
    
    	//filename
    	$output_filename = $txnData['at_pes_id']."_pssb_confirmation_letter.pdf";
    	 
    	//directory to locate file
    	$app_directory_path = DOCUMENT_PATH."/applicant/".date("mY");
    
    	//create directory to locate file
    	if (!is_dir($app_directory_path)) {
    		mkdir($app_directory_path, 0775,true);
    	}
    
    	$output_directory_path = DOCUMENT_PATH."/applicant/".date("mY")."/".$txnId;
    
    	//create directory to locate file
    	if (!is_dir($output_directory_path)) {
    		mkdir($output_directory_path, 0775,true);
    	}
    
    	//$location_path
    	$location_path = "applicant/".date("mY")."/".$txnId;
    
    	//to create PDF File
    	//$this->mailmergeConnection($filepath, $fieldValues,$education,$output_directory_path, $output_filename);
    	// ------- create PDF File section	--------
    	try{
    		require_once 'dompdf_config.inc.php';
    		 
    		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
    		$autoloader->pushAutoloader('DOMPDF_autoload');
    
    		//template path
    		$html_template_path = DOCUMENT_PATH."/template/pssb_confirmation_letter.html";
    
    
    		$html = file_get_contents($html_template_path);
    		//echo $html;exit;
    		//replace variable
    		foreach ($fieldValues as $key=>$value){
    			$html = str_replace($key,$value,$html);
    		}
    		 
    		 
    		$dompdf = new DOMPDF();
    		$dompdf->load_html($html);
    		//$dompdf->load_html('Ok');
    		$dompdf->set_paper('a4', 'potrait');
    		$dompdf->render();
    		
    		//$dompdf->stream($output_filename);
    		$dompdf = $dompdf->output();
    		 
    
    		 
    		//to rename output file
    		$output_file_path = $output_directory_path."/".$output_filename;
    		 
    		file_put_contents($output_file_path, $dompdf);
    		 
    		// ------- End PDF File section	--------
    		 
    		$status = true;
    		 
    	}catch (Exception $e) {
    		$status = false;
    	}
    	 
    	// ------- End PDF File section	--------
    
    	//update file info
    	$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
    	$fileexist = $documentDB->getDataArray($txnId, 31);
    
    	$doc["ad_filepath"]=$location_path;
    	$doc["ad_filename"]=$output_filename;
    	$doc["ad_createddt"]=date("Y-m-d");
    
    
    	if($fileexist){
    		$documentDB->updateDocument($doc,$txnId,31);
    	}else{
    		$doc['ad_appl_id'] = $txnId;
    		$doc['ad_type'] = 31;
    		 
    		$documentDB->addData($doc);
    	}
    }
    
    private function generateInvitationConfimationLetterPdf($txnId){
    
    	$registry = Zend_Registry::getInstance();
    	$locale = $registry->get('Zend_Locale');
    
    	$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
    	$txnData = $transactionDb->getTransaction($txnId);
    
    	//get applicant profile
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $appProfileDB->getTransProfile($txnData['at_appl_id'],$txnId);
    
    	//get next academic year
    	$ayearDb = new App_Model_Record_DbTable_AcademicYear();
    	$academic_year = $ayearDb->getNextAcademicYearData();
    
    	//--------get applicant program  -----------
    	$appprogramDB = new App_Model_Application_DbTable_ApplicantProgram();
    	$app_program = $appprogramDB->getPlacementProgram($txnId);
    
    	$program_data["program_code1"]="0";
    	$program_data["program_code2"]="0";
    
    	$i=1;
    	foreach($app_program as $program){
    		$program_data["program_name".$i] = $program["program_name"];
    		$program_data["faculty_name".$i] = $program["faculty"];
    		$program_data["program_code".$i] = $program["program_code"];
    		 
    		$i++;
    	}
    
    
    	//pengumumam hasil seleksi
    	setlocale (LC_ALL, $locale);
    
    	//0=sunday onwards
    	$today = date("w");
    
    	if($today<=2){
    		$selection_date = strftime('%e %B %Y',  strtotime("this Saturday")).', '.strftime('%e %B %Y',  strtotime("second Saturday")).' atau '.strftime('%e %B %Y',  strtotime("third Saturday"));
    	}else{
    		$selection_date = strftime('%e %B %Y',  strtotime("second Saturday")).', '.strftime('%e %B %Y',  strtotime("third Saturday")).' atau '.strftime('%e %B %Y',  strtotime("fourth Saturday"));
    	}
    
    	if($applicant["appl_gender"]==1) $gender="LAKI-LAKI";
    	if($applicant["appl_gender"]==2) $gender="PEREMPUAN";
    
    	$fieldValues = array (
    			'$[Applicantname]' => $applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
    			'$[dob]' => $applicant["appl_dob"],
    			'$[Sex]' => $gender,
    			'$[Address]' => $applicant["appl_address1"].','.$applicant["appl_address2"],
    			'$[phone]' => $applicant["appl_phone_hp"],
    			'$[email]' => $applicant["appl_email"],
    			'$[Discipline]' => $applicant["discipline"] ,
    			'$[PROGRAM1]' => $program_data["program_name1"],
    			'$[submission_date]'=>date('j M Y'),
    			'$[ACADEMICYEAR]'=>$academic_year["ay_code"],
    			'$[registration_date]'=>date('j M Y'),
    			'$[withdrawal_date]'=>date('j M Y'),
    			'$[seleksi_date]'=>$selection_date
    			// 'registration_date'=>$registrasi["StartDate"].' s.d '.$registrasi["EndDate"],
    			// 'withdrawal_date'=>$withdrawal["StartDate"].' s.d '.$withdrawal["EndDate"]
    
    	);
    
    	$educationDB = new App_Model_Application_DbTable_ApplicantEducation();
    
    	$education = $educationDB->getEducationDetail($txnId);
    
    	if($education==null){
    		$education = $educationDB->getEducationDetailApplId($applicant['appl_id']);
    	}
    
    	global $matapelajaran;
    	$matapelajaran=$education;
    
    	//echo var_dump($matapelajaran);exit;
    	// ------- create PDF File section	--------
    
    	//filename
    	$output_filename = $txnData['at_pes_id']."_pssb_confirmation_letter.pdf";
    
    	//directory to locate file
    	$app_directory_path = DOCUMENT_PATH."/applicant/".date("mY");
    
    	//create directory to locate file
    	if (!is_dir($app_directory_path)) {
    		mkdir($app_directory_path, 0775,true);
    	}
    
    	$output_directory_path = DOCUMENT_PATH."/applicant/".date("mY")."/".$txnId;
    
    	//create directory to locate file
    	if (!is_dir($output_directory_path)) {
    		mkdir($output_directory_path, 0775,true);
    	}
    
    	//$location_path
    	$location_path = "applicant/".date("mY")."/".$txnId;
    
    	//to create PDF File
    	//$this->mailmergeConnection($filepath, $fieldValues,$education,$output_directory_path, $output_filename);
    	// ------- create PDF File section	--------
    	try{
    		require_once 'dompdf_config.inc.php';
    		 
    		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
    		$autoloader->pushAutoloader('DOMPDF_autoload');
    
    		//template path
    		$html_template_path = DOCUMENT_PATH."/template/invitation_confirmation_letter.html";
    
    
    		$html = file_get_contents($html_template_path);
    		//echo $html;exit;
    		//replace variable
    		foreach ($fieldValues as $key=>$value){
    			$html = str_replace($key,$value,$html);
    		}
    		 
    		 
    		$dompdf = new DOMPDF();
    		$dompdf->load_html($html);
    		//$dompdf->load_html('Ok');
    		$dompdf->set_paper('a4', 'potrait');
    		$dompdf->render();
    
    		//$dompdf->stream($output_filename);
    		$dompdf = $dompdf->output();
    		 
    
    		 
    		//to rename output file
    		$output_file_path = $output_directory_path."/".$output_filename;
    		 
    		file_put_contents($output_file_path, $dompdf);
    		 
    		// ------- End PDF File section	--------
    		 
    		$status = true;
    		 
    	}catch (Exception $e) {
    		$status = false;
    	}
    
    	// ------- End PDF File section	--------
    
    	//update file info
    	$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
    	$fileexist = $documentDB->getDataArray($txnId, 31);
    
    	$doc["ad_filepath"]=$location_path;
    	$doc["ad_filename"]=$output_filename;
    	$doc["ad_createddt"]=date("Y-m-d");
    
    
    	if($fileexist){
    		$documentDB->updateDocument($doc,$txnId,31);
    	}else{
    		$doc['ad_appl_id'] = $txnId;
    		$doc['ad_type'] = 31;
    		 
    		$documentDB->addData($doc);
    	}
    }
    
    private function generateCredittransferConfimationLetterPdf($txnId){
    
    	$registry = Zend_Registry::getInstance();
    	$locale = $registry->get('Zend_Locale');
    
    	$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
    	$txnData = $transactionDb->getTransaction($txnId);
    	$IdIntake=$txnData['at_intake'];
    	
    	//get applicant profile
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $appProfileDB->getTransProfile($txnData['at_appl_id'],$txnId);
    
    	//get next academic year
    	$ayearDb = new App_Model_Record_DbTable_AcademicYear();
    	$academic_year = $ayearDb->getNextAcademicYearData();
    
    	//--------get applicant program  -----------
    	$appprogramDB = new App_Model_Application_DbTable_ApplicantProgram();
    	$app_program = $appprogramDB->getPlacementProgram($txnId);
    
    	$programDB = new App_Model_Application_DbTable_ApplicantProgram();
									$app_program = $programDB->getPlacementProgram($txnId);
									$dbCredit=new App_Model_Application_DbTable_CreditTransfer();
									$dbCreditSubject=new App_Model_Application_DbTable_CreditTransferSubject();
									$program_data["program_name1"]='';
									$program_data["program_name2"]='';
									$i=1;
									foreach($app_program as $program){
					
									if ($locale=="en_US"){
										$program_data["program_name".$i] = $program["program_name"];
									}else if ($locale=="id_ID"){
									$program_data["program_name".$i] = $program["program_name_indonesia"];
									}
					
											$i++;
									}
										
									 
									$today = date("w");
										
									if($today<=2){
										$selection_date = strftime('%e %B %Y',  strtotime("this Saturday")).', '.strftime('%e %B %Y',  strtotime("second Saturday")).' atau '.strftime('%e %B %Y',  strtotime("third Saturday"));
									}else{
										$selection_date = strftime('%e %B %Y',  strtotime("second Saturday")).', '.strftime('%e %B %Y',  strtotime("third Saturday")).' atau '.strftime('%e %B %Y',  strtotime("fourth Saturday"));
									}
										
										
									//get applicant program applied
									$programDB = new App_Model_Application_DbTable_ApplicantProgram();
									$app_program = $programDB->getPlacementProgram($txnId);
										
									 
									 //get photo student
									$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
									$file = $uploadFileDb->getFile($txnId,51);
									
									if(isset($file["pathupload"])){
									if (file_exists($file["pathupload"])) {
									$fnImage = new icampus_Function_General_Image();
									$photo_url = "http://".ONNAPP_HOSTNAME.$fnImage->getImagePath($file['pathupload'],100,123);
									//$photo_url = str_replace("/var/www/html/triapp","http://".ONNAPP_HOSTNAME."/", $file["pathupload"]);
									}else{
									$photo_url = "http://".ONNAPP_HOSTNAME."/images/no-photo.jpg";
									}
									}else{
									$photo_url = "http://".ONNAPP_HOSTNAME."/images/no-photo.jpg";
									}		 
									//once submmitted update status=CLOSE
									 
										
									if($applicant["appl_gender"]==1) $gender="LAKI-LAKI";
									if($applicant["appl_gender"]==2) $gender="PEREMPUAN";
									global $application;
									global $subjects;
									$application=$dbCredit->getDataByTransaction($txnId);
									$subjects=$dbCreditSubject->getDataByApplyId($application['idApply']);
									$fieldValues = array (
											'$[Applicantname]' => $applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
											'$[dob]' => $applicant["appl_dob"],
											'$[Sex]' => $gender,
											'$[Address]' => $applicant["appl_address1"].','.$applicant["appl_address2"],
											'$[phone]' => $applicant["appl_phone_hp"],
											'$[email]' => $applicant["appl_email"],
											'$[Discipline]' => $applicant["discipline"] ,
											'$[PROGRAM1]' => $program_data["program_name1"],
											//'$[PROGRAM2]' => $program_data["program_name2"],
											'$[submission_date]'=>date('j M Y'),
											'$[ACADEMICYEAR]'=>$academic_year["ay_code"],
											'$[registration_date]'=>date('j M Y'),
											'$[withdrawal_date]'=>date('j M Y'),
											'$[seleksi_date]'=>$selection_date,
											'photo'=>$photo_url
											// 'registration_date'=>$registrasi["StartDate"].' s.d '.$registrasi["EndDate"],
											// 'withdrawal_date'=>$withdrawal["StartDate"].' s.d '.$withdrawal["EndDate"]
												
									);
									
									//$educationDB = new App_Model_Application_DbTable_ApplicantEducation();
									//$matapelajaran = $educationDB->getEducationDetail($transaction_id);
									//echo var_dump($matapelajaran);
									// ------- create PDF File section	--------
										
									require_once 'dompdf_config.inc.php';
										
									$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
									$autoloader->pushAutoloader('DOMPDF_autoload');
										
									//template path
									$html_template_path = DOCUMENT_PATH."/template/credittransfer_application_letter.html";
										
									//filename
									$output_filename = $txnData['at_pes_id']."_credittransfer_app_letter.pdf";
									$filepath="/applicant/".date("mY")."/".$txnId;
									$output_directory_path = DOCUMENT_PATH.$filepath;
										
									//create directory to locate file
									if (!is_dir($output_directory_path)) {
										mkdir($output_directory_path, 0775);
									}
									
									/* //to create PDF File
									 if($admission_type==2){
									$this->mailmergeConnection($filepath, $fieldValues,$connection,$output_directory_path, $output_filename);
									}else{
									$this->mailmerge($filepath, $fieldValues,$output_directory_path, $output_filename);
									} */
									$html = file_get_contents($html_template_path);
									//echo $html;exit;
									//replace variable
									foreach ($fieldValues as $key=>$value){
										$html = str_replace($key,$value,$html);
									}
										
									//echo $html;exit;
									
									$dompdf = new DOMPDF();
									$dompdf->load_html($html);
									$dompdf->set_paper('a4', 'potrait');
									$dompdf->render();
										
									$dompdf = $dompdf->output();
										
									//to rename output file
									$output_file_path = $output_directory_path."/".$output_filename;
									
									file_put_contents($output_file_path, $dompdf);
										
									// ------- End PDF File section	--------
    		 
    		$status = true;
    		 
    	// ------- End PDF File section	--------
    
    	//update file info
    	$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
    	$fileexist = $documentDB->getDataArray($txnId, 75);
    
    	$doc["ad_filepath"]=$output_directory_path;
		$doc["ad_filename"]=$output_filename;
		$doc["ad_appl_id"]=$txnId;
		$doc["ad_type"]=75;
		$doc["ad_createddt"]=date("Y-m-d");
		
		
		if($fileexist){
			$documentDB->updateDocument($doc,$txnId,75);
		}else{
			$documentDB->addData($doc);
		}
    
    	 
    }
    
    public function downloadBurekolVerificationAction() {
    
    	$transid = $this->_getParam('txn_id',null);
    	global $profile;
    	global $motherData;
    	global $fatherData;
    	
    	$Transaction = new App_Model_Application_DbTable_ApplicantTransaction();
    	$trans = $Transaction->getTransaction($transid);
    
    	$appl_id = $trans['at_appl_id']; 
    
    	//getProfile
    	$profile = $Transaction->getProfileDetail($appl_id);
        
    	//parent (mother)
    	$familyDb = new App_Model_Application_DbTable_ApplicantFamily();
    	$motherData = $familyDb->getData($appl_id,'21');
    	 
    	//parent (mother)
    	$familyDb = new App_Model_Application_DbTable_ApplicantFamily();
    	$fatherData = $familyDb->getData($appl_id,'20');
    	 
    	     	 
    	require_once 'dompdf_config.inc.php';
    	
    	$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
    	$autoloader->pushAutoloader('DOMPDF_autoload');
    	
    	$html_template_path = DOCUMENT_PATH."/template/AppVerifikasi.html";
    	
    	$html = file_get_contents($html_template_path);
    	
    	//echo $html;exit;
    	$dompdf = new DOMPDF();
    	$dompdf->load_html($html);
    	$dompdf->set_paper('a4', 'potrait');
    	$dompdf->render();
    	
    	//output filename
    	$output_filename = "BiodataVerifikasi.pdf";
    	
    	//$dompdf = $dompdf->output();
    	 
    	
    	$dompdf->stream($output_filename);
    	
    	exit(); 
    	
    	
    }
    
    public function activityListAction(){
    	$this->view->title="Form: Applicant Activity Application";
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id; 
    	
    	
    	$dbStd=new App_Model_Application_DbTable_ApplicantProfile();
    	$std=$dbStd->getData($appl_id);
    	$this->view->student=$std;
    	$dbTransaction=new App_Model_Application_DbTable_ApplicantTransaction();
    	$trx=$dbTransaction->getPaidAndOfferList($appl_id);
    	$this->view->noform=$trx['at_pes_id'];
    	$this->view->trxid=$trx['at_trans_id'];
    	$programid=$trx['IdProgram'];
    	$dbActivityGrp=new App_Model_Activity_DbTable_ActivityGroup();
    	$dbActSchedule=new App_Model_Activity_DbTable_ActivityGroupSchedule();
    	$dbActScheduleLect=new App_Model_Activity_DbTable_ActivityGroupScheduleLecturer();
    	$dbParticipant=new App_Model_Activity_DbTable_ActivityParticipant();
    	$dbGrpPartcipant=new App_Model_Activity_DbTable_ActivityGroupParticipant();
    	
    	if($this->getRequest()->isPost())
    	{
    		$formData = $this->getRequest()->getPost();
    		 
    		$grp=array();
    		if (isset($formData['grp'])) $grp=$formData['grp'];
    		$grptagged=array();
    		if (isset($formData['grpTagged'])) $grptagged=$formData['grpTagged'];
    		$idstd=$formData['appl_id'];
    		$trxid=$formData['at_trans_id'];
    		$name=$formData['name'];
    		foreach ($grp as $idactivity=>$activity) {
    			foreach ($activity as $idgrp=>$value) {
    				$data=array('IdActivity'=>$idactivity,
    						'appl_id'=>$idstd,
    						'at_trans_id'=>$trxid,
    						'ParticipantName'=>$name,
    						'ParticipantStatus'=>'01',
    						'entried_dt'=>date('Y-m-d h:s:i'),
    						'IdStaff'=>0
    				);
    				
    				$row=$dbParticipant->isInStudent($idactivity, $idstd,$trx['at_trans_id']);
    				
    				if (!$row)
    					$idparticipant=$dbParticipant->addData($data);
    				else {
    					$idparticipant=$row['IdParticipant'];
    					$dbParticipant->updateData($data, $idparticipant);
    				}
    				$data=array('IdParticipant'=>$idparticipant,
    						'IdGroup'=>$idgrp,
    						'dt_entry'=>date('Y-m-d h:s:i')
    				);
    				if (!$dbGrpPartcipant->isIn($idgrp, $idparticipant))
    					$dbGrpPartcipant->addData($data);
    				if (isset($grptagged[$idactivity][$idgrp])) unset($grptagged[$idactivity][$idgrp]);
    			};
    		}
    		//delete
    		foreach ($grptagged as $idactivity=>$grpdel) {
    			foreach ($grpdel as $grpid=>$value) {
    				$row=$dbParticipant->isInStudent($idactivity, $idstd);
    				if ($row) {
    					$idparticipant=$row['IdParticipant'];
    					$dbGrpPartcipant->deleteDataByAttrib($idparticipant, $grpid);
    					if (!$dbGrpPartcipant->isIn(null, $idparticipant))
    						$dbParticipant->deleteData($idparticipant);
    				}
    			};
    		}
    	}
    	
    	$openactivity=$dbActivityGrp->getOpenActivityApplicant($programid);
    	foreach ($openactivity as $key=>$value) {
    		$grp=$dbActivityGrp->getGroupActivity($value['IdActivity'],$programid);
    		//cek participan
    		$participant=$dbParticipant->isInStudent($value['IdActivity'], $appl_id,$trx['at_trans_id']);
    		foreach ($grp as $idx=>$item) {
    			//check participan
    			$grp[$idx]['check']="0";
    			if ($participant) {
    				if ($dbGrpPartcipant->isIn($item['IdCourseTaggingGroup'], $participant['IdParticipant']))
    					$grp[$idx]['check']="1";
    			}
    			$schs=$dbActSchedule->getScheduleByGroup($item['IdCourseTaggingGroup']);
    			foreach ($schs as $scidx=>$sc) {
    				$lec=$dbActScheduleLect->getDetailsAll($sc['sc_id']);
    				$schs[$scidx]['speaker']=$lec;
    			}
    			$grp[$idx]['schedule']=$schs;
    		}
    		$openactivity[$key]['group']=$grp;
    
    	}
    	$this->view->activitylist=$openactivity;
    
    }
    
    
    public function printNameTagAction(){
    
    	$auth = Zend_Auth::getInstance();
    	//$registration_id = $auth->getIdentity()->registration_id;
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;
    	$dbActivityGrp=new App_Model_Activity_DbTable_ActivityGroup();
    	$dbActSchedule=new App_Model_Activity_DbTable_ActivityGroupSchedule();
    	$dbActScheduleLect=new App_Model_Activity_DbTable_ActivityGroupScheduleLecturer();
    	$dbParticipant=new App_Model_Activity_DbTable_ActivityParticipant();
    	$dbGrpPartcipant=new App_Model_Activity_DbTable_ActivityGroupParticipant();
    	global $activity;
    	if ($this->getRequest()->isPost()) {
    
    		$formData = $this->getRequest()->getPost();
    		$idgrp=$formData['idgrp'];
    		$idactivity=$formData['idactivity']; 
    		$activity=$dbActSchedule->getStudentsScheduleByGroup($idgrp);
    		 
    		$participant=$dbParticipant->getStudentById($idgrp, $appl_id);
    		 
    		$idparticipant=$participant['IdParticipant'];
    		$activity['ProgramName']=$participant['ProgramName'];
    		$activity['CollegeName']=$participant['CollegeName'];
    		$activity['at_pes_id']=$participant['at_pes_id'];
    		$activity['Name']=$participant['ParticipantName'];
    		//photo
    		$documentDb = new App_Model_Application_DbTable_ApplicantUploadFile();
    		$photo = $documentDb->getTxnFile($participant['at_trans_id'],51);
    		$activity['photo_raw'] = $photo;
    
    		$fnImage = new icampus_Function_General_Image();
    		$activity['photo'] = $fnImage->getImagePath($photo['pathupload'],143,150);
    
    		
    		$data=array('transaction_id'=>$participant['at_trans_id'],
    					'appl_id'=>$appl_id,
    					'groupid'=>$idgrp,
    					'idparticipant'=>$idparticipant 
    		);
    		 
    		$token=md5($activity['GroupCode'].$idgrp);
    		$dbGrpPartcipant->updateDataByAttrib(array('token'=>$token), 'IdGroup='.$idgrp.' and IdParticipant='.$idparticipant);
    		$string='www.sismob.trisakti.ac.id/examination/attendance/ppmb/idactivity/'.$idactivity.'/idgrp/'.$idgrp.'/idparticipant/'.$idparticipant.'/tokenqr/'.$token;
    		$path=$this->generateQr($data, $string);
    		$activity['qr_path']=$path;
    		
    		$dbGrpPartcipant->updateDataByAttrib(array('qr_path'=>$path), 'IdParticipant='.$idparticipant.' and IdGroup='.$idgrp);
    				//========================
    		 
    
     
    		/*
    		 * PDF Generation
    		*/
    
    		require_once 'dompdf_config.inc.php';
    
    		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
    		$autoloader->pushAutoloader('DOMPDF_autoload');
    
    		$html_template_path = DOCUMENT_PATH."/template/NameTag.html";
    
    		$html = file_get_contents($html_template_path);
    
    		//echo $html;
    		//exit;
    		$dompdf = new DOMPDF();
    		$dompdf->load_html($html);
    		$dompdf->set_paper('a5', 'landscape');
    		$dompdf->render();
    
    
    		$dompdf->stream("NameTag_".$data['transaction_id']."_".date('Ymd_Hi').".pdf");
    
    	}
    
    	exit;
    }
    
    public function generateQr($data,$string) {
    
    	 
    	require_once 'qrlib.php';
    	require_once 'qrconfig.php';
    	// how to save PNG codes to server
    	//directory to locate file
    	$app_directory_path = DOCUMENT_PATH."/applicant/".date('Y')."/".$data['idparticipant'];
    		
    	//create directory to locate file
    	if (!is_dir($app_directory_path)) {
    		mkdir($app_directory_path, 0775,true);
    	}
    		
    	$output_directory_path = DOCUMENT_PATH."/applicant/".date('Y')."/".$data['idparticipant']."/".$data['groupid'];
    
    	//create directory to locate file
    	if (!is_dir($output_directory_path)) {
    		mkdir($output_directory_path, 0775,true);
    	}
    	$tempDir = $output_directory_path;
    
    	$codeContents = $string;
    
    	// we need to generate filename somehow,
    	// with md5 or with database ID used to obtains $codeContents...
    	$fileName = $data['transaction_id'].md5($codeContents).'.png';
    
    	$pngAbsoluteFilePath = $tempDir."/".$fileName;
    	//$urlRelativeFilePath = EXAMPLE_TMP_URLRELPATH.$fileName;
    
    	// generating
    	if (!file_exists($pngAbsoluteFilePath)) {
    		require_once 'qrconfig.php';
    
    		 
    		QRcode::png($codeContents, $pngAbsoluteFilePath);
    		$dbExamGrpStd=new App_Model_Activity_DbTable_ActivityGroupParticipant();
    		$dbExamGrpStd->updateDataByAttrib(array('qr_path'=>$pngAbsoluteFilePath), 'IdParticipant='.$data['idparticipant'].' and IdGroup='.$data['idgrp']);
    
    	}
    	return $pngAbsoluteFilePath;
    
    }
    
    
    public function offerLetterAction(){
    	$this->view->title="SURAT PANGGILAN DITERIMA";
    	$txnId=$this->_getParam('transaction_id',0);
    	$this->view->transaction_id=$txnId;
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		if (isset($formData['cetak'])) {
    			$this->generateOfferLetterPdf($txnId);
    		} else {
    			$txnId=$formData['transaction_id'];
    			$paket=$formData['paket'];
    			$this->_redirect('/applicant-portal/agreement-letter/transaction_id/'.$txnId.'/paket/'.$paket);
    		}
    	}
    	//get applicant info
    	$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $applicantDB->getAllProfile($txnId);
    	
    	//get transaction info
    	$applicantTxnDB = new App_Model_Application_DbTable_ApplicantTransaction();
    	$txnData = $applicantTxnDB->getTransaction($txnId);
    	if ($txnData['at_appl_type']=="1") {
    		$typeselection="Program Seleksi USM";
    		$message="-";
    	}else
    	if ($txnData['at_appl_type']=="2") {
    		$typeselection="Program Seleksi Siswa Berpotensi";
    		$message="-";
    	} else if ($txnData['at_appl_type']=="3") {
    		$typeselection="Program Seleksi Transfer/Pindahan";
    		$message="Pelajari terlebih dahulu hasil penyetaraan, sebelum melakukan pembayaran";
    	} else if ($txnData['at_appl_type']=="4") {
    		$typeselection="Program Seleksi Undangan";
    		$message="-";
    	} else if ($txnData['at_appl_type']=="5") {
    		$typeselection="Program Seleksi Portofolio";
    		$message="-";
    	} else if ($txnData['at_appl_type']=="6") {
    		$typeselection="Program Seleksi Beasiswa";
    		$dbSetup=new App_Model_General_DbTable_Setup();
    		$typebeasiswa=$dbSetup->getDataById($txnData['appl_sub_type']);
    		$typeselection=$typeselection." ".$typebeasiswa['ssd_name_bahasa'];
    		$message="Karena anda mendapatkan beasiswa sampai dengan lulus menjadi Sarjana dalam waktu 4 tahun, maka abaikan tagihan ini";
    	} else if ($txnData['at_appl_type']=="7") {
    		$typeselection="Program Seleksi Nilai UTBK";
    		$message="-";
    	}
    	//get assessment data
    	if ($txnData['at_appl_type']=="1") {
    		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessmentUsm();
    		$assessmentData=$assessmentDb->getData($txnId);
    	} else {
    		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessment();
    		$assessmentData = $assessmentDb->getData($txnId);
    	}
    	
    	
    	//get transaction info
    	$applicantTxnDB = new App_Model_Application_DbTable_ApplicantTransaction();
    	$txnData = $applicantTxnDB->getTransaction($txnId);
    	
    	//getapplicantprogram
    	$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
    	$program = $appProgramDB->getProgramFaculty($txnId,$txnData['at_appl_type']);
    	 
    	//program data
    	$programDb = new App_Model_General_DbTable_Program();
    	$programData = $programDb->fngetProgramData($program[0]['program_id']);
    	
    	//award type
    	$award = "";
    	
    	if($programData['Award'] == 36){
    		$award = "D3";
    	}else
    	if($programData['Award'] == 363){
    		$award = "D4";
    	}else{
    		$award = "S1";
    	}
    	
    	
    	$learning_duration = $award." = ".$programData['OptimalDuration']." Semester";
    	
    	
    	//rank
    	if($assessmentData['aar_rating_rector']==1){
    		$rank = "1 (Satu)";
    		$biaya =$programData['Estimate_Fee_R1']!=null?$programData['Estimate_Fee_R1']:"";
    	}else
    	if($assessmentData['aar_rating_rector']==2){
    		$rank = "2 (Dua)";
    		$biaya =$programData['Estimate_Fee_R2']!=null?$programData['Estimate_Fee_R2']:"";
    	}else
    	if($assessmentData['aar_rating_rector']==3){
    		$rank = "3 (Tiga)";
    		$biaya =$programData['Estimate_Fee_R3']!=null?$programData['Estimate_Fee_R3']:"";
    	}
    		
    	
    	//faculty data
    	$collegeMasterDb = new App_Model_General_DbTable_Collegemaster();
    	$facultyData = $collegeMasterDb->fngetCollegemasterData($program[0]['faculty_id']);
    	
    	//get applicant parents info
    	$familyDB =  new App_Model_Application_DbTable_ApplicantFamily();
    	$father = $familyDB->fetchdata($applicant["appl_id"],20); //father's
    	 
    	//get next intake
    	$intakeDb = new App_Model_Record_DbTable_Intake();
    	$intakeData = $intakeDb->getData($txnData['at_intake']);
    	 
    	//get fee structure
    	$feeStructureDb = new Studentfinance_Model_DbTable_FeeStructure();
    	
    	//get fee structure
    	if($applicant["appl_nationality"]==96){
    		//local
    		$feeStructureDb = new Studentfinance_Model_DbTable_FeeStructure();
    		$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData['IdIntake'],$program[0]["program_id"]);
    		$biaya = number_format($biaya, 2, '.', ',');
    	
    	}else{
    		//foreigner
    		$feeStructureData = $feeStructureDb->getApplicantFeeStructure($intakeData['IdIntake'],$program[0]["program_id"],315);
    		$biaya = $biaya*2;
    		$biaya = number_format($biaya, 2, '.', ',');
    	}
    	
    	
    	//fee structure plan
    	$feeStructurePlanDb = new Studentfinance_Model_DbTable_FeeStructurePlan();
    	$paymentPlanData = $feeStructurePlanDb->getStructureData($feeStructureData['fs_id']);
    	$feeStructureData['payment_plan'] = $paymentPlanData;
    	
    	//fee structure program
    	$feeStructureProgramDb = new Studentfinance_Model_DbTable_FeeStructureProgram();
    	$feeStructureProgramData = $feeStructureProgramDb->getStructureData($feeStructureData['fs_id'],$program[0]["program_id"]);
    	
    	//fee structure plan detail
    	$fspdDb = new Studentfinance_Model_DbTable_FeeStructurePlanDetail();
    	
    	foreach ($feeStructureData['payment_plan'] as $key=>$plan){
    			
    		for($installment=1; $installment<=$plan['fsp_bil_installment']; $installment++){
    			$feeStructureData['payment_plan'][$key]['plan_detail'][$installment] = $fspdDb->getPlanData($plan['fsp_structure_id'], $plan['fsp_id'], $installment, 1,$feeStructureProgramData['fsp_program_id'],$assessmentData['aar_rating_rector']);
    		}
    	}
    	
    	 
    	$nomor=$assessmentData['asd_nomor'];
    	
    	$address = "";
    	if( isset($applicant["appl_address1"]) && $applicant["appl_address1"]!=""){
    		$address = $address . $applicant["appl_address1"]."<br />";
    	}
    	if( isset($applicant["appl_address2"]) && $applicant["appl_address2"]!=""){
    		$address = $address . $applicant["appl_address2"]."<br />";
    	}
    	if( isset($applicant["CityName"]) && $applicant["CityName"]!=""){
    		$address = $address . $applicant["CityName"]."<br />";
    	}
    	if( isset($applicant["appl_postcode"]) && $applicant["appl_postcode"]!=""){
    		$address = $address . $applicant["appl_postcode"]."<br />";
    	}
    	if( isset($applicant["StateName"]) && $applicant["StateName"]!=""){
    		$address = $address . $applicant["StateName"]."<br />";
    	}
    	
    	$fieldValues = array(
    			'NO_PES'=>$txnData["at_pes_id"],
    			'NOMOR'=>$nomor,
    			'LAMPIRAN'=>"-",
    			'TITLE_TEMPLATE'=>$this->view->translate("Pemberitahuan diterima sebagai calon Mahasiswa di Universitas Trisakti"),
    			'APPLICANT_NAME'=>$applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
    			'PARENTNAME'=>$father["af_name"],
    			'ADDRESS' =>$address,
    			'ADDRESS1'=>$applicant["appl_address1"],
    			'ADDRESS2'=>$applicant["appl_address2"],
    			'CITY'=>$applicant["CityName"],
    			'POSTCODE'=>$applicant["appl_postcode"],
    			'STATE'=>$applicant["StateName"],
    			'ACADEMIC_YEAR'=>$txnData['ay_code'],
    			'PERIOD'=>$txnData['ap_desc'],
    			'FACULTY'=>$program[0]["faculty2"],
    			'FACULTY_NAME'=>($facultyData['ArabicName']!=null?$facultyData['ArabicName']." ":"-"),
    			'FACULTY_SHORTNAME'=>($facultyData['ShortName']!=null?$facultyData['ShortName']." ":"-"),
    			'FACULTY_ADDRESS1'=>($facultyData['Add1']!=null?$facultyData['Add1']." ":"-"),
    			'FACULTY_ADDRESS2'=>($facultyData['Add2']!=null?$facultyData['Add2']." ":""),
    			'FACULTY_ADDRESS'=>($facultyData['Add1']!=null?$facultyData['Add1']." ":"").($facultyData['Add2']!=null?$facultyData['Add2']." ":""),
    			'FACULTY_PHONE'=>($facultyData['Phone1']!=null?$facultyData['Phone1']." ":"").($facultyData['Phone2']!=null?", ".$facultyData['Phone2']." ":""),
    			'FACULTY_FAX'=>($facultyData['Fax']!=null?$facultyData['Fax']." ":""),
    			'PROGRAME'=>$program[0]["program_name_indonesia"],
    			'KELAS'=>$program[0]["GroupName"],
    			'RANK' => $rank,
    			'PRINT_DATE'=>date('j M Y'),
    			'REGISTRATION_DATE_START'=> date ( 'j F Y' , strtotime ( $assessmentData['aar_reg_start_date'] ) ),
    			'REGISTRATION_DATE_END'=> date ( 'j F Y' , strtotime ( $assessmentData['aar_reg_end_date'] ) ),
    			'MESSAGE'=>$message,
    			'SELECTION_TYPE'=>$typeselection,
    			'LEARNING_DURATION' => $learning_duration,
    			'ESTIMASI_BIAYA' => $biaya,
    			'RECTOR_DATE' => date('j M Y',strtotime($assessmentData['asd_decree_date']))
    	);
    	$this->view->dataview=$fieldValues;
    	
    	 
    	//program data
    //	global $program;
    	$program = $feeStructureProgramData;
    	$this->view->program=$program;
    	//registration date
    	//global $reg_date;
    	$reg_date = array(
    			'REGISTRATION_DATE_START'=> $assessmentData['aar_reg_start_date'],
    			'REGISTRATION_DATE_END'=> $assessmentData['aar_reg_end_date']
    	);
    	
    	
    	//date payment
    	foreach($feeStructureData['payment_plan'] as $key=>$plan){
    		$start = $assessmentData['aar_reg_start_date'];
    		$end = $assessmentData['aar_reg_end_date'];
    			
    		foreach ($plan['plan_detail'] as $key2=>$installment){
    			$reg_date['date_payment'][$key][$key2]['start'] = $start;
    			$reg_date['date_payment'][$key][$key2]['end'] = $end;
    	
    			$end = date ( 'F Y' , strtotime ( '+1 month' , strtotime ( $end) ) );
    		}
    			
    		$end = $assessmentData['aar_reg_end_date'];
    	}
    	
    	$this->view->reg_date=$reg_date;
    	//fee data
    	//global $fees;
    	$fees = $feeStructureData['payment_plan'];
    	$this->view->fees=$fees;
    	//footer variable
    	//global $pes;
    	$pes = $txnData["at_pes_id"];
    	$this->view->pes=$pes;
    	 
    	//regenerate performa invoice
    	$proformaInvoiceDb = new Application_Model_DbTable_ProformaInvoice();
    	if (!$proformaInvoiceDb->getTxnData($txnId)) 
    		$proformaInvoiceDb->regenerateProformaInvoice($txnId);
    	
    	
    	
    }
     
    public function  agreementLetterAction(){
    
    	$translate = Zend_Registry::get('Zend_Translate');
    	$txnId=$this->_getParam('transaction_id',0);
    	$paket=$this->_getParam('paket',0);
    	$this->view->paket=$paket;
    	$this->view->transaction_id=$txnId;
    	//get transaction info
    	$applicantTxnDB = new App_Model_Application_DbTable_ApplicantTransaction();
    	$txnData = $applicantTxnDB->getTransaction($txnId);
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		//echo var_dump($formData);exit;
    		//generate payment
    		$proformaInvoiceDb = new Application_Model_DbTable_ProformaInvoice();
    		$dbInv=new Studentfinance_Model_DbTable_InvoiceMain();
    		$dbinvVa=new Application_Model_DbTable_ProformaInvoiceVa();
    		if (!$dbinvVa->isInByTrx($txnId)) 
    		//regenerate performa invoice
    		$proformaInvoiceDb->generateProformaInvoiceEcollection($formData['transaction_id']);
    		$inv=$dbInv->getApplicantInvoice($txnData['at_pes_id']);
    	//	echo var_dump($inv);exit;
    		if (!$inv) {
    			$proformaInvoiceDb->moveToInvoiceBasedOnPaket($txnData['at_pes_id'], $formData['paket']);
    			$inv=$dbInv->getApplicantInvoice($txnData['at_pes_id']);
    		}
    	 
    		
    		foreach ($inv as $value) {
    			if (!$dbInv->getDataByVA($value['va']) || $value['va']=="")
    				$dbInv->pushToECollForEnrollmentPerBilling($formData['transaction_id'],$value['bill_number'],'createbilling');
    		}
    		//print aggrement
    		$this->_redirect('applicant-portal/generate-agreement-letter/transaction_id/'.$txnId.'/paket/'.$paket);
    		//$this->_redirect('/applicant-portal/account');
    	}
    	//get applicant info
    	$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $applicantDB->getAllProfile($txnId);
    	 
    	
    
    
    	//getapplicantprogram
    	$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
    	$programDb = new GeneralSetup_Model_DbTable_Program();
    
    	if($txnData['at_appl_type']==2 || $txnData['at_appl_type']==4 || $txnData['at_appl_type']==5 || $txnData['at_appl_type']==6 || $txnData['at_appl_type']==7 || $txnData['at_appl_type']==3){
    		$program = $appProgramDB->getProgramFaculty($txnId,$txnData['at_appl_type']);
    		$branch=$program[0]['IdBranchOffer'];
    		$programid=$program[0]['program_id'];
    		//program data
    		$programData = $programDb->fngetProgramData($program[0]['program_id']);
    			
    		//get assessment data
    		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessment();
    		$ass_data = $assessmentDb->getData($txnId);
    
    		$assessmentData = array(
    				'nomor' => $ass_data['asd_nomor'],
    				'decree_date' => $ass_data['asd_decree_date'],
    				'rank' => $ass_data['aar_rating_rector'],
    				'registration_start_date' => $ass_data['aar_reg_start_date'],
    				'registration_end_date' => $ass_data['aar_reg_end_date'],
    				'payment_start_date' => $ass_data['aar_payment_start_date'],
    				'payment_end_date' => $ass_data['aar_payment_end_date'],
    		);
    	}else{
    		$program = $appProgramDB->getUsmOfferProgram($txnId);
    		$branch=$program['IdBranchOffer'];
    		$programid=$program['program_id'];
    		//get assessment data
    		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessmentUsm();
    		$ass_data = $assessmentDb->getData($txnId);
    			
    		$assessmentData = array(
    				'nomor' => $ass_data['aaud_nomor'],
    				'decree_date' => $ass_data['aaud_decree_date'],
    				'rank' => $ass_data['aau_rector_ranking'],
    				'registration_start_date' => $ass_data['aaud_reg_start_date'],
    				'registration_end_date' => $ass_data['aaud_reg_end_date'],
    				'payment_start_date' => $ass_data['aaud_payment_start_date'],
    				'payment_end_date' => $ass_data['aaud_payment_end_date'],
    		);
    
    		//program data
    		$programData = $programDb->fngetProgramData($program['program_id']);
    	}
    
    	//award type
    	$award = "";
    
    	if($programData['Award'] == 36){
    		$award = "D3";
    	}else
    	if($programData['Award'] == 363){
    		$award = "D4";
    	}else{
    		$award = "S1";
    	}
    
    
    	$learning_duration = $award." = ".$programData['OptimalDuration']." Semester";
    
    
    	//rank
    	$rank_digit = 3;
    	if($assessmentData['rank']==1){
    		$rank_digit = 1;
    		$rank = "1 (Satu)";
    		$biaya =$programData['Estimate_Fee_R1']!=null?number_format($programData['Estimate_Fee_R1'], 2, '.', ','):"";
    	}else
    	if($assessmentData['rank']==2){
    		$rank_digit = 2;
    		$rank = "2 (Dua)";
    		$biaya =$programData['Estimate_Fee_R2']!=null?number_format($programData['Estimate_Fee_R2'], 2, '.', ','):"";
    	}else
    	if($assessmentData['rank']==3){
    		$rank_digit = 3;
    		$rank = "3 (Tiga)";
    		$biaya =$programData['Estimate_Fee_R3']!=null?number_format($programData['Estimate_Fee_R3'], 2, '.', ','):"";
    	}else{
    		$rank = "3 (Tiga)";
    		$biaya =$programData['Estimate_Fee_R3']!=null?number_format($programData['Estimate_Fee_R3'], 2, '.', ','):"";
    	}
    
    
    
    
    	//faculty data
    	$collegeMasterDb = new GeneralSetup_Model_DbTable_Collegemaster();
    	$facultyData = $collegeMasterDb->fngetCollegemasterData($programData['IdCollege']);
    
    	//get applicant parents info
    	$familyDB =  new App_Model_Application_DbTable_ApplicantFamily();
    	$father = $familyDB->getData($applicant["appl_id"],20); //father's
    	 
    	//get next intake
    	$intakeDb = new GeneralSetup_Model_DbTable_Intake();
    	$intakeData = $intakeDb->fngetIntakeDetails($txnData['at_intake']);
    	 
    	//Nomor
    	$nomor=$assessmentData['nomor'];
    
    	$address = "";
    	if( isset($applicant["appl_address1"]) && $applicant["appl_address1"]!=""){
    		$address = $address . $applicant["appl_address1"]."<br />";
    	}
    	if( isset($applicant["appl_address2"]) && $applicant["appl_address2"]!=""){
    		$address = $address . $applicant["appl_address2"]."<br />";
    	}
    	if( isset($applicant["CityName"]) && $applicant["CityName"]!=""){
    		$address = $address . $applicant["CityName"]."<br />";
    	}
    	if( isset($applicant["appl_postcode"]) && trim($applicant["appl_postcode"])!=""){
    		$address = $address .$applicant["appl_postcode"]."<br />";
    	}
    	if( isset($applicant["StateName"]) && $applicant["StateName"]!=""){
    		$address = $address . $applicant["StateName"]."<br />";
    	}
    
    	$fieldValues = array(
    			'NO_PES'=>$txnData["at_pes_id"],
    			'NOMOR'=>$nomor,
    			'LAMPIRAN'=>"-",
    			'TITLE_TEMPLATE'=>$translate->_("Pemberitahuan diterima sebagai calon Mahasiswa di Universitas Trisakti"),
    			'APPLICANT_NAME'=>$applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
    			'PARENTNAME'=>$father["af_name"],
    			'PARENTJOB'=>$father["afj_title"],
    			'ADDRESS' =>$address,
    			'ADDRESS1'=>$applicant["appl_address1"],
    			'ADDRESS2'=>$applicant["appl_address2"],
    			'CITY'=>$applicant["CityName"],
    			'POSTCODE'=>$applicant["appl_postcode"],
    			'STATE'=>$applicant["StateName"],
    			'ACADEMIC_YEAR'=>$txnData['ay_code'],
    			'PERIOD'=>$txnData['ap_desc'],
    			'FACULTY'=>$programData["IdCollege"],
    			'FACULTY_NAME'=>($facultyData['ArabicName']!=null?$facultyData['ArabicName']." ":"-"),
    			'FACULTY_SHORTNAME'=>($facultyData['ShortName']!=null?$facultyData['ShortName']." ":"-"),
    			'FACULTY_ADDRESS1'=>($facultyData['Add1']!=null?$facultyData['Add1']." ":"-"),
    			'FACULTY_ADDRESS2'=>($facultyData['Add2']!=null?$facultyData['Add2']." ":""),
    			'FACULTY_ADDRESS'=>($facultyData['Add1']!=null?$facultyData['Add1']." ":"").($facultyData['Add2']!=null?$facultyData['Add2']." ":""),
    			'FACULTY_PHONE'=>($facultyData['Phone1']!=null?$facultyData['Phone1']." ":"").($facultyData['Phone2']!=null?", ".$facultyData['Phone2']." ":""),
    			'FACULTY_FAX'=>($facultyData['Fax']!=null?$facultyData['Fax']." ":""),
    			'PROGRAME'=>$programData["ArabicName"],
    			'RANK' => $rank,
    			'PRINT_DATE'=>date('j M Y'),
    			'REGISTRATION_DATE_START'=> date ( 'j F Y' , strtotime ( $assessmentData['registration_start_date'] ) ),
    			'REGISTRATION_DATE_END'=> date ( 'j F Y' , strtotime ( $assessmentData['registration_end_date'] ) ),
    			'LEARNING_DURATION' => $learning_duration,
    			'ESTIMASI_BIAYA' => $biaya,
    			'RECTOR_DATE' => date('j M Y',strtotime($assessmentData['decree_date']))
    	);
    
    	$this->view->dataview=$fieldValues;
    
    	 
    
    	//payment data
    	$paymentMainDb = new Studentfinance_Model_DbTable_PaymentMain();
    	$payment = $paymentMainDb->getApplicantPaymentInfo($txnData['at_pes_id']);
    
    	//get fee structure
    	//TODO:check local or foreign
    	$feeStructureDb = new Studentfinance_Model_DbTable_FeeStructure();
    	if(!$this->islocalNationality($txnId)){
    		//315 is foreigner in lookup db
    		$fee_structure = $feeStructureDb->getApplicantFeeStructure($intakeData[0]['IdIntake'],$programid,315,$branch);
    		$biaya = $biaya*2;
    		$biaya = number_format($biaya, 2);
    		
    	}else{
    		//default to local
    		$fee_structure = $feeStructureDb->getApplicantFeeStructure($intakeData[0]['IdIntake'],$programid,314,$branch);
    		$biaya = number_format($biaya, 2);
    	}
    
    	//$feeStructureDb = new Studentfinance_Model_DbTable_FeeStructure();
    	//$fee_structure = $feeStructureDb->getApplicantFeeStructure($txnData['at_intake'], $programData['IdProgram']);
    
    	//get selected payment plan
    	$paymentplanDb = new Studentfinance_Model_DbTable_FeeStructurePlan();
    	$payment_plan = $paymentplanDb->getBillingPlanByPackage($fee_structure['fs_id'],$paket);
    
    	//inject plan detail (installment)
    	$paymentPlanDetailDb = new Studentfinance_Model_DbTable_FeeStructurePlanDetail();
    	$payment_plan['installment_detail'] = array();
    	for($i=1;$i<=$payment_plan['fsp_bil_installment']; $i++){
    		$payment_plan['installment_detail'][$i] = $paymentPlanDetailDb->getPlanData($fee_structure['fs_id'], $payment_plan['fsp_id'], $i, 1, $programData['IdProgram'], $assessmentData['rank']);
    		
    	}
    
    	//registration date
    	//global $reg_date;
    	$reg_date = array(
    			'REGISTRATION_DATE_START'=> $assessmentData['registration_start_date'],
    			'REGISTRATION_DATE_END'=> $assessmentData['registration_end_date']
    	);
    	
    	//date payment
    	$start = $assessmentData['registration_start_date'];
    	$end = $assessmentData['registration_end_date'];
    
    	foreach ($payment_plan['installment_detail'] as $key=>$installment){
    		$payment_plan['payment_date'][$key]['start'] = $start;
    		$payment_plan['payment_date'][$key]['end'] = $end;
    
    		$end = date ( 'F Y' , strtotime ( '+1 month' , strtotime ( $end) ) );
    	}
    
    	$end = $assessmentData['registration_end_date'];
    	$this->view->reg_date=$reg_date;
    	//global $fee;
    	//$fee = $payment_plan;
    	$this->view->fee=$payment_plan;
    	//global $program_fee_structure;
    	$this->view->program_fee_structure = $fee_structure;
    
    
    	//program data
    	///global $program;
    	$this->view->program = $programData;
    
    	//footer variable
    	//global $pes;
    	$this->view->pes = $txnData["at_pes_id"];
    	/*echo $html;
    	 exit;*/
      
    
    }
    private function islocalNationality($txn_id){
    	//get profile
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$select = $db ->select()
    	->from(array('at'=>'applicant_transaction'))
    	->join(array('ap'=>'applicant_profile'),'ap.appl_id = at.at_appl_id')
    	->where("at_trans_id = ".$txn_id);
    		
    	$row = $db->fetchRow($select);
    		
    	//nationality
    	if( isset($row['appl_nationality']) ){
    
    		if($row['appl_nationality']==96){
    			return true;
    		}else{
    			return false;
    		}
    	}else{
    		//default to local if null data
    		return true;
    	}
    }
    
    public function generateAgreementLetterAction(){
    
    	$translate = Zend_Registry::get('Zend_Translate');
    	$txnId=$this->_getParam('transaction_id');
    	$paket=$this->_getParam('paket');
    	if ($paket=="A") $paket="0";else $paket="1";
    	//get applicant info
    	$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $applicantDB->getAllProfile($txnId);
    		
    	//get transaction info
    	$applicantTxnDB = new App_Model_Application_DbTable_ApplicantTransaction();
    	$txnData = $applicantTxnDB->getTransaction($txnId);
    
    
    	//getapplicantprogram
    	$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
    	$programDb = new App_Model_General_DbTable_Program();
    
    	if($txnData['at_appl_type']==2 || $txnData['at_appl_type']==4 || $txnData['at_appl_type']==5 || $txnData['at_appl_type']==6 || $txnData['at_appl_type']==7 || $txnData['at_appl_type']==3){
    		$program = $appProgramDB->getProgramFaculty($txnId,$txnData['at_appl_type']);
    		$branch=$program[0]['IdBranchOffer'];
    		$programid=$program[0]['program_id'];
    		//program data
    		$programData = $programDb->fngetProgramData($program[0]['program_id']);
    
    		//get assessment data
    
    		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessment();
    		$ass_data = $assessmentDb->getData($txnId);
    
    		$assessmentData = array(
    				'nomor' => $ass_data['asd_nomor'],
    				'decree_date' => $ass_data['asd_decree_date'],
    				'rank' => $ass_data['aar_rating_rector'],
    				'registration_start_date' => $ass_data['aar_reg_start_date'],
    				'registration_end_date' => $ass_data['aar_reg_end_date'],
    				'payment_start_date' => $ass_data['aar_payment_start_date'],
    				'payment_end_date' => $ass_data['aar_payment_end_date'],
    		);
    	}else{
    		$program = $appProgramDB->getUsmOfferProgram($txnId);
    		$branch=$program['IdBranchOffer'];
    		$programid=$program['program_id'];
    		//get assessment data
    		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessmentUsm();
    		$ass_data = $assessmentDb->getData($txnId);
    
    		$assessmentData = array(
    				'nomor' => $ass_data['aaud_nomor'],
    				'decree_date' => $ass_data['aaud_decree_date'],
    				'rank' => $ass_data['aau_rector_ranking'],
    				'registration_start_date' => $ass_data['aaud_reg_start_date'],
    				'registration_end_date' => $ass_data['aaud_reg_end_date'],
    				'payment_start_date' => $ass_data['aaud_payment_start_date'],
    				'payment_end_date' => $ass_data['aaud_payment_end_date'],
    		);
    
    		//program data
    		$programData = $programDb->fngetProgramData($program['program_id']);
    	}
    
    	//award type
    	$award = "";
    
    	if($programData['Award'] == 36){
    		$award = "D3";
    	}else
    	if($programData['Award'] == 363){
    		$award = "D4";
    	}else{
    		$award = "S1";
    	}
    
    
    	$learning_duration = $award." = ".$programData['OptimalDuration']." Semester";
    
    
    	//rank
    	$rank_digit = 3;
    	if($assessmentData['rank']==1){
    		$rank_digit = 1;
    		$rank = "1 (Satu)";
    		$biaya =$programData['Estimate_Fee_R1']!=null?number_format($programData['Estimate_Fee_R1'], 2, '.', ','):"";
    	}else
    	if($assessmentData['rank']==2){
    		$rank_digit = 2;
    		$rank = "2 (Dua)";
    		$biaya =$programData['Estimate_Fee_R2']!=null?number_format($programData['Estimate_Fee_R2'], 2, '.', ','):"";
    	}else
    	if($assessmentData['rank']==3){
    		$rank_digit = 3;
    		$rank = "3 (Tiga)";
    		$biaya =$programData['Estimate_Fee_R3']!=null?number_format($programData['Estimate_Fee_R3'], 2, '.', ','):"";
    	}else{
    		$rank = "3 (Tiga)";
    		$biaya =$programData['Estimate_Fee_R3']!=null?number_format($programData['Estimate_Fee_R3'], 2, '.', ','):"";
    	}
    
    
    
    
    	//faculty data
    	$collegeMasterDb = new App_Model_General_DbTable_Collegemaster();
    	$facultyData = $collegeMasterDb->fngetCollegemasterData($programData['IdCollege']);
    
    	//get applicant parents info
    	$familyDB =  new App_Model_Application_DbTable_ApplicantFamily();
    	$father = $familyDB->getData($applicant["appl_id"],20); //father's
    	// echo var_dump($programData);exit;
    	//get next intake
    	$intakeDb = new App_Model_General_DbTable_Intake();
    	$intakeData = $intakeDb->fngetIntakeDetails($txnData['at_intake']);
    	//echo var_dump($intakeData);exit;
    	//Nomor
    	$nomor=$assessmentData['nomor'];
    
    	$address = "";
    	if( isset($applicant["appl_address1"]) && $applicant["appl_address1"]!=""){
    		$address = $address . $applicant["appl_address1"]."<br />";
    	}
    	if( isset($applicant["appl_address2"]) && $applicant["appl_address2"]!=""){
    		$address = $address . $applicant["appl_address2"]."<br />";
    	}
    	if( isset($applicant["CityName"]) && $applicant["CityName"]!=""){
    		$address = $address . $applicant["CityName"]."<br />";
    	}
    	if( isset($applicant["appl_postcode"]) && trim($applicant["appl_postcode"])!=""){
    		$address = $address .$applicant["appl_postcode"]."<br />";
    	}
    	if( isset($applicant["StateName"]) && $applicant["StateName"]!=""){
    		$address = $address . $applicant["StateName"]."<br />";
    	}
    
    	$fieldValues = array(
    			'$[NO_PES]'=>$txnData["at_pes_id"],
    			'$[NOMOR]'=>$nomor,
    			'$[LAMPIRAN]'=>"-",
    			'$[TITLE_TEMPLATE]'=>$translate->_("Pemberitahuan diterima sebagai calon Mahasiswa di Universitas Trisakti"),
    			'$[APPLICANT_NAME]'=>$applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
    			'$[PARENTNAME]'=>$father["af_name"],
    			'$[PARENTJOB]'=>$father["afj_title"],
    			'$[ADDRESS]' =>$address,
    			'$ADDRESS1]'=>$applicant["appl_address1"],
    			'$ADDRESS2]'=>$applicant["appl_address2"],
    			'$[CITY]'=>$applicant["CityName"],
    			'$[POSTCODE]'=>$applicant["appl_postcode"],
    			'$[STATE]'=>$applicant["StateName"],
    			'$[ACADEMIC_YEAR]'=>$txnData['ay_code'],
    			'$[PERIOD]'=>$txnData['ap_desc'],
    			'$[FACULTY]'=>$programData["IdCollege"],
    			'$[FACULTY_NAME]'=>($facultyData['ArabicName']!=null?$facultyData['ArabicName']." ":"-"),
    			'$[FACULTY_SHORTNAME]'=>($facultyData['ShortName']!=null?$facultyData['ShortName']." ":"-"),
    			'$[FACULTY_ADDRESS1]'=>($facultyData['Add1']!=null?$facultyData['Add1']." ":"-"),
    			'$[FACULTY_ADDRESS2]'=>($facultyData['Add2']!=null?$facultyData['Add2']." ":""),
    			'$[FACULTY_ADDRESS]'=>($facultyData['Add1']!=null?$facultyData['Add1']." ":"").($facultyData['Add2']!=null?$facultyData['Add2']." ":""),
    			'$[FACULTY_PHONE]'=>($facultyData['Phone1']!=null?$facultyData['Phone1']." ":"").($facultyData['Phone2']!=null?", ".$facultyData['Phone2']." ":""),
    			'$[FACULTY_FAX]'=>($facultyData['Fax']!=null?$facultyData['Fax']." ":""),
    			'$[PROGRAME]'=>$programData["ArabicName"],
    			'$[RANK]' => $rank,
    			'$[PRINT_DATE]'=>date('j M Y'),
    			'$[REGISTRATION_DATE_START]'=> date ( 'j F Y' , strtotime ( $assessmentData['registration_start_date'] ) ),
    			'$[REGISTRATION_DATE_END]'=> date ( 'j F Y' , strtotime ( $assessmentData['registration_end_date'] ) ),
    			'$[LEARNING_DURATION]' => $learning_duration,
    			'$[ESTIMASI_BIAYA]' => $biaya,
    			'$[RECTOR_DATE]' => date('j M Y',strtotime($assessmentData['decree_date']))
    	);
    
    
    
    	require_once 'dompdf_config.inc.php';
    
    	$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
    	$autoloader->pushAutoloader('DOMPDF_autoload');
    
    	$html_template_path = DOCUMENT_PATH."/template/AgreementLetter2020.html";
    
    	$html = file_get_contents($html_template_path);
    
    	//replace variable
    	foreach ($fieldValues as $key=>$value){
    		$html = str_replace($key,$value,$html);
    	}
    
    
    	//payment data
    	$paymentMainDb = new App_Model_Finance_DbTable_PaymentMain();
    	$payment = $paymentMainDb->getApplicantPaymentInfo($txnData['at_pes_id']);
    
    	//get fee structure
    	//TODO:check local or foreign
    	$feeStructureDb = new App_Model_Finance_DbTable_FeeStructure();
    	if(!$this->islocalNationality($txnId)){
    		//315 is foreigner in lookup db
    		$fee_structure = $feeStructureDb->getApplicantFeeStructure($intakeData[0]['IdIntake'],$programid,$branch,315);
    		$biaya = $biaya*2;
    		$biaya = number_format($biaya, 2, '.', ',');
    	}else{
    		//default to local
    		$fee_structure = $feeStructureDb->getApplicantFeeStructure($intakeData[0]['IdIntake'],$programid,$branch,314);
    		$biaya = number_format($biaya, 2, '.', ',');
    	}
    
    	//$feeStructureDb = new Studentfinance_Model_DbTable_FeeStructure();
    	//$fee_structure = $feeStructureDb->getApplicantFeeStructure($txnData['at_intake'], $programData['IdProgram']);
    
    	//get selected payment plan
    	$paymentplanDb = new App_Model_Finance_DbTable_FeeStructurePlan();
    	//$payment_plan = $paymentplanDb->getBillingPlan($fee_structure['fs_id'],$payment[0]['billing_no']);
    	$payment_plan = $paymentplanDb->getBillingPlanByPackage($fee_structure['fs_id'],$paket);
    	//echo var_dump($payment_plan);exit;
    	//inject plan detail (installment)
    	$paymentPlanDetailDb = new App_Model_Finance_DbTable_FeeStructurePlanDetail();
    	$payment_plan['installment_detail'] = array();
    	for($i=1;$i<=$payment_plan['fsp_bil_installment']; $i++){
    		$payment_plan['installment_detail'][$i] = $paymentPlanDetailDb->getPlanData($fee_structure['fs_id'], $payment_plan['fsp_id'], $i, 1, $programData['IdProgram'], $assessmentData['rank']);
    
    	}
    
    	//registration date
    	global $reg_date;
    	$reg_date = array(
    			'REGISTRATION_DATE_START'=> $assessmentData['registration_start_date'],
    			'REGISTRATION_DATE_END'=> $assessmentData['registration_end_date']
    	);
    
    	//date payment
    	$start = $assessmentData['payment_start_date'];
    	$end = $assessmentData['payment_end_date'];
    
    	foreach ($payment_plan['installment_detail'] as $key=>$installment){
    		$payment_plan['payment_date'][$key]['start'] = $start;
    		$payment_plan['payment_date'][$key]['end'] = $end;
    
    		$end = date ( 'F Y' , strtotime ( '+1 month' , strtotime ( $end) ) );
    	}
    
    	$end = $assessmentData['payment_end_date'];
    
    	global $fee;
    	$fee = $payment_plan;
    
    	global $program_fee_structure;
    	$program_fee_structure = $fee_structure;
    
    
    	//program data
    	global $program;
    	$program = $programData;
    
    	//footer variable
    	global $pes;
    	$pes = $txnData["at_pes_id"];
    		
    
    	$dompdf = new DOMPDF();
    	$dompdf->load_html($html);
    	$dompdf->set_paper('a4', 'potrait');
    	@$dompdf->render();
    
    
    	//$dompdf->stream($txnData["at_pes_id"]."_agreement_letter.pdf");
    	//exit;
    	$pdf = @$dompdf->output();
    
    
    	//$location_path
    	$location_path = "applicant/".date("mY")."/".$txnId;
    
    	//output_directory_path
    	$output_directory_path = DOCUMENT_PATH."/".$location_path;
    
    	//create directory to locate file
    	if (!is_dir($output_directory_path)) {
    		mkdir($output_directory_path, 0775);
    	}
    
    	//output filename
    	$output_filename = $txnData["at_pes_id"]."_agreement_letter.pdf";
    
    	//to rename output file
    	$output_file_path = $output_directory_path."/".$output_filename;
    
    	file_put_contents($output_file_path, $pdf);
    
    	//update file info
    	/*$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
    	 $doc["ad_filepath"]=$location_path;
    	$doc["ad_filename"]=$output_filename;
    	$documentDB->updateDocument($doc,$txnId,45);*/
    
    	//update file info
    	$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
    	$fileexist = $documentDB->getDataArray($txnId, 50);
    
    	$doc["ad_filepath"]=$location_path;
    	$doc["ad_filename"]=$output_filename;
    	$doc["ad_appl_id"]=$txnId;
    	$doc["ad_type"]=50;
    
    	if($fileexist){
    
    		$documentDB->updateDocument($doc,$txnId,50);
    	}else{
    
    		$doc['ad_createddt'] = date('Y-m-d');
    		$documentDB->addData($doc);
    	}
    	//$dompdf->stream("Perjanjian_".$txnId."_".date('Ymd_Hi').".pdf");
    	$this->_redirect('applicant-portal/account');
    	//exit;
    
    }
    
	public function applyTpaTestAction() {
		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		 
		//title
    	$this->view->title = $this->view->translate("Pendaftaran Test TPA dan MPPI");
    	
    	$auth = Zend_Auth::getInstance(); 
    	$appl_id = $auth->getIdentity()->appl_id; 
    	//$appl_id = $this->_getParam('id', 0);    	
    	$this->view->appl_id = $appl_id;
    	
    	$transaction_id = $this->_getParam('id');
    	
    	$this->view->transaction_id = $transaction_id;
    	//echo $transaction_id;exit;
	
    	//get applicant profile
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	
    	//transaction data
    	$applicantTransactionDn = new App_Model_Application_DbTable_ApplicantTransaction();
    	$transaction = $applicantTransactionDn->getTransactionData($transaction_id);
    	$this->view->transaction = $transaction;    		
    	$applicantID=$transaction['at_pes_id'];
    	 
    	$ptestinfo = new App_Model_Application_DbTable_ApplicantPtest();   
    	$applicant_placement_test_info = $ptestinfo->getScheduleInfo($transaction_id);
    	
    	$this->view->aps_id = $applicant_placement_test_info["aps_id"];
    		
    	 
    	if ($this->getRequest()->isPost()) {    		
    		
    		$formData = $this->getRequest()->getPost(); 
    		//echo var_dump($formData);exit;
    		 	    		
				 	
	    		$info["apt_at_trans_id"]=	$transaction_id;
				$info["apt_ptest_code"]	=	"USM2020";
				$info["apt_aps_id"]		=	$formData["aps_test_date"]; //appl_placement_location
				$info["apt_fee_amt"]	=	200000;
				
				$appptestDB = new App_Model_Application_DbTable_ApplicantPtest();
				
				if($applicant_placement_test_info){
					//echo 'update';echo var_dump($info);exit;
					$appptestDB->updateData($info,$applicant_placement_test_info["apt_id"]);
				}else{
					//echo 'insert';exit;
	    				$appptestDB->addData($info);
				}   
				
				//generate payment
				$appptestDB = new App_Model_Application_DbTable_ApplicantPtest();
				$info["apt_bill_no"]=$applicantID;
				$appptestDB->updateData($info,$applicant_placement_test_info["apt_id"]);
				 
				 
				// buat tagihan ke BNI
				//1st:check how many program apply.
				$ptestDB = new App_Model_Application_DbTable_ApplicantProgram();
				$list_program = $ptestDB->getPlacementProgram($transaction_id);
				 
				 
				//insert into invoice and invoice detail
				$inv_data = array(
						'bill_number' => $applicantID,
						'appl_id' => $appl_id,
						'academic_year' => $transaction['at_academic_year'],
						'semester' =>0,
						'no_fomulir' => $applicantID,
						'bill_amount' => 200000,
						'bill_paid' => 0.00,
						'bill_balance' => 200000,
						'bill_description' => 'Biaya Pendaftaran TPA dan MPPI',
						'college_id' => 0,
						'program_code' => 0,
						'creator' => '1',
						'fs_id' => 0,
						'status' => 'A',
						'date_create' => date('Y-m-d h:i:s')
				);
				//echo var_dump($inv_data);exit;
				$invoiceDb=new Studentfinance_Model_DbTable_InvoiceMain();
				$invoiceDetailDb=new Studentfinance_Model_DbTable_InvoiceDetail();
				$inv=$invoiceDb->getInvoiceData($applicantID);
				//echo var_dump($inv);
				//exit;
				if (!$inv)
					$invoice_id = $invoiceDb->insert($inv_data);
				else {
					$invoice_id=$inv['id'];
					$invoiceDb->update($inv_data, 'id='.$invoice_id);
				
				}
					
				//insert invoice detail
					
				$inv_detail_data = array(
						'invoice_main_id' => $invoice_id,
						'fi_id' => 19,//biaya pendaftaran
						'fee_item_description' => 'Biaya Pendaftaran TPA dan MPPI',
						'amount' => 200000
				);
				
				$detail=$invoiceDetailDb->isIn($invoice_id, 19);
				if (!$detail)
					$invoiceDetailDb->insert($inv_detail_data);
				
				else {
					$invoiceDetailDb->updateData($inv_detail_data, 'id='.$detail['id']);
				}
					
				//bank validation id not printed
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'push-e-collection','trxid'=>$transaction_id,'invoice'=>$applicantID,'apsid'=>$formData["aps_test_date"]),'default',true));
					
				
    	 
			
    	} 
    	//to list available placement test from schedule
    	$applicantPlacementScheduleDB = new App_Model_Application_DbTable_ApplicantPlacementSchedule();
    	//$placement_test_info = $applicantPlacementScheduleDB->getInfo();
    	$this->view->testdate = $applicantPlacementScheduleDB->getAvailableDate($appl_id,$transaction_id);
    	  
    	$dbInvoice=new Studentfinance_Model_DbTable_InvoiceMain();
    	$this->view->invoice=$dbInvoice->getInvoiceData($applicantID);
	}
}
