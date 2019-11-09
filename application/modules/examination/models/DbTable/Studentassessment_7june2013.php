<?php
class Examination_Model_DbTable_Studentassessment extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = '';
	private $lobjacademicstatus;
	private $lobjsemesterModel;
	private $lobjmarksdistruteModel;

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$this->lobjsemesterModel = new GeneralSetup_Model_DbTable_Semester();
		$this->lobjacademicstatus = new Examination_Model_DbTable_Academicstatus();
		$this->lobjmarksdistruteModel = new Examination_Model_DbTable_Marksdistributionmaster();
	}
	
	
	public function fngetStudentassessmentdet($studentId){
		$lstrSelect = $this->lobjDbAdpt->select()
						->from(array('a' => 'tbl_studentsemesterstatus'), array('a.*'))
						->joinLeft(array('b' => 'tbl_studentregistration'), 'a.IdStudentRegistration = b.IdStudentRegistration',array('b.*'))
						->joinLeft(array('c' => 'tbl_program_scheme'), 'b.IdProgram = c.IdProgram',array('c.IdScheme'))
						->where("a.IdStudentRegistration = ?",$studentId);
		
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);		
		$finalstudentresult = array();
		foreach($larrResult as $rec){			
			$temp = $rec;
			$semcode = '';
			if($rec['idSemester'] != ''){
				$semId = $rec['idSemester'];
				$larrsem = $this->lobjsemesterModel->getsemDetail($rec['idSemester']);
				if(!empty($larrsem)){
					$semcode = $larrsem[0]['SemesterCode'];
				}
			}else{
				$larrsemmain = $this->lobjsemesterModel->getsemMainDet($rec['IdSemesterMain']);				
				if(!empty($larrsemmain)){
					$semcode = $larrsemmain[0]['SemesterMainCode'];
				}
			}
			$ret = $this->fngetStudentgpa($rec['IdStudentRegistration'],$semcode);
			$rec['semester'] = $semcode;
			$rec['cgpagpadet'] = $ret;
			//$academicstatus = fngetademicStatus
			$gpadet = array();
			$gpadet = $this->fngetgpadet($semcode,$rec['IdScheme'],$rec['IdProgram']);			
			if(!empty($gpadet)){
				foreach($gpadet as $dat){
					if(!empty($rec['cgpagpadet'][0])){
						if(($dat['Minimum'] <= $rec['cgpagpadet'][0]['Gpa']) && ($rec['cgpagpadet'][0]['Gpa'] <= $dat['Maximum'])  ){
							$rec['gpaGradepoint'] = $dat['Gradepoint'];
							$rec['gpaGradevalue'] = $dat['Gradevalue'];
						}
						
						if(($dat['Minimum'] <= $rec['cgpagpadet'][0]['Cgpa']) && ($rec['cgpagpadet'][0]['Cgpa'] <= $dat['Maximum'])  ){
							$rec['cgpaGradepoint'] = $dat['Gradepoint'];
							$rec['cgpaGradevalue'] = $dat['Gradevalue'];
						}
					}
				}
			}
			
			
			$finalstudentresult[] = $rec;			
		}
		return $finalstudentresult;
	}
	
	public function fngetgpadet($semcode,$scheme,$program){
		$larrgpadet = $this->lobjacademicstatus->fngetademic($semcode,$scheme,$program);
		return $larrgpadet;
	}
	
	
	public function fngetStudentgpa($Idstudent,$Idsem){
		$lstrSelect = $this->lobjDbAdpt->select()
						->from(array('a' => 'tbl_gpacalculation'), array('a.*'))
						->where("a.IdStudentRegistration = ?",$Idstudent)
						->where("a.IdSemester = ?",$Idsem);
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function fngetStudentacademic($studentId,$semcode){
		$lstrSelect = $this->lobjDbAdpt->select()
						->from(array('a' => 'tbl_studentsemesterstatus'), array('a.*'))
						->joinLeft(array('b' => 'tbl_studentregistration'), 'a.IdStudentRegistration = b.IdStudentRegistration',array('b.*'))
						->joinLeft(array('c' => 'tbl_program_scheme'), 'b.IdProgram = c.IdProgram',array('c.IdScheme'))
						->where("a.IdStudentRegistration = ?",$studentId);
		// Now get the semester Id from semestercode
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);		
		$finalstudentresult = array();
		foreach($larrResult as $rec){
			$temp = $rec;
			$ret = $this->fngetStudentgpa($rec['IdStudentRegistration'],$semcode);
			$rec['semester'] = $semcode;
			$rec['cgpagpadet'] = $ret;
			//$academicstatus = fngetademicStatus
			$gpadet = $this->fngetgpadet($semcode,$rec['IdScheme'],$rec['IdProgram']);
			foreach($gpadet as $dat){
				if(($dat['Minimum'] <= $rec['cgpagpadet'][0]['Gpa']) && ($rec['cgpagpadet'][0]['Gpa'] <= $dat['Maximum'])  ){
					$rec['gpaGradepoint'] = $dat['Gradepoint'];
					$rec['gpaGradevalue'] = $dat['Gradevalue'];
				}
				
				if(($dat['Minimum'] <= $rec['cgpagpadet'][0]['Cgpa']) && ($rec['cgpagpadet'][0]['Cgpa'] <= $dat['Maximum'])  ){
					$rec['cgpaGradepoint'] = $dat['Gradepoint'];
					$rec['cgpaGradevalue'] = $dat['Gradevalue'];
				}
			}
			
			
			$finalstudentresult[] = $rec;
		}
		return $finalstudentresult;
	}
	
	public function fngetstudentSubjectMarks($IdStudent,$Idsemdet,$Idsemmain){
		$larrResult = array();
		$semcode = ''; 
		if($Idsemdet!=''){
			$larrsem = $this->lobjsemesterModel->getsemDetail($Idsemdet);
			if(!empty($larrsem)){
				$semcode = $larrsem[0]['SemesterCode'];
			}
		}else if($Idsemmain!=''){
			$larrsemmain = $this->lobjsemesterModel->getsemMainDet($Idsemmain);				
			if(!empty($larrsemmain)){
				$semcode = $larrsemmain[0]['SemesterMainCode'];
			}
		}
		if($semcode!=''){
			$lstrSelect = $this->lobjDbAdpt->select()
						->from(array('a' => 'tbl_student_marks_entry'), array('a.*'))
						->joinLeft(array('b' => 'tbl_subjectmaster'), 'a.Course = b.IdSubject',array('b.SubjectName as coursename'))
						->where("a.IdStudentRegistration = ?",$IdStudent)
						->where("a.IdSemester =?",$semcode);
			$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);	
		}
		return $larrResult;
	}
	
	
	// Function to get the students registered subject list
	public function fetchStudentRegSubList($IdStudent,$Idsemdet,$Idsemmain){
		$lstrSelect = $this->lobjDbAdpt->select()
					->from(array('a' => 'tbl_studentregsubjects'), array('a.*'))
					->joinLeft(array('b' => 'tbl_subjectmaster'), 'a.IdSubject = b.IdSubject',array('b.SubjectName as coursename','b.SubCode','b.CreditHours'));
		$lstrSelect->where("a.IdStudentRegistration = ?",$IdStudent);
		if($Idsemdet!=''){
			$lstrSelect->where("a.IdSemesterDetail =?",$Idsemdet);
		}else if($Idsemmain!=''){				
			$lstrSelect->where("a.IdSemesterMain =?",$Idsemmain);
		}		
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);	
				
		return $larrResult;
	}
	
	
	// Function to get the student gpa for the students course
	public function fncalculatestudentcourseGpa($data){
		//echo '<pre>';
		//print_r($data);
		
		$semcode = ''; 
		if($data['IdSemesterDetails']!=''){
			$larrsem = $this->lobjsemesterModel->getsemDetail($data['IdSemesterDetails']);
			if(!empty($larrsem)){
				$semcode = $larrsem[0]['SemesterCode'];
			}
		}else if($data['IdSemesterMain']!=''){
			$larrsemmain = $this->lobjsemesterModel->getsemMainDet($data['IdSemesterMain']);				
			if(!empty($larrsemmain)){
				$semcode = $larrsemmain[0]['SemesterMainCode'];
			}
		}
		
		
		
		/*
		$larrgpadet = $this->lobjacademicstatus->fngetademic($semcode,$data['IdScheme'],$data['IdProgram']);
		echo '<pre>';
		print_r($larrgpadet);*/
		
		
		// Now first get the marks distribution for the course
		$stmarksdist = $this->lobjmarksdistruteModel->fngetmarksdistr($semcode,$data['IdProgram'],$data['IdSubject']);
		//echo '<pre>Marks distribution';
		//print_r($stmarksdist);
		$marks = 0;
		$stdcoursewithmarks = array();
		$attendencestatus = '';
		foreach($stmarksdist as $ret){
			 $studentmarks = $this->fngetstudentCoursemarks($data['IdStudentRegistration'],$data['IdStudentRegSubjects'],$ret['IdComponentItem'],$ret['IdCourse'],$ret['semester']);
			 if(!empty($studentmarks)){
			 	//echo floatval($studentmarks['TotalMarkObtained']);
			 	$avmark = (floatval($studentmarks['TotalMarkObtained'])) / (floatval($studentmarks['MarksTotal']));
			 	$marks = $marks + ($avmark * $ret['Percentage']);
			 }
			 $ret['TotalMarkObtained'] = $studentmarks['TotalMarkObtained'];
			 $ret['MarksTotal'] = $studentmarks['MarksTotal'];
			 $ret['getmarks'] = $marks;
			 $ret['AttendanceStatus'] = $studentmarks['AttendanceStatus'];
			 $stdcoursewithmarks[] = $ret;
		}
		return $marks;
	}
	
	public function fngetstudentCoursemarks($Idstudent,$idstudentregsubject,$componentitem,$course,$semcode){
		$lstrSelect = $this->lobjDbAdpt->select()
						->from(array('a' => 'tbl_student_marks_entry'), array('a.MarksTotal','a.TotalMarkObtained','a.AttendanceStatus'))									
						->where("a.IdStudentRegistration = ?",$Idstudent)
						->where("a.IdStudentRegSubjects =?",$idstudentregsubject)
						->where("a.ComponentItem =?",$componentitem)	
						->where("a.Course =?",$course)	
						->where("a.IdSemester =?",$semcode);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);	
		return $larrResult;
	}
	
	
	public function fncheckstudentcoursemarks($semcode,$Idstudent,$course){
		$flag = true;
		$lstrSelect = $this->lobjDbAdpt->select()
						->from(array('a' => 'tbl_student_marks_entry'), array('a.MarksTotal','a.TotalMarkObtained','a.AttendanceStatus'))									
						->where("a.IdStudentRegistration = ?",$Idstudent)	
						->where("a.Course =?",$course)	
						->where("a.IdSemester =?",$semcode);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);	
		
		if(empty($larrResult)){
			$flag = false;
		}
		return $flag;
	}
	
	
	public function fngetsetudentcoursemarks($Idstudent,$idstudentregsubject){
		$lstrSelect = $this->lobjDbAdpt->select()
						->from(array('a' => 'tbl_studentregsubjects'), array('a.IdGrade','a.Totalmarks','a.GradePoint'))									
						->where("a.IdStudentRegistration = ?",$Idstudent)
						->where("a.IdStudentRegSubjects =?",$idstudentregsubject);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);	
		return $larrResult;
	}
	
}