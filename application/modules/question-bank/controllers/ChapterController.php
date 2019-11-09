<?php

class QuestionBank_ChapterController extends Zend_Controller_Action {
	
	public function indexAction() {
		//title
    	$this->view->title="Question Pool";
    	
    	//paginator
		$poolDB = new App_Model_Question_DbTable_Chapter();
		$poolData = $poolDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($poolData));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add New Question Pool";
    	
    	$form = new QuestionBank_Form_Chapter();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				$date = date('Y-m-d H:i:s');
				
				$auth = Zend_Auth::getInstance();
				$idUpd = $auth->getIdentity()->id;
				
				$data = array(
					'name' => $formData['name'],
					'idPool' => $formData['idPool'],
//					'status' => $formData['status'],
					'dateUpd' => $date,
					'updUser' =>$idUpd
				);
				
				
				//process form 
				$pool = new App_Model_Question_DbTable_Chapter();
				$pool->addData($data);
				
				//redirect	
				$this->_redirect($this->view->url(array('module'=>'question-bank','controller'=>'chapter', 'action'=>'index'),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
        $this->view->form = $form;
        
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Edit Company";
    	
    	$form = new QuestionBank_Form_Chapter();
    	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
	    		
	    		$date = date('Y-m-d H:i:s');
				
				$auth = Zend_Auth::getInstance();
				$idUpd = $auth->getIdentity()->id;
				
	    		$data = array(
					'name' => $formData['name'],
					'idPool' => $formData['idPool'],
//					'status' => $formData['status'],
					'dateUpd' => $date,
					'updUser' =>$idUpd
				);
				
				
				$pool = new App_Model_Question_DbTable_Chapter(); 
				$pool->updateData($data,$id);
				
				$this->_redirect($this->view->url(array('module'=>'question-bank','controller'=>'chapter', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$pool = new App_Model_Question_DbTable_Chapter();
    			$form->populate($pool->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$pool = new App_Model_Question_DbTable_Chapter();
    		$pool->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'question-bank','controller'=>'chapter', 'action'=>'index'),'default',true));
    	
    }

}
