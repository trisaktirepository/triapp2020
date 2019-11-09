<?php 
class Examination_Model_DbTable_Gpacalculation extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_gpacalculation';
    private $lobjDbAdpt;
    
	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function fnSavegpa($formData) { //Function for adding the Program Branch details to the table	
	
		unset ( $formData ['Idgpacalculation']);		
		unset ( $formData ['Save']);
	    return $this->insert($formData);
	}
	
	public function fnUpdategpa($larrformData,$lintIdapplication){
				
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();	
			$lstrTable = "tbl_gpacalculation";
			$lstrWhere = "Idgpacalculation = ".$larrformData['Idgpacalculation'];
			$idgpacal=$larrformData['Idgpacalculation'];
			
			unset ( $larrformData ['IdApplication']);
			unset ( $larrformData ['Idgpacalculation']);		
			unset ( $larrformData ['Save']);	
					
			$lstrMsg = $lobjDbAdpt->update($lstrTable,$larrformData,$lstrWhere);
			
			$lstrselect = $lobjDbAdpt->select()
							->from(array("gc"=>"tbl_gpacalculation"),array("gc.IdStudentRegistration","gc.Gpa"))
							->where("gc.Idgpacalculation = ?",$idgpacal)
							->order("gc.IdStudentRegistration");
			$larrresult = $lobjDbAdpt->fetchRow($lstrselect);
			
			$lstrselect1 = $lobjDbAdpt->select()
							->from(array("gc"=>"tbl_gpacalculation"),array("max(gc.IdStudentRegistration) as maxidreg"))
							->where("gc.IdApplication = ?",$lintIdapplication)
							->group("gc.IdApplication");
			$larrresult1 = $lobjDbAdpt->fetchRow($lstrselect1);
		
			
			for($i=$larrresult['IdStudentRegistration'];$i<$larrresult1['maxidreg'];$i++){				
				echo $i;
				$lstrselect2 = $lobjDbAdpt->select()
							->from(array("gc"=>"tbl_gpacalculation"),array("AVG(gc.Gpa) as newcgpa"))
							->where("gc.IdStudentRegistration <= ?",$i)
							->group("gc.IdApplication");
				$larrresult2 = $lobjDbAdpt->fetchRow($lstrselect2);						
				$this->UpdateCgpa($larrresult2['newcgpa'],$i,$lintIdapplication);
			}
			
			
			return $lstrMsg;
	}
	
	public function UpdateCgpa($newcgpa,$idregistration,$lintIdapplication){
		
		
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();			
		$lstrTable = "tbl_cgpacalculation";
		$lstrWhere = "IdRegistration = ".$idregistration." AND IdApplication=k".$lintIdapplication;
		$larrformData['Cgpa']=$newcgpa;
		$larrformData['UpdDate']=date ( 'Y-m-d H:i:s' );
		$lstrMsg = $lobjDbAdpt->update($lstrTable,$larrformData,$lstrWhere);
	}
	
	public function fnSearchStudentsubjects($post = array()) { //Function to get the user details
	   $auth = Zend_Auth::getInstance();
	   $lintidstaff =$auth->getIdentity()->IdStaff;
       $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
       				
		$lstrSelect = $lobjDbAdpt->select()
									 ->from(array("tbl_studentapplication"=>"tbl_studentapplication"),array("tbl_studentapplication.*"))
									 ->join(array('tbl_studentregistration'=>'tbl_studentregistration'),'tbl_studentapplication.IdApplication = tbl_studentregistration.IdApplication',array('tbl_studentregistration.IdStudentRegistration','tbl_studentregistration.registrationId','tbl_studentregistration.IdSemester','tbl_studentregistration.IdSemestersyllabus'))
									 ->joinLeft(array('tbl_studentregsubjects'=>'tbl_studentregsubjects'),'tbl_studentregistration.IdStudentRegistration = tbl_studentregsubjects.IdStudentRegistration',array('tbl_studentregsubjects.IdSubject'))
									 ->joinLeft(array('tbl_subjectmarksentry'=>'tbl_subjectmarksentry'),'tbl_studentregsubjects.IdStudentRegistration = tbl_subjectmarksentry.IdStudentRegistration and tbl_studentregsubjects.IdSubject = tbl_subjectmarksentry.idSubject',array('tbl_subjectmarksentry.IdSubject'))
									 ->join(array('tbl_semester'=>'tbl_semester'),'tbl_studentregistration.IdSemestersyllabus = tbl_semester.IdSemester',array('tbl_semester.ShortName'))
									 ->where('tbl_studentapplication.FName like "%" ? "%"',$post['field4'])
       								 ->where('tbl_studentregistration.IdSemester like "%" ? "%"',$post['field2'])
       								 ->where('tbl_studentregistration.registrationId like "%" ? "%"',$post['field3'])
       								 ->where("tbl_studentapplication.Offered = 1")
       								 ->where("tbl_studentapplication.Termination = 0")
       								 ->where("tbl_studentapplication.Accepted = 1")
       								 ->group("tbl_studentapplication.IdApplication");
       								 
		if(isset($post['field8']) && !empty($post['field8']) ){
				$lstrSelect = $lstrSelect->where("tbl_studentapplication.IDCourse = ?",$post['field8']);
		}
		
        $larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	 }
	 
	public function fngetSubjectNameCombo()
	{
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		 $lstrselect = $lobjDbAdpt->select()
							->from(array("pg"=>"tbl_subjectmaster"),array("pg.IdSubject AS key","pg.SubjectName AS value"))
							->where("pg.Active = 1")
							->order("pg.SubjectName");
		$larrresult = $lobjDbAdpt->fetchAll($lstrselect);
		return $larrresult;
	}
	
	public function fnGetStudentsList(){
	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
								 ->from(array('stud'=>'tbl_studentapplication'),array("key"=>"stud.IdApplication","value"=>"CONCAT_WS(' ',IFNULL(stud.FName,''),IFNULL(stud.MName,''),IFNULL(stud.LName,''))"))
								 ->where('stud.Active = 1')
								 ->where("stud.Termination = 0")
								 ->order('stud.FName');
								  //echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function fnViewSubjectDetails($IdApplication) { //Function to get the user details
      	$auth = Zend_Auth::getInstance();
	   $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	   $lstrSelect = $lobjDbAdpt->select()
       								->from(array("tbl_studentapplication"=>"tbl_studentapplication"),array("tbl_studentapplication.IDCourse"))
									->join(array('tbl_studentregistration'=>'tbl_studentregistration'),'tbl_studentapplication.IdApplication = tbl_studentregistration.IdApplication',array('tbl_studentregistration.IdStudentRegistration','tbl_studentregistration.registrationId','tbl_studentregistration.IdSemester','tbl_studentregistration.IdSemestersyllabus'))
									->join(array('tbl_studentregsubjects'=>'tbl_studentregsubjects'),'tbl_studentregistration.IdStudentRegistration = tbl_studentregsubjects.IdStudentRegistration',array('tbl_studentregsubjects.IdSubject'))
									->join(array('tbl_subjectmarksentry'=>'tbl_subjectmarksentry'),'tbl_studentregsubjects.IdStudentRegistration = tbl_subjectmarksentry.IdStudentRegistration and tbl_studentregsubjects.IdSubject = tbl_subjectmarksentry.idSubject',array('tbl_subjectmarksentry.IdSubject'))
									->join(array('tbl_semester'=>'tbl_semester'),'tbl_studentregistration.IdSemestersyllabus = tbl_semester.IdSemester',array('tbl_semester.IdSemester'))
									->join(array('tbl_verifiermarks'=>'tbl_verifiermarks'),'tbl_subjectmarksentry.idSubjectMarksEntry = tbl_verifiermarks.idSubjectMarksEntry',array('tbl_verifiermarks.verifiresubjectmarks','tbl_verifiermarks.Rank'))
									->where("tbl_studentapplication.IdApplication = ?",$IdApplication);									
        $larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	 }
	 
	public function fnGetSubjectprerequisitsvalidation($lintidsubject,$lintidapplicant,$takemarks){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		
		
	if($takemarks == 0) {
         			 					
			 $select ='SELECT w.*,SUM(w.totalsubmarks) as fullcalculatedmarks FROM
				(SELECT `a`.*,`c`.`IdSemestersyllabus`,`sa`.`IDCourse`,`e`.`idVerifierMarks`, `e`.`idverifier` , AVG(e.verifiresubjectmarks) AS `totalsubmarks`,`sm`.`IdSubject` ,`sm`.`SubjectName`,`sm`.`CreditHours`,`gpa`.`Idgpacalculation`
					
				FROM `tbl_subjectprerequisites` AS `a`
					
					INNER JOIN `tbl_studentregsubjects` AS `b` ON a.IdRequiredSubject = b.IdSubject
					INNER JOIN `tbl_studentregistration` AS `c` ON b.IdStudentRegistration = c.IdStudentRegistration
					INNER JOIN `tbl_studentapplication` AS `sa` ON c.IdApplication = sa.IdApplication					
					INNER JOIN `tbl_subjectmarksentry` AS `d` ON c.IdStudentRegistration = d.IdStudentRegistration AND a.IdRequiredSubject= d.idSubject
					INNER JOIN `tbl_subjectmaster` as `sm` ON d.idSubject = sm.IdSubject
					INNER JOIN `tbl_verifiermarks` AS `e` ON d.idSubjectMarksEntry = e.idSubjectMarksEntry WHERE (c.IdStudentRegistration  =  '.$lintidapplicant.') 
					LEFT JOIN `tbl_gpacalculation` as `gpa` ON c.IdStudentRegistration = gpa.IdStudentRegistration
					GROUP BY `e`.`idSubjectMarksEntry`) w 
					GROUP BY w.IdRequiredSubject';		

		} else if($takemarks == 1) {			
			 				 
			 $select = $lobjDbAdpt->select()
			 				->from(array('c'=>'tbl_studentregistration'),array('c.IdStudentRegistration','c.IdSemestersyllabus'))
			 				->join(array('sa'=>'tbl_studentapplication'),'c.IdApplication = sa.IdApplication',array('sa.IDCourse'))
			 				->join(array('b'=>'tbl_studentregsubjects'),'c.IdStudentRegistration = b.IdStudentRegistration')
			 				->join(array('d'=>'tbl_subjectmarksentry'),'b.IdStudentRegistration = d.IdStudentRegistration AND b.IdSubject = d.idSubject',array())
							->join(array('e'=>'tbl_verifiermarks'),'d.idSubjectMarksEntry = e.idSubjectMarksEntry',array( 'SUM(e.verifiresubjectmarks) as fullcalculatedmarks','e.idVerifierMarks','e.idverifier'))
							->join(array('tbl_subjectmaster'=>'tbl_subjectmaster'),'d.idSubject = tbl_subjectmaster.IdSubject')
							->joinLeft(array('tbl_gpacalculation'=>'tbl_gpacalculation'),'c.IdStudentRegistration = tbl_gpacalculation.IdStudentRegistration')
							->where("e.Rank = 1 OR e.Rank = 0")						
			 				->where('c.IdStudentRegistration = ?',$lintidapplicant)
			 				->group('d.idSubject');
		}
		
		$larrResult = $lobjDbAdpt->fetchAll($select);
		
		return $larrResult;
	}
	
    public function fnGetGradepoints($IdProgram,$IdSemester,$IdSubject,$subjectmarks){
    	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
    	$select 	= $lobjDbAdpt->select()
						->from(array("a" =>"tbl_gradesetup"),array('a.GradePoint','a.MinPoint','a.MaxPoint'))	
					    ->join(array("sm"=>"tbl_subjectmaster"),'a.IdSubject = sm.IdSubject',array('sm.CreditHours'))
		            	->where("a.IdProgram = ?",$IdProgram)
		            	->where("a.IdSemester = ?",$IdSemester)
		            	->where("a.IdSubject = ?",$IdSubject);		            	
		//  echo $select;      	
		return $result = $lobjDbAdpt->fetchAll($select);
    	
    }
}
?>