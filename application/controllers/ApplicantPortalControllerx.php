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

    	
    	
    	 //transaction data
        $transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
      
    	$transaction = $transactionDb->getApplicantTransaction($appl_id);
    	if (!$transaction){    		
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'new-application'),'default',true));
    	}
    	    	
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
		
		$i=0;
		$txnProgram = array();
		
		foreach ($paginator as $txn){
			$txnProgram[$i] = $applicantProgramDb->getApplicantProgramByID($txn['at_trans_id']);
			
			$i++;
		}
			
		$this->view->txnProgram = $txnProgram;
		
    }
    
    public function newApplicationAction(){
    	//create new transaction record
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id; 
    	
		//insert applicant id into applicant_transaction
        $data_trans = array();
		$data_trans["at_appl_id"] = $appl_id;				
		$data_trans["at_status"]="APPLY";
		$data_trans["at_create_by"] = $appl_id;
		$data_trans["at_create_date"]=date("Y-m-d H:i:s");				
						
		$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
		$txnId = $transDB->addData($data_trans);
						
		$auth->getIdentity()->transaction_id = $txnId;
		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'biodata'),'default',true));
    }
    
    public function continueApplicationAction(){
    	$txnId = $this->_getParam('id', 0);
    	
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id; 
    	
    	$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
    	
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
    	
    	$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
    	$txnData = $transactionDb->getTransactionData($txnId);
    
    	if($txnId!=0 && $transactionDb->checkValidApplicant($txnId, $appl_id)){
    		
    		$applicantDocumentDb = new App_Model_Application_DbTable_ApplicantDocument();
    		
    		$docData = $applicantDocumentDb->getData($txnId,$typeId);
    		
    		
	    		if($docData){
	    			return DOCUMENT_PATH.DIRECTORY_SEPARATOR.$docData['ad_filepath'].DIRECTORY_SEPARATOR.$docData['ad_filename'];
	    		}else{
	    			
	    					//CASE regenerate
	    					if($typeId==31){
					    		//Add Function Here
					    	}else
					    	if($typeId==32){
					    		//Add Function Here
					    	}else
					    	if($typeId==45){				    	
					    			if($txnData["at_appl_type"]==1){
						    			$this->generateUsmOfferLetterPDF($txnId);
						    		}elseif($txnData["at_appl_type"]==2){
						    			$this->generateOfferLetterPDF($txnId);
						    		}
					    	}else
					    	if($typeId==30){
					    		//Add Function Here
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

    		if ($form->isValid($formData)) {
				
    			$info["appl_fname"]=strtoupper($formData["appl_fname"]);
				$info["appl_mname"]=strtoupper($formData["appl_mname"]);
				$info["appl_lname"]=strtoupper($formData["appl_lname"]);
				$info["appl_email"]=$formData["appl_email"];			
				$info["appl_dob"]=$formData["appl_dob"]['day']."-".$formData["appl_dob"]['month']."-".$formData["appl_dob"]['year'];
				$info["appl_birth_place"]=$formData["appl_birth_place"];
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
	
				
				$this->view->noticeSuccess = $this->view->translate("Data Successfuly Saved");
    			
				$form->populate($formData);
    			$this->view->form = $form;
    			
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
				
			
    			
    		if($applicant['appl_admission_type']==null){
    			$applicant['appl_admission_type'] = '2';
    		}

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
    		
    	$form = new App_Form_PlacementTest(array('aphplacementcode'=>$program["aph_placement_code"],'aphfeesprogram'=>$program["aph_fees_program"],
    	'aphfeeslocation'=>$program["aph_fees_location"],'transactionid'=>$transaction_id ,'applid'=>$appl_id));
    	
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

				if($applicant_placement_test_info["apt_id"]){
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
		
				    					
						$output_directory_path_surat = DOCUMENT_PATH."/".$surat["ad_filepath"];
												
						//filename
						$output_filename_surat = $surat["ad_filename"];
						
						//print_r($fieldValuesSurat);
						//echo "$filepath_surat, $fieldValuesSurat, $output_directory_path_surat, $output_filename_surat)";					
				    	$this->mailmerge($filepath_surat, $fieldValuesSurat, $output_directory_path_surat, $output_filename_surat);


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

   
	private function generateOfferLetterPDF($txnId){
	
		//get applicant info
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $applicantDB->getAllProfile($txnId);
    	
    	//get transaction info
    	$applicantTxnDB = new App_Model_Application_DbTable_ApplicantTransaction();
		$txnData = $applicantTxnDB->getTransaction($txnId);
		
		//get assessment data
		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessment();
		$assessmentData = $assessmentDb->getData($txnId);
				
		
		//getapplicantprogram
		$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();
		$program = $appProgramDB->getProgramFaculty($txnId);
    			
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
			$biaya =$programData['Estimate_Fee_R1']!=null?number_format($programData['Estimate_Fee_R1'], 2, '.', ','):""; 
		}else
		if($assessmentData['aar_rating_rector']==2){
			$rank = "2 (Dua)"; 
			$biaya =$programData['Estimate_Fee_R2']!=null?number_format($programData['Estimate_Fee_R2'], 2, '.', ','):"";
		}else
		if($assessmentData['aar_rating_rector']==3){
			$rank = "3 (Tiga)"; 
			$biaya =$programData['Estimate_Fee_R3']!=null?number_format($programData['Estimate_Fee_R3'], 2, '.', ','):"";
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
		$feeStructureDb = new App_Model_Finance_DbTable_FeeStructure();
		$feeStructureData = $feeStructureDb->getApplicantFeeStructure('',$program[0]["program_id"]);
		
		//fee structure plan
		$feeStructurePlanDb = new App_Model_Finance_DbTable_FeeStructurePlan();
		$paymentPlanData = $feeStructurePlanDb->getStructureData($feeStructureData['fs_id']);
		$feeStructureData['payment_plan'] = $paymentPlanData;
		
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
				'$[LEARNING_DURATION]' => $learning_duration,
				'$[ESTIMASI_BIAYA]' => $biaya,
				'$[RECTOR_DATE]' => date('j M Y',strtotime($assessmentData['asd_decree_date']))
		);
		
		require_once 'dompdf_config.inc.php';
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		$html_template_path = DOCUMENT_PATH."/template/OfferLetter.html";
		
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
		$dompdf->render();
		
		
		//$location_path
		$location_path = "applicant/".date("mY")."/".$txnId;
		
	
		//untuk stream ke student
		$dompdf->stream($txnData["at_pes_id"]."_offer_letter.pdf");
			
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
		$proformaInvoiceDb = new Application_Model_DbTable_ProformaInvoice();
		$proformaInvoiceDb->regenerateProformaInvoice($txnId);
				
		//return DOCUMENT_PATH.DIRECTORY_SEPARATOR.$location_path.DIRECTORY_SEPARATOR.$output_filename;
		
	}
	
	private function generateOfferLetterPDF_x($txnId){
		
		$offerleter = new icampus_Function_Application_Offerletter();
		
		return $offerleter->generateOfferLetter($txnId);
	}
	
	private function generateUsmOfferLetterPDF($txnId){
		
		$offerleter = new icampus_Function_Application_Offerletter();
		
		$offerleter->generateUsmOfferLetter($txnId);	
		
		$this->getDocumentPath($txnId, 45);
		
		
	}
	
	
	private function generateUsmOfferLetterPDF_xi($txnId){
		
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
				'$ADDRESS1]'=>$applicant["appl_address1"],
				'$ADDRESS2]'=>$applicant["appl_address2"],
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
		$dompdf->stream($txnData["at_pes_id"]."_offer_letter.pdf");
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
						)
						)
						->where('im.appl_id = ?', $appl_id);
						
		$select_payment = $db->select()
						->from(
							array('pm'=>'payment_main'),array(
															'record_date'=>'pm.payment_date',
															'description' => 'pm.payment_description',
															'txn_type' => new Zend_Db_Expr ('"Payment"'),
															'debit' =>new Zend_Db_Expr ('"0.00"'),
															'credit' => 'amount',
															'document' => 'pbrd.bancs_journal_number',
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
															'document' => new Zend_Db_Expr ('"null"')
														)
						)
						->where('cn.appl_id = ?', $appl_id)
						->where('cn.cn_approver is not null')
						->where('cn.cn_approve_date is not null');

		//refund
		$select_refund = $db->select()
						->from(
							array('rfd'=>'refund'),array(
															'record_date'=>'rfd.rfd_approve_date',
															'description' => 'rfd.rfd_desc',
															'txn_type' => new Zend_Db_Expr ('"Refund"'),
															'debit' => 'rfd.rfd_amount',
															'credit' => new Zend_Db_Expr ('"0.00"'),
															'document' => new Zend_Db_Expr ('"null"')
														)
						)
						->where('rfd.rfd_appl_id  = ?', $appl_id)
						->where('rfd.rfd_approver_id is not null')
						->where('rfd.rfd_approve_date is not null');				
						
		$select = $db->select()
				    ->union(array($select_invoice, $select_payment, $select_creditnote, $select_refund),  Zend_Db_Select::SQL_UNION_ALL)
				    ->order("record_date");
		
   		//echo $select;
		//exit;				
		$row = $db->fetchAll($select);
		
		if(!$row){
			$row = null;
		}
		
		$this->view->account = $row;
	}
	
	public function accountInvoiceAction(){
		if( $this->getRequest()->isXmlHttpRequest() ){
			$this->_helper->layout->disableLayout();
		}
		
		$appl_id = $this->_getParam('id', null);
		$this->view->appl_id = $appl_id;
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
						->from(array('im'=>'invoice_main'),array(
							'record_date'=>'im.date_create',
							'description' => 'im.bill_description',
							'txn_type' => new Zend_Db_Expr ('"Invoice"'),
							'debit' =>'bill_amount',
							'credit' => new Zend_Db_Expr ('"0.00"'),
							'document' => 'bill_number',
							)
						)
						->where('im.appl_id = ?', $appl_id);
						
		$row = $db->fetchAll($select);
		
		if(!$row){
			$row = null;
		}
		
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
}

