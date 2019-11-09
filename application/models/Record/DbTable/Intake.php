<?php


class App_Model_Record_DbTable_Intake extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_intake';
	protected $_primary = "IdIntake";
		
	public function getIntake($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is no Intake Information");
		}
			
		return $row->toArray();
	}
	
	
	public function getCurrentIntake(){

		$db = Zend_Db_Table::getDefaultAdapter();
		$curdate = date("Y-m-d");
		
		 $select = $db->select()
				->from($this->_name)
				->where("ApplicationStartDate <= ?", $curdate)  
    	        ->where("ApplicationEndDate >= ?", $curdate);   
			
			$row = $db->fetchRow($select);
				
		if(!$row){
			//throw new Exception("There is No Data");
			return false;
		}
		
		return $row;
	}
	public function getCurrentIntakeAll(){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$curdate = date("Y-m-d");
	
		$select = $db->select()
		->from($this->_name,array('key'=>'IdIntake','value'=>'IntakeId'))
		->where("ApplicationStartDate <= ?", $curdate)
		->where("ApplicationEndDate >= ?", $curdate);
			
		$row = $db->fetchAll($select);
	
		if(!$row){
			//throw new Exception("There is No Data");
			return false;
		}
	
		return $row;
	}
	
	
	
	public function getActiveIntake($intake){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$curdate = date("Y-m-d");
	
		$select = $db->select()
		->from($this->_name)
		->where("ApplicationStartDate <= ?", $curdate)
		->where("ApplicationEndDate >= ?", $curdate)
		->where('IdIntake=?',$intake);
		//echo $select;exit;
		$row = $db->fetchRow($select);
	
		if(!$row){
			//throw new Exception("There is No Data");
			return false;
		}
	
		return $row;
	}
	
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
					->from($this->_name)
					->order('ApplicationStartDate');
								
			$row = $db->fetchAll($select);
		}
		
		
		
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}
	

}

