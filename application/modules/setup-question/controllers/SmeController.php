<?php
class SetupQuestion_SmeController extends Zend_Controller_Action
{
    public function init ()
    {
        /* Initialize action controller here */
    }
    
    public function indexAction ()
    {
        $storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        if (! $data) {
            $this->_redirect('index/index');
        }
             
        //title
    	$this->view->title="Search Assessment Component";
    	
    	$faculty_id = $this->_getParam('faculty_id', 0);
    	$course_id  = $this->_getParam('course_id', 0); 
               
    
    	//get faculty
//    	$faculty = new SetupQuestion_Model_Faculty();
//        $faculty_list = $faculty->fetchAll();
//    	$this->view->faculty = $faculty_list;  
   
    	/*//get course
    	$course = new SetupQuestion_Model_Course();
        $course_list = $course->fetchAll();
    	$this->view->course = $course_list; */ 
    	
    		
			if($this->getRequest()->getPost('course_id')){				
				
				$faculty_id = $this->getRequest()->getPost('faculty_id');
				$course_id  = $this->getRequest()->getPost('course_id');
				
				$this->view->faculty_id = $faculty_id;
	    	    $this->view->course_id = $course_id;
				
	    	        //get assessment component
		    		$assessment_component = new SetupQuestion_Model_Assessmenttype();
		    		$component_list = $assessment_component->getMainComponent($course_id);
		    		$this->view->component = $component_list;
			}
    	
    	
        
    }
    
    
    
