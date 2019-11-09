<?php


class SystemSetup_Model_DbTable_User extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'u001_user';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('r'=>$this->_name))
						->where('r.id = '.$id);
						
			$stmt = $db->query($select);
	        $row = $stmt->fetch();						
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('r'=>$this->_name));
						
			$stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		}
		
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('u'=>$this->_name));
		
		return $selectData;
	}
	
	public function addData($postData=null){
		
		try{
			$data = array(
				'staff_id' => $postData['staff_id'],
				'fullname' => $postData['fullname'],
				'username' => $postData['username'],
				'password' => $postData['password'],
				'date_created' => date('Y-m-d H:i:s'),
				'created_by' => $postData['created_by']
				);
				
			$uid  = $this->insert($data);
		}catch (Exception $e){
			throw new Exception($e);
		}				
			
		return $uid;
	}
	
	public function updateData($postData,$id){
		
		$data = array(
			'staff_id' => $postData['staff_id'],
			'fullname' => $postData['fullname'],
			'username' => $postData['username'],
			
			);
			
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
	
	
	public function searchUser($keywords){
			$db = Zend_Db_Table::getDefaultAdapter();
			
			$select     = $db ->select()
						      ->from($this->_name);
						      
			if($keywords) $select->where("fullname LIKE ?", '%'.$keywords.'%' );
    		if($keywords) $select->where('username LIKE ?', '%'.$keywords.'%');

    		
    		$stmt = $db->query($select);
	    	$row = $stmt->fetchAll();	        
		
			return $row;    					      
		
	}
	
	
}

