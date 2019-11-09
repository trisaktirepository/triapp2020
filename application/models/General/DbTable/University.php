<?php 
class App_Model_General_DbTable_University extends Zend_Db_Table_Abstract
{
    protected $_name = 'g003_university';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from(array('u'=>$this->_name))
	                 ->where('u.'.$this->_primary.' = ' .$id)
	                 ->joinLeft(array('c'=>'g001_country'),'u.country_id = c.id',array('country_name'=>'name'))
	                 ->joinLeft(array('s'=>'g002_state'),'u.state_id = s.id',array('state_name'=>'name'));
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
        
		}else{
			$select = $db->select()
	                 ->from($this->_name);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        $row =  $row->toArray();
		}
		
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db ->select()
							->from($this->_name);
		
		return $selectData;
	}
	
	public function addData($data){
		$data = array(
			'name' => $data['name'],
			'address1' => $data['address1'],
			'address2' => $data['address2'],
			'postcode' => $data['postcode'],
			'city' => $data['city'],
			'state_id' => $data['state_id'],
			'country_id' => $data['country_id'],
			'phone' => $data['phone'],
			'email' => $data['email'],
			'url' => $data['url']
		);
			
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		$data = array(
			'name' => $data['name'],
			'address1' => $data['address1'],
			'address2' => $data['address2'],
			'postcode' => $data['postcode'],
			'city' => $data['city'],
			'state_id' => $data['state_id'],
			'country_id' => $data['country_id'],
			'phone' => $data['phone'],
			'email' => $data['email'],
			'url' => $data['url']
		);
			
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
}
?>