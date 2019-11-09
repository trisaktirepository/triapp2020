<?php
class Examination_Model_DbTable_StudentMarkRaw extends Zend_Db_Table { //Model Class for Users Details
	
	protected $_name = 'student_mark_raw';
	protected $_primary = 'tmp_id';

	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
    public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function updateData($data,$id){		
		 $this->update($data, "tmp_id = '".(int)$id."'");
	}
	
	
	
}
?>