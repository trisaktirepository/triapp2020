<?
class Application_Manual2Controller extends Zend_Controller_Action
{

public function indexAction(){
	
		$this->view->title = "Online Application (Manual Entry)";
				
		$studentDB = new App_Model_Application_DbTable_Manual();
		
	
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$student_data = $studentDB->search($formData);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($student_data));
			$paginator->setItemCountPerPage(100);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
		}else{
			$student_data = $studentDB->getPaginateData();
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($student_data));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
		}
		
		$this->view->paginator = $paginator;
		
    	
	} 
	
public function listAction(){
	
		$this->view->title = "Online Application (Student Application)";
				
		$studentDB = new App_Model_Application_DbTable_Manual();
		
	
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$student_data = $studentDB->searchList($formData);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($student_data));
			$paginator->setItemCountPerPage(100);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
		}else{
			$student_data = $studentDB->getPaginateData(1);
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($student_data));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
		}
		
		$this->view->paginator = $paginator;
		
    	
	} 
	
public function addAction()
    {
    	//title
    	$this->view->title="Add New Applicant";
    	
    	$form = new Application_Form_Manual();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$manual = new App_Model_Application_DbTable_Manual();
				$manual->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'manual', 'action'=>'index'),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Edit Award";
    	
    	$form = new Application_Form_Manual();
    	    	
    	$this->view->form = $form;
    	
    	echo $id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				echo " masuk";
				$manual = new App_Model_Application_DbTable_Manual(); 
				$manual->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'manual', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$manual = new App_Model_Application_DbTable_Manual();
    			$form->populate($manual->getData($id));
    		}
    		
    	}
    }
    
    public function offerAction(){
    	//title
    	$this->view->title="Offer Program";
    	
    	$form = new Application_Form_Manual();
    	    	
    	$this->view->form = $form;
    	
    	echo $id = $this->_getParam('id', 0);
    	
    	$manual = new App_Model_Application_DbTable_Manual(); 
		$applicant = $manual->getData($id);
		$this->view->applicant = $applicant;
		
		$appliedDB = new App_Model_Application_DbTable_AppliedProgram();
		$applied= $appliedDB->getAppliedProgram($id);
		$this->view->applied = $applied;
//		
//    	if ($this->getRequest()->isPost()) {
//    		
//    		$formData = $this->getRequest()->getPost();
//    		
//	    	if ($form->isValid($formData)) {
//				
//				$manual = new App_Model_Application_DbTable_Manual(); 
//				$manual->updateData($formData,$id);
//				
//				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'manual', 'action'=>'index'),'default',true));
//			}else{
//				$form->populate($formData);	
//			}
//    	}else{
//    		if($id>0){
//    			$manual = new App_Model_Application_DbTable_Manual();
//    			$form->populate($manual->getData($id));
//    		}
//    		
//    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$manual = new App_Model_Application_DbTable_Manual();
    		$manual->deleteData($id);
    	}
    	
    	$this->_redirect($this->view->url(array('module'=>'application','controller'=>'manual', 'action'=>'index'),'default',true));
    	
    }
}
?>