<?php 

class Examination_Model_DbTable_ExamGroupAttendance extends Zend_Db_Table_Abstract {
	
	protected $_name = 'exam_group_attendance';
	protected $_primary = "ega_id";
	
	public function getData($group_id, $student_id, $student_nim=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
			->from(array('ega'=>$this->_name))
			->where('ega.ega_eg_id = ?',$group_id)
			->where('ega.ega_student_id =?', $student_id);
		
		if($student_nim){
			$select->where('ega.ega_student_nim =?',$student_nim);
		}
		
		$row = $db->fetchRow($select);
		
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
	public function getGroupData($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
					->from(array('ega'=>$this->_name))
					->where('ega.eg_id = ?',$idGroup);
		
		$row = $db->fetchAll($select);
		
		if($row){
			return $row;
		}else{
			return null;
		}
		
	}
	
	public function getExamAttendaceStatus($idsemester,$idSubject,$type,$idstudent){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('ega'=>$this->_name))
		->join(array('eg'=>'exam_group'),'ega.ega_eg_id=eg.eg_id',array())
		->where('eg.eg_sem_id = ?',$idsemester)
		->where('ega.ega_student_id=?',$idstudent)
		->where('eg.eg_assessment_type=?',$type)
		->where('eg.eg_sub_id = ?',$idSubject);
	
		$row = $db->fetchRow($select);
		
		return $row;
		 
	
	}	
	
	public function getExamGroupAttendanceAll($idsemester,$idprogram,$type){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('ega'=>$this->_name),array())
		->join(array('eg'=>'exam_group'),'ega.ega_eg_id=eg.eg_id')
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=eg.eg_sub_id',array('subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage','faculty_id'=>'IdFaculty'))
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=eg.eg_sem_id',array('semester_name'=>'SemesterMainName'))
		->joinLeft(array('r'=>'appl_room'),'r.av_id=eg.eg_room_id')
		->join(array('eat'=>'tbl_examination_assessment_type'), 'eat.IdExaminationAssessmentType = eg.eg_assessment_type', array('eg_exam_name'=>'DescriptionDefaultlang'))
		->join(array('ep'=>'exam_group_program'),'eg.eg_id=ep.egp_eg_id',array())
		->join(array('p'=>'tbl_program'),'p.IdProgram=ep.egp_program_id',array())
		->where('eg.eg_sem_id = ?',$idsemester) 
		->where('eg.eg_assessment_type=?',$type)
		->where('p.IdProgram=?',$idprogram)
		->group('eg.eg_id');
	
		$row = $db->fetchAll($select);
	
		return $row;
			
	
	}
	
	public function getExamGroupAttendanceByTime($idsemester,$idprogram,$type,$dt,$dtstop,$stime,$entime){
	
		if ($dtstop < $dt) $dtstop=$dt;
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('ega'=>$this->_name),array())
		->join(array('eg'=>'exam_group'),'ega.ega_eg_id=eg.eg_id')
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=eg.eg_sub_id',array('subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage','faculty_id'=>'IdFaculty'))
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=eg.eg_sem_id',array('semester_name'=>'SemesterMainName'))
		->joinLeft(array('r'=>'appl_room'),'r.av_id=eg.eg_room_id')
		->join(array('eat'=>'tbl_examination_assessment_type'), 'eat.IdExaminationAssessmentType = eg.eg_assessment_type', array('eg_exam_name'=>'DescriptionDefaultlang'))
		->join(array('ep'=>'exam_group_program'),'eg.eg_id=ep.egp_eg_id',array())
		->join(array('p'=>'tbl_program'),'p.IdProgram=ep.egp_program_id',array())
		->where('eg.eg_sem_id = ?',$idsemester)
		->where('eg.eg_assessment_type=?',$type)
		->where('p.IdProgram=?',$idprogram)
		->where('eg.eg_date>=?',date('Y-m-d',strtotime($dt)))
		->where('eg.eg_date<=?',date('Y-m-d',strtotime($dtstop)))
		->where('eg.eg_start_time >=?',$stime)
		->where('eg.eg_end_time <=?',$entime)
		->group('eg.eg_id');
	 
		$row = $db->fetchAll($select);
	
		
		return $row;
			
	
	}
	public function insert($data=array()){
	
		if( !isset($data['ega_last_edit_by']) ){
			$auth = $auth = Zend_Auth::getInstance();
				
			$data['ega_last_edit_by'] = $auth->getIdentity()->iduser;
		}
	
		if( !isset($data['ega_last_edit_date']) ){
			$data['ega_last_edit_date'] = date('Y-m-d H:i:a');
		}
	
		return parent::insert($data);
	}
	
	public function update($data=array(),$where){
		if( !isset($data['ega_last_edit_by']) ){
			$auth = $auth = Zend_Auth::getInstance();
		
			$data['ega_last_edit_by'] = $auth->getIdentity()->iduser;
		}
		
		if( !isset($data['ega_last_edit_date']) ){
			$data['ega_last_edit_date'] = date('Y-m-d H:i:a');
		}
		
		return parent::update($data,$where);
	}
	
}