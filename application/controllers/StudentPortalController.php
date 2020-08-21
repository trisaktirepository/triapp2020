<?php

class StudentPortalController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function ajaxloginAction(){
    	 
    	$this->view->title = $this->view->translate("Application Login");
    	//get string
    	$username=$this->_getParam('userid', null);
    	$password=$this->_getParam('password', null);
    	$tokenid=$this->_getParam('token', null);
    	$auth = Zend_Auth::getInstance();
    	
    	$this->_helper->layout->disableLayout();
    	 
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    	if($auth->hasIdentity()){
    			
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'biodata'),'default',true));
    	}
    	 
    	 // collect the data from the user
    	Zend_Loader::loadClass('Zend_Filter_StripTags');
    	$filter = new Zend_Filter_StripTags();
    	$username = $filter->filter($username);
    	$password = $filter->filter($password);
    	$dbAdapter = Zend_Db_Table::getDefaultAdapter();
    	$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
    
    	$authAdapter->setTableName('applicant_profile')
    				->setIdentityColumn('appl_email')
    				->setCredentialColumn('appl_password');
    	// Set the input credential values to authenticate against
    	$authAdapter->setIdentity($username);			 
    	$authAdapter->setCredential($password);
    
    	// do the authentication
    	$auth = Zend_Auth::getInstance();
    	$result = $auth->authenticate($authAdapter);
    	if ($result->isValid()) {
    		$data = $authAdapter->getResultRowObject(null, 'password');
     		if ($data->appl_role==1){
    					 
    			//$data->role = "student";
    			$studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
    			$student = $studentRegDB->getData($data->appl_id);
    
    			if(isset($student["IdStudentRegistration"])){
    						$data->programcode = $student['ProgramCode'];
    						$data->registration_id = $student["IdStudentRegistration"];
    						$data->role = "student";
    						$this->gstrsessionTRIAPP = new Zend_Session_Namespace('triapp');
    						$this->gstrsessionTRIAPP->__set('survey',"0");
    						//echo "student";exit;
    			} else {
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
     
    
    				}else{
    					$data->role = "applicant";
    				}
    				$data->msg="Login Oke";
        
    		}  else $data->msg="Login gagal";
    		
    		$auth->getStorage()->write($data);
    		
    		$ajaxContext->addActionContext('view', 'html')
    		->addActionContext('form', 'html')
    		->addActionContext('process', 'json')
    		->initContext();
    		
    		$json = Zend_Json::encode($data);
    		echo $json;
    		exit();
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
    
    public function indexAction()
    {
    	
    	//survey
    	
    	$this->view->title = $this->view->translate("Academic Information");
        
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();    	
    
    	//print_r($auth->getIdentity());
    	
    	$appl_id = $auth->getIdentity()->appl_id; 
    	$registration_id = $auth->getIdentity()->registration_id;    

    	$this->view->appl_id = $appl_id;
    	$this->view->IdStudentRegistration = $registration_id;
    	
    	$studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	
        //print_r($applicant);
        
        //To get Student Academic Info        
        $studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
        $student = $studentRegDB->getStudentInfo($registration_id);
    	$this->view->student = $student;
  	
    	 //To get Registered Courses   
         $landscapeDb = new App_Model_Record_DbTable_Landscape();
         $landscape = $landscapeDb->getData($student["IdLandscape"]);
         $this->view->landscape = $landscape;
         
         if($landscape["LandscapeType"]==43 || $landscape["LandscapeType"]==44) {//Semester Based         	
         	
         	//get total registered semester          	
         	$semester = $studentSemesterDB->getRegisteredSemester($registration_id);
         	$this->view->semester = $semester;
         	
         	//get subjects
  		
         }elseif($landscape["LandscapeType"]==45){
         	
         	//get registered blocks         	
         	$blocks = $studentSemesterDB->getRegisteredBlock($registration_id);
         	$this->view->blocks = $blocks;
         }
         
         //Semester Registration Status         
         $registered_semester = $studentSemesterDB->getRegisteredSemester($registration_id);
    	 $this->view->registered_semester = $registered_semester;
    	
    }
    public function applyDeferAction()
    {
    	 
    	//survey
    	 
    	$this->view->title = $this->view->translate("Defer");
    
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    
    	//print_r($auth->getIdentity());
    	 
    	$appl_id = $auth->getIdentity()->appl_id;
    	$registration_id = $auth->getIdentity()->registration_id;
    
    	$this->view->appl_id = $appl_id;
    	$this->view->IdStudentRegistration = $registration_id;
    	 
    	$studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
    	 
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	$dbDefer=new App_Model_Registration_DbTable_ApplyDefer();
    	//print_r($applicant);
    	if($this->getRequest()->isPost())
    	{
    		$formData = $this->getRequest()->getPost();
    		$data=array('IdStudentRegistration'=>$formData['IdStudentRegistration'],
    					'IdSemesterMain'=>$formData['IdSemesterMain'],
    					'reason'=>$formData['reason'],
    					'created_at'=>date('Y-m-d H:s:i'),
    					'created_by'=>$auth->getIdentity()->id,
    					'request_type'=>$formData['request_type']);
    		if (!$row=$dbDefer->isIn($registration_id, $formData['IdSemesterMain'])) 
    			 $dbDefer->addData($data);
    		else 
    			$dbDefer->update($data, 'id='.$row['id'].' and status=1');
    		
    	}
    	//To get Student Academic Info
    	$studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
    	$student = $studentRegDB->getStudentInfo($registration_id);
    	$this->view->student = $student;
    	 
    	$dbSemester=new App_Model_General_DbTable_Semestermaster();
    	$this->view->semester=$dbSemester->getAllSemesterCanDefer($registration_id);
    	 
    	$dbDeferReson=new App_Model_Registration_DbTable_DeferType();
    	$this->view->defertype=$dbDeferReson->getData();
    	
    	
    	$this->view->deferhistory=$dbDefer->getDeferHistory($registration_id);
    	$dbInvoice=new Studentfinance_Model_DbTable_InvoiceMain();
    	$invoicedet='';
    	$invoice=$dbInvoice->getInvoiceByStd($registration_id,$this->view->semester[0]['IdSemesterMaster'],38);
    	if ($invoice) {
    		$dbInvoiceDetail=new Studentfinance_Model_DbTable_InvoiceDetail();
    		$invoicedet=$dbInvoiceDetail->getInvoiceDetail($invoice['id']);
    		$this->view->invoicedetail=$invoicedet;
    	}
    }
    
    
	public function logoutAction()
    {
        $storage = new Zend_Auth_Storage_Session();
        $storage->clear();
        
        $this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'index'),'default',true));
    }
    
    
     public function viewScheduleAction(){
    	
    	$this->view->title = "View Schedule";
    	
    	$idGroup = $this->_getParam('idGroup', 0);
    	$idstd = $this->_getParam('idstd', 0);
    	$this->view->idGroup = $idGroup;
    	$this->view->idstd=$idstd;
		//
		
    	//get group info
    	$courseGroupDb = new App_Model_Registration_DbTable_CourseGroup();
    	$group = $courseGroupDb->getInfo($idGroup);
    	$this->view->group = $group;
    	//chect hide status
    	$dbStd=new App_Model_Registration_DbTable_Studentregistration();
    	$std=$dbStd->getStudentRegistrationDetail($idstd);
    	$dbSemeseter=new App_Model_General_DbTable_Semestermaster();
    	$idsemster=$group['IdSemester'];
    	$progid=$group['ProgramCreator'];
    	 
    	$this->view->open="";
    	if ($dbSemeseter->getSemesterCourseRegistrationValidate($progid, null, $idsemster,$std['IdIntake'])) $this->view->open=1;
    	
    	
    	$dbProgram=new App_Model_General_DbTable_Program();
    	$program=$dbProgram->getData($progid);
    	$this->view->hidelecturer="";
    	if ($program['hide_lecturer']=="1") $this->view->hidelecturer="1";
    	//----------------------
    	$groupSchdeleDb = new App_Model_Registration_DbTable_CourseGroupSchedule();
		$schedule = $groupSchdeleDb->getSchedule($idGroup);
		$this->view->schedule = $schedule;
//minor
		$groupSchdeleMinorDb = new App_Model_Registration_DbTable_CourseGroupScheduleMinor();
		$schedule = $groupSchdeleMinorDb->getScheduleByStd($idGroup,$idstd);
		$this->view->scheduleminor = $schedule;
		
    	
		
     }
     
 	public function printScheduleAction(){
    	
 		$this->_helper->layout->setLayout('preview');
 		
    	$this->view->title = "Class Schedule";
    	
    	$idGroup = $this->_getParam('idGroup', 0);
    	$this->view->idGroup = $idGroup;
				
    	//get group info
    	$courseGroupDb = new App_Model_Registration_DbTable_CourseGroup();
    	$group = $courseGroupDb->getInfo($idGroup);
    	$this->view->group = $group;
    	
    	//chect hide status
    	$dbSemeseter=new App_Model_General_DbTable_Semestermaster();
    	$idsemster=$group['IdSemester'];
    	$progid=$group['ProgramCreator'];
    	$this->view->open="";
    	if ($dbSemeseter->getSemesterCourseRegistrationValidate($progid, null, $idsemster,null)) $this->view->open=1;
    	 
    	 
    	$dbProgram=new App_Model_General_DbTable_Program();
    	$program=$dbProgram->getData($progid);
    	$this->view->hidelecturer="";
    	if ($program['hide_lecturer']=="1") $this->view->hidelecturer="1";
    	//----------------------
    	
    	$groupSchdeleDb = new App_Model_Registration_DbTable_CourseGroupSchedule();
		$schedule = $groupSchdeleDb->getSchedule($idGroup);
		$this->view->schedule = $schedule;		
		
    	
		
     }
     
     
  
    
    
    public function viewKrsAction(){
    	
    	$idstudentsemsterstatus = $this->_getParam('idstudentsemsterstatus', 0);
    	$this->view->idstudentsemsterstatus = $idstudentsemsterstatus;
    	
    	$auth = Zend_Auth::getInstance();
    	$IdStudentRegistration = $auth->getIdentity()->registration_id;
    	 
    	$this->view->IdStudentRegistration = $IdStudentRegistration;
    	 
    	$type = $this->_getParam('type', 0);
    	$this->view->title = "Kartu Rencana Asli";
    	//$this->_redirect("http://www.print.trisakti.ac.id/student-portal/view-krs/idstudentsemsterstatus/'.$idstudentsemsterstatus.'/registration_id/'.$IdStudentRegistration.'/type/".$type);
    		
    	global $subject_list;
    	    	
    	

    	
    	//To get Student Academic Info        
        $studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
        $student = $studentRegDB->getStudentInfo($IdStudentRegistration);
        if($student["majoring"]=="common"|$student["majoring"]=="Bersama"){
        	$student["majoring"]="-";
        	$student["majoring_english"]="-";
        }
        global $majoring;
        $majoring=$student["majoring"];
        global $printmajoring;
        $printmajoring=$student['print_majoring'];
        //brach
        $branchDb=new App_Model_General_DbTable_Branchofficevenue();
        $branch=$branchDb->fnviewBranchofficevenueDtls($student['IdBranch']);
        //get info majoring
        $majorDb = new App_Model_General_DbTable_Program();
        $major = $majorDb->fnviewMajoring($student['IdProgramMajoring']);
        $program=$majorDb->getData($student['IdProgram']);
        if ($major['Address1']=='') {
        	if ($branch["Addr1"]=='') {
        		$addphone=$program["Phone1"];
        		$addemail=$program["Email"];
        		$add=$program["Address1"].' '.$program["Address2"].' '.$program["StateName"].' '.ucwords(strtolower($program["CountryName"]));
        		 
        	} else {
	        	$addphone=$branch["Phone"];
	        	$addemail=$branch["Email"];
	        	$add=$branch["Addr1"].' '.$branch["Addr2"].' '.$branch["StateName"].' '.ucwords(strtolower($branch["CountryName"]));
        	}
        } else {
        	$addphone=$major["Phone1"];
        	$addemail=$major["Email"];
        	$add=$major["Address1"].' '.$major["Address2"].' '.$major["StateName"].' '.ucwords(strtolower($major["CountryName"]));
        }
        
        //get semester info
    	$semesterStatusDb = new App_Model_Record_DbTable_Studentsemesterstatus();
    	$semester = $semesterStatusDb->getSemesterInfo($idstudentsemsterstatus);
    	
    	//get subjects
    	$registerSubjectDB = new App_Model_Record_DbTable_StudentRegSubjects();
    	$subject_list  = $registerSubjectDB->getActiveRegisteredCourse($semester["IdSemesterMain"],$IdStudentRegistration);
    	$total_credit_hours = $registerSubjectDB->getTotalCreditHoursActiveRegisteredCourse($semester["IdSemesterMain"],$IdStudentRegistration);
		
		//get subject course group schedule
		$groupSchduleDb = new App_Model_General_DbTable_CourseGroupSchedule();
		$dbGroupStdMinor=new App_Model_General_DbTable_CourseGroupStudentMinor();
		$dbSchMinor=new App_Model_General_DbTable_CourseGroupScheduleMinor();
		$dbGrpMinor=new App_Model_General_DbTable_CourseGroupMinor();
		$dbStaff=new App_Model_General_DbTable_Staffmaster();
		foreach ($subject_list as $index=>$subject){
			if ($student['IdProgram']!=60) {
				$schedule = $groupSchduleDb->getSchedule($subject['IdCourseTaggingGroup']);
				
				if($schedule){
					$subject_list[$index]['start_sc_date'] = $schedule[0]['sc_date'];
					$subject_list[$index]['end_sc_date'] = $schedule[sizeof($schedule)-1]['sc_date'];
						
					$subject_list[$index]['GroupName'] = $schedule[0]['GroupName'];
				} else $subject_list[$index]['GroupName']='-';
			} else {
				//medical Profession
				$schedule=$dbGroupStdMinor->getScheduleMinor($semester["IdSemesterMain"], $IdStudentRegistration, $subject['IdCourseTaggingGroup']);
				if($schedule){
					$subject_list[$index]['start_sc_date'] = $schedule[0]['sc_date'];
					$subject_list[$index]['end_sc_date'] = $schedule[0]['sc_date_end'];
				
					$subject_list[$index]['GroupName'] = $schedule[0]['GroupName'];
				} else $subject_list[$index]['GroupName']='-';
			}
			
			if ($subject['lecturer_id']!='') $lect=$dbStaff->getStaffFullName($subject['lecturer_id']);
			else if ($subject['IdLecturer']!='') $lect=$dbStaff->getStaffFullName($subject['IdLecturer']);
			else $lect='-';
			$subject_list[$index]['lec']=$lect; 
		}
    	//get info dekan faculty
    	$programDb = new App_Model_General_DbTable_Program();
    	$program = $programDb->getCollegeDean($student["IdProgram"]);
    	
    	//get info college
    	$collegedB = new GeneralSetup_Model_DbTable_Collegemaster();
        $college = $collegedB->getFullInfoCollege($student["IdCollege"]);
        	
    	//get salutation
    	$defDB = new App_Model_General_DbTable_Definationms();
    	$dean_front_salutation = $defDB->getData($program["FrontSalutation"]);
    	$dean_back_salutation  = $defDB->getData($program["BackSalutation"]);    	
    	$academic_front_salutation = $defDB->getData($student["FrontSalutation"]);
    	$academic_back_salutation  = $defDB->getData($student["BackSalutation"]);
    	    	
    	//get photo student
    	$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
    	$file = $uploadFileDb->getFile($student["transaction_id"],51);
    	    	
    	if(isset($file["pathupload"])){    		
    	if (file_exists($file["pathupload"])) { 		
    			$photo_url  = $file["pathupload"];
    		}else{
    			$photo_url  = "/var/www/html/triapp/public/images/no_image.gif";
    		}
    	}else{
    		$photo_url = "/var/www/html/triapp/public/images/no_image.gif";
    	}

    	/* ------------------------------
    	 * start create directrory folder
    	 * ------------------------------ */
    	   
		//$location_path
		$location_intake_path = DOCUMENT_PATH."/student/".$student["IdIntake"];
		
        //create directory to locate file			
		if (!is_dir($location_intake_path)) {
	    	mkdir($location_intake_path, 0775);
		}
		
		
        //$location_path
		$location_program_path = $location_intake_path."/".$student["ProgramCode"];
		
        //create directory to locate file			
		if (!is_dir($location_program_path)) {
	    	mkdir($location_program_path, 0775);
		}
		
		//output_directory_path
		$output_directory_path = $location_program_path."/".$student["registrationId"];
		
        //create directory to locate file			
		if (!is_dir($output_directory_path)) {
	    	mkdir($output_directory_path, 0775);
		}			
		
		//creating folder student
		if($student["repository"]==''){
			$studentRegDB->updateData(array('repository'=>"student/".$student["IdIntake"]."/".$student["ProgramCode"]."/".$student["registrationId"]),$IdStudentRegistration);
		}		
				
		//output filename 
		$output_filename = $student["registrationId"]."_kartu_rencana_studi";
		
		if($type==2){
			$output_filename .="_detail";
		}
		
		$output_filename .= ".pdf";
		
		//to rename output file			
		$output_file_path = $output_directory_path."/".$output_filename;
		 		
		
		/* ------------------------------
    	 * end  create directrory folder
    	 * ------------------------------ */

		//file type
		if($type==1){
			$file_type = 53;
				
			//get subjects
			$registerSubjectDB = new App_Model_Record_DbTable_StudentRegSubjects();
			$subject_list  = $registerSubjectDB->getSemesterSubjectRegistered($semester["IdSemesterMain"],$IdStudentRegistration);
			
			//get subject course group schedule
			$groupSchduleDb = new GeneralSetup_Model_DbTable_CourseGroupSchedule();
			foreach ($subject_list as $index=>$subject){
				$schedule = $groupSchduleDb->getSchedule($subject['IdCourseTaggingGroup']);
				
				if($schedule){
					$subject_list[$index]['start_sc_date'] = $schedule[0]['sc_date'];
					$subject_list[$index]['end_sc_date'] = $schedule[sizeof($schedule)-1]['sc_date'];
						
					$subject_list[$index]['schedule'] = $schedule;
				}
			}
				
		}else
		if($type==2){
			$file_type = 67;
				
			//get subjects
			$registerSubjectDB = new App_Model_Record_DbTable_StudentRegSubjects();
			$subject_list  = $registerSubjectDB->getSemesterSubjectRegistered($semester["IdSemesterMain"],$IdStudentRegistration);
		
			//get subject course group schedule
			$groupSchduleDb = new GeneralSetup_Model_DbTable_CourseGroupSchedule();
			foreach ($subject_list as $index=>$subject){
				$schedule = $groupSchduleDb->getSchedule($subject['IdCourseTaggingGroup']);
				
				if($schedule){
					$subject_list[$index]['start_sc_date'] = $schedule[0]['sc_date'];
					$subject_list[$index]['end_sc_date'] = $schedule[sizeof($schedule)-1]['sc_date'];
						
					$subject_list[$index]['schedule'] = $schedule;
				}
			}
				
		
				
		}else{
			$file_type = 53;
		}
		
		//check kalo dah generate ke sebelum ni?
		Global $kode_sandi;
    	$documentDb = new App_Model_Application_DbTable_ApplicantDocument();
    	$document = $documentDb->getData($student["transaction_id"],$file_type); 
    	
		if(isset($document["ad_id"])){			
			$kode_sandi = $document["ad_kode_sandi"];
			
			if($kode_sandi==""){
				$kode_sandi = md5($document['ad_id']);
			}
			
		}else{	
		
			//insert info file into applicant_documents	
			$fileData["ad_appl_id"]=$student["transaction_id"];
			$fileData["ad_type"]=53;
			$fileData["ad_filepath"]="student/".$student["IdIntake"]."/".$student["ProgramCode"]."/".$student["registrationId"];
			$fileData["ad_filename"]=$output_filename;
			$fileData["ad_createddt"]=date("Y-m-d H:i:s");
			$id_document = $documentDb->addData($fileData);
			$kode_sandi = md5($id_document);
			
			//update document kode sandi
			$fileKode["ad_kode_sandi"]=$kode_sandi;
	    	$documentDb->updateData($fileKode,$id_document);
		}
		
    	
		$fieldValues = array(
    	  '$[PROGRAM]'=>$student["ArabicName"],
		  '$[STRATA]'=>$student["strata"],
    	  '$[FACULTY]'=>'FAKULTAS '.$student["CollegeName"],
		  '$[KONSENTRASI]'=>$student["majoring"],
		  '$[MAJORING]'=>$student["majoring_english"],
    	  '$[NIM]'=>$student["registrationId"],
    	  '$[NAME]'=>$student["appl_fname"].' '.$student["appl_mname"].' '.$student["appl_lname"],
    	  '$[PERIODE]'=>$semester["SemesterMainName"],
    	  '$[ACADEMIC_ADVISOR]'=>$academic_front_salutation["BahasaIndonesia"].' '.$student["AcademicAdvisor"].' '.$academic_back_salutation["BahasaIndonesia"],
    	  '$[DEAN]'=>$dean_front_salutation["BahasaIndonesia"].' '.$program["FullName"].' '.$dean_back_salutation["BahasaIndonesia"],
    	  '$[PHOTO]'=>$photo_url,
    	  '$[TOTAL_SUBJECT]'=>$total_credit_hours,
    	  '$[KODE_SANDI]'=>$kode_sandi,
		  '$[ADDRESS]'=>$college["Add1"].' '.$college["Add2"].' '.$college["CityName"].' '.$college["StateName"],
		  '$[PHONE]'=>$college["Phone1"],
		  '$[EMAIL]'=>$college["Email"],
				'$[B_ADDRESS]'=>$add,
				'$[B_PHONE]'=>$addphone,
				'$[B_EMAIL]'=>$addemail
    	);
    			
		require_once 'dompdf_config.inc.php';
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		//template path
		if($type==1){//summary
			$html_template_path = DOCUMENT_PATH."/template/kartu_rencana_studi_fkg.html";
		}else
		if($type==2){//detail
			$html_template_path = DOCUMENT_PATH."/template/kartu_rencana_studi_fkg_detail.html";
		}else{//normal
			$html_template_path = DOCUMENT_PATH."/template/kartu_rencana_studi.html";
		}
		
		$html = file_get_contents($html_template_path);
			
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);	
		}
	//echo $html;exit;
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		@$dompdf->render();
		
		$dompdf = @$dompdf->output();
		//$dompdf->stream($output_filename);						
		
		file_put_contents($output_file_path, $dompdf);
		
		$this->view->file_path = $output_file_path;
		
    }
    
    public function khsAction(){
    	
    	 $this->view->title = "";
    	 
    	 // disable layouts for this action:
        $this->_helper->layout->disableLayout();
        
    	 //get applicant profile
    	 $auth = Zend_Auth::getInstance();    	
    
    	 //print_r($auth->getIdentity());
    	
    	 $appl_id = $auth->getIdentity()->appl_id; 
    	 $registration_id = $auth->getIdentity()->registration_id;    
		 $this->view->idstudreg=$registration_id;	
    	 $this->view->appl_id = $appl_id;
    	 $this->view->IdStudentRegistration = $registration_id;
    	 
    	 //To get Student Academic Info        
         $studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
         $student = $studentRegDB->getStudentInfo($registration_id);
    	 $this->view->student = $student;
    	
    	 
    	 //get info college
    	$collegedB = new App_Model_General_DbTable_Collegemaster();
        $college = $collegedB->getData($student["IdCollege"]);
        
        //get salutation
    	$defDB = new App_Model_General_DbTable_Definationms();
    	$academic_front_salutation = $defDB->getData($student["FrontSalutation"]);
    	$academic_back_salutation  = $defDB->getData($student["BackSalutation"]);
    	
    	$this->view->academic_advisor = $academic_front_salutation['BahasaIndonesia'].' '.$student["AcademicAdvisor"] .' '.$academic_back_salutation['BahasaIndonesia'];  	
    	 
    	 //To get Registered Courses   
         $landscapeDb = new App_Model_Record_DbTable_Landscape();
         $landscape = $landscapeDb->getData($student["IdLandscape"]);
         $this->view->landscape = $landscape;
         
         $dbPublish=new App_Model_Exam_DbTable_PublishMark();
         if($landscape["LandscapeType"]==43) {//Semester Based         	
         	
	         	//get total registered semester 
	         	$studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
	         	$semester = $studentSemesterDB->getRegisteredSemesterKHS($registration_id);
	         	
	         	foreach($semester as $key=>$sem){
				
					//get course registered  per semester
		  			$courseRegisterDb = new App_Model_Record_DbTable_StudentRegistration();
		  			$courses = $courseRegisterDb->getCourseRegisteredBySemester($registration_id,$sem["IdSemesterMain"]);
		  			//echo var_dump($courses);exit;
		  			foreach ($courses as $index=>$value) {
		  				if ($dbPublish->isAllMarkShown($value['IdSemesterMain'], $value['IdProgram'], $value['IdSubject'], $value['IdCourseTaggingGroup'])) {
		  					$courses[$index]['publish']="1";
		  				} else $courses[$index]['publish']="0";
		  			}
		  			//echo "---";echo var_dump($courses);exit;
		  			$semester[$key]["courses"]=$courses;
		  			
		  			//get gpa and cgpa
		  			$studentGradeDb = new App_Model_Exam_DbTable_StudentGrade();
		  			$grade = $studentGradeDb->getGradebySemester($registration_id,$sem["idstudentsemsterstatus"]);
		  			$semester[$key]["sem_credithour"] = $grade["sg_univ_sem_credithour"];
		  			$semester[$key]["cum_credithour"] = $grade["sg_cum_credithour"];	  			
		  			$semester[$key]["gpa"] = $grade["sg_univ_gpa"];
		  			$semester[$key]["cgpa"] = $grade["sg_cgpa"];
		  			$semester[$key]["sks_lulus"] = $grade["sg_sem_sks_lulus"];
		  			$semester[$key]["sks_gagal"] = $grade["sg_sem_sks_gagal"];	

		  			//get publish date
		  			$publishMarkDb = new App_Model_Exam_DbTable_PublishMark();
	    			$publish = $publishMarkDb->getPublishResult($student["IdProgram"],$sem["IdSemesterMain"]);
	    			$semester[$key]["publish_date"] = $publish["pm_date"];
		  			
	         	} 
	         	//echo var_dump($semester);exit;
	         	
           		
         }elseif($landscape["LandscapeType"]==44){
         	
	         	//get registered blocks
	         	$studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
	         	$blocks = $studentSemesterDB->getRegisteredSemesterBlockKHS($registration_id);         	
				
	         	foreach($blocks as $key=>$block){
			
		         	//get course registered  by block
		  			$courseRegisterDb = new App_Model_Record_DbTable_StudentRegistration();
		  			$courses = $courseRegisterDb->getCourseRegisteredBySemesterBlock($registration_id,$block["IdSemesterMain"],$block["IdBlock"]);
		  			//echo var_dump($courses);exit;
					foreach ($courses as $index=>$value) {
						if ($value['IdSemesterMain']!=''&& $value['IdProgram']!=''&& $value['IdSubject']!=''&& $value['IdCourseTaggingGroup']!='') {
		  					if ($dbPublish->isAllMarkShown($value['IdSemesterMain'], $value['IdProgram'], $value['IdSubject'], $value['IdCourseTaggingGroup'])) {
		  						$courses[$index]['publish']="1";
		  					} else $courses[$index]['publish']="0";
						} else $courses[$index]['publish']="0";
		  			}
		  			 
		  			$blocks[$key]["courses"]=$courses;
		  			
		  			//get gpa and cgpa

		  			$studentGradeDb = new App_Model_Exam_DbTable_StudentGrade();
		  			$grade = $studentGradeDb->getGradebySemester($registration_id,$block["idstudentsemsterstatus"]);
		  			
					$blocks[$key]["blockname"] = '';
					if(isset($block["blockname"]))
						$blocks[$key]["blockname"] = $block["blockname"];
		  			
					$blocks[$key]["sem_credithour"] = $grade["sg_sem_credithour"];
		  			$blocks[$key]["cum_credithour"] = $grade["sg_cum_credithour"];	  			
		  			$blocks[$key]["gpa"] = $grade["sg_gpa"];
		  			$blocks[$key]["cgpa"] = $grade["sg_cgpa"];
		  			$blocks[$key]["sks_lulus"] = $grade["sg_sem_sks_lulus"];
		  			$blocks[$key]["sks_gagal"] = $grade["sg_sem_sks_gagal"];	
		  			
		  			
		  			//get publish date
		  			$publishMarkDb = new App_Model_Exam_DbTable_PublishMark();
	    			$publish = $publishMarkDb->getPublishResult($student["IdProgram"],$block["IdSemesterMain"]);
	    			$blocks[$key]["publish_date"] = $publish["pm_date"];
	    			
		  			$semester = $blocks;
		  			
	         	}
	         	
         }
         
                 
         $this->view->semester = $semester;
         
         
         //get available publish date
         $publishMarkDb = new App_Model_Exam_DbTable_PublishMark();
         $this->view->all_date_publish = $publishMarkDb->publishAvailableDate($registration_id,$student["IdProgram"],$landscape["LandscapeType"]);
         
    }
    
    
    public function viewDetailAction(){
    	
    	// disable layouts for this action:
        $this->_helper->layout->disableLayout();
        
        //get applicant profile
    	$auth = Zend_Auth::getInstance();  
    	
    	$IdStudentRegistration = $auth->getIdentity()->registration_id; 
    	$this->view->IdStudentRegistration = $IdStudentRegistration; 
    	
    	$idSemester = $this->_getParam('semester',0);    	
    	$idProgram = $this->_getParam('program', 0);    	
    	$idSubject = $this->_getParam('subject', 0);
    	
    	$this->view->idSemester = $idSemester;
    	$this->view->idProgram  = $idProgram;
    	$this->view->idSubject  = $idSubject;
    	
    	//get course info
    	$courseDb= new App_Model_Record_DbTable_SubjectMaster();
    	$this->view->subject = $courseDb->getData($idSubject);
    	
    	//get semester info
    	$semesterDb = new App_Model_Record_DbTable_SemesterMain();
    	$this->view->semester = $semesterDb->getData($idSemester);
    	
    	//keluarkan mark distribution component
    	$MarkDistributionDB = new App_Model_Exam_DbTable_MarkDistribution();
    	$component = $MarkDistributionDB->getListMainComponent($idSemester,$idProgram,$idSubject);
    	
    	//$program
    	$dbProgram=new App_Model_General_DbTable_Program();
    	$program=$dbProgram->fngetProgramData($idProgram);
    	$attendancemode=$program['Attendance_cal_mode'];
    	$appealDB = new App_Model_Exam_DbTable_Appeal();
    	//get student course group
    	$courseGroupDB = new App_Model_Registration_DbTable_CourseGroup();
    	$group = $courseGroupDB->getStudentCourseGroup($IdStudentRegistration,$idSemester,$idSubject);
    	$grup=$group['IdCourseTaggingGroup'];
    	if(count($component)>0){
	    	foreach($component as $index=>$comp){
	    		
	    		$subjectMarkDB = new App_Model_Exam_DbTable_StudentMarkEntry();
	    		$subject = $subjectMarkDB->getSubjectMark($idSemester,$IdStudentRegistration,$idSubject,$comp["IdMarksDistributionMaster"]);
	    		$component[$index]["TotalMarkObtained"]=$subject["TotalMarkObtained"];
	    		$component[$index]["FinalTotalMarkObtained"]=$subject["FinalTotalMarkObtained"];
	    		
	    		$publishMarkDb = new App_Model_Exam_DbTable_PublishMark();
	    		$publish = $publishMarkDb->getData($idProgram,$idSemester,$idSubject,$group["IdCourseTaggingGroup"],$comp["IdMarksDistributionMaster"],1);
	    		$component[$index]["publish_date"]=$publish["pm_date"];
	    		
	    		//appeal status
	    		$appeal = $appealDB->getInfo($IdStudentRegistration,$idSemester,$idSubject,$comp["IdMarksDistributionMaster"]);	    		
	    		$component[$index]["appeal"]=$appeal;
	    		
	    		//resit status
	    		$resitDb = new App_Model_Exam_DbTable_Resit();
				$resit = $resitDb->getInfo($IdStudentRegistration,$idSemester,$idSubject,$comp["IdMarksDistributionMaster"]);
				$component[$index]["resit"]=$resit;	
	    	
	    	}
    	}
    	
    	 
    	
    	$grpAttDtlDb = new App_Model_Exam_DbTable_CourseGroupStudentAttendanceDetail();
    	$attendance['Attend']=$grpAttDtlDb->getAttendanceStatusCount($grup,$IdStudentRegistration,395);
    	$attendance['Absent']=$grpAttDtlDb->getAttendanceStatusCount($grup,$IdStudentRegistration,398);
    	$attendance['Permission']=$grpAttDtlDb->getAttendanceStatusCount($grup,$IdStudentRegistration,396);
    	$attendance['Sick']=$grpAttDtlDb->getAttendanceStatusCount($grup,$IdStudentRegistration,397);
    	
    	if ($attendancemode=="123") $nOfattend =$attendance['Permission']+$attendance['Sick']+$attendance['Attend'];
    	else if ($attendancemode=="12") $nOfattend=$attendance['Sick']+$attendance['Attend'];
    	else if ($attendancemode=="1") $nOfattend =$attendance['Attend'];
    	
    	$attendance['Lecture']=$grpAttDtlDb->getAttendanceSessionCount($grup,$IdStudentRegistration);
    	
    	if ($attendance['Lecture']>0) 
    		$attendance['Percentage']=($nOfattend/$attendance['Lecture']*100);
    	else 
    		$attendance['Percentage']=0;
    	
    	$attendance['grup']=$grup;
    	$attendance['idstd']=$IdStudentRegistration;
    	$attendance['idsubject']=$idSubject;
    	
    	
    	$component['Attendance']=$attendance;
    	
    	
    	$this->view->component = $component;
    }
   
    
      public function resultAction(){
    	
    	$this->view->title = "Examination Result";
    	
      }
      
    
      
     public function cetakKhsAction(){
    	
    	$this->view->title = "Kartu Hasil Studi";
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    	
    	$registration_id = $auth->getIdentity()->registration_id;
    	$this->view->IdStudentRegistration = $registration_id;
    	 
    	$idSemesterStatus = $this->_getParam('idSemesterStatus',null);
    	
    	//$this->_redirect("http://www.print.trisakti.ac.id/student-portal/cetak-khs/idSemesterStatus/".$idSemesterStatus.'/registration_id/'.$registration_id);
    	 
    	global $semester;
    	global $courses;
    	
    	
    	//To get Student Academic Info        
        $studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
        $student = $studentRegDB->getStudentInfo($registration_id);
        if($student["majoring"]=="common"|$student["majoring"]=="Bersama"){
        	$student["majoring"]="-";
        	$student["majoring_english"]="-";
        }
        global $majoring;
        $majoring=$student["majoring"];
        global $printmajoring;
        $printmajoring=$student['print_majoring'];
        //brach
        $branchDb=new App_Model_General_DbTable_Branchofficevenue();
        $branch=$branchDb->fnviewBranchofficevenueDtls($student['IdBranch']);
        //get info majoring
        $majorDb = new App_Model_General_DbTable_Program();
        $major = $majorDb->fnviewMajoring($student['IdProgramMajoring']);
        if ($major['Address1']=='') {
        	$addphone=$branch["Phone"];
        	$addemail=$branch["Email"];
        	$add=$branch["Addr1"].' '.$branch["Addr2"].' '.$branch["StateName"].' '.ucwords(strtolower($branch["CountryName"]));
        } else {
        	$addphone=$major["Phone"];
        	$addemail=$major["Email"];
        	$add=$major["Addr1"].' '.$major["Addr2"].' '.$major["StateName"].' '.ucwords(strtolower($major["CountryName"]));
        }
        //get info college
    	$collegedB = new GeneralSetup_Model_DbTable_Collegemaster();
        $college = $collegedB->getFullInfoCollege($student["IdCollege"]);
        
        //get salutation
    	$defDB = new App_Model_General_DbTable_Definationms();
    	$academic_front_salutation = $defDB->getData($student["FrontSalutation"]);
    	$academic_back_salutation  = $defDB->getData($student["BackSalutation"]);
    	    	 
        //get photo student
    	$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
    	$file = $uploadFileDb->getFile($student["transaction_id"],51);
    	    	
    	if(isset($file["pathupload"])){   
    		if (file_exists($file["pathupload"])) { 		
    			$photo_url  = $file["pathupload"];
    		}else{
    			$photo_url  = "/var/www/html/triapp/public/images/no_image.gif";
    		}
    	}else{
    		    $photo_url  = "/var/www/html/triapp/public/images/no_image.gif";
    	}
    	
    	$dbPublish=new Examination_Model_DbTable_PublishMark();	
    	 //To get Registered Courses   
         $landscapeDb = new App_Model_Record_DbTable_Landscape();
         $landscape = $landscapeDb->getData($student["IdLandscape"]);
         
         if($landscape["LandscapeType"]==43) {//Semester Based        	
         	         	
         	if(isset($idSemesterStatus)){
         		
         		    //get semester regsiter info
         		    $studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
		         	$semesterStudi = $studentSemesterDB->getSemesterInfo($idSemesterStatus);		         	
         		
         			//get course registered  per semester
		  			$courseRegisterDb = new App_Model_Record_DbTable_StudentRegistration();
		  			$courses = $courseRegisterDb->getCourseRegisteredBySemester($registration_id,$semesterStudi["IdSemesterMain"]);
		  			foreach ($courses as $index=>$value) {
		  				 
		  				if ($dbPublish->isAllMarkPublished($value['IdSemesterMain'], $value['IdProgram'], $value['IdSubject'], $value['IdCourseTaggingGroup'])) {
		  					$courses[$index]['publish']="1";
		  				} else $courses[$index]['publish']="0";
		  				
		  			}
		  			 
		  			$semester[0]["courses"]=$courses;		  			
		  			
		  			//get gpa and cgpa
		  			$studentGradeDb = new App_Model_Exam_DbTable_StudentGrade();
		  			$grade = $studentGradeDb->getGradebySemester($registration_id,$idSemesterStatus);
		  			$semester[0]["sem_credithour"] = $grade["sg_sem_credithour"];
		  			$semester[0]["cum_credithour"] = $grade["sg_cum_credithour"];	  			
		  			$semester[0]["gpa"] = $grade["sg_univ_gpa"];
		  			$gpa=$grade["sg_univ_gpa"];
		  			$semester[0]["cgpa"] = $grade["sg_cgpa"];	
		  			$semester[0]["sks_lulus"] = $grade["sg_sem_sks_lulus"];
		  			$semester[0]["sks_gagal"] = $grade["sg_sem_sks_gagal"];	
		  			
         	}	         	  
         	    
           		
         }elseif($landscape["LandscapeType"]==44){
         	
         	if(isset($idSemesterStatus)){
         		 
         		//get semester regsiter info
         		$studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
         		$semesterStudi = $studentSemesterDB->getSemesterInfo($idSemesterStatus);
		         	//get registered blocks
		         	$studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
		         	$blocks = $studentSemesterDB->getRegisteredSemesterBlock($registration_id,$semesterStudi["IdSemesterMain"]);         	
		         	
		         	foreach($blocks as $key=>$block){
				
			         	//get course registered  by block
			  			$courseRegisterDb = new App_Model_Record_DbTable_StudentRegistration();
			  			$courses = $courseRegisterDb->getCourseRegisteredBySemesterBlock($registration_id,$block["IdSemesterMain"],$block["IdBlock"]);
			  			foreach ($courses as $index=>$value) {
			  				if ($dbPublish->isAllMarkPublished($value['IdSemesterMain'], $value['IdProgram'], $value['IdSubject'], $value['IdCourseTaggingGroup'])) {
			  					$courses[$index]['publish']="1";
			  				} else $courses[$index]['publish']="0";
			  			}
			  			$blocks[$key]["courses"]=$courses;
			  		    $semesterStudi['SemesterMainName']=$block["SemesterMainName"];
			  			
			  			//get gpa and cgpa
			  			$studentGradeDb = new App_Model_Exam_DbTable_StudentGrade();
			  			$grade = $studentGradeDb->getGradebySemester($registration_id,$block["idstudentsemsterstatus"]);
			  			$blocks[$key]["sem_credithour"] = $grade["sg_sem_credithour"];
			  			$blocks[$key]["cum_credithour"] = $grade["sg_cum_credithour"];	  			
			  			$blocks[$key]["gpa"] = $grade["sg_univ_gpa"];
			  			$gpa=$grade["sg_univ_gpa"];
			  			$blocks[$key]["cgpa"] = $grade["sg_cgpa"];
			  			$blocks[$key]["sks_lulus"] = $grade["sg_sem_sks_lulus"];
		  			    $blocks[$key]["sks_gagal"] = $grade["sg_sem_sks_gagal"];
		  			    
		  				$semester = $blocks;	
			  			
		         	}
         	}
		         	 
         }
        
         $chlimitDB = new App_Model_Registration_DbTable_Chlimit();
         $limit=$chlimitDB->getLimit($student['IdProgram'], $student['IdIntake'], $gpa);
         
         $fieldValues = array(
    	  '$[PROGRAM]'=>$student["ArabicName"],
          '$[STRATA]'=>$student["strata"],
    	  '$[FACULTY]'=>'FAKULTAS '.$student["CollegeName"],
    	  '$[NIM]'=>$student["registrationId"],
    	  '$[NAME]'=>$student["appl_fname"].' '.$student["appl_mname"].' '.$student["appl_lname"],    	 
    	  '$[ACADEMIC_ADVISOR]'=>$academic_front_salutation["BahasaIndonesia"].' '.$student["AcademicAdvisor"].' '.$academic_back_salutation["BahasaIndonesia"],    	 
    	  '$[PHOTO]'=>$photo_url,    	 
		  '$[ADDRESS]'=>ucwords(strtolower($college["Add1"])).' '.ucwords(strtolower($college["Add2"])).' '.ucwords(strtolower($college["CityName"])).' '.ucwords(strtolower($college["StateName"])),
		  '$[PHONE]'=>$college["Phone1"],
		  '$[EMAIL]'=>$college["Email"],
          '$[SEMESTER]'=>$semesterStudi["SemesterMainName"],
    	  '$[SKS_LULUS]'=>$grade["sg_sem_sks_lulus"],
    	  '$[SKS_GAGAL]'=>$grade["sg_sem_sks_gagal"],
    	  '$[TOTAL_SKS]'=>$grade["sg_univ_sem_credithour"],
    	  '$[SKS_KUMULATIF]'=>$grade["sg_cum_credithour"],
          '$[STRATA]'=>$student["strata"],
    	  '$[IPS]'=>$grade["sg_univ_gpa"],
          '$[limit]'=>$limit,
    	  '$[IPK]'=>$grade["sg_cgpa"],
          '$[KONSENTRASI]'=>$student["majoring"],
          '$[MAJORING]'=>$student["majoring_english"],
         		'$[B_ADDRESS]'=>$add,
         		'$[B_PHONE]'=>$addphone,
         		'$[B_EMAIL]'=>$addemail
    	);
    	
    	
    	
    	/* ------------------------------
    	 * start create directrory folder
    	 * ------------------------------ */
    	   
		//$location_path
		$location_intake_path = DOCUMENT_PATH."/student/".$student["IdIntake"];
		
        //create directory to locate file			
		if (!is_dir($location_intake_path)) {
	    	mkdir($location_intake_path, 0775);
		}
		
		
        //$location_path
		$location_program_path = $location_intake_path."/".$student["ProgramCode"];
		
        //create directory to locate file			
		if (!is_dir($location_program_path)) {
	    	mkdir($location_program_path, 0775);
		}
		
		//output_directory_path
		$output_directory_path = $location_program_path."/".$student["registrationId"];
		
        //create directory to locate file			
		if (!is_dir($output_directory_path)) {
	    	mkdir($output_directory_path, 0775);
		}			
		
		//creating folder student
		if($student["repository"]==''){
			$studentRegDB->updateData(array('repository'=>"student/".$student["IdIntake"]."/".$student["ProgramCode"]."/".$student["registrationId"]),$registration_id);
		}		
				
		//output filename 
		$output_filename = $student["registrationId"]."_kartu_hasil_studi.pdf";
		
		//to rename output file			
		$output_file_path = $output_directory_path."/".$output_filename;
		 		
		
		/* ------------------------------
    	 * end  create directrory folder
    	 * ------------------------------ */
    	
    		
    	require_once 'dompdf_config.inc.php';
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		//template path	 
		$html_template_path = DOCUMENT_PATH."/template/khs.html";
		
		$html = file_get_contents($html_template_path);			
    		
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);	
		}
	
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		@$dompdf->render();
		
		//$dompdf = $dompdf->output();
		@$dompdf->stream($output_filename);						
		
		file_put_contents($output_file_path, $dompdf);
		
		$this->view->file_path = $output_file_path;
		
    	
    	
     }
    
     	 public function assessmentAction(){
    	
    	 $this->view->title = "";
    	 
    	 // disable layouts for this action:
        $this->_helper->layout->disableLayout();
        
    	 //get applicant profile
    	 $auth = Zend_Auth::getInstance();    	
    
    	 //print_r($auth->getIdentity());
    	
    	 $appl_id = $auth->getIdentity()->appl_id; 
    	 $registration_id = $auth->getIdentity()->registration_id;    

    	 $this->view->appl_id = $appl_id;
    	 $this->view->IdStudentRegistration = $registration_id;
    	 
    	 //To get Student Academic Info        
         $studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
         $student = $studentRegDB->getStudentInfo($registration_id);
    	 $this->view->student = $student;
    	   	
    	 
    	 //To get Registered Courses   
         $landscapeDb = new App_Model_Record_DbTable_Landscape();
         $landscape = $landscapeDb->getData($student["IdLandscape"]);
         $this->view->landscape = $landscape;
         $semester=array();
         if($landscape["LandscapeType"]==43) {//Semester Based         	
         	
	         	//get total registered semester 
	         	$studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
	         	$semester = $studentSemesterDB->getRegisteredSemester($registration_id);
	         	
	         	foreach($semester as $key=>$sem){
				
					//get course registered  per semester
		  			$courseRegisterDb = new App_Model_Record_DbTable_StudentRegistration();
		  			$courses = $courseRegisterDb->getCourseRegisteredBySemester($registration_id,$sem["IdSemesterMain"]);
		  			$semester[$key]["courses"]=$courses;
		  						
		  			
	         	}
	         	
           		
         }elseif($landscape["LandscapeType"]==44){
         	
	         	//get registered blocks
	         	$studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
	         	$blocks = $studentSemesterDB->getRegisteredSemesterBlock($registration_id);         	
	        
	         	foreach($blocks as $key=>$block){
			
		         	//get course registered  by block
		  			$courseRegisterDb = new App_Model_Record_DbTable_StudentRegistration();
		  			$courses = $courseRegisterDb->getCourseRegisteredBySemesterBlock($registration_id,$block["IdSemesterMain"],null);
		  			$blocks[$key]["courses"]=$courses;
		  			
		  			$semester = $blocks;
		  			
	         	}
	         	
         }
         
         $this->view->semester = $semester;
         
        /* echo '<pre>';
          print_r($semester);         
         echo '<pre>';*/
    }
    
   
    
    public function appealAction(){
    	
    	 // disable layouts for this action:
         $this->_helper->layout->disableLayout();
        
    	 //get applicant profile
    	 $auth = Zend_Auth::getInstance();    	
    
    	 $appl_id = $auth->getIdentity()->appl_id; 
    	 $registration_id = $auth->getIdentity()->registration_id;    

    	 $this->view->appl_id = $appl_id;
    	 $this->view->IdStudentRegistration = $registration_id;
    	
    	 $idSemester = $this->_getParam('semester',0);
    	 $idSubject = $this->_getParam('subject', 0);
    	 $idComponent = $this->_getParam('id', 0);   
    	 
    	 $this->view->idSemester = $idSemester;
    	 $this->view->idComponent  = $idComponent;
    	 $this->view->idSubject  = $idSubject;
    	
    	 //get course info
    	 $courseDb= new App_Model_Record_DbTable_SubjectMaster();
    	 $this->view->subject = $courseDb->getData($idSubject);
    	
    	 //get semester info
    	 $semesterDb = new App_Model_Record_DbTable_SemesterMain();
    	 $this->view->semester = $semesterDb->getData($idSemester);
    	
    	 //get compinent info
    	 $MarkDistributionDB = new App_Model_Exam_DbTable_MarkDistribution();
    	 $this->view->component = $MarkDistributionDB->getComponentInfo($idComponent);
  
    }
    
     public function applyAppealAction(){
    	
     	 $auth = Zend_Auth::getInstance();    	
     
     	 // disable layouts for this action:
         $this->_helper->layout->disableLayout();
         
         	if ($this->getRequest()->isPost()) {
		
         		if ($this->getRequest()->isPost()) {
			
					$formData = $this->getRequest()->getPost();	
		
					$ajaxContext = $this->_helper->getHelper('AjaxContext');
					$ajaxContext->addActionContext('view', 'html');
					$ajaxContext->initContext();
					
					$data["sa_idStudentRegistration"]=$auth->getIdentity()->registration_id;
					$data["sa_idSubject"]=$formData["subject"];
					$data["sa_idSemester"]=$formData["semester"];
					$data["sa_idStudentRegSubject"]=1;
					$data["sa_idComponent"]=$formData["component"];
					$data["sa_applyDate"]=date("Y-m-d H:i:s");
					$data["sa_applyBy"]=$auth->getIdentity()->appl_id;
					$data["sa_status"]=1;
					
					$appealDb = new App_Model_Exam_DbTable_Appeal();
					$id = $appealDb->addData($data);
					
					$ajaxContext->addActionContext('view', 'html')
							->addActionContext('form', 'html')
							->addActionContext('process', 'json')
							->initContext();
			
					$json = Zend_Json::encode(array('id'=>$id));
				
					echo $json;
					exit();
         		}
	
         	}
     }
     
     
	public function resitAction(){
    	
    	 // disable layouts for this action:
        // $this->_helper->layout->disableLayout();
         $resitDb = new App_Model_Exam_DbTable_Resit();
         $resitmasterDb = new App_Model_Exam_DbTable_ResitMaster();
    	 //get applicant profile
    	 $auth = Zend_Auth::getInstance();    	
    
    	 $appl_id = $auth->getIdentity()->appl_id; 
    	 $registration_id = $auth->getIdentity()->registration_id;    

    	 $this->view->appl_id = $appl_id;
    	 $this->view->IdStudentRegistration = $registration_id;
    	 $dbStd=new App_Model_Registration_DbTable_Studentregistration();
    	 $std=$dbStd->getStudentRegistrationDetail($registration_id);
    	 
    	 $idSemester = $this->_getParam('semester',0);
    	 $idSubject = $this->_getParam('subject', 0);
    	 $idHead = $this->_getParam('idHead', 0);
    	 $idComponent = $this->_getParam('id', 0);   
    	 
    	 $this->view->idSemester = $idSemester;
    	 $this->view->idComponent  = $idComponent;
    	 $this->view->idSubject  = $idSubject;
    	 $this->view->idHead  = $idHead;
    	 if ($this->getRequest()->isPost()) {
    	 
    	 	$formData = $this->getRequest()->getPost();
    	 	$idSemester=$formData['IdSemester'];
    	 	$idComponent=$formData['IdComponent'];
    	 	$idSubject=$formData['IdSubject'];
    	 	$idHead=$formData['idHead'];
    	 	if ($idHead>0) {
    	 		$dbHeader=new App_Model_Exam_DbTable_MarksDepositHeader();
    	 		$markhead=$dbHeader->getDataById($idHead);
    	 		if ($markhead) {
    	 			$idSubject=$markhead['IdSubject'];
    	 		} 
    	 	}
    	 	$IdStudentRegistration=$formData['IdStudentRegistration'];
    	 	$this->view->idSemester = $idSemester;
    	 	$this->view->idComponent  = $idComponent;
    	 	$this->view->idSubject  = $idSubject;
    	 	if (isset($formData['charge']) && $formData['charge']!='')
    	 		$charge=$formData['charge'];
    	 	else $charge=0;
    	 	//$IdStudentRegistration = $this->_getParam('IdStudentRegistration', 0);
    	 	$std=$dbStd->getStudentRegistrationDetail($IdStudentRegistration);
    	 	
    	 	//if ($IdStudentRegistration=='')  $IdStudentRegistration=$auth->getIdentity()->registration_id;
    	 	
    	 	$row=$resitmasterDb->isInMaster($IdStudentRegistration, $idSemester,$idSubject);
    	 		
    	 	if (!$row) {
    	 		$data["sr_idStudentRegistration"]=$IdStudentRegistration;
    	 		$data["sr_idSubject"]=$idSubject;
    	 		$data["sr_idSemester"]=$idSemester;
    	 		$data["sr_applyDate"]=date("Y-m-d H:i:s");
    	 		$data["sr_applyBy"]=$auth->getIdentity()->appl_id;
    	 		$data["sr_status"]="1";
    	 		$idresitmaster=$resitmasterDb->addData($data);
    	 	} else $idresitmaster=$row['sr_id_master'];
    	 	//get compinent info
    	 	$MarkDistributionDB = new App_Model_Exam_DbTable_MarkDistribution();
    	 	$mark=$MarkDistributionDB->getComponentInfo($idComponent);
    	 	//echo $idComponent;exit;
    	 	$data=array();
    	 		
    	 	$data["sr_id_master"]=$idresitmaster;
    	 	$data["sr_idComponent"]=$idComponent;
    	 	$data["sr_idComponentType"]=$mark["IdComponentType"];
    	 	$data["sr_charge"]=$charge;
    	 	$data["sr_applyDate"]=date("Y-m-d H:i:s");
    	 	$data["sr_applyBy"]=$auth->getIdentity()->appl_id;
    	 	$data["sr_status"]="1";
    	 		
    	 	$rowresit=$resitDb->getInfo($idresitmaster, $idComponent);
    	 		
    	 	if (!$rowresit) {
    	 		//echo var_dump($rowresit);exit;
    	 		$id = $resitDb->addData($data);
    	 	} else {
    	 		$id=$rowresit['sr_id'];
    	 		$resitDb->updateData($data,$id);
    	 	}
    	 	//componenttype
    	 	$dbCompnent=new App_Model_Exam_DbTable_Assessmenttype();
    	 	$comp=$dbCompnent->fnGetAssesmentTypeNamebyID($mark['IdComponentType']);
    	 	
    	 	//subject
    	 	$dbSubject=new App_Model_General_DbTable_Subjectmaster();
    	 	$subject=$dbSubject->getData($idSubject);
    	 	
    	 	$semesterDb = new App_Model_General_DbTable_Semestermaster();
    	 	$semester = $semesterDb->fnGetSemestermaster($idSemester);
    	 		
    	 	$description="Pembayaran Resit -".$comp['IdDescription'].'-'.$subject['ShortName'].'-'.$semester['SemesterMainName'];
    	 	//Intake
			$dbIntake=new App_Model_Record_DbTable_Intake();
			$intake=$dbIntake->getData($std['IdIntake']);
			
			//academic year
			$academicYearDb = new App_Model_Record_DbTable_AcademicYear();
			$academicYear = $academicYearDb->getData($semester['idacadyear']);

			 
			//program
			$programDb = new App_Model_General_DbTable_Program();
			$program = $programDb->fngetProgramData($std['IdProgram']);
			$this->view->program=$program;
			
			//exam grouping
			$dbExam=new App_Model_Exam_DbTable_ExamGroup();
			$exam=$dbExam->getDataByExamtype($idSemester, $idSubject, $program['IdProgram'],406);
			$examdate=$exam['eg_date'];
			$expireddate=$examdate->sub(new DateInterval('P1D'));
    	 	$dbInvoice=new Studentfinance_Model_DbTable_InvoiceMain();
    	 	$dbInvoiceDet=new Studentfinance_Model_DbTable_InvoiceDetail();
    	 	$seq_data = array(
    	 			date('y',strtotime($academicYear['ay_start_date'])),
    	 			substr($intake['IntakeId'],2,2),
    	 			$program['ProgramCode'], 0
    	 	);
    	 	
    	 	$db = Zend_Db_Table::getDefaultAdapter();
    	 	$stmt = $db->prepare("SELECT invoice_seq(?,?,?,?) AS invoice_no");
    	 	$stmt->execute($seq_data);
    	 	$invoice_no = $stmt->fetch();
    	 	
    	 	$inv_data = array(
    	 			'bill_number' => $invoice_no['invoice_no'],
    	 			'appl_id' => $std['IdApplication'],
    	 			'IdStudentRegistration' => $IdStudentRegistration,
    	 			'academic_year' => $semester['idacadyear'],
    	 			'semester' => $idSemester,
    	 			'bill_amount' =>$charge,
    	 			'bill_paid' => 0.00,
    	 			'bill_balance' => $charge,
    	 			'bill_description' => $description,
    	 			'college_id' => $program['IdCollege'],
    	 			'program_code' => $program['ProgramCode'],
    	 			'creator' => '1',
    	 			'fs_id' => 0,
    	 			'status' => 'A',
    	 			'date_create' => date('Y-m-d H:i:s'),
    	 			'idactivity'=>43 //43 pembayaran resit
    	 	);
    	 	if ($formData["idinvoice"]!='') {
    	 		$invoice_id = $dbInvoice->insert($inv_data);
    	 		$dbFeeitem=new Studentfinance_Model_DbTable_FeeItem();
    	 		//insert invoice detail
    	 		 
    	 			$inv_detail_data = array(
    	 					'invoice_main_id' => $invoice_id,
    	 					'fi_id' => 42,
    	 					'fee_item_description' => 'Biaya Resit',
    	 					'amount' => $charge
    	 			);
    	 	
    	 			$dbInvoiceDet->insert($inv_detail_data);
    	 		 
    	 	}
    	 		
    	 	 
    	 	$dateexprired=date('Y-m-d H:s:i',strtotime($expireddate));
    	 	$dbInvoice->pushToEColl($invoice_id, $dateexprired,'createbilling');
    	 	 
    	 	 
    	 	
    	 	 
    	 }
    	 //get course info
    	 $courseDb= new App_Model_Record_DbTable_SubjectMaster();
    	 $this->view->subject = $courseDb->getData($idSubject);
    	 
    	 //get resit Configuration
    	 $dbresitconf=new App_Model_Exam_DbTable_ResitConfig();
    	 $config=$dbresitconf->getConfig(1, $std['IdProgram'], $std['IdProgramMajoring'], $std['IdBranch'], $idSubject);
    	 if ($config)
    		 $this->view->config=$config;
    	 else  $this->view->config=array();
    	// echo var_dump($config);exit;
    	 //get semester info
    	 $semesterDb = new App_Model_Record_DbTable_SemesterMain();
    	 $this->view->semester = $semesterDb->getData($idSemester);
    	
    	 //get compinent info
    	 $MarkDistributionDB = new App_Model_Exam_DbTable_MarkDistribution();
    	 $component=$MarkDistributionDB->getComponentInfo($idComponent);
    	 $this->view->component = $component;
    	
    	 //get course group
    	 $courseGroupStudentDb = new App_Model_General_DbTable_CourseGroupStudent();
    	 $classGroup = $courseGroupStudentDb->checkStudentCourseGroup($registration_id,$idSemester,$idSubject);
		 if ($classGroup) {
	    	 $this->view->course=$classGroup;
	    	 //get charge infoo
	    	// echo var_dump($classGroup);exit;
	    	 $dbresitfee=new App_Model_Exam_DbTable_ResitFee();
	    	 $fee=$dbresitfee->getInfo($classGroup['ProgramCreator'],$idSubject,$component['IdComponentType']);
	    	// echo var_dump($fee);exit;
	    	 $this->view->fee=$fee;
		 } else {
		 	$classGroup['IdStudentRegSubjects']=0;
		 	$this->view->course=array();
		 	$this->view->fee=array();
		 }
    	 //get tsudent_mark
    	    	 
    	 $studentMarkEntryDb = new Examination_Model_DbTable_StudentMarkEntry();
    	 $mark = $studentMarkEntryDb->getMark($registration_id, $idComponent,$classGroup['IdStudentRegSubjects'], $idSemester,$idHead);
    	
    	 $this->view->mark=$mark;
    	 //get ressult if any
		
    	 $resit=$resitmasterDb->getInfo($registration_id, $idSemester, $idSubject, $idComponent);
    	 $this->view->resit=$resit;
    	 
    	 //get resit schedulle
    	 if ($resit) {
    	 	 $dbExamSch=new App_Model_Exam_DbTable_ExamGroupStudent();
    	 	 $this->view->resitschedule=$dbExamSch->getExamGroupSchedule($registration_id, $idSemester, $idSubject,$idComponent,"1");
    	 }
  		
    }
    
    public function cancelResitAction(){
    	 
    	// disable layouts for this action:
    	// $this->_helper->layout->disableLayout();
    	$resitDb = new App_Model_Exam_DbTable_Resit();
    	$resitmasterDb = new App_Model_Exam_DbTable_ResitMaster();
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    
    	$appl_id = $auth->getIdentity()->appl_id;
    	$registration_id = $auth->getIdentity()->registration_id;
    
    	$this->view->appl_id = $appl_id;
    	$this->view->IdStudentRegistration = $registration_id;
    	$dbStd=new App_Model_Registration_DbTable_Studentregistration();
    	$std=$dbStd->getStudentRegistrationDetail($registration_id);
    
    	$idSemester = $this->_getParam('semester',0);
    	$idSubject = $this->_getParam('subject', 0);
    	$idHead = $this->_getParam('idHead', 0);
    	$idComponent = $this->_getParam('id', 0);
     	if ($idHead>0) {
    			$dbHeader=new App_Model_Exam_DbTable_MarksDepositHeader();
    			$markhead=$dbHeader->getDataById($idHead);
    			if ($markhead) {
    				$idSubject=$markhead['IdSubject'];
    			}
    	}
    
    	$row=$resitmasterDb->isInMaster($registration_id, $idSemester,$idSubject);
    	if ($row) {
	    	$rowresit=$resitDb->getInfo($row['sr_id_master'], $idComponent);
	    	$resitDb->deleteData($rowresit['sr_id']);
	
	    	
	    	if (!$resitmasterDb->hasChild($row['sr_id_master']))
	    		$resitmasterDb->deleteData($row['sr_id_master']);
    	}
    
    }
    
    
	public function applyResitAction(){
    	
     	 $auth = Zend_Auth::getInstance();    	
     
     	 // disable layouts for this action:
         $this->_helper->layout->disableLayout();
         
				
         	//	if ($this->getRequest()->isPost()) {
			
					 $idSemester = $this->_getParam('semester',0);
    			 	 $idSubject = $this->_getParam('subject', 0);
    				 $idComponent = $this->_getParam('component', 0);  	
    				 $charge=$this->_getParam('charge', 0); 
    				 $IdStudentRegistration = $this->_getParam('IdStudentRegistration', 0);
					$ajaxContext = $this->_helper->getHelper('AjaxContext');
					$ajaxContext->addActionContext('view', 'html');
					$ajaxContext->initContext();
					if ($IdStudentRegistration=='')  $IdStudentRegistration=$auth->getIdentity()->registration_id;
					$resitDb = new App_Model_Exam_DbTable_Resit();
					$resitmasterDb = new App_Model_Exam_DbTable_ResitMaster();
					$row=$resitmasterDb->isInMaster($IdStudentRegistration, $idSemester,$idSubject);
					
					if (!$row) {
						$data["sr_idStudentRegistration"]=$IdStudentRegistration;
						$data["sr_idSubject"]=$idSubject;
						$data["sr_idSemester"]=$idSemester; 
						$data["sr_applyDate"]=date("Y-m-d H:i:s");
						$data["sr_applyBy"]=$auth->getIdentity()->appl_id;
						$data["sr_status"]=1;
						$idresitmaster=$resitmasterDb->addData($data);
					} else $idresitmaster=$row['sr_id_master'];
					//get compinent info
					$MarkDistributionDB = new App_Model_Exam_DbTable_MarkDistribution();
					$mark=$MarkDistributionDB->getComponentInfo($idComponent);
					//echo $idComponent;exit;
					$data=array();
					
					$data["sr_id_master"]=$idresitmaster;
					$data["sr_idComponent"]=$idComponent;
					$data["sr_idComponentType"]=$mark["IdComponentType"];
					$data["sr_charge"]=$charge;
					$data["sr_applyDate"]=date("Y-m-d H:i:s");
					$data["sr_applyBy"]=$auth->getIdentity()->appl_id;
					$data["sr_status"]=1;
					
					$rowresit=$resitDb->getInfo($idresitmaster, $idComponent);
					
					if (!$rowresit) {
						//echo var_dump($rowresit);exit;
						$id = $resitDb->addData($data);
					} else {
						$id=$rowresit['sr_id'];
						$resitDb->updateData($data,$id);
					}
					
					$ajaxContext->addActionContext('view', 'html')
							->addActionContext('form', 'html')
							->addActionContext('process', 'json')
							->initContext();
			
					$json = Zend_Json::encode(array('id'=>$id));
				
					echo $json;
					exit();
         		//}
	
         	
     }
    
    public function changePasswordAction()
    {
        $this->view->title = $this->view->translate('Change Password');
        
        $auth = Zend_Auth::getInstance();    	
        
    	$appl_id = $auth->getIdentity()->appl_id; 
    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$stdProfileDB=new App_Model_Student_DbTable_StudentProfile();
    	$applicant = $appProfileDB->getData($appl_id);
    	
        $this->view->applicant = $applicant;
        $this->view->incorrect = false;
        $this->view->msg       = false;
        
        if($this->getRequest()->isPost())
        {
            $formData = $this->getRequest()->getPost();
            //print_r($formData);
            if($formData['current_password'] == $applicant['appl_password'])
            {
                $saveData = array('appl_password' => $formData['new_password']);
                $appProfileDB->updateData($saveData,$appl_id);
                $stdProfileDB->updateData($saveData,$appl_id);
                $this->view->msg       = true;
            }
            else
            {
                $this->view->incorrect = true;
            }
        }
        
    }
    
    
    public function homeAction(){
    	
    	$this->view->title = $this->view->translate('Home');
        
        $auth = Zend_Auth::getInstance();   

       // echo '<pre>';print_r($auth->getIdentity());
     //   exit;
        $appl_id = $auth->getIdentity()->appl_id; 
    	$IdStudentRegistration = $auth->getIdentity()->registration_id;    
    	if($IdStudentRegistration==null){ //redirect dulu
    		
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'alumni-portal', 'action'=>'biodata'),'default',true));
    	}
    	$this->view->appl_id = $appl_id;
    	$this->view->IdStudentRegistration = $IdStudentRegistration;
    	    	        
    	$withdrawalDb = new App_Model_Registration_DbTable_Withdrawal();
    	
    	//To get Current Semester
    	$studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
    	$current_semester = $studentSemesterDB->getCurrentRegSem($IdStudentRegistration);
    	$this->view->semester = $current_semester;
    	
    	if(!$current_semester){ //redirect dulu
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'student-portal', 'action'=>'index'),'default',true));
    	}
    	
    	//get student landscape type
        $landscapeDb = new App_Model_Record_DbTable_Landscape();
        $landscape = $landscapeDb->getStudentLandscape($IdStudentRegistration);
        
    	//To get Subject List from current Semester
    	$registerSubjectDB = new App_Model_Record_DbTable_StudentRegSubjects();
    	$subject = $registerSubjectDB->getRegSubjectBySemId($IdStudentRegistration,$current_semester['IdSemesterMaster'],$landscape);
    	
    	//check activity calendar
    	$activityDb = new App_Model_Record_DbTable_Activity();
    	$activity = $activityDb->getActivity($current_semester['IdSemesterMaster'],$landscape['IdProgram']);
    	$this->view->activity = $activity;
    	$dbExternalPermission=new App_Model_General_DbTable_ExternalPermission();
    	$dbExtDetail=new App_Model_General_DbTable_ExternalDetail();
    	foreach($subject as $index=>$sub){
    		//cek for external link
    		$permission=$dbExternalPermission->getDataBySubject($sub['ProgramCreator'], $sub['IdSubject']);
    		if ($permission) {
    		 
    				foreach ($permission as $idx=>$permision) {
    					$login=$dbExtDetail->getDataByStd($permision['IdExternal'], 'login',null,$IdStudentRegistration);
    					if ($login) {
    						$permission[$idx]['username']=$login['username'];
    						$permission[$idx]['password']=$login['password'];
    					} else {
    						$permission[$idx]['username']='-';
    						$permission[$idx]['password']='-';
    					}
    				}
    				$subject[$index]['permission']=$permission;
    			 
    		}
    		//check status
    		$withdrawal= $withdrawalDb->getInfo($sub['IdStudentRegSubjects']);
    		
    		if(is_array($withdrawal)){
    			$subject[$index]['withdrawal']=$withdrawal;
    		}else{
    			$subject[$index]['withdrawal']= null;
    		}
    	}
    	
    	
    	$this->view->subject = $subject;
    }
    
    public function applyActivityAction(){
    	 
    	$this->view->title = $this->view->translate('Activity Application');
    
    	$auth = Zend_Auth::getInstance();
     
    	$appl_id = $auth->getIdentity()->appl_id;
    	$IdStudentRegistration = $auth->getIdentity()->registration_id;
    	$dbStd=new App_Model_Registration_DbTable_Studentregistration();
    	$std=$dbStd->getStudentRegistrationDetail($IdStudentRegistration);
    	$this->view->appl_id = $appl_id;
    	$this->view->IdStudentRegistration = $IdStudentRegistration;
     	$dbActivity=new App_Model_Activity_DbTable_ActivityGroup();
     	$dbSchedule=new App_Model_Activity_DbTable_ActivityGroupSchedule();
     	$dbSpeaker=new App_Model_Activity_DbTable_ActivityGroupScheduleLecturer();
     	$activities=$dbActivity->getData($std['IdProgram']);
    	foreach ($activities as $key=>$value) {
    		$sch=$dbSchedule->getScheduleByGroup($value['IdCourseTaggingGroup']);
    		foreach ($sch as $sc) {
    			;
    		}
    	}
    	$this->view->subject = $subject;
    }
    
     public function withdrawalAction(){
     	
     	 $auth = Zend_Auth::getInstance();   

     	 $IdStudentRegSubjects = $this->_getParam('IdStudentRegSubjects',0);
     	     	 
		 
     	 $data['IdStudentRegistration']=$auth->getIdentity()->registration_id;
     	 $data['IdStudentRegSubjects']=$IdStudentRegSubjects;
     	 $data['w_status']=1; // 1:apply 2:approve 3:reject
     	 $data['w_applydt']=date("Y-m-d H:i:s");
     	 $data['w_applyby']=$auth->getIdentity()->appl_id;
     	  
     	 //insert dalam table withdrawal
     	 $withdrawalDb = new App_Model_Registration_DbTable_Withdrawal();
     	 $withdrawalDb->addData($data);
     	      	 
     	 
     	  // ---- start track student log ----
		 $log['IdStudentRegistration']=$auth->getIdentity()->registration_id;
		 $log['IdStudentRegSubjects']=$IdStudentRegSubjects;
		 $log['log_type']=469; //Withdraw
		 $log['log_description']='Student Apply for Withdrawal';
		 $log['log_activity_date']=date("Y-m-d H:i:s");
		 $log['log_activity_by']=$auth->getIdentity()->appl_id;
						 		
		 $LogsDb = new App_Model_General_DbTable_StudentLogs();			
		 $LogsDb->addData($log);
		 // ---- end track student log ----
		
		
     	 $this->_redirect($this->view->url(array('module'=>'default','controller'=>'student-portal', 'action'=>'home'),'default',true));
     	
     }
     
     public function viewTranscriptAction(){
		
		$this->_helper->layout()->disableLayout(); 
        $this->view->title = "Daftar Prestasi Akademik";
		
		//get applicant profile
    	$auth = Zend_Auth::getInstance();    	
    
    	//print_r($auth->getIdentity());
    	
        $appl_id = $auth->getIdentity()->appl_id; 
        $IdStudentRegistration = $auth->getIdentity()->registration_id;    

    	$this->view->appl_id = $appl_id;
    	$this->view->IdStudentRegistration = $IdStudentRegistration;
        
        //$IdStudentRegistration = $this->_getParam('id',null);  
		$this->view->id= $IdStudentRegistration;
		
		 //To get Student Academic Info        
        $studentRegDB = new App_Model_Exam_DbTable_StudentRegistration();
        $student = $studentRegDB->getStudentInfo($IdStudentRegistration);
        $this->view->student = $student;
             
         //get photo student
    	$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
    	$file = $uploadFileDb->getFile($student["transaction_id"],51);
    	    	
		if(isset($file["pathupload"])){
    		if (file_exists($file["pathupload"])) {
    			
    			$fnImage = new icampus_Function_General_Image();
    			$photo_url = $fnImage->getImagePath($file['pathupload'],100,123);
    			//$photo_url = str_replace("/var/www/html/triapp", "", $file["pathupload"]);
    				
    			$this->view->photo_url  = $photo_url;
    		}else{
    			$this->view->photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
    		}
    	}else{
    		$this->view->photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
    	}
    	

    	$studentGradeDB = new App_Model_Exam_DbTable_StudentGrade();
    	$regSubjectDB = new App_Model_Exam_DbTable_StudentRegistrationSubject();
    	
    	$student_grade = $studentGradeDB->getStudentGradeInfo($IdStudentRegistration);
    	$this->view->student_grade = $student_grade;
    	if ($student_grade) $sem=$student_grade['sg_semesterId']; else $sem='1';
    	//get cgpa info
    	$gradeDb = new App_Model_Exam_DbTable_Academicstatus();
    	//echo $student_grade['sg_semesterId']."xx".$student['IdProgram'];exit;
    	$this->view->grade = $gradeDb->getListAcademicStatus($sem,$student['IdProgram'],$type=1,$basedon='Program');
    	    	
    	//get dean info
    	$deanDB = new App_Model_General_DbTable_Deanmaster();
    	$dean = $deanDB->getCollegeDean($student['IdCollege']);
    	$this->view->dean = $dean;
    	
    	//get salutatuion
    	$definationsDb = new App_Model_General_DbTable_Definationms();
    	$this->view->FrontSalutation = $definationsDb->getData($dean['FrontSalutation']);
    	$this->view->BackSalutation  = $definationsDb->getData($dean['BackSalutation']);
    	
    	//get category and course list
    	$subject_category = $regSubjectDB->getCategoryCourseRegistered($IdStudentRegistration);    	
    	foreach($subject_category as $index=>$category){
    		$subject_list = $regSubjectDB->getCourseRegistered($IdStudentRegistration,$category["idCategory"]);
    		$subject_category[$index]["subjects"] = $subject_list;
    	}    	
    	$this->view->subject_category = $subject_category;

    }
    
    public function viewTempTranscriptAction(){
	
		$this->_helper->layout()->disableLayout(); 
        $this->view->title = "Daftar Prestasi Akademik (Matakuliah Lulus)";
	
		//get applicant profile
    	$auth = Zend_Auth::getInstance();    	
    
    	//print_r($auth->getIdentity());
    	
        $appl_id = $auth->getIdentity()->appl_id; 
        $IdStudentRegistration = $auth->getIdentity()->registration_id;    

    	$this->view->appl_id = $appl_id;
    	$this->view->IdStudentRegistration = $IdStudentRegistration;
        
        //$IdStudentRegistration = $this->_getParam('id',null);  
		$this->view->id= $IdStudentRegistration;
        
         //To get Student Academic Info        
        $studentRegDB = new App_Model_Exam_DbTable_StudentRegistration();
        $student = $studentRegDB->getStudentInfo($IdStudentRegistration);
        $this->view->student = $student;
      
		
		if($student["majoring"]=="common"|$student["majoring"]=="Bersama"){
			$student["majoring"]="-";
			$student["majoring_english"]="-";
		}
	
		$this->view->student = $student;
		 
		//get photo student
		$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
		$file = $uploadFileDb->getFile($student["transaction_id"],51);
	
		if(isset($file["pathupload"])){
			if (file_exists($file["pathupload"])) {
				 
				$fnImage = new icampus_Function_General_Image();
				$photo_url = $fnImage->getImagePath($file['pathupload'],100,123);
				//$photo_url = str_replace("/var/www/html/triapp", "", $file["pathupload"]);
	
				$this->view->photo_url  = $photo_url;
			}else{
				$this->view->photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
			}
		}else{
			$this->view->photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
		}
		 
	
		$studentGradeDB = new App_Model_Exam_DbTable_StudentGrade();
		$regSubjectDB = new App_Model_Exam_DbTable_StudentRegistrationSubject();
		 
		$student_grade = $studentGradeDB->getStudentGradeInfo($IdStudentRegistration);
		$this->view->student_grade = $student_grade;
		 if ($student_grade) $sem=$student_grade['sg_semesterId'];else $sem=1;
		//get cgpa info
		$gradeDb = new App_Model_Exam_DbTable_Academicstatus();
		//echo $student_grade['sg_semesterId']."xx".$student['IdProgram'];exit;
		$this->view->grade = $gradeDb->getListAcademicStatus($sem,$student['IdProgram'],$type=1,$basedon='Program');
	
		//get dean info
		$deanDB = new App_Model_General_DbTable_Deanmaster();
		$dean = $deanDB->getCollegeDean($student['IdCollege']);
		$this->view->dean = $dean;
		 
		//get salutatuion
		$definationsDb = new App_Model_General_DbTable_Definationms();
		$this->view->FrontSalutation = $definationsDb->getData($dean['FrontSalutation']);
		$this->view->BackSalutation  = $definationsDb->getData($dean['BackSalutation']);
		 
		//transcript profile
		$DbProfile = new App_Model_General_DbTable_TranscriptProfile();
		$DbProfileDetail = new App_Model_General_DbTable_TranscriptProfileDetail();
		//print_r($student);
       /* */
		$idProfile =$student['idTranscriptProfile'];
		if ($idProfile==0) {
			$profile = $DbProfile->getStdTranscriptProfile($student['IdProgram'],$student['IdProgramMajoring'],$student['IdLandscape']);
			
			if(!isset($profile[0]['IdProfile'])){
				$idProfile = '*';
			}
			else
			{
				$idProfile = $profile[0]['IdProfile'];
			}
		}
        //get category and course list
		//echo $idProfile;exit;
		$subject_category =$this->getTranscriptList($IdStudentRegistration,$idProfile,'1');
		
		$db = new Finalassignment_Model_DbTable_FinalAssignment();
		$ta = $db->fnGetFinalAssigmentStd($IdStudentRegistration);
		//exit;
		if (isset($ta)) {
			$this->view->TaTitle=$ta['Title'];
			$this->view->TaTitleBahasa=$ta['TitleBahasa'];
		}else{
			$this->view->TaTitle=null;
			$this->view->TaTitleBahasa=null;
		}
		$this->view->subject_category = $subject_category;
		 
	
	}
	
	public function viewAllTranscriptAction(){
	
		$this->_helper->layout()->disableLayout();
		$this->view->title = "Daftar Prestasi Akademik (Keseluruhan)";
	
		//get applicant profile
		$auth = Zend_Auth::getInstance();
	
		//print_r($auth->getIdentity());
		 
		$appl_id = $auth->getIdentity()->appl_id;
		$IdStudentRegistration = $auth->getIdentity()->registration_id;
	
		$this->view->appl_id = $appl_id;
		$this->view->IdStudentRegistration = $IdStudentRegistration;
	
		//$IdStudentRegistration = $this->_getParam('id',null);
		$this->view->id= $IdStudentRegistration;
	
		//To get Student Academic Info
		$studentRegDB = new App_Model_Exam_DbTable_StudentRegistration();
		$student = $studentRegDB->getStudentInfo($IdStudentRegistration);
		$this->view->student = $student;
	
	
		if($student["majoring"]=="common"|$student["majoring"]=="Bersama"){
			$student["majoring"]="-";
			$student["majoring_english"]="-";
		}
	
		$this->view->student = $student;
			
		//get photo student
		$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
		$file = $uploadFileDb->getFile($student["transaction_id"],51);
	
		if(isset($file["pathupload"])){
			if (file_exists($file["pathupload"])) {
					
				$fnImage = new icampus_Function_General_Image();
				$photo_url = $fnImage->getImagePath($file['pathupload'],100,123);
				//$photo_url = str_replace("/var/www/html/triapp", "", $file["pathupload"]);
	
				$this->view->photo_url  = $photo_url;
			}else{
				$this->view->photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
			}
		}else{
			$this->view->photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
		}
			
	
		$studentGradeDB = new App_Model_Exam_DbTable_StudentGrade();
		$regSubjectDB = new App_Model_Exam_DbTable_StudentRegistrationSubject();
			
		$student_grade = $studentGradeDB->getStudentGradeInfo($IdStudentRegistration);
		$this->view->student_grade = $student_grade;
		if ($student_grade) $sem= $student_grade['sg_semesterId']; else $sem='1';
			//get cgpa info
			$gradeDb = new App_Model_Exam_DbTable_Academicstatus();
			//echo $student_grade['sg_semesterId']."xx".$student['IdProgram'];exit;
			$this->view->grade = $gradeDb->getListAcademicStatus($sem,$student['IdProgram'],$type=1,$basedon='Program');

		//get dean info
		$deanDB = new App_Model_General_DbTable_Deanmaster();
		$dean = $deanDB->getCollegeDean($student['IdCollege']);
		$this->view->dean = $dean;
			
		//get salutatuion
		$definationsDb = new App_Model_General_DbTable_Definationms();
		$this->view->FrontSalutation = $definationsDb->getData($dean['FrontSalutation']);
		$this->view->BackSalutation  = $definationsDb->getData($dean['BackSalutation']);
			
		//transcript profile
		$DbProfile = new App_Model_General_DbTable_TranscriptProfile();
		$DbProfileDetail = new App_Model_General_DbTable_TranscriptProfileDetail();
		//print_r($student);
		/* */
		$idProfile =$student['idTranscriptProfile'];
		if ($idProfile==0) {
			$profile = $DbProfile->getStdTranscriptProfile($student['IdProgram'],$student['IdProgramMajoring'],$student['IdLandscape']);
				
			if(!isset($profile[0]['IdProfile'])){
				$idProfile = '*';
			}
			else
			{
				$idProfile = $profile[0]['IdProfile'];
			}
		}
		//get category and course list
		//echo $idProfile;exit;
		$subject_category =$this->getTranscriptList($IdStudentRegistration,$idProfile,null);
	
		$db = new Finalassignment_Model_DbTable_FinalAssignment();
		$ta = $db->fnGetFinalAssigmentStd($IdStudentRegistration);
		//exit;
		if (isset($ta)) {
			$this->view->TaTitle=$ta['Title'];
			$this->view->TaTitleBahasa=$ta['TitleBahasa'];
		}else{
			$this->view->TaTitle=null;
			$this->view->TaTitleBahasa=null;
		}
		$this->view->subject_category = $subject_category;
			
	
	}
    
    public function getTranscriptList($idStudentRegistration,$idProfile=null,$pass=null) {
		//get student profile
		$regSubjectDB = new App_Model_Exam_DbTable_StudentRegistrationSubject();
		$DbProfileDetail = new App_Model_General_DbTable_TranscriptProfileDetail();
		$dbStudent = new App_Model_Exam_DbTable_StudentRegistration();
		$student = $dbStudent->SearchStudentRegistration(array('IdStudentRegistration'=>$idStudentRegistration));
		$student=$student[0];
        if ($idProfile==null) {
			$student=$student[0];
			$idLandscape = $student['IdLandscape'];
			$idProgram = $student['IdProgram'];
			$idMajor = $student['IdProgramMajoring'];
			//echo var_dump($student);
			//exit;
			//transcript profile
			$DbProfile = new App_Model_General_DbTable_TranscriptProfile();
			$DbProfileDetail = new App_Model_General_DbTable_TranscriptProfileDetail();
			$idProfile = $DbProfile->getStdTranscriptProfile($idProgram, $idMajor, $idLandscape);
			//echo var_dump($idProfile);exit;
			if ($idProfile==array()) $idProfile='*'; else $idProfile=$idProfile[0]['IdProfile'];
		}
		//get category and course list
		//echo var_dump($idProfile);exit;
		
		if ($idProfile=='*') {
		
			$dbLands = new GeneralSetup_Model_DbTable_Landscapesubject();
			$dbBlock= new GeneralSetup_Model_DbTable_LandscapeBlockSubject();
			$dbProgReq = new GeneralSetup_Model_DbTable_Programrequirement();
			$subject_category = $dbProgReq->getlandscapecoursetype($student['IdProgram'], $student['IdLandscape']);
		
			foreach($subject_category as $index=>$category){
				$subject_list = $dbLands->getlandscapesubjectsPerCategory($student['IdLandscape'],$category["SubjectType"]);
				//echo var_dump($category);
				//exit;
				if ($subject_list==array()) $subject_list = $dbBlock->getLandscapeCoursePerCategory($student['IdLandscape'],$category["SubjectType"]);
				unset($subjecthighest);
				foreach ($subject_list as $key=>$subject) {
					if ($pass==null)
						$subject=$regSubjectDB->getHighestMarkofAllSemesterC($idStudentRegistration, $subject['IdSubject']);
					else 
						$subject=$regSubjectDB->getHighestMarkofAllSemesterPassed($idStudentRegistration, $subject['IdSubject']);
					if (!is_bool($subject)) $subjecthighest[$key] = $subject;
				}
				if (isset($subjecthighest)) $subject_category[$index]["subjects"] = $subjecthighest;
				else unset($subject_category[$index]);
				//echo var_dump($subject_category);
				//exit;
			}
		
		}
		else
		{
		
			$subject_category = $DbProfileDetail->fnGetTranscriptProfileName($idProfile);
			foreach($subject_category as $index=>$category){
				$subjecthighest=array();
				$subject_list = $DbProfileDetail->fnGetTranscriptProfileSubject($idProfile,$category['idGroup']);
				unset($subjecthighest);
				//echo var_dump($subject_list);exit;
				foreach($subject_list as $key=>$subject) :
				if ($pass==null)
						$subject=$regSubjectDB->getHighestMarkofAllSemesterC($idStudentRegistration, $subject['idSubject']);
				else 
						$subject=$regSubjectDB->getHighestMarkofAllSemesterPassed($idStudentRegistration, $subject['idSubject']);
				if (!is_bool($subject)) $subjecthighest[$key] = $subject;
				endforeach;
				if (isset($subjecthighest)) $subject_category[$index]["subjects"] = $subjecthighest;
					else unset($subject_category[$index]);
				 
			}
		}
		//echo var_dump($subject_category);
		//exit;
		return $subject_category;
	}
	
    
    public function cetakTempTranscriptAction(){
		 
	//get applicant profile
    	$auth = Zend_Auth::getInstance();    	
    
    	//print_r($auth->getIdentity());
    	
        $appl_id = $auth->getIdentity()->appl_id; 
        $IdStudentRegistration = $auth->getIdentity()->registration_id;    

    	$this->view->appl_id = $appl_id;
    	$this->view->IdStudentRegistration = $IdStudentRegistration;
        
        //$IdStudentRegistration = $this->_getParam('id',null);  
		$this->view->id= $IdStudentRegistration;
      //  $this->_redirect('http://www.print.trisakti.ac.id/student-portal/cetak-temp-transcript/appl_id/'.$appl_id.'/registration_id/'.$IdStudentRegistration);
         //To get Student Academic Info        
        $studentRegDB = new App_Model_Exam_DbTable_StudentRegistration();
        $student = $studentRegDB->getStudentInfo($IdStudentRegistration);
        $this->view->student = $student;
        
		if($student["majoring"]=="common"|$student["majoring"]=="Bersama"){
			$student["majoring"]="-";
			$student["majoring_english"]="-";
		}
	
		global $majoring;
		$majoring=$student["majoring"];
		global $printmajoring;
		$printmajoring=$student['print_majoring'];
		//get majoring address
		//brach
		$branchDb=new App_Model_General_DbTable_Branchofficevenue();
		$branch=$branchDb->fnviewBranchofficevenueDtls($student['IdBranch']);
		//get info majoring
		$majorDb = new App_Model_General_DbTable_Program();
		$major = $majorDb->fnviewMajoring($student['IdProgramMajoring']);
		if ($major['Address1']=='') {
			$addphone=$branch["Phone"];
			$addemail=$branch["Email"];
			$add=$branch["Addr1"].' '.$branch["Addr2"].' '.$branch["StateName"].' '.ucwords(strtolower($branch["CountryName"]));
		} else {
			$addphone=$major["Phone"];
			$addemail=$major["Email"];
			$add=$major["Addr1"].' '.$major["Addr2"].' '.$major["StateName"].' '.ucwords(strtolower($major["CountryName"]));
		}
		//get photo student
		$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
		$file = $uploadFileDb->getFile($student["transaction_id"],51);
	
		if(isset($file["pathupload"])){
			if (file_exists($file["pathupload"])) {
				$fnImage = new icampus_Function_General_Image();
				$photo_url = "http://".APP_HOSTNAME.$fnImage->getImagePath($file['pathupload'],100,123);
				//$photo_url = str_replace("/var/www/html/triapp","http://".ONNAPP_HOSTNAME."/", $file["pathupload"]);
			}else{
				$photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
			}
		}else{
			$photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
		}
		 
	
		//get info college
		$collegedB = new App_Model_General_DbTable_Collegemaster();
        $college = $collegedB->getFullInfoCollege($student["IdCollege"]);
	
	
		$studentGradeDB = new App_Model_Exam_DbTable_StudentGrade();
		$regSubjectDB = new App_Model_Exam_DbTable_StudentRegistrationSubject();
		 
		$student_grade = $studentGradeDB->getStudentGradeInfo($IdStudentRegistration);
		$this->view->student_grade = $student_grade;
        
		$student_grade = $studentGradeDB->getStudentGradeInfo($IdStudentRegistration);
		
		//get cgpa info
		global $grade;
		
		$gradeDb = new App_Model_Exam_DbTable_Academicstatus();
		//echo $student_grade['sg_semesterId']."xx".$student['IdProgram'];exit;
		$this->view->grade = $gradeDb->getListAcademicStatus($student_grade['sg_semesterId'],$student['IdProgram'],$type=1,$basedon='Program');
	
		//get dean info
		$deanDB = new App_Model_General_DbTable_Deanmaster();
		$dean = $deanDB->getCollegeDean($student['IdCollege']);
		 
		 
		//get salutatuion
		$definationsDb = new App_Model_General_DbTable_Definationms();
		$FrontSalutation = $definationsDb->getData($dean['FrontSalutation']);
		$BackSalutation  = $definationsDb->getData($dean['BackSalutation']);
		 
		//get category and course list
		global $subject_category;
		//transcript profile
		$DbProfile = new App_Model_General_DbTable_TranscriptProfile();
		$DbProfileDetail = new App_Model_General_DbTable_TranscriptProfileDetail();
		$idProfile =$student['idTranscriptProfile'];
		if ($idProfile==0) {
			$profile = $DbProfile->getStdTranscriptProfile($student['IdProgram'],$student['IdProgramMajoring'],$student['IdLandscape']);
			
			if(!isset($profile[0]['IdProfile'])){
				$idProfile = '*';
			}
			else
			{
				$idProfile = $profile[0]['IdProfile'];
			}
		}
		$subject_category =$this->getTranscriptList($IdStudentRegistration,$idProfile,'1');
	
	
		$fieldValues = array(
				'$[JURUSAN]'=>$student["Dept_Bahasa"],
				'$[STRATA]'=>$student["strata"],
				'$[PROGRAMSTUDI]'=>$student["ArabicName"],
				'$[DEPARTMENT]'=>$student["Departement"],
				'$[STUDYPROGRAM]'=>$student["ProgramName"],
				'$[FAKULTAS]'=>'FAKULTAS '.$college["ArabicName"],
				'$[FACULTY]'=> $college["CollegeName"],
				'$[ADDRESS]'=>$college["Add1"].' '.$college["Add2"].' '.$college["CityName"].' '.$college["StateName"],
				'$[PHONE]'=>$college["Phone1"],
				'$[EMAIL]'=>$college["Email"],
				'$[KONSENTRASI]'=>$student["majoring"],
				'$[MAJORING]'=>$student["majoring_english"],
				'$[PROGRAMPENDIDIKAN]'=>@$student["program_pendidikan"],
				'$[SCHEME]'=>$student["strata"],
				'$[PROGRAM]'=>$student["program_eng"],
				'$[STUDENTNAME]'=>$student["appl_fname"].' '.$student["appl_mname"].' '.$student["appl_lname"],
				'$[BIRTHDATE]'=>$student["appl_birth_place"].', '.strftime("%e %B, %Y", strtotime($student["appl_dob"])),
				'$[NIM]'=>$student["registrationId"],
				'$[PHOTO]'=>$photo_url,
				'$[DEAN]'=>$FrontSalutation['DefinitionDesc'].' '.$dean['Fullname'].' '.$BackSalutation['DefinitionDesc']	,
				'$[TOTALCREDITHOUR]'=>$student_grade['sg_cum_credithour'],
				'$[TOTALPOINT]'=>number_format($student_grade['sg_cum_totalpoint'], 2, '.', ''),
				'$[GPA]'=>number_format($student_grade['sg_cgpa'], 2, '.', ''),
				'$[CGPA_STATUS]'=>$student_grade['sg_cgpa_status'],
				'$[B_ADDRESS]'=>$add,
				'$[B_PHONE]'=>$addphone,
				'$[B_EMAIL]'=>$addemail
		);
	
	//echo var_dump($student);
	//exit;
		require_once 'dompdf_config.inc.php';
	
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
	
		//template path
		$html_template_path = DOCUMENT_PATH."/template/transcript_temp.html";
	
		$html = file_get_contents($html_template_path);
	
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);
		}
		//echo $html;exit;
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		@$dompdf->render();
	
		//echo $html;exit;
	
		//echo $html;exit;
		$output_directory_path = DOCUMENT_PATH."/student/transcript";
		
		//create directory to locate file
		if (!is_dir($output_directory_path)) {
			mkdir($output_directory_path, 0775,true);
		}
		//output filename 
		$output_filename = "transcript_temp_".$student['registrationId'].".pdf";
				
		//$dompdf = $dompdf->output();
		@$dompdf->stream($output_filename);						
							
		//to rename output file						
	    $output_file_path = $output_directory_path.'/'.$output_filename;
		
		file_put_contents($output_file_path, $dompdf);
		
		$this->view->file_path = $output_file_path;
	
		exit;
	
	}
	public function cetakAllTranscriptAction(){
			
		//get applicant profile
		$auth = Zend_Auth::getInstance();
	
		//print_r($auth->getIdentity());
		 
		$appl_id = $auth->getIdentity()->appl_id;
		$IdStudentRegistration = $auth->getIdentity()->registration_id;
	
		$this->view->appl_id = $appl_id;
		$this->view->IdStudentRegistration = $IdStudentRegistration;
	
		//$IdStudentRegistration = $this->_getParam('id',null);
		$this->view->id= $IdStudentRegistration;
		//$this->_redirect('http://www.print.trisakti.ac.id/student-portal/cetak-all-transcript/appl_id/'.$appl_id.'/registration_id/'.$IdStudentRegistration);
		
		//To get Student Academic Info
		$studentRegDB = new App_Model_Exam_DbTable_StudentRegistration();
		$student = $studentRegDB->getStudentInfo($IdStudentRegistration);
		$this->view->student = $student;
	
		if($student["majoring"]=="common"|$student["majoring"]=="Bersama"){
			$student["majoring"]="-";
			$student["majoring_english"]="-";
		}
	
		global $majoring;
		$majoring=$student["majoring"];
		global $printmajoring;
		$printmajoring=$student['print_majoring'];
		//get photo student
		$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
		$file = $uploadFileDb->getFile($student["transaction_id"],51);
	
		if(isset($file["pathupload"])){
			if (file_exists($file["pathupload"])) {
				$fnImage = new icampus_Function_General_Image();
				$photo_url = "http://".APP_HOSTNAME.$fnImage->getImagePath($file['pathupload'],100,123);
				//$photo_url = str_replace("/var/www/html/triapp","http://".ONNAPP_HOSTNAME."/", $file["pathupload"]);
			}else{
				$photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
			}
		}else{
			$photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
		}
			
	
		//get info college
		$collegedB = new App_Model_General_DbTable_Collegemaster();
		$college = $collegedB->getFullInfoCollege($student["IdCollege"]);
	
	
		$studentGradeDB = new App_Model_Exam_DbTable_StudentGrade();
		$regSubjectDB = new App_Model_Exam_DbTable_StudentRegistrationSubject();
			
		$student_grade = $studentGradeDB->getStudentGradeInfo($IdStudentRegistration);
		$this->view->student_grade = $student_grade;
	
		$student_grade = $studentGradeDB->getStudentGradeInfo($IdStudentRegistration);
	
		//get cgpa info
		global $grade;
	
		$gradeDb = new App_Model_Exam_DbTable_Academicstatus();
		//echo $student_grade['sg_semesterId']."xx".$student['IdProgram'];exit;
		$this->view->grade = $gradeDb->getListAcademicStatus($student_grade['sg_semesterId'],$student['IdProgram'],$type=1,$basedon='Program');
	
		//get dean info
		$deanDB = new App_Model_General_DbTable_Deanmaster();
		$dean = $deanDB->getCollegeDean($student['IdCollege']);
			
			
		//get salutatuion
		$definationsDb = new App_Model_General_DbTable_Definationms();
		$FrontSalutation = $definationsDb->getData($dean['FrontSalutation']);
		$BackSalutation  = $definationsDb->getData($dean['BackSalutation']);
			
		//get category and course list
		global $subject_category;
		//transcript profile
		$DbProfile = new App_Model_General_DbTable_TranscriptProfile();
		$DbProfileDetail = new App_Model_General_DbTable_TranscriptProfileDetail();
		$idProfile =$student['idTranscriptProfile'];
		if ($idProfile==0) {
			$profile = $DbProfile->getStdTranscriptProfile($student['IdProgram'],$student['IdProgramMajoring'],$student['IdLandscape']);
			
			if(!isset($profile[0]['IdProfile'])){
				$idProfile = '*';
			}
			else
			{
				$idProfile = $profile[0]['IdProfile'];
			}
		}
		$subject_category =$this->getTranscriptList($IdStudentRegistration,$idProfile,null);
	
		$fieldValues = array(
				'$[JURUSAN]'=>$student["Dept_Bahasa"],
				'$[STRATA]'=>$student["strata"],
				'$[PROGRAMSTUDI]'=>$student["ArabicName"],
				'$[DEPARTMENT]'=>$student["Departement"],
				'$[STUDYPROGRAM]'=>$student["ProgramName"],
				'$[FAKULTAS]'=>'FAKULTAS '.$college["ArabicName"],
				'$[FACULTY]'=> $college["CollegeName"],
				'$[ADDRESS]'=>$college["Add1"].' '.$college["Add2"].' '.$college["CityName"].' '.$college["StateName"],
				'$[PHONE]'=>$college["Phone1"],
				'$[EMAIL]'=>$college["Email"],
				'$[KONSENTRASI]'=>$student["majoring"],
				'$[MAJORING]'=>$student["majoring_english"],
				'$[PROGRAMPENDIDIKAN]'=>$student["program_pendidikan"],
				'$[SCHEME]'=>$student["strata"],
				'$[PROGRAM]'=>$student["program_eng"],
				'$[STUDENTNAME]'=>$student["appl_fname"].' '.$student["appl_mname"].' '.$student["appl_lname"],
				'$[BIRTHDATE]'=>$student["appl_birth_place"].', '.strftime("%e %B, %Y", strtotime($student["appl_dob"])),
				'$[NIM]'=>$student["registrationId"],
				'$[PHOTO]'=>$photo_url,
				'$[DEAN]'=>$FrontSalutation['DefinitionDesc'].' '.$dean['Fullname'].' '.$BackSalutation['DefinitionDesc']	,
				'$[TOTALCREDITHOUR]'=>$student_grade['sg_all_cum_credithour'],
				'$[TOTALPOINT]'=>number_format($student_grade['sg_all_cum_totalpoint'], 2, '.', ''),
				'$[GPA]'=>number_format($student_grade['sg_all_cgpa'], 2, '.', ''),
				'$[CGPA_STATUS]'=>$student_grade['sg_cgpa_status']
		);
	
		//echo var_dump($student);
		//exit;
		require_once 'dompdf_config.inc.php';
	
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
	
		//template path
		$html_template_path = DOCUMENT_PATH."/template/transcript_temp.html";
	
		$html = file_get_contents($html_template_path);
	
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);
		}
		//echo $html;exit;
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		@$dompdf->render();
	
		//echo $html;exit;
	
		//echo $html;exit;
		$output_directory_path = DOCUMENT_PATH."/student/transcript";
	
		//create directory to locate file
		if (!is_dir($output_directory_path)) {
			mkdir($output_directory_path, 0775,true);
		}
		//output filename
		$output_filename = "transcript_temp_".$student['registrationId'].".pdf";
	
		//$dompdf = $dompdf->output();
		@$dompdf->stream($output_filename);
			
		//to rename output file
		$output_file_path = $output_directory_path.'/'.$output_filename;
	
		file_put_contents($output_file_path, $dompdf);
	
		$this->view->file_path = $output_file_path;
	
		exit;
	
	}
    public function cetakTranscriptAction(){
    	
		
		//get applicant profile
    	$auth = Zend_Auth::getInstance();    	
    
    	//print_r($auth->getIdentity());
    	
        $appl_id = $auth->getIdentity()->appl_id; 
        $IdStudentRegistration = $auth->getIdentity()->registration_id;   
		
		 //To get Student Academic Info        
        $studentRegDB = new App_Model_Exam_DbTable_StudentRegistration();
        $student = $studentRegDB->getStudentInfo($IdStudentRegistration);
        
        
       // $this->_redirect('http://www.print.trisakti.ac.id/student-portal/cetak-transcript/appl_id/'.$appl_id.'/registration_id/'.$IdStudentRegistration);
        
         //get photo student
    	$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
    	$file = $uploadFileDb->getFile($student["transaction_id"],51);
    	    	
		if(isset($file["pathupload"])){
    		if (file_exists($file["pathupload"])) {
    			$fnImage = new icampus_Function_General_Image();
    			$photo_url = "http://".APP_HOSTNAME.$fnImage->getImagePath($file['pathupload'],100,123);	
    			//$photo_url = str_replace("/var/www/html/triapp","http://".ONNAPP_HOSTNAME."/", $file["pathupload"]);
    		}else{
    			$photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
    		}
    	}else{
    		$photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
    	}
    	

    	//get info college
    	$collegedB = new App_Model_General_DbTable_Collegemaster();
        $college = $collegedB->getFullInfoCollege($student["IdCollege"]);
        
				        
    	$studentGradeDB = new App_Model_Exam_DbTable_StudentGrade();
    	$regSubjectDB = new App_Model_Exam_DbTable_StudentRegistrationSubject();
    	
    	$student_grade = $studentGradeDB->getStudentGradeInfo($IdStudentRegistration);
    	
    	//get cgpa info
    	global $grade;
    	$gradeDb = new App_Model_Exam_DbTable_Academicstatus();
    	$grade = $gradeDb->getListAcademicStatus($student_grade['sg_semesterId'],$student['IdProgram'],$type=1,$basedon='Program');
    	    	
    	//get dean info
    	$deanDB = new App_Model_General_DbTable_Deanmaster();
    	$dean = $deanDB->getCollegeDean($student['IdCollege']);
    	
    	
    	//get salutatuion
    	$definationsDb = new App_Model_General_DbTable_Definationms();
    	$FrontSalutation = $definationsDb->getData($dean['FrontSalutation']);
    	$BackSalutation  = $definationsDb->getData($dean['BackSalutation']);
    	
    	//get category and course list
    	global $subject_category;
    	$subject_category = $regSubjectDB->getCategoryCourseRegistered($IdStudentRegistration);    	
    	foreach($subject_category as $index=>$category){
    		$subject_list = $regSubjectDB->getCourseRegistered($IdStudentRegistration,$category["idCategory"]);
    		$subject_category[$index]["subjects"] = $subject_list;
    	}    	
    	
		
    	$fieldValues = array(
			    	  '$[JURUSAN]'=>$student["NamaKolej"],
    					'$[STRATA]'=>$student["strata"],
    	 			  '$[PROGRAMSTUDI]'=>$student["ArabicName"],
    				  '$[DEPARTMENT]'=>$student["CollegeName"],
    	 			  '$[STUDYPROGRAM]'=>$student["ProgramName"],
			    	  '$[FAKULTAS]'=>'FAKULTAS '.$student["ArabicName"],
    	 			  '$[FACULTY]'=> $college["CollegeName"],			    	 
			    	  '$[ADDRESS]'=>ucwords(strtolower($college["Add1"])).' '.ucwords(strtolower($college["Add2"])).' '.ucwords(strtolower($college["CityName"])).' '.ucwords(strtolower($college["StateName"])),
					  '$[PHONE]'=>$college["Phone1"],
					  '$[EMAIL]'=>$college["Email"],
    				  '$[KONSENTRASI]'=>$student["majoring"],
					  '$[MAJORING]'=>$student["majoring_english"],
    				  '$[PROGRAMPENDIDIKAN]'=>$college["Phone1"],
    				  '$[SCHEME]'=>$student["SchemeCode"],
					  '$[PROGRAM]'=>$student["SchemeName"],
    				  '$[STUDENTNAME]'=>$student["appl_fname"].' '.$student["appl_mname"].' '.$student["appl_lname"],
					  '$[BIRTHDATE]'=>$student["appl_birth_place"].', '.strftime("%e %B, %Y", strtotime($student["appl_dob"])),
    				  '$[NIM]'=>$student["registrationId"],
    				  '$[PHOTO]'=>$photo_url,
    				  '$[DEAN]'=>$FrontSalutation['DefinitionDesc'].' '.$dean['Fullname'].' '.$BackSalutation['DefinitionDesc']	,
    	 			  '$[TOTALCREDITHOUR]'=>$student['TotalCreditHours'],	
    	 			  '$[TOTALPOINT]'=>number_format($student_grade['sg_univ_sem_totalpoint'], 2, '.', ''),
			    	  '$[GPA]'=>number_format($student_grade['sg_cgpa'], 2, '.', ''),
			    	  '$[CGPA_STATUS]'=>$student_grade['sg_cgpa_status']  
		    	   );
				    	   
		
	    require_once 'dompdf_config.inc.php';
	
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		//template path	 
		$html_template_path = DOCUMENT_PATH."/template/transcript.html";
		
		$html = file_get_contents($html_template_path);			
    		
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);	
		}
			
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		@$dompdf->render();
		//echo $html;exit;
		$output_directory_path = DOCUMENT_PATH."/student/transcript";
		
		//output filename
		$output_filename = "transcript_".$student['registrationId'].".pdf";
		
		//$dompdf = $dompdf->output();
		$output_file_path = $output_directory_path.'/'.$output_filename;
		
		@$dompdf->stream($output_filename);						
		
		file_put_contents($output_file_path, $dompdf);
		
		$this->view->file_path = $output_file_path;
		
		
		exit;
		
	}
	
	function getCourseDetailAction(){
	
	
		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->_helper->layout->disableLayout();
		}
	
		
		//get applicant profile
		$auth = Zend_Auth::getInstance();
		 
		$IdStudentRegistration = $auth->getIdentity()->registration_id;
		$this->view->IdStudentRegistration = $IdStudentRegistration;
		 
		$idSemester = $this->_getParam('semester',0);
		$idProgram = $this->_getParam('program', 0);
		$idSubject = $this->_getParam('subject', 0);
		 
		$this->view->idSemester = $idSemester;
		$this->view->idProgram  = $idProgram;
		$this->view->idSubject  = $idSubject;
		
		$studId = $IdStudentRegistration;
		$subId =  $idSubject;
		$semId = $idSemester;
	
		//get resit schedule for this subject
		$dbResitSchedule=new App_Model_Exam_DbTable_ExamGroupStudent();
		
		//student info
		$studentRegistrationDB = new App_Model_Registration_DbTable_Studentregistration();
		$studentdetails = $studentRegistrationDB->fetchStudentHistoryDetails($studId);
		$applid=$studentdetails['appl_id'];
		//program
		$dbProgram = new App_Model_General_DbTable_Program();
		$program=$dbProgram->getData($studentdetails['IdProgram']);
			
		$attendancemode=$program['Attendance_cal_mode'];
		
		//class group
		$courseGroupStudentDb = new App_Model_General_DbTable_CourseGroupStudent();
		$classGroup = $courseGroupStudentDb->checkStudentCourseGroup($studId,$semId,$subId);
	
		//class attendance
		$courseGroupStudentAttendanceDb = new Examination_Model_DbTable_CourseGroupStudentAttendanceDetail();
		$courseGroupStudentAttDb = new Examination_Model_DbTable_CourseGroupStudentAttendance();
			
		$classGroup['class_session'] = $courseGroupStudentAttendanceDb->getAttendanceSessionCount($classGroup['IdCourseTaggingGroup'],$studId);
		$classGroup['class_att_attended'] = $courseGroupStudentAttendanceDb->getAttendanceStatusCount($classGroup['IdCourseTaggingGroup'],$studId,395);
		$classGroup['class_att_permission'] = $courseGroupStudentAttendanceDb->getAttendanceStatusCount($classGroup['IdCourseTaggingGroup'],$studId,396);
		$classGroup['class_att_sick'] = $courseGroupStudentAttendanceDb->getAttendanceStatusCount($classGroup['IdCourseTaggingGroup'],$studId,397);
		$classGroup['class_att_absent'] = $courseGroupStudentAttendanceDb->getAttendanceStatusCount($classGroup['IdCourseTaggingGroup'],$studId,398);
		$classGroup['class_att_dispen'] = $courseGroupStudentAttendanceDb->getAttendanceStatusCount($classGroup['IdCourseTaggingGroup'],$studId,1701);
		if ($attendancemode=="124") {
					if ($classGroup['class_session']>16) {
						if ($classGroup['class_att_sick']+$classGroup['class_att_permission']>1) $nOfattend=$classGroup['class_att_attended']+2;
						else $nOfattend=$classGroup['class_att_attended']+$classGroup['class_att_sick']+$classGroup['class_att_permission'];
					} else {
						if ($classGroup['class_att_sick']+$classGroup['class_att_permission']>=1) $nOfattend=$classGroup['class_att_attended']+1;
						else $nOfattend=$classGroup['class_att_attended'];
					}
				
		} else
		if ($attendancemode=="123") $nOfattend =$classGroup['class_att_permission']+$classGroup['class_att_sick']+$classGroup['class_att_attended'];
		else if ($attendancemode=="12") $nOfattend=$classGroup['class_att_sick']+$classGroup['class_att_attended'];
		else if ($attendancemode=="1") $nOfattend =$classGroup['class_att_attended'];
		else if ($attendancemode=="200") $nOfattend =$classGroup['class_att_permission']+$classGroup['class_att_sick']+$classGroup['class_att_attended']+$classGroup['class_att_dispen'];
		
		if ($classGroup['class_session'] >0) $classGroup['class_attendance_percentage'] = ($nOfattend/$classGroup['class_session'] )*100; else $classGroup['class_attendance_percentage'] ='N/A';
	
		$this->view->class_group = $classGroup;
		$lectatt=$courseGroupStudentAttDb->getAttendanceByGroup($classGroup['IdCourseTaggingGroup']);
		if ($lectatt) {
			foreach ($lectatt as $key=>$value) {
				$idatt=$value['id'];
				$attdet=$courseGroupStudentAttendanceDb->getAttendanceStatus($idatt, $studId);
				$status="";
				if ($attdet) {
					if ($attdet['status']=="395") $status="Attend";
					else if ($attdet['status']=="396") $status="Permission";
					else if ($attdet['status']=="397") $status="Sick";
					else if ($attdet['status']=="398") $status="Absent";
					else if ($attdet['status']=="1701") $status="Dispensasi";
				} else $status="n/a";
				$lectatt[$key]['Status']=$status;
			}
		} else $lectatt=array();
		$this->view->attdetail=$lectatt;
		
		/*
		 * coursework mark
		*
		*/
	
		//get subject component
		$markDistributionDB = new Examination_Model_DbTable_Marksdistributionmaster();
		$component = $markDistributionDB->getListMainComponentByStd($studId,$semId,$studentdetails['IdProgram'],$subId);
		$studentMarkEntryDb=new Examination_Model_DbTable_StudentMarkEntry();
		$DbResitConf = new App_Model_Exam_DbTable_ResitConfig();
		if ($component) {
			//get student reg subject info details(subject registration info)
			$studentRegistrationSubjectDb = new Examination_Model_DbTable_StudentRegistrationSubject();
			$subjectRegInfo = $studentRegistrationSubjectDb->getSemesterSubjectStatus($semId, $studId, $subId);
		//echo var_dump($subjectRegInfo);exit;
			//cek rule
			$rule=array();
			//echo var_dump($component);
			foreach ($component as $value) {
				if ($value['Rule']=="1") {
					$rule[$value['IdMarksDistributionMaster']]=$studentMarkEntryDb->getMark($studId, $value['IdMarksDistributionMaster'], $subjectRegInfo['IdStudentRegSubjects'], $semId);
					//$rule['MarkStatus']='411';//$value['MarksEntryStatus'];
				}
			}
			//
			//get rule
			//get Resit Configuration 
			$resitConf=$DbResitConf->getConfig("1", $idProgram, 0, 0, $idSubject);
			if ($resitConf) $minMark=$resitConf['MinimumMark'];else $minMark=100;
			 
			if ($rule!=array() ) {
					$total=0;$i=0;$temp=100;$markverified="";
					$dbMarkRule=new Examination_Model_DbTable_MarksRule();
					$markrule=$dbMarkRule->getDataComponent($semId, $studentdetails['IdProgram'], null, $subId);
					if ($markrule[0]['Rules']=='AVG') {
						
						foreach ($rule as $key=>$value) {
							if ($value['TotalMarkObtained']<$temp ) {
								$temp=$value['TotalMarkObtained'];
								//if ($value['MarksEntryStatus']!='411') $markverified="1";else $markverified="";
								$idmark=$key;
							}
							$total=$total+$value['TotalMarkObtained'];
							$i++;
						}
						$total=$total/$i;
					}
					//if ($total<$markrule['Limit']) $resit=$idmark; else $resit='';
					if ($total<$minMark && $temp!=100) $resit=$idmark; else $resit='';
					//echo $total;echo $resit;exit;
				}  else {
					//get for lowestresit component 
					foreach ($component as $value) {
						if ($value['IdComponentType']=="42" || $value['IdComponentType']=="44" ) $rule[$value['IdMarksDistributionMaster']]=$studentMarkEntryDb->getMark($studId, $value['IdMarksDistributionMaster'], $subjectRegInfo['IdStudentRegSubjects'], $semId);
					}
					$total=0;$i=0;
					$temp=100;$markverified="";
					foreach ($rule as $key=>$value) {
						if ($value['TotalMarkObtained']<$temp ) {
							$temp=$value['TotalMarkObtained'];
							//if ($value['MarksEntryStatus']!='411') $markverified="1";else $markverified="";
							$idmark=$key;
						}
						$total=$total+$value['TotalMarkObtained'];
						$i++;
					}
					if ($i!=0) $total=$total/$i; else $total=0;
					if ($total<$minMark && $temp!=100) $resit=$idmark; else $resit='';
			}
			
				//
		   //get mark
			$studentMarkEntryDb = new Examination_Model_DbTable_StudentMarkEntry();
			$publishMarkDb = new App_Model_Exam_DbTable_PublishMark();
			$dbResit=new App_Model_Exam_DbTable_Resit();
			$cmp=array();
			foreach ($component as $index => $com){
					
					$publish = $publishMarkDb->getData($idProgram,$semId,$subId,$classGroup["IdCourseTaggingGroup"],$com["IdMarksDistributionMaster"],1);
					$component[$index]['student_mark'] = $studentMarkEntryDb->getMark($studId, $com['IdMarksDistributionMaster'], $subjectRegInfo['IdStudentRegSubjects'], $semId);
					$component[$index]["publish_date"]=$publish["pm_date"];
					$cmp[$index]=$com['Level'];
					$resitschedule=$dbResitSchedule->getExamGroupScheduleBySubject($idSubject, $idSemester, $com['IdComponentType'],'1');
					$component[$index]['resit']="";
					$component[$index]['jadwal']="";
					if ($com['allow_resit']=="1" && $resit!='' && $com['IdMarksDistributionMaster']==$resit) {
						//rule applied for allow resit component 
						
							if ($resit!='' && $com['IdMarksDistributionMaster']==$resit) {
								$component[$index]['resit']="1";
								//link for cancel
								$resit=$dbResit->isStudentResit($IdStudentRegistration, $idSemester, $idSubject, $com['IdMarksDistributionMaster']);
								if ($resit){
									if ($dbResitSchedule->canCancel($idSubject, $idSemester, $com['IdComponentType'],'1'))
											$component[$index]['resit']="2";
								}
								if ($resitschedule) $component[$index]['jadwal']="1"; else $component[$index]['jadwal']="";
								
							} else {
								$component[$index]['resit']="";
								$component[$index]['jadwal']="";
								 
							}
					} else if (isset($component[$index]['student_mark']) ) {
						$marks= $component[$index]['student_mark'];
						if ($marks['TotalMarkObtained']<$com['min_mark']) { //&& $com['allow_resit']=="1"
								if ($com['Level'] > 0) {
									//find all child and chek for resit;
									$currentlevel=$com['Level'];
									$cmpreverse=array();
									//echo $marks['TotalMarkObtained'].'<'.$com['min_mark'];
									foreach ($cmp as $idx=>$itm) {
										$idxs[]=$idx;
									}
									$idxs=array_reverse($idxs);
									foreach ($idxs as $itm) {
										$cmpreverse[$itm]=$cmp[$itm];
									}
									$next="0";
									//echo var_dump($cmpreverse);exit;
									foreach ($cmpreverse as $idxlevel=>$level) {
										//echo $level.'='.$currentlevel.'<br>';
										if ($level==$currentlevel && $next!="0") break;
										else {
											//echo $level.'='.$idxlevel.'<br>';
											//echo $component[$idxlevel]['student_mark']['TotalMarkObtained'].'<'.$component[$idxlevel]['min_mark'];
											//echo '<br>';
											$next="1";
											if ($component[$idxlevel]['allow_resit']=="1" && ($component[$idxlevel]['student_mark']['TotalMarkObtained']<$component[$idxlevel]['min_mark'] || $component[$idxlevel]['min_mark']==0)) {
												//echo $level.'==='.$idxlevel.'<br>';
												$component[$idxlevel]['resit']="1";
												//link for cancel
												$resit=$dbResit->isStudentResit($IdStudentRegistration, $idSemester, $idSubject, $component[$idxlevel]['IdMarksDistributionMaster']);
												if ($resit){
													if ($dbResitSchedule->canCancel($idSubject, $idSemester, $component[$idxlevel]['IdComponentType'],'1'))
														$component[$idxlevel]['resit']="2";
												}
												$resitschedule=$dbResitSchedule->getExamGroupScheduleBySubject($idSubject, $idSemester, $component[$idxlevel]['IdComponentType'],'1');
												
												if ($resitschedule) $component[$idxlevel]['jadwal']="1"; else $component[$index]['jadwal']="";
								
											} else {
												$component[$idxlevel]['resit']="";
												$component[$idxlevel]['jadwal']="";
											}
										}
									}
									//exit;
									//echo var_dump($component);exit;
								} else 	{ 
									if ($com['allow_resit']=="1") {
										$component[$index]['resit']="1";
									
										$resitschedule=$dbResitSchedule->getExamGroupScheduleBySubject($idSubject, $idSemester, $com['IdComponentType'],'1');
									
										if ($resitschedule) $component[$index]['jadwal']="1"; else $component[$index]['jadwal']="";
									}
								}
							} 
						}  
						 
				 
					
			}
			
		} else $component=array();
		$this->view->coursework = $component;
		//cek for deposit marks
		$dbMarkDeposit=new App_Model_Exam_DbTable_MarksDepositHeader();
		$markdeposit=$dbMarkDeposit->getData($idProgram, $idSemester, $idSubject);
		if ($markdeposit) {
			//has mark deposit and will show up after parent
			$idsubjectdeposit=$markdeposit['IdSubjectDeposit'];
			$idHeader=$markdeposit['id'];
			$component = $markDistributionDB->getListMainComponentByStd($studId,$semId,$studentdetails['IdProgram'],$idsubjectdeposit,null,$idHeader);
			//echo var_dump($component);exit;
			if ($component) {
				//get Resit Configuration
				$resitConf=$DbResitConf->getConfig("1", $idProgram, 0, 0, $idsubjectdeposit);
				if ($resitConf) $minMark=$resitConf['MinimumMark'];else $minMark=100;
					
				//get student reg subject info details(subject registration info)
				$studentRegistrationSubjectDb = new Examination_Model_DbTable_StudentRegistrationSubject();
				$subjectRegInfo = $studentRegistrationSubjectDb->getSemesterSubjectStatus($semId, $studId, $subId);
				 
				//get mark
				$studentMarkEntryDb = new Examination_Model_DbTable_StudentMarkEntry();
				$publishMarkDb = new App_Model_Exam_DbTable_PublishMark();
				$cmp=array();
				foreach ($component as $index => $com){
					$cmp[$index]=$com['Level'];
					$publish = $publishMarkDb->getData($idProgram,$semId,$idsubjectdeposit,$classGroup["IdCourseTaggingGroup"],$com["IdMarksDistributionMaster"],1);
					$component[$index]['student_mark'] = $studentMarkEntryDb->getMark($studId, $com['IdMarksDistributionMaster'], $subjectRegInfo['IdStudentRegSubjects'], $semId,$idHeader);
					$component[$index]["publish_date"]=$publish["pm_date"];
					$component[$index]['resit']="";
					$component[$index]['jadwal']="";
					 
					if (isset($component[$index]['student_mark']) ) {
						$marks= $component[$index]['student_mark'];
						//echo 'urut='.$index.'<br>';
						if ($marks['TotalMarkObtained']<$com['min_mark']) { //&& $com['allow_resit']=="1"
								if ($com['Level'] > 0) {
									//find all child and chek for resit;
									$currentlevel=$com['Level'];
									$cmpreverse=array();
									//echo $marks['TotalMarkObtained'].'<'.$com['min_mark'];
									foreach ($cmp as $idx=>$itm) {
										$idxs[]=$idx;
									}
									$idxs=array_reverse($idxs);
									foreach ($idxs as $itm) {
										$cmpreverse[$itm]=$cmp[$itm];
									}
									$next="0";
									//echo var_dump($cmpreverse);echo '<br>';
									foreach ($cmpreverse as $idxlevel=>$level) {
										//echo $level.'='.$currentlevel.'<br>';
										if ($level==$currentlevel && $next!="0") break;
										else {
											
											//echo $level.'='.$idxlevel.'<br>';
											//echo $component[$idxlevel]['student_mark']['TotalMarkObtained'].'<'.$component[$idxlevel]['min_mark'];
											//echo '<br>';
											$next="1";
											if ($component[$idxlevel]['allow_resit']=="1" && ($component[$idxlevel]['student_mark']['TotalMarkObtained']<$component[$idxlevel]['min_mark'] || $component[$idxlevel]['min_mark']==0)) {
												//echo $level.'==='.$idxlevel.'<br>';
												$component[$idxlevel]['resit']="1";
												//link for cancel
												$resit=$dbResit->isStudentResit($IdStudentRegistration, $idSemester, $idSubject, $component[$idxlevel]['IdMarksDistributionMaster']);
												if ($resit){
													if ($dbResitSchedule->canCancel($idSubject, $idSemester, $component[$idxlevel]['IdComponentType'],'1'))
														$component[$idxlevel]['resit']="2";
												}
												$resitschedule=$dbResitSchedule->getExamGroupScheduleBySubject($idSubject, $idSemester, $component[$idxlevel]['IdComponentType'],'1');
												
												if ($resitschedule) $component[$idxlevel]['jadwal']="1"; else $component[$index]['jadwal']="";
								
											} else {
												$component[$idxlevel]['resit']="";
												$component[$idxlevel]['jadwal']="";
											}
										}
									}
									//exit;
									//echo var_dump($component);exit;
								} else 	{ 
									if ($com['allow_resit']=="1") {
										$component[$index]['resit']="1";
									
										$resitschedule=$dbResitSchedule->getExamGroupScheduleBySubject($idSubject, $idSemester, $com['IdComponentType'],'1');
									
										if ($resitschedule) $component[$index]['jadwal']="1"; else $component[$index]['jadwal']="";
									}
								}
							} 
						}  
					
						
				}
					
			} else $component=array();
			
		} else $component=array();
		$this->view->courseworkdeposit = $component;
		//cek pembayaran
		$dbInvoice=new Studentfinance_Model_DbTable_PaymentMain();
		$row=$dbInvoice->isOutStanding($applid, $idSemester);
		if ($row && $idProgram=="4" ) $this->view->outstanding="1"; else $this->view->outstanding="0";
		
	
	}
	
	public function ijazahIndexAction(){
	
	
		//get applicant profile
    	$auth = Zend_Auth::getInstance();    	
    	$db = new App_Model_Record_DbTable_Graduation();
    	//print_r($auth->getIdentity());
    	$this->view->title="Document Approval";
    	$appl_id = $auth->getIdentity()->appl_id; 
    	$registration_id = $auth->getIdentity()->registration_id;
    	if($this->getRequest()->isPost()) {
    			
    		$formData = $this->getRequest()->getPost();
    		if (isset($formData['p_ijasah'])) {
    			$db->update(array('std_app_ijazah'=>"1"), $registration_id);
    		} else {
    			$db->update(array('std_app_ijazah'=>null), $registration_id);
    		}
    		if (isset($formData['p_transcript'])) {
    			$db->update(array('std_app_transcript'=>"1"), $registration_id);
    		} else {
    			$db->update(array('std_app_transcript'=>null), $registration_id);
    		}
    		if (isset($formData['p_skpi'])) {
    			$db->update(array('std_app_skpi'=>"1"), $registration_id);
    		} else {
    			$db->update(array('std_app_skpi'=>null), $registration_id);
    		}
    	}
		
		$this->view->graduates = $db->getGraduatesNoWis($registration_id);
		
	 
	}
	
	public function graduationProcessAction(){
	
	
		//get applicant profile
		$auth = Zend_Auth::getInstance();
		$db = new App_Model_Record_DbTable_Graduation();
		$dbStd=new App_Model_Record_DbTable_StudentRegistration();
		$dbPreq=new App_Model_Record_DbTable_Pregraduation();
		$dbGrad=new App_Model_Record_DbTable_Graduation();
		$dbConvoApp=new App_Model_Record_DbTable_ConvocationApplication();
			
		//print_r($auth->getIdentity());
		$this->view->title="Graduation Process";
		$appl_id = $auth->getIdentity()->appl_id;
		$registration_id = $auth->getIdentity()->registration_id;
		$std=$dbStd->getStudentInfo($registration_id);
		if($this->getRequest()->isPost()) {
			 
			$formData = $this->getRequest()->getPost();
			if (isset($formData['submit_apply'])) {
				//save yudicium application
				$data['idStudentRegistration']=$registration_id;
				$data['add_date']=date('Y-m-d h:m:s');
				$data['add_by']=$registration_id;
				$dbPreq->insert($data);
			}
			
			if (isset($formData['submit_approval'])) {
					
				if (isset($formData['p_ijasah'])) {
					$db->update(array('std_app_ijazah'=>"1"), $registration_id);
				} else {
					$db->update(array('std_app_ijazah'=>null), $registration_id);
				}
				if (isset($formData['p_transcript'])) {
					$db->update(array('std_app_transcript'=>"1"), $registration_id);
				} else {
					$db->update(array('std_app_transcript'=>null), $registration_id);
				}
				if (isset($formData['p_skpi'])) {
					$db->update(array('std_app_skpi'=>"1"), $registration_id);
				} else {
					$db->update(array('std_app_skpi'=>null), $registration_id);
				}
			}
			if (isset($formData['wisuda'])) {
				//save yudicium application
				$data['IdStudentRegistration']=$registration_id;
				$data['apply_date']=date('Y-m-d h:m:s');
				if (!$dbConvoApp->isIn($registration_id) )$dbConvoApp->insert($data);
			}
			
		}
	
		if ($std) {
			$dbGrage=new App_Model_Exam_DbTable_StudentGrade();
			$grade=$dbGrage->getLastStudentGradeInfo($registration_id);
			if ($grade) {
				$std['ipk']=$grade['sg_cgpa'];
				$std['sks']=$grade['sg_cum_credithour'];
				
			} else {
				$std['ipk']=0;
				$std['sks']=0;
			}
			//prerequisite
			$dbYudReq=new App_Model_Record_DbTable_YudiciumPrerequisite();
			$preq=$dbYudReq->getData($std['IdProgram'], $std['IdProgramMajoring'], null);
			if ($preq) {
				$std['ipkmin']=$preq['ipkmin'];
				$std['sksmin']=$preq['sksmin'];
				$std['outstading_min']=$preq['outstanding_min'];
				$std['subject_fail_max']=$preq['subject_fail_max'];
				$std['close_subject']=$preq['close_subject'];
				
			} else {
				$std['ipkmin']=2;
				$std['sksmin']=100;
				$std['outstading_min']=0;
				$std['subject_fail_max']=0;
				$std['close_subject']=0;
			}
			
			
			$pre=$dbPreq->getDataByStd($registration_id);
			if ($pre) $std['apply']="1"; else $std['apply']="0";
			$grad=$dbGrad->getGraduatesNoWis($registration_id);
			if ($grad) {
				if ($grad['dean_approval_skr']>0) {
					$std['dean_approved']="1";
					$std['dean_skr']=$grad['dean_sk'];
					$std['date_yudicium']=$grad['date_of_Yudisium'];
				}
					
				else {
					$std['dean_approved']="0";
					$std['dean_skr']="";
					$std['date_yudicium']=null;
				}
				if ($grad['rector_approval_skr']>0) {
						$std['rector_approved']="1";
						$std['rector_skr']=$grad['skr'];
						$std['rector_dt']=$grad['date_of_skr'];
				 }
				else {
						$std['rector_approved']="0";
						$std['rector_skr']='';
						$std['rector_dt']=null;
				}
				$std['std_app_ijazah']=$grad['std_app_ijazah'];
				$std['std_app_transcript']=$grad['std_app_transcript'];
				$std['std_app_skpi']=$grad['std_app_skpi'];
				$std['enable_print_ijazah']=$grad['enable_print_ijazah'];
				$std['enable_print_transcript']=$grad['enable_print_transcript'];
				$std['enable_print_skpi']=$grad['enable_print_skpi'];
				$std['ijazah']=$grad['ijazah'];
				$std['transcript']=$grad['transcript'];
				$std['skpi']=$grad['skpi'];
				
			} else {
				$std['std_app_ijazah']='';
				$std['std_app_transcript']='';
				$std['std_app_skpi']='';
				$std['ijazah']='';
				$std['transcript']='';
				$std['skpi']='';
				$std['enable_print_ijazah']="";
				$std['enable_print_transcript']="";
				$std['enable_print_skpi']="";
			}
			
			//get convo schecule
			$dbConvo=new App_Model_Record_DbTable_Convocation();
			$convo=$dbConvo->getConvoDate();
			$std['price']='';
			if ($convo) {
				$std['dt_wisuda']=$convo['c_date_from'];
				$std['time_wisuda']="Not determined yet";
				 
			} else {
				$std['dt_wisuda']=null;
				$std['time_wisuda']="Not determined yet";
				
			}
			//chcek apply convo
			$convoapp=$dbConvoApp->getDataByStd($registration_id);
			$msg="Belum melakukan aplikasi ikut wisuda";
			if ($convoapp) {
				if ($convoapp['convocation_id']>0) {
					$std['convoapp']="1"; 
					$std['dt_wisuda']=date('d-M-Y', strtotime($convoapp['c_date_from']));
					$msg="Pendaftaran  wisuda telah disetujui silahkan selesaikan pembayaran wisuda";
					$std['time_wisuda']=$convoapp['c_time'];
					$std['stsbiaya']='Not Paid Yet';
					$std['price']=$convoapp['price'];
				} else {
					$std['convoapp']="1";
					$msg="Pendaftaran  wisuda telah dilakukan tunggu persetujuan";
					$std['time_wisuda']=date('d-M-Y', strtotime($convoapp['c_date_from'])).' '.$convoapp['c_time'];
					$std['stsbiaya']='Not Paid Yet';
					$std['price']='';
				}
			}
			else {
					$std['convoapp']="0";
					$msg="Pendaftaran  wisuda belum dilakukan";
					$std['stsbiaya']='Not Paid Yet';
					
			}
			//check for convo approval
			$dbConvoApproval=new App_Model_Record_DbTable_ConvocationGraduate();
			$convgrad=$dbConvoApproval->getData($registration_id);
			$std['ijazah_status']='Belum tersedia';
			$std['transcript_status']='Belum tersedia';
			$std['skpi_status']='Belum tersedia';
			if ($convgrad) {
				 
					$std['convo']="1";
					$msg="Pendaftaran wisuda disetujui dan sudah membayar";
					$std['stsbiaya']='Paid';
					
				 	if ($convgrad['ijazah']=='1') $std['ijazah_status']='Sudah Tersedia silahkan diambil di Fakultas';
				 	if ($convgrad['transcript']=='1') $std['transcript_status']='Sudah Tersedia silahkan diambil di Fakultas';
				 	if ($convgrad['skpi']=='1') $std['skpi_status']='Sudah Tersedia silahkan diambil di Fakultas';
			}
			 else {
			 	$std['convo']="0";
			 	$std['stsbiaya']='Not Paid Yet';
			}
			$std['msg']=$msg;
		}
		$this->view->student = $std;
	
	
	}
	
	public function uploadCertificateAction(){
		/*
		 * check session for transaction
		*/
		$auth = Zend_Auth::getInstance();
	
	
		$auth = Zend_Auth::getInstance();
		$appl_id = $auth->getIdentity()->appl_id;
		$this->view->appl_id = $appl_id;
	
		$Idregistration = $auth->getIdentity()->registration_id;
		$this->view->registration_id = $Idregistration;
		 
		if ($this->getRequest ()->isPost ()) {
	
			$formData = $_POST;
			$docname=$formData['document_name'];
			$uploadfileDB = new App_Model_Skpi_DbTable_UploadFile();
			$idhonor=$formData['items_id'];
			$redirect=$formData['redirect_path']; 
			$docname="Bebas Pinjam Perpustakaan";
			//$redirect=$redirect[$docname];
			//echo var_dump($redirect);exit;
	
			//if($idhonors==$formData["idHonors"]){
			///upload_file
	
			$apath = DOCUMENT_PATH."/student/graduation/".$Idregistration."/".$docname;
			 
			//create directory to locate file
			if (!is_dir($apath)) {
				//echo($apath);exit;
				if (!mkdir($apath, 0775,true)) echo "Can not create directory";
			}
	
			if (is_uploaded_file($_FILES["file"]['tmp_name'])){
					
				$ext_photo = strtolower($this->getFileExtension($_FILES["file"]["name"]));
					
				if($ext_photo==".pdf" || $ext_photo==".PDF" || $ext_photo==".jpg" || $ext_photo==".JPG" || $ext_photo==".jpeg" || $ext_photo==".JPEG" || $ext_photo==".gif" || $ext_photo==".GIF" || $ext_photo==".png" || $ext_photo==".PNG"){
					$flnamephoto = date('Ymdhs')."_".$docname.$ext_photo;
					$path_photograph = $apath."/".$flnamephoto;
					if (move_uploaded_file($_FILES["file"]['tmp_name'], $path_photograph)) {
	
						$upd_photo = array(
								'auf_idStudentRegistration' => $Idregistration,
								'auf_Items'=>$idhonor,
								'auf_file_name' => $flnamephoto,
								'auf_file_type' => $docact,
								'auf_upload_date' => date("Y-m-d h:i:s"),
								'auf_upload_by' => $appl_id,
								'pathupload' => $path_photograph
						);
						$row=$uploadfileDB->getFileItems($Idregistration,$idhonor, $docact);
						if ($row)
							$uploadfileDB->updateData($upd_photo, $row['auf_id']);
						else
							$uploadfileDB->addData($upd_photo);
					}
	
				}
					
			}
	
			//}
	
		}
		$this->_redirect($redirect);
	
	}
	
	function getFileExtension($filename){
		return substr($filename, strrpos($filename, '.'));
	}
	
	public function activityListAction(){
		$this->view->title="Form: Student Activity Application";
		$auth = Zend_Auth::getInstance();
		$registration_id = $auth->getIdentity()->registration_id;
		$appl_id = $auth->getIdentity()->appl_id;
		$this->view->IdStudentRegistration = $registration_id;
		$dbStd=new App_Model_Registration_DbTable_Studentregistration();
		$std=$dbStd->getStudentRegistrationDetail($registration_id);
		$this->view->student=$std;
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
			$idstd=$formData['IdStudentRegistration'];
			$name=$formData['name'];
			 
			foreach ($grp as $idactivity=>$activity) {
				foreach ($activity as $idgrp=>$value) {
					$data=array('IdActivity'=>$idactivity,
							'IdStudentRegistration'=>$idstd,
							'ParticipantName'=>$name,
							'ParticipantStatus'=>'01',
							'entried_dt'=>date('Y-m-d h:s:i'),
							'IdStaff'=>0,
							'appl_id'=>$std['IdApplication'],
							'at_trans_id'=>$std['transaction_id']
					);
					$row=$dbParticipant->isInStudent($idactivity, $std['IdApplication'],$std['transaction_id']);
					//echo var_dump($row);
					if (!$row)
						$idparticipant=$dbParticipant->addData($data);
					else {
						$idparticipant=$row['IdParticipant'];
						$dbParticipant->updateData($data, $idparticipant);
					}
					//echo 'aprti='.$idparticipant;
					$data=array('IdParticipant'=>$idparticipant,
								'IdGroup'=>$idgrp,
								'dt_entry'=>date('Y-m-d h:s:i')
					);
					if (!$dbGrpPartcipant->isIn($idgrp, $idparticipant))
							$dbGrpPartcipant->addData($data);
					//exit;
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
		$openactivity=$dbActivityGrp->getOpenActivity($std['IdProgram']);
		foreach ($openactivity as $key=>$value) {
			$grp=$dbActivityGrp->getGroupActivity($value['IdActivity'],$std['IdProgram']);
			//cek participan
			$participant=$dbParticipant->isInStudent($value['IdActivity'],  $std['IdApplication'],$std['transaction_id']);
			
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
}

?>