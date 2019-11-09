<?php 
class Examination_Model_DbTable_Studentattandance extends Zend_Db_Table_Abstract
{
	protected $_name = 'tbl_studentattandance';

	private $lobjDbAdpt;

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}


	public function fnSearchStudentAttandance($post = array()) { //Function to get the user details

		$auth = Zend_Auth::getInstance();
		$lintidstaff =$auth->getIdentity()->IdStaff;
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('b'=>'tbl_studentregistration'),array('b.IdStudentRegistration','b.registrationId','b.IdSemester','b.IdSemestersyllabus'))
		->join(array('f'=>'tbl_studentapplication'),'b.IdApplication = f.IdApplication',array('f.FName','f.MName','f.LName','f.IdApplication'))
		->join(array('s'=>'tbl_semester'),'b.IdSemester = s.IdSemester',array('s.IdSemester','s.ShortName'))
		->where('s.ShortName like "%" ? "%"',$post['field2']);
			
		if(isset($post['field1']) && !empty($post['field1']) ){
			$lstrSelect = $lstrSelect->where("b.IdApplication = ?",$post['field1']);
		}

		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}



	public function fngetStudentNameCombo()
	{
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrselect = $lobjDbAdpt->select()
		->from(array("sr"=>"tbl_studentregistration"),array("sr.IdApplication AS key"))
		->join(array('sa'=>'tbl_studentapplication'),'sr.IdApplication = sa.IdApplication',array("sa.FName AS value"))
		->order("sa.FName");
		$larrresult = $lobjDbAdpt->fetchAll($lstrselect);
		return $larrresult;
	}

	public function fnAddStudentAttandance($formData) { //Function for adding the Program Branch details to the table
		unset ( $formData ['Save']);

		$this->insert($formData);
	}

	public function fnDeleteSubjectMarks($larrformDatas) { //Function for Delete Purchase order terms
		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_subjectmarksentry";
		//$where = $db->quoteInto('idApplication  = ?', $larrformDatas['idApplication'] AND 'idStaff  = ?', $larrformDatas['idStaff'] AND 'idProgram  = ?', $larrformDatas['idProgram'] AND 'idSubject  = ?', $larrformDatas['idSubject'] AND 'idsemster  = ?', $larrformDatas['idsemster']);
			
			
		$where[] = $db->quoteInto('idApplication  = ?', $larrformDatas['idApplication']);
		$where[] = $db->quoteInto('idStaff  = ?', $larrformDatas['idStaff']);
		$where[] = $db->quoteInto('idLandscape  = ?', $larrformDatas['idLandscape']);
		$where[] = $db->quoteInto('idProgram  = ?', $larrformDatas['idProgram']);
		$where[] = $db->quoteInto('idSubject  = ?', $larrformDatas['idSubject']);
		$where[] = $db->quoteInto('idsemster  = ?', $larrformDatas['idsemster']);
		$db->delete($table,$where);
	}

}
?>