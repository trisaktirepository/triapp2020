<?php

class App_Model_Application_DbTable_Condition extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'a006_condition';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary.' = ' .$id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Condition");
		}

		return $row->toArray();
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		echo $selectData = $db ->select()
						->from(array('edu_level'=>$this->_name))
             		    ->order($this->_primary.' DESC');
						
		return $selectData;
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
		        'sc001_name' => $postData['sc001_name'],
				'rank' => 0
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		$data = array(
				'sc001_name' => $postData['sc001_name']
				);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}

}

