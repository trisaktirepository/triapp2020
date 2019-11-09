<?php 
class Examination_Model_DbTable_Subjectsmarksverification extends Zend_Db_Table_Abstract
{
	protected $_name = 'tbl_verifiermarks';
	private $lobjDbAdpt;

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}


	public function fnSearchStudentsubjects($post = array()) { //Function to get the user details
		$auth = Zend_Auth::getInstance();
		$lintidstaff =$auth->getIdentity()->IdStaff;
		$consistantresult = 'SELECT MAX(i.IdStudentRegistration)  from tbl_studentregistration i where i.IdApplication = b.IdApplication';

		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a"=>"tbl_subjectmarksentry"),array('a.idSubjectMarksEntry','a.subjectmarks'))
		->join(array('c'=>'tbl_staffmaster'),'a.idStaff = c.IdStaff',array('c.IdStaff','c.FirstName as stafffname','c.SecondName as staffsecondname'))
		->join(array('e'=>'tbl_subjectmaster'),'a.idSubject = e.IdSubject',array('e.IdSubject','e.SubjectName'))
		->join(array('g'=>'tbl_studentregistration'),'a.IdStudentRegistration  = g.IdStudentRegistration',array('g.IdStudentRegistration','g.registrationId','g.IdSemester','g.IdSemestersyllabus'))
		->join(array('b'=>'tbl_studentapplication'),'g.IdApplication = b.IdApplication',array('b.FName','b.MName','b.LName','b.IdApplication'))
		->join(array('f'=>'tbl_landscape'),'g.IdLandscape = f.IdLandscape',array('f.LandscapeType','f.IdStartSemester','f.SemsterCount'))
		->join(array('d'=>'tbl_program'),'f.IdProgram = d.IdProgram',array('d.IdProgram','d.ProgramName'))
		->joinLeft(array('m'=>'tbl_verifiermarks'),'a.idSubjectMarksEntry = m.idSubjectMarksEntry',array('m.idVerifierMarks','m.verifiresubjectmarks'))
		->joinLeft(array('h'=>'tbl_marksentrysetup'),'a.idSubject = h.IdSubject AND h.Verification = 1 AND m.idverifier = h.IdStaff',array('h.IdMarksEntrySetup','h.Rank'))
		->joinLeft(array('i'=>'tbl_staffmaster'),'h.IdStaff = i.IdStaff',array('i.IdStaff as verifierstaffid','i.FirstName as verifierstafffname','i.SecondName as verifierstaffsecondname'))
		->join(array('k'=>'tbl_marksdistributiondetails'),'a.IdMarksDistributionDetails = k.IdMarksDistributionDetails',array('k.IdMarksDistributionDetails','k.ComponentName','k.PassMark','k.TotalMark','k.Weightage'))
		->join(array('j'=>'tbl_marksdistributionmaster'),'k.IdMarksDistributionMaster = j.IdMarksDistributionMaster',array('j.IdMarksDistributionMaster','j.Name'))
		->where("g.IdStudentRegistration = ?",new Zend_Db_Expr('('.$consistantresult.')'))
		->where('b.FName like "%" ? "%"',$post['field4'])
		->where('g.IdSemester like "%" ? "%"',$post['field2'])
		->where("b.Offered = 1")
		->where("b.Termination = 0")
		->where("b.Accepted = 1")
		->where("f.Active = 123")
		//->order("a.IdMarksDistributionDetails")
		->order("b.IdApplication")
		->order("j.IdMarksDistributionMaster")
		->order("k.IdMarksDistributionDetails");
			
		if(isset($post['field1']) && !empty($post['field1']) ){
			$lstrSelect = $lstrSelect->where("e.IdSubject = ?",$post['field1']);
		}
			
		if(isset($post['field8']) && !empty($post['field8']) ){
			$lstrSelect = $lstrSelect->where("d.IdProgram = ?",$post['field8']);
		}
		//echo $lstrSelect;
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fngetSubjectNameCombo()
	{
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrselect = $lobjDbAdpt->select()
		->from(array("pg"=>"tbl_subjectmaster"),array("pg.IdSubject AS key","pg.SubjectName AS value"))
		->where("pg.Active = 1")
		->order("pg.SubjectName");
		$larrresult = $lobjDbAdpt->fetchAll($lstrselect);
		return $larrresult;
	}

	public function fnAddSubjectVerifiedMarks($formData) { //Function for adding the Program Branch details to the table
		unset ( $formData ['Save']);
		echo "<pre>";
		print_r($formData);
		exit;
		return $this->insert($formData);
	}


	public function fnDeleteSubjectVerifiedmarksMarks($larrformDatas) { //Function for Delete Purchase order terms
		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_verifiermarks";

		if($larrformDatas['idverifier'] != "") {
			$where[] = $db->quoteInto('idSubjectMarksEntry  = ?', $larrformDatas['idSubjectMarksEntry']);
			$where[] = $db->quoteInto('idverifier  = ?', $larrformDatas['idverifier']);
		} else {
			$where[] = $db->quoteInto('idSubjectMarksEntry  = ?', $larrformDatas['idSubjectMarksEntry']);
		}

		$db->delete($table,$where);
	}

	public function fnUpdateSubjectVerifiedMarks($idVerifierMarks,$verifiresubjectmarks){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$UpdateData["verifiresubjectmarks"] = $verifiresubjectmarks;
		$lstrTable = "tbl_verifiermarks";
		$lstrWhere = "idVerifierMarks = ".$idVerifierMarks;
		$lstrMsg = $lobjDbAdpt->update($lstrTable,$UpdateData,$lstrWhere);
		return $lstrMsg;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function fnUpdateSubjectMarks($larrformDatasUpdate,$idSubjectMarksEntry) {  // function to update po details
		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_subjectmarksentry";
		$where = "idSubjectMarksEntry = '".$idSubjectMarksEntry."'";
		$db->update($table,$larrformDatasUpdate,$where);
	}


}
?>