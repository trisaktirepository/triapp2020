<?php 

class Accessbni_callbackBniContoller extends REST_Controller {
	
	 public function commitStatusAction() {

	 	$this->_helper->layout()->disableLayout();
	 	//$this->_helper->viewRenderer->setNoRender();
	 	$cid='195';
	 	$bni =new Studentfinance_Model_DbTable_BniHashing();
	 	$dbFinance=new GeneralSetup_Model_DbTable_Bank();
	 	$dbtxt=new Studentfinance_Model_DbTable_TmpTxt();
	 	$bank=$dbFinance->fnGetBankDetails(1);
	 	$secretkey=$bank['secret_key'];
	 	if ($this->_request->isPost()) {
	 		$data=$this->getResponse()->getPost();
	 		if ($data!="[]") {
	 			$dbtxt->add(array('txt'=>var_dump($data)));
	 			$dbtxt->add(array('txt'=>$data));
	 			$dbtxt->add(array('txt'=>"999"));
	 			$result=array('status'=>'002');
	 		} else {
	 			$dbtxt->add(array('txt'=>$data));
	 			$dbtxt->add(array('txt'=>"003"));
	 			$result=array('status'=>'003');
	 		}
	 			
	 			
	 	} else {
	 		$result=array('status'=>'001');
	 		$data = $this->_getParam('callback',null);
	 		$clientid = $this->_getParam('client_id',null);
	 		$dbtxt->add(array('txt'=>$data));
	 		$datapembayaran=$bni->parseData($data, $cid, $secretkey);
	 		$dbtxt->add(array('txt'=>$clientid));
	 	}
	 	
	 	
	 	$json = Zend_Json::encode($result);
	 		
	 	echo $json;
	 	exit;
	 	
	 }
	 
