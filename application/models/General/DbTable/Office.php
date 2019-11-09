<?php 
class App_Model_General_DbTable_Office extends Zend_Db_Table_Abstract
{
    protected $_name = 'g007_office';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from(array('o'=>$this->_name))
	                 ->where('o.'.$this->_primary.' = ' .$id)
	                 ->join(array('b'=>'g004_branch'),
									"b.id = o.branch_id",
									array('branch_name'=>'b.name'))
					->joinLeft(array('c'=>'g001_country'),
								"c.id = o.country_id",
								array('country_name'=>'c.name'))
					->joinLeft(array('s'=>'g002_state'),
								"s.id = o.state_id",
								array('state_name'=>'s.name'));
					                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	      
			if(!$row){
				throw new Exception("There is No Data");
			}
        
		}else{
			$select = $db->select()
	                 ->from(array('o'=>$this->_name))
	                 ->join(array('b'=>'g004_branch'),
									"b.id = o.branch_id",
									array('branch_name'=>'b.name')
						);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        
		}
		
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db ->select()
							 ->from(array('o'=>$this->_name))
							->join(array('b'=>'g004_branch'),
									"b.id = o.branch_id",
									array('branch_name'=>'b.name')
							);
		return $selectData;
	}
	
	public function getOfficeArray(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectCountry = $db ->select()
							->from(array('o'=>$this->_name))
							->join(array('b'=>'g004_branch'),
									"b.id = o.branch_id",
									array('branch_name'=>'b.name', 'branch_code'=>'b.code')
							)
							->group('o.branch_id');
							
		$stmt = $db->query($selectCountry);
        $row = $stmt->fetchAll();
	    
		
        $i=0;
        foreach ($row as $branch){
	    	$row[$i]['office'] = $this->getOfficeFromBranch($branch['branch_id']);
	    	$i++;	    	
	    }
        
        return $row;
	}
	
	public function getOfficeFromBranch($branch_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db ->select()
							->from(array('o'=>$this->_name))
							->where('branch_id = ?', $branch_id)
							->joinLeft(array('b'=>'g004_branch'),
									"b.id = o.branch_id",
									array('branch_name'=>'b.name')
							);
							
		$stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
        return $row;
	}
	
	public function addData($data){
		$data = array(
			'name' => $data['name'],
			'branch_id' => $data['branch_id'],
			'code' => $data['code'],
			'address1' => $data['address1'],
			'address2' => $data['address2'],
			'postcode' => $data['postcode'],
			'city' => $data['city'],
			'country_id' => $data['country_id'],
			'state_id' => $data['state_id'],
		);
			
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		$data = array(
			'name' => $data['name'],
			'branch_id' => $data['branch_id'],
			'code' => $data['code'],
			'address1' => $data['address1'],
			'address2' => $data['address2'],
			'postcode' => $data['postcode'],
			'city' => $data['city'],
			'country_id' => $data['country_id'],
			'state_id' => $data['state_id'],
		);
			
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
}
?>