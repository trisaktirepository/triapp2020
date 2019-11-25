<?php
class Studentfinance_Model_DbTable_PaymentMain extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'payment_main';
	protected $_primary = "id";
		
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('pm'=>$this->_name))
					->where("pm.id ?", (int)$id);

		$row = $db->fetchRow($selectData);				
		return $row;
	}
	
	public function getBankTransactionRecord($bank_transaction_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('pm'=>$this->_name))
					->join(array('pbrd'=>'payment_bank_record_detail'), 'pbrd.id = pm.transaction_reference')
					->where("pbrd.pbr_id = ?", (int)$bank_transaction_id);
		
		$row = $db->fetchAll($selectData);	

		if(!$row){
			return null;
		}else{
			return $row;
		}
	}
	
	public function getInvoicePaymentRecord($billing_no, $payer){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('pm'=>$this->_name))
					->where("pm.billing_no = ?", $billing_no)
					->where("pm.payer = ?", $payer);
		
		$row = $db->fetchRow($selectData);	

		if(!$row){
			return null;
		}else{
			return $row;
		}
	}
	
	/**
	 * 
	 * Save Bank transaction to main payment record and detail payment record
	 * @param array $data_bank_detail
	 * @throws Exception
	 */
	public function saveBankPaymentTransaction($data_bank_detail, $payment_detail_id_ref){
		try {
			
			//get transaction data
			$transaction = $this->getTransaction($data_bank_detail['payee_id']);

			
			$data = array(
	       		'billing_no' => $data_bank_detail['billing_no'],
				'payer' => $transaction['at_pes_id'],
	       		'appl_id' => $transaction['at_appl_id'],
	       		'payment_description' => $data_bank_detail['bill_ref_5'],
	       		'amount' => $data_bank_detail['amount_total'],
	       		'payment_mode' => 'SPC-BNI',
	       		'transaction_reference' => $payment_detail_id_ref,
				'payment_date' => date('Y-m-d', strtotime($data_bank_detail['paid_date']))
	       	);
	       		       		
	       	$payment_id = $this->insert($data);
	       		
	       	/*
	       	 * save payment record detail
	       	 */
	       	
	       	//get invoice detail item for payment detail description as we set in proforma invoice
	       	/*$feeItemDb = new Studentfinance_Model_DbTable_FeeItem();
	        $invoice_detail = $feeItemDb->getActiveFeeItem();
	        
	       	$paymentDetailDb = new Studentfinance_Model_DbTable_PaymentDetail();	
	       	for($i=0; $i<10; $i++){
	       		
	       		if( isset($data_bank_detail['amount_'.($i+1)]) && $data_bank_detail['amount_'.($i+1)]!=0 && $data_bank_detail['amount_'.($i+1)]!= null ){
		       		$data = array(
		       			'pm_id' => $payment_id,
		       			'item_description' => $invoice_detail[$i]['fi_name_bahasa'],
		       			'item_amount' => $data_bank_detail['amount_'.($i+1)]
		       		);
		       			
		       		$paymentDetailDb->insert($data);
	       		}
	       	}*/
		}catch (Exception $e) {
			echo $e->getMessage();
		    throw new Exception('Error in Payment Main');
		}
	}

	/*
	 * Overite Insert function
	 */
	
	public function insert($data){
		
		if( !isset($data['payment_date']) ){
			$data['payment_date'] = date('Y-m-d H:i:s');
		}
			
		return $this->insert( $data );
	}
	
	private function getTransaction($payer_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		//get application type
		$selectData = $db->select()
					->from(array('at'=>'applicant_transaction'))
					->where("at.at_pes_id = ?", $payer_id);
			
		$txn_row = $db->fetchRow($selectData);			

		if(!$txn_row){
			return null;
		}else{
			return $txn_row;
		}
	}
	
	public function getApplicantPaymentInfo($payer){
    	$db = Zend_Db_Table::getDefaultAdapter();
        $select = $db ->select()
				->from(array('pm'=>'payment_main'))
				->join(array('pi'=>'applicant_proforma_invoice'),'pm.billing_no = pi.billing_no')
				->where("pi.payee_id ='".$payer."'");

				
		$row = $db->fetchAll($select);
		
		if(!$row){
			$select = $db ->select()
			->from(array('pm'=>'applicant_transaction'))
			->where('pm.at_pes_id=?',$payer);
			$txn = $db->fetchRow($select);
			
			$select = $db ->select()
			->from(array('pm'=>'invoice_main'))
			//->join(array('pi'=>'invoice_main'),'pm.billing_no = pi.bill_number')
			->where("pm.no_fomulir ='".$payer."'")
			->where("pm.bill_paid>500000 or bill_balance=0");
			$row = $db->fetchAll($select);
			if ($row) return $row;
			else return null;
		}else{
			return $row;
		}
		
	}
	
	public function getApplicantPaymentTotalAmount($payer){
    	$db = Zend_Db_Table::getDefaultAdapter();
       	$select = $db ->select()
				->from(array('im'=>'invoice_main'),array('sum_paid'=>'sum(im.bill_paid)'))
				->join(array('pi'=>'applicant_proforma_invoice'),'im.bill_number = pi.billing_no', array())
				->where("pi.payee_id ='".$payer."'");
                                                
		$row = $db->fetchRow($select);
		
		if(!$row){
			return 0;
		}else{
			$total = $row['sum_paid'];
			
			return $total;
		}
		
	}
	public function isOutStanding($applid,$semester){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db ->select()
		->from(array('im'=>'invoice_main'))
		->where("im.appl_id ='".$applid."'")
		->where("im.semester='".$semester."' or im.semester is null")
		->where('im.bill_balance >0');
	
		$row = $db->fetchRow($select);
		return $row;
	}
	

}
?>