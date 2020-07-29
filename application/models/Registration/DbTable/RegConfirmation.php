<?php 

class App_Model_Registration_DbTable_RegConfirmation extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_reg_confirmation';
	protected $_primary = "id_rc";
	
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	function getData($trx){
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$sel = $db->select()
		->from(array('at'=>$this->_name))
		->where('at.transaction_id=?',$trx);
		$row=$db->fetchRow($sel);
		return $row;
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
		->where('a.transaction_id=?',$trx) ;
		return $db->fetchRow($select);
		
	}
	 
	  
	
	
}
?>