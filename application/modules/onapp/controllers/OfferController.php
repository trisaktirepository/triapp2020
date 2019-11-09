<?php
/**
 * @author Suliana
 */

require_once 'Zend/Controller/Action.php';

class onapp_offerController extends Zend_Controller_Action {
	
	public function indexAction() {
		
		//title
    	$this->view->title="Offer Letter Template";

    	//paginator
		$offer = new Onapp_Model_DbTable_Offer();
		$offer = $offer->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($offer));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;

	}
	
	public function addAction(){
		//title
    	$this->view->title="Add Offer Letter Item";
    	
    	$form = new Onapp_Form_Apply();
    	
    	$app_id = $this->_getParam('id', 0);
		$this->view->id = $app_id; 
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
				
				//process form 
				$IndexDbTable = new Onapp_Model_DbTable_Offer();
				$id = $IndexDbTable->addData($formData);
				
			$this->_redirect('/onapp/offer/index/');	
			
        }
    	
        $this->view->form = $form;
        $id = $this->_getParam('id', 0);
        $this->view->id = $id;
	}
	
	public function editAction(){
		
    	//title
    	$this->view->title="Additional Info";
    	    	
    	$form = new Onapp_Form_Apply();
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
				
				$IndexDbTable = new Onapp_Model_DbTable_Offer();
				$IndexDbTable->updateData($formData,$id);
				
				$this->_redirect('/onapp/offer/');	
    	}
    	$this->view->form = $form;
        $this->view->id = $id;
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$IndexDbTable = new Onapp_Model_DbTable_Offer();
    		$IndexDbTable->deleteData($id);
    	}
    		
    	$this->_redirect('/onapp/offer/');
    	
    }
    
    public function offerLetterAction(){
    	$this->_helper->layout->setLayout('onapp');
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    }
	
}