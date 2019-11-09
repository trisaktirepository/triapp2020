<? 

class Assistant_Model_DbTable_Studentregsubjects extends Zend_Db_Table_Abstract {
	
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_studentregsubjects_assistant';
	protected $_primary = "IdStudentRegSubjects";

	public function getRegisterSem($idstd) {
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select =$db->select()
		->distinct()
		->from(array('srs'=>$this->_name),array('IdSemesterMain'))
		->join(array('sm'=>'tbl_semestermaster'),'srs.IdSemesterMain=sm.IdSemesterMaster',array('IsCountable'))
		 ->where('srs.IdStudentRegistration = ?',$idstd)
		->order('sm.SemesterMainStartDate');
		$row = $db->fetchAll($select);
		 return $row;
	}
	
	public function addData($data){	
		$db = Zend_Db_Table::getDefaultAdapter();
	   $id = $db->insert($this->_name,$data);
	   return $id;
	}
	
	public function updateData($data,$id){
		$db = Zend_Db_Table::getDefaultAdapter();
		 $db->update($this->_name,$data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){		
		$db = Zend_Db_Table::getDefaultAdapter();
	  	$db->delete($this->_name,$this->_primary .' =' . (int)$id." and exam_status is null and grade_name is null");
	}
	
	public function getData($id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select =$db->select()
		->from(array('srs'=>$this->_name))
		->where('srs.IdStudentRegSubjects = ?',$id); 
		$row = $db->fetchRow($select);
		return $row;
	}
	public function getTotalRegister($idSubject=null,$idSemester){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select =$db->select()
	 				 ->from(array('srs'=>$this->_name))
	 				 ->where('srs.IdSubject = ?',$idSubject)
	 				 ->where('srs.IdSemesterMain = ?',$idSemester);
	 	$row = $db->fetchAll($select);
	 	if($row){
	 		return count($row);	
	 	}else{
	 		return null;
	 	}
				 
	}
	public function getTotalRegisterByProgram($idSubject=null,$idSemester,$idprogram){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select =$db->select()
		->from(array('srs'=>$this->_name))
		->join(array('sr'=>'tbl_studentregistration'),'srs.IdStudentRegistration=sr.IdStudentRegistration')
		->where('srs.IdSubject = ?',$idSubject)
		->where('srs.IdSemesterMain = ?',$idSemester)
		->where('sr.IdProgram=?',$idprogram);
		$row = $db->fetchAll($select);
		if($row){
			return count($row);
		}else{
			return null;
		}
			
	}
	
	public function getStudents($idSubject=null,$idSemester,$student_per_group){
		
		$db = Zend_Db_Table::getDefaultAdapter();
			
		 $select =$db->select()
	 				 ->from(array('sr'=>'tbl_studentregistration'))
	 				 ->joinLeft(array('srs'=>$this->_name),'srs.IdStudentRegistration=sr.IdStudentRegistration')
	 				 ->where('srs.IdSubject = ?',$idSubject)
	 				 ->where('srs.IdSemesterMain = ?',$idSemester)
	 				 ->where('srs.IdCourseTaggingGroup = 0')
	 				 ->order("registrationId")
	 				 ->limit($student_per_group,0);
	 	$row = $db->fetchAll($select);
	 	
	 	
	 	return $row;
				 
	}
	public function copyupdateData($groupid,$idstudentregsubjectsOri){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		 
		$select = $db ->select()
		->from(array('srs'=>'tbl_studentregsubjects'))
		->where('srs.IdStudentRegSubjects=?',$idstudentregsubjectsOri);
		$row=$db->fetchRow($select);
		
		 
		 
		$data=array('IdStudentRegistration'=>$row['IdStudentRegistration'],
				 	'IdSubject'=>$row['IdSubject'],
					'IdSemesterMain'=>$row['IdSemesterMain'],
					'UpdUser'=>$auth->getIdentity()->id,
					'UpdDate'=>date("Y-m-d H:i:s"),
					 'Active'=>"1",
					'IdCourseTaggingGroup'=>$groupid,
					'IdStudentRegSubjects_P'=>$row['IdStudentRegSubjects']
				);
		
		$this->addData($data);
		 
	}
	public function getUnTagGroupStudents($idSubject,$idSemester,$limit=""){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$curstd=$db->select()
			->from(array('srsa'=>$this->_name),array('IdStudentRegistration'))
			->where('srsa.IdSubject = ?',$idSubject)
			->where('srsa.IdSemesterMain = ?',$idSemester);
		
		$select =$db->select()
	 				 ->from(array('sr'=>'tbl_studentregistration'))
	 				 ->joinLeft(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration=sr.IdStudentRegistration')
	 				 ->joinLeft(array('ap'=>'student_profile'),'ap.appl_id=sr.IdApplication',array('appl_fname','appl_mname','appl_lname'))
	 				 ->where('srs.IdSubject = ?',$idSubject)
	 				 ->where('srs.IdSemesterMain = ?',$idSemester)
	 				 ->where('srs.IdStudentRegistration not in (?)',$curstd)//0:No Group
	 				 ->order("registrationId");
		
	 	if($limit!=""){
	 		$select->limit($limit);
	 	}
	 	$row = $db->fetchAll($select);
	 	return $row;
				 
	}
	
	public function getTaggedGroupStudents($idGroup){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select =$db->select()
	 				 ->from(array('sr'=>'tbl_studentregistration'))
	 				 ->joinLeft(array('srs'=>$this->_name),'srs.IdStudentRegistration=sr.IdStudentRegistration')
	 				 ->joinLeft(array('ap'=>'student_profile'),'ap.appl_id=sr.IdApplication',array('appl_fname','appl_mname','appl_lname'))	 				 
	 				 ->where('srs.IdCourseTaggingGroup = ?',$idGroup)//0:No Group
	 				 ->order("registrationId");
	 				
	 	$row = $db->fetchAll($select);
	 	return $row;
				 
	}
	public function getApproveGroupStudents($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select =$db->select()
		->from(array('sr'=>'tbl_studentregistration'))
		->joinLeft(array('srs'=>$this->_name),'srs.IdStudentRegistration=sr.IdStudentRegistration')
		->joinLeft(array('ap'=>'student_profile'),'ap.appl_id=sr.IdApplication',array('appl_fname','appl_mname','appl_lname'))
		->where('srs.IdCourseTaggingGroup = ?',$idGroup)
		->where('srs.ApprovedBy != 0')
		//0:No Group
		->order("registrationId");
	
		$row = $db->fetchAll($select);
		return $row;
			
	}
	public function getCountGroupStudents($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select =$db->select()
		->from(array('srs'=>$this->_name),array('count'=>'count(Registrationid)'))
		->join(array('sr'=>'tbl_studentregistration'),'srs.IdStudentRegistration=sr.IdStudentRegistration')
		->where('srs.IdCourseTaggingGroup = ?',$idGroup)//0:No Group
		->group("idCourseTaggingGroup");
	
		$row = $db->fetchRow($select);
		return $row;
			
	}
	public function getCountActiveStudents($program,$semester){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select =$db->select()
		->from(array('srs'=>$this->_name),array('count'=>'count(distinct Registrationid)'))
		->join(array('sr'=>'tbl_studentregistration'),'srs.IdStudentRegistration=sr.IdStudentRegistration')
		->where('srs.IdSemesterMain = ?',$semester)
		->where('sr.IdProgram = ?',$program);
	
		$row = $db->fetchRow($select);
		return $row;
			
	}
	public function getCountStudentsPerIntake($IdIntake,$idCollege=null,$idprogram=null,$strata=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select =$db->select()
		->from(array('srs'=>$this->_name),array('count'=>'count(distinct srs.IdStudentRegistration)'))
		->join(array('sr'=>'tbl_studentregistration'),'srs.IdStudentRegistration=sr.IdStudentRegistration',array())
		->join(array('pr'=>'tbl_program'),'sr.IdProgram=pr.IdProgram',array())
		->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=srs.IdSemesterMain',array())
		->join(array('ac'=>'tbl_academic_year'),'sm.idacadyear=ac.ay_id',array('ay_code'))
		->join(array('i'=>'tbl_intake'),'sr.IdIntake=i.IdIntake',array())
		//0:No Group
		->where('left(i.IntakeId,9)=?',$IdIntake)
		->group('ac.ay_code')
		->order("ac.ay_code ASC");
		
		if ($idprogram!=null) {
			$select->where('sr.IdProgram = ?',$idprogram);
		} else
		if ($idCollege!=null) {
			$select->join(array('cl'=>'tbl_collegemaster'),'pr.IdCollege=cl.IdCollege',array('cl.IdCollege','CollegeName'));
			$select->where('pr.IdCollege = ?',$idCollege);
		}
		if ($strata!=null) $select->where('pr.strata=?',$strata);
		//echo $select;
		//exit;
		//echo $select;exit;
		$row = $db->fetchAll($select);
		return $row;
			
	}
	
	public function getListGroupClass($semester,$idprogram){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select =$db->select()
		->from(array('srs'=>$this->_name))
		//->join(array('sr'=>'tbl_studentregistration'),'srs.IdStudentRegistration=sr.IdStudentRegistration')
		->join(array('sb'=>'tbl_subjectmaster'),'sb.idSubject=srs.idSubject',array('SubjectName'=>'BahasaIndonesia',"SubCode"=>'ShortName','CreditHours'))
		->join(array('grp'=>'tbl_course_tagging_group_assistant'),'srs.idCourseTaggingGroup=grp.idCourseTaggingGroup',array('idCourseTaggingGroup','GroupName','GroupCode','maxstud'))
		->joinLeft(array('lec'=>'tbl_staffmaster'),'grp.IdLecturer=lec.idStaff',array('lec.FullName'))
		->joinLeft(array('lec1'=>'tbl_staffmaster'),'grp.VerifyBy=lec1.idStaff',array('Verificator'=>'lec1.FullName'))
		->join(array('cgs'=>'course_group_schedule_assistant'),'grp.idCourseTaggingGroup=cgs.idGroup',array('Day'=>'sc_day','sc_start_time','sc_end_time','sc_venue'))
		->joinLeft(array('def'=>'tbl_definationms'),'lec.BackSalutation=def.idDefinition',array("BackSalutation"=>'BahasaIndonesia'))
		->joinLeft(array('def1'=>'tbl_definationms'),'lec.FrontSalutation=def1.idDefinition',array("FrontSalutation"=>'BahasaIndonesia'))
		->join(array('prg'=>'course_group_program_assistant'),'grp.idCourseTaggingGroup=prg.group_id')
		->where('srs.IdSemesterMain = ?',$semester)
		->where('prg.program_id= ?',$idprogram)
		//0:No Group
		->group("srs.idCourseTaggingGroup")
		->order('sb.BahasaIndonesia','grp.GroupCode');
	
		$row = $db->fetchAll($select);
		return $row;
			
	}
	
	public function getListGroupClassNoStd($semester,$idprogram,$idsubject=null,$day=null,$lecturer=null,$room=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select =$db->select()
		//->from(array('srs'=>$this->_name))
		//->join(array('sr'=>'tbl_studentregistration'),'srs.IdStudentRegistration=sr.IdStudentRegistration')
		->from(array('grp'=>'tbl_course_tagging_group_assistant'),array('IdCourseTaggingGroup','GroupName','GroupCode','maxstud'))
		->join(array('sb'=>'tbl_subjectmaster'),'sb.idSubject=grp.idSubject',array('SubjectName'=>'BahasaIndonesia',"SubCode"=>'ShortName','CreditHours'))
		
		->joinLeft(array('lec'=>'tbl_staffmaster'),'grp.IdLecturer=lec.idStaff',array('lec.FullName'))
		->joinLeft(array('lec1'=>'tbl_staffmaster'),'grp.VerifyBy=lec1.idStaff',array('Verificator'=>'lec1.FullName'))
		->join(array('cgs'=>'course_group_schedule'),'grp.idCourseTaggingGroup=cgs.idGroup',array('Day'=>'sc_day','sc_start_time','sc_end_time','sc_venue'))
		->joinLeft(array('def'=>'tbl_definationms'),'lec.BackSalutation=def.idDefinition',array("BackSalutation"=>'BahasaIndonesia'))
		->joinLeft(array('def1'=>'tbl_definationms'),'lec.FrontSalutation=def1.idDefinition',array("FrontSalutation"=>'BahasaIndonesia'))
		->join(array('prg'=>'course_group_program_assistant'),'grp.idCourseTaggingGroup=prg.group_id')
		->where('grp.IdSemester = ?',$semester)
		->where('prg.program_id= ?',$idprogram)
		//0:No Group
		->group("grp.idCourseTaggingGroup")
		->order('sb.BahasaIndonesia','grp.GroupCode');
		if ($day!=null) $select->where('cgs.sc_day=?',$day);
		if ($room!=null) $select->where('cgs.sc_venue=?',$room);
		if ($lecturer!=null) $select->where('grp.IdLecturer=?',$lecturer);
		if ($idsubject!=null) $select->where('grp.idSubject=?',$idsubject);
		$row = $db->fetchAll($select);
		return $row;
			
	}
	public function getListGroupClassPerDay($semester,$idprogram=null,$day,$build=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select =$db->select()
		->from(array('grp'=>'tbl_course_tagging_group_assistant'),array('GroupName','GroupCode','maxstud'))
		->join(array('sb'=>'tbl_subjectmaster'),'sb.idSubject=grp.idSubject',array('sb.IdSubject','SubjectName'=>'BahasaIndonesia',"SubCode"=>'ShortName','CreditHours'))
		->join(array('lec'=>'tbl_staffmaster'),'grp.IdLecturer=lec.idStaff',array('FullName'))
		->joinLeft(array('def'=>'tbl_definationms'),'lec.BackSalutation=def.idDefinition',array("BackSalutation"=>'BahasaIndonesia'))
		->joinLeft(array('def1'=>'tbl_definationms'),'lec.FrontSalutation=def1.idDefinition',array("FrontSalutation"=>'BahasaIndonesia'))
		->join(array('prg'=>'course_group_program_assistant'),'grp.idCourseTaggingGroup=prg.group_id')
		->join(array('sc'=>'course_group_schedule_assistant'),'grp.idCourseTaggingGroup=sc.idGroup',array('sc_date','sc_start_time','sc_end_time','sc_venue'))
		->where('grp.IdSemester = ?',$semester)
		//->where('prg.program_id= ?',$idprogram)
		->where('sc.sc_day=?',$day)
		->group('grp.idCourseTaggingGroup')
		->order('sc.sc_venue')
		->order('sc.sc_start_time');
	
		if ($idprogram!=null) $select->where('prg.program_id= ?',$idprogram); 
		if ($build!=null) $select->where('left(sc.sc_venue,2)= ?',$build);
		
		$row = $db->fetchAll($select);
		return $row;
			
	}
	
	
	public function getTotalAssigned($idSubject=null,$idSemester){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select =$db->select()
	 				 ->from(array('srs'=>$this->_name))
	 				 ->where('srs.IdSubject = ?',$idSubject)
	 				 ->where('srs.IdSemesterMain = ?',$idSemester)
	 				 ->where('IdCourseTaggingGroup != 0');
	 	$row = $db->fetchAll($select);
	 	
	 	if($row){
	 		return count($row);	
	 	}else{
	 		return null;
	 	}
				 
	}
	
	public function getTotalUnAssigned($idSubject=null,$idSemester){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select =$db->select()
	 				 ->from(array('srs'=>$this->_name))
	 				 ->where('srs.IdSubject = ?',$idSubject)
	 				 ->where('srs.IdSemesterMain = ?',$idSemester)
	 				 ->where('IdCourseTaggingGroup = 0');
	 	$row = $db->fetchAll($select);
	 	
	 	if($row){
	 		return count($row);	
	 	}else{
	 		return null;
	 	}
				 
	}
	
	
	public function updateRegistrationData($data,$id){
		 $this->update($data, 'IdStudentRegistration = '. $id);
	}
	
	public function deleteRegisterSubjects($id){		
	  $this->delete('IdStudentRegistration =' . (int)$id. " and exam_status='' and grade_name=''");
	}
	
	public function getRegStudents($idSubject=null,$idSemester,$idCollege="",$post=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
			
		 $select =$db->select()
		 			->distinct()
	 				 ->from(array('sr'=>'tbl_studentregistration'),array("registrationId"))	 				 
	 				 ->join(array('tp'=>'tbl_program'),'sr.idProgram=tp.idProgram',array())
	 				 ->join(array('tc'=>'tbl_collegemaster'),'tp.idCollege=tc.idCollege',array("collegename","idCollege"))
	 				 ->join(array('sp'=>"student_profile"),"sp.appl_id=sr.IdApplication",array("appl_fname","appl_mname","appl_lname"))
	 				 ->joinLeft(array('srs'=>$this->_name),'srs.IdStudentRegistration=sr.IdStudentRegistration')
	 				 ->joinLeft(array('at'=>'applicant_transaction'),'at.at_trans_id=sr.transaction_id',array('at_pes_id'))
	 				 ->joinLeft(array('lk'=>'sis_setup_detl'),"lk.ssd_id=sp.appl_religion",array("religion"=>"ssd_name"))
	 				 ->where('srs.IdSubject = ?',$idSubject)
	 				 ->where('srs.IdSemesterMain = ?',$idSemester)
	 				 ->order("registrationId");
	 			
	 				 
		if (isset($post['religion']) && !empty($post['religion'])) {           
            $select->where("sp.appl_religion = ?",$post['religion']);
        }
		if (isset($post['IdCollege']) && !empty($post['IdCollege'])) {           
            $select->where('tc.idCollege = ?',$post["IdCollege"]);
        }
	    if (isset($post['applicant_name']) && !empty($post['applicant_name'])) {
         
            $select->where("(sp.appl_fname LIKE '%". $post['applicant_name']."%'");
            $select->orwhere("sp.appl_mname LIKE '%". $post['applicant_name']."%'");
            $select->orwhere("sp.appl_lname LIKE '%". $post['applicant_name']."%')");
        }
        if (isset($post['IdBranch']) && !empty($post['IdBranch'])) {
        	$select->where("sr.IdBranch = ?",$post['IdBranch']);
        }
        
        if (isset($post['IdIntake']) && !empty($post['IdIntake'])) {
        	$select->where("sr.IdIntake = ?",$post['IdIntake']);
        }
        
        if (isset($post['IdMajoring']) && !empty($post['IdMajoring'])) {
        	$select->where("sr.IdProgramMajoring = ?",$post['IdMajoring']);
        }
        if (isset($post['s_student_id']) && !empty($post['s_student_id'])) { 
        	if (isset($post['e_student_id']) && !empty($post['e_student_id'])) {           
            	$select->where("sr.registrationId >= '". $post['s_student_id']."' AND sr.registrationId <= '". $post['e_student_id']."'");
        	}else{
        		$select->where("sr.registrationId >= '". $post['s_student_id']."' AND sr.registrationId <= '". $post['s_student_id']."'");
        	}
        }
        
	    if (isset($post['at_pes_id']) && !empty($post['at_pes_id'])) {           
            $select->where("at.at_pes_id LIKE '%". $post['at_pes_id']."%'");
        }    
		
        $row = $db->fetchAll($select);
	 	
	 	
	 	return $row;
				 
	}
	
	public function dropRegisterSubjects($id,$idsemester,$idprogram){	
		
		$auth = Zend_Auth::getInstance();
		$db = Zend_Db_Table::getDefaultAdapter();
		$reginfo = $this->getSubjectRegRaw($id);
		if ($idsemester=='') $idsemester=$reginfo['IdSemesterMain'];
		$studentregDB = new Registration_Model_DbTable_Studentregistration();
		$studInfo = $studentregDB->getInfo($reginfo["IdStudentRegistration"]);
		if ($idprogram=='') $idprogram=$studInfo['IdProgram'];
		//$reg=$this->getSubjectRegRaw($id);
		$authorized=true;
		if($auth->getIdentity()->IdRole=="445" || $auth->getIdentity()->IdRole=="3" ){
			if($studInfo["AcademicAdvisor"]!=$auth->getIdentity()->IdStaff){
				$authorized = false;
			}
		}
		$ssemDB = new Registration_Model_DbTable_Studentsemesterstatus();
		$issemopen = $ssemDB->checkSemesterCourseRegistration($idsemester,$idprogram);
		 
        $issemopen = array();//pak agung nak sentiasa open dekat sis
		
		if($authorized){
			
			if(is_array($issemopen)){
				$this->saveHistory($reginfo,"DROP");
				 
				$n =$this->delete('IdStudentRegSubjects =' . (int)$id." and (exam_status=''  or exam_status is null)  and (grade_name='' or grade_name is null)");
				//dele also for course_group_mapping
				if ($n>0) {
					$where='IdStudent='.$reginfo["IdStudentRegistration"].' and IdCourseTaggingGroup='.$reginfo['IdCourseTaggingGroup'];
				
					$db->delete('tbl_course_group_student_mapping',$where);
					//echo "masuk";exit;
					$returnVal["ERROR"]= null;
				} else $returnVal["ERROR"]= "NOT DELETED";
			}else{
				$returnVal["ERROR"]="POLICY ERROR : Subject can be only drop during Add&Drop Period";
				echo $returnVal["ERROR"]; 
			}
		}else{			
			$returnVal["ERROR"]= "AUTHORIZATION ERROR"; 
		}
	  	
		return $returnVal;
	}	
	
	public function saveHistory($data,$activity){
		$auth = Zend_Auth::getInstance();
		$db = Zend_Db_Table::getDefaultAdapter();
		$data["hdate"]=date("Y-m-d H:i:s");
		$data["huser"]=$auth->getIdentity()->id;
		$data["hactivity"]=$activity;		
		$db->insert("studentregsubjects_history_assistant",$data);
	}
	
	public function getSubjectInfo($idStudentRegistration,$idSubject,$idSemester=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select =$db->select()		 			
	 				 ->from(array('srs'=>'tbl_studentregsubjects_assistant'))	 				
	 				 ->where('srs.IdStudentRegistration = ?',$idStudentRegistration)
	 				 ->where('srs.IdSubject = ?',$idSubject)
	 				 ->where('srs.IdSemesterMain = ?',$idSemester);
	 	$row = $db->fetchRow($select);
	 	
	 	return $row;
		
	}
	
	public function getSubjectRegistrationInfo($idStudentRegistration,$idSemester=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select =$db->select()		 			
	 				 ->from(array('srs'=>'tbl_studentregsubjects_assistant'))	 				
	 				 ->where('srs.IdStudentRegistration = ?',$idStudentRegistration)
	 				 ->where('srs.IdSemesterMain = ?',$idSemester)
	 				 ->group("srs.IdStudentRegistration");
	 				 
	 	$row = $db->fetchRow($select);
	 	
	 	return $row;
		
	}
	
	public function getRegisteredSubject($idStudentRegistration,$semester_id, $type=null){
	
	  $db = Zend_Db_Table::getDefaultAdapter();
	  	
	  $select =$db->select()
	  ->from(array('srs'=>'tbl_studentregsubjects_assistant'))
	  ->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject = srs.IdSubject')
	  ->where('srs.IdStudentRegistration = ?',$idStudentRegistration)
	  ->where('srs.IdSemesterMain = ?',$semester_id);
	  
	  if($type){
	  	$select->where('srs.subjectlandscapetype in ('.$type.')');
	  }
	  	
	  $row = $db->fetchAll($select);
	   
	  return $row;
	
	}
	
	public function getUnInvoiceRegisteredSubject($idStudentRegistration,$semester_id, $type=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select =$db->select()
		->from(array('srs'=>'tbl_studentregsubjects_assistant'))
		->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject = srs.IdSubject')
		->where('srs.IdStudentRegistration = ?',$idStudentRegistration)
		->where('srs.invoice < 100')
		->where('srs.IdSemesterMain = ?',$semester_id);
		 
		if($type){
			$select->where('srs.subjectlandscapetype in ('.$type.')');
		}
	
		$row = $db->fetchAll($select);
	
		return $row;
	
	}
	
	public function getUnInvoiceRepeatRegisteredSubject($idStudentRegistration,$semester_id, $type=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select =$db->select()
		->from(array('srs'=>'tbl_studentregsubjects_assistant'))
		->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject = srs.IdSubject')
		->where('srs.IdStudentRegistration = ?',$idStudentRegistration)
		->where('srs.invoice < 100')
		->where('srs.IdSemesterMain = ?',$semester_id);
			
		if($type){
			$select->where('srs.subjectlandscapetype in ('.$type.')');
		}
		
		$row = $db->fetchAll($select);
		
		foreach ($row as $key=>$value) {
			//remove new subjects
			$select =$db->select()
			->from(array('srs'=>'tbl_studentregsubjects_assistant'))
			->where('srs.IdStudentRegistration = ?',$value['IdStudentRegistration'])
			->where('srs.IdSubject = ?',$value['IdSubject'])
			->where('srs.IdSemesterMain != ?',$value['IdSemesterMain']);
			$ulang=$db->fetchRow($select);
			if (!$ulang)
				unset($row[$key]);
		}
		//echo var_dump($row);exit;
		return $row;
	
	}
	
	public function getRegisteredSubjectByProgram($idprogram,$semester_id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select =$db->select()
		->from(array('srs'=>'tbl_studentregsubjects_assistant'),array())
		->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject = srs.IdSubject',array('IdSubject','SubCode','SubjectName'=>'BahasaIndonesia'))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration',array())
		->where('sr.IdProgram = ?',$idprogram)
		->where('srs.IdSemesterMain = ?',$semester_id)
		->group('s.IdSubject');
		 
		$row = $db->fetchAll($select);
	
		return $row;
	
	}
	public function getRegisteredSubjectByProgramNoStd($idprogram,$semester_id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select =$db->select()
		->from(array('cgt'=>'tbl_course_tagging_group_assistant'))
		->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject = cgt.IdSubject',array('IdSubject','SubCode','SubjectName'=>'BahasaIndonesia'))
		->join(array('cgp'=>'course_group_program_assistant'),'cgt.IdCourseTaggingGroup=cgp.group_id',array())
		->where('cgp.program_id = ?',$idprogram)
		->where('cgt.IdSemester = ?',$semester_id)
		->group('s.IdSubject')
		->order('s.BahasaIndonesia');
			
		$row = $db->fetchAll($select);
	//echo var_dump($row);exit;
		return $row;
	
	}
	
	
	public function isRegistered($idStudentRegistration,$idSubject,$idsemester=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select =$db->select()		 			
	 				 ->from(array('srs'=>'tbl_studentregsubjects_assistant'))	 				
	 				 ->where('srs.IdStudentRegistration = ?',$idStudentRegistration)
	 				 ->where('srs.IdSubject = ?',$idSubject);
	 	if ($idsemester!=null)	 $select->where('srs.IdSemesterMain = ?',$idsemester);
	 	$row = $db->fetchRow($select);
	 	
	 	return $row;
		
	}
	
	public function getSubjectRegInfo($IdStudentRegSubjects){
		
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select =$db->select()		 			
	 				 ->from(array('srs'=>'tbl_studentregsubjects'))	
	 				 ->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration') 				
	 				 ->where('srs.IdStudentRegSubjects = ?',$IdStudentRegSubjects);
	 				
	 	$row = $db->fetchRow($select);
	 	
	 	return $row;
		
	}
	
	public function deleteParentSubjects($idStudentRegistration,$idSubject){	
	
	  $this->delete('IdStudentRegistration ='.$idStudentRegistration.' AND IdSubject ='.$idSubject." and exam_status='' and grade_name=''");
	}
	
	public function getSubjectRegRaw($IdStudentRegSubjects){
		
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select =$db->select()		 			
	 				 ->from(array('srs'=>'tbl_studentregsubjects'))	
	 				 ->where('srs.IdStudentRegSubjects = ?',$IdStudentRegSubjects);
	 				
	 	$row = $db->fetchRow($select);
	 	
	 	return $row;
		
	}	
	
	
	public function getSumCreditHour($idStudentRegistration,$semester_id){
	
	  $db = Zend_Db_Table::getDefaultAdapter();
	  	
	  $select =$db->select()
	  			  ->from(array('srs'=>'tbl_studentregsubjects_assistant'),array("TotalCH"=>"SUM(CreditHours)"))
				  ->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject = srs.IdSubject',array())
				  ->where('srs.IdStudentRegistration = ?',$idStudentRegistration)
				  ->where('srs.IdSemesterMain = ?',$semester_id)
				  ->where('srs.subjectlandscapetype != 2')
				  ->where('(srs.Active = 1 OR srs.Active = 4 OR srs.Active = 5 )');
				  	
	  $row = $db->fetchRow($select);
	   
	  return $row;
	
	}
	
	public function getCountApproved($idStudentRegistration,$semester_id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select =$db->select()
		->from(array('srs'=>'tbl_studentregsubjects_assistant'),array("Count"=>"count(IdStudentRegSubjects)",'ApprovedBy'))
		//->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject = srs.IdSubject',array())
		->where('srs.IdStudentRegistration = ?',$idStudentRegistration)
		->where('srs.IdSemesterMain = ?',$semester_id)
		->where('srs.subjectlandscapetype != 2')
		->where('(srs.Active = 1 OR srs.Active = 4 OR srs.Active = 5 )')
		->group('srs.ApprovedBy');
		$row = $db->fetchAll($select);
		//echo var_dump($row);exit;
		return $row;
	
	}
	
	public function getBlockRegSubject(){
		  
		$db = Zend_Db_Table::getDefaultAdapter();
	  	
	 	
		 $select = 'SELECT A.IdStudentRegSubjects,A.IdSubject, A.IdBlock, A.subjectlandscapetype, B.type  , B.parentId
				 	FROM `tbl_studentregsubjects` A
					INNER JOIN tbl_landscapeblocksubject B on B.blockid = A.IdBlock and B.subjectid = A.`IdSubject`
					WHERE A.`IdBlock` is not null
					AND A.`subjectlandscapetype` = 1
					AND A.subjectlandscapetype != B.type limit 5,6';
					$row = $db->fetchAll($select);
		 
	 			    return $row;
	
	}
	
	public function isGroupApproved($idSemester,$idprogram,$idSubject,$idGroup){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('srs'=>'tbl_studentregsubjects'),array('nOfStudent'=>'Count(*)','mark_approveby'))
		->join(array('a'=>'tbl_course_tagging_group'),'a.IdCourseTaggingGroup=srs.IdCourseTaggingGroup')
		->join(array('cp'=>'course_group_program'),'a.IdCourseTaggingGroup=cp.group_id')
		->where('srs.IdSemesterMain = ?',$idSemester)
		->where('srs.IdCourseTaggingGroup = ?',$idGroup)
		->where("cp.program_id = ?",$idprogram)
		->where('srs.IdSubject = ?',$idSubject)
		->group('srs.mark_approveby');
	
		$row = $db->fetchAll($select);
	//	if ($idSubject==3168) {echo var_dump($row);exit;}
		return $row;
	}
	
