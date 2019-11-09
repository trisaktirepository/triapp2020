<?php
class Examination_Model_DbTable_Subjectstaffverification extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_subjectstaffverification';

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function fnAddSubjectStaffVerification($larrformData) { //Function for adding the user details to the table
		$this->insert($larrformData);
	}

	public function fnEditSubjectStaffverify($idSubject) { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
		->from(array("a" => "tbl_subjectstaffverification"),array("a.*"))
		->where("a.IdSubject = ?",$idSubject);
		return $result = $lobjDbAdpt->fetchRow($select);
	}

	public function fnupdateSubjectStaffVerification($lintIdSubjectStaffVerification,$larrformData) { //Function for updating the user
		$where = 'IdSubjectStaffVerification = '.$lintIdSubjectStaffVerification;
		$this->update($larrformData,$where);
	}

	public function fnSearchMarksEntry($post = array()) { //Function for searching the user details
		$db = Zend_Db_Table::getDefaultAdapter();
		$field7 = "sm.Active = ".$post["field7"];
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array("sm"=>"tbl_subjectmaster"),array('sm.*',"CONCAT_WS(' - ',IFNULL(sm.SubjectName,''),IFNULL(sm.SubCode,'')) AS SubjectName","CONCAT_WS(' - ',IFNULL(sm.BahasaIndonesia,''),IFNULL(sm.SubCode,'')) AS BahasaIndonesia"))
		->where('sm.BahasaIndonesia like  "%" ? "%"',$post['field3'])
		->where($field7);
			
		if(isset($post['field5']) && !empty($post['field5']) ){
			$select = $select->where("sm.IdSubject = ?",$post['field5']);

		}
		$select  ->order('sm.SubjectName');
		$result = $this->fetchAll($select);

		return $result->toArray();
	}

	/*public function fngetSemesterNameCombo()
	 {
	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	$lstrselect = $lobjDbAdpt->select()
	->from(array("pg"=>"tbl_semestermaster"),array("pg.IdSemesterMaster AS key","pg.SemesterMasterName AS value"))
	->where("pg.Active = 1")
	->order("pg.SemesterMasterName");
	$larrresult = $lobjDbAdpt->fetchAll($lstrselect);
	return $larrresult;
	}*/



}