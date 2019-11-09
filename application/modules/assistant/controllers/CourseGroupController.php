<?php
class Assistant_CourseGroupController extends Zend_Controller_Action { 

	
	public function indexAction(){
	
		$this->view->title = "Assistant Course Groupping : Subject List";
		$session = new Zend_Session_Namespace('sis');
		$idRole=$session->IdRole;
		$auth = Zend_Auth::getInstance();
		$iduser=$auth->getIdentity()->id;
		//get idstaff
		$dbUser=new GeneralSetup_Model_DbTable_User();
		$user=$dbUser->fngetUserDetails($iduser);
		$idstaff=$user['IdStaff'];
		 
		 //===============
		$form = new GeneralSetup_Form_SearchCourse();
		$this->view->form = $form;
		
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				$form->populate($formData);
				$idsemester=$formData['IdSemester'];
				$idprogram=$formData['IdProgram'];
				$this->view->idsemester=$idsemester;
				$this->view->idprogram=$idprogram;
				
				
				$dbcourseGrp=new GeneralSetup_Model_DbTable_CourseGroup();
				$subjects=$dbcourseGrp->getCourseTaggingGroupList($idsemester,$idprogram);
				$subjectRegDBAss= new Assistant_Model_DbTable_Studentregsubjects();
				$courseGroupDbAss = new Assistant_Model_DbTable_CourseGroup();
				$courseGroupDb = new GeneralSetup_Model_DbTable_CourseGroup();
				$subjectRegDB = new Registration_Model_DbTable_Studentregsubjects();
					
				foreach($subjects as $key=>$subject){			
					
					//get total student register this subject
					$total_student = $subjectRegDB->getTotalRegister($subject["IdSubject"],$formData["IdSemester"]);
					$subject["total_student"] = $total_student;				
					
					//get total group creates
					$total_group = $courseGroupDb->getTotalGroupByCourse($subject["IdSubject"],$formData["IdSemester"]);
					$subject["total_group"] = $total_group;
					$subject["IdSemester"] = $formData["IdSemester"];
					
					//get total no of student has been assigned
					$total_assigned = $subjectRegDB->getTotalAssigned($subject["IdSubject"],$formData["IdSemester"]);
					$total_unassigned = $subjectRegDB->getTotalUnAssigned($subject["IdSubject"],$formData["IdSemester"]);
					$subject["total_assigned"] = $total_assigned;
					$subject["total_unassigned"] = $total_unassigned;
					
					//get total no of student in assistant
					$total_assigned = $subjectRegDBAss->getTotalAssigned($subject["IdSubject"],$formData["IdSemester"]);
					$total_unassigned = $subjectRegDBAss->getTotalUnAssigned($subject["IdSubject"],$formData["IdSemester"]);
					$subject["total_assigned_assistant"] = $total_assigned;
					$subject["total_unassigned_assistant"] = $total_unassigned;
					
					//get total group creates
					
					$total_group = $courseGroupDbAss->getTotalGroupByCourse($subject["IdSubject"],$formData["IdSemester"]);
					$subject["total_group_assistant"] = $total_group; 
					
					$subjects[$key]=$subject;
					
				 	
				}
				//echo var_dump($subjects);exit;
												
				$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($subjects));
				$paginator->setItemCountPerPage(1000);
				$paginator->setCurrentPageNumber($this->_getParam('page',1));
				
