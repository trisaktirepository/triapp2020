<?php 
class App_Model_General_DbTable_City extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_city';
	protected $_primary = "idCity";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from(array('c'=>$this->_name))
	                 ->where('c.'.$this->_primary.' = ' .$id)
	                 ->joinLeft(array('s'=>'tbl_state'),"s.idState = c.idState",array('state_name'=>'c.CityName'));
					                     
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
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db->select()
	                 ->from(array('c'=>$this->_name))
	                 ->joinLeft(array('s'=>'tbl_state'),"s.idState = c.idState",array('state_name'=>'c.CityName'));
	                 
		return $selectData;
	}
	
	public function getStateArray(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectCountry = $db ->select()
							->from(array('s'=>$this->_name))
							->join(array('c'=>'g001_country'),
									"c.id = s.country_id",
									array('country_name'=>'c.name')
							)
							->group('s.country_id');
							
		$stmt = $db->query($selectCountry);
        $row = $stmt->fetchAll();
	    
        $i=0;
        foreach ($row as $country){
	    	$row[$i]['state'] = $this->getState($country['country_id']);
	    	$i++;	    	
	    }
        
        return $row;
	}
	
	public function getState($country_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db ->select()
							->from(array('s'=>$this->_name))
							->where('country_id = ?', $country_id)
							->join(array('c'=>'g001_country'),
									"c.id = s.country_id",
									array('country_name'=>'c.name')
							)
							->order('s.name ASC');
							
		$stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
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
	
	public function selectState(){    	
    	
          	
    	$select = $this->select()->from($this, array('id', 'name'));
        $rowSet = $this->fetchAll($select);
		$arraySet = $rowSet->toArray();
		$list = array();
		$list = array("Please Select..");
		foreach ($arraySet as $value) {
			$list[$value['id']] = $value['name'];
		}
        return $list;
    }
    
    
	public function getCityByState($state_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db ->select()
							->from(array('s'=>$this->_name))
							->where('idState = ?', $state_id)		
							->where('Active=1')					
							->order('s.CityName ASC');
							
		$stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
        return $row;
	}
}
?>