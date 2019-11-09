<?php


class SystemSetup_Model_DbTable_Controller extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'sys005_controller';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('c'=>$this->_name))
						->where('c.id = '.$id);
						
			$stmt = $db->query($select);
	        $row = $stmt->fetch();						
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('c'=>$this->_name));
						
			$stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}
	
	
	public function search($name="",$module_id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
						->from(array('c'=>$this->_name));
						
		if($name!=""){
			$select->where("name = '".$name."'");	
		}						
		
		if($module_id!=""){
			$select->where("module_id = '".$module_id."'");	
		}
		
		$stmt = $db->query($select);
	    $row = $stmt->fetch();
	    
	    return $row;
	}
	
	public function addData($data){
		$data = array(
			'module_id' => $data['module_id'],
			'name' => $data['name']
		);
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		$data = array(
			'module_id' => $data['module_id'],
			'name' => $data['name']
		);
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
}

