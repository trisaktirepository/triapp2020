<?php 
class App_Model_Discipline_DbTable_Penalty extends Zend_Db_Table_Abstract
{
    protected $_name = 'd002_penalty';
	protected $_primary = "id";
	
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
	        
		}
		
		return $row;
	}
	
	
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                ->from($this->_name);
		
		return $select;
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