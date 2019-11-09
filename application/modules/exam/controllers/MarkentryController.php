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
    	
    	if($status==1){ $this->view->noticeMessage = "Mark has been saved successfully. Examination Result has been emailed to the Candidates."; } 
    	
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
      	    	     	    	
      	    
      	    	
      	    	for($i=0; $i<count($formdata['rd_id']); $i++){
      	    		
      	    		$rd_id        = $formdata['rd_id'][$i];
      	    		$item_id      = $formdata['item_id'][$i];
      	    		$component_id = $formdata['component_id'][$i];
      	    		$course_mark  = $formdata['final_mark'][$i];
      	    		$course_grade = $formdata['grade'][$i];
      	    		$grade_symbol = $formdata['symbol'][$i];
      	    		   	    		    	    	
      	    			
      	    		/* UPDATE MARK INFO AT TABLE REGISTRATION DETAILS
      	    		   ----------------------------------------------- */	
      	    		            
			      	$info["course_mark"]  = $course_mark;	      	 	
		      	 	$info["course_grade"] = $course_grade;
		      	 	$info["grade_symbol"] = $grade_symbol;
		      	 	
		      	 	$oRegDetail=new App_Model_Record_DbTable_Registrationdetails();	      	 	
		      	 	$oRegDetail->updateStudent($info,$rd_id); 
		      	 			      	 	
		      	 	   	    		
      	    	   	/* ADD MARK INFO FOR EACH COMPONENT  
      	    	   	 -------------------------------------*/
      	    	   	
  	    	   	    $data["rd_id"]       = $rd_id;  	
      	    		$data["createddt"]   = date("Y-m-d H:i:s");
	      	 	    $data["createdby"]   = $auth->getIdentity()->id; 
	      	 	    
      	    	   	
      	    	   	for($z=0; $z<count($component_id); $z++){
      	    	   		
      	    	   		$data["component_id"] = $formdata['component_id'][$i][$z];    
      	    	   		
      	    	   		//loop
	      	    		for($x=0; $x<count($item_id); $x++){
		      	 	    
	      	    			$data["component_item_id"]=$formdata['item_id'][$i][$x];
	      	    			$data["component_student_mark"]=$formdata['mark'][$i][$x];
	      	    			$scm_id = $formdata['scm_id'][$i][$x];
	      	    			
	      	    			$markDB = new App_Model_Exam_DbTable_Markentry();
	      	    			
	      	    			if($scm_id){
	      	    				//update
				      	    	$markDB->updateMarkentry($data,$scm_id);
	      	    			}else{
				      	 	   	//add
				      	    	$markDB->addMarkentry($data);
	      	    			}
			      	    	
	      	    		}//END FOR X
	      	    	
      	    	   	}//END FOR Z
      	    	   	
      	    	}//END FOR I
      	    	
      	    	
      	    	
      	    	/*===================================================
      	    	  SEND EMAIL NOTOFICATION TO SELECTED CANDIDATES
      	    	  =================================================== */
      	     	//su comment on 30-07-2012 dulu untuk kak wani buat verification
      	    	  $this->email($formdata); //TEMPORARY COMMENT 
      	    	
      	    	/*===================================================
      	    	  END SEND EMAIL NOTOFICATION TO SELECTED CANDIDATES
      	    	  =================================================== */
      	    	     	    	
      	    	
      	    	
      	    $this->_redirect($this->view->url(array('module'=>'exam','controller'=>'markentry', 'action'=>'index','status'=>1),'default',true));	
      	    	
      	  }//END IF POST
      }
      
      
      public function email($formdata){
      	
      	for($s=0; $s<count($formdata["student_id"]); $s++){
				
					$student_id 		= $formdata["student_id"][$s];
					$grade      		= $formdata["student_grade"][$formdata["student_id"][$s]];
					$student_rdid       = $formdata["student_rdid"][$formdata["student_id"][$s]];
					$exam_date          = $formdata["exam_date"][$formdata["student_id"][$s]];
					$coursename         = $formdata["coursename"][$formdata["student_id"][$s]];
					$exam_status        = $formdata["exam_status"][$formdata["student_id"][$s]];
					
					
					//update verify info
					$auth = Zend_Auth::getInstance(); 
					
					$detail["mark_verified"] = 1;
					$detail["mark_verifydt"] = date("Y-m-d H:i:s");
					$detail["mark_verifyby"] = $auth->getIdentity()->id; 
					
					$oRegDetail=new App_Model_Record_DbTable_Registrationdetails();	      	 	
		      	 	$oRegDetail->updateStudent($detail,$student_rdid); //TEMPORARY COMMENT UTK TEST MAIL JALAN TAK
					
		      	 	
					//get student info
					$studentDB = new App_Model_Record_DbTable_Student();
					$student = $studentDB->getStudent($student_id);
					
					
					$fullname = $student["ARD_NAME"];
					$nric     = $student["ARD_IC"];
					$address  = $student["ARD_ADDRESS1"].'<br>'.
								$student["ARD_ADDRESS2"].'<br>'.
								$student["ARD_TOWN"].'<br>'.
								$student["ARD_POSTCODE"].' '.$student["ARD_CITY"].'<br>'.
								$student["ARD_STATE"];
					$recipient= $student["ARD_EMAIL"];
					$subject      = "TAKAFUL BASIC EXAMINATION RESULT";
					$templateMail = "<p>THIS IS SHOULD BE TEMPLATE</p>";
					
					
			    //10:PASS 11:FAIL
			    if($exam_status=='PASS') $templateID = 10;
			    if($exam_status=='FAIL') $templateID = 11;
			    
				$emailtemplateDb = new App_Model_General_DbTable_EmailTemplate();
    		    $templateData = $emailtemplateDb->getData($templateID);
		    	$this->view->emailTemplate = $templateData;   	    
		        
				$templateMail = $templateData['TemplateBody'];	
				
				$templateMail = str_replace("[NAME]",$fullname,$templateMail);
				$templateMail = str_replace("[NRIC]",$nric,$templateMail);
				$templateMail = str_replace("[EXAMDATE]",date('j F Y',strtotime($exam_date)),$templateMail);
				$templateMail = str_replace("[COURSENAME]",$coursename,$templateMail);						
				$templateMail = str_replace("[GRADE]",$grade,$templateMail);		
								
				
				$sent = $this->sendMail($recipient,$fullname,$subject,$templateMail); 
					
      	    	}//end for student id
      }
      
      
      public function sendMail($recipient,$fullname,$subject,$templateMail){
      	
      	    //require_once 'Zend/Mail.php';
			require_once 'Zend/Mail/Transport/Smtp.php';
			
			$config = array( 'auth' => 'login',
                             'username' => 'ibfiminfo@gmail.com',
                             'password' => 'abcd123#',
                             'ssl' => 'ssl',
                             'port' => 465);		
					
          	
			$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com',$config);
            Zend_Mail::setDefaultTransport($transport);  								
                        		
            $mail = new Zend_Mail();
			$mail->setFrom('IBFIM Administrator');
			$mail->addTo($recipient,$fullname);	//onkan bila nak pakai
			//$mail->addTo('suliana@meteor.com.my',$fullname); //disabledkan utk testing only
			$mail->setSubject('TAKAFUL BASIC EXAMINATION RESULT');
			$mail->setBodyHtml($templateMail);		
			$mail->send(); 
			
			//Send
			 $sent = true;
			 try {
			  $mail->send();
			 }
			 catch (Exception $e) {
			  $sent = false;
			 }
			
			 return $sent;
		
      }
      
      
      
      public function printSearchAction(){
      	
      	$this->view->title="Print : List of Candidate";
    	
       
    	$program_id = $this->_getParam('program_id', 0);
    	$course_id  = $this->_getParam('course_id', 0); 
    	$status  = $this->_getParam('status', 0);
    	
    	//get Program
    	$program = new App_Model_Record_DbTable_Program();
        $program_list = $program->getData();
    	$this->view->program = $program_list;      
        
        $venueDB = new App_Model_General_DbTable_Venue();
		$this->view->venue = $venueDB->getData();    	         
             	
      }
      
   	  public function printPreviewAction(){
    	
   	  	
		    $this->view->title = "Print Preview";    	
    		$this->_helper->layout->setLayout('result'); 
		
    	  if ($this->getRequest()->isPost()) {
      	    
      	    	
        	$program_id = $this->getRequest()->getPost('program_id');        	
        	$idVenue    = $this->getRequest()->getPost('idVenue');
        	$keyword    = $this->getRequest()->getPost('keyword');
        	$exam_date  = $this->getRequest()->getPost('exam_date');
        	
        	$this->view->program_id = $program_id;
        	$this->view->idVenue    = $idVenue;
        	$this->view->exam_date  = $exam_date;
        	$this->view->keyword    = $keyword;
        	
        	 $oCourse = new App_Model_Record_DbTable_Course();
             $course = $oCourse->getData();
             $this->view->courses = $course;
             
             $venueDB = new App_Model_General_DbTable_Venue();
		     $this->view->venue = $venueDB->getData($idVenue);    	    
                 	 
    	    $auth = Zend_Auth::getInstance(); 
	        $this->view->printBy = $auth->getIdentity()->fullname; 
	       
    	  }
      }
      
      
      public function saveMarkOldAction(){
      	
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
			               echo "<br>OVERALL FINAL MARK:".$overal_final_mark;
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
    
    
    public function ajaxGetGradeAction($id=null){

	 	$storage = new Zend_Auth_Storage_Session ();
		$data = $storage->read ();
		if (! $data) {
			$this->_redirect ( 'index/index' );
		}
			
    	$final_mark = $this->_getParam('final_mark', 0);
     
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
        //get final grade cek dalam table grade
  		$gradeDB = new App_Model_Exam_DbTable_Grade();
  	    $course_grade = $gradeDB->getGradeInfo($final_mark);            	 	
  	 	$info["grade"] = $course_grade["status"]; 	
  	 	$info["symbol"] = $course_grade["symbol"]; 	
    	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($info);
		$this->view->json = $json;

    }
   
}
