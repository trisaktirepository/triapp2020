<?php

require_once 'Zend/Controller/Action.php';

class Exam_AsscompitemController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

   /* public function indexAction()
    {
        // action body
        //title
    	$this->view->title="Assesment Component Item Setup";
    	
    	//get Program
    	$program = new App_Model_Record_DbTable_Program();
        $program_list = $program->selectProgram();
    	$this->view->program = $program_list; 
    	 

        $program_id=0;
         if ($this->_request->isPost()) {         	
			 $program_id= $this->getRequest()->getPost('program_id');
			 $this->view->program_id = $program_id;
         }
    	    	
    	//get course thru academic landscpe  
    	$alcourses = new App_Model_Record_DbTable_AcademicLandscape();
    	$alcourse_list = $alcourses->selectCourseAcademicLandscape($program_id);
    	$this->view->courses = $alcourse_list;
    	
         $course_id=0;
         if ($this->_request->isPost()) {   
			 $course_id = $this->getRequest()->getPost('course_id');
			 $this->view->course_id = $course_id;	
         }  
         
    
         	
    }*/
    
    
	public function additemformAction()
    {    	  
    	
    	$this->view->title="Assesment Component Setup : Add item";
    	
       	$id = $this->_getParam('id', 0);//main component id
       	
       	//get main component info
       	$oComponent = new App_Model_Exam_DbTable_Asscomponent();
        $rs_component = $oComponent->getInfo($id); 
        $this->view->component = $rs_component;
         
        
    }
    
    public function addAction()
	{		
	    if ($this->_request->isPost()) {   
			$formData = $this->getRequest()->getPost();
			
		
			$iteration  = $formData["iteration"];			
		   	$data["component_id"]  = $formData["component_id"]; //main component id
					
			 		 
			 for($i=1; $i<=$iteration; $i++){
			 	
			 	$data["component_item_name"]= $formData["component_name".$i];
			 		$data["component_item_weightage"]= $formData["component_weightage".$i];
			 			$data["component_item_passing_mark"]= $formData["component_passing_mark".$i];
			 				$data["component_item_total_mark"]= $formData["component_total_mark".$i];
			 					
			    
			 	//insert into table
			    $oComponent = new App_Model_Exam_DbTable_Asscompitem();
			    $oComponent->addAsscomponentitem($data);
			 }
				
			
				
		  //redirect
		  $this->_redirect('/exam/asscompitem/manageitem/id/'.$data["component_id"]);	
			
         }
         
         
				
    }//end addAction
    
    
    public function manageitemAction(){
    	
    	$this->view->title="Assesment Component Setup : Edit item ";
    	
        $id = $this->_getParam('id', 0);//main component id
       	$program_id = $this->_getParam('program_id', 0);
    	$course_id = $this->_getParam('course_id', 0); 

     	$this->view->course_id = $course_id;
    	$this->view->program_id = $program_id;
    	
    	//get course
    	$oCourse = new App_Model_Record_DbTable_Course();
    	$course = $oCourse->getData($course_id);
    	$this->view->coursename = $course["name"];
    	$this->view->mark_type  = $course["mark_distribution_type"];
    	
    	if($course["mark_distribution_type"]==1) $this->view->mark_tname = "By Component Weightage";
    	if($course["mark_distribution_type"]==2) $this->view->mark_tname = "By Overall Mark";
    		
       	
       	//get main component info
       	$oComponent = new App_Model_Exam_DbTable_Asscomponent();
        $rs_component = $oComponent->getInfo($id); 
        $this->view->component = $rs_component;
                
        
        //get item component info
       	$oComponentItem = new App_Model_Exam_DbTable_Asscompitem();
        $rs_component_item = $oComponentItem->getCompitemByCompId($id); 
        $this->view->component_item = $rs_component_item;
        
        //get list component
        $componentDB = new App_Model_Exam_DbTable_Component();
    	$list_component = $componentDB->getInfo($rs_component["component_id"]);
        $this->view->components = $list_component;
        
        
       
          	
            
    }
    
    
    public function modifyitemAction(){
    	
	      if ($this->_request->isPost()) {   
				
	     		$formData = $this->getRequest()->getPost();
				
			    $iteration  = $formData["iteration"];
			    $id = $this->_getParam('component_id', 0);//main component id
				$course_id  = $formData["course_id"];
				$program_id = $formData["program_id"];
				 
				 for($i=1; $i<=$iteration; $i++){
				 	
					  
					
					 	$oComponent = new App_Model_Exam_DbTable_Asscompitem();
					 	 
						if(isset($formData["id".$i])){
							
							 //update
							  $data_id = $formData["id".$i];
							  $upddata["component_item_weightage"]= $formData["component_item_weightage".$i];
						      $upddata["component_item_passing_mark"]= $formData["component_item_passing_mark".$i];
					          $upddata["component_item_total_mark"]= $formData["component_item_total_mark".$i];					    				
                   
							  $oComponent->updateAsscomponentitem($upddata,$data_id);
					    }else{ 			
					 	     //insert into table
					 	     $data["component_id"]  = $formData["component_id"]; //parent component id
					 	     $data["component_item_id"]= $formData["component_item_id".$i];
					 		 $data["component_item_weightage"]= $formData["component_item_weightage".$i];
					 	     $data["component_item_passing_mark"]= $formData["component_item_passing_mark".$i];
					         $data["component_item_total_mark"]= $formData["component_item_total_mark".$i];					    				
                    				 
					   		 $oComponent->addAsscomponentitem($data);
					    }
				 }
				 
				 $this->view->noticeMessage = "Assessment Component Item has been successfully saved.";
				
	         }
	       
	      //redirect
		   $this->_redirect('/exam/asscompitem/manageitem/id/'.$id.'/program_id/'.$program_id.'/course_id/'.$course_id);
		 
    }
    
     public function deleteitemAction(){
        	
    	 if ($this->_request->isPost()) {   
    	 	
			 $id        	= $this->getRequest()->getPost('id');	
			 $component_id  = $this->getRequest()->getPost('component_id');	
			 $course_id  =  $this->getRequest()->getPost('course_id');	
			 $program_id =  $this->getRequest()->getPost('program_id');	
				
			  
			 for($i=0; $i<count($id); $i++){
			 	//delete component
				$oCompitem = new App_Model_Exam_DbTable_Asscompitem();
    			$oCompitem->deleteAsscomponentitem($id[$i]);
    		 }
		 }
		
		   //redirect			
		    $this->_redirect('/exam/asscompitem/manageitem/id/'.$component_id.'/program_id/'.$program_id.'/course_id/'.$course_id);	
		 
    	
    }
    
	
}