     public function addfrmAction ()
    {
        $storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        if (! $data) {
            $this->_redirect('index/index');
        }
            
        //title
    	$this->view->title="Add Question";
    	
    	
		if ($this->_request->isPost()) { 
			$formdata = $this->getRequest()->getPost();  
						
			$course_id= $this->_getParam('course_id', 0);
	    	$this->view->course_id = $course_id;
	    	
	    	$topic_id= $this->_getParam('topic_id', 0);
	    	$this->view->topic_id = $topic_id;  
	    	
	    	$assessment_type = $this->_getParam('assessment_type', 0);
	    	$this->view->assessment_type = $assessment_type;
	    	
	    	$qtype= $this->_getParam('question_type', 0);
	    	$this->view->qtype = $qtype;
	    	
	    	$taxanomy_level= $this->_getParam('taxanomy_level', 0);
	    	$this->view->taxanomy_level = $taxanomy_level;	    	
	    	
	    	$status = $this->_getParam('status', 0);
	    	$this->view->status = $status;
	    	
	    	/*=============================ESSAY==============================*/
	    	if($qtype==1){ 
	    		
	    		       
	    		
			    	    $info["course_id"]    = $formdata["course_id"];
					    $info["topic_id"]     = $formdata["topic_id"];
					    $info["assessment_type"]= $formdata["assessment_type"];
					    $info["question_type"]= $formdata["question_type"];
					    $info["taxanomy_level"]= $formdata["taxanomy_level"];
					    $info["difficulty_level"]= $formdata["difficulty_level"];
					    $info["status"]       = $formdata["status"];
					    $info["point"]        = $formdata["point"];
					    $info["createddt"]    = date("Y-m-d H:i:s");
					    $info["createdby"]    = $data->username; 
					 
					    $question_main = new Question_Model_QuestionMain();
					    $qmid = $question_main->addData($info);
					    
					    
					    //Note to developer: to change kalo pakai fckeditor textearea name ada prefix #f
					    $total = $formdata["total_language"];
					    
					    for($i=1; $i<=$total; $i++){
					    	
					    	if($formdata["editor"]=="2"){//FCKEditor
					    	
							    $q["question"]         = stripslashes($formdata["fquestion".$i]);			  
							    $q["answer"]           = stripslashes($formdata["fanswer".$i]);			   
							    $q["language"]         = $i;
							    $q["question_main_id"] = $qmid;
							    
					    	}else{ //HTML EDITOR
					    		
					    		$q["question"]         = stripslashes($formdata["question".$i]);			  
							    $q["answer"]           = stripslashes($formdata["answer".$i]);			   
							    $q["language"]         = $i;
							    $q["question_main_id"] = $qmid;
					    	}
						   
						   $question = new Question_Model_Question();
						   $id = $question->addData($q);
					    }
	    	}  
	    	
	    	/*=============================MCQ==============================*/
	    	if($qtype==2){ //MCQ
	    		
	    		
			    	    $info["course_id"]    = $formdata["course_id"];
					    $info["topic_id"]     = $formdata["topic_id"];
					    $info["assessment_type"]= $formdata["assessment_type"];
					    $info["question_type"]= $formdata["question_type"];
					    $info["taxanomy_level"]= $formdata["taxanomy_level"];
					    $info["difficulty_level"]= $formdata["difficulty_level"];
					    $info["status"]       = $formdata["status"];
					    $info["point"]        = $formdata["point"];
					    $info["createddt"]    = date("Y-m-d H:i:s");
					    $info["createdby"]    = $data->username; 
					 
					    $question_main = new Question_Model_QuestionMain();
					    $qmid = $question_main->addData($info);
					    
					    $total = $formdata["total_language"]; 
					     
				     	for($i=1; $i<=$total; $i++){
				     		
				     		$q["question"]          = stripslashes($formdata["question".$i]);		
							$q["language"]          = $i;
							$q["question_main_id"]  = $qmid;		

							$question = new Question_Model_Question();
							$qid  = $question->addData($q);   
							
							
							for($j=1; $j<=4; $j++){ 
							    $a["question_main_id"]  = $qmid;	 
							    $a["question_id"]       = $qid;	 
							    $a["answer"]            = stripslashes($formdata["answer".$i.$j]);	
							    
							   if($formdata["correct".$i]==$j)
							      $a["correct_answer"] =1;
							   else 
							      $a["correct_answer"] =0;
							       
							       
								$answer = new Question_Model_QuestionMcqAnswer();
								$answer->addData($a); 		
							} 
							 
							
				     	}
	    		
	    	}
			 
	    	
			   //redirect
			   //$this->_redirect('/question/sme/');
			   $this->_redirect('question/sme/preview/id/'.$qmid);
	    	
			  	
		
		}else{	
    
	    	 
	        $faculty_id = $this->_getParam('faculty_id', 0);
	    	$course_id  = $this->_getParam('course_id', 0); 
	    	$component_id  = $this->_getParam('atype', 0); 
	    	$qtype  = $this->_getParam('qtype', 0); 
	    	
	    	$this->view->faculty_id = $faculty_id;
	    	$this->view->course_id = $course_id;    	
	    	$this->view->component_id = $component_id;    	
	    	$this->view->qtype = $qtype;    	
	        
	    	
	    	//get faculty
	    	$faculty = new SetupQuestion_Model_Faculty();
	        $faculty_list = $faculty->fetch($faculty_id);
	    	$this->view->faculty_name = $faculty_list["faculty_name"];  
	   
	    	
	    	//get course
	    	$course = new SetupQuestion_Model_Course();
	        $course_list = $course->getCourseByCourseid($course_id);
	    	$this->view->course = $course_list;      
	    		    	
	    	
	    	//get topic
	    	$syllabus = new SetupQuestion_Model_Syllabus();
	        $syllabus= $syllabus->getTopicByCourseid($course_id);
	        $this->view->syllabus = $syllabus;      	
	    	
	    	
	    	//get taxanomy level
	    	$taxanomy = new SetupQuestion_Model_Taxanomy();
	    	$taxanomy = $taxanomy->fetchAll();
	    	$this->view->taxanomy = $taxanomy;
	    	
	    	//get assessment type
	    	$assessment = new SetupQuestion_Model_Assessmenttype();
	    	$assessment = $assessment->fetch($component_id);
	    	$this->view->assessment = $assessment;
		}
    	
    	
    }
    
   
    
    
    public function viewAction(){
    	
        $storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        if (! $data) {
            $this->_redirect('index/index');
        }
                     
        //title
    	$this->view->title="List of Question";
    	
		//$course_id     = $this->_getParam('course_id', 0); 
    	//$component_id  = $this->_getParam('atype', 0); 
    	
    	$this->view->course_id = $course_id;    	
	    $this->view->component_id = $component_id;  
	    $this->view->username = $data->username;  	
	        
	    //get faculty
    	$faculty = new SetupQuestion_Model_Faculty();
        $faculty_list = $faculty->fetchAll();
    	$this->view->faculty = $faculty_list;  
    	
     	//get topic
    	$syllabus = new SetupQuestion_Model_Syllabus();
        $syllabus= $syllabus->getTopicByCourseid($course_id);
        $this->view->syllabus = $syllabus;      	
    	
    	//get topic
    	$syllabus = new SetupQuestion_Model_Syllabus();
        $syllabus= $syllabus->getTopicByCourseid($course_id);
        $this->view->syllabus = $syllabus;   

        //get status
		$oStatus = new Systemsetup_Model_Status();
	    $status = $oStatus->fetchAll();
	    $this->view->status = $status;	

	  

    	//get question
    	$question_main = new Question_Model_QuestionMain();
    	
    	if ($this->getRequest()->isPost()) {
           
                $formdata = $this->getRequest()->getPost(); 
                
            	$faculty_id = $formdata["faculty_id"];
				$course_id  = $formdata["course_id"];
				$topic_id   = $formdata["topic_id"];
				$status     = $formdata["status"];
				$orderby    = $formdata["orderby"];
				$question    = $formdata["question"];
				
				$this->view->faculty_id = $faculty_id;
	    	    $this->view->course_id = $course_id;
	    	    $this->view->topic_id = $topic_id;
	    	    $this->view->status = $status;
	    	    $this->view->orderby = $orderby;
	    	    $this->view->question = $question;
				
                
                Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
          
                $select = $question_main->view($formdata);
                $paginator = Zend_Paginator::factory($select);
                $paginator->setItemCountPerPage(10);
                $paginator->setCurrentPageNumber($this->_getParam('page', 1));
                $this->view->paginator = $paginator;
                $this->view->searchParams = array('course_id'=>$course_id, 'topic_id'=>$topic_id, 'status'=>$status, 'question'=>$question,'orderby'=>$orderby);

                 
           
        } else {
            
        	Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
            
            $select = $question_main->viewAll();
            $paginator = Zend_Paginator::factory($select);
            $paginator->setItemCountPerPage(50);
            $paginator->setCurrentPageNumber($this->_getParam('page', 1));
            $this->view->paginator = $paginator;
           
            
            
        }
    	
    }
    
