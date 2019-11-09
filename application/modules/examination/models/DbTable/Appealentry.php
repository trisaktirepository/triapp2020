<?php
class Examination_Model_DbTable_Appealentry extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_appealmarksentry';

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function fnGetAppeal() { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
		->from(array("a" => "tbl_appeal"),array("a.*"))
		->join(array("b" => "tbl_subjectmaster"),'a.IdSubject  = b.IdSubject')
		->join(array("c" => "tbl_marksdistributionmaster"),'a.IdMarksDistributionMaster = c.IdMarksDistributionMaster')
		->join(array("d" => "tbl_marksdistributiondetails"),'a.IdMarksDistributionDetails = d.IdMarksDistributionDetails');
		return $result = $lobjDbAdpt->fetchAll($select);
	}

	public function fnAddMarksEntryAppeal($larrformData) {
		$this->insert($larrformData);
	}

	public function fnEditMarksEntryAppeal($idAppeal) { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
		->from(array("a" => "tbl_appealmarksentry"),array("a.*"))
		->where("a.IdAppeal  = ?",$idAppeal);
		return $result = $lobjDbAdpt->fetchRow($select);
	}

	public function fnupdateMarksEntryAppeal($lintIdAppealEntry,$larrformData) { //Function for updating the user
		$where = 'IdAppealEntry = '.$lintIdAppealEntry;
		$this->update($larrformData,$where);
	}

	public function fnupdateVerifierMarks($lintnewmarks,$lintidverifiermarks) { //Function for updating the user

		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_verifiermarks";
		$larrformdata['verifiresubjectmarks']=$lintnewmarks;
		$where = "idVerifierMarks = '".$lintidverifiermarks."'";
		$db->update($table,$larrformdata,$where);
	}

	public function fnSearchAppeal($post = array()) { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
		->from(array("a" => "tbl_appeal"),array("a.*"))
		->join(array("b" => "tbl_subjectmaster"),'a.IdSubject  = b.IdSubject')
		->join(array("c" => "tbl_marksdistributionmaster"),'a.IdMarksDistributionMaster = c.IdMarksDistributionMaster')
		->join(array("d" => "tbl_marksdistributiondetails"),'a.IdMarksDistributionDetails = d.IdMarksDistributionDetails')
		->where('a.AppealCode like  "%" ? "%"',$post['field3'])
		->where('b.SubjectName like  "%" ? "%"',$post['field2']);
		return $result = $lobjDbAdpt->fetchAll($select);
	}
}