<?php
class Cms_CodeGeneration {

	private $lobjstudentgenerated;
	private $lobjprogrammodel;
	private $lobjintakemodel;
	private $lobjawardlevelmodel;

	public function __construct() {
		$this->lobjstudentgenerated = new App_Model_StudentGeneratedId();
		$this->lobjprogrammodel = new GeneralSetup_Model_DbTable_Program();
		$this->lobjintakemodel = new GeneralSetup_Model_DbTable_Intake();
		$this->lobjawardlevelmodel = new GeneralSetup_Model_DbTable_Awardlevel();
	}

	public function ApplicantCodeGenration($iduniversity,$configArray){
		$applicantmodelobj = new App_Model_Applicant();

		$applicantList = $applicantmodelobj->fngetapplicantList();
		$studentList = $applicantmodelobj->fngetstudentList();
		$seqno = '';
		if(empty($applicantList)){
			$seqno = '001';
		}else{
			$lengthofapplicantList = count($applicantList);
			$lengthofstudentList = count($studentList);
			$totallength = $lengthofapplicantList + $lengthofstudentList;
			$seqno = '00'.(string)($totallength + 1);
		}
		$Format = $configArray['ApplicantIdFormat'];
		$Prefix = $configArray['ApplicantPrefix'];
		$codearray = explode('-',$Format);
		$newcodeArray = array();
		$seqpos = 0;
		foreach($codearray as $key){
			switch ($key) {
				case 'px':
					$newcodeArray[] = $Prefix;
					break;

				case 'yy':
					$newcodeArray[] = date('y');
					break;

				case 'yyyy':
					$newcodeArray[] = date('Y');;
					break;

				case 'YY':
					$newcodeArray[] = date('y');
					break;

				case 'YYYY':
					$newcodeArray[] = date('Y');;
					break;

				case 'seqno':
					$newcodeArray[] = $seqno;
					$seqpos = count($newcodeArray);
					break;
			}
		}
		$newCode = '';
		$defaultcode = '';
		$i = 0;

		$newcodeArray = $this->uniquecodegeneration($newcodeArray,'tbl_applicant','ApplicantCode',$seqpos);
		$newcodeArray = $this->uniquecodegeneration($newcodeArray,'tbl_studentapplication','IdApplicant',$seqpos);

		foreach($newcodeArray as $val){
			if($i!=0){
				$defaultcode .= "_".$val;
			}
			else if($i==0){
				$defaultcode .= $val;
			}
			$newCode .= $val;
			$i++;
		}
		$codearray = array('ApplicantCode'=>$newCode,'ApplicantCodeFormat'=>$defaultcode,'IdFormat'=>$Format);

		return $codearray;
	}

	public function StaffIdGenration($iduniversity,$configArray){
		$codePattern = $configArray[0]['StaffIdFormat'];
		$tempcode = '';
		$prefix = $configArray[0]['StaffPrefix'];
		$keyArray = array('px', 'yy','yyyy','seqno','b');
		$searchArray = array();
		$replaceArray = array();
		foreach($keyArray as $key){
			if(stristr($codePattern,$key)){
				$searchArray[] = $key;
				switch ($key) {
					case 'px':
						$replaceArray[] = $prefix;
						break;

					case 'yy':
						$replaceArray[] = date('y');
						break;

					case 'yyyy':
						$replaceArray[] = date('yy');;
						break;

					case 'seqno':
						$replaceArray[] = '001';
						break;
					case 'b' :
						$replaceArray[] = 'branch';
						break;
				}
			}
		}
		return $generatedcode = str_replace($searchArray, $replaceArray, $codePattern);
	}

