<?php 
class Examination_Model_DbTable_StudentRegistration extends Zend_Db_Table_Abstract
{
	
	/**
	 * The default table name 
	 */
	
	protected $_name = 'tbl_studentregistration';
	protected $_primary = "IdStudentRegistration";
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function getSemesterByProgramIntake($idProgram,$idIntake){	
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db->select()
					  ->from(array('sr'=>$this->_name),array())
					  ->joinLeft(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=sr.IdSemesterMain',array('IdSemesterMaster','SemesterMainName'))
					  ->where('sr.IdProgram = ?',$idProgram)
					  ->where('sr.IdIntake = ?',$idIntake)
					  ->group('sm.IdSemesterMaster');					 
					  
		 $row = $db->fetchAll($select);	
		 return $row;
	}
	
	
	public function getSubjectByProgramIntake($idProgram,$idIntake,$idSemester){	
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		 $select = $db->select()
					  ->from(array('sr'=>$this->_name),array())
					  ->joinLeft(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration=sr.IdStudentRegistration',array('IdSubject'))
					  ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('SubjectName'=>'subjectMainDefaultLanguage'))
					  ->where('sr.IdProgram = ?',$idProgram)
					  ->where('sr.IdIntake = ?',$idIntake)
					  ->where('sr.IdSemesterMain = ?',$idSemester)
					  ->group('srs.IdSubject');					 
					  
		 $row = $db->fetchAll($select);	
		 return $row;
	}
	
	public function getStudentByProgramIntakeSemester($idProgram,$idIntake,$idSemester){	
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db->select()
					  ->from(array('sr'=>$this->_name),array())
					  ->joinLeft(array('sp'=>'student_profile'),'sp.appl_id=sr.idApplication',array('appl_fname','appl_mname','appl_lname'))
					  ->joinLeft(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=sr.IdSemesterMain',array('IdSemesterMaster','SemesterMainName'))
					  ->where('sr.IdProgram = ?',$idProgram)
					  ->where('sr.IdIntake = ?',$idIntake)
					  ->group('sm.IdSemesterMaster');					 
					  
		 $row = $db->fetchAll($select);	
		 return $row;
	}
	
	//nak dapatkan student yg register subject ni dan active pada semester tersebut dan kalo dah ada mark entry amik info marks dia
	public function getStudentList($idSemester,$idProgram,$idSubject,$IdMarksDistributionMaster,$formData=null){	
	
		$db = Zend_Db_Table::getDefaultAdapter();
				
		$select = $db->select()
					  ->from(array('sr'=>$this->_name),array('IdStudentRegistration','registrationId'))
					  ->join(array('sss'=>'tbl_studentsemesterstatus'),'sss.IdStudentRegistration = sr.IdStudentRegistration',array())
					  ->join(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration',array('IdStudentRegSubjects'))
					  ->joinLeft(array('sp'=>'student_profile'),'sp.appl_id=sr.idApplication',array('appl_fname','appl_mname','appl_lname'))
					  ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('SubjectName'=>'BahasaIndonesia','SubCode'))
					 // ->joinLeft(array('sme'=>'tbl_student_marks_entry'),'sme.IdStudentRegSubjects = srs.IdStudentRegSubjects',array('IdStudentMarksEntry','TotalMarkObtained','IdMarksDistributionMaster','MarksEntryStatus'))
					  ->where('sr.profileStatus=92')  // Active (refer defination table  type=20)
					  ->where('sss.studentsemesterstatus=130')  //Register pada semester tersebut
					  ->where('srs.Active=1') //Subject Status 1:Register 2:Add&Drop 3:Withdraw
					  ->where('sr.IdProgram = ?',$idProgram)
					  ->where('sss.IdSemesterMain = ?',$idSemester)
					  ->where('srs.IdSubject = ?',$idSubject)					 
					 // ->where("sme.MarksEntryStatus IS NULL OR sme.MarksEntryStatus = '407'") //407=>ENTRY 409=>SUBMITTED 411=>APPROVED					
					  ->group('sr.IdStudentRegistration')
                      ->order("sr.registrationId");
					  
		 if(isset($formData)){
		 	
		 	if(isset($formData["student_name"]) && $formData["student_name"]!=''){
		 		$select->where("sp.appl_fname  LIKE '%".$formData["student_name"]."%'");
		 		$select->orwhere("sp.appl_mname  LIKE '%".$formData["student_name"]."%'");
		 		$select->orwhere("sp.appl_lname  LIKE '%".$formData["student_name"]."%'");
		 	}
		 	
		    if(isset($formData["student_id"]) && $formData["student_id"]!=''){
		 		$select->where("sr.registrationId = ?",$formData["student_id"]);
		 	}
		 }
		 
		 
		 $rows = $db->fetchAll($select);	
		
		     //ini nak filter jika dah ada masuk markah amik yg status entry sahaja dan amik info markah yg berdasasrkan markdistribution component
		     $i=0;
			 foreach($rows as $key => $row){

			 	    $rows[$i]['IdStudentMarksEntry']='';
			        $rows[$i]['TotalMarkObtained']='';
			        
				 	//check ada x mark entry
				 	$select_mark = $db->select()
				 	 				  ->from(array('sme'=>'tbl_student_marks_entry'))
				 	 				  ->where('sme.IdStudentRegistration = ?',$row["IdStudentRegistration"])
				 	 				  ->where('sme.IdMarksDistributionMaster = ?',$IdMarksDistributionMaster)
				 	 				  ->where('sme.IdStudentRegSubjects = ?',$row["IdStudentRegSubjects"])
				 	 				  ->where('sme.IdSemester = ?',$idSemester);
				 	 				  
				 	$entry_list = $db->fetchRow($select_mark);	
				 	
				 
				 	if(isset($entry_list["IdStudentMarksEntry"]) && $entry_list["IdStudentMarksEntry"]!=''){
				 		
			             //check mark entry status	407=>ENTRY 409=>SUBMITTED 411=>APPROVED					    		
		    			if($entry_list["MarksEntryStatus"]==409 || $entry_list["MarksEntryStatus"]==411 ){		    				
		    				unset($rows[$key]);			    			
			    		}else{
			    			$rows[$i]['IdStudentMarksEntry']=$entry_list["IdStudentMarksEntry"];
			    			$rows[$i]['TotalMarkObtained']=$entry_list["TotalMarkObtained"];
			    		}//end if
				 	}
			 
			 $i++;
			 }//end foreach
			 
			// print_r($rows);
			
		return $rows;
			 
	}
	
	
	
	
	public function getStudentListMarkSubmitted($idSemester,$idProgram,$idSubject,$IdMarksDistributionMaster,$idGroup,$formData=null){	
	
		$db = Zend_Db_Table::getDefaultAdapter();	
		
		//list all student dalam course group
		$select = $db ->select()
					->from(array('gsm'=>'tbl_course_group_student_mapping'))
					->join(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = gsm.IdStudent',array('IdStudentRegistration','registrationId'))
					->join(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration',array('IdStudentRegSubjects'))
					->join(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication',array('appl_fname','appl_mname','appl_lname'))
					->where('sr.IdProgram = ?',$idProgram)					
					->where('gsm.IdCourseTaggingGroup = ?',$idGroup)
					->where('srs.IdSubject = ?',$idSubject)
					->order('sr.registrationId');
		
	 	if(isset($formData)){
		 	
		 	if(isset($formData["student_name"]) && $formData["student_name"]!=''){
		 		$select->where("sp.appl_fname  LIKE '%".$formData["student_name"]."%'");
		 		$select->orwhere("sp.appl_mname  LIKE '%".$formData["student_name"]."%'");
		 		$select->orwhere("sp.appl_lname  LIKE '%".$formData["student_name"]."%'");
		 	}
		 	
		    if(isset($formData["student_id"]) && $formData["student_id"]!=''){
		 		$select->where("sr.registrationId = ?",$formData["student_id"]);
		 	}
		 }
		 		
		$rows = $db->fetchAll($select);
			
		
	
		
		//unset student yg tak attend exam		
		foreach($rows as $key=>$student){
						
			//cari exam group student ni
			$examGroupDb =new Examination_Model_DbTable_ExamGroupStudent();
			$exam_group = $examGroupDb->checkStudentGroup($student["IdStudentRegistration"],$idSubject,$idSemester);
			
			if(is_array($exam_group)){
				
				//get exam attendance
		    	$examAttendanceDb = new Examination_Model_DbTable_ExamGroupAttendance();
		    	$attendance = $examAttendanceDb->getData($exam_group["egst_group_id"],$student["IdStudentRegistration"]);
				
		    	//jika x hadir atau x ada 
		    	if($attendance["ega_status"]!='395' || !$attendance){ //Defination type=31 Hadir=395
		    		//unset($rows[$key]);		    		
		    		
		    	}else{
		    		
		    		    $rows[$key]['IdStudentMarksEntry']='';
				        $rows[$key]['TotalMarkObtained']='';
				        
					 	//check ada x mark entry
					 	$select_mark = $db->select()
					 	 				  ->from(array('sme'=>'tbl_student_marks_entry'))
					 	 				  ->where('sme.IdStudentRegistration = ?',$student["IdStudentRegistration"])
					 	 				  ->where('sme.IdMarksDistributionMaster = ?',$IdMarksDistributionMaster)
					 	 				  ->where('sme.IdStudentRegSubjects = ?',$student["IdStudentRegSubjects"])
					 	 				  ->where('sme.IdSemester = ?',$idSemester)
					 	 				  ->where('sme.MarksEntryStatus = 411');
					 	 				  
					 	$entry_list = $db->fetchRow($select_mark);	
					 	
					 
					 	if(isset($entry_list["IdStudentMarksEntry"])){
					 		
					 		$rows[$key]['MarksEntryStatus']=$entry_list["MarksEntryStatus"];					 		
					 		$rows[$key]['IdStudentMarksEntry']=$entry_list["IdStudentMarksEntry"];
				    		$rows[$key]['TotalMarkObtained']=$entry_list["TotalMarkObtained"];
				    		
					 	}else{
					 			//unset($rows[$key]);	
					 	}
		    	}
			
			}else{
				//jika tiada exam group consider student tu tak attend any exam
				//unset($rows[$key]);					
			}//end exam group exist
			
		}//end foreacch
		
			
		/*echo '<pre>';
		print_r($rows);
		echo '</pre>';*/
		
		
		return $rows;
	}
	
	
	
	//nak dapatkan student yg register subject ni dan active pada semester tersebut dan nak mapping utk omr batch upload
	public function getStudentMapping($idSemester,$idProgram,$idSubject,$IdMarksDistributionMaster,$nim){	
	
		$db = Zend_Db_Table::getDefaultAdapter();
				
		$select = $db->select()
					  ->from(array('sr'=>$this->_name),array('IdStudentRegistration','registrationId'))
					  ->join(array('sss'=>'tbl_studentsemesterstatus'),'sss.IdStudentRegistration = sr.IdStudentRegistration',array())
					  ->join(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration',array('IdStudentRegSubjects'))
					  ->joinLeft(array('sp'=>'student_profile'),'sp.appl_id=sr.idApplication',array('appl_fname','appl_mname','appl_lname'))
					  //->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('SubjectName'=>'BahasaIndonesia','SubCode'))
					 // ->joinLeft(array('sme'=>'tbl_student_marks_entry'),'sme.IdStudentRegSubjects = srs.IdStudentRegSubjects',array('IdStudentMarksEntry','TotalMarkObtained','IdMarksDistributionMaster','MarksEntryStatus'))
					  ->where('sr.profileStatus=92')  // Active (refer defination table  type=20)
					  ->where('sss.studentsemesterstatus=130')  //Register
					  ->where('srs.Active=1') //1:Register 2:Add&Drop 3:Withdraw
					  ->where('sr.IdProgram = ?',$idProgram)
					  ->where('sss.IdSemesterMain = ?',$idSemester)
					  ->where('srs.IdSubject = ?',$idSubject)	
					  ->where('sr.registrationId = ?',$nim)					 
					 // ->where("sme.MarksEntryStatus IS NULL OR sme.MarksEntryStatus = '407'") //407=>ENTRY 409=>SUBMITTED 411=>APPROVED					
					  ->order("sr.registrationId");					  
		 
		 
		 $row = $db->fetchRow($select);		 		 
		
		 
		 if(is_array($row)){
		
		 	//check ada x mark entry sebelum ni
		 	$select_mark = $db->select()
		 	 				  ->from(array('sme'=>'tbl_student_marks_entry'))
		 	 				  ->where('sme.IdStudentRegistration = ?',$row["IdStudentRegistration"])
		 	 				  ->where('sme.IdMarksDistributionMaster = ?',$IdMarksDistributionMaster)
		 	 				  ->where('sme.IdStudentRegSubjects = ?',$row["IdStudentRegSubjects"])
		 	 				  ->where('sme.IdSemester = ?',$idSemester);
		 	 				  
		   $entry_list = $db->fetchRow($select_mark);		 	
		 	
			if(isset($entry_list["IdStudentMarksEntry"]) && $entry_list["IdStudentMarksEntry"]!=''){
				$row['IdStudentMarksEntry']=$entry_list["IdStudentMarksEntry"];
		    	$row['TotalMarkObtained']=$entry_list["TotalMarkObtained"];
		    	$row['MarksEntryStatus']=$entry_list["MarksEntryStatus"];
			}else{
		 	
			 	$row['IdStudentMarksEntry']=null;
			    $row['TotalMarkObtained']=null;
			    $row['MarksEntryStatus']=null;
		    	
		 	}
			
		 }
		 		 	
		 return $row;	
	}
	
	
	//ini nak dapatkan senarai student yg register pada semester tersebut active sahaja
	public function getStudent($idSemester,$idProgram,$idSubject,$IdMarksDistributionMaster,$formData=null){	
	
				$db = Zend_Db_Table::getDefaultAdapter();
				
				$select = $db->select()
					  ->from(array('sr'=>$this->_name),array('IdStudentRegistration','registrationId'))
					  ->join(array('sss'=>'tbl_studentsemesterstatus'),'sss.IdStudentRegistration = sr.IdStudentRegistration',array())
					  ->join(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration',array('IdStudentRegSubjects'))
					  ->joinLeft(array('sp'=>'student_profile'),'sp.appl_id=sr.idApplication',array('student_name'=>"CONCAT(appl_fname,' ',appl_mname,' ',appl_lname)"))
					  //->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('SubjectName'=>'BahasaIndonesia','SubCode'))					 
					  ->where('sr.profileStatus=92')  // Active (refer defination table  type=20)
					  ->where('sss.studentsemesterstatus=130')  //Register pada semester tersebut
					  ->where('srs.Active=1') //Subject Status 1:Register 2:Add&Drop 3:Withdraw
					  ->where('sr.IdProgram = ?',$idProgram)
					  ->where('sss.IdSemesterMain = ?',$idSemester)
					  ->where('srs.IdSubject = ?',$idSubject)	
					  ->order("sr.registrationId");
					  
				 if(isset($formData)){
				 	
				 	if(isset($formData["student_name"]) && $formData["student_name"]!=''){
				 		$select->where("sp.appl_fname  LIKE '%".$formData["student_name"]."%'");
				 		$select->orwhere("sp.appl_mname  LIKE '%".$formData["student_name"]."%'");
				 		$select->orwhere("sp.appl_lname  LIKE '%".$formData["student_name"]."%'");
				 	}
				 	
				    if(isset($formData["student_id"]) && $formData["student_id"]!=''){
				 		$select->where("sr.registrationId = ?",$formData["student_id"]);
				 	}
				 }
				 
				 
				 $rows = $db->fetchAll($select);

				 return $rows;
	}
	
	
	public function getRegistrationInfo($nim){
		
		$db = Zend_Db_Table::getDefaultAdapter();
				
		$select = $db->select()
			         ->from(array('sr'=>$this->_name))
			         ->where('sr.registrationId = ?',$nim);
			  
		$row = $db->fetchRow($select);

		return $row;	  
	}
	
	
	public function getAllStudentRegisterSubject($idSemester,$idSubject,$formData=null){	
	
				$db = Zend_Db_Table::getDefaultAdapter();
				
				$select = $db->select()
					  ->from(array('sr'=>$this->_name),array('IdStudentRegistration','registrationId','IdProgram'))
					  ->join(array('sss'=>'tbl_studentsemesterstatus'),'sss.IdStudentRegistration = sr.IdStudentRegistration',array())
					  ->join(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration',array('IdStudentRegSubjects'))
					  ->joinLeft(array('sp'=>'student_profile'),'sp.appl_id=sr.idApplication',array('student_name'=>"CONCAT(appl_fname,' ',appl_mname,' ',appl_lname)"))
					  ->join(array('p'=>'tbl_program'),'p.IdProgram=sr.IdProgram',array('program_name'=>"CONCAT(ArabicName,' - ',ProgramCode)"))
					  //->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('SubjectName'=>'BahasaIndonesia','SubCode'))					 
					  ->where('sr.profileStatus=92')  // Active (refer defination table  type=20)
					  ->where('sss.studentsemesterstatus=130')  //Register pada semester tersebut
					  ->where('srs.Active=1') //Subject Status 1:Register 2:Add&Drop 3:Withdraw					
					  ->where('sss.IdSemesterMain = ?',$idSemester)
					  ->where('srs.IdSubject = ?',$idSubject)	
					  ->order("sr.registrationId");
					  
				 if(isset($formData)){
				 	
				 	if(isset($formData["student_name"]) && $formData["student_name"]!=''){
				 		$select->where("sp.appl_fname  LIKE '%".$formData["student_name"]."%'");
				 		$select->orwhere("sp.appl_mname  LIKE '%".$formData["student_name"]."%'");
				 		$select->orwhere("sp.appl_lname  LIKE '%".$formData["student_name"]."%'");
				 	}
				 	
				    if(isset($formData["student_id"]) && $formData["student_id"]!=''){
				 		$select->where("sr.registrationId = ?",$formData["student_id"]);
				 	}
				 }
				 
				 
				 $rows = $db->fetchAll($select);

				 return $rows;
	}
	
	public function getStudentRegisterSubject($idSemester,$idSubject,$formData=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db->select()
		->distinct()
		->from(array('sr'=>$this->_name),array('IdStudentRegistration','registrationId','IdProgram'))
		->join(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration',array('IdStudentRegSubjects'))
		->where('srs.Active=1') //Subject Status 1:Register 2:Add&Drop 3:Withdraw
		->where('srs.IdSemesterMain = ?',$idSemester)
		->where('srs.IdSubject = ?',$idSubject);
		
				
		$rows = $db->fetchAll($select);
	
		return $rows;
	}
	public function getRegistrationSubject($idSemester,$idSubject){
		
		$db = Zend_Db_Table::getDefaultAdapter();
				
		$select = $db->select()
		 		     ->from(array('srs'=>'tbl_studentregsubjects'),array('IdStudentRegSubjects'))
		 		     ->where('srs.IdSemesterMain = ?',$idSemester)
		 		     ->where('srs.IdSubject = ?',$idSubject);
	}
	
	
	
	public function getStudentAttendExambyCourseGroup($idProgram,$idGroup,$idSubject,$idSemester,$IdMarksDistributionMaster,$formData=null,$idBranch=null){
			
		$db = Zend_Db_Table::getDefaultAdapter();	
		
		//list all student dalam course group
		$select = $db ->select()
				//	->from(array('gsm'=>'tbl_course_group_student_mapping'))					
					->from(array('sr'=>'tbl_studentregistration'),array('IdStudentRegistration','registrationId'))
					->join(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration',array('IdStudentRegSubjects','mark_approveby'))
					->join(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication',array('appl_fname','appl_mname','appl_lname'))
					->where('sr.IdProgram = ?',$idProgram)					
					->where('srs.IdCourseTaggingGroup = ?',$idGroup)
					->where('srs.IdSubject = ?',$idSubject)
					->where('srs.IdSemesterMain = ?',$idSemester)
					->order('sr.registrationId');
		
		//if ($idBranch!=null) $select->where('sr.IdBranch=?',$idBranch);
	 	if(isset($formData)){
		 	
		 	if(isset($formData["student_name"]) && $formData["student_name"]!=''){
		 		$select->where("(sp.appl_fname  LIKE '%".$formData["student_name"]."%'");
		 		$select->orwhere("sp.appl_mname  LIKE '%".$formData["student_name"]."%'");
		 		$select->orwhere("sp.appl_lname  LIKE '%".$formData["student_name"]."%')");
		 	}
		 	
		    if(isset($formData["student_id"]) && $formData["student_id"]!=''){
		 		$select->where("sr.registrationId = ?",$formData["student_id"]);
		 	}
		 }
		 
		$rows = $db->fetchAll($select);
			
		
	
		
		
		foreach($rows as $key=>$student){
			
			
			//cari exam group student ni
			$examGroupDb =new Examination_Model_DbTable_ExamGroupStudent();
			$exam_group = $examGroupDb->checkStudentGroup($student["IdStudentRegistration"],$idSubject,$idSemester,$IdMarksDistributionMaster);
			
			if(is_array($exam_group)){
				
				$rows[$key]['exam_group_id']=$exam_group["egst_group_id"];
								        
				//get exam attendance
		    	$examAttendanceDb = new Examination_Model_DbTable_ExamGroupAttendance();
		    	$attendance = $examAttendanceDb->getData($exam_group["egst_group_id"],$student["IdStudentRegistration"]);
				
		    	//jika x hadir atau x ada record  set status tak hadir
		    	if($attendance["ega_status"]!='395' || !$attendance){ //Defination type=31 Hadir=395
		    			
		    			$rows[$key]['attendance']='Not Attend';
		    		
		    	}else{		    		
		    		  
		    		    $rows[$key]['attendance']='Attend';
		    	}	    
			
			}else{
				//jika tiada exam group consider student tu tak attend any exam				
				$rows[$key]['exam_group_id']='';
				$rows[$key]['attendance']='No Exam Group';
			}//end exam group exist
			
			
			
			//check ada x mark entry
		 	$select_mark = $db->select()
		 	 				  ->from(array('sme'=>'tbl_student_marks_entry'))
		 	 				  ->where('sme.IdStudentRegistration = ?',$student["IdStudentRegistration"])
		 	 				  ->where('sme.IdMarksDistributionMaster = ?',$IdMarksDistributionMaster)
		 	 				  ->where('sme.IdStudentRegSubjects = ?',$student["IdStudentRegSubjects"])
		 	 				  ->where('sme.IdSemester = ?',$idSemester);					 	 				  
		 	$entry_list = $db->fetchRow($select_mark);	
		 	
		 
		 	if(isset($entry_list["IdStudentMarksEntry"]) && $entry_list["IdStudentMarksEntry"]!=''){					 		
		 		$rows[$key]['MarksEntryStatus']=$entry_list["MarksEntryStatus"];					 		
		 		$rows[$key]['IdStudentMarksEntry']=$entry_list["IdStudentMarksEntry"];
	    		$rows[$key]['TotalMarkObtained']=$entry_list["TotalMarkObtained"];
	    		$rows[$key]['FinalTotalMarkObtained']=$entry_list["FinalTotalMarkObtained"];
		 	}else{
		 		
		 		//defaultkan mark entry info null
				$rows[$key]['IdStudentMarksEntry']='';
			    $rows[$key]['TotalMarkObtained']='';
			    $rows[$key]['FinalTotalMarkObtained']='';
		        
						
		 	}
			
		}//end foreacch
		
			
		/*echo '<pre>';
		print_r($rows);
		echo '</pre>';*/
		
		
		return $rows;
	}
	
	public function getStudentAttendExambyCourseGroupAllComponent($idProgram,$idGroup,$idSubject,$idSemester,$component,$formData=null,$idBranch=null){
			
		$db = Zend_Db_Table::getDefaultAdapter();
	
		//list all student dalam course group
		$select = $db ->select()
		//->from(array('gsm'=>'tbl_course_group_student_mapping'))
		->from(array('sr'=>'tbl_studentregistration'),array('IdStudentRegistration','registrationId'))
		->join(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration',array('IdStudentRegSubjects','mark_approveby','final_course_mark','grade_name','exam_status','grade_status'))
		->join(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication',array('appl_fname','appl_mname','appl_lname'))
		->where('sr.IdProgram = ?',$idProgram)
		->where('srs.IdCourseTaggingGroup = ?',$idGroup)
		->where('srs.IdSubject = ?',$idSubject)
		->where('srs.IdSemesterMain = ?',$idSemester)
		->order('sr.registrationId');
		
		//if ($idBranch!=null) $select->where('sr.IdBranch=?',$idBranch);
		if(isset($formData)){
	
			if(isset($formData["student_name"]) && $formData["student_name"]!=''){
				$select->where("(sp.appl_fname  LIKE '%".$formData["student_name"]."%'");
				$select->orwhere("sp.appl_mname  LIKE '%".$formData["student_name"]."%'");
				$select->orwhere("sp.appl_lname  LIKE '%".$formData["student_name"]."%')");
			}
	
			if(isset($formData["student_id"]) && $formData["student_id"]!=''){
				$select->where("sr.registrationId = ?",$formData["student_id"]);
			}
		}
		 
		$rows = $db->fetchAll($select);
			
		if (!$rows) {
			$select = $db ->select()
			//->from(array('gsm'=>'tbl_course_group_student_mapping'))
			->from(array('sr'=>'tbl_studentregistration'),array('IdStudentRegistration','registrationId'))
			->join(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration',array('IdStudentRegSubjects','mark_approveby','final_course_mark','grade_name','exam_status','grade_status'))
			->join(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication',array('appl_fname','appl_mname','appl_lname'))
			->where('sr.IdProgram = ?',$idProgram)
			->where('srs.IdCourseTaggingGroup = ?',$idGroup)
			->where('srs.IdSubject = ?',$idSubject)
			->where('srs.IdSemesterMain = ?',$idSemester)
			->order('sr.registrationId');
			
			if(isset($formData)){
			
				if(isset($formData["student_name"]) && $formData["student_name"]!=''){
					$select->where("(sp.appl_fname  LIKE '%".$formData["student_name"]."%'");
					$select->orwhere("sp.appl_mname  LIKE '%".$formData["student_name"]."%'");
					$select->orwhere("sp.appl_lname  LIKE '%".$formData["student_name"]."%')");
				}
			
				if(isset($formData["student_id"]) && $formData["student_id"]!=''){
					$select->where("sr.registrationId = ?",$formData["student_id"]);
				}
			}
				
			$rows = $db->fetchAll($select);
				
		}
	
	
	
		foreach($rows as $key=>$student){
				
			foreach ($component as $item) {
			//	echo var_dump($component);exit;
				$IdMarksDistributionMaster=$item['IdMarksDistributionMaster'];
				//cari exam group student ni
				$examGroupDb =new Examination_Model_DbTable_ExamGroupStudent();
				$exam_group = $examGroupDb->checkStudentGroup($student["IdStudentRegistration"],$idSubject,$idSemester,$IdMarksDistributionMaster);
					
				if(is_array($exam_group)){
		
					$rows[$key][$IdMarksDistributionMaster]['exam_group_id']=$exam_group["egst_group_id"];
		
					//get exam attendance
					$examAttendanceDb = new Examination_Model_DbTable_ExamGroupAttendance();
					$attendance = $examAttendanceDb->getData($exam_group["egst_group_id"],$student["IdStudentRegistration"]);
		
					//jika x hadir atau x ada record  set status tak hadir
					if($attendance["ega_status"]!='395' || !$attendance){ //Defination type=31 Hadir=395
				   
						$rows[$key][$IdMarksDistributionMaster]['attendance']='Not Attend';
		
					}else{
		
						$rows[$key][$IdMarksDistributionMaster]['attendance']='Attend';
					}
						
				}else{
					//jika tiada exam group consider student tu tak attend any exam
					$rows[$key][$IdMarksDistributionMaster]['exam_group_id']='';
					$rows[$key][$IdMarksDistributionMaster]['attendance']='No Exam Group';
				}//end exam group exist
					
					
					
				//check ada x mark entry
				$select_mark = $db->select()
				->from(array('sme'=>'tbl_student_marks_entry'))
				->where('sme.IdStudentRegistration = ?',$student["IdStudentRegistration"])
				->where('sme.IdMarksDistributionMaster = ?',$IdMarksDistributionMaster)
				->where('sme.IdStudentRegSubjects = ?',$student["IdStudentRegSubjects"])
				->where('sme.IdSemester = ?',$idSemester);
				$entry_list = $db->fetchRow($select_mark);
		
					
				if(isset($entry_list["IdStudentMarksEntry"]) && $entry_list["IdStudentMarksEntry"]!=''){
					$rows[$key][$IdMarksDistributionMaster]['MarksEntryStatus']=$entry_list["MarksEntryStatus"];
					$rows[$key][$IdMarksDistributionMaster]['IdStudentMarksEntry']=$entry_list["IdStudentMarksEntry"];
					$rows[$key][$IdMarksDistributionMaster]['TotalMarkObtained']=$entry_list["TotalMarkObtained"];
					$rows[$key][$IdMarksDistributionMaster]['FinalTotalMarkObtained']=$entry_list["FinalTotalMarkObtained"];
				}else{
					 
					//defaultkan mark entry info null
					$rows[$key][$IdMarksDistributionMaster]['IdStudentMarksEntry']='';
					$rows[$key][$IdMarksDistributionMaster]['TotalMarkObtained']='';
					$rows[$key][$IdMarksDistributionMaster]['FinalTotalMarkObtained']='';
		
		
				}
			}//end commponent
		}//end foreacch
	
	 	
		/*echo '<pre>';
			print_r($rows);
		echo '</pre>';*/
	
	
		return $rows;
	}
	
	
	public function getStudentAttendExambyCourseGroupAllComponentDetail($idProgram,$idGroup,$idSubject,$idSemester,$component,$formData=null,$idBranch=null){
			
		$db = Zend_Db_Table::getDefaultAdapter();
	
		//list all student dalam course group
		$select = $db ->select()
		//->from(array('gsm'=>'tbl_course_group_student_mapping'))
		->from(array('sr'=>'tbl_studentregistration'),array('IdStudentRegistration','registrationId'))
		->join(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration',array('IdStudentRegSubjects','mark_approveby','final_course_mark','grade_name','exam_status','grade_status'))
		->join(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication',array('appl_fname','appl_mname','appl_lname'))
		->where('sr.IdProgram = ?',$idProgram)
		->where('srs.IdCourseTaggingGroup = ?',$idGroup)
		->where('srs.IdSubject = ?',$idSubject)
		->where('srs.IdSemesterMain = ?',$idSemester)
		->order('sr.registrationId');
	
		//if ($idBranch!=null) $select->where('sr.IdBranch=?',$idBranch);
		if(isset($formData)){
	
			if(isset($formData["student_name"]) && $formData["student_name"]!=''){
				$select->where("(sp.appl_fname  LIKE '%".$formData["student_name"]."%'");
				$select->orwhere("sp.appl_mname  LIKE '%".$formData["student_name"]."%'");
				$select->orwhere("sp.appl_lname  LIKE '%".$formData["student_name"]."%')");
			}
	
			if(isset($formData["student_id"]) && $formData["student_id"]!=''){
				$select->where("sr.registrationId = ?",$formData["student_id"]);
			}
		}
			
		$rows = $db->fetchAll($select);
			
		if (!$rows) {
			$select = $db ->select()
			//->from(array('gsm'=>'tbl_course_group_student_mapping'))
			->from(array('sr'=>'tbl_studentregistration'),array('IdStudentRegistration','registrationId'))
			->join(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration',array('IdStudentRegSubjects','mark_approveby','final_course_mark','grade_name','exam_status','grade_status'))
			->join(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication',array('appl_fname','appl_mname','appl_lname'))
			->where('sr.IdProgram = ?',$idProgram)
			->where('srs.IdCourseTaggingGroup = ?',$idGroup)
			->where('srs.IdSubject = ?',$idSubject)
			->where('srs.IdSemesterMain = ?',$idSemester)
			->order('sr.registrationId');
				
			if(isset($formData)){
					
				if(isset($formData["student_name"]) && $formData["student_name"]!=''){
					$select->where("(sp.appl_fname  LIKE '%".$formData["student_name"]."%'");
					$select->orwhere("sp.appl_mname  LIKE '%".$formData["student_name"]."%'");
					$select->orwhere("sp.appl_lname  LIKE '%".$formData["student_name"]."%')");
				}
					
				if(isset($formData["student_id"]) && $formData["student_id"]!=''){
					$select->where("sr.registrationId = ?",$formData["student_id"]);
				}
			}
	
			$rows = $db->fetchAll($select);
	
		}
	
	
	
		foreach($rows as $key=>$student){
			 
			foreach ($component as $item) {
				
				$IdMarksDistributionMaster=$item['IdMarksDistributionMaster'];
				$IdMarksDistributionDetail=$item['IdMarksDistributionDetail'];
				//cari exam group student ni 
				$examGroupDb =new Examination_Model_DbTable_ExamGroupStudent();
				$exam_group = $examGroupDb->checkStudentGroup($student["IdStudentRegistration"],$idSubject,$idSemester,$IdMarksDistributionMaster);
					
				if(is_array($exam_group)){
	
					$rows[$key][$IdMarksDistributionDetail]['exam_group_id']=$exam_group["egst_group_id"];
	
					//get exam attendance
					$examAttendanceDb = new Examination_Model_DbTable_ExamGroupAttendance();
					$attendance = $examAttendanceDb->getData($exam_group["egst_group_id"],$student["IdStudentRegistration"]);
	
					//jika x hadir atau x ada record  set status tak hadir
					if($attendance["ega_status"]!='395' || !$attendance){ //Defination type=31 Hadir=395
							
						$rows[$key][$IdMarksDistributionDetail]['attendance']='Not Attend';
	
					}else{
	
						$rows[$key][$IdMarksDistributionDetail]['attendance']='Attend';
					}
	
				}else{
					//jika tiada exam group consider student tu tak attend any exam
					$rows[$key][$IdMarksDistributionDetail]['exam_group_id']='';
					$rows[$key][$IdMarksDistributionDetail]['attendance']='No Exam Group';
				}//end exam group exist
					
					
					
				//check ada x mark entry
				$select_mark = $db->select()
				->from(array('sme'=>'tbl_student_marks_entry_detail'))
				->where('sme.IdStudentRegistration = ?',$student["IdStudentRegistration"])
				->where('sme.IdMarksDistributionDetail = ?',$IdMarksDistributionDetail)
				->where('sme.IdSemester = ?',$idSemester);
				$entry_list = $db->fetchRow($select_mark);
	
					
				if(isset($entry_list["IdStudentMarksEntry"]) && $entry_list["IdStudentMarksEntry"]!=''){
					$rows[$key][$IdMarksDistributionDetail]['MarksEntryStatus']=$entry_list["MarksEntryStatus"];
					$rows[$key][$IdMarksDistributionDetail]['IdStudentMarksEntry']=$entry_list["IdStudentMarksEntry"];
					$rows[$key][$IdMarksDistributionDetail]['TotalMarkObtained']=$entry_list["TotalMarkObtained"];
					$rows[$key][$IdMarksDistributionDetail]['FinalTotalMarkObtained']=$entry_list["FinalTotalMarkObtained"];
				}else{
	
					//defaultkan mark entry info null
					$rows[$key][$IdMarksDistributionDetail]['IdStudentMarksEntry']='';
					$rows[$key][$IdMarksDistributionDetail]['TotalMarkObtained']='';
					$rows[$key][$IdMarksDistributionDetail]['FinalTotalMarkObtained']='';
	
	
				}
			}//end commponent
		}//end foreacch
	
	  
		/*echo '<pre>';
		 print_r($rows);
		echo '</pre>';*/
	
	
		return $rows;
	}
	
	
	public function getRegisterSubject($idCourseGroup,$IdStudentRegistration){
		$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
		 	 				  ->from(array('srs'=>'tbl_studentregsubjects'))
		 	 				  ->where('srs.IdCourseTaggingGroup = ?',$idCourseGroup)
		 	 				  ->where('srs.IdStudentRegistration = ?',$IdStudentRegistration); 	 				  
		 	return $row = $db->fetchRow($select);	 				 
	}
	
	
	public function getDatabyId($id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
				
		$select = $db->select()
			         ->from(array('sr'=>$this->_name))
			         ->where('sr.IdStudentRegistration = ?',$id);
			  
		$row = $db->fetchRow($select);

		return $row;	  
	}
	
	
	public function getStudentRegistration($formdata=null){
		
		
		$db = Zend_Db_Table::getDefaultAdapter();
				
		$select = $db->select()
			         ->from(array('sr'=>$this->_name))
			        // ->join(array('sss'=>'tbl_studentsemesterstatus'),'sss.IdStudentRegistration=sr.IdStudentRegistration',array('idstudentsemsterstatus','Level'))
			         ->join(array('sp'=>'student_profile'),'sp.appl_id=sr.IdApplication',array('StudentName'=>"CONCAT_WS(' ',sp.appl_fname,sp.appl_mname,sp.appl_lname)"))
					 ->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition=sr.profileStatus',array('status'=>'DefinitionDesc'));
					 //->where("sss.IdSemesterMain = ?",$formdata["IdSemester"]);
					
		if(isset($formdata)){	

			if($formdata["idIntake"]!=''){
			  $select->where("sr.IdIntake = ?",$formdata["idIntake"]);
			}
			
			if($formdata["IdProgram"]!=''){
			  $select->where("sr.IdProgram = ?",$formdata["IdProgram"]);
			}
			
			if(isset($formdata["idProfileStatus"]) && $formdata["idProfileStatus"]!=''){
			  $select->where("sr.profileStatus = ?",$formdata["idProfileStatus"]);
			}
			
			if(isset($formdata["Student"]) && $formdata["Student"]!=''){
			  $select->where("(sp.appl_fname LIKE '%".$formdata["Student"]."%'");
			   $select->orwhere("sp.appl_mname LIKE '%".$formdata["Student"]."%'");
			    $select->orwhere("sp.appl_lname LIKE '%".$formdata["Student"]."%')");
			     $select->orwhere("sr.registrationId LIKE '%".$formdata["Student"]."%'");
			}
		}	         
		$select->order('sp.appl_fname ASC');	  
		
		$row = $db->fetchAll($select);

		return $row;	  
	}
	
	public function getSemesterStatus($idStudentReg,$idSemester){
		
		$db =  Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
								->from(array('sss'=>'tbl_studentsemesterstatus')	)
								->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=sss.IdSemesterMain')	 							
								->where('sss.IdStudentRegistration = ?',$idStudentReg)
								->where('sss.IdSemesterMain = ?',$idSemester)
								->where('sss.studentsemesterstatus = 130 or studentsemesterstatus=229'); //Register & Completed
		
		return $row = $db->fetchRow($select);
	}
	
	public function getPreviousSemester($idStudentReg,$level){
		
		$db =  Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
								->from('tbl_studentsemesterstatus')								
								->where('IdStudentRegistration = ?',$idStudentReg)
								->where('Level = ?',$level)
								->where('studentsemesterstatus = 130 or studentsemesterstatus=229'); //Register & Completed
		
		return $row = $db->fetchRow($select);
		
		
	
		
	}
	
	
	public function getStudentInfo($id=0){
			
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('sr'=>$this->_name),array('sr.*','programid'=>'sr.IdProgram'))
				->join(array('ap'=>'student_profile'),'ap.appl_id=sr.IdApplication',array('appl_fname','appl_mname','appl_lname','appl_dob','appl_birth_place'))
				->join(array('p'=>'tbl_program'),'p.IdProgram=sr.IdProgram',array('ArabicName','ProgramName','ProgramCode','Departement','Dept_Bahasa','strata','StrataName','OptimalDuration','print_majoring','transcript_template'))
				->joinLeft(array('def'=>'tbl_definationms'),'def.idDefinition=p.Award',array('program_pendidikan'=>'BahasaIndonesia','program_eng'=>'description'))
				->join(array('l'=>'tbl_landscape'),'l.IdLandscape=sr.IdLandscape',array('LandscapeType','TotalCreditHours'))
				->joinLeft(array('sc'=>'tbl_scheme'),'sc.IdScheme=p.IdScheme',array('SchemeCode','SchemeName'=>'EnglishDescription','ProgramPendidikan'=>'ArabicDescription'))
				->join(array('i'=>'tbl_intake'),'i.IdIntake=sr.IdIntake',array('intake'=>'IntakeDefaultLanguage','IntakeId'))
				->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition=sr.profileStatus',array('StudentStatus'=>'DefinitionDesc'))
				->joinLeft(array('pm'=>'tbl_programmajoring'),'pm.IDProgramMajoring=sr.IDProgramMajoring',array('majoring'=>'BahasaDescription','majoring_english'=>'EnglishDescription'))
				->joinLeft(array('b'=>'tbl_branchofficevenue'),'b.IdBranch=sr.IdBranch',array('BranchName'))
				->joinLeft(array('c'=>'tbl_collegemaster'),'c.IdCollege=p.IdCollege',array('NamaKolej'=>'ArabicName','c.IdCollege','CollegeName','Add1','Phone1','Fax','Email'))
				->joinLeft(array('sm'=>'tbl_staffmaster'),'sm.IdStaff=sr.AcademicAdvisor',array('AcademicAdvisor'=>'FullName',"FrontSalutation","BackSalutation"))
				->where('sr.IdStudentRegistration = ?',$id);
				
		$row = $db->fetchRow($select);						
		
		return $row;
	}
	
	/*
	 * This function to get course registered by semester.
	 */
	public function getCourseRegisteredBySemester($registrationId,$idSemesterMain,$idBlock=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$sql = $db->select()
                        ->from(array('sr' => 'tbl_studentregistration'), array('registrationId','IdProgram','IdProgramMajoring'))  
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
	
	/*
	 * This function to get course registered by semester.
	 */
	public function getCourseRegisteredBySemesterBlock($registrationId,$idSemester,$idBlock=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		 $sql = $db->select()
                        ->from(array('sr' => 'tbl_studentregistration'), array('registrationId','IdProgram','IdProgramMajoring'))  
                        ->joinLeft(array('srs'=>'tbl_studentregsubjects'),'srs.IdStudentRegistration = sr.IdStudentRegistration')   
                        ->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=srs.IdSubject',array('SubjectName','subjectMainDefaultLanguage','BahasaIndonesia','CreditHours','SubCode'))                  
                        ->where('sr.IdStudentRegistration  = ?', $registrationId)
                        ->where("srs.IdSemesterMain = ?",$idSemester)
                        ->where("srs.subjectlandscapetype != 2")
                        //->where("srs.IdBlock = ?",$idBlock)
                        ->order("srs.BlockLevel");
                      
        $result = $db->fetchAll($sql);
        return $result;
	}
	
	
	public function getStudentLandscapeInfo($id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
				
		$select = $db->select()
			         ->from(array('sr'=>$this->_name))
			         ->join(array('l'=>'tbl_landscape'),'l.IdLandscape=sr.IdLandscape',array('IdLandscape','LandscapeType'))
			         ->where('sr.IdStudentRegistration = ?',$id);
			  
		$row = $db->fetchRow($select);

		return $row;	  
	}
	
	
	public function getStudentSemesterInfo($idStudentReg,$idSemester,$row_sem){
		$db =  Zend_Db_Table::getDefaultAdapter();
		//get Student Active Landscape
		
		$select = $db->select()->from (array('sr'=>'tbl_studentregistration'),array('IdLandscape'))
								->where('sr.IdStudentRegistration=?',$idStudentReg);
		$rLandscape = $db->fetchRow($select);
		
		//get max sem date from selected sem semester count type and academic year
		$select_max_date = $db->select()->from(array('sm'=>'tbl_semestermaster'))
					->where('sm.idacadyear = ?',$row_sem["idacadyear"] ) 
					//->where('sm.IdSemesterMaster = ?',$row_sem["IdSemesterMain"] )
					->where('sm.SemesterCountType = ?',$row_sem["SemesterCountType"] )
					->order('sm.SemesterMainStartDate DESC');
					//->limit(0,1);
		
		$row_max_date = $db->fetchRow($select_max_date);
								
		$select = $db->select()
								->from(array('sss'=>'tbl_studentsemesterstatus'),array())		
								->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=sss.IdSemesterMain',array('IdSemesterMaster'))													
								->where('sss.IdStudentRegistration = ?',$idStudentReg)															
								->where('sss.studentsemesterstatus = 130 or sss.studentsemesterstatus=229') //Register & Completed
								->where('sm.SemesterMainStartDate <= ?',$row_max_date["SemesterMainStartDate"]);
	
		//$semester = $db->fetchAll($select);
		
		
		$select_subject = $db->select()
						//	->distinct()
	 	 				  ->from(array('srs'=>'tbl_studentregsubjects'), array('subjectlandscapetype', 'IdBlock'))	 
	 	 				  ->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject =srs.IdSubject',array('IdSubject','CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category'))
	 	 				  ->join(array('l'=>'tbl_landscapesubject'),'l.IdSubject=srs.IdSubject',array())
	 	 				  ->where('srs.IdStudentRegistration = ?',$idStudentReg)
	 	 				  ->where('(srs.subjectlandscapetype =1 OR srs.subjectlandscapetype =2)')	 	 				
	 	 				  ->where('srs.Active = 1 OR srs.Active =4 OR srs.Active=5') //Status => 1:Register 4:Repeat 5:Refer
	 	 				  //->where('(srs.exam_status != "IN" OR srs.exam_status != "FR" )') //jika exam status IN & FR Credit Hour Not Count
	 	 				  ->where('srs.IdSemesterMain IN (?)',$select)
	 	 				  ->where('l.IdLandscape= ?',$rLandscape['IdLandscape'])
	 	 				  ->group('srs.IdSubject');

		$subjects = $db->fetchAll($select_subject);
		
		//echo var_dump($subjects);exit;
		if (count($subjects)==0) {
			$select_subject = $db->select()
			//->distinct()
			->from(array('srs'=>'tbl_studentregsubjects'), array('subjectlandscapetype', 'IdBlock'))
			->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject =srs.IdSubject',array('IdSubject','CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category'))
			->join(array('l'=>'tbl_landscapeblocksubject'),'l.subjectid=srs.IdSubject',array())
			->where('srs.IdStudentRegistration = ?',$idStudentReg)
			->where('(srs.subjectlandscapetype =1 OR srs.subjectlandscapetype =2)')
			->where('srs.Active = 1 OR srs.Active =4 OR srs.Active=5') //Status => 1:Register 4:Repeat 5:Refer
			//->where('(srs.exam_status != "IN" OR srs.exam_status != "FR" )') //jika exam status IN & FR Credit Hour Not Count
			->where('srs.IdSemesterMain IN (?)',$select)
			->where('l.IdLandscape= ?',$rLandscape['IdLandscape'])
			->group('srs.IdSubject');
			
			$subjects = $db->fetchAll($select_subject);
			//echo var_dump($subjects);exit;
		}
		//echo var_dump($subjects);exit;
		return $subjects; 
		
	}
	public function getStudentSubjects($idStudentReg,$idSemester,$row_sem){
		
		$db =  Zend_Db_Table::getDefaultAdapter();
		//get Student Active Landscape
	
		$select = $db->select()->from (array('sr'=>'tbl_studentregistration'),array('IdLandscape','idTranscriptProfile'))
		->where('sr.IdStudentRegistration=?',$idStudentReg);
		$rLandscape = $db->fetchRow($select);
		$idprofile=$rLandscape['idTranscriptProfile'];
		
		//get max sem date from selected sem semester count type and academic year
		$select_max_date = $db->select()->from(array('sm'=>'tbl_semestermaster'))
		->where('sm.idacadyear = ?',$row_sem["idacadyear"] )
		//->where('sm.IdSemesterMaster = ?',$row_sem["IdSemesterMain"] )
		->where('sm.SemesterCountType = ?',$row_sem["SemesterCountType"] )
		->order('sm.SemesterMainStartDate DESC');
		//->limit(0,1);
	
		$row_max_date = $db->fetchRow($select_max_date);
	
		$select = $db->select()
		->from(array('sss'=>'tbl_studentsemesterstatus'),array())
		->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=sss.IdSemesterMain',array('IdSemesterMaster'))
		->where('sss.IdStudentRegistration = ?',$idStudentReg)
		->where('sss.studentsemesterstatus = 130 or sss.studentsemesterstatus=229') //Register & Completed
		->where('sm.SemesterMainStartDate <= ?',$row_max_date["SemesterMainStartDate"]);
	
		//$semester = $db->fetchAll($select);
	
	if ($idprofile!=0) {
		$select_subject = $db->select()
		//->distinct()
		->from(array('srs'=>'tbl_studentregsubjects'), array('subjectlandscapetype', 'IdBlock'))
		->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject =srs.IdSubject',array('IdSubject','CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category'))
		->join(array('l'=>'transcript_profile_detail'),'l.IdSubject=srs.IdSubject',array())
		->where('l.idTranscriptProfile = ?',$idprofile)
		->where('srs.IdStudentRegistration = ?',$idStudentReg)
		->where('(srs.subjectlandscapetype =1 OR srs.subjectlandscapetype =2)')
		->where('srs.Active = 1 OR srs.Active =4 OR srs.Active=5') //Status => 1:Register 4:Repeat 5:Refer
		//->where('(srs.exam_status != "IN" OR srs.exam_status != "FR" )') //jika exam status IN & FR Credit Hour Not Count
		->where('srs.IdSemesterMain IN (?)',$select)
		->group('srs.IdSubject');
		$subjects = $db->fetchAll($select_subject);
		//echo var_dump($subjects);exit;
	}
	else {

		$select_subject = $db->select()
		//->distinct()
		->from(array('srs'=>'tbl_studentregsubjects'), array('subjectlandscapetype', 'IdBlock'))
		->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject =srs.IdSubject',array('IdSubject','CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category'))
		->join(array('l'=>'tbl_landscapesubject'),'l.IdSubject=srs.IdSubject',array())
		->where('srs.IdStudentRegistration = ?',$idStudentReg)
		->where('(srs.subjectlandscapetype =1 OR srs.subjectlandscapetype =2)')
		->where('srs.Active = 1 OR srs.Active =4 OR srs.Active=5') //Status => 1:Register 4:Repeat 5:Refer
		//->where('(srs.exam_status != "IN" OR srs.exam_status != "FR" )') //jika exam status IN & FR Credit Hour Not Count
		->where('srs.IdSemesterMain IN (?)',$select)
		->where('l.IdLandscape= ?',$rLandscape['IdLandscape'])
		->group('srs.IdSubject');
		
		$subjects = $db->fetchAll($select_subject);
		//echo $select_subject;exit;
		if (count($subjects)==0) {
			$select_subject = $db->select()
			//->distinct()
			->from(array('srs'=>'tbl_studentregsubjects'), array('subjectlandscapetype', 'IdBlock'))
			->join(array('s'=>'tbl_subjectmaster'),'s.IdSubject =srs.IdSubject',array('IdSubject','CreditHours','SubCode','SubjectName','NamaSubjek'=>'BahasaIndonesia','category'))
			->join(array('l'=>'tbl_landscapeblocksubject'),'l.subjectid=srs.IdSubject',array())
			->where('srs.IdStudentRegistration = ?',$idStudentReg)
			->where('(srs.subjectlandscapetype =1 OR srs.subjectlandscapetype =2)')
			->where('srs.Active = 1 OR srs.Active =4 OR srs.Active=5') //Status => 1:Register 4:Repeat 5:Refer
			//->where('(srs.exam_status != "IN" OR srs.exam_status != "FR" )') //jika exam status IN & FR Credit Hour Not Count
			->where('srs.IdSemesterMain IN (?)',$select)
			->where('l.IdLandscape= ?',$rLandscape['IdLandscape'])
			->group('srs.IdSubject');
				
			$subjects = $db->fetchAll($select_subject);
			//echo var_dump($subjects);exit;
		}
	}
		
		//echo var_dump($subjects);exit;
		return $subjects;
	
	}
	
	
	public function SearchRegisterStudent($formdata=null){
		
		
		$db = Zend_Db_Table::getDefaultAdapter();
				
		$select = $db->select()
			         ->from(array('sr'=>$this->_name))
			         ->join(array('sss'=>'tbl_studentsemesterstatus'),'sss.IdStudentRegistration=sr.IdStudentRegistration',array('idstudentsemsterstatus','IdSemesterMain'))
			         ->join(array('sp'=>'student_profile'),'sp.appl_id=sr.IdApplication',array('StudentName'=>"CONCAT_WS(' ',sp.appl_fname,sp.appl_mname,sp.appl_lname)"))
					 ->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition=sr.profileStatus',array('status'=>'DefinitionDesc'))
					 ->order('sr.registrationId');
					 					
		if(isset($formdata)){	

			if($formdata["idIntake"]!=''){
			  $select->where("sr.IdIntake = ?",$formdata["idIntake"]);
			}
			
			if($formdata["IdProgram"]!=''){
			  $select->where("sr.IdProgram = ?",$formdata["IdProgram"]);
			}
			
			if($formdata["IdSemester"]!=''){
			  $select->where("sss.IdSemesterMain = ?",$formdata["IdSemester"]);
			}
			
			if(isset($formdata["idProfileStatus"]) && $formdata["idProfileStatus"]!=''){
			  $select->where("sr.profileStatus = ?",$formdata["idProfileStatus"]);
			}
			
			if(isset($formdata["start_nim"]) && $formdata["start_nim"]!=''){			
			  $select->where("sr.registrationId BETWEEN '".$formdata["start_nim"]."' AND '".$formdata["end_nim"]."'");
			}
		}	         
			  
		
		$row = $db->fetchAll($select);

		return $row;	  
	}
	
}
	

?>