<?php

class QuestionBank_TosController extends Zend_Controller_Action {
	
	public function listBankAction() {
		//title
    	$this->view->title="List of Bank";
    	
    	//paginator
		$poolDB = new App_Model_Question_DbTable_Pool();
		$poolData = $poolDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($poolData));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	
	public function listTosAction(){
		$this->view->title="List of TOS";
		
		$pool_id = $this->_getParam('pool_id', 0);
		$this->view->pool_id = $pool_id;
		
		//paginator
		$tosDB = new App_Model_Tos_DbTable_Tos();
		$tosData = $tosDB->getPaginateTosByPool($pool_id);
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($tosData));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;	
	}
	
	
	
	public function setupTosAction(){
		
		$this->view->title="TOS Setup";
		
		
		
		if ($this->getRequest()->isPost()) {
			
			$formdata = $this->getRequest()->getPost();
			
			$auth     = Zend_Auth::getInstance();
			$userid   = $auth->getIdentity()->id;
				
				
				$data["pool_id"]  = $formdata["pool_id"];
				$data["tosname"]  = $formdata["tosname"];
				$data["status"]   = 2;
				$data["createddt"] = date('Y-m-d H:i:s');
			    $data["createdby"] = $userid;
			    
			    //print_r($data);

			    //add to tos
			    $tosDb =  new App_Model_Tos_DbTable_Tos();
			    $tos_id = $tosDb->addData($data);
				
			    $total_chapter  = $formdata["total_topic"];
			    
				//for each pool ada berapa chapter
				for($c=0; $c<$total_chapter; $c++){					
					
					$info["tos_id"]     = $tos_id;
					$info["topic_id"]   = $formdata["topic_id"][$c];
					
					for($l=1; $l<=3; $l++){
						
						$info["difficulty_level"] = $l;
						$info["NoOfQuestion"]    = $formdata["require"][$info["topic_id"]][$l];
						
						//print_r($info);
						$tosDb->addDetails($info);
					}					
				}
				
			
	    	$this->view->noticeSuccess = "Data has been saved successfully.";	
	    		
			
			$this->_redirect($this->view->url(array('module'=>'question-bank','controller'=>'tos', 'action'=>'edit-tos','tid'=>$tos_id,'pool_id'=>$data["pool_id"]),'default',true));	
			
			
		}else{
		
			$pool_id = $this->_getParam('pool_id', 0);
			$this->view->pool_id = $pool_id;
			
			$tos_id = $this->_getParam('tid', 0);
			$this->view->tos_id = $tos_id;
			
			//get Pool/Bank Info
			$poolDB = new App_Model_Question_DbTable_Pool();
	    	$poolData = $poolDB->getData($pool_id);
	    	$this->view->pool = $poolData;   
	    	
	    	//get chapter for each bank
		 	$chapterDB = new App_Model_Question_DbTable_Chapter();
		 	$chapter = $chapterDB->getTopicbyPool($pool_id);
		 	$this->view->chapter = $chapter;
		 	
		 	//if dah ada cek
		    $tosDb =  new App_Model_Tos_DbTable_Tos();
			$tos = $tosDb->getData($tos_id);
			$this->view->tos = $tos;
		}
		 
	}
	
	
	public function editTosAction(){
		
		$this->view->title="Edit TOS";
		
		
		
		if ($this->getRequest()->isPost()) {
			
			$formdata = $this->getRequest()->getPost();
			
			$auth     = Zend_Auth::getInstance();
			$userid   = $auth->getIdentity()->id;
				
				
				$data["tosname"]  = $formdata["tosname"];			
				$data["modifydt"] = date('Y-m-d H:i:s');
			    $data["modifyby"] = $userid;
			    
			    //print_r($data);

			    //add to tos
			    $tosDb =  new App_Model_Tos_DbTable_Tos();
			    $tosDb->updateData($data,$formdata["tos_id"]);
				
			    $total_chapter  = $formdata["total_topic"];
			    
				//for each pool ada berapa chapter
				for($c=0; $c<$total_chapter; $c++){	
										
					$topic_id = $formdata["topic_id"][$c];
					
					for($l=1; $l<=3; $l++){						
					
						$info["NoOfQuestion"]    = $formdata["require"][$topic_id][$l];						
						$id       = $formdata["tos_details_id"][$topic_id][$l];
						
						//print_r($info);
						$tosDb->updateDetails($info,$id);
					}					
				}
				
			$this->view->noticeSuccess = "Data has been saved successfully.";	
		
			$this->_redirect($this->view->url(array('module'=>'question-bank','controller'=>'tos', 'action'=>'edit-tos','tid'=>$formdata["tos_id"],'pool_id'=>$formdata["pool_id"]),'default',true));	
			
			
		}else{
		
			$pool_id = $this->_getParam('pool_id', 0);
			$this->view->pool_id = $pool_id;
			
			$tos_id = $this->_getParam('tid', 0);
			$this->view->tos_id = $tos_id;
			
			//get Pool/Bank Info
			$poolDB = new App_Model_Question_DbTable_Pool();
	    	$poolData = $poolDB->getData($pool_id);
	    	$this->view->pool = $poolData;   
	    	
	    	//get chapter for each bank
		 	$chapterDB = new App_Model_Question_DbTable_Chapter();
		 	$chapter = $chapterDB->getTopicbyPool($pool_id);
		 	$this->view->chapter = $chapter;
		 	
		 	//if dah ada cek
		    $tosDb =  new App_Model_Tos_DbTable_Tos();
			$tos = $tosDb->getData($tos_id);
			$this->view->tos = $tos;
			
			
		}
		 
	}
	
	
	public function changeStatusAction(){		
		   
			//update other tos under thus bank to inactive
			$tosDb =  new App_Model_Tos_DbTable_Tos();
			$data["status"] = 2;
			$pool_id = $this->_getParam('pool_id', 0);
			$tosDb->updateStatus($data,$pool_id);
			
			//update tos status
			$tos_id = $this->_getParam('tos_id', 0);
			$info["status"] = $this->_getParam('val', 0);
			$tosDb->updateData($info,$tos_id);
			
			$this->_redirect($this->view->url(array('module'=>'question-bank','controller'=>'tos', 'action'=>'list-tos','pool_id'=>$pool_id),'default',true));	
	}
	
	
	 public function delTosAction($id = null){
	 	
    	$id = $this->_getParam('tid', 0);
    	
    	if($id>0){
    		$tosDB = new App_Model_Tos_DbTable_Tos();
    		$tosDB->deleteData($id);
    		$tosDB->deleteDetails($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'question-bank','controller'=>'tos', 'action'=>'list-tos')));
    	
    }
	
		
	
}