     public function previewAction(){
    	
        $storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        if (! $data) {
            $this->_redirect('index/index');
        }
                     
        //title
    	$this->view->title="Preview Question";
    	
    	$q_id= $this->_getParam('id', 0);
    	$this->view->q_id = $q_id;    	
    	
    	//get question main
    	$question_main = new Question_Model_QuestionMain();
    	$question_main = $question_main->preview($q_id);
    	$this->view->q = $question_main;
    	
    	//get question
    	$question = new Question_Model_Question();
    	$question = $question->preview($q_id);
    	$this->view->question = $question;    
    	
		//get assessment type
    	$assessment = new SetupQuestion_Model_Assessmenttype();
    	$assessment = $assessment->getComponentName($question_main["assessment_type"]);
    	$this->view->assessment = $assessment;
    	
    	
    	//get course
    	$course = new SetupQuestion_Model_Course();
        $course_list = $course->getCourseByCourseid($question_main["course_id"]);
    	$this->view->course = $course_list;     
		    	
    	
    }
    
    
    public function editAction(){
    	
        $storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        if (! $data) {
            $this->_redirect('index/index');
        }
                     
        //title
    	$this->view->title="Edit Question";
    	
    	    	
    	if ($this->_request->isPost()) { 
    		$formdata = $this->getRequest()->getPost();   	
    		    
    	
    			/*=============================ESSAY==============================*/
	    	if($formdata["question_type"]==1){ 
	    			   
    		    $info["course_id"]       = $formdata["course_id"];
			    $info["topic_id"]        = $formdata["topic_id"];
			    $info["assessment_type"] = $formdata["assessment_type"];
			    $info["question_type"]   = $formdata["question_type"];
			    $info["taxanomy_level"]  = $formdata["taxanomy_level"];
			    $info["difficulty_level"]= $formdata["difficulty_level"];
			    $info["status"]          = $formdata["status"];
			    $info["point"]           = $formdata["point"];
			    $info["modifydt"]       = date("Y-m-d H:i:s");
			    $info["modifyby"]       = $data->username; 	
    		 			 
			  
			    $question_main = new Question_Model_QuestionMain();
			    $question_main->updateData($info,$formdata["q_id"]);
			    
			    $total = $formdata["total_language"];
			    
			    for($i=1; $i<=$total; $i++){
			    	
			    	if($formdata["editor"]=="2"){//FCKEditor
			    	
					    $q["question"]     = stripslashes($formdata["fquestion".$i]);			  
				   		$q["answer"]       = stripslashes($formdata["fanswer".$i]);			   
				   		$q["id"]           = $formdata["id".$i];
					    
			    	}else{ //HTML EDITOR
			    		
			    		$q["question"]     = stripslashes($formdata["question".$i]);			  
				   		$q["answer"]       = stripslashes($formdata["answer".$i]);			   
				   		$q["id"]           = $formdata["id".$i];		
			    	}
			    					  	   
				   
				   $question = new Question_Model_Question();
				   $question->updateData($q,$formdata["id".$i]);
			    }
			 
	    	}   
			    
			    
			    
			    
			    
			    
			  
			    /*=============================MCQ==============================*/
	    	if($formdata["question_type"]==2){ //MCQ
	    		
		    		$info["course_id"]       = $formdata["course_id"];
				    $info["topic_id"]        = $formdata["topic_id"];
				    $info["assessment_type"] = $formdata["assessment_type"];
				    $info["question_type"]   = $formdata["question_type"];
				    $info["taxanomy_level"]  = $formdata["taxanomy_level"];
				    $info["difficulty_level"]= $formdata["difficulty_level"];
				    $info["status"]          = $formdata["status"];
				    $info["point"]           = $formdata["point"];
				    $info["modifydt"]       = date("Y-m-d H:i:s");
				    $info["modifyby"]       = $data->username; 	
	    		
			    	$question_main = new Question_Model_QuestionMain();
			        $question_main->updateData($info,$formdata["q_id"]);
			   
			        
					    $total = $formdata["total_language"]; 
					     
				     	for($i=1; $i<=$total; $i++){
				     		
				     		$q["question"]          = stripslashes($formdata["question".$i]);	
				     		
							$question = new Question_Model_Question();
				            $question->updateData($q,$formdata["qid".$i]);   
							
							
							for($j=1; $j<=4; $j++){ 							  
							    $a["answer"]            = stripslashes($formdata["answer".$i.$j]);
							    
							    echo $formdata["correct".$i];	 
							   
							    if($formdata["correct".$i]==$j)
							      $a["correct_answer"] =1;
							    else 
							      $a["correct_answer"] =0;	 
							 
							        
								$answer = new Question_Model_QuestionMcqAnswer();
								$answer->updateData($a,$formdata["aid".$i.$j]); 		
							} 
							print_r($formdata);
							 
							
				     	}
	    		
	    	      }
			    
			   
			   //redirect
			  // $this->_redirect('/question/sme/view');
			   $this->_redirect('question/sme/preview/id/'.$formdata["q_id"]);		
    		
    	}else{
    		
	    		$q_id= $this->_getParam('id', 0);
		    	$this->view->q_id = $q_id;    	
		    	
		    	//get question main
		    	$question_main = new Question_Model_QuestionMain();
		    	$question_main = $question_main->preview($q_id);
		    	$this->view->q = $question_main;
		    	
		    	//get question
		    	$question = new Question_Model_Question();
		    	$question = $question->getQuestion($q_id);
		    	$this->view->question = $question;    
		    	
		    	
		    	if($question_main["question_type"]==1){	
	              
			    	$this->view->question1 = $question[0]["question"];
			    	$this->view->question2 = $question[1]["question"];
			    	$this->view->answer1   = $question[0]["answer"];
			    	$this->view->answer2   = $question[1]["answer"];   
		    	}
		    	
		    	
		    	if($question_main["question_type"]==2){	
	               //ini pakai kalo ada 1 language aje
			    	$this->view->question1 = $question[0]["question"];
			    	$this->view->qid1 = $question[0]["id"];			    	
			    	
			    	$answer = new Question_Model_QuestionMcqAnswer();
	    			$answers = $answer->getAnswers($question[0]["id"]);
	    			$this->view->answers = $answers;
			    	  
		    	}
		    	
		    		
		    		   
		    	
		    	//get course
		    	$course = new SetupQuestion_Model_Course();
		        $course_list = $course->getCourseByCourseid($question_main["course_id"]);
		    	$this->view->course = $course_list;      	
		    	
		    	//get topic
		    	$syllabus = new SetupQuestion_Model_Syllabus();
		        $syllabus = $syllabus->getTopicByCourseid($question_main["course_id"]);
		    	$this->view->syllabus = $syllabus;  		    	    	
		   
		    	//get assessment type
		    	$assessment = new SetupQuestion_Model_Assessmenttype();
		    	$assessment = $assessment->getComponentName($question_main["assessment_type"]);
		    	$this->view->assessment = $assessment;
		    	    	
		    	//get taxanomy level
		    	$taxanomy = new SetupQuestion_Model_Taxanomy();
		    	$taxanomy = $taxanomy->fetchAll();
		    	$this->view->taxanomy = $taxanomy;
		    	
		    	//get taxanomy level
		    	$difficulty = new SetupQuestion_Model_DifficultyLevel();
		    	$difficulty = $difficulty->fetchAll();
		    	$this->view->difficulty = $difficulty;
    	}
    	
    	
    }
    
    
    public function delAction(){
    	
    	$storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        if (! $data) {
            $this->_redirect('index/index');
        }
                
        $id = $this->_getParam('id', 0);
        
        $question_main = new Question_Model_QuestionMain(); 
        $question_main->deleteData($id);
        
        
        $question = new Question_Model_Question();
        $question->deleteByMainID($id);
        
         $this->_redirect('question/sme/view');
    }
    
    
    public function searchstatusAction(){
    	
    	$storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        if (! $data) {
            $this->_redirect('index/index');
        }
                     
        //title
    	$this->view->title="Question";
    	
      	
    	//get faculty
    	$faculty = new SetupQuestion_Model_Faculty();
        $faculty_list = $faculty->fetchAll();
    	$this->view->faculty = $faculty_list;  
   
        	
    	if ($this->getRequest()->isPost()) {  
    		
		    $formdata = $this->getRequest()->getPost(); 
            
        	$faculty_id = $formdata["faculty_id"];
			$course_id  = $formdata["course_id"];  		
			
		    $this->view->faculty_id    = $faculty_id;  
    	    $this->view->course_id     = $course_id;      	
   
    		
			$syllabus = new SetupQuestion_Model_Syllabus();
			$syllabus_list = $syllabus->getTopicByCourseid($course_id);
			$this->view->syllabus = $syllabus_list;	  
			
			$oStatus = new Systemsetup_Model_Status();
    	    $status = $oStatus->fetchAll();
    	    $this->view->status = $status;	  
    	}
    	
    }
    
    
    public function searchtypeAction(){
    	$storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        if (! $data) {
            $this->_redirect('index/index');
        }
                     
        //title
    	$this->view->title="List of Question";
    	
    	
    	$question_main = new Question_Model_QuestionMain();
    	
    	if ($this->getRequest()->isPost()) {
           
                $formdata = $this->getRequest()->getPost(); 
            	
				$course_id  = $formdata["course_id"];
				$atype      = $formdata["atype"];				
				$orderby    = $formdata["orderby"];				
				
	    	    $this->view->course_id = $course_id;
	    	    $this->view->atype     = $atype;	    	   
	    	    $this->view->orderby   = $orderby;	
	    	  
	    	
        	   //get course
		    	$course = new SetupQuestion_Model_Course();
		        $course_list = $course->getCourseByCourseid($course_id);
		    	$this->view->course = $course_list;  
		    
		    	//get assessment type
		    	$assessment = new SetupQuestion_Model_Assessmenttype();
		    	$assessment = $assessment->getComponentName($atype);
		    	$this->view->assessment = $assessment;			
                
                Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
          
                $select = $question_main->view($formdata);
                $paginator = Zend_Paginator::factory($select);
                $paginator->setItemCountPerPage(50);
                $paginator->setCurrentPageNumber($this->_getParam('page', 1));
                $this->view->paginator = $paginator;
           
        } else {
        	
        		$course_id = $this->_getParam('course_id', 0);
    	        $atype = $this->_getParam('atype', 0);
    	        
    	        $this->view->course_id = $course_id;
	    	    $this->view->atype     = $atype;	  	   
	    	   
        	
        	   //get course
		    	$course = new SetupQuestion_Model_Course();
		        $course_list = $course->getCourseByCourseid($course_id);
		    	$this->view->course = $course_list;  
		    
		    	//get assessment type
		    	$assessment = new SetupQuestion_Model_Assessmenttype();
		    	$assessment = $assessment->getComponentName($atype);
		    	$this->view->assessment = $assessment;
		    	
        	    $condition = array ('course_id'=>$course_id,'assessment_type'=>$atype);
    	
		    	Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
		        
		        $select = $question_main->view($condition);
		        $paginator = Zend_Paginator::factory($select);
		        $paginator->setItemCountPerPage(50);
		        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
		        $this->view->paginator = $paginator;
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
    	$course = new SetupQuestion_Model_Course();
        $course_list = $course->getCourseByFaculty($faculty_id);    	
    	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($course_list);
		$this->view->json = $json;

    }
    
     public function ajaxGetTopicAction($id=null){

	 	$storage = new Zend_Auth_Storage_Session ();
		$data = $storage->read ();
		if (! $data) {
			$this->_redirect ( 'index/index' );
		}
			
    	$course_id = $this->_getParam('course_id', 0);
     
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
        //get topic
        $syllabus = new SetupQuestion_Model_Syllabus();
        $topic_list = $syllabus->getTopicByCourseid($course_id);    	
    	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($topic_list);
		$this->view->json = $json;

    }
    
       
   
}





