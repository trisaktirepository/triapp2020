<?php 
class App_Model_General_DbTable_SisSetupDetail extends Zend_Db_Table_Abstract
{
	protected $_name = 'sis_setup_detl';
	protected $_primary = "ssd_id";
	
	
	public function getData($ssd_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where($this->_primary. ' = ?', $ssd_id);
					  
		 $row = $db->fetchRow($select);	
		
		 if($row){
		 	return $row;
		 }else{
		 	return false;
		 }
	}
	
	public function getDataList($ssd_code){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where('ssd_code = ?', $ssd_code);
					  
		 $row = $db->fetchAll($select);	
		
		 if($row){
		 	return $row;
		 }else{
		 	return false;
		 }
	}
}
?>