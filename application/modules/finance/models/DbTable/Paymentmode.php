<?php
/**
 * @author Suliana
 */

class Finance_Model_DbTable_Paymentmode extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'f001_paymentmode';
	protected $_primary = "id";
	
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
	                ->from(array('f'=>$this->_name));
		
		return $select;
	}
	
	public function getDataIndividual(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$idmode = array(3);
		
		$select = $db->select()
	                ->from(array('f'=>$this->_name))
	                ->where('f.id NOT IN (?)',$idmode);
		
	    $result = $db->fetchAll($select);  
		return $result;
	}
	
	public function addData($postData){
		
		$data= array(
					'fr_paymode'=>$postData['fr_paymode'],
					'fr_desc'=>$postData['fr_desc'],
					'fr_category'=>$postData['fr_category'],
					'fr_bankslipsts'=>$postData['fr_bankslipsts']
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