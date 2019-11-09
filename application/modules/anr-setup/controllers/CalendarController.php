<?php

class AnrSetup_CalendarController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        //title
        $this->view->title = "Calendar Setup";
        
        $activityDB = new App_Model_Record_DbTable_Activity();
        $activityList = $activityDB->getData();
        $this->view->activitylist = $activityList;
       	$this->view->color = array('red', 'blue', 'green', 'yellow', 'aqua', 'fuchsia','lime', 'maroon', 'navy', 'olive', 'purple', 'silver', 'teal', 'white');
       	
       	$calendarDB = new App_Model_Record_DbTable_Calendar();
       	$calendar = $calendarDB->getSemesterEventList();
       	
       	$this->view->eventList = $calendar;
    }
    
    public function viewAction(){
    	//title
		$this->view->title = "Event Details";
		
		$calendarID = $this->_getParam('id', 0);
		$this->view->id = $calendarID;
		
		if($calendarID==0){
			$this->view->noticeError = "Semester is not defined";
		}
		
		$calendarDB = new App_Model_Record_DbTable_Calendar();
		$this->view->data = $calendarDB->getData($calendarID); 

		/*echo "<pre>";
       	print_r($this->view->data);
       	echo "</pre>";*/
    }
    
    public function addAction(){
    	//title
		$this->view->title = "Add Calendar Event";
		
    	$form = new AnrSetup_Form_Calendar();
    	    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$calendarDB = new App_Model_Record_DbTable_Calendar();
				$calendarDB->addData($formData);
								
				//redirect
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'calendar', 'action'=>'index'),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
    	
    }
    
    public function editAction(){
    	//title
		$this->view->title = "Edit Calendar Event";
		
		$calendarID = $this->_getParam('id', 0);
		$this->view->id = $calendarID;
		
		$calendarDB = new App_Model_Record_DbTable_Calendar();
		$data = $calendarDB->getData($calendarID); 
		
    	$form = new AnrSetup_Form_Calendar();
    	    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$calendarDB = new App_Model_Record_DbTable_Calendar();
				$calendarDB->updateData($formData,$calendarID);
								
				//redirect
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'calendar', 'action'=>'index'),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }else{
        	$form->populate($data);
        }
    	
        $this->view->form = $form;
    }
    
    public function deleteAction(){
    	$calendarID = $this->_getParam('id', 0);
    	$calendarDB = new App_Model_Record_DbTable_Calendar();
		$calendarDB->deleteData($calendarID);
								
		//redirect
		$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'calendar', 'action'=>'index'),'default',true));
    }
}

