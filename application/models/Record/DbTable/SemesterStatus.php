<?php
/*
 *  @author Muhamad Alif
 *  @created Jan 16, 2011
 */
class App_Model_Record_DbTable_SemesterStatus extends Zend_Db_Table_Abstract
{
    protected $_name = 'r021_student_semester_status';
    protected $_primary = 'id';
	
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
	
	public function isRegistered($student_id, $semester_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					->from(array('ss'=>$this->_name))
					->where('ss.student_id = '.$student_id) 
					->where('ss.semester_id  = '.$semester_id)
					->where('ss.semester_status_id = 1');// registered status = 1 from table semester_status;
		
		$stmt = $db->query($select);
	
		$row = $stmt->fetchAll();
		
		if($row){
			return true;	
		}else{
			return false;
		}
	}
	
	
	public function getExamSlipNo($student_id,$semester_id){
		
			$db = Zend_Db_Table::getDefaultAdapter();
			
			$select = $db->select()
						->from(array('ss'=>$this->_name))
						->where('ss.student_id  = '.$student_id)
						->where('ss.semester_id = '.$semester_id);						
			$row = $db->fetchRow($select);
			
			return $row;
	}
	
	
	
	public function updateStudentSemester($data,$id){
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
}