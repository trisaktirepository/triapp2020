<?php 

class Zoom_NotificationController extends Zend_Controller_Action {

	
	public function postAction() {
	
		$this->_helper->layout()->disableLayout();
		$dbZoom=new Zoom_Model_DbTable_Notification();
		if ($this->_request->isPost()) {
			$data = $this->_request->getParams();
			$dbZoom->add(array('json'=>$data['applicationId']));
		}
		$json = Zend_Json::encode(array('200'));
		echo $json;
		exit();
	}

	 public function meetingAction() {
	 
	 	 
		$this->_helper->layout->disableLayout(); 
		
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('view', 'html');
		$ajaxContext->initContext();
		
    	$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
	 	$dbZoom=new Zoom_Model_DbTable_Notification();
    	
    	if ($this->getRequest()->isPost()) {
    		$json_str=file_get_contents('php://input');
			$dbZoom->add(array('json'=>$json_str));
    		 
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
    	