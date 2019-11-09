<?php

class icampus_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    private $_auth;
    private $_acl;
    private $current_role;
    
    private $no_check_module = array("onnapp","test","application",'authentication', 'admin');

    private $_noauth = array('module' => 'default',
                             'controller' => 'online-application',
                             'action' => 'index');

    private $_noacl = array('module' => 'default',
                            'controller' => 'error',
                            'action' => 'permission');
   
    public function __construct($auth, $acl)
    {
        $this->_auth = $auth;
        $this->_acl = $acl;
    }

	public function preDispatch(Zend_Controller_Request_Abstract $request){
			
		$controller = $request->controller;
		$action = $request->action;
		$module = $request->module;
		
		//echo $this->_auth->hasIdentity();
		//echo $module." : ".$controller." : ".$action;	
			
		if ($this->_auth->hasIdentity()) {
			
			
			/*********************
			 *  for acl filtering 
			 **********************/
			
			$acl_controller = strtolower(str_replace("-","",$request->controller));
			$acl_action = str_replace("-","",$request->action);
			$acl_module = $request->module;
			
			if ($module != "default" && $module != "index"){
				$resource = strtolower(str_replace("-","",$module)) . '_' .$acl_controller;
			}else{
				$resource = $acl_controller;
			}
			
			$role = $this->_auth->getIdentity()->role;

			if (! $this->_acl->isAllowed($role, $resource,$acl_action)) {
				$module = $this->_noacl['module'];
				$controller = $this->_noacl['controller'];
				$action = $this->_noacl['action'];
			}else{
				
			}
			
			//layout
			//$layoutPath = dirname(dirname($this->getLayout()->getLayoutPath())). DIRECTORY_SEPARATOR . 'layouts/scripts/';
			//echo $layoutPath;
			//$this->getLayout()->setLayoutPath();
	        //$this->getLayout()->setLayout($moduleName);
			
		}else 
		if($module!="default" && in_array($module,$this->no_check_module)){
			
			/*********************
			 *  for acl filtering 
			 **********************/
			
			$acl_controller = strtolower(str_replace("-","",$request->controller));
			$acl_action = str_replace("-","",$request->action);
			$acl_module = $request->module;
			
			if ($module != "default" && $module != "index"){
				$resource = strtolower(str_replace("-","",$module)) . '_' .$acl_controller;
			}else{
				$resource = $acl_controller;
			}
			
			if (! $this->_acl->isAllowed("guest", $resource,$acl_action)) {
				$module = $this->_noacl['module'];
				$controller = $this->_noacl['controller'];
				$action = $this->_noacl['action'];
			}	
		}else
		if($module=="default" && $controller=="authentication"){
			
		}else 
		if($module=="default" && $controller=="online-application"){
			/*********************
			 *  for acl filtering 
			 **********************/
			
			$acl_controller = strtolower(str_replace("-","",$request->controller));
			$acl_action = str_replace("-","",$request->action);
			$acl_module = $request->module;
			
			if ($module != "default" && $module != "index"){
				$resource = strtolower(str_replace("-","",$module)) . '_' .$acl_controller;
			}else{
				$resource = $acl_controller;
			}
			
			if (! $this->_acl->isAllowed("guest", $resource,$acl_action)) {
				$module = "default";
				$controller = "online-application";
				$action = "index";
			}
			
		}else 
		if($module=="default" && $controller=="admin"){
			$module = "default";
			$controller = "authentication";
			$action = "login";			
		}else{
			$module = $this->_noauth['module'];
			$controller = $this->_noauth['controller'];
			$action = $this->_noauth['action'];
		}
	
		
		$request->setModuleName($module);
		$request->setControllerName($controller);
		$request->setActionName($action);
	}
}

?>
