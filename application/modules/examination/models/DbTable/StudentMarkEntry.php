<?php
class Examination_Model_DbTable_StudentMarkEntry extends Zend_Db_Table { //Model Class for Users Details
	
	protected $_name = 'tbl_student_marks_entry';
	protected $_primary = 'IdStudentMarksEntry';

	public function addData($data){		
	   
		$id = $this->insert($data);
		//echo var_dump($data);exit;
	   return $id;
	}
	
    public function deleteData($id){		
	  $this->delete("IdStudentMarksEntry = '".(int)$id."'");
	}
	
	public function updateData($data,$id){
		
		 $this->update($data, "IdStudentMarksEntry = '".(int)$id."'");
	}
	
	
	public function checkMarkEntry($IdStudentRegistration,$IdMarksDistributionMaster,$IdStudentRegSubjects,$idSemester){
		
		 $db = Zend_Db_Table::getDefaultAdapter();
		
		 //check ada x mark entry sebelum ni
	 	 $select_mark = $db->select()
	 	 				  ->from(array('sme'=>'tbl_student_marks_entry'))
	 	 				  ->where('sme.IdStudentRegistration = ?',$IdStudentRegistration)
	 	 				  ->where('sme.IdMarksDistributionMaster = ?',$IdMarksDistributionMaster)
	 	 				  ->where('sme.IdStudentRegSubjects = ?',$IdStudentRegSubjects)
	 	 				  ->where('sme.IdSemester = ?',$idSemester);
	 	 $entry_list= $db->fetchRow($select_mark);
	 	// echo 'ekke';exit;
	     return $entry_list;
	}
	
	public function getMark($IdStudentRegistration,$IdMarksDistributionMaster,$IdStudentRegSubjects,$idSemester,$header=null,$idsubject=null){
		
		 $db = Zend_Db_Table::getDefaultAdapter();
			
		 //check ada x mark entry sebelum ni
	 	 $select_mark = $db->select()
	 	 				  ->from(array('sme'=>'tbl_student_marks_entry'))
	 	 				  ->joinLeft(array('s'=>'tbl_staffmaster'),'s.IdStaff=sme.ApprovedBy',array('ApprovedBy'=>'FullName'))
	 	 				  ->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition=sme.MarksEntryStatus',array('BahasaIndonesia','DefinitionDesc'))
	 	 				  ->where('sme.IdStudentRegistration = ?',$IdStudentRegistration)
	 	 				  ->where('sme.IdMarksDistributionMaster = ?',$IdMarksDistributionMaster)
	 	 				  ->where('sme.IdStudentRegSubjects = ?',$IdStudentRegSubjects)
	 	 				  ->where('sme.IdSemester = ?',$idSemester);
	 	 $row=$db->fetchRow($select_mark);
	 	 if (!$row) {
	 	 	$select_mark = $db->select()
	 	 	->from(array('sme'=>'tbl_student_marks_entry'))
	 	 	->joinLeft(array('s'=>'tbl_staffmaster'),'s.IdStaff=sme.ApprovedBy',array('ApprovedBy'=>'FullName'))
	 	 	->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition=sme.MarksEntryStatus',array('BahasaIndonesia','DefinitionDesc'))
	 	 	->where('sme.IdStudentRegistration = ?',$IdStudentRegistration)
	 	 	->where('sme.IdMarksDistributionMaster = ?',$IdMarksDistributionMaster)
	 	 	->where('sme.IdSemester = ?',$idSemester);
	 	 	if ($header!=null) $select_mark->where('sme.IdHeader = ?',$header);
	 	 	if ($idsubject!=null) $select_mark->where('sme.Course = ?',$idsubject);
	 	 	$row=$db->fetchRow($select_mark);
	 	 	//echo $select_mark;exit;
	 	 }
	     return $row;
	}
	public function getStudentMark($idStudentMarkEntry){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		//check ada x mark entry sebelum ni
		$select_mark = $db->select()
		->from(array('sme'=>'tbl_student_marks_entry'))
		->join(array('cmp'=>'tbl_marksdistributionmaster'),'cmp.IdMarksDistributionMaster=sme.idMarksDistributionMaster',array('Percentage'))
		->join(array('def'=>'tbl_examination_assessment_type'),'def.IdExaminationAssessmentType=sme.component',array('ComponentName'=>'DescriptionDefaultlang'))
		->join(array('sm'=>'tbl_subjectmaster'),'sm.idsubject=sme.course',array('BahasaIndonesia','SubjectName','ShortName','CreditHours'))
		//->joinLeft(array('s'=>'tbl_staffmaster'),'s.IdStaff=sme.ApprovedBy',array('ApprovedBy'=>'FullName'))
		//->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition=sme.MarksEntryStatus',array('MarkStatus'=>'BahasaIndonesia','DefinitionDesc'))
		->where('sme.IdStudentMarksEntry = ?',$idStudentMarkEntry);
	
		return $entry_list = $db->fetchRow($select_mark);
	}
	
	
	public function getStudentMarkByMarkDistributionMaster($idmark,$idStudent){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		//check ada x mark entry sebelum ni
		$select_mark = $db->select()
		->from(array('sme'=>'tbl_student_marks_entry'))
		->join(array('dst'=>'tbl_marksdistributionmaster'),'sme.IdMarksDistributionMaster=dst.IdMarksDistributionMaster')
		->where('sme.IdMarksDistributionMaster = ?',$idmark)
		->where('sme.IdStudentRegistration = ?',$idStudent);
	
		return $entry_list = $db->fetchRow($select_mark);
	}
	
