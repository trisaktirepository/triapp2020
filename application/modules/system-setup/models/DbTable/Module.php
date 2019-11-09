<?php


class SystemSetup_Model_DbTable_Module extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'sys002_module';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('m'=>$this->_name))
						->where('m.id = '.$id);
						
			$stmt = $db->query($select);
	        $row = $stmt->fetch();						
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('m'=>$this->_name));
						
			$stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Module");
		}
		
		return $row;
	}
	
	
	public function search($name="",$alias=""){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
						->from(array('m'=>$this->_name));
						
		if($name!=""){
			$select->where("name = '".$name."'");	
		}						
		
		if($alias!=""){
			$select->where("alias = '".$alias."'");	
		}
		
		$stmt = $db->query($select);
	    $row = $stmt->fetch();
	    
	    return $row;
	}
	
	public function updateData($data,$id){
		$data = array(
			'name' => $data['name'],
			'alias' => $data['alias'],
			'status' => $data['status']
		);
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
}