	public function RegistrationIdGenration($iduniversity,$configArray,$studentdetails=NULL){

		$seqno = "";
		$programID =  $intakeID = $branchID = $awardID = $genderID = '';
		$studentregmodelobj = new Registration_Model_DbTable_Studentregistration();
		$programmodelobj = new GeneralSetup_Model_DbTable_Program();

		$studentList = $studentregmodelobj->fnfetchAllStudents();
		$lstrresetconfig =  $configArray['ResetRegistrationSeq'];

		if(!empty($studentdetails)) {
	  $programID = $studentdetails['ProgramOfferred'];
	   
	  if($programID!='') {
	  	$awardDetails = $programmodelobj->fnfetchProgramAward($programID);
	  	$awardID = $awardDetails[0]['Award'];
	  }
	   
	  $intakeID =  $studentdetails['intake'];
	  $branchID =  $studentdetails['BranchOfferred'];
	  $genderID =  $studentdetails['Gender'];
	  if($genderID=='1') { $genderData = 'F';  } else {  $genderData = 'M'; }
		}
		switch(trim($lstrresetconfig)) {
			case "":
				$seqno = ($studentList + 1);
				break;
			case "program":
				$seqno = $this->lobjstudentgenerated->fngetseqno("program",$programID,"");
				break;
			case "intake":
				$seqno = $this->lobjstudentgenerated->fngetseqno("intake","",$intakeID);
				break;
			case "progintake":
				$seqno = $this->lobjstudentgenerated->fngetseqno("progintake",$programID,$intakeID);
				break;
			case "year":
				$seqno = $this->lobjstudentgenerated->fngetseqno("year");
				break;
		}
                //temporary arrangment for code generation
                $seqno = $this->fnformatseqno($seqno);
                
                $lobjresult = $this->lobjintakemodel->fnfetchIntakeCode($intakeID);
                if(isset($lobjresult[0]['IntakeId'])) {
                    $lstrtempintake = $lobjresult[0]['IntakeId'];
                }
                $lobjresult = $this->lobjprogrammodel->fnfetchProgramShortName($programID);
                if(isset($lobjresult[0]['ShortName'])) {
                    $lstrtempprogramcode = $lobjresult[0]['ShortName'];
                }
                $lstrtempcode = $lstrtempprogramcode.$seqno."_".$lstrtempintake;
                $lboolflag = true;
                //Temp checking for uniquiecode
                while($lboolflag) {
                    $lbooltemp = $this->checkUniqueGeneratedCode($lstrtempcode, 'tbl_studentregistration','registrationId');
                    if(!$lbooltemp) {
                        $seqno = intval($seqno) + 1;
                        $seqno = $this->fnformatseqno($seqno);
                        $lstrtempcode = $lstrtempprogramcode.$seqno."_".$lstrtempintake;
                    }
                    else {
                        $lboolflag = false;
                    }
                }

		$Format = $configArray['RegistrationIdFormat'];
		$prefix = $configArray['RegistrationPrefix'];
		$codearray = explode('-',$Format);
		$newcodeArray = array();
		$seqpos = 0;
		foreach($codearray as $key){
			switch ($key) {
				case 'px':
					$newcodeArray['px'] = $prefix;
					break;

				case 'yyyy':
					$newcodeArray['yyyy'] = date('Y');
					break;

				case 'yy':
					$newcodeArray['yy'] = date('y');
					break;

				case 'seqno':
					$newcodeArray['seqno'] = $seqno;
					$seqpos = count($newcodeArray);
					break;

				case 'iid' :
					$lobjresult = $this->lobjintakemodel->fnfetchIntakeCode($intakeID);
					$newcodeArray['iid'] = $lobjresult[0]['IntakeId'];
					break;

				case 'b' :
					$newcodeArray['b'] = $branchID;
					break;

				case 'p' :
					$lobjresult = $this->lobjprogrammodel->fnfetchProgramShortName($programID);
					$newcodeArray['p'] = $lobjresult[0]['ShortName'];
					break;

				case 'a' :
					$lobjresult = $this->lobjawardlevelmodel->fnfetchAwardCode($awardID);
					$newcodeArray['a'] = $lobjresult[0]['DefinitionCode'];
					break;

				case 'g' :
					$newcodeArray['g'] = $genderData;
					break;
			}
		}
		$newcodeArray = $this->uniquecodegeneration($newcodeArray,'tbl_studentregistration','registrationId',$seqpos);
		 
		$newCode = '';
		$defaultcode = '';
		$i=0;
		foreach($newcodeArray as $val){
			if($i!=0){
				$defaultcode .= "-".$val;
			}
			else if($i==0){
				$defaultcode .= $val;
			}
			$newCode .= $val;
			$i++;
		}
		$codearray = array('IdStudentRegistrationFormatted'=>$lstrtempcode,'registrationId'=>$newCode,'IdFormat'=>$Format,'larrsepvalues' => $newcodeArray);
                //$codearray = array('IdStudentRegistrationFormatted'=>$defaultcode,'registrationId'=>$newCode,'IdFormat'=>$Format,'larrsepvalues' => $newcodeArray);
		return $codearray;

	}

