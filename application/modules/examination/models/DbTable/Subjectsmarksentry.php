<?php 
class Examination_Model_DbTable_Subjectsmarksentry extends Zend_Db_Table_Abstract
{
	protected $_name = 'tbl_subjectmarksentry';
	private $lobjDbAdpt;

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}


	public function fnSearchStudentsubjects($post = array()) { //Function to get the user details
		$auth = Zend_Auth::getInstance();
		$lintidstaff =$auth->getIdentity()->IdStaff;
		$consistantresult = 'SELECT MAX(i.IdStudentRegistration)  from tbl_studentregistration i where i.IdApplication = f.IdApplication';
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a"=>"tbl_studentregsubjects"),array("a.IdSubject"))
		->join(array('b'=>'tbl_studentregistration'),'a.IdStudentRegistration  = b.IdStudentRegistration',array('b.IdStudentRegistration','b.registrationId','b.IdSemester','b.IdSemestersyllabus'))
		->join(array('c'=>'tbl_landscape'),'b.IdLandscape   = c.IdLandscape',array('c.IdLandscape','c.LandscapeType','c.IdStartSemester','c.SemsterCount'))
		->join(array('d'=>'tbl_program'),'c.IdProgram = d.IdProgram',array('d.IdProgram','d.ProgramName'))
		->join(array('e'=>'tbl_subjectmaster'),'a.IdSubject = e.IdSubject',array('e.IdSubject',"CONCAT_WS(' - ',IFNULL(e.SubjectName,''),IFNULL(e.SubCode,'')) AS SubjectName",'e.SubCode'))
		->join(array('f'=>'tbl_studentapplication'),'b.IdApplication = f.IdApplication',array('f.FName','f.MName','f.LName','f.IdApplication'))
		->join(array('g'=>'tbl_subjectcoordinatorlist'),'e.IdSubject = g.IdSubject',array())
		->join(array('h'=>'tbl_staffmaster'),'g.IdStaff = h.IdStaff',array('h.IdStaff','h.FirstName','h.SecondName'))
		->join(array('j'=>'tbl_marksdistributionmaster'),'a.IdSubject = j.IdCourse AND c.IdProgram=j.IdProgram',array('j.IdMarksDistributionMaster','j.Name'))
		->join(array('k'=>'tbl_marksdistributiondetails'),'j.IdMarksDistributionMaster = k.IdMarksDistributionMaster AND k.idStaff="'.$lintidstaff.'" AND k.idSemester=b.IdSemestersyllabus',array('k.IdMarksDistributionDetails','k.ComponentName','k.PassMark','k.TotalMark','k.Weightage'))
		->join(array('x'=>'tbl_semester'),'b.IdSemestersyllabus = x.IdSemester',array('x.year'))
		->joinLeft(array('i'=>'tbl_subjectmarksentry'),'b.IdStudentRegistration = i.IdStudentRegistration AND g.IdStaff = i.idStaff AND a.IdSubject=i.idSubject AND k.IdMarksDistributionDetails = i.IdMarksDistributionDetails',array('i.idSubjectMarksEntry','i.subjectmarks'))
		->joinLeft(array('s'=>'tbl_disciplinaryaction'),'f.IdApplication = s.IdApplication AND s.Approve=1',array('s.IdDisciplinaryAction'))
		->joinLeft(array('t'=>'tbl_disciplinaryactiondetails'),'s.IdDisciplinaryAction  = t.IdDisciplinaryAction AND a.IdSubject = t.idSubject AND b.IdSemestersyllabus = t.idSemester',array('t.idSubject as dispidsubject'))
		->where("b.IdStudentRegistration = ?",new Zend_Db_Expr('('.$consistantresult.')'))
		->where('g.IdStaff = ?',$lintidstaff)
		->where('f.FName like "%" ? "%"',$post['field4'])
		->where('b.IdSemester like "%" ? "%"',$post['field2'])
		->where("f.Offered = 1")
		->where("f.Termination = 0")
		->where("f.Accepted = 1")
		->where("c.Active = 123")
		->order("f.IdApplication")
		->order("j.IdMarksDistributionMaster")
		->order("k.IdMarksDistributionDetails");
			
			
		if(isset($post['field1']) && !empty($post['field1']) ){
			$lstrSelect = $lstrSelect->where("e.IdSubject = ?",$post['field1']);
		}
			
		if(isset($post['field8']) && !empty($post['field8']) ){
			$lstrSelect = $lstrSelect->where("d.IdProgram = ?",$post['field8']);

		}
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function fngetSubjectNameCombo()
	{
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrselect = $lobjDbAdpt->select()
		->from(array("a"=>"tbl_subjectmaster"),array("key"=>"a.IdSubject","value"=>"CONCAT_WS(' - ',IFNULL(a.SubjectName,''),IFNULL(a.SubCode,''))"))
		->where("a.Active = 1")
		->order("a.SubjectName");
		$larrresult = $lobjDbAdpt->fetchAll($lstrselect);
		return $larrresult;
	}

	public function fnAddSubjectMarks($formData) { //Function for adding the Program Branch details to the table
		unset ( $formData ['Save']);
		return $this->insert($formData);
	}

	public function fnDeleteSubjectMarks($larrformDatas) { //Function for Delete Purchase order terms
		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_subjectmarksentry";
		$where[] = $db->quoteInto('IdStudentRegistration  = ?', $larrformDatas['IdStudentRegistration']);
		$where[] = $db->quoteInto('idStaff  = ?', $larrformDatas['idStaff']);
		$where[] = $db->quoteInto('idSubject  = ?', $larrformDatas['idSubject']);
		$db->delete($table,$where);
	}

	public function fnGPACalculation($IdStudentRegistration,$idSubject,$subjectmarks) { //Function to get the user details
		$auth = Zend_Auth::getInstance();
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a"=>"tbl_studentregsubjects"),array("a.IdSubject"))
		->join(array('b'=>'tbl_studentregistration'),'a.IdStudentRegistration  = b.IdStudentRegistration',array('b.IdStudentRegistration','b.registrationId','b.IdSemestersyllabus'))
		->join(array('c'=>'tbl_landscape'),'b.IdLandscape   = c.IdLandscape',array('c.IdLandscape','c.LandscapeType','c.IdStartSemester','c.SemsterCount'))
		->join(array('d'=>'tbl_program'),'c.IdProgram = d.IdProgram',array('d.IdProgram','d.ProgramName'))
		->join(array('e'=>'tbl_subjectmaster'),'a.IdSubject = e.IdSubject',array('e.IdSubject','e.SubjectName','e.CreditHours'))
		->join(array('f'=>'tbl_studentapplication'),'b.IdApplication = f.IdApplication',array('f.FName','f.MName','f.LName','f.IdApplication'))
		->joinLeft(array('i'=>'tbl_subjectmarksentry'),'b.IdStudentRegistration = i.IdStudentRegistration AND a.IdSubject=i.idSubject',array('i.idSubjectMarksEntry','i.subjectmarks'))
		->join(array('gs'=>'tbl_gradesetup'),'gs.IdProgram = c.IdProgram  AND gs.IdSemester = b.IdSemestersyllabus AND gs.IdSubject = a.IdSubject',array('gs.GradePoint','gs.IdSemester'))
		->where('a.IdStudentRegistration = ?',$IdStudentRegistration)
		->where('a.IdSubject = ?',$idSubject)
		->where($subjectmarks .' '."BETWEEN gs.MinPoint and gs.MaxPoint")
		->where("f.Offered = 1")
		->where("f.Termination = 0")
		->where("f.Accepted = 1")
		->where("c.Active = 123")
		->order("e.SubjectName")
		->group("a.IdSubject");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}


	public function fnAddGPACalculationDtls($arrIdStudentRegistration,$IdSemester,$sumGradePoint,$gpa,$ldtsystemDate,$UpdUser){
		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_gpacalculation";
		$countvar=count($arrIdStudentRegistration);
		$larrAddGPA = array('GradePoint'=>$sumGradePoint,
				'IdStudentRegistration'=>$arrIdStudentRegistration,
				'Gpa'=>$gpa,
				'IdSemester'=>$IdSemester,
				'UpdDate'=>$ldtsystemDate,
				'UpdUser'=>$UpdUser
		);

		$db->insert($table,$larrAddGPA);
	}

	public function fnUpdateSubjectMarks($idSubjectMarksEntry,$subjectmarks){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$UpdateData["subjectmarks"] = $subjectmarks;
		$lstrTable = "tbl_subjectmarksentry";
		$lstrWhere = "idSubjectMarksEntry = ".$idSubjectMarksEntry;
		$lstrMsg = $lobjDbAdpt->update($lstrTable,$UpdateData,$lstrWhere);
		return $lstrMsg;
	}

}
?>