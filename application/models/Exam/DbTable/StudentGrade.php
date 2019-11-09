<?php

class App_Model_Exam_DbTable_StudentGrade extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_student_grade';
	protected $_primary = "sg_id";
		
	public function getGrade($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Student Grade");
		}			
		return $row->toArray();
	}
	
	
	function getGradebySemester($IdStudentRegistration,$idstudentsemsterstatus,$genarray=0){
		
		$select = $this->select()
					   ->from($this->_name)
					   ->where('sg_IdStudentRegistration = ?',$IdStudentRegistration)
					   ->where('sg_idstudentsemsterstatus = ?',(int)$idstudentsemsterstatus);
		$rowSet = $this->fetchRow($select);
		if($genarray==0){
			return $rowSet;
		}else{
			if(!$rowSet){
				return false;
			}else{			
				return $rowSet->toArray();		
			}
		}
	}
	
    function getStudentGradeInfo($IdStudentRegistration){
		
		 $select = $this->select()
					    ->from(array('sg'=>$this->_name))	
					   ->where('sg_IdStudentRegistration = ?',$IdStudentRegistration)
					   ->join(array('sss'=>'tbl_studentsemesterstatus'),'sss.idstudentsemsterstatus=sg.sg_idstudentsemsterstatus',array())	
					   ->order('sss.Level DESC');
		
		return $rowSet = $this->fetchRow($select);
		
	}
	
	function getLastStudentGradeInfo($IdStudentRegistration){
	
		$select = $this->select()
		->from(array('sg'=>$this->_name))
		
		->join(array('sss'=>'tbl_studentsemesterstatus'),'sss.idstudentsemsterstatus=sg.sg_idstudentsemsterstatus',array())
		->where('sg_IdStudentRegistration = ?',$IdStudentRegistration)
		->where('sg_cgpa is not null')
		->order('sss.Level DESC');
	
		return $rowSet = $this->fetchRow($select);
	
	}
	
}

