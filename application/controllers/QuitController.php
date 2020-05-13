<?php

class QuitController extends Zend_Controller_Action {
	
	public function init(){
		
	}
	
	public function indexAction()
    {
    	$this->view->title=$this->view->translate("Quit");
    	
    	$msg = $this->_getParam('msg', 0);
    	if($msg) $this->view->noticeSuccess=$this->view->translate("Your application will be processed when Trisakti received the complete documents for QUIT. You will receive your refund (cheque) within four working days from the Date of the Approval");
    	
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;    	
    	
    	 //transaction data
        $transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();      
        $paginator = $transactionDb->getQuitPaidAndOfferList($appl_id);
        $this->view->paginator = $paginator;
    	    			
		//transaction programme
		$applicantProgramDb = new App_Model_Application_DbTable_ApplicantProgram();
		
		$i=0;
		$txnProgram = array();
		
		foreach ($paginator as $txn){
			//$txnProgram[$i] = $applicantProgramDb->getApplicantProgramByID($txn['at_trans_id']);		
			$txnProgram[$i] = $applicantProgramDb->getProgramOffered($txn['at_trans_id'],$txn["at_appl_type"]);	
			$i++;
		}
			
		$this->view->txnProgram = $txnProgram;
    	
    }
    
    
    public function applyQuitAction(){
    	
    	$this->view->title=$this->view->translate("Application for Quit");
    	
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;    	
    	
    	$txnId = $this->_getParam('id', 0);
    	
    	$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();  
    	
    	if($txnId!=0 && $transactionDb->checkValidApplicant($txnId, $appl_id)){
    		$auth->getIdentity()->transaction_id = $txnId;
    	}else{
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
    	}
    	
    	$applicant = $transactionDb->getTransaction($txnId);
    	$this->view->applicant = $applicant;
    	
    	$applicantProgramDb = new App_Model_Application_DbTable_ApplicantProgram();
    	$txnProgram = $applicantProgramDb->getProgramOffered($txnId,$applicant["at_appl_type"]);
    	$this->view->program = $txnProgram;
    	
    	//get payment status
    	$financeDb =  new Studentfinance_Model_DbTable_PaymentMain();
    	$payment = $financeDb->getApplicantPaymentTotalAmount($applicant["at_pes_id"]);     	  	
    	$this->view->payment = $payment;
    	
    	$form =  new App_Form_Quit();
    	$this->view->form = $form;
    	
    	if ($this->getRequest()->isPost()) {    		
    		
    		$formData = $this->getRequest()->getPost(); 
    		if($form->isValid($formData)){  
    			
    			//update transaction
    			$transactionDb->updateData(array('at_quit_status'=>1), $formData["transaction_id"]);
    			
    			//save quit info
    			$info["aq_trans_id"]=$formData["transaction_id"];
    			$info["aq_reason"]=$formData["aq_reason"];
    			$info["aq_authorised_personnel"]=$formData["aq_authorised_personnel"];
    			$info["aq_relationship"]=$formData["aq_relationship"];
    			$info["aq_address"]=$formData["aq_address"];
    			$info["aq_identity_type"]=$formData["aq_identity_type"];
    			$info["aq_identity_no"]=$formData["aq_identity_no"];	
    			$info["aq_createddt"]=date("Y-m-d H:i:s");    		   
    			
    			$quitDb = new App_Model_Application_DbTable_ApplicantQuit();
    			$quitDb->addData($info);    			
    			    		
    			try
				{					
					 ///upload_file
					$pathupload = "/applicant/".date("mY");
					$applicant_path = DOCUMENT_PATH.$pathupload;
					
	    			//create directory to locate file			
					if (!is_dir($applicant_path)) {
				    	mkdir($applicant_path, 0775);
					}
									
					$applicant_path = $applicant_path."/".$formData["transaction_id"];
					
	    			//create directory to locate file			
					if (!is_dir($applicant_path)) {
				    	mkdir($applicant_path, 0775);
					}
					
					$adapter = new Zend_File_Transfer_Adapter_Http();				
					$adapter->setDestination($applicant_path);
					//->addValidator(‘Size’,false,array(‘max’ => 10000))
					//->addValidator(‘Extension’,false,array(‘extension’ => ‘txt,sql’,'case’ => true));
					
					$files = $adapter->getFileInfo();
				
					foreach($files as $fieldname=>$fileinfo)
					{
					
						if (($adapter->isUploaded($fileinfo['name']))&& ($adapter->isValid($fileinfo['name'])))
						{
							
							$new_filename = date('Ymdhs').'_'.$fileinfo['name'];					
							$adapter->addFilter('Rename',array('target'=>$applicant_path.'/'.$new_filename,'overwrite'=>true));
							$adapter->receive($fileinfo['name']);
							
							if($fieldname=="aq_document_payment_slip") $type = 46;
							if($fieldname=="aq_document_identity_card") $type = 48;
							if($fieldname=="aq_document_surat_pernyataan") $type = 47;
							
						    //then, store links etc in db for retrieval later..						    
							$uploadfileDB = new App_Model_Application_DbTable_UploadFile();
						    $doc = array(
												'auf_appl_id' => $formData["transaction_id"],
												'auf_file_name' => $new_filename, 
												'auf_file_type' => $type, 
												'auf_upload_date' => date("Y-m-d h:i:s"),
												'auf_upload_by' => $formData["transaction_id"],
												'pathupload' => $pathupload
											   );
						
							$uploadfileDB->addData($doc);	
							
						}
					}
					
					$this->_redirect($this->view->url(array('module'=>'default','controller'=>'quit', 'action'=>'index','msg'=>'success'),'default',true));
				
				}
				catch (Exception $ex)
				{
					echo "Exception!\n";
					echo $ex->getMessage();
				}
				
                exit;
    			
    		}	//end if	
    	}//end if    
    }
    
    
    public  function downloadAction(){
    	
    	$this->view->title=$this->view->translate("Download Quit Form");
    	
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;    	
    	
    	$txnId = $this->_getParam('id', 0);    	
    	    
    	$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();  
    	$applicant = $transactionDb->getTransaction($txnId);
		$this->view->applicant = $applicant;
		
		$applicantProgramDb = new App_Model_Application_DbTable_ApplicantProgram();
		$program_data = $applicantProgramDb->getProgramOffered($txnId,$applicant["at_appl_type"]);
    	
    	if($txnId!=0 && $transactionDb->checkValidApplicant($txnId, $appl_id)){
    		$auth->getIdentity()->transaction_id = $txnId;
    	}else{
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
    	}

    	
    	
    	
	    	//cek document dah create ke belum
	    	$applicantDocumentDb = new App_Model_Application_DbTable_ApplicantDocument();    		
	    	$docData = $applicantDocumentDb->getData($txnId,49);
    		
	    	//generate each time user download
    		if($docData & 1==0){
    			$this->view->file_path = DOCUMENT_PATH.DIRECTORY_SEPARATOR.$docData['ad_filepath'].DIRECTORY_SEPARATOR.$docData['ad_filename'];
    		}else{		
    			
		    			/*
		    			 * Start GENERATE
		    			 */
		    			
		    			
		    			// ------- create PDF File section	--------  
		    			
		    			setlocale(LC_MONETARY, 'id_ID');
		    			setlocale(LC_TIME, 'id_ID');
		    			
		    			global $payment;
		    			global $quit_charges;
		    			global $invoice;
		    			
    					$quitDb = new App_Model_Application_DbTable_ApplicantQuit();
						$quit = $quitDb->getInfo($txnId);
						
						$definationDB = new App_Model_General_DbTable_Definationms();
						$defination = $definationDB->getData($quit["aq_identity_type"]);
						
						//get list payment made by applicant
						$paymentDb = new App_Model_Finance_DbTable_PaymentMain();	
					    $payment = $paymentDb->getPaymentDetails($applicant["at_pes_id"]);
						
					    //get invoice
					    $invoiceMainDb = new Studentfinance_Model_DbTable_InvoiceMain();
					    $invoice = $invoiceMainDb->getInvoicedProformaData($applicant["at_pes_id"]);
					    
					    //mark paid if having transfer to advance payment
					    $advancePaymentDb = new Studentfinance_Model_DbTable_AdvancePayment();
					    for($i=0; $i<sizeof($invoice); $i++){
					    
					    	//get advance payment transfered
					    	$advPymt = $advancePaymentDb->getAdvancePaymentFromInvoice($invoice[$i]['id']);
					    
					    	if($advPymt){
					    		$invoice[$i]['paid_transfer_to_advpymt'] = $advPymt['advpy_amount'];
					    	}
					    
					    	//remove
					    }
					    
						//get Paket info
						$feeStructureDB = new App_Model_Finance_DbTable_FeeStructurePlan();	
						$paket = $feeStructureDB->getStructurePlan($payment[0]["fs_id"],$payment[0]["fsp_id"]);
						
						//get quit charges
						$quitFeeDb = new Studentfinance_Model_DbTable_FeeQuitFaculty();
						$quit_charges = $quitFeeDb->getQuitCharges($program_data["faculty_id"], $applicant["at_intake"],date("Y-m-d",strtotime($quit["aq_createddt"])));
																						
						$collection_date = strftime('%e-%B-%Y', strtotime('+4 days', strtotime(date("Y-m-d",strtotime($quit["aq_approvaldt"])))));
						$approve_date    = strftime("%e-%B-%Y",strtotime($quit["aq_approvaldt"]));
						
							
						
		    			$fieldValues = array (
						    
		    			     '$[Academicyear]' => $applicant["ay_code"],		    			 	
		    			 	 '$[Period]' => $applicant["ap_number"],	
		    			
						 	 '$[Applicantname]' => $applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
		    			     '$[Applicantid]' => $applicant["at_pes_id"],
							 '$[Faculty]' => $program_data["faculty_indonesia"],				    
							 '$[Programme]' => $program_data["program_name_indonesia"],
		    			     '$[Paket]' => $paket["fsp_name"],	
		    				 '$[Address1]' => $applicant["appl_address1"],
						     '$[Address2]' => $applicant["appl_address2"],
		    			     '$[Phone]' => $applicant["appl_phone_hp"],	

		    			 	 '$[PersonalAuthorised]' => $quit["aq_authorised_personnel"],		    			 	
		    			 	 '$[PersonalAddress]' => $quit["aq_address"],		    				
		    				 '$[IdentityType]' => $defination["DefinitionDesc"],	
		    				 '$[IdentityNo]' => $quit["aq_identity_no"],
		    			    // '$[Perincian]' => $perincian,
		    				
		    				 '$[CollectionDate]' => $collection_date,
		    			     '$[ApprovedDate]' => $approve_date
						);		
						    	
						
						
		    			$monthyearfolder=date("mY");
		    			
				    	//directory to locate file
						$app_directory_path = DOCUMENT_PATH."/applicant/".$monthyearfolder;	
		
						//create directory to locate file			
						if (!is_dir($app_directory_path)) {
						    mkdir($app_directory_path, 0775,true);
						}
						
						$output_directory_path = DOCUMENT_PATH."/applicant/".$monthyearfolder."/".$txnId;
							
		    			//create directory to locate file			
						if (!is_dir($output_directory_path)) {
						    mkdir($output_directory_path, 0775);
						}				
														
						//$location_path
					    $location_path = "applicant/".$monthyearfolder."/".$txnId;								
						
						//filename
						$output_filename = $applicant["at_pes_id"]."_quitform.pdf";
						
						try{
							require_once 'dompdf_config.inc.php';
							
							$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
							$autoloader->pushAutoloader('DOMPDF_autoload');
							
							//template path	 
							$html_template_path = DOCUMENT_PATH."/template/quitform.html";
							
							$html = file_get_contents($html_template_path);
							
							//replace variable
							foreach ($fieldValues as $key=>$value){
								$html = str_replace($key,$value,$html);	
							}
								
							
							$dompdf = new DOMPDF();
							$dompdf->load_html($html);
							$dompdf->set_paper('a4', 'potrait');
							@$dompdf->render();
							
							$dompdf = @$dompdf->stream($output_filename);
							//$dompdf = $dompdf->output();
							
							
							
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
						/*$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
						$doc["ad_appl_id"]=$txnId;
						$doc["ad_type"]=49; //QUIT FORM
						$doc["ad_filepath"]=$location_path;
						$doc["ad_filename"]=$output_filename;
						$doc["ad_createddt"]=date("Y-m-d");
						$documentDB->addData($doc);	
		    	
						$this->view->file_path = DOCUMENT_PATH.DIRECTORY_SEPARATOR.$location_path.DIRECTORY_SEPARATOR.$output_filename;
		    	       */
    			
    			/*
    			 * END GENERATE
    			 */
    				
		   }//end cek document
	
		       			
    }
    
    
    public function generateQuitLetter($txnId,$applicant,$program_data){
    	
    			$fieldValues = array (
				     '$[Applicantid]' => $applicant["at_pes_id"], 
				 	 '$[Applicantname]' => $applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
				     '$[Address1]' => $applicant["appl_address1"],
				     '$[Address2]' => $applicant["appl_address2"],				
					 '$[Facultyname]' => $program_data["faculty_indonesia"],				    
					 '$[Programme]' => $program_data["program_name"]					
				);		    	
				//print_r($fieldValues);exit;
						
    			$monthyearfolder=date("mY");
    			
		    	//directory to locate file
				$app_directory_path = DOCUMENT_PATH."/applicant/".$monthyearfolder;	

				//create directory to locate file			
				if (!is_dir($app_directory_path)) {
				    mkdir($app_directory_path, 0775,true);
				}
				
				$output_directory_path = DOCUMENT_PATH."/applicant/".$monthyearfolder."/".$txnId;
					
    			//create directory to locate file			
				if (!is_dir($output_directory_path)) {
				    mkdir($output_directory_path, 0775);
				}				
												
				//$location_path
			    $location_path = "applicant/".$monthyearfolder."/".$txnId;								
				
				//filename
				$output_filename = $applicant["at_pes_id"]."_quitform.pdf";
				
				try{
					require_once 'dompdf_config.inc.php';
					
					$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
					$autoloader->pushAutoloader('DOMPDF_autoload');
					
					//template path	 
					$html_template_path = DOCUMENT_PATH."/template/quitform.html";
					
					$html = file_get_contents($html_template_path);
					
					//replace variable
					foreach ($fieldValues as $key=>$value){
						$html = str_replace($key,$value,$html);	
					}
						
					
					$dompdf = new DOMPDF();
					$dompdf->load_html($html);
					$dompdf->set_paper('a4', 'potrait');
					@$dompdf->render();
					
					$dompdf = @$dompdf->output();
					
					
					
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
				$doc["ad_appl_id"]=$txnId;
				$doc["ad_type"]=49; //QUIT FORM
				$doc["ad_filepath"]=$location_path;
				$doc["ad_filename"]=$output_filename;
				$doc["ad_createddt"]=date("Y-m-d");
				$documentDB->addData($doc);	
    	
				return DOCUMENT_PATH.DIRECTORY_SEPARATOR.$location_path.DIRECTORY_SEPARATOR.$output_filename;
    	       
    			
    			
    }
    
    
     public function editQuitAction(){
    	
    	$this->view->title=$this->view->translate("Application for Quit");
    	
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;    	
    	
    	$txnId = $this->_getParam('id', 0);
    	
    	$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();  
    	
    	if($txnId!=0 && $transactionDb->checkValidApplicant($txnId, $appl_id)){
    		$auth->getIdentity()->transaction_id = $txnId;
    	}else{
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'applicant-portal', 'action'=>'index'),'default',true));
    	}
    	