	public function isAttend($idstudentregsubjects) {
		
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('srs'=>'tbl_studentregsubjects'))
		->where('IdStudentRegSubjects=?',$idstudentregsubjects);
		
		$row = $db->fetchRow($select);
		$idsemester=$row['IdSemesterMain'];
		$idgroup=$row['IdCourseTaggingGroup'];
		$idstudentregistration=$row['IdStudentRegistration'];
		$idsubject=$row['IdSubject'];
		
		$select = $db->select()
		->from(array('cga'=>"course_group_attendance_assistant"))
		->join(array('cgad'=>'course_group_attendance_detail'),'cga.id=cgad.course_group_att_id')
		->join(array('ctg'=>'tbl_course_tagging_group'),'cga.group_id=ctg.idCourseTaggingGroup')
		->where('ctg.IdSemester=?',$idsemester)
		->where('ctg.IdSubject=?',$idsubject)
		->where('cga.group_id=?',$idgroup)
		->where('cgad.student_id=?',$idstudentregistration);
		$row = $db->fetchRow($select);
		if ($row) return true; else return false;
		
	}
	
	public function getRegSubjectbyBlock($idStudentRegistration,$blockid)
	{
	$db = Zend_Db_Table::getDefaultAdapter();
	  
	  $select =$db->select()
	  ->from(array('srs'=>'tbl_studentregsubjects_assistant'))
	  ->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject = srs.IdSubject')
	  ->where('srs.IdStudentRegistration = ?',$idStudentRegistration)
	  ->where('srs.IdBlock = ?',$blockid);
	  
	  	  
	  $row = $db->fetchAll($select);
	   
	  return $row;
	}
}

?>