<?php
/**
 * @author Suliana
 */

class Application_Model_DbTable_ApplicantEntry extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'a002_applicant_entry';
	protected $_primary = "ID";
	
	public function getData($app_id=0){
		$app_id = (int)$app_id;
		
		if($app_id!=0){
			$row = $this->fetchAll('APP_ID = '. $app_id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Such Qualification.");
		}
		return $row->toArray();
	}
	
	public function getPaginateData($app_id=0){
		$app_id = (int)$app_id;
		$db = Zend_Db_Table::getDefaultAdapter();
		
		if($id!=0){
			$select = $db->select()
					->from($this->_name)
					->where('APP_ID = '. $app_id)
					->order($this->_primary);
		}else{
			$select = $db->select()
					->from($this->_name)
					->order($this->_primary);
		}
		
		return $select;
	}
	
	public function getEntry($app_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					->from($this->_name)
					->where('APP_ID  = '.$app_id.' AND APP_ENTRY != 0')
					->order('ID');
					
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		
		return $result;
	}
	
	public function addData($postData){
		
		$data= array(
					'APP_ID'=>$postData['APP_ID'],
					'APP_ENTRY'=>$postData['APP_ENTRY'],
					'APP_YEAR'=>$postData['APP_YEAR'],
					'APP_SCHOOL'=>$postData['APP_SCHOOL'],
					'APP_QUA'=>$postData['APP_QUA'],
					'APP_GRADE'=>$postData['APP_GRADE'],
					'APP_STAT'=>$postData['APP_STAT']
					); 

		$this->insert($data);
 		$id = $this->getAdapter()->lastInsertId();
		return $id;
		
	}
	
	/*public function addData($postData){
		
		$data= array(
					'sa001_ic'=>$postData['sa001_ic'],
					'sa001_name'=>$postData['sa001_name'],
					'sa001_contact'=>$postData['sa001_contact'],
					'sa001_email'=>$postData['sa001_email']
					); 

		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		
		$data= array(
					'sa001_address'=>$postData['sa001_address'],
					'sa001_address2'=>$postData['sa001_address2'],
					'sa001_poscode'=>$postData['sa001_poscode'],
					'sa001_state'=>$postData['sa001_state'],
					'sa001_country'=>$postData['sa001_country'],
					'sa001_date'=>$postData['sa001_date']
					);
		
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	*/
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}

}