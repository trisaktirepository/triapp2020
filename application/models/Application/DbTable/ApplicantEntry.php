<?php
/**
 * @author Suliana
 */

class App_Model_Application_DbTable_ApplicantEntry extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'a002_applicant_entry';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->where('a.app_id = '.$id)
						->join(array('p'=>'a004_education_level'),'p.sc001_program_id = a.award');
						
	        $row = $db->fetchAll($select);				
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->join(array('p'=>'a004_education_level'),'p.sc001_program_id = a.award');
						
			$row = $db->fetchAll($select);
		}
		
//		if(!$row){
//			throw new Exception("There is No Applicant");
//		}
		
		return $row;
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
	
	public function addData($appID,$postData){
		
		$data= array(
					'app_id'=>$appID,
					'place_obtained'=>$postData['place_obtained'],
					'year_obtained'=>$postData['year_obtained'],
					'award'=>$postData['award'],
					'cert'=>strtoupper($postData['cert']),
					'school_name'=>strtoupper($postData['school_name']),
					'result'=>$postData['result'],
					'point'=>$postData['point']
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