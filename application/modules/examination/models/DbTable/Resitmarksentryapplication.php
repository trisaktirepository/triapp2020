<?php

class Examination_Model_DbTable_Resitmarksentryapplication extends Zend_Db_Table_Abstract {

	protected $_name = 'tbl_examination_resit_application';
	private $lobjDbAdpt;

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}


	public function addresitmarksentryapplication($data){
		$lastinsertid = $this->insert($data);
		return $lastinsertid;
	}

	public function addresitmarksentryapplicationmarks($marksdata){
		$this->lobjDbAdpt->insert('tbl_examination_resit_marksentry',$marksdata);
		return true;
	}

	public function fnAllapplication(){
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_examination_resit_application'), array('a.IdExaminationResitApplication','a.ExaminationResitApplicationCode','a.StudentCode','a.IdProgram','a.Status','a.mode'))
		->joinLeft(array('nm' => 'tbl_studentregistration'), 'nm.IdStudentRegistration = a.IdStudent',array("CONCAT_WS(' ',IFNULL(nm.FName,''),IFNULL(nm.MName,''),IFNULL(nm.LName,'')) as StudentName"))
		->joinLeft(array('b' => 'tbl_definationms'), 'a.Status = b.idDefinition',array('b.DefinitionDesc'))
		->joinLeft(array('c' => 'tbl_program'), 'a.IdProgram = c.IdProgram',array('c.ProgramName'))
		->joinLeft(array('d' => 'tbl_examination_resit_marksentry'), 'a.IdExaminationResitApplication = d.IdExaminationResitApplication',array('d.IdComponent'))
		->joinLeft(array('e' => 'tbl_examination_assessment_type'), 'd.IdComponent = e.IdExaminationAssessmentType',array('e.Description as componentname'))
		->group('a.IdExaminationResitApplication');
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}


	public function fnSearchapplication($post = array()) { //Function for searching the user details
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_examination_resit_application'), array('a.IdExaminationResitApplication','a.ExaminationResitApplicationCode','a.StudentCode','a.IdProgram','a.Status','a.mode'))
		->joinLeft(array('nm' => 'tbl_studentregistration'), 'nm.IdStudentRegistration = a.IdStudent',array("CONCAT_WS(' ',IFNULL(nm.FName,''),IFNULL(nm.MName,''),IFNULL(nm.LName,'')) as StudentName"))
		->joinLeft(array('b' => 'tbl_definationms'), 'a.Status = b.idDefinition',array('b.DefinitionDesc'))
		->joinLeft(array('c' => 'tbl_program'), 'a.IdProgram = c.IdProgram',array('c.ProgramName'))
		->joinLeft(array('d' => 'tbl_examination_resit_marksentry'), 'a.IdExaminationResitApplication = d.IdExaminationResitApplication',array('d.IdComponent'))
		->joinLeft(array('e' => 'tbl_examination_assessment_type'), 'd.IdComponent = e.IdExaminationAssessmentType',array('e.Description as componentname'))
		->group('a.IdExaminationResitApplication');

		if (isset($post['field3']) && !empty($post['field3'])) {
			$lstrSelect->where('a.StudentCode like  "%" ? "%"', $post['field3']);
		}

		if (isset($post['field8']) && !empty($post['field8'])) {
			$lstrSelect = $lstrSelect->where('a.SemesterCode like  "%" ? "%"', $post['field8']);
		}
		if (isset($post['field5']) && !empty($post['field5'])) {
			$lstrSelect = $lstrSelect->where("a.IdProgram = ?", $post['field5']);
		}
		if (isset($post['field27']) && !empty($post['field27'])) {
			$lstrSelect = $lstrSelect->where("d.IdProgram = ?", $post['field27']);
		}

		if (isset($post['field10']) && !empty($post['field10'])) {
			$lstrSelect = $lstrSelect->where("a.Status = ?", $post['field10']);
		}

		if (isset($post['field24']) && !empty($post['field24'])) {
			$lstrSelect = $lstrSelect->where('a.semester like  "%" ? "%"', $post['field24']);
		}

		if (isset($post['field20']) && !empty($post['field20'])) {
			$lstrSelect = $lstrSelect->where('a.mode like  "%" ? "%"', $post['field20']);
		}

		if (isset($post['field4']) && !empty($post['field4'])) {
			$lstrSelect = $lstrSelect->where('a.ExaminationResitApplicationCode like  "%" ? "%"', $post['field20']);
		}
			
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);

		return $larrResult;
	}

	public function getapplicationdate($id){
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_examination_resit_application'), array('a.ExaminationResitApplicationCode','a.StudentCode','a.IdProgram','a.Status','a.mode','a.SemesterCode','a.IdStudent'))
		->joinLeft(array('b' => 'tbl_definationms'), 'a.Status = b.idDefinition',array('b.DefinitionDesc'))
		->joinLeft(array('c' => 'tbl_program'), 'a.IdProgram = c.IdProgram',array('c.ProgramName'))
		->joinLeft(array('d' => 'tbl_examination_resit_marksentry'), 'a.IdExaminationResitApplication = d.IdExaminationResitApplication',array('d.IdComponent','d.IdCourse'))
		->joinLeft(array('e' => 'tbl_examination_assessment_type'), 'd.IdComponent = e.IdExaminationAssessmentType',array('e.Description as componentname'))
		->joinLeft(array('f' => 'tbl_subjectmaster'), 'd.IdCourse = f.IdSubject',array("CONCAT_WS('-',IFNULL(f.SubjectName,''),IFNULL(f.SubCode,'')) as course"))
		->where("a.IdExaminationResitApplication = ?", $id)->group('d.IdCourse');
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);

		return $larrResult;

	}


	public function deleteresitmarksentryapplicationmarks($id){
		$thjdbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrwhere = "IdExaminationResitApplication = ".$id;
		$thjdbAdpt->delete('tbl_examination_resit_marksentry',$lstrwhere);
	}


	public function totalAppealResitMax($getIDstudentReg, $semesterCode, $maxAppeal ) {

		// COUNT Draft
		$appealsql1 =  $this->lobjDbAdpt->select()->from(array("appeal" =>"tbl_examination_resit_application"),array('Count(appeal.IdExaminationResitApplication) as totalDraft'))
		->where("appeal.IdStudent = ?",$getIDstudentReg)
		->where("appeal.SemesterCode = ?",$semesterCode)
		//->where("appeal.IdCourse = ?",$idcourse)
		->where("appeal.Status = ?",'192');
		$result1 = $this->lobjDbAdpt->fetchAll($appealsql1);
		$totalDraft =  $result1[0]['totalDraft'];

		// COUNT Entry
		$appealsql2 =  $this->lobjDbAdpt->select()->from(array("appeal" =>"tbl_examination_resit_application"),array('Count(appeal.IdExaminationResitApplication) as totalEntry'))
		->where("appeal.IdStudent = ?",$getIDstudentReg)
		->where("appeal.SemesterCode = ?",$semesterCode)
		//->where("appeal.IdCourse = ?",$idcourse)
		->where("appeal.Status = ?",'193');
		$result2 = $this->lobjDbAdpt->fetchAll($appealsql2);
		$totalEntry =  $result2[0]['totalEntry'];

		// COUNT Approve
		$appealsql3 =  $this->lobjDbAdpt->select()->from(array("appeal" =>"tbl_examination_resit_application"),array('Count(appeal.IdExaminationResitApplication) as totalApprove'))
		->where("appeal.IdStudent = ?",$getIDstudentReg)
		->where("appeal.SemesterCode = ?",$semesterCode)
		//->where("appeal.IdCourse = ?",$idcourse)
		->where("appeal.Status = ?",'243');
		$result3 = $this->lobjDbAdpt->fetchAll($appealsql3);
		$totalApprove =  $result3[0]['totalApprove'];

		// COUNT Reject
		$appealsql4 =  $this->lobjDbAdpt->select()->from(array("appeal" =>"tbl_examination_resit_application"),array('Count(appeal.IdExaminationResitApplication) as totalReject'))
		->where("appeal.IdStudent = ?",$getIDstudentReg)
		->where("appeal.SemesterCode = ?",$semesterCode)
		//->where("appeal.IdCourse = ?",$idcourse)
		->where("appeal.Status = ?",'195');
		$result4 = $this->lobjDbAdpt->fetchAll($appealsql4);
		$totalReject =  $result4[0]['totalReject'];
		// ENDS

		$finalTotal = ($totalDraft+$totalEntry+$totalApprove);
			
			
		$errMsg  =  '0';  // CAN ADD
		if($finalTotal < $maxAppeal) {
			$errMsg  =  '0';  // can add
		} else   {
			$errMsg  =  '1';  // cannot add
		}
		return $errMsg;

	}


	public function getMaxAppealResitperSemester($IdUniversity) {

		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_examination_resit_configuration"), array("a.ApplicationCount"))
		->where('a.IdUniversity =?',$IdUniversity);
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}




}
?>
