<?php


class SystemSetup_Model_DbTable_Audit extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'sys001_log';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('l'=>$this->_name))
						->where('l.id = '.$id);
						
			$stmt = $db->query($select);
	        $row = $stmt->fetch();						
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('l'=>$this->_name));
						
			$stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		}
		
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('log'=>$this->_name))
						->join(array('user'=>'u001_user'),"user.id = log.user_id", array('staff_id'=>'staff_id','fullname'=>'fullname','username'=>'username'))
						->order("log.id DESC");
		
		return $selectData;
	}
}

