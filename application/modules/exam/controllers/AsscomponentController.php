<?php
require_once 'Zend/Controller/Action.php';

class Exam_AsscomponentController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
 
        //title
    	$this->view->title="Mark Distribution";
    	
    	$program_id = $this->_getParam('program_id', 0);
    	$course_id  = $this->_getParam('course_id', 0); 
    	
    	//get Program
    	$program = new App_Model_Record_DbTable_Program();
        $program_list = $program->getData();
    	$this->view->program = $program_list;  


        $oCourse = new App_Model_Record_DbTable_Course();
        $courses = $oCourse->getData();
        $this->view->courses=$courses;
    	
        
         if ($this->_request->isPost()) {   
			 $course_id  = $this->getRequest()->getPost('course_id');	
			 $program_id = $this->getRequest()->getPost('program_id');	
         
             $this->view->course_id  = $course_id;
             $this->view->program_id = $program_id;
         
            $oComponent = new App_Model_Exam_DbTable_Asscomponent();
            $rs_component = $oComponent->getAsscomponent($program_id,$course_id);
            $this->view->rs_component = $rs_component;   
          }      

    	  
    }
    
    
    public function addformAction(){
    	
        //title
    	$this->view->title="Assesment Component Setup";
    	
    	//get Program
    	
        $program_id = $this->_getParam('program_id', 0);
    	$course_id = $this->_getParam('course_id', 0); 
        
    	$oProgram = new App_Model_Record_DbTable_Program();
    	$program  = $oProgram->getData($program_id);    	
    	
    	$oCourse = new App_Model_Record_DbTable_Course();
    	$course  = $oCourse->getData($course_id);
    	
    	//To list Branches
    	$oBranch = new App_Model_General_DbTable_Branch();
    	$branch = $oBranch->getData();
    	$this->view->branches = $branch;
    	
 		$this->view->program_id = $program_id;
 		$this->view->course_id = $course_id;
 		$this->view->program_name = $program["program_name"];
    	$this->view->course_name  = $course["code"].' - '.$course["name"];
    	$this->view->mark_type  = $course["mark_distribution_type"];
    		
		if($course["mark_distribution_type"]==1) $this->view->mark_tname = "By Component Weightage";
		if($course["mark_distribution_type"]==2) $this->view->mark_tname = "By Overall Mark";
		
    	
    	//to list Component
    	$componentDB = new App_Model_Exam_DbTable_Component();
    	$rs_component = $componentDB->getInfo(0);
        $this->view->components = $rs_component;
        
        
        
        if ($this->_request->isPost()) {   
			$formData = $this->getRequest()->getPost();			
		
			$iteration  		= $formData["iteration"];
			$data["course_id"]  = $formData["course_id"];
			$data["program_id"] = $formData["program_id"];
			
				 		 
				 for($i=1; $i<=$iteration; $i++){
				 	
				 	$data["component_id"]= $formData["component_id".$i];
				 	$data["component_weightage"]= $formData["component_weightage".$i];
				 	$data["component_passing_mark"]= $formData["component_passing_mark".$i];
				 	$data["component_total_mark"]= $formData["component_total_mark".$i];				 					
				    
				 	//insert into table
				    $oComponent = new App_Model_Exam_DbTable_Asscomponent();
				    $oComponent->addAsscomponent($data);
				 }
				 
			 $this->view->noticeMessage = "Assessment Component has been successfully saved.";
			  //redirect
		    $this->_redirect('/exam/asscomponent/manage/program_id/'.$data["program_id"].'/course_id/'.$data["course_id"]);	
		 }
         
        
    }
    
    
	public function addAction()
	{		
	    if ($this->_request->isPost()) {   
			$formData = $this->getRequest()->getPost();
			
		
			$iteration  		= $formData["iteration"];
			$data["course_id"]  = $formData["course_id"];
			$data["program_id"] = $formData["program_id"];
				 		 
				 for($i=1; $i<=$iteration; $i++){
				 	
				 	$data["component_id"]= $formData["component_id".$i];
				 	$data["component_weightage"]= $formData["component_weightage".$i];
				 	$data["component_passing_mark"]= $formData["component_passing_mark".$i];
				 	$data["component_total_mark"]= $formData["component_total_mark".$i];
				 					
				    
				 	//insert into table
				    $oComponent = new App_Model_Exam_DbTable_Asscomponent();
				    $oComponent->addAsscomponent($data);
				 }
				
				
		  //redirect
		  $this->_redirect('/exam/asscomponent/index/program_id/'.$data["program_id"].'/course_id/'.$data["course_id"]);	
			
         }
         
         
				
    }//end addAction
    
    public function manageAction(){
    		$this->view->title="Modify Assesment Component";
    		
    		$program_id = $this->_getParam('program_id', 0);
    		$course_id = $this->_getParam('course_id', 0); 

    		$oProgram = new App_Model_Record_DbTable_Program();
    		$program = $oProgram->getData($program_id);    		
    		
    		$oCourse = new App_Model_Record_DbTable_Course();
    		$course  = $oCourse->getData($course_id);
    		    		
	 		$this->view->program_id = $program_id;
	 		$this->view->course_id  = $course_id;
	 		$this->view->program_name = $program["program_name"];
    		$this->view->course_name  = $course["code"].' - '.$course["name"];
    		$this->view->mark_type  = $course["mark_distribution_type"];
    		
    		if($course["mark_distribution_type"]==1) $this->view->mark_tname = "By Component Weightage";
    		if($course["mark_distribution_type"]==2) $this->view->mark_tname = "By Overall Mark";
    		
            $oComponent = new App_Model_Exam_DbTable_Asscomponent();
            $rs_component = $oComponent->getAsscomponent($program_id,$course_id);
            $this->view->rs_component = $rs_component;
            
            //to list Component
    		$componentDB = new App_Model_Exam_DbTable_Component();
    		$list_component = $componentDB->getInfo(0);
       		$this->view->components = $list_component;
       		
       		
       		if ($this->_request->isPost()) {   
				 $formData = $this->getRequest()->getPost();
				
			    $iteration          = $formData["iteration"];			    
				$data["course_id"]  = $formData["course_id"];
				$data["program_id"] = $formData["program_id"];
				 
				 for($i=1; $i<=$iteration; $i++)
				 {
				 	
					 $data["id"]= $formData["id".$i];					 	
					 $data["component_id"]= $formData["component_id".$i];					 							 			
					 $data["component_total_mark"]= $formData["component_total_mark".$i];
					 			
					if(isset($formData["component_weightage".$i]))	{
					 	$data["component_weightage"]= $formData["component_weightage".$i];
					}					    				

				 	$oComponent = new App_Model_Exam_DbTable_Asscomponent();
				 	
					if(isset($data['id'])){		
						 //update
						 $oComponent->updateAsscomponent($data,$data['id']);
				    }else{ 			
				 	     //insert into table				 
				   		 $oComponent->addAsscomponent($data);
				    }
				 }
				 
				  $this->view->noticeMessage = "Assessment Component has been successfully saved.";
				
	         }
            
    }
    
    
    
    
    public function deleteAction(){
        	
    	 if ($this->_request->isPost()) {   
    	 	
			 $id        = $this->getRequest()->getPost('id');	
			 $course_id = $this->getRequest()->getPost('course_id');	
			 $program_id= $this->getRequest()->getPost('program_id');			
			  
			 for($i=0; $i<count($id); $i++){
			 	//delete component
				$oComponent = new App_Model_Exam_DbTable_Asscomponent();
    			$oComponent->deleteAsscomponent($id[$i]);
    		 }
		 }
		
		   //redirect			
		   $this->_redirect('/exam/asscomponent/manage/program_id/'.$program_id.'/course_id/'.$course_id);
    	
    }
    
    
	
}

?>