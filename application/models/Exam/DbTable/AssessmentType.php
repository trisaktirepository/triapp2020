<?php

class App_Model_Exam_DbTable_AssessmentType extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_examination_assessment_type';
	protected $_primary = "IdExaminationAssessmentType";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		
		if(!$row){
			throw new Exception("There is No Assessment Type");
		}			
		return $row->toArray();
	}
	
	
	
}
?>