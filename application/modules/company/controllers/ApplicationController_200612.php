<?php

class Company_ApplicationController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->title = "Application List";
    }
    
	public function batchApplicationAction()
    {
        $this->view->title = "Batch Application List";
    }

    /**
     * 
     * Batch registration - by candidate
     */
	public function addBatchCandidateStep1Action(){
		$this->view->title = "Add Batch Registration - by candidate";
		
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			
			$studentDB = new App_Model_Record_DbTable_Student();
			$courseDB = new App_Model_Record_DbTable_Course();
			$feeStructureDB = new Finance_Model_DbTable_Feestructure();
			$scheduleDB = new App_Model_Schedule_DbTable_Schedule();
		 
			$data = array();
			$i=0;
			foreach ($formData['student_id'] as $student){
				$data[] = array(
							'student_info' => $studentDB->getStudent($student),
							'course_info' => $courseDB->getData($formData['course_id'][$i]),
							'course_fee' => $feeStructureDB->getFee($formData['course_id'][$i])
							);
							
				if(isset($formData['schedule_id'][$i])){
					$data[$i]['schedule_info'] = $scheduleDB->getDataComplete($formData['schedule_id'][$i]);
				}			
							
				$i++;			
			}
			
			$this->view->data = $data;
			/*echo "<pre>";
			print_r($data);
			echo "</pre>";*/
		}
	}
	
	public function addBatchCandidateStep2Action(){
		$this->view->title = "Add Batch Registration - by candidate";
		
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			
			
			$studentDB = new App_Model_Record_DbTable_Student();
			$courseDB = new App_Model_Record_DbTable_Course();
			$feeStructureDB = new Finance_Model_DbTable_Feestructure();
			$scheduleDB = new App_Model_Schedule_DbTable_Schedule();
		 
			$data = array();
			$i=0;
			foreach ($formData['student_id'] as $student){
				
				$data[$i] = array(
							'student_info' => $studentDB->getStudent($student),
							'course_info' => $courseDB->getData($formData['course_id'][$i]),
							'course_fee' => $feeStructureDB->getFee($formData['course_id'][$i])
							);
							
				if(isset($formData['schedule_id'][$i])){
					$data[$i]['schedule_info'] = $scheduleDB->getDataComplete($formData['schedule_id'][$i]);
				}			
							
				$i++;			
			}
			
			$this->view->data = $data;
			/*echo "<pre>";
			print_r($data);
			echo "</pre>";*/
			
			 //exam center list
	    	$examCenterDB = new App_Model_General_DbTable_Venue();
	    	$this->view->examcenter = $examCenterDB->getData();

		}else{
			//redirect
			$this->_redirect($this->view->url(array('module'=>'company','controller'=>'application', 'action'=>'add-batch-candidate-step1'),'default',true));
		}
	}
	
	public function addBatchCandidateStep3Action(){
		$this->view->title = "Add Batch Registration - by candidate";
		
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			
			
			$studentDB = new App_Model_Record_DbTable_Student();
			$courseDB = new App_Model_Record_DbTable_Course();
			$feeStructureDB = new Finance_Model_DbTable_Feestructure();
			$scheduleDB = new App_Model_Schedule_DbTable_Schedule();
		 
			$data = array();
			$i=0;
			foreach ($formData['student_id'] as $student){
					
				$data[] = array(
							'student_info' => $studentDB->getStudent($student),
							'course_info' => $courseDB->getData($formData['course_id'][$i]),
							'course_fee' => $feeStructureDB->getFee($formData['course_id'][$i]),
							'schedule_info' =>$scheduleDB->getDataComplete($formData['schedule_id'][$i])
							);
							
							
							
				$i++;			
			}
			
			$this->view->data = $data;
			
			/*echo "<pre>";
			print_r($data);
			echo "</pre>";*/
			
			$modeDB = new Finance_Model_DbTable_Paymentmode();
	        $modeData = $modeDB->getDataIndividual();
	        $this->view->paymentmode = $modeData;
			
		}else{
			//redirect
			$this->_redirect($this->view->url(array('module'=>'company','controller'=>'application', 'action'=>'add-batch-candidate-step1'),'default',true));
		}
		
	}
	
	public function addBatchCandidateStep4Action(){
		$this->view->title = "Add Batch Registration - by candidate";
		
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			
			$studentDB = new App_Model_Record_DbTable_Student();
			$courseDB = new App_Model_Record_DbTable_Course();
			$feeStructureDB = new Finance_Model_DbTable_Feestructure();
			$scheduleDB = new App_Model_Schedule_DbTable_Schedule();
		 
			$totalfee = 0;
			$data = array();
			$i=0;
			foreach ($formData['student_id'] as $student){
				$amount_detail = $feeStructureDB->getFee($formData['course_id'][$i]);	
				
				$data[] = array(
							'student_info' => $studentDB->getStudent($student),
							'course_info' => $courseDB->getData($formData['course_id'][$i]),
							'course_fee' => $amount_detail,
							'schedule_info' =>$scheduleDB->getDataComplete($formData['schedule_id'][$i])
							);

				$totalfee = $totalfee + $amount_detail['amount'];
											
				$i++;			
			}
			
			$modeDB = new Finance_Model_DbTable_Paymentmode();
			$this->view->payment_mode = $modeDB->getData($formData['payment_mode_id']);
			$this->view->payment_mode_id = $formData['payment_mode_id'];
			$this->view->payment_amount = $totalfee;
			
			$this->view->data = $data;
			
			
		}else{
			//redirect
			$this->_redirect($this->view->url(array('module'=>'company','controller'=>'application', 'action'=>'add-batch-candidate-step1'),'default',true));
		}
		
	}
	
	public function addBatchCandidateStep5Action(){
		$this->view->title = "Add Batch Registration - by candidate";
		
		$auth = Zend_Auth::getInstance();
		$company_id = $auth->getIdentity()->id;
		
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			
			$payment_mode_id = $formData['payment_mode_id'];
			$date = date('Y-m-d H:i:s');
			
			$total_student = count($formData['student_id']);
			
			$scheduleDB = new App_Model_Schedule_DbTable_Schedule();
			$registrationdetailsDB = new App_Model_Record_DbTable_Registrationdetails();
			
			for ($a=0; $a<$total_student; $a++){
				
				$schedule_details = $scheduleDB->schedule_details($formData['schedule_id'][$a]);
				
				$add['idApplication'] = $formData['student_id'][$a];
				$add['idCourse'] = $formData['course_id'][$a];
				$add['idSchedule'] = $formData['schedule_id'][$a];
				$add['idVenue'] = $schedule_details["exam_center"];
				$add['idProgram'] = 1;
				$add['paymentMode'] = $payment_mode_id;
				$add['paymentStatus'] = 0;
				$add['dateApplied'] = $date;
				$add['seatStatus'] = 0;
				$add['batchId'] = $company_id;
				
				echo "<pre>";
				print_r($add);
				echo "</pre>";
				
				//Insert into r016_registrationdetails
				$regis_students = $registrationdetailsDB->add($add);
			}
			
		}else{
			//redirect
			$this->_redirect($this->view->url(array('module'=>'company','controller'=>'application', 'action'=>'add-batch-candidate-step1'),'default',true));
		}			
	}
	
	public function ajaxSelectCandidateAction(){
		$name = $this->_getParam('name', null);
		$this->view->name = $name;
		
		$ic = $this->_getParam('ic', null);
		$this->view->ic = $ic;
    	    	
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	$auth = Zend_Auth::getInstance();
		$company_id = $auth->getIdentity()->id;
		
	  	$studentDB = new App_Model_Record_DbTable_Student();
	  	$condition = null;
	  	if($name!=null){
	  		$condition = "ARD_NAME like '%".mysql_real_escape_string($name)."%'";	
	  	}
	  	
	  	if($ic!=null){
	  		if($condition!=null){
	  			$condition .= " AND ARD_IC like '%".mysql_real_escape_string($ic)."%'";
	  		}else{
	  			$condition = "ARD_IC like '%".mysql_real_escape_string($ic)."%'";
	  		}
	  	}
	  	
	  	$studentList = $studentDB->getCompanyStudentData($company_id, $condition);
	  	
	  	$this->view->studentList = $studentList;
	  	
	  	$courseDB = new App_Model_Record_DbTable_Course();
	  	$this->view->courses = $courseDB->getCourse();
	}
	
	public function ajaxGetStudentDataAction(){
		$id = $this->_getParam('id', 0);
    	    	
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	
	  	
	  	 $studentDB = new App_Model_Record_DbTable_Student();
		 $student_data = $studentDB->getStudent($id);
		
		
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($student_data);
		
		$this->view->json = $json;
	}
	
	public function ajaxGetCourseDataAction(){
		$id = $this->_getParam('id', 0);
    	    	
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	
	  	
	  	 $courseDB = new App_Model_Record_DbTable_Course();
		 $course_data = $courseDB->getData($id);
		
		
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($course_data);
		
		$this->view->json = $json;
	}
	
	public function ajaxGetCourseFeeAction(){
		$pid = $this->_getParam('pid', 0);
		$cid = $this->_getParam('cid', 0);
    	    	
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	
	  	
	  	 $feeStructureDB = new Finance_Model_DbTable_Feestructure();
		 $data = $feeStructureDB->getFee($cid);
		
		
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($data);
		
		$this->view->json = $json;
	}
	
	public function ajaxSelectScheduleAction(){
		$course_id = $this->_getParam('course_id', 0);
		$this->view->course_id = $course_id;
		
		$examcenter_id = $this->_getParam('exam_center_id', 0);
		$this->view->exam_center_id = $examcenter_id;
    	    	
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
	}
	
	public function ajaxGetScheduleDataAction(){
		$course_id = $this->_getParam('course_id', 0);
		$course_id_arr = $this->_getParam('course_id[]', 0);
		$id = $this->_getParam('exam_center_id', 0);
		
    	$start = $this->_getParam('start', 0);
    	$end = $this->_getParam('end', 0);
    	
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            
            $ajaxContext = $this->_helper->getHelper('AjaxContext');
	        $ajaxContext->addActionContext('view', 'html');
	        $ajaxContext->initContext();
	        
	        $dtStart = date('Y-m-d',$start);
	        $dtEnd = date('Y-m-d',$end);
	        
	        $scheduleDB = new App_Model_Schedule_DbTable_Schedule();
			$data = $scheduleDB->getSchedule($dtStart, $dtEnd, $id,$course_id);
			
			//add course and venue detail
			$i=0;
			foreach ($data as $sch){
				//course
				$scheduleCourseDB = new App_Model_Schedule_DbTable_ScheduleCourse();
				$courses = $scheduleCourseDB->getScheduleData($sch['id']);
				$data[$i]['course'] = $courses;
				
				//venue detail
				$scheduleVenueDB = new App_Model_Schedule_DbTable_ScheduleVenue();
				$venue = $scheduleVenueDB->getScheduleData2($sch['id']);
				$data[$i]['venue'] = $venue;
				
				$i++;
			}
			
			/*echo "<pre>";
			print_r($data);
			echo "</pre>";
			exit;*/
			
			$schedule=array();
			$i=0;
			foreach ($data as $event){
				
				$courselist = "";
				foreach ($event['course'] as $course){
					if($courselist==""){
						$courselist = "-".$course['course_name'];
					}else{
						$courselist .= "\n -".$course['course_name'];
					}
				}
				
				$schedule[$i] = array(
							'id'=> $event['id'],
							//'title'=>$event['course_name'] ."\n ". $event['exam_center_name']."\n".date('g:ia', strtotime($event['exam_time_start']))." - ".date('g:ia', strtotime($event['exam_time_end'])),
							'title'=>" ".$event['exam_center_name']."\n ".$courselist ,
							'allDay'=>false,
							'start'=>$event['exam_date']." ".$event['exam_time_start'],
							'end'=>$event['exam_date']." ".$event['exam_time_end'],
							'venue'=>$event['exam_center_name'],
							'examdate'=>date('d/m/Y', strtotime($event['exam_date'])),
							'examtimestart'=>date('g:ia', strtotime($event['exam_time_start'])),
							'examtimeend'=>date('g:ia', strtotime($event['exam_time_end'])),
							'examcenter'=>$event['exam_center']
							);
				$i++;							
			}
			
		
			$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

			$json = Zend_Json::encode($schedule);
		
			$this->view->json = $json;
        
        }else{
        	die();
        }
	}
	
	
	/**
	 * 
	 * Batch registration - by course
	 */
	public function addBatchCourseStep1Action(){
		$this->view->title = "Add Batch Registration - by course";
		
	}
	
	public function ajaxSeatAvailabilityAction(){
		$course_id = $this->_getParam('course_id', 0);
		$scheduleid = $this->_getParam('scheduleid', 0);
		$examcenter = $this->_getParam('examcenter', 0);
		
		if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
		
		$venueDetailDB = new App_Model_General_DbTable_VenueDetail();
		$capacity =  $venueDetailDB->getCapacity($examcenter);
		$capacitySeat = $capacity['SUM(capacity)'];
		
		$registrationDB = new App_Model_Record_DbTable_Registrationdetails();
		$seat = $registrationDB->countSeatRegister($scheduleid,$course_id,$examcenter);
		$countSeat = count($seat);
		
		$available = $capacitySeat - $countSeat;
		
		if ($available>0){
			$seat = $available;
		}else{
			$seat = 0;
		}
		
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($seat);
	
		$this->view->json = $json;
	}
}

