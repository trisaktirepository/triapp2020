<?php
class Examination_Model_DbTable_Marksdetails extends Zend_Db_Table {
	protected $_name = 'tbl_marksdistributiondetails';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function fnAddMarksDisributionDetail($larrformData) { //Function for adding the user details to the table
		$this->insert($larrformData);
	}

	public function fnGetMarksDistributiondetailById($Idmarksdistributionmaster){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributiondetails"), array("a.IdComponentType","a.IdComponentItem","a.Weightage","a.TotalMark","a.status","a.Percentage"))
		->joinLeft(array("b" => "tbl_definationms"), 'a.status = b.idDefinition', array('b.DefinitionDesc AS status'))
		->joinLeft(array("c" => "tbl_examination_assessment_type"), 'a.IdComponentType = c.IdExaminationAssessmentType', array('c.Description AS component'))
		->joinLeft(array("d" => "tbl_examination_assessment_item"), 'a.IdComponentItem = d.IdExaminationAssessmentType', array('d.Description AS componentitem'))
		->where("a.IdMarksDistributionMaster = ?", $Idmarksdistributionmaster)
		->order("a.IdMarksDistributionMaster");
		return $result = $lobjDbAdpt->fetchAll($select);
	}

	public function fndeleteMarksDistributiondetailById($Idmarksdistributionmaster){
		$thjdbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrwhere = "IdMarksDistributionMaster = ".$Idmarksdistributionmaster;
		$thjdbAdpt->delete('tbl_marksdistributiondetails',$lstrwhere);
	}

	public function fngetentryBymarksmasterId($Idmarksdistributionmaster){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributiondetails"), array("a.*"))
		->where("a.IdMarksDistributionMaster = ?", $Idmarksdistributionmaster);
		return $result = $lobjDbAdpt->fetchAll($select);
	}

	public function fngetstotalmarksofcomponent($Idmarksdistributionmaster){
		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributiondetails"), array("SUM(a.Weightage) as sumweightage"))
		->where("a.IdMarksDistributionMaster = ?", $Idmarksdistributionmaster);
		return $result = $this->lobjDbAdpt->fetchRow($select);
	}
}

?>
