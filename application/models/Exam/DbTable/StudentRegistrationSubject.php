<?php 
class App_Model_Exam_DbTable_StudentRegistrationSubject extends Zend_Db_Table_Abstract
{
	
	/**
	 * The default table name 
	 */
	
	protected $_name = 'tbl_studentregsubjects';
	protected $_primary = "IdStudentRegSubjects";
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
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
						            
        $result = $db->fetchAll($sql);
        return $result;
	}
	
	public function isRegistered($registrationId,$idSemesterMain,$idsubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$sql = $db->select()
		->from(array('sr' => 'tbl_studentregistration'), array('registrationId'))
		->join(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration')
		 ->where('sr.IdStudentRegistration = ?', $registrationId)
		->where('srs.IdSemesterMain = ?',$idSemesterMain)
		->where('srs.IdSubject=?',$idsubject) ; 
	
		$result = $db->fetchRow($sql);
		if ($result) return true;else return false;
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
    		SELECT  srs.*,CreditHours,SubCode,BahasaIndonesia as SubName,sm.semesterfunctiontype FROM `tbl_studentregsubjects` AS `srs` 
    		INNER JOIN `tbl_semestermaster` AS `sm` ON sm.idsemestermaster=srs.idsemestermain
    		INNER JOIN  tbl_subjectmaster as sbm ON  sbm.IdSubject=srs.IdSubject
    		inner join(
    		select idsubject,max(grade_point) as maxgrade from tbl_studentregsubjects AS `srs` 
    		INNER JOIN `tbl_semestermaster` AS `sm` ON sm.idsemestermaster=srs.idsemestermain 
    		WHERE (srs.IdStudentRegistration = '$registrationId') AND (idacadyear = '".$sem["idacadyear"]."') AND (srs.subjectlandscapetype != 2)		 
    		AND (semestercounttype = '".$sem["SemesterCountType"]."') 
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
    		  SELECT  srs.*,CreditHours,SubCode,BahasaIndonesia as SubName,sm.semesterfunctiontype FROM `tbl_studentregsubjects` AS `srs`
    		  INNER JOIN `tbl_semestermaster` AS `sm` ON sm.idsemestermaster=srs.idsemestermain
    		  INNER JOIN  tbl_subjectmaster as sbm ON  sbm.IdSubject=srs.IdSubject
    		  WHERE srs.IdStudentRegistration = '$registrationId' AND srs.subjectlandscapetype != 2
        	  AND srs.idSemesterMain = '".$idSemesterMain."'
        	  GROUP BY srs.idsubject    
        	  ";
		}
		//AND (semesterfunctiontype = 1 OR semesterfunctiontype = 0) line 86 and 79
		//
		/*$sql ="
		SELECT srs.*,CreditHours,SubCode,BahasaIndonesia as SubName,sm.semesterfunctiontype FROM `tbl_studentregsubjects` AS `srs` 
		INNER JOIN `tbl_semestermaster` AS `sm` ON sm.idsemestermaster=srs.idsemestermain
		INNER JOIN  tbl_subjectmaster as sbm ON  sbm.IdSubject=srs.IdSubject
		inner join(
		select idsubject,max(grade_point) as maxgrade from (SELECT idsubject, grade_point

          FROM tbl_studentregsubjects AS srs
          INNER JOIN tbl_semestermaster AS sm ON sm.idsemestermaster = srs.idsemestermain
          WHERE (
          srs.IdStudentRegistration ='$registrationId'
          )
          AND (
          idacadyear ='".$sem["idacadyear"]."'
          )
          AND (
          semestercounttype ='".$sem["SemesterCountType"]."'
          )
          AND (
          semesterfunctiontype =1
          )
          AND idsubject
          IN (
          
          SELECT idsubject
          
          FROM tbl_studentregsubjects AS srs
          INNER JOIN tbl_semestermaster AS sm ON sm.idsemestermaster = srs.idsemestermain
          WHERE (
          srs.IdStudentRegistration ='$registrationId'
          )
          AND (
          idacadyear ='".$sem["idacadyear"]."'
          )
          AND (
          semestercounttype ='".$sem["SemesterCountType"]."'
          )
          AND (
          semesterfunctiontype =0
          )
          )
          UNION 
          SELECT idsubject, grade_point
          
          FROM tbl_studentregsubjects AS srs 
          INNER JOIN tbl_semestermaster AS sm ON sm.idsemestermaster = srs.idsemestermain
          WHERE (
          srs.IdStudentRegistration ='$registrationId'
          )
          AND (
          idacadyear ='".$sem["idacadyear"]."'
          )
          AND (
          semestercounttype ='".$sem["SemesterCountType"]."'
          )
          AND (
          semesterfunctiontype =0
          )
          ) as a group by idsubject
		) maxtable on maxtable.idsubject = srs.idsubject and maxtable.maxgrade = srs.grade_point
		WHERE 
		(srs.IdStudentRegistration = '$registrationId') AND (idacadyear = '".$sem["idacadyear"]."') 
		AND (semestercounttype = '".$sem["SemesterCountType"]."') AND 
		(semesterfunctiontype = 1 OR semesterfunctiontype = 0)   
		group by srs.IdSubject    
		";*/
		//echo $sql;
		//exit;         
        $result = $db->fetchAll($sql);
        return $result;
	}	
	public function getCategoryCourseRegistered($IdStudentRegistration){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	 	 				  ->from(array('srs'=>'tbl_studentregsubjects'),array())	 
	 	 				  ->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('idCategory'=>'category'))  
	 	 				  ->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition=sm.category',array('category'=>'DefinitionDesc','kategori'=>'BahasaIndonesia'))	 	
	 	 				  ->where('srs.exam_status = "C"')			 
	 	 				  ->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
	 	 				  ->group('sm.category');
	 	 				  				  
	 	return $row = $db->fetchAll($select);	 	
	}
	
	public function getCourseRegistered($IdStudentRegistration,$idCategory=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	 	 				  ->from(array('srs'=>'tbl_studentregsubjects'),array('grade_name','grade_point'))	 
	 	 				  ->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category'))  	 	
	 	 				  ->where('srs.exam_status = "C"')			 
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
	
	public function getHighestMark($IdStudentRegistration,$IdSubject,$semester){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		//get max sem date from selected sem semester count type and academic year
		$select_max_date = $db->select()->from(array('sm'=>'tbl_semestermaster'))
		->where('sm.idacadyear = ?',$semester["idacadyear"] )
		->where('sm.SemesterCountType = ?',$semester["SemesterCountType"] )
		->order('sm.SemesterMainStartDate DESC')
		->limit(0,1);
		
		$row_max_date = $db->fetchRow($select_max_date);
		 				  
		$select = $db->select()
	 	 				  ->from(array('srs'=>'tbl_studentregsubjects'))	
	 	 				  ->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject=srs.IdSubject',array('CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category')) 
 						  ->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=srs.IdSemesterMain',array())
	 	 				  ->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
	 	 				  ->where('srs.IdSubject = ?',$IdSubject)
	 	 				  ->where('sm.SemesterMainStartDate <= ?',$row_max_date["SemesterMainStartDate"])
	 	 				  ->where(" (srs.exam_status != '') OR (srs.grade_status != '')")
	 	 				  ->order('srs.grade_point desc');
		
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
	
		
	public function getHighestMarkofAllSemester($IdStudentRegistration,$IdSubject){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 				  
	    $select = $db->select()
	 	 				  ->from(array('srs'=>'tbl_studentregsubjects'))	
	 	 				  ->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject=srs.IdSubject',array('CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category')) 
 				          ->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
	 	 				  ->where('srs.IdSubject = ?',$IdSubject)	 	 				
	 	 				  ->order('srs.final_course_mark desc');
	 				  
	 	return $row = $db->fetchRow($select);	 
	}
	
	public function getHighestMarkofAllSemesterPassed($IdStudentRegistration,$IdSubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db->select()
		->from(array('srs'=>'tbl_studentregsubjects'),array('srs.*','Grade'=>'grade_name'))
		->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject=srs.IdSubject',array('sks'=>'CreditHours','CreditHours','SubjectCode'=>'ShortName','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category','ShortName'))
		->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
		->where('srs.IdSubject = ?',$IdSubject)
		->where('srs.exam_status = "C"')
		->where('srs.grade_status = "Pass"')
		->order('srs.grade_point desc')
		->order('srs.final_course_mark desc');
	//echo $select;exit;
		return $row = $db->fetchRow($select);
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
    
    public function getHighestMarkofAllSemesterC($IdStudentRegistration,$IdSubject){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 				  
	    $select = $db->select()
	 	 				  ->from(array('srs'=>'tbl_studentregsubjects'))	
	 	 				  ->joinRight(array('s'=>'tbl_subjectmaster'),'s.IdSubject=srs.IdSubject',array('CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category','ShortName')) 
 				          ->where('srs.IdStudentRegistration = ?',$IdStudentRegistration)
	 	 				  ->where('srs.IdSubject = ?',$IdSubject)
	 	 				  ->where('srs.exam_status="C"')	 	 				
	 	 				  ->order('srs.Grade_Point desc');
	 				  
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
        ->where("srs.grade_name NOT IN ('MG','DR')")
		->where('srs.exam_status="C"')
		->order('srs.Grade_Point desc');
	
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
	
}
	

?>