<?php 

class App_Model_Application_DbTable_RegConfirmation extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_reg_confirmation';
	protected $_primary = "id_rc";
	
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		// echo var_dump($data);echo $id;exit;
		 $this->update($data, $this->_primary .' = "'. $id.'"');
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function isIn($trx) {
		 
	 
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->where('a.transaction_id=?',$sms) ;
		return $db->fetchRow($select);
		
	}
	 
	  
	
	
}
?>