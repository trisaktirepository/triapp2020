<?php

class App_Model_Record_DbTable_ActivityCalender extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_activity_calender';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Activity");
		}
		
		return $row->toArray();
	}
	
	
	public function getPeriodByActivity($idActivity){

		$db = Zend_Db_Table::getDefaultAdapter();
		$curdate = date("Y-m-d");
		
		 $select = $db->select()
				->from($this->_name)
				->where("idActivity= ?", $idActivity)//online application
				->where("StartDate <= ?", $curdate)  
    	        ->where("EndDate >= ?", $curdate);   
			
			$row = $db->fetchRow($select);
				
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}
	
	public function getActivityDate($idActivity,$idPeriod){

		$db = Zend_Db_Table::getDefaultAdapter();
		$curdate = date("Y-m-d");
		
		 $select = $db->select()
				->from($this->_name)
				->where("idActivity=?", $idActivity )
				->where("IdPeriod=?" , $idPeriod)
				->where("StartDate <= ?", $curdate)  
    	        ->where("EndDate >= ?", $curdate)
    	        ->order("StartDate ASC LIMIT 1");   
    	      
		$row = $db->fetchRow($select);
		
		return $row;
	}
	
	
	public function getNearestActivityDate($idActivity,$idPeriod){

		$db = Zend_Db_Table::getDefaultAdapter();
		$curdate = date("Y-m-d");
		
		 $select = $db->select()
				->from($this->_name)
				->where("idActivity=?", $idActivity )
				->where("IdPeriod=?" , $idPeriod)
				->where("StartDate > ?", $curdate)  
    	        ->order("StartDate ASC LIMIT 1");   
    	     
		$row = $db->fetchRow($select);
				
		
		return $row;
	}
	
	
}

