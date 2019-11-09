<?php
/**
 * Setup_CompanyController
 * 
 * @author Muhamad Alif
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class Finance_PromotionController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Promotion Type Setup";
    	
    	//paginator
		$promote = new Finance_Model_DbTable_Promotion();
		$promote = $promote->getPaginatePromotion();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($promote));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add Promotion Type";
    	
    	$form = new Finance_Form_Promotion();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			//if ($form->isValid($formData)) {
				
				//process form 
				$program = new Finance_Model_DbTable_Promotion();
				$program->addPromotion($formData);
				
				//redirect
				$this->_redirect('/finance/promotion/');		
//			}else{
//				$form->populate($formData);
//			}
        	
        }  
    	
       // $this->view->form = $form;
        
        
    }
    
	public function editAction(){
		//title
    	$this->view->title="Edit Program";
    	
    	$form = new Finance_Form_Promotion();
    	//$form->submit->setLabel('Update');
    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$program = new Finance_Model_DbTable_Promotion(); 
				$program->updateProgram($formData,$id);
				
				$this->_redirect('/finance/promotion/');
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$program = new Finance_Model_DbTable_Promotion();
    			$form->populate($program->getProgram($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$program = new Setup_Model_DbTable_Placementtest();
    		$program->deleteProgram($id);
    	}
    		
    	$this->_redirect('/setup/placementtest/');
    	
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
            
	  	
	  	
	  	// $programDB = new Setup_Model_DbTable_Placementtest();
		// $program_data = $programDB->getProgramMaster($id);
		
		
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($program_data);
		
		$this->view->json = $json;

    }
}


