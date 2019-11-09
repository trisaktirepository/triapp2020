<?php
/**
 * Setup_CompanyController
 * 
 * @author Muhamad Alif
 * @version 
 */

class GeneralSetup_StateController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Governorate Setup";
    	
    	//paginator
		$stateDB = new App_Model_General_DbTable_State();
		$state = $stateDB->getStateArray();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($state));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add Governorate";
    	
    	$form = new GeneralSetup_Form_State();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$stateDB = new App_Model_General_DbTable_State();
				$stateDB->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'state', 'action'=>'index'),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
        
        
    }
    
	public function editAction(){
		//title
    	$this->view->title="Edit Governorate";
    	
    	$form = new GeneralSetup_Form_State();
    	//$form->submit->setLabel('Update');
    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$stateDB = new App_Model_General_DbTable_State(); 
				$stateDB->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'state', 'action'=>'index'),'default',true)); 
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$stateDB = new App_Model_General_DbTable_State();
    			$form->populate($stateDB->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$state = new App_Model_General_DbTable_State();
    		$state->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'state', 'action'=>'index'),'default',true));
    	
    }
    
    public function viewAction(){
    	$this->view->title = "Governorate's Disctricts Setup";
    	$id = $this->_getParam('id', 0);
    	
    	$stateDB = new App_Model_General_DbTable_State();
    	$state = $stateDB->getData($id);
    	
    	$this->view->state = $state;
    	
    	//paginator
		$districtDB = new App_Model_General_DbTable_District();
		$district = $districtDB->getPaginateData($id);
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($district));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
    }
    
	public function addDistrictAction()
    {
    	$id = $this->_getParam('id', 0);
    	$this->view->state_id = $id;
    	
    	//title
    	$this->view->title="Add Governorate's Disctrict";
    	
    	$form = new GeneralSetup_Form_Disctrict(array('stateID'=>$id));
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$disctrictDB = new App_Model_General_DbTable_District();
				$disctrictDB->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'state', 'action'=>'view', 'id'=>$id),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
        
        
    }
    
	public function editDistrictAction()
    {
    	$id = $this->_getParam('id', 0);
    	    	
    	//title
    	$this->view->title="Edit Governorate's Disctrict";
    	
    	$form = new GeneralSetup_Form_Disctrict();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form
				$disctrictDB = new App_Model_General_DbTable_District();
				$disctrictDB->updateData($formData,$id);
								
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'state', 'action'=>'view', 'id'=>$formData['state_id']),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }else{
    		if($id>0){
    			$districtDB = new App_Model_General_DbTable_District();
    			$data = $districtDB->getData($id);
    			$this->view->state_id =$data['state_id']; 
    			
    			$form = new GeneralSetup_Form_Disctrict(array('stateID'=>$data['state_id']));
    			$form->populate($data);
    		}
    	}
    	
        $this->view->form = $form;   
    }
    
	public function deleteDistrictAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$districtDB = new App_Model_General_DbTable_District();
    		$data = $districtDB->getData($id);
    		$districtDB->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'state', 'action'=>'view','id'=>$data['state_id']),'default',true));
    	
    }
}

