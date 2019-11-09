<?php

class Schedule_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	$exam_center = $this->_getParam('exam_center', 0);
    	$this->view->exam_center = $exam_center;
    	
        // title
        $this->view->title = "Exam Schedule";
        
        //exam center list
    	$examCenterDB = new App_Model_General_DbTable_Venue();
    	$this->view->examcenter = $examCenterDB->getData();
    	
    	$scheduleDB = new App_Model_Schedule_DbTable_Schedule();
        $schedule = $scheduleDB->getScheduleAll($exam_center);
        $this->view->schedule = $schedule;
    }
    
	public function viewAction()
    {
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
    	}
    	
    	$id = $this->_getParam('id', 0);
    	
        // title
        $this->view->title = "Exam Schedule - Detail";
        
        $scheduleDB = new App_Model_Schedule_DbTable_Schedule();
        $schedule = $scheduleDB->getData($id);
        $this->view->schedule = $schedule;
        
        $scheduleCourseDB = new App_Model_Schedule_DbTable_ScheduleCourse();
        $course = $scheduleCourseDB->getScheduleData($id);
        $this->view->course = $course;
        
        $scheduleVenueDB = new App_Model_Schedule_DbTable_ScheduleVenue();
        $venue = $scheduleVenueDB->getScheduleData($id);
        $this->view->venue = $venue;
    }

    public function addAction(){
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
        $exam_center_id = $this->_getParam('exam_center_id', 0);
        $this->view->exam_center_id = $exam_center_id;
        
    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			$data = array(
						'exam_date'		=>date('Y-m-d', strtotime($formData['exam_date']) ),
						'exam_time_start'=>$formData['start'],
						'exam_time_end'	=>$formData['end'],
						'exam_center'	=>$formData['exam_center'],
						'program_id'	=>$formData['program_id'],
						'course_id'		=>$formData['course_id'],
						'venue_id'		=>$formData['venue_id']
					);
			
			if(isset($formData['repeat'])){
					
				$data_repeat = array();
				
				$data_repeat['start'] = date('Y-m-d', strtotime($formData['exam_date']) );
				$data_repeat['end'] = date('Y-m-d', strtotime($formData['end_date']));
				$data_repeat['repeat_end'] = $formData['end_until']; 
				
				
				///** Repeat Data **///
				$arr_day = null;
				if( $formData['repeat_type']==1 ){//*** daily ***//
					
					$data_repeat['repeat_every'] 	= $formData['daily_every'];
					
					//get arr day
					if($data_repeat['repeat_end']==1){
						$data_repeat['repeat_occurance'] = $formData['end_occurence'];
						$arr_day = $this->makeDayArrayOccurence($data_repeat['start'], $data_repeat['repeat_occurance'], $data_repeat['repeat_every']);
					}else
					if($data_repeat['repeat_end']==2){
						$data_repeat['repeat_end_date'] = $formData['end_date'];
						$arr_day = $this->makeDayArray($data_repeat['start'], $data_repeat['end'], $data_repeat['repeat_every']);
					}	
				}else
				if( $formData['repeat_type']==2 ){//*** weekly ***//
					
					$data_repeat['repeat_every'] 	= $formData['weekly_every'];
					$data_repeat['repeat_on'] 	= $formData['weekly_day'];
					
					//get arr day
					if($data_repeat['repeat_end']==1){
						$data_repeat['repeat_occurance'] = $formData['end_occurence'];
						
						$arr_day = $this->makeWeekArrayOccurence($data_repeat['start'], $data_repeat['repeat_occurance'],$data_repeat['repeat_every'],$data_repeat['repeat_on'] );
					}else
					if($data_repeat['repeat_end']==2){
						$data_repeat['repeat_end_date'] = $formData['end_date'];
						
						$arr_day = $this->makeWeekArray( $data_repeat['start'], $data_repeat['repeat_end_date'] , $data_repeat['repeat_every'], $data_repeat['repeat_on']);
					}
					
				}else
				if( $formData['repeat_type']==3 ){//*** monthly ***//
					
					//$data_repeat['repeat_every'] 	= $formData['monthly_every'];
					//$data_repeat['repeat_by'] 	= $formData['monthly_by'];
					
					//get arr day
					if($data_repeat['repeat_end']==1){
						$data_repeat['repeat_occurance'] = $formData['end_occurence'];
						
						$arr_day = $this->makeMonthArrayOccurence($data_repeat['start'], $data_repeat['repeat_occurance'] );
					}else
					if($data_repeat['repeat_end']==2){
						$data_repeat['repeat_end_date'] = $formData['end_date'];
						
						$arr_day = $this->makeMonthArray($data_repeat['start'], $data_repeat['repeat_end_date'] );
					}
					
					
				}
				
				//add to database
				foreach ($arr_day as $date_exam){
					
					$data['exam_date'] = date('Y-m-d', strtotime($date_exam) );
					
					//schedule
					$scheduleDB = new App_Model_Schedule_DbTable_Schedule();
					$sid = $scheduleDB->addData($data);
					
					//course
					$scheduleCourseDB = new App_Model_Schedule_DbTable_ScheduleCourse();
					foreach ($data['course_id'] as $course_id){
						$scheduleCourseDB->addData(array('schedule_id'=>$sid, 'course_id'=>$course_id));
					}
					
					//schedule venue
					$scheduleDetailDB = new App_Model_Schedule_DbTable_ScheduleVenue();
					foreach ($data['venue_id'] as $venue_id){
						$scheduleDetailDB->addData(array('schedule_id'=>$sid,'venue_id'=>$venue_id));
					}
				}
				
				
			}else{
				/** No Repeat **/	
				//schedule
				$scheduleDB = new App_Model_Schedule_DbTable_Schedule();
				$sid = $scheduleDB->addData($data);
				
				//course
				$scheduleCourseDB = new App_Model_Schedule_DbTable_ScheduleCourse();
				foreach ($data['course_id'] as $course_id){
					$scheduleCourseDB->addData(array('schedule_id'=>$sid, 'course_id'=>$course_id));
				}
					
				//schedule venue
				$scheduleDetailDB = new App_Model_Schedule_DbTable_ScheduleVenue();
				foreach ($data['venue_id'] as $venue_id){
					$scheduleDetailDB->addData(array('schedule_id'=>$sid,'venue_id'=>$venue_id));
				}
			}
			
				
			
			//redirect
			$this->_redirect($this->view->url(array('module'=>'schedule','controller'=>'index', 'action'=>'index'),'default',true));	
			
        	
        }else{
        
	    	$this->view->title = "Add Exam Schedule";
	    	
	    	//program list
	    	$programDB = new App_Model_Record_DbTable_Program();
	    	$this->view->programlist = $programDB->getData();
	    	
	    	//exam center list
	    	$examCenterDB = new App_Model_General_DbTable_Venue();
	    	$this->view->examcenter = $examCenterDB->getData();
        }
    }
    
    public function editAction(){
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
    	$this->view->title = "Edit Exam Schedule";
    	$schedule_id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			$data = array(
						//'id'			=>$schedule_id,
						'exam_date'		=>date('Y-m-d', strtotime($formData['exam_date']) ),
						'exam_time_start'=>$formData['start'],
						'exam_time_end'	=>$formData['end'],
						'exam_center'	=>$formData['exam_center'],
						'program_id'	=>$formData['program_id'],
						//'course_id'		=>$formData['course_id'],
						//'venue_id'		=>$formData['venue_id']
					);

			
			//schedule
			$scheduleDB = new App_Model_Schedule_DbTable_Schedule();
			$scheduleDB->updateData($data, $schedule_id);
			
			//course
			$scheduleCourseDB = new App_Model_Schedule_DbTable_ScheduleCourse();
			$scheduleCourseDB->deleteScheduleData($schedule_id);
			foreach ($formData['course_id'] as $course){
				$scheduleCourseDB->addData(array('schedule_id'=>$schedule_id,'course_id'=>$course));	
			}
			
			//venue
    		$scheduleVenueDB = new App_Model_Schedule_DbTable_ScheduleVenue();
			$scheduleVenueDB->deleteScheduleData($schedule_id);
			foreach ($formData['venue_id'] as $venue){
				$scheduleVenueDB->addData(array('schedule_id'=>$schedule_id,'venue_id'=>$venue));	
			}
			
			//redirect
			$this->_redirect($this->view->url(array('module'=>'schedule','controller'=>'index', 'action'=>'index'),'default',true));
    	}else{
    	
	    	$scheduleDB = new App_Model_Schedule_DbTable_Schedule();
	    	$schedule = $scheduleDB->getDataComplete($schedule_id);
	    	$this->view->schedule = $schedule;
	    	    	
	    	//program list
	    	$programDB = new App_Model_Record_DbTable_Program();
	    	$this->view->programlist = $programDB->getData();
	    	
	    	//exam center list
	    	$examCenterDB = new App_Model_General_DbTable_Venue();
	    	$this->view->examcenter = $examCenterDB->getData();
    	}
    }
    
    public function deleteAction(){
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
    	$this->view->title = "Delete Exam Schedule";
    }
    
    public function ajaxGetCourseAction(){
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            
            $ajaxContext = $this->_helper->getHelper('AjaxContext');
	        $ajaxContext->addActionContext('view', 'html');
	        $ajaxContext->initContext();
	        
	        $programCourseDB = new App_Model_Record_DbTable_ProgramCourse();
			$course_data = $programCourseDB->getProgramCourse($id);
		
		
			$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

			$json = Zend_Json::encode($course_data);
		
			$this->view->json = $json;
        
        }else{
        	die();
        }
    }
    
	public function ajaxGetVenueDetailAction(){
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            
            $ajaxContext = $this->_helper->getHelper('AjaxContext');
	        $ajaxContext->addActionContext('view', 'html');
	        $ajaxContext->initContext();
	        
	        $venueDetailDB = new App_Model_General_DbTable_VenueDetail();
			$venue_detail_data = $venueDetailDB->getCenterData($id);
		
		
			$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

			$json = Zend_Json::encode($venue_detail_data);
		
			$this->view->json = $json;
        
        }else{
        	die();
        }
    }
    
	public function ajaxGetScheduleAction(){
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
			$data = $scheduleDB->getSchedule($dtStart, $dtEnd, $id);
			
			//add course and venue detail
			$i=0;
			foreach ($data as $sch){
				//course
				$scheduleCourseDB = new App_Model_Schedule_DbTable_ScheduleCourse();
				$courses = $scheduleCourseDB->getScheduleData($sch['id']);
				$data[$i]['course'] = $courses;
				
				//venue detail
				$scheduleVenueDB = new App_Model_Schedule_DbTable_ScheduleVenue();
				$venue = $scheduleVenueDB->getScheduleData($sch['id']);
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
							'end'=>$event['exam_date']." ".$event['exam_time_end']
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
    
	public function ajaxUpdateScheduleTimeAction(){
		
    	$id = $this->_getParam('id', 0);
    	$start = $this->_getParam('start', 0);
    	$end = $this->_getParam('end', 0);
    	
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            
            $ajaxContext = $this->_helper->getHelper('AjaxContext');
	        $ajaxContext->addActionContext('view', 'html');
	        $ajaxContext->initContext();
	        
	        $dtExam = date('Y-m-d',$start);
	        $dtStart = date('H:i:s',$start);
	        $dtEnd = date('H:i:s',$end);
	        
	        $data = array(
	        			'exam_date'=>$dtExam,
	        			'exam_time_start'=>$dtStart,
	        			'exam_time_end'=>$dtEnd
	        		);
	        		
	        $scheduleDB = new App_Model_Schedule_DbTable_Schedule();
	        
			if($scheduleDB->update($data, 'id = '. (int)$id)){
				$status=array('status'=>1);	
			}else{
				$status=array('status'=>0);
			}
	       
			
			
		
			$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

			$json = Zend_Json::encode($status);
		
			$this->view->json = $json;
        
        }else{
        	die();
        }
    }
    
	public function makeDayArray( $startDate , $endDate, $skipday ){
		// Just to be sure - feel free to drop these is your sure of the input
		$startDate = strtotime( $startDate );
		$endDate   = strtotime( $endDate );
		
		// New Variables
		$currDate  = $startDate;
		$dayArray  = array();
		
		// Loop until we have the Array
		do{
			$dayArray[] = date( 'Y-m-d' , $currDate );
		    $currDate = strtotime( '+'.$skipday.' day' , $currDate );
		} while( $currDate<=$endDate );
		
		// Return the Array
		return $dayArray;
	}
	
	public function makeDayArrayOccurence( $startDate , $occurence,  $skipday){
		$i=0;
		$startDate = strtotime( $startDate );
		
		// New Variables
		$currDate  = $startDate;
		$dayArray  = array();
		
		// Loop until we have the Array
		do{
			$dayArray[] = date( 'Y-m-d' , $currDate );
		    $currDate = strtotime( '+'.$skipday.' day' , $currDate );
		    
		    $i++;
		} while( $i<=$occurence );
		
		// Return the Array
		return $dayArray;
	}
	
	public function makeWeekArray( $startDate, $endDate , $skipweek, $arr_week){

		$startDate = strtotime( $startDate );
		$endDate   = strtotime( $endDate );
		
		$skipweek = (int)$skipweek;
		
		// New Variables
		$currDate  = $startDate;
		$dayArray  = array();
		
		// Loop until we have the Array
		do{
				
			$day = date( 'w' , $currDate );
			
			if(  in_array($day, $arr_week) ){
				$dayArray[] = date( 'Y-m-d' , $currDate );	
			}
			
			//skip week
			if($day==0 && $skipweek>1){
		    	$currDate = strtotime( '+'.((($skipweek-1)*7)+1).' day' , $currDate );
			}else{
				$currDate = strtotime( '+1 day' , $currDate );	
			}
			
		} while( $currDate<=$endDate );
		
		// Return the Array
		return $dayArray;
	}
	
	public function makeWeekArrayOccurence( $startDate , $occurence,  $skipweek, $arr_week){
		$i=0;
		$startDate = strtotime( $startDate );
		
		$skipweek = (int)$skipweek;
		
		// New Variables
		$currDate  = $startDate;
		$dayArray  = array();
		
		// Loop until we have the Array
		do{
				
			$day = date( 'w' , $currDate );
			
			if(  in_array($day, $arr_week) ){
				$dayArray[] = date( 'Y-m-d' , $currDate );	
			}
			
			//skip week
			if($day==0 && $skipweek>1){
		    	$currDate = strtotime( '+'.((($skipweek-1)*7)+1).' day' , $currDate );
			}else{
				$currDate = strtotime( '+1 day' , $currDate );	
			}
			
		    $i++;
		} while( sizeof($dayArray)<$occurence );
		
		// Return the Array
		return $dayArray;
	}
	
	public function makeMonthArrayOccurence($startDate , $occurence,  $skipmonth=0, $repeatby=0){
		
		$startDate = strtotime( $startDate );
		
		$skipmonth = (int)$skipmonth;
		
		// New Variables
		$currDate  = $startDate;
		$dayArray  = array();
		
		// Loop until we have the Array
		do{
			$dayArray[] = date( 'Y-m-d' , $currDate );
				
			$i=1;//check same date next month else skip to another month
			while( date('j', strtotime('+'.$i.'  month', $currDate)) != date('j', $startDate) ){
				$i++;
			}
			
			$currDate = strtotime('+'.$i.'  month', $currDate);
			
		} while( sizeof($dayArray)<$occurence );
		
		// Return the Array
		return $dayArray;
	}
	
	public function makeMonthArray($startDate , $endDate,  $skipmonth=0, $repeatby=0){
		
		$startDate = strtotime( $startDate );
		$endDate   = strtotime( $endDate );
		
		$skipmonth = (int)$skipmonth;
		
		// New Variables
		$currDate  = $startDate;
		$dayArray  = array();
		
		// Loop until we have the Array
		do{
			$dayArray[] = date( 'Y-m-d' , $currDate );
				
			$i=1;//check same date next month else skip to another month
			while( date('j', strtotime('+'.$i.'  month', $currDate)) != date('j', $startDate) ){
				$i++;
			}
			
			$currDate = strtotime('+'.$i.'  month', $currDate);
			
		} while( $currDate<=$endDate );
		
		// Return the Array
		return $dayArray;
	}
}