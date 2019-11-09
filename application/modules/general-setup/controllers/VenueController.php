<?php
/**
 * Venue Controller for ICampus
 * 
 * @author Muhamad Alif
 * @version 1.0
 */

class GeneralSetup_VenueController extends Zend_Controller_Action {
	
	public function indexAction() {
		//title
    	$this->view->title="Exam Center Setup";
    	
    	//paginator
    	$venueDB = new App_Model_General_DbTable_Venue();
		
		$data = $venueDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($data));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add Exam Center";
    	
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	$form = new GeneralSetup_Form_Venue(array('url'=>$this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'index', 'id'=>$id),'default',true)));
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$venueDB = new App_Model_General_DbTable_Venue();
				$venueDB->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'index'),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
        
        
    }
    
	public function editAction(){
		//title
    	$this->view->title="Edit Exam Center";
    	
    	$id = $this->_getParam('id', 0);
    	
    	
    	$venueDB = new App_Model_General_DbTable_Venue();
    	$data = $venueDB->getData($id);
    	
    	$form = new GeneralSetup_Form_Venue(array('url'=>$this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'index'),'default',true)));
    	$this->view->form = $form;
    	
    	
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				//process form 
				$venueDB = new App_Model_General_DbTable_Venue();
				$venueDB->updateData($formData,$id);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'index'),'default',true)); 
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$form->populate($data);
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$venueDB = new App_Model_General_DbTable_Venue();
    		$data = $venueDB->getData($id);
    		
    		$venueDB->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'index'),'default',true));
    	
    }
    
    public function detailAction(){
    	$this->view->title="Venue Setup";
		$id = $this->_getParam('id', 0);
		$this->view->id = $id;
		
    	if($id!=0){
			$vanueDetailDB = new App_Model_General_DbTable_VenueDetail();
			
			$venuedetail = $vanueDetailDB->getPaginateData($id);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($venuedetail));
			$paginator->setItemCountPerPage(15);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
			
			$venueDB = new App_Model_General_DbTable_Venue();
			$this->view->venue = $venueDB->getData($id);
			
		}else{
			$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'index'),'default',true));
		}
    }
    
    
	public function addDetailAction()
    {
    	//title
    	$this->view->title="Add Venue";
    	
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	//venue
    	$venueDB = new App_Model_General_DbTable_Venue();
		$venueData = $venueDB->getData($id);
		$this->view->venue = $venueData;
    	
    	$form = new GeneralSetup_Form_VenueDetail(array('venueid'=>$id, 'url'=>$this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'detail', 'id'=>$id),'default',true)));
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$venueDetailDB = new App_Model_General_DbTable_VenueDetail();
				$venueDetailDB->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'detail', 'id'=>$id),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
        
        
    }
    
	public function editDetailAction(){
		//title
    	$this->view->title="Edit Venue";
    	
    	$id = $this->_getParam('id', 0);
    		
		//detail
    	$venueDetailDB = new App_Model_General_DbTable_VenueDetail();
    	$data = $venueDetailDB->getData($id);
    	
    	$form = new GeneralSetup_Form_VenueDetail(array('venueid'=>$data['venue_id'], 'url'=>$this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'detail', 'id'=>$data['venue_id']),'default',true)));
    	$this->view->form = $form;
    	
    	//venue
    	$venueDB = new App_Model_General_DbTable_Venue();
		$venueData = $venueDB->getData($data['venue_id']);
		$this->view->venue = $venueData;
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				//process form 
				$venueDetailDB = new App_Model_General_DbTable_VenueDetail();
				$venueDetailDB->updateData($formData,$id);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'detail', 'id'=>$venueData['id']),'default',true)); 
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$form->populate($data);
    		}
    		
    	}
    }
    
	public function deleteDetailAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$venueDetailDB = new App_Model_General_DbTable_VenueDetail();
    		$data = $venueDetailDB->getData($id);
    		
    		$venueDetailDB->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'detail', 'id'=>$data['venue_id']),'default',true));
    	
    }
	
	public function viewBranchAction(){
		$this->view->title="Venue Setup - Branch";
		$id = $this->_getParam('id', 0);
		$this->view->id = $id;
		
		
		
		if($id!=0){
			$branchDB = new App_Model_General_DbTable_Branch();
			$branch = $branchDB->getData($id);
			$this->view->branch = $branch;
			
			$venueDB = new App_Model_General_DbTable_Venue();
			$venue = $venueDB->getBranchVenue($id);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($venue));
			$paginator->setItemCountPerPage(15);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
			
		}else{
			$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'index'),'default',true));
		}
		
	}
	
	public function addBranchAction()
    {
    	//title
    	$this->view->title="Add Venue - Branch";
    	
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	$form = new GeneralSetup_Form_Venue(array('branchid'=>$id, 'url'=>$this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'view-branch', 'id'=>$id),'default',true)));
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$venueDB = new App_Model_General_DbTable_Venue();
				$venueDB->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'view-branch', 'id'=>$id),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
        
        
    }
    
	public function editBranchAction(){
		//title
    	$this->view->title="Edit Venue - Branch";
    	
    	$id = $this->_getParam('id', 0);
    	
    	
    	$venueDB = new App_Model_General_DbTable_Venue();
    	$data = $venueDB->getData($id);
    	$this->view->branch_id = $data['branch_id'];
    	
    	$form = new GeneralSetup_Form_Venue(array('branchid'=>$data['branch_id'], 'url'=>$this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'view-branch', 'id'=>$data['branch_id']),'default',true)));
    	$this->view->form = $form;
    	
    	
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				//process form 
				$venueDB = new App_Model_General_DbTable_Venue();
				$venueDB->updateData($formData,$id);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'view-branch', 'id'=>$data['branch_id']),'default',true)); 
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$form->populate($data);
    		}
    		
    	}
    }
    
	public function deleteBranchAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$venueDB = new App_Model_General_DbTable_Venue();
    		$data = $venueDB->getData($id);
    		
    		$venueDB->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'view-branch', 'id'=>$data['branch_id']),'default',true));
    	
    }
	
	
	
	public function viewOfficeAction(){
		$this->view->title="Venue Setup - Office";

		$id = $this->_getParam('id', 0);
		$this->view->id = $id;
		
		if($id!=0){
			$officeDB = new App_Model_General_DbTable_Office();
			$office = $officeDB->getData($id);
			$this->view->office = $office;
			
			$venueDB = new App_Model_General_DbTable_Venue();
			$venue = $venueDB->getOfficeVenue($id);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($venue));
			$paginator->setItemCountPerPage(15);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
			
		}else{
			$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'index'),'default',true));
		}
	}
	
	public function addOfficeAction()
    {
    	//title
    	$this->view->title="Add Venue - Office";
    	
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	$form = new GeneralSetup_Form_Venue(array('officeid'=>$id, 'url'=>$this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'view-office', 'id'=>$id),'default',true)));
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$venueDB = new App_Model_General_DbTable_Venue();
				$venueDB->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'view-office', 'id'=>$id),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
    }
    
	public function editOfficeAction(){
		//title
    	$this->view->title="Edit Venue - Office";
    	
    	$id = $this->_getParam('id', 0);
    	
    	
    	$venueDB = new App_Model_General_DbTable_Venue();
    	$data = $venueDB->getData($id);
    	$this->view->office_id = $data['office_id'];
    	
    	$form = new GeneralSetup_Form_Venue(array('officeid'=>$data['office_id'], 'url'=>$this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'view-office', 'id'=>$data['office_id']),'default',true)));
    	$this->view->form = $form;
    	
    	
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				//process form 
				$venueDB = new App_Model_General_DbTable_Venue();
				$venueDB->updateData($formData,$id);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'view-office', 'id'=>$data['office_id']),'default',true)); 
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$form->populate($data);
    		}
    		
    	}
    }
    
	public function deleteOfficeAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$venueDB = new App_Model_General_DbTable_Venue();
    		$data = $venueDB->getData($id);
    		
    		$venueDB->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'venue', 'action'=>'view-office', 'id'=>$data['office_id']),'default',true));
    	
    }

}

