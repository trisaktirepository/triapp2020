<?php
/**
 * @author Muhamad Alif
 * @version 1.0
 */

class GeneralSetup_HighschoolSubjectController extends Zend_Controller_Action {
	
	private $_DbObj;
	
	public function init(){
		$this->_DbObj = new GeneralSetup_Model_DbTable_SchoolSubject();
	}
	
	public function indexAction() {
		//title
    	$this->view->title= $this->view->translate("Subject Set-up");
    	
    	$this->view->searchForm = new GeneralSetup_Form_SchoolSubject();
    	
    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			//paginator
			$data = $this->_DbObj->getPaginateData($formData);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($data));
			$paginator->setItemCountPerPage(PAGINATION_SIZE);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
		
    	}else{
    		//paginator
			$data = $this->_DbObj->getPaginateData();
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($data));
			$paginator->setItemCountPerPage(PAGINATION_SIZE);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;	
    	}
    	
	}
	
	public function detailAction(){
    	$this->view->title= $this->view->translate("Subject Set-up")." - ".$this->view->translate("Detail");
    	
		$id = $this->_getParam('id', null);
		$this->view->id = $id;
		
    	if($id){  		
    		//subject data
    		$this->view->subject = $this->_DbObj->getData($id);
    					
		}else{
			$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'highschool-subject', 'action'=>'index'),'default',true));
		}
    }
	
	public function addAction()
    {
    	//title
    	$this->view->title= $this->view->translate("Subject Set-up")." - ".$this->view->translate("Add");
    	    	
    	$form = new GeneralSetup_Form_SchoolSubject();
    	
    	$form->cancel->onClick = "window.location ='".$this->view->url(array('module'=>'general-setup','controller'=>'highschool-subject', 'action'=>'index'),'default',true)."'; return false;";
    	    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			if ($form->isValid($formData)) {
				
				$id = $this->_DbObj->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'highschool-subject', 'action'=>'detail', 'id'=>$id),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
    }
    
	public function editAction(){
		$id = $this->_getParam('id', 0);
		
		//title
    	$this->view->title= $this->view->translate("Subject Set-up")." - ".$this->view->translate("Edit");
    	
    	$form = new GeneralSetup_Form_SchoolSubject();
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$this->_DbObj->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'highschool-subject', 'action'=>'detail', 'id'=>$id),'default',true)); 
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			
    			$form->populate($this->_DbObj->getData($id));
    		}
    	}
    	
    	$this->view->form = $form;
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$db = new GeneralSetup_Model_DbTable_SchoolSubject();
    		$db->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'highschool-subject', 'action'=>'index'),'default',true));
    	
    }
}

