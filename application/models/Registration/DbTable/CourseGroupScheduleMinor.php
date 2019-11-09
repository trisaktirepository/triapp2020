<?php 

class App_Model_Registration_DbTable_CourseGroupScheduleMinor extends Zend_Db_Table_Abstract {
	
	protected $_name = 'course_group_schedule_minor';
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
					  ->join(array('cg'=>'tbl_course_tagging_group_minor'),'cg.IdCourseTaggingGroupMinor=a.idGroup',array('GroupName','IdLecturer'))
					  ->where('idGroup = ?',$idGroup);	
		 				  
		 $row = $db->fetchAll($select);	
		// echo $select;exit;
		 return $row;
	}
	
	public function getScheduleByStd($idGroup,$stdid){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array("a"=>$this->_name))
		->joinLeft(array("b"=>"tbl_staffmaster"),'b.IdStaff=a.IdLecturer',array('FullName','FirstName','SecondName','ThirdName'))
		->join(array('cg'=>'tbl_course_tagging_group_minor'),'cg.IdCourseTaggingGroupMinor=a.idGroup',array('GroupName','IdLecturer'))
		->join(array('cgt'=>'course_group_student_minor'),'cgt.idcoursetagginggroupminor=cg.IdCourseTaggingGroupMinor',array())
		->where('idGroup = ?',$idGroup)
		->where('cgt.IdStudentRegistration=?',$stdid);
			
		$row = $db->fetchAll($select);
		// echo $select;exit;
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
					  ->joinLeft(array('cg'=>'tbl_course_tagging_group_minor'),'cg.IdCourseTaggingGroupMinor=s.idGroup')
					  ->joinLeft(array('c'=>'tbl_subjectmaster'),'c.IdSubject=cg.IdSubject',array('SubjectName','SubCode','subjectMainDefaultLanguage'))
					  ->joinLeft(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=cg.IdSemester',array('semester'=>'SemesterMainDefaultLanguage'))
					  ->joinLeft(array('l'=>'tbl_staffmaster'),'l.IdStaff=cg.IdLecturer',array('FullName','FirstName','SecondName','ThirdName'))
					  ->joinLeft(array('sf'=>'tbl_staffmaster'),'sf.IdStaff=s.IdLecturer',array('sFullName'=>'FullName'))
					  ->where('sc_id = ?',$idSchedule);					  
		 $row = $db->fetchRow($select);	
		 
		 return $row;
	}
	
	public function GetGroupSchedule($std) {
		
		$db = Zend_Db_Table::getDefaultAdapter();
	
		 
		$select = $db ->select()
		->distinct()
		->from(array('s'=>$this->_name),array('sc_date','sc_day' , 'sc_start_time','sc_end_time'))
		->join(array('cg'=>'tbl_course_tagging_group'),'cg.IdCourseTaggingGroup=s.idGroup',array('GroupName'))
		->join(array('c'=>'tbl_subjectmaster'),'c.IdSubject=cg.IdSubject',array('SubjectName','SubCode','subjectMainDefaultLanguage'))
		->join(array('srs'=>'tbl_studentregsubjects'),'cg.IdCourseTaggingGroup=srs.IdCourseTaggingGroup',array('srs.IdCourseTaggingGroup'))
		->where('srs.IdStudentRegistration in (?)',$std);
		
		$row = $db->fetchAll($select);
			
		return $row;
	
		//
	}
	
	public function GetGroupScheduleByLecturer($idlec,$idsemester) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
			
		$select = $db ->select()
		//->distinct()
		->from(array('s'=>$this->_name),array('sc_date','sc_day' , 'sc_start_time','sc_end_time','sc_venue'))
		->join(array('cg'=>'tbl_course_tagging_group'),'cg.IdCourseTaggingGroup=s.idGroup',array('GroupName'))
		->join(array('c'=>'tbl_subjectmaster'),'c.IdSubject=cg.IdSubject',array('SubjectName'=>'c.BahasaIndonesia','SubCode','subjectMainDefaultLanguage'))
		->where('cg.IdSemester =?',$idsemester)
		->where('cg.IdLecturer='.$idlec.' or s.IdLecturer='.$idlec);
	
		$row = $db->fetchAll($select);
			
		return $row;
	
		//
	}
	
	public function GetGroupScheduleByRoom($idsem,$idroom) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
			
		$select = $db ->select()
		->distinct()
		->from(array('s'=>$this->_name),array('sc_date','sc_day' , 'sc_start_time','sc_end_time'))
		->join(array('cg'=>'tbl_course_tagging_group'),'cg.IdCourseTaggingGroup=s.idGroup',array('GroupName'))
		->where('s.sc_venue=?',$idroom)
		->where('cg.IdSemester=?',$idsem);
	
		$row = $db->fetchAll($select);
			
		return $row;
	
		//
	}
	
	public function getStudentsScheduleByGroup($idGroup) {
		
		$db = Zend_Db_Table::getDefaultAdapter();
	
		 
		$select = $db ->select()
			->from(array('srs'=>'tbl_studentregsubjects'), array('IdStudentRegistration','IdSemesterMain'))
			->where('srs.IdCourseTaggingGroup=?',$idGroup); 
		$rows=$db->fetchAll($select);
		if ($rows) {
			$allclasses=array();
			foreach ($rows as $item) {
				$idstd=$item['IdStudentRegistration'];
				$idsemester=$item['IdSemesterMain'];
				//get klas schedule each student
				$select = $db ->select()
				->from(array('srs'=>'tbl_studentregsubjects'), array('IdStudentRegistration','IdSemesterMain'),array())
				->join(array('ctg'=>'tbl_course_tagging_group'),'ctg.IdCourseTaggingGroup=srs.IdCourseTaggingGroup',array())
				->join(array('cs'=>'course_group_schedule'),'cd.idGroup=ctg.IdCourseTaggingGroup',array('sc_date','sc_day','sc_start_time','sc_end_time'))
				->where('srs.IdSemesterMain=?',$idsemester)
				->where('srs.IdStudentRegistration=?',$idstd);
				$classes=$db->fetchAll($select);
				if ($classes) {
					foreach ($classes as $value) {
						$allclasses[]=$value;
					}
				}
			}
			$allclasses=array_unique($allclasses);
			
		}
		return $allclasses;
	}
	 
}