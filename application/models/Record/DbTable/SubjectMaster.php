<?php


class App_Model_Record_DbTable_SubjectMaster extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_subjectmaster';
	protected $_primary = "IdSubject";
	
	
	
	public function getData($idCourse=0){
		
		 $select  = $this->select()
	                    ->from($this->_name)
	                    ->where('IdSubject = ?',$idCourse);
	                    
	     $result = $this->fetchRow($select);    
       
         return $result;  
	}
	

	

}

