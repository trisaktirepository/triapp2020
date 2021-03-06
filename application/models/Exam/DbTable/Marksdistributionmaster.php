<?php

class App_Model_Exam_DbTable_Marksdistributionmaster extends Zend_Db_Table {

	protected $_name = 'tbl_marksdistributionmaster';
	protected $_primary = 'IdMarksDistributionMaster';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function fnGetMarksDistributionMaster() { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributionmaster"), array("a.MarksApplicationCode","a.IdCourse", "a.IdProgram", "a.IdMarksDistributionMaster", "a.semester","a.IdScheme","a.IdFaculty"))
		->joinLeft(array("b" => 'tbl_subjectmaster'), 'a.IdCourse = b.IdSubject', array('b.SubjectName'))
		->joinLeft(array("c" => 'tbl_collegemaster'), 'a.IdFaculty = c.IdCollege', array('c.CollegeName'))
		->joinLeft(array("d" => 'tbl_program'), 'a.IdProgram = d.IdProgram', array('d.ProgramName','d.ProgramCode'))
		->joinLeft(array("e" => 'tbl_scheme'), 'a.IdScheme = e.IdScheme', array('e.EnglishDescription AS schemename'))
		->joinLeft(array("f" => "tbl_definationms"), 'a.status = f.idDefinition', array('f.DefinitionDesc AS status'))
		//->where("a.Status = 193 OR a.Status = 243 ")
		//->group('a.IdCourse')
		//->group('a.IdProgram')
		//->group('a.semester')
		//->group('a.IdFaculty')
		//->group('a.IdScheme')
		->group('a.MarksApplicationCode');
		;
		/*$auth = Zend_Auth::getInstance();
		$loggeduserId = $auth->getIdentity()->iduser;
		if($loggeduserId!=1){
			$select->where("a.UpdUser =?",$loggeduserId);
		}*/

		return $result = $lobjDbAdpt->fetchAll($select);
	}

	public function fnSearchMarksDistributionMaster($post = array()) { //Function for searching the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributionmaster"), array("a.MarksApplicationCode","a.IdCourse", "a.IdProgram", "a.IdMarksDistributionMaster", "a.semester","a.IdScheme","a.IdFaculty"))
		->joinLeft(array("b" => 'tbl_subjectmaster'), 'a.IdCourse = b.IdSubject', array('b.SubjectName'))
		->joinLeft(array("c" => 'tbl_collegemaster'), 'a.IdFaculty = c.IdCollege', array('c.CollegeName'))
		->joinLeft(array("d" => 'tbl_program'), 'a.IdProgram = d.IdProgram', array('d.ProgramName','d.ProgramCode'))
		->joinLeft(array("e" => 'tbl_scheme'), 'a.IdScheme = e.IdScheme', array('e.EnglishDescription AS schemename'))
		->joinLeft(array("f" => "tbl_definationms"), 'a.status = f.idDefinition', array('f.DefinitionDesc AS status'))
		//->group('a.IdCourse')
		//->group('a.IdProgram')
		//->group('a.semester')
		//->group('a.IdFaculty')
		//->group('a.IdScheme');
		->group('a.MarksApplicationCode');


		if (isset($post['field5']) && !empty($post['field5'])) {
			$select = $select->where("a.IdFaculty = ?", $post['field5']);
		}
		if (isset($post['field23']) && !empty($post['field23'])) {
			$select = $select->where("a.IdScheme = ?", $post['field23']);

		}

		if (isset($post['field24']) && !empty($post['field24'])) {
			$select = $select->where('a.semester like  "%" ? "%"', $post['field24']);
		}

		if (isset($post['field8']) && !empty($post['field8'])) {
			$select = $select->where("a.IdProgram = ?", $post['field8']);
		}

		if (isset($post['field20']) && !empty($post['field20'])) {
			$select = $select->where("b.IdSubject = ?", $post['field20']);
		}
		//echo $select;die();
		//$select->where("a.Status = 193 OR a.Status = 243 ");
		/*$auth = Zend_Auth::getInstance();
		$loggeduserId = $auth->getIdentity()->iduser;
		if($loggeduserId!=1){
			$select->where("a.UpdUser =?",$loggeduserId);
		}*/
		$larrResult = $lobjDbAdpt->fetchAll($select);

		return $larrResult;
	}

