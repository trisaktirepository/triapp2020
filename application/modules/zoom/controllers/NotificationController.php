<?php 

class Zoom_NotificationController extends Zend_Controller_Action {


	public function meetingAction() {
		
		$this->_helper->layout->setLayout('application');
		 
    	$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
	 	$dbZoom=new Zoom_Model_DbTable_Notification();
    	
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		$dbZoom->add(var_dump($formData));
    	}
	}
}
?>
    	