<?php 
class App_Model_General_DbTable_Branch extends Zend_Db_Table_Abstract
{
    protected $_name = 'g004_branch';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                ->from(array('b'=>$this->_name))
	                ->where('b.'.$this->_primary.' = ' .$id)
					->joinLeft(array('c'=>'g001_country'),
								"c.id = b.country_id",
								array('country_name'=>'c.name'))
					->joinLeft(array('s'=>'g002_state'),
								"s.id = b.state_id",
								array('state_name'=>'s.name'));
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	        
			if(!$row){
				throw new Exception("There is No Data");
			}
        
		}else{
			$select = $db->select()
	                ->from(array('b'=>$this->_name))
					->joinLeft(array('c'=>'g001_country'),
								"c.id = b.country_id",
								array('country_name'=>'c.name'))
					->joinLeft(array('s'=>'g002_state'),
								"s.id = b.state_id",
								array('state_name'=>'s.name'));
			                     
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
		
		$select = $db->select()
	                ->from(array('b'=>$this->_name))
					->joinLeft(array('c'=>'g001_country'),
								"c.id = b.country_id",
								array('country_name'=>'c.name'))
					->joinLeft(array('s'=>'g002_state'),
								"s.id = b.state_id",
								array('state_name'=>'s.name'));
		
		return $select;
	}
	
	public function addData($data){
		$data = array(
			'name' => $data['name'],
			'code' => $data['code'],
			'address1' => $data['address1'],
			'address2' => $data['address2'],
			'city' => $data['city'],
			'state_id' => $data['state_id'],
			'country_id' => $data['country_id'],
			'postcode' => $data['postcode']
		);
			
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		$data = array(
			'name' => $data['name'],
			'code' => $data['code'],
			'address1' => $data['address1'],
			'address2' => $data['address2'],
			'city' => $data['city'],
			'state_id' => $data['state_id'],
			'country_id' => $data['country_id'],
			'postcode' => $data['postcode']
		);
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function getBranchArray(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectCountry = $db ->select()
							->from(array('b'=>$this->_name));
							
		$stmt = $db->query($selectCountry);
        $row = $stmt->fetchAll();
	    
		$officeDB = new App_Model_General_DbTable_Office();
		
        $i=0;
        foreach ($row as $branch){
	    	$row[$i]['office'] = $officeDB->getOfficeFromBranch($branch['id']);
	    	$i++;	    	
	    }
	    
        return $row;
	}
}
?>