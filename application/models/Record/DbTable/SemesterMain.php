<?php
class App_Model_Record_DbTable_SemesterMain extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_semestermaster';
    protected $_primary = 'IdSemesterMaster';
	
    
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from($this->_name)
					->where($this->_name.".".$this->_primary .' = '. $id);
					
				$row = $db->fetchRow($select);
		}else{
			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from($this->_name);
								
			$row = $db->fetchAll($select);
		}
		
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}
	
	
	public function getCurrentSemester(){
			$db = Zend_Db_Table::getDefaultAdapter();
			$curdate = new Zend_Db_Expr('CURDATE()');
    	
    		$select = $db->select()    	    	
    	               ->from($this->_name)
    	   			   ->where("SemesterMainStartDate <= ?", $curdate)  
    	               ->where("SemesterMainEndDate >= ?", $curdate); 
					
			$row = $db->fetchRow($select);
			return $row;
	}
	
	
}