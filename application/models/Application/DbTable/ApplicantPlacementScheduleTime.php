<?php 
class App_Model_Application_DbTable_ApplicantPlacementScheduleTime extends Zend_Db_Table_Abstract
{
    protected $_name = 'appl_placement_schedule_time';
	protected $_primary = "apst_id";
	

	public function addData($data){
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){		
		$this->delete($this->_primary .' = ' . (int)$id);
	}
		
}
?>