<?php 

class Accessbni_CallbackBniController extends REST_Controller {
	
	 public function postAction() {

	 	$this->_helper->layout()->disableLayout();
	 	//$this->_helper->viewRenderer->setNoRender();
	 	$bni =new Studentfinance_Model_DbTable_BniHashing();
	 	$dbFinance=new GeneralSetup_Model_DbTable_Bank();
	 	$dbtxt=new Studentfinance_Model_DbTable_TmpTxt();
	 	$bank=$dbFinance->fnGetBankDetails(1);
	 	$dbVa = new Accessbni_Model_DbTable_VA();
	 	$secretkey=$bank['secret_key'];
	 	if ($this->_request->isPost()) {
	 		$data = $this->_request->getParams();
	 		//$dbtxt->add(array('txt'=>Zend_Json::encode($data)));
	 		//$data=Zend_Json::decode($data);
	 		$dataHashed=$data['data'];
	 		$cid=$data['client_id'];
	 		//$resut=array('status'=>'008');
	 		$data=$bni->parseData($dataHashed, $cid, $secretkey);
	 		$dbtxt->add(array('txt'=>Zend_Json::encode($data)));
	 		
	 		$notif=array("trx_id"=>$data['trx_id'],
	 				"virtual_account"=>$data['virtual_account'],
	 				"customer_name"=>$data['customer_name'],
	 				"trx_amount"=>$data['trx_amount'],
	 				"payment_amount"=>$data['payment_amount'],
	 				"cumulative_payment_amount"=>$data['cumulative_payment_amount'],
	 				"payment_ntb"=>$data['payment_ntb'],
	 				"datetime_payment"=>$data['datetime_payment'],
	 				"datetime_payment_iso8601"=>$data['datetime_payment_iso8601']	
	 			);
	 		if (!$dbVa->isInVA($data['virtual_account'], $data['payment_ntb'], $data['payment_amount'])) {
	 		//	$resut=array('status'=>'009');
	 			$dbVa->insert($notif); 
	 			if ($this->updatePaymentSlip($notif['virtual_account'],$notif['payment_ntb'],$notif['payment_amount'],$notif['datetime_payment']) ) 
	 		 		$result=array('status'=>'000');
	 			else $result=array('status'=>'001');
	 		} else $result=array('status'=>'000');
	 		 
	 	}  
	 	else $result=array('status'=>'001');
	 	
	 	
	 	$json = Zend_Json::encode($result);
	 		
	 	echo $json;
	 	exit;
	 	
	 }
	 public function getAction(){
	 	echo "GET Ok";
	 }
	 
	 public function putAction(){
	 	echo "PUT Ok";
	 }
	 
