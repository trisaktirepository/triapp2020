<?php 
class App_Model_General_DbTable_District extends Zend_Db_Table_Abstract
{
    protected $_name = 'g006_district';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from(array('d'=>$this->_name))
	                 ->where('d.'.$this->_primary.' = ' .$id)
	                 ->join(array('s'=>'g002_state'),
									"s.id = d.state_id",
									array('state_name'=>'s.name')
									);
					                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	        
			if(!$row){
				throw new Exception("There is No District Data");
			}
        
		}else{
			$select = $db->select()
	                 ->from(array('d'=>$this->_name))
	                 ->join(array('s'=>'g002_state'),
									"s.id = d.state_id",
									array('state_name'=>'s.name')
									);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        
		}
		
		return $row;
	}
	
	public function getPaginateData($id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		if($id!=0){
			$selectData = $db ->select()
							->from(array('d'=>$this->_name))
							->where('d.state_id = '.$id)
							->join(array('s'=>'g002_state'),
									"s.id = d.state_id",
									array('state_name'=>'s.name')
									);	
		}else{
			$selectData = $db ->select()
							->from(array('d'=>$this->_name))
							->join(array('s'=>'g002_state'),
									"s.id = d.state_id",
									array('state_name'=>'s.name')
									);
		}
		
		
		return $selectData;
	}
	
	public function addData($data){
		$data = array(
			'name' => $data['name'],
			'state_id' => $data['state_id']
		);
			
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		$data = array(
			'name' => $data['name'],
			'state_id' => $data['state_id']
		);
			
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
}
?>