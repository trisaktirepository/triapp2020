<?php

class icampus_Plugin_SystemLog extends Zend_Controller_Plugin_Abstract
{

	public function preDispatch(Zend_Controller_Request_Abstract $request){
		
		$controller = $request->controller;
		$action = $request->action;
		$module = $request->module;
		$params = $request->getParams();
		
		$auth = Zend_Auth::getInstance();
		$user_id = $auth->getIdentity()->id;

		if($params==null){
			foreach ($params as $param){
	        	$path .=  "/".$param;	
	        }
		}else{
			$path = "/".$module."/".$controller."/".$action;
		}
		
        $message = $path;
        
        //get logger from session
        $logger=Zend_Registry::get('system_logger');
        
        //put access log
        $logger->info($message);
	}
}

?>
