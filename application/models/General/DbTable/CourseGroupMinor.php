<?php 

class App_Model_General_DbTable_CourseGroupMinor extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_course_tagging_group_minor';
	protected $_primary = "IdCourseTaggingGroupMinor";
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		// echo var_dump($data);echo $id;exit;
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	
	public function getGroupList($idgrup){
		
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('cg'=>$this->_name))
					  ->joinLeft(array('cgs'=>'course_group_student_minor'),'cg.IdCourseTaggingGroupMinor=cgs.IdCourseTaggingGroupMinor',array())
					  ->joinLeft(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=cgs.IdStudentRegistration')
					  ->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
					  ->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'))
					 // ->joinLeft(array('u'=>'tbl_user'),'u.iduser=cg.creator',array('owner'=>'loginName'))
					  ->where('cg.group_id = ?',$idgrup)
					  ->group('cg.IdCourseTaggingGroupMinor') ;
		
		// echo $select;exit;
		 $row = $db->fetchAll($select);	
		// echo var_dump($row);exit;
		 return $row;
	}
	
	
	public function getGroupListMinor($idgrup,$idlect){
	
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cg'=>$this->_name),array('GroupName','ProgramNameShort'=>'GroupCode','IdCourseTaggingGroup'=>'IdCourseTaggingGroupMinor','Bcode'=>'IdCourseTaggingGroupMinor'))
		->joinLeft(array('cgs'=>'course_group_student_minor'),'cg.IdCourseTaggingGroupMinor=cgs.IdCourseTaggingGroupMinor',array())
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=cgs.IdStudentRegistration',array())
		//->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
		->joinLeft(array('stm'=>'tbl_mark_operator_detail'),'stm.IdCourseTaggingGroup=cg.IdCourseTaggingGroupMinor',array())
		// ->joinLeft(array('u'=>'tbl_user'),'u.iduser=cg.creator',array('owner'=>'loginName'))
		->where('cg.group_id = ?',$idgrup)
		->where('cg.IdLecturer='.$idlect.' or stm.Entrier='.$idlect)
		->group('cg.IdCourseTaggingGroupMinor') ;
	
		// echo $select;
		$row = $db->fetchAll($select);
		// echo var_dump($row);exit;
		return $row;
	}
	public function getGroupListByProgramBranchLimited($idSubject,$idSemester,$idProgram=null,$idBranch=null){
	
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cg'=>$this->_name))
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('SubCode'=>'ShortName','SubjectName'=>'sm.BahasaIndonesia'))
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
		->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'))
		->joinLeft(array('stm2'=>'tbl_staffmaster'),'stm2.IdStaff=cg.VerifyBy',array('VerFront'=>'FrontSalutation','VerFullName'=>'FullName','VerBack'=>'BackSalutation'))
		->joinLeft(array('p'=>'tbl_program'),'p.IdProgram=cg.programcreator')
		->where('IdSemester = ?',$idSemester);
		 if ($idSubject!=null ) $select->where('cg.IdSubject = ?',$idSubject);
		 if($session->IdRole == 311 || $session->IdRole == 298){
		 	$select->where("p.IdCollege =?",$session->idCollege);
		 }
		 if($session->IdRole == 470 || $session->IdRole == 480){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
		 	$select->where("cg.programcretor='".$session->idDepartment."'");
		 }
		 
		$row = $db->fetchAll($select);
	}
	public function getGroupListByProgramBranch($idSubject,$idSemester,$idProgram=null,$idBranch=null){
	
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cg'=>$this->_name)) 
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('SubCode'=>'ShortName','SubjectName'=>'sm.BahasaIndonesia'))
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
		->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'))
		->joinLeft(array('stm2'=>'tbl_staffmaster'),'stm2.IdStaff=cg.VerifyBy',array('VerFront'=>'FrontSalutation','VerFullName'=>'FullName','VerBack'=>'BackSalutation'))
		->joinLeft(array('cgp'=>'course_group_program'),'cg.IdCourseTaggingGroup=cgp.group_id',array())
		->joinLeft(array('p'=>'tbl_program'),'p.IdProgram=cgp.program_id')
		->joinLeft(array('cgb'=>'course_group_branch'),'cg.IdCourseTaggingGroup=cgb.group_id',array())
		->joinLeft(array('br'=>'tbl_branchofficevenue'),'br.IdBranch=cgb.branch_id',array('br.IdBranch','br.BranchName'))
		->where('IdSemester = ?',$idSemester);
		if ($idProgram!=null ) $select->where('p.IdProgram = ?',$idProgram);
		if ($idSubject!=null ) $select->where('cg.IdSubject = ?',$idSubject);
		if ($idBranch!=null) $select->where('br.IdBranch = ?',$idBranch);
		// echo $select;
		$row = $db->fetchAll($select);
		if (!$row) {
			$select = $db ->select()
			->from(array('cg'=>$this->_name))
			->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('SubCode'=>'ShortName','SubjectName'=>'sm.BahasaIndonesia'))
			
			->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
			->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'))

			->joinLeft(array('stm2'=>'tbl_staffmaster'),'stm2.IdStaff=cg.VerifyBy',array('VerFront'=>'FrontSalutation','VerFullName'=>'FullName','VerBack'=>'BackSalutation'))
			->joinLeft(array('cgp'=>'course_group_program'),'cg.IdCourseTaggingGroup=cgp.group_id',array())
			->joinLeft(array('p'=>'tbl_program'),'p.IdProgram=cgp.program_id')
			->joinLeft(array('cgb'=>'course_group_branch'),'cg.IdCourseTaggingGroup=cgb.group_id',array())
			->joinLeft(array('br'=>'tbl_branchofficevenue'),'br.IdBranch=cgb.branch_id',array('br.IdBranch','br.BranchName'))
			->where('cg.IdSubject = ?',$idSubject)
			->where('cg.IdSemester = ?',$idSemester);
			if ($idProgram!=null ) $select->where('p.IdProgram = ?',$idProgram);
			$row = $db->fetchAll($select);
		}
		//echo var_dump($row);exit;
		return $row;	}
	
		
		public function getListofSubject($idSemester,$idprogram){
		 
			$db = Zend_Db_Table::getDefaultAdapter();
		
			$select = $db ->select()
			->distinct()
			->from(array('cg'=>$this->_name),array())
			
			->joinLeft(array('cgp'=>'course_group_program'),'cg.IdCourseTaggingGroup=cgp.group_id',array())
			->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('sm.IdSubject','subject_code'=>'ShortName','subject_name'=>'BahasaIndonesia'))
			 
			->where('cgp.program_id = ?',$idprogram)
			->where('cg.IdSemester = ?',$idSemester)
			->order('sm.BahasaIndonesia');
			
			return $db->fetchAll($select);
			
		}
		
	public function getGroupListPerProgram($idSubject,$idSemester,$idprogram,$branch=null){
	
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cg'=>$this->_name))
		//->distinct()
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
		->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation','StaffId','IdStaff'))
		->joinLeft(array('cgp'=>'course_group_program'),'cg.IdCourseTaggingGroup=cgp.group_id')
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'BahasaIndonesia'))
		//->where('cg.IdSubject = ?',$idSubject)
		->where('cg.programcreator = ?',$idprogram)
		->where('cg.IdSemester = ?',$idSemester)
		->group('cg.IdCourseTaggingGroup');
		
		if ($idSubject!=null ) $select->where('cg.IdSubject = ?',$idSubject);
		
		if ($branch!=null) {
			$select->joinLeft(array('cgb'=>'course_group_branch'),'cg.IdCourseTaggingGroup=cgb.group_id',array())
				   ->joinLeft(array('br'=>'tbl_branchofficevenue'),'br.IdBranch=cgb.branch_id',array('br.IdBranch','br.BranchName'));
			$select->where('cg.branchcreator=?',$branch);
		
		}
		
		$row = $db->fetchAll($select);
		
		//echo var_dump($row);exit;
		return $row;
	}
	public function getGroupListPerGroupProgram($idSubject,$idSemester,$program){
	
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
		$programid=array();
		foreach ($program as $value) {
			$programid[]=$value['IdProgram'];
		}
		
		$programid=implode(',', $programid);
		 
		$select = $db ->select()
		->from(array('cg'=>$this->_name))
		//->distinct()
		->join(array('cs'=>'course_group_schedule'),'cg.idcoursetagginggroup=cs.idgroup',array('sc_day','sc_start_time'))
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
		->joinLeft(array('ss'=>'tbl_staffmaster'),'ss.IdStaff=cg.IdLecturer',array('lecturername'=>'ss.FullName'))
		->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'))
		->joinLeft(array('cgp'=>'course_group_program'),'cg.IdCourseTaggingGroup=cgp.group_id')
		->joinLeft(array('cgc'=>'course_group_branch'),'cg.IdCourseTaggingGroup=cgc.group_id')
		->joinLeft(array('br'=>'tbl_branchofficevenue'),'br.IdBranch=cgc.branch_id',array('branchname'=>'br.BranchName'))
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'BahasaIndonesia'))
		 
		->where('cg.IdSemester = ?',$idSemester);
		//echo $select;exit;
		if ($programid!='') $select->where('cgp.program_id in ( ?)',$programid);
		if ($idSubject!=null ) $select->where('cg.IdSubject = ?',$idSubject);
	
		/* if ($branch!=null) {
			$select->joinLeft(array('cgb'=>'course_group_branch'),'cg.IdCourseTaggingGroup=cgb.group_id',array())
			->joinLeft(array('br'=>'tbl_branchofficevenue'),'br.IdBranch=cgb.branch_id',array('br.IdBranch','br.BranchName'));
			$select->where('cgb.branch_id=?',$branch);
	
		} */
	
		$row = $db->fetchAll($select);
	
		//echo var_dump($row);exit;
		return $row;
	}
	
	
	public function getGroupListByProgram($idSemester,$program=null,$idcollege=null){
	 
		 
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$select = $db ->select()
		->from(array('cg'=>$this->_name))
		 
		->join(array('cs'=>'course_group_schedule'),'cg.idcoursetagginggroup=cs.idgroup',array('sc_id','sc_day','sc_start_time','sc_end_time','sc_venue','IdLecturer'=>'IFNULL(cs.IdLecturer,cg.IdLecturer)','Extra'))
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
		//->join(array('ss'=>'tbl_staffmaster'),'ss.IdStaff=cg.IdLecturer',array('lecturername'=>'ss.FullName','FrontSalutation','FullName','BackSalutation'))
		->join(array('cgp'=>'course_group_program'),'cg.IdCourseTaggingGroup=cgp.group_id',array())
		->join(array('p'=>'tbl_program'),'p.IdProgram=cgp.program_id',array('ProgramName'=>'ArabicName','strata'))
		->joinLeft(array('cgc'=>'course_group_branch'),'cg.IdCourseTaggingGroup=cgc.group_id')
		->joinLeft(array('br'=>'tbl_branchofficevenue'),'br.IdBranch=cgc.branch_id',array('branchname'=>'br.BranchName'))
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'BahasaIndonesia','sks'=>'CreditHours'))
	 	->where('cg.IdSemester = ?',$idSemester)
	 	->group('IdLecturer')
		->group('cg.idcoursetagginggroup');
		 
		if($program!=null)  $select->where('cgp.program_id=?',$program);
		if($idcollege!=null)  $select->where('p.IdCollege=?',$idcollege);
		
		$row = $db->fetchAll($select);
	
		//echo $select;exit;
		return $row;
	}
	
	public function getGroupListByLecturer($idLecturer,$idSemester){
	
			
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db ->select()
		->from(array('cg'=>$this->_name))
			
		->join(array('cs'=>'course_group_schedule'),'cg.idcoursetagginggroup=cs.idgroup',array('sc_id','sc_day','sc_start_time','sc_end_time','sc_venue','IdLecturer'=>'IFNULL(cs.IdLecturer,cg.IdLecturer)','Extra'))
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
		//->join(array('ss'=>'tbl_staffmaster'),'ss.IdStaff=cg.IdLecturer',array('lecturername'=>'ss.FullName','FrontSalutation','FullName','BackSalutation'))
		->join(array('cgp'=>'course_group_program'),'cg.IdCourseTaggingGroup=cgp.group_id',array())
		->join(array('p'=>'tbl_program'),'p.IdProgram=cgp.program_id',array('ProgramName'=>'ArabicName','strata'))
		->joinLeft(array('cgc'=>'course_group_branch'),'cg.IdCourseTaggingGroup=cgc.group_id')
		->joinLeft(array('br'=>'tbl_branchofficevenue'),'br.IdBranch=cgc.branch_id',array('branchname'=>'br.BranchName'))
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'BahasaIndonesia','sks'=>'CreditHours'))
		->where('cg.IdSemester = ?',$idSemester)
		->where('cg.IdLecturer=?',$idLecturer)
		 
		->group('cg.idcoursetagginggroup');
			
		 
		$row = $db->fetchAll($select);
	
		//echo $select;exit;
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
	
	public function isProgramCreator($idSemester,$idsubject,$idprogram){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>'tbl_course_tagging_group'))
		->where("IdSubject = ?",$idsubject)
		->where('IdSemester = ?',$idSemester)
		->where('ProgramCreator=?',$idprogram);
		$row = $db->fetchAll($select);
			
		return $row;
	}
	
	public function getCourseTransfer(){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>'course_group_transfer'))
		->where('a.status is null')
		->order('a.subcode')
		->order('a.groupcode') 
		->order('a.programcode')
		->order('a.branchcode');
		$row = $db->fetchAll($select);
		return $row;
	}
	
	public function updateCourseTransfer($data,$id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$db ->update('course_group_transfer',$data,'id='.(int)$id);
	}
	
	public function getInfo($idGroup){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('cg'=>$this->_name))
					  ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage','faculty_id'=>'IdFaculty','credithours'))
					  ->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
					  ->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation','NIDN'=>'Dosen_Code_EPSBED','StaffId','StaffJobType','StaffGrade','StaffStatus','EduLevel'))
					  ->where('IdCourseTaggingGroupMinor = ?',$idGroup)
					;					  
		
		 $row = $db->fetchRow($select);	
		 return $row;
	}
	public function getDataByStudentRegSubject($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cg'=>$this->_name))
		->join(array('srs'=>'tbl_studentregsubjects'),'cg.IdCourseTaggingGroup=srs.IdCourseTaggingGroup',array('IdStudentRegistration'))
		->where('srs.IdStudentRegSubjects = ?',$idGroup);
	
		$row = $db->fetchRow($select);
		return $row;
	}
	
	public function getCourseAsExam($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cg'=>$this->_name),array('eg_sub_id'=>'cg.IdSubject','av_room_code'=>'GroupCode'))
		->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'BahasaIndonesia','faculty_id'=>'IdFaculty'))
		->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
		->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation','NIDN'=>'Dosen_Code_EPSBED','StaffId','StaffJobType','StaffGrade','StaffStatus','EduLevel'))
		->where('IdCourseTaggingGroup = ?',$idGroup);
		$row = $db->fetchRow($select);
		 
		return $row;
	}
	
	public function getInfoSchedulle($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cg'=>$this->_name),array('cg.*','Coordinator'=>'cg.IdLecturer'))
		->join(array('sc'=>'course_group_schedule_minor'),'cg.IdCourseTaggingGroupMinor=sc.idGroup',array('sc.*','Lecturer'=>'sc.IdLecturer'))
		->where('IdCourseTaggingGroupMinor = ?',$idGroup);
		$row = $db->fetchAll($select);
		
		return $row;
	}
	public function getInfoCourseRegistration($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cg'=>'tbl_studentregsubjects'))
		->where('IdCourseTaggingGroup = ?',$idGroup);
		$row = $db->fetchAll($select);
	
		return $row;
	}
	
	public function getInfoScheduleRow($idsch){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cg'=>$this->_name))
		->join(array('sc'=>'course_group_schedule_minor'),'cg.IdCourseTaggingGroupMinor=sc.idGroup')
		->where('sc_id = ?',$idsch);
		$row = $db->fetchAll($select);
	
		return $row;
	}
	
	public function getInfoSchedulleByLecturer($idsemester,$idlec=null,$idsubject=null,$idprogram=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cg'=>$this->_name))
		->join(array('sc'=>'course_group_schedule_minor'),'cg.IdCourseTaggingGroupMinor=sc.idGroup')
	 	 			  
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage','faculty_id'=>'IdFaculty','sks'=>'CreditHours'))
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))	  
		
		->where('cg.IdSemester = ?',$idsemester);
		if ($idlec!=null) $select->where('cg.IdLecturer ='.$idlec.' or sc.IdLecturer='.$idlec);
		if ($idsubject!=null) $select->where('cg.IdSubject =?',$idsubject);
		 
		$row = $db->fetchAll($select);
		return $row;
	}
	
	 
	
	 
	
	public function getInfoGrroupALl($idGroup,$idprogram=null,$idsem=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		//$idGroup=implode(',',$idGroup);
		$select = $db ->select()
		->distinct()
		->from(array('srs'=>"course_group_student_minor"),array('IdStudentRegistration'))
		 
		->join(array('tgm'=>'tbl_course_tagging_group_minor'),'tgm.IdCourseTaggingGroupMinor=tg.IdCourseTaggingGroupMinor',array())
		->join(array('st'=>'tbl_studentregistration'),'srs.idstudentregistration=st.idstudentregistration',array('registrationid'))
		->join(array('sp'=>'student_profile'),'st.idApplication=sp.appl_id',array("studentname"=>'CONCAT(appl_fname," ",appl_mname," ",appl_lname)'))
		->where('tgm.IdCourseTaggingGroupMinor in ( ? )',$idGroup);
		if ($idprogram!=null) $select->where('st.IdProgram=?',$idprogram);
		if ($idsem!=null) $select->where('tg.IdSemester=?',$idsem);
		$row = $db->fetchAll($select);
		//echo $select;exit;
		return $row;
	}
	public function getInfoGroupByProgramCreator($idCollege,$idprogram=null,$idsem=null,$idsubject=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		//$idGroup=implode(',',$idGroup);
		$select = $db ->select()
		->from(array('cg'=>$this->_name))
		->join(array('tm'=>'tbl_subjectmaster'),'tm.IdSubject=cg.IdSubject',array('subject_name'=>'tm.BahasaIndonesia','subject_code'=>'tm.ShortName'))
		->order('tm.BahasaIndonesia')
		->order('cg.BranchCreator');
		if ($idCollege!=null && $idprogram==null) {
			$select->join(array('pr'=>'tbl_program'),'cg.ProgramCreator=pr.IdProgram') 
				->where('pr.IdCollege=?',$idCollege);
		}
		if ($idprogram!=null ) $select->where('cg.ProgramCreator=?',$idprogram);
		if ($idsem!=null) $select->where('cg.IdSemester=?',$idsem);
		if ($idsubject!=null) $select->where('cg.IdSubject=?',$idsubject);
		 
		$row = $db->fetchAll($select);
		//echo $select;exit;
		return $row;
	}
    
    public function getInfoWithProgram($idGroup){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('cg'=>$this->_name))
					  ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage','faculty_id'=>'IdFaculty'))
					  ->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester')
					  ->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'))
					  ->joinLeft(array('cp'=>'course_group_program'),'cp.group_id=cg.IdCourseTaggingGroup')
					  ->joinLeft(array('p'=>'tbl_program'),'cp.program_id=p.IdProgram')
					  ->where('IdCourseTaggingGroup = ?',$idGroup);					  
		 
         $row = $db->fetchAll($select);	
		 return $row;
	}
	public function getInfoAllProgram($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cg'=>$this->_name))
		->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage','faculty_id'=>'IdFaculty'))
		->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester')
		->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'))
		->joinLeft(array('cp'=>'course_group_program'),'cp.group_id=cg.IdCourseTaggingGroup')
		->joinLeft(array('p'=>'tbl_program'),'cp.program_id=p.IdProgram')
		->where('IdCourseTaggingGroup = ?',$idGroup);
			
		$row = $db->fetchAll($select);
		return $row;
	}
	
	public function getMarkEntryGroupList($idSubject,$idSemester){
		
		$auth = Zend_Auth::getInstance();
		
		//print_r($auth->getIdentity());
	
		$db = Zend_Db_Table::getDefaultAdapter();	

		
		
		$select = $db ->select()
					  ->from(array('cg'=>$this->_name))
					  ->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
					  ->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'))
					 // ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage'))
					  ->where('IdSubject = ?',$idSubject)
					  ->where('IdSemester = ?',$idSemester);	

		 $rows = $db->fetchAll($select);	
		 	
		
		 if($auth->getIdentity()->IdRole!=1 && $auth->getIdentity()->IdRole!=298 && $auth->getIdentity()->IdRole!=605){//sekiranya bukan admin dia boleh tgk group dia sahaja
		 	
		 	
				 foreach($rows as $index=>$row){		 	
				 	
				 	//cari list lecturer dalam group tu
				 	//course group schedule
					$select_group_schedule = $db ->select()
								 				 ->from(array('cgs'=>'course_group_schedule'))
								 				 ->where("cgs.idGroup = ?",$row["IdCourseTaggingGroup"])
								 				 ->where("cgs.IdLecturer = ?",$auth->getIdentity()->iduser);
					$lecturer = $db->fetchRow($select_group_schedule);	
											
					if(!$lecturer["sc_id"]){							
						//check adakah dia coordinator
						if($row["IdLecturer"]!=$auth->getIdentity()->iduser){
							unset($rows[$index]);
						}
					}
								
				 }//end foreach
		 	
		 }//end if
		 
		
		 return $rows;
	}
	
	
	public function getMarkApprovalGroupList($subject_id,$idSemester,$program_id=null) {
		
		$auth = Zend_Auth::getInstance();
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('cg'=>$this->_name))
					  ->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
					  ->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'))
					  ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'BahasaIndonesia'))
					  ->where('IdSemester = ?',$idSemester);	

		 if($auth->getIdentity()->IdRole!=1 && $auth->getIdentity()->IdRole!=298){//sekiranya bukan admin dia boleh tgk group dia sahaja
		 	$select->where("cg.VerifyBy = ?",$auth->getIdentity()->IdStaff);
		 }  
		 if ($subject_id!='')
		 	 $select->where('cg.IdSubject = ?',$subject_id);
		 
		 if ($program_id!='') {
		 	//$select->join(array('cg'=>'programc'),'pr.group_id=cg.idcoursetagginggroup');
		 	$select->where('cg.programcreator = ?',$program_id);
		 }
		 $select->order('sm.BahasaIndonesia');
		 $row = $db->fetchAll($select);	
		 return $row;
	}
	
	
	public function getCourseTaggingGroupList($idSemester,$idprogram=null,$idsubject=null,$branch=null){		
		
		$auth = Zend_Auth::getInstance();
		
		$id_user = $auth->getIdentity()->IdStaff;
		$idRole  = $auth->getIdentity()->IdRole;
		
	/*	$id_user = 747;
		$idRole=3;*/
		
		$db = Zend_Db_Table::getDefaultAdapter();	
		
		$operator=$db->select()
			->from(array('op'=>'tbl_mark_operator'),array('IdCourseTaggingGroup'))
			->where('op.Entrier=?',$id_user);
		$row=$db->fetchAll($operator);
		//check if user is coordinator so display all
	    $select = $db ->select()
					  ->from(array('cg'=>$this->_name))
					  ->join(array('cgs'=>'course_group_schedule'),'cgs.idGroup=cg.IdCourseTaggingGroup',array())
					  ->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
					  ->join(array('pr'=>'course_group_program'),'pr.group_id=cg.IdCourseTaggingGroup',array())
					   ->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','coordinator'=>'FullName','BackSalutation'))
					  ->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('IdSubject','SubCode','subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage',"SubjectName"=>'subjectMainDefaultLanguage'))					  
					  ->where('cg.IdSemester = ?',$idSemester)					  
					  
					  ->group('IdCourseTaggingGroup');	
	   
					  if($idRole==3){ 
					  	//hanya dosen saja
					  	if ($row) {
						  	$select->where('(cg.IdLecturer = ?',$id_user);
						  	$select->orwhere('cgs.IdLecturer = ?',$id_user);
						  	$select->orwhere('cg.IdCourseTaggingGroup in (?))',$operator);
					  	} else {
					  		$select->where('(cg.IdLecturer = ?',$id_user);
					  		$select->orwhere('cgs.IdLecturer = ?)',$id_user);
					  		 
					  		
					  	}
					  }	
		//echo $select;exit;
					  	
		if ($idprogram!=null) $select->where('cg.ProgramCreator=?',$idprogram);
		if ($idsubject!=null) $select->where('cg.IdSubject=?',$idsubject);
		if ($branch!=null) {
			//$select->joinLeft(array('br'=>'course_group_branch'),'br.group_id=cg.IdCourseTaggingGroup',array());
			$select->where('cg.BranchCreator=?',$branch);
		}
		$rows = $db->fetchAll($select);
		 //echo var_dump($rows);exit;
		 return $rows;				
	}
	
	public function getCourseTaggingGroupListForMarkEntry($idGroup){
	
		$auth = Zend_Auth::getInstance();
	
		$id_user = $auth->getIdentity()->IdStaff;
		$idRole  = $auth->getIdentity()->IdRole;
	
		/*	$id_user = 747;
			$idRole=3;*/
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$operator=$db->select()
		->from(array('op'=>'tbl_mark_operator'),array('IdCourseTaggingGroup'))
		->where('op.Entrier=?',$id_user);
		$row=$db->fetchAll($operator);
		//check if user is coordinator so display all
		$select = $db ->select()
		->from(array('cg'=>$this->_name))
		->join(array('cgs'=>'course_group_schedule'),'cgs.idGroup=cg.IdCourseTaggingGroup',array())
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
		->join(array('pr'=>'course_group_program'),'pr.group_id=cg.IdCourseTaggingGroup',array())
		->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','coordinator'=>'FullName','BackSalutation'))
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('IdSubject','SubCode','subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage',"SubjectName"=>'subjectMainDefaultLanguage'))
		->where('cg.IdSemester = ?',$idSemester)
			
		->group('IdCourseTaggingGroup');
	
		if($idRole==3){
			//hanya dosen saja
			if ($row) {
				$select->where('(cg.IdLecturer = ?',$id_user);
				$select->orwhere('cgs.IdLecturer = ?',$id_user);
				$select->orwhere('cg.IdCourseTaggingGroup in (?))',$operator);
			} else {
				$select->where('(cg.IdLecturer = ?',$id_user);
				$select->orwhere('cgs.IdLecturer = ?)',$id_user);
	
					
			}
		}
		//echo $select;exit;
	
		if ($idprogram!=null) $select->where('cg.programcreator=?',$idprogram);
		if ($idsubject!=null) $select->where('cg.IdSubject=?',$idsubject);
		if ($branch!=null && $branch>0) {
			$select->joinLeft(array('br'=>'tbl_branchofficevenue'),'br.group_id=cg.branchcreator',array());
			$select->where('br.IdBranch=?',$branch);
		}
		$rows = $db->fetchAll($select);
		//echo var_dump($rows);exit;
		return $rows;
	}
	
	public function getGroupListPerEntrier($idSubject,$idSemester,$idprogram,$branch=null,$identrier,$mode){
	
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cg'=>'tbl_course_tagging_group_assistant'))
		//->distinct()
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
		->joinLeft(array('cgp'=>'course_group_program_assistant'),'cg.IdCourseTaggingGroup=cgp.group_id')
		->joinLeft(array('p'=>'tbl_program'),'p.IdProgram=cgp.program_id',array('ProgramName'=>'p.ArabicName','ProgramCode','ProgramNameShort'=>'p.ShortName'))
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'BahasaIndonesia'))
		//->where('cg.IdSubject = ?',$idSubject)
		->where('cgp.program_id = ?',$idprogram)
		->where('cg.IdSemester = ?',$idSemester);
	
		if ($idSubject!=null ) $select->where('cg.IdSubject = ?',$idSubject);
		if ($identrier!=null ) {
			if ($mode=="1") {
				$select->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'));
	
			} else {
				$select->joinLeft(array('stm'=>'tbl_studentregistration'),'stm.IdStudentRegistration=cg.IdLecturer',array('registrationId'));
				$select->joinLeft(array('sp'=>'student_profile'),'sp.appl_id=cg.IdLecturer',array('FullName'=>'CONCAT(appl_fname," ",appl_mname," ",appl_lname)'));
					
			}
			if ($session->IdRole!=1)
				$select->where('cg.IdLecturer = ?',$identrier);
				
		}
	
		$select->joinLeft(array('cgb'=>'course_group_branch_assistant'),'cg.IdCourseTaggingGroup=cgb.group_id',array())
		->joinLeft(array('br'=>'tbl_branchofficevenue'),'br.IdBranch=cgb.branch_id',array('br.IdBranch','br.BranchName','Bcode'=>'br.ShortName'));
			
		if ($branch!=null) {
			$select->where('cgb.branch_id=?',$branch);
		}
		//echo $select;exit;
		$row = $db->fetchAll($select);
	
		//echo var_dump($row);exit;
		return $row;
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
	
	public function getCourseBranchByProgram($semester,$program){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
			
		$select = $db ->select()
		 
		->from(array('br'=>'course_group_branch'),array())
		->join(array('ctg'=>'tbl_course_tagging_group'),'br.group_id=ctg.idCourseTaggingGroup',array())
		->join(array('cp'=>'course_group_program'),'cp.group_id=ctg.idCourseTaggingGroup',array())
		->join(array('brh'=>'tbl_branchofficevenue'),'br.branch_id=brh.IdBranch',array('key'=>'IdBranch','value'=>'BranchName'))
		->where('ctg.IdSemester = ?',$semester)
		->where('cp.program_id = ?',$program)
		->group('brh.IdBranch');
	
		$rows = $db->fetchAll($select);
		//echo var_dump($rows);exit;
		return $rows;
	}
	
	public function getCoursePayment($semester,$program,$branch){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
			
		$select = $db ->select()		 
		->from(array('ctg'=>'tbl_course_tagging_group'),array('GroupName'=>'GroupName','GroupCode','idgroup'=>'IdCourseTaggingGroup','Coordinator'=>'ctg.IdLecturer'))
		->join(array('sm'=>'tbl_subjectmaster'),'ctg.IdSubject=sm.IdSubject',array('SubCode'=>'ShortName','SubjectName'=>'BahasaIndonesia','sks'=>'CreditHours'))
		->join(array('cs'=>'course_group_schedule'),'ctg.IdCourseTaggingGroup=cs.idGroup',array('Extra'))
		->join(array('tsm'=>'tbl_staffmaster'),'tsm.IdStaff = ctg.IdLecturer', array('StaffGrade','StaffJobType','EduLevel'))
		->where('ctg.IdSemester = ?',$semester)
		->group('ctg.IdCourseTaggingGroup')
		->order('tsm.StaffJobType')
		->order('tsm.IdStaff');
		if ($program!=null) $select->where('ctg.ProgramCreator in ('.$program.')');
		if ($branch!=null) $select->where('ctg.BranchPaid in ('.$branch.')');
	
		$rows = $db->fetchAll($select);
		//echo var_dump($rows);exit;
		return $rows;
	}
	public function getStudentProgram($semester,$program,$intake=null,$idstd=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
			
		$select = $db ->select()
			
		->from(array('br'=>'tbl_studentregsubjects'),array('IdStudentRegistration'))
		->join(array('ctg'=>'tbl_course_tagging_group'),'br.idCourseTaggingGroup=ctg.idCourseTaggingGroup',array())
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=br.IdStudentRegistration',array('registrationId','IdLandscape','jenis_pendaftaran','sks_diakui'))
		 ->join(array('sp'=>'student_profile'),'sp.appl_id=sr.idapplication')
		->where('br.IdSemesterMain = ?',$semester)
		->where('sr.IdProgram = ?',$program)
		->group('br.IdStudentRegistration');
		if ($intake!=null) $select->where('sr.IdIntake=?',$intake);
		if ($idstd!=null) $select->where('sr.registrationId=?',$idstd);
		$rows = $db->fetchAll($select);
		//echo var_dump($rows);exit;
		return $rows;
	}
	
	public function getProgramOfStudent($gid){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
			
		$select = $db ->select()
			
		->from(array('br'=>'tbl_studentregsubjects'),array())
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=br.IdStudentRegistration',array())
		->join(array('pr'=>'tbl_program'),'pr.IdProgram=sr.IdProgram',array('key'=>'pr.IdProgram','value'=>'pr.ArabicName'))
		->where('br.IdCourseTaggingGroup = ?',$gid)
		->group('pr.IdProgram');
	 	$rows = $db->fetchAll($select);
		//echo var_dump($rows);exit;
		return $rows;
	}
	
	public function getProgramAndBranchOfStudent($idsemester,$idsubject){
		$auth = Zend_Auth::getInstance();
		$db = Zend_Db_Table::getDefaultAdapter();
		$idRole  = $auth->getIdentity()->IdRole;
		$session = new Zend_Session_Namespace('sis');
		$select = $db ->select()
			
		->from(array('br'=>'tbl_studentregsubjects'),array())
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=br.IdStudentRegistration',array('IdProgram','IdBranch'))
		->join(array('p'=>'tbl_program'),'p.IdProgram=sr.IdProgram',array())
		->where('br.IdSubject = ?',$idsubject)
		->where('br.IdSemesterMain = ?',$idsemester)
		->group('sr.IdProgram')
		->group('sr.IdBranch');
		if($session->IdRole !=1){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
			$select->where("p.IdCollege='".$session->idCollege."'");
		}
		
		$rows = $db->fetchAll($select);
	
		return $rows;
	}
	
	public function getSubjectCourseByProgram($semester,$program=null,$branch=null){
		 
		$db = Zend_Db_Table::getDefaultAdapter();
		$session = new Zend_Session_Namespace('sis');
		$auth=Zend_Auth::getInstance();
		$select = $db ->select()
		->from(array('ctg'=>'tbl_course_tagging_group'),array())
		->join(array('cp'=>'course_group_program'),'cp.group_id=ctg.idCourseTaggingGroup',array())
		->join(array('a'=>'tbl_program'),'a.IdProgram=cp.program_id',array())
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=ctg.IdSubject',array('sm.IdSubject','SubCode'=>'ShortName','SubjectName'=>'BahasaIndonesia','CreditHours'))
		->where('ctg.IdSemester = ?',$semester)
		->group('ctg.IdSubject')
		->order('sm.BahasaIndonesia');
		if($session->IdRole == 311 || $session->IdRole == 298){
			$select->where("a.IdCollege =?",$session->idCollege);
		}
		if($session->IdRole == 470 || $session->IdRole == 480){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
			$select->where("ctg.programcreator='".$session->idDepartment."'");
		}
		if($session->IdRole == 3){ //Lecturer only
			$select->where("ctg.IdLecturer='".$auth->getIdentity()->IdStaff."'");
		}
		if ($program!=null) $select->where('ctg.programcreator = ?',$program);
		if ($branch!=null) $select->where('ctg.branchcreator = ?',$branch);
		
		$rows = $db->fetchAll($select);
		//echo $select;exit;
		return $rows;
	}
	
	
    public function getMarkEntryGroupListt($idgroup){
		
		$auth = Zend_Auth::getInstance();
		
		//print_r($auth->getIdentity());
	
		$db = Zend_Db_Table::getDefaultAdapter();	

		
		
		$select = $db ->select()
					  ->from(array('cg'=>$this->_name))
					  ->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
					  ->join(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation','StaffId'))
					  ->join(array('cp'=>'course_group_program'),'cp.group_id=cg.IdCourseTaggingGroup',array())
					  ->where('IdCourseTaggingGroup = ?',$idgroup)
					  ->group('cg.IdCourseTaggingGroupM');	
		 if(!($auth->getIdentity()->IdRole==1 || $auth->getIdentity()->IdRole==298 || $auth->getIdentity()->IdRole==470 || $auth->getIdentity()->IdRole==605 || $auth->getIdentity()->IdRole==480)){//sekiranya bukan admin dia boleh tgk group dia sahaja
		 	$select->where('IdLecturer = ?', $auth->getIdentity()->IdStaff); }
		 if ($idprogram !=null ) $select->where('cg.programcreator=?',$idprogram);
		  
		 
		$rows = $db->fetchAll($select);
		 return $rows;
	}
	public function getCountStudentCourseList($idprogram,$idbranch,$idSemester){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name),array('nOfStudent'=>'count(*)'))
		->join(array('srs'=>'tbl_studentregsubjects'),'a.IdCourseTaggingGroup=srs.IdCourseTaggingGroup')
		->join(array('s'=>'tbl_subjectmaster'),'a.IdSubject=s.IdSubject',array('SubCode','SubjectName','CreditHours'))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration')
		->join(array('cp'=>'course_group_program'),'a.IdCourseTaggingGroup=cp.group_id')
		//->joinLeft(array('cb'=>'course_group_branch'),'a.IdCourseTaggingGroup=cb.group_id')
		->joinLeft(array('staff'=>'tbl_staffmaster'),'a.IdLecturer=staff.IdStaff',array('Lecturer'=>'CONCAT(FirstName," ",ThirdName)'))
		->where("cp.program_id = ?",$idprogram)
		->where('srs.IdSemesterMain = ?',$idSemester)
		->group('a.IdSubject');
		if ($idbranch!='All') $select->where('sr.IdBranch=?',$idbranch);
		
		$row = $db->fetchAll($select);
		//echo var_dump($row);exit;
		return $row;
		
	}
	public function getCountStudentByGroup($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>'tbl_studentregsubjects'),array('nOfStudent'=>'count(*)'))
		 
		->where('a.IdCourseTaggingGroup = ?',$idGroup)  ;
		$row = $db->fetchRow($select);
		//echo var_dump($row);exit;
		return $row['nOfStudent'];
	
	}
	public function getCountStudentBySetOfCourse($idprogram,$idbranch,$idSemester,$idsubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name),array('nOfStudent'=>'count(*)','GroupCode','GroupName','IdCourseTaggingGroup'))
		->join(array('srs'=>'tbl_studentregsubjects'),'a.IdCourseTaggingGroup=srs.IdCourseTaggingGroup',array())
		//->join(array('s'=>'tbl_subjectmaster'),'a.IdSubject=s.IdSubject',array('Subcode','SubjectName','CreditHours'))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration',array())
		->join(array('cp'=>'course_group_program'),'a.IdCourseTaggingGroup=cp.group_id',array())
		//->joinLeft(array('cb'=>'course_group_branch'),'a.IdCourseTaggingGroup=cb.group_id')
		->joinLeft(array('staff'=>'tbl_staffmaster'),'a.IdLecturer=staff.IdStaff',array('Lecturer'=>'CONCAT(FirstName," ",ThirdName)'))
		->where("cp.program_id = ?",$idprogram)
		->where('srs.IdSemesterMain = ?',$idSemester)
		->where('a.IdSubject=?',$idsubject)
		->group('a.IdCourseTaggingGroup');
		if ($idbranch!='All') $select->where('sr.IdBranch=?',$idbranch);
	
		$row = $db->fetchAll($select);
		//echo var_dump($row);exit;
		return $row;
	
	}
	public function getCountCourseByLecturer($idSemester,$idsubject,$idlecturer,$idbranch=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name),array('nOfCourse'=>'count(*)','IdLecturer'))
		->join(array('srs'=>'tbl_studentregsubjects'),'a.IdCourseTaggingGroup=srs.IdCourseTaggingGroup',array())
		//->join(array('s'=>'tbl_subjectmaster'),'a.IdSubject=s.IdSubject',array('Subcode','SubjectName','CreditHours'))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration',array())
		->join(array('cp'=>'course_group_program'),'a.IdCourseTaggingGroup=cp.group_id',array())
		//->joinLeft(array('cb'=>'course_group_branch'),'a.IdCourseTaggingGroup=cb.group_id')
		->joinLeft(array('staff'=>'tbl_staffmaster'),'a.IdLecturer=staff.IdStaff',array('Lecturer'=>'CONCAT(FirstName," ",ThirdName)'))
		->where('a.IdLecturer=?',$idlecturer)
		->where('srs.IdSemesterMain = ?',$idSemester)
		->where('a.IdSubject=?',$idsubject)
		->group('a.IdLecturer');
		if ($idbranch!='All') $select->where('a.BranchCreator=?',$idbranch);
	
		$row = $db->fetchAll($select);
		//echo var_dump($row);exit;
		return $row;
	
	}
    
    public function getMarkEntryGroupListBySemester($idSemester){
		
		$auth = Zend_Auth::getInstance();
		
		//print_r($auth->getIdentity());
	
		$db = Zend_Db_Table::getDefaultAdapter();	

		
		
		$select = $db ->select()
					  ->from(array('cg'=>$this->_name))
					  ->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
					  ->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'))
					  ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage'))
					  //->where('IdSubject = ?',$idSubject)
					  ->where('IdSemester = ?',$idSemester);	

		 	
		 	
		
		 if($auth->getIdentity()->IdRole!=1 && $auth->getIdentity()->IdRole!=298 && $auth->getIdentity()->IdRole!=470){//sekiranya bukan admin dia boleh tgk group dia sahaja
		 	$select->where('IdLecturer = ?', $auth->getIdentity()->IdStaff);
		 	
		 }//end if
        
		$rows = $db->fetchAll($select);
		 return $rows;
	}
	
	public function isCourseGroupEmpty($idGroup) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
			
		$select = $db ->select()
			
		->from(array('a'=>$this->_name),array())
		 ->join(array('srs'=>'course_group_student_minor'),'a.IdCourseTaggingGroupMinor=srs.IdCourseTaggingGroupMinor',array('IdStudentRegistration'))
		->where('a.IdCourseTaggingGroupMinor=?',$idGroup);
		$row = $db->fetchAll($select);
			
		return $row;
	
		//
	}
	
	public function isCourseGroup($idsemester,$idsubject,$groupcode,$program=null) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
			
		$select = $db ->select()
			
		->from(array('a'=>$this->_name))
		->where('a.IdSubject=?',$idsubject)
		->where('a.IdSemester=?',$idsemester)
		;
		if ($groupcode!=null) $select->where('a.GroupCode=?',$groupcode);
		if ($program!=null) $select->where('a.programcreator=?',$program);
		$row = $db->fetchRow($select);
			
		return $row;
	
		//
	}
	
	public function isArchive($groupid) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
			
		$select = $db ->select()
			
		->from(array('a'=>$this->_name))
		->where('a.IdCourseTaggingGroup=?',$groupid)
		->where('a.dt_archived is not null') ;
		$row = $db->fetchRow($select);
		if ($row) return true;
		else return false;
	
		//
	}
	
	 public function getClassGroupAttendance($idprogram,$idsemester,$idsubject,$idstudentregistration) {
	 	 
	 	
	 	$dbProgram = new GeneralSetup_Model_DbTable_Program();
	 	$program=$dbProgram->getDataDetail($idprogram);
	 		
	 	$attendancemode=$program['Attendance_cal_mode'];
	 	//class group
	 	$courseGroupStudentDb = new GeneralSetup_Model_DbTable_CourseGroupStudent();
	 	$classGroup = $courseGroupStudentDb->checkStudentCourseGroup($idstudentregistration,$idsemester,$idsubject);
	 	
	 	//class attendance
	 	$courseGroupStudentAttendanceDb = new Examination_Model_DbTable_CourseGroupStudentAttendanceDetail();
	 	$courseGroupStudentAttDb = new Examination_Model_DbTable_CourseGroupStudentAttendance();
	 		
	 	$classGroup['class_session'] = $courseGroupStudentAttendanceDb->getAttendanceSessionCount($classGroup['IdCourseTaggingGroup'],$idstudentregistration);
	 	$classGroup['class_att_attended'] = $courseGroupStudentAttendanceDb->getAttendanceStatusCount($classGroup['IdCourseTaggingGroup'],$idstudentregistration,395);
	 	$classGroup['class_att_permission'] = $courseGroupStudentAttendanceDb->getAttendanceStatusCount($classGroup['IdCourseTaggingGroup'],$idstudentregistration,396);
	 	$classGroup['class_att_sick'] = $courseGroupStudentAttendanceDb->getAttendanceStatusCount($classGroup['IdCourseTaggingGroup'],$idstudentregistration,397);
	 	$classGroup['class_att_absent'] = $courseGroupStudentAttendanceDb->getAttendanceStatusCount($classGroup['IdCourseTaggingGroup'],$idstudentregistration,398);
	 	$nOfattend=0;
	 	if ($attendancemode=="124") {
	 		$nOfattend =$classGroup['class_att_attended'];
	 		if ($classGroup['class_session']>15) {
	 			if ($classGroup['class_att_permission']>0 || $classGroup['class_att_sick']>0) {
	 				$nOfattend=$nOfattend+2;
	 			}
	 		} else {
	 			if ($classGroup['class_att_permission']>0 || $classGroup['class_att_sick']>0) {
	 				$nOfattend=$nOfattend+1;
	 			}
	 		}
	 		 
	 	}
	 	else if ($attendancemode=="123") $nOfattend =$classGroup['class_att_permission']+$classGroup['class_att_sick']+$classGroup['class_att_attended'];
	 	else if ($attendancemode=="12") $nOfattend=$classGroup['class_att_sick']+$classGroup['class_att_attended'];
	 	else if ($attendancemode=="1") $nOfattend =$classGroup['class_att_attended'];
	 	$classGroup['class_att_calculated']=$nOfattend;
	 	if ($classGroup['class_session'] > 0) {
	 		$classGroup['class_attendance_percentage'] = ($nOfattend/$classGroup['class_session'] )*100;
	 	} else
	 		$classGroup['class_attendance_percentage']='';
	 	return $classGroup;
	 }
	 
	 public function getLecturerAttendance($idprogram,$idsemester) {
	 
	 	 	$db = Zend_Db_Table::getDefaultAdapter();
	 	
	 		$sql =  $db->select()
	 		->from(array('srs'=>'tbl_studentregsubjects'),array('IdCourseTaggingGroup'))
	 		->join(array('ct'=>'tbl_course_tagging_group'),'ct.IdCourseTaggingGroup=srs.IdCourseTaggingGroup',array('GroupName','GroupCode'))
	 		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration',array())
	 		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'sm.BahasaIndonesia','credithours'))
	 		->where("srs.IdSemesterMain = ?",$idsemester)
	 		->where("sr.IdProgram = ?",$idprogram)
	 		->group('srs.IdCourseTaggingGroup');
	 	
	 		$row = $db->fetchAll($sql);
	 		
	 		foreach ($row as $key=>$value) {
	 			$sql =  $db->select()
	 				->from(array('cat'=>'course_group_attendance'),array('natt'=>'count(*)','lecturer_id'))
	 				->where('cat.group_id=?',$value['IdCourseTaggingGroup'])
	 				->group('cat.lecturer_id');
	 			$row[$key]['att']=$db->fetchAll($sql);
	 		}
	 		return $row;
	 	 
	 }
	
}