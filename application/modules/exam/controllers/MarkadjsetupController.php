<?php
ini_set('display_errors', 'on');

require_once 'Zend/Controller/Action.php';

class Exam_MarkadjsetupController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		
		
		//title
    	$this->view->title="Mark Adjustment Setup";
    	
    	
	  //get semester
    	$oSemester = new App_Model_Record_DbTable_Semester();
    	$semester_list = $oSemester->getData();
    	$this->view->semester = $semester_list;  
    	
   	
        $semester_id=0;
         if ($this->_request->isPost()) {         	
			 $semester_id= $this->getRequest()->getPost('semester_id');
			 $this->view->semester_id = $semester_id;

         }
         
        //get Program
    	$program = new App_Model_Record_DbTable_Program();
        $program_list = $program->selectSemProgram($semester_id);
    	$this->view->program = $program_list;  

        $program_id=0;
         if ($this->_request->isPost()) {         	
			 $program_id= $this->getRequest()->getPost('program_id');
			 $this->view->program_id = $program_id;
         }
                 
         
        $program_id=0;
         if ($this->_request->isPost()) {         	
			 $program_id= $this->getRequest()->getPost('program_id');
			 $this->view->program_id = $program_id;
         }
               
         
	        
         
        //get Course
        if($program_id){
        	
         //get selected program info
         $program_info = $program->getData($program_id);
         $faculty_id= $program_info["faculty_id"];
         
    	$course = new App_Model_Record_DbTable_Course();
    	$course_list = $course->selectCourseByFaculty($faculty_id);
        $this->view->course_list = $course_list;
        }
       
         	
	     $course_id=0;
         if ($this->_request->isPost()) {         	
			 $program_id= $this->getRequest()->getPost('course_id');
			 $this->view->course_id = $course_id;
         }
         
         
	    	//paginator
	    	
			$oMark = new App_Model_Exam_DbTable_Markadjustment();
			$mark = $oMark->getPaginateData($program_id,$semester_id);
			
			if(!$mark){
					throw new Exception("There is No Data Found!");
			}else{
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($mark));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
			}
          
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add New";
    	
    	$form = new Exam_Form_Markadjsetup();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$oMark = new App_Model_Exam_DbTable_Markadjustment();
				$oMark->addData($formData);
				
				//redirect
				$this->_redirect('/exam/markadjsetup/');		
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
        
    }
    
public function editAction(){
    	//title
    	$this->view->title="Edit";
    	
    	$form = new Exam_Form_Markadjsetup();
    	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$oMark = new App_Model_Exam_DbTable_Markadjustment(); 
				$oMark->updateData($formData,$id);
				
				$this->_redirect('/exam/markadjsetup/');	
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			
    			$oMark = new App_Model_Exam_DbTable_Markadjustment(); 
    			$form->populate($oMark->getData($id));
    			
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$oMark = new App_Model_Exam_DbTable_Markadjustment(); 
    		$oMark->deleteData($id);
    	}
    		
    	$this->_redirect('/exam/markadjsetup/');
    	
    }
    
	/*public function deleteAction($id = null){
		
		 if ($this->_request->isPost()) { 		 		 		
			$id = $this->getRequest()->getPost('id');	
		 	
			  for($i=0; $i<count($id); $i++){
			  	$oMark = new App_Model_Exam_DbTable_Markadjustment(); 
    			$oMark->deleteData($id[$i]);
			  }
		 }
		     
    	$this->_redirect('/exam/markadjsetup/');
    	
    }*/

}



