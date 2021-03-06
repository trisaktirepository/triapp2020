<?php

class ExamCenter_RecordController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        
    	$this->_helper->layout->setLayout('exam');
    	$this->view->title="Application List";
    	
    	$auth = Zend_Auth::getInstance();
		$idUpd = $auth->getIdentity()->id;
		
		$courseDB = new App_Model_Record_DbTable_Course();
		$courseData = $courseDB->getData();
		$this->view->courseList = $courseData;
		
    //paginator
		$registrationDB = new App_Model_Record_DbTable_Registrationdetails();
		$registrationData = $registrationDB->getPaginateDataFinance('list','idVenue = '.$idUpd);
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($registrationData));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
		
		$this->view->page = $this->_getParam('page',1);
		
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$msg = $this->_flashMessenger->getMessages();
		if($msg!=null){
			$this->view->noticeMessage = $msg[0];
		}
		
    }
    
	public function startExamAction()
    {
        // action body
        
    	$this->_helper->layout->setLayout('exam');
    	$this->view->title="Start Examination";
    	
   		if ($this->getRequest()->isPost()) {
			
			echo $formData = $this->getRequest()->getPost();
			echo $date = $formData['start_date'];
			$this->view->datepicker = $date;
    	}else{
    		echo $date = date('Y-m-d');
    	}
    	
    	
    	$auth = Zend_Auth::getInstance();
		$idUpd = $auth->getIdentity()->id;
    	
    	$scheduleDB = new App_Model_Schedule_DbTable_Schedule();
    	$checkSchedule = $scheduleDB->getScheduleByExamCenter($date,$idUpd);
    	if($checkSchedule){
    		$this->view->noticeSuccess = "Takaful Basic Examination on $date";
    		echo "<pre>";
    		print_r($checkSchedule);
    		echo "</pre>";
    		$this->view->schedule = $checkSchedule;
    		
    	}else{
    		$this->view->noticeError = "NO Takaful Basic Examination on $date";
    	}
    	
    	
		
    }
    
 	public function ajaxSaveStartExamAction($id=null){
    	
    	$idSchedule = $this->_getParam('idSche', 0);
    	$idCourse = $this->_getParam('idCourse', 0);
    	$idCenter = $this->_getParam('idCenter', 0);
    	$idStart = $this->_getParam('idStart', 0);
    	echo $id = $this->_getParam('id', 0);
    	
    	
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
            
        $time = date('H:i:s');
        
		$startDB = new App_Model_Exam_DbTable_StartExam();
		
		if($id==1){
			$data = array(
		        'idCourse' => $idCourse,
		        'idSchedule' => $idSchedule,
		        'idCenter' => $idCenter,
		        'startTime' => $time
	        );
        
			$idInsert = $startDB->add($data);
		}else{
			$dataClose = array(
		        'endTime' => $time
	        );
        	$idInsert = $idStart;
			$updateData = $startDB->updateData($dataClose,$idStart);
		}

		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($idInsert);
		
		$this->view->json = $json;

    }


}

