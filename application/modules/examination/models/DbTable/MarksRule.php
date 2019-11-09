<?php 

class Examination_Model_DbTable_MarksRule extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_marks_rule';
	protected $_primary = "IdMarkRule"; 
	

	 
	
	
	public function getData($id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
					  ->from(array('sa'=>$this->_name))					 
					  ->join(array('p'=>'tbl_program'),'p.IdProgram=sa.IdProgram')
						->where('Active="1"')
					  ->where("IdMarkRule = ?",$id);
		
		//echo $select;
		$row = $db->fetchRow($select);
		
		return $row;
		
	}
	 
	public function getDataComponent($idSemester, $idProgram, $idBranch, $idSubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('sa'=>$this->_name))
		->join(array('p'=>'tbl_marksdistributionmaster'),'sa.IdSemesterMain=p.semester and sa.IdSubject=p.IdCourse and sa.IdProgram=p.IdProgram')
		->join(array('s'=>'tbl_examination_assessment_type'),'s.IdExaminationAssessmentType=p.IdComponentType',array('componentname'=>'DescriptionDefaultlang'))
		->where('Active="1"')
		->where("sa.IdSemesterMain = ?",$idSemester)
		->where("sa.IdProgram = ?",$idProgram)
		->where("sa.IdSubject = ?",$idSubject);
	
		//echo $select;
		$row = $db->fetchAll($select);
		//echo var_dump($row);exit;
		return $row;
	
	}
	public function getDataByProgram($id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('sa'=>$this->_name))
		->join(array('p'=>'tbl_program'),'p.IdProgram=sa.IdProgram')
		->where('sa.Active="1"')
		->where("sa.IdProgram = ?",$id);
	
		//echo $select;
		$row = $db->fetchAll($select);
	
		return $row;
	
	}
	public function isIn($sem,$prog,$branch,$course){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('sa'=>$this->_name)) 
		->where('sa.Active="1"')
		->where("sa.IdSemesterMain = ?",$sem) 
		->where("sa.IdProgram = ?",$prog);
		
		if ($branch!=null) $select->where("sa.IdBranch = ?",$branch);
		if ($course!=null) $select->where("sa.IdSubject = ?",$course);
		//echo $select;
		$row = $db->fetchRow($select);
	
		return $row;
	
	}
	 
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function addData($data){
		return $this->insert($data);
	}
	 
	 
	
	
	
}

?>