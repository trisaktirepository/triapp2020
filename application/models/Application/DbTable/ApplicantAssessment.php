<?php 

class App_Model_Application_DbTable_ApplicantAssessment extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'applicant_assessment';
	protected $_primary = "aar_id";
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	
	public function getData($transaction_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from(array('arr'=>$this->_name))
					  ->joinLeft(array('asd'=>'applicant_selection_detl'), 'asd.asd_id = arr.aar_rector_selectionid')
					  ->where("arr.aar_trans_id = '".$transaction_id."'")
					  ->order('arr.aar_id desc');
				  
		 $row = $db->fetchRow($select);	
		 return $row;
	}
	
	public function updateAssessmentData($data,$id){
		 $this->update($data, 'aar_trans_id = '. (int)$id);
	}
	
	public function getInfo($transaction_id){
		
		 $db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db ->select()
					  ->from(array('arr'=>$this->_name))	
						->where('arr.aar_rector_status="1"')					  
					  ->where("arr.aar_trans_id = '".$transaction_id."'");
									  
		 $row = $db->fetchRow($select);	
		 
		 if($row){
		    return $row;
		 }else{
		 	return null;
		 }
	}
}	
?>