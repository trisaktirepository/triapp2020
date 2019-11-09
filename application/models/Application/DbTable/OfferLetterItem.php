<?php

class App_Model_Application_DbTable_OfferLetterItem extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'a011_offerletter_item';
	protected $_primary = "id";
		
	public function getTemplate($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary.' = ' .$id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Placement Test");
		}

		return $row->toArray();
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('of'=>$this->_name))
             		    ->order($this->_primary.' DESC');
						
		return $selectData;
	}
	
	
	public function checkItem($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
							->from($this->_name)
							->where('template_id = '.$id);
		
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		
		return $result;
	}
	
	public function getList(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from(array('t'=>'a010_offerletter_template'))
		->join(array('i'=>'a011_offerletter_item'), 'i.template_id = t.id')
		->order('i.order ASC');
//		->where('t.status = 1');	
		//->order('charging_type');
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		
		return $result;
	}
	
	public function add($postData,$template_id){
		
		$auth = Zend_Auth::getInstance(); 
		
		$data = array(
		        'template_id' => $template_id,
		        'title' => $postData['title'],
		        'content' => $postData['content'],
		        'order' => $postData['order'],
				'status' => 1
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		
		$auth = Zend_Auth::getInstance(); 
		
		$data = array(
				'title' => $postData['title'],
		        'content' => $postData['content'],
		        'order' => $postData['order'],
				'status' => 1
				);
			
		$this->update($data, 'template_id = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}

}

