<?php
/**
 * Setup_CompanyController
 * 
 * @author Muhamad Alif
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class Finance_AddfeeController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Additional Fee Setup";
    	
    	//paginator
		$typefee = new Finance_Model_DbTable_Typefee();
		$typefee = $typefee->getPaginatePromotion();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($typefee));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add Additional Fee";
    	

    	$form = new Finance_Form_Promotion();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();

				//process form 
				$program = new Finance_Model_DbTable_Typefee();
				$id = $program->addAdditional($formData);
				
				//redirect
					$this->_redirect('/finance/addfee/');		
					
        	
        }       
        $id = $this->_getParam('id', 0);
		$this->view->id = $id; 
		      
    }
	
	public function addFeeAction()
    {
    	//title
    	$this->view->title="Add  Fee";
    	
    	
        
    }
    
	public function editAction(){
		//title
    	$this->view->title="Edit Promotion";
    	
    	$form = new Finance_Form_Addfee();
    	//$form->submit->setLabel('Update');
    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
//    	echo $id;
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$program = new Finance_Model_DbTable_Typefee();
				$program->updateProgram($formData,$id);
				
				$this->_redirect('/finance/billfee/add-fee/id/'.$id);		
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$program = new Finance_Model_DbTable_Typefee();
    			$form->populate($program->getProgram($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$type = new Finance_Model_DbTable_Typefee();
    		$type->deleteProgram($id);
    	}
    		
    	$this->_redirect('/finance/addfee/');		
    	
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


