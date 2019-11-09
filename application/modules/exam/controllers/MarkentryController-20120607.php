<?php 
require_once 'Zend/Controller/Action.php';

class Exam_MarkentryController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }
    
    
	public function indexAction()
    {
       	$this->view->title="Mark Entry : List of Candidate";
    	
       
    	$program_id = $this->_getParam('program_id', 0);
    	$course_id  = $this->_getParam('course_id', 0); 
    	$status  = $this->_getParam('status', 0);
    	
    	if($status==1){ $this->view->noticeMessage = "Student mark has been saved."; } 
    	
    	//get Program
    	$program = new App_Model_Record_DbTable_Program();
        $program_list = $program->getData();
    	$this->view->program = $program_list;  

        $oCourse = new App_Model_Record_DbTable_Course();
        $courses = $oCourse->getData();
        $this->view->courses=$courses;
        
        $venueDB = new App_Model_General_DbTable_Venue();
		$this->view->venue = $venueDB->getData();
    	         
         
        if ($this->_request->isPost()) { 
        	
        	$program_id = $this->getRequest()->getPost('program_id');
        	$course_id  = $this->getRequest()->getPost('course_id');
        	$idVenue    = $this->getRequest()->getPost('idVenue');
        	$keyword    = $this->getRequest()->getPost('keyword');
        	$exam_date  = $this->getRequest()->getPost('exam_date');
        	
        	$this->view->program_id = $program_id;
        	$this->view->course_id = $course_id;
        	$this->view->idVenue = $idVenue;
        	$this->view->exam_date = $exam_date;
        	
        	 $oCourse = new App_Model_Record_DbTable_Course();
             $course = $oCourse->getData($course_id);
             $this->view->type = $course["mark_distribution_type"];
        	 
    	     //get assessment component
	         $asscomponent = new App_Model_Exam_DbTable_Asscomponent();
	         $asscomponent_list = $asscomponent->getComponentList($program_id,$course_id);   
	         $this->view->component = $asscomponent_list;
	         
	        
	         $oStudent = new App_Model_Record_DbTable_Student();			
			 $student  = $oStudent->getStudentAttendExam($program_id,$course_id,$idVenue,$keyword,$exam_date);
			 $this->view->student_list = $student;   
			
			 
		}     
             	
      }
      
      
      public function saveMarkAction(){
      	
      	  if ($this->getRequest()->isPost()) {
      	    
      	    	$formdata = $this->getRequest()->getPost();   
	      	 	
      	    	$auth = Zend_Auth::getInstance(); 
      	    	
      	    	 $type = $formdata['mark_distribution_type'];
      	    	
      	    	for($i=0; $i<count($formdata['rd_id']); $i++){
      	    		
      	    		echo 'STUDENT REG ID:'.$formdata['rd_id'][$i];
      	    		echo '<br>';
      	    		
      	    		$rd_id = $formdata['rd_id'][$i];
      	    		$item_id = $formdata['item_id'][$i];
      	    		$component_id =  $formdata['component_id'][$i];
      	    		
      	    		echo 'COMPONENT COUNT:'.COUNT($component_id);
      	    		echo '<br>';
      	    		echo 'ITEM COUNT:'.COUNT($item_id);
      	    		echo '<br>==================<br>';
      	    		
      	    		$data["rd_id"]       = $rd_id;      	    		
      	    		$data["createddt"]   = date("Y-m-d H:i:s");
	      	 	    $data["createdby"]   = $auth->getIdentity()->id;  
	      	 	    
	      	 	    	
		      	 	  $overal_final_mark=0;
		      	 	  $final_mark=0;
		      	 	  $all_total_mark=0;
		      	 	  $candidate_total_mark=0;
	      	 	    
	      	 	    //loop
      	    		for($z=0; $z<count($component_id); $z++){
      	    			
      	    			$data["component_id"] = $formdata['component_id'][$i][$z];      
      	    			
	      	    		//loop
	      	    		for($x=0; $x<count($item_id); $x++){
	      	    			
	      	    			$data["component_item_id"]=$formdata['item_id'][$i][$x];
	      	    			$data["component_student_mark"]=$formdata['mark'][$i][$x];
	      	    			
	      	    			$markDB = new App_Model_Exam_DbTable_Markentry();	
	      	    			$markDB->addMarkentry($data);
	      	    			
	      	    			//print_r($data);
	      	    			
	      	    		/* ==== Calculate final mark ====
			      	 	  --------------
			      	 	  By Weightage
			      	 	  --------------
		                  Final Mark Part A =  (candidate_mark/total_mark)*weightage
		                  Final Mark Part B =  (candidate_mark/total_mark)*weightage
		                  $mark = Final Mark Part A + Final Mark Part B
		                  
		                  ------------
		                  Overall mark
		                  ------------
		                  Final Mark  = (candidate_mark Part A + candidate_mark Part B)/ (total_mark Part A + total_mark Part B)
		                  mark = Final Mark * 100
		                  ===========================================================
		                */		                
	      	    		 
			      	 	
			              //nak dapatkan total_mark dan weightage for component Part A
			              $componentDB = new App_Model_Exam_DbTable_Asscompitem();
			              $itemInfo = $componentDB->getData($data["component_id"],$data["component_item_id"]);
				      	 	
			              $candidate_mark = $data["component_student_mark"];
			              $weightage      = $itemInfo["component_item_weightage"];
			              $total_mark     = $itemInfo["component_item_total_mark"];
			              
			             
			             
			              if($type==1){
			      	 	  	$final_mark = (($candidate_mark/$total_mark)*$weightage);
			      	 	  	$overal_final_mark = ceil($overal_final_mark) + ceil($final_mark);
			              }else{
			              	$candidate_total_mark = ceil($candidate_total_mark) + ($candidate_mark);
			              	$all_total_mark = ceil($all_total_mark) + ceil($total_mark);
			              	$overal_final_mark = (ceil($candidate_total_mark) / ceil($all_total_mark))*100;
			              }
			              
			               echo "<BR>=========== MARK ==============<BR>";
			               echo "TYPE:".$type;
			               echo "<br>candidate_mark:".$candidate_mark;
			               echo "<br>weightage:".$weightage;
			               echo "<br>total_mark:".$total_mark;
			               echo "<br>FINAL MARK:".$final_mark;
			               echo "<br>OVERAL FINAL MARK:".$overal_final_mark;
			               echo "<BR>=========================<BR>";
	      	    		}
	      	    		
	      	    		
	      	    		
      	    		}//loop component
      	    		
      	    		
      	    		 //get final grade cek dalam table grade
		      		$gradeDB = new App_Model_Exam_DbTable_Grade();
		      	    $course_grade = $gradeDB->getGradeInfo($overal_final_mark);
		            
			      	$info["course_mark"]  = $overal_final_mark;	      	 	
		      	 	$info["course_grade"] = $course_grade["status"];
		      	 	$oRegDetail=new App_Model_Record_DbTable_Registrationdetails();	      	 	
		      	 	$oRegDetail->updateStudent($info,$data["rd_id"]); 
      	    		
      	    		
      	    	}
      	    	
      	    	 
      	    	 $this->_redirect($this->view->url(array('module'=>'exam','controller'=>'markentry', 'action'=>'index','status'=>1),'default',true));	
      	  }
      	
      }
      
      
      public function manageAction(){
      	
	      	$this->view->title="Mark Entry : Add Student Mark";
	    	
	       	$program_id  = $this->_getParam('program_id', 0);
        	$course_id   = $this->_getParam('course_id', 0);
        	$rd_id   = $this->_getParam('id', 0);	
        	
        	$this->view->program_id = $program_id;
        	$this->view->course_id = $course_id;    
        	$this->view->rd_id = $rd_id;    

        	$oCourse = new App_Model_Record_DbTable_Course();
            $courses = $oCourse->getData($course_id);
            $this->view->course=$courses;
            
            $oRegDetail = new App_Model_Record_DbTable_Registrationdetails();
            $sdetail = $oRegDetail->getStudent($rd_id);
            $student_id = $sdetail["idApplication"];
        	  
        	$oStudent = new App_Model_Record_DbTable_Student();        
	        $student = $oStudent->getStudent($student_id);
	        $this->view->student = $student;
	        
           //get assessment component
           $asscomponent = new App_Model_Exam_DbTable_Asscomponent();
           $asscomponent_list = $asscomponent->getComponentList($program_id,$course_id);   
           $this->view->asscomponent_list = $asscomponent_list;
	         
           
       
      	    if ($this->getRequest()->isPost()) {
      	    
      	    	$formdata = $this->getRequest()->getPost();   
	      	 	
      	    	$auth = Zend_Auth::getInstance(); 
	      	 	
	      	 	$component_id        = $formdata['component_id'];
	      	 	$component_item_id   = $formdata['component_item_id'];
	      	 	$mark                = $formdata['mark'];
	      	 	$type                = $formdata['type'];
	      	 	
	      	 	$data["rd_id"]       = $formdata['rd_id'];
	      	 	$data["createddt"]   = date("Y-m-d H:i:s");
	      	 	$data["createdby"]   = $auth->getIdentity()->id;   	
	      	 	
	      	 	$omark= new App_Model_Exam_DbTable_Markentry();	
	      	 	$course_mark = 0;
	      	 	
	      	 	for($c=0; $c<count($component_id); $c++){
	      	 		
	      	 	  $overal_final_mark=0;
	      	 	  $final_mark=0;
	      	 	  $all_total_mark=0;
	      	 	  $candidate_total_mark=0;
	      	 	  
	      	 	  $data["component_id"]      = $component_id[$c];
	      	      
	      	 	  for($k=0; $k<count($mark); $k++){
	      	 	  					     				
	      				$data["component_item_id"]      = $component_item_id[$k];
	      				$data["component_student_mark"] = $mark[$k];	
	      				
			      		if(isset($formdata['scm_id']))  { 	
			      			
			      			if($formdata['scm_id'][$k]!=""){	      			
				      			$scm_id    = $formdata['scm_id'][$k]; 	 	
								$omark->updateMarkEntry($data,$scm_id);
			      			}else{
			      				$omark->addMarkentry($data);
			      			}
			      	 	}else{
			      	 		$omark->addMarkentry($data);
			      	 	}
			      	 	
			      	 	
			      	 	
			      	 	 /* ==== Calculate final mark ====
			      	 	  --------------
			      	 	  By Weightage
			      	 	  --------------
		                  Final Mark Part A =  (candidate_mark/total_mark)*weightage
		                  Final Mark Part B =  (candidate_mark/total_mark)*weightage
		                  $mark = Final Mark Part A + Final Mark Part B
		                  
		                  ------------
		                  Overall mark
		                  ------------
		                  Final Mark  = (candidate_mark Part A + candidate_mark Part B)/ (total_mark Part A + total_mark Part B)
		                  mark = Final Mark * 100
		                  ===========================================================
		                */		                
			      	 	
			              //nak dapatkan total_mark dan weightage for component Part A
			              $componentDB = new App_Model_Exam_DbTable_Asscompitem();
			              $itemInfo = $componentDB->getData($data["component_id"],$data["component_item_id"]);
				      	 	
			              $candidate_mark = $data["component_student_mark"];
			              $weightage      = $itemInfo["component_item_weightage"];
			              $total_mark     = $itemInfo["component_item_total_mark"];
			              
			              if($type==1){
			      	 	  	$final_mark = (($candidate_mark/$total_mark)*$weightage);
			      	 	  	$overal_final_mark = ceil($overal_final_mark) + ceil($final_mark);
			              }else{
			              	$candidate_total_mark = ceil($candidate_total_mark) + ($candidate_mark);
			              	$all_total_mark = ceil($all_total_mark) + ceil($total_mark);
			              	$overal_final_mark = (ceil($candidate_total_mark) / ceil($all_total_mark))*100;
			              }
			      	 	
			      	 	  
	      	 	  }//end for	
	      	 	  
	      	 	    //get final grade cek dalam table grade
		      		$gradeDB = new App_Model_Exam_DbTable_Grade();
		      	    $course_grade = $gradeDB->getGradeInfo($overal_final_mark);
		            //$course_grade["symbol"].'-'.$course_grade["status"];
		      	
			      	$info["course_mark"]  = $overal_final_mark;	      	 	
		      	 	$info["course_grade"] = $course_grade["status"];
		      	 	$oRegDetail=new App_Model_Record_DbTable_Registrationdetails();	      	 	
		      	 	$oRegDetail->updateStudent($info,$data["rd_id"]); 
			      	
	      	 	  
	      	 	}//end component for
	      	 	     
             	      	 	
	            $this->view->noticeMessage = "This student mark has been saved.";
		 	    }
	       		  
      }
      
           
          
       public function requestAction(){
       	
    	$this->_helper->layout->setLayout('result');
    	$auth = Zend_Auth::getInstance(); 
    	
    	//get fullname session id
    	//$auth->getIdentity()->id
    	
    	$program_id  = $this->_getParam('program_id', 0);
    	$semester_id = $this->_getParam('semester_id', 0);  
    	$course_id   = $this->_getParam('course_id', 0);
    	$matrix_no   = $this->_getParam('matrix_no', 0);
    	$fullname   = $this->_getParam('fullname', 0);  
    	
    		//get program name
    		$oProgram = new App_Model_Record_DbTable_Program();
    		$program_info = $oProgram->getData($program_id);
    		$this->view->program_name = $program_info["main_name"];
    	
    		//get semester name    		
    	    $oSemester = new App_Model_Record_DbTable_Semester();
    	    $semester_info = $oSemester->getData($semester_id);    	   
    	    $this->view->semester_name = $semester_info["name"].' '.$semester_info["year"];
    	    
    	    $oCourse =  new App_Model_Record_DbTable_Course();
    	    $course_info = $oCourse->getData($course_id);
    	    $this->view->course_name = $course_info["code"].'-'.$course_info["name"];
    	
    	//get assessment component
         $asscomponent = new App_Model_Exam_DbTable_Asscomponent();
         $asscomponent_list = $asscomponent->getComponentList($program_id,$course_id);   
         $this->view->asscomponent_list = $asscomponent_list;
       
         
         $oStudent = new App_Model_Record_DbTable_Student();
		 $student  = $oStudent->getStudentCourseReg($course_id,$matrix_no,$fullname);
		 $this->view->student_list = $student;
    
    	$oUser = new SystemSetup_Model_DbTable_User();
    	$user = $oUser->getData($auth->getIdentity()->id);
		$this->view->username=$user["fullname"];
    	
    	$request_number= 'SMV'.rand(111111,999999).$program_id.$semester_id.$course_id;//programcode_semesterID
    	$this->view->request_number =$request_number;
    	
    	
    	}
    	
    	public function approveAction(){
    		$this->_helper->layout->setLayout('result');   	
    	}
       
       
       public function confirmAction(){
       	
       	    $auth = Zend_Auth::getInstance(); 
    	
       		$request_no  = $this->getRequest()->getPost('request_no');
       		
       	 		 //To update student_course_registration with total mark earn for that semester
       	 		
       		 	 if ($this->getRequest()->isPost()) { 
       		 	 	
       		 	 	    //insert into mark_verification table
       		 	 	    $oMark = new App_Model_Exam_DbTable_MarkVerification();
       		 	 	   	$verification_id = $oMark->verifyMark($request_no);       		 	 	    
       		 	 	
       		 	 	 	$scr_id            = $this->getRequest()->getPost('scr_id');
       		 	 	 	$final_course_mark = $this->getRequest()->getPost('final_course_mark');  
       		 	 	 	$grade_course_mark = $this->getRequest()->getPost('grade_course_mark');       		 	 	 	
       		 	 	 	
	      				$oSCREg= new App_Model_Exam_DbTable_StudentCourseRegistration();
	      				
						for($i=0; $i<count($scr_id); $i++){	  
							//update
							$id = $scr_id[$i];
							$data["final_course_mark"]   = $final_course_mark[$i];
							$data["grade_course_mark"]   = $grade_course_mark[$i];
							$data["mark_verification_id"]= $verification_id;
														
							$oSCREg->updateMark($data,$id);
						}							
       		 	 }       
       		 	
       		//redirect
			echo "<script>alert('Student mark has been successfully approved!')</script>";
			$this->_redirect('/exam/markentry/');	
       }
       
       
       
       
       
       public function ajaxGetCourseAction($id=null){

	 	$storage = new Zend_Auth_Storage_Session ();
		$data = $storage->read ();
		if (! $data) {
			$this->_redirect ( 'index/index' );
		}
			
    	$faculty_id = $this->_getParam('program_id', 0);
     
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
        //get course
    	$course = new GeneralSetup_Model_Course();
        $course_list = $course->getCourseByFaculty($faculty_id);    	
    	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($course_list);
		$this->view->json = $json;

    }
   
}