	public function fnGetMarksDistributionMasterByCourse($Idcourse,$IdScheme,$IdFaculty,$IdProgram,$appcode) { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributionmaster"), array("a.*"))
		->joinLeft(array("b" => 'tbl_subjectmaster'), 'a.IdCourse = b.IdSubject', array('b.SubjectName'))
		->joinLeft(array("c" => 'tbl_collegemaster'), 'a.IdFaculty = c.IdCollege', array('c.CollegeName'))
		->joinLeft(array("d" => 'tbl_program'), 'a.IdProgram = d.IdProgram', array('d.ProgramName','d.IdProgram as program'))
		->joinLeft(array("e" => 'tbl_scheme'), 'a.IdScheme = e.IdScheme', array('e.EnglishDescription AS schemename'))
		->joinLeft(array("f" => "tbl_definationms"), 'a.status = f.idDefinition', array('f.DefinitionDesc AS status'))
		->joinLeft(array("g" => "tbl_examination_assessment_type"), 'a.IdComponentType = g.IdExaminationAssessmentType', array('g.Description AS component'))
		->joinLeft(array("h" => "tbl_examination_assessment_item"), 'a.IdComponentItem = h.IdExaminationAssessmentType', array('h.Description AS componentitem'))
		->where("a.IdCourse = ?", $Idcourse)
		->where("a.IdScheme = ?", $IdScheme)
		->where("a.IdFaculty = ?", $IdFaculty)
		->where("a.IdProgram = ?", $IdProgram)
		->where("a.MarksApplicationCode = ?", $appcode)
		//->where("a.Status = 193 OR a.Status = 243 ")
		//->group('a.IdComponentType')
		->order('a.IdMarksDistributionMaster');
		return $result = $lobjDbAdpt->fetchAll($select);
	}


	public function fnGetMarksDistributionMasterById($Id) { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributionmaster"), array("a.*"))
		->joinLeft(array("b" => 'tbl_subjectmaster'), 'a.IdCourse = b.IdSubject', array('b.SubjectName','BahasaIndonesia'))
		->joinLeft(array("c" => 'tbl_collegemaster'), 'a.IdFaculty = c.IdCollege', array('c.CollegeName'))
		->joinLeft(array("d" => 'tbl_program'), 'a.IdProgram = d.IdProgram', array('d.ProgramName','d.IdProgram as program','ArabicName'))
		->joinLeft(array("e" => 'tbl_scheme'), 'a.IdScheme = e.IdScheme', array('e.EnglishDescription AS schemename'))
		->joinLeft(array("f" => "tbl_definationms"), 'a.status = f.idDefinition', array('f.DefinitionDesc AS status'))
		->joinLeft(array("g" => "tbl_examination_assessment_type"), 'a.IdComponentType = g.IdExaminationAssessmentType', array('g.Description AS component'))
		->joinLeft(array("h" => "tbl_examination_assessment_item"), 'a.IdComponentItem = h.IdExaminationAssessmentType', array('h.Description AS componentitem'))
		->where("a.IdMarksDistributionMaster = ?", $Id);
		return $result = $lobjDbAdpt->fetchRow($select);
	}


	public function fncheckexistmasterentry($program,$course,$semester,$IdComponentType,$IdComponentItem,$appcode){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributionmaster"), array("a.IdMarksDistributionMaster"))
		->where("a.IdProgram = ?", $program)
		->where("a.IdCourse = ?", $course)
		->where("a.semester = ?", $semester)
		->where("a.IdComponentType = ?", $IdComponentType)
		->where("a.IdComponentItem = ?", $IdComponentItem)
		->where("a.MarksApplicationCode = ?", $appcode)
		->order('a.IdMarksDistributionMaster');
		return $result = $lobjDbAdpt->fetchAll($select);
	}



	public function fnGetSubjectName() { //function to get subject name
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributionmaster"), array("key" => "a.IdMarksDistributionMaster", "value" => "a.Name"))
		->where("a.Active = 1")
		->order("a.Name");

		return $result = $lobjDbAdpt->fetchAll($select);
	}

