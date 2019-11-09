<?php
class App_Model_General_DbTable_Room extends Zend_Db_Table { 	
	protected $_name = 'appl_room'; // table name
	
	/*
	 * fetch all  Active Bank details
	 */
    public function fnGetRoomDetails() {
		$select = $this->select()
			->setIntegrityCheck(false)  ;	
			//->join(array('a' => 'tbl_bank'),array('IdBank'))			
			//->where("Active = 1");
		$result = $this->fetchAll($select);
		
		return $result->toArray();    	  
    }
    public function fnGetRoom($idroom) {
    	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"appl_room"))
		 				 ->where('a.av_id=?',$idroom);
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
    }
    public function fnGetRoomByVenueCode($idroom) {
    	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
    	$lstrSelect = $lobjDbAdpt->select()
    	->from(array("a"=>"appl_room"))
    	->where('a.av_room_code=?',$idroom);
    	$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
    	return $larrResult;
    }
    
	public function fnGetRoomList($owner=null){
		
		$session = new Zend_Session_Namespace('sis');
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"appl_room"))
		 				 ->join(array('c'=>'tbl_collegemaster'),'c.IdCollege=a.owner')
		 				 ->where("a.av_status = 1")
		 				 ->order("a.av_room_code");
		if (isset($owner)) $lstrSelect->where('a.owner=?',$owner);
		if($session->IdRole != 1){ 
			$lstrSelect->where("a.owner='".$session->idCollege."'");
		}
		
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

    /*
     * search method
     */
	public function fnSearchroom($post = array()) {
		    $db = Zend_Db_Table::getDefaultAdapter();
		    $field7 = "Active = ".$post["field7"];
		    $select = $this->select()
			->setIntegrityCheck(false)  	
			->join(array('a' => 'tbl_bank'),array('IdBank'))
			->where("a.BankName LIKE '%".$post['field3']."%'")
			->where("a.Email LIKE '%".$post['field2']."%'")
			->where($field7);
		$result = $this->fetchAll($select);
		
		return $result->toArray();
	}
	
	/*
	 * add bank row
	 */
	public function fnAddRoom($post) {		
						
		$this->insert($post);
	}
	public function fnGetBuilding() {
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $this->select()
			->from(array('r'=>$this->_name),array('Building'=>'r.av_building'))
			->group('r.av_building');
		$result = $this->fetchAll($select);
	
		return $result->toArray();
	}
	
	public function fnGetBuildingJakarta() {
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $this->select()
		->from(array('r'=>$this->_name),array('Building'=>'r.av_building'))
		->where('r.av_location_code="1"')
		->group('r.av_building');
		$result = $this->fetchAll($select);
	
		return $result->toArray();
	}
	
	
     public function fnUpdateRoom($lintIdBank, $formData) {
    	
    	$this->update($formData,$where);
    }
}
