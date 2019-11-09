<?php 

class App_Model_Exam_DbTable_ExamSlipRelease extends Zend_Db_Table_Abstract {
	
	protected $_name = 'exam_slip_release';
	protected $_primary = "esr_id";
	
	public function getData($id=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
			->from(array('esr'=>$this->_name));
		
		if($id){
			$select->where('ega.ega_student_nim =?',$student_nim);
			$row = $db->fetchRow($select);
		}else{
			$row = $db->fetchAll($select);
		}
		
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
	public function getReleaseData($semesterId,$assessmentTypeId=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
					->from(array('esr'=>$this->_name))
					->join(array('ass'=>'tbl_examination_assessment_type'), 'ass.IdExaminationAssessmentType = esr.esr_assessment_type_id')
					->where('esr_semester_id = ?', $semesterId);
		
		if($assessmentTypeId){
			$select->where('esr_assessment_type_id = ?', $assessmentTypeId);
			
			$row = $db->fetchRow($select);
		}else{
			$row = $db->fetchAll($select);
		}
	
		
	
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
	public function getStudentExamSlipReleseData($studentId,$semesterId){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		//student assessment type from exam group
		$sql = $db ->select()
		->from(array('egs'=>'exam_group_student'), array())
		->join(array('eg'=>'exam_group'),'eg.eg_id = egs.egst_group_id', 'DISTINCT(eg.eg_assessment_type)')
		->where('egs.egst_semester_id=?',$semesterId)
		->where('egs.egst_student_id = ?',$studentId);
		
		
		$select = $db ->select()
		->from(array('esr'=>$this->_name))
		->join(array('ass'=>'tbl_examination_assessment_type'), 'ass.IdExaminationAssessmentType = esr.esr_assessment_type_id')
		->where('esr_semester_id = ?', $semesterId)
		->where('esr_assessment_type_id in (?)',$sql);
	
		$row = $db->fetchAll($select);
		
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
	public function getStudentExamSlipReleseDataPerYear($studentId,$year){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		//student assessment type from exam group
		$sql = $db ->select()
		->from(array('egs'=>'exam_group_student'), array())
		->join(array('eg'=>'exam_group'),'eg.eg_id = egs.egst_group_id', 'DISTINCT(eg.eg_assessment_type)')
		->join(array("sm"=>'tbl_semestermaster'),'sm.IdSemesterMaster=eg.eg_sem_id',array())
		->where('sm.idacadyear=?',$year)
		->where('egs.egst_student_id = ?',$studentId);
		//echo $year;exit;
	
		$select = $db ->select()
		->from(array('esr'=>$this->_name))
		->join(array('ass'=>'tbl_examination_assessment_type'), 'ass.IdExaminationAssessmentType = esr.esr_assessment_type_id')
		->join(array("sm"=>'tbl_semestermaster'),'sm.IdSemesterMaster=esr.esr_semester_id')
		->where('sm.idacadyear=?',$year)
		->where('esr_assessment_type_id in (?)',$sql);
	
		$row = $db->fetchAll($select);
	
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
	public function getAssessmentStatus($semesterId,$assessmentTypeId){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from(array('esr'=>$this->_name),array('esr_status'))
						->where('esr_semester_id = ?', $semesterId)
						->where('esr_assessment_type_id = ?', $assessmentTypeId);
		
		$row = $db->fetchRow($select);
		
		if($row){
			return $row['esr_status'];
		}else{
			return null;
		}
	}
	
	public function idSlipOpenToDownload($idProgram,$idSemester,$type=null) {
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select 	= $db->select()
		->from(array('pm'=>'tbl_publish_mark'))
		->joinLeft(array('p'=>'tbl_program'),'p.IdProgram=pm.pm_idProgram',array('ProgramCode','ProgamName'=>'ArabicName'))
		->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=pm.pm_idSemester',array('SemesterName'=>'SemesterMainName'))
		->join(array('a' => 'tbl_examination_assessment_type'),'a.IdExaminationAssessmentType=pm.pm_type',array('value' => 'a.Description'))
		->where('pm_idSemester = ?',$idSemester)
		->where('pm_date<=CURDATE()');
		if ($type!=null) $select->where('pm_type = ?',$type);
		if(isset($idProgram) && $idProgram!=''){
			$select->where('pm_idProgram = ?',$idProgram);
		}
		$result = $db->fetchRow($select);
		if ($result) return true; else return false;
		
	}
	
}