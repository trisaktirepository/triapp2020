<?php


class App_Model_Record_DbTable_StudentRegistration extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_studentregistration';
	protected $_primary = "IdStudentRegistration";
	
	
	public function getData($appl_id=0) {
		
		if($appl_id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('sr'=>$this->_name))	
					->join(array('pr'=>'tbl_program'),'sr.IdProgram=pr.IdProgram')				
					->where('sr.IdApplication = ?',$appl_id)
					->where('sr.profileStatus = 92 or sr.profileStatus =248'); //Active
					
			$stmt = $db->query($select);						
			$row = $stmt->fetch();		
		}else{
			$row = $this->fetchAll();
			$row=$row->toArray();
		}
		
		if(!$row){
			//throw new Exception("There is No Information Found");
		}
		return $row;
	}
	
	
	public function getStudentInfo($id=0) {
			
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('sr'=>$this->_name))
				->join(array('ap'=>'student_profile'),'ap.appl_id=sr.IdApplication',array('StudentName'=>'CONCAT(appl_fname," ",appl_mname," ",appl_lname)','appl_fname','appl_mname','appl_lname','appl_phone_hp','appl_birth_place','appl_dob','appl_nationality','appl_email'))
				->join(array('p'=>'tbl_program'),'p.IdProgram=sr.IdProgram',array('ArabicName','ProgramName','ProgramCode','IdProgram','AdvisorDefaultApprove','Departement','Dept_Bahasa','strata','StrataName','OptimalDuration','Gradepoint_rmd','paket','print_majoring','MeetAdvisor'))
				->join(array('i'=>'tbl_intake'),'i.IdIntake=sr.IdIntake',array('intake'=>'IntakeDefaultLanguage','IntakeId'))
				->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=sr.IdSemesterMain',array('scheme'=>'Scheme'))
				->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition=sr.profileStatus',array('StudentStatus'=>'DefinitionDesc'))
				->joinLeft(array('pm'=>'tbl_programmajoring'),'pm.IDProgramMajoring=sr.IDProgramMajoring',array('majoring'=>'BahasaDescription','majoring_english'=>'EnglishDescription'))
				->joinLeft(array('b'=>'tbl_branchofficevenue'),'b.IdBranch=sr.IdBranch',array('BranchName'))
				->joinLeft(array('c'=>'tbl_collegemaster'),'c.IdCollege=p.IdCollege',array('CollegeName'=>'ArabicName','c.IdCollege'))
				->joinLeft(array('sm'=>'tbl_staffmaster'),'sm.IdStaff=sr.AcademicAdvisor',array('AcademicAdvisor'=>'FullName',"FrontSalutation","BackSalutation"))
				->where('sr.IdStudentRegistration = ?',$id);
				//->where('sr.profileStatus = 92 or sr.profileStatus =248'); //Active
				
		//echo $select;
        $row = $db->fetchRow($select);				
		
		return $row;
	}
	
	
     /*
	 * This function to get course registered by semester.
	 */
	public function getCourseRegisteredBySemester($registrationId,$idSemesterMain,$idBlock=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$sql = $db->select()
                        ->from(array('sr' => 'tbl_studentregistration'), array('registrationId','IdProgram'))  
                        ->joinLeft(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration')
                        ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('SubjectName','subjectMainDefaultLanguage','BahasaIndonesia','CreditHours','SubCode'))                     
                        ->where('sr.IdStudentRegistration = ?', $registrationId)
                        ->where("srs.subjectlandscapetype != 2")
                       	->where('srs.IdSemesterMain = ?',$idSemesterMain);   
                                           
        if(isset($idBlock) && $idBlock != ''){ //Block 
        	$sql->where("srs.IdBlock = ?",$idBlock);
        	$sql->order("srs.BlockLevel");
        }  else $sql->order("srs.UpdDate");
		
     
        //echo $sql;     
        $result = $db->fetchAll($sql);
        return $result;
	}
	
	
	
	/*
	 * Get semester regular for particular semester
	 */
	function getSemesterRegular($idsemmain,$idstudreg){
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = "SELECT * FROM `tbl_semestermaster` sm
		inner join tbl_studentsemesterstatus as sss on sss.IdSemesterMain=sm.IdSemesterMaster
		inner join(
		select idacadyear,semestercounttype from tbl_semestermaster where idsemestermaster=".$idsemmain." )semmaster
			on sm.idacadyear=semmaster.idacadyear and sm.semestercounttype=semmaster.semestercounttype
			WHERE
			sss.idstudentregistration = $idstudreg and
			semesterfunctiontype = 0
			";
			$row = $db->fetchRow($sql);
			return $row;
	}
	
	
	/*
	 * This function to get course registered by semester.
	 */
	public function getCourseRegisteredByBlock($registrationId,$idBlock=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		 $sql = $db->select()
                        ->from(array('sr' => 'tbl_studentregistration'), array('registrationId'))  
                        ->joinLeft(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration')   
                        ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('SubjectName','subjectMainDefaultLanguage','BahasaIndonesia','CreditHours','SubCode'))                  
                        ->where('sr.IdStudentRegistration  = ?', $registrationId)
                        ->where("srs.IdBlock = ?",$idBlock)
                        ->order("srs.BlockLevel");
                      
        $result = $db->fetchAll($sql);
        return $result;
	}
	
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	
	public function getCourseRegisteredBySemesterBlock($registrationId,$idSemester,$idBlock=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		 $sql = $db->select()
                        ->from(array('sr' => 'tbl_studentregistration'), array('registrationId','IdProgram'))  
                        ->joinLeft(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration')   
                        ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('SubjectName','subjectMainDefaultLanguage','BahasaIndonesia','CreditHours','SubCode'))                  
                        ->where('sr.IdStudentRegistration  = ?', $registrationId)
                        ->where("srs.IdSemesterMain = ?",$idSemester)
                        
                        ->where("srs.subjectlandscapetype != 2")
                        ->order("srs.BlockLevel");
                                              
        if($idBlock != null)
		{
			$sql->where("srs.IdBlock = ?",$idBlock);
		}
		$result = $db->fetchAll($sql);
        return $result;
	}
    
    public function getCourseRegisteredBySemesterId($registrationId,$idSemesterMain,$idBlock=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		/*$sql = $db->select('SUM CreditHours AS Total')
                        ->from(array('sr' => 'tbl_studentregistration'), array('registrationId'))  
                        ->joinLeft(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration')
                        ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('SubjectName','subjectMainDefaultLanguage','BahasaIndonesia','CreditHours','SubCode'))                     
                        ->where('sr.IdStudentRegistration = ?', $registrationId)
                        ->where('srs.IdSemesterMain IN ('.$idSemesterMain.')')   
                        ->where('srs.subjectlandscapetype != 2');*/   
		$sql = $db->select()
						->distinct()
                        ->from(array('sr' => 'tbl_studentregistration'), array())  
                        ->joinLeft(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration',array())
                        ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('CreditHours','SubCode'))                     
                        ->where('sr.IdStudentRegistration = ?', $registrationId)
                        ->where('srs.IdSemesterMain IN ('.$idSemesterMain.')')   
                        ->where('srs.subjectlandscapetype != 2');
						//->group('srs.IdCourseTaggingGroup');
            //echo $sql;exit;                                                       
        if(isset($idBlock) && $idBlock != ''){ //Block 
        	$sql->where("srs.IdBlock = ?",$idBlock);
        	$sql->order("srs.BlockLevel");
        }  

     //echo $sql;
             
        $result = $db->fetchAll($sql);
       return $result;
	}
	
    public function getSubjectSchedule($id,$idGroup)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $sql = $db->select()
                ->from(array('a' => 'tbl_course_tagging_group'))
                ->joinRight(array('b' => 'course_group_schedule'),'b.idGroup = a.IdCourseTaggingGroup')
                ->where('a.idSubject = ?',(int)$id)
                ->where('a.IdCourseTaggingGroup = ?',(int)$idGroup);
        //echo $sql;
        $row = $db->fetchAll($sql);
        
        return $row;
    }
    
    public function getRegisterMngCourse($idGroup)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    
    	$sql = $db->select()
    	->from(array('a' => 'tbl_course_tagging_group'),array('BranchCreator','nCourse'=>'COUNT(*)'))
    	->join(array('def'=>'tbl_branchofficevenue'),'def.IdBranch=a.BranchCreator',array('BranchMng'=>'def.BranchName'))
    	->where('a.IdCourseTaggingGroup in ('.$idGroup.')')
    	->group('a.BranchCreator');
    	//echo $sql;
    	$row = $db->fetchAll($sql);
    
    	return $row;
    }
    
    public function getCourse($idGroup,$branch)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    
    	$sql = $db->select()
    	->from(array('a' => 'tbl_course_tagging_group'),array('BranchCreator','IdCourseTaggingGroup','IdSubject'))
    	->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=a.IdSubject',array('ShortName','BahasaIndonesia'))
    	->where('a.IdCourseTaggingGroup in ('.$idGroup.')')
    	->where('a.BranchCreator =?',$branch);
    	//echo $sql;
    	$row = $db->fetchAll($sql);
    	 
    	return $row;
    }
    
    public function getInfo($idGroup){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('cg'=>'tbl_course_tagging_group'))
					  ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage','faculty_id'=>'IdFaculty'))
					  ->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=cg.IdSemester',array('semester_name'=>'SemesterMainName'))
					  ->joinLeft(array('stm'=>'tbl_staffmaster'),'stm.IdStaff=cg.IdLecturer',array('FrontSalutation','FullName','BackSalutation'))
					  ->where('IdCourseTaggingGroup = ?',$idGroup);					  
		 $row = $db->fetchRow($select);	
		 return $row;
	}
    
    public function getTotalStudentRegistered($idSubject,$idSemester,$idGroup)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql =   $db->select()
                    ->from('tbl_studentregsubjects',array('Total' => ('COUNT(*)')))
                    //->columns()
                    ->where('IdSemesterMain = ?',(int)$idSemester)
                    ->where('IdCourseTaggingGroup = ?',(int)$idGroup)
                    ->where('IdSubject = ?',(int)$idSubject);
                    
        $row =  $db->fetchRow($sql);
        //echo $sql;
        return $row;
    }
	
	public function getDataAlumni($appl_id=0) {
		
		if($appl_id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('sr'=>$this->_name))					
					->where('sr.IdApplication = ?',$appl_id)
					->where('sr.profileStatus = 96'); //Active
					
			$stmt = $db->query($select);						
			$row = $stmt->fetch();		
		}else{
			$row = $this->fetchAll();
			$row=$row->toArray();
		}
		
		if(!$row){
			//throw new Exception("There is No Information Found");
		}
		return $row;
	}
	
	function getSemesterStd($stdid) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sr'=>'tbl_studentregistration'))
		->join(array('i'=>'tbl_intake'),'i.IdIntake=sr.IdIntake')
		->join(array('l'=>'tbl_landscape'),'sr.IdLandscape=l.IdLandscape')
		->where('sr.IdStudentRegistration = ?',$stdid);
		
		$row=$db->fetchRow($select);
		$intake=substr($row['IntakeId'], 0,4);
		for ($i=0;$i<$row['semestermax']/2;$i++) {
			$semester[$i]=$intake;
			$intake++;
		}
		return array('semestercount'=>$row['SemsterCount'],'semestermax'=>$row['semestermax'],'sem'=>$semester);
	}
	
	function isIn($stdid,$semestercode) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sr'=>'tbl_studentsemesterstatus'))
		->join(array('sm'=>'tbl_semestermaster'),'sr.IdSemesterMain=sm.IdSemesterMaster',array())
		->where('sr.IdStudentRegistration=?',$stdid)
		->where('sm.SemesterMainCode=?',$semestercode);
		$row=$db->fetchRow($select);
		if ($row) return true;else return false;
	}
}

?>
