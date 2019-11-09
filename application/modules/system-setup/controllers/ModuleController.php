<?php

class SystemSetup_ModuleController extends Zend_Controller_Action
{
	/**
	 * The default action - show the home page
	 */
    public function indexAction() 
    {
        $this->view->title = "Module Configuration";
        
        $Module_path =  APPLICATION_PATH ."/modules";
       
        $modules = array();
        
    	if ($handle = opendir($Module_path)) {
    		$i=0;
			while (false !== ($file = readdir($handle))) {
				
		    	if ($file != "." && $file != "..") {
		    		$moduleDB = new SystemSetup_Model_DbTable_Module();
		    		$module = $moduleDB->search($file);

		    		/*** MODULE ***/
		        	$modules[$i]['module']['name'] = $file;
		        	$modules[$i]['module']['id'] = isset($module['id'])?$module['id']:null;
		        	$modules[$i]['module']['alias'] = isset($module['alias'])?$module['alias']:"-";
		        	$modules[$i]['module']['status'] = isset($module['status']) && $module['status']==1?true:false;
		        	
		        	/*** CONTROLLER ***/
		    		$controller_path = $Module_path."/".$file."/controllers";
		        	if ($handle_2 = opendir($controller_path)) {
			    		$j=0;
						while (false !== ($file2 = readdir($handle_2))) {
							if ($file2 != "." && $file2 != ".." && $file2 != ".sharedentries") {
								
								$modules[$i]['controller'][$j]['name'] =  str_replace("Controller.php",'',$file2);
								$modules[$i]['controller'][$j]['status'] = 0;
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
    
	public function toggleAction(){
		
		$name = $this->_getParam('name', "");
		
    	$moduleDB = new SystemSetup_Model_DbTable_Module();
		$module = $moduleDB->search($name);
		
		if($module){
			if($module['status']==0){
				$moduleDB->update(array('status'=>1),'id = '.$module['id']);
			}else{
				$moduleDB->update(array('status'=>0),'id = '.$module['id']);
			}
		}else{
			$module_id =  $moduleDB->insert(array('name'=>$name,'alias'=>$name,'status'=>1));
			
			/*** CONTROLLER ***/
			$Module_path =  APPLICATION_PATH ."/modules";
			$controllerDB = new SystemSetup_Model_DbTable_Controller();
			
    		$controller_path = $Module_path."/".$name."/controllers";
    		
        	if ($handle_2 = opendir($controller_path)) {
	    		
				while (false !== ($file2 = readdir($handle_2))) {
					if ($file2 != "." && $file2 != ".." && $file2 != ".sharedentries") {
						
						$c_name = str_replace("Controller.php",'',$file2);
						$controllerDB->addData(array('name'=>$c_name,'module_id'=>$module_id));
					}
				}
        	}
		}
		
		//redirect
		$this->_redirect($this->view->url(array('module'=>'system-setup','controller'=>'module', 'action'=>'index'),'default',true));
    }
    
    public function editAction(){
    	$module_name = $this->getRequest()->getPost('module_name', null);
    	$alias = $this->getRequest()->getPost('alias', null);
    	
    	
		
    	if($module_name!=null){
	    	$moduleDB = new SystemSetup_Model_DbTable_Module();
			$module = $moduleDB->search($module_name);
			
			if($module){
				$moduleDB->update(array('alias'=>$alias),"name = '".$module_name."'");
			}else{
				$moduleDB->insert(array('name'=>$module_name,'alias'=>$alias,'status'=>1));
			}
    	}
    	//redirect
    	$this->_redirect($this->view->url(array('module'=>'system-setup','controller'=>'module', 'action'=>'index'),'default',true));
    }
    
}

