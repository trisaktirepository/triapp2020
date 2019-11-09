<?php

class App_Model_Application_DbTable_PlacementTestSchedule extends Zend_Db_Table_Abstract {

	protected $_name = 'appl_placement_schedule';
	protected $_primary = "aps_id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('aps'=>$this->_name))
					->where('aps.aps_id = '.$id);

			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
						->from(array('aps'=>$this->_name))
						->order('aps.aps_location_id ASC');
								
			$row = $db->fetchAll($select);
		}
		
		return $row;
		
	}
	
	public function getPlacementTestData($placementTestCode=null){
				
		if($placementTestCode!=null){
			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('aps'=>$this->_name))
					->joinLeft(array('al'=>'appl_location'), 'al.al_id = aps_location_id ')
					->where("aps.aps_placement_code = '".$placementTestCode."'")
					->order("aps_location_id ASC")
					->order("aps_test_date ASC");

			$row = $db->fetchAll($select);
			
		}else{
			$row = null;
		}
		
		return $row;
		
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('al'=>$this->_name))
					->joinLeft(array('u'=>'tbl_user'),'u.iduser = al.al_update_by', array('al_update_by_name'=>'fName'))
					->joinLeft(array('ar'=>'appl_room'),'ar.av_location_code = al.al_id', array('tot_room' => new Zend_Db_Expr('COUNT(av_id)')))
					->where('al.al_status = ?', 1)
					->group('al.al_id')
					->order('al.al_location_name ASC');
						
		return $selectData;
	}
	
	public function searchPaginate($post = array()){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('al'=>$this->_name))
						->joinLeft(array('u'=>'tbl_user'),'u.iduser = al.al_update_by', array('al_update_by_name'=>'fName'))
						->joinLeft(array('ar'=>'appl_room'),'ar.av_location_code = al.al_id', array('tot_room' => new Zend_Db_Expr('COUNT(av_id)')))
						->where("al.al_location_code LIKE '%".$post['al_location_code']."%'")
						->where("al.al_location_name LIKE '%".$post['al_location_name']."%'")
						->group('al.al_id')
             		    ->order('al.al_location_name ASC');
						
		return $selectData;
	}
	
	public function addData($postData){
		
		$data = array(
		        'aps_placement_code' => $postData['aps_placement_code'],
				'aps_location_id' => $postData['aps_location_id'],
				'aps_test_date' => date('Y-m-d', strtotime($postData['aps_test_date'])),
				'aps_start_time' => $postData['aps_start_time']
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		
		$data = array(
		        'aps_placement_code' => $postData['aps_placement_code'],
				'aps_location_id' => $postData['aps_location_id'],
				'aps_test_date' => date('Y-m-d', strtotime($postData['aps_test_date'])),
				'aps_start_time' => '08:00:00'
				);
			
		$this->update($data, 'aps_id = '. (int)$id);
	}
	
	public function deleteData($id){
		if($id!=0){
			$this->delete('aps_id = '. (int)$id);
		}
	}

public function getInfo($condition=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('aps'=>$this->_name))
				->joinLeft(array('al'=>'appl_location'),'al.al_id=aps.aps_location_id',array('location_name'=>'al.al_location_name'));

		if($condition!=null){
			if($condition["schedule_id"]!=''){
				$select->where('aps.aps_id = '.$condition["schedule_id"]);
			}
		}

		echo $select;
		$row = $db->fetchRow($select);
		
		
		return $row;
		
	}
}

