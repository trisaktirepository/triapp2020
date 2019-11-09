<?php


class App_Model_Exam_DbTable_Intake extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'r014_intake';
	protected $_primary = "id";
		
	public function getIntake($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is no Intake Information");
		}
			
		return $row->toArray();
	}
	
	public function getPaginateIntake(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
							->from($this->_name)
							->order('id');
		
		return $select;
	}
	
	public function addIntake($postData){
			$data = array(
				'name'  => $postData['name'],
				'description'  => $postData['description']				
				);
			
		$this->insert($data);
	}
	
	public function updateIntake($postData,$id){
		$data = array(
				'name'  => $postData['name'],
				'description'  => $postData['description']				
				);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteIntake($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
	
	
    public function getIntakeList()	{
	
	    $select  = $this->select()
	                    ->from($this,array('id','name','description'));
        $result = $this->fetchAll($select);    
        $result = $result->toArray();        
        $list = array("Please Select..");
		foreach ($result as $value) {
			$list[$value['id']] = $value['name'];
		}
        return $list;
	    
	}
	
	
		public function selectIntake(){    	
    	
        $select = $this->select()->from($this, array('id', 'name'));
    	$rowSet = $this->fetchAll($select);
    	$arraySet = $rowSet->toArray();
    	return $arraySet;
    }
	

}

