<?php
 
class App_Model_Smsgateway_DbTable_SmsTemplate extends Zend_Db_Table {  
	
	
	protected $_name = 'tbl_sms_message_template';
	protected $_primary ='id';
	
	 
	public function insertData($lobjFormData){
	 
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($this->_name,$lobjFormData);
		$lastInsertId = $this->getAdapter()->lastInsertId();
		return $lastInsertId;
	}
	
	public function updateData($lobjFormData,$id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$where = 'id='.$id;
		$db->update($this->_name,$lobjFormData,$where);
	}
	
	public function getData($id=null) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select= $db->select()
		->from($this->_name);
		if ($id!=null) {
			$select->where('id=?',$id);
			return $db->fetchRow($select);
		}
		return $db->fetchAll($select);
	}
	public function getDataStmt($stmt) {
		$db = Zend_Db_Table::getDefaultAdapter();
		return $db->fetchAll($stmt);
		 
	}
	
	
	 

}

?>