	public function uniquecodegeneration($newcodeArray,$table,$column,$seqpos){
		//return $newcodeArray;

		$newCode = "";
		foreach($newcodeArray as $key => $value){
			$newCode .= $value;
		}

		if($this->checkUniqueGeneratedCode($newCode,$table,$column)){
			return $newcodeArray;
		}else{
			$newseq = str_replace('00', '', $newcodeArray[$seqpos-1]) + 1;
			$newcodeArray[$seqpos-1] = '00'.$newseq;
			return $this->uniquecodegeneration($newcodeArray,$table,$column,$seqpos);
		}
	}


	public function checkUniqueGeneratedCode($code,$table,$coloumn){
		$studentregmodelobj = new Registration_Model_DbTable_Studentregistration();
		$ret = $studentregmodelobj->checkCodeExist($code,$table,$coloumn);
		if(empty($ret)){
			return true;
		}else{
			return false;
		}
	}

	public function StudentIdGenration($iduniversity,$configArray){
		$codePattern = $configArray[0]['StudentIdFormat'];
		$tempcode = '';
		$prefix = $configArray[0]['StudentIdPrefix'];
		$keyArray = array('px', 'yy','yyyy','seqno','b','iid','p','a','g');
		$searchArray = array();
		$replaceArray = array();
		foreach($keyArray as $key){
			if(stristr($codePattern,$key)){
				$searchArray[] = $key;
				switch ($key) {
					case 'px':
						$replaceArray[] = $prefix;
						break;

					case 'yy':
						$replaceArray[] = date('y');
						break;

					case 'yyyy':
						$replaceArray[] = date('yy');;
						break;

					case 'seqno':
						$replaceArray[] = '001';
						break;
					case 'iid' :
						$replaceArray[] = 'branch';
						break;

					case 'p' :
						$replaceArray[] = 'Programs';
						break;

					case 'a' :
						$replaceArray[] = 'Award';
						break;

					case 'g' :
						$replaceArray[] = 'F';
						break;
				}
			}
		}
		return $generatedcode = str_replace($searchArray, $replaceArray, $codePattern);
	}

	public function AgentIdGenration($iduniversity,$configArray){
		$codePattern = $configArray[0]['AgentIdFormat'];
		$tempcode = '';
		$prefix = $configArray[0]['AgentPrefix'];
		$keyArray = array('px','seqno');
		$searchArray = array();
		$replaceArray = array();
		foreach($keyArray as $key){
			if(stristr($codePattern,$key)){
				$searchArray[] = $key;
				switch ($key) {
					case 'px':
						$replaceArray[] = $prefix;
						break;

					case 'seqno':
						$replaceArray[] = '001';
						break;
				}
			}
		}
		return $generatedcode = str_replace($searchArray, $replaceArray, $codePattern);
	}


	public function SubjectIdGenration($iduniversity,$configArray){
		$codePattern = $configArray[0]['StudentIdFormat'];
		$tempcode = '';
		$prefix = $configArray[0]['StudentIdPrefix'];
		$keyArray = array('ch', 'a','seqno','f','l');
		$searchArray = array();
		$replaceArray = array();
		foreach($keyArray as $key){
			if(stristr($codePattern,$key)){
				$searchArray[] = $key;
				switch ($key) {
					case 'ch':
						$replaceArray[] = 'CreditHrs';
						break;

					case 'a':
						$replaceArray[] = 'Award';
						break;

					case 'seqno':
						$replaceArray[] = '001';
						break;

					case 'f' :
						$replaceArray[] = 'faculty';
						break;

					case 'l' :
						$replaceArray[] = 'level';
						break;
				}
			}
		}
		return $generatedcode = str_replace($searchArray, $replaceArray, $codePattern);
	}

	public function CourseIdGenration($iduniversity,$configArray){
		$codePattern = $configArray[0]['CourseIdFormat'];
		$tempcode = '';
		$prefix = $configArray[0]['CoursePrefix'];
		$keyArray = array('px', 'yy','yyyy','seqno');
		$searchArray = array();
		$replaceArray = array();
		foreach($keyArray as $key){
			if(stristr($codePattern,$key)){
				$searchArray[] = $key;
				switch ($key) {
					case 'px':
						$replaceArray[] = $prefix;
						break;

					case 'yy':
						$replaceArray[] = date('y');
						break;

					case 'yyyy':
						$replaceArray[] = date('yy');;
						break;

					case 'seqno':
						$replaceArray[] = '001';
						break;
				}
			}
		}
		return $generatedcode = str_replace($searchArray, $replaceArray, $codePattern);
	}

