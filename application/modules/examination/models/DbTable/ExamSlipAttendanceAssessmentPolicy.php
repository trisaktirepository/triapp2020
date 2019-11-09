<?php 

class Examination_Model_DbTable_ExamSlipAttendanceAssessmentPolicy extends Zend_Db_Table_Abstract {
	
	protected $_name = 'exam_slip_attendance_assessment_policy';
	protected $_primary = "esaap_id";
	
	public function getData($id=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
			->from(array('esaap'=>$this->_name));
		
		if($id){
			$select->where('esaap.esaap_id =?',$id);
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
	
	public function getDataByProgram($program_id){
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
					  ->from(array('esaap'=>$this->_name))
					  ->join(array('ass'=>'tbl_examination_assessment_type'), 'ass.IdExaminationAssessmentType = esaap.esaap_assessment_type_id')
				      ->where('esaap.esaap_program_id =?',$program_id);
	
		$row = $db->fetchAll($select);
	
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
	public function getAssessmentNotAssigened($program_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$sel = $db ->select()
					->from(array('esaap'=>$this->_name),array('esaap_assessment_type_id'))
					->where('esaap.esaap_program_id =?',$program_id);
		
		$select = $db ->select()
		->from(array('ass'=>'tbl_examination_assessment_type'))
		->where('ass.IdExaminationAssessmentType not in ('.$sel.')');
		
		$row = $db->fetchAll($select);
		
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
}