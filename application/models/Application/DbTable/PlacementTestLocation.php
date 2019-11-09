<?php

class App_Model_Application_DbTable_PlacementTestLocation extends Zend_Db_Table_Abstract {

	protected $_name = 'appl_location';
	protected $_primary = "al_id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('al'=>$this->_name))
					->joinLeft(array('u'=>'tbl_user'),'u.iduser = al.al_update_by', array('al_update_by_name'=>'fName'))
					->joinLeft(array('c'=>'tbl_countries'),'c.idCountry = al.al_country', array('al_country_name'=>'CountryName'))
					->joinLeft(array('s'=>'tbl_state'),'s.idState = al.al_state', array('al_state_name'=>'StateName'))
					->joinLeft(array('ct'=>'tbl_city'),'ct.idCity = al.al_city', array('al_city_name'=>'CityName'))
					->where('al.al_id = '.$id);

			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
						->from(array('al'=>$this->_name))
						->where('al.al_status = ?', 1)
						->order('al.al_location_name ASC');
								
			$row = $db->fetchAll($select);
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
		        'al_location_code' => $postData['al_location_code'],
				'al_location_name' => $postData['al_location_name'],
				'al_address1' => $postData['al_address1'],
				'al_address2' => $postData['al_address2'],
				'al_city' => $postData['al_city'],
				'al_state' => $postData['al_state'],
				'al_country' => $postData['al_country'],
				'al_phone' => $postData['al_phone'],
				'al_contact_person' => $postData['al_contact_person'],
				'al_status' => $postData['al_status'],
				'al_update_by' => $postData['al_update_by'],
				'al_update_date' => $postData['al_update_date']
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		
		$data = array(
		        'al_location_code' => $postData['al_location_code'],
				'al_location_name' => $postData['al_location_name'],
				'al_address1' => $postData['al_address1'],
				'al_address2' => $postData['al_address2'],
				'al_city' => $postData['al_city'],
				'al_state' => $postData['al_state'],
				'al_country' => $postData['al_country'],
				'al_phone' => $postData['al_phone'],
				'al_contact_person' => $postData['al_contact_person'],
				'al_update_by' => $postData['al_update_by'],
				'al_update_date' => $postData['al_update_date']
				);
			
		$this->update($data, 'al_id = '. (int)$id);
	}
	
	public function deleteData($data,$id){
		if($id!=0){
			$this->update($data, 'al_id = '. (int)$id);
		}
	}

}

