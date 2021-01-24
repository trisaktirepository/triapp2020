<?php 

class Zoom_NotificationController extends Zend_Controller_Action {


	public function meetingAction() {
		
		$this->_helper->layout->setLayout('application');

		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->_helper->layout->disableLayout();
		}
		
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('view', 'html');
		$ajaxContext->initContext();
		
    	$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
	 	$dbZoom=new Zoom_Model_DbTable_Notification();
    	
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		$dbZoom->add(var_dump($formData));
    		 
    	}
    	
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    	
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    	
    	$json = Zend_Json::encode(array('200'));
    	
    	echo $json;
    	exit();
    	 
	}
}
?>
    	