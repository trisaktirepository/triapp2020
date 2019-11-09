<?php 

class App_Model_Registration_DbTable_CourseGroupSchedule extends Zend_Db_Table_Abstract {
	
	protected $_name = 'course_group_schedule';
	protected $_primary = "sc_id";
	
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	public function getScheduleRow($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from($this->_name)
		->where('idGroup = ?',$idGroup);
	
		$select .= 					" ORDER BY sc_date, CASE sc_day
                                 WHEN 'Monday' THEN 1
                                 WHEN 'Tuesday' THEN 2
                                 WHEN 'Wednesday' THEN 3
                                 WHEN 'Thursday' THEN 4
                                 WHEN 'Friday' THEN 5
                                 WHEN 'Saturday' THEN 6
                                 WHEN 'Sunday' THEN 7
                                 ELSE 8
                                 END ";
	
		$row = $db->fetchRow($select);
			
		return $row;
	}
	public function getSchedule($idGroup){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from($this->_name)
					  
					//  ->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=a.IdSubject')
					  ->where('idGroup = ?',$idGroup);

		$select .= 					" ORDER BY sc_date, CASE sc_day
                                 WHEN 'Monday' THEN 1
                                 WHEN 'Tuesday' THEN 2
                                 WHEN 'Wednesday' THEN 3
                                 WHEN 'Thursday' THEN 4
                                 WHEN 'Friday' THEN 5
                                 WHEN 'Saturday' THEN 6
                                 WHEN 'Sunday' THEN 7
                                 ELSE 8
                                 END ";
		
		 $row = $db->fetchAll($select);	
		 
		 return $row;
	}
	
	public function getData($idSchedule){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where('sc_id = ?',$idSchedule);					  
		 $row = $db->fetchRow($select);	
		 
		 return $row;
	}
	
	
	public function getDetailsInfo($idSchedule){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('s'=>$this->_name))
					  ->joinLeft(array('cg'=>'tbl_course_tagging_group'),'cg.IdCourseTaggingGroup=s.idGroup')
					  ->joinLeft(array('c'=>'tbl_subjectmaster'),'c.IdSubject=cg.IdSubject',array('SubjectName','SubCode','subjectMainDefaultLanguage'))
					  ->joinLeft(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=cg.IdSemester',array('semester'=>'SemesterMainDefaultLanguage'))
					  ->joinLeft(array('l'=>'tbl_staffmaster'),'l.IdStaff=cg.IdLecturer',array('FullName','FirstName','SecondName','ThirdName'))
					  ->where('sc_id = ?',$idSchedule);					  
		 $row = $db->fetchRow($select);	
		 
		 return $row;
	}
	
	
	
	
}