<?php
class Examination_Model_DbTable_Assigngrades extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_grade';

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}


	public function fngetSemesterNameCombo()
	{
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("pg"=>"tbl_semester"),array("IdSemester AS key","ShortName AS value"))
		->where("pg.Active = 1")
		->order("pg.ShortName");
			
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}


	public function fngetGradeNameCombo()
	{
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("pg"=>"tbl_gradesetup"),array("pg.IdGradeSetUp","pg.GradeDesc"))
		->where("pg.Active = 1")
		->order("pg.GradeDesc");
			
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnSearchStudentlist($post) { //Function to get the Program Branch details
		$field7 = "a.Active = ".$post["field7"];

		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('e' => 'tbl_studentregistration',array('e.IdStudentRegistration')))
		->join(array('a' => 'tbl_studentapplication'),'e.IdApplication = a.IdApplication',array('a.FName'))
		->join(array('b' => 'tbl_semester'),'e.IdSemester = b.IdSemester',array('b.SemesterCode'))
		->join(array('c' => 'tbl_landscape'),'e.IdLandscape = c.IdLandscape',array('c.IdLandscape','c.SemsterCount'))
		->join(array('i' => 'tbl_program'),'c.IdProgram = i.IdProgram',array('i.IdProgram','i.ProgramName'))
		->join(array('g'=>'tbl_gradesetup'),'g.IdSemester = e.IdSemester',array('g.GradeDesc'))
		->joinLeft(array('k'=>'tbl_landscapeblocksubject'),'c.IdLandscape = k.IdLandscape',array('k.subjectid'))
		->join(array('l'=>'tbl_subjectmaster'),'k.subjectid = l.IdSubject',array('l.IdSubject','l.SubjectName'))
		->joinLeft(array('f'=>'tbl_grade'),'e.IdStudentRegistration = f.IdStudent',array())
		->joinLeft(array('h'=>'tbl_idstudgrade'),'f.IdStudentGrade = h.IdStudentGrade',array('h.IdStudentGrade','h.IdGrade'))
		->where('a.FName  like "%" ? "%"',$post['field3'])
		->where('a.StudentId  like "%" ? "%"',$post['field2'])
		->where("b.IdSemester  = ?",$post['field5'])
		->where($field7);
			
		$result = $this->fetchAll($select);
		return $result->toArray();
	}


	public function fninsertGrades($larrformdata)
	{
		unset($larrformdata['Save']);
		unset($larrformdata['IdGrade']);
		unset($larrformdata['IdSubject']);
			
		$lastinsertid = $this->insert($larrformdata);
		return $lastinsertid;

	}

	public function fninsertGradesSubjects($larrformdata){

		unset($larrformdata['Save']);
		unset($larrformdata['IdStudent']);
		unset($larrformdata['SemesterNumber']);
		unset($larrformdata['UpdUser']);
		unset($larrformdata['UpdDate']);

		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_idstudgrade";
		$db->insert($table,$larrformdata);

	}


	public function fndeleteOldMarks($IdPrograms,$IdApplication)
	{
		$thjdbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrwhere = "IdApplication = ".$IdApplication." AND IdProgram = ".$IdPrograms;

		$thjdbAdpt->delete('tbl_varifiedprogramchecklist',$lstrwhere);
	}

















}