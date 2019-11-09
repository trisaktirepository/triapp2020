<?php 

class Examination_Model_DbTable_Appeal extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_student_appeal';
	protected $_primary = "sa_id";
	protected $_appeal_mark = 'tbl_student_appeal_mark';
	protected $_appeal_mark_id = "sam_id";
	

	public function getListApplication($formdata=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
					  ->from(array('sa'=>$this->_name))
					  ->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=sa.sa_idStudentRegistration',array('IdStudentRegistration','registrationId'))
					  ->join(array('sp'=>'student_profile'),'sp.appl_id=sr.idApplication',array('appl_fname','appl_mname','appl_lname'))
					  ->joinLeft(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=sa.sa_idSemester',array('IdSemesterMaster','SemesterMainName'))
					  ->joinLeft(array('s'=>'tbl_subjectmaster'),'s.IdSubject=sa.sa_idSubject',array('SubjectCode'=>'SubCode','SubjectName'=>'subjectMainDefaultLanguage'));
					  
		$rows = $db->fetchAll($select);
		
		if(isset($formdata)){
			
			if(isset($formdata["idIntake"]) && $formdata["idIntake"]!=''){
				$select->where("sr.IdIntake = ?",$formdata["idIntake"]);
			}
			
			if(isset($formdata["IdProgram"]) && $formdata["IdProgram"]!=''){
				$select->where("sr.IdProgram = ?",$formdata["IdProgram"]);
			}
			
			if(isset($formdata["IdSemester"]) && $formdata["IdSemester"]!=''){
				$select->where("sa.sa_idSemester = ?",$formdata["IdSemester"]);
			}
						
			if(isset($formdata["status"]) && $formdata["status"]!=''){
				$select->where("sa.sa_status = ?",$formdata["status"]);
			}
			
			if(isset($formdata["Student"]) && $formdata["Student"]!=''){
				$select->where("((sp.appl_fname  LIKE '%".$formdata["Student"]."%'");
		 		$select->orwhere("sp.appl_mname  LIKE '%".$formdata["Student"]."%'");
		 		$select->orwhere("sp.appl_lname  LIKE '%".$formdata["Student"]."%')");
		 		$select->orwhere("sr.registrationId  LIKE '%".$formdata["Student"]."%')");
			}
		}
		
		//echo $select;
		$rows = $db->fetchAll($select);
		
		return $rows;
	}
	
	
	public function getData($id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
					  ->from(array('sa'=>$this->_name))					 
					  ->joinLeft(array('sm'=>'tbl_staffmaster'),'sm.IdStaff=sa.sa_dosen_penilai',array('dosen_penilai'=>'FullName'))
					  ->where("sa_id = ?",$id);
		
		//echo $select;
		$row = $db->fetchRow($select);
		
		return $row;
		
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	
	public function addAppealMark($data) {
		$db = Zend_Db_Table::getDefaultAdapter();		
		$db->insert($this->_appeal_mark, $data);
	}
	
	
	public function getAppealMark($id,$idMaster,$idDetails=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
					  ->from(array('sam'=>$this->_appeal_mark))	
					  ->where("sam.sam_sa_id = ?",$id)
					  ->where("sam_idComponent = ?",$idMaster);
					  
		if(isset($idDetails) && $idDetails!=''){
			$select->where("sam.sam_idItem = ?",$idDetails);
		}
		
		//echo $select;
		$row = $db->fetchRow($select);
		
		return $row;
		
	}
	
	public function updateAppealMark($data,$id){
		$db = Zend_Db_Table::getDefaultAdapter();	
		$db->update($this->_appeal_mark,$data,'sam_id = '. (int)$id);
		
	}
	
	
	
}

?>