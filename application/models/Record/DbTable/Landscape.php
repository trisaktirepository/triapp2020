<?php
class App_Model_Record_DbTable_Landscape extends Zend_Db_Table_Abstract
{
	protected $_name = 'tbl_landscape';
	
        
	public function getData($idlandscape){
     	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array('l'=>$this->_name))
		 				 ->where("l.IdLandscape = ?",$idlandscape);
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
     }
     
     
	public function getCourseInfo($idLandscapeSubject){
    	
    	 $db = Zend_Db_Table::getDefaultAdapter();
    	 
    	  $select = $db->select()
		 				 ->from(array("ls"=>"tbl_landscapesubject"))		
		 				 ->joinLeft(array("d"=>"tbl_definationms"),'d.idDefinition=ls.SubjectType',array('DefinitionDesc'))
		 				 ->where("ls.IdLandscapeSub = ?",$idLandscapeSubject);
		 			 
		  $row = $db->fetchRow($select);
		  return $row;
    }
    
    
    public function getStudentLandscape($IdStudentRegistration){
    	
    	 $db = Zend_Db_Table::getDefaultAdapter();
    	 
    	  $select = $db->select()
		 				 ->from(array("l"=>"tbl_landscape"),array('LandscapeType'))		
		 				 ->join(array("sr"=>"tbl_studentregistration"),'sr.IdLandscape=l.IdLandscape',array('IdLandscape','IdProgram'))
		 				 ->where("sr.IdStudentRegistration = ?",$IdStudentRegistration);
		 			 
		  $row = $db->fetchRow($select);
		  return $row;
    }
    
     
}
?>
