<?php
class App_Model_Record_DbTable_SubjectsOffered extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_subjectsoffered';
    protected $_primary = 'IdSubjectsOffered';
   
    public function getSubjectsOfferBySemester($idSemester,$idSubject){
    	
    	  $db = Zend_Db_Table::getDefaultAdapter();
    	  
    	  $select = $db->select()
		 				 ->from(array("so"=>$this->_name))		
		 				 ->where("so.IdSemester = ?",$idSemester)
		 				 ->where("so.IdSubject = ?",$idSubject);		 				
		 				 
		  $larrResult = $db->fetchRow($select);
		  return $larrResult;
    }
    
}