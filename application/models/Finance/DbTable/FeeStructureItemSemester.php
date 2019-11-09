<?php 
class App_Model_Finance_DbTable_FeeStructureItemSemester extends Zend_Db_Table_Abstract {
/**
	 * The default table name 
	 */
	protected $_name = 'fee_structure_item_semester';
	protected $_primary = "fsis_id";
		
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fsis'=>$this->_name))
					->where("fsis.fsis_id = '".$id."'");
			
		$row = $db->fetchRow($selectData);				
		return $row;
	}
	
	public function getStructureItemData($fee_structure_item_id, $sem=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fsis'=>$this->_name))
					->where("fsis.fsis_item_id = '".$fee_structure_item_id."'");
					
		if($sem!=null){
			$selectData->where('fsis.fsis_semester =?', $sem);
			
			$row = $db->fetchRow($selectData);
		}else{
			$row = $db->fetchAll($selectData);	
		}

		if($row){
			return $row;
		}else{
			return null;
		}	
	}
		
}

?>