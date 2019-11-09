<?php

/**
Program Requirement
 */

class App_Model_Record_DbTable_ProgramRequirementDetail extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'r008_program_requirement_detail';
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
	        $row =  $row->toArray();
		}
		
		if(!$row){
			throw new Exception("There is No Program Requirement Detail Data");
		}
		
		return $row;
	}
	
	public function getCourseRequirement($program_ID=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$program_ID = (int)$program_ID;
		
		if($program_ID!=0){

	        $select = $db->select()
	                 ->from(array('req'=>$this->_name))
	                 ->where('req.program_id = ' .$program_ID)
	                 ->join(array('type'=>'r009_course_type'), 'req.course_type_id = type.id', array('course_type_name'=>'name'));
            			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
        
		}else{
			throw new Exception("There is No given Program");
		}
		
		return $row;
	}

	public function addData($data){
		
		$data = array(
				'program_id' => $data['program_id'],
				'course_type_id' => $data['course_type_id'],
				'credit_hour' => $data['credit_hour']			
				);
		
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		$data = array(
				'program_id' => $data['program_id'],
				'course_type_id' => $data['course_type_id'],
				'credit_hour' => $data['credit_hour']			
				);
	
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
}