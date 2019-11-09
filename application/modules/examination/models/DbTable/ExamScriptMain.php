<?php 

class Examination_Model_DbTable_ExamScriptMain extends Zend_Db_Table_Abstract {
	
	protected $_name = 'exam_script_main';
	protected $_primary = "IdScript";
	

	public function getData($idScript){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('eg'=>$this->_name))
		->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=eg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'BahasaIndonesia','faculty_id'=>'IdFaculty'))
		->joinLeft(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=eg.IdSemesterMain',array('semester_name'=>'SemesterMainName'))
		->join(array('eat'=>'tbl_examination_assessment_type'), 'eat.IdExaminationAssessmentType = eg.exam_type', array('eg_exam_name'=>'DescriptionDefaultlang'))
		->where('eg.IdScript = ?',$idScript);
		$row = $db->fetchRow($select);
		
		return $row;
	}
	
	 
	
	public function getExamScriptList($idSemester=null,$idprogram=null,$idsubject=null,$examtype=null,$idbranch=null,$iddistribution=null){
		
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select() 
					  ->from(array('eg'=>$this->_name))
					   ->joinLeft(array('ep'=>'tbl_subjectmaster'),'eg.IdSubject=ep.IdSubject',array('ShortName','ep.BahasaIndonesia','ep.CreditHours'))
					  ->joinLeft(array('p'=>'tbl_program'),'p.IdProgram=eg.IdProgram',array())
					  ->joinLeft(array('su'=>'tbl_user'),'su.iduser=eg.verificator',array())
					  ->joinLeft(array('st'=>'tbl_staffmaster'),'su.IdStaff=st.IdStaff',array('verificatorname'=>'st.FullName'))
					  ->joinLeft(array('st1'=>'tbl_staffmaster'),'eg.PrintedBy=st1.IdStaff',array('printedbyname'=>'st1.FullName'))
					  	
					  
						;
			if ($idSemester!=null) $select->where('eg.IdSemesterMain = ?',$idSemester);
			if ($idsubject!=null) $select->where('eg.IdSubject = ?',$idsubject)	;	  
			if ($idprogram!=null) $select->where('p.IdProgram = ?',$idprogram)	; 
			if ($examtype!=null) $select->where('eg.exam_type = ?',$examtype)	;
			if ($idbranch!=null) $select->where('eg.IdBranch = ?',$idbranch)	;
			else $select->where('eg.IdBranch = "0"');
			if ($iddistribution!=null) $select->where('eg.IdDistributionMaster = ?',$iddistribution)	;
			
			$row = $db->fetchAll($select);
			//echo var_dump($row);exit;
		 return $row;
	}
	
	public function getExamScriptListByPrint($idSemester=null,$idprogram=null,$idsubject=null,$examtype=null,$idbranch=null,$iddistribution=null){
	
		$session = new Zend_Session_Namespace('sis');
		$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		$idstaff=$auth->getIdentity()->IdStaff;
		
		$select = $db ->select()
		->from(array('eg'=>$this->_name))
		->joinLeft(array('ep'=>'tbl_subjectmaster'),'eg.IdSubject=ep.IdSubject',array('ShortName','ep.BahasaIndonesia','ep.CreditHours'))
		->joinLeft(array('p'=>'tbl_program'),'p.IdProgram=eg.IdProgram',array())
		->joinLeft(array('su'=>'tbl_user'),'su.iduser=eg.verificator',array())
		->joinLeft(array('st'=>'tbl_staffmaster'),'su.IdStaff=st.IdStaff',array('verificatorname'=>'st.FullName'))
		->joinLeft(array('st1'=>'tbl_staffmaster'),'eg.PrintedBy=st1.IdStaff',array('printedbyname'=>'st1.FullName'))
	    ->where('eg.PrintedBy=?',$idstaff);
		
		if ($idSemester!=0 ) $select->where('eg.IdSemesterMain = ?',$idSemester);
		if ($idsubject!=0) $select->where('eg.IdSubject = ?',$idsubject)	;
		if ($idprogram!=0) $select->where('p.IdProgram = ?',$idprogram)	;
		if ($examtype!=0) $select->where('eg.exam_type = ?',$examtype)	;
		if ($idbranch!=0) $select->where('eg.IdBranch = ?',$idbranch)	;
		else $select->where('eg.IdBranch = "0"');
		if ($iddistribution!=0) $select->where('eg.IdDistributionMaster = ?',$iddistribution)	;
			
		$row = $db->fetchAll($select);
		//echo var_dump($row);exit;
		return $row;
	}
	
	 
	 
	public function insertData($data=array()){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($this->_name,$data);
		return $db->lastInsertId();;
	}
	
	public function updateData($data=array(),$idscript){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		return $db->update($this->_name,$data,'IdScript='.(int)$idscript);
	}
	
	public function deleteData($idscript){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		return $db->delete($this->_name,'IdScript='.(int)$idscript);
	}
	public function fnGetNameList($name) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = ?',$name)
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		//echo $select;exit;
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	
	
}