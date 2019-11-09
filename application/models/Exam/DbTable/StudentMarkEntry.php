<?php


class App_Model_Exam_DbTable_StudentMarkEntry extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_student_marks_entry';
	protected $_primary = "IdStudentMarksEntry";
		
	
	public function getSubjectMark($idSemester,$IdStudentRegistration,$idSubject,$IdMarksDistributionMaster){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$sql = $db->select()
                        ->from(array('sme' => $this->_name))                                            
                        ->where('sme.IdStudentRegistration = ?', $IdStudentRegistration)
                        ->where('sme.IdSemester = ?',$idSemester)
                        ->where('sme.Course = ?',$idSubject)
                        ->where('sme.IdMarksDistributionMaster = ?',$IdMarksDistributionMaster);
         
        return $result = $db->fetchRow($sql);
        
	}
   
	

}

