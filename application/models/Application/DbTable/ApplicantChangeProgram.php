<?php 

class App_Model_Application_DbTable_ApplicantChangeProgram extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'applicant_change_program';
	protected $_primary = "acp_id";
	
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
					  ->from(array('acp'=>$this->_name))							 		  
					  ->where("acp.acp_trans_id = '".$transaction_id."'");
									  
		 $row = $db->fetchRow($select);	
		 
		 if($row){
		    return $row;
		 }else{
		 	return null;
		 }
	}
	
	public function getChangeProgram($transaction_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from(array('acp'=>$this->_name))		
					  ->joinLeft(array('ap'=>'applicant_program'),'ap.ap_at_trans_id=acp.acp_chg_prog_trans_id')
					  ->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=ap.ap_prog_code')			  
					  ->where("acp.acp_trans_id = '".$transaction_id."'");
									  
		 $row = $db->fetchAll($select);	
		 
		 if($row){
		    return $row;
		 }else{
		 	return null;
		 }
	}
	
	
	public function getListChangeProgram($appl_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db ->select()
					  ->from(array('acp'=>$this->_name))						
					  ->where("acp.acp_appl_id = '".$appl_id."'");
									  
		 $row = $db->fetchAll($select);	
		 
		 if($row){
		    return $row;
		 }else{
		 	return null;
		 }
	}
	
	public function getData($id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from(array('acp'=>$this->_name))							 		  
					  ->where("acp.acp_id = '".$id."'");
									  
		 $row = $db->fetchRow($select);	
		 
		 if($row){
		    return $row;
		 }else{
		 	return null;
		 }
	}
}