				$this->view->list_subject = $paginator;
				 
			}//end if
			
		}
		
	}
	public function copyToAction(){
	
		$auth = Zend_Auth::getInstance();
	
		if ($this->getRequest()->isPost()) {
				
			$formData = $this->getRequest()->getPost();
			
			$subjects=$formData['copyto'];
			$idsemesterfrom=$formData['copyfrom'];
			$idsemeterto=$formData['IdSemester'];
			$idprogram=$formData['programme'];
			if ($idsemesterfrom!=$idsemeterto) {
				$dbprogram=new GeneralSetup_Model_DbTable_CourseGroupProgram();
				$dbBranch=new GeneralSetup_Model_DbTable_CourseGroupBranch();
				$dbschedule=new GeneralSetup_Model_DbTable_CourseGroupSchedule();
				$dbCourse=new GeneralSetup_Model_DbTable_CourseGroup();
				foreach ($subjects as $value) {
					$idsubject=$value;
					$coursegrp=$dbCourse->getGroupListByProgramBranch($idsubject,$idsemesterfrom,$idprogram,null);
					//echo var_dump($coursegrp);exit;
					foreach ($coursegrp as $course) {;
						//course group
						$data_upd["GroupCode"] = $course['GroupCode'];
						$data_upd["GroupName"] = $course["GroupName"];
						$data_upd["IdLecturer"]  = $course["IdLecturer"];
						$data_upd["VerifyBy"]  = $course["VerifyBy"];
						$data_upd["maxstud"]  = $course["maxstud"];
						$data_upd["remark"]  = $course["remark"];
						$data_upd["IdSemester"]  = $idsemeterto;
						$data_upd["IdSubject"]  = $course["IdSubject"];
						$data_upd["IdUniversity"]  = $course["IdUniversity"];
						$data_upd["attendance_mode"]  = $course["attendance_mode"];
						$data_upd["UpdUser"]  = $auth->getIdentity()->id;
						$data_upd["UpdDate"]  = date('Y-m-d H:mm:ss');
						$grp=$dbCourse->isCourseGroup($idsemeterto, $course["IdSubject"], $course['GroupCode']);
						if (!$grp) {
								$idgrp=$dbCourse->addData($data_upd);
							//tagging program
							 
							$data=array('group_id'=>$idgrp,'program_id'=>$idprogram);
							$dbprogram->addData($data);
							//tgging branch
							if ($course['IdBranch']!='') {
								$data=array('group_id'=>$idgrp,'branch_id'=>$course['IdBranch']);
								$dbBranch->insertCourseGroupBranch($data);
							}
							//setup schedulle
							
							//get schedule
							$schedules=$dbschedule->getSchedule($course['IdCourseTaggingGroup']);
							//echo var_dump($schedules);exit;
							foreach ($schedules as $item) {
								//echo var_dump($item);exit;
								unset($item['sc_id']);
								unset($item['FullName']);
								unset($item['FirstName']);
								unset($item['SecondName']);
								unset($item['ThirdName']);
								unset($item['GroupName']);
								$item['sc_createdby']=$auth->getIdentity()->id;
								$item['sc_createddt']=date('Y-m-d H:mm:ss');
								$item['idGroup']=$idgrp; 
								$dbschedule->addData($item);
							}
						} else $dbCourse->updateData($data_upd, $grp['IdCourseTaggingGroup']);
					}
				}
			}
		}
		$this->_redirect($this->view->url(array('module'=>'generalsetup','controller'=>'course-group', 'action'=>'index','idSubject'=>$idSubject,'idSemester'=>$idSemester),'default',true));
		
	}
	
	public function createGroupAction(){
		
		$auth = Zend_Auth::getInstance(); 
		
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			//print_r($formData);
			
			if($formData["generate_group_type"]==2){ //auto create group

				for($i=1; $i<=$formData["no_of_group"]; $i++){
					
					//create group
					$data["IdSemester"] = $formData["idSemester"];
					$data["IdSubject"]  = $formData["IdSubject"];
					$data["ProgramCreator"]  = $formData["IdProgram"];
					$data["GroupCode"]  = "Group ".$i;
					$data["IdUniversity"]  = 1;
					$data["Creator"]  = $auth->getIdentity()->id;
					$data["UpdUser"]  = $auth->getIdentity()->id;
					$data["UpdDate"]  = date("Y-m-d H:i:s");
					$courseGroupDb = new Assistant_Model_DbTable_CourseGroup();
					$idGroup = $courseGroupDb->addData($data);
					
					
					/*echo '<pre>';
					print_r($data);
					echo '</pre>';*/
					
					
					if($formData["assign_student_type"]==2){ //auto assign student to group
						
						//check how many student 
						if($formData["generate_group_type"]>0){
							
							$student_per_group = abs($formData["total_student"])/abs($formData["no_of_group"]);
						    $student_per_group = ceil($student_per_group);
							
							//query student register order by registrationID
							$subjectRegDB = new Assistant_Model_DbTable_Studentregsubjects();
							$list_student = $subjectRegDB->getStudents($formData["IdSubject"],$formData["idSemester"],$student_per_group);
							
							
							foreach($list_student as $student){
								
								//Update Studenr Register Subject
								$subjectRegDB->updateData(array('IdCourseTaggingGroup'=>$idGroup),$student["IdStudentRegSubjects"]);
								
								//insert dalam student mapping
								$info["IdCourseTaggingGroup"]=$idGroup;
								$info["IdStudent"]=$student["IdStudentRegistration"];
								
								$mappingDb = new Assistant_Model_DbTable_CourseGroupStudent();
								$mappingDb->addData($info);
							}//end foreach							
							
						}//end if
						
					}//end if
				}//end for
			}//end if
			
			
			$this->_redirect($this->view->url(array('module'=>'assistant','controller'=>'course-group', 'action'=>'group-list','idSubject'=>$formData["IdSubject"],'idSemester'=>$formData["idSemester"]),'default',true));
		}//end if post
	}
	
	public function groupListAction(){
		
		$this->view->title = "Course Groupping : Group List";
		$auth = Zend_Auth::getInstance();
		$idSubject = $this->_getParam('idSubject', 0);
		$idSemester = $this->_getParam('idSemester', 0);
		$idprogram = $this->_getParam('IdProgram', 0);
		
		$this->view->idSemester = $idSemester;
		$this->view->idSubject = $idSubject;
		$this->view->idProgram = $idprogram;
		$this->view->owner=$auth->getIdentity()->id;
		//get usign student
		 
		//semester
		$semesterDb = new GeneralSetup_Model_DbTable_Semestermaster();
		$this->view->semester = $semesterDb->getData($idSemester);
		
		//get Subject Info
		$subjectDb = new GeneralSetup_Model_DbTable_Subjectmaster();
		$subject = $subjectDb->getData($idSubject);
		$this->view->subject = $subject;
				
		$courseGroupDb = new Assistant_Model_DbTable_CourseGroup();
		$groups = $courseGroupDb->getGroupList($idSubject,$idSemester);
		
		$courseGroupStudent = new Assistant_Model_DbTable_CourseGroupStudent();
			
		$dbStudreg=new Assistant_Model_DbTable_Studentregsubjects();
		$this->view->unsignedstd=count($dbStudreg->getUnTagGroupStudents($idSubject, $idSemester));
		 
		$courseProgramDb = new Assistant_Model_DbTable_CourseGroupProgram();
		$courseGroupBranchDb = new Assistant_Model_DbTable_CourseGroupBranch();
		$i=0;
		foreach($groups as $group){
			
			//get verify by name
			if($group["VerifyBy"]){
			$staffDB = new GeneralSetup_Model_DbTable_Staffmaster();
			$verifyBy = $staffDB->getData($group["VerifyBy"]);
			$group['VerifyByName']=$verifyBy["FullName"];
			}
			
			//$total_student = $courseGroupStudent->getTotalStudent($group["IdCourseTaggingGroup"]);
			$total_student = $courseGroupStudent->getTotalStudentViaSubReg($group["IdCourseTaggingGroup"]);
			$group["total_student"] = $total_student;
			
			
			//program
			$group["program"] = $courseProgramDb->getGroupData($group['IdCourseTaggingGroup']);
			//branch 
			$group["branch"] = $courseGroupBranchDb->getGroupBranchData($group['IdCourseTaggingGroup']);
			
			$groups[$i]=$group;
			
		$i++;
		}
		
		$this->view->list_groups = $groups;

	}
	
	public function deleteGroupAction(){
		
		$idGroup = $this->_getParam('idGroup', 0);
		$idSemester = $this->_getParam('idSemester', 0);
		$idSubject = $this->_getParam('idSubject', 0);
		
		if($idGroup){
			$courseGroupDb = new GeneralSetup_Model_DbTable_CourseGroup();
			$courseGroupDb->deleteData($idGroup);
		}
		
		$this->_redirect($this->view->url(array('module'=>'generalsetup','controller'=>'course-group', 'action'=>'group-list','idSubject'=>$idSubject,'idSemester'=>$idSemester),'default',true));
		
	}
	
	public function assignStudentAction(){
		
		$this->view->title = "Assign Student to Group";
		
		$idGroup = $this->_getParam('idGroup', 0);
		$idSemester = $this->_getParam('idSemester', 0);
		$idSubject = $this->_getParam('idSubject', 0);
		
		$this->view->idGroup = $idGroup;
		$this->view->idSemester = $idSemester;
		$this->view->idSubject = $idSubject;
		
		//get list student yg belum di assign ke any group
		$subjectRegDB = new Assistant_Model_DbTable_Studentregsubjects();
		$list_student = $subjectRegDB->getUnTagGroupStudents($idSubject,$idSemester);
		$this->view->list_student = $list_student;
		
		//get group info
		$courseGroupDb = new Assistant_Model_DbTable_CourseGroup();
		$group = $courseGroupDb->getInfo($idGroup);
		$this->view->group = $group;
		
		$form = new GeneralSetup_Form_SearchGroupStudent();
		$this->view->form = $form;
		
		if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
    		if(isset($formData["Search"])){
    			if(isset($formData["bil_student"])){
    				$limit = $formData["bil_student"];
    				
    			}

				$list_student = $subjectRegDB->getUnTagGroupStudents($idSubject,$idSemester,$limit);
				$this->view->list_student = $list_student;
    		}else{
	    		for($i=0; $i<count($formData["IdStudentRegistration"]); $i++){
	    			
	    			$idStudentRegistration = $formData["IdStudentRegistration"][$i];
	    			$IdStudentRegSubjects = $formData["IdStudentRegSubjects"][$idStudentRegistration];
	    			
	    			/* $data["IdCourseTaggingGroup"] = $formData["idGroup"];
	    			$data["IdStudent"] = $idStudentRegistration;
	    			
		    		$studentGroupDb = new Assistant_Model_DbTable_CourseGroupStudent();
		    		$studentGroupDb->addData($data); */
		    			    		
		    		//update student reg subject table
		    		//copy from tbl_studentregsubjetcs
		    		$subjectRegDB->copyupdateData($formData["idGroup"],$IdStudentRegSubjects);
	    		}
    		
    		$this->_redirect($this->view->url(array('module'=>'assistant','controller'=>'course-group', 'action'=>'group-list','idSubject'=>$idSubject,'idSemester'=>$idSemester,'msg'=>1),'default',true));
    		}
    	}
	}
	
	public function timeSpaceAction(){
	
		$this->view->title = "Time Space of this Group";
	
		$idGroup = $this->_getParam('IdGroup', 0);
		$idSemester = $this->_getParam('IdSemester', 0);
		$idSubject = $this->_getParam('IdSubject', 0);
	
		$this->view->idGroup = $idGroup;
		$this->view->idSemester = $idSemester;
		$this->view->idSubject = $idSubject;
	  	//get course
	  	$dbcourse=new GeneralSetup_Model_DbTable_CourseGroup();
	  	$group=$dbcourse->getInfo($idGroup);
	  	$this->view->group=$group;
	  	//get group schedule
	  	$dbSchedule=new GeneralSetup_Model_DbTable_CourseGroupSchedule();
	  	$schedule=$dbSchedule->getSchedule($idGroup);
	  	$this->view->schedule=$schedule;
	  	
	  	//get student in group
	  	$dbStdregsubject=new Registration_Model_DbTable_Studentregsubjects();
	  	$std=$dbStdregsubject->getApproveGroupStudents($idGroup);
	   
	  	$timespace=array();
	  	$grps='';
	  	foreach ($std as $value) {
	  		$stdid=$value['IdStudentRegistration'];
	  		$groupids=$dbStdregsubject->getRegisteredSubject($stdid,$idSemester);
	  		 
	  		foreach ($groupids as $grp) {
	  			$grpid=$grp['IdCourseTaggingGroup'];
		  		$grps=$grps.$grpid.',';
	  		}
	  		 	
	  	}
	  	$daystopnew='';
	  	$daystartnew='';
	  	$grps=explode(',', $grps);
	  	$grps=array_values(array_unique($grps)); 
	  	foreach ($grps as $value) {
		  		$rows=$dbSchedule->getSchedule($value);
		  		if ($rows) {
		  			
			  		foreach ($rows as $items) {
			  			//check with existing
			  			$i=date('N', strtotime($items['sc_day']));
			  			//echo var_dump($items);
			  			$daynowstart=$items['sc_start_time'];
			  			$daynowstop=$items['sc_end_time'];
			  			//echo date('Y-m-d H:i',strtotime($daynowstart)).'-'.date('Y-m-d H:i',strtotime($daynowstop));exit;
			  			if (array_key_exists($i, $timespace)) {
			  				$statusadd=0;
			  				//echo $i.'->';echo var_dump($timespace[$i]); echo '--- <br>';
				  			foreach ($timespace[$i] as $key=>$days ) {
				  				//check if class to exixting and the merge it
				  				$dayinstart=$days['sc_start_time'];
				  				$dayinstop=$days['sc_end_time'];
				  				 
				  				if (($dayinstart<=$daynowstart && $daynowstart <= $dayinstop)  && $daynowstop>=$dayinstop ) {
				  					$daystopnew=$daynowstop;
				  					$daystartnew=$dayinstart;
				  				//	echo 'stop'.date('H:i',strtotime($daynowstop)).'-'.$items['sc_day']."==<br>";
				  					$statusadd++;
				  				} else   			 
				  				if ($dayinstart<=$daynowstop && $daynowstop <= $dayinstop && $daynowstart<=$dayinstart) {
				  					$daystartnew=$daynowstart;
				  					$daystopnew=$dayinstop;
				  				//	echo 'start'.date('H:i',strtotime($daynowstart)).'-'.$items['sc_day']."==<br>";
				  					$statusadd++;
				  				} else 				  				
				  				if ($dayinstart>=$daynowstart && $daynowstop >= $dayinstop ) {
				  					$daystartnew=$daynowstart;
				  					$daystopnew=$daynowstop;
				  				//	echo 'start stop'.date('H:i',strtotime($daynowstop)).'-'.$items['sc_day']."==<br>";
				  					$statusadd++;
				  					//echo date('H:i',strtotime($dayinstart)).'-'.date('H:i',strtotime($dayinstop));exit;
				  				} else if  (($daynowstop < $dayinstart || $daynowstart > $dayinstop) && ($statusadd==0)) {
				  					$statusadd=100;
				  					//echo $i."=0==>";echo var_dump($timespace[$i]);echo '<br>';
				  					
				  				} else break;
				  				
				  				if ($statusadd>0 && $statusadd!=100)  
				  				 		$timespace[$i][$key]=array('sc_start_time'=>$daystartnew,'sc_end_time'=>$daystopnew,'sc_day'=>$items['sc_day']);
				  				 
				  			
				  			}
				  			if ($statusadd==100) {
				  				//echo $i."=1==>";echo var_dump($timespace[$i]);echo '<br>';
				  				$timespace[$i][]=array('sc_start_time'=>$daynowstart,'sc_end_time'=>$daynowstop,'day'=>$items['sc_day']);
				  				//echo $i."=2==>";echo var_dump($timespace[$i]);echo '<br>';
				  			}
			  			} else $timespace[$i][]=array('sc_start_time'=>$daynowstart,'sc_end_time'=>$daynowstop,'sc_day'=>$items['sc_day']);
			  		} 
		  		}
		  		
	  	}
	  /*	
	  	//original
	  	$original=array();
	  	foreach ($grps as $value) {
	  		$rows=$dbSchedule->getSchedule($value);
	  		if ($rows) {
	  			foreach ($rows as $items) {
	  				//check with existing
	  				$i=date('N', strtotime($items['sc_day']));
	  				$original[$i][]=array('sc_start_time'=>$items['sc_start_time'],'sc_end_time'=>$items['sc_end_time'],'day'=>$items['sc_day']);
	  			}
	  		}
	  	}
	  	*/
	  //echo var_dump($timespace); exit;
	  	//echo "----------------";
	  //	echo var_dump($original);exit;
	  	$this->view->timespace=$timespace;
	  	
	}
	
	
	public function removeStudentAction(){
		
		$this->view->title = "View & Remove Student from Group";
		
		
		$idGroup = $this->_getParam('idGroup', 0);
		$idSemester = $this->_getParam('idSemester', 0);
		$idSubject = $this->_getParam('idSubject', 0);
		
		$this->view->idGroup = $idGroup;
		$this->view->idSemester = $idSemester;
		$this->view->idSubject = $idSubject;
		
		//get list student yg dah di assign ke  group
		$subjectRegDB = new Assistant_Model_DbTable_Studentregsubjects();
		$list_student = $subjectRegDB->getTaggedGroupStudents($idGroup);		
		$this->view->list_student = $list_student;
		
		
		
		//get group info
		$courseGroupDb = new Assistant_Model_DbTable_CourseGroup();
		$group = $courseGroupDb->getInfo($idGroup);
		$this->view->group = $group;
		
		$form = new Assistant_Form_SearchGroupStudent();
		$this->view->form = $form;
		
		if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		//print_r($formData);
    		
    		for($i=0; $i<count($formData["IdStudentRegSubjects"]); $i++){
    			
    			$IdStudentRegSubjects = $formData["IdStudentRegSubjects"][$i];
    			
    			//$IdStudentRegistration = $formData["IdStudentRegistration"][$IdStudentRegSubjects];
    		 
		    	//update reg subject to remove tagging to group
		    	$subjectRegDB = new Assistant_Model_DbTable_Studentregsubjects();
		    	$subjectRegDB->deleteData($IdStudentRegSubjects);
		    	
    		}
    	
    		$this->_redirect($this->view->url(array('module'=>'assistant','controller'=>'course-group', 'action'=>'group-list','idSubject'=>$idSubject,'idSemester'=>$idSemester,'msg'=>2),'default',true));
    		
		}
		
		
	}
	
	
	public function addGroupAction(){
	  
	  $this->view->title = "Course Groupping : Add Group";
        	  
	  $idSubject = $this->_getParam('idSubject', 0);
	  $idSemester = $this->_getParam('idSemester', 0);
	  $idprogram = $this->_getParam('idProgram', 0);
	  
	  $this->view->idSemester = $idSemester;
	  $this->view->idSubject = $idSubject;
	  $this->view->idProgram = $idprogram;
	  //semester
	  $semesterDb = new GeneralSetup_Model_DbTable_Semestermaster();
	  $this->view->semester = $semesterDb->getData($idSemester);
	  
	  //get Subject Info
	  $subjectDb = new GeneralSetup_Model_DbTable_Subjectmaster();
	  $subject = $subjectDb->getData($idSubject);
	  $this->view->subject = $subject;
	  
	  //faculty list
	  $facultyDb = new App_Model_General_DbTable_Collegemaster();
	  $this->view->faculty_list = $facultyDb->getFaculty();
	  
	  //program list
	  $programDb = new App_Model_Record_DbTable_Program();
	  $program = array();
	  foreach ($this->view->faculty_list as $faculty){
	    $where = array(
	        'IdCollege = ?' => $faculty['IdCollege'],
	        'Active = ?' => 1
	        );
	    $programList = $programDb->fetchAll($where);
	    
	    $program[] = array(
	          'faculty' => $faculty,
	          'program' => $programList->toArray()
	        );
	  }
	  $this->view->program_list = $program;
	  
	  
	  //form
      $form = new Assistant_Form_CourseGroup(array('idSubject'=>$idSubject,'IdSemester'=>$idSemester,'idFaculty'=>$subject["IdFaculty"],'idProgram'=>$idprogram));
      $this->view->form = $form;
      
      if ($this->getRequest()->isPost()) {
      	
      	$formData = $this->getRequest()->getPost();
      	
      	if($form->isValid($formData)){
      	
        	$auth = Zend_Auth::getInstance();
        	
        	
        	//create group
        	$data["IdSemester"] = $formData["idSemester"];
        	$data["IdSubject"]  = $formData["IdSubject"];
        	$data["GroupName"]  = $formData["GroupName"];
        	$data["GroupCode"]  = $formData["GroupCode"];
        	$data["maxstud"]  = $formData["maxstud"];
        	$data["remark"]  = $formData["remark"];
        	$data["IdLecturer"]  = isset($formData["IdLecturer"])?$formData["IdLecturer"]:null;
        	$data["VerifyBy"]  = isset($formData["VerifyBy"])?$formData["VerifyBy"]:null;
        	$data["IdUniversity"]  = 1;
        	$data["UpdUser"]  = $auth->getIdentity()->id;
        	$data["Creator"]  = $auth->getIdentity()->id;
        	$data["UpdDate"]  = date("Y-m-d H:i:s");
        	$data["ProgramCreator"]  = $formData["IdProgram"];
        	$courseGroupDb = new Assistant_Model_DbTable_CourseGroup();
        	$idGroup = $courseGroupDb->addData($data);
        	
        	//course group program
        	if( isset($formData['program']) ){
        	  $courseGroupProgramDb = new Assistant_Model_DbTable_CourseGroupProgram();
        	  foreach($formData['program'] as $program){
        	    $dt = array(
        	          'group_id' => $idGroup,
        	          'program_id' => $program
        	        );
        	    
        	    $courseGroupProgramDb->insert($dt);
        	  }
        	}
        	
        	
        	$this->_redirect($this->view->url(array('module'=>'assistant','controller'=>'course-group', 'action'=>'group-list','idSubject'=>$formData["IdSubject"],'idSemester'=>$formData["idSemester"]),'default',true));
      	}else{
      	  $form->populate($formData);
      	  
      	}
      }
      
      $this->view->form = $form;
		
	}
	
	
	public function editGroupAction(){
		
	  $this->view->title = "Course Groupping : Edit Group";

	  
	  $idGroup = $this->_getParam('idGroup', 0);
	  $idSubject = $this->_getParam('idSubject', 0);
	  $idSemester = $this->_getParam('idSemester', 0);
	  
	  
	  $this->view->idSemester = $idSemester;
	  $this->view->idSubject = $idSubject;
	  $this->view->idGroup = $idGroup;
	  
	  
	  //semester
	  $semesterDb = new GeneralSetup_Model_DbTable_Semestermaster();
	  $this->view->semester = $semesterDb->getData($idSemester);
	  
	  //get Subject Info
	  $subjectDb = new GeneralSetup_Model_DbTable_Subjectmaster();
	  $subject = $subjectDb->getData($idSubject);
	  $this->view->subject = $subject;
	  
	  //faculty list
	  $facultyDb = new App_Model_General_DbTable_Collegemaster();
	  $this->view->faculty_list = $facultyDb->getData();
	  
	  //program list
	  $programDb = new App_Model_Record_DbTable_Program();
	  $program = array();
	  foreach ($this->view->faculty_list as $faculty){
	    $where = array(
	        'IdCollege = ?' => $faculty['IdCollege'],
	        'Active = ?' => 1
	        );
	    $programList = $programDb->fetchAll($where);
	    
	    $program[] = array(
	          'faculty' => $faculty,
	          'program' => $programList->toArray()
	        );
	  }
	  $this->view->program_list = $program;
	  //branch 
	  $programDb = new Assistant_Model_DbTable_Branchofficevenue();
	  $branch = array();
	  $branchList = $programDb->fnGetAllBranchList();
	  
	  $this->view->branch_list = $branchList;
	  
	  //group
	  $courseGroupDb = new Assistant_Model_DbTable_CourseGroup();
	  $data = $courseGroupDb->getInfo($idGroup);
	   
	  //group program
	  $courseGroupProgramDb = new Assistant_Model_DbTable_CourseGroupProgram();
	  $data_program = $courseGroupProgramDb->getGroupData($idGroup);
	  $this->view->data_program = $data_program;
	  
	  //group branch
	  //group program
	  $courseGroupProgramDb = new Assistant_Model_DbTable_CourseGroupBranch();
	  $data_program = $courseGroupProgramDb->getGroupBranchData($idGroup);
	  $this->view->data_branch = $data_program;
	  
	  //form
      $form = new Assistant_Form_CourseGroup(array('idSubject'=>$idSubject,'IdSemester'=>$idSemester,'idGroup'=>$idGroup));
      $this->view->form = $form;
		
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();

			if($form->isValid($formData)){
			  
			  
			    //course group
    			$data_upd["GroupCode"]  = $formData["GroupCode"];
    			$data_upd["GroupName"]  = $formData["GroupName"];
    			$data_upd["IdLecturer"]  = isset($formData["IdLecturer"]) && $formData["IdLecturer"]!=0?$formData["IdLecturer"]:null;
    			$data_upd["VerifyBy"]  = isset($formData["VerifyBy"]) && $formData["VerifyBy"]!=0?$formData["VerifyBy"]:null;
    			$data_upd["maxstud"]  = $formData["maxstud"];
    			$data_upd["remark"]  = $formData["remark"];
    			
    			$courseGroupDb = new Assistant_Model_DbTable_CourseGroup();
    			$courseGroupDb->updateData($data_upd, $formData["IdCourseTaggingGroup"]);
    			
    			
    			$courseGroupProgramDb = new Assistant_Model_DbTable_CourseGroupProgram();
    			//group program
    			if(isset($formData['program_remove'])){
    			  foreach ($formData['program_remove'] as $id_to_remove){
    			    $courseGroupProgramDb->delete('id = '.$id_to_remove);
    			  }
    			}
    			
    			if(isset($formData['program_add'])){
    			  foreach ($formData['program_add'] as $id_program_to_add){
    			    
    			    $dt = array(
        	          'group_id' => $formData["IdCourseTaggingGroup"],
        	          'program_id' => $id_program_to_add
        	        );
        	    
        	        $courseGroupProgramDb->insert($dt);
    			  }
    			}
    			//branch
    			//echo var_dump($formData);exit;
    			$courseGroupProgramDb = new Assistant_Model_DbTable_CourseGroupBranch();
    			//group program
    			if(isset($formData['branch_remove'])){
    				foreach ($formData['branch_remove'] as $id_to_remove){
    					$courseGroupProgramDb->delete('id = '.$id_to_remove);
    				}
    			}
    			if(isset($formData['branch_add'])){
    				foreach ($formData['branch_add'] as $id_branch_to_add){
    						
    					$dt = array(
    							'group_id' => $formData["IdCourseTaggingGroup"],
    							'branch_id' => $id_branch_to_add
    					);
    					 
    					$courseGroupProgramDb->insert($dt);
    				}
    			}
    			
    			$this->_redirect($this->view->url(array('module'=>'assistant','controller'=>'course-group', 'action'=>'group-list','idSubject'=>$formData["IdSubject"],'idSemester'=>$formData["idSemester"]),'default',true));
			}else{
			  $form->populate($formData);
			}
		}else{
			$form->populate($data);
		}
			
		$this->view->form = $form;
			
		
	}
	
	public function scheduleAction(){
		
		$this->view->title = "Schedule";
		
		setlocale(LC_TIME, 'id_ID');
				
		$idGroup = $this->_getParam('idGroup', 0);
		$idSemester = $this->_getParam('idSemester', 0);
		$idSubject = $this->_getParam('idSubject', 0);
		$msg = $this->_getParam('msg',null);
		$edit = $this->_getParam('edit', 0);
		$this->view->edit=$edit;
		$this->view->noticeMessage=$msg;
		$this->view->idGroup = $idGroup;
		$this->view->idSemester = $idSemester;
		$this->view->idSubject = $idSubject;
		
		$form = new Assistant_Form_ScheduleForm(array('idSubject'=>$idSubject,'IdSemester'=>$idSemester,'IdGroup'=>$idGroup));
		$this->view->form = $form;
		
		$groupSchdeleDb = new Assistant_Model_DbTable_CourseGroupSchedule();
		$schedule = $groupSchdeleDb->getSchedule($idGroup);
		$this->view->schedule = $schedule;		
		
		//get group info
		$courseGroupDb = new Assistant_Model_DbTable_CourseGroup();
		$group = $courseGroupDb->getInfo($idGroup);
		$this->view->group = $group;
	}
	
	public function addScheduleAction(){
	  
        if($this->_request->isXmlHttpRequest()){
          $this->_helper->layout->disableLayout();
        }
		
		$auth = Zend_Auth::getInstance();
		 
		$idGroup = $this->_getParam('idGroup', 0);
		$idSemester = $this->_getParam('idSemester', 0);
		$idSubject = $this->_getParam('idSubject', 0);
		
		 
		$form = new Assistant_Form_ScheduleForm(array('idSubject'=>$idSubject,'IdSemester'=>$idSemester,'IdGroup'=>$idGroup));
		$courseDb = new Assistant_Model_DbTable_CourseGroup();
		$scheduleDB = new Assistant_Model_DbTable_CourseGroupSchedule();
		 
		if ($this->getRequest()->isPost()) {
		  
		    $formData = $this->getRequest()->getPost();
		   // echo var_dump($formData);exit;
		    if($form->isValid($formData)){
			
		      if($formData["sc_date"]!=null){
    			$sc_date = date_create_from_format('d/m/Y', $formData["sc_date"]);
    			$data["sc_date"]=$sc_date->format('Y-m-d');
		      } else $data["sc_date"]=null;
		      
    			$data["idGroup"]=$formData["idGroup"];
    			
    			$data["sc_day"]=$formData["sc_day"];
    			$data["sc_start_time"]=$formData["sc_start_time"];
    			$data["sc_end_time"]=$formData["sc_end_time"];
    			$data["sc_venue"]=$formData["sc_venue"];
    			if(isset($formData["idCollege"]) && $formData["idCollege"]!=""&&$formData["idCollege"]!=0){
    				$data["idCollege"]=$formData["idCollege"];
    			}
    			if(isset($formData["idLecturer"]) && $formData["idLecturer"]!=0&&$formData["idLecturer"]!=""){
    				$data["IdLecturer"]=$formData["idLecturer"];
    			}
    			//$data["sc_class"]=$formData["sc_class"];
    			$data["sc_createdby"]=$auth->getIdentity()->id;
    			$data["sc_createddt"]=date("Y-m-d H:i:s");
    			
    			$data["sc_remark"]=$formData["sc_remark"];
    			$sch=array('sc_date'=>$data['sc_date'],
    					'sc_day'=>$data['sc_day'],
    					'sc_start_time'=>$data['sc_start_time'],
    					'sc_end_time'=>$data['sc_end_time'],
    					'IdCourseTaggingGroup'=>$data['idGroup']
    			);
    			//check for clash
    			//get semester
    			$status="1";
    			$rows=$courseDb->getInfoSchedulle($data["idGroup"]);
    			
    			if ($rows) {
    				//
    				$row=$rows[0];
    				 
	    				$idsemester=$row['IdSemester'];
	    				//get lecturer id
	    				//echo var_dump($sch);exit;
	    				if (  !isset($formData["idLecturer"]) || $formData["idLecturer"]=='') {
	    					//cek lecturer in Group
	    					$group=$courseDb->getInfo($row['IdCourseTaggingGroup']);
	    						if ($group['IdLecturer']>0) $idlec=$group['IdLecturer']; else $idlec=0;
	    				} else $idlec=$formData['idLecturer'];
	    				//cek bentrok
		    			$message="Tidak ada bentrok";
		    			if ($idlec>0) {
		    					$schedulles=$courseDb->getInfoSchedulleByLecturer($idsemester, $idlec);
			    				foreach ($schedulles as $value) {
			    					$schtmp=array('sc_date'=>$value['sc_date'],
			    							'sc_day'=>$value['sc_day'],
			    							'sc_start_time'=>$value['sc_start_time'],
			    							'sc_end_time'=>$value['sc_end_time'],
			    							'IdCourseTaggingGroup'=>$value['IdCourseTaggingGroup']
			    					);
			    					if ($this->isClash($schtmp, $sch)) {
			    							
			    						$klas=$courseDb->getinfo($value['IdCourseTaggingGroup']);
			    						$message='Kelas bentrok dengan '.$klas['subject_code'].'-'.$klas['subject_name'].' kelas:'.$klas['GroupName'];
			    						$status="0";
			    						break;
			    					} else $status='1';
			    				}
		    			} else {
		    					$status='1';
		    				}
		    				
		    			if ($status=="1")
		    					$message='Tidak bentrok jadwal dosen pengampu';
		    			
    			
    			}
    			else $message='Tidak bentrok karena klas ini belum disetup jadwalnya';
    			if ($status=="1") {
    				//check schedule clash
    				if ($this->isStudentClash($sch)) {
    					$status="0";
    					$message='Ada mahasiswa yang bentrok dengan penambahan jadwal ini';
    				}
    			}
    			//---------------
    			if ($status=="1")  
    				$scheduleDB->addData($data);
    			$this->_redirect($this->view->url(array('module'=>'assistant','controller'=>'course-group', 'action'=>'schedule','idSubject'=>$formData["IdSubject"],'idSemester'=>$formData["idSemester"],'idGroup'=>$formData["idGroup"],'msg'=>$message),'default',true));
    			 
    			
		    }else{
		      $form->populate($formData);
		    }
		}
		
		$this->view->form = $form;
		
		
	}
	
	
	public function editScheduleAction(){
		
        if($this->_request->isXmlHttpRequest()){
          $this->_helper->layout->disableLayout();
        }
		
		
		$this->view->title = "Edit Schedule";
		
		setlocale(LC_TIME, 'id_ID');
				
		$idGroup = $this->_getParam('idGroup', 0);
		$idSemester = $this->_getParam('idSemester', 0);
		$idSubject = $this->_getParam('idSubject', 0);
		$idSchedule = $this->_getParam('idSchedule', 0);
		$this->view->disabled = $this->_getParam('disabled', 0);
					
		//get data
		$scheduleDB = new Assistant_Model_DbTable_CourseGroupSchedule();
		$data = $scheduleDB->getData($idSchedule);
		
		$form = new Assistant_Form_ScheduleForm(array('idSubject'=>$idSubject,'IdSemester'=>$idSemester,'IdGroup'=>$idGroup,'IdSchedule'=>$idSchedule));		
		
		if ($this->getRequest()->isPost()) {
			
		     $formData = $this->getRequest()->getPost();
		    
		    if($form->isValid($formData)){
			
                if($formData["sc_date"]!=null && $formData["sc_date"]!=""){
                  $sc_date = date_create_from_format('d/m/Y', $formData["sc_date"]);
                  $data["sc_date"]=$sc_date->format('Y-m-d');
                }else{
                  $data["sc_date"]=null;
                }  
    			$data["sc_day"]=$formData["sc_day"];
    			$data["sc_start_time"]=$formData["sc_start_time"];
    			$data["sc_end_time"]=$formData["sc_end_time"];
    			$data["sc_venue"]=$formData["sc_venue"];
    			$data["sc_class"]=$formData["sc_class"];
    			$data["idLecturer"]=$formData["idLecturer"];
    			$data["sc_remark"]=$formData["sc_remark"];
    			
    			$scheduleDB->updateData($data, $idSchedule);
    			
    			$this->_redirect($this->view->url(array('module'=>'assistant','controller'=>'course-group', 'action'=>'schedule','idSubject'=>$formData["IdSubject"],'idSemester'=>$idSemester,'idGroup'=>$idGroup),'default',true));
			
			}else{
			  $form->populate($formData);
			}
		}
		if($data["sc_date"]!=null){
		  $data['sc_date'] = date('d/m/Y',strtotime($data['sc_date']));
		}
		$form->populate($data);
	 
		$this->view->form = $form;
	}
	
	
	public function deleteScheduleAction(){
		
			$idGroup = $this->_getParam('idGroup', 0);
			$idSemester = $this->_getParam('idSemester', 0);
			$idSubject = $this->_getParam('idSubject', 0);
			$idSchedule = $this->_getParam('idSchedule', 0);
		
			$scheduleDB = new Assistant_Model_DbTable_CourseGroupSchedule();
			$scheduleDB->deleteData($idSchedule);
			
			$this->_redirect($this->view->url(array('module'=>'assistant','controller'=>'course-group', 'action'=>'schedule','idSubject'=>$idSubject,'idSemester'=>$idSemester,'idGroup'=>$idGroup),'default',true));
			
	}
	
	public function attendanceListAction(){
		
		$this->_helper->layout->setLayout('preview');
		
		$this->view->title = "Daftar Hadir Mahasiswa";
		
		$idSchedule = $this->_getParam('idSchedule', 0);
		$this->view->idSchedule = $idSchedule;
	 
		//get schedule info
		$scheduleDB = new Assistant_Model_DbTable_CourseGroupSchedule();
		$schedule = $scheduleDB->getDetailsInfo($idSchedule);
		$this->view->schedule = $schedule;
		
		//get list student yg dah di assign ke  group
		$subjectRegDB = new Assistant_Model_DbTable_Studentregsubjects();
		$list_student = $subjectRegDB->getApproveGroupStudents($schedule["idGroup"]);		
		$this->view->list_student = $list_student;
		
		
	}
	
	public function attendanceListPdfAction(){
		
		$this->view->title = "Daftar Hadir Mahasiswa";
		$session = new Zend_Session_Namespace('sis');
		 
		$idSchedule = $this->_getParam('idSchedule', 0);
		
		$auth = $auth = Zend_Auth::getInstance();
		
		$iduser = $auth->getIdentity()->iduser;
		$dbStaff=new GeneralSetup_Model_DbTable_Staffmaster();
		$defDB = new App_Model_General_DbTable_Definationms();
		$dbUser=new GeneralSetup_Model_DbTable_User();
		$user=$dbUser->fngetUserDetails($iduser);
		
		$dbStaff=new GeneralSetup_Model_DbTable_Staffmaster();
		$staff=$dbStaff->getData($user['IdStaff']);
		if($session->IdRole == 311 || $session->IdRole == 298 || $session->IdRole == 579 || $session->IdRole == 605){
		//	$lstrSelect->where("a.IdCollege =?",$session->idCollege);
			$dbCollege=new GeneralSetup_Model_DbTable_Collegemaster();
			$college=$dbCollege->getFullInfoCollege($session->idCollege);
			$programname='';
			$collegename=$college['ArabicName'];
			$programphone=$college["Phone1"];
			$programadd=$college["Add1"].' '.$college["Add2"].' '.$college["CityName"].' '.$college["StateName"];
			$programemail=$college["Email"];
		}
		else if($session->IdRole == 470 || $session->IdRole == 480){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
			$dbprogram=new GeneralSetup_Model_DbTable_Program();
			$program=$dbprogram->getDataDetail($session->idDepartment);
			 
			$programname=$program['ProgramName'];
			$collegename=$program['CollegeName'];
			$programphone=$program["Phone1"];
			$programadd=$program["Address1"].' '.$program["Address2"].' '.$program["CityName"].' '.$program["StateName"];
			$programemail=$program["Email"];
		} else {
			$dbUniv=new GeneralSetup_Model_DbTable_University();
			$univ=$dbUniv->fngetUniversityDetail(1);
			$programname='';
			$collegename='';
			$programphone=$univ["Phone1"];
			$programadd=$univ["Add1"].' '.$univ["Add2"].' '.$univ["CityName"].' '.$univ["StateName"];
			$programemail=$univ["Email"];
				
		}
		 
		//get schedule info
		$scheduleDB = new Assistant_Model_DbTable_CourseGroupSchedule();
		$schedule_data = $scheduleDB->getDetailsInfo($idSchedule);
		if ($schedule_data["IdLecturer"]!=0) {
			$idstaff=$schedule_data['IdLecturer'];
			$staff=$dbStaff->getData($idstaff);
			$academic_front_salutation = $defDB->getData($staff["FrontSalutation"]);
			$academic_back_salutation  = $defDB->getData($staff["BackSalutation"]);
			$schedule_data['FullName']=$academic_front_salutation['BahasaIndonesia'].' '.$staff['FullName'].', '.$academic_back_salutation['BahasaIndonesia'];
			$schedule_data['NIK']=$staff['StaffId'];
		}
		//get list student yg dah di assign ke  group
		$subjectRegDB = new Assistant_Model_DbTable_Studentregsubjects();
		$list_student = $subjectRegDB->getApproveGroupStudents($schedule_data["idGroup"]);
		
		 
		$fieldValues = array(
				'$[NIK]'=>$schedule_data['NIK'],
				'$[DOSEN]'=>$schedule_data['FullName'],
				'$[PROGRAM]'=>$programname,
				'$[FACULTY]'=>$collegename,
				'$[ADDRESS]'=>$programadd,
				'$[PHONE]'=>$programphone,
				'$[EMAIL]'=>$programemail
		
		);
		 
		global $schedule;
		$schedule = $schedule_data;
		
		global $data;
		$data = $list_student;
		
		/*
		 * PDF Generation
		 */
		
		require_once 'dompdf_config.inc.php';
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		$html_template_path = DOCUMENT_PATH."/template/CourseGroupingAttendance.html";
		
		$html = file_get_contents($html_template_path);
		
			//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);	
		}
		 
		//exit;
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		$dompdf->render();
		
		
		$dompdf->stream("Attendance.pdf");
		exit;
	}
	
	public function ajaxGetLecturerAction(){
		
    	$idCollege = $this->_getParam('idCollege',null);
    	$idLecturer = $this->_getParam('IdLecturer',null);
    	$nama = $this->_getParam('nama',null);
     	$this->_helper->layout->disableLayout();
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	$staffDb = new GeneralSetup_Model_DbTable_Staffmaster();
	  	
	  	if($idLecturer!=null){
	  	  $staff = $staffDb->getData($idLecturer);
	  	}else if ($idLecturer!=null){
	  	  $staff = $staffDb->getAcademicStaff($idCollege);
	  	} else {
	  		$staff= $staffDb->getAcademicStaff(null,$nama);
	  	}      
	    
	  	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($staff);
		
		echo $json;
		exit();
    }
    
    public function ajaxGetStudentAction(){
    
    	$idCollege = $this->_getParam('idCollege',null); 
    	$nama = $this->_getParam('nama',null);
    	$this->_helper->layout->disableLayout();
    
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    
    	$stdDb = new Registration_Model_DbTable_Studentregistration();
    	  
    	$staff= $stdDb->SearchStudentRegistration(array('student_name'=>$nama,'IdCollege'=>$idCollege));
    	$student=array();
    	foreach ($staff as $value) {
    		$student[]=array('IdStudentRegistration'=>$value['IdStudentRegistration'],
    						'FullName'=>$value['registrationId'].' '.$value['student_name']	);
    	}
    	 
    
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    
    	$json = Zend_Json::encode($student);
    
    	echo $json;
    	exit();
    }
    
    public function ajaxGetStaffAction(){
    
    	$idCollege = $this->_getParam('idCollege',null);
    	$idLecturer = $this->_getParam('IdLecturer',null);
    	$nama = $this->_getParam('nama',null);
    	$this->_helper->layout->disableLayout();
    
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    
    	$staffDb = new GeneralSetup_Model_DbTable_Staffmaster();
    
    	if($idLecturer!=null){
    		$staff = $staffDb->getData($idLecturer);
    	}else if ($idLecturer!=null){
    		$staff = $staffDb->getStaff($idCollege);
    	} else {
    		$staff= $staffDb->getStaff(null,$nama);
    	}
    	 
    
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    
    	$json = Zend_Json::encode($staff);
    
    	echo $json;
    	exit();
    }
    
    public function ajaxGetCourseGroupAction(){
    
    	$idsubject = $this->_getParam('idsubject',null);
    	$idprogram = $this->_getParam('idprogram',null);
    	$idbranch = $this->_getParam('idbranch',null);
    	$idsemester = $this->_getParam('idsemester',null);
    	 
    	$this->_helper->layout->disableLayout();
    
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    
    	$dbcourse=new GeneralSetup_Model_DbTable_CourseGroup();
    
    	$courses=$dbcourse->getGroupListByProgramBranch($idsubject, $idsemester, $idprogram,$idbranch);
    
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    
    	$json = Zend_Json::encode($courses);
    
    	echo $json;
    	exit();
    }
    
    
    public function updateGroupDataMigrationAction(){
    	
    	$studentGroupDb = new GeneralSetup_Model_DbTable_CourseGroupStudent();
    	$subjectRegDB = new Registration_Model_DbTable_Studentregsubjects();
    	
    	$student = $subjectRegDB->getStudentWithGroup();
    	
    	foreach($student as $s){
    		
    		//cek dah ada lom?
    		$return_row = $studentGroupDb->checkStudentMappingGroup($s['IdCourseTaggingGroup'],$s["IdStudentRegistration"]);
    		
    		if(!$return_row){
		    	$data["IdCourseTaggingGroup"]=$s['IdCourseTaggingGroup'];
		    	$data["IdStudent"]=$s['IdStudentRegistration'];   	
	    	echo '<br>add  => '.$s['IdCourseTaggingGroup'].' | '.$s["IdStudentRegistration"] .'<br>';
	    	//$studentGroupDb->addData($data);
    		}else{
    			
    			echo '<br>exist<br>';
    		}
    	
    		
    	}exit;
    }
    public function isClash($schtmp,$sch) {
    	
    	if ($schtmp['IdCourseTaggingGroup']!=$sch['IdCourseTaggingGroup']) {
    		
    		if ($schtmp['sc_date']==$sch['sc_date'] && $schtmp['sc_day']==$sch['sc_day'] ) {
    			//check time clash
    			
    			if (($schtmp['sc_start_time']<=$sch['sc_start_time'] && $sch['sc_start_time'] < $schtmp['sc_end_time']) || //inner
    			 ($schtmp['sc_start_time']<=$sch['sc_end_time'] && $sch['sc_end_time'] <=$schtmp['sc_end_time'])|| //inner end
    			 ($schtmp['sc_start_time']<=$sch['sc_end_time'] && $schtmp['sc_start_time']>=$sch['sc_start_time'])|| //inner 2nd
    			 ($sch['sc_start_time']<=$schtmp['sc_end_time'] && $sch['sc_start_time']>=$schtmp['sc_start_time']))//inner star
    			  {
    				 
    				//echo var_dump($schtmp);echo '=ccc==';var_dump($sch);exit;
    				return true;
    				 
    			}
    			 
    		}
    	}
    	return false;
    }
    
    public function isStudentClash($newschedule) {
    	
    	$dbschedule=new GeneralSetup_Model_DbTable_CourseGroupSchedule();
    	$dbcourse=new GeneralSetup_Model_DbTable_CourseGroup();
    	$row=$dbcourse->isCourseGroupEmpty($newschedule['IdCourseTaggingGroup']);
    	 
    	if ($row) {
    		foreach ($row as $value) {
    			$rows[]=$value['IdStudentRegistration'];
    		}
    		$std=implode(',', $rows);
    		 
    		$class=$dbschedule->GetGroupSchedule($std);
    		if ($class) {
    			foreach ($class as $value) {
    				$sch=array('sc_date'=>$value['sc_date'],
    						'sc_day'=>$value['sc_day'],
    						'sc_start_time'=>$value['sc_start_time'],
    						'sc_end_time'=>$value['sc_end_time'],
    						'IdCourseTaggingGroup'=>$value['IdCourseTaggingGroup']
    				);
    				if ($this->isClash($newschedule, $sch)) return true;
    			}
    		}
    	} 
    	return false;
    }
    
    public function isExamClash($schtmp,$sch) {
    	if ($schtmp['IdSubject']!=$sch['IdSubject']) {
    		
    		if ($schtmp['eg_date']==$sch['eg_date']  ) {
    			//check time clash
    			//echo var_dump($schtmp);echo '===';var_dump($sch);
    			if (($schtmp['eg_start_time']<=$sch['eg_start_time'] && $sch['eg_start_time'] <$schtmp['eg_end_time']) || ($schtmp['eg_start_time']<$sch['eg_end_time'] && $sch['eg_end_time'] <=$schtmp['eg_end_time'])) {
    				//drop subject
    				return true;
    					
    			}
    		}
    	}
    	return false;
    }
	
    public function ajaxGetLecturerclashAction() {
    	
    	$groupid = $this->_getParam('IdGroup',null);
    	$idLecturer = $this->_getParam('IdLecturer',null);
    	$schid = $this->_getParam('IdSchedule',0);
    	$ori=$this->_getParam('ori',"No");
    	$sem=$this->_getParam('idSemester',0);
    	$sch=array();
    	if ($ori=='sch') {
    		$sch=array('sc_date'=>$this->_getParam('sc_date',null),
    				'sc_day'=> $this->_getParam('sc_day',null),
    				'sc_start_time'=>$this->_getParam('sc_start_time',null),
    				'sc_end_time'=> $this->_getParam('sc_end_time',null),
    				'IdCourseTaggingGroup'=>0,
    				'IdSemester'=>$sem
    		);
    	}
    	 
    	$this->_helper->layout->disableLayout();
    	
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    	
    	$staffDb = new GeneralSetup_Model_DbTable_CourseGroup();
    	//echo $ori;echo $groupid;
    	//get semester
    	$status="1";
    	if ($ori==0 && $schid!=0 ) 
    		$row=$staffDb->getInfoScheduleRow($schid);
    	else if ($ori=='sch')  
    		$row[]=$sch;
    	
    	else 
    		$row=$staffDb->getInfoSchedulle($groupid);
    		
    	 
    	//echo var_dump($row);exit;
    	if ($row) {
    		//
    		foreach ($row as $item) {
    			
	    		$idsemester=$item['IdSemester'];
	    		$sch=array('sc_date'=>$item['sc_date'], 
	    				'sc_day'=>$item['sc_day'],
	    				'sc_start_time'=>$item['sc_start_time'],
	    				'sc_end_time'=>$item['sc_end_time'],
	    				'IdCourseTaggingGroup'=>$item['IdCourseTaggingGroup']
	    				);
	    		//echo var_dump($sch);exit;
	    		$schedulles=$staffDb->getInfoSchedulleByLecturer($idsemester, $idLecturer);
	    		$message="Tidak ada bentrok";
	    		
	    		foreach ($schedulles as $value) {
	    			$schtmp=array('sc_date'=>$value['sc_date'],
	    					'sc_day'=>$value['sc_day'],
	    					'sc_start_time'=>$value['sc_start_time'],
	    					'sc_end_time'=>$value['sc_end_time'],
	    					'IdCourseTaggingGroup'=>$value['IdCourseTaggingGroup']
	    			);
	    			if ($this->isClash($schtmp, $sch)) {
	    				 
	    				$klas=$staffDb->getinfo($value['IdCourseTaggingGroup']);
	    				$message='Kelas bentrok dengan '.$klas['subject_code'].'-'.$klas['subject_name'].' kelas:'.$klas['GroupName'];
	    				$status="0";
	    				break;
	    			} else $status='1';
	    		}
	    		 
	    		if ($status=="1") 
	    			$message='Tidak bentrok jadwal dosen pengampu';
	    		else break;
    		}
    	}
    	else $message='Tidak bentrok karena klas ini belum disetup jadwalnya';
    	
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    	
    	$data['status']=$status;
    	$data['list']=$schedulles;
    	$data['message']=$message;
    	
    	$json = Zend_Json::encode($data);
    	
    	echo $json;
    	exit();
    }
    
    public function ajaxCheckContraintAction(){
    
    	$groupid = $this->_getParam('IdGroup',null);
    	//$idLecturer = $this->_getParam('IdLecturer',null);
    	//$schid = $this->_getParam('IdSchedule',0);
    	//$ori=$this->_getParam('ori',"No");
    	 
    	$this->_helper->layout->disableLayout();
    
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    
    	$courseDb = new GeneralSetup_Model_DbTable_CourseGroup();
    	$message='';
    	if (!$courseDb->getInfoCourseRegistration($groupid)) {
    		$status="0";
    	} else {
    		$message='karena kelas telah ada mahasiswanya, hapus dulu mahasiswanya';
    		$status="1";
    	}
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    	$data['status']=$status;
    	$data['message']=$message;
    	$data['list']=array();
    	$json = Zend_Json::encode($data);
    
    	echo $json;
    	exit();
    }
    
    public function taggingExtraCourseAction() {
    
    	//title
    	$session = new Zend_Session_Namespace('sis');
    	$this->view->role=$session->IdRole;
    	$this->view->title= $this->view->translate("Tagging Extra Courses Per Faculty");
    		
    	$msg = $this->_getParam('msg', null);
    	if( $msg!=null ){
    		$this->view->noticeMessage = $msg;
    	}
    
    	$semid = $this->_getParam('semid', null);
    	$this->view->semid = $semid;
    
    	$facid = $this->_getParam('facid', null);
    	$this->view->facid = $facid;
    	$examtype = $this->_getParam('examtype', null);
    	$this->view->default_examtype = $examtype;
    
    	if ($this->getRequest()->isPost()) {
    
    		$formData = $this->getRequest()->getPost();
    		$dbcourssched=new GeneralSetup_Model_DbTable_CourseGroupSchedule();
    		if (isset($formData['save'])) {
    			$cek=$formData['cek'];
    			$extra=$formData['extra'];
    			 
    			foreach ($cek as $key=>$value) {
    				if ($value=="0") {
    					//update to be extra ("1")
    					//echo 'update 1';exit;
    					$dbcourssched->updateData(array('Extra'=>"1"), $key);
    				}
    			}
    			foreach ($extra as $key=>$value) {
    				if (!array_key_exists($key, $cek) && $value=="1") {
    					//update to be wajib ("0");
    					//echo 'update 0';exit;
    					$dbcourssched->updateData(array('Extra'=>"0"), $key);
    				}
    			}
    		}  
	    		$idsemester=$formData['IdSemester']; 
	    		$idprogram=$formData['IdProgram'];
	    		$idcollege=$formData['IdCollege'];
	    		$dbExam = new Examination_Model_DbTable_ExamGroup();
	    		$dbStaff=new GeneralSetup_Model_DbTable_Staffmaster();
	    		$dbcourse=new GeneralSetup_Model_DbTable_CourseGroup();
	    		$courses=$dbcourse->getGroupListByProgram( $idsemester, $idprogram,$idcollege);//echo var_dump($exams);exit;
	    		$coursesresult=array();
	    		foreach ($courses as $key=>$item) {
	    			$courseid=$item['IdCourseTaggingGroup'];
	    			$nstd=$dbcourse->getCountStudentByGroup($courseid);
	    			$courses[$key]['nstd']=$nstd;
	    			 
	    			//get course
	    			$courses[$key]['FullName']=$dbStaff->getStaffFullName($item['IdLecturer']);
	    		}
	    
	    		$this->view->semid = $idsemester;
	    		$this->view->programid = $idprogram;
	    		$this->view->facid = $idcollege; 
	    		$this->view->examlist=$courses;
	    	 
    
    	}
     
    	//semester
    	$semesterDB = new GeneralSetup_Model_DbTable_Semestermaster();
    	$semesterList = $semesterDB->fnGetExamSemestermasterList();
    	$this->view->semester_list = $semesterList;
    
    	//faculty
    	$collegeDb = new GeneralSetup_Model_DbTable_Collegemaster();
    	$collegelist=$collegeDb->fnGetListofCollege();
    	//echo var_dump($collegelist);exit;
    	$this->view->college_list = $collegelist;
    	$collegeDb = new GeneralSetup_Model_DbTable_Program();
    
    	$programlist=$collegeDb->getData();
    	$this->view->program_list = $programlist;
    
    
    }
    
}
?>