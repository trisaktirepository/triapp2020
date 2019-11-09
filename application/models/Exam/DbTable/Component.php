<?php
class App_Model_Exam_DbTable_Component extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'e014_assessment_component';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		
		if(!$row){
			throw new Exception("There is No Assessment Component");
		}			
		return $row->toArray();
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                 ->from($this->_name)
	                 ->where('parent_id= 0');
		
		return $select;
	}
	
	public function getInfo($parent_id){
		
		$select = $this->select()
	                 ->from($this->_name)
	                 ->where('parent_id = ?',$parent_id);
		
	     $row = $this->fetchAll($select);
		return $row->toArray();
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
	
	public function deleteItem($id){		
	  $this->delete('parent_id =' . (int)$id);
	}
}
?>