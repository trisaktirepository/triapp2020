<?php
class Exam_ExamsittingController extends Zend_Controller_Action {

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function indexAction(){
    	$this->view->title="Exam Registration ID";
    	
    	
    	
    	$program_id = $this->_getParam('program_id', 0);
    	$course_id  = $this->_getParam('course_id', 0); 
    	
    	//get Program
    	$program = new App_Model_Record_DbTable_Program();
        $program_list = $program->getData();
    	$this->view->program = $program_list;  

        $oCourse = new App_Model_Record_DbTable_Course();
        $courses = $oCourse->getData();
        $this->view->courses=$courses;
        
        $venueDB = new App_Model_General_DbTable_Venue();
		$venue = $venueDB->getData();
		$this->view->venue = $venue;
		
		if ($this->getRequest()->isPost()) {
    		
			$formdata = $this->getRequest()->getPost();
			
    		//paginator
			$oStudent = new App_Model_Record_DbTable_Student();	
			$student = $oStudent->PaginateSearch($formdata);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($student));
			$paginator->setItemCountPerPage(50);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));			
			$this->view->student_list = $paginator;
			
    	}else{
    		
	    	//paginator
			//paginator
			$oStudent = new App_Model_Record_DbTable_Student();	
			$student = $oStudent->PaginateSearch();			
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($student));
			$paginator->setItemCountPerPage(50);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));			
			$this->view->student_list = $paginator;
    	}
    	
			 
    }
    
    
    public function slipAction(){
    		$this->view->title = "Examination Sitting";    	
    		$this->_helper->layout->setLayout('result'); 
    		
    		//get student info
    	    $student_id = $this->_getParam('ID','');
    	    $condition = array ('ID' => $student_id); 
    	   
    		$oStudent = new App_Model_Record_DbTable_Student();
    		$student_info =  $oStudent->search($condition);  
    		$this->view->student = $student_info;
    		
    		
    		
    }
}