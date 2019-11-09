<?php 

class GeneralSetup_Model_DbTable_CourseGroupStudent extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_course_group_student_mapping';
	protected $_primary = "Id";
	
	
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
	
	public function getTotalStudent($idGroup){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		 $select = $db ->select()		
					  ->from(array('gsm'=>$this->_name))
					  ->join(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = gsm.IdStudent')
					  ->where('gsm.IdCourseTaggingGroup = ?',$idGroup);					  
		 $row = $db->fetchAll($select);	
		 
		 if($row)
		 	return count($row);
		 else
		 return 0;
	}
	
	public function removeStudent($idGroup,$idStudent){		
		
	  $this->delete("IdCourseTaggingGroup='". $idGroup ."' AND IdStudent = '".$idStudent."'");
	}
	
	public function getStudent($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
					->from(array('gsm'=>$this->_name))
					->join(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = gsm.IdStudent')
					->joinLeft(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication')
					->where('IdCourseTaggingGroup = ?',$idGroup)
					->order('sr.registrationId');
	
		$row = $db->fetchAll($select);
			
		if($row)
			return $row;
		else
			return null;
	}
	
	public function getStudentbyGroup($idGroup,$student=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();	
		
	   $select = $db ->select()
					->from(array('gsm'=>$this->_name))
					->join(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = gsm.IdStudent')
					->join(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication')
					->join(array('p'=>'tbl_program'), 'p.IdProgram=sr.IdProgram',array('ProgramName'=>'ArabicName','ProgramCode'))
					->where('IdCourseTaggingGroup = ?',$idGroup)
					->order('sr.registrationId');
					
		if(isset($student)){
			$select->where("((sp.appl_fname LIKE '%".$student."%'");
			$select->orwhere("sp.appl_mname LIKE '%".$student."%'");
			$select->orwhere("sp.appl_lname LIKE '%".$student."%')");		
			$select->orwhere("sr.registrationId LIKE '%".$student."%')");
		}
		
		$row = $db->fetchAll($select);
			
		if($row)
			return $row;
		else
			return null;
	}
	
	public function checkStudentCourseGroup($IdStudentRegistration,$idSemester,$idSubject){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		 $select = $db ->select()		
					  ->from(array('cgsm'=>'tbl_studentregsubjects'))
					  ->join(array('ctg'=>'tbl_course_tagging_group'),'ctg.IdCourseTaggingGroup=cgsm.IdCourseTaggingGroup')
					  ->where('cgsm.IdStudentRegistration = ?',$IdStudentRegistration)
					  ->where('ctg.IdSemester = ?',$idSemester)
					  ->where('ctg.IdSubject = ?',$idSubject);					  
		 $row = $db->fetchRow($select);	
		 
		 return $row;
	}
}