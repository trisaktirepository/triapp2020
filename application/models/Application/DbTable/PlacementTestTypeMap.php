<?php

class App_Model_Application_DbTable_PlacementTestTypeMap extends Zend_Db_Table_Abstract {

	protected $_name = 'testtype_roomtype_map';
	protected $_primary = "idTrm";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('att'=>$this->_name))
					->where('att.act_id = '.$id);
							
			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('att'=>$this->_name));
								
			$row = $db->fetchAll($select);
		}
		
//		if(!$row){
//			throw new Exception("There is No Student Information");
//		}
		return $row;
		
	}
	
	public function getTestType($rrtype){
		 
		 
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array('att'=>$this->_name))
			->where('att.artt_id = '.$rrtype);
				
			$row = $db->fetchRow($select);
		 
		return $row;
	
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('att'=>$this->_name))
             		    ->order($this->_primary.' ASC');
						
		return $selectData;
	}
	
	public function searchPaginate($post = array()){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('att'=>$this->_name))
						->where("act_name LIKE '%".$post['act_name']."%'")
             		    ->order($this->_primary.' ASC');
						
		return $selectData;
	}
	
	public function addData($postData){
		
		$auth = Zend_Auth::getInstance(); 
		
		$data = array(
		        'act_name' => $postData['act_name']
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		
		$auth = Zend_Auth::getInstance(); 
		
		$data = array(
		        'act_name' => $postData['act_name']
				);
			
		$this->update($data, 'act_id ='. (int)$id);
	}
	
	public function deleteData($id=0){
		if($id!=0){
			$this->delete('act_id = '. (int)$id);
		}
	}

}