	 public function updatePaymentSlip(){
	 	$this->_helper->layout()->disableLayout();
	 
	 	$invoice_id = $this->_getParam('id', null);
	 	$txn_id = $this->_getParam('txn', null);
	 	$this->view->txn_id = $txn_id;
	 
	 	$this->view->title = $this->view->translate("Payment Slip Processing");
	 
	 	if ($this->_request->isPost()) {
	 			
	 		$formData = $this->_request->getPost();
	 
	 		$db = Zend_Db_Table::getDefaultAdapter();
	 		$db->beginTransaction();
	 			
	 		try {
	 
	 			$paymentMainDb = new Studentfinance_Model_DbTable_PaymentMain();
	 			$invoiceMainDb = new Studentfinance_Model_DbTable_InvoiceMain();
	 			$advancePaymentDb = new Studentfinance_Model_DbTable_AdvancePayment();
	 
	 			//invoice data
	 			$invoice = $invoiceMainDb->getData($formData['invoice_id']);
	 				
	 
	 			//calculate total amount
	 			$tot_amount = 0;
	 			foreach ($formData['amount_paid'] as $amt){
	 				$tot_amount += $amt;
	 			}
	 				
	 			//insert payment main
	 			$data = array(
	 					'billing_no' => $invoice['bill_number'],
	 					'payer' => $invoice['no_fomulir'],
	 					'appl_id' => $invoice['appl_id'],
	 					'IdStudentRegistration' => $invoice['IdStudentRegistration'],
	 					'payment_description' => $invoice['bill_description'],
	 					'amount' => $tot_amount,
	 					'payment_mode' => 'SLIP-BNI',
	 					'slip_transaction_reference' => $formData['transaction_reference'],
	 					'payment_date' => date('Y-m-d', strtotime($formData['payment_date']))
	 			);
	 			$paymentMainId = $paymentMainDb->insert($data);
	 
	 			//check for excess payment
	 			if( $tot_amount > $invoice['bill_balance'] - $invoice['cn_amount']   ){
	 				 
	 				//advance payment
	 				$adv_amount = $tot_amount - $invoice['bill_balance'];
	 				$data = array(
	 						'advpy_appl_id' => $invoice['appl_id'],
	 						'advpy_acad_year_id' => $invoice['academic_year'],
	 						'advpy_sem_id' => $invoice['semester'],
	 						'advpy_prog_code' => $invoice['program_code'],
	 						'advpy_fomulir' => $invoice['no_fomulir'],
	 						'advpy_invoice_no' => $invoice['bill_number'],
	 						'advpy_invoice_id' => $invoice['id'],
	 						'advpy_payment_id' => $paymentMainId,
	 						'advpy_description' => 'Excess Payment for invoice no:'.$invoice['bill_number'],
	 						'advpy_amount' => $adv_amount,
	 						'advpy_total_paid' => 0,
	 						'advpy_total_balance' => $adv_amount,
	 						'advpy_status' => 'A'
	 				);
	 				$advancePaymentDb->insert($data);
	 				 
	 				//update invoice
	 				$amt_paid = $tot_amount - $adv_amount;
	 				$paid = $invoice['bill_paid'] + $amt_paid;
	 				$balance = $invoice['bill_balance'] - $amt_paid;
	 				 
	 				$data = array(
	 						'bill_paid' => $paid,
	 						'bill_balance' => $balance
	 				);
	 				$invoiceMainDb->update($data, 'id = '.$invoice['id']);
	 				 
	 			}else{//no excess payment
	 				 
	 				//update invoice
	 				$amt_paid = $tot_amount;
	 				$paid = $invoice['bill_paid'] + $amt_paid;
	 				$balance = $invoice['bill_balance'] - $amt_paid;
	 				 
	 				$data = array(
	 						'bill_paid' => $paid,
	 						'bill_balance' => $balance
	 				);
	 				$invoiceMainDb->update($data, 'id = '.$invoice['id']);
	 
	 			}
	 
	 			$db->commit();
	 
	 
	 		}catch (Exception $e) {
	 			echo "Error in PaymentController. <br />";
	 			echo $e->getMessage();
	 
	 			echo "<pre>";
	 			print_r($e->getTrace());
	 			echo "</pre>";
	 
	 			$db->rollBack();
	 			 
	 			$status = false;
	 			exit;
	 			 
	 		}
	 			
	 		//redirect
	 		$this->_redirect($this->view->url(array('module'=>'studentfinance','controller'=>'payment-slip', 'action'=>'detail','txn'=>$formData['txn']),'default',true));
	 			
	 	}else{
	 		//invoice
	 		$invoiceMainDb = new Studentfinance_Model_DbTable_InvoiceMain();
	 		$invoice = $invoiceMainDb->getData($invoice_id);
	 			
	 		//invoice detail
	 		$invoiceDetailDb = new Studentfinance_Model_DbTable_InvoiceDetail();
	 		$invoice['detail'] = $invoiceDetailDb->getInvoiceDetail($invoice_id);
	 			
	 		//bill item account number
	 		$feeItemAccountDb = new Studentfinance_Model_DbTable_FeeItemAccount();
	 		foreach ($invoice['detail'] as $index=>$fee_item){
	 			$accountDetail = $feeItemAccountDb->getFacultyData($fee_item['fi_id'],$invoice['college_id']);
	 			$invoice['detail'][$index]['acc_no'] = array(
	 					'bank_id'=>$accountDetail['fiacc_bank'],
	 					'bank_name'=>$accountDetail['bank_name'],
	 					'acc_no'=>$accountDetail['fiacc_account'],
	 			);
	 		}
	 			
	 		$this->view->invoice = $invoice;
	 			
	 		//registration info
	 		if($invoice['IdStudentRegistration']!=null && $invoice['IdStudentRegistration']!=""){
	 				
	 			$studentRegistrationDb = new Registration_Model_DbTable_Studentregistration();
	 			$registration = $studentRegistrationDb->getData($invoice['IdStudentRegistration']);
	 			$this->view->registration_data = $registration;
	 				
	 			//student profile
	 			$studentProfileDb = new Records_Model_DbTable_Studentprofile();
	 			$profile = $studentProfileDb->fnGetStudentProfileByApplicationId($registration['IdApplication']);
	 			$this->view->profile = $profile;
	 				
	 		}else{
	 				
	 			//profile
	 			$profileDb = new App_Model_Application_DbTable_ApplicantProfile();
	 			$profile = $profileDb->getData($invoice['appl_id']);
	 			$this->view->profile = $profile;
	 				
	 		}
	 	}
	 
	 	/*echo "<pre>";
	 		print_r($profile);
	 	echo "</pre>";*/
	 }
}
?>