<?php 
class Exam_DeferController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    
       
    
    
    /* =================================
       STUDENT : APPLY FOR DEFER SECTION
       ================================= */
    
    
    public function indexStudentAction(){
		//title
		$this->view->title = "Apply For Exam Defer";
		
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
	
	
	 public function applyAction(){
	 	
	 	// Note:Can apply on running semester only
	 	
	 	//title
		$this->view->title = "Apply For Exam Defer";
		
		$student_id = $this->_getParam('stud_id', 0);
		$this->view->student_id= $student_id;
		
		$studentDB = new App_Model_Record_DbTable_Student();
		$student = $studentDB->getStudent($student_id);
		$this->view->student = $student;
		
		$studentLandscape = $studentDB->getStudentProfile($student_id);		
		
		$app_id       = $studentLandscape["application_id"];
		$landscape_id = $studentLandscape["landscape_id"];
		$ic_no        = $studentLandscape["ic_no"];		
		
		
		$programDB = new App_Model_Record_DbTable_Program();
		$this->view->program = $programDB->getData($student['program_id']);		
		       		
		$semesterDB = new App_Model_Record_DbTable_Semester();		
		
		//get current semester    	
    	$semester = $semesterDB->currentSemester();    	
    	$this->view->currentsemester = $semester;    
    	$this->view->semester_id     = $semester["id"];	 
    	
		//get registered course for current semester yg belum applied for defer
		$student_course_registrationDB = new App_Model_Record_DbTable_StudentCourseRegistration();
		$this->view->courses = $student_course_registrationDB->getRegCourseExamDefer($student_id, $semester["id"]);
		
		
	 }
	 
	 public function deferCourseAction(){
	 	
	 	$auth = Zend_Auth::getInstance(); 
		
	 	
	 	if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			
			for($i=0; $i<count($formData["scr_id"]); $i++){
				
				$data["scr_id"]       = $formData["scr_id"][$i];
				$data["student_id"]   = $formData["student_id"];
				$data["semester_id"]  = $formData["semester_id"];
				$data["defer_reason"] = $formData["defer_reason"][$i];
				$data["defer_date"]   = $formData["defer_date"][$i];			
				$data["createddt"]    = date("Y-m-d H:i:s");
				$data["createdby"]    = $auth->getIdentity()->id;
				
				$deferDB = new App_Model_Exam_DbTable_ExamDefer();
				$deferDB->addData($data);
			}
			$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'defer', 'action'=>'view-apply','stud_id'=>$data["student_id"]),'default',true));
			
	 	}
	 	
	 }
	 
	  public function viewApplyAction(){
	 	
	 	//title
		$this->view->title = "Exam Defer";
		
		$student_id = $this->_getParam('stud_id', 0);
		$this->view->student_id= $student_id;
		
	
		$studentDB = new App_Model_Record_DbTable_Student();
		$student = $studentDB->getStudent($student_id);
		$this->view->student = $student;
		
		$studentLandscape = $studentDB->getStudentProfile($student_id);		
		
		$app_id       = $studentLandscape["application_id"];
		$landscape_id = $studentLandscape["landscape_id"];
		$ic_no        = $studentLandscape["ic_no"];		
		
		
		$programDB = new App_Model_Record_DbTable_Program();
		$this->view->program = $programDB->getData($student['program_id']);		
		
		//get current semester          		
		$semesterDB = new App_Model_Record_DbTable_Semester();	
    	$semester = $semesterDB->currentSemester();    	
    	$this->view->currentsemester = $semester;    
        $this->view->semester_id     = $semester["id"];	 
    	
		//get Exam Defer Application : Can apply on running semester only
		$ExamDeferDB = new App_Model_Exam_DbTable_ExamDefer();	
		$condition = array("student_id"=>$student_id,"semester_id"=>$semester["id"],"scr_id"=>null);	
		$list_application = $ExamDeferDB->getlist($condition);
		$this->view->list_application = $list_application;
		
	 }
	
	
	
	/* ===============================
       ADMIN : APPROVAL DEFER SECTION
       =============================== */
	
	public function indexAction(){
    	
    	$this->view->title = "Exam Defer Approval";
    	
    	$studentDB = new App_Model_Exam_DbTable_ExamDefer();
		
		//get active program
		$programDB = new App_Model_Record_DbTable_Program();
		$this->view->programList = $programDB->getActiveProgram();
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$student_data = $studentDB->search($formData);	
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($student_data));
			$paginator->setItemCountPerPage(50);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
		}else{
			$student_data = $studentDB->getPaginateData();
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($student_data));
			$paginator->setItemCountPerPage(50);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
		}
		
		$this->view->paginator = $paginator;

    }
    
    
     public function approvalAction(){
	 	
	 	//title
		$this->view->title = "Exam Defer Approval";
		
		$student_id = $this->_getParam('stud_id', 0);
		$this->view->student_id= $student_id;
		
		$scr_id = $this->_getParam('scr_id', 0);
		$this->view->scr_id= $scr_id;
		
		$studentDB = new App_Model_Record_DbTable_Student();
		$student = $studentDB->getStudent($student_id);
		$this->view->student = $student;
		
		$studentLandscape = $studentDB->getStudentProfile($student_id);		
		
		$app_id       = $studentLandscape["application_id"];
		$landscape_id = $studentLandscape["landscape_id"];
		$ic_no        = $studentLandscape["ic_no"];		
		
		
		$programDB = new App_Model_Record_DbTable_Program();
		$this->view->program = $programDB->getData($student['program_id']);		
		       		
		$semesterDB = new App_Model_Record_DbTable_Semester();		
		
		//get current semester    	
    	$semester = $semesterDB->currentSemester();    	
    	$this->view->currentsemester = $semester;    
    	$this->view->semester_id     = $semester["id"];		
    	    	
    		
		//get Exam Defer Application : Can apply on running semester only
		$ExamDeferDB = new App_Model_Exam_DbTable_ExamDefer();		
		$condition = array("student_id"=>$student_id,"semester_id"=>$semester["id"],"scr_id"=>null);
		$list_application = $ExamDeferDB->getlist($condition);
		$this->view->list_application = $list_application;
		
	 }
	 
	  public function approveDeferAction(){
	 	
	 	$auth = Zend_Auth::getInstance(); 
		
	 	
	 	if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$student_id  = $formData["student_id"];
			$semester_id = $formData["semester_id"];
			
			
			for($i=0; $i<count($formData["id"]); $i++){
				
				$exam_defer_id         = $formData["id"][$i];
				$data["defer_status"]  = $formData["defer_status"][$i];
				$data["approveddt"]     = date("Y-m-d H:i:s");
			    $data["approvedby"]    = $auth->getIdentity()->id;	
				
				$deferDB = new App_Model_Exam_DbTable_ExamDefer();
				$deferDB->updateData($data,$exam_defer_id);
			}
			$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'defer', 'action'=>'view-approval','stud_id'=>$student_id, 'semester_id'=>$semester_id),'default',true));
			
	 	}
	 	
	 }
	 
	  public function viewApprovalAction(){
	 	
	 	//title
		$this->view->title = "Exam Defer Approval : Completed";
		
		$student_id = $this->_getParam('stud_id', 0);
		$this->view->student_id= $student_id;
				
		$studentDB = new App_Model_Record_DbTable_Student();
		$student = $studentDB->getStudent($student_id);
		$this->view->student = $student;
		
		$studentLandscape = $studentDB->getStudentProfile($student_id);		
		
		$app_id       = $studentLandscape["application_id"];
		$landscape_id = $studentLandscape["landscape_id"];
		$ic_no        = $studentLandscape["ic_no"];		
		
		
		$programDB = new App_Model_Record_DbTable_Program();
		$this->view->program = $programDB->getData($student['program_id']);		
		       		
		$semesterDB = new App_Model_Record_DbTable_Semester();		
    	$semester = $semesterDB->currentSemester();    	
    	$this->view->semester    = $semester;    
    	$this->view->semester_id = $semester["id"];	
    		
		//get Exam Defer Application : Can apply on running semester only
		$ExamDeferDB = new App_Model_Exam_DbTable_ExamDefer();		
		$condition = array("student_id"=>$student_id,"semester_id"=>$semester["id"],"scr_id"=>null);
		$list_application = $ExamDeferDB->getlist($condition);
		$this->view->list_application = $list_application;
		
	 }
	
}

?>