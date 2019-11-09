<?php
class Accessbni_Bootstrap extends Zend_Application_Module_Bootstrap 
{

	protected function _initAutoload() {
		$autoloader = new Zend_Application_Module_Autoloader(array (
				'namespace'	=> 'Accessbni_'	,
				'basePath'	=> APPLICATION_PATH
		));
		
		return $autoloader;
	}
	
	public function _initREST()
	{
		$frontController = Zend_Controller_Front::getInstance();
	
		// register the RestHandler plugin
		$frontController->registerPlugin(new REST_Controller_Plugin_RestHandler($frontController));
	
		// add REST contextSwitch helper
		$contextSwitch = new REST_Controller_Action_Helper_ContextSwitch();
		Zend_Controller_Action_HelperBroker::addHelper($contextSwitch);
	
		// add restContexts helper
		$restContexts = new REST_Controller_Action_Helper_RestContexts();
		Zend_Controller_Action_HelperBroker::addHelper($restContexts);
	}
	
	
}
?>