<?php 

class App_Model_Exam_DbTable_MarksDepositHeader extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_marks_deposit_header';
	protected $_primary = "id"; 
	

	 
	
	
	public function getData($idprogram,$idsem,$idsubject){
		
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
					  	->from(array('sa'=>$this->_name))	
					  	->join(array('sm'=>'tbl_subjectmaster'),'sa.IdSubjectDeposit=sm.IdSubject')				 
					  
					  	->where("sa.IdProgram = ?",$idprogram)
						->where("sa.IdSemesterMain=?",$idsem)
						->where("sa.IdSubject=?",$idsubject);
		
		//echo $select;
		$row = $db->fetchRow($select);
		
		return $row;
		
		
	}
	
	public function getDataByDepositSubject($idprogram,$idsem,$idsubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('sa'=>$this->_name))
		->join(array('sm'=>'tbl_subjectmaster'),'sa.IdSubjectDeposit=sm.IdSubject')
			
		->where("sa.IdProgram = ?",$idprogram)
		->where("sa.IdSemesterMain=?",$idsem)
		->where("sa.IdSubjectDeposit=?",$idsubject);
	
		//echo $select;
		$row = $db->fetchRow($select);
	
		return $row;
	
	}
	
	public function getHead($idprogram,$idsem,$idsubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('sa'=>$this->_name))
		->join(array('sm'=>'tbl_subjectmaster'),'sa.IdSubjectDeposit=sm.IdSubject')
			
		->where("sa.IdProgram = ?",$idprogram)
		->where("sa.IdSemesterMain=?",$idsem)
		->where("sa.IdSubjectDeposit=?",$idsubject);
	
		//echo $select;
		$row = $db->fetchRow($select);
	
		return $row;
	
	}
	public function getDataAllSubject($idprogram,$idsemester){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('sa'=>$this->_name))
		->join(array('sm'=>'tbl_subjectmaster'),'sa.IdSubjectDeposit=sm.IdSubject')
		->where("sa.IdProgram = ?",$idprogram)
		->group('sm.IdSubject');
	
		//echo $select;
		$row = $db->fetchAll($select);
	
		return $row;
	
	}
	public function getDataAll($idprogram,$idsubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('sa'=>$this->_name))
		->join(array('sm'=>'tbl_subjectmaster'),'sa.IdSubject=sm.IdSubject')
		->join(array('mdm'=>'tbl_marksdistributionmaster'),'mdm.IdHeader=sa.id and mdm.IdCourse=sa.IdSubjectDeposit and mdm.IdProgram=sa.IdProgram')
		->join(array('s'=>'tbl_examination_assessment_type'),'s.IdExaminationAssessmentType=mdm.IdComponentType',array('componentname'=>'DescriptionDefaultlang'))
		->where("sa.IdProgram = ?",$idprogram)
		->where("sa.IdSubjectDeposit = ?",$idsubject)
		->group('sa.IdSubject')
		->group('mdm.IdMarksDistributionMaster')
		->order('sa.IdSubject')
		->order('mdm.OrderComponent');
	
		//echo $select;
		$row = $db->fetchAll($select);
	
		return $row;
	
	}
	public function getDataById($idhead){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('sa'=>$this->_name))
			
		->where("sa.id = ?",$idhead) ;
	
		//echo $select;
		$row = $db->fetchRow($select);
	
		return $row;
	
	}
	 
	public function getDataComponent($idSemester, $idProgram, $idBranch, $idSubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('sa'=>$this->_name))
		->join(array('p'=>'tbl_marksdistributionmaster'),'sa.IdMarkRule=p.Rule')
		->join(array('s'=>'tbl_examination_assessment_type'),'s.IdExaminationAssessmentType=p.IdComponentType',array('componentname'=>'DescriptionDefaultlang'))
		->where('Active="1"')
		->where("sa.IdSemesterMain = ?",$idSemester)
		->where("sa.IdProgram = ?",$idProgram)
		->where("sa.IdSubject = ?",$idSubject);
	
		//echo $select;
		$row = $db->fetchAll($select);
	
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