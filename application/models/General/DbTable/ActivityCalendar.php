<?php 
class App_Model_General_DbTable_ActivityCalendar extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_activity_calender';
	protected $_primary = "id";
	
	public function getData($id=0){
		
			$db = Zend_Db_Table::getDefaultAdapter();
		 

	        $select = $db->select()
	                 ->from(array('c'=>$this->_name))
	                 ->where('c.'.$this->_primary.' = ' .$id);
					                     
	        $row = $db->fetchRow($select); 
	        
		
			return $row;
	}
	
	public function getDataBySem($semid,$prog,$idact){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
	
		$select = $db->select()
		->from(array('c'=>$this->_name))
		->where('c.IdSemesterMain=?',$semid)
		->where('c.IdProgram=?',$prog)
		->where('c.idActivity=?',$idact);
		
		$row = $db->fetchRow($select);
		 
	
		return $row;
	}
	
	 
	
	public function addData($data){
		 
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		 
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function getActiveData($idprogram){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
	
		$select = $db->select()
		->from(array('a'=>$this->_name))
		->join(array('b'=>'tbl_activity_calender'),'a.idActivity=b.IdActivity')
		->join(array('c'=>'tbl_semestermaster'),'c.IdSemesterMaster=b.IdSemesterMain')
		->where('a.setter="2"')
		->where('b.StartDate < CURDATE()');
		
		$row = $db->fetchRow($select);
		 
	
		return $row;
	}
    
	
	public function getActiveEvent($idact,$semester=null,$idprogram=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	 	$select = $db->select()
		->from(array('a'=>$this->_name)) 
		->join(array('c'=>'tbl_semestermaster'),'c.IdSemesterMaster=a.IdSemesterMain')
		->where('a.idActivity=?',$idact)
		->where('a.StartDate <= CURDATE()') 
	 	->where('a.EndDate >= CURDATE()');
		if ($idprogram!=null) $select->where('a.IdProgram=?',$idprogram);
		if ($semester!=null) $select->where('a.IdSemesterMain=?',$semester);
		$row = $db->fetchRow($select);
			
	
		return $row;
	}
	
	 
}
?>