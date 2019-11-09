<?php
class Studentfinance_Model_DbTable_AdvancePayment extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'advance_payment';
	protected $_primary = "advpy_id";
		
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('ap'=>$this->_name))
					->where("ap.advpy_id ?", (int)$id);

		$row = $db->fetchRow($selectData);				
		return $row;
	}
	
	

	/*
	 * Overite Insert function
	 */
	
	public function insert($data){
		
		$auth = Zend_Auth::getInstance();
		
		if(!isset($data['advpy_creator'])){
			$data['advpy_creator'] = $auth->getIdentity()->iduser;
		}
		
		$data['advpy_create_date'] = date('Y-m-d H:i:s');
			
		return parent::insert( $data );
	}
	
	public function getApplicantBalanceAvdPayment($appl_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('ap'=>$this->_name))
					->where("ap.advpy_appl_id = ?",$appl_id)
					->where("ap.advpy_total_balance > 0");

		$row = $db->fetchAll($selectData);				
		
		if(!row){
			return null;
		}else{
			return $row;
		}
	}
	
	/*
	 * Get advance payment transfered from invoice paid
	*/
	public function getAdvancePaymentFromInvoice($invoice_id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('ap'=>$this->_name))
		->joinLeft(array('u'=>'tbl_user'), 'u.iduser = ap.advpy_creator', array())
		->joinLeft(array('ts'=>'tbl_staffmaster'), 'ts.IdStaff = u.IdStaff', array('name'=>'FullName'))
		->where("ap.advpy_invoice_id = ?",$invoice_id);
	
		$row = $db->fetchRow($selectData);
	
		if(!$row){
			return null;
		}else{
			return $row;
		}
	}

}
?>