<?php
class Examination_Model_DbTable_Remarkapplication extends Zend_Db_Table_Abstract {

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function fngetCourses($getIDstudentReg, $semester) {
		$select = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_studentregsubjects', array()))
		->joinLeft(array("d" => "tbl_subjectmaster"), "d.IdSubject = a.IdSubject",array("key"=>"d.IdSubject","value"=>"CONCAT_WS('-',IFNULL(d.SubjectName,''),IFNULL(d.SubCode,''))"));

		$dataSem = explode('_',$semester);
		if($dataSem[1]=='detail') {
			$semDetail = $dataSem[0];
			$select->where("a.IdSemesterDetails = ?",$semDetail);
		} else {
			$semMain = $dataSem[0];
			$select = $select->where("a.IdSemesterMain = ?",$semMain);
		}

		$select->where("a.IdStudentRegistration = ?",$getIDstudentReg)->group("a.IdSubject");
		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;
	}


	public function fnSearchMarksEntryDetails($idcourse, $semester, $idstudent, $IdProgram) {
		$getSemCode = $this->getSemcode($semester);
		$select = $this->lobjDbAdpt->select()
		->from(array("marksmaster"=>"tbl_marksdistributionmaster"),array('marksmaster.IdMarksDistributionMaster','marksmaster.IdComponentType as IdComponentTypeMain','marksmaster.IdComponentItem as IdComponentItemMain','marksmaster.Marks as MarksTotal'))
		->joinLeft(array("compdef" =>"tbl_definationms"),'compdef.idDefinition = marksmaster.IdComponentType',array('compdef.DefinitionDesc as ComponentName'))
		->joinLeft(array("compdef2" =>"tbl_definationms"),'compdef2.idDefinition = marksmaster.IdComponentItem',array('compdef2.DefinitionDesc as ComponentItemName'))
		//->join(array("subjstaff" =>"tbl_subjectcoordinatorlist")," subjstaff.IdSubject  = '".$idcourse."' AND subjstaff.IdStaff = '".$instructor."' ",array('subjstaff.IdStaff'))
		->joinLeft(array("sme" =>"tbl_student_marks_entry"),"sme.Component = marksmaster.IdComponentType AND sme.Course='".$idcourse."' AND sme.IdStudentRegistration = '".$idstudent."' ",array('sme.AttendanceStatus','sme.MarksEntryStatus'))
		->where("marksmaster.IdProgram = ?",$IdProgram)
		->where("marksmaster.IdCourse = ?",$idcourse)
		->where("marksmaster.semester = ?",$getSemCode);
		//->where("marksmaster.IdScheme = ?",$idScheme)
		//->where("marksmaster.IdFaculty = ?",$idFaculty)
		//echo  $select;
		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;
	}

	public function fnInsertComponent($larrformData,$status,$getDetailsConfig,$idAppeal) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();

		$IdAppealEntry = $larrformData['IdAppealEntry'];
		foreach($IdAppealEntry as $key=>$value)
		{
			$examiner1marks = $larrformData[$larrformData['IdAppeal'].'_'.$value.'_examiner1marks'];
			$examiner2marks = $larrformData[$larrformData['IdAppeal'].'_'.$value.'_examiner2marks'];
			$IdStudentMarksEntry = $larrformData[$larrformData['IdAppeal'].'_'.$value.'_IdStudentMarksEntry'];
			$IdComponent = $larrformData[$larrformData['IdAppeal'].'_'.$value.'_IdComponent'];
			$IdComponentItem = $larrformData[$larrformData['IdAppeal'].'_'.$value.'_IdComponentItem'];
			$IdSubComponent = $larrformData[$larrformData['IdAppeal'].'_'.$value.'_IdSubComponent'];

			$formDataMore = array(
					'Examiner1' => $larrformData['field23'],
					'Examiner2' => $larrformData['field27'],
					'Examiner1Marks' => $examiner1marks,
					'Examiner2Marks' => $examiner2marks,
					'NewMarks' => NULL,
					'UpdUser' => $larrformData['UpdUser'],
					'UpdDate' => date('Y-m-d H:i:s'),
			);
			$where = " IdAppealEntry =  '".$value."' AND  IdAppeal='".$idAppeal."'  ";
			$lobjDbAdpt->update('tbl_appealmarksentry',$formDataMore,$where);

		}

