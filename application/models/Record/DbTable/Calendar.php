<?php

/**
 * Calendar
 * 
 * @author Muhamad Alif Muhammad
 * @date Oct 19, 2010
 * @version 
 */

class App_Model_Record_DbTable_Calendar extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'r028_calendar';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			
			$db = Zend_Db_Table::getDefaultAdapter();
					
			$select = $db->select()
					->from(array('c'=>$this->_name))
					->where('c.'.$this->_primary .' = '. $id)
					->join(array('s'=>'r001_semester'), 's.id = c.semester_id', array('semester_name'=>'name','semester_start_date'=>'start_date','semester_end_date'=>'end_date'))
					->join(array('a'=>'r027_activity'), 'a.id = c.activity_id', array('activity_name'=>'name'));
					
				$row = $db->fetchRow($select);
		}else{
			$row = $this->fetchAll()->toArray();
		}
		
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}
		
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					->from($this->_name)
					->order($this->_primary);
		
		return $select;
	}
	
	public function addData($postData){
		$data = array(
				'activity_id' => $postData['activity_id'],
				'semester_id' => $postData['semester_id'],
				'start_date' => $postData['start_date'],
				'end_date' => $postData['end_date']
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		$data = array(
				'activity_id' => $postData['activity_id'],
				'semester_id' => $postData['semester_id'],
				'start_date' => $postData['start_date'],
				'end_date' => $postData['end_date']
				);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
	
	/* 
	 * Method: getEvent
	 * */
	
	public function getEventList(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
					->from(array('c'=>$this->_name))
					->join(array('s'=>'r001_semester'), 's.id = c.semester_id', array('semester_name'=>'name','semester_start_date'=>'start_date','semester_end_date'=>'end_date'))
					->join(array('a'=>'r027_activity'), 'a.id = c.activity_id', array('activity_name'=>'name'))
					->order(array('c.start_date ASC'));
		
		$results = $db->fetchAll($select);
		
		return $results;
	}
	
/* 
	 * Method: getEvent by Semester
	 * */
	
	public function getSemesterEventList($semester_id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		if($semester_id!=0){
			$select = $db->select()
						->from(array('c'=>$this->_name))
						->where('c.semester_id = '.$semester_id)
						->join(array('s'=>'r001_semester'), 's.id = c.semester_id', array('semester_name'=>'name','semester_start_date'=>'start_date','semester_end_date'=>'end_date'))
						->join(array('a'=>'r027_activity'), 'a.id = c.activity_id', array('activity_name'=>'name'))
						->order(array('c.start_date ASC'));
			$results = $db->fetchAll($select);
			return $results;									
		}else{
			$select = $db->select()
						->from(array('s'=>'r001_semester'))
						->order(array('s.start_date DESC'));
						
			$results = $db->fetchAll($select);
			
			//get events for each semester
			$events = array();
			$i=0;
			foreach($results as $result){
				$result['events'] = $this->getSemesterEventList($result['id']);
				$events[$i] = $result;
				$i++;
			}
			
			return $events;						
		}
	}
}

