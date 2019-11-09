<?php

/**
 * AuditController
 * 
 * @author Muhamad Alif Muhammad
 * @date Feb 23, 2011
 * @version 
 */
	

class SystemSetup_AuditController extends Zend_Controller_Action
{
	/**
	 * The default action - show the home page
	 */
    public function indexAction() 
    {
        $this->view->title = "Audit Trail";
        
    	$auditDB = new SystemSetup_Model_DbTable_Audit();
    	
    	$logs = $auditDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($logs));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
		
		
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$msg = $this->_flashMessenger->getMessages();
		if($msg!=null){
			$this->view->noticeMessage = $msg[0];
		}
    }
    
}

