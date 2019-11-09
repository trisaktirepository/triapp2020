<?php

class App_Model_Application_DbTable_ApplicantUploadFile extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'appl_upload_file';
	protected $_primary = "auf_id";
	
	public function getTxnFile($txnId,$type){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from(array('a'=>$this->_name))
					  ->joinLeft(array('b'=>'sis_setup_detl'), 'b.ssd_id = a.auf_file_type')
					  ->where("auf_appl_id='".$txnId."'")
					  ->where('a.auf_file_type = '.$type);
					  
		 $row = $db->fetchRow($select);	
		 
		 if(!$row){
		 	return null;
		 }else{
		 	return $row;
		 }
		 
	}
	
	
	public function getTxnFileArray($txnId,$type=''){
		
		$txnId = (int)$txnId;
		
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->joinLeft(array('b'=>'sis_setup_detl'), 'b.ssd_id = a.auf_file_type')
						->where('a.auf_appl_id = '.$txnId);

			if($type!=''){
				$select->where('a.auf_file_type = '.$type);
			}
			
	        $row = $db->fetchAll($select);				
		
		return $row;
	}
}

