<?php
class Examination_Model_DbTable_Generategrade extends Zend_Db_Table { //Model Class for Users Details

	private $lobjstudentassessmentModel;
	private $lobjsemesterModel;
	
	public function init(){
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$this->lobjstudentassessmentModel = new Examination_Model_DbTable_Studentassessment();
		$this->lobjsemesterModel = new GeneralSetup_Model_DbTable_Semester();
	}

	public function fnGetCourseListsub($IdProgram) { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_landscape"),array(''))
		->join(array("c" => "tbl_landscapesubject"),'a.IdLandscape = c.IdLandscape',array(''))
		->joinLeft(array("d" => "tbl_subjectmaster"),'c.IdSubject  = d.IdSubject',array("key"=>"d.IdSubject","value"=>"CONCAT_WS('-',IFNULL(d.SubjectName,''),IFNULL(d.SubCode,''))"))
		->where("a.IdProgram = ?",$IdProgram)->group("d.IdSubject");

		return $result = $lobjDbAdpt->fetchAll($select);
	}


	public function fnGetStudents($post = array()){	

		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("studregsubj" => "tbl_studentregsubjects"),array('studregsubj.IdStudentRegSubjects','studregsubj.IdStudentRegistration','studregsubj.IdSubject','studregsubj.IdSemesterMain','studregsubj.IdSemesterDetails'))
		->join(array("studreg" =>"tbl_studentregistration"),'studreg.IdStudentRegistration = studregsubj.IdStudentRegistration',array('studreg.registrationId','studreg.IdProgram'))
		->joinLeft(array("prg" =>"tbl_program"),'prg.IdProgram = studreg.IdProgram',array('prg.IdCollege','prg.Award'))
		->joinLeft(array("lscp" =>"tbl_landscape"),'studreg.IdLandscape = lscp.IdLandscape',array('lscp.Scheme'))
		->joinLeft(array("sch" =>"tbl_scheme"),'lscp.Scheme = sch.IdScheme',array('sch.IdScheme'));


		if(isset($post['field23']) && !empty($post['field23']) ){
			$select->where("studregsubj.IdSubject = ?",$post['field23']);
		}


		if(isset($post['field27']) && !empty($post['field27']) ){
			$dataSem = explode('_',$post['field27']);
			if($dataSem[1]=='detail') {
				$semDetail = $dataSem[0];
				$select->where("studregsubj.IdSemesterDetails = ?",$semDetail);
				$select->joinLeft(array("semdet" =>"tbl_semester"),"semdet.IdSemester = '".$semDetail."' ",array('semdet.SemesterCode as SemCode'));
			} else {
				$semMain = $dataSem[0];
				$select = $select->where("studregsubj.IdSemesterMain = ?",$semMain);
				$select->joinLeft(array("semmain" =>"tbl_semestermaster"),"semmain.IdSemesterMaster = '".$semMain."' ",array('semmain.SemesterMainCode as SemCode'));
			}
		}


		if(isset($post['field2']) && !empty($post['field2']) ){
			$select->where("studreg.registrationId = ?",$post['field2']);
		}

		if(isset($post['field25']) && !empty($post['field25']) ){
			$select->where("sch.IdScheme = ?",$post['field25']);
		}

		if(isset($post['field5']) && !empty($post['field5']) ){
			$select->where("prg.IdCollege = ?",$post['field5']);
		}

		if(isset($post['field24']) && !empty($post['field24']) ){
			$select->where("studreg.IdProgram = ?",$post['field24']);
		}


		// only active, defer and dormant students are eligible for grade.
		$select->where("studreg.profileStatus = ?",'92')
				 ->orwhere("studreg.profileStatus = ?",'248')
				 ->orwhere("studreg.profileStatus = ?",'253');
		$select->where("studregsubj.IdGrade IS NULL")->order('studregsubj.IdStudentRegSubjects DESC');
		$result = $lobjDbAdpt->fetchAll($select);	
		
