<?php

class SystemSetup_RoleController extends Zend_Controller_Action
{
    public function indexAction() 
    {
        $this->view->title = "Roles and Permissions";
        
        $roleDB = new SystemSetup_Model_DbTable_Role();
        
        $roles = $roleDB->getData();
        $this->view->roles = $roles;
        
        if(!$roles){
        	$this->view->noticeMessage = "There are no role in the system. Please set at least one role";
        }
    }
    
	public function viewAction() 
    {
        $this->view->title = "Role's Permission";
        
        $id = $this->_getParam('id', -1);
        $this->view->role_id = $id;
        
        $roleDB = new SystemSetup_Model_DbTable_Role();
        
        $role = $roleDB->getData($id);
        $this->view->role = $role;
        
    	if(!$role){
        	$this->view->noticeMessage = "There are no role selected";
        }
        
        //modules
        $Module_path =  APPLICATION_PATH ."/modules";
       
        $modules = array();
        
    	if ($handle = opendir($Module_path)) {
    		$i=0;
			while (false !== ($file = readdir($handle))) {
				$privilegeDB = new App_Model_System_DbTable_Privilege();
		    	if ($file != "." && $file != "..") {
		    		$moduleDB = new SystemSetup_Model_DbTable_Module();
		    		$module = $moduleDB->search($file);
		    		
		    		$modules[$i]['module']['id'] = 	isset($module['id'])?$module['id']:null;	    		
		        	$modules[$i]['module']['name'] = $file;
		        	$modules[$i]['module']['alias'] = isset($module['alias'])?$module['alias']:"-";
		        	$modules[$i]['module']['status'] = isset($module['status']) && $module['status']==1?true:false;
		        	
		        	//PRIVILEGE STATUS
		        	if($modules[$i]['module']['id']!=null){
		        		
		        		$modules[$i]['module']['privilege'] = $privilegeDB->getPrivilege($id,$modules[$i]['module']['id']);
		        		
		        	}else{
		        		$modules[$i]['module']['privilege'] = array(
		        										'view'=>0,
		        										'add'=>0,
		        										'edit'=>0,
		        										'delete'=>0
		        									);
		        	}
		        	
		        	//controller
		        	$controllerDB = new SystemSetup_Model_DbTable_Controller();
		        	
		        	$controller_path = $Module_path."/".$file."/controllers";
		        	if ($handle_2 = opendir($controller_path)) {
			    		$j=0;
						while (false !== ($file2 = readdir($handle_2))) {
							if ($file2 != "." && $file2 != ".." && $file2 != ".sharedentries") {
								
								$controller = $controllerDB->search(str_replace("Controller.php",'',$file2),$modules[$i]['module']['id']);
								
								if(isset($controller['id'])){
									
									$module_id = $modules[$i]['module']['id']!=null?$modules[$i]['module']['id']:0;
									$con_pri = $privilegeDB->getPrivilege($id,$module_id,$controller['id']);
									
																
									$modules[$i]['controller'][$j] =  array(
																	'id'=>$controller['id'],
																	'name'=>str_replace("Controller.php",'',$file2),
																	'privilege'=>$con_pri
																);
																	
								}else{
									$modules[$i]['controller'][$j]['id'] = $controller['id'];
									$modules[$i]['controller'][$j]['name'] = str_replace("Controller.php",'',$file2);
									$modules[$i]['controller'][$j]['privilege'] =  array(
									        										'view'=>0,
									        										'add'=>0,
									        										'edit'=>0,
									        										'delete'=>0
									        									);
								}
								
								$j++;
							}
						}
		        	}
		        	
		        	$i++;
		    	}
		    }
		    closedir($handle);
		}
		
		$this->view->modules = $modules;
    }
    
    public function addAction(){
    	
    	$role_name = $this->getRequest()->getPost('name', null);
    
    	if($role_name!=null){
    		$roleDB = new SystemSetup_Model_DbTable_Role();
    		
    		$roleDB->addData(array('name'=>$role_name));
    	}
    	$this->_redirect($this->view->url(array('module'=>'system-setup','controller'=>'role', 'action'=>'index'),'default',true));
    }
    
	public function editAction(){
    	
		$role_id = $this->getRequest()->getPost('id', 0);
    	$role_name = $this->getRequest()->getPost('name', null);
    
    	if($role_name!=null && $role_id!=0){
    		$roleDB = new SystemSetup_Model_DbTable_Role();
    		
    		$roleDB->updateData(array('name'=>$role_name),$role_id);
    	}
    	$this->_redirect($this->view->url(array('module'=>'system-setup','controller'=>'role', 'action'=>'index'),'default',true));
    }
    
	public function deleteAction(){
    	
		$role_id = $this->getRequest()->getPost('id', 0);
    
    	if($role_id!=0){
    		$roleDB = new SystemSetup_Model_DbTable_Role();
    		
    		$roleDB->deleteData($role_id);
    	}
    	$this->_redirect($this->view->url(array('module'=>'system-setup','controller'=>'role', 'action'=>'index'),'default',true));
    }
    
    public function ajaxGetInfoAction(){
    	$id = $this->_getParam('id', 0);
    	
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	
	  	
	  	$roleDB = new SystemSetup_Model_DbTable_Role();
		$roles = $roleDB->getData($id);
		
		
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($roles);
		
		$this->view->json = $json;
    }
    
	public function ajaxTogglePrivilegeAction(){
		
    	$module_id = $this->_getParam('module_id', 0);
    	$controller_id = $this->_getParam('controller_id', 0);
    	$role_id = $this->_getParam('role_id', 0);
    	$privilege = $this->_getParam('privilege', "");
    	
    	
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	$privilegeDB = new App_Model_System_DbTable_Privilege();
	  	if($rule = $privilegeDB->isPrivilegeAvailable($role_id,$module_id,$controller_id)){
	  		
	  		if($rule[$privilege]==0){
	  			$privilegeDB->update(array($privilege=>1),"module_id = ".$module_id. " and controller_id = ".$controller_id." and role_id = ".$role_id);	
	  		}else{
	  			$privilegeDB->update(array($privilege=>0),"module_id = ".$module_id. " and controller_id = ".$controller_id." and role_id = ".$role_id);
	  		}
	  		
	  		
	  		
	  	}else{
	  		$privilegeDB->insert(array('module_id'=>$module_id, 'controller_id'=>$controller_id,'role_id'=>$role_id,$privilege=>1));
	  	}
		
	  	$rule = $privilegeDB->isPrivilegeAvailable($role_id,$module_id,$controller_id);
		
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($rule);
		
		$this->view->json = $json;
    }
    
}