	public function getItemMark($IdStudentMarksEntry,$itemId){
		
		 $db = Zend_Db_Table::getDefaultAdapter();
			
		 //check ada x mark entry sebelum ni
	 	 $select = $db->select()
	 	 				  ->from(array('sme'=>'tbl_student_detail_marks_entry'))
	 	 				  ->where('sme.IdStudentMarksEntry = ?',$IdStudentMarksEntry)
	 	 				  ->where('sme.ComponentDetail = ?',$itemId);
	 	 				  
	 	 return $row = $db->fetchRow($select);	
	}
	
	
	public function getStudentTotalMarkOri($semester_id,$program_id,$subject_id,$IdStudentRegistration,$IdStudentRegSubjects){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		
		$sql =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"),array('IdMarksDistributionMaster'))							
							    ->where("mdm.semester = ?",$semester_id)
								->where("mdm.IdProgram = ?",$program_id)
								->where("mdm.IdCourse = ?",$subject_id);
		
		 
	 	 $select = $db->select()
	 	 				  ->from(array('sme'=>'tbl_student_marks_entry'),array('FinalTotalMarkObtained'))
	 	 				  ->where('sme.IdMarksDistributionMaster IN (?)',$sql)
	 	 				  ->where('sme.IdStudentRegistration = ?',$IdStudentRegistration);
		
		 $rows = $db->fetchAll($select);	
		 
		
		 if(is_array($rows)){
		 	 $grand_total_mark=0;
			 foreach($rows as $row){		 	
			 	$grand_total_mark = abs($grand_total_mark) + abs($row["FinalTotalMarkObtained"]);
			 }
			 
			 //mappkan markah dgn grade
			 $cms_calculation = new Cms_ExamCalculation();
		     $grade	= $cms_calculation->getGradeInfo($semester_id,$program_id,$subject_id,$grand_total_mark);			
			 
		     if(isset($grade["Pass"])){
			     if($grade["Pass"]==1) $grade_status = 'Pass'; //Pass 
			     else if($grade["Pass"]==0) $grade_status = 'Fail'; //Fail
		     	 else $grade_status='';
		     }else{
		     	 $grade_status='';
		     }
			 
			 $data["final_course_mark"]=$grand_total_mark;
			 $data["grade_point"]=$grade["GradePoint"];
			 $data["grade_name"]=$grade["GradeName"];
			 $data["grade_desc"]=$grade["GradeDesc"];
			 $data["grade_status"]=$grade_status; //status should consider on mark distribution component grade status for now belum checking lagi just amik status di grade setup (22/11/2013)

			
			 //save dalam studentregsubject
			 $subRegDB = new Examination_Model_DbTable_StudentRegistrationSubject();
			 $subRegDB->updateData($data,$IdStudentRegSubjects);
		 }
	}
	
	
	public function getComponentMark($IdStudentRegistration,$IdMarksDistributionMaster,$idSemester,$idCourse){
		
		 $db = Zend_Db_Table::getDefaultAdapter();
		
	 	 $select_mark = $db->select()
	 	 				  ->from(array('sme'=>'tbl_student_marks_entry'))
	 	 				  ->where('sme.IdStudentRegistration = ?',$IdStudentRegistration)
	 	 				  ->where('sme.IdMarksDistributionMaster = ?',$IdMarksDistributionMaster)
	 	 				  ->where('sme.Course = ?',$idCourse)
	 	 				  ->where('sme.IdSemester = ?',$idSemester);
	 	 	//echo $select_mark .'<br />';	  
	     return $entry_list = $db->fetchRow($select_mark);	
	}
	public function getAllComponentMark($idprogram,$idsemester,$idsubject,$IdStudentRegistration,$idStudentRegSubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select_mark = $db->select()
		->from(array('sme'=>'tbl_student_marks_entry'))
		->join(array('dst'=>'tbl_marksdistributionmaster'),'sme.IdMarksDistributionMaster=dst.IdMarksDistributionMaster',array())
		->where('sme.IdStudentRegistration = ?',$IdStudentRegistration)
		->where('sme.IdStudentRegSubjects = ?',$idStudentRegSubject)
		->where('sme.Course = ?',$idsubject)
		//->where('sme.IdProgram = ?',$idprogram)
		->where('sme.IdSemester = ?',$idsemester);
		//echo $select_mark .'<br />';
		return $entry_list = $db->fetchAll($select_mark);
	}
	
