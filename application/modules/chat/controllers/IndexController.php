<?php

class Chat_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }


    public function indexAction()
    {
         
         

    }
    
    public function userChatAction()
    {
    	$this->_helper->layout()->disableLayout();
      $transid=$this->_getParam('transaction_id');
      $this->view->transactionid=$transid;
    	
    
    }
    
    public function getChatAction()
    {
    	$id=$this->_getParam('id');
    	$this->view->transaction_id=$id;
    	$this->_helper->layout()->disableLayout();
    	$dbChat=new Chat_Model_DbTable_Chat();
    	$dbTransaction=new App_Model_Application_DbTable_ApplicantTransaction();
    	$data=$dbChat->getDataChat($id);
    	foreach ($data as $key=>$value) {
    		$appl=$dbTransaction->getDataById($value['chat_by']);
    		if ($appl) $data[$key]['noform']=$appl['at_pes_id'];
    		else $data[$key]['noform']='Admin';
    	}
    	//echo var_dump($data);exit;
    	$this->view->data=$data;
    	 
    
    }
    
    public function adminChatAction()
    {
    	 
    	
    
    }
    public function ajaxAdminChatAction()
    {
    	$dbChat=new Chat_Model_DbTable_Chat();
    	$this->_helper->layout()->disableLayout();
    	$auth = Zend_Auth::getInstance();
    	$quest=array();
    	if ($this->getRequest()->isPost()) {
    		 
    		$formData = $this->getRequest()->getPost();
    		$row=$dbChat->getDataByUser($formData['trxid']);
    	}
    	
    	
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    		
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    	
    	$json = Zend_Json::encode($row);
    	
    	echo $json;
    	exit();
    	
    	//}
    	
    	
    }
    
    
    public function ajaxGetDestinationAction()
    {
    	$dbChat=new Chat_Model_DbTable_Chat();
    	$this->_helper->layout()->disableLayout();
    	$auth = Zend_Auth::getInstance();
    	$quest=array();
    	$row=$dbChat->getDataDest();
    	 
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    	 
    	$json = Zend_Json::encode($row);
    	 
    	echo $json;
    	exit();
    	 
    	//}
       
       
    }
    
    public function ajaxSaveChatAction()
    {
    	$dbChat=new Chat_Model_DbTable_Chat();
    	$this->_helper->layout()->disableLayout();
    	$auth = Zend_Auth::getInstance();
    	 
    	if ($this->getRequest()->isPost()) {
    		 
    		$formData = $this->getRequest()->getPost();
    		 
    		$row=$dbChat->add(array('at_trans_id'=>$formData['trxid'],
    				'chat_msg'=>$formData['text'],
    				'chat_by'=>$formData['trxid'],
    				'created_dt'=>date('Y-m-d H:i:s')
    		));
    		 
    	}
    	
     
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    	 
    	$json = Zend_Json::encode($row);
    	 
    	echo $json;
    	exit();
    	 
    	//}
       
       
    }
}

