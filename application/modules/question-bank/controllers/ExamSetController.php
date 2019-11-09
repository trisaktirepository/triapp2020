<?php

class QuestionBank_ExamSetController extends Zend_Controller_Action {
	
	public function testAction(){
		
	}

	public function listSetAction(){
		$this->view->title="List Exam Set";
		
		$courseDb = new App_Model_Record_DbTable_Course();
		$this->view->course = $courseDb->getData();
		
		if ($this->getRequest()->isPost()) {
    		
			$formdata = $this->getRequest()->getPost();
			
    		//paginator
			$ExamDB = new App_Model_Tos_DbTable_ExamSet();
			$setData = $ExamDB->getPaginateData($formdata);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($setData));
			$paginator->setItemCountPerPage(20);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));			
			$this->view->paginator = $paginator;
			
    	}else{
    		
	    	//paginator
			$ExamDB = new App_Model_Tos_DbTable_ExamSet();
			$setData = $ExamDB->getPaginateData();
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($setData));
			$paginator->setItemCountPerPage(20);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));			
			$this->view->paginator = $paginator;
    	}

	    		    	
	}
	
	
	
	
	
	
	
	public function addSetAction(){
		
		$this->view->title="Add Exam Set";
		
		if ($this->getRequest()->isPost()) {
			
			$formdata = $this->getRequest()->getPost();
			
			$auth     = Zend_Auth::getInstance();
			$userid   = $auth->getIdentity()->id;
			
			
			$data["courseid"]=$formdata["courseid"];
			$data["setname"]=$formdata["setname"];
			$data["instruction"]=$formdata["instruction"];
			$data["description"]=$formdata["description"];
			$data["startdate"]=$formdata["startdate"];
			$data["enddate"]=$formdata["enddate"];
			$data["TimeLimit"]=$formdata["TimeLimit"];
			$data["AlertTime"]=$formdata["AlertTime"];
			$data["passMark"]=$formdata["passMark"];
			$data["status"]=$formdata["status"];
			$data["createddt"] = date('Y-m-d H:i:s');
			$data["createdby"] = $userid;
								
			//add exam set
			$ExamDB = new App_Model_Tos_DbTable_ExamSet();
			$sid = $ExamDB->addData($data);
			
			//loop how many pool_id was selected
			$pools=$formdata["pool_id"]; 
			foreach ($pools as $pool_id){
				
				$info["exam_set_id"] = $sid;
				$info["pool_id"] = $pool_id;
				
				//add exam set details
				$ExamDB->addDetails($info);
			}			
			
			$this->_redirect($this->view->url(array('module'=>'question-bank','controller'=>'exam-set', 'action'=>'edit-set','sid'=>$sid),'default',true));	
			
		}else{
			
			//get Course
			$courseDB =  new App_Model_Record_DbTable_Course();
			$course_list = $courseDB->getData();
			$this->view->course = $course_list;		

			//pool info
	    	$poolDB = new App_Model_Question_DbTable_Pool();
	    	$poolData = $poolDB->getData();
	    	$this->view->pool = $poolData;        
			
		}
    	
	}
	
	
	public function editSetAction(){
		
		$this->view->title="Edit Exam Set";
		
		if ($this->getRequest()->isPost()) {
			
			$formdata = $this->getRequest()->getPost();
			
			$auth     = Zend_Auth::getInstance();
			$userid   = $auth->getIdentity()->id;
			
			
			$data["courseid"]=$formdata["courseid"];
			$data["setname"]=$formdata["setname"];
			$data["instruction"]=$formdata["instruction"];
			$data["description"]=$formdata["description"];
			$data["startdate"]=$formdata["startdate"];
			$data["enddate"]=$formdata["enddate"];
			$data["TimeLimit"]=$formdata["TimeLimit"];
			$data["AlertTime"]=$formdata["AlertTime"];
			$data["passMark"]=$formdata["passMark"];
			$data["status"]=$formdata["status"];
			$data["createddt"] = date('Y-m-d H:i:s');
			$data["createdby"] = $userid;
								
			//add exam set
			$ExamDB = new App_Model_Tos_DbTable_ExamSet();
			$ExamDB->updateData($data,$formdata["sid"]);
			
			//before update/add new bank delete old bank
			$ExamDB->deleteDetails($formdata["sid"]);
			
			//loop how many pool_id was selected
			$pools=$formdata["pool_id"]; 
			foreach ($pools as $pool_id){
				
				$info["exam_set_id"] = $formdata["sid"];
				$info["pool_id"] = $pool_id;
				
				//add exam set details
				$ExamDB->addDetails($info);
			}			
			
			$this->_redirect($this->view->url(array('module'=>'question-bank','controller'=>'exam-set', 'action'=>'edit-set','sid'=>$formdata["sid"]),'default',true));	
			
		}else{
			
			$sid= $this->_getParam('sid', 0);
		    $this->view->sid = $sid;
		    
		    $ExamDB = new App_Model_Tos_DbTable_ExamSet();
		    $set = $ExamDB->getData($sid);
		    $this->view->set = $set;	
		    
		    //get submain
		    $subdata=$ExamDB->getSubdata($sid);
		    $this->view->subdata = $subdata;
		    
		   
			//get Course
			$courseDB =  new App_Model_Record_DbTable_Course();
			$course_list = $courseDB->getData();
			$this->view->course = $course_list;		

			//pool info
	    	$poolDB = new App_Model_Question_DbTable_Pool();
	    	$poolData = $poolDB->getData();
	    	$this->view->pool = $poolData;       
			
		}
    	
	}
	
		
	
	public function generateAction(){
		
		$this->view->title="Generate Exam Paper/Questions";
		
		$venueDB = new App_Model_General_DbTable_Venue();
		$venue = $venueDB->getData();
		$this->view->venue = $venue;
		
		if ($this->getRequest()->isPost()) {
			
			$formdata = $this->getRequest()->getPost();
			
			$this->view->idVenue = $formdata["idVenue"];
			$this->view->exam_date = $formdata["exam_date"];
			
			$studentDB = new App_Model_Record_DbTable_Student();
			$data = $studentDB->getRegisteredStudent($formdata);
			$this->view->paginator = $data;			
			
		}
		
	}
	
	public function exgenerateAction(){
		
		if ($this->getRequest()->isPost()) {
			
			$formdata = $this->getRequest()->getPost();
						
			for($i=0; $i<count($formdata["student_id"]); $i++){
				
				$student_id = $formdata["student_id"][$i];
				$regId      = $formdata["regId"][$formdata["student_id"][$i]];
				$course_id  = $formdata["courseid"][$formdata["student_id"][$i]];
				
				$setDB = new App_Model_Tos_DbTable_ExamSet();
				$set = $setDB->getSetByCourse($course_id);
				$this->view->set = $set;
				
				//print_r($set);
				  echo '<h1>STUDENT ID :'.$student_id.'</h1>';
				  echo 'COURSE :'.$course_id;
				
				foreach ($set as $s){
					
					$pool_id = $s["pool_id"];
					$set_id  = $s["id"];
					
					 echo ' <br>===========================<br>BANK :'.$pool_id;
					  echo '<br>===========================<br>';
					
					$tosDB =  new App_Model_Tos_DbTable_Tos();
					$tos = $tosDB->getActiveTos($pool_id);
					$this->view->tos = $tos; // should be only 1 active tos
					
					if($tos){
								//from course dapatkan chapter loop chapter
								$chapterDB = new App_Model_Question_DbTable_Chapter();
					 		    $chapter = $chapterDB->getTopicbyPool($pool_id);
					 			
					 		    // $myarray = array();
					 		    $count_question=0;					 		   
					 		    $count_chapter=0;
					 		    foreach ($chapter as $c) {
					 		    	
					 		    	for($l=1; $l<=3; $l++){
					 		    	    // $count_question = $count_question	+ $l;
					 		    	     
					 		    		//get question from each level
					 		    		$questionDB = new App_Model_Question_DbTable_Question();
					 		    		$condition = array('pool_id'=>$pool_id,'topic_id'=>$c['id'],'difficulty_level'=>$l);
			    					    $arr = $questionDB->getQuestion($condition);
			    					    
			    					    //get number of question required for each topic & level
			    					    $condition2 = array('tos_id'=>$tos['id'],'topic_id'=>$c['id'],'difficulty_level'=>$l);
			    					    $examtos = $tosDB->getDetailTos($condition2);
			    					        					    
			    					  
			    					    echo '<br>Topic:'.$c['name'].'  Level:'. $l;
			    					    echo '<br>Available:'.count($arr);
			    					    echo '<br>Require:'.($examtos['NoOfQuestion']);
			    					    echo '<br>===========================<br>';
			    					    
			    					    //to get random question 
			    					   
			    					    $keys = array_keys($arr);    					    
										shuffle($keys);
										
										     $counter=1;
										 	 foreach ($keys as $key) {
										 	 	
										 	 	  if($counter<=$examtos['NoOfQuestion']){							 	 	  	
										 	 	  	 										 	 	  	
											 	 	  $arr_elem = $arr[$key];
											 	 	  $soalan = trim($arr_elem["english"]);
											 	 	  $qid    = $arr_elem['id'];
											 	 	  
											 	 	  //get correct answer
											 	 	  $answerDb = new App_Model_Question_DbTable_Answer();
											 	 	  $getcorrectAnswer = $answerDb->getCorrectAnswer($qid);
											 	 	  $correctAnswer = $getcorrectAnswer['id'];
											 	 	  
											 	 	  $info["id_question"]=$qid;
//											 	 	  $info["correct_answer"]=$correctAnswer;
											 	 	  $info["question"]=$soalan;
											 	 	  
											 	 	  
											 	 	  
											 	 	  $myarray[$count_question] = $info;
											 	 	 
											 	 	  $count_question = $count_question+1;
											 	 	    
											 	 	  echo '<br>';
											 	 	  echo '('.$count_question.')('.$qid.') ';
											 	 	  echo '<br>';									 	 	  
											 	 	  
										 	 	  }
										 	 	 
										 	  $counter++; }
										 	 
										echo '</pre><br>';
			    					   	
					 		    	}
					 		    $count_chapter++;
					 		    }
					 		    
					 		    //  print_r($myarray);
					 		  
					 		    $auth     = Zend_Auth::getInstance();
								$userid   = $auth->getIdentity()->id;
					 		  
					 		    $dataq["student_regID"] = $regId;					 		  
					 		    $dataq["exam_set_id"] = $set_id;
					 		    $dataq["exam_set_bank_id"] = $pool_id;
					 		    $dataq["createddt"] = date('Y-m-d H:i:s');
							    $dataq["createdby"] = $userid;
							    
							   
							    //insert dalam  q018_student_exam_set
							    $studentQuestDB =  new App_Model_Tos_DbTable_StudentExamQuestion();
							    $student_exam_set_id = $studentQuestDB->addData($dataq);
							      
							    
							    
							    //re-shuffle to randomize the question
					 		    shuffle($myarray);
					 		    
					 		    echo 'TOTAL QUESTION(S):'.count($myarray);
					 		    
					 		    $infoq["student_exam_set_id"] = $student_exam_set_id;					 		    
					 		    foreach($myarray as $myarr){
					 		   		  $infoq["question_id"] = $myarr['id_question'];
//					 		   		  $infoq["correct_answer"] = $myarr['correct_answer'];
					 		   		  $studentQuestDB->addDetails($infoq);
					 		    }
					 		    
					 		    //to inform that exam question has been generated
					 		    $student["exam_set_id"] = $set_id;
					 		    $regDB = new App_Model_Record_DbTable_Registrationdetails();
					 		    $regDB->updateExamSet($student,$regId);
					 		   
					 		    //in case nk view balik
					 		    $cinfo[]=$student_id;
					 		    //$cinfo["regId"]=$regId;
					 		    //$cinfo["courseid"]=$course_id;
					 		    $candidate[$i]=$cinfo;
		 		    
					}else{
						echo '<BR>TOS NOT AVAILABLE';
						
					}//end if TOS
				} 							
				
			}
			
			//$studentDb = new App_Model_Record_DbTable_Student();
		    //$studentData = $studentDb->getQuestStudent2($cinfo);
		    
			//$this->view->student = $studentData;
			$this->view->noticeSuccess = "Exam Question has been generated successfully.";	
	    	
		}
		
		
	}
	
	
	public function studentListAction(){
		$this->view->title="Candidate List";
		
		$courseDb = new App_Model_Record_DbTable_Course();
		$this->view->course = $courseDb->getData();
		
		$venueDB = new App_Model_General_DbTable_Venue();
		$this->view->venue = $venueDB->getData();
		
		
		if ($this->getRequest()->isPost()) {
    		
			$formdata = $this->getRequest()->getPost();
			
    		//paginator
			$studentDb = new App_Model_Record_DbTable_Student();
		    $studentData = $studentDb->getQuestStudent($formdata);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($studentData));
			$paginator->setItemCountPerPage(20);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));			
			$this->view->paginator = $paginator;
			
    	}else{
    		
	    	//paginator
			$studentDb = new App_Model_Record_DbTable_Student();
		    $studentData = $studentDb->getQuestStudent();
		
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($studentData));
			$paginator->setItemCountPerPage(20);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));			
			$this->view->paginator = $paginator;
    	}

	    		    	
	}
	
	
	public function studentQuestAction(){
		
		$this->view->title="Candidate Exam Questions";
		
		$regId= $this->_getParam('regId', 0);
		$this->view->regId = $regId;
		    
		    
		$studentQuestDB =  new App_Model_Tos_DbTable_StudentExamQuestion();
		$set= $studentQuestDB->getSet($regId);
		$this->view->set = $set;
	}
	
	
	
}
