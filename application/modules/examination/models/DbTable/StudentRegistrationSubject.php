<?php 
class Examination_Model_DbTable_StudentRegistrationSubject extends Zend_Db_Table_Abstract
{
	
	/**
	 * The default table name 
	 */
	
	protected $_name = 'tbl_studentregsubjects';
	protected $_primary = "IdStudentRegSubjects";
	
	public function updateData($data,$id){
		$where='IdStudentRegSubjects= '. (int)$id;
		//echo $where;exit;
		 $this->update( $data,$where );
	}
	
	public function addData($data){
		$this->insert($data);
	}
	public function dropGrade($data,$id,$idSubject){
		$where = 'idStudentRegistration='.$id.' and idSubject='.$idSubject." and exam_status in ('C','DR' )";
		$this->update($data, $where);
	}
		
	public function getSemesterStatus($idSemester,$IdStudentRegistration){
		$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
		 	 				  ->from(array('sss'=>'tbl_studentsemesterstatus'))
		 	 				  ->join(array('d'=>'tbl_definationms'),'d.idDefinition=sss.studentsemesterstatus',array('defination'=>'DefinitionDesc'))
		 	 				  ->where('sss.IdSemesterMain = ?',$idSemester)
		 	 				  ->where('sss.IdStudentRegistration = ?',$IdStudentRegistration); 	 				  
		 	return $row = $db->fetchRow($select);	 				 
	}
	
	
	public function getSemesterSubjectStatus($idSemester,$IdStudentRegistration,$idSubject){
		$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
		 	 				  ->from(array('srs'=>'tbl_studentregsubjects'))
		 	 				  ->where('srs.IdSemesterMain = ?',$idSemester)
		 	 				  ->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
		 	 				  ->where('srs.IdSubject = ?',$idSubject); 	 				  
		 	return $row = $db->fetchRow($select);
		 		 				 
	}
	
