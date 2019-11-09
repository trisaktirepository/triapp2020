<?php

class App_Model_Application_DbTable_PlacementTestRoom extends Zend_Db_Table_Abstract {

	protected $_name = 'appl_room';
	protected $_primary = "av_id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('ar'=>$this->_name))
					->where('ar.av_id = '.$id);
					
					
			$row = $db->fetchRow($select);
			
			if($row){
				$roomDb = new App_Model_Application_DbTable_PlacementTestRoomType();
				
				$typeData = array();
				$i=0;
				foreach($roomDb->getRoomType($row['av_id']) as $type){
					$typeData[$i] = $type['art_test_type'];
					$i++;
				}
				
				$row['av_test_type'] = $typeData;
			}
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
						->from(array('ar'=>$this->_name))
						->where('ar.av_id = ?', $id);
								
			$row = $db->fetchAll($select);
		}
		
//		if(!$row){
//			throw new Exception("There is No Student Information");
//		}
		return $row;
		
	}
	
	public function getLocationData($location_code=0){
		$location_code = (int)$location_code;
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('ar'=>$this->_name))
				->joinLeft(array('u'=>'u001_user'),'u.id = ar.av_update_by', array('ar.av_update_by_name'=>'fullname'))
				->joinLeft(array('artt'=>'appl_room_test_type'),'artt.artt_id = ar.av_test_type', array('av_test_type_name'=>'artt_name'))
				->where('ar.av_location_code = '.$location_code)
				->where('ar.av_status = 1')
				->order('IF(ISNULL(ar.av_test_type),1,0), ar.av_test_type DESC', 'ar.av_seq ASC', 'ar.av_room_name ASC')
				->order('ar.av_seq ASC')
				->order('ar.av_room_name ASC');
		
		$row = $db->fetchAll($select);
	
		if(!$row){
			return null;
		}else{
			return $row;
		}	
	}
	
	public function getLocationVenue($location_code=0){
		$location_code = (int)$location_code;
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('ar'=>$this->_name))
				->joinLeft(array('u'=>'u001_user'),'u.id = ar.av_update_by', array('ar.av_update_by_name'=>'fullname'))
				->where('ar.av_location_code = '.$location_code)
				->where('ar.av_status = 1')
				->order('ar.av_room_name ASC');
		
		$row = $db->fetchAll($select);
	
		if(!$row){
			return null;
		}else{

			$i=0;
			foreach ($row as $data){
				$roomDb = new App_Model_Application_DbTable_PlacementTestRoomType();
				$row[$i]['type'] = $roomDb->getRoomType($data['av_id']);
				
				$i++;	
			}
						
			return $row;
		}	
	}
	
	public function getPaginateData($location_code = 0){
		$db = Zend_Db_Table::getDefaultAdapter();
		if($location_code!=0){
			$selectData = $db->select()
					->from(array('ar'=>$this->_name))
					->join(array('u'=>'u001_user'),'u.id = ar.av_update_by', array('ar.av_update_by_name'=>'fullname'))
					->where('ar.av_status = ?', 1)
					->where('ar.av_location_code = ?', $location_code)
					->order('ar.av_room_name ASC');
		}else{
			$selectData = $db->select()
					->from(array('ar'=>$this->_name))
					->join(array('u'=>'u001_user'),'u.id = ar.av_update_by', array('al_update_by_name'=>'fullname'))
					->where('ar.av_status = ?', 1)
					->order('ar.av_room_name ASC');	
		}
						
		return $selectData;
	}
	
	public function searchPaginate($location_id=0, $post = array()){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('al'=>$this->_name))
						->join(array('u'=>'u001_user'),'u.id = al.al_update_by', array('al_update_by_name'=>'fullname'))
						->where("al.al_location_code LIKE '%".$post['al_location_code']."%'")
						->where("al.al_location_name LIKE '%".$post['al_location_name']."%'")
						->where("al.al_address1 LIKE '%".$post['al_address1']."%'")
						->where("al.al_address2 LIKE '%".$post['al_address2']."%'")
						->where("al.al_city like '%".$post['al_city']."%'")
						->where("al.al_state like '%".$post['al_state']."%'")
						->where("al.al_country like '%".$post['al_country']."%'")
						->where("al.al_phone like '%".$post['al_phone']."%'")
						->where("al.al_contact_person like '%".$post['al_contact_person']."%'")
						->where("al.al_status like '%".$post['al_status']."%'")
						->where("al.al_update_by like '%".$post['al_update_by']."%'")
						->where("al.al_update_date like '%".$post['al_update_date']."%'")
						->where('al.al_status = ?', 1)
             		    ->order('al.al_location_name ASC');
						
		return $selectData;
	}
	
	public function addData($postData){
		
		$data = array(
		        'av_location_code' => $postData['av_location_code'],
				'av_room_name' => $postData['av_room_name'],
				'av_room_name_short' => $postData['av_room_name_short'],
				'av_room_code' => $postData['av_room_code'],
				'av_building' => $postData['av_building'],
				'av_tutorial_capacity' => $postData['av_tutorial_capacity'],
				'av_exam_capacity' => $postData['av_exam_capacity'],
				'av_update_by' => $postData['av_update_by'],
				'av_update_date' => $postData['av_update_date'],
				'av_status' => 1,
				'av_seq' => $postData['av_seq']
				);

		return $this->insert($data);
	}
	
	public function updateData($postData,$id){
		
		$data = array(
				'av_room_name' => $postData['av_room_name'],
				'av_room_name_short' => $postData['av_room_name_short'],
				'av_room_code' => $postData['av_room_code'],
				'av_building' => $postData['av_building'],
				'av_tutorial_capacity' => $postData['av_tutorial_capacity'],
				'av_exam_capacity' => $postData['av_exam_capacity'],
				'av_update_by' => $postData['av_update_by'],
				'av_update_date' => $postData['av_update_date'],
				'av_seq' => $postData['av_seq']
				);
			
		$this->update($data, 'av_id = '. (int)$id);
	}
	
	public function deleteData($data,$id){
		if($id!=0){
			$this->update($data, 'av_id = '. (int)$id);
		}
	}

}

