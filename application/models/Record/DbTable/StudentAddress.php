<?php 


class App_Model_Record_DbTable_StudentAddress extends Zend_Db_Table_Abstract {
	
	/**
	 * The default table name 
	 */
	protected $_name = 'r019_student_address';
	protected $_primary = "id";
	
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is data found.");
		}			
		return $row->toArray();
	}
	
	
	public function getAddress($student_id,$address_type_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		
		$select = $db->select()
					->from(array('sa'=>$this->_name))
					->where('student_id = ?', $student_id)
					->where('address_type_id = ?', $address_type_id)
					->joinLeft(array('s'=>'g002_state'),'sa.state_id = s.id',array('state_name'=>'s.name'))
					->joinLeft(array('c'=>'g001_country'),'sa.country_id=c.id',array('country_name'=>'c.name'));				
		
		$row = $db->fetchRow($select);							
		
		return $row;
	}
	
	public function updateData($postData,$student_id,$address_type_id){
		
		$data = array( 
		        'student_id'    => $postData['student_id'],
 				'address1'      => $postData['address1'],
				'address2'      => $postData['address2'],
				'city'          => $postData['city'],
				'city'  		=> $postData['city'],
				'state_id'      => $postData['state_id'],
				'country_id'    => $postData['country_id'],
				'postcode'  	=> $postData['postcode'],
				);
				
		$this->update($data, array(
							'student_id = ?' => $student_id,
							'address_type_id = ?' => $address_type_id
					  ));
	}
	
	
	
}
?>