     /*
	 * This function to get course registered by semester.
	 */
	public function getCourseRegisteredBySemesterOri($registrationId,$idSemesterMain){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$sql = $db->select()
                        ->from(array('sr' => 'tbl_studentregistration'), array('registrationId'))  
                        ->join(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration')
                        ->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('CreditHours','SubCode','SubName'=>'BahasaIndonesia')) 
                        ->where('sr.IdStudentRegistration = ?', $registrationId)
                        ->where('srs.IdSemesterMain = ?',$idSemesterMain)        
                        ->where('srs.subjectlandscapetype != 2')                
                        ->where('srs.Active = 1 OR srs.Active =4 OR srs.Active=5');   //Status => 1:Register 4:Repeat 5:Refer
		//echo $sql;exit;
        $result = $db->fetchAll($sql);
        return $result;
	}
	public function getAllCourseRegisteredBySemester($idProgram,$idSemesterMain){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$sql = $db->select()
		->from(array('sr' => 'tbl_studentregistration'), array('registrationId','IdLandscape'))
		->join(array('ap'=>'applicant_profile'),'ap.appl_id=sr.idapplication',array('StudentName'=>'CONCAT(ap.appl_fname," ",ap.appl_mname," ",ap.appl_lname)'))
		->join(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration')
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('CreditHours','SubCode','SubName'=>'BahasaIndonesia'))
		//->where('sr.IdStudentRegistration = ?', $registrationId)
		->where('srs.IdSemesterMain = ?',$idSemesterMain)
		->where('srs.subjectlandscapetype != 2')
		->where('sr.IdProgram =?',$idProgram)
		->where('srs.Active = 1 OR srs.Active =4 OR srs.Active=5')
		->order('sm.BahasaIndonesia')
		->order('sr.registrationId');   //Status => 1:Register 4:Repeat 5:Refer
	
		$result = $db->fetchAll($sql);
		return $result;
	}
	public function getAllCourseRegistered($idStudentRegistration){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$sql = $db->select()
		->distinct()
		
		->join(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration',array('IdSubject'))
		
		->where('srs.subjectlandscapetype != 2')
		->where('srs.IdStudentRegistration =?',$idStudentRegistration)
		->where('srs.Active = 1 OR srs.Active =4 OR srs.Active=5');
		
		//->order('sr.registrationId');   //Status => 1:Register 4:Repeat 5:Refer
	
		$result = $db->fetchAll($sql);
		return $result;
	}
	public function getAllCourseRegisteredComplete($idStudentRegistration){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$sql = $db->select()
		->distinct()
	
		->join(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration',array('IdSubject'))
		->where('srs.subjectlandscapetype != 2')
		->where('srs.IdStudentRegistration =?',$idStudentRegistration)
		->where('srs.Active = 1 OR srs.Active =4 OR srs.Active=5')
		->where('srs.exam_status="C"');
	
		//->order('sr.registrationId');   //Status => 1:Register 4:Repeat 5:Refer
	
		$result = $db->fetchAll($sql);
		return $result;
	}
	public function getAllCourseBySemester($idProgram,$idSemesterMain){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$sql = $db->select()
		
		->from(array('srs'=>'tbl_studentregsubjects'),array())
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentregistration=srs.IdStudentregistration',array())
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('sm.IdSubject','CreditHours','SubCode','SubjectName'=>'BahasaIndonesia'))
		//->where('sr.IdStudentRegistration = ?', $registrationId)
		->where('srs.IdSemesterMain = ?',$idSemesterMain)
		->where('srs.subjectlandscapetype != 2')
		->where('sr.IdProgram =?',$idProgram)
		->where('srs.Active = 1 OR srs.Active =4 OR srs.Active=5')
		->group('sm.IdSubject')
		->order('sm.BahasaIndonesia');
		  //Status => 1:Register 4:Repeat 5:Refer
	
		$result = $db->fetchAll($sql);
		return $result;
	}
	
	public function getCourseRegisteredBySemester($registrationId,$idSemesterMain,$combineBestPembaikan=true){
		/*
		 * Dapatkan yang terbaik daripada Regular dan Pembaikan + subject tambahan dari pembaikan
		 * 
		 */
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql1=$db->select()
				->from("tbl_semestermaster")
				->where("idsemestermaster = ?",$idSemesterMain);
		
		$sem = $db->fetchRow($sql1);

		if($combineBestPembaikan){
		
    		$sql="
    		SELECT  srs.*,sr.IdProgram,CreditHours,SubCode,BahasaIndonesia as SubName,sm.semesterfunctiontype FROM `tbl_studentregsubjects` AS `srs` 
    		inner join tbl_studentregistration as sr on sr.IdStudentRegistration=srs.IdStudentRegistration
    		INNER JOIN `tbl_semestermaster` AS `sm` ON sm.idsemestermaster=srs.idsemestermain
    		INNER JOIN  tbl_subjectmaster as sbm ON  sbm.IdSubject=srs.IdSubject
    		inner join(
    		select idsubject,max(grade_point) as maxgrade from tbl_studentregsubjects AS `srs` 
    		INNER JOIN `tbl_semestermaster` AS `sm` ON sm.idsemestermaster=srs.idsemestermain 
    		WHERE (srs.IdStudentRegistration = '$registrationId') AND (idacadyear = '".$sem["idacadyear"]."') AND (srs.subjectlandscapetype != 2)		 
    		AND (semestercounttype = '".$sem["SemesterCountType"]."') AND (SemesterFunctionType in ('0','1','6'))
    		group by srs.idsubject
    		
    		) maxtable on maxtable.idsubject = srs.idsubject and maxtable.maxgrade = srs.grade_point
    		WHERE 
    		(srs.IdStudentRegistration = '$registrationId') 
    		
    		AND (idacadyear = '".$sem["idacadyear"]."')
    		AND (semestercounttype = '".$sem["SemesterCountType"]."')
    		GROUP BY srs.idsubject    
    		";
		}else{
    		  $sql="
    		  SELECT  srs.*,sr.IdProgram,CreditHours,SubCode,BahasaIndonesia as SubName,sm.semesterfunctiontype FROM `tbl_studentregsubjects` AS `srs`
			 inner join tbl_studentregistration as sr on sr.IdStudentRegistration=srs.IdStudentRegistration
    		  INNER JOIN `tbl_semestermaster` AS `sm` ON sm.idsemestermaster=srs.idsemestermain
    		  INNER JOIN  tbl_subjectmaster as sbm ON  sbm.IdSubject=srs.IdSubject
    		  WHERE srs.IdStudentRegistration = '$registrationId' AND srs.subjectlandscapetype != 2
        	  AND srs.idSemesterMain = '".$idSemesterMain."'
        	  
        	  GROUP BY srs.idsubject    
        	  ";
		}
		      
        $result = $db->fetchAll($sql);
       //	echo var_dump($result);exit;
        return $result;
	}	
	
	public function getCategoryCourseRegistered($IdStudentRegistration){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	 	 				  ->from(array('srs'=>'tbl_studentregsubjects'),array())	 
	 	 				  ->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('idCategory'=>'category'))  
	 	 				  ->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition=sm.category',array('category'=>'DefinitionDesc','kategori'=>'BahasaIndonesia'))	 	
	 	 				  ->where('srs.exam_status = "C"')
                          ->where("srs.grade_name NOT IN ('MG','DR')")
	 	 				  ->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
	 	 				  ->group('sm.category');
	 	 				  				  
	 	return $row = $db->fetchAll($select);	 	
	}
	