		// update the student status
		$formDataUpdate= array( 'Status' => $status );
		$where_st = " IdAppeal='".$idAppeal."'  ";
		$lobjDbAdpt->update('tbl_appeal',$formDataUpdate,$where_st);


	}

	public function getSemcode($semester) {
		$dataSem = explode('_',$semester);
		$semesterCode ='';
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


	public function totalAppealMax($idcourse, $getIDstudentReg, $semesterCode, $maxAppeal ) {

		// COUNT Draft
		$appealsql1 =  $this->lobjDbAdpt->select()->from(array("appeal" =>"tbl_appeal"),array('Count(appeal.IdAppeal) as totalDraft'))
		->where("appeal.IdRegistration = ?",$getIDstudentReg)
		->where("appeal.SemesterCode = ?",$semesterCode)
		//->where("appeal.IdCourse = ?",$idcourse)
		->where("appeal.Status = ?",'192');
		$result1 = $this->lobjDbAdpt->fetchAll($appealsql1);
		$totalDraft =  $result1[0]['totalDraft'];

		// COUNT Entry
		$appealsql2 =  $this->lobjDbAdpt->select()->from(array("appeal" =>"tbl_appeal"),array('Count(appeal.IdAppeal) as totalEntry'))
		->where("appeal.IdRegistration = ?",$getIDstudentReg)
		->where("appeal.SemesterCode = ?",$semesterCode)
		//->where("appeal.IdCourse = ?",$idcourse)
		->where("appeal.Status = ?",'193');
		$result2 = $this->lobjDbAdpt->fetchAll($appealsql2);
		$totalEntry =  $result2[0]['totalEntry'];

		// COUNT Approve
		$appealsql3 =  $this->lobjDbAdpt->select()->from(array("appeal" =>"tbl_appeal"),array('Count(appeal.IdAppeal) as totalApprove'))
		->where("appeal.IdRegistration = ?",$getIDstudentReg)
		->where("appeal.SemesterCode = ?",$semesterCode)
		//->where("appeal.IdCourse = ?",$idcourse)
		->where("appeal.Status = ?",'243');
		$result3 = $this->lobjDbAdpt->fetchAll($appealsql3);
		$totalApprove =  $result3[0]['totalApprove'];

		// COUNT Reject
		$appealsql4 =  $this->lobjDbAdpt->select()->from(array("appeal" =>"tbl_appeal"),array('Count(appeal.IdAppeal) as totalReject'))
		->where("appeal.IdRegistration = ?",$getIDstudentReg)
		->where("appeal.SemesterCode = ?",$semesterCode)
		//->where("appeal.IdCourse = ?",$idcourse)
		->where("appeal.Status = ?",'195');
		$result4 = $this->lobjDbAdpt->fetchAll($appealsql4);
		$totalReject =  $result4[0]['totalReject'];
		// ENDS

		$finalTotal = ($totalDraft+$totalEntry+$totalApprove);


		$errMsg  =  '0';  // CAN ADD
		if($finalTotal < $maxAppeal) {
			$errMsg  =  '0';  // can add
		} else   {
			$errMsg  =  '1';  // cannot add
		}
		return $errMsg;

	}


	public function totalAppealMaxPerStudent($idcourse, $getIDstudentReg, $semesterCode, $maxAppeal ) {

		// COUNT Draft
		$appealsql1 =  $this->lobjDbAdpt->select()->from(array("appeal" =>"tbl_appeal"),array('Count(appeal.IdAppeal) as totalDraft'))
		->where("appeal.IdRegistration = ?",$getIDstudentReg)
		//->where("appeal.SemesterCode = ?",$semesterCode)
		//->where("appeal.IdCourse = ?",$idcourse)
		->where("appeal.Status = ?",'192');
		$result1 = $this->lobjDbAdpt->fetchAll($appealsql1);
		$totalDraft =  $result1[0]['totalDraft'];

		// COUNT Entry
		$appealsql2 =  $this->lobjDbAdpt->select()->from(array("appeal" =>"tbl_appeal"),array('Count(appeal.IdAppeal) as totalEntry'))
		->where("appeal.IdRegistration = ?",$getIDstudentReg)
		//->where("appeal.SemesterCode = ?",$semesterCode)
		//->where("appeal.IdCourse = ?",$idcourse)
		->where("appeal.Status = ?",'193');
		$result2 = $this->lobjDbAdpt->fetchAll($appealsql2);
		$totalEntry =  $result2[0]['totalEntry'];

		// COUNT Approve
		$appealsql3 =  $this->lobjDbAdpt->select()->from(array("appeal" =>"tbl_appeal"),array('Count(appeal.IdAppeal) as totalApprove'))
		->where("appeal.IdRegistration = ?",$getIDstudentReg)
		//->where("appeal.SemesterCode = ?",$semesterCode)
		//->where("appeal.IdCourse = ?",$idcourse)
		->where("appeal.Status = ?",'243');
		$result3 = $this->lobjDbAdpt->fetchAll($appealsql3);
		$totalApprove =  $result3[0]['totalApprove'];

		// COUNT Reject
		$appealsql4 =  $this->lobjDbAdpt->select()->from(array("appeal" =>"tbl_appeal"),array('Count(appeal.IdAppeal) as totalReject'))
		->where("appeal.IdRegistration = ?",$getIDstudentReg)
		//->where("appeal.SemesterCode = ?",$semesterCode)
		//->where("appeal.IdCourse = ?",$idcourse)
		->where("appeal.Status = ?",'195');
		$result4 = $this->lobjDbAdpt->fetchAll($appealsql4);
		$totalReject =  $result4[0]['totalReject'];
		// ENDS

		$finalTotal = ($totalDraft+$totalEntry+$totalApprove);


		$errMsg  =  '0';  // CAN ADD
		if($finalTotal < $maxAppeal) {
			$errMsg  =  '0';  // can add
		} else   {
			$errMsg  =  '1';  // cannot add
		}
		return $errMsg;

	}



	public function getAppealDetailByID($idAppeal) {
		$semsql = $this->lobjDbAdpt->select()->from(array("appeal" =>"tbl_appeal"),array('appeal.*'))
		->joinLeft(array("d" => "tbl_subjectmaster"), "d.IdSubject = appeal.IdCourse",array("key"=>"d.IdSubject","value"=>"CONCAT_WS('-',IFNULL(d.SubjectName,''),IFNULL(d.SubCode,''))"))
		->where("appeal.IdAppeal = ?",$idAppeal);
		$result = $this->lobjDbAdpt->fetchAll($semsql);
		return $result;
	}

	public function fnSearchMarksAppealDetails($idAppeal, $idcourse, $SemesterCode, $idstudent, $IdProgram ) {

		//            $select1 = $this->lobjDbAdpt->select()
		//                        ->from(array("marksmaster"=>"tbl_marksdistributionmaster"),array('marksmaster.IdMarksDistributionMaster','marksmaster.IdComponentType as IdComponentTypeMain','marksmaster.IdComponentItem as IdComponentItemMain','marksmaster.Marks as MarksTotal'))
		//                        ->joinLeft(array("compdef" =>"tbl_examination_assessment_type"),'compdef.IdExaminationAssessmentType = marksmaster.IdComponentType',array('compdef.Description as ComponentName'))
		//                        ->joinLeft(array("compdef2" =>"tbl_examination_assessment_item"),'compdef2.IdExaminationAssessmentType = marksmaster.IdComponentItem',array('compdef2.Description as ComponentItemName'))
		//                        //->joinLeft(array("ame" =>"tbl_appealmarksentry")," ame.IdAppeal = '".$idAppeal."' ",array('ame.IdAppealEntry'))
		//                        ->joinLeft(array("sme" =>"tbl_student_marks_entry"),"sme.Component = marksmaster.IdComponentType AND sme.Course='".$idcourse."' AND sme.IdStudentRegistration = '".$idstudent."' ",array(''))
		//                       // ->where("ame.IdAppeal = ?",$idAppeal)
		//                        ->where("marksmaster.IdProgram = ?",$IdProgram)
		//                        ->where("marksmaster.IdCourse = ?",$idcourse)
		//                        ->where("marksmaster.semester = ?",$SemesterCode)
		//                        ->where("marksmaster.Status = ?",'243')
		//->group("ame.IdComponent")
		//;

		// $result = $this->lobjDbAdpt->fetchAll($select);
		// return $result;




		$select = $this->lobjDbAdpt->select()
		->from(array("appealentry"=>"tbl_appealmarksentry"),array('appealentry.*'))
		->joinLeft(array("compdef" =>"tbl_examination_assessment_type"),'compdef.IdExaminationAssessmentType = appealentry.IdComponent',array('compdef.Description as ComponentName'))
		->joinLeft(array("compdef3" =>"tbl_examination_assessment_item"),'compdef3.IdExaminationAssessmentType = appealentry.IdComponentItem',array('compdef3.Description as MainComponentItemName'))
		->joinLeft(array("sme" =>"tbl_student_marks_entry"),"sme.Component = appealentry.IdComponent AND sme.ComponentItem = appealentry.IdComponentItem AND sme.Course='".$idcourse."' AND sme.IdStudentRegistration = '".$idstudent."' AND sme.MarksEntryStatus = '313' ",array('sme.IdStudentMarksEntry'))
		->where("appealentry.IdAppeal = ?",$idAppeal)->group(array('appealentry.IdComponent','appealentry.IdComponentItem'))


		;

		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;

	}


	public function fnfetchAllComponentsDetailsAppeal($idAppeal, $IdMarksDistributionMaster,$idcourse ,$Idcmp, $idstudent) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("marksdetails"=>"tbl_marksdistributiondetails"),array("marksdetails.*"))
		->joinLeft(array("assmtype" =>"tbl_examination_assessment_type"),'assmtype.IdExaminationAssessmentType = marksdetails.IdComponentType',array('assmtype.Description as ComponentTypeName'))
		->joinLeft(array("assmitem" =>"tbl_examination_assessment_item"),'assmitem.IdExaminationAssessmentType = marksdetails.IdComponentItem',array('assmitem.Description as ComponentItemName'))
		->joinLeft(array("amed" =>"tbl_appealmarksentry")," amed.IdComponent = '".$Idcmp."' AND amed.IdSubComponent = marksdetails.IdMarksDistributionDetails AND amed.IdAppeal = '".$idAppeal."' ",array('amed.Examiner1Marks','amed.Examiner2Marks','amed.OldMarks','amed.TotalMarks'))
		->where("marksdetails.IdMarksDistributionDetails = ?",$IdMarksDistributionMaster)
		;
		return $result = $lobjDbAdpt->fetchAll($select);
	}


	public function fnfetchAllComponentsDetailsAppealNew($idAppeal, $Idcmpitem,$idcourse ,$Idcmp, $idstudent) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("appealentry"=>"tbl_appealmarksentry"),array('appealentry.*'))
		->joinLeft(array("marksdetails"=>"tbl_marksdistributiondetails")," marksdetails.IdMarksDistributionDetails = appealentry.IdSubComponent AND appealentry.IdSubComponent!='0' ", array("marksdetails.IdComponentType","marksdetails.IdComponentItem","marksdetails.IdMarksDistributionDetails"))
		->joinLeft(array("assmtype" =>"tbl_examination_assessment_type"),'assmtype.IdExaminationAssessmentType = marksdetails.IdComponentType',array('assmtype.Description as ComponentTypeName'))
		->joinLeft(array("assmitem" =>"tbl_examination_assessment_item"),'assmitem.IdExaminationAssessmentType = marksdetails.IdComponentItem',array('assmitem.Description as ComponentItemName'))

		//->from(array("marksdetails"=>"tbl_marksdistributiondetails"),array("marksdetails.*"))
		//->joinLeft(array("assmtype" =>"tbl_examination_assessment_type"),'assmtype.IdExaminationAssessmentType = marksdetails.IdComponentType',array('assmtype.Description as ComponentTypeName'))
		//->joinLeft(array("assmitem" =>"tbl_examination_assessment_item"),'assmitem.IdExaminationAssessmentType = marksdetails.IdComponentItem',array('assmitem.Description as ComponentItemName'))
		// ->joinLeft(array("amed" =>"tbl_appealmarksentry")," amed.IdComponent = '".$Idcmp."' AND amed.IdSubComponent = marksdetails.IdMarksDistributionDetails AND amed.IdAppeal = '".$idAppeal."' ",array('amed.Examiner1Marks','amed.Examiner2Marks','amed.OldMarks','amed.TotalMarks'))
		->where("appealentry.IdAppeal = ?",$idAppeal)->where("appealentry.IdComponent = ?",$Idcmp)->where("appealentry.IdComponentItem = ?",$Idcmpitem)
		;

		return $result = $lobjDbAdpt->fetchAll($select);
	}



	public function fnAppealStatus($idAppeal) {
		$semsql = $this->lobjDbAdpt->select()->from(array("appeal" =>"tbl_appeal"),array('appeal.Status'))->where("appeal.IdAppeal = ?",$idAppeal);
		$result = $this->lobjDbAdpt->fetchAll($semsql);
		return $result;
	}


	public function fnViewstaffsubject($idcourse){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("tss"=>"tbl_staffsubject"),array(""))
		->joinLeft(array("sm"=>"tbl_staffmaster"),' tss.IdStaff = sm.IdStaff ',array("key"=>"sm.IdStaff","value"=>"CONCAT_WS(' ',IFNULL(sm.FirstName,''),IFNULL(sm.SecondName,''))"))
		->where('tss.IdSubject = ?',$idcourse)
		->group('tss.IdStaff');
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnAppealStaffReg($idAppeal) {
		$semsql = $this->lobjDbAdpt->select()->from(array("appeal" =>"tbl_appealmarksentry"),array('appeal.Examiner1','appeal.Examiner2'))->where("appeal.IdAppeal = ?",$idAppeal)->group('appeal.IdAppeal');
		$result = $this->lobjDbAdpt->fetchAll($semsql);
		return $result;
	}


	public function fnSearchRemarkingApproval($post = array()) {
		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_appeal"), array("a.*"))
		->joinLeft(array('nm' => 'tbl_studentregistration'), 'nm.IdStudentRegistration = a.IdRegistration',array("CONCAT_WS(' ',IFNULL(nm.FName,''),IFNULL(nm.MName,''),IFNULL(nm.LName,'')) as StudentName"))
		->joinLeft(array('b'=>'tbl_program'),'a.IdProgram = b.IdProgram',array('b.ProgramName'))
		->joinLeft(array('c'=>'tbl_subjectmaster'),'a.IdCourse = c.IdSubject',array("CONCAT_WS(' - ',IFNULL(c.SubjectName,''),IFNULL(c.SubCode,'')) AS SubjectName"))
		->joinLeft(array('d'=>'tbl_definationms'),'a.Status = d.idDefinition',array('d.DefinitionCode'));
		//->joinLeft(array('e'=>'tbl_appealmarksentry'),'a.IdAppeal = e.IdAppeal',array('e.IdComponent'));

		if(isset($post['field23']) && !empty($post['field23']) ){
			$select->where("a.IdCourse = ?",$post['field23']);

		}

		if(isset($post['field27']) && !empty($post['field27']) ){
			$getSemCode = $this->getSemcode($post['field27']);
			$select->where("a.SemesterCode = ?",$getSemCode);

		}
		if(isset($post['field2']) && !empty($post['field2']) ){
			$select->where("a.IdStudent = ?",$post['field2']);

		}

		if(isset($post['field1']) && !empty($post['field1']) ){
			$select->where("a.ApplicationSource = ?",$post['field26']);

		}

		if(isset($post['field25']) && !empty($post['field25']) ){
			$select->where("a.Status = ?",$post['field25']);

		} else {
			$select->where("a.Status != ?",'192');
		}

		if(isset($post['field3']) && !empty($post['field3']) ){
			$select = $select->where("a.AppealApplicationID = ?",$post['field3']);
		}

		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;

	}


	public function fnSearchApprovalDetails($idAppeal, $idcourse, $SemesterCode, $idstudent, $IdProgram ) {

		//            $select = $this->lobjDbAdpt->select()
		//                        ->from(array("marksmaster"=>"tbl_marksdistributionmaster"),array('marksmaster.IdMarksDistributionMaster','marksmaster.IdComponentType as IdComponentTypeMain','marksmaster.IdComponentItem as IdComponentItemMain','marksmaster.Marks as MarksTotal'))
		//                        ->joinLeft(array("compdef" =>"tbl_definationms"),'compdef.idDefinition = marksmaster.IdComponentType',array('compdef.DefinitionDesc as ComponentName'))
		//                        ->joinLeft(array("compdef2" =>"tbl_definationms"),'compdef2.idDefinition = marksmaster.IdComponentItem',array('compdef2.DefinitionDesc as ComponentItemName'))
		//                        ->joinLeft(array("sme" =>"tbl_student_marks_entry"),"sme.Component = marksmaster.IdComponentType AND sme.Course='".$idcourse."' AND sme.IdStudentRegistration = '".$idstudent."' ",array('sme.IdStudentMarksEntry'))
		//                        ->where("marksmaster.IdProgram = ?",$IdProgram)
		//                        ->where("marksmaster.IdCourse = ?",$idcourse)
		//                        ->where("marksmaster.semester = ?",$SemesterCode)
		//                        ->where("marksmaster.Status = ?",'243')
		//                         ;
		//           $result = $this->lobjDbAdpt->fetchAll($select);
		//           return $result;

		$select = $this->lobjDbAdpt->select()
		->from(array("appealentry"=>"tbl_appealmarksentry"),array('appealentry.*'))
		->joinLeft(array("compdef" =>"tbl_examination_assessment_type"),'compdef.IdExaminationAssessmentType = appealentry.IdComponent',array('compdef.Description as ComponentName'))
		->joinLeft(array("compdef3" =>"tbl_examination_assessment_item"),'compdef3.IdExaminationAssessmentType = appealentry.IdComponentItem',array('compdef3.Description as MainComponentItemName'))
		->joinLeft(array("sme" =>"tbl_student_marks_entry"),"sme.Component = appealentry.IdComponent AND sme.ComponentItem = appealentry.IdComponentItem AND sme.Course='".$idcourse."' AND sme.IdStudentRegistration = '".$idstudent."' AND sme.MarksEntryStatus = '313' ",array('sme.IdStudentMarksEntry'))
		->where("appealentry.IdAppeal = ?",$idAppeal)->group(array('appealentry.IdComponent','appealentry.IdComponentItem'))
		//->where("marksmaster.IdCourse = ?",$idcourse)
		// ->where("marksmaster.semester = ?",$SemesterCode)
		// ->where("marksmaster.Status = ?",'243')
		//->group("ame.IdComponent")
		;

		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;



	}


	public function fnfetchAllComponentsApprovalAppeal($idAppeal, $IdMarksDistributionMaster,$idcourse ,$Idcmp, $idstudent) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("marksdetails"=>"tbl_marksdistributiondetails"),array("marksdetails.*"))
		->joinLeft(array("assmtype" =>"tbl_examination_assessment_type"),'assmtype.IdExaminationAssessmentType = marksdetails.IdComponentType',array('assmtype.Description as ComponentTypeName'))
		->joinLeft(array("assmitem" =>"tbl_examination_assessment_item"),'assmitem.IdExaminationAssessmentType = marksdetails.IdComponentItem',array('assmitem.Description as ComponentItemName'))
		->joinLeft(array("amed" =>"tbl_appealmarksentry")," amed.IdComponent = '".$Idcmp."' AND amed.IdSubComponent = marksdetails.IdMarksDistributionDetails AND amed.IdAppeal = '".$idAppeal."' ",array('amed.Examiner1Marks','amed.Examiner2Marks','amed.OldMarks','amed.NewMarks'))
		->where("marksdetails.IdMarksDistributionDetails = ?",$IdMarksDistributionMaster)
		;
		return $result = $lobjDbAdpt->fetchAll($select);
	}


	public function fnfetchAllComponentsApprovalAppealNew($idAppeal, $Idcmpitem,$idcourse ,$Idcmp, $idstudent) {

		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("appealentry"=>"tbl_appealmarksentry"),array('appealentry.*'))
		->joinLeft(array("marksdetails"=>"tbl_marksdistributiondetails")," marksdetails.IdMarksDistributionDetails = appealentry.IdSubComponent AND appealentry.IdSubComponent!='0' ", array("marksdetails.IdComponentType as IdComponentTypeDetail","marksdetails.IdComponentItem as IdComponentItemDetail","marksdetails.IdMarksDistributionDetails"))
		->joinLeft(array("assmtype" =>"tbl_examination_assessment_type"),'assmtype.IdExaminationAssessmentType = marksdetails.IdComponentType',array('assmtype.Description as ComponentTypeName'))
		->joinLeft(array("assmitem" =>"tbl_examination_assessment_item"),'assmitem.IdExaminationAssessmentType = marksdetails.IdComponentItem',array('assmitem.Description as ComponentItemName'))

		//->from(array("marksdetails"=>"tbl_marksdistributiondetails"),array("marksdetails.*"))
		//->joinLeft(array("assmtype" =>"tbl_examination_assessment_type"),'assmtype.IdExaminationAssessmentType = marksdetails.IdComponentType',array('assmtype.Description as ComponentTypeName'))
		//->joinLeft(array("assmitem" =>"tbl_examination_assessment_item"),'assmitem.IdExaminationAssessmentType = marksdetails.IdComponentItem',array('assmitem.Description as ComponentItemName'))
		// ->joinLeft(array("amed" =>"tbl_appealmarksentry")," amed.IdComponent = '".$Idcmp."' AND amed.IdSubComponent = marksdetails.IdMarksDistributionDetails AND amed.IdAppeal = '".$idAppeal."' ",array('amed.Examiner1Marks','amed.Examiner2Marks','amed.OldMarks','amed.TotalMarks'))
		->where("appealentry.IdAppeal = ?",$idAppeal)->where("appealentry.IdComponent = ?",$Idcmp)->where("appealentry.IdComponentItem = ?",$Idcmpitem)
		//->group(array('appealentry.IdComponent','appealentry.IdComponentItem'))
		;

		return $result = $lobjDbAdpt->fetchAll($select);


	}


	public function fnInsertApprovalComponent($larrformData, $status, $chooseMarks, $idAppeal) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$sumofmarks = array();
		$IdAppealEntry = $larrformData['IdAppealEntry'];
		foreach($IdAppealEntry as $key=>$value)
		{
			$newMarks = $larrformData[$larrformData['IdAppeal'].'_'.$value.'_NewMarks'];
			$oldMarks = $larrformData[$larrformData['IdAppeal'].'_'.$value.'_OldMarks'];
			$IdStudentMarksEntry = $larrformData[$larrformData['IdAppeal'].'_'.$value.'_IdStudentMarksEntry'];
			$IdComponent = $larrformData[$larrformData['IdAppeal'].'_'.$value.'_IdComponent'];
			$IdComponentItem = $larrformData[$larrformData['IdAppeal'].'_'.$value.'_IdComponentItem'];
			$IdSubComponent = $larrformData[$larrformData['IdAppeal'].'_'.$value.'_IdSubComponent'];

			if($chooseMarks=='381') {
				$finalMarks = $newMarks;
			} else {
				$finalMarks = max($oldMarks,$newMarks);
			}
			if(isset($sumofmarks[$IdComponent."_".$IdComponentItem."_".$IdStudentMarksEntry])) {
				$sumofmarks[$IdComponent."_".$IdComponentItem."_".$IdStudentMarksEntry]+= $finalMarks;
			}
			else {
				$sumofmarks[$IdComponent."_".$IdComponentItem."_".$IdStudentMarksEntry] = $finalMarks;
			}


			$formDataMore = array(
					'NewMarks' => $finalMarks,
					'UpdUser' => $larrformData['UpdUser'],
					'ApprovedBy' => $larrformData['UpdUser'],
					'UpdDate' => date('Y-m-d H:i:s'),
			);
			$where = " IdAppealEntry =  '".$value."' AND IdAppeal='".$idAppeal."'  ";
			$lobjDbAdpt->update('tbl_appealmarksentry',$formDataMore,$where);


			$formDataSDME = array(
					'MarksObtained' => $finalMarks,
					'UpdUser' => $larrformData['UpdUser'],
					'UpdDate' => date('Y-m-d H:i:s'),
			);
			$where = " Component =  '".$IdComponent."' AND ComponentItem='".$IdComponentItem."' AND ComponentDetail ='".$IdSubComponent."' AND IdStudentMarksEntry='".$IdStudentMarksEntry."'  ";
			$lobjDbAdpt->update('tbl_student_detail_marks_entry',$formDataSDME,$where);



		}

		if(count($sumofmarks)>0) {
			foreach($sumofmarks as $key=>$val) {
				$expData = explode('_',$key);
				$idstudent =  $expData[2];
				// UPDATE the totalMarksObtained
				$totalMrksUpdate = array(
						'TotalMarkObtained' => $val,
						'UpdUser' => $larrformData['UpdUser'],
						'UpdDate' => date('Y-m-d H:i:s'),
				);
				$where_TMU = "IdStudentMarksEntry='".$idstudent."'  ";
				$lobjDbAdpt->update('tbl_student_marks_entry',$totalMrksUpdate,$where_TMU);
			}
		}



		//
		// update the student status
		$formDataUpdate= array( 'Status' => $status,'Remarks'=> $larrformData['Remarks']);
		$where_st = " IdAppeal='".$idAppeal."'  ";
		$lobjDbAdpt->update('tbl_appeal',$formDataUpdate,$where_st);

	}

	public function fnInsertRejectComponent($larrformData, $status, $idAppeal) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		// update the student status
		$formDataUpdate= array( 'Status' => $status ,'Remarks'=> $larrformData['Remarks']);
		$where_st = " IdAppeal='".$idAppeal."'  ";
		$lobjDbAdpt->update('tbl_appeal',$formDataUpdate,$where_st);

	}



	public function fnupdateStudentGradePoint($idcourse, $SemesterCode, $idstudent, $IdProgram) {

		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$IdSemester = '';

		$select = $lobjDbAdpt->select()
		->from(array("studregsubj" => "tbl_studentregsubjects"),array('studregsubj.IdStudentRegSubjects','studregsubj.IdStudentRegistration','studregsubj.IdSubject','studregsubj.IdSemesterMain','studregsubj.IdSemesterDetails','studregsubj.IdGrade'))
		->join(array("studreg" =>"tbl_studentregistration"),'studreg.IdStudentRegistration = studregsubj.IdStudentRegistration',array('studreg.registrationId','studreg.IdProgram'))
		->joinLeft(array("prg" =>"tbl_program"),'prg.IdProgram = studreg.IdProgram',array('prg.IdCollege'))
		//->joinLeft(array("sch" =>"tbl_scheme"),'sch.IdScheme = prg.IdScheme',array(''))
		->joinLeft(array("lscp" =>"tbl_landscape"),'studreg.IdLandscape = lscp.IdLandscape',array('lscp.Scheme'))
		->joinLeft(array("sch" =>"tbl_scheme"),'lscp.Scheme = sch.IdScheme',array('sch.EnglishDescription as SchemeName'))
		;
		$select->where("studregsubj.IdSubject = ?",$idcourse);
		$select->where("studregsubj.IdStudentRegistration = ?",$idstudent);

		$semsql1 = $this->lobjDbAdpt->select()->from(array("semdet" =>"tbl_semestermaster"),array('semdet.IdSemesterMaster'))
		->where("semdet.SemesterMainCode = ?",$SemesterCode);
		$result1 = $this->lobjDbAdpt->fetchAll($semsql1);
		if(count($result1)>0) {
			$IdSemester = $result1[0]['IdSemesterMaster'];
			$select->where("studregsubj.IdSemesterMain = ?",$IdSemester);
		} else {
			$semsql2 = $this->lobjDbAdpt->select()->from(array("semdet" =>"tbl_semester"),array('semdet.IdSemester'))
			->where("semdet.SemesterCode = ?",$SemesterCode);
			$result2 = $this->lobjDbAdpt->fetchAll($semsql2);
			if(count($result2)>0) {
				$IdSemester = $result2[0]['IdSemester'];
				$select->where("studregsubj.IdSemesterDetails = ?",$IdSemester);
			}
		}
		$where_cond_grade =  " studregsubj.IdGrade IS NULL OR  studregsubj.IdGrade='pass'  OR  studregsubj.IdGrade='fail'  ";
		$select->where($where_cond_grade);
		//echo $select; die;
		$result = $lobjDbAdpt->fetchAll($select);


		$IdStudentMarksEntry = "";
		$TotalMarksF = 0;
		if(count($result)>0) {
			foreach($result as $values) {

				$id = $values['IdStudentRegSubjects'];
				$IdSubject = $values['IdSubject'];
				$IdProgram = $values['IdProgram'];
				$SemCode = $SemesterCode;


				$select1 = $lobjDbAdpt->select()
				->from(array("sme" => "tbl_student_marks_entry"),array('sme.IdStudentMarksEntry','sme.TotalMarkObtained'))
				->where("sme.IdStudentRegSubjects = ?",$id)->where("sme.MarksEntryStatus = ?",'313');
				$result1 = $lobjDbAdpt->fetchAll($select1);
				if(count($result1)>0) {
					//                    foreach($result1 as $res1)  {   $IdStudentMarksEntry .= $res1['IdStudentMarksEntry'].',';  }
					//                    $IdStudentMarksEntry = trim($IdStudentMarksEntry, ',');
					//
					//                    $select2 = $lobjDbAdpt->select()
					//                                         ->from(array("sdme" => "tbl_student_detail_marks_entry"),array('sdme.MarksObtained'))
					//                                         ->where("sdme.IdStudentMarksEntry IN ($IdStudentMarksEntry) ")->group("sdme.IdStudentMarksEntry");
					//                    $result2 = $lobjDbAdpt->fetchAll($select2);
					//                    foreach($result2 as $res2)  {   $TotalMarksF = $TotalMarksF + $res2['MarksObtained'];  }
					$TotalMarksF = $result1[0]['TotalMarkObtained'];


					$select3 = $lobjDbAdpt->select()
					->from(array("gsm" => "tbl_gradesetup_main"),array('gsm.IdGradeSetUpMain'))
					->where("gsm.IdProgram = ?",$IdProgram)
					->where("gsm.IdSubject = ?",$IdSubject)
					->where("gsm.IdSemester = ?",$SemCode);
					//->where("gsm.IdScheme = ?",$IdScheme);
					$result3 = $lobjDbAdpt->fetchAll($select3);
					foreach($result3 as $res3)  {
						$select4 = $lobjDbAdpt->select()
						->from(array("gs" => "tbl_gradesetup"),array('gs.Grade','gs.Pass','gs.Rank','gs.GradePoint'))
						->where(" $TotalMarksF BETWEEN  gs.MinPoint AND gs.MaxPoint")
						->where("gs.IdGradeSetUpMain = ?",$res3['IdGradeSetUpMain']);
						$result4 = $lobjDbAdpt->fetchAll($select4);
						if(count($result4)>0) {
							if($result4[0]['Rank']=='1') {
								$rankD = 'pass';
							} else {  $rankD = 'fail';
							}
							$formDataupdate = array(
									'IdGrade' => $rankD,
									'Grade' => $result4[0]['Grade'],
									'Totalmarks'  => $TotalMarksF,
									'GradePoint' => $result4[0]['GradePoint'],
									'Rank' => $result4[0]['Rank'],
									'Pass' =>  $result4[0]['Pass']
							);
							$lobjDbAdpt->update('tbl_studentregsubjects',$formDataupdate, array('IdStudentRegSubjects = ?' => $id));
						}

					}


				}

			}

		}

	}


	// calculation and updation gradepoint for exam scaling

	public function fnupdateStudentGradePointScaling($idcourse, $SemesterCode, $idstudent, $IdProgram) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$IdSemester = '';
		$select = $lobjDbAdpt->select()
		->from(array("studregsubj" => "tbl_studentregsubjects"),array('studregsubj.IdStudentRegSubjects','studregsubj.IdStudentRegistration','studregsubj.IdSubject','studregsubj.IdSemesterMain','studregsubj.IdSemesterDetails','studregsubj.IdGrade'))
		->join(array("studreg" =>"tbl_studentregistration"),'studreg.IdStudentRegistration = studregsubj.IdStudentRegistration',array('studreg.registrationId','studreg.IdProgram'))
		->joinLeft(array("prg" =>"tbl_program"),'prg.IdProgram = studreg.IdProgram',array('prg.IdCollege'))
		->joinLeft(array("lndscp" =>"tbl_landscape"),'studreg.Idlandscape = studreg.IdProgram',array(''))
		->joinLeft(array("sch" =>"tbl_scheme"),'sch.IdScheme = lndscp.Scheme',array('sch.IdScheme'))
		;
		$select->where("studregsubj.IdSubject = ?",$idcourse);
		$select->where("studregsubj.IdStudentRegistration = ?",$idstudent);

		$semsql1 = $this->lobjDbAdpt->select()->from(array("semdet" =>"tbl_semestermaster"),array('semdet.IdSemesterMaster'))
		->where("semdet.SemesterMainCode = ?",$SemesterCode);
		$result1 = $this->lobjDbAdpt->fetchAll($semsql1);

		if(count($result1)>0) {
			$IdSemester = $result1[0]['IdSemesterMaster'];
			$select->where("studregsubj.IdSemesterMain = ?",$IdSemester);
		} else {
			$semsql2 = $this->lobjDbAdpt->select()->from(array("semdet" =>"tbl_semester"),array('semdet.IdSemester'))
			->where("semdet.SemesterCode = ?",$SemesterCode);
			$result2 = $this->lobjDbAdpt->fetchAll($semsql2);
			if(count($result2)>0) {
				$IdSemester = $result2[0]['IdSemester'];
				$select->where("studregsubj.IdSemesterDetails = ?",$IdSemester);
			}
		}
		$where_cond_grade =  " studregsubj.IdGrade IS NULL OR  studregsubj.IdGrade='pass'  OR  studregsubj.IdGrade='fail'  ";
		$select->where($where_cond_grade);
		//echo $select; die;
		$result = $lobjDbAdpt->fetchAll($select);
		$IdStudentMarksEntry = "";
		$TotalMarksF = 0;
		if(count($result)>0) {
			foreach($result as $values) {

				$id = $values['IdStudentRegSubjects'];
				$IdSubject = $values['IdSubject'];
				$IdProgram = $values['IdProgram'];
				$SemCode = $SemesterCode;


				$select1 = $lobjDbAdpt->select()
				->from(array("sme" => "tbl_student_marks_entry"),array('sme.IdStudentMarksEntry','sme.TotalMarkObtainedScaling'))
				->where("sme.IdStudentRegSubjects = ?",$id)->where("sme.MarksEntryStatus = ?",'313');

				//die;
				$result1 = $lobjDbAdpt->fetchAll($select1);
				// echo '<pre>';
				//print_r($result1);
				if(count($result1)>0) {
					//foreach($result1 as $res1)  {   $IdStudentMarksEntry .= $res1['IdStudentMarksEntry'].',';  }
					//$IdStudentMarksEntry = trim($IdStudentMarksEntry, ',');

					//$select2 = $lobjDbAdpt->select()
					//                     ->from(array("sdme" => "tbl_student_detail_marks_entry"),array('sdme.MarksObtained'))
					//                     ->where("sdme.IdStudentMarksEntry IN ($IdStudentMarksEntry) ")->group("sdme.IdStudentMarksEntry");
					//$result2 = $lobjDbAdpt->fetchAll($select2);
					//foreach($result2 as $res2)  {   $TotalMarksF = $TotalMarksF + $res2['MarksObtained'];  }

					$TotalMarksF = $result1[0]['TotalMarkObtainedScaling'];
					$select3 = $lobjDbAdpt->select()
					->from(array("gsm" => "tbl_gradesetup_main"),array('gsm.IdGradeSetUpMain'))
					->where("gsm.IdProgram = ?",$IdProgram)
					->where("gsm.IdSubject = ?",$IdSubject)
					->where("gsm.IdSemester = ?",$SemCode);

					//->where("gsm.IdScheme = ?",$IdScheme);
					$result3 = $lobjDbAdpt->fetchAll($select3);

					foreach($result3 as $res3)  {
						$select4 = $lobjDbAdpt->select()
						->from(array("gs" => "tbl_gradesetup"),array('gs.Grade','gs.Pass','gs.Rank','gs.GradePoint'))
						->where(" $TotalMarksF BETWEEN  gs.MinPoint AND gs.MaxPoint")
						->where("gs.IdGradeSetUpMain = ?",$res3['IdGradeSetUpMain']);
						$result4 = $lobjDbAdpt->fetchAll($select4);
						//                           echo '<pre>';
						//                     print_r($result4); die;
						if(count($result4)>0) {
							//if($result4[0]['Rank']=='1') { $rankD = 'pass'; } else {  $rankD = 'fail'; }
							$formDataupdate = array(
									//'IdGrade' => $rankD,
									//'Grade' => $result4[0]['Grade'],
									'TotalMarksScaling'  => $TotalMarksF,
									'GradePointScaling' => $result4[0]['GradePoint'],
									// 'Rank' => $result4[0]['Rank'],
									// 'Pass' =>  $result4[0]['Pass']
							);
							// echo '<pre>';
							// print_r($formDataupdate);
							$lobjDbAdpt->update('tbl_studentregsubjects',$formDataupdate, array('IdStudentRegSubjects = ?' => $id));
						}

					}


				}

			}

		}

	}















	public function fnSearchRemarkingEntry($post = array()) {
		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_appeal"), array("a.*"))
		->joinLeft(array('nm' => 'tbl_studentregistration'), 'nm.IdStudentRegistration = a.IdRegistration',array("CONCAT_WS(' ',IFNULL(nm.FName,''),IFNULL(nm.MName,''),IFNULL(nm.LName,'')) as StudentName"))
		->joinLeft(array('b'=>'tbl_program'),'a.IdProgram = b.IdProgram',array('b.ProgramName'))
		->joinLeft(array('c'=>'tbl_subjectmaster'),'a.IdCourse = c.IdSubject',array("CONCAT_WS(' - ',IFNULL(c.SubjectName,''),IFNULL(c.SubCode,'')) AS SubjectName"))
		->joinLeft(array('d'=>'tbl_definationms'),'a.Status = d.idDefinition',array('d.DefinitionCode'));
		//->joinLeft(array('e'=>'tbl_appealmarksentry'),'a.IdAppeal = e.IdAppeal',array('e.IdComponent'));

		if(isset($post['field23']) && !empty($post['field23']) ){
			$select = $select->where("a.IdCourse = ?",$post['field23']);

		}

		if(isset($post['field27']) && !empty($post['field27']) ){
			$getSemCode = $this->getSemcode($post['field27']);
			$select = $select->where("a.SemesterCode = ?",$getSemCode);

		}
		if(isset($post['field2']) && !empty($post['field2']) ){
			$select = $select->where("a.IdStudent = ?",$post['field2']);

		}

		if(isset($post['field25']) && !empty($post['field25']) ){
			$select = $select->where("a.Status = ?",$post['field25']);

		}
		if(isset($post['field1']) && !empty($post['field1']) ){
			$select = $select->where("a.ApplicationSource = ?",$post['field1']);
		}

		if(isset($post['field3']) && !empty($post['field3']) ){
			$select = $select->where("a.AppealApplicationID = ?",$post['field3']);
		}

		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;
	}


	public function showStatusNotDraft(){
		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_definationms"), array("key"=>"a.idDefinition","value"=>"a.DefinitionDesc"))
		->where("a.idDefType=55 AND LOWER(a.DefinitionDesc) IN ('approved','Reject','Entry')");
		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;

	}

	public function fnInsertRemarking($formData) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		// update the student status
		$formData['update_date']=date('Y-m-d H:i:s');
		$row=$lobjDbAdpt->insert('tbl_remarking_history',$formData);
		$id = $lobjDbAdpt->lastInsertId();
		return $id;
	}
	public function fnUpdateRemarking($formData,$idRemarkingHistory) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		// update the student status
		$formData['update_date']=date('Y-m-d H:i:s');
		$row=$lobjDbAdpt->update('tbl_remarking_history',$formData,'idRemarkingHistory='.$idRemarkingHistory);
		return $row;
	}
	public function fnGetAllRemarkingHistory($filter=null,$status=null) {
		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_remarking_history"),array('idRemarkingHistory','update_date','oldmark','remarking','Note'))
		->join(array("sme"=>'tbl_student_marks_entry'),'a.IdStudentMarksEntry=sme.IdStudentMarksEntry')
		->join(array('sm'=>'tbl_subjectmaster'),'sme.course=sm.idsubject',array('ShortName','SubjectName'=>'BahasaIndonesia','CreditHours'))
		->join(array('smt'=>'tbl_semestermaster'),'smt.IdSemesterMaster=sme.IdSemester',array('Semester'=>'SemesterMainName'))
		->joinLeft(array('usr'=>'tbl_user'),'usr.iduser=a.updUser',array('proposer'=>"CONCAT_WS(' ',usr.fName,usr.mName,usr.lName)"))
		->join(array('sr'=>'tbl_studentregistration'),'sr.idStudentRegistration=sme.idStudentRegistration',array('registrationId'))
		->join(array('pr'=>'tbl_program'),'sr.IdProgram=pr.IdProgram',array())
		->joinLeft(array('def'=>'tbl_examination_assessment_type'),'def.IdExaminationAssessmentType=sme.Component',array('ComponentName'=>'DescriptionDefaultlang'))
		->join(array('appl'=>'applicant_profile'),'appl.appl_id=sr.IdApplication',array('student_name'=>"CONCAT_WS(' ',appl_fname,appl_mname,appl_lname)"))
		->order('a.update_date DESC');
		//->where("a.IdStudentMarkEntry=",$idRemarking);
		$session = new Zend_Session_Namespace('sis');
		
		//if ($filter==null){
			if($session->IdRole == 605 || $session->IdRole == 311 || $session->IdRole == 298){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
				$select->where("pr.IdCollege='".$session->idCollege."'");
			}
			if($session->IdRole == 470 || $session->IdRole == 480){ //scheduler and chief of profi only
				$select->where("sr.IdProgram='".$session->idDepartment."'");
			}
		//}
		
		if ($status!=null) $select->where("a.ApprovedBy is not null");
		else $select->where("a.ApprovedBy is  null");
		
		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;
	
	}
	public function fnGetRemarkingHistory($idRemarking) {
		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_remarking_history"))
		->join(array("sme"=>'tbl_student_marks_entry'),'a.IdStudentMarksEntry=sme.IdStudentMarksEntry',array('IdSemester','IdStudentRegistration','idSubject'=>'Course','IdStudentRegSubjects'))
		->join(array('sr'=>'tbl_studentregistration'),'sr.idStudentRegistration=sme.idStudentRegistration',array('IdProgram'))
		->where("a.idRemarkingHistory=?",$idRemarking);
		$result = $this->lobjDbAdpt->fetchRow($select);
		return $result;
	
	}
	public function fnApproveRemarking($formData,$idremarking) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		// update the student status
		$formData['date_of_approval']=date('Y-m-d H:i:s');
		$formData['status']="1";
		//echo var_dump($formData);exit;
		$lobjDbAdpt->update('tbl_remarking_history',$formData,'idRemarkingHistory='.$idremarking);
		
			//get mark
			$remark=$this->fnGetRemarkingHistory($idremarking);
			//echo var_dump($remark);exit;
			$db = new Examination_Model_DbTable_StudentMarkEntry();
			//update Student_Mark_Entry
			$data =array('TotalMarkObtained'=>$remark['remarking'],
						 'FinalTotalMarkObtained' => $remark['remarking']*$remark['Percentage']/100,
						 'category'=>'3','category_ref_id'=>$idremarking
						);
			//echo var_dump($data);echo $remark['IdStudentMarksEntry'];exit;
			$db->updateData($data, $remark['IdStudentMarksEntry']);
			//recalculate Grade
			$dbMark=new Examination_Model_DbTable_StudentMarkEntry();
			$dbMark->getStudentTotalMark($remark['IdSemester'], $remark['IdProgram'], $remark['idSubject'], $remark['IdStudentRegistration'], $remark['IdStudentRegSubjects'],true,true);
			
	}

	public function fnRejectRemarking($formData,$idremarking) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		// update the student status
		$formData['date_of_approval']=date('Y-m-d H:i:s');
		$formData['status']="2";
		//echo var_dump($formData);exit;
		$lobjDbAdpt->update('tbl_remarking_history',$formData,'idRemarkingHistory='.$idremarking);
	}

}