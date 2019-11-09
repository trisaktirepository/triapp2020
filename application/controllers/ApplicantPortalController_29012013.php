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
    	    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	
        $this->view->title = $this->view->translate("Home");
        
        //transaction data
        $transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
        
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
    	
    	if($txnId!=0 && $transactionDb->checkValidApplicant($txnId, $appl_id)){
    		
    		$applicantDocumentDb = new App_Model_Application_DbTable_ApplicantDocument();
    		
    		$docData = $applicantDocumentDb->getData($txnId,$typeId);
    		
    		return DOCUMENT_PATH.DIRECTORY_SEPARATOR.$docData['ad_filepath'].DIRECTORY_SEPARATOR.$docData['ad_filename'];
    		
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
		          'onClick'=>"window.location ='" . $this->view->url(array('module'=>'default', 'controller'=>'applicant-portal','action'=>'index','value'=>$this->_getParam('from', 0)),'default',true) . "'; return false;"
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
		return $status;
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
		    	mkdir($output_directory_path, 0775);
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
}

