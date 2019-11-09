<?php 
class App_Model_Record_DbTable_ApplicationPtestProgram extends Zend_Db_Table_Abstract
{
    protected $_name = 'applicant_ptest_program';
	protected $_primary = "app_id";
	
	
	public function getPlacementProgram($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		    $select = $db ->select()
						->from(array('app'=>$this->_name));
					

			//echo $select;
									
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		
	}
	
	
	public function addData($data){		
	   $id = $this->insert($data);
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