	 public function updatePaymentSlip($va,$pnb,$amount,$dtpay){
	 	 
	 		$db = Zend_Db_Table::getDefaultAdapter();
	 		$db->beginTransaction();
	 			
	 		try {
	 
	 			$paymentMainDb = new Studentfinance_Model_DbTable_PaymentMain();
	 			$invoiceMainDb = new Studentfinance_Model_DbTable_InvoiceMain();
	 			$advancePaymentDb = new Studentfinance_Model_DbTable_AdvancePayment();
	 			$dbVA=new Accessbni_Model_DbTable_VA();
	 			$dbtxt=new Studentfinance_Model_DbTable_TmpTxt();
	 			//invoice data
	 			
	 			$invoice = $invoiceMainDb->getDataByVA($va);
		 		if ($invoice) {
		 			 //invoice main ok	
		 			$invoiceMainDb->update(array('status_va'=>'PAID','dt_va_update'=>date('Y-m-d h:m:s')), "id=".(int)$invoice['id']);
		 			//get VA payment
		 			$payment=$dbVA->getDataByVA($va,$pnb,$amount,$dtpay);
		 			//$dbtxt->add(array('txt'=>'PaymentSaves='.Zend_Json::encode($payment)));
		 			$tot_amount=$payment['payment_amount'];
		 			
		 			//insert payment main
		 			$data = array(
		 					'billing_no' => $invoice['bill_number'],
		 					'payer' => $invoice['no_fomulir'],
		 					'appl_id' => $invoice['appl_id'],
		 					'IdStudentRegistration' => $invoice['IdStudentRegistration'],
		 					'payment_description' => $invoice['bill_description'],
		 					'amount' => $tot_amount,
		 					'payment_mode' => 'E-COLLECTION',
		 					'slip_transaction_reference' =>$payment['payment_ntb'],
		 					'payment_date' => date('Y-m-d', strtotime($payment['datetime_payment'])),
		 					'va'=>$invoice['va']
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
		 			return true;
		 		} else {
		 			$dbProfrmaInvoice=new Application_Model_DbTable_ProformaInvoiceVa();
		 			$dbProformaInvDetail=new Application_Model_DbTable_ProformaInvoiceDetail();
		 			$dbInvoiceDetail=new Studentfinance_Model_DbTable_InvoiceDetail();
		 			$invoice = $dbProfrmaInvoice->getDataByVa($va);
			 		if ($invoice) {
			 			//proforma invoice test
			 			$noform=$invoice['no_fomulir'];
			 			$billnumber=$invoice['bill_number'];
			 			$paket=substr($billnumber,0,1);
			 			$dbProfrmaInvoice->updateData(array('status_va'=>'PAID','dt_va_update'=>date('Y-m-d h:m:s')), (int)$invoice['id']);
			 			
			 			
			 			//pindahkan proforma invoice
			 			$rowinvoice=$dbProfrmaInvoice->getDataByNoForm($noform, $paket);
			 			if ($rowinvoice) {
			 				foreach ($rowinvoice as $value) {
			 					
			 					$idmain=$value['id'];
			 					unset($value['id']);
			 					if (!$invoiceMainDb->isIn($invoice['bill_number'])) {
			 						$id=$invoiceMainDb->insert($value);
			 						//insert detail
			 						$det=$dbProformaInvDetail->getDataByInvoice($idmain);
			 						foreach ($det as $item) {
			 							$item['invoice_main_id']=$id;
			 							unset($item['id']);
			 							if ($dbInvoiceDetail->isInDetail($item['fi_id'], $id)) 
			 								$dbInvoiceDetail->save($item);
			 						}
			 					}
			 				}
			 			}
			 			//get data from invoice_main
			 			$invoice = $invoiceMainDb->getDataByVA($va);
			 			//get VA payment
			 			$payment=$dbVA->getDataByVA($va,$pnb,$amount,$dtpay);
			 			$tot_amount=$payment['payment_amount'];
			 			//insert payment main
			 			$data = array(
			 					'billing_no' => $invoice['bill_number'],
			 					'payer' => $invoice['no_fomulir'],
			 					'appl_id' => $invoice['appl_id'],
			 					'IdStudentRegistration' => $invoice['IdStudentRegistration'],
			 					'payment_description' => $invoice['bill_description'],
			 					'amount' => $tot_amount,
			 					'payment_mode' => 'E-COLLECTION',
			 					'slip_transaction_reference' =>$payment['payment_ntb'],
			 					'payment_date' => date('Y-m-d', strtotime($payment['datetime_payment'])),
			 					'va'=>$invoice['va']
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
			 			return true;
			 		}
		 		}
	 
	 		 }  catch (Exception $e) {
	 			echo "Error in PaymentController. <br />";
	 			echo $e->getMessage();
	 
	 			echo "<pre>";
	 			print_r($e->getTrace());
	 			echo "</pre>";
	 			
	 			$db->rollBack();
	 			 $dbtxt->add(array('txt'=>var_dump($e->getTrace())));
	 			$status = false;
	 			return false;
	 			 
	 		} 
	 			
	 
	  
	 }
	 
	 
}
?>