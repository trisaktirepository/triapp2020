<?php

class icampus_Plugin_Acl extends Zend_Acl
{
	/*public function __construct ()
    {
        $aclConfig = Zend_Registry::get('acl');
        $roles = $aclConfig->acl->roles;
        $resources = $aclConfig->acl->resources;
        
        $this->_addRoles($roles);
        $this->_addResources($resources);
    }*/
    
	public function __construct ($all_resource)
    {
        $aclConfig = Zend_Registry::get('acl');
        $roles = $aclConfig->acl->roles;
        $resources = $aclConfig->acl->resources;
        
        $this->_addRoles($roles);
        
        //admin resource
        $this->_addAdminResources($all_resource);
        
        //config resource
        $this->_addResources($resources);
    }
    
    protected function _addRoles ($roles)
    {
        foreach ($roles as $name => $parents) {
            if (! $this->hasRole($name)) {
                if (empty($parents)) {
                    $parents = null;
                } else {
                    $parents = explode(',', $parents);
                }
                $this->addRole(new Zend_Acl_Role($name), $parents);
            }
        }
    }
    
    protected function _addResources ($resources)
    {
        foreach ($resources as $permissions => $controllers) {
        	
        	
    	
            foreach ($controllers as $controller => $actions) {
            	
            	
            	
                if ($controller == 'all') {
                    $controller = null;
                } /*else {
                    if (! $this->has($controller)) {
                    	//echo $controller."<br />";
                        $this->add(new Zend_Acl_Resource($controller));
                    }
                }*/
                
                foreach ($actions as $action => $role) {
                    if ($action == 'all') {
                        $action = null;
                    }
                    if ($permissions == 'allow') {
                    	
                        $this->allow($role, $controller, $action);
                        //echo "allow =".$role.":".$controller.":".$action."<br />";
                    }
                    if ($permissions == 'deny') {
                        $this->deny($role, $controller, $action);
                        //echo "deny =".$role.":".$controller.":".$action."<br />";
                    }
                }
            }
        }
    }
    
    protected function _addAdminResources($resources){
    	//$this->add(new Zend_Acl_Resource('generalsetup_university'));
    	//$this->allow('administrator','generalsetup_university');
    	foreach ($resources as $resource_name => $resource){
    		foreach ($resource as $controller_name => $controller){
    			
    			//add resource
    			if ($controller_name == 'all') {
                    //$controller_name = null;
                } else {
                    if (! $this->has($controller_name)) {
                    	//echo $controller_name."<br />";
                        $this->add(new Zend_Acl_Resource($controller_name));
                    }
                }
    			
    			foreach ($controller as $action_name){
    				if ($action_name == 'all') {
                        $action_name = null;
                    }
                    
    				try {
    					$this->allow("administrator", $controller_name, $action_name);	
    				} catch (Exception $e) {
    					echo $e;
    				}
    						
    			}
    		}
    	}
    }
}
?>
