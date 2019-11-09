<?php 
class App_Model_Application_DbTable_ApplicantPinBank extends Zend_Db_Table_Abstract
{
    protected $_name = 'appl_pin_to_bank';
	protected $_primary = "billing_no";
	
	public function getData(){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
			$select=$db->select()
			        ->from($this->_name)
					->where("status='E'");	//	entry yg belum pakai								
       
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
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	
	
	
	
	
}
?>