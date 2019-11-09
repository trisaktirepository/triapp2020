<?php 


class App_Model_Record_DbTable_StudentContact extends Zend_Db_Table_Abstract {
	
	/**
	 * The default table name 
	 */
	protected $_name = 'r017_student_contact';
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
	
	
	public function getContact($student_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
							->from(array('sc'=>$this->_name))
							->where('sc.student_id = ?', $student_id)
							->joinLeft(array('s'=>'g002_state'),'sc.ec_state = s.id',array('state_name'=>'s.name'))
							->joinLeft(array('c'=>'g001_country'),'sc.ec_country=c.id',array('country_name'=>'c.name'));														
		
		$row = $db->fetchRow($select);							
		
		return $row;
	}
	
	public function updateData($postData,$student_id){
		$auth = Zend_Auth::getInstance(); 
		
		$data = array(		    
				'email'        => $postData['email'],   
				'house_phone'  => $postData['house_phone'],
				'office_phone' => $postData['office_phone'],
				'mobile'       => $postData['mobile'],
				'ec_name'      => $postData['ec_name'],
				'ec_address'   => $postData['ec_address'],
				'ec_city'      => $postData['ec_city'],
				'ec_postcode'  => $postData['ec_postcode'],
				'ec_city'      => $postData['ec_city'],
				'ec_state'     => $postData['ec_state'],
				'ec_country'   => $postData['ec_country'],
				);
			
		$this->update($data, 'student_id = '. (int)$student_id);
	}
	
	
}
?>