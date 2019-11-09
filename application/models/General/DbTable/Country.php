<?php 
class App_Model_General_DbTable_Country extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_countries';
	protected $_primary = "idCountry";
	
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
	                 ->from($this->_name)
	                 ->order('CountryName ASC');
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        
	        if(!$row){
	        	$row =  $row->toArray();
	        }
		}
		
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db ->select()
							->from($this->_name);
		
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
		$data = array(
			'name' => $data['name'],
			'code' => $data['code'],
			'iso3' => $data['iso3'],
			'arab_continent' => $data['arab_continent'],
		);
			
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		$data = array(
			'name' => $data['name'],
			'code' => $data['code'],
			'iso3' => $data['iso3'],
			'arab_continent' => $data['arab_continent'],
		);
		
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function getState($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from(array('c'=>$this->_name))
	                 ->where('c.'.$this->_primary.' = ' .$id)
	                 ->join(array('s'=>'g002_state'),'s.country_id = c.id',array('state_id'=>'id','state_name'=>'name'));
	        
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        
			if(!$row){
				throw new Exception("There is No Data");
			}
			
			return $row;
        
		}else{
			throw new Exception("There is No Data");
		}
	}
	
	public function selectCountry(){    	
    	
          	
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
}
?>