<?php 
class AdmissionRecord_AddDropController extends Zend_Controller_Action {
	
	public function indexAction(){
		$this->view->title = "Add & Drop Course";
		
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
		
	
	public function courseAction(){
		//title
		$this->view->title = "Add & Drop Course - Course Selection";
		
		$student_id = $this->_getParam('student_id', 0);
		
		$studentDB = new App_Model_Record_DbTable_Student();
		$student = $studentDB->getStudent($student_id);
		$this->view->student = $student;		
		
		$programDB = new App_Model_Record_DbTable_Program();
		$this->view->program = $programDB->getData($student['program_id']);
					
		//get current semester
    	$semesterDB = new App_Model_Record_DbTable_Semester();				
    	$semester = $semesterDB->currentSemester();    	
    	$this->view->currentsemester = $semester;      	
		$this->view->semester_id = $semester["id"];
		$semester_id = $semester["id"];
		
		//registered course
		$student_course_registrationDB = new App_Model_Record_DbTable_StudentCourseRegistration();
		$this->view->courses = $student_course_registrationDB->getRegistrationData($student_id, $semester_id);

		//program requirement
		$programRequirementDB = new App_Model_Record_DbTable_ProgramRequirement();
		$this->view->requirement = $programRequirementDB->getCourseRequirement($student['program_id']);
		
		
		
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();			
			
			//Check for already registered for particular semester
			$studentSemesterDB = new App_Model_Record_DbTable_StudentSemester();
			if(!$studentSemesterDB->isRegistered($student_id,$semester_id)){
				//redirect
				$this->_redirect($this->view->url(array('module'=>'admission-record','controller'=>'add-drop', 'action'=>'not-register','student_id'=>$student_id, 'semester_id'=>$semester_id),'default',true));
			}else{
						
			/*	$studentDB = new App_Model_Record_DbTable_Student();
				$student = $studentDB->getStudent($student_id);
				$this->view->student = $student;
				
				$programDB = new App_Model_Record_DbTable_Program();
				$program = $programDB->getData($student['program_id']);
				$this->view->program = $program;
						
				$semester_id = $this->_getParam('semester_id', 0);
				$this->view->semester_id = $semester_id;
				$semesterDB = new App_Model_Record_DbTable_Semester();
				$this->view->semester = $semesterDB->getData($semester_id);
			*/	
								
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
		
	}
	
	public function notRegisterAction(){
		//title
		$this->view->title = "Add & Drop Course - Semester Selection";
		
		$student_id = $this->_getParam('student_id', 0);
		$semester_id = $this->_getParam('semester_id', 0);
		
		
		$studentDB = new App_Model_Record_DbTable_Student();
		$student = $studentDB->getStudent($student_id);
		$this->view->student = $student;
		
		$programDB = new App_Model_Record_DbTable_Program();
		$this->view->program = $programDB->getData($student['program_id']);
		
		$this->view->semester_id = $semester_id;
		$semesterDB = new App_Model_Record_DbTable_Semester();
		$this->view->semesterlist = $semesterDB->getData();
		$sem_error = $semesterDB->getData($semester_id);
		
		$this->view->noticeError = "This student is not registered for semester ".$sem_error['name'];
	}
	
	public function receiptAction(){
		$this->view->title = "Add & Drop Course - Semester Selection";
		
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
		
		$add = $this->_getParam('add', null);
		
		$drop = $this->_getParam('drop', null);
		
		/*
		 * Begin Transaction
		 */
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->beginTransaction();
		
		try{
			
			//add course & course history
			if($add!=null){
				$student_course_registrationDB = new App_Model_Record_DbTable_StudentCourseRegistration();
				
				$courses = explode(",", $add);
				foreach ($courses as $course){
					$data = array(
							'student_id' => $student_id,
							'semester_id' => $semester_id,	
							'course_id' => $course,
							'course_status_id' => 1,
							'entry_date' => date('Y-m-d H:i:a')
							);
					$student_course_registrationDB->addData($data);
				}
			}
			
			//drop course & course history
			if($drop!=null){
				$student_course_registrationDB = new App_Model_Record_DbTable_StudentCourseRegistration();
				$student_course_registrationHistoryDB = new App_Model_Record_DbTable_StudentCourseRegistrationHistory();
				
				
				
				$register_id = explode(",", $drop);
				
				foreach ($register_id as $reg_id){
					$reg_info = $student_course_registrationDB->getData($reg_id);	
					
					//update student_course_registration
					$data = array(
							'student_id' => $student_id,
							'semester_id' => $semester_id,	
							'course_id' => $reg_info['course_id'],
							'course_status_id' => 3,
							'entry_date' => date('Y-m-d H:i:a')
							);
							
					$id = $student_course_registrationDB->updateData($data, $reg_id);
					
					//add student_course_registration_history
					$data_history = array(
						'student_id' => $student_id,
						'semester_id' => $semester_id,		
						'course_id' => $reg_info['course_id'],
						'course_status_id' => 3,
						'entry_date' => date('Y-m-d H:i:a')
					);
					$student_course_registrationHistoryDB->addData($data_history,$reg_id);
					
				}
			}
			
			$db->commit();
			
		}catch (Exception $e){
			$db->rollBack();
    		echo $e->getMessage();
    		var_dump($e->getTrace());
		}
		
		$student_course_registrationDB = new App_Model_Record_DbTable_StudentCourseRegistration();
		$this->view->courses = $student_course_registrationDB->getRegistrationData($student_id, $semester_id);
		
		$student_course_registrationHistoryDB = new App_Model_Record_DbTable_StudentCourseRegistrationHistory();
		$this->view->history = $student_course_registrationHistoryDB->getRegistrationHistoryData($student_id, $semester_id);
	}
}    
?>