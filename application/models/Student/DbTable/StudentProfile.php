<?php 

class App_Model_Student_DbTable_StudentProfile extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'student_profile';
	protected $_primary = "appl_id";
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	public function updateData($data,$id){
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	public function getData($id=""){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name);
					  
		if($id)	{			
			 $select->where("appl_id ='".$id."'");
			 $row = $db->fetchRow($select);	
			 
		}	else{			
			$row = $db->fetchAll($select);	
		}	  
		
		 return $row;
	}
	
}

?>