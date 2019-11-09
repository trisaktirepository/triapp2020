<?php 

class Examination_Model_DbTable_StudentMarkHistory extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_student_mark_history';
	protected $_primary = 'smh_id';
	protected $_item_name = 'tbl_student_item_mark_history';
	protected $_item_primary = 'simh_id';
	
		
	public function getData($id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
					  ->from(array('sa'=>$this->_name))		
					  ->where("id = ?",$id);
		
		//echo $select;
		$row = $db->fetchRow($select);
		
		return $row;
		
	}
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function addItemData($data){
		 $db = Zend_Db_Table::getDefaultAdapter();
		 $id = $db->insert($this->_item_name, $data);
		 return $id;
	}	
	
}

?>