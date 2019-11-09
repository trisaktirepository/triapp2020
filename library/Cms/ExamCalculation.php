<?php
class Cms_ExamCalculation {
	
	public function calculateMark($ItemMarkObtained,$fullmark,$item_percentage){		
		return (abs($ItemMarkObtained)/abs($fullmark))*abs($item_percentage);
	}
	
	public function calculateFinalMark($total_mark_obtained,$percentage){
		return (abs($total_mark_obtained*$percentage)/100);
	}
	
	public function getGradeInfo($semester_id,$program_id,$subject_id,$mark_obtained,$idlandscape=null){		
		 $gradeDB = new Examination_Model_DbTable_Grade();
		 return $gradeDB->getGrade($semester_id,$program_id,$subject_id,$mark_obtained,$idlandscape);
	}
	
	public function calculateGPA($total_point,$total_credit_hour){
		if ($total_credit_hour>0) {
			$gpa = abs($total_point)/abs($total_credit_hour);
			return number_format($gpa, 2, '.', '');
		} else return 0;
	}
	
	public function calculateCGPA($total_point,$total_credit_hour){
		$gpa = abs($total_point)/abs($total_credit_hour);
		return number_format($gpa, 2, '.', '');
	}
	
	public function getAcademicStatus($semester_id,$idProgram,$type,$basedon,$cgpa){		
		 $academicDB = new Examination_Model_DbTable_Academicstatus();
		 return $academicDB->getAcademicStatus($semester_id,$idProgram,$type,$basedon,$cgpa);
	}
	
	public function getParentGrade($data){
		
		$landscapeBlockSubjectDb = new GeneralSetup_Model_DbTable_LandscapeBlockSubject(); 
		$subjectRegDB = new Examination_Model_DbTable_StudentRegistrationSubject();
		$gradeDB = new Examination_Model_DbTable_Grade();
		   
		//anak kene dapatkan sape bapak dia
    	$subject_block_info = $landscapeBlockSubjectDb->getLandscapeSubjectInfo($data['IdLandscape'],$data['IdSubject']);
    	if ($subject_block_info['parentId']!=null) {
    	//get parent info 
    	$parent = $subjectRegDB->getRegDataParent($data["IdStudentRegistration"],$subject_block_info["parentId"]);
    	    						
		//cari anak-anak yg lain
		$children = $landscapeBlockSubjectDb->getChildByParentId($subject_block_info["parentId"]);
    	
		//nak cek kalo ada highest mark adik beradik yg IN,FR,NR,MG automatik bapak kene update status yang sama
		$child_return = $this->multi_array_search($children,$data["IdStudentRegistration"],$parent);								
		
		if(is_array($child_return)){
			
				$total_credit_hour = $parent['CreditHours'];
		    	$child_mark =0 ;
				$total_child_mark = 0;
				$parent_exam_status ='C';
				$total_child_point=0;
				$status="0";		
				foreach($child_return as $child_highest_mark){																			
					
						$child_mark = $child_highest_mark["final_course_mark"] * $child_highest_mark["CreditHours"];
						$child_point=$child_highest_mark["grade_point"] * $child_highest_mark["CreditHours"];
						$total_child_mark = abs($total_child_mark) + abs($child_mark);
						$total_child_point = abs($total_child_point) + abs($child_point);
						
						//jika salah satu anak MG atau NR set parent exam_status MG atau NR ikut anak
						if($child_highest_mark["exam_status"] !='C'){
							 $parent_exam_status=$child_highest_mark["exam_status"];
						}
						if ($child_highest_mark["final_course_mark"]==0 || $child_highest_mark["final_course_mark"]=='') $status='1';
						/*echo '<pre>';
						echo $child_highest_mark["final_course_mark"].' * '.$child_highest_mark["CreditHours"].'= '.$child_mark;
						echo '<br>';*/
								
				}//end foreach
							
				$parent_final_course_mark = $total_child_mark/$total_credit_hour;
				$parent_final_course_point = $total_child_point/$total_credit_hour;
				//mappkan parent mark dgn grade				
			    if ($status=='0') $parent_grade = $gradeDB->getGrade($data['IdSemester'],$data["IdProgram"],$data['IdSubject'],$parent_final_course_mark,$data['IdLandscape']);			
				if ($status=='1') $parent_grade = $gradeDB->getGradeByGradePoint($data['IdSemester'],$data["IdProgram"],$data['IdSubject'],$parent_final_course_point,$data['IdLandscape']);
			    //update parent grade
			    $parent_grade_info["final_course_mark"]= $parent_final_course_mark;
			    $parent_grade_info["grade_point"]= $parent_grade["GradePoint"];
			    $parent_grade_info["grade_name"]= $parent_grade["GradeName"];
			    $parent_grade_info["grade_desc"]= $parent_grade["GradeDesc"];
			    $parent_grade_info["exam_status"]= $parent_exam_status;
			    
			    if($parent_grade["Pass"]==1){
			    	$parent_grade_info["grade_status"]= 'Pass';
			    }else if($parent_grade["Pass"]==0){
			    	$parent_grade_info["grade_status"]= 'Fail';
			    }
			    			  			    
			    //update parent grade info
			    $subjectRegDB->updateData($parent_grade_info,$parent["IdStudentRegSubjects"]);
		}			
		}	
									
	}//end function
	
	public function multi_array_search($children,$IdStudentRegistration,$parent){
		
		$subjectRegDB = new Examination_Model_DbTable_StudentRegistrationSubject();
		 
		$child_array = array();
		
		foreach($children as $child){
			
			//amik the highest mark
			//$child_mark  = $courseRegisterDb->getHighestMark($IdStudentRegistration,$child["subjectid"],$semester);			
			$child_highest_mark  = $subjectRegDB->getHighestMarkofAllSemester($IdStudentRegistration,$child["subjectid"]);			
			
			if ($child_highest_mark["exam_status"] =='C' && $child_highest_mark["grade_status"]=="Pass") {	
				array_push($child_array, $child_highest_mark);
			}else{			
				//if($child_highest_mark["exam_status"]=='IN' || $child_highest_mark["exam_status"]=='FR'   || $child_highest_mark["exam_status"]=='MG' ||  $child_highest_mark["exam_status"]=='DR'){
				//update parent grade
				$parent_grade_info["final_course_mark"]= '';
				$parent_grade_info["grade_point"]= '';
				$parent_grade_info["grade_name"]= null;
				// $parent_grade_info["grade_name"]= $child_highest_mark["exam_status"];
				$parent_grade_info["grade_desc"]= '';
				$parent_grade_info["grade_status"]= '';
				$parent_grade_info["exam_status"]= $child_highest_mark["exam_status"];
				
				//update parent grade info
				$subjectRegDB->updateData($parent_grade_info,$parent["IdStudentRegSubjects"]);
				return false;
				
			}
		}
		return $child_array;
	}
}

?>