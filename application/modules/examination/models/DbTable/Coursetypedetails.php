<?php
class Examination_Model_DbTable_Coursetypedetails extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_gradesetup';

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function fnGetCourseTypeList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a'=>'tbl_coursetype'),array("key"=>"a.IdCourseType","value"=>"a.CourseType"))
		->where('a.Active = 1')
		->order("a.CourseType");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetSubProgramList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a'=>'tbl_program'),array("key"=>"a.IdProgram","value"=>"a.ProgramName"))
		->where('a.Active = 1')
		->order("a.ProgramName");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function fnGetSubjectNameList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a'=>'tbl_subjectmaster'),array("key"=>"a.IdSubject","value"=>"a.SubjectName"))
		->where('a.Active = 1')
		->order("a.SubjectName");
		//echo $lstrSelect;die();
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

	public function fndeleteCourseTypeDetails($IdCourseTypeDetails) {  // function to update po details
		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_coursetypedetails";
		$larramounts = array('deleteFlag'=>1);
		$where ='IdCourseTypeDetails = '.$IdCourseTypeDetails;
		$db->update($table,$larramounts,$where);
	}

	public function fngetCourseTypeDetails() { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("ct"=>"tbl_coursetype"))
		->joinLeft(array("ctd"=>"tbl_coursetypedetails"),'ct.IdCourseType= ctd.IdCourseType',array("ctd.IdCourseType AS IdCourseTypeDtls","ctd.ComponentName"))
		->where('ct.Active = 1')
		->group("ct.CourseType")
		->order("ct.CourseType");
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

	public function fnSearchCourseTypeDetails($post = array()) { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("ct"=>"tbl_coursetype"))
		->where('ct.Bahasaindonesia   like "%" ? "%"',$post['field3']);
			
		if(isset($post['field5']) && !empty($post['field5']) ){
			$lstrSelect = $lstrSelect->where("ct.IdCourseType = ?",$post['field5']);

		}

		$lstrSelect	->order("ct.CourseType");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnViewCourseTypeDetails($IdCourseType) { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("ctd"=>"tbl_coursetypedetails"),array("ctd.IdCourseTypeDetails","ctd.ComponentName","ctd.Description","ctd.Active"))
		->where("ctd.IdCourseType = ?",$IdCourseType)
		->where("ctd.deleteFlag = 0");

		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function fnCopySearchGradeSetUpDetails($CopyFromIdProgram,$CopyFromIdSemester) { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("gs"=>"tbl_gradesetup"))
		->where("gs.IdSemester = ?",$CopyFromIdSemester)
		->where("gs.IdProgram = ?",$CopyFromIdProgram);

		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	/*public function fnCopyUpdateGradeSetUpDetails($CopyToIdSemester,$IdGradeSetUp) { //Function for updating the user
	 //print_r($IdGradeSetUp);die();
	$where ='IdGradeSetUp = '.$IdGradeSetUp;
	$larrcourse = array('IdSemester'=>$CopyToIdSemester
	);
	$this->update($larrcourse,$where);
	}
	*/

	public function fnCopyAddGradeSetUpDetails($CopyToIdProgram,$CopyToIdSemester,$larrresultUpdate) {  // function to insert po details
		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_gradesetup";
		$larrGradeSetUpDetails = array('IdProgram'=>$CopyToIdProgram,
				'IdSemester'=>$CopyToIdSemester,
				'BasedOn'=>$larrresultUpdate['BasedOn'],
				'EffectiveDate'=>$larrresultUpdate['EffectiveDate'],
				'GradeDesc'=>$larrresultUpdate['GradeDesc'],
				'GradePoint'=>$larrresultUpdate['GradePoint'],
				'MinPoint'=>$larrresultUpdate['MinPoint'],
				'MaxPoint'=>$larrresultUpdate['MaxPoint'],
				'Group'=>$larrresultUpdate['Group'],
				'Rank'=>$larrresultUpdate['Rank'],
				'Pass'=>$larrresultUpdate['Pass'],
				'DescEnglishName'=>$larrresultUpdate['DescEnglishName'],
				'DescArabicName'=>$larrresultUpdate['DescArabicName'],
				'deleteFlag'=>$larrresultUpdate['deleteFlag'],
				'Active'=>$larrresultUpdate['Active'],
				'UpdDate'=>$larrresultUpdate['UpdDate'],
				'UpdUser'=>$larrresultUpdate['UpdUser']
		);

		$db->insert($table,$larrGradeSetUpDetails);

	}

	/*public function fnAddGradeSetUp($larrformData) { //Function for adding the user details to the table
		$this->insert($larrformData);
	}*/


	public function fnAddCourseTypeDetails($larrformData) { //Function for adding the user details to the table
		$db = Zend_Db_Table::getDefaultAdapter();
		//unset($larrformData['CopyIdProgram']);
		$table = "tbl_coursetypedetails";
		$countvar=count($larrformData['ComponentNamegrid']);
		for($i=0;$i<$countvar;$i++) {
			if($larrformData['Activegrid'][$i] == 'Active'){
				$active = 1;
			}else{
				$active = 0;
			}
			$larrAddCourseType = array('IdCourseType'=>$larrformData['IdCourseType'],
					'ComponentName'=>$larrformData['ComponentNamegrid'][$i],
					'Description'=>$larrformData['Descriptiongrid'][$i],
					'deleteFlag'=>0,
					'Active'=>$active,
					'UpdDate'=>$larrformData['UpdDate'],
					'UpdUser'=>$larrformData['UpdUser']
			);

			$db->insert($table,$larrAddCourseType);
		}


	}


	public function fnviewGradeSetUp($linIdGradeSetUp) { //Function for the view user
		//echo $lintidepartment;die();
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
		->from(array("a" => "tbl_gradesetup"),array("a.*"))
		->where("a.IdGradeSetUp = ?",$linIdGradeSetUp);
		return $result = $lobjDbAdpt->fetchRow($select);
	}

	public function fnupdateGradeSetUp($lintiIdGradeSetUp,$larrformData) { //Function for updating the user
		$where = 'IdGradeSetUp = '.$lintiIdGradeSetUp;
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