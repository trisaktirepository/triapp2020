<?php

class Agent_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        $this->view->title = $this->view->translate("manual_entry");
        
        $auth = Zend_Auth::getInstance(); 
        
        $registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
        $this->view->locale = $locale;
 		//get current intake
		$intakeDB = new App_Model_Record_DbTable_Intake();
		$intake = $intakeDB->getCurrentIntake();
		$IdIntake = $intake["IdIntake"];
    	$this->view->intake=$IdIntake;
		if($IdIntake==""){
			$this->view->noticeError="Harap maaf, Pendaftaran belum dibuka";
		}       
        
        if ($this->getRequest()->isPost()) {    		
    		
    		$formData = $this->getRequest()->getPost();  

    		   	
    			$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();
    			
    			for($x=1; $x<=$formData["iteration"]; $x++){    				
    				    			
    				//cke samada pes dah pakai lom
    				$agentFormNumberDb = new App_Model_Application_DbTable_AgentFormNumber();
    				$return = $agentFormNumberDb->checkUnusedFormNo($formData["at_pes_id".$x],$IdIntake);
    				
    				if($return==1){
    					
    							//bila agent pilih existing profile 
			    				//(email yg dah ada dalam dataabase so xah create profile just create new transaction sahaja)
			    				
			    				if($formData["sel_profile".$x] != ''){ //x select existing profile
			    					
			    					$applicant_id = $formData["sel_profile".$x];
			    					$applicant = $appProfileDB->getData($applicant_id);
			    					$generate_password = $applicant["appl_password"];
									
			    				}else{
			    					
			    					$generate_password = mt_rand(100000,999999);    				
			    				
					    		  	$info["appl_fname"]=strtoupper($formData["firstname".$x]);
									$info["appl_mname"]=strtoupper($formData["middlename".$x]);
									$info["appl_lname"]=strtoupper($formData["lastname".$x]);
									$info["appl_email"]=$formData["email".$x];
									$info["appl_password"]=$generate_password;
									$info["appl_dob"]=$formData["day".$x]."-".$formData["month".$x]."-".$formData["year".$x];
									$info["appl_prefer_lang"]=2;				
									
								
									//insert student profile into applicant_profile
									$applicant_id = $appProfileDB->addData($info);
									
			    				}//end if
			    				
											
													
								
								//insert applicant id into applicant_transaction
			
								$data["at_appl_id"]=$applicant_id;
								$data["at_appl_type"]=$formData["at_appl_type".$x];
								$data["at_pes_id"]=$formData["at_pes_id".$x];									
								$data["at_status"]="APPLY";
								$data["at_create_by"]=$auth->getIdentity()->id; 
								$data["at_create_date"]=date("Y-m-d H:i:s");	
								$data["agent_id"]=$auth->getIdentity()->id;  
								$data["at_intake"]=$IdIntake;  
								$data["entry_type"]=2; //1:online 2:manual 			
								
								$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
								$transDB->addData($data);
								
								
								//update agentFormBumver
								if($formData["at_appl_type".$x]==1){//USM
									 $agentFormNumberDb = new App_Model_Application_DbTable_AgentFormNumber();
									 $agentForm["afn_taken_status"]=1;
									 $agentFormNumberDb->updateTakenFormNo($agentForm,$formData["at_pes_id".$x]);
								}
							
								//--------send email section----------
							
								//get Email Template based on preferred Language
								$templateDB = new App_Model_General_DbTable_EmailTemplate();
								$templateData = $templateDB->getData(1,2);
								
								$templateMail = $templateData['body'];				
								$templateMail = str_replace("[Candidate]",$formData["firstname".$x],$templateMail);
								$templateMail = str_replace("[EmailApplicant]",$formData["email".$x],$templateMail);
								$templateMail = str_replace("[PassApplicant]",$generate_password,$templateMail);				
								$templateMail = str_replace("[FIRST_NAME]",$formData["firstname".$x],$templateMail);
								$templateMail = str_replace("[MIDDLE_NAME]",$formData["middlename".$x],$templateMail);
								$templateMail = str_replace("[LAST_NAME]",$formData["lastname".$x],$templateMail);
								
								$sent = $this->sendMail($formData["email".$x],$formData["firstname".$x],$templateData['subject'],$templateMail);
    				}//end if    				
    									
    			}//end foreach

    			$this->view->noticeSuccess = $this->view->translate('data_has_been_saved')."<br />". $this->view->translate('thank_you');
        }
    }
    
    
    
	public function onlineAction()
    {
        // action body
        $this->view->title = $this->view->translate("online_entry");
        
        $auth = Zend_Auth::getInstance(); 
        
        //clear appl_id & txn id
        $auth->getIdentity()->appl_id = null;
        $auth->getIdentity()->transaction_id = null;
        
        $registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
        $this->view->locale = $locale;
       
    	
		//get current intake
		$intakeDB = new App_Model_Record_DbTable_Intake();
		$intake = $intakeDB->getCurrentIntake();
		$IdIntake = $intake["IdIntake"];
    	$this->view->intake=$IdIntake;
		if($IdIntake==""){
			$this->view->noticeError="Harap maaf, Pendaftaran belum dibuka";
		}      
        
        if ($this->getRequest()->isPost()) {    		
    		
    		$formData = $this->getRequest()->getPost();  

    		   	
    			$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();
    			
    			for($x=1; $x<=$formData["iteration"]; $x++){    				
    				    			
    				
    				
    				//bila agent pilih existing profile 
    				//(email yg dah ada dalam dataabase so xah create profile just create new transaction sahaja)
    				
    				if($formData["sel_profile".$x] != ''){ //x select existing profile
    					
    					$applicant_id = $formData["sel_profile".$x];
    					$applicant = $appProfileDB->getData($applicant_id);
    					$generate_password = $applicant["appl_password"];
						
    				}else{
    					
	    				$generate_password = mt_rand(100000,999999);	    				
	    				
		    		  	$info["appl_fname"]=strtoupper($formData["firstname".$x]);
						$info["appl_mname"]=strtoupper($formData["middlename".$x]);
						$info["appl_lname"]=strtoupper($formData["lastname".$x]);
						$info["appl_email"]=$formData["email".$x];
						$info["appl_password"]=$generate_password;
						$info["appl_dob"]=$formData["day".$x]."-".$formData["month".$x]."-".$formData["year".$x];
						$info["appl_prefer_lang"]=2;				
						
					
						//insert student profile into applicant_profile
						$applicant_id = $appProfileDB->addData($info);
						
    				}//end if	

    				
					
					//transaction info		
				    $data["at_appl_id"]=$applicant_id;	
				   	$data["at_appl_type"]=$formData["at_appl_type".$x];									
					$data["at_status"]="APPLY";
					$data["at_create_by"]=$auth->getIdentity()->id; 
					$data["at_create_date"]=date("Y-m-d H:i:s");	
					$data["agent_id"]=$auth->getIdentity()->id; 
					$data["entry_type"]=1; //1:online 2:manual 	
					$data["at_intake"]=$formData["IdIntake"];		
					
					$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
					$transDB->addData($data);
				
					//--------send email section----------
				
					//get Email Template based on preferred Language
					$templateDB = new App_Model_General_DbTable_EmailTemplate();
					$templateData = $templateDB->getData(1,2);
					
					$templateMail = $templateData['body'];				
					$templateMail = str_replace("[Candidate]",$formData["firstname".$x],$templateMail);
					$templateMail = str_replace("[EmailApplicant]",$formData["email".$x],$templateMail);
					$templateMail = str_replace("[PassApplicant]",$generate_password,$templateMail);				
					$templateMail = str_replace("[FIRST_NAME]",$formData["firstname".$x],$templateMail);
					$templateMail = str_replace("[MIDDLE_NAME]",$formData["middlename".$x],$templateMail);
					$templateMail = str_replace("[LAST_NAME]",$formData["lastname".$x],$templateMail);
					
					$sent = $this->sendMail($formData["email".$x],$formData["firstname".$x],$templateData['subject'],$templateMail);
					
					
    			}//end foreach

    			$this->view->noticeSuccess = $this->view->translate('data_has_been_saved')."<br />". $this->view->translate('thank_you');
        }
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
      
      
      
	public function checkApplicantidAction($at_pes_id=null){

	 	$storage = new Zend_Auth_Storage_Session ();
	 	$intakeDB = new App_Model_Record_DbTable_Intake();
		$intake = $intakeDB->getCurrentIntake();
		$IdIntake = $intake["IdIntake"];
		$data = $storage->read ();
		if (! $data) {
			$this->_redirect ( 'index/index' );
		}
			
    	$at_pes_id = $this->_getParam('at_pes_id','');
    	
     
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
        //check form_pes_id
        $agentFormNumberDb = new App_Model_Application_DbTable_AgentFormNumber();
        $validStatus = $agentFormNumberDb->checkValidFormNo($at_pes_id,$IdIntake);
        $unuseStatus = $agentFormNumberDb->checkUnusedFormNo($at_pes_id,$IdIntake);
        
        //unique id
      	$transactionDB  = new App_Model_Application_DbTable_ApplicantTransaction();
	    $appl_info=$transactionDB->uniqueApplicantid( $at_pes_id );
	   
	   
	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode( 
			array(
				'success' => ($appl_info && $unuseStatus),
				'validity' => $validStatus,
				'available' => $unuseStatus,
				'unique' => $appl_info
			)
		);
		
		echo $json;
		exit;

    }
      
	public function getEmailAction($email=null){

	 	$storage = new Zend_Auth_Storage_Session ();
		$data = $storage->read ();
		if (! $data) {
			$this->_redirect ( 'index/index' );
		}
			
    	$emel = $this->_getParam('email','');
    	
     
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
      	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();
	    $email_info=$appProfileDB->uniqueEmail($emel);
	   
	   
	    $list_applicant = $appProfileDB->getSameEmail($emel);
	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		//$json = Zend_Json::encode( array('success' => $email_info));
		$json = Zend_Json::encode( $list_applicant);
		$this->view->json = $json;
		

    }
    
    public function applicantListAction(){
    	
    	  $this->view->title = $this->view->translate("applicant_list");
    	  
    	  $auth = Zend_Auth::getInstance();

    	  //clear appl_id & txn id
    	  $auth->getIdentity()->appl_id = null;
    	  $auth->getIdentity()->transaction_id = null;
    	  
    	  $this->view->agent_id = $auth->getIdentity()->id;
    	  
    	  $entry_type = $this->_getParam('entry_type','');
    	  $this->view->entry_type = $entry_type;
    	  
    	  $form = new Agent_Form_SearchApplicant();
    	  $this->view->form = $form;
    	  
		  $transactionDB = new App_Model_Application_DbTable_ApplicantTransaction();
			//get current intake
			$intakeDB = new App_Model_Record_DbTable_Intake();
			$intake = $intakeDB->getCurrentIntake();
			$this->view->curintake = $intake["IdIntake"];	
		
			if ($this->getRequest()->isPost()) {
				$formData = $this->getRequest()->getPost();
				
				$applicant_data = $transactionDB->getAgentData($formData);
				
				$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($applicant_data));
				$paginator->setItemCountPerPage(50);
				$paginator->setCurrentPageNumber($this->_getParam('page',1));
				$this->view->searchParams = $formData;
				
				$form->populate($formData);
				$this->view->form = $form;
				
			}else{
				$condition = array ("agent_id"=>$auth->getIdentity()->id);
				
				$applicant_data = $transactionDB->getAgentPaginateData($condition);
				$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($applicant_data));
				$paginator->setItemCountPerPage(50);
				$paginator->setCurrentPageNumber($this->_getParam('page',1));
				$this->view->searchParams = $condition;
			}
			
		
			$this->view->paginator = $paginator;
    }
    
    
	public function continueAction(){
		
    	$txnId = $this->_getParam('id', 0);
    	
    	$auth = Zend_Auth::getInstance();    	    	
    	$agent_id = $auth->getIdentity()->id; 
    	
    	$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
    	
    	$txnData = $transactionDb->getTransaction($txnId);
    	
    	if($txnId!=0 && $transactionDb->checkValidAgentApplicant($txnId, $agent_id) && $txnData ){
    		    		
    		$auth->getIdentity()->transaction_id = $txnId;
    		$auth->getIdentity()->appl_id = $txnData['at_appl_id'];
    		$auth->getIdentity()->appl_prefer_lang = $txnData['appl_prefer_lang'];
    		
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'biodata'),'default',true));
    	}else{
    		$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
    	}
    }
    
    
    public function biodataAction(){
    	
    	$this->view->title = $this->view->translate("Biodata");
    	
    	$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
		//$transaction_id = $this->_getParam('id', 0);
		$auth = Zend_Auth::getInstance(); 
		$transaction_id = $auth->getIdentity()->transaction_id; 
		$this->view->transaction_id = $transaction_id;
		
   		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));	
		}
		
		$applicationTransactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
		$trans_data = $applicationTransactionDb->getTransactionData($transaction_id);
		$this->view->transaction = $trans_data;
		
		
		$appl_id = $trans_data["at_appl_id"];
		$this->view->appl_id = $appl_id;
		
		$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	
    	$form = new App_Form_Biodata(array ('lang' => $locale));
    	
    	
    if ($this->getRequest()->isPost()) {    		
    		
    		$formData = $this->getRequest()->getPost();  

    		//check nationality
    		if($formData["appl_nationality"]==1){
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
				$info["appl_nationality"]=$formData["appl_nationality"];				
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
	
				
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'contactinfo'),'default',true));
    		
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
			

	    	$form->populate($applicant);
	    	$this->view->form = $form;
    	}
    }
    
    
	public function contactinfoAction() {
		
		$msg = $this->_getParam('msg', null);
		if($msg){
			$this->view->noticeError = $msg;	
		}
			
		//title
    	$this->view->title = $this->view->translate("ContactInfo");
    	
    	//$transaction_id = $this->_getParam('id', 0);
		$auth = Zend_Auth::getInstance(); 
		$transaction_id = $auth->getIdentity()->transaction_id; 
		$this->view->transaction_id = $transaction_id;
		
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));	
		}
		
		$applicationTransactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
		$transaction = $applicationTransactionDb->getTransactionData($transaction_id);
		$this->view->transaction = $transaction;
		
		
				
		$appl_id = $transaction["at_appl_id"];
		$this->view->appl_id = $appl_id;
		    	    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	    	    	
		    	
    	$form = new App_Form_ContactInfo();
    	
    	if ($this->getRequest()->isPost()) {
	    		$formData = $this->getRequest()->getPost();
	    		
	    		if ($form->isValid($formData)) {
	    				
	    			 unset($formData['check_opt']);
	    			 unset($formData['check_opt_same']);
	    			 unset($formData['save']);
	    			
	    			$appProfileDB->updateData($formData, $formData["appl_id"]);
	    			
	    			if($transaction['entry_type']!=2){
	    				
	    				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'admission'),'default',true));
		    			
	    			}else{//kalo manual entry xyah pilih admission lagi
	    				
	    				if( $transaction['at_appl_type'] == 1 ){
							$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'placement-test'),'default',true));
		    			}else{
		    				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'programme'),'default',true));
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
		
		$msg = $this->_getParam('msg', null);
		if($msg){
			$this->view->noticeError = $msg;	
		}
			
		//title
    	$this->view->title = $this->view->translate("Admission Type");    	
    	
    	$auth = Zend_Auth::getInstance(); 
		$transaction_id = $auth->getIdentity()->transaction_id; 
		$this->view->transaction_id = $transaction_id;
		
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));	
		}
		
		$applicationTransactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
		$transaction = $applicationTransactionDb->getTransactionData($transaction_id);
		$this->view->transaction = $transaction;	
				
		$appl_id = $transaction["at_appl_id"];
		$this->view->appl_id = $appl_id;
		    	    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	
    	$form = new Agent_Form_ApplicantAdmission(array("admission_type"=>$transaction["at_appl_type"]));
    	$this->view->form = $form;
    	
    	if ($this->getRequest()->isPost()) {
	    		$formData = $this->getRequest()->getPost();
	    		
	    		if ($form->isValid($formData)) {
	    			
	    			//delete aplicant program
	    			$applicantProgram = new App_Model_Application_DbTable_ApplicantProgram();
		    		$applicantProgram->deleteTransactionData($transaction_id);
	    			
	    			$info["at_appl_type"]=$formData["at_appl_type"];
					$applicationTransactionDb->updateData($info,$transaction_id);
	    			
	    			
	    			if( $formData["at_appl_type"] == 1 ){ //USM
						$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'placement-test'),'default',true));
	    			}else{
	    				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'programme'),'default',true));
	    			}
	    		}
	    		
    	}else{
	    	$form->populate($transaction);
	    	$this->view->form = $form;
    	}//end post
    	
	}
	
	
	public function placementTestAction() {
		
		
		//title
    	$this->view->title = $this->view->translate("placement_test_schedule");
    	
    	$auth = Zend_Auth::getInstance(); 
		$transaction_id = $auth->getIdentity()->transaction_id; 
		$this->view->transaction_id = $transaction_id;
		
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));	
		}
		
		$applicationTransactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
		$transaction = $applicationTransactionDb->getTransactionData($transaction_id);
		$this->view->transaction = $transaction;
				
		$appl_id = $transaction["at_appl_id"];
		$this->view->appl_id = $appl_id;
		    	    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	   		
    	   	    	
    	//get available placement test (code)
    	$placementDB = new App_Model_Record_DbTable_PlacementHead();
    	$program = $placementDB->getPlacementTest();
    	$this->view->placement_code = $program["aph_placement_code"];
    	
    	$aph_fees_program  = $program["aph_fees_program"];
    	$aph_fees_location = $program["aph_fees_location"];
    	
    	$ptestinfo = new App_Model_Application_DbTable_ApplicantPtest();   
    	$applicant_placement_test_info = $ptestinfo->getScheduleInfo($transaction_id);
    	
    	$this->view->aps_id = $applicant_placement_test_info["aps_id"];
    		
    	$form = new Agent_Form_ApplicantPlacementTest(array('aphplacementcode'=>$program["aph_placement_code"],'aphfeesprogram'=>$program["aph_fees_program"],
    	'aphfeeslocation'=>$program["aph_fees_location"],'transactionid'=>$transaction_id ,'applid'=>$appl_id));
    	
    	if ($this->getRequest()->isPost()) {    		
    		
    		$formData = $this->getRequest()->getPost();   		    		
			
    		if($form->isValid($formData)){ 
    			   			
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
				
				
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'programme'),'default',true));
    		}else{
    			
    			    $this->view->aps_id = $formData["aps_id"]; //appl_placement_location
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
	
	
	public function programmeAction() {
		
		$auth = Zend_Auth::getInstance(); 
		$transaction_id = $auth->getIdentity()->transaction_id; 
		$this->view->transaction_id = $transaction_id;
		
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));	
		}
	
		$applicationTransactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
		$transaction = $applicationTransactionDb->getTransactionData($transaction_id);
		$this->view->transaction = $transaction;

	
		$appl_id = $transaction["at_appl_id"];
		$this->view->appl_id = $appl_id;
		 	    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	 	
		if($transaction['at_appl_type']=='0'){
    		$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'admission','msg'=>$this->view->translate('Please select admission type')),'default',true));
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
			$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'programme-highschool'),'default',true));
			
		}else{//admission type = placement test (id=1)
			//check ptest
	    	$ptestDb = new App_Model_Application_DbTable_ApplicantPtest();
	    	$ptestData = $ptestDb->getPtest($transaction_id);
	    	
	    	if($transaction["at_appl_type"]==1){//placement test
		    	if(!$ptestData){
		    		$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'placement-test','msg'=>$this->view->translate('please_enter_placement_test_info.')),'default',true));
		    	}
			}
	
    		$form = new Agent_Form_ApplicantProgramme(array('ae_appl_id'=>$applicant['appl_id'],'admissiontype'=>1,'transaction_id'=>$transaction_id));
    		
	    	if ($this->getRequest()->isPost()) {
	    		$formData = $this->getRequest()->getPost();

	    		if($form->isValid($formData)){
	    			
	    			/** EDUCATION **/
	    				$educationDb = new App_Model_Application_DbTable_ApplicantEducation();
						//delete education
						$educationDb->delete('ae_transaction_id = '.$transaction_id);
						
						//add education
						$data = array(
							'ae_appl_id' => $appl_id,
						    'ae_transaction_id' =>$transaction_id, 
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
	    			$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'uploaddocument'),'default',true));
	    			
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
	
	
	public function programmeHighschoolAction(){
		
		$auth = Zend_Auth::getInstance(); 
		$transaction_id = $auth->getIdentity()->transaction_id; 
		$this->view->transaction_id = $transaction_id;
		
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));	
		}
		
		$applicationTransactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
		$transaction = $applicationTransactionDb->getTransactionData($transaction_id);
		$this->view->transaction = $transaction;
				
		
		$appl_id = $transaction["at_appl_id"];
		$this->view->appl_id = $appl_id;
		    	    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
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
			$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'programme'),'default',true));
			
		}else{//admission type = high school(2)
			
			$form = new Agent_Form_ApplicantProgramme(array('ae_appl_id'=>$applicant['appl_id'], 'admissiontype'=>2,'transaction_id'=>$transaction_id));
 	
			if ($this->getRequest()->isPost()) {  
				$formData = $this->getRequest()->getPost();
				  		
				if ($form->isValid($formData)) {
					
					$educationDb = new App_Model_Application_DbTable_ApplicantEducation();
					//delete education
					$educationDb->delete('ae_appl_id = '.$appl_id);
					
					//add education
					$data = array(
						'ae_appl_id' => $appl_id,
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
								'aed_sem1' => ($formData['aed_sem1'][$i]!="")?$formData['aed_sem1'][$i]:0,
								'aed_sem2' => ($formData['aed_sem2'][$i]!="")?$formData['aed_sem2'][$i]:0,
								'aed_sem3' => ($formData['aed_sem3'][$i]!="")?$formData['aed_sem3'][$i]:0,
								'aed_sem4' => ($formData['aed_sem4'][$i]!="")?$formData['aed_sem4'][$i]:0,
								'aed_sem5' => ($formData['aed_sem5'][$i]!="")?$formData['aed_sem5'][$i]:0,
								'aed_sem6' => ($formData['aed_sem6'][$i]!="")?$formData['aed_sem6'][$i]:0,
								'aed_average' => ($formData['aed_average'][$i]!="")?$formData['aed_average'][$i]:0
							); 
						
							$educationDetailDb->addData($data);
							$i++;	
						}
						
						/*** PREFERED PRGORRAMME ***/
		    			$applicantProgram = new App_Model_Application_DbTable_ApplicantProgram();
		    			
		    			//delete ptest
		    			$applicantProgram->deleteTransactionData($transaction_id);

		    			//add ptest program prefered 1
		    			$data1 = array(
		    				'ap_at_trans_id' =>$transaction_id,
		    				'ap_prog_code' => $formData['ap_prog_code'],		    				
		    				'ap_preference' =>1
		    			);
		    			
		    			$applicantProgram->insert($data1);
						
						//redirect
						$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'uploaddocument'),'default',true));
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
		
	public function uploaddocumentAction(){
		
		$this->view->title = $this->view->translate("upload_document");
		
		$auth = Zend_Auth::getInstance(); 
		$transaction_id = $auth->getIdentity()->transaction_id; 
		$this->view->transaction_id = $transaction_id;
		
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));	
		}
		
		$applicationTransactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
		$transaction = $applicationTransactionDb->getTransactionData($transaction_id);
		$this->view->transaction = $transaction;
				
		$appl_id = $transaction["at_appl_id"];
		$this->view->appl_id = $appl_id;
		    	    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	    	    	
    	$applicantProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
    	$applicantProgram = $applicantProgramDB->getPlacementProgram($transaction_id);
    	
    	if(!$applicantProgram){
    		$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'programme','msg'=>$this->view->translate('please_fill_in_program_prefered')),'default',true));
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
		
		$auth = Zend_Auth::getInstance(); 
		$transaction_id = $auth->getIdentity()->transaction_id; 
		$this->view->transaction_id = $transaction_id;
		
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));	
		}
		
		$applicationTransactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
		$transaction = $applicationTransactionDb->getTransactionData($transaction_id);
		$this->view->transaction = $transaction;
				
		$appl_id = $transaction["at_appl_id"];
		$this->view->appl_id = $appl_id;
		    	    	
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
				
			
				/*if (is_uploaded_file($_FILES["passport"]['tmp_name'])){
					
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
				}*/
				
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
    		
			
    		$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'uploaddocument'),'default',true));

    	}
	}
	
	function getFileExtension($filename){
  		return substr($filename, strrpos($filename, '.'));
	}
	
	public function removeFileAction(){
		
		$auth = Zend_Auth::getInstance(); 
		$transaction_id = $auth->getIdentity()->transaction_id; 
		$this->view->transaction_id = $transaction_id;
		
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));	
		}
		
		$applicationTransactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
		$transaction = $applicationTransactionDb->getTransactionData($transaction_id);
		$this->view->transaction = $transaction;
				
		$appl_id = $transaction["at_appl_id"];
		$this->view->appl_id = $appl_id;
		    	    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
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
			
			$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'uploaddocument'),'default',true));
    	}    	
	}
	
	
	public function reuploaddocumentfilesAction() {
		
		$auth = Zend_Auth::getInstance(); 
		$transaction_id = $auth->getIdentity()->transaction_id; 
		$this->view->transaction_id = $transaction_id;
		
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));	
		}
		
		$applicationTransactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
		$transaction = $applicationTransactionDb->getTransactionData($transaction_id);
		$this->view->transaction = $transaction;
				
		$appl_id = $transaction["at_appl_id"];
		$this->view->appl_id = $appl_id;
		    	    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
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
    		
    		$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'confirmation'),'default',true));

    	}
	}
	
	
	public function confirmationAction(){
		
		$this->view->title = $this->view->translate("confirmation");
		
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
		$auth = Zend_Auth::getInstance(); 
		$transaction_id = $auth->getIdentity()->transaction_id; 
		$this->view->transaction_id = $transaction_id;
		
		if($auth->getIdentity()->transaction_id==null){
			$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));	
		}
		
		$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
		$transData = $transDB->getTransactionData($transaction_id);
		$this->view->transaction = $transData;
				
		$appl_id = $transData["at_appl_id"];
		$this->view->appl_id = $appl_id;
		
		//get applicant profile
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getTransProfile($appl_id,$transaction_id);		 
    	$this->view->applicant = $applicant;   
    		
    	$appl_prefer_lang = $applicant["appl_prefer_lang"];
    	$this->view->appl_prefer_lang = $applicant["appl_prefer_lang"];
    	
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
    	
    	
    	if((!$photo) || (!$raport)){
    		$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'uploaddocument','msg'=>$this->view->translate('please_upload_document.')),'default',true));
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

				    		/*----------utk case placement test prog id null---------*/
			    		
			    		    //nak dapatpakn placement test code
			    			$ptestinfo = new App_Model_Application_DbTable_ApplicantPtest();   
			    			$applicant_ptest_info = $ptestinfo->getScheduleInfo($transaction_id);
			    			
					    	//getPTestProgram Data
			    			$ptestProgram = new App_Model_Application_DbTable_PlacementTestProgram();
			    			$ptestData = $ptestProgram->getInfo($applicant_ptest_info["apt_ptest_code"],$program['program_code']);
			    			
			    			$appprogramDB->updateProgramPtest(array('ap_ptest_prog_id' => $ptestData['app_id']),$transaction_id,$program['program_code'],$i);			    			
			    			
			    			/* ----------------------------------------------------------- */
			    			
				    	$i++;
				    	}	
						//exit;
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
							$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','id'=>$transaction_id, 'action'=>'verification','msg'=>$error),'default',true));
							exit;
						}
						
						//update transaction period based on ptest schedule
						$ptestinfo = new App_Model_Application_DbTable_ApplicantPtest();   
						$applicant_ptest_info = $ptestinfo->getScheduleInfo($transaction_id);
						$ptPeriod   = $periodDB->getCurrentPeriod(date("n",strtotime($applicant_ptest_info['aps_test_date'])), date("Y",strtotime($applicant_ptest_info['aps_test_date'])));
						
						
						//once submmitted update status prcess sebab da bayar masa amik form dari agent
		    			$upddata["at_status"]='PROCESS';
		    			$upddata["at_intake"]=$IdIntake;
		    			$upddata["at_period"]=$ptPeriod['ap_id'];
		    			$upddata["at_submit_date"] = date("Y-m-d H:i:s");
		    		
						$transDB->updateData($upddata,$transaction_id);

						$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'viewkartu','id'=>$transaction_id),'default',true));
					}
					
				}else{	//online				
					
					//kalau dah ada pes jgn mintak no pes lagi
					//check no pes
					if( !isset($transData["at_pes_id"]) && $transData["at_pes_id"]==null ){
						//to get and update applicantID
						$applicantID = $transDB->getApplicantID($transData["at_appl_type"],$IdIntake);
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
			    		
			    		/*----------utk case placement test prog id null---------*/
			    		    if($transData["at_appl_type"]==1){ //uSM
			    		    	
				    		    //nak dapatpakn placement test code
				    			$ptestinfo = new App_Model_Application_DbTable_ApplicantPtest();   
				    			$applicant_ptest_info = $ptestinfo->getScheduleInfo($transaction_id);
				    			
				    			
						    	//getPTestProgram Data
				    			$ptestProgram = new App_Model_Application_DbTable_PlacementTestProgram();
				    			$ptestData = $ptestProgram->getInfo($applicant_ptest_info["apt_ptest_code"],$program['program_code']);
				    			
				    			$programDB->updateProgramPtest(array('ap_ptest_prog_id' => $ptestData['app_id']),$transaction_id,$program['program_code'],$i);			    			

			    		    }//end if	
			    			/* ----------------------------------------------------------- */
			    		
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

		    				//update transaction period based on ptest schedule
							$ptPeriod   = $periodDB->getCurrentPeriod(date("n",strtotime($applicant_placement_test_info['aps_test_date'])), date("Y",strtotime($applicant_placement_test_info['aps_test_date'])));
				    		
		    				//once submmitted update status=CLOSE
		    				$upddata["at_status"]='CLOSE';
				    		$upddata["at_intake"]=$IdIntake;
				    		$upddata["at_period"]=$ptPeriod["ap_id"];
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
							
							//once submmitted update status=prcess
				    		$upddata["at_status"]='PROCESS';
				    		$upddata["at_intake"]=$IdIntake;
				    		$upddata["at_period"]=$idPeriod;
				    		$upddata["at_submit_date"]=date("Y-m-d H:i:s");
							$transDB->updateData($upddata,$transaction_id);	
							
							if($applicant["appl_gender"]==1) $gender="LAKI-LAKI";
							if($applicant["appl_gender"]==2) $gender="PEREMPUAN";
							if($applicant["appl_gender"]==0) $gender="TIDAK DINYATAKAN";
			
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
		    	    $auth->getIdentity()->transaction_id= null;
		    	   
					if($admission_type==1){	
						$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'notification'),'default',true));
					}elseif ($admission_type==2){						
						$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','action'=>'viewletter','id'=>$transaction_id),'default',true));
					}
					
    	}//end post data
    		
    		
	}
	
	
	
	
	
	public function notificationAction(){	
				 
        $auth = Zend_Auth::getInstance(); 
        $auth->getIdentity()->transaction_id= null;
        
        $this->view->notice = $this->view->translate('confirmation_notice')."<br />". $this->view->translate('thank_you');
	}
	
	
	public function verificationAction(){
	
		$txnId = $this->_getParam('id', 0);
		 
		$auth = Zend_Auth::getInstance();
		$agent_id = $auth->getIdentity()->id;
		 
		$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
		 
		$txnData = $transactionDb->getTransaction($txnId);
		 
		if($txnId!=0 && $transactionDb->checkValidAgentApplicant($txnId, $agent_id) && $txnData ){
	
			$auth->getIdentity()->transaction_id = $txnId;
			$auth->getIdentity()->appl_id = $txnData['at_appl_id'];
			$auth->getIdentity()->appl_prefer_lang = $txnData['appl_prefer_lang'];
	
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'verification'),'default',true));
		}else{
			$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
		}
	}
	
	/*public function verificationAction(){
		
		$this->view->title = $this->view->translate("verify_bank_pin");			
			    	
    	$auth = Zend_Auth::getInstance(); 
		//$transaction_id = $auth->getIdentity()->transaction_id;
		 
		$transaction_id = $this->_getParam('id',''); 
		$this->view->transaction_id = $transaction_id;
		
		$msg = $this->_getParam('msg',null); 		
		$this->view->noticeError = $msg;

		
		$transdb = new App_Model_Application_DbTable_ApplicantTransaction();
    	$rstrans = $transdb->getAgentTransactionData($transaction_id);
		$this->view->transaction = $rstrans;
		
		if(!$rstrans){
    		$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
    	}    	
    	
		$appl_id = $rstrans["at_appl_id"];
		$this->view->appl_id = $appl_id;
    				    	
    	$admission_type = $rstrans["at_appl_type"];  //1:placement test 2:high school
    	    	
    	if($admission_type==1){
	    	if($rstrans["at_status"]=="PROCESS"){
	    		$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'viewkartu'),'default',true));
	    	}
		}else{
			if($rstrans["at_status"]=="PROCESS"){
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'viewletter'),'default',true));
			}
		}
    	
    	//echo $auth->getIdentity()->role;       	
    	      
    	$form = new Agent_Form_ApplicantLoginpin();     	
    	
    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			if ($form->isValid($formData)) {				
					
			
					$billing_no = $formData["billingno"];
					$pin_no = $formData["pinno"];
					
					$profileDB = new App_Model_Application_DbTable_ApplicantProfile();
					$applicant = $profileDB->verify($transaction_id,$billing_no,$pin_no);
					$this->view->applicant = $applicant;
					
					
					if($applicant){
						
						
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
							$error="Maaf tempat untuk USM telah penuh. Sila hubungi pihak manajemen universitas";
							$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index','id'=>$transaction_id, 'action'=>'verification','msg'=>$error),'default',true));
							exit;
						}
						
						//once submmitted update status=PTOCESS
						$upddata["at_status"]='PROCESS';
		    			$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
						$transDB->updateData($upddata,$transaction_id);	

						//generate No Pes //kene copy ni
						$ptestDB = new App_Model_Application_DbTable_ApplicantPtest();
						$ptestDB->generateNoPes($transaction_id);
						
						$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'viewkartu','id'=>$transaction_id),'default',true));		    	
					}else{
						$this->view->noticeError = $this->view->translate("invalid_bankid");
						$this->view->form = $form;
					}
			}
    	}else{
    		$this->view->form = $form;
    	}
    	
    	
	}*/
	
	public function viewkartuAction(){
		
		$this->view->title = $this->view->translate("view kartu");		
		
		//$auth = Zend_Auth::getInstance(); 
		//$transaction_id = $auth->getIdentity()->transaction_id;
		
		$transaction_id = $this->_getParam('id',''); 
		$this->view->transaction_id = $transaction_id;
		
		$transdb = new App_Model_Application_DbTable_ApplicantTransaction();
    	$rstrans = $transdb->getAgentTransactionData($transaction_id);
		$this->view->transaction = $rstrans;
		
		if(isset($rstrans) && ($rstrans["at_appl_type"]==1) && ($rstrans["at_status"]=='PROCESS')){
			
			
			$appl_id = $rstrans["at_appl_id"];
			$this->view->appl_id = $appl_id;		
			
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
				
				/*			$scheddb=new App_Model_Application_DbTable_ApplicantPlacementSchedule();
				$rssched= $scheddb->getinfo($applicant["apt_id"]);
				
				$locadb=new App_Model_Application_DbTable_PlacementTestLocation();
				$rsloca = $locadb->getdata($rssched[0]["aps_location_id"]);*/
				
				$location = $applicant["apt_id"];
				
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
		    			
				$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
		    	$document = $documentDB->getData($transaction_id,30); //kartu
		    	
		    	if(!$document){
	    		
	    		
	    		    //-------- get applicant photo --------
	    		   /* $photo_name='';
	    		    $photoDB = new App_Model_Application_DbTable_UploadFile();
	    		    $photo = $photoDB->getFile($transaction_id,33); //PHoto
	    		    
	    		    if($photo["auf_file_name"]){
	    		   	 	$photo_name =  $photo["auf_file_name"]; 
	    		    }*/
		    		
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
					/*$fieldValues = array (
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
					);*/
	
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
					
				
					// ------ create kartu peserta ujian in PDF	----					    	
			    	$filepath = DOCUMENT_PATH."/template/kartu.docx";   
	
			    	//create directory to locate file
					//$output_directory_path = DOCUMENT_PATH ."/".$transaction_id;
					
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
									
					
					//filename
					$output_filename = $applicantID."_kartu.pdf";
										
			    	//$this->mailmerge($filepath, $fieldValues, $output_directory_path, $output_filename,$photo_name);
				
				
				
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
			    	
			    	
			    	//save file info
					$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
					$doc["ad_appl_id"]=$transaction_id;
					$doc["ad_type"]=30; //kartu
					$doc["ad_filepath"]=$location_path;
					$doc["ad_filename"]=$output_filename;
					$doc["ad_createddt"]=date("Y-m-d");
					$documentDB->addData($doc);	
									
					
				
					$this->view->download_file = "http://".APP_HOSTNAME."/documents/applicant/".date("mY")."/".$transaction_id."/".$output_filename;
					
	    		}else{
							    		
	   			    $this->view->download_file = "http://".APP_HOSTNAME."/documents/".$document["ad_filepath"]."/".$document["ad_filename"];
				   
				}	
	
				
				
				
				//----------SURAT undangan orang tua section------
					$suratDB = new App_Model_Application_DbTable_ApplicantDocument();
		    		$surat = $suratDB->getData($transaction_id,42); //kartu
		    	 	    	    
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
				
			
		}else{
			$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
		}
				
		
    }
    
	
	public function viewletterAction(){
		
		$this->view->title = $this->view->translate("confirmation_letter");
		
		$transaction_id = $this->_getParam('id','');
		$this->view->transaction_id = $transaction_id;
		
		$transdb = new App_Model_Application_DbTable_ApplicantTransaction();
    	$rstrans = $transdb->getAgentTransactionData($transaction_id);
		$this->view->transaction = $rstrans;	

	   
		if(isset($rstrans) && ($rstrans["at_appl_type"]==2) && ($rstrans["at_status"]=='PROCESS')){
			
	    		$this->view->noticeMessage = $this->view->translate("message_application_acceptance");	
	    		
	    		$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
	    		$document = $documentDB->getData($transaction_id,31); //kartu/confirmation letter
	    			    	
	    		$this->view->download_file = "http://".APP_HOSTNAME."/documents/".$document['ad_filepath']."/".$document['ad_filename'];
			
		}else{
				$this->_redirect($this->view->url(array('module'=>'agent','controller'=>'index', 'action'=>'applicant-list'),'default',true));
		}
    }
    
    
	public function viewAction(){
		
		$this->view->title = $this->view->translate("application_info");		
		
		//$auth = Zend_Auth::getInstance(); 
		//$transaction_id = $auth->getIdentity()->transaction_id; 
		//$this->view->transaction_id = $transaction_id;
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
	
	public function ajaxGetPeriodAction(){
    	$intake_id = $this->_getParam('intake_id', 0);
    		
     	//if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        //}
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	$db = Zend_Db_Table::getDefaultAdapter();
	  	$select = $db->select()
	                 ->from(array('ap'=>'tbl_academic_period'),array('ap_id','ap_desc'))
	                 ->order('ap.ap_year')
	                 ->order('ap.ap_number');
	    
	    if($intake_id!=0){
	    	$select->where('ap.ap_intake_id = ?', $intake_id);
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
	
	
	
	
 	
}

?>