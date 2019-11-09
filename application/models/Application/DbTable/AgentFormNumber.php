<?php 

class App_Model_Application_DbTable_AgentFormNumber extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'agent_form_number';
	protected $_primary = "afn_id";
	
	
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function getData($id=""){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("at_status IN ('APPLY','CLOSE','PROCESS')")
					  ->order("at_trans_id desc");
					  
		if($id)	{			
			 $select->where("at_appl_id ='".$id."'");
			 $row = $db->fetchRow($select);				 
		}	 
		
		 return $row;
	}
	
	public function checkUnusedFormNo($form_no,$intake){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db ->select()
					  ->from($this->_name)
					  ->where("afn_form_no = ?", $form_no)
					  ->where("afn_taken_status = 0")
					  ->where("afn_intake = ?",$intake);
					  
		$row = $db->fetchRow($select);
		
		$select2 = $db ->select()
					  ->from('applicant_transaction')
					  ->where("at_pes_id = ?", $form_no);					  
		$row2 = $db->fetchRow($select2);
		
		if($row && !$row2){		
			return true;
		}else{		
			return false;
		}
	}
	
	public function checkValidFormNo($form_no,$intake){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("afn_form_no = ?", $form_no)
					  ->where("afn_intake = ?",$intake);
					  
		$row = $db->fetchRow($select);
		
		if($row){
			return true;
		}else{
			return false;
		}
	}
	
	public function updateTakenFormNo($data,$form_no){
		 $this->update($data, "afn_form_no = '".$form_no."'");
	}
	
	
	
	
}	

	
?>