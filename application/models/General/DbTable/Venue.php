<?php 
class App_Model_General_DbTable_Venue extends Zend_Db_Table_Abstract
{
    protected $_name = 'g009_venue';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                ->from(array('v'=>$this->_name))
	                ->where('v.'.$this->_primary.' = ' .$id)
					->joinLeft(array('vt'=>'g010_venue_Type'),
								"v.type = vt.id",
								array('type_name'=>'vt.name'))
					->joinLeft(array('state'=>'g002_state'),
								"v.state_id = state.id",
								array('state_name'=>'state.name'))
					->joinLeft(array('country'=>'g001_country'),
								"v.country_id = country.id",
								array('country_name'=>'country.name'))
					->order("v.name ASC");
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	        
			if(!$row){
				throw new Exception("There is No Data");
			}
        
		}else{
			$select = $db->select()
	                ->from(array('v'=>$this->_name))
					->joinLeft(array('vt'=>'g010_venue_Type'),
								"v.type = vt.id",
								array('type_name'=>'vt.name'))
					->joinLeft(array('state'=>'g002_state'),
								"v.state_id = state.id",
								array('state_name'=>'state.name'))
					->joinLeft(array('country'=>'g001_country'),
								"v.country_id = country.id",
								array('country_name'=>'country.name'))
					->order("v.name ASC");
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        
	        if(!$row){
	        	$row =  $row->toArray();
	        }
		}
//		echo $select;
		return $row;
	}
	
	public function getVenue($id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		             ->from(array('v'=>$this->_name))
					 ->where('v.id = '.$id);
	 
        $row = $db->fetchRow($select);
	      
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                ->from(array('v'=>$this->_name))
					->joinLeft(array('vt'=>'g010_venue_Type'),
								"v.type = vt.id",
								array('type_name'=>'vt.name'))
					->joinLeft(array('state'=>'g002_state'),
								"v.state_id = state.id",
								array('state_name'=>'state.name'))
					->joinLeft(array('country'=>'g001_country'),
								"v.country_id = country.id",
								array('country_name'=>'country.name'));
		
		return $select;
	}
	
	public function addData($data){
		$data = array(
			'name' => $data['name'],
			'username' => $data['username'],
			'password' => md5($data['clearpass']),
			'clearpass' => $data['clearpass'],
			'type' => $data['type'],
			'capacity' => $data['capacity'],
			'branch_id' => $data['branch_id'],
			'office_id' => $data['office_id'],
			'office_id' => $data['office_id'],
			'address1' => $data['address1'],
			'address2' => $data['address2'],
			'postcode' => $data['postcode'],
			'city' => $data['city'],
			'state_id' => $data['state_id'],
			'country_id' => $data['country_id'],
		);
			
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		$data = array(
			'name' => $data['name'],
			'username' => $data['username'],
			'password' => md5($data['clearpass']),
			'clearpass' => $data['clearpass'],
			'type' => $data['type'],
			'capacity' => $data['capacity'],
			'branch_id' => $data['branch_id'],
			'office_id' => $data['office_id'],
			'address1' => $data['address1'],
			'address2' => $data['address2'],
			'postcode' => $data['postcode'],
			'city' => $data['city'],
			'state_id' => $data['state_id'],
			'country_id' => $data['country_id'],
		);
		
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function getBranchVenue($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		$select = $db->select()
	                ->from(array('v'=>$this->_name))
	                ->where("v.branch_id = '".$id."'")
	                ->join(array('t'=>'g010_venue_type'),'v.type = t.id',array('venue_type_name'=>'t.name'))
					->joinLeft(array('b'=>'g004_branch'),
								"v.branch_id = b.id",
								array('branch_name'=>'b.name','branch_code'=>'b.code'))
					->joinLeft(array('o'=>'g007_office'),
								" v.office_id = o.id",
								array('office_name'=>'o.name', 'office_code'=>'o.code'));
		
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        
	        return $row;
	}
	
	public function getOfficeVenue($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		$select = $db->select()
	                ->from(array('v'=>$this->_name))
	                ->where("v.office_id = '".$id."'")
	                ->join(array('t'=>'g010_venue_type'),'v.type = t.id',array('venue_type_name'=>'t.name'))
					->joinLeft(array('b'=>'g004_branch'),
								"v.branch_id = b.id",
								array('branch_name'=>'b.name','branch_code'=>'b.code'))
					->joinLeft(array('o'=>'g007_office'),
								" v.office_id = o.id",
								array('office_name'=>'o.name', 'office_code'=>'o.code'));
		
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        
	        return $row;
	}
}
?>