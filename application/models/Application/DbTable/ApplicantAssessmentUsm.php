<?php 

class App_Model_Application_DbTable_ApplicantAssessmentUsm extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'applicant_assessment_usm';
	protected $_primary = "aau_id";
	
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
	
	public function getInfo($transaction_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from(array('aau'=>$this->_name))	
					  ->where('aau.aau_rector_status="1"')					  
					  ->where("aau.aau_trans_id = '".$transaction_id."'");
									  
		 $row = $db->fetchRow($select);	
		 
		 if($row){
		    return $row;
		 }else{
		 	return null;
		 }
	}

	public function getData($transaction_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from(array('aau'=>$this->_name),array('aau.*','aar_rating_rector'=>'aau_rector_ranking'))
					  ->joinLeft(array('aaud'=>'applicant_assessment_usm_detl'), 'aaud.aaud_id = aau.aau_rector_selectionid',array('aaud.*','asd_decree_date'=>'aaud_decree_date','asd_nomor'=>'aaud_nomor','aar_reg_start_date'=>'aaud_reg_start_date','aar_reg_end_date'=>'aaud_reg_end_date'))
					  ->where("aau.aau_trans_id = '".$transaction_id."'")
					  ->order('aau.aau_id desc');
				  
		 $row = $db->fetchRow($select);	
		 return $row;
	}
	
	
	
}
?>