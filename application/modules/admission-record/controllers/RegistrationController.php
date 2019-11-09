<?php

class AdmissionRecord_RegistrationController extends Zend_Controller_Action {
	
	
	
	public function indexAction(){
		//title
		$this->view->title = "Student Registration - Student Selection";
		
		$studentDB = new App_Model_Record_DbTable_Student();
		
		//program
		$programDB = new App_Model_Record_DbTable_Program();
		$this->view->programList = $programDB->getActiveProgram();
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$student_data = $studentDB->search($formData);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($student_data));
			$paginator->setItemCountPerPage(100);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
		}else{
			$student_data = $studentDB->getPaginateData();
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($student_data));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
		}
		
		$this->view->paginator = $paginator;
		
	}
	
	
	
	public function semesterAction(){
		//title
		$this->view->title = "Student Registration - Course Registration";
		
		$student_id = $this->_getParam('stud_id', 0);
		
		$studentDB = new App_Model_Record_DbTable_Student();
		$student = $studentDB->getStudent($student_id);
		$this->view->student = $student;
		
		$programDB = new App_Model_Record_DbTable_Program();
		$this->view->program = $programDB->getData($student['program_id']);		
		
		//get program requirement details
		$programReqDB = new App_Model_Record_DbTable_ProgramRequirement();
		$programReq   = $programReqDB->getProgramRequirement($student['program_id']);
		$this->view->program_requirement = $programReq; 
		
		$reqInfo = $programReqDB->getCourseRequirement($student['program_id']);		
		$this->view->max_sem_credit = $reqInfo['max_sem_credit'];
		
		
		$semesterDB = new App_Model_Record_DbTable_Semester();		
		//get current semester    	
    	$semester = $semesterDB->currentSemester();    	
    	$this->view->currentsemester = $semester;   
    	  	
    	//get list semester		
		$this->view->semesterlist = $semesterDB->getData();
			     
    	
    	//get selection semester   
    	if ($this->getRequest()->isPost()) {	
    		$semester_id = $this->getRequest()->getPost('semester_id');  
    		$select_semester_id = $semester_id;    	  	
    	}else{
    		$select_semester_id = $semester["id"];
    	}    	
    	$this->view->semester_id= $select_semester_id;
    	
    	
    	    $studentSemesterDB = new App_Model_Record_DbTable_StudentSemester();
			if($studentSemesterDB->isRegistered($student_id,$select_semester_id)){
				
				$student_course_registrationDB = new App_Model_Record_DbTable_StudentCourseRegistration();
				$this->view->courses = $student_course_registrationDB->getRegistrationData($student_id, $select_semester_id);
		
				$this->view->semester = $semesterDB->getData($select_semester_id);
				$this->view->noticeMessage = "This student was registered for semester " . $this->view->semester['name'];
			}	   	
    	 		
        //compare with offered courses from system
		$course_offeredDB = new App_Model_Record_DbTable_CourseOffered();
		$courseList = $course_offeredDB->getCourseSemOffered($student['program_id'],$select_semester_id);
		
		//TODO: check for taken and pass with exam module. 
		//This is to push only relevent subject to student
		$this->view->courseList = $courseList;
		
    	 
	}
		
	
	public function registerAction(){
		
		$this->view->title = "Student Registration - Completed";
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$student_id = $this->_getParam('student_id', 0);
			$semester_id = $this->_getParam('semester_id', 0);
						
			$studentDB = new App_Model_Record_DbTable_Student();
			$student = $studentDB->getStudent($student_id);
			$this->view->student = $student;
			
			$programDB = new App_Model_Record_DbTable_Program();
			$program = $programDB->getData($student['program_id']);
			$this->view->program = $program;
					
			$semester_id = $this->_getParam('semester_id', 0);
			$this->view->semester_id = $semester_id;
			$semesterDB = new App_Model_Record_DbTable_Semester();
			$this->view->semester = $semesterDB->getData($semester_id);
			
			
			$courseselect = $this->getRequest()->getPost('course_select');    
			
			
			for ($i=0; $i<count($courseselect); $i++){
				
				//add to table student semester
				$studentSemesterDB = new App_Model_Record_DbTable_StudentSemester();
				$reg_id = $studentSemesterDB->addData($student_id,$semester_id,1);
				
				//add to table student semester history
				$studentSemesterHistoryDB = new App_Model_Record_DbTable_StudentSemesterHistory();
				$studentSemesterHistoryDB->addData($student_id,$semester_id,1,$reg_id);
				
				//add to table student course registration	 & 	student course registration	history		
				$data = array(
							'student_id' => $formData['student_id'],
							'semester_id' => $formData['semester_id'],	
							'course_id' => $courseselect[$i],
							'course_status_id' => 1,
							'entry_date' => date('Y-m-d H:i:a')
							);							
				$student_course_registrationDB = new App_Model_Record_DbTable_StudentCourseRegistration();
				$student_course_registrationDB->addData($data);	

			}			
			
			//redirect 
		    $this->_redirect($this->view->url(array('module'=>'admission-record','controller'=>'registration', 'action'=>'receipt','student_id'=>$student_id, 'semester_id'=>$semester_id),'default',true));
		
			
		}//end if
	}
	
		
	public function receiptAction(){
		$this->view->title = "Student Registration - Completed";
				
			$student_id = $this->_getParam('student_id', 0);
		    $semester_id = $this->_getParam('semester_id', 0);
		    
		    $studentDB = new App_Model_Record_DbTable_Student();
			$student = $studentDB->getStudent($student_id);
			$this->view->student = $student;
			
			$programDB = new App_Model_Record_DbTable_Program();
			$program = $programDB->getData($student['program_id']);
			$this->view->program = $program;
					
			$semester_id = $this->_getParam('semester_id', 0);
			$this->view->semester_id = $semester_id;
			$semesterDB = new App_Model_Record_DbTable_Semester();
			$this->view->semester = $semesterDB->getData($semester_id);
		
			$student_course_registrationDB = new App_Model_Record_DbTable_StudentCourseRegistration();
			$this->view->courses = $student_course_registrationDB->getRegistrationData($student_id, $semester_id);
			
			$this->view->noticeMessage = "Student Course Registration Completed";
		
	}
	
	
	//dah x pakai dah
	public function courseAction(){
		//title
		$this->view->title = "Student Registration - Course Selection";
		
		
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			$student_id = $this->_getParam('student_id', 0);
			$semester_id = $this->_getParam('semester_id', 0);
			
			//Check for already registered
			$studentSemesterDB = new App_Model_Record_DbTable_StudentSemester();
			if($studentSemesterDB->isRegistered($student_id,$semester_id)){
				//redirect
				$this->_redirect($this->view->url(array('module'=>'admission-record','controller'=>'registration', 'action'=>'history','student_id'=>$student_id, 'semester_id'=>$semester_id),'default',true));
			}
						
			$studentDB = new App_Model_Record_DbTable_Student();
			$student = $studentDB->getStudent($student_id);
			$this->view->student = $student;
			
			$programDB = new App_Model_Record_DbTable_Program();
			$program = $programDB->getData($student['program_id']);
			$this->view->program = $program;
					
			//$semester_id = $this->_getParam('semester_id', 0);
			$semester_id       = $this->getRequest()->getPost('semester_id'); 
			$this->view->semester_id = $semester_id;
			$semesterDB = new App_Model_Record_DbTable_Semester();
			$this->view->semester = $semesterDB->getData($semester_id);
						
			//get course academic landscape
			$academic_landscapeDB = new App_Model_Record_DbTable_AcademicLandscape();
			$courses = $academic_landscapeDB->getProgramCourse($student['program_id']);
			
			//compare with offered courses from system
			$course_offeredDB = new App_Model_Record_DbTable_CourseOffered();
			$courseList = $course_offeredDB->getCourseOffered($program['id'],$semester_id);
			
			//TODO: check for taken and pass with exam module. 
			//This is to push only relevent subject to student
			$this->view->courseList = $courseList;
		}
		
	}
	
	
	
	/*
	 * ajax action to get registration data base on registration ID
	 */
	public function ajaxGetRegistrationAction($id=null){
    	$id = $this->_getParam('id', 0);
    	
    	// check is AJAX request or not
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	
	  	
	  	 $registrationDB = new App_Model_Record_DbTable_StudentCourseRegistration();
		 $registration_data = $registrationDB->getData($id);
		
		
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($registration_data);
		
		$this->view->json = $json;

    }
    
	/*
	 * ajax action to check course registration base on course id and semester id
	 * return boolean
	 */
	public function ajaxIsCourseRegisterAction($id=null){
		$student_id = $this->_getParam('id', 0);
    	$course_id = $this->_getParam('course_id', 0);
    	$semester_id = $this->_getParam('semester_id', 0);
    	
    	// check is AJAX request or not
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	
	  	
	  	 $registrationDB = new App_Model_Record_DbTable_StudentCourseRegistration();
		 $data = $registrationDB->isRegister($student_id,$semester_id,$course_id);
			
		
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();
		
		if(!$data){
			$data['status'] = 'false';
		}else{
			$data['status'] = 'true';	
		}                    
		
		$json = Zend_Json::encode($data);
		
		$this->view->json = $json;

    }
    
    public function noteAction(){
		//title
		$this->view->title = "Note";
		
	}
	
	public function autoSuggestAction(){
		
		$semester_id = $this->_getParam('semester_id', 0);
    	
    	// check is AJAX request or not
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	
	}
}
?>