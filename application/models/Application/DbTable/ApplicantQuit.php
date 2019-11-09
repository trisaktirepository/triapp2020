<?php 

class App_Model_Application_DbTable_ApplicantQuit extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'applicant_quit';
	protected $_primary = "aq_id";
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function getInfo($transaction_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from(array('aq'=>$this->_name))					  
					  ->where("aq.aq_trans_id = '".$transaction_id."'");
									  
		 $row = $db->fetchRow($select);	
		 
		 if($row){
		    return $row;
		 }else{
		 	return null;
		 }
	}
}