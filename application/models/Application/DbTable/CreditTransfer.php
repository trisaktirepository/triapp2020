<?php



class App_Model_Application_DbTable_CreditTransfer extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_apply_credit_transfer';
	protected $_primary = "idApply";
		
	public function getDataByTransaction($id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from(array('a'=>$this->_name))
		->join(array('t'=>'applicant_transaction'),'a.transaction_id=t.at_trans_id',array('at_intake'))
		->joinLeft(array('in'=>'tbl_intake'),'in.idintake=t.at_intake',array('IntakeId'))
		->join(array('p'=>'tbl_program'),'a.IdProgram=p.IdProgram',array('programdest'=>"CONCAT(ArabicName,' (',strata,')')"))
		->joinLeft(array('pt'=>'tbl_pt_pdpt'),'pt.id_sp=a.pt_asal',array('ptasal'=>'nm_sp'))
		->joinLeft(array('pr'=>'tbl_sms_pdpt'),'pr.id_sms=a.prodi_asal',array('programasal'=>"nm_lemb","nm_jenjang"))
		->where('transaction_id=?',$id);
		$row=$db->fetchRow($select);
		return $row;
	}
	
	  
	
	public function addData($postData){
		
		$auth = Zend_Auth::getInstance();
		return $this->insert($postData); 
	}
	
	public function updateData($postData,$id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->update($this->_name,$postData, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->delete($this->_name,$this->_primary . ' = ' . (int)$id);
	}
	
	public function isIn($transactionid) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select=$db->select()
			->from($this->_name)
			->where('transaction_id=?',$transactionid);
		$row=$db->fetchRow($select);
		return $row;
	}

}

