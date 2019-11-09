<?php 

class Examination_Model_DbTable_Resit extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_student_resit';
	protected $_primary = "sr_id";
	

	public function getListApplication($formdata=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
					  ->from(array('tsr'=>$this->_name))
					  ->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=tsr.sr_idStudentRegistration',array('IdStudentRegistration','registrationId'))
					  ->join(array('sp'=>'student_profile'),'sp.appl_id=sr.idApplication',array('appl_fname','appl_mname','appl_lname'))
					  ->joinLeft(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=tsr.sr_idSemester',array('IdSemesterMaster','SemesterMainName'))
					  ->joinLeft(array('s'=>'tbl_subjectmaster'),'s.IdSubject=tsr.sr_idSubject',array('SubjectCode'=>'SubCode','SubjectName'=>'subjectMainDefaultLanguage'));

		if(isset($formdata)){
			
			if(isset($formdata["idIntake"]) && $formdata["idIntake"]!=''){
				$select->where("sr.IdIntake = ?",$formdata["idIntake"]);
			}
			
			if(isset($formdata["IdProgram"]) && $formdata["IdProgram"]!=''){
				$select->where("sr.IdProgram = ?",$formdata["IdProgram"]);
			}
			
			if(isset($formdata["IdSemester"]) && $formdata["IdSemester"]!=''){
				$select->where("tsr.sr_idSemester = ?",$formdata["IdSemester"]);
			}
						
			if(isset($formdata["status"]) && $formdata["status"]!=''){
				$select->where("tsr.sr_status = ?",$formdata["status"]);
			}
			
			if(isset($formdata["Student"]) && $formdata["Student"]!=''){
				$select->where("((sp.appl_fname  LIKE '%".$formdata["Student"]."%'");
		 		$select->orwhere("sp.appl_mname  LIKE '%".$formdata["Student"]."%'");
		 		$select->orwhere("sp.appl_lname  LIKE '%".$formdata["Student"]."%')");
		 		$select->orwhere("sr.registrationId  LIKE '%".$formdata["Student"]."%')");
			}
		}
		
		echo $select;
		$rows = $db->fetchAll($select);
		
		return $rows;
	}
}

?>