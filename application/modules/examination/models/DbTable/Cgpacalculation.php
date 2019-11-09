<?php 
class Examination_Model_DbTable_Cgpacalculation extends Zend_Db_Table_Abstract
{
	protected $_name = 'tbl_gpacalculation';
	private $lobjDbAdpt;

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function fnGetAvgmarks($lintidapplicant){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrselect = $lobjDbAdpt->select()
		->from(array("tbl_studentapplication"=>"tbl_studentapplication"),array("tbl_studentapplication.*"))
		->join(array('tbl_studentregistration'=>'tbl_studentregistration'),'tbl_studentapplication.IdApplication  = tbl_studentregistration.IdApplication')
		->join(array('tbl_gpacalculation'=>'tbl_gpacalculation'),'tbl_studentregistration.IdStudentRegistration  = tbl_gpacalculation.IdStudentRegistration',array("ROUND(AVG(tbl_gpacalculation.Gpa),2) AS totalsubmarks"))
		->join(array('tbl_program'=>'tbl_program'),'tbl_studentapplication.IDCourse  = tbl_program.IdProgram',array("tbl_program.ProgramName"))
		->where("tbl_studentapplication.Active = 1")
		->where("tbl_studentapplication.IdApplication = ?",$lintidapplicant);
		$larrresult = $lobjDbAdpt->fetchRow($lstrselect);
		return $larrresult;

	}

	public function fngetStudentApplicationDetails() { //Function to get the Program Branch details
		$select = $this->select()
		->setIntegrityCheck(false)
		->join(array('a' =>'tbl_studentapplication'),array('IdApplication'))
		->join(array('c'=>'tbl_studentregistration'),'a.IdApplication  = c.IdApplication')
		->join(array('b'=>'tbl_sendoffer'),'a.IdApplication  = b.IdApplication')
		->where("a.Active = 1")
		->where("b.Approved = 1")
		->where("a.Offered = 1")
		->where("a.Accepted = 1")
		->where("a.Termination = 0")
		->order("a.FName");
		$result = $this->fetchAll($select);
		return $result->toArray();
	}

	public function fnSearchStudentApplication($post = array()) { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("sa"=>"tbl_studentapplication"),array("sa.*"))
		->join(array('c'=>'tbl_studentregistration'),'a.IdApplication  = c.IdApplication')
		->join(array('b'=>'tbl_sendoffer'),'sa.IdApplication  = b.IdApplication');
			
		if(isset($post['field5']) && !empty($post['field5']) ){
			$lstrSelect = $lstrSelect->where("sa.IdApplication = ?",$post['field5']);

		}
			
		if(isset($post['field8']) && !empty($post['field8']) ){
			$lstrSelect = $lstrSelect->where("sa.IDCourse = ?",$post['field8']);

		}
			
		$lstrSelect	->where('sa.ICNumber like "%" ? "%"',$post['field2'])
		->where('sa.StudentId like "%" ? "%"',$post['field3'])
		->where("b.Approved = 1")
		->where("sa.Offered = 1")
		->where("sa.Accepted = 1")
		->where("sa.Termination = 0")
		->order("sa.FName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}





	public function fnSavegpa($formData) { //Function for adding the Program Branch details to the table

		unset ( $formData ['IdApplication']);
		unset ( $formData ['Save']);
		return $this->insert($formData);
	}

	public function fnSearchStudentsubjects($post = array()) { //Function to get the user details
		$auth = Zend_Auth::getInstance();
		$lintidstaff =$auth->getIdentity()->IdStaff;
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("tbl_studentapplication"=>"tbl_studentapplication"),array("tbl_studentapplication.*"))
		->join(array('tbl_studentregistration'=>'tbl_studentregistration'),'tbl_studentapplication.IdApplication = tbl_studentregistration.IdApplication',array('tbl_studentregistration.IdStudentRegistration','tbl_studentregistration.registrationId','tbl_studentregistration.IdSemester','tbl_studentregistration.IdSemestersyllabus'))
		->join(array('tbl_studentregsubjects'=>'tbl_studentregsubjects'),'tbl_studentregistration.IdStudentRegistration = tbl_studentregsubjects.IdStudentRegistration',array('tbl_studentregsubjects.IdSubject'))
		->join(array('tbl_subjectmarksentry'=>'tbl_subjectmarksentry'),'tbl_studentregsubjects.IdStudentRegistration = tbl_subjectmarksentry.IdStudentRegistration and tbl_studentregsubjects.IdSubject = tbl_subjectmarksentry.idSubject',array('tbl_subjectmarksentry.IdSubject'))
		->join(array('tbl_semester'=>'tbl_semester'),'tbl_studentregistration.IdSemestersyllabus = tbl_semester.IdSemester',array('tbl_semester.ShortName'))
		->where('tbl_studentapplication.FName like "%" ? "%"',$post['field4'])
		->where('tbl_studentregistration.IdSemester like "%" ? "%"',$post['field2'])
		->where("tbl_studentapplication.Offered = 1")
		->where("tbl_studentapplication.Termination = 0")
		->where("tbl_studentapplication.Accepted = 1")
		->group("tbl_studentregistration.IdStudentRegistration");
			
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
					(SELECT `a`.*,`c`.`IdSemestersyllabus`,`sa`.`IDCourse`,`e`.`idVerifierMarks`, `e`.`idverifier` , AVG(e.verifiresubjectmarks) AS `totalsubmarks`,`sm`.`IdSubject` ,`sm`.`SubjectName`,`sm`.`CreditHours`

					FROM `tbl_subjectprerequisites` AS `a`

					INNER JOIN `tbl_studentregsubjects` AS `b` ON a.IdRequiredSubject = b.IdSubject
					INNER JOIN `tbl_studentregistration` AS `c` ON b.IdStudentRegistration = c.IdStudentRegistration
					INNER JOIN `tbl_studentapplication` AS `sa` ON c.IdApplication = sa.IdApplication
					INNER JOIN `tbl_subjectmarksentry` AS `d` ON c.IdStudentRegistration = d.IdStudentRegistration AND a.IdRequiredSubject= d.idSubject
					INNER JOIN `tbl_subjectmaster` as `sm` ON d.idSubject = sm.IdSubject
					INNER JOIN `tbl_verifiermarks` AS `e` ON d.idSubjectMarksEntry = e.idSubjectMarksEntry WHERE (c.IdStudentRegistration  =  '.$lintidapplicant.')
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
			//->join(array('tbl_gradesetup'=>'tbl_gradesetup'),'sa.IDCourse = tbl_gradesetup.IdProgram AND c.IdSemestersyllabus = tbl_gradesetup.IdSemester AND d.idSubject = tbl_gradesetup.IdSubject')
			->where("e.Rank = 1 OR e.Rank = 0")
			->where('c.IdStudentRegistration = ?',$lintidapplicant)
			->group('d.idSubject');
		}
		//echo $select;exit;
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