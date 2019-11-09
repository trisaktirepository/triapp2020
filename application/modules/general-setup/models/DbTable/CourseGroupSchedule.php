<?php 

class GeneralSetup_Model_DbTable_CourseGroupSchedule extends Zend_Db_Table_Abstract {
	
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
	
	public function getSchedule($idGroup){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array("a"=>$this->_name))
					  ->joinLeft(array("b"=>"tbl_staffmaster"),'b.IdStaff=a.IdLecturer',array('FullName','FirstName','SecondName','ThirdName'))
					  ->joinLeft(array('cg'=>'tbl_course_tagging_group'),'cg.IdCourseTaggingGroup=a.idGroup',array('*'))
					  ->where('idGroup = ?',$idGroup);					  
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
					  ->joinLeft(array('sf'=>'tbl_staffmaster'),'sf.IdStaff=s.IdLecturer',array('sFullName'=>'FullName'))
					  ->where('sc_id = ?',$idSchedule);					  
		 $row = $db->fetchRow($select);	
		 
		 return $row;
	}
	
	
	
	
}