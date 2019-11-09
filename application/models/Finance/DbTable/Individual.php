<?php 
class App_Model_General_DbTable_TakafulOperator extends Zend_Db_Table_Abstract
{
    protected $_name = 'g013_takafuloperator';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){
	                
			$select = $db->select()
	                ->from(array('f'=>$this->_name))
	                ->where('f.'.$this->_primary.' = ' .$id);	                
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	        
			if(!$row){
				throw new Exception("There is No Data");
			}
        
		}else{
			$select = $db->select()
	                ->from(array('f'=>$this->_name))
	                ->order('name ASC');
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        
		}
		
		return $row;
	}
	
	public function getDataType($client){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                ->from(array('f'=>$this->_name))
	                ->where('f.idClienttype ='.$client);
	                
	    $stmt = $db->query($select);
	    $row = $stmt->fetchAll();
		
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                ->from(array('f'=>$this->_name));
		
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