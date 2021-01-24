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
    		$data = $this->getRequest()->getPost();
			$dbZoom->add(array('json'=>implode(';',$data)));
    		 
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
    	