		if(count($result)>0) {
			$errMsg = '1';
			foreach($result as $det){			
				$formDataupdate = array();
				$studregcoursegpa = $this->lobjstudentassessmentModel->fncalculatestudentcourseGpa($det);				
				
				$semcode = '';
				$formDataupdate['Totalmarks'] = $studregcoursegpa;
				if($det['IdSemesterDetails'] != ''){				
					$larrsem = $this->lobjsemesterModel->getsemDetail($det['IdSemesterDetails']);
					if(!empty($larrsem)){
						$semcode = $larrsem[0]['SemesterCode'];
					}
				}else{
					$larrsemmain = $this->lobjsemesterModel->getsemMainDet($det['IdSemesterMain']);				
					if(!empty($larrsemmain)){
						$semcode = $larrsemmain[0]['SemesterMainCode'];
					}
				}
				
//				$gpadet = $this->lobjstudentassessmentModel->fngetgpadet($semcode,$det['IdScheme'],$det['IdProgram']);
//				if(!empty($gpadet)){
//					foreach($gpadet as $dat){						
//						if(($dat['Minimum'] <= floatval($studregcoursegpa)) && (floatval($studregcoursegpa) <= floatval($dat['Maximum']))  ){
//							$formDataupdate['IdGrade'] = $dat['Gradevalue'];
//							$formDataupdate['GradePoint'] = $dat['Gradepoint'];
//						}
//						
//						if(($dat['Minimum'] <= floatval($studregcoursegpa)) && (floatval($studregcoursegpa) <= $dat['Maximum'])  ){
//							$formDataupdate['IdGrade'] = $dat['Gradevalue'];
//							$formDataupdate['GradePoint'] = $dat['Gradepoint'];
//						}
//					}
//				}
//				$lobjDbAdpt->update('tbl_studentregsubjects',$formDataupdate, array('IdStudentRegSubjects = ?' => $det['IdStudentRegSubjects']));				

				
				$select3 = $lobjDbAdpt->select()
					->from(array("gsm" => "tbl_gradesetup_main"),array('gsm.IdGradeSetUpMain'))
					->where("gsm.IdProgram = ?",$det['IdProgram'])
					->where("gsm.IdSubject = ?",$det['IdSubject'])
					->where("gsm.IdSemester = ?",$semcode);
					//->where("gsm.IdScheme = ?",$IdScheme);

					$result3 = $lobjDbAdpt->fetchAll($select3);					
					if(count($result3)==0) {
						$select3 = $lobjDbAdpt->select()
						->from(array("gsm" => "tbl_gradesetup_main"),array('gsm.IdGradeSetUpMain'))
						->where("gsm.IdProgram = ?",$det['IdProgram'])
						->where("gsm.IdSemester = ?",$semcode);
						$result3 = $lobjDbAdpt->fetchAll($select3);
						if(count($result3)==0) {
							$select3 = $lobjDbAdpt->select()
							->from(array("gsm" => "tbl_gradesetup_main"),array('gsm.IdGradeSetUpMain'))
							->where("gsm.IdScheme = ?",$det['IdScheme'])
							->where("gsm.IdAward = ?",$det['Award'])
							->where("gsm.IdSemester = ?",$semcode);
							$result3 = $lobjDbAdpt->fetchAll($select3);
						}
					}		
								
					if(count($result3)>0) {
						foreach($result3 as $res3)  {
							$select4 = $lobjDbAdpt->select()
							->from(array("gs" => "tbl_gradesetup"),array('gs.Grade','gs.Pass','gs.Rank','gs.GradePoint'))
							->where(" $studregcoursegpa BETWEEN  gs.MinPoint AND gs.MaxPoint")
							->where("gs.IdGradeSetUpMain = ?",$res3['IdGradeSetUpMain']);
							$result4 = $lobjDbAdpt->fetchAll($select4);
							if(count($result4)>0) {
								if($result4[0]['Pass']=='1') {
									$rankD = 'pass';
								} else {  $rankD = 'fail';
								}
								$formDataupdate = array(
										'IdGrade' => $rankD,
										'Grade' => $result4[0]['Grade'],
										'Totalmarks'  => $studregcoursegpa,
										'GradePoint' => $result4[0]['GradePoint'],
										'Rank' => $result4[0]['Rank'],
										'Pass' =>  $result4[0]['Pass']
								);								
								$lobjDbAdpt->update('tbl_studentregsubjects',$formDataupdate, array('IdStudentRegSubjects = ?' => $det['IdStudentRegSubjects']));
							}

						}
					}
			}
		}else{
			$errMsg = '0';
		}
		return $errMsg;
		
		
		
