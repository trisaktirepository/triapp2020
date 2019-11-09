<?php 
class App_Model_System_DbTable_Language extends Zend_Db_Table_Abstract
{
    protected $_name = 'sys006_language';
	protected $_primary = 'id';
	
	
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from($this->_name)
	                 ->where($this->_primary.' = ' .$id);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	        
			if(!$row){
				throw new Exception("There is No Data");
			}
        
		}else{
			$select = $db->select()
	                 ->from($this->_name);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        
	        if(!$row){
	        	$row =  $row->toArray();
	        }
		}
		
		return $row;
	}
	
	public function getLang(){
		
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
				->from($this->_name)
				->order(array('id DESC'));
		
		$stmt = $db->query($select);
	    $row = $stmt->fetchAll();
		
		return $row;
	}

	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db ->select()
							->from($this->_name)
							->order(array('id DESC'));
		
		return $selectData;
	}
	
	public function getPaginateSearch($search){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db ->select()
							->from($this->_name)
							->where("name like '%$search%'");
		
		return $selectData;
	}
	
	public function addData($data){
		
		$auth = Zend_Auth::getInstance(); 
		
		$data = array(
			'english' => $data['english'],
			'arabic' => $data['arabic'],
			'createddt' => date("Y-m-d H:i:s"),
      	 	'createdby' => $auth->getIdentity()->id,
		);
			
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		$data = array(
			'english' => $data['english'],
			'arabic' => $data['arabic'],
		);
		
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
	

}
?>