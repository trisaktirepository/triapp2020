<?php

class  App_Model_Registration_DbTable_DeferType extends Zend_Db_Table_Abstract {

	protected $_name = 'tbl_record_reason_defer';
	protected $_primary = "IdRecordResonDefer";
	
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
	
	public function getData(){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('a'=>$this->_name));
		$row = $db->fetchAll($select);	
		return $row; 
	}
	
}

?>