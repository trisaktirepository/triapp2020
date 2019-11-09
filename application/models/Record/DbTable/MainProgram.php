<?php 
class App_Model_Record_DbTable_MainProgram extends Zend_Db_Table_Abstract
{
    protected $_name = 'r005_program_main';
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
        
		}else{
			$select = $db->select()
	                 ->from($this->_name);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Program");
		}
		
		return $row;
	}
	
	
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db ->select()
						->from($this->_name)
						->order('name');
				
		return $selectData;
	}
	
	public function addData($postData){
		$data = array(
		        'name' => $postData['name']
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		$data = array(
		        'name' => $postData['name']
				);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' = '. (int)$id);
	}
}
?>