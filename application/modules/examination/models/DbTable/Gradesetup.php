<?php
class Examination_Model_DbTable_Gradesetup extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_gradesetup';

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}


	public function fnGetProgramName($idProgram){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a'=>'tbl_program'),array("a.ProgramName"))
		->where('a.Active = 1')
		->order("a.ProgramName")
		->where("a.IdProgram = ?",$idProgram);
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}

	public function fnGetProgramNameList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a'=>'tbl_program'),array("key"=>"a.IdProgram","value"=>"a.ProgramName"))
		->where('a.Active = 1')
		->order("a.ProgramName");
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

	public function fnGetLandscapeList($program=null){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a'=>'tbl_landscape'),array("key"=>"a.IdLandscape","value"=>"a.ProgramDescription"))
		->where('a.Active = 1');
		//echo $lstrSelect;die();
		if ($program!=null) $lstrSelect->where('a.IdProgram=?',$program);
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function fnGetgradesetupmain($idprogram,$semesterid=null){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a'=>'tbl_gradesetup_main'))
		->join(array('sm'=>'tbl_semestermaster'),'a.IdSemester=sm.IdSemesterMaster')
		->where('a.Active = 1')
		->where('a.IdProgram = ?',$idprogram)
		->order('sm.SemesterMainStartDate DESC');
		
		//echo var_dump($lstrSelect);exit;
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	public function fnGetSubjectNameList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a'=>'tbl_subjectmaster'),array("key"=>"a.IdSubject","value"=>"CONCAT_WS('-',IFNULL(a.SubjectName,''),IFNULL(a.SubCode,''))"))
		->where('a.Active = 1')
		->order("a.SubjectName");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function fnGetSemesterNameList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a'=>'tbl_semester'),array("key"=>"a.IdSemester","value"=>"CONCAT_WS(' ',IFNULL(b.SemesterMainName,''))"))
		->join(array('b' => 'tbl_semestermaster'),'a.Semester = b.IdSemesterMaster ')
		->where('a.Active = 1');
		//->where('b.Active = 1');
		//  ->order("a.year");
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

	public function fndeletegradesetup($IdGradeSetUp) {  // function to update po details
		$db = Zend_Db_Table::getDefaultAdapter();
		//			$table = "tbl_gradesetup";
		//			$larramounts = array('deleteFlag'=>1);
		//			$where ='IdGradeSetUp = '.$IdGradeSetUp;
		//			$db->update($table,$larramounts,$where);

		$where_del = $this->lobjDbAdpt->quoteInto('IdGradeSetUp = ?', $IdGradeSetUp);
		$db->delete('tbl_gradesetup', $where_del);

	}

	public function fndeleteallgradesetup($IdGradeSetUpMain) {  // function to update po details
		$db = Zend_Db_Table::getDefaultAdapter();
		$where_del = $this->lobjDbAdpt->quoteInto('IdGradeSetUpMain = ?', $IdGradeSetUpMain);
		$db->delete('tbl_gradesetup', $where_del);

	}


	public function fngetProgramDetails() { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("p"=>"tbl_program"))
		->where('p.Active = 1')
		->order("p.ProgramName");
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


	/**
	 *
	 * FUNCTION TO SEARCH GRADE SETup
	 */
	public function fnSearchAcademicStatusDetails($post = array()) { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("p"=>"tbl_program"))
		->joinLeft(array('l'=>'tbl_landscape'),'p.IdProgram   = l.IdProgram',array('l.IdStartSemester'))
		->where('p.ArabicName   like "%" ? "%"',$post['field3']);


		if(isset($post['field5']) && !empty($post['field5']) ){
			$lstrSelect = $lstrSelect->where("p.IdProgram = ?",$post['field5']);
		}
		if(isset($post['field8']) && !empty($post['field8']) ){
			$lstrSelect = $lstrSelect->where("l.IdStartSemester = ?",$post['field8']);
		}
		$lstrSelect->group("p.ProgramName")->order("p.ProgramName");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnSearchGradeSetUpDetails($idProgram) { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("gs"=>"tbl_gradesetup"))
		->join(array("def"=>"tbl_definationms"),'gs.Grade =def.idDefinition',array("def.DefinitionCode as Grade"))
		->join(array('p' => 'tbl_program'),'gs.IdProgram = p.IdProgram',array("p.*"))
		->join(array('a'=>'tbl_semester'),'gs.IdSemester = a.IdSemester',array("CONCAT_WS(' ',IFNULL(b.SemesterMainName,'')) as Semester"))
		->join(array('b' => 'tbl_semestermaster'),'a.Semester = b.IdSemesterMaster')
		->join(array("sm"=>"tbl_subjectmaster"),'gs.IdSubject = sm.IdSubject',array("CONCAT_WS(' - ',IFNULL(sm.SubjectName,''),IFNULL(sm.SubCode,'')) AS SubjectName","sm.IdSubject","sm.SubCode"))
		->join(array('dms' => 'tbl_definationms'),'gs.DescEnglishName  = dms.idDefinition',array("dms.DefinitionDesc as DefinitionDescEng"))
		->join(array('defms' => 'tbl_definationms'),'gs.DescArabicName = defms.idDefinition',array("defms.BahasaIndonesia as DefinitionDescBahas"))
		->where("gs.IdProgram = ?",$idProgram)
		->where("gs.deleteFlag = 0");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}


	public function fnGetCourseList($IdProgram) { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
		->from(array("a" => "tbl_landscape"),array("key"=>"d.IdSubject","value"=>"d.SubjectName"))
		->join(array("b" => "tbl_landscapeblocksubject"),'a.IdLandscape = b.IdLandscape',array(''))
		->joinLeft(array("d" => "tbl_subjectmaster"),'b.subjectid = d.IdSubject',array(''))
		->where("a.IdProgram = ?",$IdProgram);
		return $result = $lobjDbAdpt->fetchAll($select);
	}

	public function fnGetCourseListsub($IdProgram) { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
		->from(array("a" => "tbl_landscape"),array("key"=>"d.IdSubject","value"=>"d.SubjectName"))
		->join(array("c" => "tbl_landscapesubject"),'a.IdLandscape = c.IdLandscape',array(''))
		->joinLeft(array("d" => "tbl_subjectmaster"),'c.IdSubject  = d.IdSubject',array(''))
		->where("a.IdProgram = ?",$IdProgram);

		return $result = $lobjDbAdpt->fetchAll($select);
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
				'IdSubject'=>$larrresultUpdate['IdSubject'],
				'EffectiveDate'=>$larrresultUpdate['EffectiveDate'],
				'GradeDesc'=>$larrresultUpdate['GradeDesc'],
				'GradePoint'=>$larrresultUpdate['GradePoint'],
				'MinPoint'=>$larrresultUpdate['MinPoint'],
				'MaxPoint'=>$larrresultUpdate['MaxPoint'],
				'Grade'=>$larrresultUpdate['Grade'],
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

	public function fnGetGrade() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType=b.idDefType',array("key"=>"a.idDefinition","value"=>"a.DefinitionCode"))
		->where('b.defTypeDesc= "Grade"')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	/**
	 * Function to Add grade list
	 * @@author: VT
	 */

	public function fnAddSubjectGradeSetUp($larrformData) { //Function for adding the user details to the table
		$db = Zend_Db_Table::getDefaultAdapter();
		unset($larrformData['CopyIdProgram']);
		unset($larrformData['CopyFromIdSemester']);
		unset($larrformData['CopyToIdSemester']);
		unset($larrformData['CopySetUp']);
		unset($larrformData['IdGradeSetUp']);
		unset($larrformData['GradeDesc']);
		unset($larrformData['GradePoint']);
		unset($larrformData['MinPoint']);
		unset($larrformData['MaxPoint']);
		//unset($larrformData['Group']);
		unset($larrformData['Rank']);
		unset($larrformData['Pass']);
		unset($larrformData['Countable']);
		unset($larrformData['DescArabicName']);
		unset($larrformData['DescEnglishName']);
		$table = "tbl_gradesetup";
		$countvar=count($larrformData['GradeDescgrid']);
		


		
		$tablemain = 'tbl_gradesetup_main';
		if($larrformData['BasedOn']=='0') {
			$larrAddGradeSetUpMain = array('IdProgram'=>NULL,
					'IdSubject'=>NULL,
					'IdAward'=>$larrformData['IdAward'],
					'IdSemester'=>$larrformData['IdSemester'],
					'IdScheme'=>$larrformData['IdScheme'],
					'BasedOn'=>$larrformData['BasedOn'],
					'Active'=>$larrformData['Active'],
					'UpdDate'=>date('Y-m-d H:i:s'),
					'UpdUser'=>$larrformData['UpdUser']
			);
		} else if($larrformData['BasedOn']=='1') {
			$larrAddGradeSetUpMain = array('IdProgram'=>$larrformData['IdProgram'],
					'IdSubject'=>$larrformData['IdSubject'],
					'IdAward'=>NULL,
					'IdSemester'=>$larrformData['IdSemester'],
					'IdScheme'=>NULL,
					'BasedOn'=>$larrformData['BasedOn'],
					'Active'=>$larrformData['Active'],
					'UpdDate'=>date('Y-m-d H:i:s'),
					'UpdUser'=>$larrformData['UpdUser']
			);
		}
		else if($larrformData['BasedOn']=='2') {
			$larrAddGradeSetUpMain = array('IdProgram'=>$larrformData['IdProgram'],
					'IdSubject'=>NULL,
					'IdAward'=>NULL,
					'IdSemester'=>$larrformData['IdSemester'],
					'IdScheme'=>NULL,
					'BasedOn'=>$larrformData['BasedOn'],
					'Active'=>$larrformData['Active'],
					'UpdDate'=>date('Y-m-d H:i:s'),
					'UpdUser'=>$larrformData['UpdUser']
			);
		}else if($larrformData['BasedOn']=='3') {
			$larrAddGradeSetUpMain = array('IdProgram'=>NULL,
					'IdSubject'=>NULL,
					'IdAward'=>NULL,
					'IdSemester'=>$larrformData['IdSemester'],
					'IdScheme'=>NULL,
					'BasedOn'=>$larrformData['BasedOn'],
					'Active'=>$larrformData['Active'],
					'UpdDate'=>date('Y-m-d H:i:s'),
					'UpdUser'=>$larrformData['UpdUser']
			);
		}
		
		
		//print_r($larrAddGradeSetUpMain);
	
		$insertmainSetup = $db->insert($tablemain,$larrAddGradeSetUpMain);
		$lastinsertID = $db->lastInsertId();
		
		//echo '<br>count:'.$countvar;
		
		for($i=0;$i<$countvar;$i++) {
			
			if($larrformData['Passgrid'][$i] == 'Yes'){
				$Passgrid = 1;
			}else{
				$Passgrid = 0;
			}
			
			if($larrformData['Countablegrid'][$i] == 'No'){
				$Countablegrid = 1;
			}else{
				$Countablegrid = 0;
			}

			$larrAddGradeSetUp = array(
					'IdGradeSetUpMain'=>$lastinsertID,
					'IdProgram'=>NULL,
					'IdSubject'=>$larrformData['IdSubjectgrid'][$i],
					'IdAward'=>NULL,
					'IdSemester'=>NULL,
					'IdScheme'=>NULL,
					'BasedOn'=>$larrformData['BasedOn'],
					'GradeDesc'=>$larrformData['GradeDescgrid'][$i],
					'GradePoint'=>$larrformData['GradePointgrid'][$i],
					'DefaultLanguage'=>$larrformData['DefaultLanguagegrid'][$i],
					'MinPoint'=>$larrformData['MinPointgrid'][$i],
					'MaxPoint'=>$larrformData['MaxPointgrid'][$i],
					'Rank'=>$larrformData['Rankgrid'][$i],
					'Grade'=>$larrformData['Gradegrid'][$i],
					'Pass'=>$Passgrid,
				    'Countable'=>$Countablegrid,
					'deleteFlag'=>0,
					'Active'=>$larrformData['Active'],
					'UpdDate'=>date('Y-m-d H:i:s'),
					'UpdUser'=>$larrformData['UpdUser']
			);
			
			//print_r($larrAddGradeSetUp);
			//$larrAddGradeSetUpx = array('IdGradeSetUpMain'=>1,'IdSubject'=>'1234');
			$db->insert($table,$larrAddGradeSetUp);
			
		}//end for


	}

	/**
	 *  Function to update grade list
	 * @author: VT
	 */

	public function fnUpdateSubjectGradeSetUp($larrformData,$lintIdGrade) { //Function for adding the user details to the table
		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_gradesetup";

		// DELETE ALL grade Lsit relaed to $lintIdGrade
		$where_del = $this->lobjDbAdpt->quoteInto('IdGradeSetUpMain = ?', $lintIdGrade);
		$db->delete('tbl_gradesetup', $where_del);

		//
		$tablemain = 'tbl_gradesetup_main';
		$larrAddGradeSetUpMain = array('IdProgram'=>$larrformData['IdProgram'],
				'IdSubject'=>$larrformData['IdSubject'],
				'IdAward'=>$larrformData['IdAward'],
				'IdSemester'=>$larrformData['IdSemester'],
				'IdLandscape'=>$larrformData['IdLandscape'],
				'IdScheme'=>$larrformData['IdScheme'],
				'BasedOn'=>$larrformData['BasedOn'],
				'Active'=>$larrformData['Active'],
				'UpdDate'=>date('Y-m-d H:i:s'),
				'UpdUser'=>$larrformData['UpdUser']
		);
		$where = 'IdGradeSetUpMain = '.$lintIdGrade;
		$db->update($tablemain,$larrAddGradeSetUpMain,$where);


		$countvar=count($larrformData['GradeDescgrid']);
		for($i=0;$i<$countvar;$i++) {
			if($larrformData['Passgrid'][$i] == 'Yes'){
				$Passgrid = 1;
			}else{
				$Passgrid = 0;
			}
			
		if($larrformData['Countablegrid'][$i] == 'No'){
				$Countgrid = 1;
			}else{
				$Countgrid = 0;
			}

			$larrAddGradeSetUp = array(
					'IdGradeSetUpMain'=>$lintIdGrade,
					'IdProgram'=>NULL,
					'IdSubject'=>$larrformData['IdSubjectgrid'][$i],
					'IdAward'=>NULL,
					'IdSemester'=>NULL,
					'IdScheme'=>NULL,
					'BasedOn'=>$larrformData['BasedOn'],
					'GradeDesc'=>$larrformData['GradeDescgrid'][$i],
					'GradePoint'=>$larrformData['GradePointgrid'][$i],
					'DefaultLanguage'=>$larrformData['DefaultLanguagegrid'][$i],
					'MinPoint'=>$larrformData['MinPointgrid'][$i],
					'MaxPoint'=>$larrformData['MaxPointgrid'][$i],
					'Rank'=>$larrformData['Rankgrid'][$i],
					'Grade'=>$larrformData['Gradegrid'][$i],
					'Pass'=>$Passgrid,
					'Countable'=>$Countgrid,
					'deleteFlag'=>0,
					'Active'=>$larrformData['Active'],
					'UpdDate'=>date('Y-m-d H:i:s'),
					'UpdUser'=>$larrformData['UpdUser']
			);
			//
			$db->insert($table,$larrAddGradeSetUp);
		}


	}



	public function fnAddProgramGradeSetUp($larrformData,$arrayresultsub) { //Function for adding the user details to the table
		$db = Zend_Db_Table::getDefaultAdapter();
		unset($larrformData['CopyIdProgram']);
		unset($larrformData['CopyFromIdSemester']);
		unset($larrformData['CopyToIdSemester']);
		unset($larrformData['CopySetUp']);
		unset($larrformData['IdGradeSetUp']);
		unset($larrformData['GradeDesc']);
		unset($larrformData['GradePoint']);
		unset($larrformData['MinPoint']);
		unset($larrformData['Grade']);
		unset($larrformData['MaxPoint']);
		unset($larrformData['Group']);
		unset($larrformData['Rank']);
		unset($larrformData['Pass']);
		unset($larrformData['DescArabicName']);
		unset($larrformData['DescEnglishName']);
		unset($larrformData['IdSubjectgrid']);

		$table = "tbl_gradesetup";
		$pusharray = self::flatten($arrayresultsub);

		$countvar=count($pusharray);
		for($i=0;$i<$countvar;$i++) {

			$countGrade=count($larrformData['GradeDescgrid']);
			for($j=0;$j<$countGrade;$j++) {
				if($larrformData['Passgrid'][$j] == 'Yes'){
					$Passgrid = 1;
				}else{
					$Passgrid = 0;
				}
				$larrAddGradeSetUp = array('IdProgram'=>$larrformData['IdProgram'],
						'IdSemester'=>$larrformData['IdSemester'],
						//'IdSubject'=>$countvar.$i,
						'IdSubject'=>$pusharray[$i],
						'BasedOn'=>$larrformData['BasedOn'],
						'EffectiveDate'=>$larrformData['EffectiveDate'],
						'GradeDesc'=>$larrformData['GradeDescgrid'][$j],
						'GradePoint'=>$larrformData['GradePointgrid'][$j],
						'MinPoint'=>$larrformData['MinPointgrid'][$j],
						'MaxPoint'=>$larrformData['MaxPointgrid'][$j],
						'Grade'=>$larrformData['Gradegrid'][$j],
						'Rank'=>$larrformData['Rankgrid'][$j],
						'Pass'=>$Passgrid,
						'DescEnglishName'=>$larrformData['DescArabicNamegrid'][$j],
						'DescArabicName'=>$larrformData['DescEnglishNamegrid'][$j],
						'deleteFlag'=>0,
						'Active'=>$larrformData['Active'],
						'UpdDate'=>$larrformData['UpdDate'],
						'UpdUser'=>$larrformData['UpdUser']
				);

				$db->insert($table,$larrAddGradeSetUp);
			}
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

	function flatten($array, $index='key') {
		$return = array();

		if (is_array($array)) {
			foreach ($array as $row) {
				$return[] = $row[$index];
			}
		}

		return $return;
	}

	/**
	 * Function to search grade setup
	 * @author: VT
	 */
	public function fnSearchGradeSetup($post=array()) {

		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("gs"=>"tbl_gradesetup_main"),array("gs.IdGradeSetUpMain","gs.IdProgram","gs.IdSemester","gs.IdSubject","gs.IdScheme","gs.IdAward","gs.BasedOn"))
		->joinLeft(array('sm' => 'tbl_semestermaster'), ' sm.IdSemesterMaster=gs.IdSemester ', array("sm.SemesterMainName"))
		->joinLeft(array('s' => 'tbl_scheme'), ' s.IdScheme=gs.IdScheme ', array("s.EnglishDescription as SchemeName"))
		->joinLeft(array('p' => 'tbl_program'), ' p.IdProgram=gs.IdProgram ', array("p.ProgramName","p.ProgramCode"))
		->joinLeft(array('sub' => 'tbl_subjectmaster'), ' sub.IdSubject=gs.IdSubject ', array("sub.SubjectName","sub.SubCode"))
		->joinLeft(array('dfs' => 'tbl_definationms'), ' dfs.idDefinition=gs.IdAward ', array("dfs.DefinitionDesc as AwardName"));


		if(isset($post['field1']) && !empty($post['field1']) ){
			//            $dataSem = explode('_',$post['field1']);
			//             if($dataSem[1]=='detail') {
			//                 $semDetail = $dataSem[0];
			//                 $semsql = $this->lobjDbAdpt->select()->from(array("semdet" =>"tbl_semester"),array('semdet.SemesterCode'))
			//                                             ->where("semdet.IdSemester = ?",$semDetail);
			//                 $result = $this->lobjDbAdpt->fetchAll($semsql);
			//                 $semesterCode = $result[0]['SemesterCode'];
			//            } else {
			//                 $semMain = $dataSem[0];
			//                 $semsql =  $this->lobjDbAdpt->select()->from(array("semmast" =>"tbl_semestermaster"),array('semmast.SemesterMainCode'))
			//                                             ->where("semmast.IdSemesterMaster = ?",$semMain);
			//                 $result = $this->lobjDbAdpt->fetchAll($semsql);
			//                 $semesterCode = $result[0]['SemesterMainCode'];
			//            }

			$semesterCode = $post['field1'];
			$lstrSelect->where("gs.IdSemester = ?",$semesterCode);
		}


		if(isset($post['field5']) && !empty($post['field5']) ){
			//$lstrSelect->joinLeft(array('s' => 'tbl_scheme'), ' s.IdScheme=gs.IdScheme ', array("s.EnglishDescription as SchemeName"));
			$lstrSelect->where("gs.IdScheme = ?",$post['field5']);
		}
		if(isset($post['field19']) && !empty($post['field19']) ){

			$lstrSelect = $lstrSelect->where("gs.IdAward = ?",$post['field19']);
		}
		if(isset($post['field20']) && !empty($post['field20']) ){
			//$lstrSelect->joinLeft(array('p' => 'tbl_program'), ' p.IdProgram=gs.IdProgram ', array("p.ProgramName"));
			$lstrSelect->where("gs.IdProgram = ?",$post['field20']);
		}
		if(isset($post['field8']) && !empty($post['field8']) ){
			//$lstrSelect->joinLeft(array('sub' => 'tbl_subjectmaster'), ' sub.IdSubject=gs.IdSubject ', array("sub.SubjectName"));
			$lstrSelect->where("gs.IdSubject = ?",$post['field8']);
		}
		if(isset($post['field7']) && !empty($post['field7']) ){
			$lstrSelect = $lstrSelect->where("gs.Active = ?",$post['field7']);
		}

		$lstrSelect->order("gs.IdSemester");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	/**
	 * Function to get program list based on scheme and award
	 * @author: VT
	 */
	public function fnListProgram($lintIdScheme,$lintIdAward){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$wh = "1=1";
		if($lintIdScheme!='' && $lintIdAward=='') {
			$wh = " b.IdScheme = '".$lintIdScheme."' ";
			$lstrSelect = $lobjDbAdpt->select()
			->from(array('a'=>'tbl_program'),array("key"=>"a.IdProgram","value"=>"CONCAT_WS('-',a.ProgramCode,a.ProgramName)"))
			->joinLeft(array('b' => 'tbl_program_scheme'),'a.IdProgram = b.IdProgram',array(''))
			->where('a.Active = 1')
			->where($wh)
			->order("a.ProgramName");
		}else if ($lintIdScheme=='' && $lintIdAward!='') {
			$wh = " a.Award = '".$lintIdAward."' ";
			$lstrSelect = $lobjDbAdpt->select()
			->from(array('a'=>'tbl_program'),array("key"=>"a.IdProgram","value"=>"CONCAT_WS('-',a.ProgramCode,a.ProgramName)"))
			->joinLeft(array('b' => 'tbl_program_scheme'),'a.IdProgram = b.IdProgram',array(''))
			->where('a.Active = 1')
			->where($wh)
			->order("a.ProgramName");
		} else if($lintIdScheme!='' && $lintIdAward!='') {
			$wh = " b.IdScheme = '".$lintIdScheme."' AND a.Award = '".$lintIdAward."' ";
			$lstrSelect = $lobjDbAdpt->select()
			->from(array('a'=>'tbl_program'),array("key"=>"a.IdProgram","value"=>"CONCAT_WS('-',a.ProgramCode,a.ProgramName)"))
			->joinLeft(array('b' => 'tbl_program_scheme'),'a.IdProgram = b.IdProgram',array(''))
			->where('a.Active = 1')
			->where($wh)
			->order("a.ProgramName");
		}else if($lintIdScheme=='' && $lintIdAward=='') {
			$lstrSelect = $lobjDbAdpt->select()
			->from(array('a'=>'tbl_program'),array("key"=>"a.IdProgram","value"=>"CONCAT_WS('-',a.ProgramCode,a.ProgramName)"))
			//->joinLeft(array('b' => 'tbl_program_scheme'),'a.IdProgram = b.IdProgram',array(''))
			->where('a.Active = 1')
			//->where($wh)
			->order("a.ProgramName");
		}
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	/**
	 * Function to get subject list based on program
	 * @author: VT
	 */
	public function fnListSubject($lintIdprogram){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('a'=>'tbl_landscapesubject'),array(""))
		->joinLeft(array('b'=>'tbl_subjectmaster'), 'b.IdSubject = a.IdSubject' ,array("key"=>"b.IdSubject","value"=>"CONCAT_WS('-',IFNULL(b.SubjectName,''),IFNULL(b.SubCode,''))"))
		->where('b.Active = 1')
		->where('a.IdProgram = ?',$lintIdprogram )
		->group("a.IdSubject")
		->order("b.SubjectName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}


	/**
	 * Function to view grade setup list
	 * @author: VT
	 */
	public function fnViewGradeList($lintIdGrade,$countable=null) {


		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->distinct()
		->from(array("gs"=>"tbl_gradesetup"),array("gs.IdGradeSetUp","gs.IdGradeSetUpMain","gs.IdSubject","gs.GradeDesc","gs.GradePoint","gs.MinPoint","gs.MaxPoint","gs.Rank","gs.Pass","gs.DefaultLanguage","gs.Grade",'gs.Countable'))
		->joinLeft(array('sub' => 'tbl_subjectmaster'), ' sub.IdSubject=gs.IdSubject ', array("sub.SubjectName","sub.SubCode"))
		->join(array('b' => 'tbl_definationms'),'b.idDefinition=gs.Grade',array("b.DefinitionDesc as GradeName"))
		//->where('sub.IdSubject is not null and sub.IdSubject!=0')
		->order('gs.GradePoint desc');
		if($lintIdGrade!='') {
			$lstrSelect->where('gs.IdGradeSetUpMain = ?',$lintIdGrade);
		}
		if($countable!=null && $countable!="2") {
			$lstrSelect->where('gs.Countable = "0"');
		}
		if($countable!=null && $countable=="2") {
			$lstrSelect->where('gs.MaxPoint >0');
		}
		//echo $lstrSelect;exit;
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function fnViewGradeListSKPI($lintIdGrade,$countable=null) {
	
	
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("gs"=>"tbl_gradesetup"),array("gs.IdGradeSetUp","gs.IdGradeSetUpMain","gs.IdSubject","gs.GradeDesc","gs.GradePoint","gs.MinPoint","gs.MaxPoint","gs.Rank","gs.Pass","gs.DefaultLanguage","gs.Grade",'gs.Countable'))
		 ->join(array('b' => 'tbl_definationms'),'b.idDefinition=gs.Grade',array("b.DefinitionDesc as GradeName"))
		->order('gs.GradePoint desc');
		if($lintIdGrade!='') {
			$lstrSelect->where('gs.IdGradeSetUpMain = ?',$lintIdGrade);
		}
		if($countable!=null && $countable!="2") {
			$lstrSelect->where('gs.Countable = "0"');
		}
		if($countable!=null && $countable=="2") {
			$lstrSelect->where('gs.MaxPoint >0');
		}
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	/**
	 * Function to check duplicate records
	 * @author: VT
	 */
	public function fncheckDuplicate($BasedOn,$IdProgram,$IdSubject,$IdScheme,$IdAward,$IdSemester,$idlandscape=null) {


		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("gs"=>"tbl_gradesetup_main"),array("gs.*"));
		//->joinLeft(array('sub' => 'tbl_subjectmaster'), ' sub.IdSubject=gs.IdSubject ', array("sub.SubjectName"));

		if($BasedOn=='0') {  // 0 is Scheme & Award
			$lstrSelect->where('gs.IdScheme = ?',$IdScheme)->where('gs.IdAward = ?',$IdAward)->where('gs.IdSemester = ?',$IdSemester)->where('gs.BasedOn = ?',$BasedOn);
		} else  if ($BasedOn=='1') { // 1 is Program & Subject
			$lstrSelect->where('gs.IdProgram = ?',$IdProgram)->where('gs.IdSubject = ?',$IdSubject)->where('gs.IdSemester = ?',$IdSemester)->where('gs.BasedOn = ?',$BasedOn);
		} else if ($BasedOn=='2')  {  // 2 is Program
			$lstrSelect->where('gs.IdProgram = ?',$IdProgram)->where('gs.IdSemester = ?',$IdSemester)->where('gs.BasedOn = ?',$BasedOn);
		} else if($BasedOn=='3'){
			$lstrSelect->where('gs.IdSemester = ?',$IdSemester)->where('gs.BasedOn = ?',$BasedOn);
		}
		if ($idlandscape!=null) $lstrSelect->where('gs.IdLandscape = ?',$idlandscape);
		//echo $lstrSelect;
	
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}


	/**
	 * Function to view grade setup Main details
	 * @author: VT
	 */

	public function fnviewGradeSetUpMain($linIdGradeSetUp) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
		->from(array("a" => "tbl_gradesetup_main"),array("a.*"))
		->where("a.IdGradeSetUpMain = ?",$linIdGradeSetUp);
		return $result = $lobjDbAdpt->fetchRow($select);
	}


	/**
	 * Function to view all grades desc
	 * @author: VT
	 */

	public function fngetAllgradeDesc($lintIdGrade) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()->from(array("a" => "tbl_gradesetup"),array("a.GradeDesc"))->where("a.IdGradeSetUpMain = ?",$lintIdGrade);
		return $result = $lobjDbAdpt->fetchAll($select);
	}

	public function fnCopyGradelist($finalresult,$larrformData,$userId) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$tablemain = 'tbl_gradesetup_main';
		$larrAddGradeSetUpMain = array('IdProgram'=>$larrformData['CopyToIdProgram'],
				'IdSubject'=>$larrformData['CopyToIdSubject'],
				'IdAward'=>$larrformData['CopyToIdAward'],
				'IdSemester'=>$larrformData['CopyToIdSemester'],
				'IdScheme'=>$larrformData['CopyToIdScheme'],
				'BasedOn'=>$larrformData['CopyBasedOn'],
				'Active'=>'1',
				'UpdDate'=>date('Y-m-d H:i:s'),
				'UpdUser'=>$userId
		);
		$insertmainSetup = $lobjDbAdpt->insert($tablemain,$larrAddGradeSetUpMain);
		$lastinsertID = $lobjDbAdpt->lastInsertId();

		foreach($finalresult as $values) {
			$larrAddGradeSetUp = array(
					'IdGradeSetUpMain'=>$lastinsertID,
					'IdProgram'=>NULL,
					'IdSubject'=>NULL,
					'IdAward'=>NULL,
					'IdSemester'=>NULL,
					'IdScheme'=>NULL,
					'BasedOn'=>$larrformData['CopyBasedOn'],
					'GradeDesc'=>$values['GradeDesc'],
					'GradePoint'=>$values['GradePoint'],
					'DefaultLanguage'=>$values['DefaultLanguage'],
					'MinPoint'=>$values['MinPoint'],
					'MaxPoint'=>$values['MaxPoint'],
					'Rank'=>$values['Rank'],
					'Grade'=>$values['Grade'],
					'Pass'=>$values['Pass'],
					'deleteFlag'=>0,
					'Active'=>'1',
					'UpdDate'=>date('Y-m-d H:i:s'),
					'UpdUser'=>$userId
			);
			$lobjDbAdpt->insert('tbl_gradesetup',$larrAddGradeSetUp);
		}

	}

	


}