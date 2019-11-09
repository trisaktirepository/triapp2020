<?php 
class GeneralSetup_LanguageController extends Zend_Controller_Action {

	public function indexAction() {
		//title
    	$this->view->title="Language Dictionary";
    	
    	//paginator
    	$form = new GeneralSetup_Form_Language();
    	$languageDb =  new GeneralSetup_Model_DbTable_Language();
		
		
		if ( $this->getRequest()->isPost() ){
    		
			$searchSql = $languageDb->searchPaginate( $this->_request->getPost () ); 
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($searchSql));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
			
    	}else{		
    		$dataList = $languageDb->getPaginateData();
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($dataList));
			$paginator->setItemCountPerPage(220);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
    	}
		$this->view->form = $form;
	}
	
	public function addAction(){
			//title
 //title
    	$this->view->title="Language - Add";
    	
    	$form = new GeneralSetup_Form_Language();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$languageDb = new GeneralSetup_Model_DbTable_Language();
				$languageDb->addData($formData);
				
				//redirect		
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'language', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
	}
	public function editAction(){
    	//title
    	$this->view->title="Language - Edit";
    	
    	$form = new GeneralSetup_Form_Language();
    	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$languageDb = new GeneralSetup_Model_DbTable_Language(); 
				$languageDb->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'language', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$languageDb = new GeneralSetup_Model_DbTable_Language();
    			$form->populate($languageDb->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		
    		//delete placement test head
    		$languageDb = new GeneralSetup_Model_DbTable_Language();
    		$languageDb->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'language', 'action'=>'index'),'default',true));
    	
    }    
}
?>