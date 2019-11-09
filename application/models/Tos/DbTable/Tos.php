<?php 
class App_Model_Tos_DbTable_Tos extends Zend_Db_Table_Abstract
{
    protected $_name = 'q014_tos'; 
	protected $_primary = "id";
	
	protected $_subname = 'q015_tos_details'; 
	protected $_subprimary = "id";

	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from($this->_name)
	                 ->where($this->_primary.' = ' .$id);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
        
		}else{
			$select = $db->select()
	                 ->from($this->_name);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		}
		
		
		return $row;
	}
	
	
	public function getSubData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from($this->_subname)
	                 ->where($this->_primary.' = ' .$id);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
        
		}else{
			$select = $db->select()
	                 ->from($this->_subname);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		}
		
		
		return $row;
	}
	
	public function getDetailTos($condition=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
	        $select = $db->select()
	                 ->from($this->_subname);
	                
	        if($condition!=""){
	        	if($condition["tos_id"]){
	        		$select->where('tos_id='.$condition["tos_id"]);
	        	}
	        	
	        	if($condition["topic_id"]){
	        		$select->where('topic_id='.$condition["topic_id"]);
	        	}
	        	
	        	if($condition["difficulty_level"]){
	        		$select->where('difficulty_level='.$condition["difficulty_level"]);
	        	}
	        }
	       //echo $select;
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	        return $row;
	}
	
	
	public function getActiveTos($pool_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	        $select = $db->select()
	                     ->from($this->_name)
	                     ->where('pool_id='.$pool_id)
	                     ->where('status = 1');
		  
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	        return $row;
	}
	
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db ->select()
							->from($this->_name);
		
		return $selectData;
	}
	
	public function getPaginateTosByPool($pool_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db ->select()
							->from($this->_name)
							->where('pool_id='.$pool_id);
		
		return $selectData;
	}
	
	
	public function addData($data){		
		return $id = $this->insert($data);
	}
	
	public function addDetails($data){	
		$db = Zend_Db_Table::getDefaultAdapter();	
		return $id = $db->insert($this->_subname,$data);
	}
	
	
	public function updateData($data,$id){
		
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function updateDetails($data,$id){
		$db = Zend_Db_Table::getDefaultAdapter();	
		$db->update($this->_subname,$data,$this->_subprimary . ' = ' . (int)$id);
	}
	
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function deleteDetails($id){
		$db = Zend_Db_Table::getDefaultAdapter();	
		$db->delete($this->_subname,'idTos =' . (int)$id);
	}
	
	public function updateStatus($data,$pool_id){
		
		$this->update($data,'pool_id = ' . (int)$pool_id);
	}
}
?>