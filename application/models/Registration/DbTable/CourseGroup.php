<?php 

class App_Model_Registration_DbTable_CourseGroup extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_course_tagging_group';
	protected $_primary = "IdCourseTaggingGroup";
	
	
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
	
	public function getCourseTaggingGroupList($idSemester,$idprogram=null,$idsubject=null,$branch=null){
	
		$auth = Zend_Auth::getInstance();
		$db = Zend_Db_Table::getDefaultAdapter();
		$id_user = $auth->getIdentity()->registration_id; 
	
		//get student from assistane group
		
		$select1 = $db ->select()
		->from(array('srs'=>'tbl_studentregsubjects_assistant'),array('IdStudentRegistration','IdSubject','IdSemesterMain'))
		->join(array('cg'=>'tbl_course_tagging_group_assistant'),'cg.IdCourseTaggingGroup=srs.IdCourseTaggingGroup',array())
		->where('cg.IdLecturer=?',$id_user)
		->where('cg.IdSemester=?',$idSemester)
		->group('srs.IdStudentRegistration')
		->group('srs.IdSubject')
		->group('srs.IdSemesterMain');
		
		if ($idprogram!=null) {
			$select1->join(array('p'=>'course_group_program_assistant'),'p.group_id=cg.IdCourseTaggingGroup',array())
					->where('cg.programcreator=?',$idprogram);
		}
		 
		$operator=$db->select()
		->from(array('op'=>'tbl_mark_operator'),array('IdCourseTaggingGroup'))
		->where('op.Entrier=?',$id_user);
		$row=$db->fetchAll($operator);
		
		//check if user is coordinator so display all
		
		$select = $db ->select()
		->from(array('cg'=>$this->_name))
		->join(array('srs'=>'tbl_studentregsubjects'),'cg.IdCourseTaggingGroup=srs.IdCourseTaggingGroup',array())
		->join(array('cgs'=>'course_group_schedule'),'cgs.idGroup=cg.IdCourseTaggingGroup')
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
		->join(array('pr'=>'course_group_program'),'pr.group_id=cg.IdCourseTaggingGroup',array())
		->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','coordinator'=>'FullName','BackSalutation'))
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('IdSubject','SubCode','subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage',"SubjectName"=>'subjectMainDefaultLanguage'))
		->join(array('ass'=>$select1),'ass.IdStudentRegistration=srs.IdStudentRegistration and ass.IdSubject=srs.IdSubject and ass.IdSemesterMain=srs.IdSemesterMain')
		->where('cg.IdSemester = ?',$idSemester)
	//	->where('cg.programcreator = ?',$idprogram)
		->group('cg.IdCourseTaggingGroup');
	
		 
			if ($row) {
				$select->where('(cg.IdLecturer = ?',$id_user);
				$select->orwhere('cgs.IdLecturer = ?',$id_user);
				$select->orwhere('cg.IdCourseTaggingGroup in (?))',$operator);
			}  
	 
	
		if ($idprogram!=null) $select->where('cg.ProgramCreator=?',$idprogram);
		if ($idsubject!=null) $select->where('cg.IdSubject=?',$idsubject);
		//if ($branch!=null) {
		//	$select->joinLeft(array('br'=>'course_group_branch'),'br.group_id=cg.IdCourseTaggingGroup');
		//	$select->where('br.branch_id=?',$branch);
		//}
		//echo $select; 
		$rows = $db->fetchAll($select);
		//echo var_dump($rows);exit;
		return $rows;
	}
	public function getCourseBranch($idCoursetaggingGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
			
		$select = $db ->select()
		->from(array('br'=>'course_group_branch'))
		->join(array('brh'=>'tbl_branchofficevenue'),'br.branch_id=brh.IdBranch')
		->where('br.group_id = ?',$idCoursetaggingGroup);
	
		$rows = $db->fetchAll($select);
		//echo var_dump($rows);exit;
		return $rows;
	}
	public function getGroupList($idSubject,$idSemester,$idprogram,$groupcode=null,$branch=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('cg'=>$this->_name))
					  ->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
					  ->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'))
					  ->joinLeft(array('cgp'=>'course_group_program'),'cgp.group_id=cg.IdCourseTaggingGroup')
					  ->joinLeft(array('owner'=>'tbl_branchofficevenue'),'owner.IdBranch=cg.BranchCreator',array('Owner'=>'BranchName'))
					 // ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage'))
					  ->where('IdSubject = ?',$idSubject)
					  ->where('IdSemester = ?',$idSemester)					  
					  ->where('cgp.program_id = ?',$idprogram);	
		if ($groupcode!='')  $select->where('GroupCode = ?',$groupcode);  
		if ($branch!=null)  {
			$select->joinLeft(array('b'=>'course_group_branch'),'b.group_id=cg.IdCourseTaggingGroup');
			$select->where('branch_id is null or branch_id = ?',$branch);
			 
		}
		//echo $select;echo "<br>";
		$row = $db->fetchAll($select);	
		 return $row;
	}
	
	public function getTotalGroupByCourse($idCourse,$idSemester){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("IdSubject = ?",$idCourse)
					  ->where('IdSemester = ?',$idSemester);					  
		 $row = $db->fetchAll($select);	
		 
		 if($row)
		 	return count($row);
		 else
		 return 0;
	}
	
	public function getInfo($idGroup){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('cg'=>$this->_name))
					  ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage'))
					  ->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'))
					  ->where('IdCourseTaggingGroup = ?',$idGroup);					  
		 $row = $db->fetchRow($select);	
		 return $row;
	}
	
	
	public function getRule($idProgram,$idbranch){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cgr'=>'course_register_rule'))
		->join(array('def'=>'tbl_branchofficevenue'),'def.IdBranch=cgr.branch_std',array('BranchStd'=>'def.BranchName'))
		->join(array('def1'=>'tbl_branchofficevenue'),'def1.IdBranch=cgr.branch_course',array('BranchMng'=>'def1.BranchName'))
		->where('IdProgram = ?',$idProgram)
		->where('branch_std = ?',$idbranch);
		 
		$row = $db->fetchAll($select);
		return $row;
	}
	
	public function isPackage($idProgram,$idbranch){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cgr'=>'course_register_package'))
		->where('IdProgram = ?',$idProgram)
		->where('IdBranch = ?',$idbranch);
		$row = $db->fetchRow($select);
		return $row;
	}
	
	public function getStudentCourseGroup($IdStudentRegistration,$idSemester,$idSubject){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		 $select = $db ->select()		
					  ->from(array('cgsm'=>'tbl_studentregsubjects'))
					  ->join(array('ctg'=>'tbl_course_tagging_group'),'ctg.IdCourseTaggingGroup=cgsm.IdCourseTaggingGroup')
					  ->where('cgsm.IdStudentRegistration = ?',$IdStudentRegistration)
					  ->where('ctg.IdSemester = ?',$idSemester)
					  ->where('ctg.IdSubject = ?',$idSubject);					  
		 $row = $db->fetchRow($select);	
		 
		 return $row;
	}
	
}