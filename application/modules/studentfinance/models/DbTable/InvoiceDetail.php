<?php
class Studentfinance_Model_DbTable_InvoiceDetail extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'invoice_detail';
	protected $_primary = "id";
		
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('idtl'=>$this->_name))
					->where("idrl.id ?", (int)$id);

		$row = $db->fetchRow($selectData);				
		return $row;
	}

	public function getInvoiceDetailItem($billing_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('im'=>'invoice_main'))
					->join(array('idtl'=>'invoice_Detail'), 'idtl.invoice_main_id = im.id')
					->where("im.bill_number ?", $billing_id);

		$row = $db->fetchRow($selectData);

		if(!$row){
			return null;
		}else{
			return $row;	
		}
	}
	
	public function getInvoiceDetail($invoice_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('idtl'=>$this->_name))
					->where("idtl.invoice_main_id = ?", $invoice_id);

		$row = $db->fetchAll($selectData);

		if(!$row){
			return null;
		}else{
			return $row;	
		}
	}
	public function updateData($data,$key) {
		$this->update($data,$key);
	}
	
	public function insertData($data) {
		$this->insert($data);
	}
	
	
	public function isIn($invoice_id,$feeitem){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('idtl'=>$this->_name))
		->where("idtl.invoice_main_id = ?", $invoice_id)
		->where('idtl.fi_id=?',$feeitem);
	
		$row = $db->fetchRow($selectData);
	
		if(!$row){
			return null;
		}else{
			return $row;
		}
	}
	
	public function getInvoiceDetailBank($invoice_id,$program,$branch){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('idtl'=>$this->_name),array('amount'))
		->join(array('item'=>'fee_item'),'item.fi_id=idtl.fi_id',array('bni_code','fi_code'))
		->join(array('ia'=>'fee_item_account'),'item.fi_id=ia.fiacc_fee_item',array())
		->join(array('acc'=>'tbl_bank_account'),'ia.fiacc_account=acc.account',array('account_code'))
		->where("idtl.invoice_main_id = ?", $invoice_id)
		->where('ia.fiacc_program_id=?',$program)
		->where('ia.fiacc_branch_id=?',$branch);
	
		$row = $db->fetchAll($selectData);
	
		if(!$row){
			$selectData = $db->select()
			->from(array('idtl'=>$this->_name),array('amount'))
			->join(array('item'=>'fee_item'),'item.fi_id=idtl.fi_id',array('bni_code','fi_code'))
			->join(array('ia'=>'fee_item_account'),'item.fi_id=ia.fiacc_fee_item',array())
			->join(array('acc'=>'tbl_bank_account'),'ia.fiacc_account=acc.account',array('account_code'))
			->where("idtl.invoice_main_id = ?", $invoice_id)
			->where('ia.fiacc_program_id=?',$program)
			->where('ia.fiacc_branch_id is null');
				
			$row = $db->fetchAll($selectData);
	
		}
		//echo $selectData;
		//echo var_dump($row);exit;
		return $row;
			
	}
}
?>