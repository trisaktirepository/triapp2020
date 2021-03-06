<?php

/**
Program Requirement
 */

class App_Model_Record_DbTable_CourseType extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'r009_course_type';
	protected $_primary = "id";
	
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from($this->_name)
	                 ->where($this->_primary.' = ' .$id);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
        
		}else{
			$select = $db->select()
	                 ->from($this->_name);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}

	public function addData($data){
		
		$data = array(
				'name' => $data['name']			
				);
		
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		$data = array(
				'name' => $data['name']			
				);
		
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
}