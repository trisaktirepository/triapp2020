<?php 

class App_Model_Activity_DbTable_ActivityGroupSchedule extends Zend_Db_Table_Abstract {
	
	protected $_name = 'activity_group_schedule';
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
					  ->join(array('cg'=>'tbl_activity_tagging_group'),'cg.IdCourseTaggingGroup=a.idGroup',array('GroupName','IdLecturer'))
					  ->where('idGroup = ?',$idGroup);	
		 				  
		 $row = $db->fetchAll($select);	
		// echo $select;exit;
		 return $row;
	}
	
	public function getAllLecturer($idGroup,$login=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array("a"=>'tbl_activity_tagging_group'))
		->joinLeft(array("b"=>"tbl_staffmaster"),'b.IdStaff=a.IdLecturer',array('FullName','FirstName','SecondName','ThirdName','Email','StaffId'))
		->join(array('u'=>'tbl_user'),'u.IdStaff=b.IdStaff',array('user'=>'loginName','password'=>'passwd_b'))
		->where('IdCourseTaggingGroup = ?',$idGroup);
		if ($login!=null) $select->where('u.loginName=?',$login);
		$coordinator = $db->fetchAll($select);
		
		foreach ($coordinator as $value) {
			if ($value['password']=='') $value['password']=$value['StaffId'].substr($value['FullName'],1,2).'#';
			$row[]=$value;
		}
		
		$select = $db ->select()
		->from(array("a"=>$this->_name))
		->joinLeft(array("b"=>"tbl_staffmaster"),'b.IdStaff=a.IdLecturer',array('FullName','FirstName','SecondName','ThirdName','Email','StaffId'))
		->join(array('cg'=>'tbl_activity_tagging_group'),'cg.IdCourseTaggingGroup=a.idGroup',array())
		->join(array('u'=>'tbl_user'),'u.IdStaff=b.IdStaff',array('user'=>'loginName','password'=>'passwd_b'))
		->where('idGroup = ?',$idGroup);
		if ($login!=null) $select->where('u.loginName=?',$login);
		$session = $db->fetchAll($select);
		
		foreach ($session as $value) {
			if ($value['password']=='') $value['password']=$value['StaffId'].substr($value['FullName'],1,2).'#';
			$row[]=$value;
		}
		// echo $select;exit;
		return $row;
	}
	
	public function getSchedulePdpt($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->distinct()
		->from(array("a"=>$this->_name),array('IdLecturer'))
		//->join(array("b"=>"tbl_staffmaster"),'b.IdStaff=a.IdLecturer',array('StaffName'=>'CONCAT(FirstName,' ','SecondName','ThirdName'))
		//->join(array('cg'=>'tbl_course_tagging_group'),'cg.IdCourseTaggingGroup=a.idGroup',array('GroupName','IdLecturer'))
		->where('a.idGroup = ?',$idGroup);
			
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
					  ->joinLeft(array('cg'=>'tbl_activity_tagging_group'),'cg.IdCourseTaggingGroup=s.idGroup')
					  ->join(array('c'=>'tbl_definationms'),'c.IdDefinition=cg.IdSubject',array('SubjectName'=>'c.BahasaIndonesia','SubCode','DefinitionCode'))
					   // ->joinLeft(array('c'=>'tbl_subjectmaster'),'c.IdSubject=cg.IdSubject',array('SubjectName','SubCode','subjectMainDefaultLanguage'))
					  ->joinLeft(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=cg.IdSemester',array('semester'=>'SemesterMainDefaultLanguage'))
					 // ->joinLeft(array('l'=>'tbl_staffmaster'),'l.IdStaff=cg.IdLecturer',array('FullName','FirstName','SecondName','ThirdName'))
					  ->joinLeft(array('sf'=>'tbl_staffmaster'),'sf.IdStaff=s.IdLecturer',array('sFullName'=>'FullName'))
					  ->where('sc_id = ?',$idSchedule);					  
		 $row = $db->fetchRow($select);	
		 
		 return $row;
	}
	
	public function GetGroupSchedule($std) {
		
		$db = Zend_Db_Table::getDefaultAdapter();
	
		 
		$select = $db ->select()
		->distinct()
		->from(array('s'=>$this->_name),array('sc_date','sc_date_end','sc_day' , 'sc_start_time','sc_end_time'))
		->join(array('cg'=>'tbl_activity_tagging_group'),'cg.IdCourseTaggingGroup=s.idGroup',array('GroupName'))
		->join(array('c'=>'tbl_definationms'),'c.IdDefinition=cg.IdSubject',array('SubjectName'=>'c.BahasaIndonesia','SubCode','DefinitionCode'))
		
		//->join(array('c'=>'tbl_subjectmaster'),'c.IdSubject=cg.IdSubject',array('SubjectName','SubCode','subjectMainDefaultLanguage'))
		//->join(array('srs'=>'tbl_studentregsubjects'),'cg.IdCourseTaggingGroup=srs.IdCourseTaggingGroup',array('srs.IdCourseTaggingGroup'))
		->where('srs.IdStudentRegistration in (?)',$std);
		
		$row = $db->fetchAll($select);
			
		return $row;
	
		//
	}
	
	public function GetGroupScheduleByLecturer($idlec,$idsemester) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
			
		$select = $db ->select()
		//->distinct()
		->from(array('s'=>$this->_name),array('sc_date','sc_date_end','sc_day' , 'sc_start_time','sc_end_time','sc_venue'))
		->join(array('cg'=>'tbl_activity_tagging_group'),'cg.IdCourseTaggingGroup=s.idGroup',array('GroupName'))
		->join(array('c'=>'tbl_definationms'),'c.IdDefinition=cg.IdSubject',array('SubjectName'=>'c.BahasaIndonesia','SubCode','DefinitionCode'))
		->where('cg.IdSemester =?',$idsemester)
		->where('cg.IdLecturer='.$idlec.' or s.IdLecturer='.$idlec);
	
		$row = $db->fetchAll($select);
			
		return $row;
	
		//
	}
	
	public function getScheduleByGroup($groupid) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
			
		$select = $db ->select()
		//->distinct()
		->from(array('s'=>$this->_name) )
		->where('s.idGroup=?',$groupid);
	
		$row = $db->fetchAll($select);
			
		return $row;
	
		//
	}
	
	public function GetGroupScheduleByRoom($idsem,$idroom,$idSchedule=null) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
			
		$select = $db ->select()
		->distinct()
		->from(array('s'=>$this->_name),array('sc_date','sc_date_end','sc_day' , 'sc_start_time','sc_end_time'))
		->join(array('cg'=>'tbl_activity_tagging_group'),'cg.IdCourseTaggingGroup=s.idGroup',array('GroupName'))
		->where('s.sc_venue=?',$idroom)
		->where('cg.IdSemester=?',$idsem);
		if ($idSchedule!=null) $select->where('s.sc_id <> ?',$idSchedule);
		$row = $db->fetchAll($select);
		//echo $select;echo var_dump($row);exit;
		return $row;
	
		//
	}
	
	public function getStudentsScheduleByGroup($idGroup) {
		
		$db = Zend_Db_Table::getDefaultAdapter();
	
		 
		 
				//get klas schedule each student
				$select = $db ->select()
				//->from(array('srs'=>'tbl_studentregsubjects'), array('IdStudentRegistration','IdSemesterMain'),array())
				->from(array('ctg'=>'tbl_activity_tagging_group'),array('GroupCode'))
				->join(array('cs'=>'activity_group_schedule'),'cs.idGroup=ctg.IdCourseTaggingGroup',array('sc_date','sc_day','sc_start_time','sc_end_time'))
				 
				->where('ctg.IdCourseTaggingGroup=?',$idGroup);
				$classes=$db->fetchRow($select);
				 
				return $classes;
		}
		
	 
	 
}