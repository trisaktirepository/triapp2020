<?php
/**
 * @author Muhamad Alif
 * @version 1.0
 */

class GeneralSetup_HighschoolDisciplineController extends Zend_Controller_Action {
	
	private $_DbObj;
	
	public function init(){
		$this->_DbObj = new GeneralSetup_Model_DbTable_SchoolDiscipline();
	}
	
	public function indexAction() {
		//title
    	$this->view->title= $this->view->translate("Discipline Set-up");
    	
    	$this->view->searchForm = new GeneralSetup_Form_SchoolDiscipline();
    	
    	$schoolDisciplineDb = $this->_DbObj;
    	
    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			//paginator
			$data = $schoolDisciplineDb->getPaginateData($formData);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($data));
			$paginator->setItemCountPerPage(PAGINATION_SIZE);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
		
    	}else{
    		//paginator
			$data = $schoolDisciplineDb->getPaginateData();
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($data));
			$paginator->setItemCountPerPage(PAGINATION_SIZE);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;	
    	}
    	
	}
	
	public function detailAction(){
    	$this->view->title= $this->view->translate("Discipline Set-up")." - ".$this->view->translate("Detail");
    	
		$code = $this->_getParam('code', null);
		$this->view->code = $code;
		
    	if($code){
    		$this->view->discipline = $this->_DbObj->getData($code);
    					
		}else{
			$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'highschool-discipline', 'action'=>'index'),'default',true));
		}
    }
	
	public function addAction()
    {
    	//title
    	$this->view->title= $this->view->translate("Discipline Set-up")." - ".$this->view->translate("Add");
    	    	
    	$form = new GeneralSetup_Form_SchoolDiscipline();
    	
    	$form->cancel->onClick = "window.location ='".$this->view->url(array('module'=>'general-setup','controller'=>'highschool-discipline', 'action'=>'index'),'default',true)."'; return false;";
    	    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			if ($form->isValid($formData)) {
				
				$code = $this->_DbObj->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'highschool-discipline', 'action'=>'detail', 'code'=>$code),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
    }
    
	public function editAction(){
		$code = $this->_getParam('code', null);
		
		//title
    	$this->view->title= $this->view->translate("Discipline Set-up")." - ".$this->view->translate("Edit");
    	
    	$form = new GeneralSetup_Form_SchoolDiscipline();
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$this->_DbObj->updateData($formData,$code);
				
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'highschool-discipline', 'action'=>'detail', 'code'=>$formData['smd_code']),'default',true)); 
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($code!=null){
    			
    			$form->populate($this->_DbObj->getData($code));
    		}
    	}
    	
    	$this->view->form = $form;
    }
    
	public function deleteAction($code = null){
    	$code = $this->_getParam('code', null);
    	
    	if($code!=null){
    		$this->_DbObj->deleteData($code);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'highschool-discipline', 'action'=>'index'),'default',true));
    	
    }
}

