<?php

class  App_Model_Registration_DbTable_Studentregistration extends Zend_Db_Table_Abstract {

	protected $_name = 'tbl_studentregistration';
	private $lobjDbAdpt;

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	public function SearchStudentRegistration($data=null,$nrow=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$setrow="0";
		$sql = $db->select()
		->from(array('sr' => 'tbl_studentregistration'),array('registrationId','IdStudentRegistration','IdProgram','IdLandscape','IdProgramMajoring','idTranscriptProfile'))
		//->join(array('sss'=>'tbl_studentsemesterstatus'),'sss.IdStudentRegistration=sr.IdStudentRegistration',array('idstudentsemsterstatus','Level'))
		->join(array('sp'=>'student_profile'),'sp.appl_id=sr.IdApplication',array('student_name'=>"CONCAT_WS(' ',appl_fname,appl_mname,appl_lname)",'appl_dob'))
		->join(array('pr'=>'tbl_program'),'pr.IdProgram=sr.IdProgram')
		->joinLeft(array('br'=>'tbl_branchofficevenue'),'br.IdBranch=sr.IdBranch')
		//->joinLeft(array('d' => 'tbl_definationms'), "d.idDefinition=sss.studentsemesterstatus", array('studentsemesterstatus'=>"DefinitionDesc"))
		->order('sr.registrationId');
	
		if(isset($data)){
			 
			if( isset($data['IdSemester']) && $data['IdSemester']!=''){
				$sql->where('sss.IdSemesterMain = ?', $data['IdSemester']);
			}
			 
			if(isset($data['IdCollege']) && $data['IdCollege']!=''){
				$sql->where('pr.IdCollege = ?', $data['IdCollege']);
			}
	
			if(isset($data['IdProgram']) && $data['IdProgram']!=''){
				$sql->where('sr.IdProgram = ?', $data['IdProgram']);
			}
			 
			if(isset ($data['IdStudent']) && $data['IdStudent']!=''){
				$sql->where('sr.registrationId = ?', $data['IdStudent']);
				$setrow="1";
				//echo $data['IdStudent'];
				//exit;
			}
			if(isset($data['IdStudentRegistration']) && $data['IdStudentRegistration']!=''){
				$sql->where('sr.idStudentRegistration = ?', $data['IdStudentRegistration']);
				$setrow="1";
			}
			if(isset ($data['IdIntake']) && isset( $data['IdIntake']) && $data['IdIntake']!=''){
				$sql->where('sr.IdIntake = ?', $data['IdIntake']);
			}
	
			if ( isset($data['student_name']) && $data['student_name']!=''){
				$sql->where('sp.appl_fname like "%'. $data['student_name'].'%"');
			}
	
		}
		//echo $sql;exit;
	
		if ($setrow=="1" && $nrow==null)
			$result = $db->fetchRow($sql);
		else
			$result = $db->fetchAll($sql);
	
		return $result;
	}
	public function checkCodeExist($code, $table, $coloumn) {
		$select = $this->lobjDbAdpt->select()
		->from(array('a' => $table), array('a.*'))
		->where("a." . $coloumn . "='" . $code . "'");
		return $result = $this->lobjDbAdpt->fetchAll($select);
	}

	public function fngetStudentApplicationDetails() { //Function to get the Program Branch details
		$select = $this->select()
		->setIntegrityCheck(false)
		->join(array('a' => 'tbl_studentapplication'), array('IdApplication'))
		->join(array('b' => 'tbl_sendoffer'), 'a.IdApplication  = b.IdApplication')
		->where("a.Active = 1")
		->where("b.Approved = 1")
		->where("a.Offered = 1")
		->where("a.Accepted = 1")
		->where("a.Termination = 0")
		->order("a.FName");
		$result = $this->fetchAll($select);
		return $result->toArray();
	}

