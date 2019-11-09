<?php 
class App_Model_Application_DbTable_ApplicantSelectionDetl extends Zend_Db_Table_Abstract
{
    protected $_name = 'applicant_selection_detl';
	protected $_primary = "asd_id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		    $select = $db ->select()
						->from($this->_name)
						->where($this->_primary .' = '.$id);

			//echo $select;
									
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		    return $row;
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