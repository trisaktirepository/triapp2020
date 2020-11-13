<?php
class Examapplicant_Model_DbTable_Simulasi extends Zend_Db_Table_Abstract
{
	
    protected $_name = "tbl_applicant_simulation";
    protected $_primary="id";
    
     
     
   
    
    public function addData($data){				
		$id = $this->insert($data);
		return $id;
	}
	
	public function updateData($data,$id){		
		$this->_db->update($this->_name,$data,$this->_primary .' = ' . (int)$id);
	}
	
	public function deleteData($id){		
		$this->_db->delete($this->_name,$this->_primary . ' = ' . (int)$id);
	}
	
	public function  getData($id=null) {
		
		$select=$this->_db->select()
			->from(array('b'=>$this->_name))
			->where('b.appl_id ='.$id);
			return $this->_db->fetchRow($select);
		 
	}
	 
	
	
}
?>
