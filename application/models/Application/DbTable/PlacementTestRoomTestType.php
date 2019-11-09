<?php

class App_Model_Application_DbTable_PlacementTestRoomTestType extends Zend_Db_Table_Abstract {

	protected $_name = 'appl_room_test_type';
	protected $_primary = "artt_id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('artt'=>$this->_name))
					->where('artt.artt_id = '.$id);
							
			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('artt'=>$this->_name));
								
			$row = $db->fetchAll($select);
		}
		
		return $row;
		
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('artt'=>$this->_name))
             		    ->order($this->_primary.' ASC');
						
		return $selectData;
	}

}

