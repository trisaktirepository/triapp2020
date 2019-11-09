<?php

class App_Model_Record_DbTable_Activity extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_activity';
	protected $_primary = "idActivity";
		
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
	
	public function getActivity($idSemester,$idProgram){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
							->from(array('a'=>$this->_name))							
							->join(array('ac'=>'tbl_activity_calender'),'ac.IdActivity=a.idActivity')
							->where('IdSemesterMain = ?',$idSemester)
							->where('IdProgram = ?',$idProgram)
							->where('a.IdActivity = 31')//withdrawal
							->where('StartDate <= CURDATE()')
							->where('EndDate >= CURDATE()'); 
		
		$row = $db->fetchRow($select);		

		if(!$row){
		
			$select = $db->select()
							->from(array('a'=>$this->_name))							
							->join(array('ac'=>'tbl_activity_calender'),'ac.IdActivity=a.idActivity')
							->where('IdSemesterMain = ?',$idSemester)							
							->where('a.IdActivity = 31') //withdrawal
							->where('StartDate <= CURDATE()')
							->where('EndDate >= CURDATE()'); 
		
			$row = $db->fetchRow($select);		
		
		}
		
		return $row;
	}

}

