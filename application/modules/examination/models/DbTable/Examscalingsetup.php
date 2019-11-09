<?php

class Examination_Model_DbTable_Examscalingsetup extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_exam_scaling_setup';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function fnExamScalingSetup($larrformData) { //Function for adding the user details to the table
		$lastinsertid = $this->insert($larrformData);
		return $lastinsertid;
	}

	public function fnCheckduplicateScalingSetup($semestercode, $IdScheme) {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_exam_scaling_setup'), array('a.IdExamScaling'))
		->where('a.semestercode =?', $semestercode)
		->where("a.IdScheme =?", $IdScheme);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}

	public function fngetAllsetup() {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_exam_scaling_setup'), array('a.IdExamScaling', 'a.IdScheme', 'a.IdFaculty', 'a.semestercode'))
		->joinLeft(array('b' => 'tbl_scheme'), 'a.IdScheme = b.IdScheme', array('b.EnglishDescription'))
		->joinLeft(array('c' => 'tbl_collegemaster'), 'a.IdFaculty = c.IdCollege', array('c.CollegeName'));
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnsearchAllsetup($post = array()) {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_exam_scaling_setup'), array('a.IdExamScaling', 'a.IdScheme', 'a.IdFaculty', 'a.semestercode'))
		->joinLeft(array('b' => 'tbl_scheme'), 'a.IdScheme = b.IdScheme', array('b.EnglishDescription'))
		->joinLeft(array('c' => 'tbl_collegemaster'), 'a.IdFaculty = c.IdCollege', array('c.CollegeName'))
		->joinLeft(array('d' => 'tbl_program_scheme'), 'b.IdScheme = d.IdScheme', array(''));

		if (isset($post['field8']) && !empty($post['field8'])) {
			$lstrSelect = $lstrSelect->where("a.semestercode = ?", $post['field8']);
		}

		if (isset($post['field10']) && !empty($post['field10'])) {
			$lstrSelect = $lstrSelect->where("a.IdScheme = ?", $post['field10']);
		}
		
		if (isset($post['field5']) && !empty($post['field5'])) {
			$lstrSelect = $lstrSelect->where("d.IdProgram = ?", $post['field5']);
		}


		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}


	public function fngetexamscaledata($id){
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_exam_scaling_setup'), array('a.IdExamScaling', 'a.IdScheme', 'a.IdFaculty', 'a.semestercode'))
		->joinLeft(array('b' => 'tbl_scheme'), 'a.IdScheme = b.IdScheme', array('b.EnglishDescription'))
		->joinLeft(array('c' => 'tbl_collegemaster'), 'a.IdFaculty = c.IdCollege', array('c.CollegeName'))
		->joinLeft(array('d' => 'tbl_exam_scaling_setup_detail'), 'a.IdExamScaling = d.IdExamScaling', array('d.IdComponent','d.IdCourse','d.Marks','d.Active'))
		->joinLeft(array('e' => 'tbl_subjectmaster'), 'd.IdCourse = e.IdSubject', array('e.SubjectName'))
		->joinLeft(array('f' => 'tbl_examination_assessment_type'), 'd.IdComponent = f.IdExaminationAssessmentType', array('f.Description AS DefinitionDesc'))
		->where('a.IdExamScaling =?',$id);
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}


	// function to check the setup by the combination of course,scheme and combination
	function checkscalingsetups($idscheme,$idcourse,$idcomponent){
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_exam_scaling_setup'), array(''))
		->joinLeft(array('b' => 'tbl_exam_scaling_setup_detail'), 'a.IdExamScaling = b.IdExamScaling', array('b.Marks'))
		->where('b.IdComponent =?',$idcomponent)
		->where('b.IdCourse =?',$idcourse)
		->where('a.IdScheme =?',$idscheme)
		->where('b.Active =?',1);

		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

}