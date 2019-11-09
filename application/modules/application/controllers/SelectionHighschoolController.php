<?php
class Application_SelectionHighschoolController extends Zend_Controller_Action
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
	
	
	public function applicantListAction(){
		
		$this->view->title = $this->view->translate("applicant_list");
		
		$faculty_id = $this->_getParam('faculty_id', 0);
    	$this->view->faculty_id = $faculty_id;
    	
		$program_code = $this->_getParam('program_code', 0);
    	$this->view->program_code = $program_code;
    	
    	$academic_year = $this->_getParam('academic_year', 0);
    	$this->view->ayear = $academic_year;
    	
    	$period = $this->_getParam('period', 0);
    	$this->view->period = $period;
    	
    	
    	
		
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			$condition=array('admission_type'=>2,"academic_year"=>$formData["academic_year"],"program_code"=>$formData["programme"],'status'=>'PROCESS');
		
			$applicant_data = $applicantDB->getDatabyProgram($condition);		
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($applicant_data));
			$paginator->setItemCountPerPage(100);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
		}else{
			
			$condition=array('admission_type'=>2,'status'=>'PROCESS');
			$applicant_data = $applicantDB->getPaginateDatabyProgram($condition);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($applicant_data));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
		}
		
		$this->view->paginator = $paginator;
	}
	
	public function documentAction(){
		
		$this->view->title = $this->view->translate("document");
		
		$program_code = $this->_getParam('program_code', 0);
    	$this->view->program_code = $program_code;
		
		$transaction_id = $this->_getParam('id', 0);
    	$this->view->transaction_id = $transaction_id;
		
		//get applicant Document/Form
    	$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
    	  		
    		    		
    	$document = $documentDB->getData($transaction_id,32); //validasi bank/hs output
    	echo $directory = "http://".APP_HOSTNAME."/documents/".$document["ad_filepath"]."/".$document["ad_filename"];  
    	$this->view->download_file=$directory;
    		
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
    	
    	//print_r($applicant);
    	
    	//get education average mark
		$educationDB = new App_Model_Application_DbTable_ApplicantEducation();
		$everage_mark = $educationDB->getAverageMark($applicant["appl_id"]);
		$this->view->everage_mark= $everage_mark;   	
    	
    	//get transaction info
    	$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
    	$transaction_info = $transDB->getTransactionData($transaction_id);
    	$this->view->transaction_status = $transaction_info["at_status"];
    	
    	if($transaction_info["at_status"]=="OFFER" || $transaction_info["at_status"]=="REJECT"){
    		
    		$this->view->noticeMessage=$this->view->translate("this_application_has_been_processed");
    		
    		$ratingDB = new App_Model_Application_DbTable_ApplicantAssessment();
    		$data= $ratingDB->getData($transaction_id);
    		$this->view->rating = $data;
    		
    		$form = new Application_Form_Processing();    		
    		
    		if($transaction_info["at_status"]=='OFFER') $status = 1;
			if($transaction_info["at_status"]=='REJECT') $status = 2;
						
			$formData["aar_rating_dean"]=$data["aar_rating_dean"];
			$formData["aar_dean_status"]=$data["aar_dean_status"];							
			$formData["aar_rating_rector"]=$data["aar_rating_rector"];
			$formData["aar_rector_status"]=$data["aar_rector_status"];
			$formData["application_status"]=$status;				
			$formData["remarks"]=$data["aar_remarks"];	
			
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
				//if($formData["application_status"]==3) $status = 'INCOMPLETE';
				
				$infoUpd["at_status"]=$status;
				$infoUpd["at_selection_status"]=3;
				$transDB->updateData($infoUpd,$transaction_id);
				
				
					//----------add rating info----------
					$ratingDB = new App_Model_Application_DbTable_ApplicantAssessment();
					$auth = Zend_Auth::getInstance(); 
    							
					$data["aar_trans_id"]=$transaction_id;				
					$data["aar_rating_dean"]=$formData["aar_rating_dean"];
					$data["aar_dean_status"]=$formData["aar_dean_status"];							
					$data["aar_rating_rector"]=$formData["aar_rating_rector"];
					$data["aar_rector_status"]=$formData["aar_rector_status"];				
					$data["aar_remarks"]=$formData["remarks"];	
					$data["aar_approvalby"]=$auth->getIdentity()->id;
					$data["aar_approvaldt"]=date("Y-m-d H:m:s");		
					
					$ratingDB->addData($data);
				
					
													
					//generate and send offer letter to applicant
					if($formData["application_status"]==1){ //offer						
						$this->sendMail($transaction_id);										
					}
				
				
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'selection-highschool', 'action'=>'processing','id'=>$transaction_id,'program_code'=>$program_code),'default',true));
			}//end post
    		
    	}
    	
    	
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
    	
		
		//$nomor = '010/AK.4.02/PSSB-BAA/Usakti/WR.I/I-3/2012';
		$nomor="";
    	
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
		
		$faculty = $this->_getParam('faculty', 0);
    	$this->view->faculty_id = $faculty;
    	
		$program_code = $this->_getParam('programme', 0);
    	$this->view->program_code = $program_code;
    	
    	$academic_year = $this->_getParam('academic_year', 0);
    	$this->view->ayear = $academic_year;
    	
    	$period = $this->_getParam('period', 0);
    	$this->view->period = $period;
    	 	
    	
    	$form = new Application_Form_SelectionSearch(array('facultyid'=>$faculty));
    	$this->view->form=$form;  
    	
    	
    	
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
					
			
			if($formData["academic_year"] && $formData["period"] && $formData["faculty"]){
				
						
			$condition=array('admission_type'=>2,
							 "academic_year"=>$formData["academic_year"],
							 "period"=>$formData["period"],
							 "faculty_id"=>$formData["faculty"],
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
				$this->view->noticeError = "Silahkan pilih Tahun Akademik,Periode dan Facultas<br>";
			}
			
		}
		
		
		
	}
	
	
	public function saveDeanRatingAction(){
		
		
		$program_code = $this->_getParam('program_code', 0);
		$faculty_id = $this->_getParam('faculty_id', 0);
		
		$auth = Zend_Auth::getInstance(); 
		
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
						
			$selection["asd_type"]=1;//Dean
			$selection["asd_nomor"]=$formData["nomor"];
			$selection["asd_decree_date"]=$formData["decree_date"];
			$selection["asd_createdby"]=$auth->getIdentity()->id;
			$selection["asd_createddt"]=date("Y-m-d H:m:s");	
			
			$selectionDB = new App_Model_Application_DbTable_ApplicantSelectionDetl();			
			$selection_id = $selectionDB->addData($selection);
			
			
			for($i=0; $i<count($formData["dean_rating"]); $i++){				
				
				//add selection information
				$rating["aar_trans_id"]=$formData["transaction_id"][$i];
				$rating["aar_dean_status"]=$formData["dean_status"][$i];	
				$rating["aar_rating_dean"]=$formData["dean_rating"][$i];
				$rating["aar_dean_selectionid"]=$selection_id;								
				$rating["aar_dean_rateby"]=$auth->getIdentity()->id;
				$rating["aar_dean_ratedt"]=date("Y-m-d H:m:s");	
				
				$ratingDB = new App_Model_Application_DbTable_ApplicantAssessment();
				$ratingDB->addData($rating);
				
				
				//update selection status
				//jika dean status==3 (pending) indicate masih dalam process
				if($formData["dean_status"][$i]==1 || $formData["dean_status"][$i]==2){
					
					$status["at_selection_status"]=1;
					$transactionDB = new App_Model_Application_DbTable_ApplicantTransaction();
					$transactionDB->updateData($status,$formData["transaction_id"][$i]);
				}				
												
			}//end for
			
			
			$this->view->noticeSuccess = $this->view->translate("information_saved_successfully");
			//should notify rector by email to rate or not?
			
			//$this->_redirect($this->view->url(array('module'=>'application','controller'=>'selection-highschool', 'action'=>'batch-dean-rating','program_code'=>$program_code,'faculty_id'=>$faculty_id),'default',true));
			
		}//end if
	}
	
	
	public function batchRectorRatingAction(){
		
		$this->view->title = $this->view->translate("Batch Rector Rating");
		
		$faculty = $this->_getParam('faculty', 0);
    	$this->view->faculty_id = $faculty;
    	
		$program_code = $this->_getParam('programme', 0);
    	$this->view->program_code = $program_code;
    	
    	$academic_year = $this->_getParam('academic_year', 0);
    	$this->view->ayear = $academic_year;
    	
    	$period = $this->_getParam('period', 0);
    	$this->view->period = $period;
    	
    	  	
    	
    	$form = new Application_Form_SelectionSearch(array('facultyid'=>$faculty));
    	$this->view->form=$form;  
    	
		
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
								
			
			//get periode information
			/*$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
			$period_info = $periodDB->getData($formData["period"]);
			$fperiod = $period_info["ap_code"];*/
			
			$condition=array('admission_type'=>2,
							"academic_year"=>$formData["academic_year"],							
							"period"=>$formData["period"],
							"faculty_id"=>$formData["faculty"],
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
		$this->view->locale=$locale;
		   
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
		$condition=array('admission_type'=>2,"faculty_id"=>$faculty_id,'program_code'=>$program_code,'academic_year'=>$academic_year,'status'=>'PROCESS','period'=>$period["ap_id"]);
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant_data = $applicantDB->getDeanSelection($condition);
				
		$this->view->applicant = $applicant_data;
	
		
		//get program name
		/*$programDB = new App_Model_Record_DbTable_Program();
		$program_data = $programDB->getProgrambyCode($program_code);
		
			if($locale=="id_ID"){
				$program_name = $program_data["ArabicName"];
			}elseif($locale=="en_US"){
				$program_name = $program_data["ProgramName"];
			}
			
		$this->view->program_name = $program_name;*/
    	
	}
	
	public function downloadBatchDeanRatingAction(){
		
		$faculty_id = $this->_getParam('faculty_id', 0);    	
		$program_code  = $this->_getParam('program_code', 0);	
		$academic_year = $this->_getParam('academic_year', 0);
		$period        = $this->_getParam('period', 0);
				
    	//to get list applicant    	
		$condition=array('admission_type'=>2,"faculty_id"=>$faculty_id,'program_code'=>$program_code,'academic_year'=>$academic_year,'status'=>'PROCESS','period'=>$period);
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant_data = $applicantDB->getDeanSelection($condition);
		
		$this->view->applicant = $applicant_data;
		
	}
	
	public function uploadBatchDeanRatingAction(){
		
		$this->view->title = $this->view->translate("upload_batch_dean_rating");
		
		$faculty_id = $this->_getParam('faculty_id', 0); 
		$this->view->faculty_id = 	$faculty_id;
		   	
		$program_code  = $this->_getParam('program_code', 0);
		$this->view->program_code = 	$program_code;
		
		$academic_year = $this->_getParam('academic_year', 0);
		$this->view->ayear = 	$academic_year;
		
		$period        = $this->_getParam('period', 0);
    	$this->view->period = $period;
    	
		
		$form = new Application_Form_UploadBatch(array('facultyid'=>$faculty_id,'programcode'=>$program_code,'academicyear'=>$academic_year));
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

							    		
							    		$selection["asd_type"]=1;//Dean
										$selection["asd_nomor"]=$formData["nomor"];
										$selection["asd_decree_date"]=$formData["decree_date"];
										$selection["asd_createdby"]=$auth->getIdentity()->id;
										$selection["asd_createddt"]=date("Y-m-d H:m:s");	
										
										$selectionDB = new App_Model_Application_DbTable_ApplicantSelectionDetl();			
										$selection_id = $selectionDB->addData($selection);
							    		
							            //add selection information
										$rating["aar_trans_id"]=$data[1];
										$rating["aar_dean_status"]=$data[4];	
										$rating["aar_rating_dean"]=$data[5];
										$rating["aar_dean_selectionid"]=$selection_id;								
										$rating["aar_dean_rateby"]=$auth->getIdentity()->id;
										$rating["aar_dean_ratedt"]=date("Y-m-d H:m:s");																			
										
										$ratingDB = new App_Model_Application_DbTable_ApplicantAssessment();
										$ratingDB->addData($rating);
										
										
										//update selection status
										//jika dean status==3 (pending) indicate masih dalam process
										if($data[4]==1 || $data[4]==2){
											
											$status["at_selection_status"]=1;										
											$transactionDB = new App_Model_Application_DbTable_ApplicantTransaction();
											$transactionDB->updateData($status,$data[1]);
										}	
										
							    	}
							       
							    $row++;    
							    }
							    fclose($handle);
							    
							    $this->view->noticeSuccess = $this->view->translate("information_saved_successfully");
								//should notify rector by email to rate or not?
							    
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
	
		$faculty_id  = $this->_getParam('faculty_id', 0);
		$program_code  = $this->_getParam('program_code', 0);
		$academic_year = $this->_getParam('academic_year', 0);
		$period        = $this->_getParam('period', 0);
    	   	
		/*
		//get periode
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period = $periodDB->getData($period);
    	$this->view->period = $period;*/
    	
    	//to get list applicant    	
		$condition=array('admission_type'=>2,'faculty_id'=>$faculty_id,'program_code'=>$program_code,'academic_year'=>$academic_year,'status'=>'PROCESS','period'=>$period);
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant_data = $applicantDB->getDeanSelection($condition);				
		$this->view->applicant = $applicant_data;	
		
		
		//get faculty name
		if($faculty_id){
			$facultyDB =  new App_Model_General_DbTable_Collegemaster();
			$faculty_info = $facultyDB->getData($faculty_id);
			$this->view->faculty_name = $faculty_info["ArabicName"];
			$this->view->faculty_code = $faculty_info["CollegeCode"];
		}
		
		if($program_code){
			//get program name
			$programDB = new App_Model_Record_DbTable_Program();
			$program_data = $programDB->getProgrambyCode($program_code);
			$this->view->program_name = $program_data["ArabicName"];
			$this->view->program_code = $program_code;
		}
    
    	
	}
	
	
	public function printBatchRectorRatingAction(){
				
		//$this->_helper->layout->disableLayout();
		$this->_helper->layout->setLayout('print');
		 
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		$this->view->locale=$locale;
			
		$faculty_id  = $this->_getParam('faculty_id', 0);
		$program_code  = $this->_getParam('program_code', 0);
		$academic_year = $this->_getParam('academic_year', 0);
		$period        = $this->_getParam('period', 0);
    	   	
		//get periode
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period = $periodDB->getData($period);
    	$this->view->period = $period;
    	
		//get faculty name
		$facultyDB = new App_Model_General_DbTable_Collegemaster();
		$faculty = $facultyDB->getData($faculty_id);
		
		if($locale=="id_ID"){
			$this->view->faculty_name = $faculty["ArabicName"];
		}else{
			$this->view->faculty_name = $faculty["CollegeName"];
		}
		
    	//to get list applicant    	
		$condition=array('admission_type'=>2,"faculty_id"=>$faculty_id,'program_code'=>$program_code,'academic_year'=>$academic_year,'status'=>'PROCESS','period'=>$period["ap_id"]);
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant_data = $applicantDB->getRectorSelection($condition);
				
		$this->view->applicant = $applicant_data;
	
		//get academic year
    	$academicDB = new App_Model_Record_DbTable_AcademicYear();
    	$academic_year_data = $academicDB->getCurrentAcademicYearData();
		$academic_year=	$academic_year_data["ay_code"];	
    	$this->view->current_academic_year = $academic_year;
    	
    	
    	
	}
	
	
	public function printRectorNilaiRaportAction(){
				
		//$this->_helper->layout->disableLayout();
		  $this->_helper->layout->setLayout('print');
	
		$faculty_id  = $this->_getParam('faculty_id', 0);
		$program_code  = $this->_getParam('program_code', 0);
		$academic_year = $this->_getParam('academic_year', 0);
		$period        = $this->_getParam('period', 0);
    	   	
		
		//get periode
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period = $periodDB->getData($period);
    	$this->view->period = $period;
    	
    	//to get list applicant    	
		$condition=array('admission_type'=>2,"faculty_id"=>$faculty_id,'program_code'=>$program_code,'academic_year'=>$academic_year,'status'=>'PROCESS','period'=>$period["ap_id"]);
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		$applicant_data = $applicantDB->getRectorSelection($condition);				
		$this->view->applicant = $applicant_data;	
		
		
		
		//get faculty name
		if($faculty_id){
			$facultyDB =  new App_Model_General_DbTable_Collegemaster();
			$faculty_info = $facultyDB->getData($faculty_id);
			$this->view->faculty_name = $faculty_info["ArabicName"];
			$this->view->faculty_code = $faculty_info["CollegeCode"];
		}
		
		if($program_code){
			//get program name
			$programDB = new App_Model_Record_DbTable_Program();
			$program_data = $programDB->getProgrambyCode($program_code);
			$this->view->program_name = $program_data["ArabicName"];
			$this->view->program_code = $program_code;
		}
    
    	
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
		$condition=array('admission_type'=>2,"faculty_id"=>$faculty_id,'program_code'=>$program_code,'academic_year'=>$academic_year,'status'=>'PROCESS','period'=>$period_data["ap_id"]);
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
							            
							    		/*if($data[5]=="Y"){
											//accept dean rate			
											$rating["aar_rating_verified"]='Y';
											$final_rate = $data[4]; 
													
										}else {
											//reject dean rate	
											$rating["aar_rating_verified"]='N';  
											$rating["aar_rating_rector"]=$data[6]; 
											$final_rate = $data[6]; 
										}*/

							    		$selection["asd_type"]=2;//Rector
										$selection["asd_nomor"]=$formData["nomor"];
										$selection["asd_decree_date"]=$formData["decree_date"];
										$selection["asd_createdby"]=$auth->getIdentity()->id;
										$selection["asd_createddt"]=date("Y-m-d H:m:s");	
										
										$selectionDB = new App_Model_Application_DbTable_ApplicantSelectionDetl();			
										$selection_id = $selectionDB->addData($selection);
										
										$rating["aar_rating_rector"]=$data[5];	
										$rating["aar_rector_status"]=$data[6];	
										$rating["aar_rector_selectionid"]=$selection_id;								
										$rating["aar_rector_rateby"]=$auth->getIdentity()->id;
										$rating["aar_rector_ratedt"]=date("Y-m-d H:m:s");	
										
										$ratingDB = new App_Model_Application_DbTable_ApplicantAssessment();
										$ratingDB->updateAssessmentData($rating,$data[1]);
										
										//print_r($rating);
										
										
										//update selection status
										if($data[6]==1 || $data[6]==2){
											
											$status["at_selection_status"]=2;				
											$transactionDB = new App_Model_Application_DbTable_ApplicantTransaction();
											$transactionDB->updateData($status,$data[1]);
										}
										
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
				
					
				/*if($formData["rector_verification"][$i]=="1"){
					//accept dean rate			
					$rating["aar_rating_verified"]='Y';
					$final_rate = $formData["dean_rating"][$i]; 
							
				}else {
					//reject dean rate	
					$rating["aar_rating_verified"]='N';  
					$rating["aar_rating_rector"]=$formData["rector_rating"][$i];
					$final_rate = $formData["rector_rating"][$i]; 
				}*/
				
				
				
				
				$selection["asd_type"]=2;//Rector
				$selection["asd_nomor"]=$formData["nomor"];
				$selection["asd_decree_date"]=$formData["decree_date"];
				$selection["asd_createdby"]=$auth->getIdentity()->id;
				$selection["asd_createddt"]=date("Y-m-d H:m:s");	
				
				$selectionDB = new App_Model_Application_DbTable_ApplicantSelectionDetl();			
				$selection_id = $selectionDB->addData($selection);
							
								
				$rating["aar_rating_rector"] = $formData["rector_rating"][$i];
				$rating["aar_rector_status"] = $formData["rector_status"][$i];
				$rating["aar_rector_selectionid"] = $selection_id;
				$rating["aar_rector_rateby"]=$auth->getIdentity()->id;
				$rating["aar_rector_ratedt"]=date("Y-m-d H:m:s");	
			
				
				//update rector rating
				$ratingDB = new App_Model_Application_DbTable_ApplicantAssessment();
				$ratingDB->updateAssessmentData($rating,$formData["transaction_id"][$i]);
				
				
				//update selection status
				if($formData["rector_status"][$i]==1 || $formData["rector_status"][$i]==2){
					
					$status["at_selection_status"]=2;				
					$transactionDB = new App_Model_Application_DbTable_ApplicantTransaction();
					$transactionDB->updateData($status,$formData["transaction_id"][$i]);
				}
								
			}//end for
			
			$this->view->noticeSuccess = $this->view->translate("saved_successfully");
			//should notify rector by email to rate or not?
			
			
		}//end if
	}
	
	
	public function batchApprovalAction(){
		
		$this->view->title = $this->view->translate("Batch Approval");
		
		$faculty_id = $this->_getParam('faculty', 0);  
		$this->view->faculty_id = $faculty_id;  
		
		$program_code = $this->_getParam('programme', 0);
    	$this->view->program_code = $program_code;
    	
    	$academic_year = $this->_getParam('academic_year', 0);
    	$this->view->ayear = $academic_year;
    	
    	$period = $this->_getParam('period', 0);
    	$this->view->period = $period;
    	
    	
    	
		$form = new Application_Form_SelectionSearch();
    	$this->view->form=$form;  
    	
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$condition=array('admission_type'=>2,
							"academic_year"=>$formData["academic_year"],
							"period"=>$formData["period"],
			 				"faculty_id"=>$formData["faculty"],
							"program_code"=>$formData["programme"],
							'status'=>'PROCESS',
			 				);
						
			$applicant_data = $applicantDB->getApprovalSelection($condition);
					
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
				
				
				$selection["asd_type"]=3;//Final
				$selection["asd_nomor"]=$formData["nomor"];
				$selection["asd_decree_date"]=$formData["decree_date"];
				$selection["asd_createdby"]=$auth->getIdentity()->id;
				$selection["asd_createddt"]=date("Y-m-d H:m:s");	
				
				$selectionDB = new App_Model_Application_DbTable_ApplicantSelectionDetl();			
				$selection_id = $selectionDB->addData($selection);
							
				$rating["aar_final_selectionid"]=$selection_id;	
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

				if($formData["approval"][$i]=="OFFER"){					
					$this->sendMail($formData["transaction_id"][$i]);
				}//END SEND MAIL
				
			}//end for
			
			$this->view->noticeSuccess = $this->view->translate("saved_successfully");
			//should notify rector by email to rate or not?
			
			
		}//end if
	}
	
	
	public function printBatchApprovalAction(){
				
		//$this->_helper->layout->disableLayout();
		$this->_helper->layout->setLayout('print');
			
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
    	    	
		$faculty_id = $this->_getParam('faculty_id', 0);  
		$this->view->faculty_id = $faculty_id;  
		
		$program_code = $this->_getParam('programme', 0);
    	$this->view->program_code = $program_code;
    	
    	$academic_year = $this->_getParam('academic_year', 0);
    	$this->view->ayear = $academic_year;
    	
    	$period = $this->_getParam('period', 0);
    	$this->view->period = $period;
    	
    	//get periode
    	$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
    	$period_info = $periodDB->getData($period);    
    	$this->view->period_name = $period_info["ap_desc"];
    	
    	$condition2=array('admission_type'=>2,
							"academic_year"=>$academic_year,
							"period"=>$period,
    						"faculty_id"=>$faculty_id,
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
		
    	
    	$activityDB = new App_Model_Record_DbTable_ActivityCalender();	
    	$registrasi = $activityDB->getNearestActivityDate(2,$period);//registrasi
    	$this->view->registrasi =$registrasi;
	}
	
	
	public function downloadBatchApprovalAction(){
		
		$faculty_id = $this->_getParam('faculty', 0); 
		$this->view->faculty_id = 	$faculty_id;
		   	
		$program_code  = $this->_getParam('programme', 0);
		$this->view->program_code = 	$program_code;
		
		$academic_year = $this->_getParam('academic_year', 0);
		$this->view->ayear = 	$academic_year;
		
		$period  = $this->_getParam('period', 0);
		$this->view->period = $period;
		
    	
    	//to get list applicant    	
		$condition=array('admission_type'=>2,"faculty_id"=>$faculty_id,'program_code'=>$program_code,'academic_year'=>$academic_year,'status'=>'PROCESS','period'=>$period);
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
    	
		
		$form = new Application_Form_UploadBatch(array ('facultyid' => $faculty_id,'programcode' => $program_code, 'academicyear' => $academic_year ) );
		$this->view->form = $form;
		
		$auth = Zend_Auth::getInstance(); 
		
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();		
          
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
							            $selection["asd_type"]=3;//Rector
										$selection["asd_nomor"]=$formData["nomor"];
										$selection["asd_decree_date"]=$formData["decree_date"];
										$selection["asd_createdby"]=$auth->getIdentity()->id;
										$selection["asd_createddt"]=date("Y-m-d H:m:s");	
										
										$selectionDB = new App_Model_Application_DbTable_ApplicantSelectionDetl();			
										$selection_id = $selectionDB->addData($selection);
							    		
										$rating["aar_final_selectionid"]=$selection_id;	
							    		$rating["aar_approvalby"]=$auth->getIdentity()->id;
										$rating["aar_approvaldt"]=date("Y-m-d H:m:s");				
										
										//update rector rating
										$ratingDB = new App_Model_Application_DbTable_ApplicantAssessment();
										$ratingDB->updateAssessmentData($rating,$data[1]);
										
										
										//update selection status
										if($data[8]==1){
												$status["at_status"]="OFFER";
										}elseif($data[8]==2){
											    $status["at_status"]="REJECT";
										}
										
										$status["at_selection_status"]=3;	//completed	
											
										$transactionDB = new App_Model_Application_DbTable_ApplicantTransaction();
										$transactionDB->updateData($status,$data[1]);
										
										if($data[8]==1){
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
			$url_student_portal = "http://".APP_HOSTNAME."/online-application";
			
			$templateDB = new App_Model_General_DbTable_EmailTemplate();
			$templateData = $templateDB->getData(5,$applicant["appl_prefer_lang"]);//offer letter
    		    				
    		$templateMail = $templateData['body'];				
			$templateMail = str_replace("[applicant_name]",$fullname,$templateMail);
			$templateMail = str_replace("[program]",$program[0]["program_name"],$templateMail);
			$templateMail = str_replace("[faculty]",$program[0]["faculty"],$templateMail);
			$templateMail = str_replace("[url_student_portal]",$url_student_portal,$templateMail);
			
			$emailDb = new App_Model_System_DbTable_Email();		
			$data = array(
				'recepient_email' => $applicant["appl_email"],
				'subject' => $templateData["subject"],
				'content' => $templateMail,
			    'attachment_path'=>'',
			    'attachment_filename'=>''
			);	
			
			//to send email with attachment
			$emailDb->addData($data);		
		
	}
	
	
	public function ajaxGetProgramAction($faculty_id=0){
    	$faculty_id = $this->_getParam('faculty_id', 4);
    	
     	//if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        //}
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	$db = Zend_Db_Table::getDefaultAdapter();
	  	$select = $db->select()
	                 ->from(array('p'=>'tbl_program'))
	                 ->where('p.IdCollege = ?', $faculty_id)
	                 ->order('p.ProgramName ASC');
	    
			
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
		
		$i=0;
		foreach ($row as $p){
			
			if($locale=="id_ID"){
				$program_name = $p["ArabicName"];
			}elseif($locale=="en_US"){
				$program_name = $p["ProgramName"];
			}
			
		$program[$i]["ProgramCode"]=$p["ProgramCode"];
		$program[$i]["ProgramName"]=$program_name;
		$i++;
		}
			
	    
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($program);
		
		echo $json;
		exit();
    }
    
    
    
	public function selectionStatusAction(){
		
		$this->view->title = $this->view->translate("selection_status");
		
		$faculty = $this->_getParam('faculty',null);
    	$this->view->faculty = $faculty;
    	
		$program_code = $this->_getParam('program_code', '');
    	$this->view->program_code = $program_code;
    	
    	$academic_year = $this->_getParam('academic_year',null);
    	$this->view->ayear = $academic_year;
    	
    	$period = $this->_getParam('period',null);
    	$this->view->period = $period;
    	
    	$selection_status = $this->_getParam('selection_status', '');
    	$this->view->selection_status = $selection_status;
    	
    	
    	$condition=array('admission_type'=>2,
						 "academic_year"=>$academic_year,
						 "period"=>$period,
						 "faculty"=>$faculty,
						 "program_code"=>$program_code,
						 "selection_status"=>$selection_status					
						 );		
								 
							 
    	  	
    	$form = new Application_Form_SelectionStatusSearch(array('facultyid'=>$faculty));
    	$this->view->form=$form;  
		
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		
		if ($this->getRequest()->isPost()) {			
			
			$formData = $this->getRequest()->getPost();											 
			
			if ($form->isValid($_POST)) {
				$applicant_data = $applicantDB->getStatusSelection($condition);				
				$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($applicant_data));
				$paginator->setItemCountPerPage(2);
				$paginator->setCurrentPageNumber($this->_getParam('page',1));			
				$this->view->paginator = $paginator;
			}
			
		}elseif($academic_year && $period && $faculty){
			
			$applicant_data = $applicantDB->getStatusSelection($condition);	
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($applicant_data));
			$paginator->setItemCountPerPage(2);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));			
			$this->view->paginator = $paginator;				
		}
		
		
		$this->view->searchParams = array('admission_type'=>2,
							 "academic_year"=>$academic_year,
							 "period"=>$period,
							 "faculty"=>$faculty,
							 "program_code"=>$program_code,
							 "selection_status"=>$selection_status					
							 );	
		
		$form->populate($condition);
		$this->view->form = $form;
		
		
	}
	
	
	
	
	
}
?>