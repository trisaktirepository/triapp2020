<?php 
class App_Model_General_DbTable_Setup extends Zend_Db_Table_Abstract
{
    protected $_name = 'sis_setup_head';
	protected $_primary = "ssh_id";
	protected $_subname = 'sis_setup_detl';
	protected $_subprimary = "ssd_id";
	
	
	public function getData($code=''){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		
        $select = $db->select()
                 ->from($this->_subname);
                 
                 if($code!=''){
                	 $select->where("ssd_code ='".$code."'") ;  
                 }         
               
				                     
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        return $row;
	}
	
	public function getDataById($code=''){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
	
		$select = $db->select()
		->from($this->_subname);
		 
		if($code!=''){
			$select->where("ssd_id ='".$code."'") ;
		}
		 
		 
		$row = $db->fetchRow($select);
		//$row = $stmt->fetchRow();
		return $row;
	}
}

?>