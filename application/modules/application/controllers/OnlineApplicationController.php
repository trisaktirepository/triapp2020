<?php

class Application_OnlineApplicationController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
		$this->_helper->layout->setLayout('onapp');
    	$this->view->title="Individual Application";
    	
    	$id= $this->_getParam('id', 0);
		$this->view->id = $id;
    	
		if ($id) {
			
			$IndexDbTable = new App_Model_Record_DbTable_Student();
			$data_applicant = $IndexDbTable->getApplicant($id);
			$this->view->applicant = $data_applicant;
			
		}
	}
	
public function paypalAction() {
		//title
		$this->_helper->layout->setLayout('onapp');
    	$this->view->title="Individual Application";
    	
    
	}
	
	public function personalAction() {
		//title
		$this->_helper->layout->setLayout('onapp');
    	$this->view->title="Individual Application";
    	
    	$id= $this->_getParam('id', 0);
		$this->view->id = $id;
		
		$lookupDB = new App_Model_General_DbTable_Lookup();
		$religionData = $lookupDB->getData(8);
		$this->view->religion = $religionData;
		
		$raceData = $lookupDB->getData(13);
		$this->view->race = $raceData;
		
		$qualificationData = $lookupDB->getData(14);
		$this->view->qualification = $qualificationData;
    	
		$takafulDB = new App_Model_General_DbTable_TakafulOperator();
		$takafulData = $takafulDB->getDataType(2);
		$this->view->takaful = $takafulData;
		
		$stateDB = new App_Model_General_DbTable_State();
		$stateData = $stateDB->getState(129); //state in malaysia only
		$this->view->state = $stateData;
		
		$studentDbTable = new App_Model_Record_DbTable_Student();
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			if($formData["qic1"]){
				$icno = $formData["qic1"].$formData["qic2"].$formData["qic3"];
				$this->view->icno = $icno;
				
				//checking disciplinary case
				$disciplineDB = new App_Model_Discipline_DbTable_StudentCase();
				$checkDiscipline = $disciplineDB->getAlow($icno);
				if($checkDiscipline){
					$this->view->msgdiscipline = "You are not allowed to register for Takaful Basic Examination. Please contact IBFIM.";
				}else{
				
					$checkStudent = $studentDbTable->checkStudent($icno);
					
					if($checkStudent){
						$this->view->student = $checkStudent;
					}
				}
			}
		}else{
			$getStudent = $studentDbTable->getStudent($id);
			$this->view->student = $getStudent;
			$this->view->icno = $getStudent["ARD_IC"];
		}
	
	}
	
	public function additionalAction(){
    	
		$this->_helper->layout->setLayout('onapp');
		//title	
    	$this->view->title="Additional Information";
    	
		$app_id = $this->_getParam('id', 0);
		$this->view->id = $app_id;
		
		if ($this->_request->isPost()) {  
			$rank = $this->getRequest()->getPost('checkprogram');   
			$n = count($rank);
			$i=0;
			$bil = 1;
			while ($i < $n) {
//				echo "<br>";
//				echo $rank[$i];
//				echo " = BIL ";
				$select = $this->getRequest()->getPost('rank_program'.$bil);   
				
				
				$data = array(
			        'applicantid' => $app_id,
			        'programid' => $rank[$i],
					'rank' => $select+1
					);
				
				$appliedDB = new App_Model_Application_DbTable_AppliedProgram();
				
				$checkapplied = $appliedDB->getAppliedProgramdetails($app_id,$rank[$i]);
				if (!$checkapplied) {
					$applied = $appliedDB->add($data);
				}
				
				$bil++;
				$i++;
			}
		}
		
		$applicantDB = new App_Model_Record_DbTable_Student();
		$applicant = $applicantDB->getApplicant($app_id);
		$this->view->applicant  = $applicant;
		
		$additionalDB = new App_Model_Application_DbTable_Additional();
		$checkAdditional = $additionalDB->getAdditional($app_id);
		$this->view->additional  = $checkAdditional;

	}
	
	public function conditionalOfferLetterAction() {
		
		$this->_helper->layout->setLayout('onapp');
    	$this->view->title="Conditional Offer Letter";
    	
    	$id= $this->_getParam('id', 0);
		$this->view->id = $id;
		
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			$additionalDB = new App_Model_Application_DbTable_Additional();
			$applicantDB = new App_Model_Record_DbTable_Student();
			$checkAdditional = $additionalDB->checkAdditional($id);
			
			if ($checkAdditional) {
				$update = $additionalDB->updateData($formData,$id);
				$updateAdd = $applicantDB->updateAdditional($formData,$id);
				
			}else{
				$additional = $additionalDB->add($formData,$id);
			}
			
			
			$templateDB = new App_Model_Application_DbTable_OfferLetterItem();
			$template = $templateDB->getList();
			$this->view->template  = $template;
			
			$applicant = $applicantDB->getApplicant($id);
			$this->view->applicant  = $applicant;
			
			$appliedDB = new App_Model_Application_DbTable_AppliedProgram();
			$applied= $appliedDB->getAppliedProgram($id);
			$this->view->appliedprogram  = $applied;
			
		}
		
		
    	
		
	}
	
	public function examDetailAction(){
		$this->_helper->layout->setLayout('onapp');
    	$this->view->title="Individual Application";
		
		$courseSelect = $this->_getParam('course_id', 0);
		$this->view->courseSelect = $courseSelect;
		
		$app_id = $this->_getParam('idApp', 0);
		$this->view->idApp = $app_id;
		
		$idApplication = $app_id;
		
		$exam_center_id = $this->_getParam('exam_center_id', 0);
		$this->view->exam_center_id = $exam_center_id;
		
		$venueDB = new App_Model_General_DbTable_Venue();
		$venueData = $venueDB->getData();
		$this->view->venue = $venueData;
		
		$schevenueDb = new App_Model_Schedule_DbTable_ScheduleApp();
		$schevenueData = $schevenueDb->getSchedulebyCourse($courseSelect);
		$this->view->listVenue = $schevenueData;
		
		
		$venuenameData = $venueDB->getVenue($exam_center_id);
		if($venuenameData){
			$this->view->venueName = $venuenameData["name"];
		}else{
			$this->view->venueName = "-- Please select -- ";
		}
		
		
			
		$studentDbTable = new App_Model_Record_DbTable_Student();
		
		$registrationDB = new App_Model_Record_DbTable_Registrationdetails();
		$checkRegister = $registrationDB->checkRegisterApplication($app_id,$courseSelect);
		
//		if($courseSelect !=0){
		$this->view->checkRegister = $checkRegister;
//		}
				
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$date = date('Y-m-d H:i:s');
			
			$icno = $formData["ARD_IC"];
			
			$checkStudent = $studentDbTable->checkStudent($icno);
			
			if(!$checkStudent){
			
				$data = array (
						"ARD_NAME" => strtoupper($formData["ARD_NAME"]),
					    "ARD_EMAIL" => $formData["ARD_EMAIL"],
						"ARD_DOB" => $formData["ARD_DOB"],
						"ARD_IC" => $formData["ARD_IC"],
					    "ARD_ADDRESS1" => $formData["ARD_ADDRESS1"],
					    "ARD_ADDRESS2" => $formData["ARD_ADDRESS2"],
					    "ARD_POSTCODE" => $formData["ARD_POSTCODE"],
					    "ARD_STATE" => $formData["ARD_STATE"],
						"ARD_CITY" => $formData["ARD_CITY"],
					    "ARD_COUNTRY" => $formData["ARD_COUNTRY"],
					    "ARD_PHONE" => $formData["ARD_PHONE"],
					    "ARD_HPHONE" => $formData["ARD_HPHONE"],
					    "ARD_RACE" => $formData["ARD_RACE"],
					    "ARD_RELIGION" => $formData["ARD_RELIGION"],
					    "ARD_SEX" => $formData["ARD_SEX"],
					    "ARD_TAKAFUL" => $formData["ARD_TAKAFUL"],
					    "ARD_QUALIFICATION" => $formData["ARD_QUALIFICATION"],
					    "ARD_CLIENTTYPE" => 1,
						"username" => $formData["username"],
						"password" => md5($formData["clearpass"]),
						"clearpass" => $formData["clearpass"],
						"ARD_DATE_APP" => $date
				);
				
//				echo "<pre>";
//				print_r($data);
//				echo "</pre>";
				
				
							
				$add = $studentDbTable->addStudent($data);
				$idApplication = $add;
				
				
			}else{
				$this->view->student=$checkStudent;
				
		
				$idStud = $checkStudent["ID"];
				$idApplication = $idStud;
				
				$data = array (
						"ARD_NAME" => strtoupper($formData["ARD_NAME"]),
					    "ARD_EMAIL" => $formData["ARD_EMAIL"],
					    "ARD_ADDRESS1" => $formData["ARD_ADDRESS1"],
					    "ARD_ADDRESS2" => $formData["ARD_ADDRESS2"],
					    "ARD_POSTCODE" => $formData["ARD_POSTCODE"],
					    "ARD_STATE" => $formData["ARD_STATE"],
						"ARD_CITY" => $formData["ARD_CITY"],
					    "ARD_COUNTRY" => $formData["ARD_COUNTRY"],
					    "ARD_PHONE" => $formData["ARD_PHONE"],
					    "ARD_HPHONE" => $formData["ARD_HPHONE"],
					    "ARD_RACE" => $formData["ARD_RACE"],
					    "ARD_RELIGION" => $formData["ARD_RELIGION"],
					    "ARD_SEX" => $formData["ARD_SEX"],
					    "ARD_TAKAFUL" => $formData["ARD_TAKAFUL"],
					    "ARD_QUALIFICATION" => $formData["ARD_QUALIFICATION"],
					    "ARD_CLIENTTYPE" => 1,
//						"username" => $formData["username"],
						"password" => md5($formData["clearpass"]),
						"clearpass" => $formData["clearpass"],
						"ARD_DATE_APP" => $date
				);
//					echo "<pre>";
//				print_r($data);
//				echo "</pre>";
				
				$add = $studentDbTable->updateStudent($data,$idStud);
				
			
				//get registration history
				
				
				
		
		
			}
			
			
			
         }
         
         
        
         
         $this->view->idApp = $idApplication;
			
		$registrationDB = new App_Model_Record_DbTable_Registrationdetails();
		$checkRegister = $registrationDB->checkRegister($idApplication);
		$this->view->register = $checkRegister;
		
		
		$countgr = count($checkRegister);
		$dataCourse = array();
		foreach($checkRegister as $ck){
			 $dataCourse[] = $ck['idCourse'];
		}
		