	public function SubjectCodeGenration($iduniversity,$configArray){
		$subjectmodelobj = new Application_Model_DbTable_Subjectsetup();
		$subjectList = $subjectmodelobj->fngetsubjectList();
		$seqno = '';
		if(empty($subjectList)){
			$seqno = '001';
		}else{
			$lengthofsubjectList = count($subjectList);
			$seqno = '00'.(string)($lengthofsubjectList + 1);
		}
		$subjectCodeFormat = $configArray['CourseIdFormat'];
		$subPrefix = $configArray['CoursePrefix'];
		$subjectcodearray = explode('-',$subjectCodeFormat);
		$newcodeArray = array();
		foreach($subjectcodearray as $key){
			switch ($key) {
				case 'px':
					$newcodeArray[] = $subPrefix;
					break;

				case 'yy':
					$newcodeArray[] = date('y');
					break;

				case 'yyyy':
					$newcodeArray[] = date('Y');;
					break;

				case 'YY':
					$newcodeArray[] = date('y');
					break;

				case 'YYYY':
					$newcodeArray[] = date('Y');;
					break;

				case 'seqno':
					$newcodeArray[] = $seqno;
					break;
			}
		}

		$newCode = '';
		$defaultcode = '';
		$i = 0;
		foreach($newcodeArray as $val){
			if($i!=0){
				$defaultcode .= "_".$val;
			}
			else if($i==0){
				$defaultcode .= $val;
			}
			$newCode .= $val;
			$i++;
		}
		$codearray = array('SubjectCode'=>$newCode,'SubjectCodeFormat'=>$defaultcode,'IdFormat'=>$subjectCodeFormat);

		return $codearray;


	}


	/**
	 * Function to calculate Course Code
	 * @author: Vipul
	 */

	public function CourseCodeGenration($idUniversity,$configArray){
		$subjectmodelobj = new GeneralSetup_Model_DbTable_Subjectmaster();
		$subjectList = $subjectmodelobj->totalSubjects();
		//asd($subjectList);
		$seqno = '';
		if(empty($subjectList)){
			$seqno = '001';
		}else{
			$totallength = $subjectList;
			$seqno = '00'.(string)($totallength + 1);
		}


		$Format = $configArray['CourseIdFormat'];
		$prefix = $configArray['CoursePrefix'];
		$codearray = explode('-',$Format);
		$newcodeArray = array();
		foreach($codearray as $key){
			switch ($key) {
				case 'px':
					$newcodeArray[] = $prefix;
					break;

				case 'YYYY':
					$newcodeArray[] = date('Y');
					break;

				case 'YY':
					$newcodeArray[] = date('y');
					break;

				case 'seqno':
					$newcodeArray[] = $seqno;
					break;
			}
		}

		$newCode = '';
		$defaultcode = '';
		$i = 0;
		foreach($newcodeArray as $val){
			if($i!=0){
				$defaultcode .= "_".$val;
			}
			else if($i==0){
				$defaultcode .= $val;
			}
			$newCode .= $val;
			$i++;
		}
		$codearray = array('SubCode'=>$newCode,'SubCodeFormat'=>$defaultcode,'IdFormat'=>$Format);

		return $codearray;

	}

	public function InventoryCodeGenration($idUniversity,$configArray){
		$inventorymodelobj = new Hostel_Model_DbTable_Inventorysetup();
		$inventoryList = $inventorymodelobj->fntotalInventories();
		$seqno = '';
		if(empty($inventoryList)){
			$seqno = '001';
		}else{
			$totallength = $inventoryList;
			$seqno = '00'.(string)($totallength + 1);
		}


		$Format = $configArray['inventoryIdFormat'];
		$prefix = $configArray['inventoryPrefix'];
		$codearray = explode('-',$Format);
		$newcodeArray = array();
		foreach($codearray as $key){
			switch ($key) {
				case 'px':
					$newcodeArray[] = $prefix;
					break;


				case 'seqno':
					$newcodeArray[] = $seqno;
					break;
			}
		}

		$newCode = '';
		foreach($newcodeArray as $val){
			$newCode .= $val;
		}
		return $newCode;

	}
        
        private function fnformatseqno($seqno) {
            switch(strlen($seqno)) {
                case 1:
                    $seqno = '000'.$seqno;
                    break;
                case 2:
                    $seqno = '00'.$seqno;
                    break;
                case 3:
                    $seqno = '0'.$seqno;
                    break;
            }
            return $seqno;
        }




}
?>
