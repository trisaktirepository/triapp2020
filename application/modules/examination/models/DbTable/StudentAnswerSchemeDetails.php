<?php
class Examination_Model_DbTable_StudentAnswerSchemeDetails extends Zend_Db_Table { 

	protected $_name = 'student_ansscheme_detl';
	protected $_primary = 'sad_id';
	
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
	
	public function getDetails($sas_id){
		
		$db =  Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					 ->from($this->_name)
					 ->where("sad_anscheme_id = ?",$sas_id);
		
		$result = $db->fetchAll($select);
	
		return $result;
	}
	
 	public function deleteDetailsData($id){		
	  $this->delete("sad_anscheme_id ='".$id."'");
	}
	
	
}
?>