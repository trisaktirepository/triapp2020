<?php
class App_Model_Exam_DbTable_Examiner extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'e016_examiner';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			
			$select = $db->select()
							->from(array('e'=>$this->_name))
							->joinLeft(array('b'=>'g004_branch'), 'b.id = e.branch_id', array('branch_id'=>'id', 'branch_name'=>'name'))
							->where('e.'.$this->_primary .' = ?', $id)
							->order('e.name');
			
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
							->from(array('e'=>$this->_name))
							->joinLeft(array('b'=>'g004_branch'), 'b.id = e.branch_id', array('branch_id'=>'id', 'branch_name'=>'name'))
							->order('e.name');
		
		return $select;
	}
	
	public function addData($postData){
		$auth = Zend_Auth::getInstance();
		
		$data = array(
			'name'  => $postData['name'],
			'username'  => $postData['username'],
			'password'  => md5($postData['password']),
			'email'  => $postData['email'],
			'branch_id'  => $postData['branch_id'],
			'create_by'	=> $postData['create_by']				
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
				'email'  => $postData['email'],
				'branch_id'  => $postData['branch_id']				
				);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
}

