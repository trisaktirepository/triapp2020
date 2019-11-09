<?php 
class Exam_ResultController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function indexAction(){
    	
    	$this->view->title = "Exam Result";

        $program_id = $this->_getParam('program_id', 0);
    	$company_id  = $this->_getParam('company_id', 0); 
    	$keyword    = $this->_getParam('keyword', ""); 
    	
    	//get Program
    	$program = new App_Model_Record_DbTable_Program();
        $program_list = $program->getData();
    	$this->view->program = $program_list;  
    	
    	/*$oCourse = new App_Model_Record_DbTable_Course();
        $courses = $oCourse->getData();
        $this->view->courses=$courses;*/

        $operatorDB = new App_Model_General_DbTable_TakafulOperator();
        $companies = $operatorDB->getData();
        $this->view->company=$companies;        
             
        $venueDB = new App_Model_General_DbTable_Venue();
		$this->view->venue = $venueDB->getData();
        
        if ($this->getRequest()->isPost()) {
			
			$program_id= $this->getRequest()->getPost('program_id');
         	$company_id = $this->getRequest()->getPost('company_id');
         	$idVenue    = $this->getRequest()->getPost('idVenue');
        	$keyword    = $this->getRequest()->getPost('keyword');
        	$exam_date  = $this->getRequest()->getPost('exam_date');
         	 
			$this->view->program_id = $program_id;
			$this->view->company_id  = $company_id;
			$this->view->keyword    = $keyword;		
			$this->view->idVenue = $idVenue;
        	$this->view->exam_date = $exam_date;	 
			
			$oStudent = new App_Model_Record_DbTable_Student();
			$student_list = $oStudent->getPaginateDistinct($program_id,$company_id,$idVenue,$keyword,$exam_date);	
				    	
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($student_list));
			$paginator->setItemCountPerPage(30);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;  
			
		}
         
    }
    
    public function resultAction(){
    	
    		$this->view->title = "View Result";    	
    		$this->_helper->layout->setLayout('result'); 
    		
    		//get registered student info
    	    $student_id = $this->_getParam('id', 1);
    		$oStudent = new App_Model_Record_DbTable_Student();
    		$student_info =  $oStudent->getRegisteredStudentInfo($student_id);    		   		
    		$this->view->student = $student_info;    	
    		    	
    		
    		//get Program
	    	$oProgram = new App_Model_Record_DbTable_Program();
	        $program = $oProgram->getData($student_info["idProgram"]);
	        $this->view->program_name    = $program["program_name"];
	        $this->view->program_id = $program["id"];   
	        
	         $oRegister = new App_Model_Record_DbTable_Registrationdetails();
	         $course_list = $oRegister->getDataByApplicantID($student_id);	
    		 $this->view->courses = $course_list;
	           		
    }   
    
    
}
?>