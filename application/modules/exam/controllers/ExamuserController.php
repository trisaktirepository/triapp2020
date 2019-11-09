<?php
class Exam_ExamuserController extends Zend_Controller_Action {

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function indexAction(){
    	$this->view->title="User Access";
    	
         if ($this->_request->isPost()) {   
			 $keywords = $this->getRequest()->getPost('keywords');

			 //search user
	    	 $oUser = new SystemSetup_Model_DbTable_User();
	    	 $list_user = $oUser->searchUser($keywords);	
	    	 $this->view->list_user = $list_user;
         }
         
    	
    	
    }
    
    public function assignAction(){
    	$this->view->title="User Access : Assign user to Programme";
	   	 if ($this->_request->isPost()) {   
				 $id= $this->getRequest()->getPost('id');
				 $this->view->user_id=$id;				 
				 
				 //to list all programmes 
				 $oProgram = new App_Model_Record_DbTable_Program();
				 $program_list = $oProgram->getData();
			     $this->view->program = $program_list;   
    			     
			     $program_id= $this->getRequest()->getPost('program_id');
				 $this->view->program_id=$program_id;
				
				
			     if($program_id){
			     	//get course under this program
					 $oCourse = new App_Model_Record_DbTable_AcademicLandscape();
				 	 $list_course = $oCourse->getCourseExamUser($program_id,$id);
				 	 $this->view->list_course=	$list_course;
	   	 		 }
	    }
    }
    
       
 	public function addAction(){
    	$auth = Zend_Auth::getInstance();
    	$oExamuser = new App_Model_Exam_DbTable_ExamUser();
			 
    		
    	if ($this->_request->isPost()) {   
			 $user_id   = $this->getRequest()->getPost('user_id');
			 $program_id= $this->getRequest()->getPost('program_id');
			 $course_id = $this->getRequest()->getPost('course_id');			
			 		 
			 	
			 	$data["user_id"]   =$user_id;
			 	$data["program_id"]=$program_id;
			 	$data["createddt"] =date("Y-m-d H:i:s");
			 	$data["createdby"] =$auth->getIdentity()->id;

			 	for($m=0; $m<count($course_id); $m++){
			 		$data["course_id"]=$course_id[$m];
			 		
			 		//insert data			 		
			 		$oExamuser->addData($data);
			 	}			 	
			 
    	}
    	
    		$this->_redirect('/exam/examuser/');
    	
    }
    
     public function unassignAction(){
    	$this->view->title="User Access : Remove user to Program";
	   	 if ($this->_request->isPost()) {   
				 $id= $this->getRequest()->getPost('id');
				 $this->view->user_id=$id;
				 
				 $program_id= $this->getRequest()->getPost('program_id');
				 $this->view->program_id=$program_id;
				 
				 //to list all programmes 
				 $oProgram = new App_Model_Record_DbTable_Program();
				 $program_list = $oProgram->getData();
			     $this->view->program = $program_list;      
			     
	   			 if($program_id){
					 //get course under this program
					 $oCourse = new App_Model_Exam_DbTable_ExamUser();
				 	 $list_course = $oCourse->getCourseExamUser($program_id,$id);
				 	 $this->view->list_course=	$list_course;
	   	 		 }
	    	}
    }
    
    
    
	public function removeAction(){
    	$auth = Zend_Auth::getInstance();
    	$oExamuser = new App_Model_Exam_DbTable_ExamUser();
			 
    		
    	if ($this->_request->isPost()) {   
			    $exam_user_id = $this->getRequest()->getPost('course_id');		
			 		 
								 	
			 	for($m=0; $m<count($exam_user_id); $m++){			 	
			 		//insert data			 		
			 		$oExamuser->deleteData($exam_user_id[$m]);
			 	}
			 
    	}
    	
    		$this->_redirect('/exam/examuser/');
    	
    }
    
}
?>