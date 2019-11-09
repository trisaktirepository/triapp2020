<?php

class App_Model_Exam_DbTable_StudentCourseRegistration extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'r024_student_course_registration';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is no Student Course Registration");
		}
			
		return $row->toArray();
	}
	
	public function getByCourseSemester($semester_id,$course_id){
		
		$select = $this->select()
					   ->from($this->_name);
					   
		if($semester_id)$select->where("semester_id =".$semester_id);
		if($course_id)  $select->where("course_id =".$course_id);
		
			$rowSet = $this->fetchAll($select);
		return		$rowSet;
	}
	
    public function updateMark($data,$id){	
    
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
}