//		print_r($dataCourse);
		
		$programDB = new App_Model_Record_DbTable_Course();
		$programData = $programDB->getRemainCourse($dataCourse);
		$this->view->program = $programData;
	}
	
	public function ajaxGetVenueAction($id=null){
    	
    	$program = $this->_getParam('id', 0);
    	$app_id = $this->_getParam('idApp', 0);
    	
    	// check is AJAX request or not
     	/*if (!$this->getRequest() -> isXmlHttpRequest()) {
        	$this->getResponse() -> setHttpResponseCode(404)
                              -> sendHeaders();
         	$this->renderScript('empty.phtml');
         	return false;
     	}*/
    	
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
		$registrationDB = new App_Model_Record_DbTable_Registrationdetails();
		$checkRegister = $registrationDB->checkRegisterApplication($app_id,$program);
		
		if($checkRegister == 1){
			$venueData = 1;
		}else{
			$venueDb = new App_Model_Schedule_DbTable_ScheduleApp();
			$venueData = $venueDb->getSchedulebyCourse($program);
		}
//         
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($venueData);
		
		$this->view->json = $json;

    }
    
	public function ajaxGetScheduleAction(){
    	$id = $this->_getParam('exam_center_id', 0);
    	$courseid = $this->_getParam('course_id', 0);
    	$start = $this->_getParam('start', 0);
    	$end = $this->_getParam('end', 0);
    	
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            
            $ajaxContext = $this->_helper->getHelper('AjaxContext');
	        $ajaxContext->addActionContext('view', 'html');
	        $ajaxContext->initContext();
	        
	        $dtStart = date('Y-m-d',$start);
	        $dtEnd = date('Y-m-d',$end);
	        
	        $scheduleDB = new App_Model_Schedule_DbTable_ScheduleApp();
			$data = $scheduleDB->getSchedule($dtStart, $dtEnd, $id,$courseid);
			
			//add course and venue detail
			$schedule=array();
			$i=0;
			foreach ($data as $event){
				//course
//				$scheduleCourseDB = new App_Model_Schedule_DbTable_ScheduleCourse();
//				$courses = $scheduleCourseDB->getScheduleData($sch['id']);
				$data[$i]['course'] = array();
				
				//venue detail
				$scheduleVenueDB = new App_Model_Schedule_DbTable_ScheduleVenue();
				$venue = $scheduleVenueDB->getScheduleData($event['id']);
				$data[$i]['venue'] = $venue;
				
				//calculated 14 days
				$examdayStart = $event['exam_date'];
				
				//mark seats availability
				$venueDetailDB = new App_Model_General_DbTable_VenueDetail();
				$capacity =  $venueDetailDB->getCapacity($id);
				$capacitySeat = $capacity['SUM(capacity)'];
				
				
				$registrationDB = new App_Model_Record_DbTable_Registrationdetails();
				$seat = $registrationDB->countSeatRegister($event['id'],$courseid,$id);
				$countSeat = count($seat);
				
				$available = $capacitySeat - $countSeat;
				
				if ($available>0){
					$color = '#026510';//available
				}else{
					$color = '#e10404';//full
				}
				
				$schedule[$i] = array(
							'id'=> $event['id'],
							'title'=>$event['course_name'] ."\n ".date('d-m-Y', strtotime($event['exam_date'])) ."\n ". $event['exam_center_name']."\n".date('g:ia', strtotime($event['exam_time_start']))." - ".date('g:ia', strtotime($event['exam_time_end'])),
//							'title'=>" ".$event['exam_center_name']." xxxx \n " ,
							'allDay'=>true,
							'start'=>$examdayStart." ".$event['exam_time_start'],
							'end'=>$examdayStart." ".$event['exam_time_end'],
							'backgroundColor' => $color,
							'borderColor' => '#000000',
							'className' => 'tableCal'
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
    
	public function ajaxCheckUsernameAction($id=null){
    	
    	$username = $this->_getParam('id', 0);
    	
    	// check is AJAX request or not
     	/*if (!$this->getRequest() -> isXmlHttpRequest()) {
        	$this->getResponse() -> setHttpResponseCode(404)
                              -> sendHeaders();
         	$this->renderScript('empty.phtml');
         	return false;
     	}*/
    	
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
		$studentDB = new App_Model_Record_DbTable_Student();
		$studentData = $studentDB->checkUsername($username);
		if($studentData){
			$msg = "Please select different username";
		}else{
			$msg = "";
		}
		
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($msg);
		
		$this->view->json = $json;

    }
    
    public function viewAction()
    {
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
    	}
    	
    	$id = $this->_getParam('id', 0);
    	$courseid = $this->_getParam('course_id');
    	$idApp = $this->_getParam('idApp');
    	
    	$this->view->id = $id;
    	$this->view->courseid = $courseid[0];
    	$this->view->idApp = $idApp[0];
    	

    	$courseid2 = $courseid[0];
        // title
        $this->view->title = "Examination Details";
        
        $scheduleDB = new App_Model_Schedule_DbTable_ScheduleApp();
        $schedule = $scheduleDB->getData($id);
        $this->view->schedule = $schedule;
        
        $course = $scheduleDB->getScheduleDataView($id,$courseid2);
        $this->view->course = $course;
        
        $scheduleVenueDB = new App_Model_Schedule_DbTable_ScheduleVenue();
        $venue = $scheduleVenueDB->getScheduleData($id);
        $this->view->venue = $venue;
        
        $modeDB = new Finance_Model_DbTable_Paymentmode();
        $modeData = $modeDB->getDataIndividual();
        $this->view->paymentmode = $modeData;
        
        $feestrustureDB = new Finance_Model_DbTable_Feestructure();
        $feedata = $feestrustureDB->getFee($courseid2);
        $this->view->fee = $feedata;
        
        //check seat availabity
        $venueDetailDB = new App_Model_General_DbTable_VenueDetail();
		$capacity =  $venueDetailDB->getCapacity($schedule['exam_center']);
		$capacitySeat = $capacity['SUM(capacity)'];
		
		$registrationDB = new App_Model_Record_DbTable_Registrationdetails();
		$seat = $registrationDB->countSeatRegister($id,$courseid2,$schedule['exam_center']);
		$countSeat = count($seat);
		
		$available = $capacitySeat - $countSeat;
		
		if ($available>0){
			$this->view->seat = $available;
		}else{
			$this->view->seat = "Seats are full.";
		}
				
    }
    
    
    public function paymentAction()
    {
    	$this->_helper->layout->setLayout('onapp');
    	$this->view->title="Individual Application";
    	
    	$id = $this->_getParam('id', 0);
    	$courseid = $this->_getParam('course_id');
    	$idApp = $this->_getParam('idApp');
    	
    	$this->view->id = $id;
    	$this->view->courseid = $courseid;
    	$this->view->idApp = $idApp;
    	
    	$scheduleDB = new App_Model_Schedule_DbTable_ScheduleApp();
     
        $schedule = $scheduleDB->getScheduleDataView($id,$courseid);
        
        foreach ($schedule as $data){
        	$idVenue = $data["exam_center"];
        	
        }
        
        $registrationDb = new App_Model_Record_DbTable_Registrationdetails();
        
        if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$mode = $formData["paymentmode"];
			$currency = $formData["currency"];
			$amount = $formData["amount"];
        
	        $date = date('Y-m-d H:i:s');
	        
//	        $checkStudent = $registrationDb->getStudent($idApp);
//	        if(!$checkStudent){
//	        
		        $data = array(
				        'idApplication' => $idApp,
				        'idProgram' => 1,
				        'idCourse' => $courseid,
				        'idSchedule' => $id,
				        'idVenue' => $idVenue,
				        'paymentMode' => $mode,
				        'paymentStatus' => 0,
				        'dateApplied' => $date,
		        		'seatStatus' => 0
		        );
		        
		        $addRegistration = $registrationDb->add($data);
		        
		        //invoice
		        
		        $invDB = new App_Model_Finance_DbTable_Invoice();
		        
		        $latestInv = $invDB->getLastInvoice();
    		
	    		if($latestInv){
	    			$currentNum = $latestInv['runningNumber'];
	    		}
	    		
	    		$currentNum = $currentNum+1;
	    		
				$runningNumber = str_pad($currentNum,6,'0',STR_PAD_LEFT);
	    		
	    		$invoiceFormat = $programCode."-".$courseCode."-".$data['id']."-".$runningNumber;
    		
		        $dataInvoice = array(
		    	 	'idApplication' => $idApp,
		    	 	'idClienttype' => 1,//individual
		    	 	'paymentmode' => $mode,
		    	 	'receiptNo' => $invoiceFormat,
		    	 	'runningNumber' => $currentNum,
		    	 	'txnAmount' => $amount,
		    	 	'txnCurrency' => $currency,
		    	 	'txnDate' => $date,
		    	 	'txnStatus' => 0,
	    	 	
	    	 	);
    	 	
    	 	
//    	 	$insert = $invDB->addData($dataInvoice);
    	 	
//		        $addInvoice = $registrationDb->add($data);
		        
//	        }
        }
    	
//    if($mode !=1 || $mode!=2){
    	$templateID = 1;
//    }else{
//    	$templateID = 2;
//    }
    	
    	$regisData = $registrationDb->getStudentProfile($idApp,$addRegistration);
    	
    	if($regisData['paymentStatus'] == 0){
    		$paymentSta = "Pending";
    	}else{
    		$paymentSta = "Completed";
    	}
    	
    	$address = $regisData["ARD_ADDRESS1"]." ".$regisData["ARD_ADDRESS2"]."<br>".$regisData["ARD_POSTCODE"]." ".$regisData["ARD_CITY"]." ".$regisData["state_name"]. "<br>".$regisData["country_name"];
    	
        $emailtemplateDb = new App_Model_General_DbTable_EmailTemplate();
    	$templateData = $emailtemplateDb->getData($templateID);
    	$this->view->emailTemplate = $templateData;
    	
        
        require_once('Zend/Mail.php');
		require_once('Zend/Mail/Transport/Smtp.php');			
		$lstrEmailTemplateFromDesc=  $templateData['TemplateFromDesc'];
		$lstrEmailTemplateSubject =  $templateData['TemplateSubject'];
		$lstrEmailTemplateBody    =  $templateData['TemplateBody'];
		$lstrEmailTemplateFooter  =  $templateData['TemplateFooter'];
	
		$lstrEmailTemplateBody = str_replace("[Candidate]",$regisData['ARD_NAME'],$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[Address]",$address,$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[ICNO]",$regisData['ARD_IC'],$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[username]",$regisData['username'],$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[password]",$regisData['clearpass'],$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[Program]",$regisData['course_name'],$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[venue]",$regisData['venue_name'],$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[Date]",date('d-m-Y', strtotime($regisData['exam_date'])),$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[SessionStart]",date('g:ia', strtotime($regisData['exam_time_start'])),$lstrEmailTemplateBody);
		//$lstrEmailTemplateBody = str_replace("[SessionEnd]",$regisData['exam_time_end'],$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[paymentMode]",$regisData['paymentMode'],$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[paymentStatus]",$paymentSta,$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[Amount]",$regisData['amount'],$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[Currency]",$regisData['currency'],$lstrEmailTemplateBody);
		
		$auth = 'ssl';
		$port = '465';
		$config = array('ssl' => $auth, 'port' => $port, 'auth' => 'login', 'username' => 'ibfiminfo@gmail.com', 'password' => 'abcd123#');
		$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
		$mail = new Zend_Mail();
		$mail->setBodyHtml($lstrEmailTemplateBody);
		$sender_email = 'ibfiminfo@gmail.com';
		$sender = $lstrEmailTemplateFromDesc;
		$receiver_email = $regisData["ARD_EMAIL"];
		$receiver = $regisData['ARD_NAME'];
		$mail->setFrom($sender_email, $sender)
			 ->addTo($receiver_email, $receiver)
	         ->setSubject($lstrEmailTemplateSubject);
		//$result = $mail->send($transport);
		$this->view->mess .= $lstrEmailTemplateBody;

 	   try {
		$result = $mail->send($transport);
		
		} catch (Exception $e) {								
	
		}
					
   
				
    	
    //redirect 
		    $this->_redirect($this->view->url(array('module'=>'application','controller'=>'online-application', 'action'=>'letter','idApp'=>$addRegistration,'id'=>$idApp),'default',true));
		
		    
    }
    
    public function letterAction()
    {
    
    	$this->_helper->layout->setLayout('onapp');
    	$this->view->title="Individual Application";
    	
    	$idApp = $this->_getParam('idApp', 0);
    	$this->view->idApp = $idApp;
    	
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	$type = $this->_getParam('type', "");
    	$this->view->type = $type;
    	
    	//checking payment clear ke tak.kalau clear, registrationdetails, kalau tak, booking
    	
    	$registrationDb = new App_Model_Record_DbTable_Registrationdetails();
    	$regisData = $registrationDb->getStudentProfile($id,$idApp);
    	
    	$emailtemplateDb = new App_Model_General_DbTable_EmailTemplate();
    	$templateData = $emailtemplateDb->getData(1);
    	$this->view->emailTemplate = $templateData;
    	
    	$venueDB = new App_Model_General_DbTable_Venue();
		$venueData = $venueDB->getData($regisData['idVenue']);
    	
//    $regisData = $registrationDb->getStudentProfile($idApp,$addRegistration);
    	
    	if($regisData['paymentStatus'] == 0){
    		$paymentSta = "Pending";
    	}else{
    		$paymentSta = "Completed";
    	}
    	
    	$address = $regisData["ARD_ADDRESS1"]." ".$regisData["ARD_ADDRESS2"]."<br>".$regisData["ARD_POSTCODE"]." ".$regisData["ARD_CITY"]." ".$regisData["state_name"]. "<br>".$regisData["country_name"];
    	
    	$venueAddress = $regisData["venue_name"]." <br>".$venueData["address1"]." ".$venueData["address2"]." <br>".$venueData["postcode"]." ".$venueData["city"]. "<br>".$venueData["state_name"]."<br>Malaysia";
    	
        $emailtemplateDb = new App_Model_General_DbTable_EmailTemplate();
    	$templateData = $emailtemplateDb->getData(1);
        
		$lstrEmailTemplateBody    =  $templateData['TemplateBody'];
	
		$lstrEmailTemplateBody = str_replace("[Candidate]",$regisData['ARD_NAME'],$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[Address]",$address,$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[ICNO]",$regisData['ARD_IC'],$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[Program]",$regisData['course_name'],$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[venue]",$venueAddress,$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[Date]",date('d-m-Y', strtotime($regisData['exam_date'])),$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[SessionStart]",date('g:ia', strtotime($regisData['exam_time_start'])),$lstrEmailTemplateBody);
		
		
//		$lstrEmailTemplateBody = str_replace("[SessionEnd]",$regisData['exam_time_end'],$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[paymentMode]",$regisData['paymentMode'],$lstrEmailTemplateBody);
//		$lstrEmailTemplateBody = str_replace("[paymentStatus]",$paymentSta,$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[Amount]",$regisData['amount'],$lstrEmailTemplateBody);
		$lstrEmailTemplateBody = str_replace("[Currency]",$regisData['currency'],$lstrEmailTemplateBody);
    	
		$this->view->emailTemplate = $lstrEmailTemplateBody;
		
//    	$mailsend = $aApply->sendmail($emailAdd, $fullName, $subject, $emailTemplateBody);
    	
    }
    
	public function generate_calendar($year, $month, $days = array(), $day_name_length = 3, $month_href = NULL, $first_day = 0, $pn = array()){

	
		$first_of_month = gmmktime(0,0,0,$month,1,$year);
		#remember that mktime will automatically correct if invalid dates are entered
		# for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998
		# this provides a built in "rounding" feature to generate_calendar()
	
		$day_names = array(); #generate all the day names according to the current locale
		for($n=0,$t=(3+$first_day)*86400; $n<7; $n++,$t+=86400) #January 4, 1970 was a Sunday
			$day_names[$n] = ucfirst(gmstrftime('%A',$t)); #%A means full textual day name
	
		list($month, $year, $month_name, $weekday) = explode(',',gmstrftime('%m,%Y,%B,%w',$first_of_month));
		$weekday = ($weekday + 7 - $first_day) % 7; #adjust for $first_day
		$title   = htmlentities(ucfirst($month_name)).'&nbsp;'.$year;  #note that some locales don't capitalize month and day names
	
		#Begin calendar. Uses a real <caption>. See http://diveintomark.org/archives/2002/07/03
		@list($p, $pl) = each($pn); @list($n, $nl) = each($pn); #previous and next links, if applicable
		if($p) $p = '<span class="calendar-prev">'.($pl ? '<a href="'.htmlspecialchars($pl).'">'.$p.'</a>' : $p).'</span>&nbsp;';
		if($n) $n = '&nbsp;<span class="calendar-next">'.($nl ? '<a href="'.htmlspecialchars($nl).'">'.$n.'</a>' : $n).'</span>';
		$calendar = '<table class="calendar" cellpadding="4" cellspacing="1">'."\n".
			'<caption class="calendar-month">'.$p.($month_href ? $title : $title).$n."</caption>\n<tr>";
//			'<caption class="calendar-month">'.$p.($month_href ? 's<a href="'.htmlspecialchars($month_href).'">'.$title.'</a>' : $title).$n."</caption>\n<tr>"; // linkkan bulan tu view details
	
		if($day_name_length){ #if the day names should be shown ($day_name_length > 0)
			#if day_name_length is >3, the full name of the day will be printed
			foreach($day_names as $d)
				$calendar .= '<th abbr="'.htmlentities($d).'">'.htmlentities($day_name_length < 4 ? substr($d,0,$day_name_length) : $d).'</th>';
			$calendar .= "</tr>\n<tr align='center'>";
		}
	
		if($weekday > 0) $calendar .= '<td colspan="'.$weekday.'">&nbsp;</td>'; #initial 'empty' days
		for($day=1,$days_in_month=gmdate('t',$first_of_month); $day<=$days_in_month; $day++,$weekday++){
			if($weekday == 7){
				$weekday   = 0; #start a new week
				$calendar .= "</tr>\n<tr align='center'>";
			}
			if(isset($days[$day]) and is_array($days[$day])){
				@list($link, $classes, $content) = $days[$day];
				if(is_null($content))  $content  = $day;
				$calendar .= '<td'.($classes ? ' class="'.htmlspecialchars($classes).'">' : '>').
//					($link ? '<a target="_blank" href="'.htmlspecialchars($link).'">'.$content.'</a>' : $content).'</td>'; // selected Date
					($link ? "<a class=\"linked\" href=\"javascript:popupSchedule('".htmlspecialchars($link)."')\">".$content."</a>" : $content)."</td>"; // selected Date
			}
			else $calendar .= "<td>$day</td>";
		}
		if($weekday != 7) $calendar .= '<td colspan="'.(7-$weekday).'">&nbsp;</td>'; #remaining "empty" days
	
		return $calendar."</tr>\n</table>\n";
	}
	
	
	public function addReqAction(){
    	
    	$id = $this->_getParam('id', 0);
		$this->view->id = $id; 
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
				
				//process form 
				$IndexDbTable = new App_Model_Record_DbTable_StudentEntry();
				$IndexDbTable->addData($formData);
				
			$this->_redirect('/application/online-application/add-apply/id/'.$id);	
        }
    	
	}
	
	
	public function editAction(){
		$this->_helper->layout->setLayout('onapp');
		$this->view->title="Additional Info";
    	    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {

    		$formData = $this->getRequest()->getPost();
				
				$applyDbTable = new App_Model_Application_DbTable_OnlineApplication();
				$applyDbTable->updateAdditionalData($formData,$id);
				
				$applyDB = new App_Model_Record_DbTable_Student();
				$place = $applyDB->getData($id);
					if ($place["ARD_PLACEMENT"] == 0) {
						$this->_redirect('/application/online-application/placement/id/'.$id);	
					}
					else {
						$this->_redirect('/application/online-application/offer-letter/id/'.$id);	
					}
				
    	}
    	$this->view->form = $form;
        $this->view->id = $id;
        
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	$id_app = $this->_getParam('id_app', 0);
    	
    	if($id>0){
    		$market = new App_Model_Record_DbTable_StudentEntry();
    		$market->deleteData($id);
    	}
    	$this->_redirect('/application/online-application/add-apply/id/'.$id_app);	
    }
    
    public function offerLetterAction(){
    	$this->_helper->layout->setLayout('onapp');
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    }
    
    public function placementAction(){
    	$this->_helper->layout->setLayout('onapp');
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    }
    
    public function sendMailAction(){
    	require_once 'Zend/Mail/Transport/Smtp.php';
    	
    	$config = array('ssl' => 'tls',
                'port' => 465); // Optional port number supplied
    	
    	$mailTransport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', array(
    'auth'     => 'login',
    'username' => 'ibfiminfo@gmail.com',
    'password' => 'abcd123#',
    'port'     => '465',
    'ssl'      => 'tls',
));
 
//	$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);

	    $mail = new Zend_Mail();
		$mail->setFrom('IBFIM Administrator');
		$mail->setBodyHtml('some message - it may be html formatted text');
		$mail->addTo('suliana@meteor.com.my', 'Full Name');
		$mail->setSubject('subject testing 123cc');
		$mail->send($mailTransport);
		
		exit;
    }

}


