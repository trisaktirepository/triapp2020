<?php

class ChangeProgramController extends Zend_Controller_Action {
	
	public function init(){
		
	}
	
	public function indexAction()
    {
    	
    	$auth = Zend_Auth::getInstance();
    	
    	$this->view->title=$this->view->translate("Change Program");
    	
    	$msg = $this->_getParam('msg',null);
    	if($msg) $this->view->noticeSuccess=$this->view->translate("Your application has been submitted.");
    	
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;    	
    	
        $changeProgramDB  =  new App_Model_Application_DbTable_ApplicantChangeProgram();
        $list =     $changeProgramDB->getListChangeProgram($appl_id); 
        $this->view->list = $list;
        
        //to check ada x program yg boleh di apply utk change program
        $txnDb = new App_Model_Application_DbTable_ApplicantTransaction();	
        $list_program = $txnDb->getPaidAndOfferChangeProgram($auth->getIdentity()->appl_id);
        $this->view->list_program = $list_program;
       
        
        if(count($list_program)==0){
        	$this->view->noticeError = $this->view->translate("There are currently no programs available for Change Program");
        }
        
    }
    
    
    public function applyAction(){
    	
    	$this->view->title=$this->view->translate("Apply Change Program");
    	
    	//create new transaction record
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;
   	    	
    	$form = new App_Form_ChangeProgram();
    	$this->view->form = $form;
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    			
			if ($form->isValid($formData)) {

				$db = Zend_Db_Table::getDefaultAdapter();
				$db->beginTransaction();
				
				try {
					
					//add change program info
					$chg_program["acp_appl_id"]=$auth->getIdentity()->appl_id;
		    		$chg_program["acp_trans_id_from"]=$formData["acp_trans_id_from"];
		    		$chg_program["acp_trans_id_to"]=$formData["acp_trans_id_to"];	    	
		    		$chg_program["acp_createddt"]=date("Y-m-d H:i:s");
		    		$chg_program["acp_createdby"]=$auth->getIdentity()->appl_id;
		    	
		    		$changeProgramDb = new App_Model_Application_DbTable_ApplicantChangeProgram();
		    		$chg_prog_id = $changeProgramDb->addData($chg_program);	    		
		    		
		    		$move["at_move_id"]=$chg_prog_id;
		    		$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();      	
		    	    $transactionDb->updateData($move,$formData["acp_trans_id_from"]);
		    	    
		    	    /*
		    	     * update applicant account
		    	     */
		    	    
		    	    //get (from) transaction data
		    	    $txnData = $transactionDb->getTransactionData($formData["acp_trans_id_from"]);
		    	    
		    	    //get program offered
		    	    $applicantProgramDb = new App_Model_Application_DbTable_ApplicantProgram();
		    	    $programOffered = $applicantProgramDb->getProgramOffered($txnData['at_trans_id'],$txnData['at_appl_type']);
		    	    
		    	    //get invoice from payee id
		    	    $invoiceMainDb = new Studentfinance_Model_DbTable_InvoiceMain();
		    	    $invoiceList = $invoiceMainDb->getIssuedInvoiceData($txnData['at_pes_id'],$programOffered['program_code']);
	 
		    	    /*
		    	     * Run Finance related process
		    	    */
		    	    $fn_change_program = new icampus_Function_Studentfinance_ChangeProgram();
		    	    
		    	    $fn_change_program->changeProgram($formData["acp_trans_id_from"], $formData["acp_trans_id_to"]);
	    	    	
		    	    $db->commit();
				} catch (Exception $e){
					
				    $db->rollBack();
				    echo $e->getMessage();
				    exit;
				} 
			}
	    	    
	    	    
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'change-program', 'action'=>'index','msg'=>'success'),'default',true));
		}
	}
    	
    
    
    public function ajaxGetChangeProgramAction(){
    	
    	$auth = Zend_Auth::getInstance();
    	
    	$txn_id = $this->_getParam('txn_id', 0);
    	
        $this->_helper->layout->disableLayout();        
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
        $txnDb = new App_Model_Application_DbTable_ApplicantTransaction();
        $row = $txnDb->getOfferChangeProgram($auth->getIdentity()->appl_id,$txn_id);        
        
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($row);
		
		/*if($row){
        	$json = Zend_Json::encode($row);
        }else{
        	$json = null;
        }*/
		
		echo $json;
		exit();
    }
    
     public function downloadAction(){
     	    	
    	    $acp_id = $this->_getParam('acp_id', 0);
    	        	    
    	    $changeProgramDb = new App_Model_Application_DbTable_ApplicantChangeProgram();
    	    $cp = $changeProgramDb->getData($acp_id);
    	    
    	     //get document
    	    $documentDB = new App_Model_Application_DbTable_ApplicantDocument();
    	    $document = $documentDB->getData($cp["acp_trans_id_from"],51);
    	    
    	    if($document){
    	    		$this->view->file_path = DOCUMENT_PATH.DIRECTORY_SEPARATOR.$document["ad_filepath"].DIRECTORY_SEPARATOR.$document["ad_filename"];
    	    }else{
    	    	
    	    	 	$applicantDb = new App_Model_Application_DbTable_ApplicantProfile();
		    	    $applicant = $applicantDb->getData($cp["acp_appl_id"]);
		    	    
		    	    $transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
		    	    $txn_asal = $transactionDb->getTransaction($cp["acp_trans_id_from"]);
		    	    $txn_tujuan = $transactionDb->getTransaction($cp["acp_trans_id_to"]);
			    
			    	$applicantProgramDb = new App_Model_Application_DbTable_ApplicantProgram();
			    	$program_asal   = $applicantProgramDb->getProgramOffered($cp["acp_trans_id_from"],$txn_asal["at_appl_type"]);
			    	$program_tujuan = $applicantProgramDb->getProgramOffered($cp["acp_trans_id_to"],$txn_tujuan["at_appl_type"]);
			    
			    	//get rank asal
			    	if($txn_asal["at_appl_type"]==1){
			    		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessmentUsm();
			    		$rank = $assessmentDb->getInfo($cp["acp_trans_id_from"]);
			    		$rank_asal  = $rank["aau_rector_ranking"];
			    	}elseif($txn_asal["at_appl_type"]==2){
			    		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessment();
			    		$rank = $assessmentDb->getInfo($cp["acp_trans_id_from"]);
			    		$rank_asal  = $rank["aar_rating_rector"];
			    	}
			    	
		     		//get rank tujuan
			    	if($txn_tujuan["at_appl_type"]==1){
			    		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessmentUsm();
			    		$rank2 = $assessmentDb->getInfo($cp["acp_trans_id_to"]);
			    		$rank_tujuan  = $rank2["aau_rector_ranking"];
			    	}elseif($txn_tujuan["at_appl_type"]==2){
			    		$assessmentDb = new App_Model_Application_DbTable_ApplicantAssessment();
			    		$rank2 = $assessmentDb->getInfo($cp["acp_trans_id_to"]);
			    		$rank_tujuan  = $rank2["aar_rating_rector"];
			    	}
		    	
			    	
			    	//get paymemt info
			    	$paymentDb = new App_Model_Finance_DbTable_PaymentMain();
			    	$payment_asal = $paymentDb->getApplicantPaymentTotalAmount($txn_asal["at_pes_id"]);
			    	$payment_tujuan = $paymentDb->getApplicantPaymentTotalAmount($txn_tujuan["at_pes_id"]);
		    	    
			    	setlocale(LC_ALL, 'id_ID');
		
		    		$fieldValues = array (
		    		         '$[Academicyear]'  => $txn_tujuan["ay_code"],
		    				 '$[Applydate]'  => strftime('%e %B %Y',strtotime($cp["acp_createddt"])),
		    				
		    				 '$[Faculty]'  => $program_asal["faculty_indonesia"],				    
							 '$[Programme]' => $program_asal["program_name_indonesia"],
						     '$[Applicantid]' => $txn_asal["at_pes_id"], 
		    		         '$[Rank]' => $rank_asal, 
						 	 '$[Applicantname]' => $applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
						     '$[Address1]' => $applicant["appl_address1"],
						     '$[Address2]' => $applicant["appl_address2"],	
		    				 '$[Payment]' => money_format('%i', $payment_asal),
		
		    				 '$[FacultyTujuan]'  => $program_tujuan["faculty_indonesia"],				    
							 '$[ProgrammeTujuan]' => $program_tujuan["program_name_indonesia"],
						     '$[ApplicantidTujuan]' => $txn_tujuan["at_pes_id"], 
		    		         '$[RankTujuan]' => $rank_tujuan, 
						 	 '$[ApplicantnameTujuan]' => $applicant["appl_fname"].' '.$applicant["appl_mname"].' '.$applicant["appl_lname"],
						     '$[Address1Tujuan]' => $applicant["appl_address1"],
						     '$[Address2Tujuan]' => $applicant["appl_address2"],	
		    				 '$[PaymentTujuan]' => money_format('%i ', $payment_tujuan),
		    		         '$[Transfer]' => money_format('%i', $payment_asal)	
							 					
						);		    	
						
							
		    			$monthyearfolder=date("mY");
		    			
				    	//directory to locate file
						$app_directory_path = DOCUMENT_PATH."/applicant/".$monthyearfolder;	
		
						//create directory to locate file			
						if (!is_dir($app_directory_path)) {
						    mkdir($app_directory_path, 0775);
						}
						
						$output_directory_path = DOCUMENT_PATH."/applicant/".$monthyearfolder."/".$cp["acp_trans_id_from"];
							
		    			//create directory to locate file			
						if (!is_dir($output_directory_path)) {
						    mkdir($output_directory_path, 0775);
						}				
														
						//$location_path
					    $location_path = "applicant/".$monthyearfolder."/".$cp["acp_trans_id_from"];								
						
						//filename
						$output_filename = $acp_id."_change_program.pdf";
						
						try{
							require_once 'dompdf_config.inc.php';
							
							$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
							$autoloader->pushAutoloader('DOMPDF_autoload');
							
							//template path	 
							$html_template_path = DOCUMENT_PATH."/template/changeprogram.html";
							
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
						$doc["ad_appl_id"]=$cp["acp_trans_id_from"];
						$doc["ad_type"]=51; //CHANGE PROGRAM
						$doc["ad_filepath"]=$location_path;
						$doc["ad_filename"]=$output_filename;
						$doc["ad_createddt"]=date("Y-m-d");
						$documentDB->addData($doc);	
		    	
						$this->view->file_path = DOCUMENT_PATH.DIRECTORY_SEPARATOR.$location_path.DIRECTORY_SEPARATOR.$output_filename;
    	    }//end if
    	    
     }
     
     
    function testAction(){
    	
    	 		//get (from) transaction data
    	 		$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction(); 
	    	    ///$txnData = $transactionDb->getTransactionData(3129);
    	
     			//get program offered
	    	    $applicantProgramDb = new App_Model_Application_DbTable_ApplicantProgram();
	    	    $programOffered = $applicantProgramDb->getProgramOffered($txnData['at_trans_id'],$txnData['at_appl_type']);
	    	    
	    	
	    	    //get invoice from payee id
	    	    $invoiceMainDb = new Studentfinance_Model_DbTable_InvoiceMain();
	    	    $invoiceList = $invoiceMainDb->getIssuedInvoiceData($txnData['at_pes_id'],$programOffered['program_code']);

	    	    echo '<pre>';
	    	    print_r($invoiceList);
	    	    echo '</pre>';
	    	    
	    	    //issue credit note & advance payment, new invoice and advance payment knockoff
	    	    if($invoiceList){
	    	    	$db = Zend_Db_Table::getDefaultAdapter();
	    	    	$db->beginTransaction();
	    	   		try {
		    	    	$creditNoteDb = new Studentfinance_Model_DbTable_CreditNote();
		    	    	
		    	    	foreach ($invoiceList as $invoice){
		    	    		$data = array(
		    	    			'cn_billing_no' => $invoice['bill_number'],
		    	    			'cn_fomulir' => $invoice['no_fomulir'],
		    	    			'appl_id' => $invoice['appl_id'],
		    	    			'cn_amount' => $invoice['bill_amount'],
		    	    			'cn_description' => 'Change Program',
		    	    			'cn_creator' => -1,
		    	    			'cn_approver' => -1,
		    	    			'cn_approve_date' => date('Y-m-d H:i:s')
		    	    		);
		    	    		
		    	    	
		    	    		 echo '<pre>';
				    	    print_r($data);
				    	    echo '</pre>';
		    	    		$creditNoteDb->insert($data);
		    	    		
		    	    		
				    	    //get payment on invoice
				    	    $paymentMainDb = new Studentfinance_Model_DbTable_PaymentMain();
				    	    $payment = $paymentMainDb->getInvoicePaymentRecord($invoice['bill_number'], $invoice['no_fomulir']);
				    	   
				    	    if($payment!=null){
					    	    //transfer payment to advance payment
					    	    $advPaymentDb = new Studentfinance_Model_DbTable_AdvancePayment();
					    	    $data2 = array(
					    	    	'advpy_appl_id' => $invoice['appl_id'],
					    	    	'advpy_acad_year_id' => $invoice['academic_year'],
					    	    	'advpy_sem_id' => $invoice['semester'],
					    	    	'advpy_prog_code' => $invoice['program_code'],
					    	    	'advpy_fomulir' => $invoice['no_fomulir'],
					    	    	'advpy_description' => 'Change Program Payment Transfer',
					    	    	'advpy_amount' => $payment['amount'],
					    	    	'advpy_total_paid' => 0,
					    	    	'advpy_total_balance' => $payment['amount'],
					    	    	'advpy_status' => 'A',
					    	    	'advpy_creator' => -1
					    	    );
					    	    
					    	     echo '<pre>';
				    	    print_r($data2);
				    	    echo '</pre>';
					    	    $advPaymentDb->insert($data2);
				    	    }
		    	    	}

		    	    	//get old program packet
		    	    	$old_program_packet = $invoiceList[0];
		    	    	
		    	    	//new program billnumber
		    	    	$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
		    	    //	$newTxn = $transactionDb->getTransactionData(6961);
		    	    	
		    	    	$new_bill = substr($old_program_packet['bill_number'],0,1).substr($old_program_packet['bill_number'],1,1).$newTxn['at_pes_id'];
		    	    	
		    	    	//bill applicant with new program invoice
		    	    	$invoiceMainDb = new Studentfinance_Model_DbTable_InvoiceMain();
		    	    	$invoiceMainDb->generateApplicantInvoice($newTxn['at_pes_id'], $new_bill, 0);
		    	    	
		    	    	//knockoff using advance payment done in generate new invoice
	    	   			
		    	    	
		    	    	$db->commit();
				    	    
	    	    	}catch (exception $e) {
					    $db->rollback();
					    echo "<pre>";
					    echo $e->getMessage();
						print_r($e->getTrace());
						echo "</pre>";
						exit;
					}
	    	    	
	    	    }
	    	    
	    	    exit;
    }
}

