		//die;

		//$IdStudentMarksEntry = "";
		//$TotalMarksF = 0;

		/*if(count($result)>0) {
			foreach($result as $values) {

				$id = $values['IdStudentRegSubjects'];
				$IdSubject = $values['IdSubject'];
				$IdProgram = $values['IdProgram'];
				$IdScheme = $values['IdScheme'];
				$IdAward = $values['Award'];
				$SemCode = $values['SemCode'];


				$select1 = $lobjDbAdpt->select()
				->from(array("sme" => "tbl_student_marks_entry"),array('sme.IdStudentMarksEntry','sme.TotalMarkObtained'))
				->where("sme.IdStudentRegSubjects = ?",$id)->where("sme.MarksEntryStatus = '313' ");
				$result1 = $lobjDbAdpt->fetchAll($select1);

				if(count($result1)>0) {
					//                    foreach($result1 as $res1)  {   $IdStudentMarksEntry .= $res1['IdStudentMarksEntry'].',';  }
					//                    $IdStudentMarksEntry = trim($IdStudentMarksEntry, ',');
					//
					//                    $select2 = $lobjDbAdpt->select()
					//                                         ->from(array("sdme" => "tbl_student_detail_marks_entry"),array('sdme.MarksObtained'))
					//                                         ->where("sdme.IdStudentMarksEntry IN ($IdStudentMarksEntry) ");//->group("sdme.IdStudentMarksEntry");
					//                    $result2 = $lobjDbAdpt->fetchAll($select2);
					//
					//                    foreach($result2 as $res2)  {   $TotalMarksF = $TotalMarksF + $res2['MarksObtained'];  }
					$TotalMarksF = $result1[0]['TotalMarkObtained'];

					$select3 = $lobjDbAdpt->select()
					->from(array("gsm" => "tbl_gradesetup_main"),array('gsm.IdGradeSetUpMain'))
					->where("gsm.IdProgram = ?",$IdProgram)
					->where("gsm.IdSubject = ?",$IdSubject)
					->where("gsm.IdSemester = ?",$SemCode);
					//->where("gsm.IdScheme = ?",$IdScheme);

					$result3 = $lobjDbAdpt->fetchAll($select3);
					if(count($result3)==0) {
						$select3 = $lobjDbAdpt->select()
						->from(array("gsm" => "tbl_gradesetup_main"),array('gsm.IdGradeSetUpMain'))
						->where("gsm.IdProgram = ?",$IdProgram)
						->where("gsm.IdSemester = ?",$SemCode);
						$result3 = $lobjDbAdpt->fetchAll($select3);
						if(count($result3)==0) {
							$select3 = $lobjDbAdpt->select()
							->from(array("gsm" => "tbl_gradesetup_main"),array('gsm.IdGradeSetUpMain'))
							->where("gsm.IdScheme = ?",$IdScheme)
							->where("gsm.IdAward = ?",$IdAward)
							->where("gsm.IdSemester = ?",$SemCode);
							$result3 = $lobjDbAdpt->fetchAll($select3);
						}
					}

					if(count($result3)>0) {
						foreach($result3 as $res3)  {
							$select4 = $lobjDbAdpt->select()
							->from(array("gs" => "tbl_gradesetup"),array('gs.Grade','gs.Pass','gs.Rank','gs.GradePoint'))
							->where(" $TotalMarksF BETWEEN  gs.MinPoint AND gs.MaxPoint")
							->where("gs.IdGradeSetUpMain = ?",$res3['IdGradeSetUpMain']);
							$result4 = $lobjDbAdpt->fetchAll($select4);
							if(count($result4)>0) {
								if($result4[0]['Pass']=='1') {
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
			$errMsg = '1';
		} else {  $errMsg = '0';
		}*/
		

	}


	public function fnGetStudentsCGPA($post = array(),$userId){
		
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("studregsubj" => "tbl_studentregsubjects"),array('studregsubj.IdStudentRegSubjects','studregsubj.IdStudentRegistration','studregsubj.IdSubject','studregsubj.IdSemesterMain','studregsubj.IdSemesterDetails','studregsubj.GradePoint'))
		->join(array("studreg" =>"tbl_studentregistration"),'studreg.IdStudentRegistration = studregsubj.IdStudentRegistration',array('studreg.registrationId','studreg.IdProgram'))
		->joinLeft(array("prg" =>"tbl_program"),'prg.IdProgram = studreg.IdProgram',array(''))
		->joinLeft(array("semdet" =>"tbl_semester"),"semdet.IdSemester = studregsubj.IdSemesterDetails ",array('semdet.SemesterCode as SemCodeDetails'))
		->joinLeft(array("semmain" =>"tbl_semestermaster"),"semmain.IdSemesterMaster = studregsubj.IdSemesterMain ",array('semmain.SemesterMainCode as SemCodeMain'))
		;
		

		if(isset($post['field27']) && !empty($post['field27']) ){
			$dataSem = explode('_',$post['field27']);
			if($dataSem[1]=='detail') {
				$semDetail = $dataSem[0];
				$select->where("studregsubj.IdSemesterDetails = ?",$semDetail);
				//$select->joinLeft(array("semdet" =>"tbl_semester"),"semdet.IdSemester = '".$semDetail."' ",array('semdet.SemesterCode as SemCode'));
			} else {
				$semMain = $dataSem[0];
				$select = $select->where("studregsubj.IdSemesterMain = ?",$semMain);
				//$select->joinLeft(array("semmain" =>"tbl_semestermaster"),"semmain.IdSemesterMaster = '".$semMain."' ",array('semmain.SemesterMainCode as SemCode'));
			}
		}
		

		if(isset($post['field2']) && !empty($post['field2']) ){
			$select->where("studreg.registrationId = ?",$post['field2']);
			//$select->group(array("studregsubj.IdSemesterMain","studregsubj.IdSemesterDetails"));
		}
		
		if(isset($post['field24']) && !empty($post['field24']) ){
			$select->where("studreg.IdProgram = ?",$post['field24']);
			//$select->group("studregsubj.IdStudentRegistration");
		}

		// only active, defer and dormant students are eligible for marks entry.
		$select->where("studreg.profileStatus = ?",'92')
				 ->orwhere("studreg.profileStatus = ?",'248')
				 ->orwhere("studreg.profileStatus = ?",'253');
		$where_grade_cond = " ( studregsubj.IdGrade IS NULL OR studregsubj.IdGrade='pass' OR  studregsubj.IdGrade='fail' )";
		$select->where($where_grade_cond)->order('studregsubj.IdStudentRegSubjects DESC');	
		
		
		
		$result = $lobjDbAdpt->fetchAll($select);
		$totalcredithour = 0;
		$markswithcredit = 0;
		$registrationId = '';
		$semcode = '';
		$IdProgram = '';
		$errMsg = '';
