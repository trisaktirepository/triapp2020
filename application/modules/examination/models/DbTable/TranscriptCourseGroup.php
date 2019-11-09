<?php
class Examination_Model_DbTable_TranscriptCourseGroup extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'transcript_course_group';

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function fngetProgramDetails()
	{
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('i' =>'tbl_program'),array('i.IdProgram','i.ProgramName','i.ArabicName'))
		->where('i.Active = 1');
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}


	public function fnViewSubjectregistrationlist($idProgram){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('sr' =>'tbl_subjectregistration'),array('sr.*'))
		->join(array('p'=>'tbl_program'),'sr.IdProgram = p.IdProgram',array('p.ProgramName','p.IdProgram'))
		->join(array('d'=>'tbl_definationms'),'sr.TerminateStatus = d.idDefinition',array('d.DefinitionCode'))
		->where('sr.IdProgram=?',$idProgram)
		->where('sr.Active = 1');
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

	public function fnSearchProgramDetails($post) { //Function to get the Program Branch details
		$field7 = "i.Active = ".$post["field7"];

		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('i' => 'tbl_program'),array('i.IdProgram','i.ProgramName','i.ArabicName'))
		->where('i.ArabicName  like "%" ? "%"',$post['field2'])
		// ->where("i.IdProgram  = ?",$post['field5'])
		->where($field7);
		if(isset($post['field5']) && !empty($post['field5']) ){
			$select = $select->where("i.IdProgram = ?",$post['field5']);
		}
		//	echo $select;
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


	public function fnDeleteSubjectregistrationpolicy($idProgram){

		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_subjectregistration";
		$where = $db->quoteInto('IdProgram = ?', $idProgram);
		$db->delete($table, $where);

	}

	public function fnaddSubjectregistrationpolicy($larrformData) { //Function for adding the University details to the table
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			
		$count = count($larrformData['IdProgramgrid']);
			
		for($i = 0 ;$i<$count ; $i++) {

			$larrFormdatainsert = array('IdProgram'=>$larrformData['IdProgram'],
					'EffectiveDate'=>date('Y-m-d',strtotime($larrformData['EffectiveDategrid'][$i])),
					'AcademicStatus'=>$larrformData['AcademicStatusIdgrid'][$i],
					'TerminateStatus'=>$larrformData['TerminateStatusIdgrid'][$i],
					'Semester'=>$larrformData['Semestergrid'][$i],
					'VirtualSemester'=>$larrformData['VirtualSemestergrid'][$i],
					'Min'=>$larrformData['Mingrid'][$i],
					'Max'=>$larrformData['Maxgrid'][$i],
					'Subjects'=>$larrformData['Subjectsgrid'][$i],
					'UpdUser'=>$larrformData['UpdUser'],
					'UpdDate'=>$larrformData['UpdDate'],
					'Active'=>1,
			);

			$this->insert($larrFormdatainsert);
		}
	}












}