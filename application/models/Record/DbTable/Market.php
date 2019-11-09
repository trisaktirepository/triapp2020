<?php

/**
 * Country
 * 
 * @author Muhamad Alif Muhammad
 * @date Apr 25, 2010
 * @version 
 */

class App_Model_Record_DbTable_Market extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'r004_market';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Market");
		}
		
		return $row->toArray();
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
							->from($this->_name)
							->order('name');
		
		return $select;
	}
	
	public function addData($postData){
		$data = array(
				'name' => $postData['name'],
				'description' => $postData['description']
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		$data = array(
				'name' => $postData['name'],
				'description' => $postData['description']
				);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}

}

