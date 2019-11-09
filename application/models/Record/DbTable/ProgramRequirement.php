<?php

/**
Program Requirement
 */

class App_Model_Record_DbTable_ProgramRequirement extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'r007_program_requirement';
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
			throw new Exception("There is No Program Requirement Data");
		}
		
		return $row;
	}
	
	public function getCourseRequirement($programID=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$programID = (int)$programID;
		
		if($programID!=0){

	        $select = $db->select()
	                 ->from($this->_name)
	                 ->where('program_id = ' .$programID);
			            			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
        
		}else{
			throw new Exception("There is No given Program");
		}
		
		if(!$row){
			return false;
		}else{
			return $row;
		}
	}

	public function addData($data){
		
		$data = array(
				'program_id' => $data['program_id'],
				'max_sem_credit' => $data['max_sem_credit'],
				'last_change' => date('Y-m-d H:i:s'),
				'update_by' => $data['update_by']			
				);
		
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		$data = array(
				'program_id' => $data['program_id'],
				'max_sem_credit' => $data['max_sem_credit'],
				'last_change' => date('Y-m-d H:i:s'),
				'update_by' => $data['update_by']			
				);
		
		$this->update($data, 'program_id = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
	
	
	
	public function getProgramRequirement($programID){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$programID = (int)$programID;
		
		if($programID!=0){

	       $select = $db->select()	       
	                 ->from(array('pr'=>$this->_name),array('max_sem_credit'=>'pr.max_sem_credit'))
	                 ->join(array('prd'=>'r008_program_requirement_detail'),'prd.program_id = pr.program_id',array('course_type_id'=>'prd.course_type_id','credit_hour'=>'prd.credit_hour'))
	                 ->joinLeft(array('ct' => 'r009_course_type'),'ct.id=prd.course_type_id',array('course_type_id'=>'id','course_type_name'=>'name'))
	                 ->where('pr.program_id = ' .$programID);
	                 
			            			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
        
		}else{
			throw new Exception("There is No given Program");
		}
		
		if(!$row){
			return false;
		}else{
			return $row;
		}
		

	}
}