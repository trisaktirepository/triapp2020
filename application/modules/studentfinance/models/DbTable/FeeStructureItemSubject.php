<?php
class Studentfinance_Model_DbTable_FeeStructureItemSubject extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fee_structure_item_subject';
	protected $_primary = "fsisub_id";
	
	public function getStructureItemData($fee_structure_item_id){
	  $db = Zend_Db_Table::getDefaultAdapter();
	  $selectData = $db->select()
	  ->from(array('fsisub'=>$this->_name))
	  ->join(array('sm'=>'tbl_subjectmaster'), 'sm.IdSubject = fsisub.fsisub_subject_id')
	  ->where("fsisub.fsisub_fsi_id = '".$fee_structure_item_id."'");
	  	
	  $row = $db->fetchAll($selectData);
	  
	
	  if($row){
	    return $row;
	  }else{
	    return null;
	  }
	}
	
}

