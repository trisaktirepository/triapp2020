<?php
class App_Model_Exam_DbTable_Markadjustment extends Zend_Db_Table_Abstract {

	protected $_name = 'e005_mark_adjustment';
	protected $_primary = "id";
	
    public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Information found.");
		}			
		return $row->toArray();
	}
	
   public function getPaginateData($program_id,$semester_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					 ->from($this->_name);
					 
		if ($program_id)$select->where('program_id = ?',$program_id);  
		if ($semester_id) $select->where('semester_id= ?',$semester_id);   
					    $select->order('id');		 
		
		
		return $select;
	}
	
	
    public function addData($postData){
		$data = array(
		        'program_id'   => $postData['program_id'],
		        'semester_id'  => $postData['semester_id'],				
				'min_mark'     => $postData['min_mark'],
				'max_mark'     => $postData['max_mark'],
				'value'		   => $postData['value']
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		$data = array(
		        'program_id'   => $postData['program_id'],
		        'semester_id'  => $postData['semester_id'],				
				'min_mark'     => $postData['min_mark'],
				'max_mark'     => $postData['max_mark'],
				'value'		   => $postData['value']
				);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
}