    	$applicant = $transactionDb->getTransaction($txnId);
    	$this->view->applicant = $applicant;
    	
    	$applicantProgramDb = new App_Model_Application_DbTable_ApplicantProgram();
    	$txnProgram = $applicantProgramDb->getProgramOffered($txnId,$applicant["at_appl_type"]);
    	$this->view->program = $txnProgram;
    	
    	//get payment status
    	$financeDb =  new App_Model_Finance_DbTable_PaymentMain();
    	$payment = $financeDb->getApplicantPaymentTotalAmount($applicant["at_pes_id"]); //
    	//$payment = $financeDb->getApplicantPaymentTotalAmount('13380008');     	
    	$this->view->payment = $payment;
    
        //get KARTU PESERTA UJIAN
    	$documentDB = new App_Model_Application_DbTable_ApplicantDocument();
    	$kartu = $documentDB->getData($txnId,30); 
    	if($kartu["ad_filename"]!=''){
    		$this->view->url_kartu = 'http://'.APP_HOSTNAME.'/documents/'.$kartu["ad_filepath"].'/'.$kartu["ad_filename"];
    	}
    	
    	//get document uploaded
    	$uploadfileDB = new App_Model_Application_DbTable_UploadFile();  
    	
    	//Slip Pembayaran
    	$payment_slip = $uploadfileDB->getFile($txnId,46);
		if($payment_slip["auf_file_name"]!=''){
    		$this->view->url_payment_slip = 'http://'.APP_HOSTNAME.'/documents'.$payment_slip["pathupload"].'/'.$payment_slip["auf_file_name"];
    	}
    	
