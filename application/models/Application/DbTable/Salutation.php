<?php

class App_Model_Application_DbTable_Salutation extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'a015_salutation';
	protected $_primary = "id";
		
	public function getProgram($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary.' = ' .$id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Salutation setup");
		}

		return $row->toArray();
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('salutation'=>$this->_name))
             		    ->order($this->_primary.' DESC');
						
		return $selectData;
	}
	
	public function getData($table,$cond=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from($table)
		->where($cond);
		//->order($orderby);
//		echo $select;
		$stmt = $select->query();
		$result = $stmt->fetchAll();
			
		return $result;
	}
	
	public function getList($table,$tbljoin,$joincond,$tbljoin2=0,$joincond2=0,$cond){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from($table)
		->join($tbljoin, $joincond)
		->join($tbljoin2, $joincond2)
		//->join($tbljoin3, $joincond3)
		->where($cond);	
		//->order('charging_type');
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		
		return $result;
	}
	
	public function add($postData){
		$data = array(
		        'name' => $postData['name']
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		$data = array(
				'name' => $postData['name']
				);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}

}

