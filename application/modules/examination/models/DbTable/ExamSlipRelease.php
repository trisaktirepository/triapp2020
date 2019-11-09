<?php 

class Examination_Model_DbTable_ExamSlipRelease extends Zend_Db_Table_Abstract {
	
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
	
	public function getReleaseData($semesterId,$assessmentTypeId){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('esr'=>$this->_name))
		->where('esr_semester_id = ?', $semesterId)
		->where('esr_assessment_type_id = ?', $assessmentTypeId);
	
		$row = $db->fetchRow($select);
	
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
	
		
	public function insert($data=array()){
	
		if( !isset($data['esr_last_edit_by']) ){
			$auth = $auth = Zend_Auth::getInstance();
				
			$data['esr_last_edit_by'] = $auth->getIdentity()->iduser;
		}
	
		if( !isset($data['esr_last_edit_date']) ){
			$data['esr_last_edit_date'] = date('Y-m-d H:i:a');
		}
	
		return parent::insert($data);
	}
	
	public function update($data=array(),$where){
		if( !isset($data['esr_last_edit_by']) ){
			$auth = $auth = Zend_Auth::getInstance();
		
			$data['esr_last_edit_by'] = $auth->getIdentity()->iduser;
		}
		
		if( !isset($data['esr_last_edit_date']) ){
			$data['esr_last_edit_date'] = date('Y-m-d H:i:a');
		}
		
		return parent::update($data,$where);
	}
	
}