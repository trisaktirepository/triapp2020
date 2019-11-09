<?php 

class Exam_Model_DbTable_ExamSlipAttendancePolicy extends Zend_Db_Table_Abstract {
	
	protected $_name = 'exam_slip_attendance_policy';
	protected $_primary = "esap_id";
	
	public function getData($id=null){
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('esap'=>$this->_name));
	
		if($id){
			$select->where('esap.esap_id =?',$id);
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
	
	public function getDataByProgram($program_id,$assessmentTypeId=null){
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('esap'=>$this->_name))
		->where('esap.esap_program_id =?',$program_id);
		
		if($assessmentTypeId){
			$select->join(array('esaap'=>'exam_slip_attendance_assessment_policy'),'esaap.esaap_assessment_type_id = '.$assessmentTypeId.' and esaap.esaap_program_id = '.$program_id);
		}
	
		$row = $db->fetchRow($select);
	
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
}