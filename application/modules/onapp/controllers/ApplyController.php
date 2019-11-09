<?php
/**
 * @author Suliana
 */
require_once 'Zend/Controller/Action.php';

class onapp_applyController extends Zend_Controller_Action {
	
	public function indexAction() {
		
//		$this->_helper->layout->disableLayout();
		$this->_helper->layout->setLayout('default');
		//title
    	$this->view->title="Online Application - List";
    	$this->view->nat="1";
    	$this->view->lang="english";
    	
    	//paginator
//		$applyDBTable = new Onapp_Model_DbTable_Apply();
//		$applyData = $applyDBTable->getData();
//		
//		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($applyData));
//		$paginator->setItemCountPerPage(10);
//		$paginator->setCurrentPageNumber($this->_getParam('page',1));
//		
//		$this->view->paginator = $paginator;

	}
	
	public function addApplyAction(){
		//title
    	$this->view->title="Add Applicant Basic Info";
    	
    	$form = new Onapp_Form_Apply();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			//if ($form->isValid($formData)) {
				
				//process form 
				$IndexDbTable = new Onapp_Model_DbTable_Apply();
				$IndexDbTable->addData($formData);
				                           
				//redirect
				//$this->_redirect('/onapp/apply/');
//			}else{
//				$form->populate($formData);
//			}
        	
        }
    	
        $this->view->form = $form;
	}
	
}