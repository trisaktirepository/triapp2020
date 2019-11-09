<?php 
class App_Model_General_DbTable_Toefl extends Zend_Db_Table_Abstract
{
    protected $_name = 'activity_participant_mark';
	protected $_primary = "idapm";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from(array('c'=>$this->_name))
	                 ->where('c.'.$this->_primary.' = ' .$id);
					                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	        
			if(!$row){
				throw new Exception("There is No Data");
			}
        
		}else{
			$select = $db->select()
	                 ->from(array('c'=>$this->_name))
	                 ->joinLeft(array('s'=>'tbl_state'),"s.idState = c.idState",array('state_name'=>'c.CityName'));
			
	        $row = $db->fetchAll($select);
	        
		}
		
		return $row;
	}
	
	public function getPrerequisite($idstd,$idcomponent,$min){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db->select()
	                 ->from(array('c'=>$this->_name))
	                 ->join(array('a'=>'activity_participant'),'c.idparticipant=a.idparticipant')
	                 ->where('c.idcomponent=?',$idcomponent)
	                 ->where('c.mark >= ?',$min);
		
		$row=$db->fetchAll($selectData);
	                 
		return $row;
	}
	
	 
	 
	
	public function addData($data){
		$data = array(
			'name' => $data['name'],
			'country_id' => $data['country_id']
		);
			
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		$data = array(
			'name' => $data['name'],
			'country_id' => $data['country_id']
		);
			
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
	
	 
    
    
	 
}
?>