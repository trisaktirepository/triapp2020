<?php

class Application_PlacementTestLocationController extends Zend_Controller_Action {
	
	private $_placementTestLocationDb;
	
	public function init(){
		$this->_placementLocationTypeDb = new App_Model_Application_DbTable_PlacementTestLocation();
	}
	
	public function indexAction() {
		//title
    	$this->view->title="Placement Test Location - Setup";
    	
    	//form
    	$form = new Application_Form_PlacementTestLocation();
    	
    	if ( $this->getRequest()->isPost() ){
    		
			$searchSql = $this->_placementLocationTypeDb->searchPaginate( $this->_request->getPost () ); 
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($searchSql));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
			
    	}else{
    	
	    	//paginator
			$dataList = $this->_placementLocationTypeDb->getPaginateData();
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($dataList));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
    	}
    	
    	$this->view->form = $form;
    	
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Placement Test Location - Add";
    	
    	$form = new Application_Form_PlacementTestLocation();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			
			if ($form->isValid($formData)) {
				
				//process form
				$auth = Zend_Auth::getInstance();
				$formData['al_update_by'] = $auth->getIdentity()->id;
				$formData['al_update_date'] = date('d/m/y h:ia');
				$formData['al_status'] = 1;
				
				$this->_placementLocationTypeDb->addData($formData);
				
				//redirect		
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test-location', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Placement Test Location - Edit";
    	
    	$form = new Application_Form_PlacementTestLocation(); 
    	$form->cancel->onClick = "window.location = '".$this->view->url(array('module'=>'application','controller'=>'placement-test-location', 'action'=>'index'),'default',true)."'; return false;";   	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
	    		$auth = Zend_Auth::getInstance();
				$formData['al_update_by'] = $auth->getIdentity()->id;
				$formData['al_update_date'] = date('y-m-d H:i:s');
				
				$this->_placementLocationTypeDb->updateData($formData,$id);
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test-location', 'action'=>'index'),'default',true));
				
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$form->populate($this->_placementLocationTypeDb->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction(){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$auth = Zend_Auth::getInstance(); 
    		$formData['al_update_by'] = $auth->getIdentity()->id;
			$formData['al_update_date'] = date('d/m/y h:ia');
			$formData['al_status'] = 0;
			
    		$this->_placementLocationTypeDb->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test-location', 'action'=>'index'),'default',true));
    	
    }
    
	public function detailAction() {
		//title
    	$this->view->title="Placement Test Location - Detail";
    	
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	$placementTestLocation = $this->_placementLocationTypeDb->getData($id);
    	$this->view->data = $placementTestLocation;
    	
    	//get venue
    	$placementTestRoomDb = new App_Model_Application_DbTable_PlacementTestRoom();
    	$this->view->roomList = $placementTestRoomDb->getLocationVenue($placementTestLocation['al_id']);
    	
    	$form = new Application_Form_PlacementTestRoom();
    	
    	$form->removeElement("save");
    	$form->removeElement("cancel");
    	
    	$this->view->form = $form;
   
	}
	
	public function addRoomAction(){
		
		$location_id = $this->_getParam('location_id', 0);
		$this->view->location_id = $location_id;
		
    	$form = new Application_Form_PlacementTestRoom();
    	
		if($this->_request->isXmlHttpRequest()){	
			$this->_helper->layout->disableLayout();
			
			$form->removeElement("save");
    		$form->removeElement("cancel");
		}
		
    	$placementTestRoomDb = new App_Model_Application_DbTable_PlacementTestRoom();
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
	    			    		
				//add room
	    		$auth = Zend_Auth::getInstance();
				$data = array(
					'av_location_code' => $location_id,
					'av_room_name' => $formData['av_room_name'],
					'av_room_name_short' => $formData['av_room_name_short'],
					'av_room_code' => $formData['av_room_code'],
					'av_building' => $formData['av_building'],
					'av_tutorial_capacity' => $formData['av_tutorial_capacity'],
					'av_exam_capacity' => $formData['av_exam_capacity'],
					'av_update_by' => $auth->getIdentity()->id,
					'av_update_date' => date('d/m/y h:ia'),
					'av_status' => 1,
					'av_seq' => $formData['av_seq'],
				);
				
				$room_id = $placementTestRoomDb->addData($data);
				
				//add room test type
				foreach ($formData['av_test_type'] as $testType){
						$data = array(
							'art_room_id'=>$room_id,
							'art_test_type'=>$testType
						);
						
						$roomTestTypeDb = new App_Model_Application_DbTable_PlacementTestRoomType();
						$roomTestTypeDb->addData($data);
				}
				
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test-location', 'action'=>'detail', 'id'=>$location_id),'default',true));
				
			}else{
				$form->populate($formData);	
			}
    	}
    	
    	$this->view->form = $form;
	}
	
	public function editRoomAction(){

		//title
    	$this->view->title="Placement Test Room - Edit";
    	
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	$placementTestRoomDb = new App_Model_Application_DbTable_PlacementTestRoom();
    	
    	
    	$form = new Application_Form_PlacementTestRoom();

		if($this->_request->isXmlHttpRequest()){	
			$this->_helper->layout->disableLayout();
			
			$form->removeElement("save");
    		$form->removeElement("cancel");
		}
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
	    		//update room
	    		$auth = Zend_Auth::getInstance();
				$data = array(
					'av_room_name' => $formData['av_room_name'],
					'av_room_name_short' => $formData['av_room_name_short'],
					'av_room_code' => $formData['av_room_code'],
					'av_building' => $formData['av_building'],
					'av_tutorial_capacity' => $formData['av_tutorial_capacity'],
					'av_exam_capacity' => $formData['av_exam_capacity'],
					'av_update_by' => $auth->getIdentity()->id,
					'av_update_date' => date('d/m/y h:ia'),
					'av_seq' => $formData['av_seq'],
				);
				
				$placementTestRoomDb->updateData($data,$id);
				
				//delete room test type
				$roomTestTypeDb = new App_Model_Application_DbTable_PlacementTestRoomType();
				$roomTestTypeDb->deleteRoomData($id);
				
				//add room test type
	    		foreach ($formData['av_test_type'] as $testType){
						$data = array(
							'art_room_id'=>$id,
							'art_test_type'=>$testType
						);
						
						$roomTestTypeDb = new App_Model_Application_DbTable_PlacementTestRoomType();
						$roomTestTypeDb->addData($data);
				}
				
				
				$data = $placementTestRoomDb->getData($id);
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test-location', 'action'=>'detail', 'id'=>$data['av_location_code']),'default',true));
				
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id!=0){
    			$data = $placementTestRoomDb->getData($id);
    			    			
    			$form->populate($data);
    			$this->view->form = $form;
    		}
    		
    	}
	}
	
	public function deleteRoomAction(){
		$id = $this->_getParam('id', 0);
    	
    	if($id!=0){
    		$auth = Zend_Auth::getInstance(); 
    		$formData['av_update_by'] = $auth->getIdentity()->id;
			$formData['av_update_date'] = date('d/m/y h:ia');
			$formData['av_status'] = 0;
			
			$placementTestRoomDb = new App_Model_Application_DbTable_PlacementTestRoom();
    		$placementTestRoomDb->deleteData($formData,$id);
    		
    		$data = $placementTestRoomDb->getData($id);
    		$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test-location', 'action'=>'detail', 'id'=>$data['av_location_code']),'default',true));
    	}

    	$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test-location', 'action'=>'index'),'default',true));
	}
}