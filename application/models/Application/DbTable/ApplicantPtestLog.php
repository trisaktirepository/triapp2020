<?php 
class App_Model_Application_DbTable_ApplicantPtestLog extends Zend_Db_Table_Abstract
{
    protected $_name = 'applicant_ptest_log';
	protected $_primary = 'apt_id';
	
	public function getData(){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	     $select = $db ->select()
					->from($this->_name)
					->where('1');										
       
        $row = $db->fetchRow($select);
		return $row;
	}
	
	public function getPtestLog($transaction_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	     $select = $db ->select()
					->from($this->_name)
					->where('apt_at_trans_id =?', $transaction_id);										
       
        $row = $db->fetchRow($select);
        
        if($row){
        	return $row;	
        }else{
        	return null;
        }
		
	}
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
}
?>