	public function fnAddMarksDisributionMaster($larrformData) { //Function for adding the user details to the table
		$this->insert($larrformData);
		$insertId = $this->lobjDbAdpt->lastInsertId($this->_name,'IdMarksDistributionMaster');
		return $insertId;
	}

	public function fnupdateMarksDisributionMaster($data,$IdMarksDistributionMaster){
		$where = 'IdMarksDistributionMaster = ' . $IdMarksDistributionMaster;
		$this->update($data, $where);
	}

	public function fncheckduplicateentry($testArray){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributionmaster"), array("a.IdMarksDistributionMaster"));

		if($testArray['course']!=0){
			$select->where("a.IdCourse = ?", $testArray['course']);
		}

		if($testArray['faculty']!=0){
			$select->where("a.IdFaculty = ?", $testArray['faculty']);
		}

		if($testArray['scheme']!=0){
			$select->where("a.IdScheme = ?", $testArray['scheme']);
		}

		if($testArray['program']!=0){
			$select->where("a.IdProgram = ?", $testArray['program']);
		}
		//echo $select;
		//        if($testArray['semester']!=0){
		//            $select->where('a.semester like  "%" ? "%"', $testArray['semester']);
		//        }

		return $result = $lobjDbAdpt->fetchAll($select);
	}

	public function fncheckexistentry($testArray){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributionmaster"), array("a.*"));

		if($testArray['course']!=0){
			$select->where("a.IdCourse = ?", $testArray['course']);
		}

		if($testArray['program']!=0){
			$select->where("a.IdProgram = ?", $testArray['program']);
		}

		if($testArray['semester']!=0){
			$select->where('a.semester like  "%" ? "%"', $testArray['semester']);
		}

		return $result = $lobjDbAdpt->fetchAll($select);
	}







	public function fnEditMarksDisributionMaster($IdMarksDistributionMaster) { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributionmaster"), array("a.*"))
		->where("a.IdMarksDistributionMaster = ?", $IdMarksDistributionMaster);
		return $result = $lobjDbAdpt->fetchRow($select);
	}

	//    public function fnupdateMarksDisributionMaster($IdMarksDistributionMaster, $larrformData) { //Function for updating the user
	//        $where = 'IdMarksDistributionMaster = ' . $IdMarksDistributionMaster;
	//        $this->update($larrformData, $where);
	//    }

	public function fnGetCourseList($IdProgram) { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_landscape"), array("key" => "d.IdSubject", "value" => "CONCAT_WS('-',IFNULL(d.SubjectName,''),IFNULL(d.SubCode,''))"))
		->join(array("b" => "tbl_landscapeblocksubject"), 'a.IdLandscape = b.IdLandscape', array(''))
		->joinLeft(array("d" => "tbl_subjectmaster"), 'b.subjectid = d.IdSubject', array(''))
		->where('a.Active =?',123);
		if($IdProgram != 0){
			$select->where("a.IdProgram = ?", $IdProgram);
		}
		$select->order("d.SubjectName");
		return $result = $lobjDbAdpt->fetchAll($select);
	}

	public function fnGetCourseListsub($IdProgram) { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_landscape"), array("key" => "d.IdSubject", "value" => "CONCAT_WS('-',IFNULL(d.SubjectName,''),IFNULL(d.SubCode,''))"))
		->join(array("c" => "tbl_landscapesubject"), 'a.IdLandscape = c.IdLandscape', array(''))
		->joinLeft(array("d" => "tbl_subjectmaster"), 'c.IdSubject  = d.IdSubject', array(''))
		->where('a.Active =?',123);

		if($IdProgram != 0){
			$select->where("a.IdProgram = ?", $IdProgram);
		}
		$select->order("d.SubjectName");
		return $result = $lobjDbAdpt->fetchAll($select);
	}

