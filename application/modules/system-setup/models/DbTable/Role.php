<?php


class SystemSetup_Model_DbTable_Role extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'sys003_role';
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
	
	public function addData($postData){
		$data = array(
				'name' => $postData['name']
				);
			
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		$data = array(
			'name' => $data['name']
		);
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
}

