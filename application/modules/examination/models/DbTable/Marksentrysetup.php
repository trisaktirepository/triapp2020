<?php
class Examination_Model_DbTable_Marksentrysetup extends Zend_Db_Table { //Model Class for Users Details
	//protected $_name = 'tbl_marksentrysetup';

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function fnAddMarksEntry($larrformData) { //Function for adding the user details to the table
		$this->insert($larrformData);
	}

	public function fnEditMarksEntry($idSubject) { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
		->from(array("a" => "tbl_marksentrysetup"),array("a.*","DATE_FORMAT(a.EffectiveDate,'%d-%m-%Y') as EffectiveDate"))
		->join(array("b" =>"tbl_subjectmaster"),'a.IdSubject = b.IdSubject',array("CONCAT_WS(' - ',IFNULL(b.SubjectName,''),IFNULL(b.SubCode,'')) AS SubjectName"))
		->join(array("c" =>"tbl_staffmaster"),'a.IdStaff = c.IdStaff')
		->where("a.IdSubject = ?",$idSubject);
		return $result = $lobjDbAdpt->fetchAll($select);
	}

	public function fnupdateMarksEntry($lintIdMarksEntrySetup,$larrformData) { //Function for updating the user
		$where = 'IdMarksEntrySetup = '.$lintIdMarksEntrySetup;
		$this->update($larrformData,$where);
	}
	
	/**
	 *  Function to search the student of the group with lecturer
	 */
	public function fnSearchStudentofGroup($post = array()){		
		$db = Zend_Db_Table::getDefaultAdapter();
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
					->from(array("studreg" =>"tbl_studentregistration"),array('studreg.registrationId','studreg.IdProgram','studreg.IdStudentRegistration'))
					->join(array("cgsm" =>"tbl_course_group_student_mapping"),'studreg.IdStudentRegistration = cgsm.IdStudent',array(''))	
					->join(array("ctg" =>"tbl_course_tagging_group"),'cgsm.IdCourseTaggingGroup = ctg.IdCourseTaggingGroup',array(''))
					->join(array("sm" =>"tbl_semestermaster"),'ctg.SemCode = sm.SemesterMainCode',array(''))
	   				->join(array("subj" =>"tbl_subjectmaster"),'subj.IdSubject = ctg.IdSubject',array('subj.SubjectName','subj.SubCode','subj.CreditHours as SubjectCreditHours'))
					->joinLeft(array("prg" =>"tbl_program"),'prg.IdProgram = studreg.IdProgram',array('prg.ProgramName','prg.ProgramCode'))
	   				->joinLeft(array("studregsubj"=>"tbl_studentregsubjects"),'studregsubj.IdStudentRegistration = studreg.IdStudentRegistration',array('studregsubj.IdStudentRegSubjects','studregsubj.IdSubject','studregsubj.IdSemesterMain','studregsubj.IdSemesterDetails'));
		$select->where("cgsm.IdCourseTaggingGroup = ?",$post['field26']);	

		if($post['field29'] != ''){
	   		$select->where("ctg.IdLecturer = ?",$post['field29']);
	   	}
	   	
	   	$select->where("studregsubj.IdSubject = ?",$post['field23']);
	   	
	   	$select->where("studreg.profileStatus = ?",'92')
				 ->orwhere("studreg.profileStatus = ?",'248')
				 ->orwhere("studreg.profileStatus = ?",'253');
				 
		if(isset($post['field27']) && !empty($post['field27']) ){
			$dataSem = explode('_',$post['field27']);
			if($dataSem[1]=='detail') {
				$semDetail = $dataSem[0];
				$select = $select->where("studregsubj.IdSemesterDetails = ?",$semDetail);
			} else {
				$semMain = $dataSem[0];
				$select = $select->where("studregsubj.IdSemesterMain = ?",$semMain);
			}
		}		
		$select->group('studreg.registrationId');
		$result = $db->fetchAll($select);		
		return $result;
	}

	/**
	 * Functio to search student and their course components
	 * @author: VT
	 */

	public function fnSearchMarksEntry($post = array(),$instructor=NULL) { //Function for searching the user details
		$db = Zend_Db_Table::getDefaultAdapter();

		$select = $db->select()
		->from(array("studregsubj"=>"tbl_studentregsubjects"),array('studregsubj.IdStudentRegSubjects','studregsubj.IdStudentRegistration','studregsubj.IdSubject','studregsubj.IdSemesterMain','studregsubj.IdSemesterDetails'))
		->join(array("studreg" =>"tbl_studentregistration"),'studreg.IdStudentRegistration = studregsubj.IdStudentRegistration',array('studreg.registrationId','studreg.IdProgram'))
		->join(array("subj" =>"tbl_subjectmaster"),'subj.IdSubject = studregsubj.IdSubject',array('subj.SubjectName','subj.SubCode','subj.CreditHours as SubjectCreditHours'))
		// to do  // ->join(array("subjstaff" =>"tbl_subjectcoordinatorlist")," subjstaff.IdSubject  = studregsubj.IdSubject AND subjstaff.IdStaff = '".$instructor."' ",array('subjstaff.IdStaff'))
		->joinLeft(array("prg" =>"tbl_program"),'prg.IdProgram = studreg.IdProgram',array('prg.ProgramName','prg.ProgramCode'))
		->joinLeft(array("lscp" =>"tbl_landscape"),'studreg.IdLandscape = lscp.IdLandscape',array('lscp.Scheme'))
		->joinLeft(array("sch" =>"tbl_scheme"),'lscp.Scheme = prg.IdScheme',array('sch.EnglishDescription as SchemeName'))
		;



		if(isset($post['field23']) && !empty($post['field23']) ){
			$select = $select->where("studregsubj.IdSubject = ?",$post['field23']);
		}

		if(isset($post['field27']) && !empty($post['field27']) ){
			$dataSem = explode('_',$post['field27']);
			if($dataSem[1]=='detail') {
				$semDetail = $dataSem[0];
				$select = $select->where("studregsubj.IdSemesterDetails = ?",$semDetail);
			} else {
				$semMain = $dataSem[0];
				$select = $select->where("studregsubj.IdSemesterMain = ?",$semMain);
			}
		}

		if(isset($post['field2']) && !empty($post['field2']) ){
			$select = $select->where("studreg.registrationId = ?",$post['field2']);
		}

		if(isset($post['field2']) && !empty($post['field2']) &&  $post['field23']==''  &&  $post['field27']=='' &&  $post['field24']=='' &&  $post['field25']==''  ){
			$select1 = $db->select()->from(array("studreg1"=>"tbl_studentregistration"))->where("studreg1.registrationId = ?",$post['field2']);

			$result1 = $db->fetchAll($select1);
			$studentID = $result1[0]['IdStudentRegistration'];
			$select = $select->where("studregsubj.IdStudentRegistration = ?",$studentID);
		} else {
			if(isset($post['field2']) && !empty($post['field2'])) {
				$select = $select->where("studreg.registrationId = ?",$post['field2']);
			}
		}




		if(isset($post['field24']) && !empty($post['field24']) ){
			$select = $select->where("studreg.IdProgram = ?",$post['field24']);
		}

		if(isset($post['field25']) && !empty($post['field25']) ){
			$select = $select->where("sch.IdScheme = ?",$post['field25']);
		}

		if(isset($post['field2']) && empty($post['field2'])) {
			$select->group('studreg.registrationId');
		}

		if(isset($post['field2']) && !empty($post['field2'])) {
			$select->group('studregsubj.IdSubject');
		}

		// only active, defer and dormant students are eligible for marks entry.
		$select->where("studreg.profileStatus = ?",'92')
				 ->orwhere("studreg.profileStatus = ?",'248')
				 ->orwhere("studreg.profileStatus = ?",'253');

		$result = $db->fetchAll($select);
		return $result;
	}

