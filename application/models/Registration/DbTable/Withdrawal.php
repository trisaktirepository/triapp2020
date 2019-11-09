<?php

class  App_Model_Registration_DbTable_Withdrawal extends Zend_Db_Table_Abstract {

	protected $_name = 'tbl_withdrawal';
	protected $_primary = "w_id";
	
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
	
	public function getInfo($IdStudentRegSubjects){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("IdStudentRegSubjects = ?",$IdStudentRegSubjects);
		$row = $db->fetchRow($select);	
		return $row; 
	}
	
}

?>