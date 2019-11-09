<?php
class SetupQuestion_AssessmenttypeController extends Zend_Controller_Action
{
    public function init ()
    {
        /* Initialize action controller here */
    }
    
    
    
    public function indexAction ()
    {
        
        //title
    	$this->view->title="Assessment Component";
    	
    	//course
    	$ocourse = new App_Model_Record_DbTable_Course();
        $course = $ocourse->getData();
    	$this->view->course =  $course;
       
    	if ($this->getRequest()->isPost()) {
    		
			echo $course_id  = $this->getRequest()->getPost('course_id');
			$this->view->courseid = $course_id; 
			
			//get course
	        $course = $ocourse->getData($course_id);
	    	$this->view->courseSelect =  $course;
	    
			//get assessment component
    		$assessment_component = new SetupQuestion_Model_Assessmenttype();
    		$component_list = $assessment_component->getMainComponent($course_id);
    		$this->view->component = $component_list;  
    	  
    	
    	}
//else{
//    		
//    		
//    	    $course_id  = $this->_getParam('courseid', 0); 
//    	    $this->view->courseid = $course_id;
//    
//    		if($course_id){
//    			
//	    		//get assessment component
//	    		$assessment_component = new SetupQuestion_Model_Assessmenttype();
//	    		$component_list = $assessment_component->getMainComponent($course_id);
//	    		$this->view->component = $component_list;
//    		}	
//	    		
    		
//    	}
    	
    	
    }
    
    
    public function addcomponentAction ()
    {
    	
        $storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        if (! $data) {
            $this->_redirect('index/index');
        }       
                
        //title
    	$this->view->title="Add Assessment Component";   
    	
    	
    	 if ($this->_request->isPost()) {   
			  $formData = $this->getRequest()->getPost();
			
		      $oitem = new SetupQuestion_Model_Assessmenttype();
		      
		       $infoadd["courseid"]  = $formData["courseid"];
               $infoadd["parent_id"] = 0;
               $infoadd["question_type"]   = 0;
			   $infoadd["component_name"]  = $formData["component_name"];
	           $infoadd["level"] = 0;//need to change with existing value commonly last insert data	
	           $infoadd["createddt"] = date("Y-m-d H:i:s");
			   $infoadd["createdby"] = $data->username; 	
		 					   
			  $oitem->addData($infoadd);	
			 
				
		  $this->_redirect('setup-question/assessmenttype/index/courseid/'.$formData["courseid"]);
		
        }else{ 	
  
	    	
	    	$courseid  = $this->_getParam('courseid', 0); 	    	
	    	$this->view->courseid = $courseid; 
	    	
	    	//get assessment component
			$oitem = new SetupQuestion_Model_Assessmenttype();
			$component = $oitem->getMainComponent($courseid);
			$this->view->component = $component;  
			
        }	  
    	
    }
    
    
    
    public function additemAction ()
    {
    	
        $storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        if (! $data) {
            $this->_redirect('index/index');
        }       
                
        //title
    	$this->view->title="Add Assessment Item";   
    	
    	
    	 if ($this->_request->isPost()) {   
			$formData = $this->getRequest()->getPost();
			
		
			$iteration  	   = $formData["iteration"];				
			
			for($i=1; $i<=$iteration; $i++){	

					 	$oitem = new SetupQuestion_Model_Assessmenttype();
					 	
						if($formData["item_id".$i]!=""){
							
							   $info["courseid"]  = $formData["courseid"];
			                   $info["parent_id"] = $formData["parent_id"];
			                   $info["question_type"]   = $formData["question_type".$i];
							   $info["component_name"]  = $formData["component_name".$i];
					           $info["level"]           = $i;					 
							   $info["id"]              = $formData["item_id".$i];
							  	
							   $oitem->updateData($info,$info['id']);
							  
					    }else{ 			
					 	     //insert into table
					 	    
							   $infoadd["courseid"]  = $formData["courseid"];
			                   $infoadd["parent_id"] = $formData["parent_id"];
			                   $infoadd["question_type"]   = $formData["question_type".$i];
							   $infoadd["component_name"]  = $formData["component_name".$i];
					           $infoadd["level"] = $i;	
					           $infoadd["createddt"] = date("Y-m-d H:i:s");
		 					   $infoadd["createdby"] = $data->username; 	
		   					 
					   		 $oitem->addData($infoadd);
					    }
					    
					
				 }
	      $this->_redirect('setup-question/assessmenttype/index/courseid/'.$formData["courseid"]);	
		 // $this->_redirect('setup-question/assessmenttype/additem/courseid/'.$formData["courseid"].'/id/'.$formData["parent_id"]);
		
        }else{ 	
  
	    	$id        = $this->_getParam('id', 0); //component id
	    	$courseid  = $this->_getParam('courseid', 0); 
	    	
	    	$this->view->parent_id = $id; 
	    	$this->view->courseid = $courseid; 
	    	
	    	//get assessment component
			$oitem = new SetupQuestion_Model_Assessmenttype();
			$item = $oitem->getComponentItem($id);
			$this->view->component_item = $item;  
			
        }	  
    	
    }
    
