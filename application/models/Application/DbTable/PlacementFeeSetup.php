<?php 

class App_Model_Application_DbTable_PlacementFeeSetup extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'appl_placement_fee_setup';
	protected $_primary = "apfs_id";
	
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
	
	
	public function getFees($condition=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name);		
					  
					  
		 if($condition!=null){
		 	if($condition["type"]!=''){
		 		$select->where("apfs_fee_type = '".$condition["type"]."'");
		 	}
		 	if($condition["value"]!=''){
		 		$select->where("apfs_value = '".$condition["value"]."'");
		 	}
		 	//echo var_dump($condition);exit;
		 	if($condition["aptcode"]!=''){
		 		$select->where("apt_code = '".$condition["aptcode"]."'");
		 	}
		 }

		 //echo $select;exit;
		 $row = $db->fetchRow($select);	
		 return $row;
	}
}
?>