	public function fngetstaffname($staffid,$IdSubject) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
		->from(array("u"=>"tbl_marksentrysetup"),array("u.IdStaff"))
		->where("u.IdStaff = ?",$staffid)
		->where("u.IdSubject = ?",$IdSubject);
		return $result = $lobjDbAdpt->fetchRow($select);
	}


	public function fnfetchAllComponents($IdSubject,$IdStudentRegistration,$IdStudentRegSubjects) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("subjcomp"=>"tbl_subcredithoursdistrbtn"),array("subjcomp.*"))
		->joinLeft(array("compdef" =>"tbl_examination_assessment_type"),'compdef.IdExaminationAssessmentType = subjcomp.Idcomponents',array('compdef.Description as ComponentName'))
		->joinLeft(array("compdef4" =>"tbl_examination_assessment_item"),'compdef4.IdExaminationAssessmentType = subjcomp.IdcomponentItem',array('compdef4.Description as MainComponentItemName'))
		->joinLeft(array("sme" =>"tbl_student_marks_entry"),"sme.Component = subjcomp.Idcomponents AND sme.ComponentItem = subjcomp.IdcomponentItem  AND sme.IdStudentRegistration='".$IdStudentRegistration."' AND sme.IdStudentRegSubjects='".$IdStudentRegSubjects."'  ",array('sme.AttendanceStatus','sme.MarksEntryStatus','sme.TotalMarkObtained'))
		->joinLeft(array("compdef3" =>"tbl_definationms"),'compdef3.idDefinition = sme.AttendanceStatus',array('compdef3.DefinitionDesc as AttendanceStatusName'))
		->joinLeft(array("compdef2" =>"tbl_definationms"),'compdef2.idDefinition = sme.MarksEntryStatus',array('compdef2.DefinitionDesc as MarksEntryStatusName'))
		->where("subjcomp.IdSubject = ?",$IdSubject);
		return $result = $lobjDbAdpt->fetchAll($select);
	}


	public function fnSearchMarksEntryDetails($idcourse, $idprogram, $semester,$idstudent, $idScheme, $idFaculty, $instructor=NULL) {
		// to do instructor coding for join tbl_student_marks_entry
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array("marksmaster"=>"tbl_marksdistributionmaster"),array('marksmaster.IdMarksDistributionMaster','marksmaster.IdComponentType as IdComponentTypeMain','marksmaster.IdComponentItem as IdComponentItemMain','marksmaster.Marks as MarksTotal'))
		->joinLeft(array("compdef" =>"tbl_examination_assessment_type"),'compdef.IdExaminationAssessmentType = marksmaster.IdComponentType',array('compdef.Description as ComponentName'))
		->joinLeft(array("compdef2" =>"tbl_examination_assessment_item"),'compdef2.IdExaminationAssessmentType = marksmaster.IdComponentItem',array('compdef2.Description as ComponentItemName'))
		//->join(array("subjstaff" =>"tbl_subjectcoordinatorlist")," subjstaff.IdSubject  = '".$idcourse."' AND subjstaff.IdStaff = '".$instructor."' ",array('subjstaff.IdStaff'))
		->joinLeft(array("sme" =>"tbl_student_marks_entry"),"sme.Component = marksmaster.IdComponentType  AND sme.ComponentItem = marksmaster.IdComponentItem AND sme.Course='".$idcourse."' AND sme.IdStudentRegistration = '".$idstudent."' ",array('sme.AttendanceStatus','sme.MarksEntryStatus','sme.TotalMarkObtained'))
		->joinLeft(array("examcal" =>"tbl_exam_calendar"),"marksmaster.IdComponentType = examcal.AssessmentType  AND examcal.Semester = '".$semester."'   ",array('examcal.StartDate','examcal.EndDate'))
		->where("marksmaster.IdProgram = ?",$idprogram)
		->where("marksmaster.IdCourse = ?",$idcourse)
		//->where("marksmaster.semester = ?",$semester)
		->where("marksmaster.IdScheme = ?",$idScheme)
		->where("marksmaster.IdFaculty = ?",$idFaculty)
		->group("marksmaster.IdMarksDistributionMaster");

		$result = $db->fetchAll($select);
		$total = count($result);

		if($total=='0') {
			// ALL scheme, all faculty
			$select = $db->select()
			->from(array("marksmaster"=>"tbl_marksdistributionmaster"),array('marksmaster.IdMarksDistributionMaster','marksmaster.IdComponentType as IdComponentTypeMain','marksmaster.IdComponentItem as IdComponentItemMain','marksmaster.Marks as MarksTotal'))
			->joinLeft(array("compdef" =>"tbl_examination_assessment_type"),'compdef.IdExaminationAssessmentType = marksmaster.IdComponentType',array('compdef.Description as ComponentName'))
			->joinLeft(array("compdef2" =>"tbl_examination_assessment_item"),'compdef2.IdExaminationAssessmentType = marksmaster.IdComponentItem',array('compdef2.Description as ComponentItemName'))
			// ->join(array("subjstaff" =>"tbl_subjectcoordinatorlist")," subjstaff.IdSubject  = '".$idcourse."' AND subjstaff.IdStaff = '".$instructor."' ",array('subjstaff.IdStaff'))
			->joinLeft(array("sme" =>"tbl_student_marks_entry"),"sme.Component = marksmaster.IdComponentType AND sme.ComponentItem = marksmaster.IdComponentItem AND sme.Course='".$idcourse."' AND sme.IdStudentRegistration = '".$idstudent."' ",array('sme.AttendanceStatus','sme.MarksEntryStatus','sme.TotalMarkObtained'))
			->joinLeft(array("examcal" =>"tbl_exam_calendar"),"marksmaster.IdComponentType = examcal.AssessmentType  AND examcal.Semester = '".$semester."'   ",array('examcal.StartDate','examcal.EndDate'))
			->where("marksmaster.IdCourse = ?",$idcourse)
			// ->where("marksmaster.semester = ?",$semester)
			->where("marksmaster.IdProgram = ?",$idprogram)
			->where("marksmaster.IdScheme = ?",'0')
			->where("marksmaster.IdFaculty = ?",'0')
			->group("marksmaster.IdMarksDistributionMaster");
			//;
			//echo $select;
			$result = $db->fetchAll($select);
			$total2 = count($result);
			if($total2=='0') {
				// ALL scheme, all faculty, All program
				$select = $db->select()
				->from(array("marksmaster"=>"tbl_marksdistributionmaster"),array('marksmaster.IdMarksDistributionMaster','marksmaster.IdComponentType as IdComponentTypeMain','marksmaster.IdComponentItem as IdComponentItemMain','marksmaster.Marks as MarksTotal'))
				->joinLeft(array("compdef" =>"tbl_examination_assessment_type"),'compdef.IdExaminationAssessmentType = marksmaster.IdComponentType',array('compdef.Description as ComponentName'))
				->joinLeft(array("compdef2" =>"tbl_examination_assessment_item"),'compdef2.IdExaminationAssessmentType = marksmaster.IdComponentItem',array('compdef2.Description as ComponentItemName'))
				// ->join(array("subjstaff" =>"tbl_subjectcoordinatorlist")," subjstaff.IdSubject  = '".$idcourse."' AND subjstaff.IdStaff = '".$instructor."' ",array('subjstaff.IdStaff'))
				->joinLeft(array("sme" =>"tbl_student_marks_entry"),"sme.Component = marksmaster.IdComponentType  AND sme.ComponentItem = marksmaster.IdComponentItem AND sme.Course='".$idcourse."' AND sme.IdStudentRegistration = '".$idstudent."' ",array('sme.AttendanceStatus','sme.MarksEntryStatus','sme.TotalMarkObtained'))
				->joinLeft(array("examcal" =>"tbl_exam_calendar"),"marksmaster.IdComponentType = examcal.AssessmentType  AND examcal.Semester = '".$semester."'   ",array('examcal.StartDate','examcal.EndDate'))
				->where("marksmaster.IdCourse = ?",$idcourse)
				//->where("marksmaster.semester = ?",$semester)
				->where("marksmaster.IdProgram = ?",'0')
				->where("marksmaster.IdScheme = ?",'0')
				->where("marksmaster.IdFaculty = ?",'0')
				->group("marksmaster.IdMarksDistributionMaster");
				$result = $db->fetchAll($select);

			}

		}


		return $result;
	}

	public function fnfetchAllComponentsDetails($IdMarksDistributionMaster,$idcourse ,$idcomp, $idcompitem, $IdStudentRegistration) {

		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("marksdetails"=>"tbl_marksdistributiondetails"),array("marksdetails.*"))
		->joinLeft(array("assmtype" =>"tbl_examination_assessment_type"),'assmtype.IdExaminationAssessmentType = marksdetails.IdComponentType',array('assmtype.Description as ComponentTypeName'))
		->joinLeft(array("assmitem" =>"tbl_examination_assessment_item"),'assmitem.IdExaminationAssessmentType = marksdetails.IdComponentItem',array('assmitem.Description as ComponentItemName'))
		->joinLeft(array("sme" =>"tbl_student_marks_entry"),"sme.Course = '".$idcourse."' AND  sme.Component = '".$idcomp."'  AND  sme.ComponentItem = '".$idcompitem."' AND sme.IdStudentRegistration = '".$IdStudentRegistration."'  ",array('sme.IdStudentMarksEntry'))
		->joinLeft(array("sdme" =>"tbl_student_detail_marks_entry")," sdme.IdStudentMarksEntry = sme.IdStudentMarksEntry AND sdme.Component = '".$idcomp."'AND sdme.ComponentItem = '".$idcompitem."' AND sdme.ComponentDetail = marksdetails.IdMarksDistributionDetails",array('sdme.MarksObtained as MarksObtainedDetail'))
		->where("marksdetails.IdMarksDistributionMaster = ?",$IdMarksDistributionMaster);
		// echo $select ; die;
		return $result = $lobjDbAdpt->fetchAll($select);
	}



	public function fnfetchAllComponentsDetailsIndividual($IdMarksDistributionMaster,$idcourse ,$Idcmp, $idstudent, $Idcmpitem) {

		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("marksdetails"=>"tbl_marksdistributiondetails"),array("marksdetails.*"))
		->joinLeft(array("assmtype" =>"tbl_examination_assessment_type"),'assmtype.IdExaminationAssessmentType = marksdetails.IdComponentType',array('assmtype.Description as ComponentTypeName'))
		->joinLeft(array("assmitem" =>"tbl_examination_assessment_item"),'assmitem.IdExaminationAssessmentType = marksdetails.IdComponentItem',array('assmitem.Description as ComponentItemName'))
		->joinLeft(array("sme" =>"tbl_student_marks_entry"),"sme.Course = '".$idcourse."' AND  sme.Component = '".$Idcmp."' AND sme.ComponentItem='".$Idcmpitem."' AND sme.IdStudentRegistration = '".$idstudent."'  ",array('sme.IdStudentMarksEntry'))
		->joinLeft(array("sdme" =>"tbl_student_detail_marks_entry")," sme.Component = '".$Idcmp."' AND sdme.ComponentItem='".$Idcmpitem."' AND sdme.ComponentDetail = marksdetails.IdMarksDistributionDetails AND sdme.IdStudentMarksEntry = sme.IdStudentMarksEntry",array('sdme.MarksObtained as MarksObtainedDetail'))
		->where("marksdetails.IdMarksDistributionMaster = ?",$IdMarksDistributionMaster)
		//->group('')
		;

		return $result = $lobjDbAdpt->fetchAll($select);
	}


	public function fnInsertComponentIndividual($larrformData,$marksentrysttaus,$IdStudentRegSubjects,$instructor=NULL) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();

		// DELETE FIRST
		$studentID = $larrformData['IdStudentRegistration'];
		$IdStudentRegSubjects = $larrformData['IdStudentRegSubjects'];
		$select = $lobjDbAdpt->select()
		->from(array("sme"=>"tbl_student_marks_entry"),array("sme.IdStudentMarksEntry"))
		->where("sme.IdStudentRegSubjects = ?",$IdStudentRegSubjects)
		->where("sme.IdStudentRegistration = ?",$studentID);
		//->where("sme.Instructor = ?",$instructor) to do
		$resultData = $lobjDbAdpt->fetchAll($select);		
		if(!empty($resultData)){
			foreach($resultData as $del) {
				$id = $del['IdStudentMarksEntry'];
				$where_del1 = $this->lobjDbAdpt->quoteInto('IdStudentMarksEntry = ?', $id);
				$lobjDbAdpt->delete('tbl_student_detail_marks_entry', $where_del1);
			}
			$where_del2 = "IdStudentRegSubjects = ".$IdStudentRegSubjects." AND IdStudentRegistration = ".$studentID;
			//$where_del2 = $this->lobjDbAdpt->quoteInto('IdStudentRegSubjects = ?', $IdStudentRegSubjects , 'IdStudentRegistration = ?', $studentID);
										 
			$lobjDbAdpt->delete('tbl_student_marks_entry', $where_del2);
		}		
		// DELETE ENDS

		$component = $larrformData['Component'];
		$totalComp = count($larrformData['Component']);
		for($i=0;$i<$totalComp;$i++) {

			$component = $larrformData['Component'][$i];
			$componentItem = $larrformData['MainComponentItem'][$i];

			$checkBoxValue = $larrformData['cb_'.$component];
			if($checkBoxValue=='1') {

					$formData = array(
										'IdSemester' => $larrformData['IdSemester'],
										'Course' => $larrformData['Course'],
										'Component' => $component,
										'ComponentItem' => $componentItem,
										'Instructor' => $larrformData['Instructor'],
										'IdStudentRegistration' => $larrformData['IdStudentRegistration'],
										'IdStudentRegSubjects' => $IdStudentRegSubjects,
										'AttendanceStatus' => $larrformData['AttendanceStatus_'.$component.'_'.$componentItem],
										'TotalMarkObtained' => $larrformData['SingleMarks_'.$component.'_'.$componentItem],
										'MarksTotal' => $larrformData['SingleTMarks_'.$component.'_'.$componentItem],
										'MarksEntryStatus' => $marksentrysttaus, // 281 0r 311 is for entry
										'UpdUser' => $larrformData['UpdUser'],
										'UpdDate' => date('Y-m-d H:i:s'),
									 );
			//asd($formData,false);
			$lobjDbAdpt->insert('tbl_student_marks_entry',$formData);
			$getlID = $lobjDbAdpt->lastInsertId();
			if(isset($larrformData[$component.'_'.$componentItem]) && $larrformData[$component.'_'.$componentItem]!='') {
				$componentValue = $larrformData[$component.'_'.$componentItem];
				if(count($componentValue)>0) {
					$TmO = 0; $TmE = 0;
					foreach($componentValue as $key=>$value) {
						$TmO = $TmO + $value;
						$TmE = $TmE + $larrformData['IndividualTMarks_'.$key];
						$formDataMore = array(
								'IdStudentMarksEntry' => $getlID,
								'Component' => $component,
								'ComponentItem' => $componentItem,
								'ComponentDetail' => $key,
								'MarksObtained' => $value,
								'TotalMarks' => $larrformData['IndividualTMarks_'.$key],
								'UpdUser' => $larrformData['UpdUser'],
								'UpdDate' => date('Y-m-d H:i:s'),
						);
						$lobjDbAdpt->insert('tbl_student_detail_marks_entry',$formDataMore);
						$lmesStudArr = array('MarksEntryStatus' => $marksentrysttaus);
						$where_upd = 'IdStudentMarksEntry = ' . $getlID;
						$lobjDbAdpt->update('tbl_student_marks_entry', $lmesStudArr, $where_upd);
					}
					$paramTM = array('TotalMarkObtained'=>$TmO,'MarksTotal'=>$TmE);
					$where_ups = array();
					$where_ups[] = $this->lobjDbAdpt->quoteInto('IdStudentMarksEntry = ?', $getlID);
					$lobjDbAdpt->update('tbl_student_marks_entry',$paramTM,$where_ups);

				}
			    }

			}

		}

	}



	public function fngetMarksStatus($idcourse,$idstudent) {

		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("sme"=>"tbl_student_marks_entry"),array("sme.MarksEntryStatus"))
		->where("sme.IdStudentRegistration = ?",$idstudent)
		->where("sme.Course = ?",$idcourse)
		->group("sme.IdStudentRegistration");
		return $result = $lobjDbAdpt->fetchAll($select);
	}

	public function fngetMarksBulkStatus($idcourse,$idcomp) {

		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("sme"=>"tbl_student_marks_entry"),array("sme.MarksEntryStatus"))
		->where("sme.Course = ?",$idcourse)
		->where("sme.Component = ?",$idcomp)
		->group("sme.IdStudentRegistration");
		return $result = $lobjDbAdpt->fetchAll($select);
	}


	/**
	 * Functio to search student and their course components
	 * @author: VT
	 */

	public function fnSearchMarksEntryBulk($post = array(),$instructor=NULL) {
		// to do instructor coding for join tbl_student_marks_entry

		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array("subj" =>"tbl_subjectmaster"),array('subj.IdSubject','subj.SubjectName','subj.SubCode','subj.CreditHours as SubjectCreditHours'))
		// to do   //->join(array("subjstaff" =>"tbl_subjectcoordinatorlist")," subjstaff.IdSubject  = subj.IdSubject AND subjstaff.IdStaff = '".$instructor."' ",array('subjstaff.IdStaff'))
		//->joinLeft(array("prg" =>"tbl_program"),'prg.IdProgram = studreg.IdProgram',array('prg.ProgramName','prg.IdScheme'))
		//->joinLeft(array("sch" =>"tbl_scheme"),'sch.IdScheme = prg.IdScheme',array('sch.EnglishDescription as SchemeName'))
		->join(array("subjcomp" =>"tbl_subcredithoursdistrbtn"),'subjcomp.IdSubject = subj.IdSubject',array('subjcomp.Idcomponents','subjcomp.IdcomponentItem','subjcomp.CreditHour as ComponentCreditHours'))
		->join(array("compdef" =>"tbl_examination_assessment_type"),'compdef.IdExaminationAssessmentType = subjcomp.Idcomponents',array('compdef.Description as ComponentName'))
		->join(array("compdef3" =>"tbl_examination_assessment_item"),'compdef3.IdExaminationAssessmentType = subjcomp.IdcomponentItem',array('compdef3.Description as MainComponentitemName'))
		->joinLeft(array("sme" =>"tbl_student_marks_entry"),'sme.Course = subj.IdSubject AND sme.Component = subjcomp.Idcomponents AND sme.ComponentItem = subjcomp.IdcomponentItem',array('sme.MarksEntryStatus','SUM(sme.TotalMarkObtained) as TMO'))
		->joinLeft(array("compdef2" =>"tbl_definationms"),'compdef2.idDefinition = sme.MarksEntryStatus',array('compdef2.DefinitionDesc as MarksEntryStatusName'))
		;


		if(isset($post['field23']) && !empty($post['field23']) ){
			$select = $select->where("subj.IdSubject = ?",$post['field23']);
		}

		if(isset($post['field24']) && !empty($post['field24']) ){
			$Compdetail = explode('-',$post['field24']);
			$select = $select->where("subjcomp.Idcomponents = ?",$Compdetail[0]);
			$select = $select->where("subjcomp.IdcomponentItem = ?",$Compdetail[1]);
		}

		if(isset($post['field25']) && !empty($post['field25']) ){
			$select = $select->joinLeft(array("prg" =>"tbl_program"), " prg.IdProgram = '".$post['field25']."' ",array('prg.ProgramName','prg.IdProgram','prg.ProgramCode'));
		}
		
		
		$semcode = '';
		$lobjsem = new GeneralSetup_Model_DbTable_Semester();
		$dataSem = explode('_',$post['field27']);
		if($dataSem[1]=='detail') {
			$det = $lobjsem->fngetsemdetailByid($dataSem[0]);
			$semcode = $det['SemesterCode'];
		} else {
			$det = $lobjsem->fngetsemmainByid($dataSem[0]);
			$semcode = $det['SemesterMainCode'];
		}
		
		if(isset($post['field26']) && !empty($post['field26']) ){
			$select = $select->joinLeft(array("ctg" =>"tbl_course_tagging_group"), " ctg.IdCourseTaggingGroup = '".$post['field26']."' ",array('ctg.IdCourseTaggingGroup'));
			$select = $select->where("ctg.SemCode = ?",$semcode);
		}
		

		$select->group(array("subjcomp.Idcomponents","subjcomp.IdcomponentItem"));
		echo $select;
		$result = $db->fetchAll($select);		
		return $result;

	}


	public function fnSearchMarksEntryBulkDetails($idScheme=NULL, $idFaculty=NULL, $idsem=NULL, $idprogram, $idcourse ,$idcomp, $idcompitem, $instructor=NULL,$group=NULL) {
		// to do instructor coding
		$db = Zend_Db_Table::getDefaultAdapter();

		$getSemData = $this->getSemcode($idsem);


		$select = $db->select()
		->from(array("studregsubj"=>"tbl_studentregsubjects"), array('studregsubj.IdStudentRegSubjects','studregsubj.IdStudentRegistration','studregsubj.IdSubject','studregsubj.IdSemesterDetails','studregsubj.IdSemesterMain'))
		->join(array("studreg" =>"tbl_studentregistration"),'studreg.IdStudentRegistration = studregsubj.IdStudentRegistration',array('studreg.registrationId','studreg.IdProgram'))
		->join(array("cgsm" =>"tbl_course_group_student_mapping"),'studreg.IdStudentRegistration = cgsm.IdStudent',array(''))					
		->join(array("ctg" =>"tbl_course_tagging_group"),'cgsm.IdCourseTaggingGroup = ctg.IdCourseTaggingGroup',array(''))
		->joinLeft(array("subjcomp" =>"tbl_subcredithoursdistrbtn")," subjcomp.IdSubject = studregsubj.IdSubject ",array('subjcomp.Idcomponents','subjcomp.IdcomponentItem','subjcomp.CreditHour as ComponentCreditHours'))
		//->join(array("subjstaff" =>"tbl_subjectcoordinatorlist")," subjstaff.IdSubject  = '".$idcourse."' AND subjstaff.IdStaff = '".$instructor."' ",array('subjstaff.IdStaff'))
		->joinLeft(array("marksmaster"=>"tbl_marksdistributionmaster"),'marksmaster.IdCourse = studregsubj.IdSubject AND marksmaster.IdComponentType = subjcomp.Idcomponents AND marksmaster.IdComponentItem = subjcomp.IdcomponentItem',array('marksmaster.IdMarksDistributionMaster','marksmaster.IdComponentType as IdComponentTypeMain','marksmaster.IdComponentItem as IdComponentItemMain','marksmaster.Marks as MarksTotal'))
		->joinLeft(array("sme" =>"tbl_student_marks_entry"),'sme.Component = marksmaster.IdComponentType  AND sme.ComponentItem = marksmaster.IdComponentItem AND sme.Course = marksmaster.IdCourse AND sme.IdStudentRegistration = studreg.IdStudentRegistration  ',array('sme.AttendanceStatus','sme.MarksEntryStatus','sme.TotalMarkObtained'))
		->joinLeft(array("examcal" =>"tbl_exam_calendar"),"marksmaster.IdComponentType = examcal.AssessmentType  AND examcal.Semester = '".$getSemData."'   ",array('examcal.StartDate','examcal.EndDate'))
		->where("studregsubj.IdSubject = ?",$idcourse)
		->where("studreg.IdProgram = ?",$idprogram)
		->where("marksmaster.IdProgram = ?",$idprogram)
		->where("marksmaster.IdCourse = ?",$idcourse)
		->where("marksmaster.IdComponentType = ?",$idcomp)
		->where("marksmaster.IdComponentItem = ?",$idcompitem)
		->where("cgsm.IdCourseTaggingGroup = ?",$group);
		;

		// only active, defer and dormant students are eligible for marks entry.
		$select->where("studreg.profileStatus = ?",'92')
				 ->orwhere("studreg.profileStatus = ?",'248')
				 ->orwhere("studreg.profileStatus = ?",'253');

		if($idsem!='') {
			$dataSem = explode('_',$idsem);
			if($dataSem[1]=='detail') {
				$semDetail = $dataSem[0];
				$select->where("studregsubj.IdSemesterDetails = ?",$semDetail);
			} else {
				$semMain = $dataSem[0];
				$select->where("studregsubj.IdSemesterMain = ?",$semMain);
			}

			if($idScheme!='' && $idFaculty!='') {
				$select->where("marksmaster.IdScheme = ?",$idScheme);
				$select->where("marksmaster.IdFaculty = ?",$idFaculty);
			}  else {
				$select->where("marksmaster.IdScheme = ?",'0');
				$select->where("marksmaster.IdFaculty = ?",'0');
			}



			//$getSemData = $this->getSemcode($idsem);
			//$select->where("marksmaster.semester = ?",$getSemData);

		}


		$select->group('studreg.registrationId');
		$result = $db->fetchAll($select);

		if(count($result)==0) {
			$select = $db->select()
			->from(array("studregsubj"=>"tbl_studentregsubjects"), array('studregsubj.IdStudentRegSubjects','studregsubj.IdStudentRegistration','studregsubj.IdSubject','studregsubj.IdSemesterDetails','studregsubj.IdSemesterMain'))
			->join(array("studreg" =>"tbl_studentregistration"),'studreg.IdStudentRegistration = studregsubj.IdStudentRegistration',array('studreg.registrationId','studreg.IdProgram'))
			->joinLeft(array("subjcomp" =>"tbl_subcredithoursdistrbtn")," subjcomp.IdSubject = studregsubj.IdSubject ",array('subjcomp.Idcomponents','subjcomp.IdcomponentItem','subjcomp.CreditHour as ComponentCreditHours'))
			//->join(array("subjstaff" =>"tbl_subjectcoordinatorlist")," subjstaff.IdSubject  = '".$idcourse."' AND subjstaff.IdStaff = '".$instructor."' ",array('subjstaff.IdStaff'))
			->joinLeft(array("marksmaster"=>"tbl_marksdistributionmaster"),'marksmaster.IdCourse = studregsubj.IdSubject AND marksmaster.IdComponentType = subjcomp.Idcomponents  AND marksmaster.IdComponentItem = subjcomp.IdcomponentItem ',array('marksmaster.IdMarksDistributionMaster','marksmaster.IdComponentType as IdComponentTypeMain','marksmaster.IdComponentItem as IdComponentItemMain','marksmaster.Marks as MarksTotal'))
			->joinLeft(array("sme" =>"tbl_student_marks_entry"),'sme.Component = marksmaster.IdComponentType  AND sme.ComponentItem = marksmaster.IdComponentItem AND sme.Course = marksmaster.IdCourse AND sme.IdStudentRegistration = studreg.IdStudentRegistration  ',array('sme.AttendanceStatus','sme.MarksEntryStatus','sme.TotalMarkObtained'))
			->joinLeft(array("examcal" =>"tbl_exam_calendar"),"marksmaster.IdComponentType = examcal.AssessmentType  AND examcal.Semester = '".$getSemData."'   ",array('examcal.StartDate','examcal.EndDate'))
			->where("studregsubj.IdSubject = ?",$idcourse)
			->where("studreg.IdProgram = ?",$idprogram)
			->where("marksmaster.IdProgram = ?",'0')
			->where("marksmaster.IdCourse = ?",$idcourse)
			->where("marksmaster.IdComponentType = ?",$idcomp)
			->where("marksmaster.IdComponentItem = ?",$idcompitem)
			;

			// only active, defer and dormant students are eligible for marks entry.
			$select->where("studreg.profileStatus = ?",'92')
				 ->orwhere("studreg.profileStatus = ?",'248')
				 ->orwhere("studreg.profileStatus = ?",'253');

			if($idsem!='') {
				$dataSem = explode('_',$idsem);
				if($dataSem[1]=='detail') {
					$semDetail = $dataSem[0];
					$select->where("studregsubj.IdSemesterDetails = ?",$semDetail);
				} else {
					$semMain = $dataSem[0];
					$select->where("studregsubj.IdSemesterMain = ?",$semMain);
				}
			}

			if($idScheme!='' && $idFaculty!='') {
				$select->where("marksmaster.IdScheme = ?",$idScheme);
				$select->where("marksmaster.IdFaculty = ?",$idFaculty);
			}  else {
				$select->where("marksmaster.IdScheme = ?",'0');
				$select->where("marksmaster.IdFaculty = ?",'0');
			}

			$select->group('studreg.registrationId');
	  		$result = $db->fetchAll($select);

		}


		if(count($result)==0) {
			$select = $db->select()
			->from(array("studregsubj"=>"tbl_studentregsubjects"), array('studregsubj.IdStudentRegSubjects','studregsubj.IdStudentRegistration','studregsubj.IdSubject','studregsubj.IdSemesterDetails','studregsubj.IdSemesterMain'))
			->join(array("studreg" =>"tbl_studentregistration"),'studreg.IdStudentRegistration = studregsubj.IdStudentRegistration',array('studreg.registrationId','studreg.IdProgram'))
			->joinLeft(array("subjcomp" =>"tbl_subcredithoursdistrbtn")," subjcomp.IdSubject = studregsubj.IdSubject ",array('subjcomp.Idcomponents','subjcomp.IdcomponentItem','subjcomp.CreditHour as ComponentCreditHours'))
			//->join(array("subjstaff" =>"tbl_subjectcoordinatorlist")," subjstaff.IdSubject  = '".$idcourse."' AND subjstaff.IdStaff = '".$instructor."' ",array('subjstaff.IdStaff'))
			->joinLeft(array("marksmaster"=>"tbl_marksdistributionmaster"),'marksmaster.IdCourse = studregsubj.IdSubject AND marksmaster.IdComponentType = subjcomp.Idcomponents AND marksmaster.IdComponentItem = subjcomp.IdcomponentItem',array('marksmaster.IdMarksDistributionMaster','marksmaster.IdComponentType as IdComponentTypeMain','marksmaster.IdComponentItem as IdComponentItemMain','marksmaster.Marks as MarksTotal'))
			->joinLeft(array("sme" =>"tbl_student_marks_entry"),'sme.Component = marksmaster.IdComponentType AND sme.ComponentItem = marksmaster.IdComponentItem AND sme.Course = marksmaster.IdCourse AND sme.IdStudentRegistration = studreg.IdStudentRegistration  ',array('sme.AttendanceStatus','sme.MarksEntryStatus','sme.TotalMarkObtained'))
			->joinLeft(array("examcal" =>"tbl_exam_calendar"),"marksmaster.IdComponentType = examcal.AssessmentType  AND examcal.Semester = '".$getSemData."'   ",array('examcal.StartDate','examcal.EndDate'))
			->where("studregsubj.IdSubject = ?",$idcourse)
			->where("studreg.IdProgram = ?",$idprogram)
			->where("marksmaster.IdProgram = ?",'0')
			->where("marksmaster.IdCourse = ?",$idcourse)
			->where("marksmaster.IdComponentType = ?",$idcomp)
			->where("marksmaster.IdComponentItem = ?",$idcompitem)
			;


			// only active, defer and dormant students are eligible for marks entry.
			$select->where("studreg.profileStatus = ?",'92')
				 ->orwhere("studreg.profileStatus = ?",'248')
				 ->orwhere("studreg.profileStatus = ?",'253');

			if($idsem!='') {
				$dataSem = explode('_',$idsem);
				if($dataSem[1]=='detail') {
					$semDetail = $dataSem[0];
					$select->where("studregsubj.IdSemesterDetails = ?",$semDetail);
				} else {
					$semMain = $dataSem[0];
					$select->where("studregsubj.IdSemesterMain = ?",$semMain);
				}
			}

			$select->where("marksmaster.IdScheme = ?",'0');
			$select->where("marksmaster.IdFaculty = ?",'0');

			$select->group('studreg.registrationId');
	  		$result = $db->fetchAll($select);

		}

		return $result;

	}



	public function fnInsertComponentBulk($larrformData,$marksentrysttaus,$instructor=NULL) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$component = $larrformData['IdStudentRegSubjects'];
		foreach($component as $values) {


			$checkBoxValue = $larrformData['cb_'.$values];
			if($checkBoxValue=='1') {

				$select = $lobjDbAdpt->select()
			->from(array("sme"=>"tbl_student_marks_entry"),array("sme.IdStudentMarksEntry","sme.AttendanceStatus","sme.MarksEntryStatus"))
			->where("sme.IdStudentRegSubjects = ?",$values)
			->where("sme.Course = ?",$larrformData['Course'])
			->where("sme.Component = ?",$larrformData['Component'])
			->where("sme.ComponentItem = ?",$larrformData['ComponentItem']);
			// ->where("sme.Instructor = ?",$instructor) to do
			$resultData = $lobjDbAdpt->fetchAll($select);
			if(count($resultData)>0) {
				foreach($resultData as $del) {
					$id = $del['IdStudentMarksEntry'];
					$where_del1 = $this->lobjDbAdpt->quoteInto('IdStudentMarksEntry = ?', $id);
					$lobjDbAdpt->delete('tbl_student_detail_marks_entry', $where_del1);
				}
				$where_del2 = $this->lobjDbAdpt->quoteInto('IdStudentMarksEntry = ?', $id);
				$lobjDbAdpt->delete('tbl_student_marks_entry', $where_del2);

				$alreadyAS =  $resultData[0]['AttendanceStatus'];
				$alreadyMES =  $resultData[0]['MarksEntryStatus'];

			} else {

				$alreadyAS = $larrformData['AttendanceStatus_'.$values];
				//$alreadyTMO =  $larrformData['SingleMarks_'.$values];
				$alreadyMES =  $marksentrysttaus;
			}



			$formData = array(
					'IdSemester' => $larrformData['IdSemester'],
					'Course' => $larrformData['Course'],
					'Component' =>  $larrformData['Component'],
					'ComponentItem' =>  $larrformData['ComponentItem'],
					'Instructor' => $larrformData['Instructor'],
					'IdStudentRegSubjects' => $values,
					'IdStudentRegistration' => $larrformData['IdStudentRegistration_'.$values],
					'AttendanceStatus' => $alreadyAS,
					'MarksEntryStatus' => $alreadyMES, // 281 or 311 is for entry
					'TotalMarkObtained' => $larrformData['SingleMarks_'.$values],
					'MarksTotal' => $larrformData['SingleTMarks_'.$values],
					'UpdUser' => $larrformData['UpdUser'],
					'UpdDate' => date('Y-m-d H:i:s'),
			);


			$lobjDbAdpt->insert('tbl_student_marks_entry',$formData);
			$getlID = $lobjDbAdpt->lastInsertId();
			if(isset($larrformData[$values]) && $larrformData[$values]!='') {
				$componentValue = $larrformData[$values];
				if(count($componentValue)>0) {
					$Tm = 0 ;  $TmE = 0 ;
					foreach($componentValue as $key=>$value) {
						$Tm = $Tm+$value;
						$TmE = $TmE+$larrformData['TotalMarks'];
						$formDataMore = array(
								'IdStudentMarksEntry' => $getlID,
								'Component' => $larrformData['Component'],
								'ComponentItem' =>  $larrformData['ComponentItem'],
								'ComponentDetail' => $key,
								'MarksObtained' => $value,
								'TotalMarks' => $larrformData['TotalMarks'],
								'UpdUser' => $larrformData['UpdUser'],
								'UpdDate' => date('Y-m-d H:i:s'),
						);
						$lobjDbAdpt->insert('tbl_student_detail_marks_entry',$formDataMore);
						//$lmesStudArr = array('MarksEntryStatus' => $marksentrysttaus);
						//$where_upd = 'IdStudentMarksEntry = ' . $getlID;
						//$lobjDbAdpt->update('tbl_student_marks_entry', $lmesStudArr, $where_upd);
					}

					$paramTM = array('TotalMarkObtained'=>$Tm,'MarksTotal'=>$TmE);
					$where_ups = array();
					$where_ups[] = $this->lobjDbAdpt->quoteInto('IdStudentMarksEntry = ?', $getlID);
					$lobjDbAdpt->update('tbl_student_marks_entry',$paramTM,$where_ups);

				}
				}

			}



		}

	}


	public function getApprovedComponents($idcourse ,$idprogram, $semester ,$idstudent, $idScheme, $idFaculty) {

		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array("marksmaster"=>"tbl_marksdistributionmaster"),array('marksmaster.Status'))
		->where("marksmaster.IdProgram = ?",$idprogram)
		->where("marksmaster.IdCourse = ?",$idcourse)
		//->where("marksmaster.semester = ?",$semester)
		->where("marksmaster.IdScheme = ?",$idScheme)
		->where("marksmaster.IdFaculty = ?",$idFaculty)->group('marksmaster.Status')
		;
		$result = $db->fetchAll($select);
		$total = count($result);

		if($total=='0') {
			// ALL scheme, all faculty
			$select = $db->select()
			->from(array("marksmaster"=>"tbl_marksdistributionmaster"),array('marksmaster.Status'))
			->where("marksmaster.IdCourse = ?",$idcourse)
			//->where("marksmaster.semester = ?",$semester)
			->where("marksmaster.IdProgram = ?",$idprogram)
			->where("marksmaster.IdScheme = ?",'0')
			->where("marksmaster.IdFaculty = ?",'0')->group('marksmaster.Status')
			;
			$result = $db->fetchAll($select);
			$total2 = count($result);
			if($total2=='0') {
				// ALL scheme, all faculty, All program
				$select = $db->select()
				->from(array("marksmaster"=>"tbl_marksdistributionmaster"),array('marksmaster.Status'))
				->where("marksmaster.IdCourse = ?",$idcourse)
				//->where("marksmaster.semester = ?",$semester)
				->where("marksmaster.IdProgram = ?",'0')
				->where("marksmaster.IdScheme = ?",'0')
				->where("marksmaster.IdFaculty = ?",'0')->group('marksmaster.Status')
				;
				$result = $db->fetchAll($select);

			}

		}

		return $result;
	}



	public function fngetRecordExist( $IdStudentRegSubjects, $idcourse ) {
		$select = $this->lobjDbAdpt->select()
		->from(array("sme"=>"tbl_student_marks_entry"),array('sme.IdStudentMarksEntry'))
		->where("sme.Course = ?",$idcourse)
		->where("sme.IdStudentRegSubjects = ?", $IdStudentRegSubjects)
		;
		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;
	}

	public function fnfetchAllComponentsDetailsIndividualAfterinsert($IdMarksDistributionMaster,$idcourse ,$Idcmp, $idstudent, $Idcmpitem) {

		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("marksdetails"=>"tbl_marksdistributiondetails"),array("marksdetails.*"))
		->joinLeft(array("assmtype" =>"tbl_examination_assessment_type"),'assmtype.IdExaminationAssessmentType = marksdetails.IdComponentType',array('assmtype.Description as ComponentTypeName'))
		->joinLeft(array("assmitem" =>"tbl_examination_assessment_item"),'assmitem.IdExaminationAssessmentType = marksdetails.IdComponentItem',array('assmitem.Description as ComponentItemName'))
		->joinLeft(array("sme" =>"tbl_student_marks_entry"),"sme.Course = '".$idcourse."' AND  sme.Component = '".$Idcmp."' AND sme.ComponentItem='".$Idcmpitem."' AND sme.IdStudentRegistration = '".$idstudent."'  ",array('sme.IdStudentMarksEntry'))
		->joinLeft(array("sdme" =>"tbl_student_detail_marks_entry")," sme.Component = '".$Idcmp."'  AND sdme.ComponentItem='".$Idcmpitem."' AND sdme.ComponentDetail = marksdetails.IdMarksDistributionDetails AND sdme.IdStudentMarksEntry = sme.IdStudentMarksEntry",array('sdme.MarksObtained as MarksObtainedDetail','sdme.TotalMarks as TotalMarksEntered'))
		->where("marksdetails.IdMarksDistributionMaster = ?",$IdMarksDistributionMaster)
		//->group('')
		;
		return $result = $lobjDbAdpt->fetchAll($select);
	}


	public function getApprovedComponentsBulk( $idprogram, $idcourse ,$idcomp ) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array("marksmaster"=>"tbl_marksdistributionmaster"),array('marksmaster.Status'))
		->where("marksmaster.IdProgram = ?",$idprogram)
		->where("marksmaster.IdCourse = ?",$idcourse)
		->where("marksmaster.Status = ?",'243')
		->where("marksmaster.IdComponentType = ?",$idcomp)
		;
		$result = $db->fetchAll($select);

		if(count($result)==0) {
			$select = $db->select()
			->from(array("marksmaster"=>"tbl_marksdistributionmaster"),array('marksmaster.Status'))
			->where("marksmaster.IdProgram = ?",'0')
			->where("marksmaster.IdCourse = ?",$idcourse)
			->where("marksmaster.Status = ?",'243')
			->where("marksmaster.IdComponentType = ?",$idcomp)
			;
			$result = $db->fetchAll($select);
		}

		return $result;
	}


	public function getstudentcoursecomponent($idstudent,$idcourse,$idcomponent){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("sme"=>"tbl_student_marks_entry"),array("sme.*"))
		->where("sme.IdStudentRegistration = ?",$idstudent)
		->where("sme.Course = ?",$idcourse)
		->where("sme.Component = ?",$idcomponent)
		->where("sme.MarksEntryStatus = '313' ");
		//echo $select; die;
		return $result = $lobjDbAdpt->fetchAll($select);
	}

	public function getdetailcomponentMarks($idmarksentry,$idcomponent){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("sme"=>"tbl_student_detail_marks_entry"),array("sme.MarksObtained","sme.TotalMarks","sme.ComponentDetail"))
		->where("sme.IdStudentMarksEntry = ?",$idmarksentry)
		->where("sme.Component = ?",$idcomponent);
		//echo $select; die;
		return $result = $lobjDbAdpt->fetchAll($select);

	}

	public function getmarksentry($idcomponent,$IdCourse,$IdStudentRegistration){
		$select = $this->lobjDbAdpt->select()
		->from(array("sme"=>"tbl_student_marks_entry"),array("sme.IdStudentMarksEntry","sme.TotalMarkObtained",'sme.TotalMarkObtainedScaling'))
		->where("sme.Course = ?",$IdCourse)
		->where("sme.Component = ?",$idcomponent)
		->where("sme.IdStudentRegistration = ?",$IdStudentRegistration);
		return $result = $this->lobjDbAdpt->fetchAll($select);
	}

	public function updatetmarksentry($data,$id){
		//           echo "<pre>";
		//           print_r($data);
		//           echo 'ID'.$id;
		//           die;
		//           $param = array();
		$where_delete = array();
		$where_delete[] = $this->lobjDbAdpt->quoteInto('IdStudentMarksEntry = ?', $id);
		$this->lobjDbAdpt->update('tbl_student_marks_entry', $data, $where_delete);

	}


	public function getSemcode($semester) {
		$dataSem = explode('_',$semester);
		if($dataSem[1]=='detail') {
			$semDetail = $dataSem[0];
			$semsql = $this->lobjDbAdpt->select()->from(array("semdet" =>"tbl_semester"),array('semdet.SemesterCode'))
			->where("semdet.IdSemester = ?",$semDetail);
			$result = $this->lobjDbAdpt->fetchAll($semsql);
			$semesterCode = $result[0]['SemesterCode'];
		} else {
			$semMain = $dataSem[0];
			$semsql =  $this->lobjDbAdpt->select()->from(array("semmast" =>"tbl_semestermaster"),array('semmast.SemesterMainCode'))
			->where("semmast.IdSemesterMaster = ?",$semMain);
			$result = $this->lobjDbAdpt->fetchAll($semsql);
			$semesterCode = $result[0]['SemesterMainCode'];
		}

		return $semesterCode;

	}


}