<?php 
class App_Model_Question_DbTable_Chapter extends Zend_Db_Table_Abstract
{
    protected $_name = 'q002_chapter';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from(array('u'=>$this->_name))
	                 ->where('u.'.$this->_primary.' = ' .$id);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
        
		}else{
			$select = $db->select()
	                 ->from($this->_name);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        $row =  $row->toArray();
		}
		
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}
	
	
	public function getTopicbyPool($pid){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	    $select = $db->select()
		             ->from($this->_name)
					 ->where('idPool='.$pid);
	 
        $row = $db->fetchAll($select);
	      
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db ->select()
							->from($this->_name);
		
		return $selectData;
	}
	
	public function addData($data){
		
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
}
?>