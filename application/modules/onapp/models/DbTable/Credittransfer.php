<?php
/**
 * @author Suliana
 */

class Onapp_Model_DbTable_Credittransfer extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'sa003_apply_req';
	protected $_primary = "sa003_apply_req_id";
	protected $_fac = 'faculty';
	
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Such Applicant.");
		}
		return $row->toArray();
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					->from($this->_name)
					->join('hg015_branch', 'ha001_hg015_branch_id = hg015_branch_id')
					->order($this->_primary);
		
		return $select;
	}
	
	public function getInfo($table){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					->from($table);
		
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		
		return $result;
	}
	
	public function addData($postData){
		
		$data= array(
					'sa003_apply_id'=>$postData['id_apply'],
					'sa003_entry'=>$postData['sc001_program_id'],
					'sa003_year'=>$postData['txtRow11'],
					'sa003_school'=>$postData['txtRow12'],
					'sa003_prog'=>$postData['txtRow13'],
					'sa003_result'=>$postData['txtRow14']
					); 

		$this->insert($data);
 		$id = $this->getAdapter()->lastInsertId();
		return $id;
		
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
	
	
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}

}

