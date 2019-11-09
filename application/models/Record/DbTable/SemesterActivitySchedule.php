<?php
class App_Model_Record_DbTable_SemesterActivitySchedule extends Zend_Db_Table_Abstract
{
    protected $_name = 'semester_activity_schedule';
    protected $_primary = 'sas_id';
	
    
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
	
	
	public function getInfo($condition=null){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from($this->_name);
			
					if($condition!=null){
						if($condition["sem_id"]!=""){
							$select->where("sas_sem_id = '".$condition["sem_id"]."'");
						}
						if($condition["id"]!=""){
							$select->where("sas_id = '".$condition["id"]."'");
						}
					}
					
					
			$row = $db->fetchRow($select);
			return $row;
	}
	
	
}