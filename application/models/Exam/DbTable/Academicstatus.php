<?php
class App_Model_Exam_DbTable_Academicstatus extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_academicstatus';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function fnGetProgramNameList() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_program'), array("key" => "a.IdProgram", "value" => "CONCAT_WS('-',a.ProgramName,a.ProgramCode)"))
		->where('a.Active = 1')
		->order("a.ProgramName");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetSemesterNameList() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_semester'), array("key" => "a.IdSemester", "value" => "CONCAT_WS(' ',IFNULL(b.SemesterMainName,''))"))
		->join(array('b' => 'tbl_semestermaster'), 'a.Semester = b.IdSemesterMaster ')
		->where('a.Active = 1');
		//->where('b.Active = 1')
		//->order("a.year");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	/* public function fngetAcademicStatusDetails() { //Function to get the user details
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
	 } */

	public function fngetProgramDetails() { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("p" => "tbl_program"))
		->where('p.Active = 1')
		->order("p.ProgramName");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fngetChargeslist() { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("cm" => "tbl_charges"), array("key" => "cm.IdCharges", "value" => "cm.ChargeName"));
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnSearchAcademicStatusDetails($post = array()) { //Function to get the user details
		/*$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		 $lstrSelect = $lobjDbAdpt->select()
		 ->from(array("p" => "tbl_program"))
		 ->joinLeft(array('l' => 'tbl_landscape'), 'p.IdProgram   = l.IdProgram', array('l.IdStartSemester'))
		 ->where('p.ArabicName   like "%" ? "%"', $post['field3']);


		 //echo  $lstrSelect;die();
		 if (isset($post['field5']) && !empty($post['field5'])) {
		 $lstrSelect = $lstrSelect->where("p.IdProgram = ?", $post['field5']);
		 }
		 if (isset($post['field8']) && !empty($post['field8'])) {
		 $lstrSelect = $lstrSelect->where("l.IdStartSemester = ?", $post['field8']);
		 }
		 $lstrSelect->order("p.ProgramName");
		 //echo $lstrSelect;die();
		 $larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		 //echo "<pre>"; print_r($larrResult); die();
		 return $larrResult;*/
		$lobjDbAdpt =  Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a" => "tbl_academicstatus"),array('a.AcademicStatus','a.Maximum','a.Minimum','a.SemesterCode','a.IdAcademicStatus'))
		->joinLeft(array("b" => "tbl_scheme"),'a.IdScheme = b.IdScheme',array('b.EnglishDescription AS schemename'))
		->joinLeft(array("c" =>"tbl_definationms"),'a.IdAward = c.idDefinition',array('c.DefinitionDesc as award'))
		->joinLeft(array('d' => 'tbl_program'),'a.IdProgram = d.IdProgram',array('d.ProgramName'))
		->where("a.Active = ?", 1);

		if (isset($post['field8']) && !empty($post['field8'])) {
			$lstrSelect = $lstrSelect->where("a.SemesterCode = ?", $post['field8']);
		}

		if (isset($post['field20']) && !empty($post['field20'])) {
			$lstrSelect = $lstrSelect->where("a.IdAward = ?", $post['field20']);
		}

		if (isset($post['field27']) && !empty($post['field27'])) {
			$lstrSelect = $lstrSelect->where("a.AcademicStatus = ?", $post['field27']);
		}

		if (isset($post['field10']) && !empty($post['field10'])) {
			$lstrSelect = $lstrSelect->where("a.IdScheme = ?", $post['field10']);
		}

		if (isset($post['field5']) && !empty($post['field5'])) {
			$lstrSelect = $lstrSelect->where("a.IdProgram = ?", $post['field5']);
		}
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnSearchAcademicDetails($idProgram) { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("as" => "tbl_academicstatus"), array("as.IdAcademicStatus", "IF(as.AcademicStatus='0','GPA','CGPA') as AcademicStatus", "as.Minimum", "as.Maximum"))
		->join(array('p' => 'tbl_program'), 'as.IdProgram = p.IdProgram', array("p.*"))
		->join(array('dms' => 'tbl_definationms'), 'as.StatusEnglishName = dms.idDefinition', array("dms.*"))
		->join(array('a' => 'tbl_semester'), 'as.IdSemester = a.IdSemester', array("CONCAT_WS(' ',IFNULL(b.SemesterMainName,'')) as Semester"))
		->join(array('b' => 'tbl_semestermaster'), 'a.Semester = b.IdSemesterMaster')
		->where("as.IdProgram = ?", $idProgram);
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnCopySearchAcademicDetails($CopyFromIdProgram, $CopyFromIdSemester) { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("as" => "tbl_academicstatus"))
		->where("as.IdProgram = ?", $CopyFromIdProgram)
		->where("as.IdSemester = ?", $CopyFromIdSemester);

		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	/* public function fnCopyUpdateAcademicDetails($CopyToIdProgram,$CopyToIdSemester,$IdAcademicStatus) { //Function for updating the user
	 //print_r($IdGradeSetUp);die();
	 $where ='IdAcademicStatus = '.$IdAcademicStatus;
	 $larrcourse = array('IdProgram'=>$CopyToIdProgram,
	 'IdSemester'=>$CopyToIdSemester
	 );
	 $this->update($larrcourse,$where);
	 } */

	public function fnCopyAddAcademicDetails($CopyToIdProgram, $CopyToIdSemester, $larrresultUpdate) {  // function to insert po details
		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_academicstatus";
		$larracademicstatusdtls = array('IdProgram' => $CopyToIdProgram,
				'IdSemester' => $CopyToIdSemester,
				'AcademicStatus' => $larrresultUpdate['AcademicStatus'],
				'Minimum' => $larrresultUpdate['Minimum'],
				'Maximum' => $larrresultUpdate['Maximum'],
				'StatusEnglishName' => $larrresultUpdate['StatusEnglishName'],
				'ClassHonorship' => $larrresultUpdate['ClassHonorship'],
				'StatusArabicName' => $larrresultUpdate['StatusArabicName'],
				'UpdDate' => $larrresultUpdate['UpdDate'],
				'UpdUser' => $larrresultUpdate['UpdUser'],
				'Active' => $larrresultUpdate['Active']
		);

		$db->insert($table, $larracademicstatusdtls);
	}


	public function fnCopyAddademicStatus($larrformData) {

		$table_main = "tbl_academicstatus";
		$table_detail = "tbl_academicstatus_details";
		$larracademicstatusdtls = array(
				'BasedOn' => $larrformData['BasedOn'],
				'IdProgram' => $larrformData['IdProgram'],
			//	'SemesterCode' => $larrformData['SemesterCode'],
		        'IdSemester' => $larrformData['IdSemester'],
				'IdAward' => $larrformData['IdAward'],
				//'MinimumTotalMarks' => $larrformData['MinimumTotalMarks'],
				//'MaximumTotalMarks' => $larrformData['MaximumTotalMarks'],
				'IdScheme' => $larrformData['IdScheme'],
				'AcademicStatus' => $larrformData['AcademicStatus'],
				'UpdDate' => $larrformData['UpdDate'],
				'UpdUser' => $larrformData['UpdUser'],
				'Active' => $larrformData['Active']
		);
		
		$this->lobjDbAdpt->insert($table_main, $larracademicstatusdtls);		
		$lastInsertID  =  $this->lobjDbAdpt->lastinsertid();
		
		$getDetailsData = $this->fnviewAcademicStatusDetails($larrformData['IdAcademicStatus']);
		if(count($getDetailsData)>0){
			foreach($getDetailsData as $values) {
				$larrAddAcademicSetUp = array(
						'IdAcademicStatus'=>$lastInsertID,
						'Minimum'=>$values['Minimum'],
						'Maximum'=>$values['Maximum'],
						'StatusEnglishName'=>$values['StatusEnglishName'],
						'StatusArabicName'=>$values['StatusArabicName']
				);
				$this->lobjDbAdpt->insert($table_detail,$larrAddAcademicSetUp);
			}
		}

	}



	public function fnAddademicStatus($larrformData) {		
		$table_main = "tbl_academicstatus";
		$table_detail = "tbl_academicstatus_details";
		$table_cal_mode = "tbl_gpa_calculation_mode";
		$countvar = count($larrformData['Minimumgrid']);
		$countcalmode = count($larrformData['landscapeTypegrid']);		
		
		
		$larracademicstatusdtls = array(
				'BasedOn' => $larrformData['BasedOn'],
				'IdProgram' => $larrformData['IdProgram'],
				'SemesterCode' => $larrformData['SemesterCode'],
		        'IdSemester' => $larrformData['IdSemester'],
				'IdAward' => $larrformData['IdAward'],
				//'MaximumTotalMarks' => $larrformData['MaximumTotalMarks'],
				//'MinimumTotalMarks' => $larrformData['MinimumTotalMarks'],			
				'IdScheme' => $larrformData['IdScheme'],
				'AcademicStatus' => $larrformData['AcademicStatus'],
				'UpdDate' => $larrformData['UpdDate'],
				'UpdUser' => $larrformData['UpdUser'],
				'Active' => $larrformData['Active']
		);
		
		$this->lobjDbAdpt->insert($table_main, $larracademicstatusdtls);
		$lastInsertID  =  $this->lobjDbAdpt->lastinsertid();


		for($i=0;$i<$countvar;$i++) {
			$larrAddAcademicSetUp = array(
					'IdAcademicStatus'=>$lastInsertID,
					'Minimum'=>$larrformData['Minimumgrid'][$i],
					'Maximum'=>$larrformData['Maximumgrid'][$i],
					//'Gradepoint'=>$larrformData['gradePgrid'][$i],
					//'Gradevalue'=>$larrformData['gradevalgrid'][$i],
					'StatusEnglishName'=>$larrformData['StatusEnglishNamegrid'][$i],
					'StatusArabicName'=>$larrformData['StatusArabicNamegrid'][$i]
			);
			$this->lobjDbAdpt->insert($table_detail,$larrAddAcademicSetUp);
		}
		
		for($j=0;$j<$countcalmode;$j++){
			$larrCalMode = array(
					'IdAcademicStatus'=>$lastInsertID,
					'LandscapeType'=>$larrformData['landscapeTypegrid'][$j],					
					'Level'=>$larrformData['levelgrid'][$j],
					'Semester'=>$larrformData['semestergrid'][$j]					
			);
			$this->lobjDbAdpt->insert($table_cal_mode,$larrCalMode);
		}
	}

	public function checkduplicate($AcademicStatus, $semcode,$idscheme,$award,$id=''){
		$lobjDbAdpt =  Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a" => "tbl_academicstatus"),array('a.*'))
		//->where('a.SemesterCode =?',$semcode)
		->where('a.IdSemester =?',$semcode)
		->where('a.IdScheme =?',$idscheme)
		->where('a.IdAward =?',$award)
		->where('a.AcademicStatus =?',$AcademicStatus)
		->where("a.Active = ?", 1);
		if($id!=''){
			$lstrSelect->where('a.IdAcademicStatus !=?',$id);
		}
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function checkduplicateByprogram($AcademicStatus, $semcode,$idprogram,$id=''){
		
		$lobjDbAdpt =  Zend_Db_Table::getDefaultAdapter();
		
		$lstrSelect = $lobjDbAdpt->select()
								 ->from(array("a" => "tbl_academicstatus"),array('a.*'))								
								 ->where('a.IdSemester =?',$semcode)
								 ->where('a.IdProgram =?',$idprogram)
								 ->where('a.AcademicStatus =?',$AcademicStatus)
								 ->where("a.Active = ?", 1);
		if($id!=''){
			$lstrSelect->where('a.IdAcademicStatus !=?',$id);
		}
		
		
		
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}


	public function fnviewAcademicStatus($lintIdAcademicStatus) { //Function for the view user
		//echo $lintidepartment;die();
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_academicstatus"), array("a.IdAcademicStatus","a.IdProgram","a.SemesterCode",
												"a.IdAward","a.IdScheme","a.Active","a.BasedOn",
												"a.AcademicStatus","a.MinimumTotalMarks","a.MaximumTotalMarks"))
		->where("a.IdAcademicStatus = ?", $lintIdAcademicStatus);
		return $result = $lobjDbAdpt->fetchRow($select);
	}


	public function fnviewAcademicStatusDetails($lintIdAcademicStatus) { //Function for the view user
		//echo $lintidepartment;die();
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_academicstatus_details"), array("a.*"))
		->where("a.IdAcademicStatus = ?", $lintIdAcademicStatus);
		return $result = $lobjDbAdpt->fetchAll($select);
	}
	
	public function fnviewCalMode($lintIdAcademicStatus) { //Function for the view user
		//echo $lintidepartment;die();
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_gpa_calculation_mode"), array("a.*"))
		->joinLeft(array("b" =>"tbl_definationms"),'a.LandscapeType = b.idDefinition',array('b.DefinitionDesc as landscapetype'))
		->where("a.IdAcademicStatus = ?", $lintIdAcademicStatus);
		return $result = $lobjDbAdpt->fetchAll($select);
	}


	public function fnupdateAcademicStatus($lintiIdAcademicStatus, $larrformData) { //Function for updating the user
		$where = 'IdAcademicStatus = ' . $lintiIdAcademicStatus;
		$table_main = "tbl_academicstatus";
		$table_detail = "tbl_academicstatus_details";
		$table_calmode = "tbl_gpa_calculation_mode";
		$countvar = count($larrformData['Minimumgrid']);
		$countcalmode = count($larrformData['landscapeTypegrid']);
		$larracademicstatusdtls = array(
				'BasedOn' => $larrformData['BasedOn'],
				'IdProgram' => $larrformData['IdProgram'],
				'SemesterCode' => $larrformData['SemesterCode'],
		        'IdSemester' => $larrformData['IdSemester'],
				'IdAward' => $larrformData['IdAward'],
				//'MinimumTotalMarks' => $larrformData['MinimumTotalMarks'],
				//'MaximumTotalMarks' => $larrformData['MaximumTotalMarks'],
				'IdScheme' => $larrformData['IdScheme'],
				'AcademicStatus'=>$larrformData['AcademicStatus'],
				'UpdDate' => $larrformData['UpdDate'],
				'UpdUser' => $larrformData['UpdUser'],
				'Active' => $larrformData['Active']
		);
		$this->update($larracademicstatusdtls, $where);


		// DELETE ALL gpacgpasetup List relaed to $lintiIdAcademicStatus
		$where_del = $this->lobjDbAdpt->quoteInto('IdAcademicStatus = ?', $lintiIdAcademicStatus);
		$this->lobjDbAdpt->delete($table_detail, $where_del);

		//asd($larrformData);
		for($i=0;$i<$countvar;$i++) {

			$larrAddAcademicSetUp = array(
					'IdAcademicStatus'=>$lintiIdAcademicStatus,
					'Minimum'=>$larrformData['Minimumgrid'][$i],
					'Maximum'=>$larrformData['Maximumgrid'][$i],
					//'Gradepoint'=>$larrformData['gradePgrid'][$i],
					//'Gradevalue'=>$larrformData['gradevalgrid'][$i],
					'StatusEnglishName'=>$larrformData['StatusEnglishNamegrid'][$i],
					'StatusArabicName'=>$larrformData['StatusArabicNamegrid'][$i]
			);
			$this->lobjDbAdpt->insert($table_detail,$larrAddAcademicSetUp);
		}
		
		
		// DELETE ALL gpacgpasetup List relaed to $lintiIdAcademicStatus
		$where_del_cal = $this->lobjDbAdpt->quoteInto('IdAcademicStatus = ?', $lintiIdAcademicStatus);
		$this->lobjDbAdpt->delete($table_calmode, $where_del_cal);
		
		for($j=0;$j<$countcalmode;$j++) {

			$larrcalmode = array(
					'IdAcademicStatus'=>$lintiIdAcademicStatus,
					'LandscapeType'=>$larrformData['landscapeTypegrid'][$j],
					'Level'=>$larrformData['levelgrid'][$j],
					'Semester'=>$larrformData['semestergrid'][$j]
			);
			$this->lobjDbAdpt->insert($table_calmode,$larrcalmode);
		}
	}

	public function fnGetCharges($IdCharges) {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_charges"))
		->where("a.IdCharges = ?", $IdCharges);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}

	public function getMinimumValue($lintAcademicStatus, $lintIdProgram) { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_academicstatus"), array("max(a.Maximum) as Maximum"))
		->where("a.AcademicStatus= ?", $lintAcademicStatus)
		->where("a.IdProgram= ?", $lintIdProgram);
		return $result = $lobjDbAdpt->fetchRow($select);
	}

	//modify by yatie 24/9/2013
	public function fnGetacademicsetupList() {
		$lobjDbAdpt =  Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a" => "tbl_academicstatus"),array('a.AcademicStatus','a.Maximum','a.Minimum','a.SemesterCode','a.IdAcademicStatus'))
		->joinLeft(array("b" => "tbl_scheme"),'a.IdScheme = b.IdScheme',array('b.EnglishDescription AS schemename'))
		->joinLeft(array("c" =>"tbl_definationms"),'a.IdAward = c.idDefinition',array('c.DefinitionDesc as award'))
		->joinLeft(array('d' => 'tbl_program'),'a.IdProgram = d.IdProgram',array('d.ProgramName','d.ProgramCode'))
		->joinLeft(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=a.IdSemester',array('SemesterMainName'))
		->where("a.Active = ?", 1)->order("a.IdSemester");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetSemesterListInAcademicSetup() {
		$lobjDbAdpt =  Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a" => "tbl_academicstatus"),array())
		->joinLeft(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=a.IdSemester',array('key'=>'IdSemesterMaster','value'=>'SemesterMainName'))
		->where("a.Active = ?", 1);
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}


	public function fngetademicStatus($semcode,$idscheme,$idprogram=''){
		$lobjDbAdpt =  Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a" => "tbl_academicstatus"),array('a.*'))
		->where('a.SemesterCode =?',$semcode)
		->where('a.IdScheme =?',$idscheme)
		->where("a.Active = ?", 1);
		if($idprogram!=''){
			$lstrSelect->where('a.IdProgram =?',$idprogram);
		}
		
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;

	}

	public function fngetasetupByschemeandaward($semcode,$idscheme,$basedon){
		$lobjDbAdpt =  Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a" => "tbl_academicstatus"),array('a.*'))
		//->where('a.SemesterCode =?',$semcode)
		->where('a.IdSemester =?',$semcode)
		->where('a.IdScheme =?',$idscheme)
		->where('a.BasedOn =?',$basedon)
		->where("a.Active = ?", 1);
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;

	}

	public function fngetasetupByprogram($semcode,$idprogram,$basedon){
		$lobjDbAdpt =  Zend_Db_Table::getDefaultAdapter();
		echo $lstrSelect = $lobjDbAdpt->select()
		->from(array("a" => "tbl_academicstatus"),array('a.*'))
		//->where('a.SemesterCode =?',$semcode)
		->where('a.IdSemester =?',$semcode)
		->where('a.IdProgram =?',$idprogram)
		->where('a.BasedOn =?',$basedon)
		->where("a.Active = ?", 1);
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;

	}

	public function fnGetStudentList($post){
		$lobjDbAdpt =  Zend_Db_Table::getDefaultAdapter();
		$studentregsem = array();
		$finalResult = array();
		$i = 0;
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_studentregistration'),array('CONCAT_WS(" ",a.FName,a.MName,a.LName) as Name','a.IdStudentRegistration','a.registrationId'))
		->join(array('b' => 'tbl_program'),'a.IdProgram = b.IdProgram',array('b.ProgramName'))
		->join(array('c' => 'tbl_studentsemesterstatus'),'a.IdStudentRegistration = c.IdStudentRegistration',array('c.idSemester','c.IdSemesterMain'))
		->joinLeft(array('d' => 'tbl_semester'),'c.idSemester = d.IdSemester',array('d.SemesterCode'))
		->joinLeft(array('e' => 'tbl_semestermaster'),'c.IdSemesterMain = e.IdSemesterMaster',array('e.SemesterMainCode'))
		->join(array('f' => 'tbl_gpacalculation'),'a.IdStudentRegistration = f.IdStudentRegistration AND (f.IdSemester = d.SemesterCode OR f.IdSemester = e.SemesterMainCode) AND f.IdProgram = a.IdProgram',array())
		->where('f.Cgpa > ?',0)
		->where('f.Gpa > ?',0)
		->order('c.idstudentsemsterstatus DESC');
		if(isset($post['field2']) && $post['field2'] != ''){
			$wh_condition = "a.FName like '%".$post['field2']."%' OR  a.MName like '%".$post['field2']."%' OR  a.LName like '%".$post['field2']."%' ";
			$select = $select->where($wh_condition);
		}
		if(isset($post['field3']) && $post['field3'] != ''){
			$wh_condition = "a.registrationId like '%".$post['field3']."%'";
			$select = $select->where($wh_condition);
		}
		if(isset($post['field27']) && $post['field27'] != ''){
			$select = $select->where('a.IdProgram = ?',$post['field27']);
		}
		if (isset($post['field10']) && !empty($post['field10'])) {
				
			$semIDs = explode('_', $post['field10']);
			if ($semIDs['1'] == 'main') {
				$idSem = $semIDs['0'];
			}
			if ($semIDs['1'] == 'detail') {
				$idSem = $semIDs['0'];
			}
			$select = $select->where("c.idSemester = ?", $idSem)
							 ->orwhere('c.IdSemesterMain = ?', $idSem);
		}
		$result = $lobjDbAdpt->fetchAll($select);
		if(count($result) != 0){
			foreach($result as $res){
				if(!in_array($res['IdStudentRegistration'], $studentregsem)){
					$studentregsem[] = $res['IdStudentRegistration'];
					$finalResult[$i] = $res;
					$i++;
				}
			}
		}
		return $finalResult;
	}
	
	
	public function fngetademic($semcode,$idscheme,$idprogram=''){
		$larrResult = array();
		$lobjDbAdpt =  Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a" => "tbl_academicstatus"),array('a.*'))
		->joinLeft(array("b" => "tbl_academicstatus_details"),'a.IdAcademicStatus = b.IdAcademicStatus',array('b.*'))
		->where('a.SemesterCode =?',$semcode)
		->where('a.IdScheme =?',$idscheme)
		->where("a.Active = ?", 1);		
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		
		if(empty($larrResult)){
			$lstrSelectnew = $lobjDbAdpt->select()
							->from(array("a" => "tbl_academicstatus"),array('a.*'))
							->joinLeft(array("b" => "tbl_academicstatus_details"),'a.IdAcademicStatus = b.IdAcademicStatus',array('b.*'))
							->where('a.SemesterCode =?',$semcode)
							->where("a.Active = ?", 1);
			$lstrSelectnew->where('a.IdProgram =?',$idprogram);
			$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		}		
		return $larrResult;

	}
	
	public function getAcademicStatus($idSemester,$idProgram,$type=0,$basedon='Program',$point_obtained){
		$lobjDbAdpt =  Zend_Db_Table::getDefaultAdapter();
		$select_grade = $lobjDbAdpt->select()
								->from(array("a" => "tbl_academicstatus"),array('a.*'))	
								->joinLeft(array('ad'=>'tbl_academicstatus_details'),'ad.IdAcademicStatus=a.IdAcademicStatus')
								->where('a.IdSemester =?',$idSemester)
								->where('a.IdProgram =?',$idProgram)
								->where('a.AcademicStatus =? OR a.AcademicStatus = 2',$type)
								->where('a.BasedOn =?',$basedon)
								->where("a.Active = ?", 1);
								
		$select_grade->where("ad.Minimum <= '".$point_obtained."' AND '".$point_obtained."' <= ad.Maximum");
		
		//echo $select_grade;
		$larrResult = $lobjDbAdpt->fetchRow($select_grade);
		
		if(!$larrResult){
			$select_grade = $lobjDbAdpt->select()
									->from(array("a" => "tbl_academicstatus"),array('a.*'))	
									->joinLeft(array('ad'=>'tbl_academicstatus_details'),'ad.IdAcademicStatus=a.IdAcademicStatus')
									->where('a.IdProgram =?',$idProgram)
									->where('a.AcademicStatus =? OR a.AcademicStatus = 2',$type)
									->where('a.BasedOn =?',$basedon)
									->where("a.Active = ?", 1);
									
			$select_grade->where("ad.Minimum <= '".$point_obtained."' AND '".$point_obtained."' <= ad.Maximum");
			
			//echo $select_grade;
			$larrResult = $lobjDbAdpt->fetchRow($select_grade);		
		}
		return $larrResult;

	}
	
	
	public function getListAcademicStatus($idSemester,$idProgram,$type=0,$basedon='Program'){
		$lobjDbAdpt =  Zend_Db_Table::getDefaultAdapter();
		
		$select_grade = $lobjDbAdpt->select()
								->from(array("a" => "tbl_academicstatus"),array('a.*'))	
								->join(array('ad'=>'tbl_academicstatus_details'),'ad.IdAcademicStatus=a.IdAcademicStatus')
								->where('a.IdSemester =?',$idSemester)
								->where('a.IdProgram =?',$idProgram)
								->where('a.AcademicStatus =? OR a.AcademicStatus = 2',$type)
								->where('a.BasedOn =?',$basedon)
								->where("a.Active = ?", 1)
								->group('a.IdAcademicStatus')
								->order('ad.Minimum');
		
		//echo $select_grade;
		$larrResult = $lobjDbAdpt->fetchAll($select_grade);
		
		if(!$larrResult){
			$select_grade = $lobjDbAdpt->select()
									->from(array("a" => "tbl_academicstatus"),array('a.*'))	
									->join(array('ad'=>'tbl_academicstatus_details'),'ad.IdAcademicStatus=a.IdAcademicStatus')
									->where('a.IdProgram =?',$idProgram)
									->where('a.AcademicStatus =? OR a.AcademicStatus = 2',$type)
									->where('a.BasedOn =?',$basedon)
									->where("a.Active = ?", 1)
									->group('a.IdAcademicStatus')
									->order('ad.Minimum');
			
			//echo $select_grade;
			$larrResult = $lobjDbAdpt->fetchAll($select_grade);		
		}
		return $larrResult;

	}
	
}