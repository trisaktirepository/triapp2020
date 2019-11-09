<?php

class ParentPortalController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function indexAction()
    {
    	
    	//survey
    	
    	$this->view->title = $this->view->translate("Academic Information");
        
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();    	
    
    	//print_r($auth->getIdentity());
    	
    	$appl_id = $auth->getIdentity()->appl_id; 
    	$registration_id = $auth->getIdentity()->registration_id;    

    	$this->view->appl_id = $appl_id;
    	$this->view->IdStudentRegistration = $registration_id;
    	
    	$studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	
        //print_r($applicant);
        
        //To get Student Academic Info        
        $studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
        $student = $studentRegDB->getStudentInfo($registration_id);
    	$this->view->student = $student;
  	
    	 //To get Registered Courses   
         $landscapeDb = new App_Model_Record_DbTable_Landscape();
         $landscape = $landscapeDb->getData($student["IdLandscape"]);
         $this->view->landscape = $landscape;
         
         if($landscape["LandscapeType"]==43 || $landscape["LandscapeType"]==44) {//Semester Based         	
         	
         	//get total registered semester          	
         	$semester = $studentSemesterDB->getRegisteredSemester($registration_id);
         	$this->view->semester = $semester;
         	
         	//get subjects
  		
         }elseif($landscape["LandscapeType"]==45){
         	
         	//get registered blocks         	
         	$blocks = $studentSemesterDB->getRegisteredBlock($registration_id);
         	$this->view->blocks = $blocks;
         }
         
         //Semester Registration Status         
         $registered_semester = $studentSemesterDB->getRegisteredSemester($registration_id);
    	 $this->view->registered_semester = $registered_semester;
    	
    }
    
    
	public function logoutAction()
    {
        $storage = new Zend_Auth_Storage_Session();
        $storage->clear();
        
        $this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'index'),'default',true));
    }
    
    
     public function viewScheduleAction(){
    	
    	$this->view->title = "View Schedule";
    	
    	$idGroup = $this->_getParam('idGroup', 0);
    	$this->view->idGroup = $idGroup;
				
    	$groupSchdeleDb = new App_Model_Registration_DbTable_CourseGroupSchedule();
		$schedule = $groupSchdeleDb->getSchedule($idGroup);
		$this->view->schedule = $schedule;		
		
    	//get group info
		$courseGroupDb = new App_Model_Registration_DbTable_CourseGroup();
		$group = $courseGroupDb->getInfo($idGroup);
		$this->view->group = $group;
		
     }
     
 	public function printScheduleAction(){
    	
 		$this->_helper->layout->setLayout('preview');
 		
    	$this->view->title = "Class Schedule";
    	
    	$idGroup = $this->_getParam('idGroup', 0);
    	$this->view->idGroup = $idGroup;
				
    	$groupSchdeleDb = new App_Model_Registration_DbTable_CourseGroupSchedule();
		$schedule = $groupSchdeleDb->getSchedule($idGroup);
		$this->view->schedule = $schedule;		
		
    	//get group info
		$courseGroupDb = new App_Model_Registration_DbTable_CourseGroup();
		$group = $courseGroupDb->getInfo($idGroup);
		$this->view->group = $group;
		
     }
     
     
  
    
    
    public function viewKrsAction(){
    	
    	$this->view->title = "Kartu Rencana Asli";
    	
    	global $subject_list;
    	    	
    	$idstudentsemsterstatus = $this->_getParam('idstudentsemsterstatus', 0);
    	$this->view->idstudentsemsterstatus = $idstudentsemsterstatus;

		$auth = Zend_Auth::getInstance();
    	$IdStudentRegistration = $auth->getIdentity()->registration_id;
    	
    	$this->view->IdStudentRegistration = $IdStudentRegistration;
    	
    	$type = $this->_getParam('type', 0);

    	
    	//To get Student Academic Info        
        $studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
        $student = $studentRegDB->getStudentInfo($IdStudentRegistration);
    	        
        //get semester info
    	$semesterStatusDb = new App_Model_Record_DbTable_Studentsemesterstatus();
    	$semester = $semesterStatusDb->getSemesterInfo($idstudentsemsterstatus);
    	
    	//get subjects
    	$registerSubjectDB = new App_Model_Record_DbTable_StudentRegSubjects();
    	$subject_list  = $registerSubjectDB->getActiveRegisteredCourse($semester["IdSemesterMain"],$IdStudentRegistration);
    	$total_credit_hours = $registerSubjectDB->getTotalCreditHoursActiveRegisteredCourse($semester["IdSemesterMain"],$IdStudentRegistration);
		
		//get subject course group schedule
		$groupSchduleDb = new GeneralSetup_Model_DbTable_CourseGroupSchedule();
		foreach ($subject_list as $index=>$subject){
			$schedule = $groupSchduleDb->getSchedule($subject['IdCourseTaggingGroup']);
			
			if($schedule){
				$subject_list[$index]['start_sc_date'] = $schedule[0]['sc_date'];
				$subject_list[$index]['end_sc_date'] = $schedule[sizeof($schedule)-1]['sc_date'];
					
				$subject_list[$index]['GroupName'] = $schedule[0]['GroupName'];
			}
		}
    	//get info dekan faculty
    	$programDb = new App_Model_General_DbTable_Program();
    	$program = $programDb->getCollegeDean($student["IdProgram"]);
    	
    	//get info college
    	$collegedB = new GeneralSetup_Model_DbTable_Collegemaster();
        $college = $collegedB->getFullInfoCollege($student["IdCollege"]);
        	
    	//get salutation
    	$defDB = new App_Model_General_DbTable_Definationms();
    	$dean_front_salutation = $defDB->getData($program["FrontSalutation"]);
    	$dean_back_salutation  = $defDB->getData($program["BackSalutation"]);    	
    	$academic_front_salutation = $defDB->getData($student["FrontSalutation"]);
    	$academic_back_salutation  = $defDB->getData($student["BackSalutation"]);
    	    	
    	//get photo student
    	$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
    	$file = $uploadFileDb->getFile($student["transaction_id"],51);
    	    	
    	if(isset($file["pathupload"])){    		
    	if (file_exists($file["pathupload"])) { 		
    			$photo_url  = $file["pathupload"];
    		}else{
    			$photo_url  = "/var/www/html/triapp/public/images/no_image.gif";
    		}
    	}else{
    		$photo_url = "/var/www/html/triapp/public/images/no_image.gif";
    	}

    	/* ------------------------------
    	 * start create directrory folder
    	 * ------------------------------ */
    	   
		//$location_path
		$location_intake_path = DOCUMENT_PATH."/student/".$student["IdIntake"];
		
        //create directory to locate file			
		if (!is_dir($location_intake_path)) {
	    	mkdir($location_intake_path, 0775);
		}
		
		
        //$location_path
		$location_program_path = $location_intake_path."/".$student["ProgramCode"];
		
        //create directory to locate file			
		if (!is_dir($location_program_path)) {
	    	mkdir($location_program_path, 0775);
		}
		
		//output_directory_path
		$output_directory_path = $location_program_path."/".$student["registrationId"];
		
        //create directory to locate file			
		if (!is_dir($output_directory_path)) {
	    	mkdir($output_directory_path, 0775);
		}			
		
		//creating folder student
		if($student["repository"]==''){
			$studentRegDB->updateData(array('repository'=>"student/".$student["IdIntake"]."/".$student["ProgramCode"]."/".$student["registrationId"]),$IdStudentRegistration);
		}		
				
		//output filename 
		$output_filename = $student["registrationId"]."_kartu_rencana_studi";
		
		if($type==2){
			$output_filename .="_detail";
		}
		
		$output_filename .= ".pdf";
		
		//to rename output file			
		$output_file_path = $output_directory_path."/".$output_filename;
		 		
		
		/* ------------------------------
    	 * end  create directrory folder
    	 * ------------------------------ */

		//file type
		if($type==1){
			$file_type = 53;
				
			//get subjects
			$registerSubjectDB = new App_Model_Record_DbTable_StudentRegSubjects();
			$subject_list  = $registerSubjectDB->getSemesterSubjectRegistered($semester["IdSemesterMain"],$IdStudentRegistration);
			
			//get subject course group schedule
			$groupSchduleDb = new GeneralSetup_Model_DbTable_CourseGroupSchedule();
			foreach ($subject_list as $index=>$subject){
				$schedule = $groupSchduleDb->getSchedule($subject['IdCourseTaggingGroup']);
				
				if($schedule){
					$subject_list[$index]['start_sc_date'] = $schedule[0]['sc_date'];
					$subject_list[$index]['end_sc_date'] = $schedule[sizeof($schedule)-1]['sc_date'];
						
					$subject_list[$index]['schedule'] = $schedule;
				}
			}
				
		}else
		if($type==2){
			$file_type = 67;
				
			//get subjects
			$registerSubjectDB = new App_Model_Record_DbTable_StudentRegSubjects();
			$subject_list  = $registerSubjectDB->getSemesterSubjectRegistered($semester["IdSemesterMain"],$IdStudentRegistration);
		
			//get subject course group schedule
			$groupSchduleDb = new GeneralSetup_Model_DbTable_CourseGroupSchedule();
			foreach ($subject_list as $index=>$subject){
				$schedule = $groupSchduleDb->getSchedule($subject['IdCourseTaggingGroup']);
				
				if($schedule){
					$subject_list[$index]['start_sc_date'] = $schedule[0]['sc_date'];
					$subject_list[$index]['end_sc_date'] = $schedule[sizeof($schedule)-1]['sc_date'];
						
					$subject_list[$index]['schedule'] = $schedule;
				}
			}
				
		
				
		}else{
			$file_type = 53;
		}
		
		//check kalo dah generate ke sebelum ni?
		Global $kode_sandi;
    	$documentDb = new App_Model_Application_DbTable_ApplicantDocument();
    	$document = $documentDb->getData($student["transaction_id"],$file_type); 
    	
		if(isset($document["ad_id"])){			
			$kode_sandi = $document["ad_kode_sandi"];
			
			if($kode_sandi==""){
				$kode_sandi = md5($document['ad_id']);
			}
			
		}else{	
		
			//insert info file into applicant_documents	
			$fileData["ad_appl_id"]=$student["transaction_id"];
			$fileData["ad_type"]=53;
			$fileData["ad_filepath"]="student/".$student["IdIntake"]."/".$student["ProgramCode"]."/".$student["registrationId"];
			$fileData["ad_filename"]=$output_filename;
			$fileData["ad_createddt"]=date("Y-m-d H:i:s");
			$id_document = $documentDb->addData($fileData);
			$kode_sandi = md5($id_document);
			
			//update document kode sandi
			$fileKode["ad_kode_sandi"]=$kode_sandi;
	    	$documentDb->updateData($fileKode,$id_document);
		}
		
    	
		$fieldValues = array(
    	  '$[PROGRAM]'=>$student["ArabicName"],
    	  '$[FACULTY]'=>'FAKULTAS '.$student["CollegeName"],
    	  '$[NIM]'=>$student["registrationId"],
    	  '$[NAME]'=>$student["appl_fname"].' '.$student["appl_mname"].' '.$student["appl_lname"],
    	  '$[PERIODE]'=>$semester["SemesterMainName"],
    	  '$[ACADEMIC_ADVISOR]'=>$academic_front_salutation["BahasaIndonesia"].' '.$student["AcademicAdvisor"].' '.$academic_back_salutation["BahasaIndonesia"],
    	  '$[DEAN]'=>$dean_front_salutation["BahasaIndonesia"].' '.$program["FullName"].' '.$dean_back_salutation["BahasaIndonesia"],
    	  '$[PHOTO]'=>$photo_url,
    	  '$[TOTAL_SUBJECT]'=>$total_credit_hours,
    	  '$[KODE_SANDI]'=>$kode_sandi,
		  '$[ADDRESS]'=>ucwords(strtolower($college["Add1"])).' '.ucwords(strtolower($college["Add2"])).' '.ucwords(strtolower($college["CityName"])).' '.ucwords(strtolower($college["StateName"])),
		  '$[PHONE]'=>$college["Phone1"],
		  '$[EMAIL]'=>$college["Email"]
    	);
    			
		require_once 'dompdf_config.inc.php';
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		//template path
		if($type==1){//summary
			$html_template_path = DOCUMENT_PATH."/template/kartu_rencana_studi_fkg.html";
		}else
		if($type==2){//detail
			$html_template_path = DOCUMENT_PATH."/template/kartu_rencana_studi_fkg_detail.html";
		}else{//normal
			$html_template_path = DOCUMENT_PATH."/template/kartu_rencana_studi.html";
		}
		
		$html = file_get_contents($html_template_path);
			
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);	
		}
	
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		$dompdf->render();
		
		$dompdf = $dompdf->output();
		//$dompdf->stream($output_filename);						
		
		file_put_contents($output_file_path, $dompdf);
		
		$this->view->file_path = $output_file_path;
		
    }
    
    public function khsAction(){
    	
    	 $this->view->title = "";
    	 
    	 // disable layouts for this action:
        $this->_helper->layout->disableLayout();
        
    	 //get applicant profile
    	 $auth = Zend_Auth::getInstance();    	
    
    	 //print_r($auth->getIdentity());
    	
    	 $appl_id = $auth->getIdentity()->appl_id; 
    	 $registration_id = $auth->getIdentity()->registration_id;    
		 $this->view->idstudreg=$registration_id;	
    	 $this->view->appl_id = $appl_id;
    	 $this->view->IdStudentRegistration = $registration_id;
    	 
    	 //To get Student Academic Info        
         $studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
         $student = $studentRegDB->getStudentInfo($registration_id);
    	 $this->view->student = $student;
    	 
    	 //get info college
    	$collegedB = new App_Model_General_DbTable_Collegemaster();
        $college = $collegedB->getData($student["IdCollege"]);
        
        //get salutation
    	$defDB = new App_Model_General_DbTable_Definationms();
    	$academic_front_salutation = $defDB->getData($student["FrontSalutation"]);
    	$academic_back_salutation  = $defDB->getData($student["BackSalutation"]);
    	
    	$this->view->academic_advisor = $academic_front_salutation['BahasaIndonesia'].' '.$student["AcademicAdvisor"] .' '.$academic_back_salutation['BahasaIndonesia'];  	
    	 
    	 //To get Registered Courses   
         $landscapeDb = new App_Model_Record_DbTable_Landscape();
         $landscape = $landscapeDb->getData($student["IdLandscape"]);
         $this->view->landscape = $landscape;
         
         $dbPublish=new App_Model_Exam_DbTable_PublishMark();
         if($landscape["LandscapeType"]==43) {//Semester Based         	
         	
	         	//get total registered semester 
	         	$studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
	         	$semester = $studentSemesterDB->getRegisteredSemesterKHS($registration_id);
	         	
	         	foreach($semester as $key=>$sem){
				
					//get course registered  per semester
		  			$courseRegisterDb = new App_Model_Record_DbTable_StudentRegistration();
		  			$courses = $courseRegisterDb->getCourseRegisteredBySemester($registration_id,$sem["IdSemesterMain"]);
		  			//echo var_dump($courses);exit;
		  			foreach ($courses as $index=>$value) {
		  				if ($dbPublish->isAllMarkPublished($value['IdSemesterMain'], $value['IdProgram'], $value['IdSubject'], $value['IdCourseTaggingGroup'])) {
		  					$courses[$index]['publish']="1";
		  				} else $courses[$index]['publish']="0";
		  			}
		  			//echo "---";echo var_dump($courses);exit;
		  			$semester[$key]["courses"]=$courses;
		  			
		  			//get gpa and cgpa
		  			$studentGradeDb = new App_Model_Exam_DbTable_StudentGrade();
		  			$grade = $studentGradeDb->getGradebySemester($registration_id,$sem["idstudentsemsterstatus"]);
		  			$semester[$key]["sem_credithour"] = $grade["sg_univ_sem_credithour"];
		  			$semester[$key]["cum_credithour"] = $grade["sg_cum_credithour"];	  			
		  			$semester[$key]["gpa"] = $grade["sg_univ_gpa"];
		  			$semester[$key]["cgpa"] = $grade["sg_cgpa"];
		  			$semester[$key]["sks_lulus"] = $grade["sg_sem_sks_lulus"];
		  			$semester[$key]["sks_gagal"] = $grade["sg_sem_sks_gagal"];	

		  			//get publish date
		  			$publishMarkDb = new App_Model_Exam_DbTable_PublishMark();
	    			$publish = $publishMarkDb->getPublishResult($student["IdProgram"],$sem["IdSemesterMain"]);
	    			$semester[$key]["publish_date"] = $publish["pm_date"];
		  			
	         	} 
	         	//echo var_dump($semester);exit;
	         	
           		
         }elseif($landscape["LandscapeType"]==44){
         	
	         	//get registered blocks
	         	$studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
	         	$blocks = $studentSemesterDB->getRegisteredSemesterBlockKHS($registration_id);         	
				
	         	foreach($blocks as $key=>$block){
			
		         	//get course registered  by block
		  			$courseRegisterDb = new App_Model_Record_DbTable_StudentRegistration();
		  			$courses = $courseRegisterDb->getCourseRegisteredBySemesterBlock($registration_id,$block["IdSemesterMain"],$block["IdBlock"]);
		  			foreach ($courses as $index=>$value) {
		  				if ($dbPublish->isAllMarkPublished($value['IdSemesterMain'], $value['IdProgram'], $value['IdSubject'], $value['IdCourseTaggingGroup'])) {
		  					$courses[$index]['publish']="1";
		  				} else $courses[$index]['publish']="0";
		  			}
		  			 
		  			$blocks[$key]["courses"]=$courses;
		  			
		  			//get gpa and cgpa
		  			$studentGradeDb = new App_Model_Exam_DbTable_StudentGrade();
		  			$grade = $studentGradeDb->getGradebySemester($registration_id,$block["idstudentsemsterstatus"]);
		  			
					$blocks[$key]["blockname"] = '';
					if(isset($block["blockname"]))
						$blocks[$key]["blockname"] = $block["blockname"];
		  			
					$blocks[$key]["sem_credithour"] = $grade["sg_sem_credithour"];
		  			$blocks[$key]["cum_credithour"] = $grade["sg_cum_credithour"];	  			
		  			$blocks[$key]["gpa"] = $grade["sg_gpa"];
		  			$blocks[$key]["cgpa"] = $grade["sg_cgpa"];
		  			$blocks[$key]["sks_lulus"] = $grade["sg_sem_sks_lulus"];
		  			$blocks[$key]["sks_gagal"] = $grade["sg_sem_sks_gagal"];	
		  			
		  			
		  			//get publish date
		  			$publishMarkDb = new App_Model_Exam_DbTable_PublishMark();
	    			$publish = $publishMarkDb->getPublishResult($student["IdProgram"],$block["IdSemesterMain"]);
	    			$blocks[$key]["publish_date"] = $publish["pm_date"];
	    			
		  			$semester = $blocks;
		  			
	         	}
	         	
         }
         
                 
         $this->view->semester = $semester;
         
         
         //get available publish date
         $publishMarkDb = new App_Model_Exam_DbTable_PublishMark();
         $this->view->all_date_publish = $publishMarkDb->publishAvailableDate($registration_id,$student["IdProgram"],$landscape["LandscapeType"]);
         
    }
    
    
    public function viewDetailAction(){
    	
    	// disable layouts for this action:
        $this->_helper->layout->disableLayout();
        
        //get applicant profile
    	$auth = Zend_Auth::getInstance();  
    	
    	$IdStudentRegistration = $auth->getIdentity()->registration_id; 
    	$this->view->IdStudentRegistration = $IdStudentRegistration; 
    	
    	$idSemester = $this->_getParam('semester',0);    	
    	$idProgram = $this->_getParam('program', 0);    	
    	$idSubject = $this->_getParam('subject', 0);
    	
    	$this->view->idSemester = $idSemester;
    	$this->view->idProgram  = $idProgram;
    	$this->view->idSubject  = $idSubject;
    	
    	//get course info
    	$courseDb= new App_Model_Record_DbTable_SubjectMaster();
    	$this->view->subject = $courseDb->getData($idSubject);
    	
    	//get semester info
    	$semesterDb = new App_Model_Record_DbTable_SemesterMain();
    	$this->view->semester = $semesterDb->getData($idSemester);
    	
    	//keluarkan mark distribution component
    	$MarkDistributionDB = new App_Model_Exam_DbTable_MarkDistribution();
    	$component = $MarkDistributionDB->getListMainComponent($idSemester,$idProgram,$idSubject);
    	
    	//get student course group
    	$courseGroupDB = new App_Model_Registration_DbTable_CourseGroup();
    	$group = $courseGroupDB->getStudentCourseGroup($IdStudentRegistration,$idSemester,$idSubject);
    	$grup=$group['IdCourseTaggingGroup'];
    	if(count($component)>0){
	    	foreach($component as $index=>$comp){
	    		
	    		$subjectMarkDB = new App_Model_Exam_DbTable_StudentMarkEntry();
	    		$subject = $subjectMarkDB->getSubjectMark($idSemester,$IdStudentRegistration,$idSubject,$comp["IdMarksDistributionMaster"]);
	    		$component[$index]["TotalMarkObtained"]=$subject["TotalMarkObtained"];
	    		$component[$index]["FinalTotalMarkObtained"]=$subject["FinalTotalMarkObtained"];
	    		
	    		$publishMarkDb = new App_Model_Exam_DbTable_PublishMark();
	    		$publish = $publishMarkDb->getData($idProgram,$idSemester,$idSubject,$group["IdCourseTaggingGroup"],$comp["IdMarksDistributionMaster"],1);
	    		$component[$index]["publish_date"]=$publish["pm_date"];
	    		
	    		//appeal status
	    		$appealDB = new App_Model_Exam_DbTable_Appeal();
	    		$appeal = $appealDB->getInfo($IdStudentRegistration,$idSemester,$idSubject,$comp["IdMarksDistributionMaster"]);	    		
	    		$component[$index]["appeal"]=$appeal;
	    		
	    		//resit status
	    		$resitDb = new App_Model_Exam_DbTable_Resit();
				$resit = $resitDb->getInfo($IdStudentRegistration,$idSemester,$idSubject,$comp["IdMarksDistributionMaster"]);
				$component[$index]["resit"]=$resit;	
	    	
	    	}
    	}
    	$grpAttDtlDb = new App_Model_Exam_DbTable_CourseGroupStudentAttendanceDetail();
    	$attendance['Attend']=$grpAttDtlDb->getAttendanceStatusCount($grup,$IdStudentRegistration,array(395));
    	$attendance['Absent']=$grpAttDtlDb->getAttendanceStatusCount($grup,$IdStudentRegistration,array(398));
    	$attendance['Others']=$grpAttDtlDb->getAttendanceStatusCount($grup,$IdStudentRegistration,array(396,397));
    	$attendance['Lecture']=$grpAttDtlDb->getAttendanceSessionCount($grup,$IdStudentRegistration);
    	if ($attendance['Lecture']>0) 
    		$attendance['Percentage']=(($attendance['Attend']+$attendance['Others'])/$attendance['Lecture']*100);
    	else 
    		$attendance['Percentage']=0;
    	
    	$attendance['grup']=$grup;
    	$attendance['idstd']=$IdStudentRegistration;
    	$attendance['idsubject']=$idSubject;
    	
    	
    	$component['Attendance']=$attendance;
    	
    	
    	$this->view->component = $component;
    }
   
    
      public function resultAction(){
    	
    	$this->view->title = "Examination Result";
    	
      }
      
    
      
     public function cetakKhsAction(){
    	
    	$this->view->title = "Kartu Hasil Studi";
    	 

    	global $semester;
    	global $courses;
    	
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();   
    	 
    	$registration_id = $auth->getIdentity()->registration_id;
    	$this->view->IdStudentRegistration = $registration_id;
    	
    	$idSemesterStatus = $this->_getParam('idSemesterStatus',null);    
    	 
    	//To get Student Academic Info        
        $studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
        $student = $studentRegDB->getStudentInfo($registration_id);
        
        //get info college
    	$collegedB = new GeneralSetup_Model_DbTable_Collegemaster();
        $college = $collegedB->getFullInfoCollege($student["IdCollege"]);
        
        //get salutation
    	$defDB = new App_Model_General_DbTable_Definationms();
    	$academic_front_salutation = $defDB->getData($student["FrontSalutation"]);
    	$academic_back_salutation  = $defDB->getData($student["BackSalutation"]);
    	    	 
        //get photo student
    	$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
    	$file = $uploadFileDb->getFile($student["transaction_id"],51);
    	    	
    	if(isset($file["pathupload"])){   
    		if (file_exists($file["pathupload"])) { 		
    			$photo_url  = $file["pathupload"];
    		}else{
    			$photo_url  = "/var/www/html/triapp/public/images/no_image.gif";
    		}
    	}else{
    		    $photo_url  = "/var/www/html/triapp/public/images/no_image.gif";
    	}
    	
    	$dbPublish=new App_Model_Exam_DbTable_PublishMark();	
    	 //To get Registered Courses   
         $landscapeDb = new App_Model_Record_DbTable_Landscape();
         $landscape = $landscapeDb->getData($student["IdLandscape"]);
         
         if($landscape["LandscapeType"]==43) {//Semester Based        	
         	         	
         	if(isset($idSemesterStatus)){
         		
         		    //get semester regsiter info
         		    $studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
		         	$semesterStudi = $studentSemesterDB->getSemesterInfo($idSemesterStatus);		         	
         		
         			//get course registered  per semester
		  			$courseRegisterDb = new App_Model_Record_DbTable_StudentRegistration();
		  			$courses = $courseRegisterDb->getCourseRegisteredBySemester($registration_id,$semesterStudi["IdSemesterMain"]);
		  			foreach ($courses as $index=>$value) {
		  				if ($dbPublish->isAllMarkPublished($value['IdSemesterMain'], $value['IdProgram'], $value['IdSubject'], $value['IdCourseTaggingGroup'])) {
		  					$courses[$index]['publish']="1";
		  				} else $courses[$index]['publish']="0";
		  			}
		  			$semester[0]["courses"]=$courses;		  			
		  			
		  			//get gpa and cgpa
		  			$studentGradeDb = new App_Model_Exam_DbTable_StudentGrade();
		  			$grade = $studentGradeDb->getGradebySemester($registration_id,$idSemesterStatus);
		  			$semester[0]["sem_credithour"] = $grade["sg_sem_credithour"];
		  			$semester[0]["cum_credithour"] = $grade["sg_cum_credithour"];	  			
		  			$semester[0]["gpa"] = $grade["sg_univ_gpa"];
		  			$gpa=$grade["sg_univ_gpa"];
		  			$semester[0]["cgpa"] = $grade["sg_cgpa"];	
		  			$semester[0]["sks_lulus"] = $grade["sg_sem_sks_lulus"];
		  			$semester[0]["sks_gagal"] = $grade["sg_sem_sks_gagal"];	
		  			
         	}	         	  
         	    
           		
         }elseif($landscape["LandscapeType"]==44){
         	
         	if(isset($idSemesterStatus)){
         		 
         		//get semester regsiter info
         		$studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
         		$semesterStudi = $studentSemesterDB->getSemesterInfo($idSemesterStatus);
		         	//get registered blocks
		         	$studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
		         	$blocks = $studentSemesterDB->getRegisteredSemesterBlock($registration_id,$semesterStudi["IdSemesterMain"]);         	
		         	
		         	foreach($blocks as $key=>$block){
				
			         	//get course registered  by block
			  			$courseRegisterDb = new App_Model_Record_DbTable_StudentRegistration();
			  			$courses = $courseRegisterDb->getCourseRegisteredBySemesterBlock($registration_id,$block["IdSemesterMain"],$block["IdBlock"]);
			  			foreach ($courses as $index=>$value) {
			  				if ($dbPublish->isAllMarkPublished($value['IdSemesterMain'], $value['IdProgram'], $value['IdSubject'], $value['IdCourseTaggingGroup'])) {
			  					$courses[$index]['publish']="1";
			  				} else $courses[$index]['publish']="0";
			  			}
			  			$blocks[$key]["courses"]=$courses;
			  		    $semesterStudi['SemesterMainName']=$block["SemesterMainName"];
			  			
			  			//get gpa and cgpa
			  			$studentGradeDb = new App_Model_Exam_DbTable_StudentGrade();
			  			$grade = $studentGradeDb->getGradebySemester($registration_id,$block["idstudentsemsterstatus"]);
			  			$blocks[$key]["sem_credithour"] = $grade["sg_sem_credithour"];
			  			$blocks[$key]["cum_credithour"] = $grade["sg_cum_credithour"];	  			
			  			$blocks[$key]["gpa"] = $grade["sg_univ_gpa"];
			  			$gpa=$grade["sg_univ_gpa"];
			  			$blocks[$key]["cgpa"] = $grade["sg_cgpa"];
			  			$blocks[$key]["sks_lulus"] = $grade["sg_sem_sks_lulus"];
		  			    $blocks[$key]["sks_gagal"] = $grade["sg_sem_sks_gagal"];
		  			    
		  				$semester = $blocks;	
			  			
		         	}
         	}
		         	 
         }
        
         $chlimitDB = new App_Model_Registration_DbTable_Chlimit();
         $limit=$chlimitDB->getLimit($student['IdProgram'], $student['IdIntake'], $gpa);
         
         $fieldValues = array(
    	  '$[PROGRAM]'=>$student["ArabicName"],
    	  '$[FACULTY]'=>'FAKULTAS '.$student["CollegeName"],
    	  '$[NIM]'=>$student["registrationId"],
    	  '$[NAME]'=>$student["appl_fname"].' '.$student["appl_mname"].' '.$student["appl_lname"],    	 
    	  '$[ACADEMIC_ADVISOR]'=>$academic_front_salutation["BahasaIndonesia"].' '.$student["AcademicAdvisor"].' '.$academic_back_salutation["BahasaIndonesia"],    	 
    	  '$[PHOTO]'=>$photo_url,    	 
		  '$[ADDRESS]'=>ucwords(strtolower($college["Add1"])).' '.ucwords(strtolower($college["Add2"])).' '.ucwords(strtolower($college["CityName"])).' '.ucwords(strtolower($college["StateName"])),
		  '$[PHONE]'=>$college["Phone1"],
		  '$[EMAIL]'=>$college["Email"],
          '$[SEMESTER]'=>$semesterStudi["SemesterMainName"],
    	  '$[SKS_LULUS]'=>$grade["sg_sem_sks_lulus"],
    	  '$[SKS_GAGAL]'=>$grade["sg_sem_sks_gagal"],
    	  '$[TOTAL_SKS]'=>$grade["sg_univ_sem_credithour"],
    	  '$[SKS_KUMULATIF]'=>$grade["sg_cum_credithour"],
          '$[STRATA]'=>$student["strata"],
    	  '$[IPS]'=>$grade["sg_univ_gpa"],
          '$[limit]'=>$limit,
    	  '$[IPK]'=>$grade["sg_cgpa"],
          '$[KONSENTRASI]'=>$student["majoring"],
          '$[MAJORING]'=>$student["majoring_english"],
    	);
    	
    	
    	
    	/* ------------------------------
    	 * start create directrory folder
    	 * ------------------------------ */
    	   
		//$location_path
		$location_intake_path = DOCUMENT_PATH."/student/".$student["IdIntake"];
		
        //create directory to locate file			
		if (!is_dir($location_intake_path)) {
	    	mkdir($location_intake_path, 0775);
		}
		
		
        //$location_path
		$location_program_path = $location_intake_path."/".$student["ProgramCode"];
		
        //create directory to locate file			
		if (!is_dir($location_program_path)) {
	    	mkdir($location_program_path, 0775);
		}
		
		//output_directory_path
		$output_directory_path = $location_program_path."/".$student["registrationId"];
		
        //create directory to locate file			
		if (!is_dir($output_directory_path)) {
	    	mkdir($output_directory_path, 0775);
		}			
		
		//creating folder student
		if($student["repository"]==''){
			$studentRegDB->updateData(array('repository'=>"student/".$student["IdIntake"]."/".$student["ProgramCode"]."/".$student["registrationId"]),$registration_id);
		}		
				
		//output filename 
		$output_filename = $student["registrationId"]."_kartu_hasil_studi.pdf";
		
		//to rename output file			
		$output_file_path = $output_directory_path."/".$output_filename;
		 		
		
		/* ------------------------------
    	 * end  create directrory folder
    	 * ------------------------------ */
    	
    		
    	require_once 'dompdf_config.inc.php';
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		//template path	 
		$html_template_path = DOCUMENT_PATH."/template/khs.html";
		
		$html = file_get_contents($html_template_path);			
    		
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);	
		}
	
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		$dompdf->render();
		
		//$dompdf = $dompdf->output();
		$dompdf->stream($output_filename);						
		
		file_put_contents($output_file_path, $dompdf);
		
		$this->view->file_path = $output_file_path;
		
    	
    	
     }
    
     	 public function assessmentAction(){
    	
    	 $this->view->title = "";
    	 
    	 // disable layouts for this action:
        $this->_helper->layout->disableLayout();
        
    	 //get applicant profile
    	 $auth = Zend_Auth::getInstance();    	
    
    	 //print_r($auth->getIdentity());
    	
    	 $appl_id = $auth->getIdentity()->appl_id; 
    	 $registration_id = $auth->getIdentity()->registration_id;    

    	 $this->view->appl_id = $appl_id;
    	 $this->view->IdStudentRegistration = $registration_id;
    	 
    	 //To get Student Academic Info        
         $studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
         $student = $studentRegDB->getStudentInfo($registration_id);
    	 $this->view->student = $student;
    	   	
    	 
    	 //To get Registered Courses   
         $landscapeDb = new App_Model_Record_DbTable_Landscape();
         $landscape = $landscapeDb->getData($student["IdLandscape"]);
         $this->view->landscape = $landscape;
         $semester=array();
         if($landscape["LandscapeType"]==43) {//Semester Based         	
         	
	         	//get total registered semester 
	         	$studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
	         	$semester = $studentSemesterDB->getRegisteredSemester($registration_id);
	         	
	         	foreach($semester as $key=>$sem){
				
					//get course registered  per semester
		  			$courseRegisterDb = new App_Model_Record_DbTable_StudentRegistration();
		  			$courses = $courseRegisterDb->getCourseRegisteredBySemester($registration_id,$sem["IdSemesterMain"]);
		  			$semester[$key]["courses"]=$courses;
		  						
		  			
	         	}
	         	
           		
         }elseif($landscape["LandscapeType"]==44){
         	
	         	//get registered blocks
	         	$studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
	         	$blocks = $studentSemesterDB->getRegisteredSemesterBlock($registration_id);         	
	        
	         	foreach($blocks as $key=>$block){
			
		         	//get course registered  by block
		  			$courseRegisterDb = new App_Model_Record_DbTable_StudentRegistration();
		  			$courses = $courseRegisterDb->getCourseRegisteredBySemesterBlock($registration_id,$block["IdSemesterMain"],null);
		  			$blocks[$key]["courses"]=$courses;
		  			
		  			$semester = $blocks;
		  			
	         	}
	         	
         }
         
         $this->view->semester = $semester;
         
        /* echo '<pre>';
          print_r($semester);         
         echo '<pre>';*/
    }
    
   
    
    public function appealAction(){
    	
    	 // disable layouts for this action:
         $this->_helper->layout->disableLayout();
        
    	 //get applicant profile
    	 $auth = Zend_Auth::getInstance();    	
    
    	 $appl_id = $auth->getIdentity()->appl_id; 
    	 $registration_id = $auth->getIdentity()->registration_id;    

    	 $this->view->appl_id = $appl_id;
    	 $this->view->IdStudentRegistration = $registration_id;
    	
    	 $idSemester = $this->_getParam('semester',0);
    	 $idSubject = $this->_getParam('subject', 0);
    	 $idComponent = $this->_getParam('id', 0);   
    	 
    	 $this->view->idSemester = $idSemester;
    	 $this->view->idComponent  = $idComponent;
    	 $this->view->idSubject  = $idSubject;
    	
    	 //get course info
    	 $courseDb= new App_Model_Record_DbTable_SubjectMaster();
    	 $this->view->subject = $courseDb->getData($idSubject);
    	
    	 //get semester info
    	 $semesterDb = new App_Model_Record_DbTable_SemesterMain();
    	 $this->view->semester = $semesterDb->getData($idSemester);
    	
    	 //get compinent info
    	 $MarkDistributionDB = new App_Model_Exam_DbTable_MarkDistribution();
    	 $this->view->component = $MarkDistributionDB->getComponentInfo($idComponent);
  
    }
    
     public function applyAppealAction(){
    	
     	 $auth = Zend_Auth::getInstance();    	
     
     	 // disable layouts for this action:
         $this->_helper->layout->disableLayout();
         
         	if ($this->getRequest()->isPost()) {
		
         		if ($this->getRequest()->isPost()) {
			
					$formData = $this->getRequest()->getPost();	
		
					$ajaxContext = $this->_helper->getHelper('AjaxContext');
					$ajaxContext->addActionContext('view', 'html');
					$ajaxContext->initContext();
					
					$data["sa_idStudentRegistration"]=$auth->getIdentity()->registration_id;
					$data["sa_idSubject"]=$formData["subject"];
					$data["sa_idSemester"]=$formData["semester"];
					$data["sa_idStudentRegSubject"]=1;
					$data["sa_idComponent"]=$formData["component"];
					$data["sa_applyDate"]=date("Y-m-d H:i:s");
					$data["sa_applyBy"]=$auth->getIdentity()->appl_id;
					$data["sa_status"]=1;
					
					$appealDb = new App_Model_Exam_DbTable_Appeal();
					$id = $appealDb->addData($data);
					
					$ajaxContext->addActionContext('view', 'html')
							->addActionContext('form', 'html')
							->addActionContext('process', 'json')
							->initContext();
			
					$json = Zend_Json::encode(array('id'=>$id));
				
					echo $json;
					exit();
         		}
	
         	}
     }
     
     
	public function resitAction(){
    	
    	 // disable layouts for this action:
         $this->_helper->layout->disableLayout();
        
    	 //get applicant profile
    	 $auth = Zend_Auth::getInstance();    	
    
    	 $appl_id = $auth->getIdentity()->appl_id; 
    	 $registration_id = $auth->getIdentity()->registration_id;    

    	 $this->view->appl_id = $appl_id;
    	 $this->view->IdStudentRegistration = $registration_id;
    	
    	 $idSemester = $this->_getParam('semester',0);
    	 $idSubject = $this->_getParam('subject', 0);
    	 $idComponent = $this->_getParam('id', 0);   
    	 
    	 $this->view->idSemester = $idSemester;
    	 $this->view->idComponent  = $idComponent;
    	 $this->view->idSubject  = $idSubject;
    	
    	 //get course info
    	 $courseDb= new App_Model_Record_DbTable_SubjectMaster();
    	 $this->view->subject = $courseDb->getData($idSubject);
    	
    	 //get semester info
    	 $semesterDb = new App_Model_Record_DbTable_SemesterMain();
    	 $this->view->semester = $semesterDb->getData($idSemester);
    	
    	 //get compinent info
    	 $MarkDistributionDB = new App_Model_Exam_DbTable_MarkDistribution();
    	 $this->view->component = $MarkDistributionDB->getComponentInfo($idComponent);
  
    }
    
    
	public function applyResitAction(){
    	
     	 $auth = Zend_Auth::getInstance();    	
     
     	 // disable layouts for this action:
         $this->_helper->layout->disableLayout();
         
         	if ($this->getRequest()->isPost()) {
		
         		if ($this->getRequest()->isPost()) {
			
					$formData = $this->getRequest()->getPost();	
		
					$ajaxContext = $this->_helper->getHelper('AjaxContext');
					$ajaxContext->addActionContext('view', 'html');
					$ajaxContext->initContext();
					
					$data["sr_idStudentRegistration"]=$auth->getIdentity()->registration_id;
					$data["sr_idSubject"]=$formData["subject"];
					$data["sr_idSemester"]=$formData["semester"];					
					$data["sr_idComponent"]=$formData["component"];
					$data["sr_charge"]=$formData["charge"];
					$data["sr_applyDate"]=date("Y-m-d H:i:s");
					$data["sr_applyBy"]=$auth->getIdentity()->appl_id;
					$data["sr_status"]=1;
					
					$resitDb = new App_Model_Exam_DbTable_Resit();
					$id = $resitDb->addData($data);
					
					$ajaxContext->addActionContext('view', 'html')
							->addActionContext('form', 'html')
							->addActionContext('process', 'json')
							->initContext();
			
					$json = Zend_Json::encode(array('id'=>$id));
				
					echo $json;
					exit();
         		}
	
         	}
     }
    
    public function changePasswordAction()
    {
        $this->view->title = $this->view->translate('Change Password');
        
        $auth = Zend_Auth::getInstance();    	
        
    	$appl_id = $auth->getIdentity()->appl_id; 
    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantFamily();	
    	$applicant = $appProfileDB->getData($appl_id,'20');
    	
        $this->view->applicant = $applicant;
        $this->view->incorrect = false;
        $this->view->msg       = false;
        
        if($this->getRequest()->isPost())
        {
            $formData = $this->getRequest()->getPost();
           // echo var_dump($formData); echo 's='.$applicant['af_password'];exit;
            if($formData['current_password'] == $applicant['af_password'])
            {
                $saveData = array('af_password' => $formData['new_password']);
                $appProfileDB->updateDataPassword($saveData,$appl_id);
                $this->view->msg       = true;
            }
            else
            {
                $this->view->incorrect = true;
            }
        }
        
    }
    
    
    public function homeAction(){
    	
    	$this->view->title = $this->view->translate('Home');
        
        $auth = Zend_Auth::getInstance();   

       // echo '<pre>';print_r($auth->getIdentity());
        
        $appl_id = $auth->getIdentity()->appl_id; 
    	$IdStudentRegistration = $auth->getIdentity()->registration_id;    

    	$this->view->appl_id = $appl_id;
    	$this->view->IdStudentRegistration = $IdStudentRegistration;
    	    	        
    	$withdrawalDb = new App_Model_Registration_DbTable_Withdrawal();
    	
    	//To get Current Semester
    	$studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
    	$current_semester = $studentSemesterDB->getCurrentRegSem($IdStudentRegistration);
    	$this->view->semester = $current_semester;
    	
    	if(!$current_semester){ //redirect dulu
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'parent-portal', 'action'=>'index'),'default',true));
    	}
    	
    	//get student landscape type
        $landscapeDb = new App_Model_Record_DbTable_Landscape();
        $landscape = $landscapeDb->getStudentLandscape($IdStudentRegistration);
        
    	//To get Subject List from current Semester
    	$registerSubjectDB = new App_Model_Record_DbTable_StudentRegSubjects();
    	$subject = $registerSubjectDB->getRegSubjectBySemId($IdStudentRegistration,$current_semester['IdSemesterMaster'],$landscape);
    	
    	//check activity calendar
    	$activityDb = new App_Model_Record_DbTable_Activity();
    	$activity = $activityDb->getActivity($current_semester['IdSemesterMaster'],$landscape['IdProgram']);
    	$this->view->activity = $activity;
    	
    	foreach($subject as $index=>$sub){
    		
    		//check status
    		$withdrawal= $withdrawalDb->getInfo($sub['IdStudentRegSubjects']);
    		
    		if(is_array($withdrawal)){
    			$subject[$index]['withdrawal']=$withdrawal;
    		}else{
    			$subject[$index]['withdrawal']= null;
    		}
    	}
    	
    	
    	$this->view->subject = $subject;
    }
    
     public function withdrawalAction(){
     	
     	 $auth = Zend_Auth::getInstance();   

     	 $IdStudentRegSubjects = $this->_getParam('IdStudentRegSubjects',0);
     	     	 
		 
     	 $data['IdStudentRegistration']=$auth->getIdentity()->registration_id;
     	 $data['IdStudentRegSubjects']=$IdStudentRegSubjects;
     	 $data['w_status']=1; // 1:apply 2:approve 3:reject
     	 $data['w_applydt']=date("Y-m-d H:i:s");
     	 $data['w_applyby']=$auth->getIdentity()->appl_id;
     	  
     	 //insert dalam table withdrawal
     	 $withdrawalDb = new App_Model_Registration_DbTable_Withdrawal();
     	 $withdrawalDb->addData($data);
     	      	 
     	 
     	  // ---- start track student log ----
		 $log['IdStudentRegistration']=$auth->getIdentity()->registration_id;
		 $log['IdStudentRegSubjects']=$IdStudentRegSubjects;
		 $log['log_type']=469; //Withdraw
		 $log['log_description']='Student Apply for Withdrawal';
		 $log['log_activity_date']=date("Y-m-d H:i:s");
		 $log['log_activity_by']=$auth->getIdentity()->appl_id;
						 		
		 $LogsDb = new App_Model_General_DbTable_StudentLogs();			
		 $LogsDb->addData($log);
		 // ---- end track student log ----
		
		
     	 $this->_redirect($this->view->url(array('module'=>'default','controller'=>'student-portal', 'action'=>'home'),'default',true));
     	
     }
     
     public function viewTranscriptAction(){
		
		$this->_helper->layout()->disableLayout(); 
        $this->view->title = "Daftar Prestasi Akademik";
		
		//get applicant profile
    	$auth = Zend_Auth::getInstance();    	
    
    	//print_r($auth->getIdentity());
    	
        $appl_id = $auth->getIdentity()->appl_id; 
        $IdStudentRegistration = $auth->getIdentity()->registration_id;    

    	$this->view->appl_id = $appl_id;
    	$this->view->IdStudentRegistration = $IdStudentRegistration;
        
        //$IdStudentRegistration = $this->_getParam('id',null);  
		$this->view->id= $IdStudentRegistration;
		
		 //To get Student Academic Info        
        $studentRegDB = new App_Model_Exam_DbTable_StudentRegistration();
        $student = $studentRegDB->getStudentInfo($IdStudentRegistration);
        $this->view->student = $student;
             
         //get photo student
    	$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
    	$file = $uploadFileDb->getFile($student["transaction_id"],51);
    	    	
		if(isset($file["pathupload"])){
    		if (file_exists($file["pathupload"])) {
    			
    			$fnImage = new icampus_Function_General_Image();
    			$photo_url = $fnImage->getImagePath($file['pathupload'],100,123);
    			//$photo_url = str_replace("/var/www/html/triapp", "", $file["pathupload"]);
    				
    			$this->view->photo_url  = $photo_url;
    		}else{
    			$this->view->photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
    		}
    	}else{
    		$this->view->photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
    	}
    	

    	$studentGradeDB = new App_Model_Exam_DbTable_StudentGrade();
    	$regSubjectDB = new App_Model_Exam_DbTable_StudentRegistrationSubject();
    	
    	$student_grade = $studentGradeDB->getStudentGradeInfo($IdStudentRegistration);
    	$this->view->student_grade = $student_grade;
    	
    	//get cgpa info
    	$gradeDb = new App_Model_Exam_DbTable_Academicstatus();
    	//echo $student_grade['sg_semesterId']."xx".$student['IdProgram'];exit;
    	$this->view->grade = $gradeDb->getListAcademicStatus($student_grade['sg_semesterId'],$student['IdProgram'],$type=1,$basedon='Program');
    	    	
    	//get dean info
    	$deanDB = new App_Model_General_DbTable_Deanmaster();
    	$dean = $deanDB->getCollegeDean($student['IdCollege']);
    	$this->view->dean = $dean;
    	
    	//get salutatuion
    	$definationsDb = new App_Model_General_DbTable_Definationms();
    	$this->view->FrontSalutation = $definationsDb->getData($dean['FrontSalutation']);
    	$this->view->BackSalutation  = $definationsDb->getData($dean['BackSalutation']);
    	
    	//get category and course list
    	$subject_category = $regSubjectDB->getCategoryCourseRegistered($IdStudentRegistration);    	
    	foreach($subject_category as $index=>$category){
    		$subject_list = $regSubjectDB->getCourseRegistered($IdStudentRegistration,$category["idCategory"]);
    		$subject_category[$index]["subjects"] = $subject_list;
    	}    	
    	$this->view->subject_category = $subject_category;

    }
    
    public function viewTempTranscriptAction(){
	
		$this->_helper->layout()->disableLayout(); 
        $this->view->title = "Daftar Prestasi Akademik (Matakuliah Lulus)";
	
		//get applicant profile
    	$auth = Zend_Auth::getInstance();    	
    
    	//print_r($auth->getIdentity());
    	
        $appl_id = $auth->getIdentity()->appl_id; 
        $IdStudentRegistration = $auth->getIdentity()->registration_id;    

    	$this->view->appl_id = $appl_id;
    	$this->view->IdStudentRegistration = $IdStudentRegistration;
        
        //$IdStudentRegistration = $this->_getParam('id',null);  
		$this->view->id= $IdStudentRegistration;
        
         //To get Student Academic Info        
        $studentRegDB = new App_Model_Exam_DbTable_StudentRegistration();
        $student = $studentRegDB->getStudentInfo($IdStudentRegistration);
        $this->view->student = $student;
      
		
		if($student["majoring"]=="common"|$student["majoring"]=="Bersama"){
			$student["majoring"]="-";
			$student["majoring_english"]="-";
		}
	
		$this->view->student = $student;
		 
		//get photo student
		$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
		$file = $uploadFileDb->getFile($student["transaction_id"],51);
	
		if(isset($file["pathupload"])){
			if (file_exists($file["pathupload"])) {
				 
				$fnImage = new icampus_Function_General_Image();
				$photo_url = $fnImage->getImagePath($file['pathupload'],100,123);
				//$photo_url = str_replace("/var/www/html/triapp", "", $file["pathupload"]);
	
				$this->view->photo_url  = $photo_url;
			}else{
				$this->view->photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
			}
		}else{
			$this->view->photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
		}
		 
	
		$studentGradeDB = new App_Model_Exam_DbTable_StudentGrade();
		$regSubjectDB = new App_Model_Exam_DbTable_StudentRegistrationSubject();
		 
		$student_grade = $studentGradeDB->getStudentGradeInfo($IdStudentRegistration);
		$this->view->student_grade = $student_grade;
		 
		//get cgpa info
		$gradeDb = new App_Model_Exam_DbTable_Academicstatus();
		//echo $student_grade['sg_semesterId']."xx".$student['IdProgram'];exit;
		$this->view->grade = $gradeDb->getListAcademicStatus($student_grade['sg_semesterId'],$student['IdProgram'],$type=1,$basedon='Program');
	
		//get dean info
		$deanDB = new App_Model_General_DbTable_Deanmaster();
		$dean = $deanDB->getCollegeDean($student['IdCollege']);
		$this->view->dean = $dean;
		 
		//get salutatuion
		$definationsDb = new App_Model_General_DbTable_Definationms();
		$this->view->FrontSalutation = $definationsDb->getData($dean['FrontSalutation']);
		$this->view->BackSalutation  = $definationsDb->getData($dean['BackSalutation']);
		 
		//transcript profile
		$DbProfile = new App_Model_General_DbTable_TranscriptProfile();
		$DbProfileDetail = new App_Model_General_DbTable_TranscriptProfileDetail();
		//print_r($student);
       /* */
		$idProfile =$student['idTranscriptProfile'];
		if ($idProfile==0) {
			$profile = $DbProfile->getStdTranscriptProfile($student['IdProgram'],$student['IdProgramMajoring'],$student['IdLandscape']);
			
			if(!isset($profile[0]['IdProfile'])){
				$idProfile = '*';
			}
			else
			{
				$idProfile = $profile[0]['IdProfile'];
			}
		}
        //get category and course list
		//echo $idProfile;exit;
		$subject_category =$this->getTranscriptList($IdStudentRegistration,$idProfile,'1');
		
		$db = new Finalassignment_Model_DbTable_FinalAssignment();
		$ta = $db->fnGetFinalAssigmentStd($IdStudentRegistration);
		//exit;
		if (isset($ta)) {
			$this->view->TaTitle=$ta['Title'];
			$this->view->TaTitleBahasa=$ta['TitleBahasa'];
		}else{
			$this->view->TaTitle=null;
			$this->view->TaTitleBahasa=null;
		}
		$this->view->subject_category = $subject_category;
		 
	
	}
	
	public function viewAllTranscriptAction(){
	
		$this->_helper->layout()->disableLayout();
		$this->view->title = "Daftar Prestasi Akademik (Keseluruhan)";
	
		//get applicant profile
		$auth = Zend_Auth::getInstance();
	
		//print_r($auth->getIdentity());
		 
		$appl_id = $auth->getIdentity()->appl_id;
		$IdStudentRegistration = $auth->getIdentity()->registration_id;
	
		$this->view->appl_id = $appl_id;
		$this->view->IdStudentRegistration = $IdStudentRegistration;
	
		//$IdStudentRegistration = $this->_getParam('id',null);
		$this->view->id= $IdStudentRegistration;
	
		//To get Student Academic Info
		$studentRegDB = new App_Model_Exam_DbTable_StudentRegistration();
		$student = $studentRegDB->getStudentInfo($IdStudentRegistration);
		$this->view->student = $student;
	
	
		if($student["majoring"]=="common"|$student["majoring"]=="Bersama"){
			$student["majoring"]="-";
			$student["majoring_english"]="-";
		}
	
		$this->view->student = $student;
			
		//get photo student
		$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
		$file = $uploadFileDb->getFile($student["transaction_id"],51);
	
		if(isset($file["pathupload"])){
			if (file_exists($file["pathupload"])) {
					
				$fnImage = new icampus_Function_General_Image();
				$photo_url = $fnImage->getImagePath($file['pathupload'],100,123);
				//$photo_url = str_replace("/var/www/html/triapp", "", $file["pathupload"]);
	
				$this->view->photo_url  = $photo_url;
			}else{
				$this->view->photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
			}
		}else{
			$this->view->photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
		}
			
	
		$studentGradeDB = new App_Model_Exam_DbTable_StudentGrade();
		$regSubjectDB = new App_Model_Exam_DbTable_StudentRegistrationSubject();
			
		$student_grade = $studentGradeDB->getStudentGradeInfo($IdStudentRegistration);
		$this->view->student_grade = $student_grade;
			
		//get cgpa info
		$gradeDb = new App_Model_Exam_DbTable_Academicstatus();
		//echo $student_grade['sg_semesterId']."xx".$student['IdProgram'];exit;
		$this->view->grade = $gradeDb->getListAcademicStatus($student_grade['sg_semesterId'],$student['IdProgram'],$type=1,$basedon='Program');
	
		//get dean info
		$deanDB = new App_Model_General_DbTable_Deanmaster();
		$dean = $deanDB->getCollegeDean($student['IdCollege']);
		$this->view->dean = $dean;
			
		//get salutatuion
		$definationsDb = new App_Model_General_DbTable_Definationms();
		$this->view->FrontSalutation = $definationsDb->getData($dean['FrontSalutation']);
		$this->view->BackSalutation  = $definationsDb->getData($dean['BackSalutation']);
			
		//transcript profile
		$DbProfile = new App_Model_General_DbTable_TranscriptProfile();
		$DbProfileDetail = new App_Model_General_DbTable_TranscriptProfileDetail();
		//print_r($student);
		/* */
		$idProfile =$student['idTranscriptProfile'];
		if ($idProfile==0) {
			$profile = $DbProfile->getStdTranscriptProfile($student['IdProgram'],$student['IdProgramMajoring'],$student['IdLandscape']);
				
			if(!isset($profile[0]['IdProfile'])){
				$idProfile = '*';
			}
			else
			{
				$idProfile = $profile[0]['IdProfile'];
			}
		}
		//get category and course list
		//echo $idProfile;exit;
		$subject_category =$this->getTranscriptList($IdStudentRegistration,$idProfile,null);
	
		$db = new Finalassignment_Model_DbTable_FinalAssignment();
		$ta = $db->fnGetFinalAssigmentStd($IdStudentRegistration);
		//exit;
		if (isset($ta)) {
			$this->view->TaTitle=$ta['Title'];
			$this->view->TaTitleBahasa=$ta['TitleBahasa'];
		}else{
			$this->view->TaTitle=null;
			$this->view->TaTitleBahasa=null;
		}
		$this->view->subject_category = $subject_category;
			
	
	}
    
    public function getTranscriptList($idStudentRegistration,$idProfile=null,$pass=null) {
		//get student profile
		$regSubjectDB = new App_Model_Exam_DbTable_StudentRegistrationSubject();
		$DbProfileDetail = new App_Model_General_DbTable_TranscriptProfileDetail();
		$dbStudent = new App_Model_Exam_DbTable_StudentRegistration();
		$student = $dbStudent->SearchStudentRegistration(array('IdStudentRegistration'=>$idStudentRegistration));
		$student=$student[0];
        if ($idProfile==null) {
			$student=$student[0];
			$idLandscape = $student['IdLandscape'];
			$idProgram = $student['IdProgram'];
			$idMajor = $student['IdProgramMajoring'];
			//echo var_dump($student);
			//exit;
			//transcript profile
			$DbProfile = new App_Model_General_DbTable_TranscriptProfile();
			$DbProfileDetail = new App_Model_General_DbTable_TranscriptProfileDetail();
			$idProfile = $DbProfile->getStdTranscriptProfile($idProgram, $idMajor, $idLandscape);
			//echo var_dump($idProfile);exit;
			if ($idProfile==array()) $idProfile='*'; else $idProfile=$idProfile[0]['IdProfile'];
		}
		//get category and course list
		//echo var_dump($idProfile);exit;
		
		if ($idProfile=='*') {
		
			$dbLands = new GeneralSetup_Model_DbTable_Landscapesubject();
			$dbBlock= new GeneralSetup_Model_DbTable_LandscapeBlockSubject();
			$dbProgReq = new GeneralSetup_Model_DbTable_Programrequirement();
			$subject_category = $dbProgReq->getlandscapecoursetype($student['IdProgram'], $student['IdLandscape']);
		
			foreach($subject_category as $index=>$category){
				$subject_list = $dbLands->getlandscapesubjectsPerCategory($student['IdLandscape'],$category["SubjectType"]);
				//echo var_dump($category);
				//exit;
				if ($subject_list==array()) $subject_list = $dbBlock->getLandscapeCoursePerCategory($student['IdLandscape'],$category["SubjectType"]);
				unset($subjecthighest);
				foreach ($subject_list as $key=>$subject) {
					if ($pass==null)
						$subject=$regSubjectDB->getHighestMarkofAllSemesterC($idStudentRegistration, $subject['IdSubject']);
					else 
						$subject=$regSubjectDB->getHighestMarkofAllSemesterPassed($idStudentRegistration, $subject['IdSubject']);
					if (!is_bool($subject)) $subjecthighest[$key] = $subject;
				}
				if (isset($subjecthighest)) $subject_category[$index]["subjects"] = $subjecthighest;
				else unset($subject_category[$index]);
				//echo var_dump($subject_category);
				//exit;
			}
		
		}
		else
		{
		
			$subject_category = $DbProfileDetail->fnGetTranscriptProfileName($idProfile);
			foreach($subject_category as $index=>$category){
				$subjecthighest=array();
				$subject_list = $DbProfileDetail->fnGetTranscriptProfileSubject($idProfile,$category['idGroup']);
				unset($subjecthighest);
				//echo var_dump($subject_list);exit;
				foreach($subject_list as $key=>$subject) :
				if ($pass==null)
						$subject=$regSubjectDB->getHighestMarkofAllSemesterC($idStudentRegistration, $subject['idSubject']);
				else 
						$subject=$regSubjectDB->getHighestMarkofAllSemesterPassed($idStudentRegistration, $subject['idSubject']);
				if (!is_bool($subject)) $subjecthighest[$key] = $subject;
				endforeach;
				if (isset($subjecthighest)) $subject_category[$index]["subjects"] = $subjecthighest;
					else unset($subject_category[$index]);
				 
			}
		}
		//echo var_dump($subject_category);
		//exit;
		return $subject_category;
	}
	
    
    public function cetakTempTranscriptAction(){
		 
	//get applicant profile
    	$auth = Zend_Auth::getInstance();    	
    
    	//print_r($auth->getIdentity());
    	
        $appl_id = $auth->getIdentity()->appl_id; 
        $IdStudentRegistration = $auth->getIdentity()->registration_id;    

    	$this->view->appl_id = $appl_id;
    	$this->view->IdStudentRegistration = $IdStudentRegistration;
        
        //$IdStudentRegistration = $this->_getParam('id',null);  
		$this->view->id= $IdStudentRegistration;
        
         //To get Student Academic Info        
        $studentRegDB = new App_Model_Exam_DbTable_StudentRegistration();
        $student = $studentRegDB->getStudentInfo($IdStudentRegistration);
        $this->view->student = $student;
        
		if($student["majoring"]=="common"|$student["majoring"]=="Bersama"){
			$student["majoring"]="-";
			$student["majoring_english"]="-";
		}
	
		global $majoring;
		$majoring=$student["majoring"];
		global $printmajoring;
		$printmajoring=$student['print_majoring'];
		//get photo student
		$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
		$file = $uploadFileDb->getFile($student["transaction_id"],51);
	
		if(isset($file["pathupload"])){
			if (file_exists($file["pathupload"])) {
				$fnImage = new icampus_Function_General_Image();
				$photo_url = "http://".APP_HOSTNAME.$fnImage->getImagePath($file['pathupload'],100,123);
				//$photo_url = str_replace("/var/www/html/triapp","http://".ONNAPP_HOSTNAME."/", $file["pathupload"]);
			}else{
				$photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
			}
		}else{
			$photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
		}
		 
	
		//get info college
		$collegedB = new App_Model_General_DbTable_Collegemaster();
        $college = $collegedB->getFullInfoCollege($student["IdCollege"]);
	
	
		$studentGradeDB = new App_Model_Exam_DbTable_StudentGrade();
		$regSubjectDB = new App_Model_Exam_DbTable_StudentRegistrationSubject();
		 
		$student_grade = $studentGradeDB->getStudentGradeInfo($IdStudentRegistration);
		$this->view->student_grade = $student_grade;
        
		$student_grade = $studentGradeDB->getStudentGradeInfo($IdStudentRegistration);
		
		//get cgpa info
		global $grade;
		
		$gradeDb = new App_Model_Exam_DbTable_Academicstatus();
		//echo $student_grade['sg_semesterId']."xx".$student['IdProgram'];exit;
		$this->view->grade = $gradeDb->getListAcademicStatus($student_grade['sg_semesterId'],$student['IdProgram'],$type=1,$basedon='Program');
	
		//get dean info
		$deanDB = new App_Model_General_DbTable_Deanmaster();
		$dean = $deanDB->getCollegeDean($student['IdCollege']);
		 
		 
		//get salutatuion
		$definationsDb = new App_Model_General_DbTable_Definationms();
		$FrontSalutation = $definationsDb->getData($dean['FrontSalutation']);
		$BackSalutation  = $definationsDb->getData($dean['BackSalutation']);
		 
		//get category and course list
		global $subject_category;
		//transcript profile
		$DbProfile = new App_Model_General_DbTable_TranscriptProfile();
		$DbProfileDetail = new App_Model_General_DbTable_TranscriptProfileDetail();
		$idProfile =$student['idTranscriptProfile'];
		if ($idProfile==0) {
			$profile = $DbProfile->getStdTranscriptProfile($student['IdProgram'],$student['IdProgramMajoring'],$student['IdLandscape']);
			
			if(!isset($profile[0]['IdProfile'])){
				$idProfile = '*';
			}
			else
			{
				$idProfile = $profile[0]['IdProfile'];
			}
		}
		$subject_category =$this->getTranscriptList($IdStudentRegistration,$idProfile,'1');
	
	
		$fieldValues = array(
				'$[JURUSAN]'=>$student["Dept_Bahasa"],
				'$[PROGRAMSTUDI]'=>$student["ArabicName"],
				'$[DEPARTMENT]'=>$student["Departement"],
				'$[STUDYPROGRAM]'=>$student["ProgramName"],
				'$[FAKULTAS]'=>'FAKULTAS '.$college["ArabicName"],
				'$[FACULTY]'=> $college["CollegeName"],
				'$[ADDRESS]'=>ucwords(strtolower($college["Add1"])).' '.ucwords(strtolower($college["Add2"])).' '.ucwords(strtolower($college["CityName"])).' '.ucwords(strtolower($college["StateName"])),
				'$[PHONE]'=>$college["Phone1"],
				'$[EMAIL]'=>$college["Email"],
				'$[KONSENTRASI]'=>$student["majoring"],
				'$[MAJORING]'=>$student["majoring_english"],
				'$[PROGRAMPENDIDIKAN]'=>@$student["program_pendidikan"],
				'$[SCHEME]'=>$student["strata"],
				'$[PROGRAM]'=>$student["program_eng"],
				'$[STUDENTNAME]'=>$student["appl_fname"].' '.$student["appl_mname"].' '.$student["appl_lname"],
				'$[BIRTHDATE]'=>$student["appl_birth_place"].', '.strftime("%e %B, %Y", strtotime($student["appl_dob"])),
				'$[NIM]'=>$student["registrationId"],
				'$[PHOTO]'=>$photo_url,
				'$[DEAN]'=>$FrontSalutation['DefinitionDesc'].' '.$dean['Fullname'].' '.$BackSalutation['DefinitionDesc']	,
				'$[TOTALCREDITHOUR]'=>$student_grade['sg_cum_credithour'],
				'$[TOTALPOINT]'=>number_format($student_grade['sg_cum_totalpoint'], 2, '.', ''),
				'$[GPA]'=>number_format($student_grade['sg_cgpa'], 2, '.', ''),
				'$[CGPA_STATUS]'=>$student_grade['sg_cgpa_status']
		);
	
	//echo var_dump($student);
	//exit;
		require_once 'dompdf_config.inc.php';
	
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
	
		//template path
		$html_template_path = DOCUMENT_PATH."/template/transcript_temp.html";
	
		$html = file_get_contents($html_template_path);
	
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);
		}
		//echo $html;exit;
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		$dompdf->render();
	
		//echo $html;exit;
	
		//echo $html;exit;
		$output_directory_path = DOCUMENT_PATH."/student/transcript";
		
		//create directory to locate file
		if (!is_dir($output_directory_path)) {
			mkdir($output_directory_path, 0775,true);
		}
		//output filename 
		$output_filename = "transcript_temp_".$student['registrationId'].".pdf";
				
		//$dompdf = $dompdf->output();
		$dompdf->stream($output_filename);						
							
		//to rename output file						
	    $output_file_path = $output_directory_path.'/'.$output_filename;
		
		file_put_contents($output_file_path, $dompdf);
		
		$this->view->file_path = $output_file_path;
	
		exit;
	
	}
	public function cetakAllTranscriptAction(){
			
		//get applicant profile
		$auth = Zend_Auth::getInstance();
	
		//print_r($auth->getIdentity());
		 
		$appl_id = $auth->getIdentity()->appl_id;
		$IdStudentRegistration = $auth->getIdentity()->registration_id;
	
		$this->view->appl_id = $appl_id;
		$this->view->IdStudentRegistration = $IdStudentRegistration;
	
		//$IdStudentRegistration = $this->_getParam('id',null);
		$this->view->id= $IdStudentRegistration;
	
		//To get Student Academic Info
		$studentRegDB = new App_Model_Exam_DbTable_StudentRegistration();
		$student = $studentRegDB->getStudentInfo($IdStudentRegistration);
		$this->view->student = $student;
	
		if($student["majoring"]=="common"|$student["majoring"]=="Bersama"){
			$student["majoring"]="-";
			$student["majoring_english"]="-";
		}
	
		global $majoring;
		$majoring=$student["majoring"];
		global $printmajoring;
		$printmajoring=$student['print_majoring'];
		//get photo student
		$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
		$file = $uploadFileDb->getFile($student["transaction_id"],51);
	
		if(isset($file["pathupload"])){
			if (file_exists($file["pathupload"])) {
				$fnImage = new icampus_Function_General_Image();
				$photo_url = "http://".APP_HOSTNAME.$fnImage->getImagePath($file['pathupload'],100,123);
				//$photo_url = str_replace("/var/www/html/triapp","http://".ONNAPP_HOSTNAME."/", $file["pathupload"]);
			}else{
				$photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
			}
		}else{
			$photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
		}
			
	
		//get info college
		$collegedB = new App_Model_General_DbTable_Collegemaster();
		$college = $collegedB->getFullInfoCollege($student["IdCollege"]);
	
	
		$studentGradeDB = new App_Model_Exam_DbTable_StudentGrade();
		$regSubjectDB = new App_Model_Exam_DbTable_StudentRegistrationSubject();
			
		$student_grade = $studentGradeDB->getStudentGradeInfo($IdStudentRegistration);
		$this->view->student_grade = $student_grade;
	
		$student_grade = $studentGradeDB->getStudentGradeInfo($IdStudentRegistration);
	
		//get cgpa info
		global $grade;
	
		$gradeDb = new App_Model_Exam_DbTable_Academicstatus();
		//echo $student_grade['sg_semesterId']."xx".$student['IdProgram'];exit;
		$this->view->grade = $gradeDb->getListAcademicStatus($student_grade['sg_semesterId'],$student['IdProgram'],$type=1,$basedon='Program');
	
		//get dean info
		$deanDB = new App_Model_General_DbTable_Deanmaster();
		$dean = $deanDB->getCollegeDean($student['IdCollege']);
			
			
		//get salutatuion
		$definationsDb = new App_Model_General_DbTable_Definationms();
		$FrontSalutation = $definationsDb->getData($dean['FrontSalutation']);
		$BackSalutation  = $definationsDb->getData($dean['BackSalutation']);
			
		//get category and course list
		global $subject_category;
		//transcript profile
		$DbProfile = new App_Model_General_DbTable_TranscriptProfile();
		$DbProfileDetail = new App_Model_General_DbTable_TranscriptProfileDetail();
		$idProfile =$student['idTranscriptProfile'];
		if ($idProfile==0) {
			$profile = $DbProfile->getStdTranscriptProfile($student['IdProgram'],$student['IdProgramMajoring'],$student['IdLandscape']);
			
			if(!isset($profile[0]['IdProfile'])){
				$idProfile = '*';
			}
			else
			{
				$idProfile = $profile[0]['IdProfile'];
			}
		}
		$subject_category =$this->getTranscriptList($IdStudentRegistration,$idProfile,null);
	
		$fieldValues = array(
				'$[JURUSAN]'=>$student["Dept_Bahasa"],
				'$[PROGRAMSTUDI]'=>$student["ArabicName"],
				'$[DEPARTMENT]'=>$student["Departement"],
				'$[STUDYPROGRAM]'=>$student["ProgramName"],
				'$[FAKULTAS]'=>'FAKULTAS '.$college["ArabicName"],
				'$[FACULTY]'=> $college["CollegeName"],
				'$[ADDRESS]'=>ucwords(strtolower($college["Add1"])).' '.ucwords(strtolower($college["Add2"])).' '.ucwords(strtolower($college["CityName"])).' '.ucwords(strtolower($college["StateName"])),
				'$[PHONE]'=>$college["Phone1"],
				'$[EMAIL]'=>$college["Email"],
				'$[KONSENTRASI]'=>$student["majoring"],
				'$[MAJORING]'=>$student["majoring_english"],
				'$[PROGRAMPENDIDIKAN]'=>$student["program_pendidikan"],
				'$[SCHEME]'=>$student["strata"],
				'$[PROGRAM]'=>$student["program_eng"],
				'$[STUDENTNAME]'=>$student["appl_fname"].' '.$student["appl_mname"].' '.$student["appl_lname"],
				'$[BIRTHDATE]'=>$student["appl_birth_place"].', '.strftime("%e %B, %Y", strtotime($student["appl_dob"])),
				'$[NIM]'=>$student["registrationId"],
				'$[PHOTO]'=>$photo_url,
				'$[DEAN]'=>$FrontSalutation['DefinitionDesc'].' '.$dean['Fullname'].' '.$BackSalutation['DefinitionDesc']	,
				'$[TOTALCREDITHOUR]'=>$student_grade['sg_all_cum_credithour'],
				'$[TOTALPOINT]'=>number_format($student_grade['sg_all_cum_totalpoint'], 2, '.', ''),
				'$[GPA]'=>number_format($student_grade['sg_all_cgpa'], 2, '.', ''),
				'$[CGPA_STATUS]'=>$student_grade['sg_cgpa_status']
		);
	
		//echo var_dump($student);
		//exit;
		require_once 'dompdf_config.inc.php';
	
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
	
		//template path
		$html_template_path = DOCUMENT_PATH."/template/transcript_temp.html";
	
		$html = file_get_contents($html_template_path);
	
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);
		}
		//echo $html;exit;
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		$dompdf->render();
	
		//echo $html;exit;
	
		//echo $html;exit;
		$output_directory_path = DOCUMENT_PATH."/student/transcript";
	
		//create directory to locate file
		if (!is_dir($output_directory_path)) {
			mkdir($output_directory_path, 0775,true);
		}
		//output filename
		$output_filename = "transcript_temp_".$student['registrationId'].".pdf";
	
		//$dompdf = $dompdf->output();
		$dompdf->stream($output_filename);
			
		//to rename output file
		$output_file_path = $output_directory_path.'/'.$output_filename;
	
		file_put_contents($output_file_path, $dompdf);
	
		$this->view->file_path = $output_file_path;
	
		exit;
	
	}
    public function cetakTranscriptAction(){
    	
		
		//get applicant profile
    	$auth = Zend_Auth::getInstance();    	
    
    	//print_r($auth->getIdentity());
    	
        $appl_id = $auth->getIdentity()->appl_id; 
        $IdStudentRegistration = $auth->getIdentity()->registration_id;   
		
		 //To get Student Academic Info        
        $studentRegDB = new App_Model_Exam_DbTable_StudentRegistration();
        $student = $studentRegDB->getStudentInfo($IdStudentRegistration);
                    
         //get photo student
    	$uploadFileDb = new App_Model_Application_DbTable_UploadFile();
    	$file = $uploadFileDb->getFile($student["transaction_id"],51);
    	    	
		if(isset($file["pathupload"])){
    		if (file_exists($file["pathupload"])) {
    			$fnImage = new icampus_Function_General_Image();
    			$photo_url = "http://".APP_HOSTNAME.$fnImage->getImagePath($file['pathupload'],100,123);	
    			//$photo_url = str_replace("/var/www/html/triapp","http://".ONNAPP_HOSTNAME."/", $file["pathupload"]);
    		}else{
    			$photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
    		}
    	}else{
    		$photo_url = "http://".APP_HOSTNAME."/images/no-photo.jpg";
    	}
    	

    	//get info college
    	$collegedB = new App_Model_General_DbTable_Collegemaster();
        $college = $collegedB->getFullInfoCollege($student["IdCollege"]);
        
				        
    	$studentGradeDB = new App_Model_Exam_DbTable_StudentGrade();
    	$regSubjectDB = new App_Model_Exam_DbTable_StudentRegistrationSubject();
    	
    	$student_grade = $studentGradeDB->getStudentGradeInfo($IdStudentRegistration);
    	
    	//get cgpa info
    	global $grade;
    	$gradeDb = new App_Model_Exam_DbTable_Academicstatus();
    	$grade = $gradeDb->getListAcademicStatus($student_grade['sg_semesterId'],$student['IdProgram'],$type=1,$basedon='Program');
    	    	
    	//get dean info
    	$deanDB = new App_Model_General_DbTable_Deanmaster();
    	$dean = $deanDB->getCollegeDean($student['IdCollege']);
    	
    	
    	//get salutatuion
    	$definationsDb = new App_Model_General_DbTable_Definationms();
    	$FrontSalutation = $definationsDb->getData($dean['FrontSalutation']);
    	$BackSalutation  = $definationsDb->getData($dean['BackSalutation']);
    	
    	//get category and course list
    	global $subject_category;
    	$subject_category = $regSubjectDB->getCategoryCourseRegistered($IdStudentRegistration);    	
    	foreach($subject_category as $index=>$category){
    		$subject_list = $regSubjectDB->getCourseRegistered($IdStudentRegistration,$category["idCategory"]);
    		$subject_category[$index]["subjects"] = $subject_list;
    	}    	
    	
		
    	$fieldValues = array(
			    	  '$[JURUSAN]'=>$student["NamaKolej"],
    	 			  '$[PROGRAMSTUDI]'=>$student["ArabicName"],
    				  '$[DEPARTMENT]'=>$student["CollegeName"],
    	 			  '$[STUDYPROGRAM]'=>$student["ProgramName"],
			    	  '$[FAKULTAS]'=>'FAKULTAS '.$student["ArabicName"],
    	 			  '$[FACULTY]'=> $college["CollegeName"],			    	 
			    	  '$[ADDRESS]'=>ucwords(strtolower($college["Add1"])).' '.ucwords(strtolower($college["Add2"])).' '.ucwords(strtolower($college["CityName"])).' '.ucwords(strtolower($college["StateName"])),
					  '$[PHONE]'=>$college["Phone1"],
					  '$[EMAIL]'=>$college["Email"],
    				  '$[KONSENTRASI]'=>$student["majoring"],
					  '$[MAJORING]'=>$student["majoring_english"],
    				  '$[PROGRAMPENDIDIKAN]'=>$college["Phone1"],
    				  '$[SCHEME]'=>$student["SchemeCode"],
					  '$[PROGRAM]'=>$student["SchemeName"],
    				  '$[STUDENTNAME]'=>$student["appl_fname"].' '.$student["appl_mname"].' '.$student["appl_lname"],
					  '$[BIRTHDATE]'=>$student["appl_birth_place"].', '.strftime("%e %B, %Y", strtotime($student["appl_dob"])),
    				  '$[NIM]'=>$student["registrationId"],
    				  '$[PHOTO]'=>$photo_url,
    				  '$[DEAN]'=>$FrontSalutation['DefinitionDesc'].' '.$dean['Fullname'].' '.$BackSalutation['DefinitionDesc']	,
    	 			  '$[TOTALCREDITHOUR]'=>$student['TotalCreditHours'],	
    	 			  '$[TOTALPOINT]'=>number_format($student_grade['sg_univ_sem_totalpoint'], 2, '.', ''),
			    	  '$[GPA]'=>number_format($student_grade['sg_cgpa'], 2, '.', ''),
			    	  '$[CGPA_STATUS]'=>$student_grade['sg_cgpa_status']  
		    	   );
				    	   
		
	    require_once 'dompdf_config.inc.php';
	
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		//template path	 
		$html_template_path = DOCUMENT_PATH."/template/transcript.html";
		
		$html = file_get_contents($html_template_path);			
    		
		//replace variable
		foreach ($fieldValues as $key=>$value){
			$html = str_replace($key,$value,$html);	
		}
			
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		$dompdf->render();
		
		//$dompdf = $dompdf->output();
		$dompdf->stream($output_filename);						
		
		file_put_contents($output_file_path, $dompdf);
		
		$this->view->file_path = $output_file_path;
		
		
		exit;
		
	}
}

?>