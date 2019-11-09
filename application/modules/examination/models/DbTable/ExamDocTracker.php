<?php 
class Examination_Model_DbTable_ExamDocTracker extends Zend_Db_Table_Abstract {
	
	protected $_name = 'exam_document_tracker';
	protected $_primary = "idDocTracker";
	

	public function getData($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('ed'=>$this->_name))
		->join(array('eg'=>'exam_group'),'eg.eg_id=ed.idExamGroup')
		->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=eg.eg_sub_id',array('subject_code'=>'SubCode','subject_name'=>'BahasaIndonesia','faculty_id'=>'IdFaculty'))
		->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=eg.eg_sem_id',array('semester_name'=>'SemesterMainName'))
		->joinLeft(array('r'=>'appl_room'),'r.av_id=eg.eg_room_id')
		->join(array('eat'=>'tbl_examination_assessment_type'), 'eat.IdExaminationAssessmentType = eg.eg_assessment_type', array('eg_exam_name'=>'DescriptionDefaultlang'))
		->where('ed.idExamGroup = ?',$idGroup);
		$row = $db->fetchRow($select);
		
		return $row;
	}
	public function insertData($data){
	
		//$db = Zend_Db_Table::getDefaultAdapter();
		$data['dt_send_toStaff']=date ('Y-m-d:mm:ss');
		$select = $this->insert($data);
		return $select;
	}
	public function updateData($data,$id){
	
		//$db = Zend_Db_Table::getDefaultAdapter();
		$data['dt_received_byProgram']=date('Y-m-d:mm:ss');
		$select = $this->update($data,$id);
		return $select;
	}
	
}
?>