<?php
require_once 'Zend/Controller/Action.php';

class Discipline_PenaltyController extends Zend_Controller_Action {	
		
	public function indexAction() {
		
		$this->view->title="Penalty Lists";    	

		//paginator
		$oPenalty = new App_Model_Discipline_DbTable_Penalty();
		$penalty_list = $oPenalty->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($penalty_list));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
        
	}
	
	public function addPenaltyAction()
    {
    	//title
    	$this->view->title="Add Penalty";
    	
    	$form = new Discipline_Form_Penalty();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$oPenalty = new App_Model_Discipline_DbTable_Penalty();
				
				$info["penalty_name"]=$formData["penalty_name"];
				$info["penalty_code"]=$formData["penalty_code"];
				$info["description"]=$formData["description"];
				$oPenalty->addData($info);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'discipline','controller'=>'penalty', 'action'=>'index'),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
        
        
    }
    
    
    public function editPenaltyAction(){
		//title
    	$this->view->title="Edit Penalty";
    	
    	$form = new Discipline_Form_Penalty();
    
    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$oPenalty = new App_Model_Discipline_DbTable_Penalty();
				$info["penalty_name"]=$formData["penalty_name"];
				$info["penalty_code"]=$formData["penalty_code"];
				$info["description"]=$formData["description"];
				$oPenalty->updateData($info,$id);
				
				$this->_redirect($this->view->url(array('module'=>'discipline','controller'=>'penalty', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$oPenalty = new App_Model_Discipline_DbTable_Penalty();
    			$form->populate($oPenalty->getData($id));
    		}
    		
    	}
    }
    
	public function deletePenaltyAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$oPenalty = new App_Model_Discipline_DbTable_Penalty();
    		$oPenalty->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'discipline','controller'=>'penalty', 'action'=>'index'),'default',true));
    	
    }
	
}
		
?>