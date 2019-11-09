<?php

class App_Model_Application_DbTable_PlacementTestWeightage extends Zend_Db_Table_Abstract {

	protected $_name = 'appl_placement_weightage';
	protected $_primary = "apw_id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('apw'=>$this->_name))
					->where('apw.apw_id = '.$id);
							
			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
						->from(array('apw'=>$this->_name));
								
			$row = $db->fetchAll($select);
		}

		return $row;
		
	}
	
	public function getWeightageData($app_id=0, $apd_id){
		
		if($app_id!=0 && $apd_id!=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('apw'=>$this->_name))
				->where('apw.apw_app_id = ?', $app_id)	
				->where('apw.apw_apd_id = ?', $apd_id);	
					
		$row = $db->fetchRow($select);
		}
		
		if($app_id!=0 && $apd_id!=0 && $row){
			return $row;	
		}else{
			return null;
		}
		
		
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
							->from(array('apw'=>$this->_name))
             		    	->order($this->_primary.' ASC');
						
		return $selectData;
	}
	
	public function addData($postData){
		
		$data = array(
		        'apw_app_id' => $postData['apw_app_id'],
				'apw_apd_id' => $postData['apw_apd_id'],
				'apw_weightage' => $postData['apw_weightage']
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		
		$data = array(
		        'apw_app_id' => $postData['apw_app_id'],
				'apw_apd_id' => $postData['apw_apd_id'],
				'apw_weightage' => $postData['apw_weightage']
				);
			
		$this->update($data, 'apw_id = '. (int)$id);
	}
	
	public function deleteData($data,$id){
		if($id!=0){
			$this->update($data, 'apw_id = '. (int)$id);
		}
	}

}

