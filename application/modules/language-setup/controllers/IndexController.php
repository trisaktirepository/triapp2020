<?php

class LanguageSetup_IndexController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $this->view->title = "Language Setup";
		
		$dataDB = new App_Model_System_DbTable_Language();
		
		$data_lang = $dataDB->getPaginateData();
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($data_lang));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
		
		$lang = new App_Model_System_DbTable_Language();
		$data_lang = $lang->getLang();
		
//		foreach( $data_lang as $key){
//		 echo "' ".$key["english"]."'=>'". $key["arabic"]."' ,";
//		}
//		

		
		
		
		
    }
    
    public function addAction()
    {
    	//title
    	$this->view->title="Add New Item";
    	
    	$form = new LanguageSetup_Form_Language();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$data = new App_Model_System_DbTable_Language();
				$data->addData($formData);
				
				//redirect	
				$this->_redirect($this->view->url(array('module'=>'language-setup','controller'=>'index', 'action'=>'index'),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
        $this->view->form = $form;
        
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Edit Item";
    	
    	$form = new LanguageSetup_Form_Language();
    	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$data = new App_Model_System_DbTable_Language(); 
				$data->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'language-setup','controller'=>'index', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$data = new App_Model_System_DbTable_Language();
    			$form->populate($data->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$data = new App_Model_System_DbTable_Language();
    		$data->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'language-setup','controller'=>'index', 'action'=>'index'),'default',true));
    	
    }


}

