<?php


class App_Model_Schedule_DbTable_ScheduleVenue extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 's002_schedule_venue';
	protected $_primary = "id";
	
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select  = $db->select()
	                    ->from(array('sv'=>$this->_name))
	                    ->where("sv.".$this->_primary.' = ?', $id)
	                    ->joinLeft(array('vd'=>'g012_venue_detail'), 'vd.id = sv.venue_id', array('venue_name'=>'vd.name'));
	                   
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
	                    ->from(array('sv'=>$this->_name))
	                    ->joinLeft(array('vd'=>'g012_venue_detail'), 'vd.id = sv.venue_id', array('venue_name'=>'vd.name'));
		
		return $select;
	}
	
	public function addData($postData){
		$data = array(
				'schedule_id' => $postData['schedule_id'],
				'venue_id' => $postData['venue_id']				
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		$data = array(
				'schedule_id' => $postData['schedule_id'],
				'venue_id' => $postData['venue_id']				
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
	                    ->from(array('sv'=>$this->_name))
	                    ->where("sv.schedule_id = ?", $id)
	                    ->joinLeft(array('vd'=>'g012_venue_detail'), 'vd.id = sv.venue_id', array('venue_name'=>'vd.name', 'venue_capacity'=>'vd.capacity'));
	                   
			$row = $db->fetchAll($select);
		}
		
		return $row;
	}
	
	public function getScheduleData2($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select  = $db->select()
	                    ->from(array('sv'=>$this->_name))
	                    ->where("sv.schedule_id = ?", $id)
	                    ->joinLeft(array('vd'=>'g012_venue_detail'), 'vd.id = sv.venue_id', array('venue_name'=>'vd.name', 'venue_capacity'=>'vd.capacity'))
	                    ->joinLeft(array('ss'=>'s001_schedule'), 'ss.id = sv.schedule_id', array('exam_center'=>'ss.exam_center'));
	                   
			$row = $db->fetchAll($select);
		}
		
		return $row;
	}
}

