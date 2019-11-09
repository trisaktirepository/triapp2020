<?php
/**
 * @author Suliana
 */

class Onapp_Model_DbTable_Branch extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'branch';
	protected $_primary = "branchID";
	protected $_entry = "sc005entry";
	
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
	
	//join
	public function getInfo($cond){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					->from($this->_name)
					->join('hg015_branch', 'ha001_hg015_branch_id = hg015_branch_id')
					->order($this->_primary);
		
		return $select;
		
		if($cond!=0){
			$row = $this->fetchRow($cond);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Such Applicant.");
		}
		return $row->toArray();
	}
	
	public function getPaginateData($cond){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					->from($this->_name)
					->where('sc005prog  ='.$cond);
					
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		
		return $result;
	}
	
	public function addData($postData){
		
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
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}

}