	public function fnGetApplicantNameList() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("sa" => "tbl_studentapplication"), array("key" => "sa.IdApplication", "value" => "CONCAT_WS(' ',IFNULL(sa.FName,''),IFNULL(sa.MName,''),IFNULL(sa.LName,''))"))
		//->where("sa.Active = 1")
		->where("sa.Status = 197")
		//->where("sa.Accepted = 1")
		//->where("sa.Termination = 0")
		->order("sa.FName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnSearchStudentApplication($post = array()) { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("sa" => "tbl_studentapplication"), array("sa.*"))
		->join(array('b' => 'tbl_sendoffer'), 'sa.IdApplication  = b.IdApplication');


		if (isset($post['field5']) && !empty($post['field5'])) {
			$lstrSelect = $lstrSelect->where("sa.IdApplication = ?", $post['field5']);
		}

		if (isset($post['field8']) && !empty($post['field8'])) {
			$lstrSelect = $lstrSelect->where("sa.IDCourse = ?", $post['field8']);
		}

		$lstrSelect->where('sa.ICNumber like "%" ? "%"', $post['field2'])
		->where('sa.StudentId like "%" ? "%"', $post['field3'])
		->where("b.Approved = 1")
		->where("sa.Offered = 1")
		->where("sa.Accepted = 1")
		->where("sa.Termination = 0")
		->order("sa.FName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function getCompleteStudentDetails($lintidapplicant) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
		->from(array('a' => 'tbl_studentapplication'), array('a.*'))
		->join(array('b' => 'tbl_collegemaster'), 'a.idCollege  = b.IdCollege')
		->join(array('c' => 'tbl_program'), 'a.IDCourse = c.IdProgram')
		->where('a.IdApplication = ?', $lintidapplicant);
		$result = $db->fetchRow($sql);
		return $result;
	}

	public function getSemesterDropDown() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("sa" => "tbl_semestermaster"), array("key" => "b.IdSemester", "value" => "CONCAT_WS(' ',IFNULL(sa.SemesterMasterName,''),IFNULL(b.year,''))"))
		->join(array('b' => 'tbl_semester'), 'sa.IdSemesterMaster = b.Semester')
		->where("b.IdSemester = 1")
		->where("sa.Active = 1");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function getLandscapeDropDown($idprogram) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a" => "tbl_landscape"), array("key" => "a.IdLandscape", "value" => "CONCAT_WS('-',IFNULL(a.ProgramDescription,'-'))"))
		->join(array('b' => 'tbl_definationms'), 'a.LandscapeType = b.idDefinition')
		->where('a.ProgramDescription != ?', '')
		->where('a.IdProgram = ?', $idprogram)
		->where("a.Active = 123");
		//echo $lstrSelect ;
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetLandscapeType($lintidlandscape) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
		->from(array('a' => 'tbl_landscape'), array('a.LandscapeType'))
		->where('a.IdLandscape = ?', $lintidlandscape);
		$result = $db->fetchRow($sql);
		return $result;
	}

	public function fnGetAllSemesterbasedSujectDetails($lintidlandscape, $idsemsyllabus) {

		$consistantresult = "SELECT i.IdSubject  from tbl_subjectsoffered i
							 where i.IdSemester='$idsemsyllabus'";

		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a" => "tbl_landscapesubject"), array("a.*"))
		->join(array('b' => 'tbl_subjectmaster'), 'a.IdSubject = b.IdSubject')
		->where("a.IdSubject IN ?", new Zend_Db_Expr('(' . $consistantresult . ')'))
		->where('a.IdSemester = 1')
		->where('a.IdLandscape = ?', $lintidlandscape);
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetAllBlockbasedSujectDetails($lintidlandscape, $idsemsyllabus) {

		$consistantresult = "SELECT i.IdSubject  from tbl_subjectsoffered i
							 where i.IdSemester='$idsemsyllabus'";

		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a" => "tbl_landscapeblocksemester"), array("a.*"))
		->join(array('b' => 'tbl_landscapeblocksubject'), 'a.blockid = b.blockid')
		->join(array('c' => 'tbl_subjectmaster'), 'b.subjectid = c.IdSubject')
		->where("b.subjectid IN ?", new Zend_Db_Expr('(' . $consistantresult . ')'))
		->where('a.IdLandscape = ?', $lintidlandscape)
		->where('b.IdLandscape = ?', $lintidlandscape)
		->where('a.semesterid = 1');


		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnAddStudentregistration($formData, $lintidapplicant, $lstrpasswd) { //Function for adding the Program Branch details to the table
		unset($formData ['IdProgram']);
		unset($formData ['Save']);
		unset($formData ['subjects']);
		unset($formData ['selectall']);
		$formData['IdSemester'] = 1;
		$formData ['IdApplication'] = $lintidapplicant;
		$formData ['Psswrd'] = $lstrpasswd;
		return $this->insert($formData);
	}

	public function fnUpdateStudentApplication($lintidapplicant) {  // function to update po details
		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_studentapplication";
		$larrapplicant = array('Registered' => '1');
		$where = "IdApplication = '" . $lintidapplicant . "'";
		$db->update($table, $larrapplicant, $where);
	}

	public function fnAddStudentSubjectsperSemester($larrformData, $larrstudentreg) {  // function to insert po details
		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_studentregsubjects";
		$countofsubjects = count($larrformData['subjects']);
		for ($i = 0; $i < $countofsubjects; $i++) {
			$larrSubjects = array('IdStudentRegistration' => $larrstudentreg,
                'IdSubject' => $larrformData['subjects'][$i],
                'UpdDate' => $larrformData['UpdDate'],
                'UpdUser' => $larrformData['UpdUser'],
                'Active' => 1
			);

			$db->insert($table, $larrSubjects);
		}
	}

	function fnGenerateCodes($idUniversity, $idpgm, $IdSemestersyllabus, $page, $IdInserted) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from('tbl_config')
		->where('idUniversity  = ?', $idUniversity);
		$result = $db->fetchRow($select);
		$sepr = $result[$page . 'Separator'];
		$str = $page . "CodeField";
		$CodeText = $page . "CodeText";
		$IdInserted = str_pad($IdInserted, 7, "0", STR_PAD_LEFT);
		for ($i = 1; $i <= 4; $i++) {
			$check = $result[$str . $i];
			$TextCode = $result[$CodeText . $i];
			switch ($check) {
				case 'Year':
					$code = date('Y');
					break;
				case 'Uniqueid':
					$code = $IdInserted;
					break;
				case 'Program':
					$select = $db->select()
					->from('tbl_program')
					->where('IdProgram  = ?', $idpgm);
					$resultCollage = $db->fetchRow($select);
					$code = $resultCollage['ProgramCode'];
					break;
				case 'Semester':
					$select = $db->select()
					->from('tbl_semester')
					->where('IdSemester  = ?', $IdSemestersyllabus);
					$resultCollage = $db->fetchRow($select);
					$code = $resultCollage['SemesterCode'];
					break;
				case 'Text':
					$code = $TextCode;
					break;
				default:
					break;
			}
			if ($i == 1)
			$accCode = $code;
			else
			$accCode .= $sepr . $code;
		}
		return $accCode;
	}

	function fnGenerateCode($idUniversity, $collageId, $page, $IdInserted) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from('tbl_config')
		->where('idUniversity  = ?', $idUniversity);
		$result = $db->fetchRow($select);
		$sepr = $result[$page . 'Separator'];
		$str = $page . "CodeField";
		$CodeText = $page . "CodeText";
		for ($i = 1; $i <= 4; $i++) {
			$check = $result[$str . $i];
			$TextCode = $result[$CodeText . $i];
			switch ($check) {
				case 'Year':
					$code = date('Y');
					break;
				case 'Uniqueid':
					$code = $IdInserted;
					break;
				case 'College':
					$select = $db->select()
					->from('tbl_collegemaster')
					->where('IdCollege  = ?', $collageId);
					$resultCollage = $db->fetchRow($select);
					$code = $resultCollage['ShortName'];
					break;
				case 'University':
					$select = $db->select()
					->from('tbl_universitymaster')
					->where('IdUniversity  = ?', $idUniversity);
					$resultCollage = $db->fetchRow($select);
					$code = $resultCollage['ShortName'];
					break;
				case 'Text':
					$code = $TextCode;
					break;
				default:
					break;
			}
			if ($i == 1)
			$accCode = $code;
			else
			$accCode .= $sepr . $code;
		}
		return $accCode;
	}

	public function fnupdateRegistrationtCode($larrstudentreg, $registrationCode) {
		$larrformData['registrationId'] = $registrationCode;
		$where = 'IdStudentRegistration = ' . $larrstudentreg;
		$this->update($larrformData, $where);
	}

	public function fnCheckExistingSemester($lintidapplicant) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
		->from(array('a' => 'tbl_studentregistration'), array('a.*'))
		->where('a.IdApplication  = ?', $lintidapplicant)
		->where('a.IdSemester = 1');
		$result = $db->fetchRow($sql);
		return $result;
	}

	public function fnGetSemesterNameList($idlandscape) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		/* 		$lstrSelect = $lobjDbAdpt->select()
		 ->from(array('c' => 'tbl_oldlandsacpesemester'),array())
		 ->join(array('a'=>'tbl_semester'),'a.IdSemester = c.semesterid',array("key"=>"a.IdSemester","value"=>"CONCAT_WS(' ',IFNULL(b.SemesterMasterName,''),IFNULL(a.year,''))"))
		 ->join(array('b' => 'tbl_semestermaster'),'a.Semester = b.IdSemesterMaster',array())
		 ->where('c.IdLandscape  = ?',$idlandscape)
		 ->where('a.Active = 1')
		 ->where('b.Active = 1')
		 ->order("a.year"); */

		$lstrSelect = $lobjDbAdpt->select()
		// ->from(array('c' => 'tbl_oldlandsacpesemester'),array())
		->from(array('a' => 'tbl_semester'), array("key" => "a.IdSemester", "value" => "CONCAT_WS(' ',IFNULL(b.SemesterMasterName,''),IFNULL(a.year,''))"))
		->join(array('b' => 'tbl_semestermaster'), 'a.Semester = b.IdSemesterMaster', array())
		//->where('c.IdLandscape  = ?',$idlandscape)
		->where('a.Active = 1')
		->where('b.Active = 1')
		->order("a.year");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnaddInvoice($regamount, $result, $IdStartSemester) {

		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_invoicemaster";

		$larrcourse['IdStudent'] = $result;
		$larrcourse['InvoiceNo'] = 1234;
		$larrcourse['InvoiceDt'] = date("Y-m-d");
		$larrcourse['InvoiceAmt'] = $regamount;
		$larrcourse['IdSemester'] = $IdStartSemester;
		$larrcourse['AcdmcPeriod'] = $IdStartSemester;
		$larrcourse['Naration'] = "Narration";
		$larrcourse['UpdDate'] = date("Y-m-d");
		$larrcourse['UpdUser'] = 1;
		$larrcourse['Active'] = 1;
		$larrcourse['Approved'] = 0;
		$larrcourse['idsponsor'] = 0;

		$db->insert($table, $larrcourse);
		$insertId = $db->lastInsertId('tbl_invoicemaster', 'IdInvoice');

		return $insertId;
	}

	public function fnaddInvDetails($regamount, $lastarrInv) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$table2 = "tbl_invoicedetails";

		$larrcourse['IdInvoice'] = $lastarrInv;
		$larrcourse['idAccount'] = 8;
		$larrcourse['Discount'] = 0;
		$larrcourse['Amount'] = $regamount;
		$larrcourse['UpdDate'] = date("Y-m-d");
		$larrcourse['UpdUser'] = 1;
		$larrcourse['Active'] = 1;
		$db->insert($table2, $larrcourse);
	}

	public function getChargeNameDropDown() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("sa" => "tbl_charges"), array("key" => "sa.IdCharges", "value" => "sa.ChargeName"));
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetChargeAmount($lintidcharge) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
		->from(array('a' => 'tbl_charges'), array('a.*'))
		->where('a.IdCharges = ?', $lintidcharge);
		$result = $db->fetchRow($sql);
		return $result;
	}

	public function fnAddStudentCharges($larrformData, $larrstudentreg) {  // function to insert po details
		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_studentregcharges";

		$countofsubjects = count($larrformData['IdCharge']);
		for ($i = 0; $i < $countofsubjects; $i++) {
			$larrcharges = array('IdStudentRegistration' => $larrstudentreg,
                'IdCharge' => $larrformData['IdCharge'][$i],
                'Charge' => $larrformData['chargepaid'][$i],
                'UpdDate' => $larrformData['UpdDate'],
                'UpdUser' => $larrformData['UpdUser']
			);
			$db->insert($table, $larrcharges);
		}
	}

	public function getProgramcharges($lintidprogram) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$currentdate = date('Y-m-d');
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_programcharges'), array('a.*'))
		->join(array('b' => 'tbl_charges'), 'a.IdCharges = b.IdCharges')
		->where('b.effectiveDate <= ?', $currentdate)
		->where('a.IdProgram = ?', $lintidprogram)
		->where('b.Payment = 1');
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetInvoicedetails($idprogram) {

		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_charges"), array("a.Rate"))
		->join(array('b' => 'tbl_accountmaster'), 'a.IdAccountMaster=b.idAccount AND b.duringRegistration = 1')
		->join(array('c' => 'tbl_landscape'), 'a.IdProgram=c.IdProgram', array("c.IdStartSemester"))
		->where("a.Active = 1")
		->where("a.IdProgram= ?", $idprogram)
		->group("a.IdCharges");
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnCheckExistingRegistrationId($regid) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
		->from(array('a' => 'tbl_studentregistration'), array('a.IdStudentRegistration'))
		->where('a.registrationId = ?', $regid);
		$result = $db->fetchRow($sql);
		return $result;
	}

	/**
	 * Function to search students based on input provides
	 * @author vipul
	 */
	
	public function fnSearchStudentAppl($post = array()) {

		$db = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $db->select()->from(array('sa' => 'tbl_studentapplication'), array('sa.*'))
		->joinLeft(array('deftn' => 'tbl_definationms'), 'deftn.idDefinition=sa.Status', array('deftn.DefinitionCode'))
		->joinLeft(array('ti' => 'tbl_studentsemesterstatus'), ' ( ti.IdApplication = sa.IdApplication )',
		array('totalCount' => new Zend_Db_Expr('COUNT(idstudentsemsterstatus)')));

		$wh = "1=1 ";

		if (isset($post['field23']) && !empty($post['field23'])) {
			$lstrSelect = $lstrSelect->joinLeft(array('prg' => 'tbl_program'), 'prg.IdProgram=sa.ProgramOfferred');
			$lstrSelect = $lstrSelect->joinLeft(array('schm' => 'tbl_scheme'), 'prg.IdScheme=schm.IdScheme');
			//$lstrSelect = $lstrSelect->where("schm.IdScheme = ?",$post['field23']);
			$wh .= " AND schm.IdScheme = '" . $post['field23'] . "' ";
		}

		if ($post['field25'] != '') {
			$lstrSelect = $lstrSelect->joinLeft(array('breg' => 'tbl_branchregistrationmap'), 'breg.IdBranch= ' . $post['field25'] . '  ');
			//$lstrSelect = $lstrSelect->where('breg.RegistrationLoc = ?',$post['field26']);
			//$lstrSelect = $lstrSelect->where('sa.BranchOfferred = ?',$post['field25']);
			$wh .= "AND sa.BranchOfferred = '" . $post['field25'] . "' ";
		}



		if (isset($post['field4']) && !empty($post['field4'])) {
			//$lstrSelect = $lstrSelect->where('sa.FName like "%" ? "%"',$post['field4']);
			$wh .= " AND sa.FName LIKE '%" . $post['field4'] . "%' ";
		}

		if (isset($post['field3']) && !empty($post['field3'])) {
			//$lstrSelect = $lstrSelect->where("sa.IdApplicant = ?",$post['field3']);
			$wh .= " AND sa.IdApplicant LIKE '%" . $post['field3'] . "%' ";
		}

		if (isset($post['field2']) && !empty($post['field2'])) {
			//$lstrSelect = $lstrSelect->where("sa.ExtraIdField1 = ?",$post['field2']);
			$wh .= " AND sa.ExtraIdField1 LIKE '%" . $post['field2'] . "%'  ";
		}

		if (isset($post['field8']) && !empty($post['field8'])) {
			//$lstrSelect = $lstrSelect->where("sa.ProgramOfferred = ?",$post['field8']);
			$wh .= " AND sa.ProgramOfferred = '" . $post['field8'] . "' ";
		}


		if (isset($post['field24']) && !empty($post['field24'])) {
			//$lstrSelect = $lstrSelect->where("sa.intake = ?",$post['field24']);
			$wh .= " AND sa.intake = '" . $post['field24'] . "' ";
		}


		//        if ($post['field25'] != '' && $post['field25'] == '') {
		//            //$lstrSelect = $lstrSelect->where("sa.BranchOfferred = ?",$post['field25']);
		//            $wh .= " AND sa.BranchOfferred = '" . $post['field25'] . "' ";
		//        }

		// echo $wh;

		$wh .= " AND ( sa.Status = 197 OR sa.Status = 198 ) ";

		if ($post == NULL) {
			$lstrSelect = $lstrSelect->where("sa.Status = 197")->orwhere("sa.Status = 198");
		} else {
			$lstrSelect = $lstrSelect->where($wh);
		}

		//->where("a.Accepted = 1
		// ->where("a.Termination = 0")

		$lstrSelect = $lstrSelect->group(array("sa.IdApplication", "ti.IdApplication"))->order("sa.FName ASC");

		//if(!empty($post)) {
		//    echo $lstrSelect; echo '</br>';
		//}

		$result = $db->fetchAll($lstrSelect);
		return $result;
	}

	/**
	 * Function to get student aplication details based on applicant ID
	 * @author Vipul
	 */
	public function fetchStudentDetails($lintidapplicant) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
		->from(array('a' => 'tbl_studentapplication'), array('a.IdApplication', 'a.FName', 'a.MName', 'a.LName', 'a.ExtraIdField1', 'a.ProgramOfferred', 'a.BranchOfferred', 'a.IdApplicant', 'a.ApplicationDate', 'a.intake', 'a.Gender'))
		//->joinLeft(array('b'=>'tbl_branchofficevenue'),'a.BranchOfferred  = b.IdBranch',array('b.BranchName'))
		//->joinLeft(array('c'=>'tbl_program'),'a.ProgramOfferred = c.IdProgram',array('c.ProgramName','c.IdScheme'))
		->joinLeft(array('b' => 'tbl_branchofficevenue'), 'a.BranchOfferred  = b.IdBranch', array('b.BranchName'))
		->joinLeft(array('c' => 'tbl_program'), 'a.ProgramOfferred = c.IdProgram', array('c.ProgramName', 'c.IdScheme'))
		->joinLeft(array('d' => 'tbl_intake'), 'a.intake = d.IdIntake', array('d.IntakeId'))
		->joinLeft(array('e' => 'tbl_scheme'), 'e.IdScheme = c.IdScheme', array('e.EnglishDescription'))
		->where('a.IdApplication = ?', $lintidapplicant);
		//echo $sql;
		$result = $db->fetchRow($sql);
		return $result;
	}

	/**
	 * Function to get semester master data based on schemeID
	 * @author Vipul
	 */
	public function fetchSemMaster($schemeID, $applicationDate, $ProgramOfferred) {
		$firstArr = $secondArr = $thirdArr = array();
		$db = Zend_Db_Table::getDefaultAdapter();
		$where_schemeDate_condition = " ( a.Scheme = '" . $schemeID . "' )   AND ( '" . $applicationDate . "' BETWEEN a.SemesterMainStartDate AND a.SemesterMainEndDate ) ";
		$sql = $db->select()
		->from(array('a' => 'tbl_semestermaster'), array('a.*'))
		->where($where_schemeDate_condition);
		//echo $sql;
		$semMasterlist = $db->fetchAll($sql);

		if (count($semMasterlist) > 0) {
			foreach ($semMasterlist as $value1) {

				$secondArr = array('key' => $value1['IdSemesterMaster'] . '_main', 'value' => $value1['SemesterMainCode'], 'SemesterStartDate' => $value1['SemesterMainStartDate'], 'SemesterEndDate' => $value1['SemesterMainEndDate']);
				array_push($thirdArr, $secondArr);

				$IdSemesterMaster = $value1['IdSemesterMaster'];
				$where_semmasterDate_condition = " ( b.Semester = '" . $IdSemesterMaster . "' ) AND (b.Program ='" . $ProgramOfferred . "')  AND ( '" . $applicationDate . "' BETWEEN b.SemesterStartDate AND b.SemesterEndDate ) ";
				$semDetailslist = $this->fetchSemDetails($where_semmasterDate_condition);
				if (count($semDetailslist) > 0) {

					foreach ($semDetailslist as $value2) {
						$firstArr = array('key' => $value2['IdSemester'] . '_detail', 'value' => $value2['SemesterCode'], 'SemesterStartDate' => $value2['SemesterStartDate'], 'SemesterEndDate' => $value2['SemesterEndDate']);
						array_push($thirdArr, $firstArr);
					}
				} else {
					//$secondArr = array('key' => $value1['IdSemesterMaster'] . '_main', 'value' => $value1['SemesterMainName'], 'SemesterStartDate' => $value1['SemesterMainStartDate'], 'SemesterEndDate' => $value1['SemesterMainEndDate']);
					//array_push($thirdArr,$secondArr);
				}
			}
		}
		return $thirdArr;
	}

	public function fetchSemMastercurrentPrev($schemeID, $applicationDate, $ProgramOfferred) {
		$firstArr = $secondArr = $thirdArr = array();
		$db = Zend_Db_Table::getDefaultAdapter();
		$where_schemeDate_condition = " ( a.Scheme = '" . $schemeID . "' )   AND ( ('" . $applicationDate . "' BETWEEN a.SemesterMainStartDate AND a.SemesterMainEndDate) OR ('" . $applicationDate . "' >= a.SemesterMainEndDate) ) ";
		$sql = $db->select()
		->from(array('a' => 'tbl_semestermaster'), array('a.*'))
		->where($where_schemeDate_condition);
		//echo $sql;
		$semMasterlist = $db->fetchAll($sql);

		if (count($semMasterlist) > 0) {
			foreach ($semMasterlist as $value1) {

				$secondArr = array('key' => $value1['IdSemesterMaster'] . '_main', 'value' => $value1['SemesterMainCode'], 'SemesterStartDate' => $value1['SemesterMainStartDate'], 'SemesterEndDate' => $value1['SemesterMainEndDate']);
				array_push($thirdArr, $secondArr);

				$IdSemesterMaster = $value1['IdSemesterMaster'];
				$where_semmasterDate_condition = " ( b.Semester = '" . $IdSemesterMaster . "' ) AND (b.Program ='" . $ProgramOfferred . "')  AND ( ('" . $applicationDate . "' BETWEEN b.SemesterStartDate AND b.SemesterEndDate )  OR ('" . $applicationDate . "' >= b.SemesterEndDate)) ";
				$semDetailslist = $this->fetchSemDetails($where_semmasterDate_condition);
				if (count($semDetailslist) > 0) {

					foreach ($semDetailslist as $value2) {
						$firstArr = array('key' => $value2['IdSemester'] . '_detail', 'value' => $value2['SemesterCode'], 'SemesterStartDate' => $value2['SemesterStartDate'], 'SemesterEndDate' => $value2['SemesterEndDate']);
						array_push($thirdArr, $firstArr);
					}
				} else {
					//$secondArr = array('key' => $value1['IdSemesterMaster'] . '_main', 'value' => $value1['SemesterMainName'], 'SemesterStartDate' => $value1['SemesterMainStartDate'], 'SemesterEndDate' => $value1['SemesterMainEndDate']);
					//array_push($thirdArr,$secondArr);
				}
			}
		}
		return $thirdArr;
	}

	/**
     * Function to get semester details data based on semesterID
     * @author Vipul
     */
    public function fetchSemDetails($where_semmasterDate_condition) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()
                        ->from(array('b' => 'tbl_semester'), array('b.*'))
                        ->where($where_semmasterDate_condition);
        //echo $sql; echo '</br>';
        $result = $db->fetchAll($sql);
        return $result;
    }

    public function fetchSemMain($where_semmasterDate_condition) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()
                        ->from(array('b' => 'tbl_semestermaster'), array('b.*'))
                        ->where($where_semmasterDate_condition);
        //echo $sql; echo '</br>';
        $result = $db->fetchAll($sql);
        return $result;
    }

	/**
     * Function to get intake data based on programID
     * @author Vipul
     */
    public function fetchIntakeDetails($programID) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()
                        ->from(array('b' => 'tbl_intake_branch_mapping'), array('b.IdIntake', 'b.IdProgram'))
                        ->join(array('a' => 'tbl_intake'), 'a.IdIntake=b.IdIntake', array("key" => "a.IdIntake", "value" => "a.IntakeId"))
                        ->where('b.IdProgram = ?', $programID);
        $result = $db->fetchAll($sql);
        return $result;
    }

	/**
     * Function to get student aplication Extra details based on applicant ID
     * @author Vipul
     */
    public function fetchStudentExtraDetails($lintidapplicant) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()
                        //->from(array('a' => 'tbl_studentapplication'),array('a.IdApplication','a.FName','a.MName','a.LName','a.NameField1','a.NameField2','a.FullArabicName','a.DateOfBirth','a.TypeofId','a.PlaceOfBirth','a.MaritalStatus',
                        //			'a.Religion','a.Race','a.Gender','a.Nationality','a.SpecialTreatment','a.SpecialTreatmentType','a.PermAddressDetails','a.PermCountry','a.PermState','a.PermCity','a.PermZip','a.CorrsAddressDetails','a.CorrsCountry','a.CorrsState','a.CorrsCity','a.CorrsZip'
                        //			,'a.OutcampusAddressDetails','a.OutcampusCountry','a.OutcampusState','a.OutcampusCity','a.OutcampusZip','a.HomePhone','a.CellPhone','a.Fax','a.RelationshipType','a.RelativeName','a.EmergencyAddressDetails','a.EmergencyCountry','a.EmergencyState','a.EmergencyCity','a.EmergencyZip','a.EmergencyHomePhone'
                        //			,'a.EmergencyCellPhone','a.EmergencyOffPhone'))
                        ->from(array('a' => 'tbl_studentapplication', array('a.*')))
                        ->where('a.IdApplication = ?', $lintidapplicant);
        $result = $db->fetchRow($sql);
        return $result;
    }


    /**
     * Function to ADD STUDENT Regisration based on applicant ID
     * @author Vipul
     */
    public function fnInsertStudentReg($larrformData, $studentExtraDetails, $getPassword) {
        //asd($larrformData);
        $db = Zend_Db_Table::getDefaultAdapter();
        $lobjstudentHistoryModel = new Registration_Model_DbTable_Studenthistory();
        $finalsemdetailID = $finalsemmainID = NULL;
        $semID = explode('_', $larrformData['IdSemestersyllabus']);
        $semtype = $semID['1'];
        if ($semtype == 'detail') {
            $finalsemdetailID = $semID['0'];
        }
        if ($semtype == 'main') {
            $finalsemmainID = $semID['0'];
        }
        //asd($studentExtraDetails);
        // set the registration ID Format and value
        $registrationId = $larrformData['registrationId'];

        $formData = array(
            //'IdApplication'=>$studentExtraDetails['IdApplication'],
            'registrationId' => $registrationId,
            'email' => $studentExtraDetails['PEmail'],
            'Psswrd' => $getPassword,
            'Status' => $studentExtraDetails['Status'],
            'IdSemesterMain' => $finalsemmainID,
            'IdSemesterDetails' => $finalsemdetailID,
            'IdApplicant' => $studentExtraDetails['IdApplicant'],
            'ApplicationDate' => date('Y-m-d H:i:s'),
            'ExtraIdField1' => $studentExtraDetails['ExtraIdField1'],
            'IdLandscape' => $larrformData['IdLandscape'],
            'IdProgram' => $studentExtraDetails['ProgramOfferred'],
            'IdBranch' => $studentExtraDetails['BranchOfferred'],
            'IdIntake' => $larrformData['IdIntake'],
            'Status' => '198', // Regd student is now Activated
            'profileStatus' => '92', // tbl_definationms idDefType = 20 // StudentStatus
            'FName' => $studentExtraDetails['FName'],
            'MName' => $studentExtraDetails['MName'],
            'LName' => $studentExtraDetails['LName'],
            'NameField1' => $studentExtraDetails['NameField1'],
            'NameField2' => $studentExtraDetails['NameField2'],
            'FullArabicName' => $studentExtraDetails['FullArabicName'],
            'DateOfBirth' => $studentExtraDetails['DateOfBirth'],
            'TypeofId' => $studentExtraDetails['TypeofId'],
            'PlaceOfBirth' => $studentExtraDetails['PlaceOfBirth'],
            'MaritalStatus' => $studentExtraDetails['MaritalStatus'],
            'Religion' => $studentExtraDetails['Religion'],
            'Race' => $studentExtraDetails['Race'],
            'Gender' => $studentExtraDetails['Gender'],
            'Nationality' => $studentExtraDetails['Nationality'],
            'SpecialTreatment' => $studentExtraDetails['SpecialTreatment'],
            'SpecialTreatmentType' => $studentExtraDetails['SpecialTreatmentType'],
            'PermAddressDetails' => $studentExtraDetails['PermAddressDetails'],
            'PermCountry' => $studentExtraDetails['PermCountry'],
            'PermState' => $studentExtraDetails['PermState'],
            'PermCity' => $studentExtraDetails['PermCity'],
            'PermZip' => $studentExtraDetails['PermZip'],
            'CorrsAddressDetails' => $studentExtraDetails['CorrsAddressDetails'],
            'CorrsCountry' => $studentExtraDetails['CorrsCountry'],
            'CorrsState' => $studentExtraDetails['CorrsState'],
            'CorrsCity' => $studentExtraDetails['CorrsCity'],
            'CorrsZip' => $studentExtraDetails['CorrsZip'],
            'OutcampusAddressDetails' => $studentExtraDetails['OutcampusAddressDetails'],
            'OutcampusCountry' => $studentExtraDetails['OutcampusCountry'],
            'OutcampusState' => $studentExtraDetails['OutcampusState'],
            'OutcampusCity' => $studentExtraDetails['OutcampusCity'],
            'OutcampusZip' => $studentExtraDetails['OutcampusZip'],
            'HomePhone' => $studentExtraDetails['HomePhone'],
            'CellPhone' => $studentExtraDetails['CellPhone'],
            'Fax' => $studentExtraDetails['Fax'],
            'RelationshipType' => $studentExtraDetails['RelationshipType'],
            'RelativeName' => $studentExtraDetails['RelativeName'],
            'EmergencyAddressDetails' => $studentExtraDetails['EmergencyAddressDetails'],
            'EmergencyCountry' => $studentExtraDetails['EmergencyCountry'],
            'EmergencyState' => $studentExtraDetails['EmergencyState'],
            'EmergencyCity' => $studentExtraDetails['EmergencyCity'],
            'EmergencyZip' => $studentExtraDetails['EmergencyZip'],
            'EmergencyHomePhone' => $studentExtraDetails['EmergencyHomePhone'],
            'EmergencyCellPhone' => $studentExtraDetails['EmergencyCellPhone'],
            'EmergencyOffPhone' => $studentExtraDetails['EmergencyOffPhone'],
            'UpdUser' => $larrformData['UpdUser'],
            'UpdDate' => date('Y-m-d H:i:s'),
        	'IdStudentRegistrationFormatted' => $larrformData['IdStudentRegistrationFormatted'],
        	'IdFormat' => $larrformData['IdFormat'],
        );
        $insertID = $this->insert($formData);
        $getlID = $db->lastInsertId();


        // MAINTAIN THE STUDENT STATUS HISTORY
        $statusArray = array();
        $statusArray['profileStatus'] = 92;
        $statusArray['IdStudentRegistration'] = $getlID;
        $statusArray['IpAddress'] = $larrformData['REMOTE_ADDR'];
        $statusArray['UpdUser'] = $larrformData['UpdUser'];
        $statusArray['UpdDate'] = $larrformData['UpdDate'];
        $lobjstudentHistoryModel->addStudentProfileHistory($statusArray);


        // INSERT INTO
        $table = "tbl_studentsemesterstatus";
        $lsemstatusArr = array('IdStudentRegistration' => $getlID,
            'IdApplication' => $studentExtraDetails['IdApplication'],
            'idSemester' => $finalsemdetailID,
            'IdSemesterMain' => $finalsemmainID,
            'studentsemesterstatus' => '130',
            'UpdDate' => $larrformData['UpdDate'],
            'UpdUser' => $larrformData['UpdUser'],
        );
        $db->insert($table, $lsemstatusArr);


        // INSERT INTO
        $table_studregsubj = "tbl_studentregsubjects";
        $courseArr = $larrformData['coursechk'];
        if (count($courseArr) > 0) {
            foreach ($courseArr as $key => $value) {
                $lstudregsubjArr = array('IdStudentRegistration' => $getlID,
                    'IdSubject' => $value,
                    'IdSemesterDetails' => $finalsemdetailID,
                    'IdSemesterMain' => $finalsemmainID,
                    'UpdDate' => $larrformData['UpdDate'],
                    'UpdUser' => $larrformData['UpdUser'],
                );
                $db->insert($table_studregsubj, $lstudregsubjArr);
            }
        }


        // UPDATE STUDENT APPLication
        $table_update_sa = "tbl_studentapplication";
        $lAppStudArr = array('registrationId' => $registrationId, 'Status' => '198', 'IdStudentRegistration' => $getlID,'DateRegistered'=>date('Y-m-d H:i:s'));
        $where = 'IdApplication = ' . $studentExtraDetails['IdApplication'];
        $db->update($table_update_sa, $lAppStudArr, $where);
        return $getlID;
    }

	/**
	 * Function to to get courses from the first semester in the Landscape setup
	 * @author Vipul
	 */
	public function fnAllCourses($idlandscape) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
		->from(array('a' => 'tbl_landscapesubject', array('a.IdLandscape', 'a.IdSemester', 'a.IdSubject', 'a.IdProgram', 'a.IDProgramMajoring')))
		->joinLeft(array('b' => 'tbl_subjectmaster'), 'a.IdSubject=b.IdSubject', array("b.SubjectName", "b.CreditHours", "b.SubCode"))
		->joinLeft(array('c' => 'tbl_user'), 'c.iduser=b.UpdUser', array("c.loginName"))
		->joinLeft(array('e' => 'tbl_landscape'), "e.IdLandscape='" . $idlandscape . "' ", array('e.AddDrop'))
		->where('a.IdLandscape = ?', $idlandscape)
		->where('a.IdSemester = ?', '1')
		->group(array('b.SubjectName', 'b.SubCode'));
		$result = $db->fetchAll($sql);
		return $result;
	}

	/**
	 * Function to to get regd courses from landscape and IdStudentRegistration
	 * @author Vipul
	 */
	public function fnGetCourses($idreg, $idlandscape) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
		->from(array('a' => 'tbl_landscapesubject', array('a.IdLandscape', 'a.IdSemester', 'a.IdSubject', 'a.IdProgram', 'a.IDProgramMajoring')))
		->joinLeft(array('d' => 'tbl_studentregsubjects'), "d.IdStudentRegistration='" . $idreg . "' ", array('d.UpdDate AS SubjectUpdateDate'))
		->join(array('b' => 'tbl_subjectmaster'), 'a.IdSubject=b.IdSubject', array("b.SubjectName", "b.CreditHours", "b.SubCode"))
		->join(array('c' => 'tbl_user'), 'c.iduser=d.UpdUser', array("c.loginName"))
		->joinLeft(array('e' => 'tbl_landscape'), "e.IdLandscape='" . $idlandscape . "' ", array('e.AddDrop'))
		->where('a.IdLandscape = ?', $idlandscape)
		->where('a.IdSemester = ?', '1')
		->group('a.IdSubject')
		->order('b.SubjectName');

        $result = $db->fetchAll($sql);
        return $result;
    }
	