	public function updateExamStatus($IdStudentRegSubjects){
		
		$examstatus=$this->getExamStatus($IdStudentRegSubjects);
		$data["exam_status"]=$examstatus;
		//echo var_dump($examstatus);exit;
		//save dalam studentregsubject
		$subRegDB = new Examination_Model_DbTable_StudentRegistrationSubject();
		$subRegDB->updateData($data,$IdStudentRegSubjects);
			
	}
	public function updateGradeName($semester_id,$program_id,$subject_id,$IdStudentRegistration=null,$IdStudentRegSubjects,$gradecalulation=true,$remark=false){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select=$db->select()
		->from(array('srs'=>"tbl_studentregsubjects"))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=srs.IdStudentRegistration')
		->where('srs.IdStudentRegSubjects=?',$IdStudentRegSubjects);
		$row=$db->fetchRow($select);
		$idlandscape=$row['IdLandscape'];
		$grand_total_mark=$row['final_course_mark'];
		if($grand_total_mark!=""){
			$cms_calculation = new Cms_ExamCalculation();
			$grade	= $cms_calculation->getGradeInfo($semester_id,$program_id,$subject_id,$grand_total_mark,$idlandscape);
			//$data["final_course_mark"]=$grand_total_mark;
			if ($remark) {
				$examstatus=$this->getExamStatus($IdStudentRegSubjects);
				$data["exam_status"]=$examstatus;
			}
		
			if ($gradecalulation) {
				if(isset($grade["Pass"])){
					if($grade["Pass"]==1) $grade_status = 'Pass'; //Pass
					else if($grade["Pass"]==0) $grade_status = 'Fail'; //Fail
					else $grade_status='';
				}else{
					$grade_status='';
				}
		
				$data["grade_point"]=$grade["GradePoint"];
				$data["grade_name"]=$grade["GradeName"];
				$data["grade_desc"]=$grade["GradeDesc"];
				$data["grade_status"]=$grade_status;
			} //status should consider on mark distribution component grade status for now belum checking lagi just amik status di grade setup (22/11/2013)
		}
		
		//save dalam studentregsubject
		$subRegDB = new Examination_Model_DbTable_StudentRegistrationSubject();
		$subRegDB->updateData($data,$IdStudentRegSubjects);
	}
	public function updatePercentage($semester_id,$program_id,$subject_id,$IdStudentRegistration,$IdStudentRegSubjects,$gradecalulation=true,$remark=false,$marks){
		$db = Zend_Db_Table::getDefaultAdapter();
		foreach ($marks as $id) {
			$select=$db->select()
			->from(array('srs'=>"tbl_student_marks_entry"))
			->where('srs.IdStudentMarksEntry=?',$id);
			$row=$db->fetchRow($select);
			$idmarkdist=$row['IdMarksDistributionMaster'];
			$component=$dbDistr->fnGetMarksDistributionMasterById($idmarkdist);
			if ($component) {
				$percentage=$component['Percentage'];
				$data['FinalTotalMarkObtained']=$row['TotalMarkObtained']*$percentage/100;
				$this->updateData($data, $id);
			}
		}
		//update total
		$this->getStudentTotalMark($semester_id, $program_id, $subject_id, $IdStudentRegistration, $IdStudentRegSubjects,true,true);
	}
	
	public function getStudentTotalMark($semester_id,$program_id,$subject_id,$IdStudentRegistration,$IdStudentRegSubjects,$gradecalulation=true,$remark=false){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		//get landscape 
		$sql =  $db->select()->from(array("mdm" =>"tbl_studentregistration"))
		->where("mdm.IdStudentRegistration = ?",$IdStudentRegistration)
		->where("mdm.IdProgram = ?",$program_id);
		
		$std = $db->fetchRow($sql);
		$idlandscape=$std['IdLandscape'];
		$idbranch=$std['IdBranch'];
		
		$sql =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"),array('IdMarksDistributionMaster','min_mark'))							
							    ->where("mdm.semester = ?",$semester_id)
								->where("mdm.IdProgram = ?",$program_id)
								->where("mdm.IdCourse = ?",$subject_id)
								->where("mdm.IdBranch = ?",$idbranch);
		 
		 $mdm = $db->fetchAll($sql);

		 if (!$mdm) {
			 	$sql =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"),array('IdMarksDistributionMaster','min_mark'))
			 	->where("mdm.semester = ?",$semester_id)
			 	->where("mdm.IdProgram = ?",$program_id)
			 	->where("mdm.IdCourse = ?",$subject_id)
			 	->where("mdm.IdBranch = '0'");
			 		
			 	$mdm = $db->fetchAll($sql);
		 	
		 }
		   
		 $grand_total_mark = 0;
		 foreach ($mdm as $m){
 			$select = $db->select()
	 	 				  ->from(array('sme'=>'tbl_student_marks_entry'),array('FinalTotalMarkObtained','IdStudentMarksEntry'))
	 	 				  ->where('sme.IdMarksDistributionMaster = ?',$m["IdMarksDistributionMaster"])
	 	 				  ->where('sme.IdStudentRegSubjects = ?',$IdStudentRegSubjects);
			$dmark = $db->fetchRow($select);	
			
			if(is_array($dmark)){
				//delete mark yang sama distribution (overcome multiple mark for the same distribution)
				$sqld="Delete from tbl_student_marks_entry where 
						IdMarksDistributionMaster = ".$m["IdMarksDistributionMaster"]."
						and IdStudentRegSubjects = ".$IdStudentRegSubjects."
						and IdStudentMarksEntry != ".$dmark["IdStudentMarksEntry"];
				
				$db->query($sqld);
				if ($m['min_mark'] <= $dmark["FinalTotalMarkObtained"])
					$grand_total_mark = $grand_total_mark + $dmark["FinalTotalMarkObtained"];
				else {
						$grand_total_mark=0; break;
					}
			}
		 }
		 //mappkan markah dgn grade
		 if($grand_total_mark!=0){
			 $cms_calculation = new Cms_ExamCalculation();
		     $grade	= $cms_calculation->getGradeInfo($semester_id,$program_id,$subject_id,$grand_total_mark,$idlandscape);			
			//echo var_dump($grade);exit;
		     $data["final_course_mark"]=$grand_total_mark;
		     if ($remark) {
		     	$examstatus=$this->getExamStatus($IdStudentRegSubjects); 
		     	$data["exam_status"]=$examstatus;
		     }

		     if ($gradecalulation) {
		     	//get pass grade from kurikulum
		     	$grade_status='';
		     	$dbstd=new Registration_Model_DbTable_Studentregistration();
		     	$std=$dbstd->getData($IdStudentRegistration);
		     	$dblanscape=new GeneralSetup_Model_DbTable_Landscapesubject();
		     	$row=$dblanscape->getInfoMinGrade($std['IdLandscape'], $subject_id, $std['IdProgramMajoring']);
			    if (!$row) {
			       	$dblanscape=new GeneralSetup_Model_DbTable_LandscapeBlockSubject();
			    	$row=$dblanscape->getInfoMinGrade($std['IdLandscape'], $subject_id, $std['IdProgramMajoring']);
			    }
			    
			    if ($row) {
			    	$grademin=$row['Min_Grade'];
			    	if ($grademin!='') {
				    	$dbgrade=new Examination_Model_DbTable_Grade();
				    	$gradepoint=$dbgrade->getGradePointByGrade($semester_id, $program_id, $subject_id, $grademin,$idlandscape);
						if ($gradepoint['GradePoint']<=$grade['GradePoint']) 
							$grade_status = 'Pass'; //Pass
						else 
							$grade_status = 'Fail'; //Fail
			    	}
			    }
			    
			    if ($grade_status=='') {
			    
				    if(isset($grade["Pass"])){
					     if($grade["Pass"]==1) $grade_status = 'Pass'; //Pass 
					     else if($grade["Pass"]==0) $grade_status = 'Fail'; //Fail
				     	 else $grade_status='';
				     }else{
				     	 $grade_status='';
				     }
			    }
				 $data["grade_point"]=$grade["GradePoint"];
				 $data["grade_name"]=$grade["GradeName"];
				 $data["grade_desc"]=$grade["GradeDesc"];
				 $data["grade_status"]=$grade_status;
		     } //status should consider on mark distribution component grade status for now belum checking lagi just amik status di grade setup (22/11/2013)
		 }else{
			 $data["final_course_mark"]=NULL;
			 $data["grade_point"]=NULL;
			 $data["grade_name"]="";
			 $data["grade_desc"]="";
			 $data["grade_status"]=""; //status should consider on mark distribution component grade status for now belum checking lagi just amik status di grade setup (22/11/2013)		 	
		 }
		
		 //save dalam studentregsubject
		 $subRegDB = new Examination_Model_DbTable_StudentRegistrationSubject();
		 $subRegDB->updateData($data,$IdStudentRegSubjects); 
		 
		 return $grand_total_mark;	 
	}
	
