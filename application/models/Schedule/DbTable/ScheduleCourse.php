<?php


class App_Model_Schedule_DbTable_ScheduleCourse extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 's003_schedule_course';
	protected $_primary = "id";
	
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select  = $db->select()
	                    ->from(array('sc'=>$this->_name))
	                    ->where("sc.".$this->_primary.' = ?', $id);
	                   
			$row = $db->fetchRow($select);
		}else{
			$row = $this->fetchAll();
			$row = $row->toArray();
		}
		
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select  = $db->select()
	                    ->from(array('sc'=>$this->_name));
		
		return $select;
	}
	
	public function addData($postData){
		$data = array(
				'schedule_id' => $postData['schedule_id'],
				'course_id' => $postData['course_id']				
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		$data = array(
				'schedule_id' => $postData['schedule_id'],
				'course_id' => $postData['course_id']				
				);
		
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	public function deleteScheduleData($id){
		$this->delete("schedule_id = " . (int)$id);
	}

	public function getScheduleData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select  = $db->select()
	                    ->from(array('sc'=>$this->_name))
	                    ->where("sc.schedule_id = ?", $id)
	                    ->joinLeft(array('c'=>'r010_course'), 'c.id = sc.course_id', array('course_name'=>'c.name'));
	                   
			$row = $db->fetchAll($select);
		}
		
		return $row;
	}
	
	
}

