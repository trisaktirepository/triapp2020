<?php

class Examination_Model_DbTable_Resitmarksentry extends Zend_Db_Table_Abstract {

	protected $_name = 'tbl_examination_resit_marksentry';
	private $lobjDbAdpt;

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function getSemcode($semester) {
		$dataSem = explode('_', $semester);
		if ($dataSem[1] == 'detail') {
			$semDetail = $dataSem[0];
			$semsql = $this->lobjDbAdpt->select()->from(array("semdet" => "tbl_semester"), array('semdet.SemesterCode'))
			->where("semdet.IdSemester = ?", $semDetail);
			$result = $this->lobjDbAdpt->fetchAll($semsql);
			$semesterCode = $result[0]['SemesterCode'];
		} else {
			$semMain = $dataSem[0];
			$semsql = $this->lobjDbAdpt->select()->from(array("semmast" => "tbl_semestermaster"), array('semmast.SemesterMainCode'))
			->where("semmast.IdSemesterMaster = ?", $semMain);
			$result = $this->lobjDbAdpt->fetchAll($semsql);
			$semesterCode = $result[0]['SemesterMainCode'];
		}
		return $semesterCode;
	}

	public function fnSearchResitMarksEntry($post = array()) {

		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_examination_resit_application"), array("a.*"))
		->joinLeft(array('nm' => 'tbl_studentregistration'), 'nm.IdStudentRegistration = a.IdStudent',array("CONCAT_WS(' ',IFNULL(nm.FName,''),IFNULL(nm.MName,''),IFNULL(nm.LName,'')) as StudentName"))
		->joinLeft(array('b' => 'tbl_program'), 'a.IdProgram = b.IdProgram', array('b.ProgramName'))
		//->joinLeft(array('c'=>'tbl_subjectmaster'),'a.IdCourse = c.IdSubject',array('c.SubjectName'))
		->joinLeft(array('d' => 'tbl_definationms'), 'a.Status = d.idDefinition', array('d.DefinitionCode'));
		//->joinLeft(array('e'=>'tbl_appealmarksentry'),'a.IdAppeal = e.IdAppeal',array('e.IdComponent'));


		if (isset($post['field27']) && !empty($post['field27'])) {
			$getSemCode = $this->getSemcode($post['field27']);
			$select->where("a.SemesterCode = ?", $getSemCode);
		}

		if (isset($post['field2']) && !empty($post['field2'])) {
			$select->where("a.IdProgram = ?", $post['field2']);
		}

		if (isset($post['field24']) && !empty($post['field24'])) {
			$select->where("a.StudentCode = ?", $post['field24']);
		}

		if (isset($post['field25']) && !empty($post['field25'])) {
			$select->where("a.Status = ?", $post['field25']);
		}

		if (isset($post['field3']) && !empty($post['field3'])) {
			$select = $select->where("a.ExaminationResitApplicationCode = ?", $post['field3']);
		}

		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;
	}

	public function getEntryDetailByID($IdExaminationResitApplication) {
		$semsql = $this->lobjDbAdpt->select()->from(array("ex" => "tbl_examination_resit_application"), array('ex.*'))
		->joinLeft(array('b' => 'tbl_program'), 'ex.IdProgram = b.IdProgram', array('b.ProgramName'))
		->where("ex.IdExaminationResitApplication = ?", $IdExaminationResitApplication);
		$result = $this->lobjDbAdpt->fetchAll($semsql);
		return $result;
	}