    	//Identity Card
    	$identity_card = $uploadfileDB->getFile($txnId,48);
		if($identity_card["auf_file_name"]!=''){
    		$this->view->url_identity_card = 'http://'.APP_HOSTNAME.'/documents'.$identity_card["pathupload"].'/'.$identity_card["auf_file_name"];
    	}
    	
    	//Surat Pernyataan Kesediaan
    	$surat_pernyataan = $uploadfileDB->getFile($txnId,47);
		if($surat_pernyataan["auf_file_name"]!=''){
    		$this->view->url_surat_pernyataan = 'http://'.APP_HOSTNAME.'/documents'.$surat_pernyataan["pathupload"].'/'.$surat_pernyataan["auf_file_name"];
    	}
    	
    	//get quit info
    	$quitDb = new App_Model_Application_DbTable_ApplicantQuit();
		$quit = $quitDb->getInfo($txnId);	
    	
    	$form =  new App_Form_Quit();
    	$form->populate($quit);
    	$this->view->form = $form;
    	
    	if ($this->getRequest()->isPost()) {    		
    		
    		$formData = $this->getRequest()->getPost(); 
    		if($form->isValid($formData)){  
    			
    			//update transaction
    			$transactionDb->updateData(array('at_quit_status'=>1), $formData["transaction_id"]);
    			
    			//save quit info
    			$info["aq_trans_id"]=$formData["transaction_id"];
    			$info["aq_reason"]=$formData["aq_reason"];
    			$info["aq_authorised_personnel"]=$formData["aq_authorised_personnel"];
    			$info["aq_relationship"]=$formData["aq_relationship"];
    			$info["aq_address"]=$formData["aq_address"];
    			$info["aq_identity_type"]=$formData["aq_identity_type"];
    			$info["aq_identity_no"]=$formData["aq_identity_no"];	    			 		   
    			
    			$quitDb = new App_Model_Application_DbTable_ApplicantQuit();
    			$quitDb->updateData($info);
				
    			try
				{					
					 ///upload_file
					$pathupload = "/applicant/".date("mY")."/".$formData["transaction_id"];
					$applicant_path = DOCUMENT_PATH.$pathupload;
					
	    			//create directory to locate file			
					if (!is_dir($applicant_path)) {
				    	mkdir($applicant_path, 0775);
					}
									
					$applicant_path = $applicant_path."/".$formData["transaction_id"];
					
	    			//create directory to locate file			
					if (!is_dir($applicant_path)) {
				    	mkdir($applicant_path, 0775);
					}
					
					$adapter = new Zend_File_Transfer_Adapter_Http();				
					$adapter->setDestination($applicant_path);
					//->addValidator(‘Size’,false,array(‘max’ => 10000))
					//->addValidator(‘Extension’,false,array(‘extension’ => ‘txt,sql’,'case’ => true));
					
					$files = $adapter->getFileInfo();
				
					foreach($files as $fieldname=>$fileinfo)
					{
					
						if (($adapter->isUploaded($fileinfo['name']))&& ($adapter->isValid($fileinfo['name'])))
						{
							
							$new_filename = date('Ymdhs').'_'.$fileinfo['name'];					
							$adapter->addFilter('Rename',array('target'=>$applicant_path.'/'.$new_filename,'overwrite'=>true));
							$adapter->receive($fileinfo['name']);
							
							if($fieldname=="aq_document_payment_slip") $type = 46;
							if($fieldname=="aq_document_identity_card") $type = 48;
							if($fieldname=="aq_document_surat_pernyataan") $type = 47;
							
						    //then, store links etc in db for retrieval later..						    
							$uploadfileDB = new App_Model_Application_DbTable_UploadFile();
						    $doc = array(
												'auf_appl_id' => $formData["transaction_id"],
												'auf_file_name' => $new_filename, 
												'auf_file_type' => $type, 
												'auf_upload_date' => date("Y-m-d h:i:s"),
												'auf_upload_by' => $formData["transaction_id"],
												'pathupload' => $pathupload
											   );
						
							$uploadfileDB->addData($doc);	
							
						}
					}
					
					
					//cek document dah create ke belum
	    			$applicantDocumentDb = new App_Model_Application_DbTable_ApplicantDocument();    		
	    			$docData = $applicantDocumentDb->getData($txnId,49);
	    			
	    			//delete info document lama regenerate baru
	    			$applicantDocumentDb->deleteData($docData["ad_id"]);
	    	
					$this->view->file_path = $this->generateQuitLetter($txnId,$applicant,$txnProgram);
					
					$this->_redirect($this->view->url(array('module'=>'default','controller'=>'quit', 'action'=>'index','msg'=>'success'),'default',true));
				
				}
				catch (Exception $ex)
				{
					echo "Exception!\n";
					echo $ex->getMessage();
				}
    		}
    	}
    	
     }
}

























