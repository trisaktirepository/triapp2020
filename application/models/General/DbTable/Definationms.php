<?php 
class App_Model_General_DbTable_Definationms extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_definationms';
	protected $_primary = "idDefinition";
	
	public function getData($id=0){
		$id = (int)$id;
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                ->from(array('d'=>$this->_name) ) 
	                ->where($this->_primary.' = ' .$id);			                     
        
        $row = $db->fetchRow($select);
		return $row;
		
	}
	
	public function getDataByType($idType=0){
				
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                ->from(array('d'=>$this->_name) ) 
	                ->where("d.idDefType = '".$idType."'");			                     
        
        $row = $db->fetchAll($select);
		return $row;
		
	}
	
	
	
	
}
?>