	public function getStudentTotalMarkPerMhs($idmark,$IdStudentRegistration,$IdStudentRegSubjects,$gradecalulation=true,$remark=false){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		
		
		$sql =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))
		->where("mdm.IdMarksDistributionMaster = ?",$idmark);
	
		$mdm = $db->fetchRow($sql);
		$semester_id=$mdm['semester'];
		$program_id=$mdm['IdProgram'];
		$subject_id=$mdm['IdCourse'];
		$idbranch=$mdm['IdBranch'];
		//get landscape
		$sql =  $db->select()->from(array("mdm" =>"tbl_studentregistration"))
		->where("mdm.IdStudentRegistration = ?",$IdStudentRegistration)
		->where("mdm.IdProgram = ?",$program_id);
		
		$std = $db->fetchRow($sql);
		$idlandscape=$std['IdLandscape'];
		 
		$sql =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))
		->where("mdm.semester = ?",$semester_id)
		->where("mdm.IdProgram = ?",$program_id)
		->where("mdm.IdCourse = ?",$subject_id)
		->where("mdm.IdBranch = ?",$idbranch);	
		$mdm = $db->fetchAll($sql);
		
		
		$grand_total_mark = 0;
		foreach ($mdm as $m){
			$select = $db->select()
			->from(array('sme'=>'tbl_student_marks_entry'),array('FinalTotalMarkObtained','IdStudentMarksEntry'))
			->where('sme.IdMarksDistributionMaster = ?',$m["IdMarksDistributionMaster"])
			->where('sme.IdStudentRegSubjects = ?',$IdStudentRegSubjects);
			$dmark = $db->fetchRow($select);
				
			if(is_array($dmark)){
				//delete mark yang sama distribution (overcome multiple mark for the same distribution)
				$sqld="Delete from tbl_student_marks_entry where
						IdMarksDistributionMaster = ".$m["IdMarksDistributionMaster"]."
						and IdStudentRegSubjects = ".$IdStudentRegSubjects."
						and IdStudentMarksEntry != ".$dmark["IdStudentMarksEntry"];
	
				$db->query($sqld);
				if ($m['min_mark'] <= $dmark["FinalTotalMarkObtained"])
					$grand_total_mark = $grand_total_mark + $dmark["FinalTotalMarkObtained"];
				else {
					$grand_total_mark=0; break;
				}
			}
		}
		//mappkan markah dgn grade
		if($grand_total_mark!=0){
			$cms_calculation = new Cms_ExamCalculation();
			$grade	= $cms_calculation->getGradeInfo($semester_id,$program_id,$subject_id,$grand_total_mark,$idlandscape);
			//echo var_dump($grade);exit;
			$data["final_course_mark"]=$grand_total_mark;
			if ($remark) {
				$examstatus=$this->getExamStatus($IdStudentRegSubjects);
				$data["exam_status"]=$examstatus;
			}
	
			if ($gradecalulation) {
				//get pass grade from kurikulum
				$grade_status='';
				$dbstd=new Registration_Model_DbTable_Studentregistration();
				$std=$dbstd->getData($IdStudentRegistration);
				$dblanscape=new GeneralSetup_Model_DbTable_Landscapesubject();
				$row=$dblanscape->getInfoMinGrade($std['IdLandscape'], $subject_id, $std['IdProgramMajoring']);
				if (!$row) {
					$dblanscape=new GeneralSetup_Model_DbTable_LandscapeBlockSubject();
					$row=$dblanscape->getInfoMinGrade($std['IdLandscape'], $subject_id, $std['IdProgramMajoring']);
				}
				 
				if ($row) {
					$grademin=$row['Min_Grade'];
					if ($grademin!='') {
						$dbgrade=new Examination_Model_DbTable_Grade();
						$gradepoint=$dbgrade->getGradePointByGrade($semester_id, $program_id, $subject_id, $grademin,$idlandscape);
						if ($gradepoint['GradePoint']<=$grade['GradePoint'])
							$grade_status = 'Pass'; //Pass
						else
							$grade_status = 'Fail'; //Fail
					}
				}
				 
				if ($grade_status=='') {
					 
					if(isset($grade["Pass"])){
						if($grade["Pass"]==1) $grade_status = 'Pass'; //Pass
						else if($grade["Pass"]==0) $grade_status = 'Fail'; //Fail
						else $grade_status='';
					}else{
						$grade_status='';
					}
				}
				$data["grade_point"]=$grade["GradePoint"];
				$data["grade_name"]=$grade["GradeName"];
				$data["grade_desc"]=$grade["GradeDesc"];
				$data["grade_status"]=$grade_status;
			} //status should consider on mark distribution component grade status for now belum checking lagi just amik status di grade setup (22/11/2013)
		}else{
			$data["final_course_mark"]=NULL;
			$data["grade_point"]=NULL;
			$data["grade_name"]="";
			$data["grade_desc"]="";
			$data["grade_status"]=""; //status should consider on mark distribution component grade status for now belum checking lagi just amik status di grade setup (22/11/2013)
		}
	
		//save dalam studentregsubject
		$subRegDB = new Examination_Model_DbTable_StudentRegistrationSubject();
		$subRegDB->updateData($data,$IdStudentRegSubjects);
			
		return $grand_total_mark;
	}
	public function getStudentComponentMark($program_id,$semester_id,$subject_id,$IdStudentRegistration,$IdStudentRegSubjects){
			
		$db = Zend_Db_Table::getDefaultAdapter();	

		$sql2 =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))													
						     ->where("mdm.IdProgram = ?",$program_id)
						     ->where("mdm.semester = ?",$semester_id)
							 ->where("mdm.IdCourse = ?",$subject_id)
							 ->where("mdm.IdComponentType = 38");
		$row2 = $db->fetchRow($sql2);
		
		if($row2){
			$sql =  $db->select()->from(array("sme" =>"tbl_student_marks_entry"),array())	
								 ->join(array('mdm'=>'tbl_marksdistributionmaster'),'mdm.IdMarksDistributionMaster = sme.IdMarksDistributionMaster',array('Component'=>'IdComponentType'))						
							     ->where("sme.IdSemester = ?",$semester_id)
								 ->where("sme.Course = ?",$subject_id)
								 ->where("sme.IdStudentRegistration = ?",$IdStudentRegistration)
								 ->where("sme.IdStudentRegSubjects = ?",$IdStudentRegSubjects)
								 ->where("mdm.IdComponentType = 38");
			
			$row = $db->fetchRow($sql);
			
			if(!$row){
				return false;
			}
		}
		
		
		
		$sql3 =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))													
						     ->where("mdm.IdProgram = ?",$program_id)
						     ->where("mdm.semester = ?",$semester_id)
							 ->where("mdm.IdCourse = ?",$subject_id)
							 ->where("mdm.IdComponentType = 40");
		$row3 = $db->fetchRow($sql3);
		  		
	    if($row3){
			$sql2 =  $db->select()->from(array("sme" =>"tbl_student_marks_entry"),array())
							 ->join(array('mdm'=>'tbl_marksdistributionmaster'),'mdm.IdMarksDistributionMaster = sme.IdMarksDistributionMaster',array('Component'=>'IdComponentType'))							
						     ->where("sme.IdSemester = ?",$semester_id)
							 ->where("sme.Course = ?",$subject_id)
							 ->where("sme.IdStudentRegistration = ?",$IdStudentRegistration)
							 ->where("sme.IdStudentRegSubjects = ?",$IdStudentRegSubjects)
							 ->where("mdm.IdComponentType = 40");
		
			$row2 = $db->fetchRow($sql2);
			
			if(!$row2){
				return false;
			}
	    }
		    
		return true;
		
	}
    
	public function getStudentMarkByMarkDist($program_id,$semester_id,$subject_id,$IdStudentRegistration,$IdStudentRegSubjects,$idmarkdist){
			
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$sql =  $db->select()->from(array("sme" =>"tbl_student_marks_entry"))
			->join(array('mdm'=>'tbl_marksdistributionmaster'),'mdm.IdMarksDistributionMaster = sme.IdMarksDistributionMaster',array())
			->where("sme.IdSemester = ?",$semester_id)
			->where("sme.Course = ?",$subject_id)
			->where("sme.IdStudentRegistration = ?",$IdStudentRegistration)
			->where("sme.ApprovedBy is not null")
			->where("sme.MarksEntryStatus = '411'")
			->where("sme.IdMarksDistributionMaster = ?",$idmarkdist);
						
			$row = $db->fetchRow($sql);
				
			return $row;
	}
	
	public function getCountStudentMarkByMarkDist($program_id,$semester_id,$idmarkdist,$subject_id){
			
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$sql =  $db->select()->from(array("sme" =>"tbl_student_marks_entry"),array('count'=>'count(*)'))
		->join(array('mdm'=>'tbl_marksdistributionmaster'),'mdm.IdMarksDistributionMaster = sme.IdMarksDistributionMaster',array())
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=sme.IdStudentRegistration',array())
		->where("sme.IdSemester = ?",$semester_id)
		->where("sme.Course = ?",$subject_id)
		->where("sr.IdProgram = ?",$program_id)
		->where("sme.IdMarksDistributionMaster = ?",$idmarkdist);
	
		$row = $db->fetchRow($sql);
	
		return $row['count'];
	}
	
	public function getCountStudentMarkByMarkDistPerProgram($semester_id,$idmarkdist,$subject_id,$grp_id){
			
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$sql =  $db->select()->from(array("sme" =>"tbl_student_marks_entry"),array('count'=>'count(*)'))
		->join(array('mdm'=>'tbl_marksdistributionmaster'),'mdm.IdMarksDistributionMaster = sme.IdMarksDistributionMaster',array())
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=sme.IdStudentRegistration',array('sr.IdProgram'))
		->join(array('srs'=>'tbl_studentregsubjects'),'sme.IdStudentRegSubjects=srs.IdStudentRegSubjects')
		->where("sme.IdSemester = ?",$semester_id)
		->where("sme.Course = ?",$subject_id)
		->where("srs.Idcoursetagginggroup = ?",$grp_id)
		->where("sme.IdMarksDistributionMaster = ?",$idmarkdist)
		->group('sr.IdProgram') ;
	
		$row = $db->fetchAll($sql);
	
		return $row;
	}
	
    public function countComponentMark($IdStudentRegistration,$IdMarksDistributionMaster,$idSemester,$idCourse){
		
		 $db = Zend_Db_Table::getDefaultAdapter();
		
	 	 $select_mark = $db->select()
	 	 				  //->count()
                          ->from('tbl_student_marks_entry',array('total' => "COUNT(*)"))
	 	 				  ->where('IdStudentRegistration IN ('.$IdStudentRegistration.')')
	 	 				  ->where('IdMarksDistributionMaster = ?',$IdMarksDistributionMaster)
	 	 				  ->where('Course = ?',$idCourse)
	 	 				  ->where('IdSemester = ?',$idSemester)
                          ->group('Course');
	 	 		  
	     //echo $select_mark;
         
         $entry_list = $db->fetchRow($select_mark);
         
         return $entry_list['total'];	
	}
    
    public function countComponentMarkApproved($IdStudentRegistration,$IdMarksDistributionMaster,$idSemester,$idCourse){
		
		 $db = Zend_Db_Table::getDefaultAdapter();
		
	 	 $select_mark = $db->select()
	 	 				  //->count()
                          ->from('tbl_student_marks_entry',array('total' => "COUNT(*)"))
	 	 				  ->where('IdStudentRegistration IN ('.$IdStudentRegistration.')')
	 	 				  ->where('IdMarksDistributionMaster = ?',$IdMarksDistributionMaster)
	 	 				  ->where('Course = ?',$idCourse)
	 	 				  ->where('IdSemester = ?',$idSemester)
	 	 				  ->where('MarksEntryStatus = 411')
                          ->group('Course');
	 	 		  
	     //echo $select_mark;
         $entry_list = $db->fetchRow($select_mark);
         
         return $entry_list['total'];	
	}
    
	public function insertMarkOfAttendance($semester,$IdSubject,$groupid,$idregistration,$IdStudentRegSubject,$IdMarksDistributionMaster,$percentage,$MarkMaks) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		//
		$courseGroupStudentAttendanceDb = new Examination_Model_DbTable_CourseGroupStudentAttendanceDetail();
		
			$class_session = $courseGroupStudentAttendanceDb->getAttendanceSessionCount($groupid,$idregistration);
			//kehadiran dihitung dari hadir+ijin+sakit
			$hadir = $courseGroupStudentAttendanceDb->getAttendanceStatusCount($groupid,$idregistration,395);
			$ijin  = $courseGroupStudentAttendanceDb->getAttendanceStatusCount($groupid,$idregistration,396);
			$sakit = $courseGroupStudentAttendanceDb->getAttendanceStatusCount($groupid,$idregistration,397);
			$class_attended = $hadir+$ijin+$sakit;
			$mark=0;
			$data=array();
			
			if ($class_session > 0) {
				
				$mark=$class_attended/$class_session*100;
				
				$markentry=$this->checkMarkEntry($idregistration, $IdMarksDistributionMaster, $IdStudentRegSubject, $semester);
				
				
				if ($markentry) {
					//update
					$id=$markentry['IdStudentMarksEntry'];
					$data['TotalMarkObtained'] = $mark;
					$data['FinalTotalMarkObtained'] =$mark*$percentage/100;
					$db->update($this->_name,$data, 'IdStudentMarksEntry='.$id);
				} else {
					
					//insert
					$data["Component"]   =  50;
					$data["ComponentItem"]   =  '';
					$data["Instructor"] = '';
					$data["AttendanceStatus"] = '';
					$data["MarksEntryStatus"] = 407; // 407=>ENTRY ( id Type= 93 )
					$data["UpdUser"] = $auth->getIdentity()->iduser;
					$data["UpdDate"] = date('Y-m-d H:i:s');
					$data["exam_group_id"] = 0;
					$data['MarksTotal']=$MarkMaks;
					$data['TotalMarkObtained'] = $mark;
					$data['FinalTotalMarkObtained'] =$mark*$percentage/100;
					$data['IdMarksDistributionMaster'] =$IdMarksDistributionMaster;
					$data['IdStudentRegSubjects']=$IdStudentRegSubject;
					$data['Course'] = $IdSubject;
					$data['IdStudentRegistration']=$idregistration;
					$data['IdSemester'] =$semester;
					//echo var_dump($data);exit;
					$db->insert($this->_name,$data);
						
				}
			
		}
		
		
	}
	
	public function isAllComponentHasMark($semester_id,$program_id,$subject_id,$IdStudentRegistration,$IdStudentRegSubjects,$idbranch=null) {
		$db = Zend_Db_Table::getDefaultAdapter();
		
		
		$sql =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"),array('IdMarksDistributionMaster'))
				->where("mdm.semester = ?",$semester_id)
				->where("mdm.IdProgram = ?",$program_id)
				->where("mdm.IdCourse = ?",$subject_id)
				->where("mdm.IdBranch = ?",$idbranch);
		
		$mdm = $db->fetchAll($sql);
		if (!$mdm) {
			$sql =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"),array('IdMarksDistributionMaster'))
			->where("mdm.semester = ?",$semester_id)
			->where("mdm.IdProgram = ?",$program_id)
			->where("mdm.IdCourse = ?",$subject_id)
			->where("mdm.IdBranch = '0'");
			
			$mdm = $db->fetchAll($sql);
		}
		$mising = null;
		foreach ($mdm as $m){
			$select = $db->select()
			->from(array('sme'=>'tbl_student_marks_entry'),array('FinalTotalMarkObtained','IdStudentMarksEntry'))
			->where('sme.IdMarksDistributionMaster = ?',$m["IdMarksDistributionMaster"])
			->where('sme.IdStudentRegSubjects = ?',$IdStudentRegSubjects);
			$dmark = $db->fetchRow($select);
			//echo var_dump($dmark);exit;
			if((!$dmark) || $dmark==array() || !isset($dmark)){
				//echo var_dump($m);echo 'kena';exit;
				return false;
			}
		}
		return true;
	}
	
	public function isAllComponentHasMarkPerMhs($idmark,$IdStudentRegSubjects) {//$semester_id,$program_id,$subject_id,$IdStudentRegistration,$IdStudentRegSubjects,$idbranch=null) {
		$db = Zend_Db_Table::getDefaultAdapter();
	
	
		$sql =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))
		->where("mdm.IdMarksDistributionMaster = ?",$idmark);
	
		$mdm = $db->fetchRow($sql);
		$semester_id=$mdm['semester'];
		$program_id=$mdm['IdProgram'];
		$subject_id=$mdm['IdCourse'];
		$idbranch=$mdm['IdBranch'];
		
		 
		$sql =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"),array('IdMarksDistributionMaster'))
		->where("mdm.semester = ?",$semester_id)
		->where("mdm.IdProgram = ?",$program_id)
		->where("mdm.IdCourse = ?",$subject_id)
		->where("mdm.IdBranch = ?",$idbranch);
				
		$mdm = $db->fetchAll($sql);
		 
		$mising = null;
		foreach ($mdm as $m){
			$select = $db->select()
			->from(array('sme'=>'tbl_student_marks_entry'),array('FinalTotalMarkObtained','IdStudentMarksEntry'))
			->where('sme.IdMarksDistributionMaster = ?',$m["IdMarksDistributionMaster"])
			->where('sme.IdStudentRegSubjects = ?',$IdStudentRegSubjects);
			$dmark = $db->fetchRow($select);
			//echo var_dump($dmark);exit;
			if((!$dmark) || $dmark==array() || !isset($dmark)){
				//echo var_dump($m);echo 'kena';exit;
				return false;
			}
		}
		return true;
	}
	
	public function isAllComponentHasVerified($semester_id,$program_id,$subject_id,$idGroup,$IdBranch=null) {
		$db = Zend_Db_Table::getDefaultAdapter();
	
	
		$sql =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"),array('IdMarksDistributionMaster'))
		->where("mdm.semester = ?",$semester_id)
		->where("mdm.IdProgram = ?",$program_id)
		->where("mdm.IdCourse = ?",$subject_id);
		if ($IdBranch!=0 ) $sql->where("mdm.IdBranch = ?",$IdBranch);
		else $sql->where("mdm.IdBranch = '0'");
	
		$mdm = $db->fetchAll($sql);
			
		$mising = null;
		foreach ($mdm as $m){
			$select = $db->select()
			->from(array('sme'=>'tbl_student_marks_entry'),array('FinalTotalMarkObtained','IdStudentMarksEntry'))
			->join(array('srs'=>'tbl_studentregsubjects'),'sme.IdStudentRegSubjects=srs.IdStudentRegSubjects')
			->where('sme.IdMarksDistributionMaster = ?',$m["IdMarksDistributionMaster"])
			->where('srs.IdCourseTaggingGroup = ?',$idGroup)
			->where('sme.ApprovedBy is not null and sme.ApprovedOn is not null');
			$dmark = $db->fetchRow($select);
			//echo var_dump($dmark);exit;
			if((!$dmark) || $dmark==array() || !isset($dmark)){
				//echo 'kena';exit;
				return false;
			}
		}
		return true;
	}
	public function getComponentVerifiedStatus($semester_id,$program_id,$subject_id,$idGroup,$idDistribution=null) {
		
		$db = Zend_Db_Table::getDefaultAdapter();
	
		if ($idDistribution==null) {
			$sql =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"),array('IdMarksDistributionMaster'))
			->where("mdm.semester = ?",$semester_id)
			->where("mdm.IdProgram = ?",$program_id)
			->where("mdm.IdCourse = ?",$subject_id);
		
			$mdm = $db->fetchAll($sql);
				
			$mising = 0;$verify=0;
			foreach ($mdm as $m){
				$select = $db->select()
				->from(array('sme'=>'tbl_student_marks_entry'),array('verified'=>'count(*)'))
				->join(array('srs'=>'tbl_studentregsubjects'),'sme.IdStudentRegSubjects=srs.IdStudentRegSubjects',array())
				->where('sme.IdMarksDistributionMaster = ?',$m["IdMarksDistributionMaster"])
				->where('srs.IdCourseTaggingGroup = ?',$idGroup)
				->where('sme.ApprovedBy is not null and sme.ApprovedOn is not null');
				$dmark = $db->fetchRow($select);
				$verify=$verify+$dmark['verified'];
				
				$select = $db->select()
				->from(array('sme'=>'tbl_student_marks_entry'),array('mising'=>'count(*)'))
				->join(array('srs'=>'tbl_studentregsubjects'),'sme.IdStudentRegSubjects=srs.IdStudentRegSubjects',array())
				->where('sme.IdMarksDistributionMaster = ?',$m["IdMarksDistributionMaster"])
				->where('srs.IdCourseTaggingGroup = ?',$idGroup)
				->where('sme.ApprovedBy is null and sme.ApprovedOn is  null');
				$dmark = $db->fetchRow($select);
				//echo var_dump($dmark);exit;
				
				$mising=$mising+$dmark['mising'];
					
				
			}
			$status[0]=$verify;
			$status[1]=$mising;
			return $status;
		}
		else {
			$select = $db->select()
				->from(array('sme'=>'tbl_student_marks_entry'),array('verified'=>'count(*)'))
				->join(array('srs'=>'tbl_studentregsubjects'),'sme.IdStudentRegSubjects=srs.IdStudentRegSubjects',array())
				->where('sme.IdMarksDistributionMaster = ?',$idDistribution)
				->where('srs.IdCourseTaggingGroup = ?',$idGroup)
				->where('sme.ApprovedBy is not null and sme.ApprovedOn is not null');
				$dmark = $db->fetchRow($select);
				$verify=$dmark['verified'];
				
				$select = $db->select()
				->from(array('sme'=>'tbl_student_marks_entry'),array('mising'=>'count(*)'))
				->join(array('srs'=>'tbl_studentregsubjects'),'sme.IdStudentRegSubjects=srs.IdStudentRegSubjects',array())
				->where('sme.IdMarksDistributionMaster = ?',$idDistribution)
				->where('srs.IdCourseTaggingGroup = ?',$idGroup)
				->where('sme.ApprovedBy is null and sme.ApprovedOn is  null');
				$dmark = $db->fetchRow($select);
				//echo var_dump($dmark);exit;
				
				$mising=$dmark['mising'];
				$status[0]=$verify;
				$status[1]=$mising;
				return $status;
				
		}
		
	}
	
	public function getExamStatus($IdStudentRegSubjects) {
		 
		//get component
		$dbcourse = new Examination_Model_DbTable_StudentRegistrationSubject();
		$course=$dbcourse->getSubjects($IdStudentRegSubjects);
		$IdProgram=$course['IdProgram'];
		$IdSubject=$course['IdSubject'];
		$semester=$course['IdSemesterMain'];
		$IdCourseTaggingGroup=$course['IdCourseTaggingGroup'];
		$IdStudentRegistration = $course['IdStudentRegistration'];
		$dbComponent = new Examination_Model_DbTable_Marksdistributionmaster();
		if (isset($semester) && isset($IdProgram) && isset($IdSubject)) {
			$components=$dbComponent->getListMainComponent($semester, $IdProgram, $IdSubject);
			$dbStudentMark = new Examination_Model_DbTable_StudentMarkEntry();
			$courseGroupStudentAttendanceDb=new Examination_Model_DbTable_CourseGroupStudentAttendanceDetail();
			$exam_status='C';
			$dbexam=new Examination_Model_DbTable_ExamGroupAttendance();
			foreach ($components as $component) {
				$IdMarksDistributionMaster=$component['IdMarksDistributionMaster'];
				$type=$component['IdComponentType'];
				if ($component['allow_null']!='1') {
					$grade = $dbStudentMark->checkMarkEntry($IdStudentRegistration, $IdMarksDistributionMaster, $IdStudentRegSubjects, $semester);
					 
					$atts=$dbexam->getExamAttendaceStatus($semester, $IdSubject, $type, $IdStudentRegistration);
					 
					$recorded=false;
					$att=true;
					if ($atts) {
						$recorded=true;
						 
						if ($atts['ega_status']=='398') $att=false; else $att=true;
					}
	
					if (!$recorded || !$att) {
						if (!($grade) || !$att) {
							//check component there
							//echo var_dump($grade);exit;
							if ($component['IdDescription']=='UAS' || $component['IdDescription']=='UTS' || $component['IdDescription']=='UTM' || $component['IdDescription']=='UAM' ) 	{$exam_status='MG'; break;} else $exam_status='IN';
						}
					}
				}
		   
			}
			//echo $exam_status;exit;
			//attendance
			$attendancePolicyDb = new Examination_Model_DbTable_ExamSlipAttendancePolicy();
			$attendance_policy = $attendancePolicyDb->getDataByProgram($IdProgram,null);
			$session = $courseGroupStudentAttendanceDb->getAttendanceSessionCount($IdCourseTaggingGroup,$IdStudentRegistration);
			//kehadiran dihitung dari hadir+ijin+sakit
	
			//echo $session."-".$count;exit;
			if ($session>0) {
				$hadir = $courseGroupStudentAttendanceDb->getAttendanceStatusCount($IdCourseTaggingGroup,$IdStudentRegistration,395);
				$ijin  = $courseGroupStudentAttendanceDb->getAttendanceStatusCount($IdCourseTaggingGroup,$IdStudentRegistration,396);
				$sakit = $courseGroupStudentAttendanceDb->getAttendanceStatusCount($IdCourseTaggingGroup,$IdStudentRegistration,397);
				$count = $hadir+$ijin+$sakit;
				if ($attendance_policy['esap_percentage']!=null) {
	
					if (($count/$session*100) < $attendance_policy['esap_percentage']) {$exam_status='NR';}
				}
				else
				{
					if (($session-$count) > $attendance_policy['esap_count']) {$exam_status='NR';}
				}
			}
	
			return $exam_status;
		}
	
		return $exam_status='';
	}
  
	public function getGrade($idstudentregsub) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		 ->from(array('srs'=>'tbl_studentregsubjects'))
		 ->where('srs.IdStudentRegSubjects=?',$idstudentregsub);
		return $db->fetchRow($select);
		
	}
	
	public function getCountGrade($idgroup) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('srs'=>'tbl_studentregsubjects'),array('Count'=>'Count(*)','exam_status'))
		->where('srs.IdCourseTaggingGroup=?',$idgroup)
		->group('srs.exam_status');
		return $db->fetchAll($select);
	
	}
}
?>