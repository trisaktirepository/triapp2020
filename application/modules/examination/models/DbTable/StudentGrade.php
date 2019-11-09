<?php
class Examination_Model_DbTable_StudentGrade extends Zend_Db_Table { 
	
	protected $_name = 'tbl_student_grade';
	protected $_primary = 'sg_id';
	
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
    public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function updateData($data,$id){		
		 $this->update($data, "sg_id = '".(int)$id."'");
	}
	
	public function updateStudentGrade($data,$filters){
		
		$where = 'sg_IdStudentRegistration='.$filters['IdStudentRegistration'].' and sg_semesterId='.$filters['IdSemester'].' and sg_idstudentsemsterstatus='.$filters['idsemesterstatus'];
		$this->update($data, $where);
	}
	public function checkStudent($idStudentReg,$idstudentsemsterstatus){
		$db =  Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
								->from($this->_name)
								->where('sg_IdStudentRegistration = ?',$idStudentReg)
								->where('sg_idstudentsemsterstatus = ?',$idstudentsemsterstatus);
		
		return $row = $db->fetchRow($select);
	}
	

	function getGradebySemester($IdStudentRegistration,$idstudentsemsterstatus,$type=null){
		$db =  Zend_Db_Table::getDefaultAdapter();
		if ($idstudentsemsterstatus=='') $idstudentsemsterstatus=0;
		$select = $db->select()
					   ->from(array('sg'=>$this->_name))
					   ->where('sg_IdStudentRegistration = ?',$IdStudentRegistration);
		
		if ($type==null)
				$select->where('sg_idstudentsemsterstatus = ?',$idstudentsemsterstatus);
		else 
		{
			$select->join(array('sss'=>'tbl_studentsemesterstatus'),'sss.idstudentsemsterstatus=sg.sg_idstudentsemsterstatus');
			$select->where('sss.IdSemesterMain = ?',$idstudentsemsterstatus);
		}
		//echo $select;exit;
		return $rowSet = $db->fetchRow($select);
		
	}
	
	
	function getSemesterEndResult($IdStudentRegistration){
		
		$db =  Zend_Db_Table::getDefaultAdapter();
		
		$select_level = $db->select()
					   ->from('tbl_studentsemesterstatus',array('mx_level'=>'MAX(level)'))					 
					   ->where('IdStudentRegistration = ?',$IdStudentRegistration);
		$level = $db->fetchRow($select_level);
		if (isset($level))	 {		   
			$select = $db->select()
					   ->from(array('sg'=>$this->_name))	
					   ->join(array('sss'=>'tbl_studentsemesterstatus'),'sss.idstudentsemsterstatus=sg.sg_idstudentsemsterstatus',array())			 
					   ->where('sg_IdStudentRegistration = ?',$IdStudentRegistration)
					   ->where('sss.Level = ?',$level["mx_level"]);
					   
		
		return $rowSet = $db->fetchRow($select);
		} else return false;
		
	}
	
	public function getStudentGrade($IdStudentRegistration,$idSemester){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
					   ->from(array('a'=>$this->_name))
					   ->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=a.sg_semesterid',array('year'=>'LEFT(SemesterMainCode,4)','SemesterCountType'))
					   ->where('sg_IdStudentRegistration = ?',$IdStudentRegistration)
					   ->where('sg_semesterId = ?',$idSemester);
		
		return $rowSet = $db->fetchRow($select);
		
	}
	
	
	public function getStudentGradeInfo($IdStudentRegistration){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
		 			    ->from(array('sg'=>$this->_name))	
					    ->join(array('sss'=>'tbl_studentsemesterstatus'),'sss.idstudentsemsterstatus=sg.sg_idstudentsemsterstatus',array('sss.Level'))
					    ->where('sg_IdStudentRegistration = ?',$IdStudentRegistration)
					    ->order('sss.Level DESC');
		$rowSet = $db->fetchRow($select);
		
		//echo $select;exit;
		//exit;
		return $rowSet;
		
	}
	
	public function getCgpaAverage($idsemester,$strata,$intake){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sg'=>$this->_name),array('ipkmax'=>'max(sg_cgpa)','ipkmin'=>'min(sg_cgpa)','ipkavg'=>'avg(sg_cgpa)','std'=>'count(*)'))
		->join(array('sr'=>'tbl_studentregistration'),'sg.sg_idstudentregistration=sr.idstudentregistration',array())
		->join(array('pr'=>'tbl_program'),'sr.IdProgram=pr.IdProgram',array('ProgramCode','ProgramName'=>'ArabicName'))
		->where('sg_semesterid = ?',$idsemester)
		->order('ipkavg desc')
		->group('pr.IdProgram')
		->where('sg_cgpa>0');
		if ($strata!='null') $select->where('pr.strata=?',$strata);
		if ($intake!='null') $select->where('sr.IdIntake=?',$intake);
		$rowSet = $db->fetchAll($select);
		//echo $select;exit;
		//exit;
		return $rowSet;
	
	}
	
	public function getStdGrade($idsemester,$program){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sg'=>$this->_name))
		->join(array('sr'=>'tbl_studentregistration'),'sg.sg_idstudentregistration=sr.idstudentregistration',array())
		->join(array('pr'=>'tbl_program'),'sr.IdProgram=pr.IdProgram',array('ProgramCode','ProgramName'=>'ArabicName'))
		->where('sg_semesterid = ?',$idsemester)
		->where('pr.IdProgram = ?',$program);
		 $rowSet = $db->fetchAll($select);
		//echo $select;exit;
		//exit;
		return $rowSet;
	
	}
	public function getGpaAverage($idsemester,$strata,$intake){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sg'=>$this->_name),array('ipkmax'=>'max(sg_gpa)','ipkmin'=>'min(sg_gpa)','ipkavg'=>'avg(sg_gpa)','std'=>'count(*)'))
		->join(array('sr'=>'tbl_studentregistration'),'sg.sg_idstudentregistration=sr.idstudentregistration',array())
		->join(array('pr'=>'tbl_program'),'sr.IdProgram=pr.IdProgram',array('ProgramCode','ProgramName'=>'ArabicName'))
		->where('sg_semesterid = ?',$idsemester)
		->where('sg_gpa>0')
		->group('pr.IdProgram')
		->order('ipkavg desc');
		if ($strata!='null') $select->where('pr.strata=?',$strata);
		if ($intake!='null') $select->where('sr.IdIntake=?',$intake);
		$rowSet = $db->fetchAll($select);
		//echo $select;exit;
		//exit;
		return $rowSet;
	
	}
	
	public function getRangeofSemester($idstart,$idstop) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
		->from(array('sm'=>'tbl_semestermaster'))
		->where('IdSemesterMaster=?',$idstart);
		$row=$db->fetchRow($sql);
		$datestart=$row['SemesterMainStartDate'];
		$sql = $db->select()
		->from(array('sm'=>'tbl_semestermaster'))
		->where('IdSemesterMaster=?',$idstop);
		$row=$db->fetchRow($sql);
		$datestop=$row['SemesterMainStartDate'];
		$sql = $db->select()
		->from(array('sm'=>'tbl_semestermaster'),array('IdSemesterMaster','SemesterMainName'))
		->where('IsCountable ="1"')
		->where('SemesterMainStartDate >=?',$datestart)
		->where('SemesterMainStartDate <=?',$datestop)
		->order('SemesterMainStartDate ASC');
		return $db->fetchAll($sql);
	}
	
}
?>