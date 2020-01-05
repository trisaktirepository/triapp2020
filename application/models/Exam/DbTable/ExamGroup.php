<?php 

class App_Model_Exam_DbTable_ExamGroup extends Zend_Db_Table_Abstract {
	
	protected $_name = 'exam_group';
	protected $_primary = "eg_id";
	

	public function getData($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('eg'=>$this->_name))
		->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=eg.eg_sub_id',array('subject_code'=>'SubCode','subject_name'=>'BahasaIndonesia','faculty_id'=>'IdFaculty'))
		->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=eg.eg_sem_id',array('semester_name'=>'SemesterMainName'))
		->joinLeft(array('r'=>'appl_room'),'r.av_id=eg.eg_room_id')
		->join(array('eat'=>'tbl_examination_assessment_type'), 'eat.IdExaminationAssessmentType = eg.eg_assessment_type', array('eg_exam_name'=>'DescriptionDefaultlang'))
		->where('eg.eg_id = ?',$idGroup);
		$row = $db->fetchRow($select);
		
		return $row;
	}
	public function getDataBySubject($semester,$subject,$program){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('eg'=>$this->_name))
		->join(array('egp'=>'exam_group_program'),'eg.eg_id=egp.egp_eg_id')
		->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=eg.eg_sub_id',array('subject_code'=>'SubCode','subject_name'=>'BahasaIndonesia','faculty_id'=>'IdFaculty'))
		->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=eg.eg_sem_id',array('semester_name'=>'SemesterMainName'))
		//->joinLeft(array('r'=>'appl_room'),'r.av_id=eg.eg_room_id')
		//->join(array('eat'=>'tbl_examination_assessment_type'), 'eat.IdExaminationAssessmentType = eg.eg_assessment_type', array('eg_exam_name'=>'DescriptionDefaultlang'))
		->where('eg.eg_sem_id = ?',$semester)
		->where('egp.egp_program_id = ?',$program)
		->where('eg.eg_sub_id =?',$subject);
		
		$row = $db->fetchRow($select);
	
		return $row;
	}
	
	public function getDataByExamtype($semester,$subject,$program,$examtype){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('eg'=>$this->_name))
		->join(array('egp'=>'exam_group_program'),'eg.eg_id=egp.egp_eg_id')
		->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=eg.eg_sub_id',array('subject_code'=>'SubCode','subject_name'=>'BahasaIndonesia','faculty_id'=>'IdFaculty'))
		->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=eg.eg_sem_id',array('semester_name'=>'SemesterMainName'))
		//->joinLeft(array('r'=>'appl_room'),'r.av_id=eg.eg_room_id')
		//->join(array('eat'=>'tbl_examination_assessment_type'), 'eat.IdExaminationAssessmentType = eg.eg_assessment_type', array('eg_exam_name'=>'DescriptionDefaultlang'))
		->where('eg.eg_sem_id = ?',$semester)
		->where('egp.egp_program_id = ?',$program)
		->where('eg.eg_sub_id =?',$subject)
		->where('eg.eg_assessment_type=?',$examtype);
	
		$row = $db->fetchRow($select);
	
		return $row;
	}
	public function getDataBySubjectAll($semester,$subject,$program){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->distinct()
		->from(array('eg'=>$this->_name),array('eg_date','eg_start_time','eg_end_time'))
		->join(array('egp'=>'exam_group_program'),'eg.eg_id=egp.egp_eg_id',array())
		->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=eg.eg_sub_id',array('subject_code'=>'SubCode','subject_name'=>'BahasaIndonesia','faculty_id'=>'IdFaculty'))
		->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=eg.eg_sem_id',array('semester_name'=>'SemesterMainName'))
		//->joinLeft(array('r'=>'appl_room'),'r.av_id=eg.eg_room_id'
		//->join(array('eat'=>'tbl_examination_assessment_type'), 'eat.IdExaminationAssessmentType = eg.eg_assessment_type', array('eg_exam_name'=>'DescriptionDefaultlang'))
		->where('eg.eg_sem_id = ?',$semester)
		->where('egp.egp_program_id = ?',$program)
		->where('eg.eg_sub_id =?',$subject);
	
		$row = $db->fetchAll($select);
	
		return $row;
	}
	public function getGroupList($idSubject,$idSemester,$idprogram=null){
		
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('eg'=>$this->_name))
					  ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=eg.eg_sub_id',array('subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage','faculty_id'=>'IdFaculty'))
					  ->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=eg.eg_sem_id',array('semester_name'=>'SemesterMainName'))
					  ->joinLeft(array('r'=>'appl_room'),'r.av_id=eg.eg_room_id')
					  ->joinLeft(array('ep'=>'exam_group_program'),'eg.eg_id=ep.egp_eg_id')
					  ->joinLeft(array('p'=>'tbl_program'),'p.IdProgram=ep.egp_program_id')
					  ->join(array('eat'=>'tbl_examination_assessment_type'), 'eat.IdExaminationAssessmentType = eg.eg_assessment_type', array('eg_exam_name'=>'DescriptionDefaultlang'))
					  ->where('eg.eg_sub_id = ?',$idSubject)
					  ->where('eg.eg_sem_id = ?',$idSemester);
					  
			if($session->IdRole == 605 || $session->IdRole == 311 || $session->IdRole == 298){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
				$select->where("p.IdCollege='".$session->idCollege."' or p.IdCollege is null");
			}
			if($session->IdRole == 470 || $session->IdRole == 480){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
				$select->where("p.IdProgram='".$session->idDepartment."' or ep.egp_program_id is null");
			}	  
		 
			$row = $db->fetchAll($select);
		 	
		 return $row;
	}
	
	public function getTotalGroupByCourse($idCourse,$idSemester){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("IdSubject = ?",$idCourse)
					  ->where('IdSemester = ?',$idSemester);					  
		 $row = $db->fetchAll($select);	
		 
		 if($row)
		 	return count($row);
		 else
		 return 0;
	}
	
	public function insert($data=array()){
	
		if( !isset($data['eg_create_by']) ){
			$auth = $auth = Zend_Auth::getInstance();
				
			$data['eg_create_by'] = $auth->getIdentity()->iduser;
		}
	
		if( !isset($data['eg_create_date']) ){
			$data['eg_create_date'] = date('Y-m-d H:i:a');
		}
	
		return parent::insert($data);
	}
	
	public function isExamAvailable($semester,$program){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('eg'=>$this->_name))
		->join(array('egp'=>'exam_group_program'),'eg.eg_id=egp.egp_eg_id')
		->where('eg.eg_sem_id = ?',$semester)
		->where('egp.egp_program_id = ?',$program); 
	
		$row = $db->fetchRow($select);
	
		if ($row) return true; else return false;
	}
	
}