	public function fnSearchMarksEntry($post = array()) { //Function for searching the user details
		$db = Zend_Db_Table::getDefaultAdapter();
		$field7 = "a.Active = " . $post["field7"];
		$select = $this->select()
		->setIntegrityCheck(false)
		->join(array('a' => 'tbl_subjectmaster'), 'a.IdSubject = b.IdSubject')
		->where('a.IdSubject = ?', $post['field5'])
		->where('a.BahasaIndonesia like  "%" ? "%"', $post['field3'])
		->where($field7)
		->order('a.SubjectName');
		$result = $this->fetchAll($select);
		return $result->toArray();
	}


	public function fncheckduplicateByCourse($Idcourse){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributionmaster"), array("a.IdMarksDistributionMaster"))
		->where("a.IdCourse = ?", $Idcourse);
		return $result = $lobjDbAdpt->fetchAll($select);
	}


	public function fngetProgramexistinmarkdistribution(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributionmaster"), array(""))
		->joinLeft(array("b" => "tbl_program"),'a.IdProgram = b.IdProgram',array('key' => 'b.IdProgram','value' => 'b.ProgramName'))
		->group('b.ProgramName');
		return $result = $lobjDbAdpt->fetchAll($select);
	}


	public function fngetfrommarksetupCourse(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributionmaster"), array("a.IdCourse"))
		->group('a.IdCourse');
		return $result = $lobjDbAdpt->fetchAll($select);
	}

	public function fngetSemesterexistinmarkdistribution(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributionmaster"), array('key' => 'a.semester','value' => 'a.semester'))
		->group('a.semester');
		return $result = $lobjDbAdpt->fetchAll($select);
	}


	public function fncheckMaxEntry($larrformData) {
		$errMsg  =  '0';  // CAN ADD
		// COUNT Approve
		$appealsql3 =  $this->lobjDbAdpt->select()->from(array("mdm" =>"tbl_marksdistributionmaster"),array('Count(mdm.IdMarksDistributionMaster) as totalApprove'))
		->where("mdm.IdScheme = ?",$larrformData['IdScheme'])
		->where("mdm.IdFaculty = ?",$larrformData['IdFaculty'])
		->where("mdm.IdProgram = ?",$larrformData['IdProgram'])
		->where("mdm.IdCourse = ?",$larrformData['IdCourse'])
		->where("mdm.semester = ?",$larrformData['semestercode'])
		->where(" mdm.Status = 243 OR mdm.Status = 193 ");

		$result3 = $this->lobjDbAdpt->fetchAll($appealsql3);
		$totalApprove =  $result3[0]['totalApprove'];
		if($totalApprove=='0') {
			$errMsg  =  '0';  // CAN ADD
		} else {
			$errMsg  =  '1';  // cannot add
		}
		return $errMsg;
	}



