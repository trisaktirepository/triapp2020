<?php

class OnlineApplicationController extends Zend_Controller_Action {
	
	public function init(){
		//$this->_helper->layout->setLayout('application');
	}
	
	public function indexAction(){
		//echo $this->_helper->utility->formatdate(date("Y-m-d H:i:s"));
		//echo 'hrer';exit;
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
				//get student data first
				$select=$dbAdapter->select()
					->from('student_profile')
					->where('appl_email=?',$username)
					->where('appl_password=?',$password);
				$row=$dbAdapter->fetchRow($select);
				if ($row) 
					$authAdapter->setTableName('student_profile')
					->setIdentityColumn('appl_email')
					->setCredentialColumn('appl_password');
				else
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
                 	if($data->appl_role==1){
                    	
                   		//$data->role = "student";                    	
                		
                    	$studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
       					$student = $studentRegDB->getData($data->appl_id);
       					
						if(isset($student["IdStudentRegistration"]))
						{
							$data->programcode = $student['ProgramCode'];
							$data->registration_id = $student["IdStudentRegistration"];
							$data->role = "student";
							$this->gstrsessionTRIAPP = new Zend_Session_Namespace('triapp');
							$this->gstrsessionTRIAPP->__set('survey',"0");
							//echo "student";exit;
						}
						else
						{
							$alumni = $studentRegDB->getDataAlumni($data->appl_id);
							
							if(isset($alumni["IdStudentRegistration"]))
							{
								 
								$data->registration_id = $alumni["IdStudentRegistration"];
								$data->role = "alumni";
							}
							else
							{
							 
								$data->role = "guest";
							}
						}	
       					

       					/*print_r($data);
       					
       					echo '<br>---';
       					echo $data->appl_role;
       					exit; */
       					
                    }else{
                    	$data->role = "applicant";
                    }
                   
                    
                    
                    $auth->getStorage()->write($data);
                    
                   
                    
                 	//disabled temporary 
                    if($data->appl_role==1){                 	 	
                     	if($data->role == "alumni")
							$this->_redirect($this->view->url(array('module'=>'default','controller'=>'alumni-portal', 'action'=>'biodata'),'default',true));
                     	else
                     		//$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'dispatcher','id'=>$auth->getIdentity()->registration_id,'type'=>'student'),'default',true));
							$this->_redirect($this->view->url(array('module'=>'default','controller'=>'student-portal', 'action'=>'home'),'default',true));
                     }else{
                    	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
                     }
                    $this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
                    
                } else {
                    
                    //cek for parent
                	//process form
                	$dbAdapter = Zend_Db_Table::getDefaultAdapter();
                	$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
                	
                	$authAdapter->setTableName('applicant_family')
                	->setIdentityColumn('af_email')
                	->setCredentialColumn('af_password');
                	
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
                		//get data applicant
                		
                		$dbapplicant=new App_Model_Application_DbTable_ApplicantProfile();
                		$datauser=$dbapplicant->getData($data->af_appl_id);
                	
                		/*
                		 * transaction move to applicant portal
                		*/
                		//echo $data['appl_role'];exit;
                		$data->appl_fname=$datauser['appl_fname'];
                		$data->appl_mname=$datauser['appl_mname'];
                		$data->appl_lname=$datauser['appl_lname'];
                		$data->appl_id=$datauser['appl_id'];
                		if($datauser['appl_role']==1){
                			 
                			//$data->role = "student";
                	
                			$studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
                			$student = $studentRegDB->getData($datauser['appl_id']);
                	
                			if(isset($student["IdStudentRegistration"]))
                			{
                				$data->registration_id = $student["IdStudentRegistration"];
                				$data->role = "parent";
                				$data->programcode = $student['ProgramCode'];
                				$this->gstrsessionTRIAPP = new Zend_Session_Namespace('triapp');
                				$this->gstrsessionTRIAPP->__set('survey',"0");
                				
                				//echo "student";exit;
                			}
                			else
                			{
                				$alumni = $studentRegDB->getDataAlumni($datauser['appl_id']);
                					
                				if(isset($alumni["IdStudentRegistration"]))
                				{
                	
                					$data->registration_id = $alumni["IdStudentRegistration"];
                					$data->role = "alumni";
                				}
                				else
                				{
                	
                					$data->role = "guest";
                				}
                			}
                	
                	
                			/*print_r($data);
                	
                			echo '<br>---';
                			echo $data->appl_role;
                			exit; */
                	
                		}else{
                			$data->role = "applicant";
                		}
                		 
                		 
                	
                	
                		$auth->getStorage()->write($data);
                	
                	 
                		//disabled temporary
                		if($data->appl_role==1){
                			if($data->role == "alumni")
                				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'alumni-portal', 'action'=>'biodata'),'default',true));
                			else
                				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'survey', 'action'=>'dispatcher','id'=>$auth->getIdentity()->registration_id,'type'=>'student'),'default',true));
                			//$this->_redirect($this->view->url(array('module'=>'default','controller'=>'student-portal', 'action'=>'home'),'default',true));
                		}else{
                			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
                		}
                		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
                	
                	} else {
                    $this->view->noticeError = 'Login failed. Either username or password is incorrect';
                	}
                }
				
			} else{
				$form->populate($formData);
				$this->view->form = $form;
			}
			
    	}
    	
    	$this->view->form = $form;
	}
	
	public function adminAction(){
		$this->view->title = $this->view->translate("Admin : Login as student/applicant");
		$form = new App_Form_AdminLogin();
		$data["stusername"]=$this->_getParam('key', null);
		$form->populate($data);
/*		echo $this->encrypt_decrypt($data["stusername"],false);
		exit;*/
		$this->view->form = $form;
		if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    			
			if ($form->isValid($formData)) {	
				$adb = new App_Model_General_DbTable_Admin();	
				$admin = $adb->adminlogin($formData["username"],$formData["password"]);
				if(is_array($admin)){
					$formData["username"] = $this->encrypt_decrypt($data["stusername"],false);
					//print_r($formData);
					Zend_Loader::loadClass('Zend_Filter_StripTags');
		            $filter = new Zend_Filter_StripTags();
		            $username = $filter->filter($formData["username"]);
		            $password = $filter->filter($formData["username"]);
		            
		            
					//process form 
					$dbAdapter = Zend_Db_Table::getDefaultAdapter();
					$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
					
					$authAdapter->setTableName('applicant_profile')
					    		->setIdentityColumn('appl_id')
					    		->setCredentialColumn('appl_id');
					    		
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
	                    $data->adminid = $admin["iduser"];
	                    $data->adminlogin = $admin["loginName"];
	                    /*
	                     * transaction move to applicant portal
	                     */
	                 	if($data->appl_role==1){
	                    	
	                   		$data->role = "student";                    	
	                		
	                    	$studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
	       					$student = $studentRegDB->getData($data->appl_id);
	       					$data->registration_id = $student["IdStudentRegistration"];
	
	       					$data->role = "student";
	
	       					
	                    }else{
	                    	$data->role = "applicant";
	                    }
	                    
	                   	                    
	                    
	                    $auth->getStorage()->write($data);
	                    
	                    if($data->appl_role==1){                 	 	
	                     	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'student-portal', 'action'=>'home'),'default',true));
	                     }else{
	                    	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
	                     }
	                    $this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
	                    
	                } else {
	                    // failure: clear database row from session
	                    $this->view->noticeError = 'Login failed. Either username or password is incorrect';
	                }
									
				}
			}    
		}	
	}
	public function biodataAction() {
		
		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			if($auth->getIdentity()->role=='agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}	
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
					$info["appl_no_of_child"]=$formData["appl_no_of_child"];	
				}
							
				
				$appProfileDB->updateData($info, $formData["appl_id"]);
				
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
				$father["af_family_condition"]=$formData["condition"];
				$father["af_education_level"]=$formData["fedulevel"];
							
				
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
	
				//15/12/2012
				//update application_transaction
				//$applicationTransactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
				//$applicationTransactionDb->updateData(array('at_appl_type'=>$formData["appl_admission_type"]), $auth->getIdentity()->transaction_id);
				
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'contactinfo'),'default',true));
    		
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
    		}
    		*/
			$this->view->data = $applicant;

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
			if($auth->getIdentity()->role=='agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}	
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
			if($auth->getIdentity()->role=='agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}	
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
	    			//$applicantProgram = new App_Model_Application_DbTable_ApplicantProgram();
		    		//$applicantProgram->deleteTransactionData($auth->getIdentity()->transaction_id);
	    			//cek bulan dan tahun
					$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
					$period   = $periodDB->getCurrentPeriod(date("m"),date("Y"),null);
					$idPeriod = $period["ap_id"];
					 
	    			$info["at_appl_type"]=$formData["at_appl_type"];
	    			$info['at_period']=$idPeriod;
	    			$info['at_academic_year']=0;
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
			if($auth->getIdentity()->role=='agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}	
		}
		    	
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
    	$applicationEducationData = $applicationEducationDb->getDataSchool($applicant['appl_id'],$transaction_id);
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
			
		}
        elseif($transaction['at_appl_type'] == 3){
            /*CREDIT TRANSFER*/
            $this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-credittransfer'),'default',true));
        }
        elseif($transaction['at_appl_type'] == 4){
        	/*INVITATION*/
        	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-invitation'),'default',true));
        } elseif($transaction['at_appl_type'] == 5){
        	/*PORTFOLIO*/
        	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-portfolio'),'default',true));
        } elseif($transaction['at_appl_type'] == 6){
        	/*SCHOLARSHIP*/
        	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-scholar'),'default',true));
        } elseif($transaction['at_appl_type'] == 7){
        	/*Nilai UTBK*/
        	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-utbk'),'default',true));
        }
        else{//admission type = placement test (id=1)
			//check ptest
	    	$ptestDb = new App_Model_Application_DbTable_ApplicantPtest();
	    	$ptestData = $ptestDb->getPtest($transaction_id);
	    	$code=$ptestData['apt_ptest_code'];
	    	$dbPtsHead=new App_Model_Application_DbTable_PlacementTest();
	    	$ptest=$dbPtsHead->getDataByCode($code);
	    	if($transaction["at_appl_type"]==1){//placement test
		    	if(!$ptestData){
		    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'placement-test','msg'=>$this->view->translate('please_enter_placement_test_info.')),'default',true));
		    	}
			}
		
    		$form = new App_Form_Programme(array('ae_appl_id'=>$applicant['appl_id'],'admissiontype'=>1,'testcode'=>$ptest['level_kkni']));
    		
	    	if ($this->getRequest()->isPost()) {
	    		$formData = $this->getRequest()->getPost();

	    		if($form->isValid($formData)){
	    			$intake=$formData['intake_id'];
	    			$dbIntake=new App_Model_Record_DbTable_IntakeAcademicYear();
	    			$yearacad=$dbIntake->getDataByIntake($intake);
	    			if ($yearacad)
	    				$yearacad=$yearacad['acad_year'];
	    			else $yearacad=0;
	    			
	    			$placementDB = new App_Model_Record_DbTable_PlacementHead();
	    			$applicantTransactionDn->updateData(array('at_academic_year'=>$yearacad), $transaction_id);
	    				
	    			/** EDUCATION **/
	    				$educationDb = new App_Model_Application_DbTable_ApplicantEducation();
	    				
	    				//delete education on current transaction
						$educationDb->delete('ae_appl_id = '.$auth->getIdentity()->appl_id . ' and ae_transaction_id = '.$transaction_id);
						
						if (isset($formData['ae_discpline'])) $aedicipline=$formData['ae_discpline']; else $aedicipline=0;
						
						//add education
						$data = array(
							'ae_appl_id' => $auth->getIdentity()->appl_id,
							'ae_transaction_id' => $transaction_id,
							'ae_institution' => $formData['ae_institution'],
							'ae_discipline_code' => $aedicipline,
							'ae_year_from' => date('y-m-d', strtotime($formData['ae_year_from'])),
							'ae_year_end' => date('y-m-d', strtotime($formData['ae_year_end'])),
							'ae_award'=>$formData['ae_award']
						); 
						
						$aed_id = $educationDb->addData($data);
	    			/** END EDUCATION **/
	    			
	    			/*** PREFERED PRGORRAMME ***/
	    			$applicantProgram = new App_Model_Application_DbTable_ApplicantProgram();
	    			
	    			//delete ptest
	    			$applicantProgram->deleteTransactionData($transaction['at_trans_id']);
	    			
	    			//getPTestProgram Data
	    			$ptestProgram = new App_Model_Application_DbTable_PlacementTestProgram();
	    			//get Idbranch
	    			
	    			$dbbranch=new App_Model_General_DbTable_Branchofficevenue();
	    			$branch=$dbbranch->getData($formData['group1']);
	    			//add ptest program prefered 1
	    			$ptestData = $ptestProgram->getData($formData['app_id']);
	    			$data1 = array(
	    				'ap_at_trans_id' =>$transaction['at_trans_id'],
	    				'ap_prog_code' => $ptestData['app_program_code'],
	    				'ap_ptest_prog_id' => $ptestData['app_id'],
	    				'ap_preference' =>1,
	    				'IdProgramBranch'=>$formData['group1'],
	    				'IdBranch'=>$branch['IdBranch']
	    			);
	    			$row=$applicantProgram->IsIn($transaction['at_trans_id'], '1');
		    			if (!$row)
		    				$applicantProgram->insert($data1);
		    			else 
		    				$applicantProgram->updateData($data1, $row['ap_id']);
		    		
		    		//$dbPeriod=new App_Model_DbTable_Appl
		    		$dbTransaction=new App_Model_Application_DbTable_ApplicantTransaction();
		    		$dbTransaction->updateData(array('at_intake'=>$intake,'at_academic_year'=>$yearacad), $transaction_id);
		    		
		    		 
	    			$dbbranch=new App_Model_General_DbTable_Branchofficevenue();
	    			
	    			///add ptest program prefered 2
	    			if (isset($formData['app_id2']) && $formData['app_id2']!=null){
	    				$branch=$dbbranch->getData($formData['group2']);
	    				$ptestData2 = $ptestProgram->getData($formData['app_id2']);
		    			$data2 = array(
		    				'ap_at_trans_id' =>$transaction['at_trans_id'],
		    				'ap_prog_code' => $ptestData2['app_program_code'],
		    				'ap_ptest_prog_id' => $ptestData2['app_id'],
		    				'ap_preference' =>2,
		    				'IdProgramBranch'=>$formData['group2'],
		    				'IdBranch'=>$branch['IdBranch']
		    			);
		    			$row=$applicantProgram->IsIn($transaction['at_trans_id'], '2');
		    			if (!$row)
		    				$applicantProgram->insert($data2);
		    			else 
		    				$applicantProgram->updateData($data2, $row['ap_id']);
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
	    			
	    			$this->view->programe_selected =  $dataPreferedProgram;
	    			
	    			$dataToPopulate['app_id'] = $dataPreferedProgram[0]['ap_ptest_prog_id'];
	    			if (isset($dataPreferedProgram[1]))
	    				$dataToPopulate['app_id2']= $dataPreferedProgram[1]['ap_ptest_prog_id'];
	    		}
	    		
	    		
	    		if($applicationEducationData){
	    			
	    			$this->view->education_selected =  $applicationEducationData;
	    			
	    			$dataToPopulate['ae_institution'] = $applicationEducationData['ae_institution'];
	    			$dataToPopulate['ae_year_from'] =  date('F Y', strtotime($applicationEducationData['ae_year_from']));
	    			$dataToPopulate['ae_year_end'] =  date('F Y', strtotime($applicationEducationData['ae_year_end']));
	    			$dataToPopulate['ae_discpline'] =  $applicationEducationData['ae_discipline_code'];
	    			
	    			//get school master data
	    			$schoolMasterDb = new App_Model_General_DbTable_SchoolMaster();
	    			$schoolData = $schoolMasterDb->getData($dataToPopulate['ae_institution']);
	    			if (isset($schoolData['sm_type'])) {
	    				$dataToPopulate['type'] = $schoolData['sm_type'];
	    				$dataToPopulate['state'] = $schoolData['sm_state'];
	    			}
	    		}
	    		
	    		//$form->populate($dataToPopulate);
	    		
		    	$this->view->form = $form;	
	    	}
	    	
	    	$this->view->form = $form;
		}
	}
	
	public function programmePortfolioAction() {
		/*
		 * check session for transaction
		*/
		$auth = Zend_Auth::getInstance();
		if($auth->getIdentity()->transaction_id==null){
			if($auth->getIdentity()->role=='agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}
		}
		 
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
		$applicationEducationData = $applicationEducationDb->getDataSchool($applicant['appl_id'],$transaction_id);
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
				
		}
		elseif($transaction['at_appl_type'] == 3){
			/*CREDIT TRANSFER*/
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-credittransfer'),'default',true));
		}
		elseif($transaction['at_appl_type'] == 4){
			/*INVITATION*/
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-invitation'),'default',true));
		} elseif($transaction['at_appl_type'] == 6){
			/*SCholarship*/
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-scholarship'),'default',true));
		} elseif($transaction['at_appl_type'] == 1){
			/*USM*/
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme'),'default',true));
		}
		else{//admission type = placement test (id=1)
			//check ptest
			 
	
			$form = new App_Form_Programme(array('ae_appl_id'=>$applicant['appl_id'],'admissiontype'=>5,null));
	
			if ($this->getRequest()->isPost()) {
				$formData = $this->getRequest()->getPost();
	
				if($form->isValid($formData)){
					$intake=$formData['intake_id'];
					$dbIntake=new App_Model_Record_DbTable_IntakeAcademicYear();
					$yearacad=$dbIntake->getDataByIntake($intake);
					if ($yearacad)
						$yearacad=$yearacad['acad_year'];
					else $yearacad=0;
	
					$placementDB = new App_Model_Record_DbTable_PlacementHead();
					$applicantTransactionDn->updateData(array('at_academic_year'=>$yearacad), $transaction_id);
		    
					/** EDUCATION **/
					$educationDb = new App_Model_Application_DbTable_ApplicantEducation();
		    
					//delete education on current transaction
					$educationDb->delete('ae_appl_id = '.$auth->getIdentity()->appl_id . ' and ae_transaction_id = '.$transaction_id);
	
					if (isset($formData['ae_discpline'])) $aedicipline=$formData['ae_discpline']; else $aedicipline=0;
	
					//add education
					$data = array(
							'ae_appl_id' => $auth->getIdentity()->appl_id,
							'ae_transaction_id' => $transaction_id,
							'ae_institution' => $formData['ae_institution'],
							'ae_discipline_code' => $aedicipline,
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
					//get Idbranch
					$dbbranch=new App_Model_General_DbTable_Branchofficevenue();
					 
					$branch=$dbbranch->getData($formData['group']);
					//add ptest program prefered 1
					$data1 = array(
							'ap_at_trans_id' =>$transaction['at_trans_id'],
							'ap_prog_code' => $formData['app_id'],
							'ap_preference' =>1,
							'IdProgramBranch'=>$formData['group'],
							'IdBranch'=>$branch['IdBranch']
					);
					
					//echo var_dump($formData);exit;
					//checking for selected programme
					if( isset($data1['ap_prog_code']) && $data1['ap_prog_code']!=null && $data1['ap_prog_code']!="" && $data1['ap_prog_code']!=0 ){
						$row=$applicantProgram->IsIn($transaction['at_trans_id'], '1');
						if (!$row)
							$applicantProgram->insert($data1);
						else
							$applicantProgram->updateData($data1, $row['ap_id']);
						 
					}else{
						$this->view->noticeError = $this->translate("Silalah pilih program studi");
					
						$form->populate($formData);
						$this->view->form = $form;
					}
					
					//$dbPeriod=new App_Model_DbTable_Appl
					$dbTransaction=new App_Model_Application_DbTable_ApplicantTransaction();
					$dbTransaction->updateData(array('at_intake'=>$intake,'at_academic_year'=>$yearacad), $transaction_id);
	
			   
					$dbbranch=new App_Model_General_DbTable_Branchofficevenue();
	
					/* ///add ptest program prefered 2
					if (isset($formData['app_id2']) && $formData['app_id2']!=null){
						$branch=$dbbranch->getData($formData['group2']);
						$ptestData2 = $ptestProgram->getData($formData['app_id2']);
						$data2 = array(
								'ap_at_trans_id' =>$transaction['at_trans_id'],
								'ap_prog_code' => $ptestData2['app_program_code'],
								'ap_ptest_prog_id' => $ptestData2['app_id'],
								'ap_preference' =>2,
								'IdProgramBranch'=>$formData['group2'],
								'IdBranch'=>$branch['IdBranch']
						);
						$row=$applicantProgram->IsIn($transaction['at_trans_id'], '2');
						if (!$row)
							$applicantProgram->insert($data2);
						else
							$applicantProgram->updateData($data2, $row['ap_id']);
					}
	 */
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
	
					$this->view->programe_selected =  $dataPreferedProgram;
	
					$dataToPopulate['app_id'] = $dataPreferedProgram[0]['ap_ptest_prog_id'];
					//$dataToPopulate['app_id2']= $dataPreferedProgram[1]['ap_ptest_prog_id'];
				}
		   
		   
				if($applicationEducationData){
	
					$this->view->education_selected =  $applicationEducationData;
	
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
		   
				//$form->populate($dataToPopulate);
		   
				$this->view->form = $form;
			}
	
			$this->view->form = $form;
		}
	}
	
	public function programmeScholarAction() {
		/*
		 * check session for transaction
		*/
		$auth = Zend_Auth::getInstance();
		if($auth->getIdentity()->transaction_id==null){
			if($auth->getIdentity()->role=='agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}
		}
		 
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
		$applicationEducationData = $applicationEducationDb->getDataSchool($applicant['appl_id'],$transaction_id);
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
				
		}
		elseif($transaction['at_appl_type'] == 3){
			/*CREDIT TRANSFER*/
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-credittransfer'),'default',true));
		}
		elseif($transaction['at_appl_type'] == 4){
			/*INVITATION*/
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-invitation'),'default',true));
		} elseif($transaction['at_appl_type'] == 5){
			/*PORTFOLIO*/
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-portfolio'),'default',true));
		} elseif($transaction['at_appl_type'] == 1){
			/*USM*/
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme'),'default',true));
		}
		else{//admission type = placement test (id=1)
			//check ptest
			 
			 
			$form = new App_Form_Programme(array('ae_appl_id'=>$applicant['appl_id'],'admissiontype'=>6,'testcode'=>''));
	
			if ($this->getRequest()->isPost()) {
				$formData = $this->getRequest()->getPost();
	
				if($form->isValid($formData)){
					$intake=$formData['intake_id'];
					$dbIntake=new App_Model_Record_DbTable_IntakeAcademicYear();
					$yearacad=$dbIntake->getDataByIntake($intake);
					if ($yearacad)
						$yearacad=$yearacad['acad_year'];
					else $yearacad=0;
	
					$placementDB = new App_Model_Record_DbTable_PlacementHead();
					$applicantTransactionDn->updateData(array('at_academic_year'=>$yearacad), $transaction_id);
		    
					/** EDUCATION **/
					$educationDb = new App_Model_Application_DbTable_ApplicantEducation();
		    
					//delete education on current transaction
					$educationDb->delete('ae_appl_id = '.$auth->getIdentity()->appl_id . ' and ae_transaction_id = '.$transaction_id);
	
					if (isset($formData['ae_discpline'])) $aedicipline=$formData['ae_discpline']; else $aedicipline=0;
	
					//add education
					$data = array(
							'ae_appl_id' => $auth->getIdentity()->appl_id,
							'ae_transaction_id' => $transaction_id,
							'ae_institution' => $formData['ae_institution'],
							'ae_discipline_code' => $aedicipline,
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
					//get Idbranch
	
					$dbbranch=new App_Model_General_DbTable_Branchofficevenue();
					$branch=$dbbranch->getData($formData['group']);
		    			//add ptest program prefered 1
		    			$data1 = array(
		    				'ap_at_trans_id' =>$transaction['at_trans_id'],
		    				'ap_prog_code' => $formData['app_id'],		    				
		    				'ap_preference' =>1,
		    				'IdProgramBranch'=>$formData['group'],
		    				'IdBranch'=>$branch['IdBranch']
		    			);
		    			
		    			//checking for selected programme
		    			if( isset($data1['ap_prog_code']) && $data1['ap_prog_code']!=null && $data1['ap_prog_code']!="" && $data1['ap_prog_code']!=0 ){
		    				$row=$applicantProgram->IsIn($transaction['at_trans_id'], '1');
		    				if (!$row)
		    					$applicantProgram->insert($data1);
		    				else
		    					$applicantProgram->updateData($data1, $row['ap_id']);
		    				 
		    			}else{
		    				$this->view->noticeError = $this->translate("Silalah pilih program studi");
		    				
		    				$form->populate($formData);
							$this->view->form = $form;
		    			}
						
					//$dbPeriod=new App_Model_DbTable_Appl
					$dbTransaction=new App_Model_Application_DbTable_ApplicantTransaction();
					$dbTransaction->updateData(array('appl_sub_type'=>$formData['scholar'],'at_intake'=>$intake,'at_academic_year'=>$yearacad), $transaction_id);
	
			   
					$dbbranch=new App_Model_General_DbTable_Branchofficevenue();
	
					/* ///add ptest program prefered 2
					if (isset($formData['app_id2']) && $formData['app_id2']!=null){
						$branch=$dbbranch->getData($formData['group2']);
						$ptestData2 = $ptestProgram->getData($formData['app_id2']);
						$data2 = array(
								'ap_at_trans_id' =>$transaction['at_trans_id'],
								'ap_prog_code' => $ptestData2['app_program_code'],
								'ap_ptest_prog_id' => $ptestData2['app_id'],
								'ap_preference' =>2,
								'IdProgramBranch'=>$formData['group2'],
								'IdBranch'=>$branch['IdBranch']
						);
						$row=$applicantProgram->IsIn($transaction['at_trans_id'], '2');
						if (!$row)
							$applicantProgram->insert($data2);
						else
							$applicantProgram->updateData($data2, $row['ap_id']);
					} */
	
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
	
					$this->view->programe_selected =  $dataPreferedProgram;
	
					$dataToPopulate['app_id'] = $dataPreferedProgram[0]['ap_ptest_prog_id'];
					//$dataToPopulate['app_id2']= $dataPreferedProgram[1]['ap_ptest_prog_id'];
				}
		   
		   
				if($applicationEducationData){
	
					$this->view->education_selected =  $applicationEducationData;
	
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
		   
				//$form->populate($dataToPopulate);
		   
				$this->view->form = $form;
			}
	
			$this->view->form = $form;
		}
	}
	 
	
	public function programmeMagisterDoktorAction() {
		/*
		 * check session for transaction
		*/
		$auth = Zend_Auth::getInstance();
		if($auth->getIdentity()->transaction_id==null){
			if($auth->getIdentity()->role=='agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}
		}
		 
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
		$applicationEducationData = $applicationEducationDb->getDataSchool($applicant['appl_id'],$transaction_id);
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
				
		}
		elseif($transaction['at_appl_type'] == 3){
			/*CREDIT TRANSFER*/
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-credittransfer'),'default',true));
		}
		else{//admission type = placement test (id=1)
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
					$intake=$formData['intake_id'];
					$dbIntake=new App_Model_Record_DbTable_IntakeAcademicYear();
					$yearacad=$dbIntake->getDataByIntake($intake);
					if ($yearacad)
						$yearacad=$yearacad['acad_year'];
					else $yearacad=0;
					 
					$placementDB = new App_Model_Record_DbTable_PlacementHead();
					$applicantTransactionDn->updateData(array('at_academic_year'=>$yearacad), $transaction_id);
					 
					/** EDUCATION **/
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
					/** END EDUCATION **/
	
					/*** PREFERED PRGORRAMME ***/
					$applicantProgram = new App_Model_Application_DbTable_ApplicantProgram();
	
					//delete ptest
					$applicantProgram->deleteTransactionData($transaction['at_trans_id']);
	
					//getPTestProgram Data
					$ptestProgram = new App_Model_Application_DbTable_PlacementTestProgram();
					//get Idbranch
	
					$dbbranch=new App_Model_General_DbTable_Branchofficevenue();
					$branch=$dbbranch->getData($formData['group1']);
					//add ptest program prefered 1
					$ptestData = $ptestProgram->getData($formData['app_id']);
					$data1 = array(
							'ap_at_trans_id' =>$transaction['at_trans_id'],
							'ap_prog_code' => $ptestData['app_program_code'],
							'ap_ptest_prog_id' => $ptestData['app_id'],
							'ap_preference' =>1,
							'IdProgramBranch'=>$formData['group1'],
							'IdBranch'=>$branch['IdBranch']
					);
					$row=$applicantProgram->IsIn($transaction['at_trans_id'], '1');
					if (!$row)
						$applicantProgram->insert($data1);
					else
						$applicantProgram->updateData($data1, $row['ap_id']);
	
					$dbTransaction=new App_Model_Application_DbTable_ApplicantTransaction();
					$dbTransaction->updateData(array('at_intake'=>$intake,'at_academic_year'=>$yearacad), $transaction_id);
	
					$dbbranch=new App_Model_General_DbTable_Branchofficevenue();
					$branch=$dbbranch->getData($formData['group2']);
					///add ptest program prefered 2
					if($formData['app_id2']!=null){
						 
						$ptestData2 = $ptestProgram->getData($formData['app_id2']);
						$data2 = array(
								'ap_at_trans_id' =>$transaction['at_trans_id'],
								'ap_prog_code' => $ptestData2['app_program_code'],
								'ap_ptest_prog_id' => $ptestData2['app_id'],
								'ap_preference' =>2,
								'IdProgramBranch'=>$formData['group2'],
								'IdBranch'=>$branch['IdBranch']
						);
						$row=$applicantProgram->IsIn($transaction['at_trans_id'], '2');
						if (!$row)
							$applicantProgram->insert($data2);
						else
							$applicantProgram->updateData($data2, $row['ap_id']);
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
	
					$this->view->programe_selected =  $dataPreferedProgram;
	
					$dataToPopulate['app_id'] = $dataPreferedProgram[0]['ap_ptest_prog_id'];
					$dataToPopulate['app_id2']= $dataPreferedProgram[1]['ap_ptest_prog_id'];
				}
		   
		   
				if($applicationEducationData){
	
					$this->view->education_selected =  $applicationEducationData;
	
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
		   
				//$form->populate($dataToPopulate);
		   
				$this->view->form = $form;
			}
	
			$this->view->form = $form;
		}
	}
	
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
    	
		$msg = $this->_getParam('msg', null);
		if($msg){
			$this->view->noticeError = $msg;	
		}
    	
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
    	$applicationEducationData = $applicationEducationDb->getDataSchool($applicant['appl_id'],$transaction_id);
    	$this->view->educationData = $applicationEducationData;
    	
    	//applicant program preference
    	$applicantProgramDb = new App_Model_Application_DbTable_ApplicantProgram();
    	$applicantProgram = $applicantProgramDb->getPlacementProgram($transaction_id);
    	    	
    	//title
    	$this->view->title = $this->view->translate("Programme");
    	
		//check for admission type
		if( $transaction['at_appl_type'] == 1 ){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme'),'default',true));
			
		}
        elseif ($transaction['at_appl_type'] == 3)
        {
            $this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme'),'default',true));
        } elseif($transaction['at_appl_type'] == 4){
			/*CREDIT TRANSFER*/
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-invitation'),'default',true));
		} elseif($transaction['at_appl_type'] == 5){
        	/*CREDIT TRANSFER*/
        	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-portfolio'),'default',true));
        } elseif($transaction['at_appl_type'] == 6){
        	/*CREDIT TRANSFER*/
        	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-scholar'),'default',true));
        }
        else{//admission type = high school(2)
			$form = new App_Form_Programme(array('ae_appl_id'=>$applicant['appl_id'], 'admissiontype'=>2));

			if ($this->getRequest()->isPost()) {  
				$formData = $this->getRequest()->getPost();
				
				  		
				if ($form->isValid($formData)) {
					
					$intake=$formData['intake_id'];
					$dbIntake=new App_Model_Record_DbTable_IntakeAcademicYear();
					$yearacad=$dbIntake->getDataByIntake($intake);
					if ($yearacad)
						$yearacad=$yearacad['acad_year'];
					else $yearacad=0;
					 
					$placementDB = new App_Model_Record_DbTable_PlacementHead();
					$applicantTransactionDn->updateData(array('at_intake'=>$intake,'at_academic_year'=>$yearacad), $transaction_id);
					 
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
						'ae_year_end' => date('y-m-d', strtotime($formData['ae_year_end'])),
						'ae_award'=>$formData['ae_award']
							
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
		    			$dbbranch=new App_Model_General_DbTable_Branchofficevenue();
		    			$branch=$dbbranch->getData($formData['grouppssb']);
		    			//add ptest program prefered 1
		    			$data1 = array(
		    				'ap_at_trans_id' =>$transaction['at_trans_id'],
		    				'ap_prog_code' => $formData['ap_prog_code'],		    				
		    				'ap_preference' =>1,
		    				'IdProgramBranch'=>$formData['grouppssb'],
		    				'IdBranch'=>$branch['IdBranch']
		    			);
		    			
		    			//checking for selected programme
		    			if( isset($data1['ap_prog_code']) && $data1['ap_prog_code']!=null && $data1['ap_prog_code']!="" && $data1['ap_prog_code']!=0 ){
		    				$row=$applicantProgram->IsIn($transaction['at_trans_id'], '2');
		    				if (!$row)
		    					$applicantProgram->insert($data1);
		    				else
		    					$applicantProgram->updateData($data1, $row['ap_id']);
		    				 
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
	    			
	    			$this->view->education_selected =  $applicationEducationData;
	    			
	    		}
	    		
	    		if($applicantProgram){
	    			$this->view->programe_selected = $applicantProgram;
	    			
	    			$dataToPopulate['ap_prog_code'] = $applicantProgram[0]['ap_prog_code'];
	    			//$dataToPopulate['ap_prog_code2'] = $applicantProgram[1]['ap_prog_code'];
	    		}
		    	
	    		//$form->populate($dataToPopulate);
	    		$this->view->form = $form;
	    	}
	    	
			$this->view->form = $form;
		}
	}	
	
	public function programmeInvitationAction(){
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
		$appl_id = $auth->getIdentity()->appl_id;
		$this->view->appl_id = $appl_id;
		
		$msg = $this->_getParam('msg', null);
		if($msg){
			$this->view->noticeError = $msg;
		}
		//open periode Desember till Januari
		
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
		$applicationEducationData = $applicationEducationDb->getDataSchool($applicant['appl_id'],$transaction_id);
		$this->view->educationData = $applicationEducationData;
		 
		//applicant program preference
		$applicantProgramDb = new App_Model_Application_DbTable_ApplicantProgram();
		$applicantProgram = $applicantProgramDb->getPlacementProgram($transaction_id);
	
		//title
		$this->view->title = $this->view->translate("Programme");
		 
		//check for admission type
		if( $transaction['at_appl_type'] == 1 ){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme'),'default',true));
				
		}
		elseif ($transaction['at_appl_type'] == 3)
		{
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-credittransfer'),'default',true));
		} elseif($transaction['at_appl_type'] == 2){
			/*CREDIT TRANSFER*/
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-highschool'),'default',true));
		} elseif($transaction['at_appl_type'] == 5){
        	/*Portfolio*/
        	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-portfolio'),'default',true));
        } elseif($transaction['at_appl_type'] == 6){
        	/*Scholarship*/
        	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-scholar'),'default',true));
        }
		else{//admission type = invitation
			$form = new App_Form_Programme(array('ae_appl_id'=>$applicant['appl_id'], 'admissiontype'=>4));
	
			if ($this->getRequest()->isPost()) {
				$formData = $this->getRequest()->getPost();
	
				if ($form->isValid($formData)) {
					//echo var_dump($formData);exit;
					$intake=$formData['intake_id'];
					$dbIntake=new App_Model_Record_DbTable_IntakeAcademicYear();
					$yearacad=$dbIntake->getDataByIntake($intake);
					if ($yearacad)
						$yearacad=$yearacad['acad_year'];
					else $yearacad=0;
					
					$placementDB = new App_Model_Record_DbTable_PlacementHead();
					$applicantTransactionDn->updateData(array('at_intake'=>$intake,'at_academic_year'=>$yearacad), $transaction_id);
					 
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
							'ae_year_end' => date('y-m-d', strtotime($formData['ae_year_end'])),
							'ae_award'=>$formData['ae_award']
							
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
						$dbbranch=new App_Model_General_DbTable_Branchofficevenue();
						$branch=$dbbranch->getData($formData['grouppssb']);
						//add ptest program prefered 1
						$data1 = array(
								'ap_at_trans_id' =>$transaction['at_trans_id'],
								'ap_prog_code' => $formData['ap_prog_code'],
								'ap_preference' =>1,
								'IdProgramBranch'=>$formData['grouppssb'],
								'IdBranch'=>$branch['IdBranch']
						);
						 
						//checking for selected programme
						if( isset($data1['ap_prog_code']) && $data1['ap_prog_code']!=null && $data1['ap_prog_code']!="" && $data1['ap_prog_code']!=0 ){
							$row=$applicantProgram->IsIn($transaction['at_trans_id'], '2');
							if (!$row)
								$applicantProgram->insert($data1);
							else
								$applicantProgram->updateData($data1, $row['ap_id']);
							 
						}else{
							$this->view->noticeError = $this->translate("Silalah pilih program studi");
	
							$form->populate($formData);
							$this->view->form = $form;
						}
						//second preferrence
						$branch2=$dbbranch->getData($formData['grouppssb2']);
						//add ptest program prefered 2
						$data2 = array(
								'ap_at_trans_id' =>$transaction['at_trans_id'],
								'ap_prog_code' => $formData['ap_prog_code_2'],
								'ap_preference' =>2,
								'IdProgramBranch'=>$formData['grouppssb2'],
								'IdBranch'=>$branch2['IdBranch']
						);
							
						//checking for selected programme
						if( isset($data2['ap_prog_code']) && $data2['ap_prog_code']!=null && $data2['ap_prog_code']!=""  ){
							$row=$applicantProgram->IsIn($transaction['at_trans_id'], '2');
							if (!$row)
								$applicantProgram->insert($data2);
							else
								$applicantProgram->updateData($data2, $row['ap_id']);
						
						}else{
							$this->view->noticeError = $this->translate("Silalah pilih program studi ke-2");
							//$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'program-invitation'),'default',true));
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
	
					$this->view->education_selected =  $applicationEducationData;
	
				}
		   
				if($applicantProgram){
					$this->view->programe_selected = $applicantProgram;
	
					$dataToPopulate['ap_prog_code'] = $applicantProgram[0]['ap_prog_code'];
					//$dataToPopulate['ap_prog_code2'] = $applicantProgram[1]['ap_prog_code'];
				}
			  
				//$form->populate($dataToPopulate);
				$this->view->form = $form;
			}
	
			$this->view->form = $form;
		}
	}
	
	public function programmeUtbkAction(){
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
		$appl_id = $auth->getIdentity()->appl_id;
		$this->view->appl_id = $appl_id;
	
		$msg = $this->_getParam('msg', null);
		if($msg){
			$this->view->noticeError = $msg;
		}
		//open periode Desember till Januari
		$dbPlacement=new App_Model_Application_DbTable_PlacementTest();
		$placementcode=$dbPlacement->getUTBKCurrent();
		
		if ($placementcode) $placementcode=$placementcode['aph_placement_code'];
		else $placementcode="";
		//echo $placementcode;exit;
		$this->view->placementcode=$placementcode;
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
		$applicationEducationData = $applicationEducationDb->getDataSchool($applicant['appl_id'],$transaction_id);
		$this->view->educationData = $applicationEducationData;
			
		//applicant program preference
		$applicantProgramDb = new App_Model_Application_DbTable_ApplicantProgram();
		$applicantProgram = $applicantProgramDb->getPlacementProgram($transaction_id);
	
		//title
		$this->view->title = $this->view->translate("Programme");
			
		//check for admission type
		if( $transaction['at_appl_type'] == 1 ){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme'),'default',true));
	
		}
		elseif ($transaction['at_appl_type'] == 3)
		{
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-credittransfer'),'default',true));
		}elseif ($transaction['at_appl_type'] == 4)
		{
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-invitation'),'default',true));
		}
		 elseif($transaction['at_appl_type'] == 2){
			/*CREDIT TRANSFER*/
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-highschool'),'default',true));
		} elseif($transaction['at_appl_type'] == 5){
			/*Portfolio*/
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-portfolio'),'default',true));
		} elseif($transaction['at_appl_type'] == 6){
			/*Scholarship*/
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-scholar'),'default',true));
		}
		else{//admission type = Unbk
			$form = new App_Form_Programme(array('ae_appl_id'=>$applicant['appl_id'], 'admissiontype'=>7));
	
			if ($this->getRequest()->isPost()) {
				$formData = $this->getRequest()->getPost();
	
				if ($form->isValid($formData)) {
					//echo var_dump($formData);exit;
					$intake=$formData['intake_id'];
					$dbIntake=new App_Model_Record_DbTable_IntakeAcademicYear();
					$yearacad=$dbIntake->getDataByIntake($intake);
					if ($yearacad)
						$yearacad=$yearacad['acad_year'];
					else $yearacad=0;
						
					$placementDB = new App_Model_Record_DbTable_PlacementHead();
					$applicantTransactionDn->updateData(array('at_intake'=>$intake,'at_academic_year'=>$yearacad), $transaction_id);
	
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
							'ae_year_end' => date('y-m-d', strtotime($formData['ae_year_end'])),
							'ae_award'=>$formData['ae_award']
					);
					$edu=$educationDb->getData($auth->getIdentity()->appl_id, $transaction_id);
					if ($edu) {
						$aed_id=$edu['ae_id'];
						$educationDb->updateData($data, $aed_id);
					}
					else 
						$aed_id = $educationDb->addData($data);
	
					if($aed_id){
						$educationDetailDb = new App_Model_Application_DbTable_ApplicantEducationDetail();
						$dbAppMark=new App_Model_Application_DbTable_ApplicantCompMark();
						//add education detail
						$i=0;
						$dbPlacementDtl=new App_Model_Application_DbTable_PlacementTestDetail();
						
						//echo var_dump($formData['aed_subject_id']); 
						foreach ( $formData['aed_subject_id'] as $subject_id) {
							
							$apldetail=$dbPlacementDtl->getPlacementTestComponentData($placementcode,$subject_id);
							//echo $placementcode;echo $subject_id;
							//echo var_dump($apldetail); exit;
							
							$data = array(
									'apcm_at_trans_id'=>$transaction_id,
									'apcm_apd_id'=>$apldetail['apd_id'],
									'apcm_mark'=>($formData['aed_mark'][$i]!="" && $formData['aed_mark'][$i]!="0" )?$formData['aed_mark'][$i]:null,
									'apcm_status'=>1,
									'apcm_prog_code'=>null,
									'pcode'=>$placementcode
									  
									 );
							if  (!$dbAppMark->getCompMarkByTxnIdCompId($transaction_id, $apldetail['apd_id']))
								$dbAppMark->addData($data);
							$i++;
						}
	
						/*** PREFERED PRGORRAMME ***/
						$applicantProgram = new App_Model_Application_DbTable_ApplicantProgram();
							
						//delete ptest
						$applicantProgram->deleteTransactionData($transaction['at_trans_id']);
						$dbbranch=new App_Model_General_DbTable_Branchofficevenue();
						$branch=$dbbranch->getData($formData['grouppssb']);
						//add ptest program prefered 1
						$data1 = array(
								'ap_at_trans_id' =>$transaction['at_trans_id'],
								'ap_prog_code' => $formData['ap_prog_code'],
								'ap_preference' =>1,
								'IdProgramBranch'=>$formData['grouppssb'],
								'IdBranch'=>$branch['IdBranch']
						);
							
						//checking for selected programme
						if( isset($data1['ap_prog_code']) && $data1['ap_prog_code']!=null && $data1['ap_prog_code']!="" && $data1['ap_prog_code']!=0 ){
							$row=$applicantProgram->IsIn($transaction['at_trans_id'], '1');
							if (!$row)
								$applicantProgram->insert($data1);
							else
								$applicantProgram->updateData($data1, $row['ap_id']);
	
						}else{
							$this->view->noticeError = $this->translate("Silalah pilih program studi");
	
							$form->populate($formData);
							$this->view->form = $form;
						}
						//second preferrence
						$branch2=$dbbranch->getData($formData['grouppssb2']);
						//add ptest program prefered 2
						$data2 = array(
								'ap_at_trans_id' =>$transaction['at_trans_id'],
								'ap_prog_code' => $formData['ap_prog_code_2'],
								'ap_preference' =>2,
								'IdProgramBranch'=>$formData['grouppssb2'],
								'IdBranch'=>$branch2['IdBranch']
						);
							
						//checking for selected programme
						if( isset($data2['ap_prog_code']) && $data2['ap_prog_code']!=null && $data2['ap_prog_code']!=""  ){
							$row=$applicantProgram->IsIn($transaction['at_trans_id'], '2');
							if (!$row)
								$applicantProgram->insert($data2);
							else
								$applicantProgram->updateData($data2, $row['ap_id']);
	
						}else{
							$this->view->noticeError = $this->translate("Silalah pilih program studi ke-2");
							//$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'program-invitation'),'default',true));
							$form->populate($formData);
							$this->view->form = $form;
						}
						//third preferrence
						$branch3=$dbbranch->getData($formData['grouppssb3']);
						//add ptest program prefered 3
						$data3 = array(
								'ap_at_trans_id' =>$transaction['at_trans_id'],
								'ap_prog_code' => $formData['ap_prog_code_3'],
								'ap_preference' =>3,
								'IdProgramBranch'=>$formData['grouppssb3'],
								'IdBranch'=>$branch3['IdBranch']
						);
							
						//checking for selected programme
						if( isset($data3['ap_prog_code']) && $data3['ap_prog_code']!=null && $data3['ap_prog_code']!=""  ){
							$row=$applicantProgram->IsIn($transaction['at_trans_id'], '3');
							if (!$row)
								$applicantProgram->insert($data3);
							else
								$applicantProgram->updateData($data3, $row['ap_id']);
						
						}else{
							$this->view->noticeError = $this->translate("Silalah pilih program studi ke-3");
							//$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'program-invitation'),'default',true));
							$form->populate($formData);
							$this->view->form = $form;
						}
						
						//fourth preferrence
						$branch4=$dbbranch->getData($formData['grouppssb4']);
						//add ptest program prefered 3
						$data4 = array(
								'ap_at_trans_id' =>$transaction['at_trans_id'],
								'ap_prog_code' => $formData['ap_prog_code_4'],
								'ap_preference' =>4,
								'IdProgramBranch'=>$formData['grouppssb4'],
								'IdBranch'=>$branch3['IdBranch']
						);
							
						//checking for selected programme
						if( isset($data4['ap_prog_code']) && $data4['ap_prog_code']!=null && $data4['ap_prog_code']!=""  ){
							$row=$applicantProgram->IsIn($transaction['at_trans_id'], '4');
							if (!$row)
								$applicantProgram->insert($data4);
							else
								$applicantProgram->updateData($data4, $row['ap_id']);
						
						}else{
							$this->view->noticeError = $this->translate("Silalah pilih program studi ke-4");
							//$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'program-invitation'),'default',true));
							$form->populate($formData);
							$this->view->form = $form;
						}
						//redirect
						if  ($dbAppMark->getCompMarkByTxnIdCompId($transaction_id))
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
	
					$this->view->education_selected =  $applicationEducationData;
	
				}
				 
				if($applicantProgram){
					$this->view->programe_selected = $applicantProgram;
	
					$dataToPopulate['ap_prog_code'] = $applicantProgram[0]['ap_prog_code'];
					$dataToPopulate['ap_prog_code_2'] = $applicantProgram[1]['ap_prog_code'];
					$dataToPopulate['ap_prog_code_3'] = $applicantProgram[2]['ap_prog_code'];
					$dataToPopulate['ap_prog_code_4'] = $applicantProgram[3]['ap_prog_code'];
				}
					
				//$form->populate($dataToPopulate);
				$this->view->form = $form;
			}
	
			$this->view->form = $form;
		}
	}
	
	public function ajaxGetSchoolAction(){
    	$school_type_id = $this->_getParam('school_type_id', 0);
    	$school_state_id = $this->_getParam('school_state_id', 0);
    	$school_city_id = $this->_getParam('school_city_id', 0);
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
		                 ->where('sm.sm_status="1"')
		  				 ->order('sm.sm_name ASC');
		    
		    if($school_type_id!=0){
		    	$select->where('sm.sm_type = ?', $school_type_id);
		    }
			if($school_state_id!=0){
		    	$select->where('sm.sm_state = ?', $school_state_id);
		    }
		    if($school_city_id!=0){
		    	$select->where('sm.sm_city = ?', $school_city_id);
		    }
	  	}else if($keyvalue==2){
	  		//get invited school
		  	$select = $db->select()
		  				 ->distinct()
		                 ->from(array('sm'=>'school_master'),array('sm_id','sm_name'))
		                 ->join(array('in'=>'tbl_school_program'),'sm.sm_id=in.sm_id',array())
		                 ->where('in.active="1"')
		                 ->where('sm.sm_status="1"')
		  				 ->order('sm.sm_name ASC');
		    
		    if($school_type_id!=0){
		    	$select->where('sm.sm_type = ?', $school_type_id);
		    }
			if($school_state_id!=0){
		    	$select->where('sm.sm_state = ?', $school_state_id);
		    }
		    if($school_city_id!=0){
		    	$select->where('sm.sm_city = ?', $school_city_id);
		    }
		    //echo $select;exit;
	  	} else {
	  		$select = $db->select()
		                 ->from(array('sm'=>'school_master'))
		                 ->where('sm.sm_status="1"')
		                 ->order('sm.sm_name ASC');
		    
		    if($school_type_id!=0){
		    	$select->where('sm.sm_type = ?', $school_type_id);
		    }
			if($school_state_id!=0){
		    	$select->where('sm.sm_state = ?', $school_state_id);
		    }
		    if($school_city_id!=0){
		    	$select->where('sm.sm_city = ?', $school_city_id);
		    }
	  	}
	    
	    
	    
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
	  //	echo var_dump($row);exit;
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($row);
		
		echo $json;
		exit();
    }
    
    public function ajaxGetSchoolInvitationAction(){
    	$school_type_id = $this->_getParam('school_type_id', 0);
    	$school_state_id = $this->_getParam('school_state_id', 0);
    	$school_city_id = $this->_getParam('school_city_id', 0);
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
    		->where('sm.sm_status="1"')
    		->order('sm.sm_name ASC');
    
    		if($school_type_id!=0){
    			$select->where('sm.sm_type = ?', $school_type_id);
    		}
    		if($school_state_id!=0){
    			$select->where('sm.sm_state = ?', $school_state_id);
    		}
    		if($school_city_id!=0){
    			$select->where('sm.sm_city = ?', $school_city_id);
    		}
    	}else if($keyvalue==2){
    		//get invited school
    		$select = $db->select()
    		->distinct()
    		->from(array('sm'=>'school_master'),array('sm_id','sm_name'))
    		->join(array('in'=>'tbl_school_program'),'sm.sm_id=in.sm_id',array())
    		->where('in.active="1"')
    		->where('sm.sm_status="1"')
    		->order('sm.sm_name ASC');
    
    		if($school_type_id!=0){
    			$select->where('sm.sm_type = ?', $school_type_id);
    		}
    		if($school_state_id!=0){
    			$select->where('sm.sm_state = ?', $school_state_id);
    		}
    		if($school_city_id!=0){
    			$select->where('sm.sm_city = ?', $school_city_id);
    		}
    	} else {
    		$select = $db->select()
    		->from(array('sm'=>'school_master'))
    		->where('sm.sm_status="1"')
    		->order('sm.sm_name ASC');
    
    		if($school_type_id!=0){
    			$select->where('sm.sm_type = ?', $school_type_id);
    		}
    		if($school_state_id!=0){
    			$select->where('sm.sm_state = ?', $school_state_id);
    		}
    		if($school_city_id!=0){
    			$select->where('sm.sm_city = ?', $school_city_id);
    		}
    	}
    	 
    	 
    	 
    	$stmt = $db->query($select);
    	$row = $stmt->fetchAll();
    	//	echo var_dump($row);exit;
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    
    	$json = Zend_Json::encode($row);
    
    	echo $json;
    	exit();
    }
    
     
    public function ajaxGetSchoolPdptAction(){
    	 
    	$idwil = $this->_getParam('school_city_id', 0); 
    	//if ($this->getRequest()->isXmlHttpRequest()) {
    	$this->_helper->layout->disableLayout();
    	//}
    
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    
    	$db = Zend_Db_Table::getDefaultAdapter();
    
    	 $dbSchool = new App_Model_Application_DbTable_Pt();
    	 
    	 $row=$dbSchool->getByWilayah($idwil);
    	//	echo var_dump($row);exit;
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    
    	$json = Zend_Json::encode($row);
    
    	echo $json;
    	exit();
    }
    
    public function ajaxGetPtPdptAction(){
    
    	$pt = $this->_getParam('pt', 0);
    	//if ($this->getRequest()->isXmlHttpRequest()) {
    	$this->_helper->layout->disableLayout();
    	//}
    
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    
    	$db = Zend_Db_Table::getDefaultAdapter();
    
    	$dbPt = new App_Model_Application_DbTable_Pt();
    
    	$row=$dbPt->getByNama($pt);
    	//	echo var_dump($row);exit;
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    
    	$json = Zend_Json::encode($row);
    
    	echo $json;
    	exit();
    }
    public function ajaxGetBranchAction(){
    	$program_id = $this->_getParam('program_id', 0);
    	$type = $this->_getParam('type', 0);
    	//if ($this->getRequest()->isXmlHttpRequest()) {
    	$this->_helper->layout->disableLayout();
    	//}
    
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    
    	$db = Zend_Db_Table::getDefaultAdapter();
     
    		$select = $db->select()
    		->distinct()
    		->from(array('sm'=>'appl_program_branch'),array('IdProgramBranch','GrpMhs'=>'sm.GroupName'))
    		->join(array('br'=>'tbl_branchofficevenue'),'br.IdBranch=sm.IdBranch',array())
    		->join(array('pr'=>'tbl_program'),'pr.IdProgram=sm.IdProgram',array())
    		->where('sm.active="1"')
    		->order('sm.GroupName ASC');
    	if ($type=='pssb') {
    		if($program_id!=0){
    			$select->where('pr.programcode = ?', $program_id);
    		}
    	} else if ($type=='transfer') {
    		
		    		if($program_id!=0){
		    			$select->where('pr.IdProgram = ?', $program_id);
		    		}
    	} else if ($type=='portofolio') {
    					if($program_id!=0){
    						$select->where('pr.programcode = ?', $program_id);
    					}
    	} else if ($type=='scholarship') {
    						if($program_id!=0){
    						$select->where('pr.programcode = ?', $program_id);
    						}
    	}else {
    				if($program_id!=0){
		    			$select->join(array('app'=>'appl_placement_program'),'pr.ProgramCode=app.app_program_code');
		    			$select->where('app.app_id = ?', $program_id);
    				} 
    	}
    	 
    	$stmt = $db->query($select);
    	$row = $stmt->fetchAll();
    	//	echo $select;exit;
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    
    	$json = Zend_Json::encode($row);
    
    	echo $json;
    	exit();
    }
    
     
    
    public function ajaxGetCitysAction(){
    	$stateid = $this->_getParam('school_state_id', 0);
    	// echo $stateid;exit;
    	$keyvalue = $this->_getParam('keyvalue', 0);
    	 
    	//if ($this->getRequest()->isXmlHttpRequest()) {
    	$this->_helper->layout->disableLayout();
    	//}
    
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    
    	$db = Zend_Db_Table::getDefaultAdapter();
    
    	 
    		$select = $db->select()
    		->from(array('sm'=>'tbl_city'),array('idCity','CityName'))
    		->where('sm.active="1"')
    		->where('sm.idState= ?',$stateid)
    		->order('sm.cityname ASC');
       
    	//$stmt = $db->query($select);
    	$row = $db->fetchAll($select);
    
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
	  	$select = $db->select()
	                 ->from(array('sd'=>'school_discipline'),array('smd_code','smd_desc'))
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
    
    public function ajaxGetTranscriptAction(){
    	
    	$nim = $this->_getParam('nim', 0);
    	$studentRegDB = new App_Model_Registration_DbTable_Studentregistration();
    	$std=$studentRegDB->getStudentRegistrationByNim($nim);
    	//if ($this->getRequest()->isXmlHttpRequest()) {
    	$this->_helper->layout->disableLayout();
    	//}
    	$db = Zend_Db_Table::getDefaultAdapter();
    	

    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    	
    	if ($std) {
    		$idStudentRegistration=$std['IdStudentRegistration'];
    		$idprogram=$std['IdProgram'];
    		$idlandscape=$std['IdLandscape'];
    		$regSubjectDB= new App_Model_Exam_DbTable_StudentRegistrationSubject();
    		$dbLands = new GeneralSetup_Model_DbTable_Landscapesubject();
			$dbBlock= new GeneralSetup_Model_DbTable_LandscapeBlockSubject();
			$subject_list = $dbLands->getlandscapesubjects($idprogram,$idlandscape);
			if ($subject_list==array()) $subject_list = $dbBlock->getLandscapeCourse($idlandscape);
			 
			foreach ($subject_list as $key=>$subject) {
					$subject=$regSubjectDB->getHighestMarkofAllSemesterPassed($idStudentRegistration, $subject['IdSubject']);
					if (!is_bool($subject)) $subjects[] = $subject;
					
					
			}
    	} else $subjects=array();
    	
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    
    	$json = Zend_Json::encode($subjects);
    
    	echo $json;
    	exit();
    }
    
	public function ajaxGetDisciplineSubjectAction(){
    	$discipline_code = $this->_getParam('discipline_code', 0);
    	$appltype = $this->_getParam('appltype', 0);
    	//$programs = $this->_getParam('programs', 0);
    	$placecode= $this->_getParam('placecode', 0);
    	$type= $this->_getParam('type', 0);
    	//if ($programs!=0)
    	//	$programs=implode(',', $programs);
    	 
     	//if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        //}
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
        $auth = Zend_Auth::getInstance(); 
    	$appl_id = $auth->getIdentity()->appl_id;
    	$trans_id = $auth->getIdentity()->transaction_id;
    	
	  	$db = Zend_Db_Table::getDefaultAdapter();
	  		  	//get applicant education head data
	  	$applicationEducationDb = new App_Model_Application_DbTable_ApplicantEducation();
	  	$educationData = $applicationEducationDb->getDataByapplID($appl_id);
	  	
	  	
	  	 if($type=="1" || $type=="2") {
	  		//get from UTBK
	  		$select = $db->select()
	  			->from(array('a'=>'appl_placement_utbk_test_type'))
	  			->join(array('c'=>'appl_component'),'a.idcomponent=c.ac_id',array('subjectname'=>'ac_comp_name_bahasa','ac_comp_code'))
	  			->join(array('e'=>'appl_placement_detl'),'e.apd_comp_code=c.ac_comp_code')
	  			->where('a.type=?',$type)
	  			->where('e.apd_placement_code=?',$placecode) ;
	  			 
	  		$stmt = $db->query($select);
	  		$row = $stmt->fetchAll();
	  		foreach ($row as $key=>$value) {
	  			$select = $db->select()
	  			->from(array('d'=>'applicant_ptest_comp_mark'))
	  			->where('d.apcm_at_trans_id=?',$trans_id)
	  			->where('d.apcm_apd_id=?',$value['apd_id'])
	  			->where('d.pcode=?',$placecode);
	  			$mark=$db->fetchRow($select);
	  			if ($mark) {
	  				$row[$key]['mark']=$mark['apcm_mark'];
	  			} else $row[$key]['mark']=0;
	  			 
	  		}
	  	} else {  
	  		//get for raport
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
	  	}
	  
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
    	$intake = $this->_getParam('intake_id', 0);
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
	                 ->join(array('p'=>'tbl_program'),'p.ProgramCode = app.app_program_code', array('ArabicName','ProgramName','ProgramCode','IdProgram','strata') )
	                 ->joinLeft(array('apr'=>'appl_program_req'),"apr.apr_program_code = app.app_program_code and apr.apr_decipline_code = '".$discipline_code."'")
	                 ->join(array('ip'=>'appl_placement_intake_program'),'p.IdProgram=ip.IdProgram',array())
	                 ->where('app.app_placement_code  = ?', $placementTestCode['apt_ptest_code'])
	                 ->where('ip.IdIntake=?',$intake)
	                 ->order('p.ArabicName ASC');
			
	  	// check program offer
	  	$select->where("p.UsmOffer = 1");
	  	
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
    
    public function ajaxGetProgrammePortofolioAction(){
    	$discipline_code = $this->_getParam('discipline_code', 0);
    	$intake = $this->_getParam('intake_id', 0);
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
	                 ->where("at.at_appl_type=5");	
    
    	//get placementest program data filtered with discipline
    	$select = $db->select()
    				 ->distinct()
	                 ->from(array('apr'=>'appl_program_req'),array())
	                 ->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode = apr.apr_program_code',array('ProgramCode','ProgramName','ArabicName','strata') )
	                 ->join(array('ip'=>'appl_placement_intake_program'),'p.IdProgram=ip.IdProgram',array())
	                 ->where('ip.IdIntake=?',$intake)
	                 ->where("p.ProgramCode NOT IN (?)",$select_applied)
	                 ->order('p.ArabicName ASC');
    		
    	// check program offer
    	$select->where("p.PortofolioOffer = 1");
    
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
    
    public function ajaxGetProgrammeScholarshipAction(){
    	$discipline_code = $this->_getParam('discipline_code', 0);
    	$intake = $this->_getParam('intake_id', 0);
    	$idScholar=$this->_getParam('idscholar', 0);
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
	                 ->where("at.at_appl_type=6");	
    
    	//get placementest program data filtered with discipline
    	$select = $db->select()
    				 ->distinct()
	                 ->from(array('apr'=>'appl_program_req'),array())
	                 ->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode = apr.apr_program_code' ,array('ProgramCode','ProgramName','ArabicName','strata'))
	                 ->join(array('ip'=>'tbl_scholar_program'),'p.IdProgram=ip.IdProgram',array())
	                 ->where('ip.IdIntake=?',$intake)
	                 ->where('ip.idScholar=?',$idScholar)
	                 ->where("p.ProgramCode NOT IN (?)",$select_applied)
	                 ->order('p.ArabicName ASC');
    		
    	// check program offer
    	$select->where("p.ScholarshipOffer = 1");
    
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
    	$intake = $this->_getParam('intake_id', 0);
    	
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
	  				->distinct()
	                 ->from(array('apr'=>'appl_program_req'),array())
	                 ->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode = apr.apr_program_code',array('ProgramCode','ProgramName','ArabicName','strata') )
	                 ->join(array('ip'=>'appl_placement_intake_program'),'p.IdProgram=ip.IdProgram',array())
	                 ->where('ip.IdIntake=?',$intake)
	                 ->where("p.ProgramCode NOT IN (?)",$select_applied)
	                 ->order('p.ArabicName ASC');
	    
	    
	  	// check program offer
	  	$select->where("p.PssbOffer = 1");
	                 
	    if($discipline_code!=0){
	    	$select->where('apr.apr_decipline_code  = ?', $discipline_code);
	    }
	    
		/*if($academic_year!=0){
	    	$select->where('apr.apr_academic_year  = ?', $academic_year);
	    } */            
				
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
    public function ajaxGetProgrammeInvitationAction(){
    	$discipline_code = $this->_getParam('discipline_code', 0);
    	$intake = $this->_getParam('intake_id', 0);
    	$school=$this->_getParam('school', 0);
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
    	->distinct()
    	->from(array('at'=>'applicant_transaction'),array())
    	->join(array('ap'=>'applicant_program'),'ap.ap_at_trans_id=at.at_trans_id',array('ap_prog_code'=>'distinct(ap.ap_prog_code)'))
    	->where("at.at_appl_id= '".$appl_id."'")
    	->where("ap.ap_at_trans_id != '".$transaction_id."'")
    	->where("at.at_appl_type=4");
    
    
    	//get program data based on discipline
    	$select = $db->select()
    	->distinct()
    	->from(array('apr'=>'appl_program_req'),array())
    	->join(array('p'=>'tbl_program'),'p.ProgramCode = apr.apr_program_code',array('ProgramCode','ProgramName','ArabicName','strata') )
    	->join(array('sp'=>'tbl_school_program'),'sp.program_id=p.IdProgram',array())
    	->join(array('ip'=>'appl_placement_intake_program'),'p.IdProgram=ip.IdProgram',array())
    	->where('ip.IdIntake=?',$intake)
    	->where('sp.active="1"')
    	->where("p.ProgramCode NOT IN (?)",$select_applied)
    	->order('p.ArabicName ASC');
    	 
    	 
    	// check program offer
    	//$select->where("p.PssbOffer = 1");
    
    	if($discipline_code!=0){
    		$select->where('apr.apr_decipline_code  = ?', $discipline_code);
    	}
    	 
    	/*if($academic_year!=0){
    	 $select->where('apr.apr_academic_year  = ?', $academic_year);
    	} */
    
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
    
    public function ajaxGetProgrammeUtbkAction(){
    	$discipline_code = $this->_getParam('discipline_code', 0);
    	$intake = $this->_getParam('intake_id', 0);
    	$school=$this->_getParam('school', 0);
    	$prog=$this->_getParam('prog', 0);
    	$programs=array();
    	if (!($prog==0 || $prog==null)) $programs[]='"'.$prog.'"';
    	$prog=$this->_getParam('prog2', 0);
    	if (!($prog==0 || $prog==null)) $programs[]='"'.$prog.'"';
    	$prog=$this->_getParam('prog3', 0);
    	if (!($prog==0 || $prog==null)) $programs[]='"'.$prog.'"';
    	$prog=$this->_getParam('prog4', 0);
    	if (!($prog==0 || $prog==null)) $programs[]='"'.$prog.'"';
    	
    	if (count($programs)>0) $programs=implode(',', $programs);
    	else $programs=null;
    	
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
    	->distinct()
    	->from(array('at'=>'applicant_transaction'),array())
    	->join(array('ap'=>'applicant_program'),'ap.ap_at_trans_id=at.at_trans_id',array('ap_prog_code'=>'distinct(ap.ap_prog_code)'))
    	->where("at.at_appl_id= '".$appl_id."'")
    	->where("ap.ap_at_trans_id != '".$transaction_id."'")
    	->where("at.at_appl_type=7");
    
    
    	//get program data based on discipline
    	$select = $db->select()
    	->distinct()
    	->from(array('apr'=>'appl_program_req'),array())
    	->join(array('p'=>'tbl_program'),'p.ProgramCode = apr.apr_program_code',array('ProgramCode','ProgramName','ArabicName','strata') )
    	->join(array('sp'=>'tbl_school_program'),'sp.program_id=p.IdProgram',array())
    	->join(array('ip'=>'appl_placement_intake_program'),'p.IdProgram=ip.IdProgram',array())
    	->where('ip.IdIntake=?',$intake)
    	->where('sp.active="1"')
    	->where("p.ProgramCode NOT IN (?)",$select_applied)
    	
    	->order('p.ArabicName ASC');
    
    	if ($programs!=null) $select->where("p.ProgramCode NOT IN (".$programs.")");
    	//echo $select;
    	// check program offer
    	//$select->where("p.PssbOffer = 1");
    
    	if($discipline_code!=0){
    		$select->where('apr.apr_decipline_code  = ?', $discipline_code);
    	}
    
    	/*if($academic_year!=0){
    	 $select->where('apr.apr_academic_year  = ?', $academic_year);
    	} */
    	//echo $select;
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
    
    public function ajaxGetProgrammeHsTwoAction(){
    	
    	$discipline_code = $this->_getParam('discipline_code', 0);
    	$intake = $this->_getParam('intake_id', 0);
    	 
    	$program[]= $this->_getParam('prog', 0);
    	$program[]=$this->_getParam('prog1', 0);;
    	 
    	$program[]=$this->_getParam('prog2', 0);;
     
    	$program[]=$this->_getParam('prog3', 0); 
    	$appltype=$this->_getParam('appltype', 0); 
    	
    	$this->_helper->layout->disableLayout();
    	$program=implode(',', $program);
    
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
    	->where("at.at_appl_type=?",$appltype);
    
    
    	//get program data based on discipline
    	$select = $db->select()
    	->distinct()
    	->from(array('apr'=>'appl_program_req'))
    	->join(array('p'=>'tbl_program'),'p.ProgramCode = apr.apr_program_code',array('ProgramCode','ProgramName','ArabicName','strata') )
    	->join(array('sp'=>'tbl_school_program'),'sp.program_id=p.IdProgram',array())
    	->join(array('ip'=>'appl_placement_intake_program'),'p.IdProgram=ip.IdProgram',array())
    	->where('ip.IdIntake=?',$intake)
    	->where('sp.active="1"')
    	->where("p.ProgramCode NOT IN (?)",$select_applied)
    	->where("p.ProgramCode NOT IN ('0300','0400')") // FK dan FKG tidak ikut pilihan 2
    	->where("p.ProgramCode NOT IN (".$program.")")
    	->order('p.ArabicName ASC');
    	 
    	 
    	// check program offer
    	//$select->where("p.invi = 1");
    
    	if($discipline_code!=0){
    		$select->where('apr.apr_decipline_code  = ?', $discipline_code);
    	}
    	 
    	/*if($academic_year!=0){
    	 $select->where('apr.apr_academic_year  = ?', $academic_year);
    	} */
    
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
    public function ajaxGetProdiPdptAction(){
    	//$discipline_code = $this->_getParam('discipline_code', 0);
    	$idsp = $this->_getParam('id_pt', 0);
    	 
    	$this->_helper->layout->disableLayout();
    
     
    
    	//program in placement test with discipline filter
    
    	//transaction data
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;
    	$transaction_id = $auth->getIdentity()->transaction_id;
    	 
    	$db = Zend_Db_Table::getDefaultAdapter();
    
     	$dbsms=new App_Model_Application_DbTable_Sms();
     	$row=$dbsms->getDataByPT($idsp);
     	
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
     	$ajaxContext->addActionContext('view', 'html');
     	$ajaxContext->initContext();
     	
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    
    
    	 
    		$json = Zend_Json::encode($row);
    	 
    
    	echo $json;
    	exit();
    }
    
	public function uploaddocumentOldAction(){
		 
		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			if($auth->getIdentity()->role=='agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}	
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
    	
    	$dbAppTest=new App_Model_Application_DbTable_ApplicantPtest();
    	$ptest=$dbAppTest->getPtest($transaction_id);
    	$testCode=$ptest['apt_ptest_code'];
    	
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
    	if (count($applicantProgram)==1) {
    		$prog1=$applicantProgram[0]['program_id'];
    		$prog2=null;
    	}
    	else if (count($applicantProgram)==1) {
    		$prog1=$applicantProgram[0]['program_id'];
    		$prog2=$applicantProgram[1]['program_id'];
    	} 
    	
    	$document=new App_Model_Application_DbTable_DocumentPrerequisite();
    	$documentlist=$document->getDataByProgram($testCode,$prog1,$prog2);
    	$this->view->documentlist=$documentlist;
    	
    	$noticeMessage ="<p>".$this->view->translate("Silakan mengunggah semua dokumen tertera dibawah").":-</p>";
    	$noticeMessage .="<table width='100%' cellpadding=5 cellspacing=1 border=0 class='table3'><tr><td>".$this->view->translate("Dokumen utk PSSB")."</td><td>".$this->view->translate("Dokumen utk USM")."</td><td>".$this->view->translate("Dokumen utk Credit Transfer")."</td></tr>";
    	$noticeMessage .="<tr><td width='34%'><ul>";
    	$noticeMessage .="<li>".$this->view->translate("photograph")."</li>";
    	$noticeMessage .="<li>".$this->view->translate("nric")."</li>";
    	$noticeMessage .="<li>".$this->view->translate("academic_award")."</li>"; //surat buta huruf
    	$noticeMessage .="<li>".$this->view->translate("school_transcript")."</li>";
    	$noticeMessage .="</ul></td>";
    	$noticeMessage .="<td width='33%'><ul>";
    	$noticeMessage .="<li>".$this->view->translate("photograph")."</li>";
    	$noticeMessage .="<li>".$this->view->translate("nric")."</li>";
    	$noticeMessage .="<li>".$this->view->translate("Surat Keterangan tidak buta warna dari Dokter Mata")."</li>"; //surat buta huruf
    	$noticeMessage .="</ul></td>";
        $noticeMessage .="<td><ul>";
        $noticeMessage .="<li>".$this->view->translate("photograph")."</li>";
    	$noticeMessage .="<li>".$this->view->translate("nric")."</li>";
    	$noticeMessage .="<li>".$this->view->translate("academic_award")."</li>"; //surat buta huruf
    	$noticeMessage .="<li>".$this->view->translate("academic_transcript")."</li>";
        $noticeMessage .="</ul></td></tr></table>";    	
    	
    	$this->view->noticeMessage = $noticeMessage;
    	
	}
	
	public function uploaddocumentAction(){
			
		/*
		 * check session for transaction
		*/
		$auth = Zend_Auth::getInstance();
		if($auth->getIdentity()->transaction_id==null){
			if($auth->getIdentity()->role=='agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}
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
		$dbAppTest=new App_Model_Application_DbTable_ApplicantPtest();
			
		$this->view->transaction = $transaction;
		if ($transaction["at_appl_type"]=="2") $testCode='PSSB';
		else if ($transaction["at_appl_type"]=="3") $testCode='CREDIT';
		else if ($transaction["at_appl_type"]=="4") $testCode='INVITATION';
		else if ($transaction["at_appl_type"]=="5") $testCode='PORTOFOLIO';
		
		else if ($transaction["at_appl_type"]=="6") $testCode='SCHOLARSHIP';
		else if ($transaction["at_appl_type"]=="7") $testCode='UTBK';
		else {
			$ptest=$dbAppTest->getPtest($transaction_id);
			$testCode=$ptest['apt_ptest_code'];
		}
		
		 
		$msg = $this->_getParam('msg', null);
		if($msg){
			$this->view->noticeError = $msg;
		}
			
		$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant = $appProfileDB->getData($appl_id);
		$this->view->applicant = $applicant;
		 
		$uploadFileDB = new App_Model_Application_DbTable_UploadFile();
		$getUploadData = $uploadFileDB->getUploadDataNew($transaction_id);
		$this->view->uploadeddata = $getUploadData;
		
		if (count($applicantProgram)==1) {
			$prog1=$applicantProgram[0]['program_id'];
			$prog2=null;
			$prog3=null;
			$prog4=null;
		}
		else if (count($applicantProgram)==2) {
			$prog1=$applicantProgram[0]['program_id'];
			$prog2=$applicantProgram[1]['program_id'];
			$prog3=null;
			$prog4=null;
		}
		else if (count($applicantProgram)==4) {
			$prog1=$applicantProgram[0]['program_id'];
			$prog2=$applicantProgram[1]['program_id'];
			$prog3=$applicantProgram[2]['program_id'];
			$prog4=$applicantProgram[3]['program_id'];
		}
		 
		$document=new App_Model_Application_DbTable_DocumentPrerequisite();
		$documentlist=$document->getDataByProgram($testCode,$prog1,$prog2,$prog3,$prog4);
		foreach ($documentlist as $key=>$value) {
			foreach ($getUploadData as $up) {
				
				 if ($value['IdDocument']==$up['auf_file_type']) unset($documentlist[$key]);
			}
		}
		$this->view->documentlist=$documentlist;
		 
		 
		 
	}
	
	public function uploaddocumentfilesOldAction(){
		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			if($auth->getIdentity()->role=='agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}	
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
					
					$ext_photo = strtolower($this->getFileExtension($_FILES["photograph"]["name"]));
					
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
					
					$ext_nric = strtolower($this->getFileExtension($_FILES["nric"]["name"]));
					
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
					
					$ext_passport = strtolower($this->getFileExtension($_FILES["passport"]["name"]));
					
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
						$ext_academic = strtolower($this->getFileExtension($_FILES["academic_award".$a]["name"]));
						
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
						$ext_transcript = strtolower($this->getFileExtension($_FILES["academic_transcript".$b]["name"]));
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
	public function uploaddocumentfilesAction(){
		/*
		 * check session for transaction
		*/
		$auth = Zend_Auth::getInstance();
		if($auth->getIdentity()->transaction_id==null){
			if($auth->getIdentity()->role=='agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}
		}
	
		//$appl_id = $this->_getParam('id', 0);
		$auth = Zend_Auth::getInstance();
		$appl_id = $auth->getIdentity()->appl_id;
		$this->view->appl_id = $appl_id;
		 
		$transaction_id = $auth->getIdentity()->transaction_id;
		$this->view->transaction_id = $transaction_id;
	
		$dbAppTest=new App_Model_Application_DbTable_ApplicantPtest();
		 
		//transaction data
		$applicantTransactionDn = new App_Model_Application_DbTable_ApplicantTransaction();
		$transaction = $applicantTransactionDn->getTransactionData($transaction_id);
		
			
		$this->view->transaction = $transaction;
		if ($transaction["at_appl_type"]=="2") $testCode='PSSB';
		else if ($transaction["at_appl_type"]=="3") $testCode='CREDIT';
		else if ($transaction["at_appl_type"]=="4") $testCode='INVITATION';
		else if ($transaction["at_appl_type"]=="5") $testCode='PORTOFOLIO';
		
		else if ($transaction["at_appl_type"]=="6") $testCode='SCHOLARSHIP';
		else if ($transaction["at_appl_type"]=="7") $testCode='UTBK';
		else {
			$ptest=$dbAppTest->getPtest($transaction_id);
			$testCode=$ptest['apt_ptest_code'];
		}
		
		$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant = $appProfileDB->getData($appl_id);
		$this->view->applicant = $applicant;
		
		$applicantProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
		$applicantProgram = $applicantProgramDB->getPlacementProgram($transaction_id);
			
		if (count($applicantProgram)==1) {
			$prog1=$applicantProgram[0]['program_id'];
			$prog2=null;
			$prog3=null;
			$prog4=null;
		}
		else if (count($applicantProgram)==2) {
			$prog1=$applicantProgram[0]['program_id'];
			$prog2=$applicantProgram[1]['program_id'];
			$prog3=null;
			$prog4=null;
		}
		
		else if (count($applicantProgram)==4) {
			$prog1=$applicantProgram[0]['program_id'];
			$prog2=$applicantProgram[1]['program_id'];
			$prog3=$applicantProgram[0]['program_id'];
			$prog4=$applicantProgram[1]['program_id'];
		}
			
		$document=new App_Model_Application_DbTable_DocumentPrerequisite();
		$documentlist=$document->getDataByProgram($testCode,$prog1,$prog2,$prog3,$prog4);
		//$this->view->documentlist=$documentlist;
		
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
				//$files=$formData['fileup'];
				foreach ($documentlist as $doc) {
					$docid="fileup".$doc['IdDocument'];
					if (is_uploaded_file($_FILES[$docid]['tmp_name'])){
							
						$ext_photo = strtolower($this->getFileExtension($_FILES[$docid]["name"]));
							
						if($ext_photo==".jpg" || $ext_photo==".JPG" || $ext_photo==".jpeg" || $ext_photo==".JPEG" || $ext_photo==".gif" || $ext_photo==".GIF" || $ext_photo==".png" || $ext_photo==".PNG"){
							$flnamephoto = date('Ymdhs')."_".$doc['code'].$ext_photo;
							$path_photograph = $major_path."/".$flnamephoto;
							move_uploaded_file($_FILES[$docid]['tmp_name'], $path_photograph);
		
							$upd_photo = array(
									'auf_appl_id' => $formData["transaction_id"],
									'auf_file_name' => $flnamephoto,
									'auf_file_type' => $doc['IdDocument'],
									'auf_upload_date' => date("Y-m-d h:i:s"),
									'auf_upload_by' => $transaction_id,
									'pathupload' => $path_photograph
							);
		
							$uploadfileDB->addData($upd_photo);
		
						}
							
					}
				}
				/* if (is_uploaded_file($_FILES["nric"]['tmp_name'])){
						
					$ext_nric = strtolower($this->getFileExtension($_FILES["nric"]["name"]));
						
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
						
					$ext_passport = strtolower($this->getFileExtension($_FILES["passport"]["name"]));
						
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
				} */
	
				for ($a=1; $a<=$iteration_academic; $a++){
					//
					if (is_uploaded_file($_FILES["academic_award".$a]['tmp_name'])){
					$ext_academic = strtolower($this->getFileExtension($_FILES["academic_award".$a]["name"]));
	
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
	
			/* for ($b=1; $b<=$iteration_transcript; $b++){
				//
				if (is_uploaded_file($_FILES["academic_transcript".$b]['tmp_name'])){
					$ext_transcript = strtolower($this->getFileExtension($_FILES["academic_transcript".$b]["name"]));
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
					}*/
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
			if($auth->getIdentity()->role=='agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}	
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
							
							$ext_photo = strtolower($this->getFileExtension($_FILES["photograph"]["name"]));
							
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
						
							$ext_photo = strtolower($this->getFileExtension($_FILES["photograph"]["name"]));
							
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
								
							
							$ext_nric = strtolower($this->getFileExtension($_FILES["nric"]["name"]));
							
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
						
							$ext_nric = strtolower($this->getFileExtension($_FILES["nric"]["name"]));
							
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
							$ext_academic = strtolower($this->getFileExtension($_FILES["academic_award".$a]["name"]));
							
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
							$ext_academic = strtolower($this->getFileExtension($_FILES["academic_award".$a]["name"]));
							
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
							$ext_transcript = strtolower($this->getFileExtension($_FILES["academic_transcript".$b]["name"]));
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
							$ext_transcript = strtolower($this->getFileExtension($_FILES["academic_transcript".$b]["name"]));
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
			if($auth->getIdentity()->role=='agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}	
		}
		
		$msg = $this->_getParam('msg', null);
		if($msg){
			$this->view->noticeError = $msg;	
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
    	$aph_fix_schedulle = $program["aph_fix_schedule"];
    	$ptestinfo = new App_Model_Application_DbTable_ApplicantPtest();   
    	$applicant_placement_test_info = $ptestinfo->getScheduleInfo($transaction_id);
    	
    	$this->view->aps_id = $applicant_placement_test_info["aps_id"];
    		
    	$form = new App_Form_PlacementTest(array('aphplacementcode'=>$program["aph_placement_code"],'aphfeesprogram'=>$program["aph_fees_program"],
    	'aphfeeslocation'=>$program["aph_fees_location"],'transactionid'=>$transaction_id ,'applid'=>$appl_id,'fix_schedule'=>$aph_fix_schedulle));
    	
    	if ($this->getRequest()->isPost()) {    		
    		
    		$formData = $this->getRequest()->getPost(); 
    		//echo var_dump($formData);exit;
    		if($form->isValid($formData)){  		    		
				
	    		//get billing runno bankID & applicant(payeeID)
	    		//$bankpinDB = new App_Model_Application_DbTable_ApplicantPinBank();
	    		//$bank_info = $bankpinDB->getData();

    					
	    		$info["apt_at_trans_id"]=	$transaction_id;
				$info["apt_ptest_code"]	=	$formData["aph_placement_code"];
				$info["apt_aps_id"]		=	$formData["aps_test_date"]; //appl_placement_location
				$info["apt_fee_amt"]	=	$formData["apt_fee_amt"];
				
				$appptestDB = new App_Model_Application_DbTable_ApplicantPtest();
				
				if($applicant_placement_test_info){
					//echo 'update';echo var_dump($info);exit;
					$appptestDB->updateData($info,$applicant_placement_test_info["apt_id"]);
				}else{
					//echo 'insert';exit;
	    				$appptestDB->addData($info);
				}   
				
				//to update appl_pin_to_bank to bankID has been used "P"
				//$pin["status"]="P"; //dah pakai
				//$bankpinDB->updateData($pin,$bank_info["billing_no"]);
				
				
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme'),'default',true));
    		}else{
    			 
    			//$form->populate($formData);
    			$this->view->form = $form;
    		}
			
    	}else{
    		
    		
	    	if($applicant_placement_test_info){
	    		$this->view->data = $applicant_placement_test_info;
	    		
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
	public function confirmationOldAction(){
		/*
		 * check session for transaction
		 */
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			if($auth->getIdentity()->role=='agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}	
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
			    	
    	$admission_type = $transData["at_appl_type"];//1:placement test 2:high school 3:credit transfer 
		
    	
    	//------- checking section -------
    	/**
    	 * Check for all steps that involve in application
    	 * 1.Admission Type
    	 * 2.Placement Test Schedule (for USM only)
    	 * 3.Programme 
    	 * 4.Documents
    	 */

    	//*** check admission type
    	if(!isset($admission_type) || $admission_type==null || $admission_type=='' || $admission_type=='0'){
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'admission','msg'=>$this->view->translate('Please select admission type')),'default',true));
    	}
    	
		//*** check date for placement test
    	if($admission_type==1){
    		$ptestDb = new App_Model_Application_DbTable_ApplicantPtest();
			$ptestData = $ptestDb->getScheduleInfo($transaction_id);
			
			
			if(!$ptestData){
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'placement-test','msg'=>$this->view->translate('Please select schedule for placement test')),'default',true));			
			}
    	}
    	
		//*** check programme
		$applicantProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
    	$applicantProgram = $applicantProgramDB->getPlacementProgram($transaction_id);
    	
    	if(!$applicantProgram){
    		if($admission_type==2){
    			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-highschool','msg'=>$this->view->translate('please_fill_in_program_prefered')),'default',true));
    		}else{
    			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme','msg'=>$this->view->translate('please_fill_in_program_prefered')),'default',true));	
    		}
    	}
    	
    	//*** check document
    	/*
    	 * Disable check on other document execluding photo
    	 */
		$fileDB =new App_Model_Application_DbTable_UploadFile();
    	$photo = $fileDB->getFile($transaction_id,33); //photo
    	
    	/*$nric = $fileDB->getFile($transaction_id,34); //nric
    	  $transcript = $fileDB->getFile($transaction_id,36); //Medical Report*/
    	
    	if(($admission_type==2) || ($admission_type==3)){
    		$raport = $fileDB->getFile($transaction_id,37); //Raport/transcript
		}else{
			$raport="x";
		}  	
    	
		if((!$photo) || (!$raport)){
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'uploaddocument','msg'=>$this->view->translate('please_upload_document.')),'default',true));
    	}
    	//------- end checking section -------
    	
    	
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
    	
				
    		//----------- empty transaction id in session to avoid back button -------
			$auth = Zend_Auth::getInstance(); 
			$auth->getIdentity()->transaction_id = null;
    		
    		//get academic year
    	    $ayearDb = new App_Model_Record_DbTable_AcademicYear();
			$academic_year = $ayearDb->getNextAcademicYearData();							
									
    	
			//get current intake
			//$intakeDB = new App_Model_Record_DbTable_Intake();
			//$intake = $intakeDB->getCurrentIntake();
			$IdIntake = $transData['at_intake'];//$intake["IdIntake"];
							
			//get period ->utk letak application ni pada period period yg mana berdasarkan application activity calendar
			/*$activityDB = new App_Model_Record_DbTable_ActivityCalender();					
			$activity = $activityDB->getPeriodByActivity(29);//online application
			$idPeriod = $activity["IdPeriod"];*/
			
    	
			//get period
			
    		//cek bulan dan tahun
			$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
			$period   = $periodDB->getCurrentPeriod(date("m"),date("Y"),$IdIntake);
			$idPeriod = $period["ap_id"];
			
			//echo 'period='.$transData["entry_type"];exit;
			$ptestinfo = new App_Model_Application_DbTable_ApplicantPtest();
			$applicant_placement_test_info = $ptestinfo->getScheduleInfo($transaction_id);
				
			//generate no peserta
			//manual entry already have no peserta
			if($transData["entry_type"]==2)
            {	 
				$applicantID = $transData["at_pes_id"];
				   
				if($transData["at_appl_type"]==1){ //USM
						
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
						$error="Maaf tempat untuk USM telah penuh. Sila memilih lokasi ujian yang lain";
						$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'notification','msg'=>$error),'default',true));
						
					}
					
					//update transaction period based on ptest schedule
					//update ptest data
		       		$ptestinfo = new App_Model_Application_DbTable_ApplicantPtest();   
					$applicant_placement_test_info = $ptestinfo->getScheduleInfo($transaction_id);
					$ptPeriod   = $periodDB->getCurrentPeriod(date("m",strtotime($applicant_placement_test_info['aps_test_date'])), date("Y",strtotime($applicant_placement_test_info['aps_test_date'])));
					
					//once submmitted update status prcess sebab da bayar masa amik form dari agent
	    			$upddata["at_status"]='PROCESS';
	    			$upddata["at_academic_year"]=$academic_year["ay_id"];
	    			$upddata["at_intake"]=$IdIntake;
	    			$upddata["at_period"]=$ptPeriod["ap_id"];
	    			$upddata["at_submit_date"] = date("Y-m-d H:i:s");
	    		
					$transDB->updateData($upddata,$transaction_id);
                    $this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'viewkartu'),'default',true));
				}
					
			}
            else
            {	//online USM				
					
				
				//kalau dah ada pes jgn mintak no pes lagi
				//check no pes
				if( !isset($transData["at_pes_id"]) && $transData["at_pes_id"]==null ){
					
                    //to get and update applicantID
					$applicantID = $transDB->getApplicantID($transData["at_appl_type"],$transData["at_intake"],$applicant_placement_test_info['aps_placement_code']);//
					//echo $applicantID;exit;
					$data["at_pes_id"]=$applicantID;
                    
					$transDB->updateData($data, $transData["at_trans_id"]);
                    
				}else{
					
                    $applicantID = $transData["at_pes_id"];
				}
			//	exit;
			}//end generate no peserta	
            //die;
			//-------- placement test (USM) section -------------
			if($admission_type==1){
			       	
		       	//update ptest data
		       	$ptestinfo = new App_Model_Application_DbTable_ApplicantPtest();   
				$applicant_placement_test_info = $ptestinfo->getScheduleInfo($transaction_id);
		    	
				$appptestDB = new App_Model_Application_DbTable_ApplicantPtest();
				$info["apt_bill_no"]=$applicantID;
				$appptestDB->updateData($info,$applicant_placement_test_info["apt_id"]);
				
				//update transaction period based on ptest schedule
				$ptPeriod   = $periodDB->getCurrentPeriod(date("m",strtotime($applicant_placement_test_info['aps_test_date'])), date("Y",strtotime($applicant_placement_test_info['aps_test_date'])),$IdIntake);
				
				//once submmitted update status=CLOSE
				$upddata["at_status"]='CLOSE';
				//$upddata["at_academic_year"]=$academic_year["ay_id"];
				//$upddata["at_intake"]=$IdIntake;
				$upddata["at_period"]=$ptPeriod["ap_id"];
				$upddata["at_submit_date"]=date("Y-m-d H:i:s");
				    		
				$transDB->updateData($upddata,$transaction_id);
	
				$status = $this->generateBankValidationPDF($transaction_id);
				
				//save file info
				$output_directory_path = DOCUMENT_PATH."/applicant/".date("mY")."/".$transaction_id;
				$location_path = "applicant/".date("mY")."/".$transaction_id;
				$output_filename = $applicantID."_validasi_bank.pdf";
			
				$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
				$doc["ad_appl_id"]=$transaction_id;
				$doc["ad_type"]=32;
				$doc["ad_filepath"]=$location_path;
				$doc["ad_filename"]=$output_filename;
				$doc["ad_createddt"]=date("Y-m-d");
				$documentDB->addData($doc);
				
				
				//generate USM Charges invoice
				if( $transData["entry_type"]==0 ){
					$data_invoice = array(
						'bill_number' => $transData['at_pes_id'],
						'appl_id' => $transData['at_appl_id'],
						'no_fomulir' =>$transData['at_pes_id'],
						'academic_year' => $transData['at_academic_year'],
						'bill_amount' =>$applicant_placement_test_info['apt_fee_amt'],
						'bill_paid'=>0,
						'bill_balance'=>$applicant_placement_test_info['apt_fee_amt'],
						'bill_description' => 'USM Charges',
						'college_id' => 0,
						'program_code' => '0000',
						'date_create' => date('Y-m-d H:i:s'),
						'creator' => '-1',
						'fs_id' => 0,
						'fsp_id' => 0,
						'status' =>'A'
					);
					
					
					$invoiceMainDb = new Studentfinance_Model_DbTable_InvoiceMain();
					$invoiceMainDb->insert($data_invoice);
				}
			
			}elseif($admission_type==2){	
            
            //-------- high school cert (PSSB) section -------------
			
				setlocale (LC_ALL, $locale);
				
				//filetype	
				$fileType = 31;
				
       	 	   
				//pengumumam hasil seleksi			   	
			    //0=sunday onwards
			    $today = date("w");
		    
				if($today<=2){								 
					$selection_date = strftime('%e %B %Y',  strtotime("this Saturday")).', '.strftime('%e %B %Y',  strtotime("second Saturday")).' atau '.strftime('%e %B %Y',  strtotime("third Saturday"));
				}else{		
					$selection_date = strftime('%e %B %Y',  strtotime("second Saturday")).', '.strftime('%e %B %Y',  strtotime("third Saturday")).' atau '.strftime('%e %B %Y',  strtotime("fourth Saturday"));
				}
    	     
    							
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
		    	/* //
		    	//get photo student
		    	$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
		    	$file = $uploadFileDb->getFile($transaction_id,51);
		    	
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
		    	}		 */	
				//once submmitted update status=CLOSE
	    		$upddata["at_status"]='PROCESS';
	    		//$upddata["at_intake"]=$IdIntake;
	    		$upddata["at_period"]=$idPeriod;
	    		$upddata["at_submit_date"]=date("Y-m-d H:i:s");
				$transDB->updateData($upddata,$transaction_id);	
							
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
	        	    '$[seleksi_date]'=>$selection_date,
					//'photo'=>$photo_url
				   // 'registration_date'=>$registrasi["StartDate"].' s.d '.$registrasi["EndDate"],
				   // 'withdrawal_date'=>$withdrawal["StartDate"].' s.d '.$withdrawal["EndDate"]
	        	    
	        	);
				global $matapelajaran;        	
		        $educationDB = new App_Model_Application_DbTable_ApplicantEducation();
		    	$matapelajaran = $educationDB->getEducationDetail($transaction_id);
				//echo var_dump($matapelajaran); 
				// ------- create PDF File section	--------	
		
		    	require_once 'dompdf_config.inc.php';
		    		
		    	$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		    	$autoloader->pushAutoloader('DOMPDF_autoload');
		    	
				//template path
				$html_template_path = DOCUMENT_PATH."/template/pssb_confirmation_letter.html";
				
				//filename
				$output_filename = $applicantID."_pssb_confirmation_letter.pdf";
				$filepath="/applicant/".date("mY")."/".$transaction_id;
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
				 
				//save file info
				$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
				$doc["ad_appl_id"]=$transaction_id;
				$doc["ad_type"]=$fileType;
				$doc["ad_filepath"]=$filepath;
				$doc["ad_filename"]=$output_filename;
				$doc["ad_createddt"]=date("Y-m-d");
				//echo var_dump($doc);exit;
				$documentDB->addData($doc);
                //------- end high school cert section ----------
			}
            elseif($admission_type==3)
            {
                //die;
                	
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
							
				//once submmitted update status=CLOSE
	    		$upddata["at_status"]='PROCESS';
	    		$upddata["at_intake"]=$IdIntake;
	    		$upddata["at_period"]=$idPeriod;
	    		$upddata["at_submit_date"]=date("Y-m-d H:i:s");
				
                //die;
                $transDB->updateData($upddata,$transaction_id);	
				
                $this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'notification'),'default',true));
                
                //------- end credit transfer section ----------
            }
			
			     					

			// --------- Send Email Section  ---------------
			$attachment_path = $output_directory_path.'/'.$output_filename;
			
			$templateDB = new App_Model_General_DbTable_EmailTemplate();
			
			if($admission_type==1){	// placement test
				$templateData = $templateDB->getData(6,$appl_prefer_lang);
			}else
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
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'notification'),'default',true));
					
    	}//end post data
    		
    		
	}
	
	
	public function confirmationAction(){
		/*
		 * check session for transaction
		*/
		$auth = Zend_Auth::getInstance();
		if($auth->getIdentity()->transaction_id==null){
			if($auth->getIdentity()->role=='agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}
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
		$this->view->profile=$applicant;
		//get transaction data
		$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
		$transData = $transDB->getTransactionData($transaction_id);
		$this->view->transaction = $transData;
	
		$admission_type = $transData["at_appl_type"];//1:placement test 2:high school 3:credit transfer
	
		 
		//------- checking section -------
		/**
		 * Check for all steps that involve in application
		 * 1.Admission Type
		 * 2.Placement Test Schedule (for USM only)
		 * 3.Programme
		 * 4.Documents
		 */
	
		//*** check admission type
		if(!isset($admission_type) || $admission_type==null || $admission_type=='' || $admission_type=='0'){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'admission','msg'=>$this->view->translate('Please select admission type')),'default',true));
		}
		 
		//*** check date for placement test
		if($admission_type==1){
			$ptestDb = new App_Model_Application_DbTable_ApplicantPtest();
			$ptestData = $ptestDb->getScheduleInfo($transaction_id);
				
				
			if(!$ptestData){
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'placement-test','msg'=>$this->view->translate('Please select schedule for placement test')),'default',true));
			}
		}
		 
		//*** check programme
		$applicantProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
		$applicantProgram = $applicantProgramDB->getPlacementProgram($transaction_id);
		$this->view->program=$applicantProgram;
		if(!$applicantProgram){
			if($admission_type==2){
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-highschool','msg'=>$this->view->translate('please_fill_in_program_prefered')),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme','msg'=>$this->view->translate('please_fill_in_program_prefered')),'default',true));
			}
		} else {
			if($admission_type==2 && count($applicantProgram)>1) {
				//delete secodn program
				$applicantProgramDB->deleteData($applicantProgram[1]['ap_id']);
				
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-highschool','msg'=>$this->view->translate('please_fill_in_program_prefered')),'default',true));
			}		
		}
		 
		//*** check document
		/*
		 * Disable check on other document execluding photo
		*/
	//transaction data
		$applicantTransactionDn = new App_Model_Application_DbTable_ApplicantTransaction();
		$transaction = $applicantTransactionDn->getTransactionData($transaction_id);
		$dbAppTest=new App_Model_Application_DbTable_ApplicantPtest();
			
		$this->view->transaction = $transaction;
		if ($transaction["at_appl_type"]=="2") $testCode='PSSB';
		else if ($transaction["at_appl_type"]=="3") $testCode='CREDIT';
		else if ($transaction["at_appl_type"]=="4") $testCode='INVITATION';
		else if ($transaction["at_appl_type"]=="5") $testCode='PORTOFOLIO';
		else if ($transaction["at_appl_type"]=="6") $testCode='SCHOLARSHIP';
		else if ($transaction["at_appl_type"]=="7") $testCode='UTBK';
		else {
			$ptest=$dbAppTest->getPtest($transaction_id);
			$testCode=$ptest['apt_ptest_code'];
		}
		
		
		$uploadFileDB = new App_Model_Application_DbTable_UploadFile();
		$getUploadData = $uploadFileDB->getUploadDataNew($transaction_id);
		$this->view->uploadeddata = $getUploadData;
		
		if (count($applicantProgram)==1) {
			$prog1=$applicantProgram[0]['program_id'];
			$prog2=null;
			$prog3=null;
			$prog4=null;
		}
		else if (count($applicantProgram)==2) {
			$prog1=$applicantProgram[0]['program_id'];
			$prog2=$applicantProgram[1]['program_id'];
			$prog3=null;
			$prog4=null;
		} else if (count($applicantProgram)==4 ) {
			$prog1=$applicantProgram[0]['program_id'];
			$prog2=$applicantProgram[1]['program_id'];
			$prog3=$applicantProgram[2]['program_id'];
			$prog4=$applicantProgram[3]['program_id'];
		}
			
		$document=new App_Model_Application_DbTable_DocumentPrerequisite();
		$documentlist=$document->getDataByProgram($testCode,$prog1,$prog2,$prog3,$prog4);
		foreach ($documentlist as $key=>$value) {
			foreach ($getUploadData as $up) {
		
				if ($value['IdDocument']==$up['auf_file_type']) unset($documentlist[$key]);
			}
		}
		
		if (count($documentlist)>0) {
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'uploaddocument','msg'=>$this->view->translate('please_upload_document.')),'default',true));
			
		}
		 
		//------- end checking section -------
		 
		 
		 
		if ($this->getRequest()->isPost()) {
	
			$formData = $this->getRequest()->getPost();
	
			 
	
			//----------- empty transaction id in session to avoid back button -------
			$auth = Zend_Auth::getInstance();
			$auth->getIdentity()->transaction_id = null;
	
			//get academic year
			$ayearDb = new App_Model_Record_DbTable_AcademicYear();
			$academic_year = $ayearDb->getNextAcademicYearData();
				
			 
			//get current intake
			//$intakeDB = new App_Model_Record_DbTable_Intake();
			//$intake = $intakeDB->getCurrentIntake();
			$IdIntake = $transData['at_intake'];//$intake["IdIntake"];
				
			//get period ->utk letak application ni pada period period yg mana berdasarkan application activity calendar
			/*$activityDB = new App_Model_Record_DbTable_ActivityCalender();
				$activity = $activityDB->getPeriodByActivity(29);//online application
			$idPeriod = $activity["IdPeriod"];*/
				
			 
			//get period
				
			//cek bulan dan tahun
			$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
			$period   = $periodDB->getCurrentPeriod(date("m"),date("Y"),$IdIntake);
			$idPeriod = $period["ap_id"];
				
			//echo 'period='.$transData["entry_type"];exit;
			$ptestinfo = new App_Model_Application_DbTable_ApplicantPtest();
			$applicant_placement_test_info = $ptestinfo->getScheduleInfo($transaction_id);
	
			//generate no peserta
			//manual entry already have no peserta
			if($transData["entry_type"]==2)
			{
				$applicantID = $transData["at_pes_id"];
						
				if($transData["at_appl_type"]==1){ //USM
			
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
						//$appprogramDB = new App_Model_Application_DbTable_ApplicantProgram();
							
						if($program_data["program_code2"]=="0"){
						$program_data["program_code2"] = $program_data["program_code1"];
					}
						
					$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();
					$applicant_ptest = $appProfileDB->viewkartu($transaction_id);
						
					$data = $appprogramDB->getProcedure($transaction_id,$program_data["program_code1"],$program_data["program_code2"],$applicant_ptest["apt_aps_id"]);
			
					if($data[0]["roomid"]==0){
					$error="Maaf tempat untuk USM telah penuh. Sila memilih lokasi ujian yang lain";
					$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'notification','msg'=>$error),'default',true));
			
					}
						
					//update transaction period based on ptest schedule
							//update ptest data
					$ptestinfo = new App_Model_Application_DbTable_ApplicantPtest();
					$applicant_placement_test_info = $ptestinfo->getScheduleInfo($transaction_id);
					$ptPeriod   = $periodDB->getCurrentPeriod(date("m",strtotime($applicant_placement_test_info['aps_test_date'])), date("Y",strtotime($applicant_placement_test_info['aps_test_date'])));
						
					//once submmitted update status prcess sebab da bayar masa amik form dari agent
					$upddata["at_status"]='PROCESS';
					//$upddata["at_academic_year"]=$academic_year["ay_id"];
					//$upddata["at_intake"]=$IdIntake;
					$upddata["at_period"]=$ptPeriod["ap_id"];
					$upddata["at_submit_date"] = date("Y-m-d H:i:s");
					 
					$transDB->updateData($upddata,$transaction_id);
							$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'viewkartu'),'default',true));
				}
			
			} else {	//online USM
						
	
					//kalau dah ada pes jgn mintak no pes lagi
					//check no pes
				if( !isset($transData["at_pes_id"]) && $transData["at_pes_id"]==null ){
			
							//to get and update applicantID
					$applicantID = $transDB->getApplicantID($transData["at_appl_type"],$transData["at_intake"],$applicant_placement_test_info['aps_placement_code']);//
					//echo $applicantID;exit;
					$data["at_pes_id"]=$applicantID;
	
					$transDB->updateData($data, $transData["at_trans_id"]);
	
				}else{
						
					$applicantID = $transData["at_pes_id"];
				}
				
			//	exit;
			}//end generate no peserta
            //die;
			//-------- placement test (USM) section -------------
			if($admission_type==1){
					 
					//update ptest data
					$ptestinfo = new App_Model_Application_DbTable_ApplicantPtest();
					$applicant_placement_test_info = $ptestinfo->getScheduleInfo($transaction_id);
					 
					$appptestDB = new App_Model_Application_DbTable_ApplicantPtest();
					$info["apt_bill_no"]=$applicantID;
					$appptestDB->updateData($info,$applicant_placement_test_info["apt_id"]);
					$ptPeriod   = $periodDB->getCurrentPeriod(date("m",strtotime($applicant_placement_test_info['aps_test_date'])), date("Y",strtotime($applicant_placement_test_info['aps_test_date'])),$transaction['at_intake']);
					
						//update transaction period based on ptest schedule
						//$ptPeriod   = $periodDB->getCurrentPeriod(date("m",strtotime($applicant_placement_test_info['aps_test_date'])), date("Y",strtotime($applicant_placement_test_info['aps_test_date'])),$IdIntake);
	
						//once submmitted update status=CLOSE
						$upddata["at_status"]='CLOSE';
						//$upddata["at_academic_year"]=$academic_year["ay_id"];
						//$upddata["at_intake"]=$IdIntake;
						$upddata["at_period"]=$ptPeriod["ap_id"];
						$upddata["at_submit_date"]=date("Y-m-d H:i:s");
	
						$transDB->updateData($upddata,$transaction_id);
	
						// buat tagihan ke BNI
						//1st:check how many program apply.
						$ptestDB = new App_Model_Application_DbTable_ApplicantProgram();
						$list_program = $ptestDB->getPlacementProgram($transaction_id);
						$total_program_apply = count($list_program);
						
						$feeDB = new App_Model_Application_DbTable_PlacementFeeSetup();
						$dbPlaceHead=new App_Model_Application_DbTable_PlacementTest();
						$head=$dbPlaceHead->getDataByCode($testCode);
						if ($head['aph_fees_location']== "1") $condition = array('type'=>'LOCATION','value'=>'','aptcode'=>$testCode);
						if ($head['aph_fees_program']== "1") $condition = array('type'=>'PROGRAM','value'=>$total_program_apply,'aptcode'=>$testCode);
						$fees_info = $feeDB->getFees($condition);
						$program_fee = $fees_info["apfs_amt"];
						//add 200.000 if prgram fk dan atau fkg
						$additional=0;
						foreach ($list_program as $prog) {
							
							if ($prog['ap_prog_code']=='0300' ||$prog['ap_prog_code']=='0400' ) 
								$additional=200000;
						}
						$program_fee=$program_fee+$additional;
						//insert into invoice and invoice detail
						$inv_data = array(
								'bill_number' => $applicantID,
								'appl_id' => $appl_id,
								'academic_year' => $academic_year,
								'semester' =>0,
								'no_fomulir' => $applicantID,
								'bill_amount' => $program_fee,
								'bill_paid' => 0.00,
								'bill_balance' => $program_fee,
								'bill_description' => 'Biaya Pendaftaran USM',
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
								'fee_item_description' => 'Biaya Pendaftaran USM',
								'amount' => $program_fee
						);
						$detail=$invoiceDetailDb->isIn($applicantID, 19);
						if (!$detail)
							$invoiceDetailDb->insert($inv_detail_data);
						
						else {
							$invoiceDetailDb->updateData($inv_detail_data, 'id='.$detail['id']);
						}
							
						
						/* $status = $this->generateBankValidationPDF($transaction_id);
	
						//save file info
						$output_directory_path = DOCUMENT_PATH."/applicant/".date("mY")."/".$transaction_id;
						$location_path = "applicant/".date("mY")."/".$transaction_id;
						$output_filename = $applicantID."_validasi_bank.pdf";
									
						$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
						$doc["ad_appl_id"]=$transaction_id;
						$doc["ad_type"]=32;
						$doc["ad_filepath"]=$location_path;
						$doc["ad_filename"]=$output_filename;
						$doc["ad_createddt"]=date("Y-m-d");
						$documentDB->addData($doc); */
	
					//bank validation id not printed 
						$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'push-e-collection','trxid'=>$transaction_id,'invoice'=>$applicantID),'default',true));
							
								 
		
			}elseif($admission_type==2){
	
						//-------- high school cert (PSSB) section -------------
		
						setlocale (LC_ALL, $locale);
	
				//filetype
							$fileType = 31;
	
							 
							//pengumumam hasil seleksi
							//0=sunday onwards
							$today = date("w");
	
							if($today<=2){
							$selection_date = strftime('%e %B %Y',  strtotime("this Saturday")).', '.strftime('%e %B %Y',  strtotime("second Saturday")).' atau '.strftime('%e %B %Y',  strtotime("third Saturday"));
							}else{
							$selection_date = strftime('%e %B %Y',  strtotime("second Saturday")).', '.strftime('%e %B %Y',  strtotime("third Saturday")).' atau '.strftime('%e %B %Y',  strtotime("fourth Saturday"));
							}
	
    		
							//get applicant program applied
							$programDB = new App_Model_Application_DbTable_ApplicantProgram();
							$app_program = $programDB->getPlacementProgram($transaction_id);
	
							$program_data["program_name1"]='';
							$program_data["program_name2"]='';
							//$program_data["program_name3"]='';
							//$program_data["program_name4"]='';
							$i=1;
							foreach($app_program as $program){
	
			    				if ($locale=="en_US"){
			    					$program_data["program_name".$i] = $program["program_name"];
								}else if ($locale=="id_ID"){
									$program_data["program_name".$i] = $program["program_name_indonesia"];
							}
	
		    		$i++;
							}
							/* //
							//get photo student
							$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
			    	$file = $uploadFileDb->getFile($transaction_id,51);
			    	 
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
			    				}		 */
			    				//once submmitted update status=CLOSE
			    				$upddata["at_status"]='PROCESS';
			    				//$upddata["at_intake"]=$IdIntake;
			    				$upddata["at_period"]=$idPeriod;
			    				$upddata["at_submit_date"]=date("Y-m-d H:i:s");
			    				$transDB->updateData($upddata,$transaction_id);
			    					
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
		        	    			'$[seleksi_date]'=>$selection_date,
		        	    				//'photo'=>$photo_url
		        	    				// 'registration_date'=>$registrasi["StartDate"].' s.d '.$registrasi["EndDate"],
		        	    				// 'withdrawal_date'=>$withdrawal["StartDate"].' s.d '.$withdrawal["EndDate"]
	
		        	    			);
		        	    			global $matapelajaran;
		        	    			$educationDB = new App_Model_Application_DbTable_ApplicantEducation();
		        	    			$matapelajaran = $educationDB->getEducationDetail($transaction_id);
		        	    										//echo var_dump($matapelajaran);
		        	    										// ------- create PDF File section	--------
	
		        	    			require_once 'dompdf_config.inc.php';
	
		        	    			$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		        	    			$autoloader->pushAutoloader('DOMPDF_autoload');
		        	    										 
		        	    										//template path
									$html_template_path = DOCUMENT_PATH."/template/pssb_confirmation_letter.html";
	
					//filename
									$output_filename = $applicantID."_pssb_confirmation_letter.pdf";
									$filepath="/applicant/".date("mY")."/".$transaction_id;
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
										
									//save file info
									$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
									$doc["ad_appl_id"]=$transaction_id;
									$doc["ad_type"]=$fileType;
									$doc["ad_filepath"]=$filepath;
									$doc["ad_filename"]=$output_filename;
									$doc["ad_createddt"]=date("Y-m-d");
									//echo var_dump($doc);exit;
									$documentDB->addData($doc);
								//------- end high school cert section ----------
							}
							elseif ($admission_type==3)
							{
									$fileType=75;
									//die;
							 		//----credit transfer
									//get applicant program applied
									$programDB = new App_Model_Application_DbTable_ApplicantProgram();
									$app_program = $programDB->getPlacementProgram($transaction_id);
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
										
									//once submmitted update status=CLOSE
									$upddata["at_status"]='PROCESS';
									$upddata["at_intake"]=$IdIntake;
									$upddata["at_period"]=$idPeriod;
											$upddata["at_submit_date"]=date("Y-m-d H:i:s");
					
										//die;
									$transDB->updateData($upddata,$transaction_id);
									//create application letter
									//pengumumam hasil seleksi
									//0=sunday onwards
									$today = date("w");
										
									if($today<=2){
										$selection_date = strftime('%e %B %Y',  strtotime("this Saturday")).', '.strftime('%e %B %Y',  strtotime("second Saturday")).' atau '.strftime('%e %B %Y',  strtotime("third Saturday"));
									}else{
										$selection_date = strftime('%e %B %Y',  strtotime("second Saturday")).', '.strftime('%e %B %Y',  strtotime("third Saturday")).' atau '.strftime('%e %B %Y',  strtotime("fourth Saturday"));
									}
										
										
									//get applicant program applied
									$programDB = new App_Model_Application_DbTable_ApplicantProgram();
									$app_program = $programDB->getPlacementProgram($transaction_id);
										
									 
									 //get photo student
									$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
									$file = $uploadFileDb->getFile($transaction_id,51);
									
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
									$upddata["at_status"]='PROCESS';
									//$upddata["at_intake"]=$IdIntake;
									$upddata["at_period"]=$idPeriod;
									$upddata["at_submit_date"]=date("Y-m-d H:i:s");
									$transDB->updateData($upddata,$transaction_id);
										
									if($applicant["appl_gender"]==1) $gender="LAKI-LAKI";
									if($applicant["appl_gender"]==2) $gender="PEREMPUAN";
									global $application;
									global $subjects;
									$application=$dbCredit->getDataByTransaction($transaction_id);
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
									$output_filename = $applicantID."_credittransfer_app_letter.pdf";
									$filepath="/applicant/".date("mY")."/".$transaction_id;
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
										
									//save file info
									$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
									$doc["ad_appl_id"]=$transaction_id;
									$doc["ad_type"]=$fileType;
									$doc["ad_filepath"]=$filepath;
									$doc["ad_filename"]=$output_filename;
									$doc["ad_createddt"]=date("Y-m-d");
									//echo var_dump($doc);exit;
									$documentDB->addData($doc);
									//-----------------
									$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'notification'),'default',true));
					
										//------- end credit transfer section ----------
										
								}
		
					 else if ($admission_type==4) {


					 	//-------- Invitation section -------------
					 	
					 	setlocale (LC_ALL, $locale);
					 	
					 	//filetype
					 	$fileType = 76;
					 	
					 	
					 	//pengumumam hasil seleksi
					 	//0=sunday onwards
					 	$today = date("w");
					 	
					 	if($today<=2){
					 		$selection_date = strftime('%e %B %Y',  strtotime("this Saturday")).', '.strftime('%e %B %Y',  strtotime("second Saturday")).' atau '.strftime('%e %B %Y',  strtotime("third Saturday"));
					 	}else{
					 		$selection_date = strftime('%e %B %Y',  strtotime("second Saturday")).', '.strftime('%e %B %Y',  strtotime("third Saturday")).' atau '.strftime('%e %B %Y',  strtotime("fourth Saturday"));
					 	}
					 	
					 	
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
					 	/* //
					 	 //get photo student
					 	$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
					 	$file = $uploadFileDb->getFile($transaction_id,51);
					 	 
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
					 	}		 */
					 	//once submmitted update status=CLOSE
					 	$upddata["at_status"]='PROCESS';
					 	//$upddata["at_intake"]=$IdIntake;
					 	$upddata["at_period"]=$idPeriod;
					 	$upddata["at_submit_date"]=date("Y-m-d H:i:s");
					 	$transDB->updateData($upddata,$transaction_id);
					 	
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
					 			'$[PROGRAM2]' => $program_data["program_name2"],
					 			'$[submission_date]'=>date('j M Y'),
					 			'$[ACADEMICYEAR]'=>$academic_year["ay_code"],
					 			'$[registration_date]'=>date('j M Y'),
					 			'$[withdrawal_date]'=>date('j M Y'),
					 			'$[seleksi_date]'=>$selection_date,
					 			//'photo'=>$photo_url
					 			// 'registration_date'=>$registrasi["StartDate"].' s.d '.$registrasi["EndDate"],
					 			// 'withdrawal_date'=>$withdrawal["StartDate"].' s.d '.$withdrawal["EndDate"]
					 	
					 	);
					 	global $matapelajaran;
					 	$educationDB = new App_Model_Application_DbTable_ApplicantEducation();
					 	$matapelajaran = $educationDB->getEducationDetail($transaction_id);
					 	//echo var_dump($matapelajaran);
					 	// ------- create PDF File section	--------
					 	
					 	require_once 'dompdf_config.inc.php';
					 	
					 	$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
					 	$autoloader->pushAutoloader('DOMPDF_autoload');
					 	
					 	//template path
					 	$html_template_path = DOCUMENT_PATH."/template/invitation_confirmation_letter.html";
					 	
					 	//filename
					 	$output_filename = $applicantID."_invitation_confirmation_letter.pdf";
					 	$filepath="/applicant/".date("mY")."/".$transaction_id;
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
					 	
					 	//save file info
					 	$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
					 	$doc["ad_appl_id"]=$transaction_id;
					 	$doc["ad_type"]=$fileType;
					 	$doc["ad_filepath"]=$filepath;
					 	$doc["ad_filename"]=$output_filename;
					 	$doc["ad_createddt"]=date("Y-m-d");
					 	//echo var_dump($doc);exit;
					 	$documentDB->addData($doc);
					 	//------- end high school cert section ----------
					 					
					 } else if ($admission_type==5) {


					 	//-------- Protfolio section -------------
					 	
					 	setlocale (LC_ALL, $locale);
					 	
					 	//filetype
					 	$fileType = 83;
					 	
					 	
					 	//pengumumam hasil seleksi
					 	//0=sunday onwards
					 	$today = date("w");
					 	
					 	if($today<=2){
					 		$selection_date = strftime('%e %B %Y',  strtotime("this Saturday")).', '.strftime('%e %B %Y',  strtotime("second Saturday")).' atau '.strftime('%e %B %Y',  strtotime("third Saturday"));
					 	}else{
					 		$selection_date = strftime('%e %B %Y',  strtotime("second Saturday")).', '.strftime('%e %B %Y',  strtotime("third Saturday")).' atau '.strftime('%e %B %Y',  strtotime("fourth Saturday"));
					 	}
					 	
					 	
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
					 	/* //
					 	 //get photo student
					 	$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
					 	$file = $uploadFileDb->getFile($transaction_id,51);
					 	 
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
					 	}		 */
					 	//once submmitted update status=CLOSE
					 	$upddata["at_status"]='PROCESS';
					 	//$upddata["at_intake"]=$IdIntake;
					 	$upddata["at_period"]=$idPeriod;
					 	$upddata["at_submit_date"]=date("Y-m-d H:i:s");
					 	$transDB->updateData($upddata,$transaction_id);
					 	
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
					 			//'$[PROGRAM2]' => $program_data["program_name2"],
					 			'$[submission_date]'=>date('j M Y'),
					 			'$[ACADEMICYEAR]'=>$academic_year["ay_code"],
					 			'$[registration_date]'=>date('j M Y'),
					 			'$[withdrawal_date]'=>date('j M Y'),
					 			'$[seleksi_date]'=>$selection_date,
					 			//'photo'=>$photo_url
					 			// 'registration_date'=>$registrasi["StartDate"].' s.d '.$registrasi["EndDate"],
					 			// 'withdrawal_date'=>$withdrawal["StartDate"].' s.d '.$withdrawal["EndDate"]
					 	
					 	);
					 	global $matapelajaran;
					 	$educationDB = new App_Model_Application_DbTable_ApplicantEducation();
					 	$matapelajaran = $educationDB->getEducationDetail($transaction_id);
					 	//echo var_dump($matapelajaran);
					 	// ------- create PDF File section	--------
					 	
					 	require_once 'dompdf_config.inc.php';
					 	
					 	$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
					 	$autoloader->pushAutoloader('DOMPDF_autoload');
					 	
					 	//template path
					 	$html_template_path = DOCUMENT_PATH."/template/portofolio_confirmation_letter.html";
					 	
					 	//filename
					 	$output_filename = $applicantID."_portofolio_confirmation_letter.pdf";
					 	$filepath="/applicant/".date("mY")."/".$transaction_id;
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
					 	
					 	//save file info
					 	$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
					 	$doc["ad_appl_id"]=$transaction_id;
					 	$doc["ad_type"]=$fileType;
					 	$doc["ad_filepath"]=$filepath;
					 	$doc["ad_filename"]=$output_filename;
					 	$doc["ad_createddt"]=date("Y-m-d");
					 	//echo var_dump($doc);exit;
					 	$documentDB->addData($doc);
					 	//------- end high school cert section ----------
					 					
					 } else if ($admission_type==6) {


					 	//-------- Scholarship section -------------
					 	
					 	setlocale (LC_ALL, $locale);
					 	
					 	//filetype
					 	$fileType = 84;
					 	
					 	
					 	//pengumumam hasil seleksi
					 	//0=sunday onwards
					 	$today = date("w");
					 	
					 	if($today<=2){
					 		$selection_date = strftime('%e %B %Y',  strtotime("this Saturday")).', '.strftime('%e %B %Y',  strtotime("second Saturday")).' atau '.strftime('%e %B %Y',  strtotime("third Saturday"));
					 	}else{
					 		$selection_date = strftime('%e %B %Y',  strtotime("second Saturday")).', '.strftime('%e %B %Y',  strtotime("third Saturday")).' atau '.strftime('%e %B %Y',  strtotime("fourth Saturday"));
					 	}
					 	
					 	
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
					 	/* //
					 	 //get photo student
					 	$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
					 	$file = $uploadFileDb->getFile($transaction_id,51);
					 	 
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
					 	}		 */
					 	//once submmitted update status=CLOSE
					 	$upddata["at_status"]='PROCESS';
					 	//$upddata["at_intake"]=$IdIntake;
					 	$upddata["at_period"]=$idPeriod;
					 	$upddata["at_submit_date"]=date("Y-m-d H:i:s");
					 	$transDB->updateData($upddata,$transaction_id);
					 	
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
					 			//'$[PROGRAM2]' => $program_data["program_name2"],
					 			'$[submission_date]'=>date('j M Y'),
					 			'$[ACADEMICYEAR]'=>$academic_year["ay_code"],
					 			'$[registration_date]'=>date('j M Y'),
					 			'$[withdrawal_date]'=>date('j M Y'),
					 			'$[seleksi_date]'=>$selection_date,
					 			//'photo'=>$photo_url
					 			// 'registration_date'=>$registrasi["StartDate"].' s.d '.$registrasi["EndDate"],
					 			// 'withdrawal_date'=>$withdrawal["StartDate"].' s.d '.$withdrawal["EndDate"]
					 	
					 	);
					 	global $matapelajaran;
					 	$educationDB = new App_Model_Application_DbTable_ApplicantEducation();
					 	$matapelajaran = $educationDB->getEducationDetail($transaction_id);
					 	//echo var_dump($matapelajaran);
					 	// ------- create PDF File section	--------
					 	
					 	require_once 'dompdf_config.inc.php';
					 	
					 	$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
					 	$autoloader->pushAutoloader('DOMPDF_autoload');
					 	
					 	//template path
					 	$html_template_path = DOCUMENT_PATH."/template/scholarship_confirmation_letter.html";
					 	
					 	//filename
					 	$output_filename = $applicantID."_scholarship_confirmation_letter.pdf";
					 	$filepath="/applicant/".date("mY")."/".$transaction_id;
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
					 	
					 	//save file info
					 	$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
					 	$doc["ad_appl_id"]=$transaction_id;
					 	$doc["ad_type"]=$fileType;
					 	$doc["ad_filepath"]=$filepath;
					 	$doc["ad_filename"]=$output_filename;
					 	$doc["ad_createddt"]=date("Y-m-d");
					 	//echo var_dump($doc);exit;
					 	$documentDB->addData($doc);
					 	//------- end high school cert section ----------
					 					
					 } else if ($admission_type==7){



					 	//-------- Invitation section -------------
					 		
					 	setlocale (LC_ALL, $locale);
					 		
					 	//filetype
					 	$fileType = 87;
					 		
					 		
					 	//pengumumam hasil seleksi
					 	//0=sunday onwards
					 	$today = date("w");
					 		
					 	if($today<=2){
					 		$selection_date = strftime('%e %B %Y',  strtotime("this Saturday")).', '.strftime('%e %B %Y',  strtotime("second Saturday")).' atau '.strftime('%e %B %Y',  strtotime("third Saturday"));
					 	}else{
					 		$selection_date = strftime('%e %B %Y',  strtotime("second Saturday")).', '.strftime('%e %B %Y',  strtotime("third Saturday")).' atau '.strftime('%e %B %Y',  strtotime("fourth Saturday"));
					 	}
					 		
					 		
					 	//get applicant program applied
					 	$programDB = new App_Model_Application_DbTable_ApplicantProgram();
					 	$app_program = $programDB->getPlacementProgram($transaction_id);
					 		
					 	$program_data["program_name1"]='';
					 	$program_data["program_name2"]='';
					 	$program_data["program_name3"]='';
					 	$program_data["program_name4"]='';
					 	$i=1;
					 	foreach($app_program as $program){
					 			
					 		if ($locale=="en_US"){
					 			$program_data["program_name".$i] = $program["program_name"];
					 		}else if ($locale=="id_ID"){
					 			$program_data["program_name".$i] = $program["program_name_indonesia"];
					 		}
					 			
					 		$i++;
					 	}
					 	/* //
					 	 //get photo student
					 	$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
					 	$file = $uploadFileDb->getFile($transaction_id,51);
					 	
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
					 	}		 */
					 	//once submmitted update status=CLOSE
					 	$upddata["at_status"]='PROCESS';
					 	//$upddata["at_intake"]=$IdIntake;
					 	$upddata["at_period"]=$idPeriod;
					 	$upddata["at_submit_date"]=date("Y-m-d H:i:s");
					 	$transDB->updateData($upddata,$transaction_id);
					 		
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
					 			'$[PROGRAM2]' => $program_data["program_name2"],
					 			'$[PROGRAM3]' => $program_data["program_name3"],
					 			'$[PROGRAM4]' => $program_data["program_name4"],
					 			'$[submission_date]'=>date('j M Y'),
					 			'$[ACADEMICYEAR]'=>$academic_year["ay_code"],
					 			'$[registration_date]'=>date('j M Y'),
					 			'$[withdrawal_date]'=>date('j M Y'),
					 			'$[seleksi_date]'=>$selection_date,
					 			//'photo'=>$photo_url
					 			// 'registration_date'=>$registrasi["StartDate"].' s.d '.$registrasi["EndDate"],
					 			// 'withdrawal_date'=>$withdrawal["StartDate"].' s.d '.$withdrawal["EndDate"]
					 				
					 	);
					 	global $matapelajaran;
					 	$educationDB = new App_Model_Application_DbTable_ApplicantEducation();
					 	
					 	$matapelajaran = $educationDB->getUTBKDetail($transaction_id);
					 	//echo var_dump($matapelajaran);
					 	// ------- create PDF File section	--------
					 		
					 	require_once 'dompdf_config.inc.php';
					 		
					 	$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
					 	$autoloader->pushAutoloader('DOMPDF_autoload');
					 		
					 	//template path
					 	$html_template_path = DOCUMENT_PATH."/template/utbk_confirmation_letter.html";
					 		
					 	//filename
					 	$output_filename = $applicantID."_utbk_confirmation_letter.pdf";
					 	$filepath="/applicant/".date("mY")."/".$transaction_id;
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
					 		
					 	//save file info
					 	$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
					 	$doc["ad_appl_id"]=$transaction_id;
					 	$doc["ad_type"]=$fileType;
					 	$doc["ad_filepath"]=$filepath;
					 	$doc["ad_filename"]=$output_filename;
					 	$doc["ad_createddt"]=date("Y-m-d");
					 	//echo var_dump($doc);exit;
					 	$documentDB->addData($doc);
					 	//------- end high school cert section ----------
					 						 
					 	
					 }
	
					// --------- Send Email Section  ---------------
					$attachment_path = $output_directory_path.'/'.$output_filename;
								
					$templateDB = new App_Model_General_DbTable_EmailTemplate();
								
					if($admission_type==1){	// placement test
						$templateData = $templateDB->getData(6,$appl_prefer_lang);
					}else
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
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'notification'),'default',true));
			
    	}//end post data
	
	
	}
	
	
	public function confirmationOldestAction(){
		/*
		 * check session for transaction
		*/
		$auth = Zend_Auth::getInstance();
		if($auth->getIdentity()->transaction_id==null){
			if($auth->getIdentity()->role=='agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}
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
	
		$admission_type = $transData["at_appl_type"];//1:placement test 2:high school 3:credit transfer
	
		 
		//------- checking section -------
		/**
		 * Check for all steps that involve in application
		 * 1.Admission Type
		 * 2.Placement Test Schedule (for USM only)
		 * 3.Programme
		 * 4.Documents
		 */
	
		//*** check admission type
		if(!isset($admission_type) || $admission_type==null || $admission_type=='' || $admission_type=='0'){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'admission','msg'=>$this->view->translate('Please select admission type')),'default',true));
		}
		 
		//*** check date for placement test
		if($admission_type==1){
			$ptestDb = new App_Model_Application_DbTable_ApplicantPtest();
			$ptestData = $ptestDb->getScheduleInfo($transaction_id);
				
			if(!$ptestData){
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'placement-test','msg'=>$this->view->translate('Please select schedule for placement test')),'default',true));
			}
		}
		 
		//*** check programme
		$applicantProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
		$applicantProgram = $applicantProgramDB->getPlacementProgram($transaction_id);
		 
		if(!$applicantProgram){
			if($admission_type==2){
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-highschool','msg'=>$this->view->translate('please_fill_in_program_prefered')),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme','msg'=>$this->view->translate('please_fill_in_program_prefered')),'default',true));
			}
		}
		 
		//*** check document
		/*
		 * Disable check on other document execluding photo
		*/
		$fileDB =new App_Model_Application_DbTable_UploadFile();
		$photo = $fileDB->getFile($transaction_id,33); //photo
		 
		/*$nric = $fileDB->getFile($transaction_id,34); //nric
		 $transcript = $fileDB->getFile($transaction_id,36); //Medical Report*/
		 
		if(($admission_type==2) || ($admission_type==3)){
			$raport = $fileDB->getFile($transaction_id,37); //Raport/transcript
		}else{
			$raport="x";
		}
		 
		if((!$photo) || (!$raport)){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'uploaddocument','msg'=>$this->view->translate('please_upload_document.')),'default',true));
		}
		//------- end checking section -------
		 
		 
		 
		if ($this->getRequest()->isPost()) {
	
			$formData = $this->getRequest()->getPost();
	
			 
	
			//----------- empty transaction id in session to avoid back button -------
			$auth = Zend_Auth::getInstance();
			$auth->getIdentity()->transaction_id = null;
	
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
			$period   = $periodDB->getCurrentPeriod(date("m"),date("Y"));
			$idPeriod = $period["ap_id"];
				
					//echo 'mati atas';die;
					//generate no peserta
					//manual entry already have no peserta
					if($transData["entry_type"]==2)
					{
							$applicantID = $transData["at_pes_id"];
								
							if($transData["at_appl_type"]==1){ //USM
	
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
							$error="Maaf tempat untuk USM telah penuh. Sila memilih lokasi ujian yang lain";
					$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'notification','msg'=>$error),'default',true));
	
					}
			
					//update transaction period based on ptest schedule
					//update ptest data
					$ptestinfo = new App_Model_Application_DbTable_ApplicantPtest();
					$applicant_placement_test_info = $ptestinfo->getScheduleInfo($transaction_id);
					$ptPeriod   = $periodDB->getCurrentPeriod(date("m",strtotime($applicant_placement_test_info['aps_test_date'])), date("Y",strtotime($applicant_placement_test_info['aps_test_date'])));
						
								//once submmitted update status prcess sebab da bayar masa amik form dari agent
								$upddata["at_status"]='PROCESS';
								$upddata["at_academic_year"]=$academic_year["ay_id"];
								$upddata["at_intake"]=$IdIntake;
								$upddata["at_period"]=$ptPeriod["ap_id"];
								$upddata["at_submit_date"] = date("Y-m-d H:i:s");
								 
								$transDB->updateData($upddata,$transaction_id);
								$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'viewkartu'),'default',true));
					}
						
					}
					else
					{	//online USM
						
					//echo 'lain';die;
				//kalau dah ada pes jgn mintak no pes lagi
				//check no pes
					if( !isset($transData["at_pes_id"]) && $transData["at_pes_id"]==null ){
							
						//to get and update applicantID
						$applicantID = $transDB->getApplicantID($transData["at_appl_type"],$transData["at_intake"]);//
						$data["at_pes_id"]=$applicantID;
	
						$transDB->updateData($data, $transData["at_trans_id"]);
	
						}else{
							
						$applicantID = $transData["at_pes_id"];
						}
	
						}//end generate no peserta
						//die;
						//-------- placement test (USM) section -------------
			if($admission_type==1){
				   
						//update ptest data
							$ptestinfo = new App_Model_Application_DbTable_ApplicantPtest();
							$applicant_placement_test_info = $ptestinfo->getScheduleInfo($transaction_id);
							 
							$appptestDB = new App_Model_Application_DbTable_ApplicantPtest();
								$info["apt_bill_no"]=$applicantID;
								$appptestDB->updateData($info,$applicant_placement_test_info["apt_id"]);
	
								//update transaction period based on ptest schedule
								$ptPeriod   = $periodDB->getCurrentPeriod(date("m",strtotime($applicant_placement_test_info['aps_test_date'])), date("Y",strtotime($applicant_placement_test_info['aps_test_date'])));
	
					//once submmitted update status=CLOSE
					$upddata["at_status"]='CLOSE';
					$upddata["at_academic_year"]=$academic_year["ay_id"];
					$upddata["at_intake"]=$IdIntake;
					$upddata["at_period"]=$ptPeriod["ap_id"];
							$upddata["at_submit_date"]=date("Y-m-d H:i:s");
	
							$transDB->updateData($upddata,$transaction_id);
	
									$status = $this->generateBankValidationPDF($transaction_id);
	
							//save file info
							$output_directory_path = DOCUMENT_PATH."/applicant/".date("mY")."/".$transaction_id;
							$location_path = "applicant/".date("mY")."/".$transaction_id;
							$output_filename = $applicantID."_validasi_bank.pdf";
								
							$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
							$doc["ad_appl_id"]=$transaction_id;
				$doc["ad_type"]=32;
					$doc["ad_filepath"]=$location_path;
					$doc["ad_filename"]=$output_filename;
							$doc["ad_createddt"]=date("Y-m-d");
							$documentDB->addData($doc);
	
							//generate USM Charges invoice
							if( $transData["entry_type"]==0 ){
							$data_invoice = array(
							'bill_number' => $transData['at_pes_id'],
							'appl_id' => $transData['at_appl_id'],
							'no_fomulir' =>$transData['at_pes_id'],
							'academic_year' => $transData['at_academic_year'],
							'bill_amount' =>$applicant_placement_test_info['apt_fee_amt'],
							'bill_paid'=>0,
							'bill_balance'=>$applicant_placement_test_info['apt_fee_amt'],
							'bill_description' => 'USM Charges',
									'college_id' => 0,
											'program_code' => '0000',
											'date_create' => date('Y-m-d H:i:s'),
						'creator' => '-1',
							'fs_id' => 0,
						'fsp_id' => 0,
						'status' =>'A'
					);
										
							
							$invoiceMainDb = new Studentfinance_Model_DbTable_InvoiceMain();
							$invoiceMainDb->insert($data_invoice);
							}
								
							}elseif($admission_type==2){
	
									//-------- high school cert (PSSB) section -------------
										
									setlocale (LC_ALL, $locale);
	
									//filetype
									$fileType = 31;
	
									//template path
										$filepath = DOCUMENT_PATH."/template/pssb_confirmation_letter.docx";
	
										//filename
										$output_filename = $applicantID."_pssb_confirmation_letter.pdf";
	
										//pengumumam hasil seleksi
										//0=sunday onwards
			    $today = date("w");
	
												if($today<=2){
												$selection_date = strftime('%e %B %Y',  strtotime("this Saturday")).', '.strftime('%e %B %Y',  strtotime("second Saturday")).' atau '.strftime('%e %B %Y',  strtotime("third Saturday"));
									}else{
										$selection_date = strftime('%e %B %Y',  strtotime("second Saturday")).', '.strftime('%e %B %Y',  strtotime("third Saturday")).' atau '.strftime('%e %B %Y',  strtotime("fourth Saturday"));
									}
	
										
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
													//------- end high school cert section ----------
	}
	elseif($admission_type==3)
	{
	//die;
	 
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
						
					//once submmitted update status=CLOSE
					$upddata["at_status"]='PROCESS';
					$upddata["at_intake"]=$IdIntake;
					$upddata["at_period"]=$idPeriod;
					$upddata["at_submit_date"]=date("Y-m-d H:i:s");
	
					//die;
					$transDB->updateData($upddata,$transaction_id);
	
							$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'notification'),'default',true));
	
							//------- end credit transfer section ----------
	}
		
	 
	
	// --------- Send Email Section  ---------------
			$attachment_path = $output_directory_path.'/'.$output_filename;
								
							$templateDB = new App_Model_General_DbTable_EmailTemplate();
								
							if($admission_type==1){	// placement test
							$templateData = $templateDB->getData(6,$appl_prefer_lang);
	}else
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
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'notification'),'default',true));
			
	}//end post data
	
	
	}
	
	
	
	
	public function notificationAction(){
		
		$auth = Zend_Auth::getInstance();
		if($auth->getIdentity()->transaction_id==null){
			if($auth->getIdentity()->role=='agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}
		}
		
		/*$storage = new Zend_Auth_Storage_Session();
        	$storage->clear();*/
		
		/*
		 * clear agent session on txn and appl to prevent back in browser
		 */
		if($auth->getIdentity()->role=='agent'){
			$auth->getIdentity()->appl_id = null;
			$auth->getIdentity()->appl_prefer_lang = null;
		}
		
		$auth->getIdentity()->transaction_id = null;
		/*** 
		 * End Clear Session
		 */
		
		$msg = $this->_getParam('msg',null); 		
		if($msg){
			$this->view->noticeError = $msg;
		}else{        
    	    $this->view->notice = $this->view->translate('confirmation_notice')."<br />". $this->view->translate('thank_you');
		}
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
		//echo 'here';exit;
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
			if($auth->getIdentity()->role=='agent'){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
			}else{
				$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
			}	
		}
		
		$msg = $this->_getParam('msg',null); 		
		$this->view->noticeError = $msg;
		
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
					
					//check date USM dah lepas atau belum kalau belum force tukar tarikh
					$pdb = new App_Model_Application_DbTable_ApplicantPtest();
					$sched = $pdb->getScheduleInfo($transaction_id);
					
					$testcode=$sched['apt_ptest_code'];
					
					$dbPlacementHead=new App_Model_Application_DbTable_PlacementTest();
					$head=$dbPlacementHead->getDataByCode($testcode);
					if (!$head) $testcode=null;
					else if ($head['level_kkni']=="6") $testcode=null;
					
					if(strtotime($sched["aps_test_date"])<time()){
						$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'change-date','id'=>$transaction_id ,'from'=>'verify'),'default',true));
						exit;
					}
					
					//end check tarikh
					
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
				    	$program_data["program_name2"]="";
				    	$program_data["faculty_name2"]="";
				    	
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
						//echo $transaction_id.",".$program_data["program_code1"].",".$program_data["program_code2"].",".$applicant["apt_aps_id"].",".$testcode;
						//exit;
						
						$data = $appprogramDB->getProcedure($transaction_id,$program_data["program_code1"],$program_data["program_code2"],$applicant["apt_aps_id"],$testcode);
						
						if($data[0]["roomid"]==0){
							$error="Maaf tempat untuk USM telah penuh. Sila hubungi pihak manajemen universitas";
							$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'verification','msg'=>$error),'default',true));
							exit;
						}
						
						//once submmitted update status=PTOCESS
						$upddata["at_status"]='PROCESS';
		    			$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
						$transDB->updateData($upddata,$transaction_id);	

						//generate No Pes
						$ptestDB = new App_Model_Application_DbTable_ApplicantPtest();
						$ptestDB->generateNoPes($transaction_id);
						
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
    
	public function createUsmCardAction(){
		/*
		 * check session for transaction
		*/
		$transaction_id=$this->_getParam('id', 0);
		$auth = Zend_Auth::getInstance();
		//cek for pembayaran uang pendaftaran
		$dbInvoice=new Studentfinance_Model_DbTable_InvoiceMain();
		$dbTransaction=new App_Model_Application_DbTable_ApplicantTransaction();
		$transaction=$dbTransaction->getDataById($transaction_id);
		
		$pesid=$transaction['at_pes_id'];
		$payment=$dbInvoice->getInvoiceDataByFormulir($pesid,'Pendaftaran');
		if ($payment['status_va']!='P') 
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'view-payment','id'=>$pesid,'trxid'=>$transaction_id),'default',true));
			
		$msg = $this->_getParam('msg',null);
		$this->view->noticeError = $msg;
	
		$this->view->title = $this->view->translate("verify_bank_pin");
	
		$auth = Zend_Auth::getInstance();
		$appl_id = $auth->getIdentity()->appl_id;
		$this->view->appl_id = $appl_id;
		 
		$transaction_id = $transaction['at_trans_id'];
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
		 
		 
					
				$billing_no = $pesid; 
					
				$profileDB = new App_Model_Application_DbTable_ApplicantProfile();
				$applicant = $profileDB->getTransProfile($appl_id,$transaction_id);
					
				//check date USM dah lepas atau belum kalau belum force tukar tarikh
				$pdb = new App_Model_Application_DbTable_ApplicantPtest();
				$sched = $pdb->getScheduleInfo($transaction_id);
					
				$testcode=$sched['apt_ptest_code'];
					
				$dbPlacementHead=new App_Model_Application_DbTable_PlacementTest();
				$head=$dbPlacementHead->getDataByCode($testcode);
				if (!$head) $testcode=null;
				else if ($head['level_kkni']=="6") $testcode=null;
					
				if(strtotime($sched["aps_test_date"])<time()){
					$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'change-date','id'=>$transaction_id ,'from'=>'verify'),'default',true));
					exit;
				}
					
				//end check tarikh
					
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
					$program_data["program_name2"]="";
					$program_data["faculty_name2"]="";
					 
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
					//echo $transaction_id.",".$program_data["program_code1"].",".$program_data["program_code2"].",".$applicant["schedule_id"].",".$testcode;
					//exit;
	
					$data = $appprogramDB->getProcedure($transaction_id,$program_data["program_code1"],$program_data["program_code2"],$applicant["schedule_id"],$testcode);
					//echo var_dump($data);exit;
					if($data[0]["roomid"]==0){
						$error="Maaf tempat untuk USM telah penuh. Sila hubungi pihak manajemen universitas";
						$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'verification','msg'=>$error),'default',true));
						exit;
					}
	
					//once submmitted update status=PTOCESS
					$upddata["at_status"]='PROCESS';
					$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
					$transDB->updateData($upddata,$transaction_id);
	
					//generate No Pes
					$ptestDB = new App_Model_Application_DbTable_ApplicantPtest();
					$ptestDB->generateNoPes($transaction_id);
	
						
				}// else $this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
				$this->_redirect('http://www.print.trisakti.ac.id/online-application/viewkartu/appl_id/'.$appl_id.'/transaction_id/'.$transaction_id);
					
			 
		 
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
				
                /*
                 * Add By Jasdy
                 * Generate verification key
                 */ 
                $verifyKey = '';
                $keys = array_merge(range(0, 9), range('a', 'z'));
                
                for ($i = 0; $i < 20; $i++) {
                    $verifyKey .= $keys[array_rand($keys)];
                }
                
                $salt = 'trisakti';
                $beforeHash = time().$verifyKey.$salt;
                $verifyKey = md5($beforeHash);
                $info["verifyKey"] = $verifyKey;
				/*
                 * End Generate Key
                 */ 
                $url = $this->view->url(array('action'=>'index'),'default',true);
                $path = APP_HOSTNAME.'/authentication/verify/key/'.$info["verifyKey"];
                $verifyLink = "<a href=\"".$path."\">".$path."</a>";
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
				$templateMail = str_replace("[Verify]",$verifyLink,$templateMail);
				//$templateMail = str_replace("[LAST_NAME]",$formData["appl_lname"],$templateMail);
				
				
				$sent = $this->sendMail($formData["appl_email"],$formData["appl_fname"],$templateData['subject'],$templateMail); 
				
				//display nanti alif cantikkan2
				
                
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
		//exit;

    }
    public function ajaxGetStatePdptAction($id=null){
    
    	$storage = new Zend_Auth_Storage_Session ();
    	$data = $storage->read ();
    	if (! $data) {
    		$this->_redirect ( 'index/index' );
    	}
    		
    	$country_id = $this->_getParam('country_id', 0);
    	 
    	//if ($this->getRequest()->isXmlHttpRequest()) {
    		$this->_helper->layout->disableLayout();
    	//}
    
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    
    	//get course
    	$stateDB = new App_Model_Application_DbTable_Wilayah();
    	$state_list = $stateDB->getByWilayah($country_id);
    	 
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    
    	$json = Zend_Json::encode($state_list);
    	$this->view->json = $json;
    	echo $json;
    	exit;
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
	$select_aps = $this->_getParam('select_aps', 0);

    	$select_date = $this->_getParam('select_date', 0);
    	$select_time = $this->_getParam('select_time', 0);
     
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
        $applicantPlacementScheduleDB = new App_Model_Application_DbTable_ApplicantPlacementSchedule();
    	$location_list = $applicantPlacementScheduleDB->getLocationByDate($select_date,$select_time,$select_aps);
    	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($location_list);
		echo $json;
		exit();

    }
    
    
    public function ajaxGetDateAction(){
    
    	$storage = new Zend_Auth_Storage_Session ();
    	$data = $storage->read ();
    	if (! $data) {
    		$this->_redirect ( 'index/index' );
    	}
    		
    	$placementcode = $this->_getParam('placement_code', 0);
    	$applid=$this->_getParam('applid', 0);
    	$transactionid=$this->_getParam('transactionid', 0);
    	
    	if ($this->getRequest()->isXmlHttpRequest()) {
    		$this->_helper->layout->disableLayout();
    	}
    
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    
    	$applicantPlacementScheduleDB = new App_Model_Application_DbTable_ApplicantPlacementSchedule();
    	$datelist = $applicantPlacementScheduleDB->getAvailableDate($applid,$transactionid,0,$placementcode);
    	 
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    
    	$json = Zend_Json::encode($datelist);
    	echo $json;
		exit();
    
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
    		$condition = array('type'=>'PROGRAM','value'=>$total_program_apply,'aptcode'=>$code);
    		//echo var_dump($condition);exit;
    		$fees_info = $feeDB->getFees($condition);
    		$program_fee = $fees_info["apfs_amt"];
    	}
    	
		//get Fees by location
    	if($location==1){
    		
    		//1st:check where is the location.    		
    		$location_id = $lid;
    		
    		$feeDB = new App_Model_Application_DbTable_PlacementFeeSetup();
    		$condition = array('type'=>'LOCATION','value'=>$location_id,'aptcode'=>$code);
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
    
    
	public function ajaxGetCityPdptAction($id=null){

	 	$storage = new Zend_Auth_Storage_Session ();
		$data = $storage->read ();
		if (! $data) {
			$this->_redirect ( 'index/index' );
		}
			
    	$state_id = $this->_getParam('state_id', "");
    	
     
     	//if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
       // }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
       $cityDb = new App_Model_Application_DbTable_Wilayah();
       $list_city = $cityDb->getByWilayah($state_id);
       
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($list_city);
		$this->view->json = $json;
		echo $json;
		exit();

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
    
    
    
 	public function viewPaymentAction(){
 		$this->view->title='Uang Pendaftaran Ujian Saringan Masuk Usakti';
 		$invoice=$this->_getParam('id', 0);
 		$this->view->transactionid=$this->_getParam('trxid', 0);
 		$dbInvoice=new Studentfinance_Model_DbTable_InvoiceMain();
 		$this->view->invoice=$dbInvoice->getInvoiceDataByFormulir($invoice);
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
		
		//----------- empty transaction id in session to avoid back button -------
		$auth = Zend_Auth::getInstance(); 
		$auth->getIdentity()->transaction_id = null;
 
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
		    	$filepath = DOCUMENT_PATH."/template/kartu.htmml";   

		    					
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
				
												
				//$location_path
			    $location_path = "applicant/".$monthyearfolder."/".$transaction_id;	
								
				
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

    			//return $status;				
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
    
    private function generateBankValidationPDF($txnId){
		$status = false;
    	
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
    	//filetype	
		$fileType = 32;
	
		
		
		//get transaction data
    	$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
    	$txnData = $transDB->getTransaction($txnId);
		
		//get applicant profile
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($txnData['appl_id'],$txnId);
		
		//get ptest & schedule
		$ptestDb = new App_Model_Application_DbTable_ApplicantPtest();
		$ptestData = $ptestDb->getScheduleInfo($txnId);
		
		//filename
		$output_filename = $txnData['at_pes_id']."_validasi_bank.pdf";
		    		
		//get applicant parents info
		$appFamilyDB = new App_Model_Application_DbTable_ApplicantFamily();
		$applicant_family = $appFamilyDB->fetchdata($txnData['appl_id'],21); //nak cari mother info   
		    		
    	//get applicant program applied
		$programDB = new App_Model_Application_DbTable_ApplicantProgram();
		$app_program = $programDB->getPlacementProgram($txnId);	

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
    	
		//get education
    	$applicationEducationDb = new App_Model_Application_DbTable_ApplicantEducation();
    	$applicationEducationData = $applicationEducationDb->getDataSchool($applicant['appl_id'],$txnId);
    	
		//CREATE PDF USING DOMPDF
		//field
		$fieldValues = array (
							     '$[BILLNO]' => $txnData['at_pes_id'],  							    
							     '$[FEE]' => number_format($ptestData["apt_fee_amt"], 2, '.', ','),
							 	 '$[APPLICANT_NAME]' => $applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
							     '$[ADDRESS1]' => $applicant["appl_address1"],
							     '$[ADDRESS2]' => $applicant["appl_address2"],
								 '$[MOBILE_NUMBER]' => $applicant["appl_phone_hp"], 
								 '$[DOB]' => $applicant["appl_dob"],
								 '$[EMAIL]' => $applicant["appl_email"],
								 '$[MOTHER_NAME]' => $applicant_family["af_name"],
								 '$[SCHOOL_DISCIPLINE]' => $applicationEducationData["smd_desc"],//jurusan yg dipilih
							     '$[TEST_DATE]' => date('j M Y',strtotime($ptestData["aps_test_date"])),
							     '$[LOCATION]' => $ptestData["al_location_name"],
							     '$[PROGRAM1]' => $program_data["program_name1"],
							     '$[PROGRAM2]' => $program_data["program_name2"]
							);
									
		
								
    	// ------- create PDF File section	--------   
		try{
			require_once 'dompdf_config.inc.php';
			
			$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
			$autoloader->pushAutoloader('DOMPDF_autoload');
			
			//template path	 
			$html_template_path = DOCUMENT_PATH."/template/validasi_bank.html";
			
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
			
			//$location_path
			$location_path = "applicant/".date("mY")."/".$txnId;
			
			//output_directory_path
			$output_directory_path = DOCUMENT_PATH."/".$location_path;
			
			//create directory to locate file			
			if (!is_dir($output_directory_path)) {
		    	mkdir($output_directory_path, 0775);
			}
			
			//output filename 
			$output_filename = $txnData['at_pes_id']."_validasi_bank.pdf";
			
			//to rename output file			
			$output_file_path = $output_directory_path."/".$output_filename;
	
			file_put_contents($output_file_path, $dompdf);
					
			// ------- End PDF File section	--------
			
			$status = true;
		
		}catch (Exception $e) {
			$status = false;	
		}

    	return $status;
    }
    
 	private function viewkartuOldAction(){
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
		
		//----------- empty transaction id in session to avoid back button -------
		$auth = Zend_Auth::getInstance(); 
		$auth->getIdentity()->transaction_id = null;
 
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
    
    public function viewlulus1Action(){
     	
		$auth = Zend_Auth::getInstance(); 
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));	
		}
		
		$this->view->title = $this->view->translate("Lulus Tahap Pertama");		
		
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
		
		//----------- empty transaction id in session to avoid back button -------
		$auth = Zend_Auth::getInstance(); 
		$auth->getIdentity()->transaction_id = null;
 
		if($applicant){
			
			//print_r($applicant);exit;
			$billing_no=$applicant["billing_no"];
			$pin_no = $applicant["REGISTER_NO"];
			$applicantID = $applicant["at_pes_id"];
					
			$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
	    	$document = $documentDB->getData($transaction_id,40); //Lulus 1
	    	//$document=false;
	    	
	    	if(!$document){
    		
  
				$fieldValues = array (
				     '$[NO_PES]' => $applicantID, 
				 	 '$[APPLICANT_NAME]' => $applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
				     '$[ADDRESS]' => $applicant["appl_address1"]."<br>".$applicant["appl_address2"],
					 '$[FACULTY]' => $program_data["faculty_name1"], 
					 '$[PROGRAME]' => $program_data["program_name1"],
					 '$[INTAKE]' => $program_data["program_name1"],
					 '$[PERIOD]' => $program_data["program_name1"],
					 '$[RECTOR_DATE]' => $program_data["program_name1"],
				
				);		    	
				//print_r($fieldValues);exit;
				$monthyearfolder=date("mY");

				
		    					
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
				
												
				//$location_path
			    $location_path = "applicant/".$monthyearfolder."/".$transaction_id;	
								
				
				//filename
				$output_filename = $applicantID."_lulus1.pdf";
									
		    	
				// ------- create PDF File section	--------   
				try{
					require_once 'dompdf_config.inc.php';
					
					$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
					$autoloader->pushAutoloader('DOMPDF_autoload');
					
					//template path	 
					$html_template_path = DOCUMENT_PATH."/template/Lulu1Letter.html";
					
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

    			//return $status;				
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
		}
    }
        
    public function programmeCredittransferAction()
    {
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
  
		$msg = $this->_getParam('msg', null);
		if($msg){
			$this->view->noticeError = $msg;	
		}
		
    	//transaction data
    	$applicantTransactionDn = new App_Model_Application_DbTable_ApplicantTransaction();
    	$transaction = $applicantTransactionDn->getTransactionData($transaction_id);
    	$this->view->transaction = $transaction;
    	
    	//applicant profile data
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($auth->getIdentity()->appl_id);
    	$this->view->applicant = $applicant;
    	$dbApply=new App_Model_Application_DbTable_CreditTransfer();
    	$dbAppySubject=new App_Model_Application_DbTable_CreditTransferSubject();
    	 
    	if( $transaction['at_appl_type'] == 1 ){
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme'),'default',true));
    			
    	}
    	elseif ($transaction['at_appl_type'] == 2)
    	{
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-highschool'),'default',true));
    	} elseif($transaction['at_appl_type'] == 4){
    		/*CREDIT TRANSFER*/
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-invitation'),'default',true));
    	} elseif($transaction['at_appl_type'] == 5){
        	/*CREDIT TRANSFER*/
        	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-portfolio'),'default',true));
        } elseif($transaction['at_appl_type'] == 6){
        	/*CREDIT TRANSFER*/
        	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'programme-scholar'),'default',true));
        }
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		$intake=$formData['intake_id'];
    		$dbIntake=new App_Model_Record_DbTable_IntakeAcademicYear();
    		$yearacad=$dbIntake->getDataByIntake($intake);
    		if ($yearacad)
    			$yearacad=$yearacad['acad_year'];
    		else $yearacad=0;
    			
    		$placementDB = new App_Model_Record_DbTable_PlacementHead();
    		$applicantTransactionDn->updateData(array('at_intake'=>$intake,'at_academic_year'=>$yearacad), $transaction_id);
    		
    		$subjectcode=$formData['subjectcode'];
    		$subjectnames=$formData['subjectname'];
    		$skss=$formData['sks'];
    		$grades=$formData['grade'];
    		$row=$dbApply->isIn($formData['transactionId']);
    		if ($row) $idapply=$row['idApply']; else $idapply=0;
	    	if ($formData['nim_asal']!='') {
	    		$data=array( 'PT_Asal'=>$formData['pt_asal'], 
	    				'Prodi_Asal'=>$formData['prodi_asal'],
	    				'nim_asal'=>$formData['nim_asal'],
	    				'transaction_id'=>$formData['transactionId'],
	    				'dt_entry'=>date('Y-m-d h:s:i'),
	    				'user_id'=>$auth->getIdentity()->appl_id,
	    				'IdProgram'=>$formData['ap_prog_code']);
	    		
	    		if ($row) {
	    			///update
	    			$dbApply->updateData($data, $row['idApply']);
	    			$idapply=$row['idApply'];
	    		} else {
	    			//add
	    			$idapply=$dbApply->addData($data);
	    		}
	    		//appply program
	    		//delete ptest
	    		$applicantProgram=new App_Model_Application_DbTable_ApplicantProgram();
	    		$applicantProgram->deleteTransactionData($transaction['at_trans_id']);
	    		$dbProgram=new App_Model_General_DbTable_Program();
	    		$prog=$dbProgram->getData($formData['ap_prog_code']);
	    		
	    		$dbbranch=new App_Model_General_DbTable_Branchofficevenue();
	    		$branch=$dbbranch->getData($formData['grouppssb']);
	    		//add ptest program prefered 1
	    		$data1 = array(
	    				'ap_at_trans_id' =>$transaction['at_trans_id'],
	    				'ap_prog_code' => $prog['ProgramCode'],
	    				'ap_preference' =>1,
	    				'IdProgramBranch'=>$formData['grouppssb'],
	    				'IdBranch'=>$branch['IdBranch']
	    		);
	    		 
	    		//checking for selected programme
	    		if( isset($data1['ap_prog_code']) && $data1['ap_prog_code']!=null && $data1['ap_prog_code']!="" && $data1['ap_prog_code']!=0 ){
	    			$row=$applicantProgram->IsIn($transaction['at_trans_id'], '2');
	    			if (!$row)
	    				$applicantProgram->insert($data1);
	    			else
	    				$applicantProgram->updateData($data1, $row['ap_id']);
	    			 
	    		}else{
	    			$this->view->noticeError = $this->translate("Silalah pilih program studi");
	    		
	    			 
	    		}
	    	}
	    	
	    	if ($idapply!=0) {
	    		foreach ($subjectcode as $key=>$value) {
	    			$subject=$subjectnames[$key];
	    			$sks = $skss[$key];
	    			$grade=$grades[$key];
	    			$data=array('SubjectCode'=>$value,
	    						'SubjectName'=>$subject, 
	    						'sks'=>$sks,
	    						'Grade'=>$grade,
	    						'dt_entry'=>date('Y-m-d h:s:i'),
	    						'idApply'=>$idapply);
	    			$row=$dbAppySubject->isIn($idapply, $value);
	    			if ($row) {
	    				//update
	    				$dbAppySubject->updateData($data, $row['idCTSubject']);
	    			} else {
	    				//insert
	    				$dbAppySubject->addData($data);
	    			}
	    			
	    		}
	    		if (isset($formData['drop'])) {
	    			foreach ($formData['drop'] as $value) {
	    				$dbAppySubject->deleteDataBySubcode($idapply, $value);
	    			}
	    		}
	    	}
    		//echo var_dump($formData);exit;
    	}
    	
        $Program = new App_Model_General_DbTable_Program();
    	$programs = $Program->fnGetProgramListCreditTransfer();
        $dbIntakeId=new App_Model_Record_DbTable_Intake();
        $this->view->intake=$dbIntakeId->getCurrentIntakeAll();
        $this->view->programs = $programs;
        $application=$dbApply->getDataByTransaction($transaction_id);
    	$subjectproposed=array();
    	if ($application) {
    		$subjectproposed=$dbAppySubject->getDataByApplyId($application['idApply']);
    	}
    	//echo $transaction_id;echo var_dump($appProgram);exit;
    	$this->view->subjects = $subjectproposed;
        $this->view->application = $application; 
            
         
        
        $this->view->title = $this->view->translate("Programme");
    }
    
    private function encrypt_decrypt ($data, $encrypt) {
	    if ($encrypt == true) {
	        $output = base64_encode (convert_uuencode ($data));
	    } else {
	        $output = convert_uudecode (base64_decode ($data));
	    } 
	    return $output;
	}
    
    public function ajaxGetSubjectAction()
    {
        $this->_helper->layout->disableLayout();
    
        $programId = $this->_getParam('program_id',null);
         
        $auth = Zend_Auth::getInstance();    	
    	$transaction_id = $auth->getIdentity()->transaction_id;
        
        $applicantTransactionDn = new App_Model_Application_DbTable_ApplicantTransaction();
    	$transaction = $applicantTransactionDn->getTransactionData($transaction_id);
    	$this->view->transaction = $transaction;
        
        if($programId != null)
        {
            $this->view->programid = $programId;
            
            $Landscape = new App_Model_General_DbTable_Landscape();
            $landscape = $Landscape->getActiveLandscape($programId, $transaction['at_intake']);
            
            $this->view->landscapeType = $landscape[0]['LandscapeType'];
            
            if($landscape[0]['LandscapeType'] == 43)
            {
                $subjects = $Landscape->getLandscapeSubjects($programId,$landscape[0]['IdLandscape']);
            
                $this->view->subjects = $subjects;
            }
            elseif($landscape[0]['LandscapeType'] == 44)
            {
                $subjects = $Landscape->getLandscapeSubjectBlock($programId,$landscape[0]['IdLandscape']);
            
                $this->view->subjects = $subjects;
            }
            $CreditApply = new App_Model_General_DbTable_Creditapply();
            $credit_apply = $CreditApply->getData($transaction_id);
            $this->view->creditapply=$credit_apply;
            $CreditTransfer=new App_Model_General_DbTable_Credittransfer();
            if(isset($credit_apply['idCreditApply'])) {
            	$idCreditApply=$credit_apply['idCreditApply'];
            	
            	foreach ($subjects as $key=>$value) {
            		//get tranfer applicaytiom data
            		$row=$CreditTransfer->isIn($idCreditApply, $value['key']);
            		
            		if ($row) $subjects[$key]['apply']=$row;
            		else $subjects[$key]['apply']=array();
            	}
            }
            
            $dbbranch=new App_Model_General_DbTable_Branchofficevenue();
           	$this->view->branch=$dbbranch->getDatabyProgram($programId);
           // echo var_dump($subjects);exit;
            $this->view->subjects = $subjects;
            $this->view->landscape = $landscape;
        }
    }
    
    public function creditHourApplyAction()
    {
        $auth = Zend_Auth::getInstance();    	
    	$transaction_id = $auth->getIdentity()->transaction_id;
    	$appl_id = $auth->getIdentity()->appl_id;
        
        $username = $auth->getIdentity()->appl_email;
        
        $applicantTransactionDn = new App_Model_Application_DbTable_ApplicantTransaction();
    	$transaction = $applicantTransactionDn->getTransactionData($transaction_id);
    	$this->view->transaction = $transaction;
       
        
        if($this->getRequest()->isPost())
        {
            
            $formData = $this->getRequest()->getPost();
            $kls=$formData['ap_kelas_code'];
            $dbbranch=new App_Model_General_DbTable_Branchofficevenue();
            $branch=$dbbranch->getData($kls);
            $Program = new App_Model_General_DbTable_Program();
            $program = $Program->fngetProgramData($formData['program_id']);
            /*** PREFERED PRGORRAMME ***/
            $applicantProgram = new App_Model_Application_DbTable_ApplicantProgram();
            
            //delete ptest
            $applicantProgram->deleteTransactionData($transaction_id);

            //add ptest program prefered 1
            $data1 = array(
                'ap_at_trans_id' =>$transaction_id,
                'ap_prog_code' => $program['ProgramCode'],		    								
                'ap_preference' =>1,
                'ap_usm_mark' =>0,
                'ap_usm_status' =>0,
            	'IdBranch'=>$branch['IdBranch'],
            	'IdProgramBranch'=>$branch['IdProgramBranch']
            );
            
            $row=$applicantProgram->IsIn($transaction['at_trans_id'], '1');
            if (!$row)
            	$applicantProgram->insert($data1);
            else
            	$applicantProgram->updateData($data1, $row['ap_id']);            
            
            $apply = array(
                'idLandscape' => $formData['landscape_id'],
                'idProgram' => $formData['program_id'],
                'idApplicant' => $appl_id,
                'usernameApply' => $username,
                'idTransaction' => $transaction_id,
                'updated' => date('Y-m-d H:i:s'),
            	'jenjang_asal'=>$formData['ap_jenjang'],
            	'program_asal'=>$formData['ap_program_asal'],
            	'pt_asal'=>$formData['ap_univ_asal'],
            	'akreditasi_asal'=>$formData['ap_akreditasi'] 
            );
            
            
            $CreditApply = new App_Model_General_DbTable_Creditapply();
            $credit_apply = $CreditApply->getData($transaction_id);
            
            $CreditApply->delete($transaction_id);

            $apply_id    = $CreditApply->insert($apply);
            
            unset($formData['landscape_id']);
            unset($formData['program_id']);
            unset($formData['submit']);
            
           // print_r($formData);
            $CreditTransfer = new App_Model_General_DbTable_Credittransfer();
            $CreditTransfer->delete($credit_apply['idCreditApply']);
            
            foreach($formData['code'] as $key => $value)
            {
               // $key_split = explode('_',$key);
                
                $idSubject = $key;
                
                if($value != '')
                {
                	//echo var_dump($formData);exit;
                	$subjectcode=$value;
                	$subjectname=$formData['name'][$key];
                	$subjectCr=$formData['cr'][$key];
                	$subjectgrade=$formData['grade'][$key];
                    $transfer = array(
                    'IdSubject' => $idSubject,
                   // 'user_apply' => $username,
                    'IdCreditTransferApply' => $apply_id,
                    'EquiCourseCode'=>$subjectcode,
                    'EquiCourse'=>$subjectname,
                    'EquiGrade'=>$subjectgrade,
                    'EquiCreditHour'=>$subjectCr
                    
                    );
                    
                    $CreditTransfer->insert($transfer);
                } 
                
            }
            //exit;
        }
        $this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application','action'=>'uploaddocument'),'default',true));
    }
    public function applicantBurekolVerificationAction() {
    
    	$txtid = $this->_getParam('txn_id',null);
    
    	$Transaction = new App_Model_Application_DbTable_ApplicantTransaction();
    	$trans = $Transaction->getDataById($txtid);
    	
    	$appl_id = $trans['at_appl_id'];
    	$this->view->applid=$appl_id;
    	$pes_id=$trans['at_pes_id'];
    	$this->view->pes_id = $pes_id;
    	$this->view->transaction_id = $txn_id = $trans['at_trans_id'];
    
    	$applicantProfileProposeDb = new App_Model_Application_DbTable_ApplicantProfilePropose();
    	$applicantProfileDb=new App_Model_Application_DbTable_ApplicantProfile();
    	 

    	
    
    	if ($this->getRequest()->isPost()) {
    		$auth = Zend_Auth::getInstance();
    			
    		$formData = $this->getRequest()->getPost();
    
    		//profile
    		$pes_id=$formData['registration_id'];
    		if ($formData['appl_nik']!="" &&
    				$formData['appl_uan']!="") {
    		$data = array(
    				'appl_fname' => $formData['appl_fname'],
    				'appl_mname' => $formData['appl_mname'],
    				'appl_lname' => $formData['appl_lname'],
    				'appl_name_kartu' => $formData['appl_name_kartu'],
    				'appl_dob' => $formData['appl_dob'],
    				'appl_birth_place' => $formData['appl_birth_place'],
    				'appl_gender' => $formData['appl_gender'],
    				'appl_religion' => $formData['appl_religion'],
    				'appl_marital_status' => $formData['appl_marital_status'],
    				'appl_nationality' => $formData['appl_nationality'],
    				'appl_address_rw' => $formData['appl_address_rw'],
    				'appl_address_rt' => $formData['appl_address_rt'],
    				'appl_address1' => $formData['appl_address1'],
    				'appl_province' => $formData['appl_province'],
    				'appl_state' => $formData['appl_state'],
    				'appl_city' => $formData['appl_city'],
    				'appl_kecamatan' => $formData['appl_kecamatan'],
    				'appl_kelurahan' => $formData['appl_kelurahan'],
    				'appl_postcode' => $formData['appl_postcode'],
    				'appl_caddress_rw' => $formData['appl_caddress_rw'],
    				'appl_caddress_rt' => $formData['appl_caddress_rt'],
    				'appl_caddress1' => $formData['appl_caddress1'],
    				'appl_cprovince' => $formData['appl_cprovince'],
    				'appl_cstate' => $formData['appl_cstate'],
    				'appl_ccity' => $formData['appl_ccity'],
    				'appl_ckecamatan' => $formData['appl_ckecamatan'],
    				'appl_ckelurahan' => $formData['appl_ckelurahan'],
    				'appl_cpostcode' => $formData['appl_cpostcode'],
    				'appl_phone_home' => $formData['appl_phone_home'],
    				'appl_phone_hp' => $formData['appl_phone_hp'],
    				'appl_email' => $formData['appl_email'],
    				'appl_nis' => $formData['appl_nis'],
    				'appl_nik' => $formData['appl_nik'],
    				'appl_uan' => $formData['appl_uan'],
    				'appl_idbranch'=>$formData['appl_idbranch'],
    				'appl_id' => $formData['profile_id'],
    				'burekol_verify_date'=>date('Y-M-d h:s:i'),
    				'burekol_verify_by'=>$auth->getIdentity()->appl_id
    		);
    			
    			
    		//$applicantProfileDb->updateData($data,'id = '.$formData['profile_id']);
    		if ($applicantProfileProposeDb->getProfileAll($formData['profile_id']))  
    			$applicantProfileProposeDb->updateData($data,$formData['profile_id']);
    		else 
    			$applicantProfileProposeDb->addData($data);
    			
    		//echo 'here'; die;
    		//mother's name
    			
    		// $studentRegistrationDB = new Registration_Model_DbTable_Studentregistration();
    		// $studentdetails = $studentRegistrationDB->fetchStudentHistoryDetails($formData['registration_id']);
    			
    		$familyDb = new App_Model_Application_DbTable_ApplicantFamily();
    		$familyDbPropose=new App_Model_Application_DbTable_ApplicantFamilyPropose();
    		$appl_id=$formData['profile_id'];
    		$mother = $familyDbPropose->getData($appl_id, 21);
    		 
    		if($mother){
    			$data = array(
    					'af_name' => $formData['af_name'],
    					'af_phone' => $formData['af_hp_ibu'],
    			);
    
    			$familyDbPropose->update($data,'af_appl_id = '.$appl_id.' and af_relation_type = 21');
    		}else{
    			$data = array(
    					'af_name' => $formData['af_name'],
    					'af_appl_id' => $appl_id,
    					'af_relation_type' => 21,
    					'af_address1' => $formData['appl_address1'],
    					'af_address2' => '',
    					'af_city' => $formData['appl_city'],
    					'af_postcode' => $formData['appl_postcode'],
    					'af_state' => $formData['appl_state'],
    					'af_phone' => $formData['af_hp_ibu'],
    					'af_email' => '',
    					'af_job' => '',
    					'af_id'=>$formData['af_id']
    			);
    
    			$familyDbPropose->insert($data);
    		}
    		$father = $familyDbPropose->getData($appl_id, 20);
    		if($father){
    			$data = array(
    					'af_name' => $formData['af_name_father'],
    					'af_phone' =>  $formData['af_hp_father'],
    			);
    				
    			$familyDbPropose->update($data,'af_appl_id = '.$appl_id.' and af_relation_type = 20');
    		}
    		else{
    			$data = array(
    					'af_name' => $formData['af_name_father'],
    					'af_appl_id' => $appl_id,
    					'af_relation_type' => 20,
    					'af_address1' => $formData['appl_address1'],
    					'af_address2' => '',
    					'af_city' => $formData['appl_city'],
    					'af_postcode' => $formData['appl_postcode'],
    					'af_state' => $formData['appl_state'],
    					'af_phone' =>  $formData['af_hp_father'],
    					'af_email' => '',
    					'af_job' => '',
    					'af_id'=>$formData['af_id']
    			);
    				
    			$familyDbPropose->insert($data);
    		}
    
    		//registration checklist
    		/* $checklistStudentDb = new App_Model_Registration_DbTable_ChecklistStudent();
    		$checklist = $checklistStudentDb->getChecklistStudentData($formData['registration_id']);
    			
    		foreach ($checklist as $list){
    
    			if( in_array($list['rcs_id'], $formData['checklist']) ){
    				if(isset($list['rcstd_id']) && $list['rcstd_id']==''){
    					//insert checklist
    					$data_ckl = array(
    							'rcstd_idStudentRegistration' => $formData['registration_id'],
    							'rcstd_rcs_id' => $list['rcs_id'],
    							'rcstd_status' => '1'
    					);
    					$checklistStudentDb->insert($data_ckl);
    				}else{
    					//update checklist
    					if($list['rcstd_status']==0){
    						$data_ckl = array(
    								'rcstd_status' => '1'
    						);
    						$checklistStudentDb->update($data_ckl,'rcstd_id='.$list['rcstd_id']);
    					}
    				}
    			}else{
    				if(isset($list['rcstd_id']) && $list['rcstd_id']!=''){
    					//update disabled
    					$data_ckl = array(
    							'rcstd_status' => 0
    					);
    					$checklistStudentDb->update($data_ckl,'rcstd_id='.$list['rcstd_id']);
    				}else{
    					//ignore
    				}
    			}
    		} */
    		} else $this->view->msg='Lengkapi semua isian';
    	}
    	
    	$dbBranch=new App_Model_General_DbTable_Branchofficevenue();
    	$this->view->branchlist=$dbBranch->fnGetAllBranchList();
    	//echo var_dump($this->view->branchlist);exit;
    
    	//parent (mother)
    	$familyDb = new App_Model_Application_DbTable_ApplicantFamily();
    	$familyDbPropose = new App_Model_Application_DbTable_ApplicantFamilyPropose();
    	$motherData = $familyDbPropose->getData($appl_id,'21');
    	if (!$motherData) $motherData = $familyDb->getData($appl_id,'21');
    	$this->view->motherData = $motherData;
    
    	//parent (mother)
    	$fatherData = $familyDbPropose->getData($appl_id,'20');
    	if (!$fatherData) $fatherData = $familyDb->getData($appl_id,'20');
    	$this->view->fatherData = $fatherData;
    
    	//lookup table list
    	$sisSetupDetailDb = new App_Model_General_DbTable_SisSetupDetail();
    	$religionList = $sisSetupDetailDb->getDataList("RELIGION");
    	$this->view->religionList = $religionList;
    
    	$maritalList = $sisSetupDetailDb->getDataList("MARITAL");
    	$this->view->maritalList = $maritalList;
    
    	$cityDb = new App_Model_General_DbTable_City();
    	$cityList = $cityDb->getData();
    	$this->view->cityList = $cityList;
    
    	$stateDb = new App_Model_General_DbTable_State();
    	$stateList = $stateDb->getData();
    	$this->view->stateList = $stateList;
    
    	$countryDb = new App_Model_General_DbTable_Country();
    	$countryList = $countryDb->getData();
    	$this->view->countryList = $countryList;
    
       	//registration checklist
    	$checklistStudentDb = new App_Model_Registration_DbTable_ChecklistStudent();
    	$checklist = $checklistStudentDb->getChecklistStudentData($pes_id);
    	$this->view->checklist = $checklist;
    
    	$this->view->pes_id = $pes_id;
    	//getProfile
    	$profile=$applicantProfileProposeDb->getProfileAll($appl_id);
    	if (!$profile) $profile=$applicantProfileDb->getProfileAll($appl_id);
    	$this->view->profile = $profile;
    
    	$DocumentUploads = new App_Model_General_DbTable_Maintenance();
    	$checklist = $DocumentUploads->fnGetMaintenanceMsDetails(33);
    
    
    
    	$doc = array();
    	$documentDb = new App_Model_Application_DbTable_ApplicantUploadFile();
    	//photo
    	$photo = $documentDb->getTxnFile($txn_id,51);
    	$this->view->photo = $photo;
    
    	foreach ($checklist as $key => $value)
    	{
    		 
    		$doc[] = array(
    				'type_id' => $value['idDefinition'],
    				'type_name' => $value['DefinitionDesc'],
    				'data' => $documentDb->getTxnFileArray($txn_id,$value['idDefinition'])
    		);
    	}
    
    	$this->view->documentList = $doc;
    
    }
    
    public function uploadFileAction() {
    	
    	$pes_id = $this->_getParam('id',null);
    	// echo $pes_id;
    	$Transaction = new App_Model_Application_DbTable_ApplicantTransaction();
    	$trans = $Transaction->getTxn($pes_id);
    
    	$appl_id = $trans['at_appl_id'];
    	$this->view->transaction_id = $txn_id = $trans['at_trans_id'];
    	// echo "trans=".$txn_id;
    	$auth = Zend_Auth::getInstance();
    
    	$formData = $this->getRequest()->getPost();
    
    	$DocumentUploads = new App_Model_General_DbTable_Maintenance();
    	$checklist = $DocumentUploads->fnGetMaintenanceDisplay($formData['type_id']);
    
    	///upload_file
    	$apath = DOCUMENT_PATH."/applicant";
    	//$apath = "/Users/alif/git/triapp/documents/applicant";
    
    	//create directory to locate file
    	if (!is_dir($apath)) {
    		mkdir($apath, 0775);
    	}
    		
    	///upload_file
    	$applicant_path = DOCUMENT_PATH."/applicant/".date("mY");
    	//$applicant_path = "/Users/alif/git/triapp/documents/applicant/".date("mY");
    
    	//create directory to locate file
    	if (!is_dir($applicant_path)) {
    		mkdir($applicant_path, 0775);
    	}
    
    
    	$major_path = $applicant_path."/".$txn_id;
    
    	//create directory to locate file
    	if (!is_dir($major_path)) {
    		mkdir($major_path, 0775);
    	}
    
    	if (is_uploaded_file($_FILES["file"]['tmp_name'])){
    		$ext_photo = $this->getFileExtension($_FILES["file"]["name"]);
    
    		if($ext_photo==".jpg" || $ext_photo==".JPG" || $ext_photo==".jpeg" || $ext_photo==".JPEG" || $ext_photo==".gif" || $ext_photo==".GIF" || $ext_photo==".png" || $ext_photo==".PNG" || $ext_photo == ".pdf" || $ext_photo == ".PDF"){
    			$flnamenric = date('Ymdhs')."_".$checklist['idDefinition'].$ext_photo;
    			$path_photo = $major_path."/".$flnamenric;
    			move_uploaded_file($_FILES["file"]['tmp_name'], $path_photo);
    				
    			$upd_photo = array(
    					'auf_appl_id' => $txn_id,
    					'auf_file_name' => $flnamenric,
    					'auf_file_type' => $formData['type_id'],
    					'auf_upload_date' => date("Y-m-d h:i:s"),
    					'auf_upload_by' => $auth->getIdentity()->appl_id,
    					'pathupload' => $path_photo
    			);
    			 
    				
    			$uploadfileDB = new App_Model_Application_DbTable_UploadFile();
    
    			$previous_record = $uploadfileDB->getFile($formData["transaction_id"],$formData['type_id']);
    			echo var_dump($previous_record);
    			if($previous_record){
    				$uploadfileDB->updateData($upd_photo,$previous_record['auf_id']);
    			}else{
    				$uploadfileDB->addData($upd_photo);
    			}
    		}
    		//exit;
    	}
    
    	$this->_redirect( $this->baseUrl . $formData['redirect_path']);
    }
    
    public function pushECollectionAction(){
    	
    	 
    	date_default_timezone_set('Asia/Bangkok');
    	$trxid = $this->_getParam('trxid', null);
    	$invoice = $this->_getParam('invoice', null);
    	$re = $this->_getParam('re', null);
    	
    	 
    	//$spcInvoiceDb = new Studentfinance_Model_DbTable_InvoiceSpc();
    	$invoiceDet = new Studentfinance_Model_DbTable_InvoiceDetail();
    	$dbInvoice = new Studentfinance_Model_DbTable_InvoiceMain(); 
    	$dbTransaction=new App_Model_Application_DbTable_ApplicantTransaction();
    	$dbAppFee=new Studentfinance_Model_DbTable_ApplicationFee();
    	$transaction=$dbTransaction->getDataById($trxid);
    	$intakeid=$transaction['at_intake'];
    	$dbIntake=new App_Model_General_DbTable_Intake();
    	$intake=$dbIntake->fngetIntakeById($intakeid);
    	$bni = new Studentfinance_Model_DbTable_AccessBni();
    	$dbProgram=new App_Model_General_DbTable_Program(); 
    	$dbFinance=new App_Model_General_DbTable_Bank();
    	$bank=$dbFinance->fnGetBankDetails(1);
    	$secretkey=$bank['secret_key'];
    	$url=$bank['url_api'];
    	//create invoice
    	 
    	$dbInvoice->pushToECollForEnrollment($invoice,$intake['ApplicationEndDate'],'createbilling','c',$re);
    			 
    	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal','action'=>'index'),'default',true));
    	
    
    		
    }
}
?>