//	public function fnGetprintCourses($idreg, $idlandscape) {
//		$db = Zend_Db_Table::getDefaultAdapter();
//		$sql = $db->select()
//		->from(array('a' => 'tbl_studentregistration'), array('a.*'))
//		->joinLeft(array('b' => 'tbl_branchofficevenue'), 'a.IdBranch  = b.IdBranch', array('b.BranchName'))
//		->joinLeft(array('c' => 'tbl_program'), 'a.IdProgram = c.IdProgram', array('c.ProgramName', 'c.IdScheme'))
//		->joinLeft(array('d' => 'tbl_intake'), 'a.IdIntake = d.IdIntake', array('d.IntakeId'))
//		->joinLeft(array('e' => 'tbl_scheme'), 'e.IdScheme = c.IdScheme', array('e.EnglishDescription'))
//		->joinLeft(array('f'=>'tbl_city'),'f.idCity = a.PermCity',array('f.CityName'))
//		->joinLeft(array('g'=>'tbl_state'),'g.idState = a.PermState',array('g.StateName'))
//		->joinLeft(array('h'=>'tbl_countries'),'h.idCountry = a.PermCountry',array('h.CountryName'))
//		->joinLeft(array('i'=>'tbl_definationms'),'i.idDefinition = profileStatus',array('i.DefinitionDesc'))
//		->where('a.IdStudentRegistration = ?', $idreg);
//		$result = $db->fetchRow($sql);
//		return $result;
//	}
	
	public function fnGetprintCourses($idreg, $idlandscape) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
		->from(array('a' => 'tbl_landscapesubject', array('a.IdLandscape', 'a.IdSemester', 'a.IdSubject', 'a.IdProgram', 'a.IDProgramMajoring')))
		->join(array('d' => 'tbl_studentregsubjects'), "d.IdStudentRegistration='" . $idreg . "' AND a.IdSubject = d.IdSubject", array('d.UpdDate AS SubjectUpdateDate'))
		->join(array('b' => 'tbl_subjectmaster'), 'a.IdSubject=b.IdSubject', array("b.SubjectName", "b.CreditHours", "b.SubCode"))
		->join(array('c' => 'tbl_user'), 'c.iduser=d.UpdUser', array("c.loginName"))
		->where('a.IdLandscape = ?', $idlandscape)
		->where('a.IdSemester = ?', '1')
		->group('a.IdSubject')
		->order('b.SubjectName');

		$result = $db->fetchAll($sql);
		return $result;
	}

    /**
     * Function to to get courses from the first semester in the Landscape setup
     * @author Vipul
     */
    public function fnGetCoursesRegistered($idreg) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()
                        //->from(array('a' => 'tbl_landscapesubject',array('a.IdSubject')))
                        //->join(array('b' => 'tbl_subjectmaster'),'a.IdSubject=b.IdSubject', array())
                        ->from(array('d' => 'tbl_studentregsubjects'), array('d.IdSubject'))
                        //->where('a.IdLandscape = ?',$idlandscape)
                        ->where('d.IdStudentRegistration = ?', $idreg);
        //->where('a.IdSemester = ?','1');
        // echo $sql;
        $result = $db->fetchAll($sql);
        return $result;
    }

    /**
	 * Function to get student aplication details based on applicant ID
	 * @author Vipul
	 */
	public function fetchStudentRegDetails($lintidapplicant, $lintidreg) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
		->from(array('a' => 'tbl_studentregistration'), array('a.*'))
		->joinLeft(array('b' => 'tbl_branchofficevenue'), 'a.IdBranch  = b.IdBranch', array('b.BranchName'))
		->joinLeft(array('c' => 'tbl_program'), 'a.IdProgram = c.IdProgram', array('c.ProgramName', 'c.IdScheme'))
		->joinLeft(array('d' => 'tbl_intake'), 'a.IdIntake = d.IdIntake', array('d.IntakeId'))
		->joinLeft(array('e' => 'tbl_scheme'), 'e.IdScheme = c.IdScheme', array('e.EnglishDescription'))
		->joinLeft(array('f'=>'tbl_city'),'f.idCity = a.PermCity',array('f.CityName'))
		->joinLeft(array('g'=>'tbl_state'),'g.idState = a.PermState',array('g.StateName'))
		->joinLeft(array('h'=>'tbl_countries'),'h.idCountry = a.PermCountry',array('h.CountryName'))
		->joinLeft(array('i'=>'tbl_definationms'),'i.idDefinition = profileStatus',array('i.DefinitionDesc'))
		->where('a.IdStudentRegistration = ?', $lintidreg);
		$result = $db->fetchRow($sql);
		return $result;
	}

    /**
     * Function to to get courses from the first semester in the Landscape setup
     * @author Vipul
     */
    public function fnGetRegisteredCourses($IdStudentRegistration) {   // $idlandscape is here IdStudentRegistration
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()
                        ->from(array('a' => 'tbl_studentregsubjects', array('a.*')))
                        ->join(array('b' => 'tbl_subjectmaster'), 'a.IdSubject=b.IdSubject', array("b.SubjectName", "b.CreditHours", "b.SubCode", "b.UpdDate", "b.UpdUser"))
                        ->join(array('c' => 'tbl_user'), 'c.iduser=b.UpdUser', array("c.loginName"))
                        ->where('a.IdStudentRegistration = ?', $IdStudentRegistration);
        $result = $db->fetchAll($sql);
        return $result;
    }

    /**
     * Function to REVERT back all the processes generated in the Register button
     * @author Vipul
     */
    public function fnRevertReg($lintidreg) {   // $idlandscape is here IdStudentRegistration
        $db = Zend_Db_Table::getDefaultAdapter();

        // delete from tbl_studentsemesterstatus
        $where_studentsemesterstatus = $this->lobjDbAdpt->quoteInto('IdStudentRegistration = ?', $lintidreg);
        $db->delete('tbl_studentsemesterstatus', $where_studentsemesterstatus);

        // delete from tbl_studentregsubjects
        $where_studentregsubjects = $this->lobjDbAdpt->quoteInto('IdStudentRegistration = ?', $lintidreg);
        $db->delete('tbl_studentregsubjects', $where_studentregsubjects);

        // update from tbl_studentapplication
        $table_update_sa = "tbl_studentapplication";
        $lAppStudArr = array('registrationId' => NULL, 'Status' => '197', 'IdStudentRegistration' => NULL);
        $where_studentapplication = 'IdStudentRegistration = ' . $lintidreg;
        $db->update($table_update_sa, $lAppStudArr, $where_studentapplication);

        // delete from tbl_studentregistration
        $where_studentregistration = $this->lobjDbAdpt->quoteInto('IdStudentRegistration = ?', $lintidreg);
        $db->delete('tbl_studentregistration', $where_studentregistration);
        
        // delete from tbl_studentregistration
        $where_studentregistration = $this->lobjDbAdpt->quoteInto('IdStudentRegistration = ?', $lintidreg);
        $db->delete('tbl_student_generated_id', $where_studentregistration);
    }

    /**
     * Function to check uniquiness of registrationId in student reg table
     * @author Vipul
     */
    public function fncheckUniqueRegID($registrationId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()
                        ->from(array('a' => 'tbl_studentregistration', array('a.IdStudentRegistration', 'a.registrationId')))
                        ->where('a.registrationId = ?', $registrationId);
        $result = $db->fetchAll($sql);
        $totalVal = count($result);
        return $totalVal;
    }

    /**
     * Function fetch all students(and count) from student reg table
     * @author Vipul
     */
    public function fnfetchAllStudents() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()->from(array('a' => 'tbl_studentregistration', array('a.IdStudentRegistration')));
        $result = $db->fetchAll($sql);
        $totalVal = count($result);
        return $totalVal;
    }

    /**
     * Function to get ALL semester for registration history
     * @author Vipul
     */
    public function fetchAllSemMaster() {

        $firstArr = $secondArr = $thirdArr = array();
        $db = Zend_Db_Table::getDefaultAdapter();
        $where_schemeDate_condition = " ( 1 = 1) ";
        $sql = $db->select()
                        ->from(array('a' => 'tbl_semestermaster'), array('a.*'))
                        ->where($where_schemeDate_condition);
        $semMasterlist = $db->fetchAll($sql);

        if (count($semMasterlist) > 0) {
            foreach ($semMasterlist as $value1) {
                $IdSemesterMaster = $value1['IdSemesterMaster'];
                $where_semmasterDate_condition = " ( b.Semester = '" . $IdSemesterMaster . "' )  ";
                $semDetailslist = $this->fetchSemDetails($where_semmasterDate_condition);
                if (count($semDetailslist) > 0) {

                    foreach ($semDetailslist as $value2) {
                        $firstArr = array('key' => $value2['IdSemester'] . '_detail', 'value' => $value2['SemesterCode']);
                        array_push($thirdArr, $firstArr);
                    }
                } else {
                    $secondArr = array('key' => $value1['IdSemesterMaster'] . '_main', 'value' => $value1['SemesterMainCode']);
                    array_push($thirdArr, $secondArr);
                }
            }
        }
        return $thirdArr;
    }

    public function fnGetSemesterList($lintidlandscape) {
        $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();

        $lstrSelect = $lobjDbAdpt->select()
                        ->from(array("sa" => "tbl_landscape"), array("sa.SemsterCount"))
                        ->where("sa.Active = 123")
                        ->where("sa.IdLandscape= ?", $lintidlandscape);
        $larrResult = $lobjDbAdpt->fetchAll($lstrSelect);

        return $larrResult;
    }

    /**
     * Function to get ALL regd. Students for registration history
     * @author Vipul
     */
    public function fetchAllForRegistrationHistory($post=NULL) {

    	$session = new Zend_Session_Namespace('sis');
    	
        $db = Zend_Db_Table::getDefaultAdapter();

        $lstrSelect = $db->select()->from(array('sa' => 'tbl_studentregistration'), array('sa.*'))
                        ->joinLeft(array('p'=>'applicant_profile'),'p.appl_id=sa.IdApplication',array('appl_fname','appl_mname','appl_lname'))
                        ->joinLeft(array('deftn' => 'tbl_definationms'), 'deftn.idDefinition=sa.Status', array('deftn.DefinitionCode'))
                        ->joinLeft(array('prg' => 'tbl_program'), 'prg.IdProgram=sa.IdProgram', array('prg.ProgramName'))
                        ->joinLeft(array('intk' => 'tbl_intake'), 'intk.IdIntake=sa.IdIntake', array('intk.IntakeId'))
                        ->where("sa.OldIdStudentRegistration IS NULL")
                        ->order("sa.FName ASC");

        if($session->IdRole == 311 || $session->IdRole == 298){ 			
			$lstrSelect->where("prg.IdCollege =?",$session->idCollege);
		} 
		
        if (isset($post['field2']) && !empty($post['field2'])) {
         
            $lstrSelect->where("(p.appl_fname LIKE '%". $post['field2']."%'");
            $lstrSelect->orwhere("p.appl_mname LIKE '%". $post['field2']."%'");
            $lstrSelect->orwhere("p.appl_lname LIKE '%". $post['field2']."%')");
        }

        if (isset($post['field3']) && !empty($post['field3'])) {           
            $lstrSelect->where("sa.registrationId LIKE '%". $post['field3']."%'");
        }

        if (isset($post['field5']) && !empty($post['field5'])) {           
            $lstrSelect->where("sa.profileStatus = ?",$post['field5']);
        }


        if (isset($post['field23']) && !empty($post['field23'])) {
            $lstrSelect = $lstrSelect->joinLeft(array('semst' => 'tbl_studentsemesterstatus'), 'semst.IdStudentRegistration=sa.IdStudentRegistration');
            $semID = explode('_', $post['field23']);
            $semtype = $semID['1'];
            
            if ($semtype == 'detail') {
                $finalsemdetailID = $semID['0'];              
                $lstrSelect->where("semst.idSemester = ?",$finalsemdetailID);
            }
            
            if ($semtype == 'main') {
                $finalsemmainID = $semID['0'];               
                $lstrSelect->where("semst.IdSemesterMain = ?",$finalsemmainID);
            }
        }



        if (isset($post['field24']) && !empty($post['field24'])) {            
            $lstrSelect->where("sa.IdProgram = ?",$post['field24']);
        }

        if (isset($post['field25']) && !empty($post['field25'])) {
        	
            $lstrSelect = $lstrSelect->join(array('crs' => 'tbl_studentregsubjects'), 'crs.IdStudentRegistration=sa.IdStudentRegistration');
            $lstrSelect->where("crs.IdSubject = ?",$post['field25']);
        }

        if (isset($post['field26']) && !empty($post['field26'])) {           
            $lstrSelect->where("sa.IdIntake = ?",$post['field26']);
        }

        if (isset($post['field15']) && !empty($post['field15'])) {          
            $lstrSelect->where("sa.ApplicationDate  = ?",$post['field15']);
        }
       
       
        $result = $db->fetchAll($lstrSelect);
        return $result;
    }
    

     /**
     * Function to get student history details based on  IdStudentRegistration
     * @author Vipul
     * @alter yatie
     */
    public function fetchStudentHistoryDetails($lintidreg) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()
                        ->from(array('a' => 'tbl_studentregistration'), array('a.*'))
                        ->joinleft(array('p'=>'applicant_profile'),'p.appl_id=a.IdApplication',array('appl_fname','appl_mname','appl_lname','appl_id'))
                        ->joinLeft(array('b' => 'tbl_branchofficevenue'), 'a.IdBranch  = b.IdBranch', array('BranchName'))
                        ->joinLeft(array('c' => 'tbl_program'), 'a.IdProgram = c.IdProgram', array('c.ProgramName', 'c.IdScheme','ArabicName'))
                        ->joinLeft(array('d' => 'tbl_intake'), 'a.IdIntake = d.IdIntake', array('d.IntakeId'))
                        ->joinLeft(array('e' => 'tbl_scheme'), 'e.IdScheme = c.IdScheme', array('e.EnglishDescription'))
                        ->joinLeft(array('f' => 'tbl_landscape'), 'f.IdLandscape = a.IdLandscape', array('f.ProgramDescription'))
                        ->joinLeft(array('g' => 'tbl_user'), 'g.iduser=a.UpdUser', array("g.loginName"))
                        ->where('a.IdStudentRegistration = ?', $lintidreg);
        $result = $db->fetchRow($sql);
        return $result;
    }

    /**
     * Function to to get courses semester wise
     * @author Vipul
     */
    public function fnGetRegisteredCoursesSemesterWise($lintidreg) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()
                        ->from(array('a' => 'tbl_studentsemesterstatus', array('a.*')))
                        ->joinLeft(array('b' => 'tbl_semestermaster'), 'a.IdSemesterMain=b.IdSemesterMaster', array("b.SemesterMainName"))
                        ->joinLeft(array('c' => 'tbl_semester'), 'c.idSemester=a.IdSemester', array("c.SemesterCode"))
                        ->where('a.IdStudentRegistration = ?', $lintidreg)
                        ->order('a.idstudentsemsterstatus');
        $result = $db->fetchAll($sql);
        return $result;
    }

    /**
     * Function to to get courses from the semester
     * @author Vipul
     */
    public function fnGetRegisteredCoursesMain($condition) {   // $idlandscape is here IdStudentRegistration
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()
                        ->from(array('a' => 'tbl_studentregsubjects', array('a.*')))
                        ->join(array('b' => 'tbl_subjectmaster'), 'a.IdSubject=b.IdSubject', array("b.SubjectName", "b.CreditHours", "b.SubCode"))
                        ->join(array('d' => 'tbl_coursetype'), 'b.CourseType = d.IdCourseType', array("d.CourseType"))
                        ->join(array('c' => 'tbl_user'), 'c.iduser=b.UpdUser', array("c.loginName"))
                        ->where($condition);
        $result = $db->fetchAll($sql);
        return $result;
    }

    /*
     * Function to get all courses for senior student
     */

    public function fnGetSeniorStudentCourses($idlandscape, $IdProgram, $SemesterCount) {
        $sql = $this->lobjDbAdpt->select()
                        ->from(array('a' => 'tbl_landscapesubject', array('a.IdLandscape', 'a.IdSemester', 'a.IdSubject', 'a.IdProgram', 'a.IDProgramMajoring')))
                        ->joinLeft(array('b' => 'tbl_subjectmaster'), 'a.IdSubject=b.IdSubject', array("b.SubjectName", "b.CreditHours", "b.SubCode", "b.UpdDate", "b.UpdUser"))
                        ->joinLeft(array('c' => 'tbl_user'), 'c.iduser = b.UpdUser', array("c.loginName"))
                        ->where('a.IdLandscape = ?', $idlandscape)
                        ->where('a.IdProgram = ?', $IdProgram)
                        ->where('a.IdSemester <= ?', ($SemesterCount))
                        ->group("b.IdSubject");
        
        $result = $this->lobjDbAdpt->fetchAll($sql);
        return $result;
    }

    public function checkRegisterSemester($IdStudentRegistration, $idSemester) {
        $sql = $this->lobjDbAdpt->select()
                        ->from(array('a' => 'tbl_studentsemesterstatus', array('a.idstudentsemsterstatus')))
                        ->where('a.IdStudentRegistration =?', $IdStudentRegistration)
                        ->where('a.IdSemesterMain =?', $idSemester)
                        ->orwhere('a.idSemester =?', $idSemester);
        $result = $this->lobjDbAdpt->fetchAll($sql);
        return $result;
    }

    public function changePrevioussemstatus($IdStudentRegistration, $idSemester) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $table = "tbl_studentsemesterstatus";
        $where = "IdStudentRegistration = '" . $IdStudentRegistration . "' AND (idSemester='" . $idSemester . "' OR IdSemesterMain = '$idSemester') ";
        $data = array('studentsemesterstatus' => '229');
        $db->update($table, $data, $where);
    }

    public function AddNewSemForStudent($data) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $table = "tbl_studentsemesterstatus";
        $db->insert($table, $data);
        return ($db->lastInsertId());
    }

    /**
     * Function to insert subjects that are not yet registered
     * @author vipul
     */
    public function fnInsertStudentRegSubjects($larrformData, $lintidreg) {   // $idlandscape is here IdStudentRegistration
        $db = Zend_Db_Table::getDefaultAdapter();

        $finalsemdetailID = $finalsemmainID = NULL;
        $semID = explode('_', $larrformData['IdSemestersyllabus']);
        $semtype = $semID['1'];
        if ($semtype == 'detail') {
            $finalsemdetailID = $semID['0'];
        }
        if ($semtype == 'main') {
            $finalsemmainID = $semID['0'];
        }

        // INSERT INTO
        $table_studregsubj = "tbl_studentregsubjects";
        $courseArr = $larrformData['coursechk'];
        if (count($courseArr) > 0) {
            foreach ($courseArr as $key => $value) {
                $lstudregsubjArr = array('IdStudentRegistration' => $lintidreg,
                    'IdSubject' => $value,
                    'IdSemesterDetails' => $finalsemdetailID,
                    'IdSemesterMain' => $finalsemmainID,
                    'UpdDate' => $larrformData['UpdDate'],
                    'UpdUser' => $larrformData['UpdUser'],
                );
                $db->insert($table_studregsubj, $lstudregsubjArr);
            }
        }
    }

    /**
     * Function to get the reg location on basis of intake and scheme IDs
     * @author vipul
     */
    public function fngetRegLocation($rlIntake, $rlScheme) {
        $sql = $this->lobjDbAdpt->select()
                        ->from(array('a' => 'tbl_registrationlocation', array('a.IdRegistrationLocation', 'a.RegistrationLocationName', 'a.RegistrationLocationIntake')))
                        ->join(array('b' => 'tbl_registration_info'), 'a.IdRegistrationLocation=b.IdRegistrationLocation AND b.RegistrationLocationScheme=' . $rlScheme . ' ', array("b.RegistrationLocationScheme"))
                        ->where('a.RegistrationLocationIntake = ?', $rlIntake);
        //echo $sql; die;
        $result = $this->lobjDbAdpt->fetchAll($sql);
        return $result;
    }

    /**
     * Function to get the reg courses
     * @author vipul
     */
    public function fnsumRegCourses($regId) {
        $sql = $this->lobjDbAdpt->select()
                        ->from(array('a' => 'tbl_studentregsubjects', array('SUM(a.*) as totalSub')))
                        //->join(array('b' => 'tbl_registration_info'), 'a.IdRegistrationLocation=b.IdRegistrationLocation AND b.RegistrationLocationScheme='.$rlScheme.' ', array("b.RegistrationLocationScheme"))
                        ->where('a.IdStudentRegistration = ?', $regId);
        $result = $this->lobjDbAdpt->fetchAll($sql);
        return $result;
    }

    /**
     * Function to get student history profile status
     * @author Vipul
     */
    public function fetchStudentProfileHistory($lintidreg) {
        //$db = Zend_Db_Table::getDefaultAdapter();
//			$sql = $db->select()
//                                         ->from(array('a' => 'tbl_studentregistration'),array('a.profileStatus','a.UpdUser','a.UpdDate'))	
//                                         ->join(array('c' => 'tbl_user'), 'c.iduser=a.UpdUser', array("c.loginName"))
//                                         ->joinLeft(array('deftn'=>'tbl_definationms'),'deftn.idDefinition=a.profileStatus',array('deftn.DefinitionCode'))
//                			 ->where('a.IdStudentRegistration = ?',$lintidreg);
//			$result = $db->fetchAll($sql);
//			return $result;


        $lstrSelect = $this->lobjDbAdpt->select()
                        ->from(array("a" => "tbl_student_status_history"), array('a.*'))
                        ->joinLeft(array("b" => "tbl_definationms"), "a.profileStatus = b.idDefinition", array("b.DefinitionDesc"))
                        ->joinLeft(array("c" => "tbl_user"), "a.UpdUser = c.iduser", array("c.loginName"))
                        ->where("a.IdStudentRegistration = ?", $lintidreg)
                        ->order("a.IdStudentHistory");
        $larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
        return $larrResult;
    }

    /**
     * Function to get student history profile status
     * @author Vipul
     */
    public function fetchStudentSemesterHistory($lintidreg) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()
                        ->from(array('a' => 'tbl_studentsemesterstatus'), array('a.*'))
                        ->joinLeft(array('d' => 'tbl_semester'), 'd.IdSemester=a.idSemester', array('d.SemesterCode'))
                        ->joinLeft(array('e' => 'tbl_semestermaster'), 'e.IdSemesterMaster=a.IdSemesterMain', array('e.SemesterMainName', 'e.SemesterMainCode'))
                        ->join(array('c' => 'tbl_user'), 'c.iduser=a.UpdUser', array("c.loginName"))
                        ->joinLeft(array('deftn' => 'tbl_definationms'), 'deftn.idDefinition=a.studentsemesterstatus', array('deftn.DefinitionCode'))
                        ->where('a.IdStudentRegistration = ?', $lintidreg);
        $result = $db->fetchAll($sql);
        return $result;
    }

    /**
     * Function to search unregistered students based on schemeId and semester start and end date.
     * @author vipul
     */
    public function fnSearchUnregStudents($post = array()) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $currentDate = date('Y-m-d');
        $lstrSelect = $db->select()
                        ->from(array('a' => 'tbl_studentregistration'), array('a.IdStudentRegistration', 'a.registrationId', 'a.IdProgram', 'a.IdBranch', 'a.IdIntake', 'a.profileStatus', 'a.FName', 'a.MName', 'a.LName', 'a.UpdDate', 'a.UpdUser', 'a.ExtraIdField1'))
                        //->joinLeft(array('u' => 'tbl_user'), 'u.iduser=a.UpdUser', array("u.loginName"))
                        ->joinLeft(array('deftn' => 'tbl_definationms'), 'deftn.idDefinition=a.profileStatus', array('deftn.DefinitionCode'))
                        ->joinLeft(array('p' => 'tbl_program'), 'a.IdProgram = p.IdProgram', array('p.ProgramName', 'p.IdScheme'))
                        ->joinLeft(array('s' => 'tbl_scheme'), 's.IdScheme = p.IdScheme', array('s.EnglishDescription'))
                        ->join(array('ss' => 'tbl_studentsemesterstatus'), 'ss.IdStudentRegistration = a.IdStudentRegistration', array(''))
        //->joinLeft(array('sm' => 'tbl_semestermaster')," ( s.IdScheme=sm.Scheme )  AND ( '$currentDate' BETWEEN sm.SemesterMainStartDate AND sm.SemesterMainEndDate ) ", array("sm.IdSemesterMaster","sm.SemesterMainStatus","sm.SemesterMainName as semMName"))
        //->joinLeft(array('sj' => 'tbl_semester')," ( sj.Program=a.IdProgram ) AND ( '$currentDate' BETWEEN sj.SemesterStartDate AND sj.SemesterEndDate ) ", array("sj.IdSemester","sj.SemesterStatus","sj.SemesterCode as semDName"))
        //->joinLeft(array('sss' => 'tbl_studentsemesterstatus')," ( sm.IdSemesterMain=sss.IdSemesterMaster )  ", array("sj.IdSemester","sj.SemesterStatus","sj.SemesterCode as semDName"))
        ;
        //asd($post);

        $wh = "( a.profileStatus = '92' OR a.profileStatus = '248' ) ";
        if (isset($post['field4']) && !empty($post['field4'])) {
            $wh .= " AND  ( a.FName LIKE '%" . $post['field4'] . "%' OR a.LName LIKE '%" . $post['field4'] . "%' ) ";
        }

        if (isset($post['field2']) && !empty($post['field2'])) {
            $wh .= " AND  ( a.registrationId LIKE '%" . $post['field2'] . "%'  ) ";
        }

        if (isset($post['field3']) && !empty($post['field3'])) {
            $wh .= " AND  ( a.ExtraIdField1 LIKE '%" . $post['field3'] . "%'  ) ";
        }

        if (isset($post['field8']) && !empty($post['field8'])) {
            $wh .= " AND a.IdProgram = '" . $post['field8'] . "' ";
        }

        if (isset($post['field23']) && !empty($post['field23'])) {
            $wh .= " AND sm.Scheme = '" . $post['field23'] . "' ";
        }
        //echo $wh; die;
        if ($post == NULL) {
            $lstrSelect = $lstrSelect->where($wh)->group('a.IdStudentRegistration');
        } else {
            $lstrSelect = $lstrSelect
                            //->joinLeft(array('ss'=>'tbl_studentsemesterstatus'),'ss.IdStudentRegistration = a.IdStudentRegistration',array(''))
                            ->where($wh)->group('a.IdStudentRegistration');
            //echo $lstrSelect; echo '</br>';
        }

        //echo $lstrSelect; echo '</br>';

        $result = $db->fetchAll($lstrSelect);
        //asd($result);
        return $result;
    }

    /**
     * Function to get semester master data based on schemeID
     * @author Vipul
     */
    public function fetchSemMasterUnique($schemeID, $applicationDate, $ProgramOfferred) {
        $firstArr = $secondArr = $thirdArr = array();
        $db = Zend_Db_Table::getDefaultAdapter();
        $where_schemeDate_condition = " ( a.Scheme = '" . $schemeID . "' ) AND ( SemesterMainStatus!='0')   AND ( '" . $applicationDate . "' BETWEEN a.SemesterMainStartDate AND a.SemesterMainEndDate ) ";
        $sql = $db->select()
                        ->from(array('a' => 'tbl_semestermaster'), array('a.*'))
                        ->where($where_schemeDate_condition);
        //echo $sql;
        $semMasterlist = $db->fetchAll($sql);

        if (count($semMasterlist) > 0) {
            foreach ($semMasterlist as $value1) {

                //$secondArr = array('key' => $value1['IdSemesterMaster'] . '_main', 'value' => $value1['SemesterMainName'], 'SemesterStartDate' => $value1['SemesterMainStartDate'], 'SemesterEndDate' => $value1['SemesterMainEndDate']);
                //array_push($thirdArr,$secondArr);

                $IdSemesterMaster = $value1['IdSemesterMaster'];
                $where_semmasterDate_condition = " ( b.Semester = '" . $IdSemesterMaster . "' ) AND ( SemesterStatus!='0') AND (b.Program ='" . $ProgramOfferred . "')  AND ( '" . $applicationDate . "' BETWEEN b.SemesterStartDate AND b.SemesterEndDate ) ";
                $semDetailslist = $this->fetchSemDetails($where_semmasterDate_condition);
                if (count($semDetailslist) > 0) {

                    foreach ($semDetailslist as $value2) {
                        $firstArr = array('key' => $value2['IdSemester'] . '_detail', 'value' => $value2['SemesterCode'], 'SemesterStatus' => $value2['SemesterStatus']);
                        array_push($thirdArr, $firstArr);
                    }
                } else {
                    $secondArr = array('key' => $value1['IdSemesterMaster'] . '_main', 'value' => $value1['SemesterMainName'], 'SemesterStatus' => $value1['SemesterMainStatus']);
                    array_push($thirdArr, $secondArr);
                }
            }
        }
        return $thirdArr;
    }

    /**
     * Function to get semester master data based on schemeID
     * @author Vipul
     */
    public function fetchSemUnique($semID, $regID, $type) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $where = " ( a.IdStudentRegistration = '" . $regID . "' ) ";
        if ($type == 'main') {
            $where .= " AND  ( IdSemesterMain = '" . $semID . "' ) ";
        }
        if ($type == 'detail') {
            $where .= " AND  ( idSemester = '" . $semID . "' ) ";
        }
        $sql = $db->select()
                        ->from(array('a' => 'tbl_studentsemesterstatus'), array('a.*'))
                        ->where($where)
                        ->group('a.IdStudentRegistration');
        //echo $sql; echo '</br>';
        $result = $db->fetchAll($sql);
        return $result;
    }

    /**
     * Function to get last semester based on studentID
     * @author Vipul
     */
    public function fetchlastSem($regID) {
        $db = Zend_Db_Table::getDefaultAdapter();
        //AND  a.studentsemesterstatus='130'
        $where = " ( a.IdStudentRegistration = '" . $regID . "' ) ";
        $sql = $db->select()
                        ->from(array('a' => 'tbl_studentsemesterstatus'), array('a.*'))
                        ->joinLeft(array('sm' => 'tbl_semestermaster'), " ( sm.IdSemesterMaster=a.IdSemesterMain ) ", array("sm.SemesterMainName as SemesterMainnName"))
                        ->joinLeft(array('sj' => 'tbl_semester'), " ( sj.IdSemester=a.idSemester ) ", array("sj.SemesterCode as SemesterDetailName"))
                        ->where($where)
                        ->order('a.IdStudentRegistration DESC')
                        ->limit('1');
        //echo $sql; echo '</br>';
        $result = $db->fetchAll($sql);
        return $result;
    }

    public function getStudentforcredtTransfer($data) {
        $lstrSelect = $this->lobjDbAdpt->select()
                        ->from(array('a' => 'tbl_studentregistration'), array('key' => 'a.IdStudentRegistration', 'name' => "CONCAT_WS(' ',IFNULL(a.FName,''),IFNULL(a.MName,''),IFNULL(a.LName,''))"))
                        ->where("a.profileStatus IN (92,248,253)");

        if ($data['Name'] != '') {
            $wh_condition = "a.FName like '%" . $data['Name'] . "%' OR  a.MName like '%" . $data['Name'] . "%' OR  a.LName like '%" . $data['Name'] . "%' ";
            $lstrSelect = $lstrSelect->where($wh_condition);
        }

        if ($data['StudentId'] != '') {
            $lstrSelect = $lstrSelect->where('a.registrationId like "%" ? "%"', $data['StudentId']);
        }

        if ($data['IdProfileStatus'] != '') {
            $lstrSelect = $lstrSelect->where('a.profileStatus like "%" ? "%"', $data['IdProfileStatus']);
        }

        if ($data['IdSemester'] != '') {
            $where = "a.IdSemesterDetails = '" . $data['IdSemester'] . "' OR a.IdSemesterMain = '" . $data['IdSemester'] . "'";
            $lstrSelect = $lstrSelect->where($where);
        }

        if ($data['IdNric'] != '') {
            $lstrSelect = $lstrSelect->where('a.ExtraIdField1 like "%" ? "%"', $data['IdNric']);
        }
        $result = $this->lobjDbAdpt->fetchAll($lstrSelect);
        return $result;
    }

    public function getStudentRegistrationDetail($id) {
        $sql = $this->lobjDbAdpt->select()
                        ->from(array('a' => 'tbl_studentregistration'), array('IdProgramMajoring','IdBranch','a.registrationId', 'a.IdProgram', "CONCAT_WS(' ',IFNULL(a.FName,''),IFNULL(a.MName,''),IFNULL(a.LName,'')) AS name", "a.ExtraIdField1 AS NRIC", "a.IdProgram", "a.IdIntake", "a.profileStatus", 'a.IdStudentRegistration', 'a.Gender', 'a.HomePhone', 'a.CellPhone','IdApplication','transaction_id'))
                        ->join(array('sp'=>'student_profile'),'a.IdApplication=sp.appl_id')
                        ->joinLeft(array('b' => 'tbl_program'), "a.IdProgram = b.IdProgram", array("b.ProgramName", "b.IdScheme",'IdCollege'))
                        ->joinLeft(array('c' => 'tbl_intake'), "a.IdIntake = c.IdIntake", array("c.IntakeDesc"))
                        ->joinLeft(array('d' => 'tbl_definationms'), "a.profileStatus = d.idDefinition", array("d.DefinitionDesc"))
                        ->joinLeft(array('f' => 'tbl_definationms'), "a.SpecialTreatmentType = f.idDefinition", array("f.DefinitionDesc as specialtreatment"))
                        ->joinLeft(array('e' => 'tbl_scheme'), "b.IdScheme = e.IdScheme", array("e.EnglishDescription"))
                        ->where("a.IdStudentRegistration =?", $id);
       // echo $sql;exit;
        $result = $this->lobjDbAdpt->fetchRow($sql);
        return $result;
    }

    public function getStudentAdvisor($registrationId) {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$sql = $db->select()
    	->from(array('sr' => 'tbl_studentregistration'))
    	->join(array('st'=>'tbl_staffmaster'),'sr.AcademicAdvisor=st.IdStaff',array('AdvisorName'=>'CONCAT(firstname," ",secondname," ",thirdname)','FullName','StaffId'))
    	->joinLeft(array('def'=>'tbl_definationms'),'def.IdDefinition=st.BackSalutation',array('GelarBelakang'=>'def.BahasaIndonesia'))
    	->joinLeft(array('def1'=>'tbl_definationms'),'def1.IdDefinition=st.FrontSalutation',array('GelarDepan'=>'def1.BahasaIndonesia'))
    	->where('sr.IdStudentRegistration = ?', $registrationId);
    	$result = $db->fetchRow($sql);
    	 
    	return $result;
    }
    public function getStudentSemesters($id) {
        $sql = $this->lobjDbAdpt->select()
                        ->from(array('a' => 'tbl_studentsemesterstatus'), array('a.IdSemesterMain', 'a.idSemester'))
                        ->joinLeft(array('d' => 'tbl_definationms'), "a.studentsemesterstatus = d.idDefinition", array("d.DefinitionDesc"))
                        ->joinLeft(array('b' => 'tbl_semestermaster'), "a.IdSemesterMain = b.IdSemesterMaster", array("b.SemesterMainCode"))
                        ->joinLeft(array('c' => 'tbl_semester'), "a.idSemester = c.IdSemester", array("c.SemesterCode"))
                        ->where("a.IdStudentRegistration =?", $id)
                        ->order("a.idstudentsemsterstatus");
        $result = $this->lobjDbAdpt->fetchAll($sql);
        return $result;
    }

    public function updateStudentProfile($id, $data) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $table_update_sa = "tbl_studentregistration";
        $where = 'IdStudentRegistration = ' . $id;
        $db->update($table_update_sa, $data, $where);
    }

    public function updateStudentoldsem($idstudentsemsterstatus, $oldsem) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $table_update_sa = "tbl_studentsemesterstatus";
        $where = 'idstudentsemsterstatus = ' . $idstudentsemsterstatus;
        $db->update($table_update_sa, $oldsem, $where);
    }

    /**
     * FETCH ALL CURRENT Semesters
     */
    public function fnfetchAllCurentSemester($regID, $idProgram, $idScheme) {
        //echo $regID;
        $objsemDmodel = new GeneralSetup_Model_DbTable_Semester();
        $currentDate = date('Y-m-d');
        $finalsemname = $idSemM = $idSemD = '';
        if ($idScheme != '') {
            $where_1 = "( ( sm.Scheme='" . $idScheme . "' )  AND ( '$currentDate' BETWEEN sm.SemesterMainStartDate AND sm.SemesterMainEndDate ) )";
            $sql_1 = $this->lobjDbAdpt->select()
                            ->from(array('sm' => 'tbl_semestermaster'), array('sm.IdSemesterMaster'))
                            ->where($where_1);
            $result_1 = $this->lobjDbAdpt->fetchAll($sql_1);
            $resultFinal_1 = $this->flatten_array($result_1);
            //asd($result_1,false);
        }

        if ($idProgram != '') {
            $where_2 = "( ( sd.Program='" . $idProgram . "' )  AND ( '$currentDate' BETWEEN sd.SemesterStartDate AND sd.SemesterEndDate ) )";
            $sql_2 = $this->lobjDbAdpt->select()
                            ->from(array('sd' => 'tbl_semester'), array('sd.IdSemester'))
                            ->where($where_2);
            $result_2 = $this->lobjDbAdpt->fetchAll($sql_2);
            $resultFinal_2 = $this->flatten_array($result_2);
            //asd($result_2);
        }


        $sql_3 = $this->lobjDbAdpt->select()
                        ->from(array('a' => 'tbl_studentsemesterstatus'), array('a.IdSemesterMain as IdSemesterMaster'))
                        ->where("a.IdStudentRegistration =?", $regID);
        $result_3 = $this->lobjDbAdpt->fetchAll($sql_3);
        $resultFinal_3 = $this->flatten_array($result_3);
        //asd($result_3,false);


        $sql_4 = $this->lobjDbAdpt->select()
                        ->from(array('a' => 'tbl_studentsemesterstatus'), array('a.idSemester as IdSemester'))
                        ->where("a.IdStudentRegistration =?", $regID);
        $result_4 = $this->lobjDbAdpt->fetchAll($sql_4);
        $resultFinal_4 = $this->flatten_array($result_4);
        //asd($result_4);


        $result = array_intersect($resultFinal_1, $resultFinal_3);
        //asd($result,false);

        $result22 = array_intersect($resultFinal_2, $resultFinal_4);
        //asd($result22);

        if (count($result) == 0 && count($result22) == 0) {

            $result_diff = array_diff($resultFinal_1, $resultFinal_3);
            //asd($result_diff);
            if (count($result_diff) > 0) {
                $idSemM = $result_diff[0];
            }

            if (count($result_diff) == 0) {
                $result_diff = array_diff($result_2, $result_4);
                //asd($result_diff);
                if (count($result_diff) > 0) {
                    $idSemD = $result_diff[0];
                }
            }


            if ($idSemM != '') {
                $semName = $objsemDmodel->getsemMainDet($idSemM);
                $finalsemname = $semName[0]['SemesterMainCode'];
            } else if ($idSemD != '') {
                $semName = $objsemDmodel->getsemDet($idSemD);
                $finalsemname = $semName[0]['SemesterCode'];
            }
            //asd($finalsemname);
        }


        return $finalsemname;
    }

    private function flatten_array($mArray) {
        $sArray = array();

        foreach ($mArray as $row) {
            if (!(is_array($row))) {
                if ($sArray[] = $row) {
                    
                }
            } else {
                $sArray = array_merge($sArray, self::flatten_array($row));
            }
        }
        return $sArray;
    }

    public function fnSearchregStudentsstat($larrformData) {
		$sql = $this->lobjDbAdpt->select()
		->from(array('c' => 'tbl_studentregistration'), array("COUNT(c.IdStudentRegistration) as count","c.ApplicationDate"))
		->join(array('b' => 'tbl_program'), "b.IdProgram = c.IdProgram", array('b.IdScheme','b.ProgramName','b.IdProgram'));
		//->join(array('a' => 'tbl_definationms'), "a.idDefinition = b.Award", array('a.idDefinition', 'a.DefinitionDesc'));

		if (!empty($larrformData['Scheme']) && $larrformData['Scheme'] != '') {
			$sql = $sql->joinLeft(array('h' => 'tbl_scheme'), 'h.IdScheme = "' . $larrformData['Scheme'] . '"', array('h.EnglishDescription', 'h.IdScheme'))
			->where("b.IdScheme =?", $larrformData['Scheme']);
		}

		if (!empty($larrformData['field23']) && $larrformData['field23'] != '') {
			if ($larrformData['sem'] == "Main") {
				$sql = $sql->joinLeft(array('d' => 'tbl_studentsemesterstatus'), 'c.IdStudentRegistration = d.IdStudentRegistration', array())
				->joinLeft(array('e' => 'tbl_semestermaster'), 'e.IdSemesterMaster = "' . $larrformData['field23'] . '"', array('e.SemesterMainCode AS semcode','e.IdSemesterMaster AS Idsemmain'))
				->where("d.IdSemesterMain =?", $larrformData['field23']);
			} else if ($larrformData['sem'] == "Detail") {
				$sql = $sql->joinLeft(array('d' => 'tbl_studentsemesterstatus'), 'c.IdStudentRegistration = d.IdStudentRegistration', array())
				->joinLeft(array('f' => 'tbl_semester'), 'f.IdSemester = "' . $larrformData['field23'] . '"', array('f.SemesterCode AS semcode','f.IdSemester AS Idsemdetail'))
				->where("d.idSemester =?", $larrformData['field23']);
			}
		}
		if (!empty($larrformData['field26']) && $larrformData['field26'] != '') {
			$sql = $sql->joinLeft(array('g' => 'tbl_intake'), 'g.IdIntake = "' . $larrformData['field26'] . '"', array('g.IntakeDesc', 'g.IdIntake'))
			->where("c.IdIntake =?", $larrformData['field26']);
		}
    	if (!empty($larrformData['field5']) && $larrformData['field5'] != '') {
			$sql = $sql->where("c.IdProgram =?", $larrformData['field5']);
		}
    	if ((!empty($larrformData['RegistrationDateFrom']) && $larrformData['RegistrationDateFrom'] != '') && (!empty($larrformData['RegistrationDateTo']) && $larrformData['RegistrationDateTo'] != '')) {
			$sql = $sql->where("DATE(c.ApplicationDate) >=?", $larrformData['RegistrationDateFrom'])
					   ->where("DATE(c.ApplicationDate) <=?", $larrformData['RegistrationDateTo']);
		}
    	if ((!empty($larrformData['RegistrationDateFrom']) && $larrformData['RegistrationDateFrom'] != '') && (empty($larrformData['RegistrationDateTo']) && $larrformData['RegistrationDateTo'] == '')) {
			$sql = $sql->where("DATE(c.ApplicationDate) >=?", $larrformData['RegistrationDateFrom']);
		}
    	if ((!empty($larrformData['RegistrationDateTo']) && $larrformData['RegistrationDateTo'] != '') && (empty($larrformData['RegistrationDateFrom']) && $larrformData['RegistrationDateFrom'] == '')) {
			$sql = $sql->where("DATE(c.ApplicationDate) <=?", $larrformData['RegistrationDateTo']);
		}
        $sql->group('DATE(c.ApplicationDate)');
		$sql->group('b.IdProgram'); 
		$result = $this->lobjDbAdpt->fetchAll($sql);
                //echo "<pre>";print_r($result);die;
		return $result;
	}

    public function fngetstudents($larrformData) {
		$sql = $this->lobjDbAdpt->select()
		->from(array('c' => 'tbl_studentregistration'), array('c.registrationId', 'c.IdApplicant','c.FName','c.MName','c.LName','c.ApplicationDate','c.ExtraIdField1','c.Gender','c.CorrsAddressDetails','c.CorrsZip','c.RelativeName'))
		->joinLeft(array('b' => 'tbl_program'), "b.IdProgram = c.IdProgram", array('b.ProgramName as DefinitionDesc'))
		->joinLeft(array('in' => 'tbl_intake'), 'in.IdIntake = c.IdIntake', array('in.IntakeDesc AS StudentIntake'))
		->joinLeft(array('su' => 'tbl_studentregsubjects'), "su.IdStudentRegistration = c.IdStudentRegistration", array("COUNT(su.IdStudentRegSubjects) as RegSub"))
		->joinLeft(array('a' => 'tbl_definationms'),'a.idDefinition = c.Race', array('a.DefinitionDesc AS Race'))
		->joinLeft(array('co' => 'tbl_countries'),'co.idCountry = c.Nationality', array('co.CountryName AS Citizenship'))
		->joinLeft(array('cc' => 'tbl_countries'),'cc.idCountry = c.CorrsCountry', array('cc.CountryName AS Country'))
		->joinLeft(array('cs' => 'tbl_state'),'cs.idState = c.CorrsState', array('cs.StateName AS State'))
		->joinLeft(array('cci' => 'tbl_city'),'cci.idCity = c.CorrsCity', array('cci.CityName AS City'))
		->joinLeft(array('r' => 'tbl_definationms'),'r.idDefinition = c.Religion', array('r.DefinitionDesc AS Religion'));
         
		if (!empty($larrformData['Scheme']) && $larrformData['Scheme'] != '') {
			$sql = $sql->joinLeft(array('h' => 'tbl_scheme'), 'h.IdScheme = "' . $larrformData['Scheme'] . '"', array('h.EnglishDescription', 'h.IdScheme'))
			->where("b.IdScheme =?", $larrformData['Scheme']);
		}
		 
		if (!empty($larrformData['Idsem']) && $larrformData['Idsem'] != '') {
			if ($larrformData['sem'] == "main") {
				$sql = $sql->joinLeft(array('d' => 'tbl_studentsemesterstatus'), 'c.IdStudentRegistration = d.IdStudentRegistration', array())
				->joinLeft(array('e' => 'tbl_semestermaster'), 'e.IdSemesterMaster = "' . $larrformData['Idsem'] . '"', array('e.SemesterMainCode AS semcode','e.IdSemesterMaster AS Idsemmain'))
				->where("d.IdSemesterMain =?", $larrformData['Idsem']);
			} else if ($larrformData['sem'] == "detail") {
				$sql = $sql->joinLeft(array('d' => 'tbl_studentsemesterstatus'), 'c.IdStudentRegistration = d.IdStudentRegistration', array())
				->joinLeft(array('f' => 'tbl_semester'), 'f.IdSemester = "' . $larrformData['Idsem'] . '"', array('f.SemesterCode AS semcode','f.IdSemester AS Idsemdetail'))
				->where("d.idSemester =?", $larrformData['Idsem']);
			}
		}
		if (!empty($larrformData['Intake']) && $larrformData['Intake'] != '') {
			$sql = $sql->joinLeft(array('g' => 'tbl_intake'), 'g.IdIntake = "' . $larrformData['Intake'] . '"', array('g.IntakeDesc', 'g.IdIntake'))
			->where("c.IdIntake =?", $larrformData['Intake']);
		}
		if (!empty($larrformData['program']) && $larrformData['program'] != '') {
			$sql = $sql->where("b.IdProgram =?", $larrformData['program']);
		}
		$sql = $sql->where("DATE(c.UpdDate) = '".$larrformData['dd']."'");
		$sql->group('c.IdStudentRegistration'); 
		$result = $this->lobjDbAdpt->fetchAll($sql);
		return $result;
	}

    
    public function registeredstudentList(){
        $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
        $lstrSelect = $lobjDbAdpt->select()
                    ->from(array("a" => "tbl_studentregistration"), array("key" => "a.IdStudentRegistration", "value" => "CONCAT_WS(' ',IFNULL(a.FName,''),IFNULL(a.MName,''),IFNULL(a.LName,''))"))
                    ->where('a.profileStatus =?',92)
                    ->order("a.FName");
        $larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
        return $larrResult;
    }
    
    
    /**
     * Function to get semester master data based on schemeID
     * @author Vipul
     */
    public function fetchSemMasterCurrentFuture($schemeID, $applicationDate, $ProgramOfferred) {
        $firstArr = $secondArr = $thirdArr = array();
        $db = Zend_Db_Table::getDefaultAdapter();
        $where_schemeDate_condition = " ( a.Scheme = '" . $schemeID . "' )   AND ( ( '" . $applicationDate . "' BETWEEN a.SemesterMainStartDate AND a.SemesterMainEndDate) OR ( '".$applicationDate."'<=a.SemesterMainEndDate )  ) ";
        $sql = $db->select()
                        ->from(array('a' => 'tbl_semestermaster'), array('a.*'))
                        ->where($where_schemeDate_condition);
        //echo $sql;
        $semMasterlist = $db->fetchAll($sql);

        if (count($semMasterlist) > 0) {
            foreach ($semMasterlist as $value1) {

                $secondArr = array('key' => $value1['IdSemesterMaster'] . '_main', 'value' => $value1['SemesterMainCode'], 'SemesterStartDate' => $value1['SemesterMainStartDate'], 'SemesterEndDate' => $value1['SemesterMainEndDate']);
                array_push($thirdArr, $secondArr);

                $IdSemesterMaster = $value1['IdSemesterMaster'];
                $where_semmasterDate_condition = " ( b.Semester = '" . $IdSemesterMaster . "' ) AND (b.Program ='" . $ProgramOfferred . "')  AND ( ( '" . $applicationDate . "' BETWEEN b.SemesterStartDate AND b.SemesterEndDate ) OR ( '".$applicationDate."'<=b.SemesterEndDate ) ) ";
                $semDetailslist = $this->fetchSemDetails($where_semmasterDate_condition);
                if (count($semDetailslist) > 0) {

                    foreach ($semDetailslist as $value2) {
                        $firstArr = array('key' => $value2['IdSemester'] . '_detail', 'value' => $value2['SemesterCode'], 'SemesterStartDate' => $value2['SemesterStartDate'], 'SemesterEndDate' => $value2['SemesterEndDate']);
                        array_push($thirdArr, $firstArr);
                    }
                } else {
                    //$secondArr = array('key' => $value1['IdSemesterMaster'] . '_main', 'value' => $value1['SemesterMainName'], 'SemesterStartDate' => $value1['SemesterMainStartDate'], 'SemesterEndDate' => $value1['SemesterMainEndDate']);
                    //array_push($thirdArr,$secondArr);
                }
            }
        }
        return $thirdArr;
    }
   
    
    // function to get curent sem from semester status table for a student
    public function fngetcursem($id) {
         $where_condition = " ( a.IdStudentRegistration = '" . $id . "' ) ";
         $sql =  $this->lobjDbAdpt->select()
                        ->from(array('a' => 'tbl_studentsemesterstatus'), array('a.*'))
                        ->where($where_condition)
                 ->order('idstudentsemsterstatus DESC')
                 ->limit('1');       
        $semMasterlist =  $this->lobjDbAdpt->fetchAll($sql);
        return $semMasterlist;
    }
    
    
    
    /*yatie*/
    
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	/*
	 * This function to get how many and what are the semester that the student had registered.
	 */
	public function getSemesterRegistration($registrationId){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$sql = $db->select()
                        ->from(array('sr' => 'tbl_studentregistration'), array('registrationId','IdLandscape'))  
                        ->joinLeft(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration')                     
                        ->where('sr.IdStudentRegistration = ?', $registrationId)
                        ->group('IdSemesterMain')
                        ->order("SemesterLevel ASC");
        $result = $db->fetchAll($sql);
        return $result;
	}
	
	/*
	 * This function to get how many and what are the block per semester.
	 */
	public function getBlockBySemester($idLandscape,$semester_level){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$sql = $db->select()
                   ->from(array('lb' => 'tbl_landscapeblock'))
                   ->where('lb.idlandscape= ?',$idLandscape)
                   ->where('lb.semester = ?',$semester_level) ; 
                  
        $result = $db->fetchAll($sql);
        return $result;
	}
	
	/*
	 * This function to get course registered by semester.
	 */
	public function getCourseRegisteredBySemester($registrationId,$idSemesterMain,$idBlock=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$sql = $db->select()
                        ->from(array('sr' => 'tbl_studentregistration'), array('registrationId'))  
                        ->joinLeft(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration')
                        ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('SubjectName','subjectMainDefaultLanguage','BahasaIndonesia','CreditHours','SubCode'))                     
                        ->where('sr.IdStudentRegistration = ?', $registrationId)
                        ->where('srs.IdSemesterMain = ?',$idSemesterMain);   
                                           
        if(isset($idBlock) && $idBlock != ''){ //Block 
        	$sql->where("srs.IdBlock = ?",$idBlock);
        	$sql->order("srs.BlockLevel");
        }  

     
             
        $result = $db->fetchAll($sql);
        return $result;
	}
	
	public function getStudentRegistrationHistory($registrationId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()
                        ->from(array('a' => 'tbl_studentregistration'))                       
                        ->joinLeft(array('c' => 'tbl_program'), 'a.IdProgram = c.IdProgram', array('c.ProgramName', 'c.IdScheme'))
                        ->joinLeft(array('d' => 'tbl_intake'), 'a.IdIntake = d.IdIntake', array('d.IntakeId'))
                        ->joinLeft(array('e' => 'tbl_scheme'), 'e.IdScheme = c.IdScheme', array('e.EnglishDescription'))
                        ->joinLeft(array('f' => 'tbl_landscape'), 'f.IdLandscape = a.IdLandscape', array('f.ProgramDescription'))
                        ->where('a.IdStudentRegistration = ?', $registrationId);
        $result = $db->fetchRow($sql);
        return $result;
    }
    
    public function getStudentRegistrationByNim($nim) {
    	
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$sql = $db->select()
    	->from(array('a' => 'tbl_studentregistration'))
    	->joinLeft(array('c' => 'tbl_program'), 'a.IdProgram = c.IdProgram', array('c.ProgramName', 'c.IdScheme'))
    	->where('a.registrationId = ?', $nim);
    	$result = $db->fetchRow($sql);
    	return $result;
    }
    
    
	/*
	 * This function to get course registered by block level (Block only).
	 */
	public function getCourseRegisteredBySemesterLevel($registrationId,$last_block_level,$idBlock=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
			 	
		$sql = $db->select()
                        ->from(array('sr' => 'tbl_studentregistration'), array('registrationId','IdProgram'))  
                        ->joinLeft(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration')                     
                        ->where('sr.IdStudentRegistration  = ?', $registrationId)
                        ->where('srs.BlockLevel = ?',$last_block_level);                      
                       
        $list_reg_course = $db->fetchAll($sql);

      
        $total_fail=0;
        $status_fail=false;
        foreach($list_reg_course as $course){

        	
	        	if($course["IdSemesterDetails"]!=0 && $course["IdSemesterDetails"]!=''){
	        		$idSemester = $course["IdSemesterDetails"];
	        	}else{
	        		$idSemester = $course["IdSemesterMain"];
	        	}
	        	
	        	if($course["grade_point"]=='') $course["grade_point"]=0;
	        	$result["Pass"] = 0;
	        	
        	    //nak cek ada tak course yg failed
        		 $sqlgrade = $db->select()
	                        ->from(array('gsm' => 'tbl_gradesetup_main')) 
	                        ->joinleft(array('gs' => 'tbl_gradesetup'),'gs.IdGradeSetUp =gsm.IdGradeSetUpMain')                                            
	                        ->where('gsm.IdProgram = ?',$course["IdProgram"] )
	                        ->where('gsm.IdSubject = ?',$course["IdSubject"])
	                        ->where("gsm.IdSemester = ?",$idSemester)
	                        ->where("gs.GradePoint = ?",$course["grade_point"]);  
	                       
	            $result = $db->fetchRow($sqlgrade);   
	            
	          
	             
	            if($result["Pass"]!=1){	
	            	
	            	//kira berapa byk course yg fail
	            	$total_fail = $total_fail+1;
	            	
	            	//set ada subject fail
	            	$status_fail = true;            	
	            }
                        
        }
        
        return array($status_fail,$total_fail);
	}
	
	public function changePreviousBlockStatus($IdStudentRegistration, $idSemester) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $table = "tbl_studentsemesterstatus";
        $where = "IdStudentRegistration = '" . $IdStudentRegistration . "'  AND  studentsemesterstatus  = '130' AND (idSemester='" . $idSemester . "' OR IdSemesterMain = '$idSemester') ";
        $data = array('studentsemesterstatus' => '229');
        $db->update($table, $data, $where);
    }
    
    
/*
	 * This function to get course registered by semester.
	 */
	public function getCourseRegisteredByBlock($registrationId,$idBlock=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		 $sql = $db->select()
                        ->from(array('sr' => 'tbl_studentregistration'), array('registrationId'))  
                        ->joinLeft(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration')   
                        ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('SubjectName','subjectMainDefaultLanguage','BahasaIndonesia','CreditHours','SubCode'))                  
                        ->where('sr.IdStudentRegistration  = ?', $registrationId)
                        ->where("srs.IdBlock = ?",$idBlock)
                        ->order("srs.BlockLevel");
                      
        $result = $db->fetchAll($sql);
        return $result;
	}
	
	
	public function getListStudent($post=NULL) {

    	$session = new Zend_Session_Namespace('sis');
    	$auth = Zend_Auth::getInstance();
    	
        $db = Zend_Db_Table::getDefaultAdapter();

        $lstrSelect = $db->select()->from(array('sa' => 'tbl_studentregistration'), array('sa.*'))
        				->join(array('at'=>'applicant_transaction'),'at.at_trans_id=sa.transaction_id',array('at_pes_id'))
                        ->joinLeft(array('p'=>'student_profile'),'p.appl_id=sa.IdApplication',array('appl_fname','appl_mname','appl_lname','appl_religion'))
                        ->joinLeft(array('deftn' => 'tbl_definationms'), 'deftn.idDefinition=sa.Status', array('deftn.DefinitionCode','Status')) //Application STtsu
                        ->joinLeft(array('defination' => 'tbl_definationms'), 'defination.idDefinition=sa.profileStatus', array('profileStatus'=>'DefinitionCode')) //Application STtsu
                        ->joinLeft(array('prg' => 'tbl_program'), 'prg.IdProgram=sa.IdProgram', array('prg.ArabicName','ProgramName'))
                        ->joinLeft(array('intk' => 'tbl_intake'), 'intk.IdIntake=sa.IdIntake', array('intk.IntakeId'))
                        ->joinLeft(array('s'=>'tbl_staffmaster'),'s.IdStaff = sa.AcademicAdvisor',array('advisor'=>'Fullname'))
                        ->joinLeft(array('lk'=>'sis_setup_detl'),"lk.ssd_id=p.appl_religion",array("religion"=>"ssd_name"))
                        //->where("sa.OldIdStudentRegistration IS NULL")//ini letak siap2 ikut team india
                        ->order("sa.registrationId ASC");
                        
    	if($session->IdRole == 311 || $session->IdRole == 298){ 			
			$lstrSelect->where("prg.IdCollege =?",$session->idCollege);
		}else{
			
			if(isset($post['IdCollege']) && !empty($post['IdCollege'])){
				$lstrSelect->where("prg.IdCollege =?",$post["IdCollege"]);
			}
		}              
	    
		if($session->IdRole == 445 ){ 
						
			$lstrSelect->where("sa.AcademicAdvisor =?",$auth->getIdentity()->IdStaff);
		}		
		

        if (isset($post['applicant_name']) && !empty($post['applicant_name'])) {
         
            $lstrSelect->where("(p.appl_fname LIKE '%". $post['applicant_name']."%'");
            $lstrSelect->orwhere("p.appl_mname LIKE '%". $post['applicant_name']."%'");
            $lstrSelect->orwhere("p.appl_lname LIKE '%". $post['applicant_name']."%')");
        }

        if (isset($post['applicant_nomor']) && !empty($post['applicant_nomor'])) {           
            $lstrSelect->where("sa.registrationId LIKE '%". $post['applicant_nomor']."%'");
        }
        
	    if (isset($post['at_pes_id']) && !empty($post['at_pes_id'])) {           
            $lstrSelect->where("at.at_pes_id LIKE '%". $post['at_pes_id']."%'");
        }        

        if (isset($post['profile_status']) && !empty($post['profile_status'])) {           
            $lstrSelect->where("sa.profileStatus = ?",$post['profile_status']);
        }

        if (isset($post['IdProgram']) && !empty($post['IdProgram'])) {            
            $lstrSelect->where("sa.IdProgram = ?",$post['IdProgram']);
        }        

        if (isset($post['IdIntake']) && !empty($post['IdIntake'])) {           
            $lstrSelect->where("sa.IdIntake = ?",$post['IdIntake']);
        }
        
        
		if (isset($post['student_id']) && !empty($post['student_id'])) {           
            $lstrSelect->where("sa.registrationId = ?",$post['student_id']);
        }
        
		if (isset($post['tagging_status']) && !empty($post['tagging_status'])) {   

			if($post['tagging_status']==1){
				$lstrSelect->where("sa.AcademicAdvisor != 0");
			}else if($post['tagging_status']==2){
				$lstrSelect->where("sa.AcademicAdvisor = 0");
			}            
        }
                
        
        if (isset($post['registrationId_from']) && !empty($post['registrationId_from'])) {          	
        	   $lstrSelect->where("sa.registrationId BETWEEN '". $post['registrationId_from']."' AND '".$post['registrationId_to']."'");
        }
        
        
        
	 	if (isset($post['profileStatus']) && !empty($post['profileStatus'])) {           
            $lstrSelect->where("sa.profileStatus = ?",$post['profileStatus']);
        }
        
		if (isset($post['religion']) && !empty($post['religion'])) {           
            $lstrSelect->where("p.appl_religion = ?",$post['religion']);
        }

       
        //echo $lstrSelect;
        $result = $db->fetchAll($lstrSelect);
        return $result;
    }
    
	public function getNewStudentList($post=NULL) {

    	$session = new Zend_Session_Namespace('sis');
    	 
        $db = Zend_Db_Table::getDefaultAdapter();

        $lstrSelect = $db->select()->from(array('sa' => 'tbl_studentregistration'), array('sa.*'))
                        ->joinLeft(array('p'=>'student_profile'),'p.appl_id=sa.IdApplication',array('appl_fname','appl_mname','appl_lname'))
                        ->joinLeft(array('deftn' => 'tbl_definationms'), 'deftn.idDefinition=sa.Status', array('deftn.DefinitionCode'))
                        ->joinLeft(array('prg' => 'tbl_program'), 'prg.IdProgram=sa.IdProgram', array('prg.ArabicName','ProgramName'))
                        ->joinLeft(array('intk' => 'tbl_intake'), 'intk.IdIntake=sa.IdIntake', array('intk.IntakeId'))
                        ->where("sa.student_type=0") //new student
                        ->order("p.appl_fname ASC");
                        
    	if($session->IdRole == 311 || $session->IdRole == 298){ 			
			$lstrSelect->where("prg.IdCollege =?",$session->idCollege);
		}              

        if (isset($post['applicant_name']) && !empty($post['applicant_name'])) {
         
            $lstrSelect->where("(p.appl_fname LIKE '%". $post['applicant_name']."%'");
            $lstrSelect->orwhere("p.appl_mname LIKE '%". $post['applicant_name']."%'");
            $lstrSelect->orwhere("p.appl_lname LIKE '%". $post['applicant_name']."%')");
        }  

        if (isset($post['IdProgram']) && !empty($post['IdProgram'])) {            
            $lstrSelect->where("sa.IdProgram = ?",$post['IdProgram']);
        }        

        if (isset($post['IdIntake']) && !empty($post['IdIntake'])) {           
            $lstrSelect->where("sa.IdIntake = ?",$post['IdIntake']);
        }
        
	  	if (isset($post['registrationId_from']) && !empty($post['registrationId_from'])) {          	
        	   $lstrSelect->where("sa.registrationId BETWEEN '". $post['registrationId_from']."' AND '".$post['registrationId_to']."'");
        }

       
        //echo $lstrSelect;
        $result = $db->fetchAll($lstrSelect);
        return $result;
    }
    
	public function updateData($data,$id){
		 $this->update($data, 'IdStudentRegistration = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete('IdStudentRegistration = '. (int)$id);
	}
	
	public function revertreg($nim){
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql1="select IdStudentRegistration, IdApplication, `transaction_id`, `registrationId` from tbl_studentregistration  where registrationId='$nim'";
		$result = $db->fetchRow($sql1);
		echo $sql1."<br>"; 

		
		//Update appl_role dlm applicant_profile
		$sql4 ="Update applicant_profile set appl_role = 0 where appl_id='".$result["IdApplication"]."'";
		echo $sql4."<br>";
		$db->query($sql4);
		
		//update applicant_transaction (3 fields at_registration_status,)
		$sql5 ="UPDATE applicant_transaction set at_registration_status=null, at_IdStudentRegistration=0 , at_registration_date=null where at_trans_id=".$result["transaction_id"];
		echo $sql5."<br>";
		$db->query($sql5);
		
		//delete history tbl_student_status_history
		$sql6 ="Delete from tbl_student_status_history where IdStudentRegistration='".$result["IdStudentRegistration"]."'";
		echo $sql6."<br>";
		$db->query($sql6);
		
		//delete student_profile
		$sql7 ="Delete from student_profile where appl_id='".$result["IdApplication"]."'";
		echo $sql7."<br>";
		$db->query($sql7);
		
		//Delete tbl_studentsemesterstatus		
		$sql3 ="Delete from tbl_studentsemesterstatus where IdStudentRegistration='".$result["IdStudentRegistration"]."'";
		echo $sql3."<br>";
		$db->query($sql3);		
		
		//Delete row tbl_studentregistration
		$sql2 = "Delete from tbl_studentregistration where registrationId='$nim'";
		$db->query($sql2);
		echo $sql2."<br>";		
		 
	}
    
	
	public function getTotalRegistration($appl_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()
                        ->from('tbl_studentregistration')
                        ->where('IdApplication = ?', $appl_id);
        $result = $db->fetchAll($sql);
        return count($result);
    }
    
    public function getCountGroupStudents($idGroup){
    
    	$db = Zend_Db_Table::getDefaultAdapter();
    
    	$select =$db->select()
    	->from(array('srs'=>'tbl_studentregsubjects'),array('count'=>'count(Registrationid)'))
    	->join(array('sr'=>'tbl_studentregistration'),'srs.IdStudentRegistration=sr.IdStudentRegistration',array())
    	->where('srs.IdCourseTaggingGroup = ?',$idGroup)//0:No Group
    	->group("idCourseTaggingGroup");
    
    	$row = $db->fetchRow($select);
    	return $row['count'];
    		
    }
    public function getCapacityGroup($idGroup){
    
    	$db = Zend_Db_Table::getDefaultAdapter();
    
    	$select =$db->select()
    	->from(array('ctg'=>'tbl_course_tagging_group'),array('maxstud'))
    	//->join(array('sr'=>'tbl_studentregistration'),'srs.IdStudentRegistration=sr.IdStudentRegistration')
    	->where('ctg.IdCourseTaggingGroup = ?',$idGroup);//0:No Group
    	//->group("idCourseTaggingGroup");
    
    	$row = $db->fetchRow($select);
    	return $row['maxstud'];
    
    }
    public function getLandscape($id) {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	
    	$select =$db->select()
    	->from(array('sr'=>'tbl_studentregistration'),array())
    	->join(array('l'=>'tbl_landscape'),'sr.IdLandscape=l.IdLandscape',array())
    	->join(array('ls'=>'tbl_landscapesubject'),'l.IdLandscape=ls.IdLandscape',array())
    	->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=ls.IdSubject',array('key'=>'IdSubject','value'=>'CONCAT(BahasaIndonesia,"-",SubCode)'))
    	->where('sr.IdStudentRegistration=?',$id)
    	->order('sm.BahasaIndonesia');
    	$row = $db->fetchAll($select);
    	if (!$row) {
    		$select =$db->select()
    		->from(array('sr'=>'tbl_studentregistration'),array())
    		->join(array('l'=>'tbl_landscape'),'sr.IdLandscape=l.IdLandscape',array())
    		->join(array('ls'=>'tbl_landscapeblocksubject'),'l.IdLandscape=ls.IdLandscape',array())
    		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=ls.subjectid',array('key'=>'IdSubject','value'=>'CONCAT(BahasaIndonesia,"-",SubCode)'))
    		->where('sr.IdStudentRegistration=?',$id)
    		->order('sm.BahasaIndonesia');
    	}
    	//echo $select;exit;
    	return $row;
    }
    
    public function isTakenSemFinal($idsdtreg,$landscape) {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	 
    	
    	$sem7 =$db->select()
    	->from(array('l'=>'tbl_landscapeblocksubject'),array('subjectid'))
    	->where('l.blockid=7')
    	->where('l.IdLandscape=?',$landscape);
    	
    	$select =$db->select()
    	->from(array('sr'=>'tbl_studentregsubjects'),array())
    	->join(array('st'=>'tbl_studentregistration'),'sr.IdStudentRegistration=st.IdStudentRegistration')
    	->where('sr.IdSubject in (?)',$sem7)
    	->where('st.IdStudentRegistration=?',$idsdtreg);
    	$row=$db->fetchRow($select);
    	if ($row) return true;else return false;
    }
    
    public function getSubjectSemFinal($idsdtreg,$landscape) {
    	
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$sem7 =$db->select()
    	->from(array('l'=>'tbl_landscapeblocksubject'),array('subjectid'))
    	->where('l.blockid=7')
    	->where('l.IdLandscape=?',$landscape);
    	$row=$db->fetchAll($sem7);
    	return $row;
    	
    }
}

?>