	public function fngetLastRecord() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributionmaster"), array("a.MarksApplicationCode","a.IdMarksDistributionMaster"))
		->order("a.IdMarksDistributionMaster DESC")->limit("1");
		return $result = $lobjDbAdpt->fetchAll($select);
	}
	
	
	public function fngetmarksdistr($semcode,$IdProgram,$IdSubject){		
		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributionmaster"), array("a.*"))
		->where("a.IdProgram = ?",$IdProgram)
		->orwhere("a.IdProgram = ?",0)
		->where("a.IdCourse = ?",$IdSubject)
		->orwhere("a.IdCourse = ?",0)
		->where("a.semester = ?",$semcode);
		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;
	}
	
	public function getMarkId($semcode,$IdProgram,$IdSubject,$idcomponent){
		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributionmaster"), array("a.*"))
		->where("a.IdProgram = ?",$IdProgram)
		->where("a.IdCourse = ?",$IdSubject)
		->where("a.IdComponentType = ?",$idcomponent)
		->where("a.semester = ?",$semcode);
		
		$result = $this->lobjDbAdpt->fetchRow($select);
		return $result;
	}
	public function fngetmarksdistrsubject($semcode,$IdSubject){
		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_marksdistributionmaster"), array("a.*"))
		->join(array('pr'=>'tbl_program'),'a.IdProgram=pr.IdProgram',array('ProgramCode','ProgramName'=>'ArabicName'))
		->joinLeft(array('eat'=>'tbl_examination_assessment_type'),'eat.IdExaminationAssessmentType=a.IdComponentType',array('component_name'=>'DescriptionDefaultlang'))
		->where("a.IdCourse = ?",$IdSubject)
		->where("a.semester = ?",$semcode);
		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;
	}
	
	
	public function checkExistEntry($idSemester,$idProgram,$idSubject,$idBranch=null) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		
		$sql =  $this->lobjDbAdpt->select()->from(array("mdm" =>"tbl_marksdistributionmaster"),array('Count(*) as Total'))
							    ->where("mdm.semester = ?",$idSemester)
								->where("mdm.IdProgram = ?",$idProgram)
								->where("mdm.IdCourse = ?",$idSubject)	
								//->where("mdm.IdBranch = ?",$idBranch)
								->where(" mdm.Status = 243 OR mdm.Status = 193 "); //Entry or approved

		if ($idBranch!=null) $sql->where("mdm.IdCourse = ?",$idSubject);
		
		$result = $this->lobjDbAdpt->fetchRow($sql);
		if ($result) {
			$sql =  $this->lobjDbAdpt->select()->from(array("mdm" =>"tbl_marksdistributionmaster"),array('Count(*) as Total'))
			->where("mdm.semester = ?",$idSemester)
			->where("mdm.IdProgram = ?",$idProgram)
			->where("mdm.IdCourse = ?",$idSubject)
			->where(" mdm.Status = 243 OR mdm.Status = 193 "); //Entry or approved
			 
			$result = $this->lobjDbAdpt->fetchRow($sql);
		}
		return $result['Total'];
	}
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
    public function deleteData($id){
    	//deleted row
      $sql = $this->lobjDbAdpt
      	->select()
      	->from(array('mdm'=>$this->_name))
      	->join(array('nm'=>'tbl_student_marks_entry'),'nm.IdMarksDistributionMaster=mdm.IdMarksDistributionMaster')
      	->where('mdm.IdMarksDistributionMaster =' . (int)$id);
      $result = $this->lobjDbAdpt->fetchRow($sql);
      //delete row
	  if (!$result) $this->delete($this->_primary .' =' . (int)$id);
	  else {
		  //update allow copy to not allow copy for this component in previous semester
		  $where='IdProgram='.$result['IdProgram'].' and IdCourse='.$result['IdCourse'].' and IdComponentType='.$result['IdComponentType'].' and IdComponentItem='.$result['IdComponentItem'];
		  $this->update(array('allow_copy'=>"0"), $where);
	  }
	}
	
	public function getListMainComponent($idSemester,$idProgram,$idSubject,$idBranch=null) {
		
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		
		$sql =  $this->lobjDbAdpt->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))
		->joinLeft(array('eat'=>'tbl_examination_assessment_type'),'eat.IdExaminationAssessmentType=mdm.IdComponentType',array('IdDescription','component_name'=>'DescriptionDefaultlang'))
		->where("mdm.semester = ?",$idSemester)
		->where("mdm.IdProgram = ?",$idProgram)
		->where("mdm.IdCourse = ?",$idSubject)
		
		->where("mdm.Status = 243 OR mdm.Status = 193 ")
		->order('mdm.OrderComponent')
		; //Entry or approved
		if ($idBranch == null || $idBranch=='') $sql->where("mdm.IdBranch = '0' or mdm.IdBranch = ''"); 
		else $sql->where("mdm.IdBranch = ?",$idBranch);
			
		$result = $this->lobjDbAdpt->fetchAll($sql);
		
		if (!$result) {
			
		 	$sql =  $this->lobjDbAdpt->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))
									->joinLeft(array('eat'=>'tbl_examination_assessment_type'),'eat.IdExaminationAssessmentType=mdm.IdComponentType',array('IdDescription','component_name'=>'DescriptionDefaultlang'))
								    ->where("mdm.semester = ?",$idSemester)
									->where("mdm.IdProgram = ?",$idProgram)
									->where("mdm.IdCourse = ?",$idSubject)		
									->where("mdm.IdBranch = '0' or mdm.IdBranch = ''")
									->where("mdm.Status = 243 OR mdm.Status = 193 ")
		 							->order('mdm.OrderComponent')
									;
			$result = $this->lobjDbAdpt->fetchAll($sql);
		
			 if (!$result) {
			
				//get type of semester
				$sql = $this->lobjDbAdpt->select()
				->from('tbl_semestermaster')
				->where('IdSemesterMaster=?',$idSemester);
				$result1 = $this->lobjDbAdpt->fetchRow($sql);
				$iscount=$result1['IsCountable'];
				$semesterdate=$result1['SemesterMainStartDate'];
				//echo var_dump($result);exit;
				//get lastest setup
				$sql =  $this->lobjDbAdpt->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))
				->joinLeft(array('eat'=>'tbl_examination_assessment_type'),'eat.IdExaminationAssessmentType=mdm.IdComponentType',array('IdDescription','component_name'=>'DescriptionDefaultlang'))
				->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=mdm.semester')
				->where("mdm.IdProgram = ?",$idProgram)
				->where("mdm.IdCourse = ?",$idSubject)
				->where('s.IsCountable=?',$iscount)
				->where("mdm.Status = 243 OR mdm.Status = 193 ")
				->order('s.SemesterMainStartDate DESC'); //Entry or approved
				
				if ($idBranch == null || $idBranch == '' ) 	$sql->where("mdm.IdBranch = '0' or mdm.IdBranch = ''");
				$result = $this->lobjDbAdpt->fetchRow($sql);
				
				//echo $result1['SemesterMainStartDate'].'='.$semesterdate;exit;
				if ($result && $result['SemesterMainStartDate']<$semesterdate) {
					//echo var_dump($result);exit;
					$semesterPre=$result["semester"];
					//get previous setup
					$sql =  $this->lobjDbAdpt->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))
					//->joinLeft(array('eat'=>'tbl_examination_assessment_type'),'eat.IdExaminationAssessmentType=mdm.IdComponentType',array('IdDescription','component_name'=>'DescriptionDefaultlang'))
					->where("mdm.semester = ?",$semesterPre)
					->where("mdm.IdProgram = ?",$idProgram)
					->where("mdm.IdCourse = ?",$idSubject)
					->where('mdm.allow_copy="1"')
					->where("mdm.Status = 243 OR mdm.Status = 193 ");
					if ($idBranch == null || $idBranch == '' ) 	$sql->where("mdm.IdBranch = '0' or mdm.IdBranch = ''");
						
				   $result1 = $this->lobjDbAdpt->fetchAll($sql);
				   foreach ($result1 as  $data) {
						//save tto current semester
						$idprev=$data['IdMarksDistributionMaster'];
						unset($data['IdMarksDistributionMaster']);
						$data['semester']=$idSemester;
						$this->lobjDbAdpt->insert('tbl_marksdistributionmaster',$data);
					}
					//get current setup
					$sql =  $this->lobjDbAdpt->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))
					->joinLeft(array('eat'=>'tbl_examination_assessment_type'),'eat.IdExaminationAssessmentType=mdm.IdComponentType',array('IdDescription','component_name'=>'DescriptionDefaultlang'))
					->where("mdm.semester = ?",$idSemester)
					->where("mdm.IdProgram = ?",$idProgram)
					->where("mdm.IdCourse = ?",$idSubject)
					->where("mdm.Status = 243 OR mdm.Status = 193 ");
					if ($idBranch == null || $idBranch=='' ) 	$sql->where("mdm.IdBranch = '0' or mdm.IdBranch = ''");
						 
					$result = $this->lobjDbAdpt->fetchAll($sql);
				
				}
			} 
		}
		return $result;
	}
	
	public function getListMainComponentForSetup($idSemester,$idProgram,$idSubject,$idBranch=null,$delstatus=null) {
	
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	
		$sql =  $this->lobjDbAdpt->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))
		->joinLeft(array('eat'=>'tbl_examination_assessment_type'),'eat.IdExaminationAssessmentType=mdm.IdComponentType',array('IdDescription','component_name'=>'DescriptionDefaultlang'))
		->where("mdm.semester = ?",$idSemester)
		->where("mdm.IdProgram = ?",$idProgram)
		->where("mdm.IdCourse = ?",$idSubject)
		
		->where("mdm.Status = 243 OR mdm.Status = 193 ")
		->order('mdm.OrderComponent')
		; //Entry or approved
		
		if ($idBranch!='') $sql->where("mdm.IdBranch = ?",$idBranch);
		if ($idBranch == null || $idBranch=='' ) 	$sql->where("mdm.IdBranch = '0' or mdm.IdBranch = ''");
		 
		$result = $this->lobjDbAdpt->fetchAll($sql);
		 
		if (!$result && $delstatus!='1') {
				
			    //get type of semester
				$sql = $this->lobjDbAdpt->select()
				->from('tbl_semestermaster')
				->where('IdSemesterMaster=?',$idSemester);
				$result1 = $this->lobjDbAdpt->fetchRow($sql);
				$iscount=$result1['IsCountable'];
				$semesterdate=$result1['SemesterMainStartDate'];
				//echo var_dump($result);exit;
				//get lastest setup
				$sql =  $this->lobjDbAdpt->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))
				->joinLeft(array('eat'=>'tbl_examination_assessment_type'),'eat.IdExaminationAssessmentType=mdm.IdComponentType',array('IdDescription','component_name'=>'DescriptionDefaultlang'))
				->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=mdm.semester')
				->where("mdm.IdProgram = ?",$idProgram)
				->where("mdm.IdCourse = ?",$idSubject)
				->where('s.IsCountable=?',$iscount)
				->where("mdm.Status = 243 OR mdm.Status = 193 ")
				->order('s.SemesterMainStartDate DESC'); //Entry or approved
				if ($idBranch == null || $idBranch=='' ) 	$sql->where("mdm.IdBranch = '0' or mdm.IdBranch = ''");
					
				if ($idBranch!='') $sql->where("mdm.IdBranch = ?",$idBranch);
				$result1 = $this->lobjDbAdpt->fetchRow($sql);
	
				//echo $result1['SemesterMainStartDate'].'='.$semesterdate;exit;
				if ($result1 && $result1['SemesterMainStartDate']<$semesterdate) {
					//echo var_dump($result);exit;
					$semesterPre=$result1["semester"];
					//get previous setup
					$sql =  $this->lobjDbAdpt->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))
					->joinLeft(array('eat'=>'tbl_examination_assessment_type'),'eat.IdExaminationAssessmentType=mdm.IdComponentType',array('IdDescription','component_name'=>'DescriptionDefaultlang'))
					->where("mdm.semester = ?",$semesterPre)
					->where("mdm.IdProgram = ?",$idProgram)
					->where("mdm.IdCourse = ?",$idSubject)
					->where('mdm.allow_copy="1"')
					->where("mdm.Status = 243 OR mdm.Status = 193 ")
					->order('mdm.OrderComponent');
					if ($idBranch!='') $sql->where("mdm.IdBranch = ?",$idBranch);
					if ($idBranch == null || $idBranch=='' ) 	$sql->where("mdm.IdBranch = '0' or mdm.IdBranch = ''");
						
					$result1 = $this->lobjDbAdpt->fetchAll($sql);
					foreach ($result1 as $data) {
						//save tto current semester
						$idprev=$data['IdMarksDistributionMaster'];
						unset($data['IdMarksDistributionMaster']);
						unset($data['IdDescription']);
						unset($data['component_name']);
						$data['semester']=$idSemester;
						$this->lobjDbAdpt->insert('tbl_marksdistributionmaster',$data);
					}
					//get current setup
					$sql =  $this->lobjDbAdpt->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))
					->joinLeft(array('eat'=>'tbl_examination_assessment_type'),'eat.IdExaminationAssessmentType=mdm.IdComponentType',array('IdDescription','component_name'=>'DescriptionDefaultlang'))
					->where("mdm.semester = ?",$idSemester)
					->where("mdm.IdProgram = ?",$idProgram)
					->where("mdm.IdCourse = ?",$idSubject)
					->where("mdm.Status = 243 OR mdm.Status = 193 ")
					->order('mdm.OrderComponent');
					if ($idBranch!='') $sql->where("mdm.IdBranch = ?",$idBranch);
					if ($idBranch == null || $idBranch=='' ) 	$sql->where("mdm.IdBranch = '0' or mdm.IdBranch = ''");
						
					$result = $this->lobjDbAdpt->fetchAll($sql);
	
				}
			
		} 
		return $result;
	}
	
	public function getMarkDistComponent($idSemester,$idProgram,$idSubject) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	
	
		$sql =  $this->lobjDbAdpt->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))
		->joinLeft(array('eat'=>'tbl_examination_assessment_type'),'eat.IdExaminationAssessmentType=mdm.IdComponentType',array())
		->where("mdm.semester = ?",$idSemester)
		->where("mdm.IdProgram = ?",$idProgram)
		->where("mdm.IdCourse = ?",$idSubject)
		->where("mdm.Status = 243 OR mdm.Status = 193 "); //Entry or approved
	
		$result = $this->lobjDbAdpt->fetchAll($sql);
		
		return $result;
	}
	
	public function getTotalMark($idSemester,$idProgram,$idSubject,$idBranch) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		
		$sql =  $this->lobjDbAdpt->select()->from(array("mdm" =>"tbl_marksdistributionmaster"),array('TotalMark'=>'SUM(Marks)'))
								->joinLeft(array('eat'=>'tbl_examination_assessment_type'),'eat.IdExaminationAssessmentType=mdm.IdComponentType',array())
							    ->where("mdm.semester = ?",$idSemester)
								->where("mdm.IdProgram = ?",$idProgram)
								->where("mdm.IdCourse = ?",$idSubject)				
								->where(" mdm.Status = 243 OR mdm.Status = 193 "); //Entry or approved
		
		if ($idBranch!='') $sql->where("mdm.IdBranch = ?",$idBranch);
		if ($idBranch == null || $idBranch=='' ) 	$sql->where("mdm.IdBranch = '0' or mdm.IdBranch = ''");
		
		$result = $this->lobjDbAdpt->fetchRow($sql);
		
		return $result["TotalMark"];
	}
	
	
	
	public function getInfoComponent($id) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		
		$sql =  $this->lobjDbAdpt->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))	
										->joinLeft(array('eat'=>'tbl_examination_assessment_type'),'eat.IdExaminationAssessmentType=mdm.IdComponentType',array('component_name'=>'DescriptionDefaultlang'))							
							    		->where("mdm.IdMarksDistributionMaster = ?",$id);

		$result = $this->lobjDbAdpt->fetchRow($sql);
		
		return $result;
	}
	
	public function getStudentMarkComponent($program_id,$semester_id,$subject_id,$IdStudentRegistration,$IdStudentRegSubjects){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$sql =  $db->select()->from(array("sme" =>"tbl_student_marks_entry"),array('IdMarksDistributionMaster'))														
						     ->where("sme.IdSemester = ?",$semester_id)
							 ->where("sme.Course = ?",$subject_id)
							 ->where("sme.IdStudentRegistration = ?",$IdStudentRegistration)
							 ->where("sme.IdStudentRegSubjects = ?",$IdStudentRegSubjects);
							 
		$sql2 =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))													
						     ->where("mdm.IdProgram = ?",$program_id)
						     ->where("mdm.semester = ?",$semester_id)
							 ->where("mdm.IdCourse = ?",$subject_id)
							 ->where("mdm.IdMarksDistributionMaster NOT IN (?)",$sql);
		
		$row = $db->fetchAll($sql2);
	
		if(count($row)>0){					
			echo ' missing component';
			return false;
		}else{
			echo 'no missing component';
			return true;
		}
		
		
		/*
		 *  SELECT *  
			FROM `tbl_marksdistributionmaster` 
			WHERE `IdProgram` = 4 
			AND `IdCourse` = 2338
			AND `semester` = 1
			AND IdMarksDistributionMaster IN ( 
			SELECT IdMarksDistributionMaster
			FROM `tbl_student_marks_entry` 
			WHERE `IdSemester` LIKE '1' 
			AND `Course` = 2338
			AND `IdStudentRegistration` = 2084
			AND `IdStudentRegSubjects` = 7954 )
		 */
	}
	
	
	


}