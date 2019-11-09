<?php
require_once 'Zend/Controller/Action.php';

class Exam_ComponentController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
 
        //title
    	$this->view->title="Component Name Setup";
    	
    	//paginator
		$componentDB = new App_Model_Exam_DbTable_Component();
		$component   = $componentDB->getPaginateData();
		
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($component));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
    
    }
    
    
    
    public function addformAction(){    	
    	//title
    	$this->view->title="Setup Component Name : Add";
    }
    
    public function addAction(){
    	$auth = Zend_Auth::getInstance();     	
    	
	 	if ($this->_request->isPost()) {   
			$formData = $this->getRequest()->getPost();		
		
			$iteration  		         = $formData["iteration"];			
		    $data["component_name"]      = $formData["component_name"];
		   	$data["createdby"]           = $auth->getIdentity()->id;
		   	$data["createddt"]           = date("Y-m-d H:i:s");
		    
		    $componentDB = new App_Model_Exam_DbTable_Component();
			$parent_id = $componentDB->addData($data);
						 
			
			 for($i=1; $i<=$iteration; $i++){	
			 	
				$info["component_name"] = $formData["component_item_name".$i];
				$info["parent_id"]      = $parent_id;
				$info["createdby"]      = $auth->getIdentity()->id;
		   	    $info["createddt"]      = date("Y-m-d H:i:s");
				$componentDB->addData($info);
			 }	
			 
	 	}
	 	 //redirect
		  $this->_redirect('/exam/component/index');	
			
    }
    
    public function editformAction(){    	
    	//title
    	$this->view->title="Setup Component Name : Edit";
    	
    	$auth = Zend_Auth::getInstance();
    	
    	if ($this->_request->isPost()) {   
    		
    		$formData = $this->getRequest()->getPost();		
		
			$iteration  		         = $formData["iteration"];			
		    $data["component_name"]      = $formData["component_name"];
		    $data["id"]                  = $formData["parent_id"];
		         
		    //update parent component
		    $componentDB = new App_Model_Exam_DbTable_Component();
			$componentDB->updateData($data,$data["id"]);
			
			
			for($i=1; $i<=$iteration; $i++){				 	
			
					$component_item_id	= $formData["id".$i];						
					
			   	    if(isset($component_item_id)){
						//update
					 	$info["id"]	= $formData["id".$i];
					 	$info["component_name"] = $formData["component_item_name".$i];
					    $componentDB->updateData($info,$info['id']);
					    
				    }else{ 			
				 	     //insert into table
				 	     $component["component_name"] = $formData["component_item_name".$i];
				 	     $component["parent_id"]      = $formData["parent_id"];
				 	     $component["createdby"]      = $auth->getIdentity()->id;
		   	    		 $component["createddt"]      = date("Y-m-d H:i:s");		
		   	    				 
				   		 $id = $componentDB->addData($component);
				    }
				   
			 }
			 
			  //redirect
		     $this->_redirect($this->view->url(array('module'=>'exam','controller'=>'component', 'action'=>'index'),'default',true));
			 
	   
	    	
    	}else{
    		$id = $this->_getParam('id', 0);
	    	
	    	$componentDB = new App_Model_Exam_DbTable_Component();
	    	$component = $componentDB->getData($id);
	    	$this->view->component = $component;
	    	
	    	$component_item= $componentDB->getInfo($id);
	    	$this->view->component_item = $component_item;
    	}
    	
    		
    }
    
    
    public function deleteAction(){
    	
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$componentDB = new App_Model_Exam_DbTable_Component();
    		$componentDB->deleteData($id); //delete main component
    		$componentDB->deleteItem($id); //delete anak2 dia
    	}
    		
    	//redirect
		$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'component', 'action'=>'index'),'default',true));
    	
    }
    
    public function deleteItemAction(){
    	
      	if ($this->_request->isPost()) {   
    	 	
			 $parent_id = $this->getRequest()->getPost('parent_id');	
			 $id = $this->getRequest()->getPost('id');	
				  
			 for($i=0; $i<count($id); $i++){
			 	//delete component
				$oComponent = new App_Model_Exam_DbTable_Component();
    			$oComponent->deleteData($id[$i]);
    		 }
		 }
    		
    	//redirect
		$this->_redirect('/exam/component/editform/id/'.$parent_id);
    	
    }
    
    
}