<?php
class Examination_Model_DbTable_Gpastudents extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_gpastudents';

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}


	public function fnGetGpaStudentsList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('sr'=>'tbl_studentregistration'),array("sr.IdStudentRegistration","sr.IdSemester AS Sem","sr.IdApplication AS IdAppn"))
		->join(array("stud"=>"tbl_studentapplication"),'sr.IdApplication=stud.IDApplication',array("CONCAT_WS(' ',IFNULL(stud.FName,''),IFNULL(stud.MName,''),IFNULL(stud.LName,'')) as StudentName"))
		->join(array('l' => 'tbl_landscape'),'sr.IdLandscape = l.IdLandscape')
		->join(array('s' => 'tbl_semester'),'l.IdStartSemester = s.IdSemester')
		->join(array('sm' => 'tbl_semestermaster'),'s.Semester = sm.IdSemesterMaster',array("CONCAT_WS(' ',IFNULL(sm.SemesterMainName,''),IFNULL(s.year,'')) AS SemesterName"))
		->where('stud.Active = 1')
		->group("sr.IdStudentRegistration")
		->order("stud.FName");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	/*public function fnGetGpaStudentsList(){
	 $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	$lstrSelect = $lobjDbAdpt->select()
	->from(array('sr'=>'tbl_studentregistration'),array("sr.*"))
	->join(array("stud"=>"tbl_studentapplication"),'sr.IdApplication=stud.IDApplication',array("CONCAT_WS(' ',IFNULL(stud.FName,''),IFNULL(stud.MName,''),IFNULL(stud.LName,'')) as StudentName"))
	->join(array('sm' => 'tbl_semestermaster'),'sr.IdSemester = sm.IdSemesterMaster',array("CONCAT_WS(' ',IFNULL(sm.SemesterMasterName,''),IFNULL(s.year,'')) AS SemesterName"))
	->join(array('s' => 'tbl_semester'),'sm.IdSemesterMaster = s.Semester')
	->where('stud.Active = 1')
	->group("StudentName")
	->order("stud.FName");
	//echo $lstrSelect;die();
	$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
	return $larrResult;
	}*/
	public function fnGetSemester($IdStudentRegistration){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('sr'=>'tbl_studentregistration'),array("sr.IdSemester AS Sem","sr.IdApplication"))
		->join(array("stud"=>"tbl_studentapplication"),'sr.IdApplication=stud.IDApplication',array("CONCAT_WS(' ',IFNULL(stud.FName,''),IFNULL(stud.MName,''),IFNULL(stud.LName,'')) as StudentName"))
		->join(array('l' => 'tbl_landscape'),'sr.IdLandscape = l.IdLandscape')
		->join(array('s' => 'tbl_semester'),'l.IdStartSemester = s.IdSemester')
		->join(array('sm' => 'tbl_semestermaster'),'s.Semester = sm.IdSemesterMaster',array("CONCAT_WS(' ',IFNULL(sm.SemesterMainName,''),IFNULL(s.year,'')) AS SemesterName"))
		->where('stud.Active = 1')
		->where("sr.IdStudentRegistration = ?",$IdStudentRegistration)
		->group("sr.IdStudentRegistration")
		->order("stud.FName");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}

	public function fnSearchGpaStudentDetails($IdApplication){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("gs"=>"tbl_gpastudents"))
		->join(array('sr'=>'tbl_studentregistration'),'gs.IdApplication = sr.IdApplication',array("sr.IdSemester AS Sem"))
		->join(array("stud"=>"tbl_studentapplication"),'sr.IdApplication=stud.IDApplication',array("CONCAT_WS(' ',IFNULL(stud.FName,''),IFNULL(stud.MName,''),IFNULL(stud.LName,'')) as StudentName"))
		->join(array('l' => 'tbl_landscape'),'sr.IdLandscape = l.IdLandscape')
		->join(array('s' => 'tbl_semester'),'l.IdStartSemester = s.IdSemester')
		->join(array('sm' => 'tbl_semestermaster'),'s.Semester = sm.IdSemesterMaster',array("CONCAT_WS(' ',IFNULL(sm.SemesterMainName,''),IFNULL(s.year,'')) AS SemesterName"))
		->where("gs.IdApplication = ?",$IdApplication)
		->where('stud.Active = 1')
		->group("gs.IdGpaStudents")
		->order("stud.FName");
		// echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnviewGpastudents($IdStudentRegistration) { //Function for the view user
		//echo $lintidepartment;die();
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
		->from(array("a" => "tbl_gpastudents"),array("a.*"))
		->where("a.IdStudentRegistration = ?",$IdStudentRegistration);
		return $result = $lobjDbAdpt->fetchRow($select);
	}


	public function fngetStudentslist() { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("stud"=>"tbl_studentapplication"),array("key"=>"sr.IdStudentRegistration","value"=>"CONCAT_WS(' ',IFNULL(stud.FName,''),IFNULL(stud.MName,''),IFNULL(stud.LName,''))"))
		->join(array('sr' => 'tbl_studentregistration'),'stud.IdApplication = sr.IdApplication ')
		->order("stud.FName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetSemesterNameList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a'=>'tbl_semester'),array("key"=>"a.IdSemester","value"=>"CONCAT_WS(' ',IFNULL(b.SemesterMainName,''),IFNULL(a.year,''))"))
		->join(array('b' => 'tbl_semestermaster'),'a.Semester = b.IdSemesterMaster ')
		->where('a.Active = 1')
		->where('b.Active = 1')
		->order("a.year");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	/*public function fngetAcademicStatusDetails() { //Function to get the user details
	 $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	$lstrSelect = $lobjDbAdpt->select()
	->from(array("as"=>"tbl_academicstatus"))
	->join(array('p' => 'tbl_program'),'as.IdProgram = p.IdProgram',array("p.*"))
	->join(array('a'=>'tbl_semester'),'as.IdSemester = a.IdSemester',array("CONCAT_WS(' ',IFNULL(b.SemesterMasterName,''),IFNULL(a.year,'')) as Semester"))
	->join(array('b' => 'tbl_semestermaster'),'a.Semester = b.IdSemesterMaster ')
	->join(array('u' => 'tbl_user'),'as.UpdUser = u.iduser',array("CONCAT_WS(' ',IFNULL(u.fName,''),IFNULL(u.mName,''),IFNULL(u.lName,'')) as UserName"))
	->where('as.Active = 1')
	//echo $lstrSelect;die();
	->order("p.ProgramName");
	//echo $lstrSelect;die();
	$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
	return $larrResult;
	}*/

	public function fngetProgramDetails() { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("p"=>"tbl_program"))
		->where('p.Active = 1')
		->order("p.ProgramName");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fngetChargeslist() { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("cm"=>"tbl_charges"),array("key"=>"cm.IdCharges","value"=>"cm.ChargeName"));
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnSearchGpastudentsDetails($post = array()) { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('sr'=>'tbl_studentregistration'),array("sr.IdSemester AS Sem","sr.IdApplication"))
		->join(array("stud"=>"tbl_studentapplication"),'sr.IdApplication=stud.IDApplication',array("CONCAT_WS(' ',IFNULL(stud.FName,''),IFNULL(stud.MName,''),IFNULL(stud.LName,'')) as StudentName"))
		->join(array('l' => 'tbl_landscape'),'sr.IdLandscape = l.IdLandscape')
		->join(array('s' => 'tbl_semester'),'l.IdStartSemester = s.IdSemester')
		->join(array('sm' => 'tbl_semestermaster'),'s.Semester = sm.IdSemesterMaster',array("CONCAT_WS(' ',IFNULL(sm.SemesterMainName,''),IFNULL(s.year,'')) AS SemesterName"));

		if(isset($post['field5']) && !empty($post['field5']) ){
			$lstrSelect = $lstrSelect->where("sr.IdApplication = ?",$post['field5']);

		}
			
		if(isset($post['field8']) && !empty($post['field8']) ){
			$lstrSelect = $lstrSelect->where("s.IdSemester = ?",$post['field8']);

		}

		$lstrSelect	->where('stud.Active = 1')
		->group("sr.IdStudentRegistration")
		->order("stud.FName");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}


	public function fnAddGpastudents($larrformData) { //Function for adding the user details to the table
		$this->insert($larrformData);
	}



	public function fnupdateGpastudents($lintiIdGpastudents,$larrformData) { //Function for updating the user
		$where = 'IdGpaStudents = '.$lintiIdGpastudents;
		$this->update($larrformData,$where);
	}

	public function fnGetCharges($IdCharges){
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a"=>"tbl_charges"))
		->where("a.IdCharges = ?",$IdCharges);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}

}