    public function delAction($id=null){

	 	$storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        if (! $data) {
            $this->_redirect('index/index');
        }  

       $id = $this->_getParam('id', 0);
       $courseid = $this->_getParam('courseid', 0);
               
       $oitem = new SetupQuestion_Model_Assessmenttype();
       $oitem->deleteData($id);
        	
       $this->_redirect('setup-question/assessmenttype/index/courseid/'.$courseid);
        
     }
    
     
    
     public function deleteAction($id=null){

	 	$storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        if (! $data) {
            $this->_redirect('index/index');
        }  

               
       $oitem = new SetupQuestion_Model_Assessmenttype();
        
        if ($this->_request->isPost()) { 			
        	$formdata = $this->getRequest()->getPost();          	
           
        	for($i=0; $i<count($formdata["id"]); $i++){  
        		 
        		 $courseid  = $formdata["courseid"];
        		 $parent_id = $formdata["parent_id"];
        		 
        		 $oitem->deleteData($formdata["id"][$i]);
        	}
        	
        	
           $this->_redirect('setup-question/assessmenttype/additem/courseid/'.$courseid.'/id/'.$parent_id);
        	
        }

        
     }
    
    
    
    public function ajaxGetCourseAction($id=null){

	 	$storage = new Zend_Auth_Storage_Session ();
		$data = $storage->read ();
		if (! $data) {
			$this->_redirect ( 'index/index' );
		}
			
    	$faculty_id = $this->_getParam('faculty_id', 0);
     
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
        //get course
    	$course = new App_Model_Record_DbTable_Course();
        $course_list = $course->getCourseByFaculty($faculty_id);    	
    	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($course_list);
		$this->view->json = $json;

    }
    
    
     public function runscriptaddAction(){
    	
    	
    	$oSyllabus = new SetupQuestion_Model_Assessmenttype();
    	  	
    	//$courseid = array('OUMH1103','SBMA1103','MPW1143_2143','MPW1113_2113','MPW1153_2153','MPW1133_2133','OUMH1203','OUMH1303','OUMM2103','OUMM3203');
    	//$courseid = array('OUMH2103');
    	$component_name = array("PART A","PART B", "PART C");
    	
    	for($m=0; $m<=9; $m++){
    		
    			echo '<pre>';
	    		$inf["component_name"] = "FINAL EXAM";
	    		$inf["question_type"] = 0;
	    		$inf["parent_id"] = 0;
	    		$inf["courseid"] = $courseid[$m];
	    		$inf["level"] = 0;
	    		$inf["createdby"] = "System";
	    		$inf["createddt"] = date("Y-m-d H:i:s");
	    		print_r($inf);
	    		echo '</pre>';
	    		$pid = $oSyllabus->addData($inf);
	    		
	    	for($i=0; $i<=2; $i++){	   
	    		 		
	    		echo '<pre>';
	    		$info["component_name"] = $component_name[$i];
	    		$info["question_type"] = 1;
	    		$info["parent_id"] = $pid;
	    		$info["courseid"] = $courseid[$m];
	    		$info["level"] = $i;
	    		$info["createdby"] = "System";
	    		$info["createddt"] = date("Y-m-d H:i:s");
	    		print_r($info);
	    		echo '</pre>';
	    		$oSyllabus->addData($info);    		
	    	}
    	}
    }
    
       
   
}





