<?php
/**
 * Setup_CompanyController
 * 
 * @author Muhamad Alif
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class Onapp_PlacementtestController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Placement Test Setup";
    	
    	//paginator
		$placement = new Onapp_Model_DbTable_Placementtest();
		$placement = $placement->getPaginatePlacementtest();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($placement));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add Placement Test";
    	
    	//$form = new Setup_Form_Placementtest();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			//if ($form->isValid($formData)) {
				
				//process form 
				$program = new Onapp_Model_DbTable_Placementtest();
				$program->addPlacementtest($formData);
				
				//redirect
				$this->_redirect('/onapp/placementtest/');		
//			}else{
//				$form->populate($formData);
//			}
        	
        }  
    	
       // $this->view->form = $form;
        
        
    }
    
	public function editAction(){
		//title
    	$this->view->title="Edit Program";
    	
    	$form = new Setup_Form_Placementtest();
    	//$form->submit->setLabel('Update');
    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$program = new Onapp_Model_DbTable_Placementtest(); 
				$program->updateProgram($formData,$id);
				
				$this->_redirect('/setup/placementtest/');
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$program = new Onapp_Model_DbTable_Placementtest();
    			$form->populate($program->getProgram($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$program = new Onapp_Model_DbTable_Placementtest();
    		$program->deleteProgram($id);
    	}
    		
    	$this->_redirect('/onapp/placementtest/');
    	
    }
    
	public function ajaxGetProgramAction($id=null){
    	$id = $this->_getParam('id', 0);
    	
    	// check is AJAX request or not
     	/*if (!$this->getRequest() -> isXmlHttpRequest()) {
        	$this->getResponse() -> setHttpResponseCode(404)
                              -> sendHeaders();
         	$this->renderScript('empty.phtml');
         	return false;
     	}*/
    	
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	
	  	
	  	// $programDB = new Onapp_Model_DbTable_Placementtest();
		// $program_data = $programDB->getProgramMaster($id);
		
		
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($program_data);
		
		$this->view->json = $json;

    }
}