	public function fnViewstaffsubject() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("tss" => "tbl_staffsubject"), array(""))
		->joinLeft(array("sm" => "tbl_staffmaster"), ' tss.IdStaff = sm.IdStaff ', array("key" => "sm.IdStaff", "value" => "CONCAT_WS(' ',IFNULL(sm.FirstName,''),IFNULL(sm.SecondName,''))"))
		->group('tss.IdStaff');
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnSearchMarksEntryDetails($IdExaminationResitApplication) {

		$select = $this->lobjDbAdpt->select()
		->from(array("marksmaster" => "tbl_examination_resit_application"), array('marksmaster.IdStudent'))
		->joinLeft(array("sme" => "tbl_examination_resit_marksentry"), "sme.IdExaminationResitApplication = marksmaster.IdExaminationResitApplication ", array('sme.*'))
		->joinLeft(array('subj' => 'tbl_subjectmaster'), 'sme.IdCourse = subj.IdSubject', array("CONCAT_WS('-',IFNULL(subj.SubjectName,''),IFNULL(subj.SubCode,'')) as SubjectName"))
		->joinLeft(array("compdef" => "tbl_examination_assessment_type"), 'compdef.IdExaminationAssessmentType = sme.IdComponent', array('compdef.Description as ComponentName'))
		->joinLeft(array("mdd" => "tbl_marksdistributiondetails"), "mdd.IdMarksDistributionDetails = sme.IdComponentDetail ", array(''))
		->joinLeft(array("compdef2" => "tbl_examination_assessment_type"), 'compdef2.IdExaminationAssessmentType = mdd.IdComponentType', array('compdef2.Description as SubComponentName'))
		->joinLeft(array("compdef4" => "tbl_examination_assessment_item"), 'compdef4.IdExaminationAssessmentType = sme.ComponentItem', array('compdef4.Description as MainSubComponentName'))
		//->joinLeft(array("ame" =>"tbl_appealmarksentry")," ame.IdAppeal = '".$idAppeal."' ",array('ame.IdAppealEntry'))
		//->joinLeft(array("sme" =>"tbl_student_marks_entry"),"sme.Component = marksmaster.IdComponentType AND sme.Course='".$idcourse."' AND sme.IdStudentRegistration = '".$idstudent."' ",array(''))
		// ->where("ame.IdAppeal = ?",$idAppeal)
		->where("marksmaster.IdExaminationResitApplication = ?", $IdExaminationResitApplication)
		// ->where("marksmaster.IdCourse = ?",$idstudent)
		// ->where("marksmaster.semester = ?",$SemesterCode)
		//->where("marksmaster.Status = ?",'243')
		//->group("ame.IdComponent")
		;

		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;
	}

	public function fnInsertmarks($larrformData, $status, $getDetailsConfig, $IdExaminationResitApplication) {
		$newMarks = $larrformData['NM'];
		foreach ($newMarks as $key => $values) {
			$formDataMore = array('NewMarks' => $values, 'IdExaminer1' => $larrformData['field23']);
			$where = " IdExaminationResitApplicationMarks =  '" . $key . "'  ";
			$this->lobjDbAdpt->update('tbl_examination_resit_marksentry', $formDataMore, $where);
		}

		// update the application status
		$formDataUpdate = array('Status' => $status);
		$where_st = " IdExaminationResitApplication ='" . $IdExaminationResitApplication . "'  ";
		$this->lobjDbAdpt->update('tbl_examination_resit_application', $formDataUpdate, $where_st);
	}

	public function fngetStaffReg($IdExaminationResitApplication) {
		$semsql = $this->lobjDbAdpt->select()->from(array("appeal" => "tbl_examination_resit_marksentry"), array('appeal.IdExaminer1'))->where("appeal.IdExaminationResitApplication = ?", $IdExaminationResitApplication)->group('appeal.IdExaminationResitApplication');
		$result = $this->lobjDbAdpt->fetchAll($semsql);
		return $result;
	}

	public function fnApplicationEntryStatus($IdExaminationResitApplication) {
		$semsql = $this->lobjDbAdpt->select()->from(array("appeal" => "tbl_examination_resit_application"), array('appeal.Status'))->where("appeal.IdExaminationResitApplication = ?", $IdExaminationResitApplication);
		$result = $this->lobjDbAdpt->fetchAll($semsql);
		return $result;
	}

	public function fnSearchResitMarksApproval($post = array()) {

		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_examination_resit_application"), array("a.*"))
		->joinLeft(array('nm' => 'tbl_studentregistration'), 'nm.IdStudentRegistration = a.IdStudent',array("CONCAT_WS(' ',IFNULL(nm.FName,''),IFNULL(nm.MName,''),IFNULL(nm.LName,'')) as StudentName"))
		->joinLeft(array('b' => 'tbl_program'), 'a.IdProgram = b.IdProgram', array('b.ProgramName'))
		//->joinLeft(array('c'=>'tbl_subjectmaster'),'a.IdCourse = c.IdSubject',array('c.SubjectName'))
		->joinLeft(array('d' => 'tbl_definationms'), 'a.Status = d.idDefinition', array('d.DefinitionCode'));
		//->joinLeft(array('e'=>'tbl_appealmarksentry'),'a.IdAppeal = e.IdAppeal',array('e.IdComponent'));


		if (isset($post['field27']) && !empty($post['field27'])) {
			$getSemCode = $this->getSemcode($post['field27']);
			$select->where("a.SemesterCode = ?", $getSemCode);
		}

		if (isset($post['field2']) && !empty($post['field2'])) {
			$select->where("a.IdProgram = ?", $post['field2']);
		}

		if (isset($post['field24']) && !empty($post['field24'])) {
			$select->where("a.StudentCode = ?", $post['field24']);
		}

		if (isset($post['field3']) && !empty($post['field3'])) {
			$select = $select->where("a.ExaminationResitApplicationCode = ?", $post['field3']);
		}

		if (isset($post['field25']) && !empty($post['field25'])) {
			$select->where("a.Status = ?", $post['field25']);
		} else {
			$select->where("a.Status != ?", '192');
		}

		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;
	}

	public function fnInsertApprovalReject($larrformData, $status, $chooseMarks, $IdExaminationResitApplication) {

		$finalMarks = $larrformData['IdExaminationResitApplicationMarks'];
		$IdStudent = $larrformData['IdStudent'];
		$IdCourse = $larrformData['IdCourse'];
		$totalMarks = 0;
		foreach ($finalMarks as $key=>$values) {
			$FM = $larrformData['FM_' . $values];
			$RM = $larrformData['RM_' . $values];
			$OM = $larrformData['OM_' . $values];
			$CB = $larrformData['resitchk'][$key];
			$CTY = $larrformData['CTY_' . $values];
			$CIT = $larrformData['CIT_' . $values];
			if ($CB == '1') {

				if ($chooseMarks == '381') {
					$newMarks = $FM;
				} else {
					$newMarks = max($OM, $FM);
				}

				$formDataMore = array('FinalMarks' => $newMarks, 'Status' => $status, 'EntryRemark' => $RM);
				$where = " IdExaminationResitApplicationMarks =  '" . $values . "'  ";
				$this->lobjDbAdpt->update('tbl_examination_resit_marksentry', $formDataMore, $where);

				// UPDATE the totalMarksObtained
				$totalMrksUpdateSME = array( 'TotalMarkObtainedResit' => $newMarks );
				$where_TMU_SME = " IdStudentRegistration='".$IdStudent."' AND Course='".$IdCourse."' AND Component='".$CTY."'  AND ComponentItem='".$CIT."'  ";
				$this->lobjDbAdpt->update('tbl_student_marks_entry',$totalMrksUpdateSME,$where_TMU_SME);

				$totalMarks =  $totalMarks+$newMarks;

			}
		}


		if($totalMarks!='') {
			// UPDATE the totalMarksObtained
			$totalMrksUpdate = array('TotalMarksResit' => $totalMarks);
			$where_TMU = " IdStudentRegistration='".$IdStudent."' AND  IdSubject='".$IdCourse."'  ";
			$this->lobjDbAdpt->update('tbl_studentregsubjects',$totalMrksUpdate,$where_TMU);
		}

		// update the application status
		$formDataUpdate = array('Status' => $status);
		$where_st = " IdExaminationResitApplication ='" . $IdExaminationResitApplication . "'  ";
		$this->lobjDbAdpt->update('tbl_examination_resit_application', $formDataUpdate, $where_st);
	}

	public function fnupdateStudentGradePoint($SemesterCode, $idstudent, $IdProgram) {

		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$IdSemester = '';

		$select = $lobjDbAdpt->select()
		->from(array("studregsubj" => "tbl_studentregsubjects"), array('studregsubj.IdStudentRegSubjects', 'studregsubj.IdStudentRegistration', 'studregsubj.IdSubject', 'studregsubj.IdSemesterMain', 'studregsubj.IdSemesterDetails', 'studregsubj.IdGrade'))
		->join(array("studreg" => "tbl_studentregistration"), 'studreg.IdStudentRegistration = studregsubj.IdStudentRegistration', array('studreg.registrationId', 'studreg.IdProgram'))
		->joinLeft(array("prg" => "tbl_program"), 'prg.IdProgram = studreg.IdProgram', array('prg.IdCollege'))
		//->joinLeft(array("sch" => "tbl_scheme"), 'sch.IdScheme = prg.IdScheme', array(''))
		->joinLeft(array("lscp" =>"tbl_landscape"),'studreg.IdLandscape = lscp.IdLandscape',array('lscp.Scheme'))
		->joinLeft(array("sch" =>"tbl_scheme"),'lscp.Scheme = sch.IdScheme',array('sch.EnglishDescription as SchemeName'))
		;
		//$select->where("studregsubj.IdSubject = ?",$idcourse);
		$select->where("studregsubj.IdStudentRegistration = ?", $idstudent);

		$semsql1 = $this->lobjDbAdpt->select()->from(array("semdet" => "tbl_semestermaster"), array('semdet.IdSemesterMaster'))
		->where("semdet.SemesterMainCode = ?", $SemesterCode);
		$result1 = $this->lobjDbAdpt->fetchAll($semsql1);
		if (count($result1) > 0) {
			$IdSemester = $result1[0]['IdSemesterMaster'];
			$select->where("studregsubj.IdSemesterMain = ?", $IdSemester);
		} else {
			$semsql2 = $this->lobjDbAdpt->select()->from(array("semdet" => "tbl_semester"), array('semdet.IdSemester'))
			->where("semdet.SemesterCode = ?", $SemesterCode);
			$result2 = $this->lobjDbAdpt->fetchAll($semsql2);
			if (count($result2) > 0) {
				$IdSemester = $result2[0]['IdSemester'];
				$select->where("studregsubj.IdSemesterDetails = ?", $IdSemester);
			}
		}
		$where_cond_grade = " studregsubj.IdGrade IS NULL OR  studregsubj.IdGrade='pass'  OR  studregsubj.IdGrade='fail'  ";
		$select->where($where_cond_grade);
		$select->group("studregsubj.IdSubject");
		$result = $lobjDbAdpt->fetchAll($select);


		$IdStudentMarksEntry = "";
		$TotalMarksF = 0;
		if (count($result) > 0) {
			foreach ($result as $values) {

				$id = $values['IdStudentRegSubjects'];
				$IdSubject = $values['IdSubject'];
				$IdProgram = $values['IdProgram'];
				$SemCode = $SemesterCode;


				$select1 = $lobjDbAdpt->select()
				->from(array("sme" => "tbl_student_marks_entry"), array('sme.TotalMarkObtainedResit'))
				->where("sme.IdStudentRegSubjects = ?", $id)->where("sme.MarksEntryStatus = ?", '313');
				$result1 = $lobjDbAdpt->fetchAll($select1);
				if (count($result1) > 0) {
					//                    foreach ($result1 as $res1) {
					//                        $IdStudentMarksEntry .= $res1['IdStudentMarksEntry'] . ',';
					//                    }
					//                    $IdStudentMarksEntry = trim($IdStudentMarksEntry, ',');
					//
					//                    $select2 = $lobjDbAdpt->select()
					//                                    ->from(array("sdme" => "tbl_student_detail_marks_entry"), array('sdme.MarksObtained'))
					//                                    ->where("sdme.IdStudentMarksEntry IN ($IdStudentMarksEntry) ")->group("sdme.IdStudentMarksEntry");
					//                    $result2 = $lobjDbAdpt->fetchAll($select2);
					//                    foreach ($result2 as $res2) {
					//                        $TotalMarksF = $TotalMarksF + $res2['MarksObtained'];
					//                    }

					$TotalMarksF = $result1[0]['TotalMarkObtainedResit'];


					$select3 = $lobjDbAdpt->select()
					->from(array("gsm" => "tbl_gradesetup_main"), array('gsm.IdGradeSetUpMain'))
					->where("gsm.IdProgram = ?", $IdProgram)
					->where("gsm.IdSubject = ?", $IdSubject)
					->where("gsm.IdSemester = ?", $SemCode);
					//->where("gsm.IdScheme = ?",$IdScheme);
					$result3 = $lobjDbAdpt->fetchAll($select3);
					foreach ($result3 as $res3) {
						$select4 = $lobjDbAdpt->select()
						->from(array("gs" => "tbl_gradesetup"), array('gs.Grade', 'gs.Pass', 'gs.Rank', 'gs.GradePoint'))
						->where(" $TotalMarksF BETWEEN  gs.MinPoint AND gs.MaxPoint")
						->where("gs.IdGradeSetUpMain = ?", $res3['IdGradeSetUpMain']);
						$result4 = $lobjDbAdpt->fetchAll($select4);
						if (count($result4) > 0) {
							//                            if ($result4[0]['Rank'] == '1') {
							//                                $rankD = 'pass';
							//                            } else {
							//                                $rankD = 'fail';
							//                            }
							if($result4[0]['Pass']=='1') {
								$rankD = 'pass';
							} else {  $rankD = 'fail';
							}
							$formDataupdate = array(
									'IdGrade' => $rankD,
									'Grade' => $result4[0]['Grade'],
									'Totalmarks' => $TotalMarksF,
									'GradePoint' => $result4[0]['GradePoint'],
									'Rank' => $result4[0]['Rank'],
									'Pass' => $result4[0]['Pass']
							);
							$lobjDbAdpt->update('tbl_studentregsubjects', $formDataupdate, array('IdStudentRegSubjects = ?' => $id));
						}
					}
				}
			}
		}
	}

	public function getstudentprogram($studentcode) {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_studentregistration"), array("a.IdStudentRegistration", "a.IdProgram"))
		->joinLeft(array('b' => 'tbl_program'), 'a.IdProgram = b.IdProgram', array('b.ProgramName'))
		->where('a.registrationId =?', $studentcode);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}

	public function getstudentcourses($IdStudentRegistration) {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_studentregistration"), array(""))
		->joinLeft(array('b' => 'tbl_landscapesubject'), 'a.IdLandscape = b.IdLandscape', array(''))
		->joinLeft(array('c' => 'tbl_subjectmaster'), 'b.IdSubject = c.IdSubject', array('key' => 'c.IdSubject', 'value' =>"CONCAT_WS('-',IFNULL(c.SubjectName,''),IFNULL(c.SubCode,''))"))
		->where('a.IdStudentRegistration =?', $IdStudentRegistration)
		->order('c.SubjectName ASC');
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function getfinalcomponets($Idcourse) {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributionmaster"), array("a.IdComponentType"))
		->joinLeft(array('b' => 'tbl_examination_assessment_type'), 'a.IdComponentType = b.IdExaminationAssessmentType', array('b.Description as DefinitionDesc'))
		->joinLeft(array('c' => 'tbl_subcredithoursdistrbtn'), 'a.IdComponentType = c.Idcomponents', array(''))
		->where('b.Description like  "%" ? "%"', 'final')
		->where('c.IdSubject  =?', $Idcourse)
		->group("a.IdComponentType");
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

}