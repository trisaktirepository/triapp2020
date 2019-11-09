<?php

class App_Model_Application_DbTable_PlacementTestRoomType extends Zend_Db_Table_Abstract {

	protected $_name = 'appl_room_type';
	protected $_primary = "art_id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('art'=>$this->_name))
					->where('art.art_id = '.$id);
							
			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('art'=>$this->_name));
								
			$row = $db->fetchAll($select);
		}
		
		return $row;
		
	}
	
	public function getRoomType($room_id=0){
		$room_id = (int)$room_id;
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('art'=>$this->_name))
				->joinLeft(array('artt'=>'appl_room_test_type'),'artt.artt_id = art.art_test_type', array('test_name'=>'artt_name'))
				->where('art.art_room_id = '.$room_id);
		
		$row = $db->fetchAll($select);
	
		if(!$row){
			return null;
		}else{
			return $row;
		}	
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('artt'=>$this->_name))
             		    ->order($this->_primary.' ASC');
						
		return $selectData;
	}

	public function addData($postData){
		
		$data = array(
			'art_room_id'=>$postData['art_room_id'],
			'art_test_type'=>$postData['art_test_type']
		);
			
		return $this->insert($data);
	}
	
	public function deleteRoomData($room_id){
		if($room_id!=0){
			$this->delete('art_room_id = '. (int)$room_id);
		}
	}
}

