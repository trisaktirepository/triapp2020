<?php 

class App_Model_Application_DbTable_ApplicantAssessmentUsmDetl extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'applicant_assessment_usm_detl';
	protected $_primary = "aaud_id";
	
	public function addData($data){		
	   $id =  $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}

	
}
?>