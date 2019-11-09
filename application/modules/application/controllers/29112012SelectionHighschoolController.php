<?php
class Application_29112012SelectionHighschoolController extends Zend_Controller_Action
{

	public function indexAction(){
	
		$this->view->title = $this->view->translate("application_summary");
				
		//get academic year
		$academicDB = new App_Model_Record_DbTable_AcademicYear();
		$academic_year = $academicDB->getData();		
    	$this->view->academic_year = $academic_year;
    	
    	//get academic period
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period = $periodDB->getData();
    	$this->view->period = $period;
    	
    	//get program
    	$programDB = new App_Model_Record_DbTable_Program();
    	$program = $programDB->getData();
    	$this->view->program = $program;
    	
    	if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$this->view->ac_year = $formData["academic_year"];
			
			$condition=array("IdProgram"=>$formData["programme"]);
			$program_data = $programDB->searchProgram($condition);		
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($program_data));
			$paginator->setItemCountPerPage(100);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
    	}else{
    		
    		$program_data = $programDB->searchPaginateProgram();	
    		
    		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($program_data));
			$paginator->setItemCountPerPage(100);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
    	}
    	
			$this->view->paginator = $paginator;
	} 
	
	
	public function listApplicantAction(){
		
		$this->view->title = $this->view->translate("applicant_list");
		
		$faculty_id = $this->_getParam('faculty_id', 0);
    	$this->view->faculty_id = $faculty_id;
    	
		$program_code = $this->_getParam('program_code', 0);
    	$this->view->program_code = $program_code;
    	
    	$academic_year = $this->_getParam('academic_year', 0);
    	$this->view->ayear = $academic_year;
    	
    	$period = $this->_getParam('period', 0);
    	$this->view->period = $period;
    	
    	//get academic year
		$academicDB = new App_Model_Record_DbTable_AcademicYear();
		$academic_year = $academicDB->getData();		
    	$this->view->academic_year = $academic_year;
    	
    	//get academic period
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$periode = $periodDB->getData();
    	$this->view->periode = $periode;
    	
    	
    	//get program (search form)
    	$programDB = new App_Model_Record_DbTable_Program();
    	$condition["IdCollege"]=7; //for noe set default in future patut login based on user/dean tagged to faculty
    	$program = $programDB->searchProgram($condition);
    	$this->view->program = $program;
    	
		
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			$condition=array('admission_type'=>2,"academic_year"=>$formData["academic_year"],"program_code"=>$formData["programme"],'status'=>'PROCESS');
		
			$applicant_data = $applicantDB->getDatabyProgram($condition);		
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($applicant_data));
			$paginator->setItemCountPerPage(100);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
		}else{
			
			$condition=array('admission_type'=>2,'program_code'=>$program_code,'status'=>'PROCESS');
			$applicant_data = $applicantDB->getPaginateDatabyProgram($condition);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($applicant_data));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
		}
		
		$this->view->paginator = $paginator;
	}
	
	
	public function biodataAction(){
		
		$this->view->title = $this->view->translate("biodata");
		
		$transaction_id = $this->_getParam('id', 0);
    	$this->view->transaction_id = $transaction_id;
		
    	    //get applicant info
			$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
    		$applicant = $applicantDB->getAllProfile($transaction_id);
    		$this->view->applicant= $applicant;
    		
    		//get applicant parents info
    		$familyDB =  new App_Model_Application_DbTable_ApplicantFamily();
    		$father = $familyDB->fetchdata($applicant["appl_id"],20); //father's
    		$mother = $familyDB->fetchdata($applicant["appl_id"],21); //mother's
    		$this->view->father= $father;
    		$this->view->mother= $mother;
    		
    		
    		//get applicant program
    		$applicantProgram = new App_Model_Application_DbTable_ApplicantProgram();
    		$program_list = $applicantProgram->getPlacementProgram($transaction_id);
    		
    		$program['program_name1']='';
    		$program['program_name2']='';
    		
    		$i=1;
    		foreach ($program_list as $p){	    			 
    			$program['program_name'.$i]=$p["program_name"];
    		$i++;
    		}
    		$this->view->program = $program;
    		
    		
    		//get applicant Document/Form
    		$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
    		$directory = "http://".APP_HOSTNAME."/documents/".$transaction_id."/";
    		
    		$kartu = $documentDB->getData($transaction_id,30); //kartu
    		$this->view->kartu=$directory.$kartu["ad_filename"];
    		
    		$confirmation = $documentDB->getData($transaction_id,32); //validasi bank/hs output
    		$this->view->confirmation=$directory.$confirmation["ad_filename"];
    		
    		$undangan = $documentDB->getData($transaction_id,42); //surat undangan 
    		$this->view->undangan=$directory.$undangan["ad_filename"];
	}
	
	public function contactinfoAction(){
		
		$this->view->title = $this->view->translate("contact_info");
	}
	
	public function programmeAction(){
		
		$this->view->title = $this->view->translate("programme");
	}
	
	public function documentAction(){
		
		$this->view->title = $this->view->translate("document");
		
		$program_code = $this->_getParam('program_code', 0);
    	$this->view->program_code = $program_code;
		
		$transaction_id = $this->_getParam('id', 0);
    	$this->view->transaction_id = $transaction_id;
		
		//get applicant Document/Form
    	$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
    	$directory = "http://".APP_HOSTNAME."/documents/".$transaction_id."/";    		
    		    		
    	$confirmation = $documentDB->getData($transaction_id,32); //validasi bank/hs output
    	$this->view->download_file=$directory.$confirmation["ad_filename"];
    		
	}
	
	public function processingAction(){
		
		$this->view->title = $this->view->translate("processing");
		
		$program_code = $this->_getParam('program_code', 0);
    	$this->view->program_code = $program_code;
    	
    	$academic_year = $this->_getParam('academic_year', 0);
    	$this->view->ayear = $academic_year;
		
		$transaction_id = $this->_getParam('id', 0);
    	$this->view->transaction_id = $transaction_id;
		
		
    	//get applicant program info
    	$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
    	$condition=array('admission_type'=>2,'program_code'=>$program_code,'transaction_id'=>$transaction_id);
		$applicant = $applicantDB->getDatabyProgram($condition);
    	$this->view->applicant= $applicant;
    	
    	//get education average mark
		$educationDB = new App_Model_Application_DbTable_ApplicantEducation();
		$everage_mark = $educationDB->getAverageMark($applicant["appl_id"]);
		$this->view->everage_mark= $everage_mark;   	
    	
    	//get transaction info
    	$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
    	$transaction_info = $transDB->getTransactionData($transaction_id);
    	$this->view->transaction_status = $transaction_info["at_status"];
    	
    	if($transaction_info["at_status"]=="OFFER" || $transaction_info["at_status"]=="REJECT" || $transaction_info["at_status"]=="INCOMPLETE"){
    		$this->view->noticeMessage=$this->view->translate("application_has_been_processed");
    		
    		$ratingDB = new App_Model_Application_DbTable_ApplicantAssessment();
    		$data= $ratingDB->getData($transaction_id);
    		$this->view->rating = $data;
    		
    		$form = new Application_Form_Processing();
    		
    		if( $data["aar_rating_verified"]=="Y") $verify='1'; else  $verify='2';
    		
    		if($transaction_info["at_status"]=='OFFER') $status = 1;
			if($transaction_info["at_status"]=='REJECT') $status = 2;
			if($transaction_info["at_status"]=='INCOMPLETE') $status = 3;
    		    						
				$formData["dean_rating"] = $data["aar_rating_dean"];				
				$formData["rector_verification"]=$verify;
				$formData["rector_rating"]= $data["aar_rating_rector"];
				$formData["acceptance_rank"]= $data["aar_rating_final"];				
				$formData["application_status"] =$status;
				$formData["remarks"] = $data["aar_remarks"];	
					
			$form->populate($formData);
			$this->view->form = $form;
    	}else
    	
    	if($transaction_info["at_status"]=="PROCESS")
    	{    		
    		$form = new Application_Form_Processing();			
			$this->view->form = $form;
					
		
			if ($this->getRequest()->isPost()) {
				$formData = $this->getRequest()->getPost();
				
				//---------update transaction status----------
				$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
				
			    $status='';
				if($formData["application_status"]==1) $status = 'OFFER';
				if($formData["application_status"]==2) $status = 'REJECT';
				if($formData["application_status"]==3) $status = 'INCOMPLETE';
				
				$infoUpd["at_status"]=$status;
				$transDB->updateData($infoUpd,$transaction_id);
				
				
				//----------add rating info----------
				$ratingDB = new App_Model_Application_DbTable_ApplicantAssessment();
				$auth = Zend_Auth::getInstance(); 
    			
				
				if($formData["rector_verification"]=="1") $verify='Y'; else  $verify='N';
				
				$data["aar_trans_id"]=$transaction_id;
				$data["aar_rating_dean"]=$formData["dean_rating"];
				$data["aar_rating_verified"]=$verify;				
				$data["aar_rating_final"]=$formData["acceptance_rank"];
				$data["aar_remarks"]=$formData["remarks"];	
				$data["aar_verifyby"]=$auth->getIdentity()->id;
				$data["aar_verifydt"]=date("Y-m-d H:m:s");		

				if(isset($formData["rector_rating"])){
					$data["aar_rating_rector"]=$formData["rector_rating"];
				}
				$ratingDB->addData($data);
				
								
				//generate and send offer letter to applicant
				if($formData["application_status"]==1){ //offer
					$this->offerLetter($transaction_id);					
				}
				
				
				$this->view->noticeSuccess=$this->view->translate("process_completed");
			}//end post
    		
    	}
    	
    	
	}
	
	public function printBatchApprovalxAction(){
		
		$user = $this->_getParam('user', 0);		
		$program_code = $this->_getParam('program_code', 0);
    	
    	//get academic year
    	$academicDB = new App_Model_Record_DbTable_AcademicYear();
    	$academic_year = $this->_getParam('academic_year', 0);
    	
    	if(!$academic_year){			
			$academic_year_data = $academicDB->getCurrentAcademicYearData();
			$academic_year=	$academic_year_data["ay_id"];	
    	}
    	
    	//GET academic yaer code
    	$academic_year_code = $academicDB->getData($academic_year);
    	$academic_code=$academic_year_code["ay_code"];
    	
		
		
    	//to get list applicant    	
		$condition=array('admission_type'=>2,'program_code'=>$program_code,'academic_year'=>$academic_year,'status'=>'PROCESS');
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant_data = $applicantDB->getDatabyProgram($condition);
				
		$applicantConnections=array();
		foreach ($applicant_data as $entry){
			
			$educationDB = new App_Model_Application_DbTable_ApplicantEducation();		
			$everage_mark = $educationDB->getAverageMark($entry["appl_id"]);
		
			$applicant["date_applied"]=date('j M Y',strtotime($entry["submit_date"]));
			$applicant["applicant_id"]=$entry["applicantID"];
			$applicant["name"]=$entry["appl_fname"].' '.$entry["appl_mname"].' '.$entry["appl_lname"];
			$applicant["school"]= $entry["school"];
			$applicant["programme"]=$entry["program_name"];
			$applicant["mark"]=$everage_mark;
			$applicant["status"]=$entry["status"];
			
			 array_push($applicantConnections,$applicant);
			
		}//END FOREACH
		
		
		    // -------- define files/documents info ---------
			$date_created = date('dmY');
			
			if($user==1){
				
				//get NOMOR
				$nomor = "899/PSSB/FH/VIIII/".$academic_code;
		
				$filepath = DOCUMENT_PATH."/template/dean_approval.docx"; 
				$output_filename = "dean_approval_".$date_created.".pdf";
				
			}//END USER 1
			
			if($user==2){
				
				 //get NOMOR
				 $nomor = "041/AK402/FPSSB-BAAH/USAKTI/WR.1/".$academic_code;
				 
				 $filepath = DOCUMENT_PATH."/template/rector_approval.docx"; 
				 $output_filename = "rector_approval_".$date_created.".pdf"; 
				  
			}//END USER 2
					
    	
		
			$fieldValues = array(
					'FACULTY'=>'',
			        'NOMOR'=>$nomor,
			        'date_printed'=>date('j M Y'),
			        'PERIODE'=>'',
			        'ACADEMIC_YEAR'=>$academic_code,
					'DEAN'=>''
			);
		
		 	
			
						
			//create directory to locate file
			$output_directory_path = "/data/apps/triapp/documents/download";
			if (!is_dir($output_directory_path)) {
		    	mkdir($output_directory_path, 0775);
			}
			
			$output_directory_path = "/data/apps/triapp/documents/download/".date('mY');
			if (!is_dir($output_directory_path)) {
		    	mkdir($output_directory_path, 0775);
			}

			//to rename output file			
			$output_file_path = $output_directory_path."/".$output_filename;		   
		    
			// -------- end define files/documents info ---------
		    
				
		    // create and retrieve document
    		
			$mailMerge = new Zend_Service_LiveDocx_MailMerge();
			 
			$mailMerge->setUsername('yatie')
			          ->setPassword('al_hasib');			
			  
			$mailMerge->setLocalTemplate($filepath);
			
			$mailMerge->setFieldValues($fieldValues);
			$mailMerge->assign('connection', $applicantConnections);
			
			$mailMerge->createDocument();
			 
			$document = $mailMerge->retrieveDocument('pdf');			 
			
			file_put_contents($output_file_path, $document);
			
			
		
			$download_file = "http://".APP_HOSTNAME."/documents/download/".$output_filename;				
		    $this->view->download_file = $download_file;
		    $this->view->filename = $output_filename;
		    $this->view->output_file_path = $output_file_path;
		    			
	}
	
	
	public function offerLetter($transaction_id){
		
		
		//get applicant info
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $applicantDB->getAllProfile($transaction_id);
    	
    	//getapplicantprogram
		$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();		
		$program = $appProgramDB->getProgramFaculty($transaction_id);
		
		//get applicant parents info
    	$familyDB =  new App_Model_Application_DbTable_ApplicantFamily();
    	$father = $familyDB->fetchdata($applicant["appl_id"],20); //father's    	
    	
		
		$nomor = '010/AK.4.02/PSSB-BAA/Usakti/WR.I/I-3/2012';
    	
			$fieldValues = array(
					'applicantID'=>$applicant["applicantID"],
			        'NOMOR'=>$nomor,
			        'Title_Template'=>$this->view->translate("title_template_offer_letter"),
			        'APPLICANTNAME'=>$applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
					'PARENTNAME'=>$father["af_name"],
			        'Address1'=>$applicant["appl_address1"],
					'Address2'=>$applicant["appl_address2"],
					'City'=>$applicant["CityName"],
					'Postcode'=>$applicant["appl_postcode"],
					'State'=>$applicant["StateName"],				
			    	'semester_offer'=>'',
					'period_offer'=>'',
					'FACULTYNAME'=>$program[0]["faculty"],
					'PROGRAMME'=>$program[0]["program_name"],
			        'print_date'=>date('j M Y'),
					'payment_date_paketa'=>'',
					'payment_date_paketb'=>'',
					'payment_date_paketb_cicilan1'=>'',
					'payment_date_paketb_cicilan2'=>'',
					'payment_date_paketb_cicilan3'=>'',
					'payment_date_paketb_cicilan4'=>'',
					'payment_date_paketb_cicilan5'=>'',
					'payment_date_paketb_cicilan6'=>''			        
			);
		
		 	//template file path
			//$filepath = DOCUMENT_PATH."/template/offer_letter.docx"; 
						
			//create directory to locate file
			//$output_directory_path = "/data/apps/triapp/documents/".$transaction_id;
			//if (!is_dir($output_directory_path)) {
		    //	mkdir($output_directory_path, 0775);
			//}
									
			//output filename 
			//$output_filename = $applicant["applicantID"]."_offer_letter.pdf";

			//to rename output file			
			//$output_file_path = $output_directory_path."/".$output_filename;		   

			//no need to create here create on offer letter
			//$this->mailmergeAction($filepath,$fieldValues,$output_file_path);
			
			//save file info
			//$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
			//$doc["ad_appl_id"]=$applicant["appl_id"];
			//$doc["ad_type"]=43; //offer letter
			//$doc["ad_filepath"]=$output_directory_path;
			//$doc["ad_filename"]=$output_filename;
			//$doc["ad_createddt"]=date("Y-m-d");
			//$documentDB->addData($doc);		
		
			
			
			
			//-------------------send mail section------------------
			$fullname = $applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"];
			//$attachment_path = $output_directory_path.'/'.$output_filename;
			
			$templateDB = new App_Model_General_DbTable_EmailTemplate();
			$templateData = $templateDB->getData(5,$applicant["appl_prefer_lang"]);//offer letter
    		    				
    		$templateMail = $templateData['body'];				
			$templateMail = str_replace("[applicant_name]",$fullname,$templateMail);
			
			$emailDb = new App_Model_System_DbTable_Email();		
			$data = array(
				'recepient_email' => $applicant["appl_email"],
				'subject' => $templateData["subject"],
				'content' => $templateMail
				//'attachment_path' => $attachment_path,
			    //'attachment_filename' => $output_filename
			);	
			
			//to send email with attachment
			$emailDb->addData($data);		
		
	}
	
	public function mailmergeAction($filepath,$fieldValues,$output_file_path){
			
		    // create and retrieve document
		    
		    		
			$mailMerge = new Zend_Service_LiveDocx_MailMerge();
			 
			$mailMerge->setUsername('yatie')
			          ->setPassword('al_hasib');			
			  
			$mailMerge->setLocalTemplate($filepath);
			
			$mailMerge->setFieldValues($fieldValues);
			
			$mailMerge->createDocument();
			 
			$document = $mailMerge->retrieveDocument('pdf');			 
			
			file_put_contents($output_file_path, $document);
	}
	
	
	public function batchDeanRatingAction(){
		
		$this->view->title = $this->view->translate("Batch Dean Rating");
		
		$faculty_id = $this->_getParam('faculty_id', 0);
    	$this->view->faculty_id = $faculty_id;
    	
		$program_code = $this->_getParam('program_code', 0);
    	$this->view->program_code = $program_code;
    	
    	$academic_year = $this->_getParam('academic_year', 0);
    	$this->view->ayear = $academic_year;
    	
    	$period = $this->_getParam('period', 0);
    	$this->view->period = $period;
    	
    	
    	
    	//----------- for search -------------- 
    	
    	//get academic year
		$academicDB = new App_Model_Record_DbTable_AcademicYear();
		$academic_year = $academicDB->getData();		
    	$this->view->academic_year = $academic_year;
    	
    	//get academic period
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$periode = $periodDB->getData();
    	$this->view->periode = $periode;
    	    	
    	//get program (search form)
    	$programDB = new App_Model_Record_DbTable_Program();
    	$condition["IdCollege"]=$faculty_id; 
    	$program = $programDB->searchProgram($condition);
    	$this->view->program = $program;
    	
		
    	
    	$form = new Application_Form_SelectionSearch();
    	$this->view->form=$form;  
    	
    	
    	
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			
			
			if($formData["period"]){
			
			//get periode information
			$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
			$period_info = $periodDB->getData($formData["period"]);
			$fperiod = $period_info["ap_code"];
			
			
			$condition=array('admission_type'=>2,
							 "academic_year"=>$formData["academic_year"],
							 "period"=>$fperiod,
							 "program_code"=>$formData["programme"],
							 'status'=>'PROCESS'
							 );
		
			$applicant_data = $applicantDB->getDeanSelection($condition);		
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($applicant_data));
			$paginator->setItemCountPerPage(100);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
			
			
			$form->populate($formData);
			$this->view->form = $form;
			
			}else{
				$this->view->noticeError = "Sila pilihkan Tahun Akademic,periode dan Program Studi<br>";
			}
			
		}
		
		
		
	}
	
	
	public function saveDeanRatingAction(){
		
		
		$program_code = $this->_getParam('program_code', 0);
		$faculty_id = $this->_getParam('faculty_id', 0);
		
		$auth = Zend_Auth::getInstance(); 
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			for($i=0; $i<count($formData["dean_rating"]); $i++){				
				
				//add selection information
				$rating["aar_trans_id"]=$formData["transaction_id"][$i];
				$rating["aar_rating_dean"]=$formData["dean_rating"][$i];								
				$rating["aar_dean_rateby"]=$auth->getIdentity()->id;
				$rating["aar_dean_ratedt"]=date("Y-m-d H:m:s");	
				
				$ratingDB = new App_Model_Application_DbTable_ApplicantAssessment();
				$ratingDB->addData($rating);
				
				
				//update selection status
				$status["at_selection_status"]=1;
				
				$transactionDB = new App_Model_Application_DbTable_ApplicantTransaction();
				$transactionDB->updateData($status,$formData["transaction_id"][$i]);
								
			}//end for
			
			
			$this->view->noticeSuccess = $this->view->translate("information_saved_successfully");
			//should notify rector by email to rate or not?
			
			$this->_redirect($this->view->url(array('module'=>'application','controller'=>'selection-highschool', 'action'=>'batch-dean-rating','program_code'=>'0230','faculty_id'=>'7'),'default',true));
			
		}//end if
	}
	
	
	public function batchRectorRatingAction(){
		
		$this->view->title = $this->view->translate("Batch Rector Rating");
		
		$faculty_id = $this->_getParam('faculty_id', 0);
    	$this->view->faculty_id = $faculty_id;
    	
    	$program_code = $this->_getParam('program_code', 0);
    	$this->view->program_code = $program_code;
    	
    	$academic_year = $this->_getParam('academic_year', 0);
    	$this->view->ayear = $academic_year;
    	
    	$period = $this->_getParam('period', 0);
    	$this->view->period = $period;
    	
    	
    	//get academic year
		$academicDB = new App_Model_Record_DbTable_AcademicYear();
		$academic_year = $academicDB->getData();		
    	$this->view->academic_year = $academic_year;
    	
    	
    	//get academic period
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$periode = $periodDB->getData();
    	$this->view->periode = $periode;
    	
    	
    	
    	//get program (search form)
    	$programDB = new App_Model_Record_DbTable_Program();
    	$condition["IdCollege"]=7; //for noe set default in future patut login based on user/dean tagged to faculty
    	$program = $programDB->searchProgram($condition);
    	$this->view->program = $program;
    	
    	
    	$form = new Application_Form_SelectionSearch();
    	$this->view->form=$form;  
    	
		
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
								
			
			//get periode information
			$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
			$period_info = $periodDB->getData($formData["period"]);
			$fperiod = $period_info["ap_code"];
			
			$condition=array('admission_type'=>2,
							"academic_year"=>$formData["academic_year"],
							"period"=>$fperiod,
							"program_code"=>$formData["programme"],
							'status'=>'PROCESS',
			 				);
						
			$applicant_data = $applicantDB->getRectorSelection($condition);		
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($applicant_data));
			$paginator->setItemCountPerPage(100);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
			
			
			$form->populate($formData);
			$this->view->form = $form;
		}
		
		
	}
	
	public function printBatchDeanRatingAction(){
				
		//$this->_helper->layout->disableLayout();
		  $this->_helper->layout->setLayout('print');
		  
	    $registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
		   
		$this->view->title = $this->view->translate("batch_dean_rating");
    	
		//$form = new Application_Form_HighSchoolSelectionSearch();		
		//$this->view->form = $form;	
			
		$faculty_id = $this->_getParam('faculty_id', 0);    	
		$program_code  = $this->_getParam('program_code', 0);
		$this->view->program_code = $program_code;
		$academic_year = $this->_getParam('academic_year', 0);
		$period        = $this->_getParam('period', 0);
    	   	
		
		//get faculty name
		$facultyDB = new App_Model_General_DbTable_Collegemaster();
		$faculty = $facultyDB->getData($faculty_id);
		
		if($locale=="id_ID"){
			$this->view->faculty_name = $faculty["ArabicName"];
		}else{
			$this->view->faculty_name = $faculty["CollegeName"];
		}
		
		
		//get dean name
		$deanDB = new App_Model_General_DbTable_DeanList();
		$dean = $deanDB->getDeanByCollege($faculty_id);
		$this->view->dean_name = $dean["FullName"];
		
		//get academic year
    	$academicDB = new App_Model_Record_DbTable_AcademicYear();
    	$academic_year_data = $academicDB->getCurrentAcademicYearData();
		$academic_year_info=	$academic_year_data["ay_code"];	
    	$this->view->current_academic_year = $academic_year_info;
		
		
		//get periode
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period = $periodDB->getData($period);
    	$this->view->period = $period;
		
    	//to get list applicant    	
		$condition=array('admission_type'=>2,'program_code'=>$program_code,'academic_year'=>$academic_year,'status'=>'PROCESS','period'=>$period["ap_code"]);
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant_data = $applicantDB->getDeanSelection($condition);
				
		$this->view->applicant = $applicant_data;
	
		
		//get program name
		$programDB = new App_Model_Record_DbTable_Program();
		$program_data = $programDB->getProgrambyCode($program_code);
		$this->view->program_name = $program_data["ArabicName"];
    	
    	$this->view->nomor = "899/PSSB/FH/VIIII/2013";
    	
	}
	
	public function downloadBatchDeanRatingAction(){
		
		$faculty_id = $this->_getParam('faculty_id', 0);    	
		$program_code  = $this->_getParam('program_code', 0);	
		$academic_year = $this->_getParam('academic_year', 0);
		$period        = $this->_getParam('period', 0);
		
		//get periode
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period = $periodDB->getData($period);
    	$this->view->period = $period;
		
    	//to get list applicant    	
		$condition=array('admission_type'=>2,'program_code'=>$program_code,'academic_year'=>$academic_year,'status'=>'PROCESS','period'=>$period["ap_code"]);
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant_data = $applicantDB->getDeanSelection($condition);
		
		$this->view->applicant = $applicant_data;
		
	}
	
	public function uploadBatchDeanRatingAction(){
		
		$faculty_id = $this->_getParam('faculty_id', 0); 
		$this->view->faculty_id = 	$faculty_id;
		   	
		$program_code  = $this->_getParam('program_code', 0);
		$this->view->program_code = 	$program_code;
		
		$academic_year = $this->_getParam('academic_year', 0);
		$this->view->ayear = 	$academic_year;
		
		$period        = $this->_getParam('period', 0);
    	$this->view->period = $period;
    	
		
		$form = new Application_Form_UploadBatch();
		$this->view->form = $form;
		
		$auth = Zend_Auth::getInstance(); 
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();

				
			 if ($form->isValid ( $_POST )) {			 	
	         	    if ($form->filename->isUploaded()) {	         	    	
	         	    
						$myfile = $form->getValues ();
						$locationFile = $form->filename->getFileName();
						
						$myfile ["filename"] = date ( 'Ymd' ).'_'.$myfile ["filename"];
												
						//echo $myfile ["filename"];
						$fullPathNameFile = DOCUMENT_PATH.'/download/'.date("mY").'/' .$myfile ["filename"];
						
						// Renommage du fichier
						$filterRename = new Zend_Filter_File_Rename ( array ('target' => $fullPathNameFile, 'overwrite' => true ) );
						$filterRename->filter ( $locationFile );
						
						
							$row = 1;
							if (($handle = fopen($fullPathNameFile, "r")) !== FALSE) {
							    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
							        
							    	
							       
							    	if($row!=1){
							    		
							    		
							            //add selection information
										$rating["aar_trans_id"]=$data[1];
										$rating["aar_rating_dean"]=$data[4];								
										$rating["aar_dean_rateby"]=$auth->getIdentity()->id;
										$rating["aar_dean_ratedt"]=date("Y-m-d H:m:s");	
										
										
										$ratingDB = new App_Model_Application_DbTable_ApplicantAssessment();
										$ratingDB->addData($rating);
										
										//print_r($rating);
										//update selection status
										$status["at_selection_status"]=1;										
										$transactionDB = new App_Model_Application_DbTable_ApplicantTransaction();
										$transactionDB->updateData($status,$data[1]);
							    	}
							       
							    $row++;    
							    }
							    fclose($handle);
							    
							}//end if
						
						
						
					} else {
						$myfile ["filename"] = "";
					}
	            }
            
				
				
			
					
		}//end post

		
		
	}
	
	public function printNilaiRaportAction(){
				
		//$this->_helper->layout->disableLayout();
		  $this->_helper->layout->setLayout('print');
	
		
		$program_code  = $this->_getParam('program_code', 0);
		$academic_year = $this->_getParam('academic_year', 0);
		$period        = $this->_getParam('period', 0);
    	   	
		
		//get periode
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period = $periodDB->getData($period);
    	$this->view->period = $period;
    	
    	//to get list applicant    	
		$condition=array('admission_type'=>2,'program_code'=>$program_code,'academic_year'=>$academic_year,'status'=>'PROCESS','period'=>$period["ap_code"]);
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant_data = $applicantDB->getDeanSelection($condition);				
		$this->view->applicant = $applicant_data;	
		
		//get program name
		$programDB = new App_Model_Record_DbTable_Program();
		$program_data = $programDB->getProgrambyCode($program_code);
		$this->view->program_name = $program_data["ArabicName"];
		$this->view->program_code = $program_code;
    
    	
	}
	
	
	public function printBatchRectorRatingAction(){
				
		//$this->_helper->layout->disableLayout();
		 $this->_helper->layout->setLayout('print');
			
		$program_code  = $this->_getParam('program_code', 0);
		$academic_year = $this->_getParam('academic_year', 0);
		$period        = $this->_getParam('period', 0);
    	   	
		//get periode
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period = $periodDB->getData($period);
    	$this->view->period = $period;
    	
    	//get faculty by program code    	
    	$programDB = new App_Model_Record_DbTable_Program();
    	$condition["ProgramCode"]=$program_code; 
    	$program = $programDB->searchProgram($condition);
    	$this->view->faculty_name = $program["faculty"];
		
    	//to get list applicant    	
		$condition=array('admission_type'=>2,'program_code'=>$program_code,'academic_year'=>$academic_year,'status'=>'PROCESS','period'=>$period["ap_code"]);
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant_data = $applicantDB->getDatabyProgram($condition);
				
		$this->view->applicant = $applicant_data;
	
		//get academic year
    	$academicDB = new App_Model_Record_DbTable_AcademicYear();
    	$academic_year_data = $academicDB->getCurrentAcademicYearData();
		$academic_year=	$academic_year_data["ay_code"];	
    	$this->view->current_academic_year = $academic_year;
    	
    	$this->view->nomor = "041/AK402/PSSB-BAA/USAKTI/WR.1/2013";
    	
	}
	
	
	public function printRectorNilaiRaportAction(){
				
		//$this->_helper->layout->disableLayout();
		  $this->_helper->layout->setLayout('print');
	
		
		$program_code  = $this->_getParam('program_code', 0);
		$academic_year = $this->_getParam('academic_year', 0);
		$period        = $this->_getParam('period', 0);
    	   	
		
		//get periode
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period = $periodDB->getData($period);
    	$this->view->period = $period;
    	
    	//to get list applicant    	
		$condition=array('admission_type'=>2,'program_code'=>$program_code,'academic_year'=>$academic_year,'status'=>'PROCESS','period'=>$period["ap_code"]);
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant_data = $applicantDB->getRectorSelection($condition);				
		$this->view->applicant = $applicant_data;	
		
		//get program name
		$programDB = new App_Model_Record_DbTable_Program();
		$program_data = $programDB->getProgrambyCode($program_code);
		$this->view->program_name = $program_data["ArabicName"];
		$this->view->program_code = $program_code;
    
    	
	}
	
	
	public function downloadBatchRectorRatingAction(){
		
		$faculty_id = $this->_getParam('faculty_id', 0); 
		   	
		$program_code  = $this->_getParam('program_code', 0);
		$this->view->program_code = 	$program_code;
		
		$academic_year = $this->_getParam('academic_year', 0);
		
		$period  = $this->_getParam('period', 0);
		
		//get periode
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period_data = $periodDB->getData($period);
    	$this->view->period = $period_data["ap_code"];
    	
    	//to get list applicant    	
		$condition=array('admission_type'=>2,'program_code'=>$program_code,'academic_year'=>$academic_year,'status'=>'PROCESS','period'=>$period_data["ap_code"]);
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant_data = $applicantDB->getRectorSelection($condition);
		
		$this->view->applicant = $applicant_data;
		
	}
	
	
	public function uploadBatchRectorRatingAction(){
		
		$this->view->title = $this->view->translate("upload_batch_rector_rating");
				
		$faculty_id = $this->_getParam('faculty_id', 0);
		$this->view->faculty_id = 	$faculty_id; 
		   	
		$program_code  = $this->_getParam('program_code', 0);
		$this->view->program_code = 	$program_code;
		
		$academic_year = $this->_getParam('academic_year', 0);
		$this->view->academic_year = 	$academic_year;
		
		$period  = $this->_getParam('period', 0);
		$this->view->period = 	$period;
		
		$form = new Application_Form_UploadBatch();
		$this->view->form = $form;
		
		$auth = Zend_Auth::getInstance(); 
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();

				
			 if ($form->isValid ( $_POST )) {			 	
	         	    if ($form->filename->isUploaded()) {	         	    	
	         	    
						$myfile = $form->getValues ();
						$locationFile = $form->filename->getFileName();
						
						$myfile ["filename"] = date ( 'Ymd' ).'_'.$myfile ["filename"];
												
						//echo $myfile ["filename"];
						$fullPathNameFile = DOCUMENT_PATH.'/download/'.date("mY").'/' .$myfile ["filename"];
						
						// Renommage du fichier
						$filterRename = new Zend_Filter_File_Rename ( array ('target' => $fullPathNameFile, 'overwrite' => true ) );
						$filterRename->filter ( $locationFile );
						
						
							$row = 1;
							if (($handle = fopen($fullPathNameFile, "r")) !== FALSE) {
							    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
							        
							    	
							       
							    	if($row!=1){
							            //add selection information
							            
							    		if($data[5]=="Y"){
											//accept dean rate			
											$rating["aar_rating_verified"]='Y';
											$final_rate = $data[4]; 
													
										}else {
											//reject dean rate	
											$rating["aar_rating_verified"]='N';  
											$rating["aar_rating_rector"]=$data[6]; 
											$final_rate = $data[6]; 
										}
														
										
										$rating["aar_rating_final"]=$final_rate;								
										$rating["aar_rector_rateby"]=$auth->getIdentity()->id;
										$rating["aar_rector_ratedt"]=date("Y-m-d H:m:s");	
										
										$ratingDB = new App_Model_Application_DbTable_ApplicantAssessment();
										$ratingDB->updateAssessmentData($rating,$data[1]);
										
										//print_r($rating);
										
										//update selection status
										$status["at_selection_status"]=2;				
										$transactionDB = new App_Model_Application_DbTable_ApplicantTransaction();
										$transactionDB->updateData($status,$data[1]);
							    	}
							       
							    $row++;    
							    }
							    fclose($handle);
							    
							}//end if
						
							$this->view->noticeSuccess=$this->view->translate("document_has_been_successfully_uploaded");
						
					} else {
						$myfile ["filename"] = "";
					}
	            }
            
				
				
			
					
		}//end post

		
		
	}
	
	
	public function saveRectorRatingAction(){
		
		
		$auth = Zend_Auth::getInstance(); 
		
		$program_code  = $this->_getParam('program_code', 0);
		$faculty_id = $this->_getParam('faculty_id', 0);
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			
			
			for($i=0; $i<count($formData["transaction_id"]); $i++){	
				
					
				if($formData["rector_verification"][$i]=="1"){
					//accept dean rate			
					$rating["aar_rating_verified"]='Y';
					$final_rate = $formData["dean_rating"][$i]; 
							
				}else {
					//reject dean rate	
					$rating["aar_rating_verified"]='N';  
					$rating["aar_rating_rector"]=$formData["rector_rating"][$i];
					$final_rate = $formData["rector_rating"][$i]; 
				}
				
								
				$rating["aar_rating_final"] = $final_rate;
				$rating["aar_dean_rateby"]=$auth->getIdentity()->id;
				$rating["aar_dean_ratedt"]=date("Y-m-d H:m:s");	
			
				
				//update rector rating
				$ratingDB = new App_Model_Application_DbTable_ApplicantAssessment();
				$ratingDB->updateAssessmentData($rating,$formData["transaction_id"][$i]);
				
				
				//update selection status
				$status["at_selection_status"]=2;				
				$transactionDB = new App_Model_Application_DbTable_ApplicantTransaction();
				$transactionDB->updateData($status,$formData["transaction_id"][$i]);
								
			}//end for
			
			$this->view->noticeSuccess = $this->view->translate("saved_successfully");
			//should notify rector by email to rate or not?
			
			$this->_redirect($this->view->url(array('module'=>'application','controller'=>'selection-highschool', 'action'=>'batch-rector-rating','program_code'=>'0230','faculty_id'=>'7'),'default',true));
			
		}//end if
	}
	
	
	public function batchApprovalAction(){
		
		$this->view->title = $this->view->translate("Batch Approval");
		
		$faculty_id = $this->_getParam('faculty_id', 0);  
		$this->view->faculty_id = $faculty_id;  
		
		$program_code = $this->_getParam('program_code', 0);
    	$this->view->program_code = $program_code;
    	
    	$academic_year = $this->_getParam('academic_year', 0);
    	$this->view->ayear = $academic_year;
    	
    	$period = $this->_getParam('period', 0);
    	$this->view->period = $period;
    	
    	
    	//get academic year
		$academicDB = new App_Model_Record_DbTable_AcademicYear();
		$academic_year = $academicDB->getData();		
    	$this->view->academic_year = $academic_year;
    	
    	
    	//get academic period
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$periode = $periodDB->getData();
    	$this->view->periode = $periode;
    	
    	
    	//get program (search form)
    	$programDB = new App_Model_Record_DbTable_Program();
    	$condition["IdCollege"]=$faculty_id;
    	$program = $programDB->searchProgram($condition);
    	$this->view->program = $program;
    	
		$form = new Application_Form_SelectionSearch();
    	$this->view->form=$form;  
    	
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			//get periode information
			$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
			$period_info = $periodDB->getData($formData["period"]);
			$fperiod = $period_info["ap_code"];
			
			$condition=array('admission_type'=>2,
							"academic_year"=>$formData["academic_year"],
							"period"=>$fperiod,
							"program_code"=>$formData["programme"],
							'status'=>'PROCESS',
			 				);
						
			$applicant_data = $applicantDB->getApprovalSelection($condition);
					
			/*
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($applicant_data));
			$paginator->setItemCountPerPage(100);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));*/
			
			$this->view->paginator = $applicant_data;
			
			$form->populate($formData);
			$this->view->form = $form;
		}
	}
	
	
	public function saveApprovalAction(){
		
		
		$auth = Zend_Auth::getInstance(); 
		
		$program_code  = $this->_getParam('program_code', 0);
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
					
			
			for($i=0; $i<count($formData["transaction_id"]); $i++){	
				
					
				$rating["aar_approvalby"]=$auth->getIdentity()->id;
				$rating["aar_approvaldt"]=date("Y-m-d H:m:s");				
				
				//update rector rating
				$ratingDB = new App_Model_Application_DbTable_ApplicantAssessment();
				$ratingDB->updateAssessmentData($rating,$formData["transaction_id"][$i]);
				
				
				//update selection status
				$status["at_status"]=$formData["approval"][$i];
				$status["at_selection_status"]=3;	//completed			
				$transactionDB = new App_Model_Application_DbTable_ApplicantTransaction();
				$transactionDB->updateData($status,$formData["transaction_id"][$i]);

				
			}//end for
			
			$this->view->noticeSuccess = $this->view->translate("saved_successfully");
			//should notify rector by email to rate or not?
			
			$this->_redirect($this->view->url(array('module'=>'application','controller'=>'selection-highschool', 'action'=>'batch-approval','program_code'=>'0230','faculty_id'=>'7'),'default',true));
			
		}//end if
	}
	
	
	public function printBatchApprovalAction(){
				
		//$this->_helper->layout->disableLayout();
		$this->_helper->layout->setLayout('print');
			
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
    	    	
		$faculty_id = $this->_getParam('faculty_id', 0);  
		$this->view->faculty_id = $faculty_id;  
		
		$program_code = $this->_getParam('program_code', 0);
    	$this->view->program_code = $program_code;
    	
    	$academic_year = $this->_getParam('academic_year', 0);
    	$this->view->ayear = $academic_year;
    	
    	$period = $this->_getParam('period', 0);
    	$this->view->period = $period;
    	
    	//get periode
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period_info = $periodDB->getData($period);
    	$fperiod = $period_info["ap_code"];
    	$this->view->period_name = $period_info["ap_desc"];
    	
    	$condition2=array('admission_type'=>2,
							"academic_year"=>$academic_year,
							"period"=>$fperiod,
							"program_code"=>$program_code,
							'status'=>'PROCESS',
			 				);
    	
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant_data = $applicantDB->getApprovalSelection($condition2);	
		$this->view->applicant = $applicant_data;
		
		//get faculty name
		$facultyDB = new App_Model_General_DbTable_Collegemaster();
		$faculty = $facultyDB->getData($faculty_id);
		
		if($locale=="id_ID"){
			$this->view->faculty_name = $faculty["ArabicName"];
		}else{
			$this->view->faculty_name = $faculty["CollegeName"];
		}
		
		//get academic year
    	$academicDB = new App_Model_Record_DbTable_AcademicYear();
    	$academic_year_data = $academicDB->getCurrentAcademicYearData();
		$academic_year_info=	$academic_year_data["ay_code"];	
    	$this->view->current_academic_year = $academic_year_info;
		
	}
	
	
	public function downloadBatchApprovalAction(){
		
		$faculty_id = $this->_getParam('faculty_id', 0); 
		$this->view->faculty_id = 	$faculty_id;
		   	
		$program_code  = $this->_getParam('program_code', 0);
		$this->view->program_code = 	$program_code;
		
		$academic_year = $this->_getParam('academic_year', 0);
		$this->view->ayear = 	$academic_year;
		
		$period  = $this->_getParam('period', 0);
		
		//get periode
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period_data = $periodDB->getData($period);
    	$this->view->period = $period_data["ap_code"];
    	
    	//to get list applicant    	
		$condition=array('admission_type'=>2,'program_code'=>$program_code,'academic_year'=>$academic_year,'status'=>'PROCESS','period'=>$period_data["ap_code"]);
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant_data = $applicantDB->getApprovalSelection($condition);
		
		$this->view->applicant = $applicant_data;
		
	}
	
	
	public function uploadBatchApprovalAction(){
		
		$this->view->title = $this->view->translate("upload_batch_approval");
		
		$faculty_id = $this->_getParam('faculty_id', 0); 
		$this->view->faculty_id = 	$faculty_id;
		   	
		$program_code  = $this->_getParam('program_code', 0);
		$this->view->program_code = 	$program_code;
		
		$academic_year = $this->_getParam('academic_year', 0);
		$this->view->ayear = 	$academic_year;
		
		$period        = $this->_getParam('period', 0);
    	$this->view->period = $period;
    	
		
		$form = new Application_Form_UploadBatch(array ('programcode' => $program_code, 'academicyear' => $academic_year ) );
		$this->view->form = $form;
		
		$auth = Zend_Auth::getInstance(); 
		
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();		
                       
            $programcode  = $formData["programcode"];
            $academicyear = $formData["academicyear"];

			 	if ($form->isValid ( $_POST )) {
			 		
	         	    if ($form->filename->isUploaded ()) {
	         	    	
	         	    	$myfile = $form->getValues ();
						$locationFile = $form->filename->getFileName();
						
						$myfile ["filename"] = date ( 'Ymd' ).'_'.$myfile ["filename"];
												
						//echo $myfile ["filename"];
						$fullPathNameFile = DOCUMENT_PATH.'/download/'.date("mY").'/' .$myfile ["filename"];
						
						// Renommage du fichier
						$filterRename = new Zend_Filter_File_Rename ( array ('target' => $fullPathNameFile, 'overwrite' => true ) );
						$filterRename->filter ( $locationFile );
						
						
							$row = 1;
							if (($handle = fopen($fullPathNameFile, "r")) !== FALSE) {
							    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
							    									        
							    	if($row!=1){
							            //add selection information
							            
							    		$rating["aar_approvalby"]=$auth->getIdentity()->id;
										$rating["aar_approvaldt"]=date("Y-m-d H:m:s");				
										
										//update rector rating
										$ratingDB = new App_Model_Application_DbTable_ApplicantAssessment();
										$ratingDB->updateAssessmentData($rating,$data[1]);
										
										
										//update selection status
										$status["at_status"]=$data[5];
										$status["at_selection_status"]=3;	//completed			
										$transactionDB = new App_Model_Application_DbTable_ApplicantTransaction();
										$transactionDB->updateData($status,$data[1]);
										
										if($data[5]=="OFFER"){
											//$transaction_id = $data[1]; //row number 2
											$this->sendMail($data[1]);
										}//END SEND MAIL
							    		
							    	}//end if
							       
							    $row++;    
							    }//end while
							    fclose($handle);
							    
							}//end if
							
							$this->view->noticeSuccess=$this->view->translate("document_has_been_successfully_uploaded");
    		
						
					} else {
						$myfile ["filename"] = "";
					}
					
	            }//end post
									
		}//end post

		
		
	}
	
	
	public function sendMail($transaction_id){
		
		
		//get applicant info
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
    	$applicant = $applicantDB->getAllProfile($transaction_id);
    	
    	//getapplicantprogram
		$appProgramDB = new App_Model_Application_DbTable_ApplicantProgram();		
		$program = $appProgramDB->getProgramFaculty($transaction_id);
		
		//get applicant parents info
    	$familyDB =  new App_Model_Application_DbTable_ApplicantFamily();
    	$father = $familyDB->fetchdata($applicant["appl_id"],20); //father's    	
    	
			 	
			$fullname = $applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"];
			
			$templateDB = new App_Model_General_DbTable_EmailTemplate();
			$templateData = $templateDB->getData(5,$applicant["appl_prefer_lang"]);//offer letter
    		    				
    		$templateMail = $templateData['body'];				
			$templateMail = str_replace("[applicant_name]",$fullname,$templateMail);
			$templateMail = str_replace("[program]",$program[0]["program_name"],$templateMail);
			$templateMail = str_replace("[faculty]",$program[0]["faculty"],$templateMail);
			
			$emailDb = new App_Model_System_DbTable_Email();		
			$data = array(
				'recepient_email' => $applicant["appl_email"],
				'subject' => $templateData["subject"],
				'content' => $templateMail				
			);	
			
			//to send email with attachment
			$emailDb->addData($data);		
		
	}
	
	
	
	
}
?>