//		echo '<pre>';
//		print_r($result);
		// Below code changed for usty so hide the previous code and new code for implemen new way to calculate the cgpa / gpa
		if(count($result)>0) {
			foreach($result as $values) {
				$idStudent = $values['IdStudentRegistration'];
				$IdSemesterMain = $values['IdSemesterMain'];
				$IdSemesterDetails = $values['IdSemesterDetails'];
				$IdProgram = $values['IdProgram'];
				if($values['SemCodeMain']!='') {
					$IdSemesterCode = $values['SemCodeMain'];
				} else {  
					$IdSemesterCode = $values['SemCodeDetails'];
				}
				$registrationId = $values['registrationId'];
				
				$where_del  =   "IdStudentRegistration = '".$idStudent."' AND IdSemester = '".$IdSemesterCode."' ";
				$lobjDbAdpt->delete('tbl_gpacalculation',$where_del);				
				
				//Now get the total credit hours for the subjects
				$totalCreditHrsSubj = $this->fngetsubjectcredithour($values['IdSubject']);									
				if(!empty($totalCreditHrsSubj)){
					$totalcredithour = $totalcredithour + $totalCreditHrsSubj[0]['CreditHours'];
				}				
				// Now get the marks for the respected subject				
				if($values['SemCodeDetails'] != ''){
					$semcode = $values['SemCodeDetails'];
				}else if($values['SemCodeMain'] != ''){
					$semcode = $values['SemCodeMain'];
				}
				$marksofsubject = $this->fngetstudentsubjectMarks($idStudent,$semcode,$values['IdSubject'],$values['IdStudentRegSubjects']);
				// Multiply with credit hours
				$calculatedmarks = $marksofsubject * $totalCreditHrsSubj[0]['CreditHours'];
				$markswithcredit = $markswithcredit + $calculatedmarks;
			}
			$errMsg = '1';
		}else{
			$errMsg = '0';
		}
		
		// Now the GPA is
		if($errMsg == '1'){
			$gpa = $markswithcredit / $totalcredithour;		
			$paramData = array('IdStudentRegistration' => $idStudent,
							'registrationId' => $registrationId,
							'IdSemester' => $semcode,
							'IdProgram' => $IdProgram,
							'Gpa' => $gpa,
							'Cgpa' => $gpa,
							'UpdUser' => $userId,
							'UpdDate' => date('Y-m-d H:i:s'),
							'IdApplication' => 0,
					);					
			$lobjDbAdpt->insert('tbl_gpacalculation',$paramData);
		}
		
		
		
		
	
		
		//die;

		/*if(count($result)>0) {
			foreach($result as $values) {
				$idStudent = $values['IdStudentRegistration'];
				$IdSemesterMain = $values['IdSemesterMain'];
				$IdSemesterDetails = $values['IdSemesterDetails'];
				$IdProgram = $values['IdProgram'];

				if($values['SemCodeMain']!='') {
					$IdSemesterCode = $values['SemCodeMain'];
				} else {  $IdSemesterCode = $values['SemCodeDetails'];
				}

				$registrationId = $values['registrationId'];

				$where_del  =   "IdStudentRegistration = '".$idStudent."' AND IdSemester = '".$IdSemesterCode."' ";
				$lobjDbAdpt->delete('tbl_gpacalculation',$where_del);


				$totalCreditHrsSubj = $this->getSubGradePtCreditHrsSum($idStudent,$IdSemesterMain,$IdSemesterDetails);
				
				$totalCreditHrsSem = $this->getSemesterCreditHrsSum($idStudent,$IdSemesterMain,$IdSemesterDetails);
				
				$totalCreditHrsTaken = $this->getTakenCreditHrsSum($idStudent,$IdSemesterMain,$IdSemesterDetails);

				if($totalCreditHrsSubj>0 && $totalCreditHrsSubj!='') {
					$totalGPA    =   ($totalCreditHrsSubj/$totalCreditHrsSem);
					$totalCGPA    =   ($totalCreditHrsSubj/$totalCreditHrsTaken);
				} else {
					$totalGPA    =   '0';
					$totalCGPA    =   '0';
				}

				$paramData = array(     'IdStudentRegistration' => $idStudent,
						'registrationId' => $registrationId,
						'IdSemester' => $IdSemesterCode,
						'IdProgram' => $IdProgram,
						'Gpa' => $totalGPA,
						'Cgpa' => $totalCGPA,
						'UpdUser' => $userId,
						'UpdDate' => date('Y-m-d H:i:s'),
						'IdApplication' => 0,
				);
				$lobjDbAdpt->insert('tbl_gpacalculation',$paramData);

			}
			$errMsg = '1';
		} else  {  $errMsg = '0';
		}*/
	}
	
	public function fngetstudentsubjectMarks($idStudent,$semcode,$idsubject,$idStudentRegSubjects){
		$subjectmarks = 0;
		$result = array();
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("a" => "tbl_student_marks_entry"),array('a.TotalMarkObtained'))
		->where('a.IdStudentRegistration = ?',$idStudent)
		->where('a.IdSemester	 = ?',$semcode)
		->where('a.Course = ?',$idsubject)
		->where('a.IdStudentRegSubjects = ?',$idStudentRegSubjects);
		$result = $lobjDbAdpt->fetchAll($select);
		if(!empty($result)){
			foreach($result as $rec){
				$subjectmarks = $subjectmarks + $rec['TotalMarkObtained'];
			}
		}
		return $subjectmarks;
	}
	
	public function fngetsubjectcredithour($idsubject){
		$result = array();
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("subj" => "tbl_subjectmaster"),array('subj.CreditHours'))
		->where('subj.IdSubject = ?',$idsubject);
		$result = $lobjDbAdpt->fetchAll($select);	
		return $result;	
	}


	public function getSubGradePtCreditHrsSum($idStudent,$IdSemesterMain=NULL,$IdSemesterDetails=NULL) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$TotalMarksAbove =  0;
		$select1 = $lobjDbAdpt->select()
		->from(array("srs" => "tbl_studentregsubjects"),array('srs.GradePoint','srs.IdSubject'))
		->joinLeft(array("subj" => "tbl_subjectmaster"), "subj.IdSubject = srs.IdSubject",array('subj.CreditHours'))
		->where("srs.IdStudentRegistration = ?",$idStudent);
		if($IdSemesterMain!='') {
			$select1->where("srs.IdSemesterMain = ?",$IdSemesterMain);
		} else {
			$select1->where("srs.IdSemesterDetails = ?",$IdSemesterDetails);
		}
		$select1->group("srs.IdSubject");
		
		$result1 = $lobjDbAdpt->fetchAll($select1);
		if(count($result1)>0) {
			foreach($result1 as $res1)  {
				$TotalMarksAbove = $TotalMarksAbove + ( $res1['GradePoint']*$res1['CreditHours']);
			}
		} else { $TotalMarksAbove  =0;
		}
		return $TotalMarksAbove;
	}


	public function getSemesterCreditHrsSum($idStudent,$IdSemesterMain=NULL,$IdSemesterDetails=NULL) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$TotalMarksBelow = 0;
		$select1 = $lobjDbAdpt->select()
		->from(array("srs" => "tbl_studentregsubjects"),array('srs.GradePoint','srs.IdSubject'))
		->joinLeft(array("subj" => "tbl_subjectmaster"), "subj.IdSubject = srs.IdSubject",array('subj.CreditHours'))
		->where("srs.IdStudentRegistration = ?",$idStudent);
		if($IdSemesterMain!='') {
			$select1->where("srs.IdSemesterMain = ?",$IdSemesterMain);
		} else {
			$select1->where("srs.IdSemesterDetails = ?",$IdSemesterDetails);
		}
		$select1->group("srs.IdSubject");
		;
		$result1 = $lobjDbAdpt->fetchAll($select1);
		if(count($result1)>0) {
			foreach($result1 as $res2)  {
				$TotalMarksBelow = $TotalMarksBelow + ( $res2['CreditHours']);
			}
		} else { $TotalMarksBelow  =0;
		}
		return $TotalMarksBelow;
	}

	public function getTakenCreditHrsSum($idStudent,$IdSemesterMain=NULL,$IdSemesterDetails=NULL) {

		$db = Zend_Db_Table::getDefaultAdapter();
		$TotalHrs2 = $TotalHrs1 = 0;
		$sql1 = $db->select()
		->from(array('a' => 'tbl_studentregsubjects'),array('a.IdSubject'))
		->joinLeft(array("subj" => "tbl_subjectmaster"), "subj.IdSubject = a.IdSubject",array('subj.CreditHours'))
		->where('a.IdStudentRegistration = ?',$idStudent);
		if($IdSemesterMain!='') {
			$sql1->where("a.IdSemesterMain = ?",$IdSemesterMain);
		} else {
			$sql1->where("a.IdSemesterDetails = ?",$IdSemesterDetails);
		}
		$sql1->where('a.IdGrade = ?','pass');
		$result1 = $db->fetchAll($sql1);
		if(count($result1)>0) {
			foreach($result1 as $res1)  {
				$TotalHrs1 = $TotalHrs1 + ( $res1['CreditHours']);
			}
		} else {  $TotalHrs1 = 0;
		}

		$sql2 = $db->select()
		->from(array('a' => 'tbl_studentregsubjects'),array('a.IdSubject'))
		->joinLeft(array("subj" => "tbl_subjectmaster"), "subj.IdSubject = a.IdSubject",array('subj.CreditHours'))
		->where('a.IdStudentRegistration = ?',$idStudent);
		if($IdSemesterMain!='') {
			$sql2->where("a.IdSemesterMain = ?",$IdSemesterMain);
		} else {
			$sql2->where("a.IdSemesterDetails = ?",$IdSemesterDetails);
		}
		$sql2->where('a.IdGrade = ?','fail');
		$result2 = $db->fetchAll($sql2);
		if(count($result2)>0) {
			foreach($result2 as $res2)  {
				$TotalHrs2 = $TotalHrs2 + ( $res2['CreditHours']);
			}
		} else {  $TotalHrs2 = 0;
		}

		$result = $TotalHrs1+$TotalHrs2;
		return $result;

	}




	public function fnGetStudentsCGPAofrExamResit( $SemesterCode, $idstudent, $IdProgram, $userId ){

		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("studregsubj" => "tbl_studentregsubjects"),array('studregsubj.IdStudentRegSubjects','studregsubj.IdStudentRegistration','studregsubj.IdSubject','studregsubj.IdSemesterMain','studregsubj.IdSemesterDetails','studregsubj.GradePoint'))
		->join(array("studreg" =>"tbl_studentregistration"),'studreg.IdStudentRegistration = studregsubj.IdStudentRegistration',array('studreg.registrationId','studreg.IdProgram'))
		->joinLeft(array("prg" =>"tbl_program"),'prg.IdProgram = studreg.IdProgram',array(''))
		;

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



		$select->where("studreg.IdStudentRegistration = ?",$idstudent);
		$select->where("studreg.IdProgram = ?",$IdProgram);


		$where_grade_cond = " ( studregsubj.IdGrade IS NULL OR studregsubj.IdGrade='pass' OR  studregsubj.IdGrade='fail' )";
		$select->where($where_grade_cond)->order('studregsubj.IdStudentRegSubjects DESC');
		$result = $lobjDbAdpt->fetchAll($select);

		//asd($result);


		if(count($result)>0) {
			foreach($result as $values) {
				$idStudent = $values['IdStudentRegistration'];
				$IdSemesterMain = $values['IdSemesterMain'];
				$IdSemesterDetails = $values['IdSemesterDetails'];
				$IdProgram = $values['IdProgram'];

				$registrationId = $values['registrationId'];

				$where_del  =   "IdStudentRegistration = '".$idStudent."' AND IdSemester = '".$SemesterCode."' ";
				$lobjDbAdpt->delete('tbl_gpacalculation',$where_del);


				$totalCreditHrsSubj     =   $this->getSubGradePtCreditHrsSum($idStudent,$IdSemesterMain,$IdSemesterDetails);
				$totalCreditHrsSem      =   $this->getSemesterCreditHrsSum($idStudent,$IdSemesterMain,$IdSemesterDetails);
				$totalCreditHrsTaken    =   $this->getTakenCreditHrsSum($idStudent,$IdSemesterMain,$IdSemesterDetails);

				//$totalGPA    =   ($totalCreditHrsSubj/$totalCreditHrsSem);
				//$totalCGPA    =   ($totalCreditHrsSubj/$totalCreditHrsTaken);

				if($totalCreditHrsSubj>0 && $totalCreditHrsSubj!='') {
					$totalGPA    =   ($totalCreditHrsSubj/$totalCreditHrsSem);
					$totalCGPA    =   ($totalCreditHrsSubj/$totalCreditHrsTaken);
				} else {
					$totalGPA    =   '0';
					$totalCGPA    =   '0';
				}


				$paramData = array(     'IdStudentRegistration' => $idStudent,
						'registrationId' => $registrationId,
						'IdSemester' => $SemesterCode,
						'IdProgram' => $IdProgram,
						'Gpa' => $totalGPA,
						'Cgpa' => $totalCGPA,
						'UpdUser' => $userId,
						'UpdDate' => date('Y-m-d H:i:s'),
						'IdApplication' => 0,
				);
				$lobjDbAdpt->insert('tbl_gpacalculation',$paramData);

			}
			$errMsg = '1';
		} else  {  $errMsg = '0';
		}


	}



	// Update cgpa and gpa for exam scaling
	public function fnGetStudentsCGPAofrExamResitScaling( $SemesterCode, $idstudent, $IdProgram, $userId ){

		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array("studregsubj" => "tbl_studentregsubjects"),array('studregsubj.IdStudentRegSubjects','studregsubj.IdStudentRegistration','studregsubj.IdSubject','studregsubj.IdSemesterMain','studregsubj.IdSemesterDetails','studregsubj.GradePoint'))
		->join(array("studreg" =>"tbl_studentregistration"),'studreg.IdStudentRegistration = studregsubj.IdStudentRegistration',array('studreg.registrationId','studreg.IdProgram'))
		->joinLeft(array("prg" =>"tbl_program"),'prg.IdProgram = studreg.IdProgram',array(''))
		;

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



		$select->where("studreg.IdStudentRegistration = ?",$idstudent);
		$select->where("studreg.IdProgram = ?",$IdProgram);


		$where_grade_cond = " ( studregsubj.IdGrade IS NULL OR studregsubj.IdGrade='pass' OR  studregsubj.IdGrade='fail' )";
		$select->where($where_grade_cond)->order('studregsubj.IdStudentRegSubjects DESC');
		$result = $lobjDbAdpt->fetchAll($select);

		//asd($result);


		if(count($result)>0) {
			foreach($result as $values) {
				$idStudent = $values['IdStudentRegistration'];
				$IdSemesterMain = $values['IdSemesterMain'];
				$IdSemesterDetails = $values['IdSemesterDetails'];
				$IdProgram = $values['IdProgram'];

				$registrationId = $values['registrationId'];

				$where_del  =   "IdStudentRegistration = '".$idStudent."' AND IdSemester = '".$SemesterCode."' ";
				//$lobjDbAdpt->delete('tbl_gpacalculation',$where_del);


				$totalCreditHrsSubj     =   $this->getSubGradePtCreditHrsSum($idStudent,$IdSemesterMain,$IdSemesterDetails);
				$totalCreditHrsSem      =   $this->getSemesterCreditHrsSum($idStudent,$IdSemesterMain,$IdSemesterDetails);
				$totalCreditHrsTaken    =   $this->getTakenCreditHrsSum($idStudent,$IdSemesterMain,$IdSemesterDetails);

				$totalGPA    =   ($totalCreditHrsSubj/$totalCreditHrsSem);
				$totalCGPA    =   ($totalCreditHrsSubj/$totalCreditHrsTaken);

				$paramData = array(     //'IdStudentRegistration' => $idStudent,
						//'registrationId' => $registrationId,
						//'IdSemester' => $SemesterCode,
						//'IdProgram' => $IdProgram,
						'GpaScaling' => $totalGPA,
						'CgpaScaling' => $totalCGPA,
						'UpdUser' => $userId,
						'UpdDate' => date('Y-m-d H:i:s'),
						// 'IdApplication' => 0,
				);
				$lobjDbAdpt->update('tbl_gpacalculation',$paramData, $where_del);

			}
			$errMsg = '1';
		} else  {  $errMsg = '0';
		}


	}




}