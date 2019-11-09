<?php
class App_Model_Exam_DbTable_Lecturer extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'e019_lecturer';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			
			$select = $db->select()
							->from(array('l'=>$this->_name))
							->joinLeft(array('b'=>'g004_branch'), 'b.id = l.branch_id', array('branch_id'=>'id', 'branch_name'=>'name'))
							->joinLeft(array('c'=>'g001_country'), 'c.id = l.country_origin', array('country_id'=>'id', 'country_name'=>'name'))
							->where('l.'.$this->_primary .' = ?', $id)
							->order('l.name');
			
			$row = $db->fetchRow($select);
		}else{
			$row = $this->fetchAll();
			
			$row = $row->toArray();
		}
		
		if(!$row){
			throw new Exception("There is no data");
		}
			
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
							->from(array('l'=>$this->_name))
							->joinLeft(array('b'=>'g004_branch'), 'b.id = l.branch_id', array('branch_id'=>'id', 'branch_name'=>'name'))
							->joinLeft(array('c'=>'g001_country'), 'c.id = l.country_origin', array('country_id'=>'id', 'country_name'=>'name'))
							->order('l.name');
		
		return $select;
	}
	
	public function addData($postData){
		
		$data = array(
			'name'  => $postData['name'],
			'salutation'  => $postData['salutation'],
			'identity_id'  => $postData['identity_id'],
			'identity_type_id'  => $postData['identity_type_id'],
			'branch_id'  => $postData['branch_id'],
			'country_origin'	=> $postData['country_origin'],
			'email'	=> $postData['email'],
			'status'	=> $postData['status'],				
			);
		
		try{
			$id = $this->insert($data);
		}catch (Exception $e){
			throw new Exception($e);
		}
		
		return $id;
	}
	
	public function updateData($postData,$id){
		$data = array(
			'name'  => $postData['name'],
			'salutation'  => $postData['salutation'],
			'identity_id'  => $postData['identity_id'],
			'identity_type_id'  => $postData['identity_type_id'],
			'branch_id'  => $postData['branch_id'],
			'country_origin'	=> $postData['country_origin'],
			'email'	=> $postData['email'],
			'status'	=> $postData['status'],				
			);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
}

