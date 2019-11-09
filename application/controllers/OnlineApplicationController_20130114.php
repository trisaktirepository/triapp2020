<?php

class OnlineApplicationController extends Zend_Controller_Action {
	
	public function init(){
		//$this->_helper->layout->setLayout('application');
	}
	
	public function indexAction(){
		//echo $this->_helper->utility->formatdate(date("Y-m-d H:i:s"));
		
		$this->view->title = $this->view->translate("Application Login");
		
		$form = new App_Form_ApplicationLogin();
		
		//redirect
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'biodata'),'default',true));
    	}
    	//placement test program list
		$placementTestProgramDb = new App_Model_Application_DbTable_PlacementTestProgram();
		$placementTestProgramList = $placementTestProgramDb->getPlacementtestProgramData('PT00001');
		$this->view->programList = $placementTestProgramList;
    	
    	//annoucement
    	$ann = new Application_Model_DbTable_Announcement();
    	$rsanntitle=$ann->fetch(2);
    	$rsannmesg=$ann->fetch(1);
    	$this->view->rsanntitle=$rsanntitle;
    	$this->view->rsannmesg=$rsannmesg;
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    			
			if ($form->isValid($formData)) {
				
				// collect the data from the user
	            Zend_Loader::loadClass('Zend_Filter_StripTags');
	            $filter = new Zend_Filter_StripTags();
	            $username = $filter->filter($this->_request->getPost('username'));
	            $password = $filter->filter($this->_request->getPost('password'));
	            
	            
				//process form 
				$dbAdapter = Zend_Db_Table::getDefaultAdapter();
				$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
				
				$authAdapter->setTableName('applicant_profile')
				    		->setIdentityColumn('appl_email')
				    		->setCredentialColumn('appl_password');
				    		
				// Set the input credential values to authenticate against
                $authAdapter->setIdentity($username);
                //$authAdapter->setCredential(md5($password));
                $authAdapter->setCredential($password);
                
				// do the authentication 
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
                if ($result->isValid()) {
                    // success : store database row to auth's storage system
                    // (not the password though!)
                    $data = $authAdapter->getResultRowObject(null, 'password');
                    
                    
                    /*
                     * transaction move to applicant portal
                     */
                    $data->role = "applicant";
                    
                    /*
                    $transDB = new App_Model_Application_DbTable_ApplicantTransaction();
                    $transaction_data= $transDB->getLastTransaction($data->appl_id);                  
                    $data->transaction_id = $transaction_data["at_trans_id"];
                    
                    //inject role
                    if($transaction_data["at_status"]=='APPLY'){
                    	$data->role = "applicant";
                    }else
                    if($transaction_data["at_status"]=='CLOSE'){
                    	$data->role = "postapplicant";
                    	
                    }else                
                    if($transaction_data["at_status"]=='PROCESS'){
                    	//create new transaction record
                    	
                    	//insert applicant id into applicant_transaction
                    	$data_trans = array();
						$data_trans["at_appl_id"] = $data->appl_id;				
						$data_trans["at_status"]="APPLY";
						$data_trans["at_create_by"] = $data->appl_id;
						$data_trans["at_create_date"]=date("d-m-Y");				
						
						$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
						$trans_id = $transDB->addData($data_trans);
						
						$data->transaction_id = $trans_id;
						
						$data->role = "applicant";
                    }*/
                    
                    
                    $auth->getStorage()->write($data);
                    
                  
                    //set language by getting preferred language
                    
					//$registry = Zend_Registry::getInstance();
					//$locale = $registry->get('Zend_Locale');
					//$this->locale = $locale->toString();

                    //move to applicant portal
                    /*if($transaction_data["at_status"]=='APPLY'){
                    	
                    	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'biodata'),'default',true));
                    }else                    
                	if($transaction_data["at_status"]=='CLOSE'){
                		
                	   	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'verification'),'default',true));
                    }else
                	if($transaction_data["at_status"]=='PROCESS'){
                		
                	   	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'biodata'),'default',true));
                    }*/
                    
                    $this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
                    
                } else {
                    // failure: clear database row from session
                    $this->view->noticeError = 'Login failed. Either username or password is incorrect';
                }
				
			}else{
				$form->populate($formData);
				$this->view->form = $form;
			}
			
    	}
    	
    	$this->view->form = $form;
	}
	
	public function biodataAction() {
		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));	
		}
		
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
    	
    	//transaction data
    	$applicantTransactionDn = new App_Model_Application_DbTable_ApplicantTransaction();
    	$transaction = $applicantTransactionDn->getTransactionData($auth->getIdentity()->transaction_id);
    	$this->view->transaction = $transaction;
    	
    	    	
    	$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
    	$form = new App_Form_Biodata(array ('lang' => $locale));
    	
    	if ($this->getRequest()->isPost()) {    		
    		
    		$formData = $this->getRequest()->getPost();  

    		if ($form->isValid($formData)) {
				
    			$info["appl_fname"]=strtoupper($formData["appl_fname"]);
				$info["appl_mname"]=strtoupper($formData["appl_mname"]);
				$info["appl_lname"]=strtoupper($formData["appl_lname"]);
				$info["appl_email"]=$formData["appl_email"];			
				$info["appl_dob"]=$formData["appl_dob"]['day']."-".$formData["appl_dob"]['month']."-".$formData["appl_dob"]['year'];
				$info["appl_gender"]=$formData["appl_gender"];
				$info["appl_nationality"]=$formData["appl_nationality"];
				//$info["appl_admission_type"]=$formData["appl_admission_type"];
				
				$info["appl_religion"]=$formData["appl_religion"];
				$info["appl_marital_status"]=$formData["appl_marital_status"];				
				$info["appl_jacket_size"]=$formData["appl_jacket_size"];

				if($formData["appl_marital_status"]!=""){
					$info["appl_no_of_child"]=$formData["appl_no_of_child"];	
				}
							
				
				$appProfileDB->updateData($info, $formData["appl_id"]);
				
				$father["af_appl_id"]=$formData["appl_id"];
				$father["af_name"]=$formData["father_name"];
				$father["af_relation_type"]=$formData["relationtype"];
				$father["af_address1"]=$formData["appl_address1"];
				$father["af_address2"]=$formData["appl_address2"];
				$father["af_state"]=$formData["appl_state"];
				$father["af_city"]=$formData["appl_city"];
				$father["af_postcode"]=$formData["appl_postcode"];
				$father["af_phone"]=$formData["telephone"];
				$father["af_email"]=$formData["email"];
				$father["af_job"]=$formData["job"];
				$father["af_family_condition"]=$formData["condition"];
				
				
				
				$mother["af_appl_id"]=$formData["appl_id"];
				$mother["af_name"]=$formData["mother_name"];
				$mother["af_relation_type"]=$formData["mrelationtype"];
				$mother["af_address1"]=$formData["mappl_address1"];
				$mother["af_address2"]=$formData["mappl_address2"];
				$mother["af_state"]=$formData["mappl_state"];
				$mother["af_city"]=$formData["mappl_city"];
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
	
				//15/12/2012
				//update application_transaction
				//$applicationTransactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
				//$applicationTransactionDb->updateData(array('at_appl_type'=>$formData["appl_admission_type"]), $auth->getIdentity()->transaction_id);
				
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'contactinfo'),'default',true));
    		
    		} else {
    			$form->populate($formData);
				$this->view->form = $form;
					
    		}//end isvalid form
			
    	}else{
    		$family = new App_Model_Application_DbTable_ApplicantFamily();
    		
    		$rsfather=$family->fetchdata($appl_id,20);
    		
    		if ($rsfather){
    		$applicant["father_name"]=$rsfather["af_name"];
			$applicant["relationtype"]=$rsfather["af_relation_type"];
			$applicant["appl_address1"]=$rsfather["af_address1"];
			$applicant["appl_address2"]=$rsfather["af_address2"];
			$applicant["appl_state"]=$rsfather["af_state"];
			$applicant["appl_city"]=$rsfather["af_city"];
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
			$applicant["mappl_address1"]=$rsmother["af_address1"];
			$applicant["mappl_address2"]=$rsmother["af_address2"];
			$applicant["mappl_state"]=$rsfather["af_state"];
			$applicant["mappl_city"]=$rsmother["af_city"];
			$applicant["mappl_postcode"]=$rsmother["af_postcode"];
			$applicant["mtelephone"]=$rsmother["af_phone"];
			$applicant["memail"]=$rsmother["af_email"];
			$applicant["mjob"]=$rsmother["af_job"];
			$applicant["medulevel"]=$rsmother["af_education_level"];
			$applicant["mcondition"]=$rsfather["af_family_condition"];
			}
				
			
    			
    		/*if($applicant['appl_admission_type']==null){
    			$applicant['appl_admission_type'] = '2';
    		}
    		*/
    		

	    	$form->populate($applicant);
	    	$this->view->form = $form;
    	}
	}
	
	public function contactinfoAction() {
		
		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));	
		}
		
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
    	
    	//transaction data
    	$applicantTransactionDn = new App_Model_Application_DbTable_ApplicantTransaction();
    	$transaction = $applicantTransactionDn->getTransactionData($auth->getIdentity()->transaction_id);
    	$this->view->transaction = $transaction;
    	
		//check biodata
    	if($applicant['appl_email']==null){
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'biodata','msg'=>$this->view->translate('Please fill in Biodata.')),'default',true));
    	}
    	
    	$form = new App_Form_ContactInfo();
    	
    	if ($this->getRequest()->isPost()) {
	    		$formData = $this->getRequest()->getPost();
	    		
	    		if ($form->isValid($formData)) {
	    				
	    			 unset($formData['check_opt']);
	    			 unset($formData['check_opt_same']);
	    			 unset($formData['save']);
	    			
	    			$appProfileDB->updateData($formData, $formData["appl_id"]);
	    		
	    			
	    			if($transaction['entry_type']!=2){
	    				
	    				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'admission','id'=>$transaction_id),'default',true));
		    			
	    			}else{//kalo manual entry xyah pilih admission lagi
	    				
	    				if( $transaction['at_appl_type'] == 1 ){
							$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'placement-test','id'=>$transaction_id),'default',true));
		    			}else{
		    				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme','id'=>$transaction_id),'default',true));
		    			}
	    			}
				
	    		}else{
					$form->populate($formData);
					$this->view->form = $form;
	    		}
    	}else{
	    	$form->populate($applicant);
	    	$this->view->form = $form;
    	}
	}
	
	
	public function admissionAction() {
		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));	
		}
		
		$msg = $this->_getParam('msg', null);
		if($msg){
			$this->view->noticeError = $msg;	
		}
			
		//title
    	$this->view->title = $this->view->translate("Admission Type");    	
    	
    	$auth = Zend_Auth::getInstance(); 
    	$appl_id = $auth->getIdentity()->appl_id;      	
    	$this->view->appl_id = $appl_id;
    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	
    	//transaction data
    	$applicantTransactionDn = new App_Model_Application_DbTable_ApplicantTransaction();
    	$transaction = $applicantTransactionDn->getTransactionData($auth->getIdentity()->transaction_id);
    	$this->view->transaction = $transaction;
    	
    	
    	$form = new App_Form_Admission();
    	$this->view->form = $form;
    	
    	if ($this->getRequest()->isPost()) {
	    		$formData = $this->getRequest()->getPost();
	    		
	    		if ($form->isValid($formData)) {
	    			
	    			//delete aplicant program
	    			$applicantProgram = new App_Model_Application_DbTable_ApplicantProgram();
		    		$applicantProgram->deleteTransactionData($auth->getIdentity()->transaction_id);
	    			
	    			$info["at_appl_type"]=$formData["at_appl_type"];
					$applicantTransactionDn->updateData($info, $auth->getIdentity()->transaction_id);
	    			
	    			
	    			if( $formData["at_appl_type"] == 1 ){ //USM
						$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'placement-test'),'default',true));
	    			}else{
	    				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme'),'default',true));
	    			}
	    		}
	    		
    	}else{
	    	$form->populate($transaction);
	    	$this->view->form = $form;
    	}//end post
    	
	}
	
	
	
	public function programmeAction() {
		
		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));	
		}
		
		$auth = Zend_Auth::getInstance();    	
    	$transaction_id = $auth->getIdentity()->transaction_id;
    	$this->view->transaction_id = $auth->getIdentity()->transaction_id;
    	
    	//transaction data
    	$applicantTransactionDn = new App_Model_Application_DbTable_ApplicantTransaction();
    	$transaction = $applicantTransactionDn->getTransactionData($transaction_id);
    	$this->view->transaction = $transaction;
    	
    	//applicant profile data
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($auth->getIdentity()->appl_id);
    	$this->view->applicant = $applicant;
    	
		if($transaction['at_appl_type']=='0'){
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'admission','msg'=>$this->view->translate('Please select admission type')),'default',true));
    	}
    	
    	//application education head data
    	$applicationEducationDb = new App_Model_Application_DbTable_ApplicantEducation();
    	$applicationEducationData = $applicationEducationDb->getDataByapplID($applicant['appl_id']);
    	$this->view->educationData = $applicationEducationData;
    	
		
		
		$msg = $this->_getParam('msg', null);
		if($msg){
			$this->view->noticeError = $msg;	
		}
    	
    	//title
    	$this->view->title = $this->view->translate("Programme");
    		
		//check for admission type
		if( $transaction['at_appl_type'] == 2 ){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-highschool'),'default',true));
			
		}else{//admission type = placement test (id=1)
			//check ptest
	    	$ptestDb = new App_Model_Application_DbTable_ApplicantPtest();
	    	$ptestData = $ptestDb->getPtest($transaction_id);
	    	
	    	if($transaction["at_appl_type"]==1){//placement test
		    	if(!$ptestData){
		    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'placement-test','msg'=>$this->view->translate('please_enter_placement_test_info.')),'default',true));
		    	}
			}
		
    		$form = new App_Form_Programme(array('ae_appl_id'=>$applicant['appl_id'],'admissiontype'=>1));
    		
	    	if ($this->getRequest()->isPost()) {
	    		$formData = $this->getRequest()->getPost();

	    		if($form->isValid($formData)){
	    			
	    			/** EDUCATION **/
	    				$educationDb = new App_Model_Application_DbTable_ApplicantEducation();
						//delete education
						$educationDb->delete('ae_appl_id = '.$auth->getIdentity()->appl_id);
						
						//add education
						$data = array(
							'ae_appl_id' => $auth->getIdentity()->appl_id,
							'ae_institution' => $formData['ae_institution'],
							'ae_discipline_code' => $formData['ae_discpline'],
							'ae_year_from' => date('y-m-d', strtotime($formData['ae_year_from'])),
							'ae_year_end' => date('y-m-d', strtotime($formData['ae_year_end']))
						); 
						
						$aed_id = $educationDb->addData($data);
	    			/** END EDUCATION **/
	    			
	    			/*** PREFERED PRGORRAMME ***/
	    			$applicantProgram = new App_Model_Application_DbTable_ApplicantProgram();
	    			
	    			//delete ptest
	    			$applicantProgram->deleteTransactionData($transaction['at_trans_id']);
	    			
	    			//getPTestProgram Data
	    			$ptestProgram = new App_Model_Application_DbTable_PlacementTestProgram();
	    			
	    			
	    			//add ptest program prefered 1
	    			$ptestData = $ptestProgram->getData($formData['app_id']);
	    			$data1 = array(
	    				'ap_at_trans_id' =>$transaction['at_trans_id'],
	    				'ap_prog_code' => $ptestData['app_program_code'],
	    				'ap_ptest_prog_id' => $ptestData['app_id'],
	    				'ap_preference' =>1
	    			);
	    			$applicantProgram->insert($data1);
	    			
	    			///add ptest program prefered 2
	    			if($formData['app_id2']!=null){
	    				
	    				$ptestData2 = $ptestProgram->getData($formData['app_id2']);
		    			$data2 = array(
		    				'ap_at_trans_id' =>$transaction['at_trans_id'],
		    				'ap_prog_code' => $ptestData2['app_program_code'],
		    				'ap_ptest_prog_id' => $ptestData2['app_id'],
		    				'ap_preference' =>2
		    			);
		    			
		    			$applicantProgram->insert($data2);
	    			}
	    			
	    			//redirect
	    			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'uploaddocument'),'default',true));
	    			
	    		}else{
	    			$form->populate($formData);
		    		$this->view->form = $form;
	    		}
	    				
	    	}else{
	    		//get current data
	    		$applicantProgramDb = new App_Model_Application_DbTable_ApplicantProgram();
	    		$dataPreferedProgram = $applicantProgramDb->getPlacementProgram($transaction['at_trans_id']);

	    		$dataToPopulate = array();
	    		if($dataPreferedProgram){
	    			$dataToPopulate['app_id'] = $dataPreferedProgram[0]['ap_ptest_prog_id'];
	    			$dataToPopulate['app_id2']= $dataPreferedProgram[1]['ap_ptest_prog_id'];
	    		}
	    		
	    		if($applicationEducationData){
	    			$dataToPopulate['ae_institution'] = $applicationEducationData['ae_institution'];
	    			$dataToPopulate['ae_year_from'] =  date('F Y', strtotime($applicationEducationData['ae_year_from']));
	    			$dataToPopulate['ae_year_end'] =  date('F Y', strtotime($applicationEducationData['ae_year_end']));
	    			$dataToPopulate['ae_discpline'] =  $applicationEducationData['ae_discipline_code'];
	    			
	    			//get school master data
	    			$schoolMasterDb = new App_Model_General_DbTable_SchoolMaster();
	    			$schoolData = $schoolMasterDb->getData($dataToPopulate['ae_institution']);
	    			
	    			$dataToPopulate['type'] = $schoolData['sm_type'];
	    			$dataToPopulate['state'] = $schoolData['sm_state'];
	    		}
	    		
	    		$form->populate($dataToPopulate);
	    		
		    	$this->view->form = $form;	
	    	}
	    	
	    	$this->view->form = $form;
		}
	}
	
	/**
	 * 
	 * Sila bagi tahu yati jika berubah dkt action ini
	 * Action Related to Agent
	 */
	public function programmeHighschoolAction(){
		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));	
		}
		
		$auth = Zend_Auth::getInstance();    	
    	$transaction_id = $auth->getIdentity()->transaction_id;
    	$this->view->transaction_id = $auth->getIdentity()->transaction_id;
    	
    	//transaction data
    	$applicantTransactionDn = new App_Model_Application_DbTable_ApplicantTransaction();
    	$transaction = $applicantTransactionDn->getTransactionData($transaction_id);
    	$this->view->transaction = $transaction;
    	
    	//applicant profile data
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($auth->getIdentity()->appl_id);
    	$this->view->applicant = $applicant;
    	
    	//application education head data
    	$applicationEducationDb = new App_Model_Application_DbTable_ApplicantEducation();
    	$applicationEducationData = $applicationEducationDb->getDataByapplID($applicant['appl_id']);
    	$this->view->educationData = $applicationEducationData;
    	
    	//applicant program preference
    	$applicantProgramDb = new App_Model_Application_DbTable_ApplicantProgram();
    	$applicantProgram = $applicantProgramDb->getPlacementProgram($transaction_id);
    	    	
    	//title
    	$this->view->title = $this->view->translate("Programme");
    	
		//check for admission type
		if( $transaction['at_appl_type'] == 1 ){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme'),'default',true));
			
		}else{//admission type = high school(2)
			$form = new App_Form_Programme(array('ae_appl_id'=>$applicant['appl_id'], 'admissiontype'=>2));

			if ($this->getRequest()->isPost()) {  
				$formData = $this->getRequest()->getPost();
				
				  		
				if ($form->isValid($formData)) {
					
					$educationDb = new App_Model_Application_DbTable_ApplicantEducation();
					
					//delete education on current transaction
					$educationDb->delete('ae_appl_id = '.$auth->getIdentity()->appl_id . ' and ae_transaction_id = '.$transaction_id);
					
					//add education
					$data = array(
						'ae_appl_id' => $auth->getIdentity()->appl_id,
						'ae_transaction_id' => $transaction_id,
						'ae_institution' => $formData['ae_institution'],
						'ae_discipline_code' => $formData['ae_discpline'],
						'ae_year_from' => date('y-m-d', strtotime($formData['ae_year_from'])),
						'ae_year_end' => date('y-m-d', strtotime($formData['ae_year_end']))
					); 
					
					$aed_id = $educationDb->addData($data);
					
					if($aed_id){
						$educationDetailDb = new App_Model_Application_DbTable_ApplicantEducationDetail();
						
						//add education detail
						$i=0;
						foreach ( $formData['aed_subject_id'] as $subject_id) {
							$data = array(
								'aed_ae_id' => $aed_id,
								'aed_subject_id' => $subject_id,
								'aed_sem1' => ($formData['aed_sem1'][$i]!="" && $formData['aed_sem1'][$i]!="0" )?$formData['aed_sem1'][$i]:null,
								'aed_sem2' => ($formData['aed_sem2'][$i]!="" && $formData['aed_sem2'][$i]!="0" )?$formData['aed_sem2'][$i]:null,
								'aed_sem3' => ($formData['aed_sem3'][$i]!="" && $formData['aed_sem3'][$i]!="0" )?$formData['aed_sem3'][$i]:null,
								'aed_sem4' => ($formData['aed_sem4'][$i]!="" && $formData['aed_sem4'][$i]!="0" )?$formData['aed_sem4'][$i]:null,
								'aed_sem5' => ($formData['aed_sem5'][$i]!="" && $formData['aed_sem5'][$i]!="0" )?$formData['aed_sem5'][$i]:null,
								'aed_sem6' => ($formData['aed_sem6'][$i]!="" && $formData['aed_sem6'][$i]!="0" )?$formData['aed_sem6'][$i]:null,
								'aed_average' => ($formData['aed_average'][$i]!="" && $formData['aed_average'][$i]!="0" )?$formData['aed_average'][$i]:null
							); 
						
							$educationDetailDb->addData($data);
							$i++;	
						}
						
						/*** PREFERED PRGORRAMME ***/
		    			$applicantProgram = new App_Model_Application_DbTable_ApplicantProgram();
		    			
		    			//delete ptest
		    			$applicantProgram->deleteTransactionData($transaction['at_trans_id']);

		    			//add ptest program prefered 1
		    			$data1 = array(
		    				'ap_at_trans_id' =>$transaction['at_trans_id'],
		    				'ap_prog_code' => $formData['ap_prog_code'],		    				
		    				'ap_preference' =>1
		    			);
		    			
		    			//checking for selected programme
		    			if( isset($data1['ap_prog_code']) && $data1['ap_prog_code']!=null && $data1['ap_prog_code']!="" && $data1['ap_prog_code']!=0 ){
		    				$applicantProgram->insert($data1);
		    			}else{
		    				$this->view->noticeError = $this->translate("Silalah pilih program studi");
		    				
		    				$form->populate($formData);
							$this->view->form = $form;
		    			}
						
						//redirect
						$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'uploaddocument'),'default',true));
					}
				}else{
					$form->populate($formData);
					$this->view->form = $form;		
				}
					    				
	    	}else{
	    		$dataToPopulate = array();
	    		if($applicationEducationData){
	    			$dataToPopulate['ae_institution'] = $applicationEducationData['ae_institution'];
	    			$dataToPopulate['ae_year_from'] =  date('F Y', strtotime($applicationEducationData['ae_year_from']));
	    			$dataToPopulate['ae_year_end'] =  date('F Y', strtotime($applicationEducationData['ae_year_end']));
	    			$dataToPopulate['ae_discpline'] =  $applicationEducationData['ae_discipline_code'];
	    			
	    			//get school master data
	    			$schoolMasterDb = new App_Model_General_DbTable_SchoolMaster();
	    			$schoolData = $schoolMasterDb->getData($dataToPopulate['ae_institution']);
	    			
	    			$dataToPopulate['type'] = $schoolData['sm_type'];
	    			$dataToPopulate['state'] = $schoolData['sm_state'];
	    		}
	    		
	    		if($applicantProgram){
	    			$dataToPopulate['ap_prog_code'] = $applicantProgram[0]['ap_prog_code'];
	    			//$dataToPopulate['ap_prog_code2'] = $applicantProgram[1]['ap_prog_code'];
	    		}
		    	
	    		$form->populate($dataToPopulate);
	    		$this->view->form = $form;
	    	}
	    	
			$this->view->form = $form;
		}
	}	
	
	public function ajaxGetSchoolAction(){
    	$school_type_id = $this->_getParam('school_type_id', 0);
    	$school_state_id = $this->_getParam('school_state_id', 0);
    	$keyvalue = $this->_getParam('keyvalue', 0);
    	
     	//if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        //}
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	$db = Zend_Db_Table::getDefaultAdapter();
	  	
	  	if($keyvalue==1){
		  	$select = $db->select()
		                 ->from(array('sm'=>'school_master'),array('sm_id','sm_name'))
		                 ->order('sm.sm_name ASC');
		    
		    if($school_type_id!=0){
		    	$select->where('sm.sm_type = ?', $school_type_id);
		    }
			if($school_state_id!=0){
		    	$select->where('sm.sm_state = ?', $school_state_id);
		    }
	  	}else{
	  		$select = $db->select()
		                 ->from(array('sm'=>'school_master'))
		                 ->order('sm.sm_name ASC');
		    
		    if($school_type_id!=0){
		    	$select->where('sm.sm_type = ?', $school_type_id);
		    }
			if($school_state_id!=0){
		    	$select->where('sm.sm_state = ?', $school_state_id);
		    }
	  	}
	    
	    
	    
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
	  	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($row);
		
		echo $json;
		exit();
    }
    
	public function ajaxGetDisciplineAction(){
    	$school_type_id = $this->_getParam('school_type_id', 2);
    	
     	//if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        //}
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	$db = Zend_Db_Table::getDefaultAdapter();
	  	$select = $db->select(array('smd_code','smd_desc'))
	                 ->from(array('sd'=>'school_discipline'))
	                 ->where('sd.smd_school_type = ?', $school_type_id)
	                 ->order('sd.smd_desc ASC');
	    
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
	  	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($row);
		
		echo $json;
		exit();
    }
    
	public function ajaxGetDisciplineSubjectAction(){
    	$discipline_code = $this->_getParam('discipline_code', 0);
    	    	
     	//if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        //}
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
        $auth = Zend_Auth::getInstance(); 
    	$appl_id = $auth->getIdentity()->appl_id;
    	
	  	$db = Zend_Db_Table::getDefaultAdapter();
	  	
	  	//get applicant education head data
	  	$applicationEducationDb = new App_Model_Application_DbTable_ApplicantEducation();
	  	$educationData = $applicationEducationDb->getDataByapplID($appl_id);
	  	
	  	$select = $db->select()
	                 ->from(array('sds'=>'school_decipline_subject'))
	                 ->where('sds.sds_discipline_code  = ?', $discipline_code)
	                 ->join(array('s'=>'school_subject'),'s.ss_id = sds.sds_subject')
	                 //->joinLeft(array('aed'=>'applicant_education_detl'),'aed.')
	                 ->order('s.ss_core_subject DESC')
	                 ->order('s.ss_subject ASC');
	                 
	    if($educationData){
	    	$select->joinLeft(array('aed'=>'applicant_education_detl'),"aed.aed_ae_id = ".$educationData['ae_id']." and  aed.aed_subject_id = sds.sds_subject");
	    }
	    	   
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
	  	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($row);
		
		echo $json;
		exit();
    }
	
	public function ajaxGetProgrammePtAction(){
    	$discipline_code = $this->_getParam('discipline_code', 0);
    	
        $this->_helper->layout->disableLayout();
        
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
        //program in placement test with discipline filter

        //transaction data
		$auth = Zend_Auth::getInstance();    	
    	$transaction_id = $auth->getIdentity()->transaction_id;
    	$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
        $transaction_data= $transDB->getTransactionData($transaction_id);
    	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		//get placement test data
		$select = $db->select(array('apt_ptest_code'))
	                 ->from(array('ap'=>'applicant_ptest'))
	                 ->where('ap.apt_at_trans_id = ?', $transaction_id);
	                 
	    $stmt = $db->query($select);
        $placementTestCode = $stmt->fetch();
        
        //get placementest program data filtered with discipline
	  	$select = $db->select()
	                 ->from(array('app'=>'appl_placement_program'))
	                 ->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode = app.app_program_code', array('ArabicName','ProgramName','ProgramCode','IdProgram') )
	                 ->join(array('apr'=>'appl_program_req'),"apr.apr_program_code = app.app_program_code and apr.apr_decipline_code = '".$discipline_code."' and apr.apr_academic_year = ".$transaction_data['at_academic_year'])
	                 ->where('app.app_placement_code  = ?', $placementTestCode['apt_ptest_code'])
	                 ->order('p.ArabicName ASC');
				
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
        
	  	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		
		if($row){
        	$json = Zend_Json::encode($row);
        }else{
        	$json = null;
        }
		
		echo $json;
		exit();
    }
    
	public function ajaxGetProgrammeHsAction(){
    	$discipline_code = $this->_getParam('discipline_code', 0);
    	$academic_year = $this->_getParam('academic_year', 0);
    	
        $this->_helper->layout->disableLayout();
        
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
        //program in placement test with discipline filter

        //transaction data
		$auth = Zend_Auth::getInstance();
		$appl_id = $auth->getIdentity()->appl_id;    	
    	$transaction_id = $auth->getIdentity()->transaction_id;
    	$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
        $transaction_data= $transDB->getTransactionData($transaction_id);
    	
		$db = Zend_Db_Table::getDefaultAdapter();
		
	                 
		//get transaction data
		$select = $db->select()
	                 ->from(array('at'=>'applicant_transaction'))
	                 ->where('at.at_trans_id = ?', $transaction_id);
	                 
	    $stmt = $db->query($select);
        $transactionData = $stmt->fetch();
        
        $select_applied = $db->select()
         			 ->from(array('at'=>'applicant_transaction'),array())
	                 ->join(array('ap'=>'applicant_program'),'ap.ap_at_trans_id=at.at_trans_id',array('ap_prog_code'=>'distinct(ap.ap_prog_code)'))
	                 ->where("at.at_appl_id= '".$appl_id."'")
	                 ->where("ap.ap_at_trans_id != '".$transaction_id."'")
	                 ->where("at.at_appl_type=2");	                 
	               

        //get program data based on discipline
	  	$select = $db->select()
	                 ->from(array('apr'=>'appl_program_req'))
	                 ->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode = apr.apr_program_code' )
	                 ->where("p.ProgramCode NOT IN (?)",$select_applied)
	                 ->order('p.ArabicName ASC');
	                 
	    if($discipline_code!=0){
	    	$select->where('apr.apr_decipline_code  = ?', $discipline_code);
	    }
	    
		if($academic_year!=0){
	    	$select->where('apr.apr_academic_year  = ?', $academic_year);
	    }             
				
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
        
	  	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		
		if($row){
        	$json = Zend_Json::encode($row);
        }else{
        	$json = null;
        }
		
		echo $json;
		exit();
    }
    
	public function uploaddocumentAction(){
		
		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));	
		}
		
		$this->view->title = $this->view->translate("upload_document");
		
		//$appl_id = $this->_getParam('id', 0);
    	$auth = Zend_Auth::getInstance(); 
    	
    	$appl_id = $auth->getIdentity()->appl_id; 
    	$transaction_id = $auth->getIdentity()->transaction_id; 
    	     	
    	$this->view->appl_id = $appl_id;    	
    	$this->view->transaction_id = $transaction_id;
    	    	    	
    	$applicantProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
    	$applicantProgram = $applicantProgramDB->getPlacementProgram($transaction_id);
    	
    	if(!$applicantProgram){
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme','msg'=>$this->view->translate('please_fill_in_program_prefered')),'default',true));
    	}

    		//transaction data
    	$applicantTransactionDn = new App_Model_Application_DbTable_ApplicantTransaction();
    	$transaction = $applicantTransactionDn->getTransactionData($transaction_id);
    	$this->view->transaction = $transaction;
    	
		$msg = $this->_getParam('msg', null);
		if($msg){
			$this->view->noticeError = $msg;	
		}
    	   	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	
    	$uploadFileDB = new App_Model_Application_DbTable_UploadFile();
    	$getUploadData = $uploadFileDB->getUploadData($transaction_id);
    	$this->view->uploadeddata = $getUploadData;
    	
    	
    	$noticeMessage ="<p>".$this->view->translate("Silakan mengunggah semua dokumen tertera dibawah").":-</p>";
    	$noticeMessage .="<table width='100%' cellpadding=5 cellspacing=1 border=0 class='table3'><tr><td>".$this->view->translate("Dokumen utk PSSB")."</td><td>".$this->view->translate("Dokumen utk USM")."</td></tr>";
    	$noticeMessage .="<tr><td width='50%'><ul>";
    	$noticeMessage .="<li>".$this->view->translate("photograph")."</li>";
    	$noticeMessage .="<li>".$this->view->translate("nric")."</li>";
    	$noticeMessage .="<li>".$this->view->translate("academic_award")."</li>"; //surat buta huruf
    	$noticeMessage .="<li>".$this->view->translate("academic_transcript")."</li>";
    	$noticeMessage .="</ul></td>";
    	$noticeMessage .="<td><ul>";
    	$noticeMessage .="<li>".$this->view->translate("photograph")."</li>";
    	$noticeMessage .="<li>".$this->view->translate("nric")."</li>";
    	$noticeMessage .="<li>".$this->view->translate("Surat Keterangan tidak buta warna dari Dokter Mata")."</li>"; //surat buta huruf
    	$noticeMessage .="</ul></td></tr></table>";    	
    	
    	$this->view->noticeMessage = $noticeMessage;
    	
	}
	
	public function uploaddocumentfilesAction(){
		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));	
		}
		
		//$appl_id = $this->_getParam('id', 0);
    	$auth = Zend_Auth::getInstance(); 
    	$appl_id = $auth->getIdentity()->appl_id;      	
    	$this->view->appl_id = $appl_id;
    	
    	$transaction_id = $auth->getIdentity()->transaction_id;
    	$this->view->transaction_id = $transaction_id;
    	    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	
    	if ($this->getRequest ()->isPost ()) {
			$formData = $_POST;
			
			$uploadfileDB = new App_Model_Application_DbTable_UploadFile();
			
    		if($transaction_id==$formData["transaction_id"]){
    			$iteration_academic = $formData["iteration_academic"];
    			$iteration_transcript = $formData["iteration_transcript"];
    			
    			
    			///upload_file
				$apath = DOCUMENT_PATH."/applicant";
				
    			//create directory to locate file			
				if (!is_dir($apath)) {
			    	mkdir($apath, 0775);
				}
    			
				///upload_file
				$applicant_path = DOCUMENT_PATH."/applicant/".date("mY");
				
    			//create directory to locate file			
				if (!is_dir($applicant_path)) {
			    	mkdir($applicant_path, 0775);
				}
				
				
				$major_path = $applicant_path."/".$formData["transaction_id"];
				
    			//create directory to locate file			
				if (!is_dir($major_path)) {
			    	mkdir($major_path, 0775);
				}
				
				if (is_uploaded_file($_FILES["photograph"]['tmp_name'])){
					
					$ext_photo = $this->getFileExtension($_FILES["photograph"]["name"]);
					
					if($ext_photo==".jpg" || $ext_photo==".JPG" || $ext_photo==".jpeg" || $ext_photo==".JPEG" || $ext_photo==".gif" || $ext_photo==".GIF" || $ext_photo==".png" || $ext_photo==".PNG"){
						$flnamephoto = date('Ymdhs')."_photograph".$ext_photo;
						$path_photograph = $major_path."/".$flnamephoto;
						move_uploaded_file($_FILES["photograph"]['tmp_name'], $path_photograph);
						
						$upd_photo = array(
							'auf_appl_id' => $formData["transaction_id"],
							'auf_file_name' => $flnamephoto, 
							'auf_file_type' => 33, 
							'auf_upload_date' => date("Y-m-d h:i:s"),
							'auf_upload_by' => $transaction_id,
							'pathupload' => $path_photograph
						);
						
						$uploadfileDB->addData($upd_photo);
						
					}				
					
				}
				if (is_uploaded_file($_FILES["nric"]['tmp_name'])){
					
					$ext_nric = $this->getFileExtension($_FILES["nric"]["name"]);
					
					if($ext_nric==".jpg" || $ext_nric==".JPG" || $ext_nric==".jpeg" || $ext_nric==".JPEG" || $ext_nric==".gif" || $ext_nric==".GIF" || $ext_nric==".png" || $ext_nric==".PNG"){
						$flnamenric = date('Ymdhs')."_nric".$ext_nric;
						$path_nric = $major_path."/".$flnamenric;
						move_uploaded_file($_FILES["nric"]['tmp_name'], $path_nric);
						
						$upd_nric = array(
							'auf_appl_id' => $formData["transaction_id"],
							'auf_file_name' => $flnamenric, 
							'auf_file_type' => 34, 
							'auf_upload_date' => date("Y-m-d h:i:s"),
							'auf_upload_by' => $transaction_id,
							'pathupload' => $path_nric
						);
						
						
						$uploadfileDB->addData($upd_nric);
					}
				}
				
			
				if (is_uploaded_file($_FILES["passport"]['tmp_name'])){
					
					$ext_passport = $this->getFileExtension($_FILES["passport"]["name"]);
					
					if($ext_passport==".jpg" || $ext_passport==".JPG" || $ext_passport==".jpeg" || $ext_passport==".JPEG" || $ext_passport==".gif" || $ext_passport==".GIF" || $ext_passport==".png" || $ext_passport==".PNG"){
						$flnamepassport = date('Ymdhs')."_passport".$ext_passport;
						$path_passport = $major_path."/".$flnamepassport;
						move_uploaded_file($_FILES["passport"]['tmp_name'], $path_passport);
						
						$upd_passport = array(
							'auf_appl_id' => $formData["transaction_id"],
							'auf_file_name' => $flnamepassport, 
							'auf_file_type' => 35, 
							'auf_upload_date' => date("Y-m-d h:i:s"),
							'auf_upload_by' => $transaction_id,
							'pathupload' => $path_passport
						);
						
						$uploadfileDB->addData($upd_passport);
					}
				}
				
				for ($a=1; $a<=$iteration_academic; $a++){
//						
					if (is_uploaded_file($_FILES["academic_award".$a]['tmp_name'])){
						$ext_academic = $this->getFileExtension($_FILES["academic_award".$a]["name"]);
						
						if($ext_academic==".jpg" || $ext_academic==".JPG" || $ext_academic==".jpeg" || $ext_academic==".JPEG" || $ext_academic==".gif" || $ext_academic==".GIF" || $ext_academic==".png" || $ext_academic==".PNG"){
						
							$flnameacademic = date('Ymdhs')."_academic_".$a.$ext_academic;
							$path_academic = $major_path."/".$flnameacademic;
							move_uploaded_file($_FILES["academic_award".$a]['tmp_name'], $path_academic);
							
							$upd_academic = array(
								'auf_appl_id' => $formData["transaction_id"],
								'auf_file_name' => $flnameacademic, 
								'auf_file_type' => 36, 
								'auf_upload_date' => date("Y-m-d h:i:s"),
								'auf_upload_by' => $transaction_id,
								'pathupload' => $path_academic
							);
							
							$uploadfileDB->addData($upd_academic);
						}
					}
				}
				
				for ($b=1; $b<=$iteration_transcript; $b++){
//						
					if (is_uploaded_file($_FILES["academic_transcript".$b]['tmp_name'])){
						$ext_transcript = $this->getFileExtension($_FILES["academic_transcript".$b]["name"]);
						if($ext_transcript==".jpg" || $ext_transcript==".JPG" || $ext_transcript==".jpeg" || $ext_transcript==".JPEG" || $ext_transcript==".gif" || $ext_transcript==".GIF" || $ext_transcript==".png" || $ext_transcript==".PNG"){
						
							$flnametranscript = date('Ymdhs')."_transcript_".$b.$ext_transcript;
							$path_transcript = $major_path."/".$flnametranscript;
							move_uploaded_file($_FILES["academic_transcript".$b]['tmp_name'], $path_transcript);
							
							$upd_transcript = array(
								'auf_appl_id' => $formData["transaction_id"],
								'auf_file_name' => $flnametranscript, 
								'auf_file_type' => 37, 
								'auf_upload_date' => date("Y-m-d h:i:s"),
								'auf_upload_by' => $transaction_id,
								'pathupload' => $path_transcript
							);
							
							$uploadfileDB->addData($upd_transcript);
						}
					}
				}
    		}
    		
    		$msg = $this->_getParam('msg', null);
			if($msg){
				$this->view->noticeError = $msg;
			}
    		
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'uploaddocument'),'default',true));

    	}
	}
	
	function getFileExtension($filename){
  		return substr($filename, strrpos($filename, '.'));
	}
	
	public function removeFileAction(){
		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));	
		}
		
		$auth = Zend_Auth::getInstance(); 
    	$appl_id = $auth->getIdentity()->appl_id;      	
    	$this->view->appl_id = $appl_id;
    	
    	$transaction_id = $auth->getIdentity()->transaction_id;
    	$this->view->transaction_id = $transaction_id;
    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($transaction_id);
    	$this->view->applicant = $applicant;
    	
    	$uploadFileDB = new App_Model_Application_DbTable_UploadFile();    	
		
    	///upload_file
    	//	$applicant_path = DOCUMENT_PATH."/applicant/".date("mY");
		
				
    	if ($this->getRequest ()->isPost ()) {
			$formData = $_POST;
			
			$chk = count($formData["rem"]);
			
			for ($i=0; $i<$chk; $i++){
				
				$fileid = $formData["rem"][$i];
				
				$getfile = $uploadFileDB->getFileByID($transaction_id,$fileid);
				
				$pathupload=$getfile["pathupload"];
				$dt = explode("documents",$pathupload);
				$path = $dt[1];
				$major_path = DOCUMENT_PATH.$path;
								
				if($getfile["auf_id"]==$fileid){					
					//$rmfileatch = $major_path."/".$getfile["auf_file_name"];
					$rmfileatch = $major_path;
					
					//remove uploaded files
					if(is_file($rmfileatch)){
						unlink($rmfileatch);
					}
					
					//delete from table
					$uploadFileDB->deleteData($fileid);
				}			
			}
			
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'uploaddocument'),'default',true));
    	}    	
	}
	
	
	public function reuploaddocumentfilesAction() {
		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));	
		}
		
		//$appl_id = $this->_getParam('id', 0);
    	$auth = Zend_Auth::getInstance(); 
    	$appl_id = $auth->getIdentity()->appl_id;      	
    	$this->view->appl_id = $appl_id;
    	
    	$transaction_id = $auth->getIdentity()->transaction_id;
    	$this->view->transaction_id = $transaction_id;
    	    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($transaction_id);
    	$this->view->applicant = $applicant;
    	
    	$uploadfileDB = new App_Model_Application_DbTable_UploadFile();
    	
    	if ($this->getRequest ()->isPost ()) {
			$formData = $_POST;
			
    		if($transaction_id==$formData["transaction_id"]){
    			$iteration_academic = $formData["iteration_academic"];
    			$iteration_transcript = $formData["iteration_transcript"];
    			
				///upload_file
				//$major_path = DOCUMENT_PATH."/".$formData["transaction_id"];
				
    			//create directory to locate file			
				/*if (!is_dir($output_directory_path)) {
			    	mkdir($major_path, 0775);
				}*/
    			
    			
    			///upload_file
				$apath = DOCUMENT_PATH."/applicant";
				
    			//create directory to locate file			
				if (!is_dir($apath)) {
			    	mkdir($apath, 0775);
				}
    			
				///upload_file
				$applicant_path = DOCUMENT_PATH."/applicant/".date("mY");
				
    			//create directory to locate file			
				if (!is_dir($applicant_path)) {
			    	mkdir($applicant_path, 0775);
				}
				
				
				$major_path = $applicant_path."/".$formData["transaction_id"];
				
    			//create directory to locate file			
				if (!is_dir($major_path)) {
			    	mkdir($major_path, 0775);
				}
				
				
				
				$uploadedphoto = $uploadfileDB->getFile($transaction_id,33);
				
				if($_FILES["photograph"]['name']){
				
					if($uploadedphoto){
						
						//insert again
						if (is_uploaded_file($_FILES["photograph"]['tmp_name'])){
							
								$rmfileatch = $major_path."/".$uploadedphoto["auf_file_name"];
								//remove uploaded files
								if(is_file($rmfileatch)){
									unlink($rmfileatch);
								}
								
								//delete from table
								$uploadfileDB->deleteData($uploadedphoto["auf_id"]);
							
							$ext_photo = $this->getFileExtension($_FILES["photograph"]["name"]);
							
							if($ext_photo==".jpg" || $ext_photo==".JPG" || $ext_photo==".jpeg" || $ext_photo==".JPEG" || $ext_photo==".gif" || $ext_photo==".GIF" || $ext_photo==".png" || $ext_photo==".PNG"){
								
								$flnamephoto = date('Ymdhs')."_photograph".$ext_photo;
								$path_photograph = $major_path."/".$flnamephoto;
								move_uploaded_file($_FILES["photograph"]['tmp_name'], $path_photograph);
								
								$upd_photo = array(
									'auf_appl_id' => $formData["transaction_id"],
									'auf_file_name' => $flnamephoto, 
									'auf_file_type' => 33, 
									'auf_upload_date' => date("Y-m-d h:i:s"),
									'auf_upload_by' => $transaction_id,
							    	'pathupload' => $path_photograph
								);
								
								$uploadfileDB->addData($upd_photo);
								
							}						
						}
					}else{
						if (is_uploaded_file($_FILES["photograph"]['tmp_name'])){
						
							$ext_photo = $this->getFileExtension($_FILES["photograph"]["name"]);
							
							if($ext_photo==".jpg" || $ext_photo==".JPG" || $ext_photo==".jpeg" || $ext_photo==".JPEG" || $ext_photo==".gif" || $ext_photo==".GIF" || $ext_photo==".png" || $ext_photo==".PNG"){
								
								$flnamephoto = date('Ymdhs')."_photograph".$ext_photo;
								$path_photograph = $major_path."/".$flnamephoto;
								move_uploaded_file($_FILES["photograph"]['tmp_name'], $path_photograph);
								
								$upd_photo = array(
									'auf_appl_id' => $formData["transaction_id"],
									'auf_file_name' => $flnamephoto, 
									'auf_file_type' => 33, 
									'auf_upload_date' => date("Y-m-d h:i:s"),
									'auf_upload_by' => $transaction_id,
								    'pathupload' => $path_photograph
								);
								
								$uploadfileDB->addData($upd_photo);
								
							}						
						}
					}
					
						
				}
				
				
				$uploadednric = $uploadfileDB->getFile($transaction_id,34);
				
				
				if($_FILES["nric"]["name"]){
					
					if($uploadednric){
						
						
						//insert again
						if (is_uploaded_file($_FILES["nric"]['tmp_name'])){
						
								$rmfileatch = $major_path."/".$uploadednric["auf_file_name"];
							
								//remove uploaded files
								if(is_file($rmfileatch)){
									unlink($rmfileatch);
								}
								
								//delete from table
								$uploadfileDB->deleteData($uploadednric["auf_id"]);
								
							
							$ext_nric = $this->getFileExtension($_FILES["nric"]["name"]);
							
							if($ext_nric==".jpg" || $ext_nric==".JPG" || $ext_nric==".jpeg" || $ext_nric==".JPEG" || $ext_nric==".gif" || $ext_nric==".GIF" || $ext_nric==".png" || $ext_nric==".PNG"){
								
								$flnamenric = date('Ymdhs')."_nric".$ext_nric;
								$path_nric = $major_path."/".$flnamenric;
								move_uploaded_file($_FILES["nric"]['tmp_name'], $path_nric);
								
								$upd_nric = array(
									'auf_appl_id' => $formData["transaction_id"],
									'auf_file_name' => $flnamenric, 
									'auf_file_type' => 34, 
									'auf_upload_date' => date("Y-m-d h:i:s"),
									'auf_upload_by' => $transaction_id,
								     'pathupload' => $path_nric
								);
								
								$uploadfileDB->addData($upd_nric);
							}
						}
					}else{
					 
						
						if (is_uploaded_file($_FILES["nric"]['tmp_name'])){
						
							$ext_nric = $this->getFileExtension($_FILES["nric"]["name"]);
							
							if($ext_nric==".jpg" || $ext_nric==".JPG" || $ext_nric==".jpeg" || $ext_nric==".JPEG" || $ext_nric==".gif" || $ext_nric==".GIF" || $ext_nric==".png" || $ext_nric==".PNG"){
								
								$flnamenric = date('Ymdhs')."_nric".$ext_nric;
								$path_nric = $major_path."/".$flnamenric;
								move_uploaded_file($_FILES["nric"]['tmp_name'], $path_nric);
								
								$upd_nric = array(
									'auf_appl_id' => $formData["transaction_id"],
									'auf_file_name' => $flnamenric, 
									'auf_file_type' => 34, 
									'auf_upload_date' => date("Y-m-d h:i:s"),
									'auf_upload_by' => $transaction_id,
								  'pathupload' => $path_nric
								);
								
								$uploadfileDB->addData($upd_nric);
							}
						}
					}
				}
				
				
				$uploadedacademic = $uploadfileDB->getFileArray($transaction_id,36);
				
				if($uploadedacademic){
					
					$lastfileacademic = count($uploadedacademic);
					
					$new_a = $lastfileacademic+1;
					$totalacademic = $lastfileacademic+$iteration_academic;
					
					for ($a=1; $a<=$iteration_academic; $a++){
						
						if (is_uploaded_file($_FILES["academic_award".$a]['tmp_name'])){
							$ext_academic = $this->getFileExtension($_FILES["academic_award".$a]["name"]);
							
							if($ext_academic==".jpg" || $ext_academic==".JPG" || $ext_academic==".jpeg" || $ext_academic==".JPEG" || $ext_academic==".gif" || $ext_academic==".GIF" || $ext_academic==".png" || $ext_academic==".PNG"){
							
								$flnameacademic = date('Ymdhs')."_academic_".$new_a.$ext_academic;
								$path_academic = $major_path."/".$flnameacademic;
								move_uploaded_file($_FILES["academic_award".$a]['tmp_name'], $path_academic);
								
								$upd_academic = array(
									'auf_appl_id' => $formData["transaction_id"],
									'auf_file_name' => $flnameacademic, 
									'auf_file_type' => 36, 
									'auf_upload_date' => date("Y-m-d h:i:s"),
									'auf_upload_by' => $transaction_id,
								  'pathupload' => $path_academic
								);
								
								$uploadfileDB->addData($upd_academic);
							}
						}
						
						$new_a++;
					}
				
				}else{
					for ($a=1; $a<=$iteration_academic; $a++){
//						
						if (is_uploaded_file($_FILES["academic_award".$a]['tmp_name'])){
							$ext_academic = $this->getFileExtension($_FILES["academic_award".$a]["name"]);
							
							if($ext_academic==".jpg" || $ext_academic==".JPG" || $ext_academic==".jpeg" || $ext_academic==".JPEG" || $ext_academic==".gif" || $ext_academic==".GIF" || $ext_academic==".png" || $ext_academic==".PNG"){
							
								$flnameacademic = date('Ymdhs')."_academic_".$a.$ext_academic;
								$path_academic = $major_path."/".$flnameacademic;
								move_uploaded_file($_FILES["academic_award".$a]['tmp_name'], $path_academic);
								
								$upd_academic = array(
									'auf_appl_id' => $formData["transaction_id"],
									'auf_file_name' => $flnameacademic, 
									'auf_file_type' => 36, 
									'auf_upload_date' => date("Y-m-d h:i:s"),
									'auf_upload_by' => $transaction_id, 
									'pathupload' => $path_academic								
								);
								
								$uploadfileDB->addData($upd_academic);
							}
						}
					}
				}
				
				$uploadedtranscript = $uploadfileDB->getFileArray($transaction_id,37);
				
				if($uploadedtranscript){
					
					$lastfiletranscript = count($uploadedtranscript);
					
					$new_b = $lastfiletranscript+1;
					$totaltranscript = $lastfiletranscript+$iteration_transcript;
					
					for ($b=1; $b<=$iteration_transcript; $b++){
						
						if (is_uploaded_file($_FILES["academic_transcript".$b]['tmp_name'])){
							$ext_transcript = $this->getFileExtension($_FILES["academic_transcript".$b]["name"]);
							if($ext_transcript==".jpg" || $ext_transcript==".JPG" || $ext_transcript==".jpeg" || $ext_transcript==".JPEG" || $ext_transcript==".gif" || $ext_transcript==".GIF" || $ext_transcript==".png" || $ext_transcript==".PNG"){
							
								$flnametranscript = date('Ymdhs')."_transcript_".$new_b.$ext_transcript;
								$path_transcript = $major_path."/".$flnametranscript;
								move_uploaded_file($_FILES["academic_transcript".$b]['tmp_name'], $path_transcript);
								
								$upd_transcript = array(
									'auf_appl_id' => $formData["transaction_id"],
									'auf_file_name' => $flnametranscript, 
									'auf_file_type' => 37, 
									'auf_upload_date' => date("Y-m-d h:i:s"),
									'auf_upload_by' => $transaction_id,
								 'pathupload' => $path_transcript
								);
								
								$uploadfileDB->addData($upd_transcript);
							}
						}
						
						$new_b++;
					}
				}else{
					for ($b=1; $b<=$iteration_transcript; $b++){
	//						
						if (is_uploaded_file($_FILES["academic_transcript".$b]['tmp_name'])){
							$ext_transcript = $this->getFileExtension($_FILES["academic_transcript".$b]["name"]);
							if($ext_transcript==".jpg" || $ext_transcript==".JPG" || $ext_transcript==".jpeg" || $ext_transcript==".JPEG" || $ext_transcript==".gif" || $ext_transcript==".GIF" || $ext_transcript==".png" || $ext_transcript==".PNG"){
							
								$flnametranscript = date('Ymdhs')."_transcript_".$b.$ext_transcript;
								$path_transcript = $major_path."/".$flnametranscript;
								move_uploaded_file($_FILES["academic_transcript".$b]['tmp_name'], $path_transcript);
								
								$upd_transcript = array(
									'auf_appl_id' => $formData["transaction_id"],
									'auf_file_name' => $flnametranscript, 
									'auf_file_type' => 37, 
									'auf_upload_date' => date("Y-m-d h:i:s"),
									'auf_upload_by' => $transaction_id,
								 'pathupload' => $path_transcript
								);
								
								$uploadfileDB->addData($upd_transcript);
							}
						}
					}
				}
    		}
    		
    		$msg = $this->_getParam('msg', null);
			if($msg){
				$this->view->noticeError = $msg;
			}
    		
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'confirmation'),'default',true));

    	}
	}
	
	
	public function placementTestAction() {
		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));	
		}
		
		//$this->_helper->layout->setLayout('application');
		
		//title
    	$this->view->title = $this->view->translate("placement_test_schedule");
    	
    	$auth = Zend_Auth::getInstance(); 
    	$appl_id = $auth->getIdentity()->appl_id; 
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
    		
    	$form = new App_Form_PlacementTest(array('aphplacementcode'=>$program["aph_placement_code"],'aphfeesprogram'=>$program["aph_fees_program"],
    	'aphfeeslocation'=>$program["aph_fees_location"],'transactionid'=>$transaction_id ,'applid'=>$appl_id));
    	
    	if ($this->getRequest()->isPost()) {    		
    		
    		$formData = $this->getRequest()->getPost();   	
    			    		
			if($form->isValid($formData)){  
	    		//get billing runno bankID & applicant(payeeID)
	    		//$bankpinDB = new App_Model_Application_DbTable_ApplicantPinBank();
	    		//$bank_info = $bankpinDB->getData();
	    		   			
	    		$info["apt_at_trans_id"]=	$transaction_id;
				$info["apt_ptest_code"]	=	$formData["app_ptest_code"];
				$info["apt_aps_id"]		=	$formData["aps_id"]; //appl_placement_location
				$info["apt_fee_amt"]	=	$formData["apt_fee_amt"];
				
				$appptestDB = new App_Model_Application_DbTable_ApplicantPtest();
				
				if($applicant_placement_test_info["apt_id"]){
					$appptestDB->updateData($info,$applicant_placement_test_info["apt_id"]);
				}else{
	    			$appptestDB->addData($info);
				}   
				
				//to update appl_pin_to_bank to bankID has been used "P"
				//$pin["status"]="P"; //dah pakai
				//$bankpinDB->updateData($pin,$bank_info["billing_no"]);
				
				
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme'),'default',true));
				
			}else{
    			$form->populate($formData);
    			$this->view->form = $form;
    		}
    	}else{
    		
    		
	    	if($applicant_placement_test_info){
	    		$form->populate($applicant_placement_test_info);
    		}
	    	$this->view->form = $form;
    	}
	}
	
	/* DEVELOPER INFO
	 * To get Applicant ID
	 * Update
	 * To generate Validasi Bank for (placement test) OR (high school cert)
	 * To send email with attachment
	 * High Scholl : Upadte status to PROCESS
	 * Placement  : Update status to CLOSE
	 *
	 */
	public function confirmationAction(){
		
		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));	
		}
		
		$this->view->title = $this->view->translate("confirmation");
		
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
	
    	$auth = Zend_Auth::getInstance(); 
    	$appl_id = $auth->getIdentity()->appl_id;      	
    	$this->view->appl_id = $appl_id;
    	
    	$appl_prefer_lang = $auth->getIdentity()->appl_prefer_lang;      	
    	$this->view->appl_prefer_lang = $appl_prefer_lang;
    	
    	$transaction_id = $auth->getIdentity()->transaction_id; 
    	$this->view->transaction_id = $transaction_id;
    	
    	
    	
    	
    	//get applicant profile
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getTransProfile($appl_id,$transaction_id);
		    		
		    		
    	//get transaction data
    	$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
    	$transData = $transDB->getTransactionData($transaction_id);
		$this->view->transaction = $transData;
			    	
    	$admission_type = $transData["at_appl_type"];  //1:placement test 2:high school
			    	
    	
    	
    	//------- checking section -------
    		   	
    	$fileDB =new App_Model_Application_DbTable_UploadFile();
    	$photo = $fileDB->getFile($transaction_id,33); //photo
    	$nric = $fileDB->getFile($transaction_id,34); //nric
    	 
    	if($admission_type==2){
    		$raport = $fileDB->getFile($transaction_id,37); //Raport/transcript
		}else{
			$raport="x";
		}   	
    	$transcript = $fileDB->getFile($transaction_id,36); //Medical Report
    	
    	
		if(!$photo){
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'uploaddocument','msg'=>$this->view->translate('please_upload_document.')),'default',true));
    	}
    	//------- end checking section -------
    	
    	
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
    		//get academic year
    	    $ayearDb = new App_Model_Record_DbTable_AcademicYear();
			$academic_year = $ayearDb->getNextAcademicYearData();							
									
			//get current intake
			$intakeDB = new App_Model_Record_DbTable_Intake();
			$intake = $intakeDB->getCurrentIntake();
			$IdIntake = $intake["IdIntake"];
							
			//get period ->utk letak application ni pada period period yg mana berdasarkan application activity calendar
			/*$activityDB = new App_Model_Record_DbTable_ActivityCalender();					
			$activity = $activityDB->getPeriodByActivity(29);//online application
			$idPeriod = $activity["IdPeriod"];*/
			
			
			//get period
    		//cek bulan dan tahun
			$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
			$period   = $periodDB->getCurrentPeriod(date("n"),date("Y"));
			$idPeriod = $period["ap_id"];
			
			
    			if($transData["entry_type"]==2){	//manual entry already have no peserta 

					$applicantID = $transData["at_pes_id"];   

					if($transData["at_appl_type"]==1){ //uSM
						
					 //--------get applicant program  -----------
				    	$appprogramDB = new App_Model_Application_DbTable_ApplicantProgram();
				    	$app_program = $appprogramDB->getPlacementProgram($transaction_id);
				    	
				    	$program_data["program_code1"]="0";
				    	$program_data["program_code2"]="0";
				    	
				    	$i=1;						    	
				    	foreach($app_program as $program){
				    		$program_data["program_name".$i] = $program["program_name"];
				    		$program_data["faculty_name".$i] = $program["faculty"];
				    		$program_data["program_code".$i] = $program["program_code"];
				    								    	
				    	$i++;
				    	}	
						
						////to get and update sit no
						$appprogramDB = new App_Model_Application_DbTable_ApplicantProgram();
						
						if($program_data["program_code2"]=="0"){
							$program_data["program_code2"] = $program_data["program_code1"];
						}
						
						
						$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
						$applicant_ptest = $appProfileDB->viewkartu($transaction_id);
						
						$data = $appprogramDB->getProcedure($transaction_id,$program_data["program_code1"],$program_data["program_code2"],$applicant_ptest["apt_aps_id"]);
													
						if($data[0]["roomid"]==0){
							$error="Maaf tempat untuk USM telah penuh";
							echo $error;exit;
						}
						
						//once submmitted update status prcess sebab da bayar masa amik form dari agent
		    			$upddata["at_status"]='PROCESS';
		    			$upddata["at_intake"]=$IdIntake;
		    			$upddata["at_period"]=$idPeriod;
		    			$upddata["at_submit_date"] = date("Y-m-d H:i:s");
		    		
						$transDB->updateData($upddata,$transaction_id);

						$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'viewkartu'),'default',true));
					}
					
				}else{	//online				
					
					//kalau dah ada pes jgn mintak no pes lagi
					//check no pes
					if( !isset($transData["at_pes_id"]) && $transData["at_pes_id"]==null ){
						//to get and update applicantID
						$applicantID = $transDB->getApplicantID($transData["at_appl_type"]);
						$data["at_pes_id"]=$applicantID;
						$transDB->updateData($data, $transData["at_trans_id"]);
					}else{
						$applicantID = $transData["at_pes_id"];
					}
					
				}//end if	
    		
    		
					
		    		//get applicant program applied
		    		$programDB = new App_Model_Application_DbTable_ApplicantProgram();
		    		$app_program = $programDB->getPlacementProgram($transaction_id);	

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
			    	
			    	
			    	
		    	   //-------- placement test section -------------
			       if($admission_type==1){

			       	        //filetype	
							$fileType = 32;
									    			
				    		//template path	 
							$filepath = DOCUMENT_PATH."/template/validasi_bank.docx";   

							//filename
							$output_filename = $applicantID."_validasi_bank.pdf";
							
							$ptestinfo = new App_Model_Application_DbTable_ApplicantPtest();   
		    				$applicant_placement_test_info = $ptestinfo->getScheduleInfo($transaction_id);
		    	
							$appptestDB = new App_Model_Application_DbTable_ApplicantPtest();
							$info["apt_bill_no"]=$applicantID;
							$appptestDB->updateData($info,$applicant_placement_test_info["apt_id"]);
					

					       	//get plcement test date
				    		$scheduleDB = new App_Model_Application_DbTable_PlacementTestSchedule();
				    		$condition = array('schedule_id'=>$applicant['schedule_id']);
				    		$placement = $scheduleDB->getInfo($condition);
				    		
				    		//get applicant parents info
		    				$appFamilyDB = new App_Model_Application_DbTable_ApplicantFamily();
		    				$applicant_family = $appFamilyDB->fetchdata($appl_id,21); //nak cari mother info   
				    		
		    				//once submmitted update status=CLOSE
				    		$upddata["at_status"]='CLOSE';
				    		$upddata["at_intake"]=$IdIntake;
				    		$upddata["at_period"]=$idPeriod;
				    		$upddata["at_submit_date"]=date("Y-m-d H:i:s");
				    		
							$transDB->updateData($upddata,$transaction_id);	
		    		
				    		setlocale(LC_MONETARY, 'id_ID');
				    		
							$fieldValues = array (
							     'billno' => $applicantID,  							    
							     'fee' => money_format('%i', $applicant["fee"]),
							 	 'Applicantname' => $applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
							     'Address1' => $applicant["appl_address1"],
							     'Address2' => $applicant["appl_address2"],
								 'mobilenumber' => $applicant["appl_phone_hp"],
								 'Kodya' => $applicant["appl_address2"], 
								 'dob' => $applicant["appl_dob"],
								 'email' => $applicant["appl_email"],
								 'Mothername' => $applicant_family["af_name"],
								 'Schoolname' => $applicant["discipline"],//jurusan yg dipilih
							     'TestDate' => date('j M Y',strtotime($placement["aps_test_date"])),
							     'Location' => $placement["location_name"],
							     'PROGRAM1' => $program_data["program_name1"],
							     'PROGRAM2' => $program_data["program_name2"]
							);
							
								
			       }					
			       //------- End placement test section ----------

			      
					
				   //-------- high school cert section -------------
			       if($admission_type==2){	
			       			//filetype	
							$fileType = 31;
							
			       	 	    //template path
			       			$filepath = DOCUMENT_PATH."/template/pssb_confirmation_letter.docx"; 
			       			
			       			//filename
							$output_filename = $applicantID."_pssb_confirmation_letter.pdf";	
							
							//pengumumam hasil seleksi						
							setlocale (LC_ALL, $locale); 
							   	
						    //0=sunday onwards
						    $today = date("w");
					    
						       if($today<=2){								 
			    					$selection_date = strftime('%e %B %Y',  strtotime("this Saturday")).', '.strftime('%e %B %Y',  strtotime("second Saturday")).' atau '.strftime('%e %B %Y',  strtotime("third Saturday"));
			    		
								}else{		
										
									$selection_date = strftime('%e %B %Y',  strtotime("second Saturday")).', '.strftime('%e %B %Y',  strtotime("third Saturday")).' atau '.strftime('%e %B %Y',  strtotime("fourth Saturday"));
								}
    	     
    							
							//$seleksi = $activityDB->getNearestActivityDate(30,$idPeriod);//seleksi							
							//$registrasi = $activityDB->getNearestActivityDate(2,$idPeriod);//registrasi						
							//$withdrawal = $activityDB->getNearestActivityDate(31,$idPeriod);//$withdrawal
							
							//once submmitted update status=CLOSE
				    		$upddata["at_status"]='PROCESS';
				    		$upddata["at_intake"]=$IdIntake;
				    		$upddata["at_period"]=$idPeriod;
				    		$upddata["at_submit_date"]=date("Y-m-d H:i:s");
							$transDB->updateData($upddata,$transaction_id);	
							
							if($applicant["appl_gender"]==1) $gender="LAKI-LAKI";
							if($applicant["appl_gender"]==2) $gender="PEREMPUAN";
			
							$fieldValues = array (
				        	    'Applicantname' => $applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],  
				        		'dob' => $applicant["appl_dob"],
				        	    'Sex' => $gender,
				        		'Address' => $applicant["appl_address1"].','.$applicant["appl_address2"],					   	
				        	    'phone' => $applicant["appl_phone_hp"],
				        	    'email' => $applicant["appl_email"],
				        	    'Discipline' => $applicant["discipline"] ,
				        		'PROGRAM1' => $program_data["program_name1"],						     
				        	    'submission_date'=>date('j M Y'),
				        	    'ACADEMICYEAR'=>$academic_year["ay_code"],
							 	'registration_date'=>date('j M Y'),
				        	    'withdrawal_date'=>date('j M Y'),
				        	    'seleksi_date'=>$selection_date
							   // 'registration_date'=>$registrasi["StartDate"].' s.d '.$registrasi["EndDate"],
							   // 'withdrawal_date'=>$withdrawal["StartDate"].' s.d '.$withdrawal["EndDate"]
				        	    
				        	);
					        	
				        $educationDB = new App_Model_Application_DbTable_ApplicantEducation();
				    	$connection = $educationDB->getEducationDetail($transaction_id);
				    	   	
				      
						/*foreach ($a as $v1) {
						    foreach ($v1 as $v2 => $v3) {
						        //  echo "$v2 => $v3";
						         $subject_id= $v1["ss_id"];		       
						         $fieldValues[$v2.'_'.$subject_id]= $v3;
						        // echo '<br>';
						    }		    
						}	*/
			       }					
			       //------- end high school cert section ----------
			       
			     	//----------- empty transaction id in session to avoid back button -------
					$auth = Zend_Auth::getInstance(); 
					$auth->getIdentity()->transaction_id = null;
		
			       
			       // ------- create PDF File section	--------	

			       
					//directory to locate file
					$app_directory_path = DOCUMENT_PATH."/applicant/".date("mY");	

					//create directory to locate file			
					if (!is_dir($app_directory_path)) {
				    	mkdir($app_directory_path, 0775);
					}
				
					$output_directory_path = DOCUMENT_PATH."/applicant/".date("mY")."/".$transaction_id;
					
    				//create directory to locate file			
					if (!is_dir($output_directory_path)) {
				    	mkdir($output_directory_path, 0775);
					}
					
					//$location_path
			        $location_path = "applicant/".date("mY")."/".$transaction_id;	
								
					
					//to create PDF File					
					if($admission_type==2){
						$this->mailmergeConnection($filepath, $fieldValues,$connection,$output_directory_path, $output_filename);
					}else{
						$this->mailmerge($filepath, $fieldValues,$output_directory_path, $output_filename);
					}
										
					
					//save file info
					$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
					$doc["ad_appl_id"]=$transaction_id;
					$doc["ad_type"]=$fileType;
					$doc["ad_filepath"]=$location_path;
					$doc["ad_filename"]=$output_filename;
					$doc["ad_createddt"]=date("Y-m-d");
					$documentDB->addData($doc);		
				   // ------- End PDF File section	--------	
										
					
										
					
					// --------- Send Email Section  ---------------
					$attachment_path = $output_directory_path.'/'.$output_filename;
					
					$templateDB = new App_Model_General_DbTable_EmailTemplate();
					
					if($admission_type==1){	// placement test
					 	$templateData = $templateDB->getData(6,$appl_prefer_lang);
					}
					
    				if($admission_type==2){	//high school
					 	$templateData = $templateDB->getData(4,$appl_prefer_lang);
					}
					
		    		
		    		$email_receipient = $applicant["appl_email"];
		    		$name_receipient  = $applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"];    		
		    		    		
		    		$templateMail = $templateData['body'];				
					$templateMail = str_replace("[Candidate]",$name_receipient,$templateMail);
					
					$templateMail = str_replace("[FIRST_NAME]",$applicant["appl_fname"],$templateMail);
					$templateMail = str_replace("[MIDDLE_NAME]",$applicant["appl_mname"],$templateMail);
					$templateMail = str_replace("[LAST_NAME]",$applicant["appl_lname"],$templateMail);
					
					$emailDb = new App_Model_System_DbTable_Email();		
					$data = array(
						'recepient_email' => $email_receipient,
						'subject' => $templateData["subject"],
						'content' => $templateMail,
						'attachment_path' => $attachment_path,
					    'attachment_filename' => $output_filename
					);	
		
					//to send email with attachment
					$emailDb->addData($data);			
					// --------- End Email Section  ---------------
		    		    		
		    		
										
					
					//notice message					
					//$this->view->download_file = "http://".APP_HOSTNAME."/documents/".$appl_id."/".$output_filename;
		    	   
					$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'notification'),'default',true));
					
    	}//end post data
    		
    		
	}
	
	
	
	
	
	public function notificationAction(){
		
/*		$storage = new Zend_Auth_Storage_Session();
        $storage->clear();*/
        
        $this->view->notice = $this->view->translate('confirmation_notice')."<br />". $this->view->translate('thank_you');
	}
	
	
	public function viewAction(){
		
		$this->view->title = $this->view->translate("application_info");		
		
		$auth = Zend_Auth::getInstance(); 
    	$appl_id = $auth->getIdentity()->appl_id;      	
    	$this->view->appl_id = $appl_id;
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
		    	mkdir($output_directory_path, 0775);
			}

			//to rename output file			
			$output_file_path = $output_directory_path."/".$output_filename;
			
			file_put_contents($output_file_path, $document);
			if($photoFilename){
				$mailMerge->deleteImage($output_directory_path."/".$photoFilename);
			}
	}
	
	
	
	protected function mailmergeConnection($filepath,$fieldValues,$connection,$output_directory_path,$output_filename,$photoFilename=''){
		
		
			$mailMerge = new Zend_Service_LiveDocx_MailMerge();
									
			$mailMerge->setUsername('yatie')
			          ->setPassword('al_hasib');	
			           			 
			          
			$mailMerge->setLocalTemplate($filepath);		   
			
			$mailMerge->assign($fieldValues);			
			$mailMerge->assign('connection', $connection);
			$mailMerge->createDocument();
			 
			$document = $mailMerge->retrieveDocument('pdf');
						
			//create directory to locate file			
			if (!is_dir($output_directory_path)) {
		    	mkdir($output_directory_path, 0775);
			}

			//to rename output file			
			$output_file_path = $output_directory_path."/".$output_filename;
			
			file_put_contents($output_file_path, $document);
			if($photoFilename){
				$mailMerge->deleteImage($output_directory_path."/".$photoFilename);
			}
	}
	
	
	

	public function loginAction() {
		
		$this->_helper->layout->setLayout('application');
		
		//title
    	$this->view->title = $this->view->translate("Application Login");
    	
    	$form = new App_Form_ApplicationLogin();
	   
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
			if ($form->isValid($formData)) {
				
				$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();				
				$result = $appProfileDB->verify($formData["username"],$formData["password"]);
				
				
				if($result){
					$this->_redirect($this->url(array('controller'=>'online-application','action'=>'index')));
				}else{
					$this->view->form = "Sorry, Invalid Username and Password.";
				}
				
			}else{
				$form->populate($formData);
			}
			
    	}
    	
    	$this->view->form = $form;
    	
    	
	}
	
	public function logoutAction()
    {
        $storage = new Zend_Auth_Storage_Session();
        $storage->clear();
        
        $this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'index'),'default',true));
    }
    
    public function forgotPasswordAction(){
    	$this->view->title = $this->view->translate("forgot_password");
    	$this->view->msg = $this->view->translate("forget_password_fill_detail");
    	
    	$form = new App_Form_ForgotPassword();
    	
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		
			if ($form->isValid($formData)) {
				
				//check email & dob
				$dob = $formData['appl_dob']['day']."-".$formData['appl_dob']['month']."-".$formData['appl_dob']['year'];
				
				$applicantProfileDb = new App_Model_Application_DbTable_ApplicantProfile();
				$data = $applicantProfileDb->getForgotPasswordData($formData['appl_email'], $dob);
				
				if($data){
					
					
					$url = $this->view->url(array('action'=>'index'),'default',true);
					
					$displaymsg  =  $this->view->translate('forgot_password_message').":- <br><br>";
					$displaymsg .=  $this->view->translate("username").':'.$data["appl_email"].'<br>';
					$displaymsg .=  $this->view->translate("password").':'.$data['appl_password'].'<br><br>';		
					$displaymsg .=  $this->view->translate('click_here_to').' '."<a href='.$url.'> ".$this->view->translate('sign_in').".</a><br><br>";		
					$displaymsg .=  $this->view->translate('thank_you');
				
				
					$this->view->msg = $displaymsg;				
					$this->view->form = "";
					$this->view->noticeSuccess = $this->view->translate("forget_password_sended_email");
					
					//send email
					//get Email Template based on preferred Language
					$templateDB = new App_Model_General_DbTable_EmailTemplate();
					$templateData = $templateDB->getData(2,$data["appl_prefer_lang"]);
				
					$templateMail = $templateData['body'];				
					$templateMail = str_replace("[Candidate]",$data["appl_fname"],$templateMail);
					$templateMail = str_replace("[EmailApplicant]",$data["appl_email"],$templateMail);
					$templateMail = str_replace("[PassApplicant]",$data['appl_password'],$templateMail);
					$templateMail = str_replace("[FIRST_NAME]",$data["appl_fname"],$templateMail);
					$templateMail = str_replace("[MIDDLE_NAME]",$data["appl_mname"],$templateMail);
					$templateMail = str_replace("[LAST_NAME]",$data["appl_lname"],$templateMail);
							
					$sent = $this->sendMail($data["appl_email"],$data["appl_fname"],$templateData['subject'],$templateMail);
					
				}else{
					$this->view->noticeError = $this->view->translate("forget_password_not_found");
					$form->populate($formData);	
					$this->view->form = $form;
				}
				
			}else{
				$form->populate($formData);
				$this->view->form = $form;
			}
    	}else{
    		$this->view->form = $form;	
    	}
    	
    }

	public function verificationAction(){
		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));	
		}
		
		$this->view->title = $this->view->translate("verify_bank_pin");		
		
		$auth = Zend_Auth::getInstance(); 
    	$appl_id = $auth->getIdentity()->appl_id;      	
    	$this->view->appl_id = $appl_id;
    	
    	$transaction_id = $auth->getIdentity()->transaction_id;      	
    	$this->view->transaction_id = $transaction_id;
    	
    	$transdb = new App_Model_Application_DbTable_ApplicantTransaction();
    	$rstrans = $transdb->getTransactionData($transaction_id);
    	

    	//get transaction data
    	$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
    	$transData = $transDB->getTransactionData($transaction_id);
			    	
    	$admission_type = $transData["at_appl_type"];  //1:placement test 2:high school
    	
    	if($admission_type==1){
	    	if($rstrans["at_status"]=="PROCESS"){
	    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'viewkartu'),'default',true));
	    	}
		}else{
			if($rstrans["at_status"]=="PROCESS"){
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'viewletter'),'default',true));
			}
		}
    	
    	//echo $auth->getIdentity()->role;       	
    	      
    	$form = new App_Form_Loginpin();     	
    	
    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			if ($form->isValid($formData)) {				
					
			
					$billing_no = $formData["billingno"];
					$pin_no = $formData["pinno"];
					
					$profileDB = new App_Model_Application_DbTable_ApplicantProfile();
					$applicant = $profileDB->verify($transaction_id,$billing_no,$pin_no);
					$this->view->applicant = $applicant;
					
					
					if($applicant){
						
						//to get and update applicantID
						//$applicantID = $transdb->getApplicantID($rstrans["at_appl_type"]);
						//$data["at_pes_id"]=$applicantID;
						//$transdb->updateData($data, $rstrans["at_trans_id"]);
				    	
						
						
				 	    //--------get applicant program  -----------
				    	$appprogramDB = new App_Model_Application_DbTable_ApplicantProgram();
				    	$app_program = $appprogramDB->getPlacementProgram($transaction_id);
				    	
				    	$program_data["program_code1"]="0";
				    	$program_data["program_code2"]="0";
				    	
				    	$i=1;						    	
				    	foreach($app_program as $program){
				    		$program_data["program_name".$i] = $program["program_name"];
				    		$program_data["faculty_name".$i] = $program["faculty"];
				    		$program_data["program_code".$i] = $program["program_code"];
				    								    	
				    	$i++;
				    	}					    					    	

				    	//to get and update sit no
						$appprogramDB = new App_Model_Application_DbTable_ApplicantProgram();
						
						if($program_data["program_code2"]=="0"){
							$program_data["program_code2"] = $program_data["program_code1"];
						}
						$data = $appprogramDB->getProcedure($transaction_id,$program_data["program_code1"],$program_data["program_code2"],$applicant["apt_aps_id"]);
													
						if($data[0]["roomid"]==0){
							$error="Maaf tempat untuk USM telah penuh";
							echo $error;exit;
						}
						
						//once submmitted update status=PTOCESS
						$upddata["at_status"]='PROCESS';
		    			$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
						$transDB->updateData($upddata,$transaction_id);	

						
						$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'viewkartu'),'default',true));		    	
					}else{
						$this->view->noticeError = $this->view->translate("invalid_bankid");
						$this->view->form = $form;
					}
			}
    	}else{
    		$this->view->form = $form;
    	}
    	
    	
	}
    
	
	
	public function registerAction() {
		$this->_helper->layout->setLayout('application');
		
		//redirect if already login
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'biodata'),'default',true));
    	}
		
		//title
    	$this->view->title = $this->view->translate("new_registration");
    	
    	$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
	

		$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();
		
    	$form = new App_Form_ApplicationRegistration(array ('lang' => $locale ));
    	$form->removeElement("cancel");
    	
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		
			if ($form->isValid($formData)) {
				
				//check for unique email
				if(!$appProfileDB->uniqueEmail($formData['appl_email'])){
					$form->populate($formData);
					
					$this->view->noticeError = $this->view->translate("msg_duplicate_email");
					
				}else{
				//save into applicant_profile
				if($formData["appl_nationality"]==0){
					$formData["appl_nationality"]=$formData["country_origin"];
				}

				$generate_password = mt_rand(100000,999999);
				//echo printf("[%06s]\n",$generate_password); // zero-padding works on strings too
				
				$info["appl_fname"]=strtoupper($formData["appl_fname"]);
				$info["appl_mname"]=strtoupper($formData["appl_mname"]);
				$info["appl_lname"]=strtoupper($formData["appl_lname"]);
				$info["appl_email"]=$formData["appl_email"];
				$info["appl_password"]=$generate_password;
				$info["appl_dob"]=$formData["appl_dob"]['day']."-".$formData["appl_dob"]['month']."-".$formData["appl_dob"]['year'];
				//$info["appl_dob"]=$formData["appl_dob-day"];
				$info["appl_prefer_lang"]=$formData["appl_prefer_lang"];
				$info["appl_nationality"]=$formData["appl_nationality"];
				
				
				//insert student profile into applicant_profile
				$applicant_id = $appProfileDB->addData($info);

				/*
				 * Insert in portal
				 */
				//insert applicant id into applicant_transaction
				/*$data["at_appl_id"]=$applicant_id;				
				$data["at_status"]="APPLY";
				$data["at_create_by"]=$applicant_id;
				$data["at_create_date"]=date("Y-m-d H:i:s");				
				
				$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
				$transDB->addData($data);*/
				
				
				//--------send email section----------
				
				//get Email Template based on preferred Language
				$templateDB = new App_Model_General_DbTable_EmailTemplate();
				$templateData = $templateDB->getData(1,$formData["appl_prefer_lang"]);
				
				$templateMail = $templateData['body'];				
				$templateMail = str_replace("[Candidate]",$formData["appl_fname"],$templateMail);
				$templateMail = str_replace("[EmailApplicant]",$formData["appl_email"],$templateMail);
				$templateMail = str_replace("[PassApplicant]",$generate_password,$templateMail);				
				$templateMail = str_replace("[FIRST_NAME]",$formData["appl_fname"],$templateMail);
				$templateMail = str_replace("[MIDDLE_NAME]",$formData["appl_mname"],$templateMail);
				$templateMail = str_replace("[LAST_NAME]",$formData["appl_lname"],$templateMail);
				
				
				$sent = $this->sendMail($formData["appl_email"],$formData["appl_fname"],$templateData['subject'],$templateMail); 
				
				//display nanti alif cantikkan2
				$url = $this->view->url(array('action'=>'index'),'default',true);
				
				$display  =  $this->view->translate('new_account_created_msg').":- <br><br>";
				$display .=  $this->view->translate("username").':'.$formData["appl_email"].'<br>';
				$display .=  $this->view->translate("password").':'.$generate_password.'<br><br>';		
				$display .=  $this->view->translate('click_here_to').' '."<a href='.$url.'> ".$this->view->translate('sign_in')."</a><br><br>";		
				$display .=  $this->view->translate('thank_you');
				
				$this->view->notice = $display;
				
				}
				
			}else{
				$form->populate($formData);
			}
    	}
    	
    	//placement test program list
		$placementTestProgramDb = new App_Model_Application_DbTable_PlacementTestProgram();
		$placementTestProgramList = $placementTestProgramDb->getPlacementtestProgramData('PT00001');
		$this->view->programList = $placementTestProgramList;
		
		//annoucement
    	$ann = new Application_Model_DbTable_Announcement();
    	$rsanntitle=$ann->fetch(2);
    	$rsannmesg=$ann->fetch(1);
    	$this->view->rsanntitle=$rsanntitle;
    	$this->view->rsannmesg=$rsannmesg;
    	$this->view->form = $form;
	}
	
	
 	public function sendMail($recipientemail,$fullname,$subject,$templateMail){
      	
      	    $emailDb = new App_Model_System_DbTable_Email();		
			$data = array(
				'recepient_email' => $recipientemail,
				'subject' => $subject,
				'content' => $templateMail,
			    'attachment_filename' => '',
			    'attachment_path' => ''
			     
			);			
			return $emailDb->addData($data);		
      }
      
      
	public function sendAttachmentMail($recipientemail,$fullname,$subject,$templateMail){
      	
		    $fileContents = file_get_contents('documents/');
		   
		    
      	    //require_once 'Zend/Mail.php';
			require_once 'Zend/Mail/Transport/Smtp.php';
			
			$config = array( 'auth' => 'login',
                             'username' => 'cik.yatie@gmail.com',
                             'password' => 'issabelz81',
                             'ssl' => 'ssl',
                             'port' => 465);		
					
          	
			$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com',$config);
            Zend_Mail::setDefaultTransport($transport);  								

            $myView = new Zend_View();
			$myView->addScriptPath(APPLICATION_PATH . '/views/scripts/online-application/');
			$html_body = $myView->render('email.phtml');
            
            $mail = new Zend_Mail();
            $mail->setType(Zend_Mime::MULTIPART_RELATED);
			$mail->setFrom('TRISAKTI Administrator');			
			$mail->addTo('cik.yatie@gmail.com',$fullname); 
			$mail->setSubject($subject);
			$mail->setBodyHtml($html_body);	
			$file = $mail->createAttachment($fileContents);
		    $file->filename = "placement_test_notification.docx";	
			$mail->send(); 
			
			//Send
			 $sent = true;
			 try {
			  $mail->send();
			 }
			 catch (Exception $e) {
			  $sent = false;
			 }
			
			 return $sent;
		
      }
      
	public function ajaxGetStateAction($id=null){

	 	$storage = new Zend_Auth_Storage_Session ();
		$data = $storage->read ();
		if (! $data) {
			$this->_redirect ( 'index/index' );
		}
			
    	$country_id = $this->_getParam('country_id', 0);
     
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
        //get course
    	$stateDB = new App_Model_General_DbTable_State();
        $state_list = $stateDB->getState($country_id);    	
    	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($state_list);
		$this->view->json = $json;

    }
    
	public function ajaxGetCountryAction($id=null){

	 	$storage = new Zend_Auth_Storage_Session ();
		$data = $storage->read ();
		if (! $data) {
			$this->_redirect ( 'index/index' );
		}
			
    	$country_id = $this->_getParam('country_id', 0);
     
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
        //get course
    	$countryDB = new App_Model_General_DbTable_Country();
        $country_list = $countryDB->getData();    	
    	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($country_list);
		$this->view->json = $json;

    }
    
	public function ajaxGetLocationAction($id=null){

	 	$storage = new Zend_Auth_Storage_Session ();
		$data = $storage->read ();
		if (! $data) {
			$this->_redirect ( 'index/index' );
		}
			
    	$select_date = $this->_getParam('select_date', 0);
     
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
        $applicantPlacementScheduleDB = new App_Model_Application_DbTable_ApplicantPlacementSchedule();
    	$location_list = $applicantPlacementScheduleDB->getLocationByDate($select_date);
    	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($location_list);
		$this->view->json = $json;

    }
    
    
	public function ajaxGetFeesAction($id=null){

	 	$storage = new Zend_Auth_Storage_Session ();
		$data = $storage->read ();
		if (! $data) {
			$this->_redirect ( 'index/index' );
		}
			
		$transaction_id = $this->_getParam('transaction_id', 0);
    	$code = $this->_getParam('code', 0);
    	$program = $this->_getParam('program', 0);
    	$location = $this->_getParam('location', 0);
    	$lid = $this->_getParam('lid', 0);//location id
    	
    	$program_fee=0;
    	$location_fee=0;
    	
     
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
		//get Fees by Program
    	if($program==1){
    		
    		//1st:check how many program apply.    		
    		$ptestDB = new App_Model_Application_DbTable_ApplicantProgram();	
    		$list_program = $ptestDB->getPlacementProgram($transaction_id);
    		$total_program_apply = count($list_program);
    		
    		$feeDB = new App_Model_Application_DbTable_PlacementFeeSetup();
    		$condition = array('type'=>'PROGRAM','value'=>$total_program_apply);
    		$fees_info = $feeDB->getFees($condition);
    		$program_fee = $fees_info["apfs_amt"];
    	}
    	
		//get Fees by location
    	if($location==1){
    		
    		//1st:check where is the location.    		
    		$location_id = $lid;
    		
    		$feeDB = new App_Model_Application_DbTable_PlacementFeeSetup();
    		$condition = array('type'=>'LOCATION','value'=>$location_id);
    		$fees_info = $feeDB->getFees($condition);
    		$location_fee = $fees_info["apfs_amt"];
    	}
    	
    	$total_fees = abs($program_fee)+abs($location_fee);
    	
    	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($total_fees);
		$this->view->json = $json;

    }
    
    
	public function ajaxGetCityAction($id=null){

	 	$storage = new Zend_Auth_Storage_Session ();
		$data = $storage->read ();
		if (! $data) {
			$this->_redirect ( 'index/index' );
		}
			
    	$state_id = $this->_getParam('state_id', 0);
    	
     
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
       $cityDb = new App_Model_General_DbTable_City();
       $list_city = $cityDb->getCityByState($state_id);
       
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($list_city);
		$this->view->json = $json;

    }
    
    
    
    public function parentAction(){

	 	$this->_helper->layout->setLayout('application');
		
		//title
    	$this->view->title = $this->view->translate("Parent");    	
    
    	$auth = Zend_Auth::getInstance(); 
    	$appl_id = $auth->getIdentity()->appl_id;      	
    	$this->view->appl_id = $appl_id;
    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	
    	$form = new App_Form_ContactInfo();
    	
    	if ($this->getRequest()->isPost()) {    		
    		
    		$formData = $this->getRequest()->getPost();
    		
    			$info["appl_address1"]=$formData["appl_address1"];
				$info["appl_address2"]=$formData["appl_address2"];
				$info["appl_state"]=$formData["appl_state"];
				$info["appl_province"]=$formData["appl_province"];	
				$info["appl_postcode"]=$formData["appl_postcode"];		
				$info["appl_caddress1"]=$formData["appl_caddress1"];
				$info["appl_caddress2"]=$formData["appl_caddress2"];
				$info["appl_cstate"]=$formData["appl_cstate"];
				$info["appl_cprovince"]=$formData["appl_cprovince"];	
				$info["appl_cpostcode"]=$formData["appl_cpostcode"];
				
				$info["appl_phone_hp"]=$formData["appl_phone_hp"];	
				$info["appl_phone_home"]=$formData["appl_phone_home"];
				
    			$appProfileDB->updateData($info, $formData["appl_id"]);
    		
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme','id'=>$appl_id),'default',true));
			
			
    	}else{
	    	$form->populate($applicant);
	    	$this->view->form = $form;
    	}
    	
			
    	

    }
    
    
    
 	public function viewkartuAction(){
 		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));	
		}
		
		$this->view->title = $this->view->translate("view kartu");		
		
		$auth = Zend_Auth::getInstance(); 
    	$appl_id = $auth->getIdentity()->appl_id; 
    	//echo $appl_id;
    	    	
    	$this->view->appl_id = $appl_id;
    	
    	$transaction_id = $auth->getIdentity()->transaction_id;      	
    	$this->view->transaction_id = $transaction_id;   
    	$profileDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant = $profileDB->viewkartu($transaction_id);
		$this->view->applicant = $applicant;
		
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
 
		if($applicant){
			
			//print_r($applicant);exit;
			$billing_no=$applicant["billing_no"];
			$pin_no = $applicant["REGISTER_NO"];
			$applicantID = $applicant["at_pes_id"];
			
			$locadb=new App_Model_Application_DbTable_PlacementTestRoom();
			$room = $locadb->getdata($applicant["apt_room_id"]);			
			
			
			/*$scheddb=new App_Model_Application_DbTable_ApplicantPlacementSchedule();
			$rssched= $scheddb->getinfo($applicant["apt_id"]);
			
			$locadb=new App_Model_Application_DbTable_PlacementTestLocation();
			$rsloca = $locadb->getdata($rssched[0]["aps_location_id"]);*/
			
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
	    	
	    	if(!$document){
    		
    		
    		    //-------- get applicant photo --------
    		    $photo_name='';
    		    $photoDB = new App_Model_Application_DbTable_UploadFile();
    		    $photo = $photoDB->getFile($transaction_id,33); //PHoto
    		    
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
					
					$comp['exam_date_time1']=date('M d Y',strtotime($component["aps_test_date"])).' '.$component["aps_start_time"];											
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
						
						$comp['exam_date_time2']=date('M d Y',strtotime($component["aps_test_date"])).' '.$component2["aps_start_time"];											
	    				$comp['location_venue_sitno2'] = $room["av_room_name"]." - ".$applicant["apt_sit_no"];
						//$x++;
	    			}	
		    	}	
		    	//print_r($comp);exit;		
				$fieldValues = array (
				     'pinnumber' => $applicantID, 
				 	 'Applicantname' => $applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
				     'Address1' => $applicant["appl_address1"],
				     'Address2' => $applicant["appl_address2"],
					 'Mobilenumber' => $applicant["appl_phone_hp"],
					 'Facultyname1' => $program_data["faculty_name1"], 
				     'Facultyname2' => $program_data["faculty_name2"], 
					 'Programme1' => $program_data["program_name1"],
					 'Programme2' => $program_data["program_name2"],
					 'Comp_Program1' => $comp["comp_program1"],
					 'Exam_Date_Time1' => $comp["exam_date_time1"],
				     'location_venue_sitno1' => $comp['location_venue_sitno1'],
					 'Comp_Program2' => $comp["comp_program2"],
					 'Exam_Date_Time2' => $comp["exam_date_time2"],
				     'location_venue_sitno2' => $comp['location_venue_sitno2'],
				     'username' => $applicant["appl_email"],
				     'password' => $applicant["appl_password"]
				);

				
				$monthyearfolder=date("mY");

				// ------ create kartu peserta ujian in PDF	----					    	
		    	$filepath = DOCUMENT_PATH."/template/kartu.docx";   

		    	//create directory to locate file
				//$output_directory_path = DOCUMENT_PATH ."/".$transaction_id;
				
				//directory to locate file
				$app_directory_path = DOCUMENT_PATH."/applicant/".$monthyearfolder;	

				//create directory to locate file			
				if (!is_dir($app_directory_path)) {
				    mkdir($app_directory_path, 0775);
				}
				
				$output_directory_path = DOCUMENT_PATH."/applicant/".$monthyearfolder."/".$transaction_id;
					
    			//create directory to locate file			
				if (!is_dir($output_directory_path)) {
				    mkdir($output_directory_path, 0775);
				}
				
				//kalau file gambar duduk kat folder bulantahun lain
	    		if(!is_file("$output_directory_path/".$photo_name)){
					copy($photodetail,"$output_directory_path/".$photo_name);
				}	
								
				//$location_path
			    $location_path = "applicant/".$monthyearfolder."/".$transaction_id;	
								
				
				//filename
				$output_filename = $applicantID."_kartu.pdf";
									
		    	$this->mailmerge($filepath, $fieldValues, $output_directory_path, $output_filename,$photo_name);
			
		    	//save file info
				$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
				$doc["ad_appl_id"]=$transaction_id;
				$doc["ad_type"]=30; //kartu
				$doc["ad_filepath"]=$location_path;
				$doc["ad_filename"]=$output_filename;
				$doc["ad_createddt"]=date("Y-m-d");
				$documentDB->addData($doc);	
								
				
			
				$this->view->download_file = "http://".APP_HOSTNAME."/documents/applicant/".$monthyearfolder."/".$transaction_id."/".$output_filename;
				
    		}else{
						    		
			    $this->view->download_file = "http://".APP_HOSTNAME."/documents/".$document["ad_filepath"]."/".$document["ad_filename"];
			   
			}	

			
			
			
			//----------SURAT undangan orang tua section------
				$suratDB = new App_Model_Application_DbTable_ApplicantDocument();
	    		$surat = $suratDB->getData($transaction_id,42); //kartu
	    	    //$surat=false;
	    	    
	    		if(!$surat){
	    			
	    			
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
	    			    
						$fieldValuesSurat = array (
						     'ApplicantName' => $applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
						     'ACADEMICYEAR' => $academic_year["ay_code"],
						     'TestDate' => date('j F Y',strtotime($schedule["aps_test_date"])),
						     'Time' => date("g:i a.",strtotime($schedule["aps_start_time"])),
						     'Location' => $location,
						     'verifydate' => date("j F Y")
							
						);
						
						// ------ create kartu peserta ujian in PDF	----					    	
				    	$filepath_surat = DOCUMENT_PATH."/template/undangan.docx";   
		
				    	//create directory to locate file
						//$output_directory_path_surat = DOCUMENT_PATH ."/".$transaction_id;
						
						//directory to locate file
						$app_directory_path_surat = DOCUMENT_PATH."/applicant/".date("mY");	
		
						//create directory to locate file			
						if (!is_dir($app_directory_path_surat)) {
						    mkdir($app_directory_path_surat, 0775);
						}
						
						$output_directory_path_surat = DOCUMENT_PATH."/applicant/".date("mY")."/".$transaction_id;
							
		    			//create directory to locate file			
						if (!is_dir($output_directory_path_surat)) {
						    mkdir($output_directory_path_surat, 0775);
						}
							
						//$location_path
					    $location_path_surat = "applicant/".date("mY")."/".$transaction_id;
						
						//filename
						$output_filename_surat = $applicantID."_undangan.pdf";
											
				    	$this->mailmerge($filepath_surat, $fieldValuesSurat, $output_directory_path_surat, $output_filename_surat);
				
				    	//save file info
						$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
						$doc["ad_appl_id"]=$transaction_id;
						$doc["ad_type"]=42; //surat undangan
						$doc["ad_filepath"]=$location_path_surat;
						$doc["ad_filename"]=$output_filename_surat;
						$doc["ad_createddt"]=date("Y-m-d");
						$documentDB->addData($doc);	
						
						$this->view->download_surat_undangan = "http://".APP_HOSTNAME."/documents/applicant/".date("mY")."/".$transaction_id."/".$output_filename_surat;
	    		}else{
	    			    $this->view->download_surat_undangan = "http://".APP_HOSTNAME."/documents/".$document["ad_filepath"]."/".$surat["ad_filename"];
	    		}
				//-----------------end SURAT undangan orang tua section --------------------
		}
    }
    
   
    
    
    
    public function viewletterAction(){
   		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));	
		}
		
		$this->view->title = $this->view->translate("view_confirmation_letter");		
		
		$auth = Zend_Auth::getInstance(); 
		
    	$appl_id = $auth->getIdentity()->appl_id; 
    	$this->view->appl_id = $appl_id; 
    	   	
    	$transaction_id = $auth->getIdentity()->transaction_id;      	
    	$this->view->transaction_id = $transaction_id;   
    	
    	$this->view->noticeMessage = $this->view->translate("message_application_acceptance");	
    	
    	$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
    	$document = $documentDB->getData($transaction_id,31); //kartu/confirmation letter
    	
	    	
    	$this->view->download_file = "http://".APP_HOSTNAME."/documents/".$document['ad_filepath']."/".$document['ad_filename'];
    	
    }
    
    public function viewDocumentRequirementAction(){
    	$this->_helper->layout->disableLayout();
    	$pid = $this->_getParam('pid', 0);
    	
    	if($pid!=0){
	    	//program data
	    	$db = Zend_Db_Table::getDefaultAdapter();
	    	$select = $db->select()
		                 ->from(array('p'=>'tbl_program'))
		                 ->where('p.idProgram = ?', $pid);
					
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	        
	        //placement test component & weightage
	        $placementTestWeightageDb = new App_Model_Application_DbTable_PlacementTestWeightage();
	        $placementTestDetailDb = new App_Model_Application_DbTable_PlacementTestDetail();
	        $programComponentDb = new App_Model_Application_DbTable_PlacementTestProgramComponent();
	        $placementTestProgramDb = new App_Model_Application_DbTable_PlacementTestProgram();
	        
	        
	        $row['component'] = $programComponentDb->getProgramData($row['IdProgram']);
	        $row['component_program'] = $placementTestProgramDb->getPlacementtestProgramData('PT00001',$row['ProgramCode']);
	        
	        $i=0;
	        foreach ($row['component'] as $comp){
	        	$row['components']['detail'][$i] = $placementTestDetailDb->getPlacementTestComponentData('PT00001', $comp['apps_comp_code']);
	        	
	        	$row['components']['weightage'][$i] = $placementTestWeightageDb->getWeightageData($row['component_program'][0]['app_id'], $row['components']['detail'][$i]['apd_id'] );
	        	$i++;
	        }
	        
	        //placement test schedule
	        $placementTestScheduleDb = new App_Model_Application_DbTable_PlacementTestSchedule();
	        
	        $this->view->placement_test_ScheduleList = $placementTestScheduleDb->getPlacementTestData('PT00001');
	        
	        $this->view->program = $row;
    	}
        
    }
}
?>