	public function getCourseRegistered($IdStudentRegistration,$idCategory=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	 	 				  ->from(array('srs'=>'tbl_studentregsubjects'),array('grade_name','grade_point'))	 
	 	 				  ->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('sm.IdSubject','CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category','ShortName'))  	 	
	 	 				  ->where('srs.exam_status = "C"')			 
	 	 				  ->where("srs.grade_name NOT IN ('MG','DR')")
                          ->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
	 	 				  ->group('srs.IdSubject');
	 	 				  
	 	if(isset($idCategory) && $idCategory!=''){
	 	 	 $select ->where('sm.category = ?',$idCategory);
	 	}			  		
	 	 		  
	 	return $row = $db->fetchAll($select);	 	
	}
	
	public function getCourseTranscript($IdStudentRegistration){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db->select()
		->from(array('srs'=>'tbl_studentregsubjects'),array('grade_name','grade_point'))
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('sm.IdSubject','CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category','ShortName'))
		->where('srs.exam_status = "C"')
		->where("srs.grade_name NOT IN ('MG','DR')")
		->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
		->group('srs.IdSubject');
	
		if(isset($idCategory) && $idCategory!=''){
			$select ->where('sm.category = ?',$idCategory);
		}
	
		return $row = $db->fetchAll($select);
	}
	
	public function getHighestMarkOld($IdStudentRegistration,$IdSubject,$semester){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
    	$select_max = $db->select()
	 	 				  ->from(array('srs'=>'tbl_studentregsubjects'),array('max(grade_point)'))
	 	 				  ->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=srs.IdSemesterMain',array())	 
	 	 				  ->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
	 	 				  ->where('srs.IdSubject = ?',$IdSubject)
	 	 				  ->where('sm.SemesterMainStartDate <= ?',$semester["SemesterMainStartDate"]);
	 	 				  
	 	 $select = $db->select()
	 	 				  ->from(array('srs'=>'tbl_studentregsubjects'))	
	 	 				  ->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category')) 
	 	 				  ->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
	 	 				  ->where('srs.IdSubject = ?',$IdSubject)
	 	 				  ->where('srs.grade_point = (?)',$select_max);
	 				  
	 	return $row = $db->fetchRow($select);	 	
	}
	
	public function getMaxSemStartDate($IdStudentRegistration,$semester){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		//get max sem date from selected sem semester count type and academic year
		$select_max_date = $db->select()->from(array('sm'=>'tbl_semestermaster'))
		->where('sm.idacadyear = ?',$semester["idacadyear"] )
		->where('sm.SemesterCountType = ?',$semester["SemesterCountType"] )
		->order('sm.SemesterMainStartDate DESC');
		//->limit(0,1);
	
		$row_max_date = $db->fetchRow($select_max_date);
		//echo var_dump($row_max_date);exit;
		
		return $row_max_date;
	}
	
	public function getHighestMark($IdStudentRegistration,$IdSubject,$semester){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		//get max sem date from selected sem semester count type and academic year
		$select_max_date = $db->select()->from(array('sm'=>'tbl_semestermaster'))
		->where('sm.idacadyear = ?',$semester["idacadyear"] )
		//->where('sm.IdSemesterMaster = ?',$semester["IdSemesterMain"] )
		->where('sm.SemesterCountType = ?',$semester["SemesterCountType"] )
		->order('sm.SemesterMainStartDate DESC');
		//->limit(0,1);
		
		$row_max_date = $db->fetchRow($select_max_date);
		// echo var_dump($row_max_date);exit;		  
		$select = $db->select()
	 	 				  ->from(array('srs'=>'tbl_studentregsubjects'))	
	 	 				  ->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject=srs.IdSubject',array('CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category')) 
 						  ->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=srs.IdSemesterMain',array())
	 	 				  ->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
	 	 				  ->where('srs.IdSubject = ?',$IdSubject)
	 	 				  ->where('sm.SemesterMainStartDate <= ?',$row_max_date["SemesterMainStartDate"])
	 	 				  ->where(" (srs.exam_status ='C')")
	 	 				  ->order('srs.grade_point desc') 
	 	 				  ->order('srs.final_course_mark desc');
	 	 				 // ->limit(0,1);
		
		$row = $db->fetchRow($select);
	 	
	 	return $row; 	 	
	}
	
	public function getHighestMarkByStdRegSubjects($idStudentRegSubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		//get semester and subject
		$select = $db->select()
		->from(array('srs'=>'tbl_studentregsubjects'))
		->where('srs.IdStudentRegSubjects=?',$idStudentRegSubject);
		$row=$db->fetchRow($select);
		if ($row) {
			$IdStudentRegistration=$row['IdStudentRegistration'];
			$idsemester=$row["IdSemesterMain"];
			$IdSubject=$row['IdSubject'];
			//get semester detail
			$select = $db->select()
			->from(array('sm'=>'tbl_semestermaster'))
			->where('sm.IdSemesterMaster=?',$idsemester);
			$semester=$db->fetchRow($select);
			
			/* //get max sem date from selected sem semester count type and academic year
			$select_max_date = $db->select()->from(array('sm'=>'tbl_semestermaster'))
			->where('sm.idacadyear = ?',$semester["idacadyear"] )
			//->where('sm.IdSemesterMaster = ?',$semester["IdSemesterMain"] )
			->where('sm.SemesterCountType = ?',$semester["SemesterCountType"] )
			->order('sm.SemesterMainStartDate DESC');
			//->limit(0,1); */
		
			//$row_max_date = $db->fetchRow($select_max_date);
			// echo var_dump($row_max_date);exit;
			$select = $db->select()
			->from(array('srs'=>'tbl_studentregsubjects'))
			->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject=srs.IdSubject',array('CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category'))
			->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=srs.IdSemesterMain',array())
			->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
			->where('srs.IdSubject = ?',$IdSubject)
			->where('sm.idacadyear=?',$semester['idacadyear'])
			->where('sm.SemesterCountType=?',$semester['SemesterCountType'])
			//->where('sm.SemesterMainStartDate <= ?',$row_max_date["SemesterMainStartDate"])
			->where(" (srs.exam_status ='C')")
			->order('srs.grade_point desc')
			->order('srs.final_course_mark desc');
			// ->limit(0,1);
		
			$row = $db->fetchRow($select);
		} else $row=array();
		return $row;
	}
	public function getHighestGradeByStdRegSubjects($idStudentRegSubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		//get semester and subject
		$select = $db->select()
		->from(array('srs'=>'tbl_studentregsubjects'))
		->where('srs.IdStudentRegSubjects=?',$idStudentRegSubject);
		$row=$db->fetchRow($select);
		if ($row) {
			$IdStudentRegistration=$row['IdStudentRegistration'];
			$idsemester=$row["IdSemesterMain"];
			$IdSubject=$row['IdSubject'];
			//get semester detail
			$select = $db->select()
			->from(array('sm'=>'tbl_semestermaster'))
			->where('sm.IdSemesterMaster=?',$idsemester);
			$semester=$db->fetchRow($select);
			
			//get max sem date from selected sem semester count type and academic year
			$select = $db->select()
			->from(array('srs'=>'tbl_studentregsubjects'))
			->join(array('sm'=>'tbl_semestermaster'),'srs.IdSemesterMain=sm.IdSemesterMaster')
			->where('sm.idacadyear = ?',$semester["idacadyear"] )
			->where('srs.IdSubject = ?',$IdSubject )
			->where('srs.IdStudentRegistration=?',$IdStudentRegistration)
			->where('sm.SemesterCountType = ?',$semester["SemesterCountType"] )
			->order('srs.grade_point desc');
			//->limit(0,1);
	 
	
			$row = $db->fetchRow($select);
		} else $row=array();
		return $row;
	}
	public function getHighestMarkPerSemester($IdStudentRegistration,$IdSubject,$row_max_date){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from(array('srs'=>'tbl_studentregsubjects'))
		//->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject=srs.IdSubject',array('CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category'))
		->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=srs.IdSemesterMain',array())
		->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
		->where('srs.IdSubject = ?',$IdSubject)
		->where('sm.SemesterMainStartDate <= ?',$row_max_date["SemesterMainStartDate"])
		->where(" (srs.exam_status != '') OR (srs.grade_status != '')")
		->order('srs.grade_point desc') 
		->order('srs.final_course_mark desc');
		//->limit(0,1);
	
		$row = $db->fetchRow($select);
		 
		return $row;
	}
	
	public function getData($IdStudentRegSubjects){
		$db = Zend_Db_Table::getDefaultAdapter();
	    $select = $db->select()
	 	 				  ->from(array('srs'=>'tbl_studentregsubjects'))	
	 	 				  ->join(array('sr' => 'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration')  
	 	 				  ->where('srs.IdStudentRegSubjects= ?',$IdStudentRegSubjects);	 	 							  
	 	return $row = $db->fetchRow($select);	 				 
	}
	
	public function getSubjects($IdStudentRegSubjects){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('srs'=>'tbl_studentregsubjects'))
		->join(array('sr' => 'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration',array('IdProgram'))
		->where('srs.IdStudentRegSubjects= ?',$IdStudentRegSubjects);
		return $row = $db->fetchRow($select);
	}
	
	public function getGroupIdBySubjects($semesterid,$programid,$subjectid){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->distinct()
		->from(array('srs'=>'tbl_studentregsubjects'),array('IdCourseTaggingGroup'))
		//->join(array('st'=>'tbl_studentregistration'),'srs.idStudentregistration=st.idstudentregistration',array())
		->where('srs.IdSubject= ?',$subjectid)
		//->where('st.IdProgram= ?',$programid)
		->where('srs.IdSemesterMain= ?',$semesterid);
		return $row = $db->fetchAll($select);
	}
	
	public function getStudentByGroup($groupid){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->distinct()
		->from(array('srs'=>'tbl_studentregsubjects'),array('IdStudentRegistration','IdStudentRegSubjects'))
		//->join(array('st'=>'tbl_studentregistration'),'srs.idStudentregistration=st.idstudentregistration',array())
		->where('srs.IdCourseTaggingGroup= ?',$groupid); 
		return $row = $db->fetchAll($select);
	}
	public function getHighestMarkofAllSemester($IdStudentRegistration,$IdSubject){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 				  
	    $select = $db->select()
	 	 				  ->from(array('srs'=>'tbl_studentregsubjects'))	
	 	 				  ->joinRight(array('s'=>'tbl_subjectmaster'),'s.IdSubject=srs.IdSubject',array('CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category','ShortName')) 
	 	 				  ->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=srs.IdSemesterMain',array('SemesterMainName'))
 				          ->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
	 	 				  ->where('srs.IdSubject = ?',$IdSubject)
	 	 				  ->where('srs.exam_status="C"')
	 	 				  ->where('srs.grade_status="Pass"')
	 	 				  ->order('srs.Grade_Point desc')
	    				  ->order('srs.final_course_mark desc');
	 				  
	 	return $row = $db->fetchRow($select);	 
	}
	public function getHighestMarkofAllSemesterNoStatus($IdStudentRegistration,$IdSubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db->select()
		->from(array('srs'=>'tbl_studentregsubjects'))
		->joinRight(array('s'=>'tbl_subjectmaster'),'s.IdSubject=srs.IdSubject',array('CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category','ShortName'))
		->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=srs.IdSemesterMain',array('SemesterMainName'))
		->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
		->where('srs.IdSubject = ?',$IdSubject)
		->where('srs.exam_status="C"')
		//->where('srs.grade_status="Pass"')
		->order('srs.Grade_Point desc')
		->order('srs.final_course_mark desc');
	
		return $row = $db->fetchRow($select);
	}
	public function getHighestMarkofAllSemesterWithDrop($IdStudentRegistration,$IdSubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db->select()
		->from(array('srs'=>'tbl_studentregsubjects'))
		->joinRight(array('s'=>'tbl_subjectmaster'),'s.IdSubject=srs.IdSubject',array('CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category','ShortName'))
		->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
		->where('srs.IdSubject = ?',$IdSubject)
		->where('srs.exam_status in ("DR","C")')
		//->where('srs.grade_status="Pass"')
		->order('srs.Grade_Point desc') 
		->order('srs.final_course_mark desc');
	
		return $row = $db->fetchRow($select);
	}
	public function getHighestMarkofAllSemesterProfile($IdStudentRegistration,$IdSubject,$idProfile){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db->select()
		->from(array('srs'=>'tbl_studentregsubjects'))
		->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject=srs.IdSubject',array('CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','ShortName'))
		->join(array('p'=>'transcript_profile_detail'),'p.idSubject=srs.IdSubject',array('category'=>'p.idGroup'))
		->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
		->where('srs.IdSubject = ?',$IdSubject)
        //->where("srs.grade_name NOT IN ('MG','DR')")
        ->where('srs.grade_status="Pass"')
		->where('srs.exam_status="C"')
		->order('srs.Grade_Point desc') 
		->order('srs.final_course_mark desc');
	
		return $row = $db->fetchRow($select);
	}
	public function getHighestMarkofAllSemesterProfileWithDrop($IdStudentRegistration,$IdSubject,$idProfile){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db->select()
		->from(array('srs'=>'tbl_studentregsubjects'))
		->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject=srs.IdSubject',array('CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','ShortName'))
		->join(array('p'=>'transcript_profile_detail'),'p.idSubject=srs.IdSubject',array('category'=>'p.idGroup'))
		->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
		->where('srs.IdSubject = ?',$IdSubject)
		//->where("srs.grade_name NOT IN ('MG','DR')")
		->where('srs.grade_status="Pass"')
		->where('srs.exam_status in ("DR","C")')
		->order('srs.Grade_Point desc')
		->order('srs.final_course_mark desc');
		$row = $db->fetchRow($select);
		 
		return $row;
	}
	
	
	public function getRegDataParent($IdStudentRegistration,$parentId){
		$db = Zend_Db_Table::getDefaultAdapter();
	    $select = $db->select()
	 	 				  ->from(array('srs'=>'tbl_studentregsubjects'),array('IdStudentRegSubjects'))	
	 	 				  ->join(array('lbs' => 'tbl_landscapeblocksubject'),'lbs.subjectid = srs.IdSubject')  
	 	 				  ->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject=srs.IdSubject',array('CreditHours'))
	 	 				  ->where('srs.subjectlandscapetype= 2')
	 	 				  ->where('srs.IdStudentRegistration= ?',$IdStudentRegistration)
	 	 				  ->where('lbs.IdLandscapeblocksubject= ?',$parentId);	 	 							  
	 	return $row = $db->fetchRow($select);	 				 
	}
	
	
	//utk update run
	public function getSubjectParent(){
		$db = Zend_Db_Table::getDefaultAdapter();
	    $select = $db->select()
	 	 				  ->from(array('srs'=>'tbl_studentregsubjects'))	
	 	 				  ->join(array('sr' => 'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration',array('IdLandscape'))  	 				    
	 	 				  ->where('srs.subjectlandscapetype= 2')
	 	 				  ->where('srs.final_course_mark > 0')	
	 	 				  ->group('srs.IdStudentRegistration')
	 	 				  ->group('srs.IdSubject') ;	
	 	 				  //->limit(50,0);				
	 	 				  
	 	 				  						  
	 	return $row = $db->fetchAll($select);	 				 
	}
	
	public function updateChildMark($data,$IdStudentRegistration, $idSubject, $IdSemesterMain){
		 $db = Zend_Db_Table::getDefaultAdapter();
		  echo '<br>';
		 echo 'IdStudentRegistration = '.$IdStudentRegistration.' AND IdSubject = '.$idSubject.' AND IdSemesterMain = '.$IdSemesterMain;
		 echo '<br>';
		 //$db->update($data,'IdStudentRegistration = '.$IdStudentRegistration.' AND IdSubject = '.$idSubject.'IdBlock = '.$idBlock);
	}
	
	
	//run update child mark
	public function getStudentRegSubjectInfo($idSemester,$IdStudentRegistration,$idSubject){
		$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
		 	 				  ->from(array('srs'=>'tbl_studentregsubjects'))
		 	 				  ->where('srs.IdSemesterMain = ?',$idSemester)
		 	 				  ->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)		 	 				  
		 	 				  ->where('srs.IdSubject = ?',$idSubject); 	 				  
		 	return $row = $db->fetchRow($select);	 				 
	}
	
	
	
	//run child 
	public function getSemesterSubjectStatusDua($IdStudentRegistration,$idSubject){
		$db = Zend_Db_Table::getDefaultAdapter();
			 $select = $db->select()
		 	 				  ->from(array('srs'=>'tbl_studentregsubjects'))		 	 				
		 	 				  ->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
		 	 				  ->where('srs.IdSubject = ?',$idSubject)
		 	 				  ->where('srs.grade_point IS NOT NULL')
		 	 				  ->where('srs.final_course_mark IS NULL OR srs.final_course_mark=0'); 	 				  
		 	return $row = $db->fetchRow($select);	 				 
	}
	public function getStudentRegSubjects($IdStudentRegistration,$idSubject){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('srs'=>'tbl_studentregsubjects'))
		->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
		->where('srs.IdSubject = ?',$idSubject)
		->where('srs.grade_point IS NOT NULL');
		//->where('srs.final_course_mark IS NULL OR srs.final_course_mark=0');
		return $row = $db->fetchRow($select);
	}
	public function getStudentDataLandscape($IdStudentRegSubjects){
		
		$db = Zend_Db_Table::getDefaultAdapter();
	    $select = $db->select()
	 	 				  ->from(array('srs'=>'tbl_studentregsubjects'))	
	 	 				  ->join(array('sr' => 'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration')  
	 	 				  ->join(array('l' => 'tbl_landscape'),'l.IdLandscape=sr.IdLandscape',array('LandscapeType'))  
	 	 				  ->where('srs.IdStudentRegSubjects= ?',$IdStudentRegSubjects);	 	 							  
	 	return $row = $db->fetchRow($select);	 				 
	}
	
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
	
	
	public function getStudentRegParentSubject($IdLandscapeblocksubject,$IdStudentRegistration,$IdSemesterMain){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	 	 				  ->from(array('lbs'=>'tbl_landscapeblocksubject'))	
	 	 				  ->join(array('srs' => 'tbl_studentregsubjects'),'srs.IdSubject = lbs.subjectid AND srs.IdBlock=lbs.blockid')	 	 				  
	 	 				  ->where('lbs.IdLandscapeblocksubject = ?',$IdLandscapeblocksubject)
	 	 				  ->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
	 	 				  ->where('srs.IdSemesterMain = ?',$IdSemesterMain);	 	 							  
	 	return $row = $db->fetchRow($select);	 	
	 	
		/*
		 * SELECT * 
FROM `tbl_landscapeblocksubject` as lbs
JOIN tbl_studentregsubjects as srs ON srs.IdSubject = lbs.`subjectid` AND  srs.IdBlock=lbs.`blockid`
where `IdLandscapeblocksubject`=1
AND IdStudentRegistration ='2710'
AND srs.IdSemesterMain='1'
		 */
	}
	
	
	function getDifferentType(){
				
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$sql = "SELECT srs.`IdStudentRegSubjects`,srs.`IdStudentRegistration`,srs.`IdSubject`,srs.IdSemesterMain,srs.`subjectlandscapetype`,lbs.type
				FROM `tbl_studentregsubjects` as srs
				JOIN tbl_landscapeblocksubject as lbs ON lbs.subjectid=srs.`IdSubject`
				WHERE srs.`subjectlandscapetype` != lbs.type
				and lbs.IdLandscape=10 limit 0,3";
		return $row = $db->fetchAll($sql);	
	}
		
	function updateDataType($data,$IdStudentRegSubjects){		 
		 $this->update($data,"IdStudentRegSubjects = '".$IdStudentRegSubjects."'");
	}
	public function getSurveyStatus($idstudentregistration,$idsemester){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('srs'=>'tbl_studentregsubjects'))
		->where('srs.survey_status="0"')
		->where('srs.IdSemesterMain=?',$idsemester)
		->where('srs.IdStudentRegistration= ?',$idstudentregistration);
		
		return $row = $db->fetchRow($select);
	}
	
	public function isCompleted($IdStudentRegistration,$idSubject,$grade=null,$type=null,$total_credit=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		if(($grade==null)){
			$sql = $db->select()
			->from(array('srs' => 'tbl_studentregsubjects'))
			->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
			->where('srs.IdSubject = ?',$idSubject)
			->where('srs.exam_status = ?','C');
			//->where('srs.grade_status = ?','Pass');
			$result = $db->fetchRow($sql);
			if(!empty($result)){
				return true;
			}else{
				return false;
			}
	
		}else{
				
			if($type==2){ //Total Credit Hour
	
				if($total_credit >= $grade){
					return true;
				}else{
					return false;
				}
	
			}else{ //Pass With Grade
	
				//ni sepatutnya check kat setup tapi urgent hardcode sini dulu
				$gpoint["C"]=2.00;
				$gpoint["C+"]=2.50;
				$gpoint["B-"]=2.75;
				$gpoint["B"]=3.00;
				$gpoint["B+"]=3.50;
				$gpoint["A"]=4.00;
				$gpoint["A-"]=3.75;
				$gpoint["D"]=1.00;
				$gpoint["E"]=0.00;
				if(isset($gpoint[$grade])){
					$sql = $db->select()
					->from(array('srs' => 'tbl_studentregsubjects'))
					->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
					->where('srs.IdSubject = ?',$idSubject)
					->where('srs.exam_status = ?','C')
					->where('srs.grade_point >= ?',$gpoint[$grade]);
					$result = $db->fetchRow($sql);
	
					if(!empty($result)){
						return true;
					}else{
						return false;
					}
				}else{
					return false;
				}
			}
		}
	}
	public function isCoRequisite($IdStudentRegistration,$idSubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		if(($grade==null)){
			$sql = $db->select()
			->from(array('srs' => 'tbl_studentregsubjects'))
			->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
			->where('srs.IdSubject = ?',$idSubject);
				
			$result = $db->fetchRow($sql);
			if(!empty($result)){
				return true;
			}else{
				return false;
			}
	
		}
	}
	
	public function getTranscriptList($idStudentRegistration,$idProfile=null) {
		//get student profile
		$regSubjectDB = new Examination_Model_DbTable_StudentRegistrationSubject();
		$DbProfileDetail = new App_Model_General_DbTable_TranscriptProfileDetail();
		$studentRegDB = new Examination_Model_DbTable_StudentRegistration();
		$student = $studentRegDB->getStudentInfo($idStudentRegistration);
		//echo var_dump($student);exit;
		 
		if ($idProfile==null) {
				
			$idLandscape = $student['IdLandscape'];
			$idProgram = $student['IdProgram'];
			$idMajor = $student['IdProgramMajoring'];
			//echo var_dump($student);
			//exit;
			//transcript profile
			$DbProfile = new GeneralSetup_Model_DbTable_TranscriptProfile();
			$DbProfileDetail = new GeneralSetup_Model_DbTable_TranscriptProfileDetail();
			$idProfile = $DbProfile->getStdTranscriptProfile($idProgram, $idMajor, $idLandscape);
			//echo var_dump($idProfile);exit;
			if ($idProfile==array()) $idProfile='*'; else $idProfile=$idProfile[0]['IdProfile'];
		}
		//get category and course list
		//echo var_dump($idProfile);exit;
		//}
		if ($idProfile=='*') {
	
			$dbLands = new GeneralSetup_Model_DbTable_Landscapesubject();
			$dbBlock= new GeneralSetup_Model_DbTable_LandscapeBlockSubject();
			$dbProgReq = new GeneralSetup_Model_DbTable_Programrequirement();
			$subject_category = $dbProgReq->getlandscapecoursetype($student['IdProgram'], $student['IdLandscape']);
	
			foreach($subject_category as $index=>$category){
				$subject_list = $dbLands->getlandscapesubjectsPerCategory($student['IdLandscape'],$category["SubjectType"]);
				if ($subject_list==array()) $subject_list = $dbBlock->getLandscapeCoursePerCategory($student['IdLandscape'],$category["SubjectType"]);
				unset($subjecthighest);
				foreach ($subject_list as $key=>$subject) {
					$subject=$regSubjectDB->getHighestMarkofAllSemester($idStudentRegistration, $subject['IdSubject']);
					if (!is_bool($subject)) $subjecthighest[$key] = $subject;
				}
				if (isset($subjecthighest)) $subject_category[$index]["subjects"] = $subjecthighest;
				else unset($subject_category[$index]);
				//echo var_dump($subject_category);
				//exit;
			}
	
		}
		else
		{
	
			$subject_category = $DbProfileDetail->fnGetTranscriptProfileName($idProfile);
			//echo var_dump($subject_category);echo '--'.$idProfile;exit;
			foreach($subject_category as $index=>$category){
				$subjecthighest=array();
				$subject_list = $DbProfileDetail->fnGetTranscriptProfileSubject($idProfile,$category['idGroup']);
				//echo var_dump($subject_list);exit;
				unset($subjecthighest);
				foreach($subject_list as $key=>$subject) :
				$subject=$regSubjectDB->getHighestMarkofAllSemesterProfile($idStudentRegistration, $subject['idSubject'],$idProfile);
				if (!is_bool($subject)) $subjecthighest[$key] = $subject;
				endforeach;
				if (isset($subjecthighest)) $subject_category[$index]["subjects"] = $subjecthighest;
				else unset($subject_category[$index]);
					
			}
		}
		//echo var_dump($subject_category);
		//exit;
		return $subject_category;
	}
	public function isIn($IdStudentRegistration,$idSubject,$idSemester){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		 
			$sql = $db->select()
			->from(array('srs' => 'tbl_studentregsubjects'))
			->where('srs.IdStudentRegistration = ?', $IdStudentRegistration)
			->where('srs.IdSubject = ?',$idSubject)
			->where('srs.IdSemesterMain = ?',$idSemester);
			//->where('srs.grade_status = ?','Pass');
			//echo $sql;exit;
			$result = $db->fetchRow($sql);
			return $result;
	}
		
}
	

?>