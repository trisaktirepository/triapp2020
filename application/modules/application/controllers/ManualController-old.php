<?
class Application_ManualController extends Zend_Controller_Action
{

public function indexAction(){
	
		$this->view->title = "Online Application (Manual Entry)";
				
		$studentDB = new Application_Model_DbTable_Manual();
		$student_data = $studentDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($student_data));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
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
				$manual = new Application_Model_DbTable_Manual();
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
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$manual = new Application_Model_DbTable_Manual(); 
				$manual->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'manual', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$manual = new Application_Model_DbTable_Manual();
    			$form->populate($manual->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$manual = new Application_Model_DbTable_Manual();
    		$manual->deleteData($id);
    	}
    	
    	$this->_redirect($this->view->url(array('module'=>'application','controller'=>'manual', 'action'=>'index'),'default',true));
    	
    }
}
?>