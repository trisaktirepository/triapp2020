<?php
class GeneralSetup_Model_DbTable_FinalAssignment extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_finalAssignment';
	protected $_primary='idFinalAssignment';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function fnGetStudentProfileByIdStudentRegistration($IdStudentRegistration){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("sp"=>"student_profile"))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdApplication = sp.appl_id', array('registrationId','IdProgram','IdLandscape'))
		->where('sr.IdStudentRegistration = ?', $IdStudentRegistration);
		 
		 
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	public function fnGetProgramNameList() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_program'), array("key" => "a.IdProgram", "value" => "CONCAT_WS('-',a.ProgramName,a.ProgramCode)","name" => "CONCAT_WS('-',a.ProgramName,a.ProgramCode)"))
		->where('a.Active = 1')
		->order("a.ProgramName");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetSemesterNameList() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_semestermaster'), array("key" => "a.IdSemesterMaster", "value" => "a.SemesterMainName", "name" => "a.SemesterMainName"))
		//->join(array('b' => 'tbl_semestermaster'), 'a.Semester = b.IdSemesterMaster ')
		//->where('a.Active = 1');
		//->where('b.Active = 1')
		->order("a.SemesterMainName");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetFinalAssigmentStd($idstd) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a' =>$this->_name))
		->where('a.idStudentRegistration = ?',$idstd);
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		//echo var_dump($larrResult);
		//exit;
		///$larrResult=$larrResult[0];
		return $larrResult;
	}
	
	public function fnAddFinalAssigmentStd($data) {
		$db = Zend_Db_Table::getDefaultAdapter();		
		$db->insert($this->_name, $data);
	}
	public function fnDelFinalAssigmentStd($idFinal) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$db->delete($this->_primary .' = '. (int)$idFinal);
	}
	public function fnUpdateFinalAssigmentStd($data,$idFinal) {
		 $db = Zend_Db_Table::getDefaultAdapter();
		 $where = 'idFinalAssignment = '. $idFinal;
		 $this->update($data,$where );
	}
	public function getListAcademicStaff($idCollege=null) {
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$select = $db->select()
		->from(array("sm"=>"tbl_staffmaster"),array("key"=>"sm.IdStaff","value"=>"CONCAT_WS('-',sm.fullName,sm.StaffId)","name"=>"CONCAT('-',sm.fullName,sm.StaffId)"))
		->where("Active = 1")
		->where("StaffAcademic = 0")
		->order('sm.FullName');
	
		if($idCollege){
			$select->where("IdCollege = ?",$idCollege);
		}
	
		//echo $select;
		$row = $db->fetchAll($select);
	
		return $row;
	}
	public function getlandscapesubjects($pid,$final_lid){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a"=>"tbl_landscapesubject"),array("key"=>"IdSubject","value"=>"CONCAT_WS('-',b.subcode,b.BahasaIndonesia)","name"=>"CONCAT_WS('-',b.subcode,b.BahasaIndonesia)"))
		->join(array("b"=>"tbl_subjectmaster"),"a.IdSubject=b.IdSubject")
		->where("a.IdProgram = ?",$pid)
		->where("a.IdLandscape = ?",$final_lid);
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		if ($larrResult==array()) {
			$lstrSelect = $lobjDbAdpt->select()
			->from(array("a"=>"tbl_landscapeblocksubject"),array("key"=>"subjectid","value"=>"CONCAT_WS('-',b.subcode,b.BahasaIndonesia)","name"=>"CONCAT_WS('-',b.subcode,b.BahasaIndonesia)"))
			->join(array("b"=>"tbl_subjectmaster"),"a.subjectid=b.IdSubject")
			//->where("a.IdProgram = ?",$pid)
			->where("a.IdLandscape = ?",$final_lid);
			$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		}
		return $larrResult;
	}


	

	

	
	
}