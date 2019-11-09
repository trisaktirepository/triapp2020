<?php 

class Exam_Model_DbTable_ExamGroupStudent extends Zend_Db_Table_Abstract {
	
	protected $_name = 'exam_group_student';
	protected $_primary = "egst_id";
	

	public function getData($id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('egst'=>$this->_name))
		->where('egst.egst_id = ?',$id);
		
		$row = $db->fetchRow($select);
		
		return $row;
	}
	
	public function getStudentList($group_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('egst'=>$this->_name))
					  ->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=egst.egst_student_id', array('transaction_id'=>'transaction_id','appl_id'=>'IdApplication','IdStudentRegistration'))
					  ->join(array('tp'=>'tbl_program'),'sr.idProgram=tp.idProgram',array('ProgramName'=>'ArabicName', 'ProgramCode'=>'ProgramCode'))
					  ->joinLeft(array('ap'=>'student_profile'),'ap.appl_id=sr.IdApplication',array('appl_fname','appl_mname','appl_lname'))
					  ->where('egst.egst_group_id = ?',$group_id);
							  
		$row = $db->fetchAll($select);
		return $row;
	}
	
	public function getStudentListAttendance($group_id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('egst'=>$this->_name))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=egst.egst_student_id', array('transaction_id'=>'transaction_id','appl_id'=>'IdApplication'))
		->join(array('tp'=>'tbl_program'),'sr.idProgram=tp.idProgram',array('ProgramName'=>'ArabicName', 'ProgramCode'=>'ProgramCode'))
		->joinLeft(array('ap'=>'student_profile'),'ap.appl_id=sr.IdApplication',array('appl_fname','appl_mname','appl_lname'))
		->joinLeft(array('ega'=>'exam_group_attendance'), 'ega.ega_student_id = egst.egst_student_id and ega.ega_eg_id = '.$group_id, array('att_status'=>'ega_status'))
		->where('egst.egst_group_id = ?',$group_id);
			
		$row = $db->fetchAll($select);
	
		return $row;
	}
	
	public function getTotalStudentAssigned($group_id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
					->from($this->_name)
					->where('egst_group_id = ?',$group_id);
		
		$row = $db->fetchAll($select);
			
		if($row)
			return count($row);
		else
			return 0;
	}
	
	public function checkStudentGroup($IdStudentRegistration,$subject_id,$semester_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		 $select = $db ->select()
					  ->from(array('egst'=>$this->_name))
					  ->join(array('eg'=>'exam_group'),'eg.eg_id=egst.egst_group_id',array('eg_group_name'))
					  ->where('egst.egst_student_id = ?',$IdStudentRegistration)
					  ->where('egst.egst_subject_id = ?',$subject_id)
					  ->where('egst.egst_semester_id = ?',$semester_id);
							  
		$row = $db->fetchRow($select);
		
		return $row;
	}
	
	
	public function getAssessment($subject_id,$semester_id,$idgroup){
			 
		 $db = Zend_Db_Table::getDefaultAdapter();	

		 $select_student = $db ->select()
					           ->from(array('cgsm'=>'tbl_course_group_student_mapping'),array('IdStudent'))
					           ->where('`IdCourseTaggingGroup` = ?',$idgroup);
		
		 $select = $db ->select()
					  ->from(array('eg'=>'exam_group'),array(''))
					  ->join(array('egst'=>$this->_name ),'eg.eg_id=egst.egst_group_id',array())
					  ->join(array('eat'=>'tbl_examination_assessment_type' ),'eat.IdExaminationAssessmentType = eg.eg_assessment_type',array('assessment_id'=>'IdExaminationAssessmentType','assessment_name'=>'DescriptionDefaultlang'))
					  ->where('egst.`egst_subject_id` = ?',$subject_id)
					  ->where('eg.`eg_sem_id` = ?',$semester_id)
					  ->where('egst.`egst_student_id` IN (?)',$select_student)
					  ->group('eg.eg_assessment_type');
							  
		$rows = $db->fetchAll($select);
		
		return $rows;
	}
	
	
	public function getExamGroup($subject_id,$semester_id,$idgroup,$idAssessment){
		
		
		 $db = Zend_Db_Table::getDefaultAdapter();	

		 $select_student = $db ->select()
					           ->from(array('cgsm'=>'tbl_course_group_student_mapping'),array('IdStudent'))
					           ->where('IdCourseTaggingGroup = ?',$idgroup);
		
		 $select = $db ->select()
					  ->from(array('eg'=>'exam_group'),array('eg_id','eg_group_name'))
					  ->join(array('egst'=>$this->_name ),'eg.eg_id=egst.egst_group_id',array())
					  ->join(array('eat'=>'tbl_examination_assessment_type' ),'eat.IdExaminationAssessmentType = eg.eg_assessment_type',array())
					  ->where('egst.`egst_subject_id` = ?',$subject_id)
					  ->where('eg.`eg_sem_id` = ?',$semester_id)
					  ->where('eg.eg_assessment_type = ?',$idAssessment)
					  ->where('egst.`egst_student_id` IN (?)',$select_student)
					  ->group('eg.eg_id');
							  
		$rows = $db->fetchAll($select);
		
		return $rows;
	}
	
	public function getExamGroupSchedule($studentId, $semesterId, $subjectId, $assessmentType=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
			->from(array('egs'=>'exam_group_student'))
			->join(array('eg'=>'exam_group'), 'eg.eg_id = egs.egst_group_id')
			->join(array('v'=>'appl_room'), 'v.av_id = eg.eg_room_id')
			->where('egs.egst_student_id = ?',$studentId)
			->where('egs.egst_semester_id = ?', $semesterId)
			->where('egs.egst_subject_id = ?',$subjectId);
		
		if($assessmentType){
			$select->where('eg.eg_assessment_type = ?', $assessmentType);
		}
			
		$rows = $db->fetchAll($select);
		
		return $rows;
		
	}
}