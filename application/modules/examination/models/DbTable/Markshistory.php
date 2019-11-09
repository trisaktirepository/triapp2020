<?php
class Examination_Model_DbTable_Markshistory extends Zend_Db_Table_Abstract {
	protected $_name = 'tbl_marks_distribution_history';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function addMarksHistory($historyArray){
		$this->lobjDbAdpt->insert('tbl_marks_distribution_history',$historyArray);
		return true;
	}

	public function getHistory($id){
		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_marks_distribution_history"), array('a.*'))
		->where("a.IdMarksDistributionMaster =?",$id);
		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;
	}

	public function getAllHistory(){
		$select = $this->lobjDbAdpt->select()
		->from(array("mhist" => "tbl_marks_distribution_history"), array('mhist.*'))
		->joinLeft(array('mrksdis' => 'tbl_marksdistributionmaster'),'mhist.IdMarksDistributionMaster = mrksdis.IdMarksDistributionMaster',array('mrksdis.IdScheme','mrksdis.IdFaculty','mrksdis.IdProgram','mrksdis.IdCourse','mrksdis.semester'))
		->joinLeft(array('scm' => 'tbl_scheme'),'mrksdis.IdScheme = scm.IdScheme',array('scm.EnglishDescription AS schemename'))
		->joinLeft(array('fac' => 'tbl_collegemaster'),'mrksdis.IdFaculty = fac.IdCollege',array('fac.CollegeName AS facultyname'))
		->joinLeft(array('prg' => 'tbl_program'),'mrksdis.IdProgram = prg.IdProgram',array('prg.ProgramName'))
		->joinLeft(array('course' => 'tbl_subjectmaster'),'mrksdis.IdCourse = course.IdSubject',array('course.SubjectName AS coursename'))
		->joinLeft(array('dfms' => 'tbl_definationms'),'mhist.OldStatus = dfms.idDefinition',array('dfms.DefinitionDesc AS oldstatus'))
		->joinLeft(array('dfm' => 'tbl_definationms'),'mhist.NewStatus = dfm.idDefinition',array('dfm.DefinitionDesc AS newstatus'))
		->joinLeft(array('user' => 'tbl_user'),'mhist.UpdatedBy = user.iduser',array('user.loginName AS user'))
		->order('mhist.IdMarksDistributionMaster');
		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;
	}
}

?>
