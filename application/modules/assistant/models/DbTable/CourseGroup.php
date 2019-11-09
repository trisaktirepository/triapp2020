<?php 

class Assistant_Model_DbTable_CourseGroup extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_course_tagging_group_assistant';
	protected $_primary = "IdCourseTaggingGroup";
	
	
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
	
	
	public function getGroupList($idSubject,$idSemester){
		
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('cg'=>$this->_name))
					  //->distinct()
					  ->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
					  ->joinLeft(array('stm'=>'tbl_studentregistration'),'stm.IdStudentRegistration=cg.IdLecturer',array('registrationId'))
					  ->joinLeft(array('sp'=>'student_profile'),'stm.IdApplication=sp.appl_id',array('FullName'=>'CONCAT(appl_fname," ",appl_lname)'))
					 // ->joinLeft(array('cgp'=>'course_group_program'),'cg.IdCourseTaggingGroup=cgp.group_id',array())
					 // ->joinLeft(array('p'=>'tbl_program'),'p.IdProgram=cgp.program_id')
					 // ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage'))
					  ->where('cg.IdSubject = ?',$idSubject)
					  ->where('cg.IdSemester = ?',$idSemester);
		
		// echo $select;
		 $row = $db->fetchAll($select);	
		// echo var_dump($row);exit;
		 return $row;
	}
	
	public function getGroupListByProgramBranch($idSubject,$idSemester,$idProgram,$idBranch=null){
	
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cg'=>$this->_name))
		//->distinct()
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
		->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'))
		->joinLeft(array('stm2'=>'tbl_staffmaster'),'stm2.IdStaff=cg.VerifyBy',array('VerFront'=>'FrontSalutation','VerFullName'=>'FullName','VerBack'=>'BackSalutation'))
		->joinLeft(array('cgp'=>'course_group_program_assistant'),'cg.IdCourseTaggingGroup=cgp.group_id',array())
		->joinLeft(array('p'=>'tbl_program'),'p.IdProgram=cgp.program_id')
		->joinLeft(array('cgb'=>'course_group_branch_assistant'),'cg.IdCourseTaggingGroup=cgb.group_id',array())
		->joinLeft(array('br'=>'tbl_branchofficevenue'),'br.IdBranch=cgb.branch_id',array('br.IdBranch','br.BranchName'))
		->where('p.IdProgram = ?',$idProgram)
		->where('IdSemester = ?',$idSemester);
		if ($idSubject!=null ) $select->where('IdSubject = ?',$idSubject);
		if ($idBranch!=null) $select->where('br.IdBranch = ?',$idBranch);
		// echo $select;
		$row = $db->fetchAll($select);
		if (!$row) {
			$select = $db ->select()
			->from(array('cg'=>$this->_name))
			->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
			->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'))

			->joinLeft(array('stm2'=>'tbl_staffmaster'),'stm2.IdStaff=cg.VerifyBy',array('VerFront'=>'FrontSalutation','VerFullName'=>'FullName','VerBack'=>'BackSalutation'))
			->joinLeft(array('cgp'=>'course_group_program_assistant'),'cg.IdCourseTaggingGroup=cgp.group_id',array())
			->joinLeft(array('p'=>'tbl_program'),'p.IdProgram=cgp.program_id')
			->joinLeft(array('cgb'=>'course_group_branch_assistant'),'cg.IdCourseTaggingGroup=cgb.group_id',array())
			->joinLeft(array('br'=>'tbl_branchofficevenue'),'br.IdBranch=cgb.branch_id',array('br.IdBranch','br.BranchName'))
			->where('cg.IdSubject = ?',$idSubject)
			->where('cgp.program_id = ?',$idProgram)
			->where('cg.IdSemester = ?',$idSemester);
			$row = $db->fetchAll($select);
		}
		//echo var_dump($row);exit;
		return $row;	}
	
		
		public function getListofSubject($idSemester,$idprogram){
		 
			$db = Zend_Db_Table::getDefaultAdapter();
		
			$select = $db ->select()
			->distinct()
			->from(array('cg'=>$this->_name),array())
			->joinLeft(array('cgp'=>'course_group_program_assistant'),'cg.IdCourseTaggingGroup=cgp.group_id',array())
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
		->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'))
		->joinLeft(array('cgp'=>'course_group_program_assistant'),'cg.IdCourseTaggingGroup=cgp.group_id')
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'BahasaIndonesia'))
		//->where('cg.IdSubject = ?',$idSubject)
		->where('cgp.program_id = ?',$idprogram)
		->where('cg.IdSemester = ?',$idSemester);
		
		if ($idSubject!=null ) $select->where('cg.IdSubject = ?',$idSubject);
		
		if ($branch!=null) {
			$select->joinLeft(array('cgb'=>'course_group_branch_assistant'),'cg.IdCourseTaggingGroup=cgb.group_id',array())
				   ->joinLeft(array('br'=>'tbl_branchofficevenue'),'br.IdBranch=cgb.branch_id',array('br.IdBranch','br.BranchName'));
			$select->where('cgb.branch_id=?',$branch);
		
		}
		
		$row = $db->fetchAll($select);
		
		//echo var_dump($row);exit;
		return $row;
	}
	
	public function getGroupListPerEntrier($idSubject,$idSemester,$idprogram,$branch=null,$identrier,$mode){
	
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cg'=>$this->_name))
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
			
		if ($branch!=null && $branch!=0) {
			$select->where('cgb.branch_id=?',$branch);
		}
		//echo $select;exit;
		$row = $db->fetchAll($select);
	
		//echo var_dump($row);exit;
		return $row;
	}
	
	public function getGroupListPerEntrierClass($idSubject,$idSemester,$lstd,$identrier,$mode){
	
		$auth = Zend_Auth::getInstance();
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cg'=>$this->_name),array('IdCourseTaggingGroup','GroupName','GroupCode'))
		//->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName')) 
		->join(array('srs'=>'tbl_studentregsubjects_assistant'),'cg.IdCourseTaggingGroup=srs.IdCourseTaggingGroup',array())
		//->where('cgp.program_id = ?',$idprogram)
		->where('cg.IdSemester = ?',$idSemester)
		->where('srs.IdStudentRegistration in ('.$lstd.')')
		->group("cg.IdCourseTaggingGroup");
	
		if ($idSubject!=null ) $select->where('cg.IdSubject = ?',$idSubject);
		if ($identrier!=null ) {
			if ($mode=="1") {
				$select->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'));
	
			} else {
				$select->joinLeft(array('stm'=>'tbl_studentregistration'),'stm.IdStudentRegistration=cg.IdLecturer',array('registrationId'));
			 		
			}
			if ($session->IdRole!=1)
				$select->where('cg.IdLecturer = ?',$identrier);
				
		}
	
		 
		//echo $select;exit;
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
		->join(array('cs'=>'course_group_schedule_assistant'),'cg.idcoursetagginggroup=cs.idgroup',array('sc_day','sc_start_time'))
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
		->joinLeft(array('ss'=>'tbl_staffmaster'),'ss.IdStaff=cg.IdLecturer',array('lecturername'=>'ss.FullName'))
		->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'))
		->joinLeft(array('cgp'=>'course_group_program_assistant'),'cg.IdCourseTaggingGroup=cgp.group_id')
		->joinLeft(array('cgc'=>'course_group_branch_assistant'),'cg.IdCourseTaggingGroup=cgc.group_id')
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
		 
		->join(array('cs'=>'course_group_schedule_assistant'),'cg.idcoursetagginggroup=cs.idgroup',array('sc_id','sc_day','sc_start_time','sc_end_time','sc_venue','IdLecturer'=>'IFNULL(cs.IdLecturer,cg.IdLecturer)','Extra'))
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
		//->join(array('ss'=>'tbl_staffmaster'),'ss.IdStaff=cg.IdLecturer',array('lecturername'=>'ss.FullName','FrontSalutation','FullName','BackSalutation'))
		->join(array('cgp'=>'course_group_program_assistant'),'cg.IdCourseTaggingGroup=cgp.group_id',array())
		->join(array('p'=>'tbl_program'),'p.IdProgram=cgp.program_id',array('ProgramName'=>'ArabicName'))
		->joinLeft(array('cgc'=>'course_group_branch_assistant'),'cg.IdCourseTaggingGroup=cgc.group_id')
		->joinLeft(array('br'=>'tbl_branchofficevenue'),'br.IdBranch=cgc.branch_id',array('branchname'=>'br.BranchName'))
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'BahasaIndonesia','sks'=>'CreditHours'))
	 	->where('cg.IdSemester = ?',$idSemester)
	 	->group('IdLecturer')
		->group('cg.idcoursetagginggroup');
		 
		if($program!=null)  $select->where('cgp.program_id=?',$program);
		if($idcollege!=null)  $select->where('p.IdCollege=?',$idcollege);
		
		$row = $db->fetchAll($select);
	
		//echo var_dump($row);exit;
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
					  ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage','faculty_id'=>'IdFaculty','credithours'))
					  ->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
					  ->joinLeft(array('stm'=>'tbl_studentregistration'),'stm.IdStudentRegistration=cg.IdLecturer',array('registrationId'))
					  ->joinLeft(array('sp'=>'student_profile'),'sp.appl_id=stm.IdApplication',array('FullName'=>'CONCAT(appl_fname," ",appl_lname)'))
					  ->where('IdCourseTaggingGroup = ?',$idGroup);					  
		 $row = $db->fetchRow($select);	
		 return $row;
	}
	
	public function getInfoSchedulle($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cg'=>$this->_name))
		->join(array('sc'=>'course_group_schedule_assistant'),'cg.IdCourseTaggingGroup=sc.idGroup')
		->where('IdCourseTaggingGroup = ?',$idGroup);
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
		->join(array('sc'=>'course_group_schedule_assistant'),'cg.IdCourseTaggingGroup=sc.idGroup')
		->where('sc_id = ?',$idsch);
		$row = $db->fetchAll($select);
	
		return $row;
	}
	
	public function getInfoSchedulleByLecturer($idsemester,$idlec=null,$idsubject=null,$idprogram=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cg'=>$this->_name))
		->join(array('sc'=>'course_group_schedule_assistant'),'cg.IdCourseTaggingGroup=sc.idGroup')
	 	->join(array('pr'=>'course_group_program_assistant'),'pr.group_id=cg.IdCourseTaggingGroup')
					  
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage','faculty_id'=>'IdFaculty','sks'=>'CreditHours'))
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))	  
		
		->where('cg.IdSemester = ?',$idsemester);
		if ($idlec!=null) $select->where('cg.IdLecturer ='.$idlec.' or sc.IdLecturer='.$idlec);
		if ($idsubject!=null) $select->where('cg.IdSubject =?',$idsubject);
		if ($idprogram!=null) $select->where('pr.program_id =?',$idprogram);
		
		$row = $db->fetchAll($select);
		return $row;
	}
	
	 
	
	 
	
	public function getInfoGrroupALl($idGroup,$idprogram=null,$idsem=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		//$idGroup=implode(',',$idGroup);
		$select = $db ->select()
		->distinct()
		->from(array('srs'=>"tbl_studentregsubjects_assistant"),array('IdStudentRegistration'))
		->join(array('tg'=>'tbl_course_tagging_group_assistant'),'tg.IdCourseTaggingGroup=srs.IdCourseTaggingGroup',array())
		->join(array('st'=>'tbl_studentregistration_assistant'),'srs.idstudentregistration=st.idstudentregistration',array('registrationid'))
		->join(array('sp'=>'student_profile'),'st.idApplication=sp.appl_id',array("studentname"=>'CONCAT(appl_fname," ",appl_mname," ",appl_lname)'))
		->where('tg.IdCourseTaggingGroup in ( ? )',$idGroup);
		if ($idprogram!=null) $select->where('st.IdProgram=?',$idprogram);
		if ($idsem!=null) $select->where('tg.IdSemester=?',$idsem);
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
					  ->joinLeft(array('cp'=>'course_group_program_assistant'),'cp.group_id=cg.IdCourseTaggingGroup')
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
								 				 ->from(array('cgs'=>'course_group_schedule_assistant'))
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
		 	$select->join(array('pr'=>'course_group_program_assistant'),'pr.group_id=cg.idcoursetagginggroup');
		 	$select->where('pr.program_id = ?',$program_id);
		 }
		 $select->order('sm.BahasaIndonesia');
		 $row = $db->fetchAll($select);	
		 return $row;
	}
	
	
	public function getCourseTaggingGroupList($idSemester,$idprogram=null,$idsubject=null,$branch=null){		
		
		$auth = Zend_Auth::getInstance();
		
		$id_user = $auth->getIdentity()->registration_id;
		 
		
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
					  ->join(array('cgs'=>'course_group_schedule_assistant'),'cgs.idGroup=cg.IdCourseTaggingGroup')
					  ->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
					  ->join(array('pr'=>'course_group_program_assistant'),'pr.group_id=cg.IdCourseTaggingGroup')
					   ->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','coordinator'=>'FullName','BackSalutation'))
					  ->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('IdSubject','subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage'))					  
					  ->where('cg.IdSemester = ?',$idSemester)					  
					  ->order('SubCode')
					  ->order('IdCourseTaggingGroup');	
	   
					    
					  	//bukan admin
					  	if ($row) {
						  	$select->where('(cg.IdLecturer = ?',$id_user);
						  	$select->orwhere('cgs.IdLecturer = ?',$id_user);
						  	$select->orwhere('cg.IdCourseTaggingGroup in (?))',$operator);
					  	} else {
					  		$select->where('(cg.IdLecturer = ?',$id_user);
					  		$select->orwhere('cgs.IdLecturer = ?)',$id_user);
					  		 
					  	 
					  }	
		//echo $select;exit;	
		if ($idprogram!=null) $select->where('pr.program_id=?',$idprogram);
		if ($idsubject!=null) $select->where('cg.IdSubject=?',$idsubject);
		if ($branch!=null) {
			$select->joinLeft(array('br'=>'course_group_branch_assistant'),'br.group_id=cg.IdCourseTaggingGroup');
			$select->where('br.branch_id=?',$branch);
		}
		$rows = $db->fetchAll($select);
		 //echo var_dump($rows);exit;
		 return $rows;				
	}
	public function getCourseBranch($idCoursetaggingGroup){
	 
		$db = Zend_Db_Table::getDefaultAdapter();
	
		 
		$select = $db ->select()
		 ->from(array('br'=>'course_group_branch_assistant'))
		 ->join(array('brh'=>'tbl_branchofficevenue'),'br.branch_id=brh.IdBranch')
		 ->where('br.group_id = ?',$idCoursetaggingGroup); 
	 
		$rows = $db->fetchAll($select);
		//echo var_dump($rows);exit;
		return $rows;
	}
	
	public function getCourseBranchByProgram($semester,$program){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
			
		$select = $db ->select()
		->distinct()
		->from(array('br'=>'course_group_branch_assistant'),array())
		->join(array('ctg'=>'tbl_course_tagging_group_assistant'),'br.group_id=ctg.idCourseTaggingGroup',array())
		->join(array('cp'=>'course_group_program_assistant'),'cp.group_id=ctg.idCourseTaggingGroup',array())
		->join(array('brh'=>'tbl_branchofficevenue'),'br.branch_id=brh.IdBranch',array('key'=>'IdBranch','value'=>'BranchName'))
		->where('ctg.IdSemester = ?',$semester)
		->where('cp.program_id = ?',$program);
	
		$rows = $db->fetchAll($select);
		//echo var_dump($rows);exit;
		return $rows;
	}
	
	
    public function getMarkEntryGroupListt($idSubject,$idSemester,$idprogram=null){
		
		$auth = Zend_Auth::getInstance();
		
		//print_r($auth->getIdentity());
	
		$db = Zend_Db_Table::getDefaultAdapter();	

		
		
		$select = $db ->select()
					  ->from(array('cg'=>$this->_name))
					  ->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
					  ->join(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation','StaffId'))
					  ->join(array('cp'=>'course_group_program'),'cp.group_id=cg.IdCourseTaggingGroup',array())
					  ->where('IdSubject = ?',$idSubject)
					  ->where('IdSemester = ?',$idSemester);	
		 if(!($auth->getIdentity()->IdRole==1 || $auth->getIdentity()->IdRole==298 || $auth->getIdentity()->IdRole==470 || $auth->getIdentity()->IdRole==605 || $auth->getIdentity()->IdRole==480)){//sekiranya bukan admin dia boleh tgk group dia sahaja
		 	$select->where('IdLecturer = ?', $auth->getIdentity()->IdStaff); }
		 if ($idprogram !=null ) $select->where('cp.program_id=?',$idprogram);
		 //end if
		//echo $idprogram;echo $select;exit;
		$rows = $db->fetchAll($select);
		 return $rows;
	}
	public function getCountStudentCourseList($idprogram,$idbranch,$idSemester){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name),array('nOfStudent'=>'count(*)'))
		->join(array('srs'=>'tbl_studentregsubjects_assistant'),'a.IdCourseTaggingGroup=srs.IdCourseTaggingGroup')
		->join(array('s'=>'tbl_subjectmaster'),'a.IdSubject=s.IdSubject',array('SubCode','SubjectName','CreditHours'))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration')
		->join(array('cp'=>'course_group_program_assistant'),'a.IdCourseTaggingGroup=cp.group_id')
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
		->from(array('a'=>'tbl_studentregsubjects_assistant'),array('nOfStudent'=>'count(*)'))
		 
		->where('a.IdCourseTaggingGroup = ?',$idGroup)  ;
		$row = $db->fetchRow($select);
		//echo var_dump($row);exit;
		return $row['nOfStudent'];
	
	}
	public function getCountStudentBySetOfCourse($idprogram,$idbranch,$idSemester,$idsubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name),array('nOfStudent'=>'count(*)','GroupCode','GroupName','IdCourseTaggingGroup'))
		->join(array('srs'=>'tbl_studentregsubjects_assistants'),'a.IdCourseTaggingGroup=srs.IdCourseTaggingGroup',array())
		//->join(array('s'=>'tbl_subjectmaster'),'a.IdSubject=s.IdSubject',array('Subcode','SubjectName','CreditHours'))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration',array())
		->join(array('cp'=>'course_group_program_assistant'),'a.IdCourseTaggingGroup=cp.group_id',array())
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
		 ->join(array('srs'=>'tbl_studentregsubjects_assistant'),'a.IdCourseTaggingGroup=srs.IdCourseTaggingGroup',array('IdStudentRegistration'))
		->where('a.IdCourseTaggingGroup=?',$idGroup);
		$row = $db->fetchAll($select);
			
		return $row;
	
		//
	}
	
	public function isCourseGroup($idsemester,$idsubject,$groupcode) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
			
		$select = $db ->select()
			
		->from(array('a'=>$this->_name))
		->where('a.IdSubject=?',$idsubject)
		->where('a.IdSemester=?',$idsemester)
		->where('a.GroupCode=?',$groupcode);
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
	
	 
	
}